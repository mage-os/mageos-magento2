<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See LICENCE.txt for licence details.
 */
declare(strict_types=1);

namespace Magento\CustomAdminLogo\Model\Config\Backend;

use Magento\Config\Model\Config\Backend\Image;

class AdminMenuLogo extends Image
{
    public const UPLOAD_DIR = 'admin/logo/custom/menu';

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
