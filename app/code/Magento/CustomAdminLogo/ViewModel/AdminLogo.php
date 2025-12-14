<?php
/**
 * Copyright © element119. All rights reserved.
 * See LICENCE.txt for licence details.
 */
declare(strict_types=1);

namespace Magento\CustomAdminLogo\ViewModel;

use Magento\CustomAdminLogo\Model\AdminLogo as AdminLogoModel;
use Magento\Framework\App\Area;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class AdminLogo implements ArgumentInterface
{
    /**
     * @param AdminLogoModel $adminLogo
     * @param RequestInterface $request
     */
    public function __construct(
        private readonly AdminLogoModel $adminLogo,
        private readonly RequestInterface $request
    ) {
    }

    /**
     * Get admin logo model
     *
     * @return AdminLogoModel
     */
    public function getAdminLogoModel(): AdminLogoModel
    {
        return $this->adminLogo;
    }

    /**
     * Is admin login page
     *
     * @return bool
     */
    public function isAdminLoginPage(): bool
    {
        return $this->request->getRouteName() === Area::AREA_ADMINHTML
            && $this->request->getControllerName() === 'auth'
            && $this->request->getActionName() === 'login';
    }
}
