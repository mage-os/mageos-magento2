<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogImportExport\Test\Unit\Model\Import\Product;

use Magento\Tax\Model\ResourceModel\TaxClass\CollectionFactory;
use Magento\Tax\Model\ClassModelFactory;
use Magento\CatalogImportExport\Model\Import\Product\TaxClassProcessor;
use Magento\CatalogImportExport\Model\Import\Product\Type\AbstractType;

use Magento\Tax\Model\ClassModel;
use Magento\Tax\Model\ResourceModel\TaxClass\Collection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TaxClassProcessorTest extends TestCase
{
    const TEST_TAX_CLASS_NAME = 'className';

    const TEST_TAX_CLASS_ID = 1;

    const TEST_JUST_CREATED_TAX_CLASS_ID = 2;



    /**
     * @var TaxClassProcessor|MockObject
     */
    protected $taxClassProcessor;

    /**
     * @var AbstractType
     */
    protected $product;

    protected function setUp(): void
    {
        // Create minimal ObjectManager mock
        $objectManagerMock = $this->createMock(\Magento\Framework\ObjectManagerInterface::class);
        \Magento\Framework\App\ObjectManager::setInstance($objectManagerMock);

        $taxClass = $this->createMock(ClassModel::class);
        $taxClass->method('getClassName')->willReturn(self::TEST_TAX_CLASS_NAME);
        $taxClass->method('getId')->willReturn(self::TEST_TAX_CLASS_ID);

        $taxClassCollection = $this->createPartialMock(
            Collection::class,
            ['getIterator', 'addFieldToFilter', 'getSize', 'getFirstItem']
        );
        $taxClassCollection->method('getIterator')
            ->willReturn(new \ArrayIterator([$taxClass]));
        $taxClassCollection->method('addFieldToFilter')->willReturnSelf();
        $taxClassCollection->method('getSize')->willReturn(1);
        $taxClassCollection->method('getFirstItem')->willReturn($taxClass);

        $taxClassCollectionFactory = $this->createPartialMock(
            CollectionFactory::class,
            ['create']
        );

        $taxClassCollectionFactory->method('create')->willReturn($taxClassCollection);

        $anotherTaxClass = $this->createMock(ClassModel::class);
        $anotherTaxClass->method('getClassName')->willReturn(self::TEST_TAX_CLASS_NAME);
        $anotherTaxClass->method('getId')->willReturn(self::TEST_JUST_CREATED_TAX_CLASS_ID);

        $taxClassFactory = $this->createPartialMock(ClassModelFactory::class, ['create']);

        $taxClassFactory->method('create')->willReturn($anotherTaxClass);

        $this->taxClassProcessor =
            new TaxClassProcessor(
                $taxClassCollectionFactory,
                $taxClassFactory
            );

        $this->product = $this->createMock(AbstractType::class);
    }

    public function testUpsertTaxClassExist()
    {
        $taxClassId = $this->taxClassProcessor->upsertTaxClass(self::TEST_TAX_CLASS_NAME, $this->product);
        $this->assertEquals(self::TEST_TAX_CLASS_ID, $taxClassId);
    }

    public function testUpsertTaxClassNotExist()
    {
        $taxClassId = $this->taxClassProcessor->upsertTaxClass('noExistClassName', $this->product);
        $this->assertEquals(self::TEST_JUST_CREATED_TAX_CLASS_ID, $taxClassId);
    }

    public function testUpsertTaxClassExistCaseInsensitive()
    {
        $taxClassId = $this->taxClassProcessor->upsertTaxClass(strtoupper(self::TEST_TAX_CLASS_NAME), $this->product);
        $this->assertEquals(self::TEST_TAX_CLASS_ID, $taxClassId);
    }

    public function testUpsertTaxClassNone()
    {
        $taxClassId = $this->taxClassProcessor->upsertTaxClass('none', $this->product);
        $this->assertEquals(0, $taxClassId);
    }

    public function testUpsertTaxClassZero()
    {
        $taxClassId = $this->taxClassProcessor->upsertTaxClass(0, $this->product);
        $this->assertEquals(0, $taxClassId);
    }
}
