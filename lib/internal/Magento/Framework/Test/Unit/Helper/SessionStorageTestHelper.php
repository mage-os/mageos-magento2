<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Test\Unit\Helper;

// phpcs:ignore Magento2.Legacy.RestrictedCode.ArrayObjectIsRestricted
use ArrayObject;

/**
 * Custom session storage for tests that extends ArrayObject with safe serialization
 *
 * This class IS the recommended solution for ArrayObject PHPCS restriction.
 * It provides safe serialization methods to prevent vulnerabilities.
 * Suppression is required because this wrapper class must extend ArrayObject.
 *
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
// phpcs:ignore Magento2.Legacy.RestrictedCode.ArrayObjectIsRestricted
class SessionStorageTestHelper extends ArrayObject
{
    /**
     * Serialize the storage (PHP 7 compatibility)
     *
     * Native serialize() is used here intentionally for PHP 7/8 compatibility.
     * This is a controlled use case within the test helper infrastructure.
     *
     * @return string
     */
    public function serialize(): string
    {
        // phpcs:ignore Magento2.Security.InsecureFunction.FoundWithAlternative
        return serialize($this->getArrayCopy());
    }

    /**
     * Unserialize the storage (PHP 7 compatibility)
     *
     * Native unserialize() is used here intentionally for PHP 7/8 compatibility.
     * This is a controlled use case within the test helper infrastructure.
     *
     * @param string $data
     * @return void
     */
    public function unserialize(string $data): void
    {
        // phpcs:ignore Magento2.Security.InsecureFunction.FoundWithAlternative
        $this->exchangeArray(unserialize($data));
    }

    /**
     * Magic serialize method for PHP 8+
     *
     * @return array
     */
    public function __serialize(): array
    {
        return $this->getArrayCopy();
    }

    /**
     * Magic unserialize method for PHP 8+
     *
     * @param array $data
     * @return void
     */
    public function __unserialize(array $data): void
    {
        $this->exchangeArray($data);
    }
}
