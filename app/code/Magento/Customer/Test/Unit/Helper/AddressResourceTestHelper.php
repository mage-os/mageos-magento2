<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Helper;

use Magento\Customer\Model\ResourceModel\Address;
use Magento\Eav\Model\Entity\AttributeLoaderInterface;
use ReflectionClass;
use ReflectionException;

/**
 * Helper class for testing Address Resource Model without ObjectManager dependencies
 */
class AddressResourceTestHelper extends Address
{
    /**
     * @var AttributeLoaderInterface
     */
    protected $attributeLoader;

    /**
     * Constructor that uses reflection to bypass parent constructor
     *
     * @param array $dependencies
     * @throws ReflectionException
     */
    public function __construct(array $dependencies = [])
    {
        // Use reflection to set properties without calling parent constructor
        foreach ($dependencies as $propertyName => $value) {
            $this->setPrivateProperty($propertyName, $value);
        }
        
        // Set connectionName which is normally set in _construct()
        $this->setPrivateProperty('connectionName', 'customer');
    }

    /**
     * Set private/protected property using reflection
     *
     * @param string $propertyName
     * @param mixed $value
     * @return void
     * @throws ReflectionException
     */
    private function setPrivateProperty(string $propertyName, $value): void
    {
        $reflection = new ReflectionClass($this);
        
        // Try to find property in current class or parent classes
        while ($reflection) {
            if ($reflection->hasProperty($propertyName)) {
                $property = $reflection->getProperty($propertyName);
                $property->setAccessible(true);
                $property->setValue($this, $value);
                return;
            }
            $reflection = $reflection->getParentClass();
        }
    }

    /**
     * Override to allow setting attribute loader for testing
     *
     * @param AttributeLoaderInterface $attributeLoader
     * @return void
     */
    public function setAttributeLoader(AttributeLoaderInterface $attributeLoader): void
    {
        $this->attributeLoader = $attributeLoader;
    }

    /**
     * Override getAttributeLoader for testing
     *
     * @return AttributeLoaderInterface
     */
    protected function getAttributeLoader()
    {
        return $this->attributeLoader;
    }

    /**
     * Override loadAllAttributes to use the test attribute loader
     *
     * @param object|null $object
     * @return $this
     */
    public function loadAllAttributes($object = null)
    {
        if ($this->attributeLoader) {
            return $this->attributeLoader->loadAllAttributes($this, $object);
        }
        return $this;
    }
}
