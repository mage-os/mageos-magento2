<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See LICENCE.txt for licence details.
 */
declare(strict_types=1);

namespace Magento\CustomAdminLogo\Scope;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    private const XML_PATH_LOGIN_LOGO = 'admin/e119_admin_logos/login';
    private const XML_PATH_MENU_LOGO = 'admin/e119_admin_logos/menu';

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig
    ) {
    }

    /**
     * Get admin login logo file from configuration
     *
     * @return string|null
     */
    public function getAdminLoginLogoFileName(): ?string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_LOGIN_LOGO, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get admin menu logo file from configuration
     *
     * @return string|null
     */
    public function getAdminMenuLogoFileName(): ?string
    {
        return $this->scopeConfig->getValue(self::XML_PATH_MENU_LOGO, ScopeInterface::SCOPE_STORE);
    }
}
