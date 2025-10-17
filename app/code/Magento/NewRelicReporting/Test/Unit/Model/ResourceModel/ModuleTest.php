<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\NewRelicReporting\Test\Unit\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\NewRelicReporting\Model\ResourceModel\Module;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * Unit test for Module ResourceModel
 *
 * @covers \Magento\NewRelicReporting\Model\ResourceModel\Module
 */
class ModuleTest extends TestCase
{
    /**
     * @var Module
     */
    private Module $resourceModel;

    /**
     * @return void
     * @throws Exception
     */
    protected function setUp(): void
    {
        $context = $this->createMock(Context::class);
        $this->resourceModel = new Module($context);
    }

    /**
     * Test that the ResourceModel extends AbstractDb
     *
     * @return void
     */
    public function testExtendsAbstractDb(): void
    {
        $this->assertInstanceOf(AbstractDb::class, $this->resourceModel);
    }

    /**
     * Test that _construct initializes main table and primary key correctly
     *
     * @return void
     * @throws \ReflectionException
     */
    public function testInitializesMainTableAndPrimaryKey(): void
    {
        $reflection = new ReflectionClass($this->resourceModel);

        $mainTableProperty = $reflection->getProperty('_mainTable');
        $mainTableProperty->setAccessible(true);
        $this->assertEquals('reporting_module_status', $mainTableProperty->getValue($this->resourceModel));

        $idFieldNameProperty = $reflection->getProperty('_idFieldName');
        $idFieldNameProperty->setAccessible(true);
        $this->assertEquals('entity_id', $idFieldNameProperty->getValue($this->resourceModel));
    }
}
