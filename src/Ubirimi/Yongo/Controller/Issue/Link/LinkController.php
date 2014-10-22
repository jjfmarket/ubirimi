<?php

namespace Ubirimi\Yongo\Controller\Issue\Link;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Ubirimi\Container\UbirimiContainer;
use Ubirimi\UbirimiController;
use Ubirimi\Util;
use Ubirimi\Yongo\Event\IssueEvent;
use Ubirimi\Yongo\Event\YongoEvents;
use Ubirimi\Yongo\Repository\Issue\LinkType;

class LinkController extends UbirimiController
{
    public function indexAction(Request $request, SessionInterface $session)
    {
        Util::checkUserIsLoggedInAndRedirect();

        $loggedInUserId = $session->get('user/id');

        $issueId = $request->request->get('id');
        $issue = $this->getRepository('yongo.issue.issue')->getByParameters(array('issue_id' => $issueId), $loggedInUserId);
        $project = $this->getRepository('yongo.project.project')->getById($issue['issue_project_id']);

        $linkTypeData = explode('_', $request->request->get('link_type'));
        $linkTypeId = $linkTypeData[0];
        $type = $linkTypeData[1];
        $linkedIssues = $request->request->get('linked_issues');
        $comment = Util::cleanRegularInputField($_POST['comment']);

        $date = Util::getServerCurrentDateTime();
        LinkType::addLink($issueId, $linkTypeId, $type, $linkedIssues, $date);

        if ($comment != '') {
            $this->getRepository('yongo.issue.comment')->add($issueId, $loggedInUserId, $comment, $date);

            $issueEvent = new IssueEvent($issue, $project, IssueEvent::STATUS_UPDATE, array('issueId' => $issueId, 'comment' => $comment));
            UbirimiContainer::get()['dispatcher']->dispatch(YongoEvents::YONGO_ISSUE_LINK_EMAIL, $issueEvent);
        }

        return new Response('');
    }
}