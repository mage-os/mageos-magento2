<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Model\Product\CopyConstructor;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\CopyConstructor\Related;
use Magento\Catalog\Model\Product\Link;
use Magento\Catalog\Model\ResourceModel\Product\Link\Collection;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use Magento\Framework\TestFramework\Unit\Helper\MockCreationTrait;
use PHPUnit\Framework\TestCase;

class RelatedTest extends TestCase
{

    use MockCreationTrait;
    /**
     * @var \\Magento\Catalog\Model\Product\CopyConstructor\Related
     */
    protected $_model;

    /**
     * @var MockObject
     */
    protected $_productMock;

    /**
     * @var MockObject
     */
    protected $_duplicateMock;

    /**
     * @var MockObject
     */
    protected $_linkMock;

    /**
     * @var MockObject
     */
    protected $_linkCollectionMock;

    protected function setUp(): void
    {
        $this->_model = new Related();

        $this->_productMock = $this->createMock(Product::class);

        $this->_duplicateMock = $this->createPartialMockWithReflection(
            Product::class,
            ['setRelatedLinkData']
        );

        $this->_linkMock = $this->createPartialMockWithReflection(
            Link::class,
            ['setAttributes', 'getAttributes']
        );

        $this->_productMock->method('getLinkInstance')->willReturn(
            $this->_linkMock
        );
    }

    public function testBuild()
    {
        $helper = new ObjectManager($this);
        $expectedData = ['100500' => ['some' => 'data']];

        $attributes = ['attributeOne' => ['code' => 'one'], 'attributeTwo' => ['code' => 'two']];

        $this->_linkMock->method('setAttributes')->willReturnSelf();


        $this->_linkMock->method('getAttributes')->willReturn($attributes);


        $this->_linkMock->setAttributes($attributes);

        $productLinkMock = $this->createPartialMockWithReflection(
            Link::class,
            ['setLinkedProductId', 'getLinkedProductId', 'setArrayData', 'toArray']
        );


        $productLinkMock->method('setLinkedProductId')->willReturnSelf();


        $productLinkMock->method('getLinkedProductId')->willReturn('100500');


        $productLinkMock->method('toArray')->willReturn(['some' => 'data']);


        $productLinkMock->setLinkedProductId('100500');

        $collectionMock = $helper->getCollectionMock(
            Collection::class,
            [$productLinkMock]
        );
        $this->_productMock->expects(
            $this->once()
        )->method(
            'getRelatedLinkCollection'
        )->willReturn(
            $collectionMock
        );

        $this->_duplicateMock->method('setRelatedLinkData')->willReturnSelf();


        $this->_duplicateMock->setRelatedLinkData($expectedData);

        $this->_model->build($this->_productMock, $this->_duplicateMock);
    }
}
