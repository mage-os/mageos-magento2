<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\NewRelicReporting\Test\Unit\Model\ResourceModel\Module;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\NewRelicReporting\Model\Module;
use Magento\NewRelicReporting\Model\ResourceModel\Module as ModuleResource;
use Magento\NewRelicReporting\Model\ResourceModel\Module\Collection;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionException;

/**
 * Unit test for Module Collection
 *
 * @covers \Magento\NewRelicReporting\Model\ResourceModel\Module\Collection
 */
class CollectionTest extends TestCase
{
    /**
     * Test that Collection extends AbstractCollection
     *
     * @return void
     */
    public function testExtendsAbstractCollection(): void
    {
        $reflection = new ReflectionClass(Collection::class);
        $this->assertTrue($reflection->isSubclassOf(AbstractCollection::class));
    }

    /**
     * Test that _construct calls _init with correct model and resource model
     *
     * @return void
     * @throws ReflectionException
     */
    public function testConstructInitializesCorrectModelAndResource(): void
    {
        // Create a partial mock that only mocks the _init method
        $collection = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['_init'])
            ->getMock();

        // Verify _init is called once with the correct parameters
        $collection->expects($this->once())
            ->method('_init')
            ->with(
                $this->equalTo(Module::class),
                $this->equalTo(ModuleResource::class)
            );

        // Call the protected _construct method via reflection
        $reflection = new ReflectionClass($collection);
        $constructMethod = $reflection->getMethod('_construct');
        $constructMethod->setAccessible(true);
        $constructMethod->invoke($collection);
    }
}
