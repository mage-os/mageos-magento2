<?php
/**
 * Copyright 2017 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Plugin\Model\AttributeSetRepository;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Plugin\Model\AttributeSetRepository\RemoveProducts;
use Magento\Eav\Api\AttributeSetRepositoryInterface;
use Magento\Eav\Api\Data\AttributeSetInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\TestFramework\Unit\Helper\MockCreationTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Provide tests for RemoveProducts plugin.
 */
class RemoveProductsTest extends TestCase
{
    use MockCreationTrait;
    /**
     * @var RemoveProducts
     */
    private $testSubject;

    /**
     * @var CollectionFactory|MockObject
     */
    private $collectionFactory;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $this->collectionFactory = $this->createPartialMock(CollectionFactory::class, ['create']);
        $this->testSubject = $objectManager->getObject(
            RemoveProducts::class,
            [
                'collectionFactory' => $this->collectionFactory,
            ]
        );
    }

    /**
     * Test plugin will delete all related products for given attribute set.
     */
    public function testAfterDelete()
    {
        $attributeSetId = '1';

        /** @var Collection|MockObject $collection */
        $collection = $this->createMock(Collection::class);
        $collection->expects(self::once())
            ->method('addFieldToFilter')
            ->with(self::identicalTo('attribute_set_id'), self::identicalTo(['eq' => $attributeSetId]));
        $collection->expects(self::once())
            ->method('delete');

        $this->collectionFactory->expects(self::once())
            ->method('create')
            ->willReturn($collection);

        /** @var AttributeSetRepositoryInterface|MockObject $attributeSetRepository */
        $attributeSetRepository = $this->createMock(AttributeSetRepositoryInterface::class);

        /** @var AttributeSetInterface|MockObject $attributeSet */
        $attributeSet = $this->createPartialMockWithReflection(
            AttributeSetInterface::class,
            ['getAttributeSetId', 'setAttributeSetId', 'getAttributeSetName', 'setAttributeSetName',
             'getSortOrder', 'setSortOrder', 'getEntityTypeId', 'setEntityTypeId',
             'getExtensionAttributes', 'setExtensionAttributes', 'getId']
        );
        $attributeSet->method('getAttributeSetId')->willReturn($attributeSetId);
        $attributeSet->method('getId')->willReturn($attributeSetId);
        $attributeSet->method('setAttributeSetId')->willReturnSelf();
        $attributeSet->method('getAttributeSetName')->willReturn(null);
        $attributeSet->method('setAttributeSetName')->willReturnSelf();
        $attributeSet->method('getSortOrder')->willReturn(null);
        $attributeSet->method('setSortOrder')->willReturnSelf();
        $attributeSet->method('getEntityTypeId')->willReturn(null);
        $attributeSet->method('setEntityTypeId')->willReturnSelf();
        $attributeSet->method('getExtensionAttributes')->willReturn(null);
        $attributeSet->method('setExtensionAttributes')->willReturnSelf();

        self::assertTrue($this->testSubject->afterDelete($attributeSetRepository, true, $attributeSet));
    }
}
