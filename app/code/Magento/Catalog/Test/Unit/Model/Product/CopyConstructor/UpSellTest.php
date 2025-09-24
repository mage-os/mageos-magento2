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

        $this->_duplicateMock = new class extends Product {
            private $upSellLinkData = null;
            
            public function __construct()
            {
                // Don't call parent constructor to avoid dependencies
            }
            
            public function setUpSellLinkData($data)
            {
                $this->upSellLinkData = $data;
                return $this;
            }
            
            public function getUpSellLinkData()
            {
                return $this->upSellLinkData;
            }
        };

        $this->_linkMock = new class extends Link {
            private $upSellLinkCollection = null;
            private $attributes = null;
            private $useUpSellLinksResult = null;
            
            public function __construct()
            {
                // Don't call parent constructor to avoid dependencies
            }
            
            public function getUpSellLinkCollection()
            {
                return $this->upSellLinkCollection;
            }
            
            public function setUpSellLinkCollection($collection)
            {
                $this->upSellLinkCollection = $collection;
                return $this;
            }
            
            public function getAttributes($type = null)
            {
                return $this->attributes;
            }
            
            public function setAttributes($attributes)
            {
                $this->attributes = $attributes;
                return $this;
            }
            
            public function useUpSellLinks()
            {
                return $this->useUpSellLinksResult ?: $this;
            }
            
            public function setUseUpSellLinksResult($result)
            {
                $this->useUpSellLinksResult = $result;
                return $this;
            }
        };

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

        $productLinkMock = new class extends \Magento\Catalog\Model\ResourceModel\Product\Link {
            private $linkedProductId = null;
            private $arrayData = null;
            
            public function __construct()
            {
                // Don't call parent constructor to avoid dependencies
            }
            
            public function getLinkedProductId()
            {
                return $this->linkedProductId;
            }
            
            public function setLinkedProductId($id)
            {
                $this->linkedProductId = $id;
                return $this;
            }
            
            public function toArray($keys = null)
            {
                return $this->arrayData;
            }
            
            public function setArrayData($data)
            {
                $this->arrayData = $data;
                return $this;
            }
        };

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
