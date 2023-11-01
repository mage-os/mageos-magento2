<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Setup\Controller;

use Magento\Framework\Module\ModuleList;
use Magento\Setup\Model\ObjectManagerProvider;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class Success extends AbstractActionController
{
    /**
     * @var ModuleList
     */
    protected $moduleList;

    /**
     * @var ObjectManagerProvider
     */
    protected $objectManagerProvider;

    /**
     * @param ModuleList $moduleList
     * @param ObjectManagerProvider $objectManagerProvider
     */
    public function __construct(ModuleList $moduleList, ObjectManagerProvider $objectManagerProvider)
    {
        $this->moduleList = $moduleList;
        $this->objectManagerProvider = $objectManagerProvider;
    }

    /**
     * @return ViewModel
     * @throws \Magento\Setup\Exception
     */
    public function indexAction()
    {
        if ($this->moduleList->has('Magento_SampleData')) {
            /** @var \Magento\Framework\Setup\SampleData\State $sampleData */
            $sampleData = $this->objectManagerProvider->get()->get(\Magento\Framework\Setup\SampleData\State::class);
            $isSampleDataErrorInstallation = $sampleData->hasError();
        } else {
            $isSampleDataErrorInstallation = false;
        }
        $view = new ViewModel([
            'isSampleDataErrorInstallation' => $isSampleDataErrorInstallation
        ]);
        $view->setTerminal(true);
        return $view;
    }
}
