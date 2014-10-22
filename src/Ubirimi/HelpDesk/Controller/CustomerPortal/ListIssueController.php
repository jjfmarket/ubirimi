<?php

namespace Ubirimi\HelpDesk\Controller\CustomerPortal;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Ubirimi\SystemProduct;
use Ubirimi\UbirimiController;
use Ubirimi\Util;

class ListIssueController extends UbirimiController
{
    public function indexAction(Request $request, SessionInterface $session)
    {
        Util::checkUserIsLoggedInAndRedirect();

        $menuSelectedCategory = 'home';
        $clientId = $session->get('client/id');
        $projectsForBrowsing = $this->getRepository('ubirimi.general.client')->getProjects($clientId, null, null, true);
        $clientSettings = $this->getRepository('ubirimi.general.client')->getSettings($clientId);

        $session->set('selected_product_id', SystemProduct::SYS_PRODUCT_HELP_DESK);
        $selectedProductId = $session->get('selected_product_id');

        $cliMode = false;

        if ($projectsForBrowsing) {
            $projectIdsAndNames = Util::getAsArray($projectsForBrowsing, array('id', 'name'));
            $projectsForBrowsing->data_seek(0);
            $projectIds = Util::getAsArray($projectsForBrowsing, array('id'));

            $searchCriteria = $this->getRepository('yongo.issue.issue')->getSearchParameters($projectsForBrowsing, $session->get('client/id'), 1);
            $issuesResult = null;
        }

        if ($request->request->has('search')) {
            $searchParameters = $this->getRepository('yongo.issue.issue')->prepareDataForSearchFromPostGet($projectIds, $request->request->all(), $request->query->all());

            $redirectLink = str_replace("%7C", "|", http_build_query($searchParameters));

            return new RedirectResponse('/helpdesk/customer-portal/tickets?' . $redirectLink);
        } else {
            $getSearchParameters = $this->getRepository('yongo.issue.issue')->prepareDataForSearchFromURL($request->query->all(), 30);
            $getSearchParameters['helpdesk_flag'] = 1;
            // check to see if the project Ids are all belonging to the client
            $getProjectIds = $request->request->has('project') ? explode('|', $request->query->get('project')) : null;
            if ($getProjectIds) {
                for ($pos = 0; $pos < count($getProjectIds); $pos++) {
                    $projectFilter = $this->getRepository('yongo.project.project')->getById($getProjectIds[$pos]);

                    if ($projectFilter['client_id'] != $session->get('client/id')) {
                        return new RedirectResponse('/general-settings/bad-link-access-denied');
                    }
                }
            }

            $parseURLData = parse_url($_SERVER['REQUEST_URI']);
            $projectsForBrowsing = array(229);
            if (isset($parseURLData['query']) && $projectsForBrowsing) {
                if (Util::searchQueryNotEmpty($getSearchParameters)) {
                    $issuesResult = $this->getRepository('yongo.issue.issue')->getByParameters($getSearchParameters, $session->get('user/id'));

                    $issues = $issuesResult[0];
                    $issuesCount = $issuesResult[1];
                    $issuesPerPage = $session->get('user/issues_per_page');
                    $currentSearchPage = isset($_GET['page']) ? $_GET['page'] : 1;
                    $countPages = ceil($issuesCount / 30);
                    $getSearchParameters['count_pages'] = $countPages;
                    $getSearchParameters['link_to_page'] = '/helpdesk/customer-portal/tickets';
                }
            }
        }

        $SLAs = $this->getRepository('helpDesk.sla.sla')->getByProjectIds(array(229));

        $columns = array('code', 'summary', 'priority', 'status', 'created', 'updated', 'reporter', 'assignee', 'settings_menu');
        if (Util::checkUserIsLoggedIn()) {
            $columns = explode('#', $session->get('user/issues_display_columns'));

            $columns[] = 'settings_menu';
            $columns[] = '';
        }

        $parseData = parse_url($_SERVER['REQUEST_URI']);
        $query = isset($parseData['query']) ? $parseData['query'] : '';

        if (isset($query)) {
            $session->set('last_search_parameters', $parseData['query']);
        } else {
            $session->remove('last_search_parameters');
        }

        return $this->render(__DIR__ . '/../../Resources/views/customer_portal/ListIssue.php', get_defined_vars());
    }
}
