<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Controller\Adminhtml\Category\RefreshPath;

/**
 * @SuppressWarnings(PHPMD.UselessOverridingMethod)
 */
class RefreshPathTestHelper extends RefreshPath
{
    /**
     * @var mixed
     */
    private $requestMock = null;

    public function setRequestMock($requestMock)
    {
        $this->requestMock = $requestMock;
        return $this;
    }

    public function getRequest()
    {
        if ($this->requestMock !== null) {
            return $this->requestMock;
        }
        return parent::getRequest();
    }
}

