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
use Magento\Eav\Test\Unit\Helper\AttributeSetInterfaceTestHelper;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Provide tests for RemoveProducts plugin.
 */
class RemoveProductsTest extends TestCase
{
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
        $this->collectionFactory = $this->getMockBuilder(CollectionFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['create'])
            ->getMock();
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
        $collection = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()
            ->getMock();
        $collection->expects(self::once())
            ->method('addFieldToFilter')
            ->with(self::identicalTo('attribute_set_id'), self::identicalTo(['eq' => $attributeSetId]));
        $collection->expects(self::once())
            ->method('delete');

        $this->collectionFactory->expects(self::once())
            ->method('create')
            ->willReturn($collection);

        /** @var AttributeSetRepositoryInterface|MockObject $attributeSetRepository */
        $attributeSetRepository = $this->getMockBuilder(AttributeSetRepositoryInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var AttributeSetInterface|MockObject $attributeSet */
        $attributeSet = new AttributeSetInterfaceTestHelper();
        $attributeSet->setIdReturn($attributeSetId);

        self::assertTrue($this->testSubject->afterDelete($attributeSetRepository, true, $attributeSet));
    }
}
