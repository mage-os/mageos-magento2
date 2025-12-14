<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\CustomAdminLogo\Model\Config\Backend;

use Magento\Config\Model\Config\Backend\Image;

class AdminLoginLogo extends Image
{
    public const UPLOAD_DIR = 'admin/logo/custom/login';

    /**
     * @inheritDoc
     */
    protected function _getUploadDir(): string
    {
        return $this->_mediaDirectory->getAbsolutePath(self::UPLOAD_DIR);
    }

    /**
     * @inheritDoc
     */
    protected function _addWhetherScopeInfo(): bool
    {
        return false;
    }
}
