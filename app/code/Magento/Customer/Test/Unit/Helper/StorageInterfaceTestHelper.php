<?php
/**
 * Copyright 2016 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Helper;

use Magento\Framework\Session\StorageInterface;

/**
 * Test helper for StorageInterface with custom methods
 */
class StorageInterfaceTestHelper implements StorageInterface
{
    /**
     * @var array<string, mixed>
     */
    private array $testData = [];

    /**
     * Get data
     *
     * @param string|null $key
     * @return mixed
     */
    public function getData($key = null)
    {
        if ($key === null) {
            return $this->testData;
        }
        return $this->testData[$key] ?? null;
    }

    /**
     * Set data
     *
     * @param string|array $key
     * @param mixed $value
     * @return $this
     */
    public function setData($key, $value = null)
    {
        if (is_array($key)) {
            $this->testData = $key;
        } else {
            $this->testData[$key] = $value;
        }
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function init($namespace)
    {
        // Stub implementation
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function unsetData($key = null)
    {
        if ($key === null) {
            $this->testData = [];
        } else {
            unset($this->testData[$key]);
        }
    }

    /**
     * @inheritdoc
     */
    public function getNamespace()
    {
        return 'test_namespace';
    }
}
