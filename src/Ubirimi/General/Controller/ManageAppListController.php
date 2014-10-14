<?php

namespace Ubirimi\General\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Ubirimi\UbirimiController;
use Ubirimi\Util;
use Ubirimi\Container\UbirimiContainer;

use Ubirimi\SystemProduct;

class ManageAppListController extends UbirimiController
{
    public function indexAction(Request $request, SessionInterface $session)
    {
        Util::checkUserIsLoggedInAndRedirect();

        $application = $request->request->get('app');
        $visible = $request->request->get('visible');

        $currentDate = Util::getServerCurrentDateTime();

        switch ($application) {
            case 'yongo':
                $productId = SystemProduct::SYS_PRODUCT_YONGO;
                break;
            case 'agile':
                $productId = SystemProduct::SYS_PRODUCT_CHEETAH;
                break;
            case 'helpdesk':
                $productId = SystemProduct::SYS_PRODUCT_HELP_DESK;
                break;
            case 'events':
                $productId = SystemProduct::SYS_PRODUCT_CALENDAR;
                break;
            case 'documentador':
                $productId = SystemProduct::SYS_PRODUCT_DOCUMENTADOR;
                break;
            case 'svn':
                $productId = SystemProduct::SYS_PRODUCT_SVN_HOSTING;
                break;
        }

        if ($visible) {
            $this->getRepository('ubirimi.general.client')->addProduct($session->get('client/id'), $productId, $currentDate);
        } else {
            $this->getRepository('ubirimi.general.client')->deleteProduct($session->get('client/id'), $productId);
            if ($productId == SystemProduct::SYS_PRODUCT_YONGO) {
                $this->getRepository('ubirimi.general.client')->deleteProduct($session->get('client/id'), SystemProduct::SYS_PRODUCT_HELP_DESK);
                $this->getRepository('ubirimi.general.client')->deleteProduct($session->get('client/id'), SystemProduct::SYS_PRODUCT_CHEETAH);
            }
        }

        $clientProducts = $this->getRepository('ubirimi.general.client')->getProducts($session->get('client/id'), 'array');

        UbirimiContainer::get()['session']->remove("client/products");

        if (count($clientProducts)) {
            array_walk($clientProducts, function($value, $key) {
                UbirimiContainer::get()['session']->set("client/products/{$key}", $value);
            });
        } else {
            $this->getRepository('ubirimi.general.client')->addProduct($session->get('client/id'), $productId, $currentDate);
            $session->set('client/products', array(array('sys_product_id' => $productId)));
        }

        return new Response('');
    }
}
