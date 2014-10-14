<?php

namespace Ubirimi\Yongo\Controller\Issue;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Ubirimi\SystemProduct;
use Ubirimi\UbirimiController;
use Ubirimi\Util;

class SearchListPrintableContentController extends UbirimiController
{
    public function indexAction(Request $request, SessionInterface $session)
    {
        $issuesPerPage = $session->get('user/issues_per_page');
        $loggedInUserId = $session->get('user/id');

        $searchParameters = array();
        $parseURLData = null;

        $getFilter = $request->get('filter');
        $getPage = $request->get('page');
        $getSortColumn = $request->get('sort') ? $request->get('sort') : 'created';
        $getSortOrder = $request->get('order') ? $request->get('order') : 'desc';
        $getSearchQuery = $request->get('search_query');;
        $getSummaryFlag = $request->get('summary_flag');
        $getDescriptionFlag = $request->get('description_flag');
        $getCommentsFlag = $request->get('comments_flag');
        $getProjectIds = $request->get('project') ? explode('|', $request->get('project')) : null;
        $getAssigneeIds = $request->get('assignee') ? explode('|', $request->get('assignee')) : null;
        $getReportedIds = $request->get('reporter') ? explode('|', $request->get('reporter')) : null;
        $getIssueTypeIds = $request->get('type') ? explode('|', $request->get('type')) : null;
        $getIssueStatusIds = $request->get('status') ? explode('|', $request->get('status')) : null;
        $getIssuePriorityIds = $request->get('priority') ? explode('|', $request->get('priority')) : null;
        $getProjectComponentIds = $request->get('component') ? explode('|', $request->get('component')) : null;
        $getProjectVersionIds = $request->get('version') ? explode('|', $request->get('version')) : null;
        $getIssueResolutionIds = $request->get('resolution') ? explode('|', $request->get('resolution')) : null;

        $getSearchParameters = array('search_query' => $getSearchQuery,
            'summary_flag' => $getSummaryFlag,
            'description_flag' => $getDescriptionFlag,
            'comments_flag' => $getCommentsFlag,
            'project' => $getProjectIds,
            'assignee' => $getAssigneeIds,
            'reporter' => $getReportedIds,
            'filter' => $getFilter,
            'type' => $getIssueTypeIds,
            'status' => $getIssueStatusIds,
            'priority' => $getIssuePriorityIds,
            'component' => $getProjectComponentIds,
            'version' => $getProjectVersionIds,
            'resolution' => $getIssueResolutionIds,
            'sort' => $getSortColumn,
            'sort_order' => $getSortOrder);

        $parseURLData = parse_url($_SERVER['REQUEST_URI']);

        if (isset($parseURLData['query'])) {
            if (Util::searchQueryNotEmpty($getSearchParameters)) {

                $issues = $this->getRepository('yongo.issue.issue')->getByParameters($getSearchParameters, $loggedInUserId);
                $issuesCount = $issues->num_rows;
                $getSearchParameters['link_to_page'] = '/yongo/issue/printable-list';
            }
        }

        $columns = array('code',
            'summary',
            'priority',
            'status',
            'created',
            'updated',
            'reporter',
            'assignee');

        $sectionPageTitle = $session->get('client/settings/title_name') . ' / ' . SystemProduct::SYS_PRODUCT_YONGO_NAME . ' / Print List Full Content';
        $menuSelectedCategory = null;

        return $this->render(__DIR__ . '/../../Resources/views/issue/search/SearchListPrintableContent.php', get_defined_vars());
    }
}