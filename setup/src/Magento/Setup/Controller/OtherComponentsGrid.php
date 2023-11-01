<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Controller;

use Magento\Composer\InfoCommand;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\JsonModel;

/**
 * Controller for other components grid on select version page
 */
class OtherComponentsGrid extends AbstractActionController
{
    /**
     * @var \Magento\Framework\Composer\ComposerInformation
     */
    private $composerInformation;

    /**
     * @var \Magento\Composer\InfoCommand
     */
    private $infoCommand;

    /**
     * @param \Magento\Framework\Composer\ComposerInformation $composerInformation
     * @param \Magento\Framework\Composer\MagentoComposerApplicationFactory $magentoComposerApplicationFactory
     */
    public function __construct(
        \Magento\Framework\Composer\ComposerInformation $composerInformation,
        \Magento\Framework\Composer\MagentoComposerApplicationFactory $magentoComposerApplicationFactory
    ) {
        $this->composerInformation = $composerInformation;
        $this->infoCommand = $magentoComposerApplicationFactory->createInfoCommand();
    }

    /**
     * No index action, return 404 error page
     *
     * @return \Laminas\View\Model\ViewModel
     */
    public function indexAction()
    {
        $view = new \Laminas\View\Model\ViewModel;
        $view->setTemplate('/error/404.phtml');
        $this->getResponse()->setStatusCode(\Zend\Http\Response::STATUS_CODE_404);
        return $view;
    }

    /**
     * Get Components from composer info command
     *
     * @return JsonModel
     * @throws \RuntimeException
     */
    public function componentsAction()
    {
        try {
            $components = $this->composerInformation->getInstalledMagentoPackages();
            foreach ($components as $component) {
                if (!$this->composerInformation->isPackageInComposerJson($component['name'])) {
                    unset($components[$component['name']]);
                    continue;
                }
                $componentNameParts = explode('/', $component['name']);
                $packageInfo = $this->infoCommand->run($component['name']);
                if (!$packageInfo) {
                    throw new \RuntimeException('Package info not found for ' . $component['name']);
                }
                if ($packageInfo[InfoCommand::NEW_VERSIONS]) {
                    $currentVersion = $packageInfo[InfoCommand::CURRENT_VERSION];
                    $components[$component['name']]['version'] = $currentVersion;
                    $versions = [];
                    foreach ($packageInfo[InfoCommand::NEW_VERSIONS] as $version) {
                        $versions[] = ['id' => $version, 'name' => $version];
                    }
                    $versions[] = [
                        'id' => $packageInfo[InfoCommand::CURRENT_VERSION],
                        'name' => $packageInfo[InfoCommand::CURRENT_VERSION]
                    ];

                    $versions[0]['name'] .= ' (latest)';
                    $versions[count($versions) - 1]['name'] .= ' (current)';

                    $components[$component['name']]['vendor'] = $componentNameParts[0];
                    $components[$component['name']]['updates'] = $versions;
                    $components[$component['name']]['dropdownId'] = 'dd_' . $component['name'];
                    $components[$component['name']]['checkboxId'] = 'cb_' . $component['name'];
                } else {
                    unset($components[$component['name']]);
                }
            }
            return new JsonModel(
                [
                    'components' => array_values($components),
                    'total' => count($components),
                    'responseType' => ResponseTypeInterface::RESPONSE_TYPE_SUCCESS
                ]
            );
        } catch (\Exception $e) {
            return new JsonModel(
                [
                    'responseType' => ResponseTypeInterface::RESPONSE_TYPE_ERROR
                ]
            );
        }
    }
}
