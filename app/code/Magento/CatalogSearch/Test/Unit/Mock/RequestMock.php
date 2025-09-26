<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogSearch\Test\Unit\Mock;

use Magento\Framework\App\Console\Request;

/**
 * Mock class for Request with additional methods
 */
class RequestMock extends Request
{
    /**
     * Mock method for getQueryValue
     *
     * @return mixed
     */
    public function getQueryValue()
    {
        return $this->queryValue;
    }

    /**
     * Set the query value
     *
     * @param mixed $value
     * @return $this
     */
    public function setQueryValue($value)
    {
        $this->queryValue = $value;
        return $this;
    }

    /**
     * Required method from Request
     */
    protected function _construct(): void
    {
        // Mock implementation
    }
}
