<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Setup\Controller;

use Magento\Setup\Model\Navigation as NavModel;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\JsonModel;
use Laminas\View\Model\ViewModel;
use Magento\Setup\Model\Cron\Status;

/**
 * Class Navigation
 *
 */
class Navigation extends AbstractActionController
{
    /**
     * @var NavModel
     */
    protected $navigation;

    /**
     * @var Status
     */
    protected $status;

    /**
     * @var ViewModel
     */
    protected $view;

    /**
     * @param NavModel $navigation
     * @param Status $status
     */
    public function __construct(NavModel $navigation, Status $status)
    {
        $this->navigation = $navigation;
        $this->status = $status;
        $this->view = new ViewModel;
        $this->view->setVariable('menu', $this->navigation->getMenuItems());
        $this->view->setVariable('main', $this->navigation->getMainItems());
    }

    /**
     * @return JsonModel
     */
    public function indexAction()
    {
        $json = new JsonModel;
        $json->setVariable('nav', $this->navigation->getData());
        $json->setVariable('menu', $this->navigation->getMenuItems());
        $json->setVariable('main', $this->navigation->getMainItems());
        $json->setVariable('titles', $this->navigation->getTitles());
        return $json;
    }

    /**
     * @return array|ViewModel
     */
    public function menuAction()
    {
        $this->view->setVariable('menu', $this->navigation->getMenuItems());
        $this->view->setVariable('main', $this->navigation->getMainItems());
        $this->view->setTemplate('/magento/setup/navigation/menu.phtml');
        $this->view->setTerminal(true);
        return $this->view;
    }

    /**
     * @return array|ViewModel
     */
    public function headerBarAction()
    {
        if ($this->navigation->getType() === NavModel::NAV_UPDATER) {
            if ($this->status->isUpdateError() || $this->status->isUpdateInProgress()) {
                $this->view->setVariable('redirect', '../' . Environment::UPDATER_DIR . '/index.php');
            }
        }
        $this->view->setTemplate('/magento/setup/navigation/header-bar.phtml');
        $this->view->setTerminal(true);
        return $this->view;
    }
}
