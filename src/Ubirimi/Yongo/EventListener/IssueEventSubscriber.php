<?php

/*
 *  Copyright (C) 2012-2014 SC Ubirimi SRL <info-copyright@ubirimi.com>
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License version 2 as
 *  published by the Free Software Foundation.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301, USA.
 */

namespace Ubirimi\Yongo\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Ubirimi\Container\UbirimiContainer;
use Ubirimi\Yongo\Event\IssueEvent;
use Ubirimi\Yongo\Event\YongoEvents;

class IssueEventSubscriber implements EventSubscriberInterface
{

    public function onIssue(IssueEvent $event)
    {
        $container = UbirimiContainer::get();
    }


    public function onIssueCommentEmail(IssueEvent $event)
    {
        $container = UbirimiContainer::get();

        $container['issue.email']->emailIssueComment($event->getIssue(), $event->getProject(), $event->getExtra());
    }

    public function onIssueLinkEmail(IssueEvent $event)
    {
        $container = UbirimiContainer::get();

        $container['issue.email']->emailIssueLink($event->getExtra()['issueId'], $event->getProject(), $event->getExtra()['comment']);
    }

    public function onIssueShareEmail(IssueEvent $event)
    {
        $container = UbirimiContainer::get();

        $container['issue.email']->emailIssueShare($event->getIssue(), $event->getExtra()['userIds'], $event->getExtra()['noteContent']);
    }

    public function onIssueWorkLogged(IssueEvent $event)
    {
        $container = UbirimiContainer::get();

        $container['issue.email']->emailIssueWorkLogged($event->getIssue(), $event->getProject(), $event->getExtra());
    }

    public function onIssueAddAttachment(IssueEvent $event)
    {
        $container = UbirimiContainer::get();

        $container['issue.email']->emailIssueAddAttachemnt($event->getIssue(), $event->getProject(), $event->getExtra());
    }

    public function onIssueWorkLogUpdated(IssueEvent $event)
    {
        $container = UbirimiContainer::get();

        $container['issue.email']->emailIssueWorkLogUpdated($event->getIssue(), $event->getProject(), $event->getExtra());
    }

    public function onIssueWorkLogDeleted(IssueEvent $event)
    {
        $container = UbirimiContainer::get();

        $container['issue.email']->emailIssueWorkLogDeleted($event->getIssue(), $event->getProject(), $event->getExtra());
    }

    public static function getSubscribedEvents()
    {
        return array(
            YongoEvents::YONGO_ISSUE => 'onIssue',
            YongoEvents::YONGO_ISSUE_EMAIL => 'onIssueEmail',
            YongoEvents::YONGO_ISSUE_LINK_EMAIL => 'onIssueLinkEmail',
            YongoEvents::YONGO_ISSUE_SHARE_EMAIL => 'onIssueShareEmail',
            YongoEvents::YONGO_ISSUE_WORK_LOGGED => 'onIssueWorkLogged',
            YongoEvents::YONGO_ISSUE_WORK_LOG_UPDATED => 'onIssueWorkLogUpdated',
            YongoEvents::YONGO_ISSUE_WORK_LOG_DELETED => 'onIssueWorkLogDeleted',
            YongoEvents::YONGO_ISSUE_ADD_ATTACHMENT => 'onIssueAddAttachment'
        );
    }
}