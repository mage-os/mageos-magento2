<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Model;

use Laminas\ServiceManager\ServiceLocatorInterface;
use Magento\Framework\App\DeploymentConfig;

class Navigation
{
    /**
     * Types of wizards
     */
    const NAV_INSTALLER = 'navInstaller';
    const NAV_UPDATER = 'navUpdater';
    /**#@- */

    /**#@- */
    private mixed $navStates;

    /**
     * @var string
     */
    private string $navType;

    /**
     * @var string
     */
    private mixed $titles;

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @param DeploymentConfig $deploymentConfig
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\RuntimeException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __construct(ServiceLocatorInterface $serviceLocator, DeploymentConfig $deploymentConfig)
    {
        if ($deploymentConfig->isAvailable()) {
            $this->navStates = $serviceLocator->get('config')[self::NAV_UPDATER];
            $this->navType = self::NAV_UPDATER;
            $this->titles = $serviceLocator->get('config')[self::NAV_UPDATER . 'Titles'];
        } else {
            $this->navStates = $serviceLocator->get('config')[self::NAV_INSTALLER];
            $this->navType = self::NAV_INSTALLER;
            $this->titles = $serviceLocator->get('config')[self::NAV_INSTALLER . 'Titles'];
        }
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->navType;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->navStates;
    }

    /**
     * Retrieve array of menu items
     *
     * Returns only items with 'nav' equal to TRUE
     *
     * @return array
     */
    public function getMenuItems()
    {
        return array_values(array_filter(
            $this->navStates,
            function ($value) {
                return isset($value['nav']) && (bool)$value['nav'];
            }
        ));
    }

    /**
     * Retrieve array of menu items
     *
     * Returns only items with 'main' equal to TRUE
     *
     * @return array
     */
    public function getMainItems()
    {
        $result = array_values(array_filter(
            $this->navStates,
            function ($value) {
                return isset($value['main']) && (bool)$value['main'];
            }
        ));
        return $result;
    }

    /**
     * Returns titles of the navigation pages
     *
     * @return array
     */
    public function getTitles()
    {
        return $this->titles;
    }
}
