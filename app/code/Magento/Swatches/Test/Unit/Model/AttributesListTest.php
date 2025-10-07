<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Swatches\Test\Unit\Model;

use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory;
use Magento\Catalog\Test\Unit\Helper\AttributeTestHelper;
use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Swatches\Helper\Data;
use Magento\Swatches\Model\AttributesList;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AttributesListTest extends TestCase
{
    /**
     * @var AttributesList
     */
    protected $attributeListModel;

    /**
     * @var Collection|MockObject
     */
    protected $collectionMock;

    /**
     * @var Attribute|MockObject
     */
    protected $attributeMock;

    /** @var Data|MockObject */
    protected $swatchHelper;

    protected function setUp(): void
    {
        $this->swatchHelper = $this->createMock(Data::class);

        $this->collectionMock = $this->createMock(
            Collection::class
        );

        /** @var  CollectionFactory $collectionFactoryMock */
        $collectionFactoryMock = $this->createPartialMock(
            CollectionFactory::class,
            ['create']
        );
        $collectionFactoryMock->expects($this->once())->method('create')->willReturn($this->collectionMock);

        $this->attributeMock = new AttributeTestHelper();
        $this->collectionMock
            ->expects($this->once())
            ->method('getItems')
            ->willReturn(['id' => $this->attributeMock]);

        $this->attributeListModel = new AttributesList(
            $collectionFactoryMock,
            $this->swatchHelper
        );
    }

    public function testGetAttributes()
    {
        $ids = [1, 2, 3];
        $result = [
            [
                'id' => 'id',
                'label' => 'label',
                'code' => 'code',
                'options' => ['options'],
                'canCreateOption' => false
            ]
        ];

        $this->collectionMock
            ->expects($this->any())
            ->method('addFieldToFilter')
            ->with('main_table.attribute_id', $ids);

        $this->swatchHelper->expects($this->once())->method('isSwatchAttribute')
            ->with($this->attributeMock)
            ->willReturn(true);

        $this->assertEquals($result, $this->attributeListModel->getAttributes($ids));
    }
}
