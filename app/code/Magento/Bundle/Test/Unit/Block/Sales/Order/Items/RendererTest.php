<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Bundle\Test\Unit\Block\Sales\Order\Items;

use PHPUnit\Framework\Attributes\DataProvider;
use Magento\Sales\Model\Order;
use Magento\Bundle\Block\Sales\Order\Items\Renderer;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Item;
use Magento\Sales\Model\Order\Shipment;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RendererTest extends TestCase
{
    /** @var Item|MockObject */
    protected $orderItem;

    /** @var Renderer $model */
    protected $model;

    /** @var Json|MockObject $serializer */
    protected $serializer;

    protected function setUp(): void
    {
        /** @var Item $orderItem */
        $this->orderItem = new class extends Item {
            private $orderItem = null;
            private $orderItemId = null;
            private $productOptions = [];
            private $parentItem = null;
            private $id = null;
            
            public function __construct() {}
            
            public function getOrderItem() { return $this->orderItem; }
            public function setOrderItem($orderItem) { $this->orderItem = $orderItem; return $this; }
            public function getOrderItemId() { return $this->orderItemId; }
            public function setOrderItemId($orderItemId) { $this->orderItemId = $orderItemId; return $this; }
            public function getProductOptions() { return $this->productOptions; }
            public function setProductOptions(?array $options = null) { $this->productOptions = $options; return $this; }
            public function getParentItem() { return $this->parentItem; }
            public function setParentItem($parentItem) { $this->parentItem = $parentItem; return $this; }
            public function getId() { return $this->id; }
            public function setId($id) { $this->id = $id; return $this; }
        };

        $this->serializer = $this->createMock(Json::class);
        $objectManager = new ObjectManager($this);
        $this->model = $objectManager->getObject(
            Renderer::class,
            ['serializer' => $this->serializer]
        );
    }

    #[DataProvider('getChildrenEmptyItemsDataProvider')]
    public function testGetChildrenEmptyItems($class, $method, $returnClass)
    {
        if ($returnClass === Invoice::class) {
            /** @var Invoice $salesModel */
            $salesModel = new class extends Invoice {
                private $allItems = [];
                
                public function __construct() {}
                
                public function getAllItems() { return $this->allItems; }
                public function setAllItems($allItems) { $this->allItems = $allItems; return $this; }
            };
        } elseif ($returnClass === Shipment::class) {
            /** @var Shipment $salesModel */
            $salesModel = new class extends Shipment {
                private $allItems = [];
                
                public function __construct() {}
                
                public function getAllItems() { return $this->allItems; }
                public function setAllItems($allItems) { $this->allItems = $allItems; return $this; }
            };
        } else {
            /** @var Creditmemo $salesModel */
            $salesModel = new class extends Creditmemo {
                private $allItems = [];
                
                public function __construct() {}
                
                public function getAllItems() { return $this->allItems; }
                public function setAllItems($allItems) { $this->allItems = $allItems; return $this; }
            };
        }
        $salesModel->setAllItems([]);

        if ($class === \Magento\Sales\Model\Order\Invoice\Item::class) {
            /** @var \Magento\Sales\Model\Order\Invoice\Item $item */
            $item = new class extends \Magento\Sales\Model\Order\Invoice\Item {
                private $methodResult = null;
                private $orderItem = null;
                
                public function __construct() {}
                
                public function getInvoice() { return $this->methodResult; }
                public function setMethodResult($result) { $this->methodResult = $result; return $this; }
                public function getOrderItem() { return $this->orderItem; }
                public function setOrderItem($orderItem) { $this->orderItem = $orderItem; return $this; }
            };
        } elseif ($class === \Magento\Sales\Model\Order\Shipment\Item::class) {
            /** @var \Magento\Sales\Model\Order\Shipment\Item $item */
            $item = new class extends \Magento\Sales\Model\Order\Shipment\Item {
                private $methodResult = null;
                private $orderItem = null;
                
                public function __construct() {}
                
                public function getShipment() { return $this->methodResult; }
                public function setMethodResult($result) { $this->methodResult = $result; return $this; }
                public function getOrderItem() { return $this->orderItem; }
                public function setOrderItem($orderItem) { $this->orderItem = $orderItem; return $this; }
            };
        } else {
            /** @var \Magento\Sales\Model\Order\Creditmemo\Item $item */
            $item = new class extends \Magento\Sales\Model\Order\Creditmemo\Item {
                private $methodResult = null;
                private $orderItem = null;
                
                public function __construct() {}
                
                public function getCreditmemo() { return $this->methodResult; }
                public function setMethodResult($result) { $this->methodResult = $result; return $this; }
                public function getOrderItem() { return $this->orderItem; }
                public function setOrderItem($orderItem) { $this->orderItem = $orderItem; return $this; }
            };
        }
        $item->setMethodResult($salesModel);
        $item->setOrderItem($this->orderItem);
        $this->orderItem->setId(1);

        $this->assertNull($this->model->getChildren($item));
    }

    /**
     * @return array
     */
    public static function getChildrenEmptyItemsDataProvider()
    {
        return [
            [
                \Magento\Sales\Model\Order\Invoice\Item::class,
                'getInvoice',
                Invoice::class
            ],
            [
                \Magento\Sales\Model\Order\Shipment\Item::class,
                'getShipment',
                Shipment::class
            ],
            [
                \Magento\Sales\Model\Order\Creditmemo\Item::class,
                'getCreditmemo',
                Creditmemo::class
            ]
        ];
    }

    #[DataProvider('getChildrenDataProvider')]
    public function testGetChildren($parentItem)
    {
        if ($parentItem) {
            /** @var Item $parentItem */
            $parentItem = new class extends Item {
                private $id = null;
                
                public function __construct() {}
                
                public function getId() { return $this->id; }
                public function setId($id) { $this->id = $id; return $this; }
            };
            $parentItem->setId(1);
        }
        $this->orderItem->setOrderItem($this->orderItem);
        $this->orderItem->setParentItem($parentItem);
        $this->orderItem->setOrderItemId(2);
        $this->orderItem->setId(1);

        /** @var Invoice $salesModel */
        $salesModel = new class extends Invoice {
            private $allItems = [];
            
            public function __construct() {}
            
            public function getAllItems() { return $this->allItems; }
            public function setAllItems($allItems) { $this->allItems = $allItems; return $this; }
        };
        $salesModel->setAllItems([$this->orderItem]);

        /** @var \Magento\Sales\Model\Order\Invoice\Item $item */
        $item = new class extends \Magento\Sales\Model\Order\Invoice\Item {
            private $invoice = null;
            private $orderItem = null;
            
            public function __construct() {}
            
            public function getInvoice() { return $this->invoice; }
            public function setInvoice($invoice) { $this->invoice = $invoice; return $this; }
            public function getOrderItem() { return $this->orderItem; }
            public function setOrderItem($orderItem) { $this->orderItem = $orderItem; return $this; }
        };
        $item->setInvoice($salesModel);
        $item->setOrderItem($this->orderItem);

        $this->assertSame([2 => $this->orderItem], $this->model->getChildren($item));
    }

    /**
     * @return array
     */
    public static function getChildrenDataProvider()
    {
        return [
            [true],
            [false],
        ];
    }

    #[DataProvider('isShipmentSeparatelyWithoutItemDataProvider')]
    public function testIsShipmentSeparatelyWithoutItem($productOptions, $result)
    {
        $this->model->setItem($this->orderItem);
        $this->orderItem->setProductOptions($productOptions);

        $this->assertSame($result, $this->model->isShipmentSeparately());
    }

    /**
     * @return array
     */
    public static function isShipmentSeparatelyWithoutItemDataProvider()
    {
        return [
            [['shipment_type' => 1], true],
            [['shipment_type' => 0], false],
            [[], false]
        ];
    }

    #[DataProvider('isShipmentSeparatelyWithItemDataProvider')]
    public function testIsShipmentSeparatelyWithItem($productOptions, $result, $parentItem)
    {
        if ($parentItem) {
            /** @var Item $parentItem */
            $parentItem = new class extends Item {
                private $productOptions = [];
                
                public function __construct() {}
                
                public function getProductOptions() { return $this->productOptions; }
                public function setProductOptions(?array $options = null) { $this->productOptions = $options; return $this; }
            };
            $parentItem->setProductOptions($productOptions);
        } else {
            $this->orderItem->setProductOptions($productOptions);
        }
        $this->orderItem->setParentItem($parentItem);
        $this->orderItem->setOrderItem($this->orderItem);

        $this->assertSame($result, $this->model->isShipmentSeparately($this->orderItem));
    }

    /**
     * @return array
     */
    public static function isShipmentSeparatelyWithItemDataProvider()
    {
        return [
            [['shipment_type' => 1], false, false],
            [['shipment_type' => 0], true, false],
            [['shipment_type' => 1], true, true],
            [['shipment_type' => 0], false, true],
        ];
    }

    #[DataProvider('isChildCalculatedWithoutItemDataProvider')]
    public function testIsChildCalculatedWithoutItem($productOptions, $result)
    {
        $this->model->setItem($this->orderItem);
        $this->orderItem->setProductOptions($productOptions);

        $this->assertSame($result, $this->model->isChildCalculated());
    }

    /**
     * @return array
     */
    public static function isChildCalculatedWithoutItemDataProvider()
    {
        return [
            [['product_calculations' => 0], true],
            [['product_calculations' => 1], false],
            [[], false],
        ];
    }

    #[DataProvider('isChildCalculatedWithItemDataProvider')]
    public function testIsChildCalculatedWithItem($productOptions, $result, $parentItem)
    {
        if ($parentItem) {
            /** @var Item $parentItem */
            $parentItem = new class extends Item {
                private $productOptions = [];
                
                public function __construct() {}
                
                public function getProductOptions() { return $this->productOptions; }
                public function setProductOptions(?array $options = null) { $this->productOptions = $options; return $this; }
            };
            $parentItem->setProductOptions($productOptions);
        } else {
            $this->orderItem->setProductOptions($productOptions);
        }
        $this->orderItem->setParentItem($parentItem);
        $this->orderItem->setOrderItem($this->orderItem);

        $this->assertSame($result, $this->model->isChildCalculated($this->orderItem));
    }

    /**
     * @return array
     */
    public static function isChildCalculatedWithItemDataProvider()
    {
        return [
            [['product_calculations' => 0], false, false],
            [['product_calculations' => 1], true, false],
            [['product_calculations' => 0], true, true],
            [['product_calculations' => 1], false, true],
        ];
    }

    public function testGetSelectionAttributes()
    {
        $this->orderItem->setProductOptions([]);
        $this->assertNull($this->model->getSelectionAttributes($this->orderItem));
    }

    public function testGetSelectionAttributesWithBundle()
    {
        $bundleAttributes = 'Serialized value';
        $options = ['bundle_selection_attributes' => $bundleAttributes];
        $unserializedResult = 'result of "bundle_selection_attributes" unserialization';

        $this->serializer->expects($this->any())
            ->method('unserialize')
            ->with($bundleAttributes)
            ->willReturn($unserializedResult);
        $this->orderItem->setProductOptions($options);

        $this->assertEquals($unserializedResult, $this->model->getSelectionAttributes($this->orderItem));
    }

    #[DataProvider('canShowPriceInfoDataProvider')]
    public function testCanShowPriceInfo($parentItem, $productOptions, $result)
    {
        $this->model->setItem($this->orderItem);
        $this->orderItem->setOrderItem($this->orderItem);
        $this->orderItem->setParentItem($parentItem);
        $this->orderItem->setProductOptions($productOptions);

        $this->assertSame($result, $this->model->canShowPriceInfo($this->orderItem));
    }

    /**
     * @return array
     */
    public static function canShowPriceInfoDataProvider()
    {
        return [
            [true, ['product_calculations' => 0], true],
            [false, [], true],
            [false, ['product_calculations' => 0], false],
        ];
    }

    #[DataProvider('getValueHtmlWithAttributesDataProvider')]
    public function testGetValueHtmlWithAttributes($qty)
    {
        $price = 100;
        /** @var Order $orderModel */
        $orderModel = new class extends Order {
            private $formatPriceResult = '';
            
            public function __construct() {}
            
            public function formatPrice($price, $addBrackets = false) { return $this->formatPriceResult; }
            public function setFormatPriceResult($result) { $this->formatPriceResult = $result; return $this; }
        };
        $orderModel->setFormatPriceResult($price);

        /** @var Renderer $model */
        $model = new class extends Renderer {
            private $order = null;
            private $selectionAttributes = [];
            private $escapeHtmlResult = '';
            
            public function __construct() {}
            
            public function getOrder() { return $this->order; }
            public function setOrder($order) { $this->order = $order; return $this; }
            public function getSelectionAttributes($item) { return $this->selectionAttributes; }
            public function setSelectionAttributes($attributes) { $this->selectionAttributes = $attributes; return $this; }
            public function escapeHtml($data, $allowedTags = null) { return $this->escapeHtmlResult; }
            public function setEscapeHtmlResult($result) { $this->escapeHtmlResult = $result; return $this; }
        };
        $model->setEscapeHtmlResult('Test');
        $model->setOrder($orderModel);
        $model->setSelectionAttributes([
                'qty' => $qty ,
                'price' => $price,
            ]);
        $this->assertSame($qty . ' x Test ' . $price, $model->getValueHtml($this->orderItem));
    }

    /**
     * @return array
     */
    public static function getValueHtmlWithAttributesDataProvider()
    {
        return [
            [1],
            [1.5],
        ];
    }
}
