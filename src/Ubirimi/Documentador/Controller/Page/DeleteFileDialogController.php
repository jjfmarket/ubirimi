<?php

namespace Ubirimi\Documentador\Controller\Page;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Ubirimi\UbirimiController;

class DeleteFileDialogController extends UbirimiController
{
    public function indexAction(Request $request, SessionInterface $session)
    {
        return new Response('Are you sure you want to delete this file?');
    }
}