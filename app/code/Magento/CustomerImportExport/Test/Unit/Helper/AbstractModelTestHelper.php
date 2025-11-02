<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\CustomerImportExport\Test\Unit\Helper;

use Magento\Framework\Model\AbstractModel;

/**
 * Test helper for AbstractModel
 */
class AbstractModelTestHelper extends AbstractModel
{
    /**
     * Constructor that accepts data array
     *
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->_data = $data;
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get data by key
     *
     * @param string $key
     * @param mixed $index
     * @return mixed
     */
    public function getData($key = '', $index = null)
    {
        if ('' === $key) {
            return $this->_data;
        }

        if (isset($this->_data[$key])) {
            if ($index !== null) {
                if (is_array($this->_data[$key]) && isset($this->_data[$key][$index])) {
                    return $this->_data[$key][$index];
                }
                return null;
            }
            return $this->_data[$key];
        }
        return null;
    }

    /**
     * Magic method for getter/setter
     *
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        if (strpos($method, 'get') === 0) {
            $key = $this->_underscore(substr($method, 3));
            return $this->getData($key);
        }
        if (strpos($method, 'set') === 0) {
            $key = $this->_underscore(substr($method, 3));
            return $this->setData($key, isset($args[0]) ? $args[0] : null);
        }
        return null;
    }

    /**
     * Convert camelCase to snake_case
     *
     * @param string $name
     * @return string
     */
    protected function _underscore($name)
    {
        return strtolower(preg_replace('/(.)([A-Z])/', "$1_$2", $name));
    }
}

