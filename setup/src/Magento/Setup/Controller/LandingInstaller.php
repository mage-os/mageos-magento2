<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Setup\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

/**
 * Controller for Setup Landing page
 */
class LandingInstaller extends AbstractActionController
{
    /**
     * @var \Magento\Framework\App\ProductMetadata
     */
    protected $productMetadata;

    /**
     * @param \Magento\Framework\App\ProductMetadata $productMetadata
     */
    public function __construct(\Magento\Framework\App\ProductMetadata $productMetadata)
    {
        $this->productMetadata = $productMetadata;
    }

    /**
     * @return array|ViewModel
     */
    public function indexAction()
    {
        $welcomeMsg = "Welcome to MageOS Web Installer, your online store headquarters.<br>"
            . "Click 'Agree and Set Up MageOS' or read ";
        $docRef = "http://devdocs.magento.com/guides/v1.0/install-gde/install/install-web.html";
        $agreeButtonText = "Agree and Install MageOS";
        $view = new ViewModel;
        $view->setTerminal(true);
        $view->setTemplate('/magento/setup/landing.phtml');
        $view->setVariable('version', $this->productMetadata->getVersion());
        $view->setVariable('welcomeMsg', $welcomeMsg);
        $view->setVariable('docRef', $docRef);
        $view->setVariable('agreeButtonText', $agreeButtonText);
        return $view;
    }
}
