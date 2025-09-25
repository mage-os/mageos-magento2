<?php
/**
 * Copyright 2016 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Mock;

use Magento\Backend\Model\View\Result\Redirect;

/**
 * Mock class for ResultRedirect with additional methods
 */
class ResultRedirectMock extends Redirect
{
    /**
     * Mock method for setData
     *
     * @param array $data
     * @return $this
     */
    public function setData(array $data): self
    {
        return $this;
    }
}
