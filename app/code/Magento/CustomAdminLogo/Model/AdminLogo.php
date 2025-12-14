<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See LICENCE.txt for licence details.
 */
declare(strict_types=1);

namespace Magento\CustomAdminLogo\Model;

use Magento\CustomAdminLogo\Model\Config\Backend\AdminLoginLogo;
use Magento\CustomAdminLogo\Model\Config\Backend\AdminMenuLogo;
use Magento\CustomAdminLogo\Scope\Config;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Driver\File as FileDriver;
use Magento\Framework\UrlInterface;

class AdminLogo
{
    /**
     * @param Config $moduleConfig
     * @param FileDriver $fileDriver
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        private readonly Config $moduleConfig,
        private readonly FileDriver $fileDriver,
        private readonly UrlInterface $urlBuilder
    ) {
    }

    /**
     * Get custom admin login logo src
     *
     * @return string|null
     */
    public function getCustomAdminLoginLogoSrc(): ?string
    {
        if (!$logoFileName = $this->moduleConfig->getAdminLoginLogoFileName()) {
            return null;
        }

        return $this->fileDriver->getAbsolutePath(
            $this->urlBuilder->getBaseUrl() . DirectoryList::MEDIA . DIRECTORY_SEPARATOR,
            AdminLoginLogo::UPLOAD_DIR . DIRECTORY_SEPARATOR . $logoFileName
        );
    }

    /**
     * Get custom admin menu logo src
     *
     * @return string|null
     */
    public function getCustomAdminMenuLogoSrc(): ?string
    {
        if (!$logoFileName = $this->moduleConfig->getAdminMenuLogoFileName()) {
            return null;
        }

        return $this->fileDriver->getAbsolutePath(
            $this->urlBuilder->getBaseUrl() . DirectoryList::MEDIA . DIRECTORY_SEPARATOR,
            AdminMenuLogo::UPLOAD_DIR . DIRECTORY_SEPARATOR . $logoFileName
        );
    }
}
