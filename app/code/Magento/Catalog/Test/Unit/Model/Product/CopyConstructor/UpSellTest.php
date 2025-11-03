<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Model\Product\CopyConstructor;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\CopyConstructor\UpSell;
use Magento\Catalog\Model\Product\Link;
use Magento\Catalog\Model\ResourceModel\Product\Link\Collection;
use Magento\Catalog\Test\Unit\Helper\LinkTestHelper;
use Magento\Catalog\Test\Unit\Helper\ProductLinkTestHelper;
use Magento\Catalog\Test\Unit\Helper\ProductTestHelper;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class UpSellTest extends TestCase
{
    /**
     * @var UpSell
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
        $this->_model = new UpSell();

        $this->_productMock = $this->createMock(Product::class);

        $this->_duplicateMock = new ProductTestHelper();

        $this->_linkMock = new LinkTestHelper();

        $this->_productMock->method('getLinkInstance')->willReturn(
            $this->_linkMock
        );
    }

    public function testBuild()
    {
        $helper = new ObjectManager($this);
        $expectedData = ['100500' => ['some' => 'data']];

        $attributes = ['attributeOne' => ['code' => 'one'], 'attributeTwo' => ['code' => 'two']];

        $this->_linkMock->setAttributes($attributes);

        $productLinkMock = new ProductLinkTestHelper();

        $productLinkMock->setLinkedProductId('100500');
        $productLinkMock->setArrayData(['some' => 'data']);

        $collectionMock = $helper->getCollectionMock(
            Collection::class,
            [$productLinkMock]
        );
        $this->_productMock->expects(
            $this->once()
        )->method(
            'getUpSellLinkCollection'
        )->willReturn(
            $collectionMock
        );

        $this->_duplicateMock->setUpSellLinkData($expectedData);

        $this->_model->build($this->_productMock, $this->_duplicateMock);
    }
}
