<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Test\Unit\Helper;

use Magento\Framework\DataObject;

class DataObjectTestHelper extends DataObject
{
    /**
     * @var callable|null
     */
    private $getDataCallback = null;

    public function setGetDataCallback($callback)
    {
        $this->getDataCallback = $callback;
        return $this;
    }

    public function getData($key = '', $index = null)
    {
        if ($this->getDataCallback !== null) {
            return call_user_func($this->getDataCallback, $key);
        }
        return parent::getData($key, $index);
    }
}

