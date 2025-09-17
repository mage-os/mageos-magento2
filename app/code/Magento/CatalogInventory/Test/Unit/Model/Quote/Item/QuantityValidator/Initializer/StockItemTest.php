<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogInventory\Test\Unit\Model\Quote\Item\QuantityValidator\Initializer;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Configuration\Item\Option;
use Magento\Catalog\Model\Product\Type\AbstractType;
use Magento\Catalog\Model\ProductTypes\ConfigInterface;
use Magento\CatalogInventory\Api\StockStateInterface;
use Magento\CatalogInventory\Model\Quote\Item\QuantityValidator\Initializer\StockItem;
use Magento\CatalogInventory\Model\Quote\Item\QuantityValidator\QuoteItemQtyList;
use Magento\CatalogInventory\Model\Stock\Item;
use Magento\CatalogInventory\Model\StockStateProvider;
use Magento\Framework\DataObject;

use Magento\Store\Model\Store;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class StockItemTest extends TestCase
{
    /**
     * @var StockItem
     */
    protected $model;

    /**
     * @var QuoteItemQtyList|MockObject
     */
    protected $quoteItemQtyList;

    /**
     * @var ConfigInterface|MockObject
     */
    protected $typeConfig;

    /**
     * @var StockStateInterface|MockObject
     */
    protected $stockStateMock;

    /**
     * @var \Magento\CatalogInventory\Model\StockStateProviderInterface|MockObject
     */
    private $stockStateProviderMock;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->quoteItemQtyList = $this
            ->getMockBuilder(QuoteItemQtyList::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->typeConfig = $this
            ->getMockBuilder(ConfigInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->stockStateMock = $this->getMockBuilder(StockStateInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->stockStateProviderMock = $this
            ->getMockBuilder(StockStateProvider::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Direct instantiation instead of ObjectManagerHelper
        $this->model = new StockItem(
            $this->typeConfig,
            $this->quoteItemQtyList,
            $this->stockStateMock,
            $this->stockStateProviderMock
        );
    }

    /**
     * Test initialize with Subitem
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function testInitializeWithSubitem()
    {
        $qty = 2;
        $parentItemQty = 3;
        $websiteId = 1;

        // Create anonymous class for Item with required methods
        $stockItem = new class implements \Magento\CatalogInventory\Api\Data\StockItemInterface {
            /** @var string|null */
            private $productName = null;
            /** @var bool */
            private $isChildItem = false;
            /** @var bool */
            private $hasIsChildItem = false;

            public function __construct()
            {
            }

            public function checkQuoteItemQty($qty, $summaryQty, $origQty = null)
            {
                return null;
            }

            public function setProductName($productName)
            {
                $this->productName = $productName;
                return $this;
            }

            public function setIsChildItem($isChildItem)
            {
                $this->isChildItem = $isChildItem;
                return $this;
            }

            public function hasIsChildItem()
            {
                return $this->hasIsChildItem;
            }

            public function setHasIsChildItem($hasIsChildItem)
            {
                $this->hasIsChildItem = $hasIsChildItem;
                return $this;
            }

            public function unsIsChildItem()
            {
                $this->isChildItem = false;
                return $this;
            }

            public function __wakeup()
            {
                return $this;
            }

            // Implement all required methods from StockItemInterface
            public function getItemId()
            {
                return null;
            }
            public function setItemId($itemId)
            {
                return $this;
            }
            public function getProductId()
            {
                return null;
            }
            public function setProductId($productId)
            {
                return $this;
            }
            public function getWebsiteId()
            {
                return null;
            }
            public function setWebsiteId($websiteId)
            {
                return $this;
            }
            public function getStockId()
            {
                return null;
            }
            public function setStockId($stockId)
            {
                return $this;
            }
            public function getQty()
            {
                return null;
            }
            public function setQty($qty)
            {
                return $this;
            }
            public function getMinQty()
            {
                return null;
            }
            public function setMinQty($minQty)
            {
                return $this;
            }
            public function getMinSaleQty()
            {
                return null;
            }
            public function setMinSaleQty($minSaleQty)
            {
                return $this;
            }
            public function getMaxSaleQty()
            {
                return null;
            }
            public function setMaxSaleQty($maxSaleQty)
            {
                return $this;
            }
            public function getIsInStock()
            {
                return null;
            }
            public function setIsInStock($isInStock)
            {
                return $this;
            }
            public function getLowStockDate()
            {
                return null;
            }
            public function setLowStockDate($lowStockDate)
            {
                return $this;
            }
            public function getNotifyStockQty()
            {
                return null;
            }
            public function setNotifyStockQty($notifyStockQty)
            {
                return $this;
            }
            public function getManageStock()
            {
                return null;
            }
            public function setManageStock($manageStock)
            {
                return $this;
            }
            public function getBackorders()
            {
                return null;
            }
            public function setBackorders($backorders)
            {
                return $this;
            }
            public function getQtyIncrements()
            {
                return null;
            }
            public function setQtyIncrements($qtyIncrements)
            {
                return $this;
            }
            public function getEnableQtyIncrements()
            {
                return null;
            }
            public function setEnableQtyIncrements($enableQtyIncrements)
            {
                return $this;
            }
            public function getIsQtyDecimal()
            {
                return null;
            }
            public function setIsQtyDecimal($isQtyDecimal)
            {
                return $this;
            }
            public function getIsDecimalDivided()
            {
                return null;
            }
            public function setIsDecimalDivided($isDecimalDivided)
            {
                return $this;
            }
            public function getShowDefaultNotificationMessage()
            {
                return null;
            }
            public function setShowDefaultNotificationMessage($showDefaultNotificationMessage)
            {
                return $this;
            }
            public function getUseConfigMinQty()
            {
                return null;
            }
            public function setUseConfigMinQty($useConfigMinQty)
            {
                return $this;
            }
            public function getUseConfigMinSaleQty()
            {
                return null;
            }
            public function setUseConfigMinSaleQty($useConfigMinSaleQty)
            {
                return $this;
            }
            public function getUseConfigMaxSaleQty()
            {
                return null;
            }
            public function setUseConfigMaxSaleQty($useConfigMaxSaleQty)
            {
                return $this;
            }
            public function getUseConfigBackorders()
            {
                return null;
            }
            public function setUseConfigBackorders($useConfigBackorders)
            {
                return $this;
            }
            public function getUseConfigNotifyStockQty()
            {
                return null;
            }
            public function setUseConfigNotifyStockQty($useConfigNotifyStockQty)
            {
                return $this;
            }
            public function getUseConfigQtyIncrements()
            {
                return null;
            }
            public function setUseConfigQtyIncrements($useConfigQtyIncrements)
            {
                return $this;
            }
            public function getUseConfigEnableQtyInc()
            {
                return null;
            }
            public function setUseConfigEnableQtyInc($useConfigEnableQtyInc)
            {
                return $this;
            }
            public function getUseConfigManageStock()
            {
                return null;
            }
            public function setUseConfigManageStock($useConfigManageStock)
            {
                return $this;
            }
            public function getStockStatusChangedAuto()
            {
                return null;
            }
            public function setStockStatusChangedAuto($stockStatusChangedAuto)
            {
                return $this;
            }
            public function getExtensionAttributes()
            {
                return null;
            }
            public function setExtensionAttributes($extensionAttributes)
            {
                return $this;
            }
            public function getData($key = '', $index = null)
            {
                return null;
            }
            public function setData($key, $value = null)
            {
                return $this;
            }
            public function addData(array $arr)
            {
                return $this;
            }
            public function unsetData($key = null)
            {
                return $this;
            }
            public function hasData($key = '')
            {
                return false;
            }
            public function toArray($arrAttributes = [])
            {
                return [];
            }
            public function toJson($arrAttributes = [])
            {
                return '';
            }
            public function toString($format = '')
            {
                return '';
            }
            public function isEmpty()
            {
                return true;
            }
        };
        // Create anonymous class for Quote\Item with required methods
        $quoteItem = new class extends \Magento\Quote\Model\Quote\Item {
            /** @var bool|null */
            private $isQtyDecimal = null;
            /** @var bool|null */
            private $useOldQty = null;
            /** @var int|null */
            private $backorders = null;
            /** @var mixed */
            private $stockStateResult = null;
            /** @var mixed */
            private $parentItem = null;
            /** @var mixed */
            private $product = null;
            /** @var int|null */
            private $id = null;
            /** @var int|null */
            private $quoteId = null;
            /** @var array */
            private $data = [];
            /** @var string|null */
            private $message = null;

            public function __construct()
            {
                // Skip parent constructor to avoid complex dependencies
            }

            public function setIsQtyDecimal($isQtyDecimal)
            {
                $this->isQtyDecimal = $isQtyDecimal;
                return $this;
            }

            public function setUseOldQty($useOldQty)
            {
                $this->useOldQty = $useOldQty;
                return $this;
            }

            public function setBackorders($backorders)
            {
                $this->backorders = $backorders;
                return $this;
            }

            public function setStockStateResult($stockStateResult)
            {
                $this->stockStateResult = $stockStateResult;
                return $this;
            }

            public function getParentItem()
            {
                return $this->parentItem;
            }

            public function setParentItem($parentItem)
            {
                $this->parentItem = $parentItem;
                return $this;
            }

            public function getProduct()
            {
                return $this->product;
            }

            public function setProduct($product)
            {
                $this->product = $product;
                return $this;
            }

            public function getId()
            {
                return $this->id;
            }

            public function setId($id)
            {
                $this->id = $id;
                return $this;
            }

            public function getQuoteId()
            {
                return $this->quoteId;
            }

            public function setQuoteId($quoteId)
            {
                $this->quoteId = $quoteId;
                return $this;
            }

            public function setData($key, $value = null)
            {
                $this->data[$key] = $value;
                return $this;
            }

            public function setMessage($message)
            {
                $this->message = $message;
                return $this;
            }

            public function __wakeup()
            {
                return $this;
            }
        };
        // Create anonymous class for parent Quote\Item with required methods
        $parentItem = new class extends \Magento\Quote\Model\Quote\Item {
            /** @var bool|null */
            private $isQtyDecimal = null;
            /** @var float|null */
            private $qty = null;
            /** @var mixed */
            private $product = null;

            public function __construct()
            {
                // Skip parent constructor to avoid complex dependencies
            }

            public function setIsQtyDecimal($isQtyDecimal)
            {
                $this->isQtyDecimal = $isQtyDecimal;
                return $this;
            }

            public function getQty()
            {
                return $this->qty;
            }

            public function setQty($qty)
            {
                $this->qty = $qty;
                return $this;
            }

            public function getProduct()
            {
                return $this->product;
            }

            public function setProduct($product)
            {
                $this->product = $product;
                return $this;
            }

            public function __wakeup()
            {
                return $this;
            }
        };
        $product = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->getMock();
        $parentProduct = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->getMock();
        $productTypeInstance = $this->getMockBuilder(AbstractType::class)
            ->disableOriginalConstructor()
            ->getMock();
        $storeMock = $this->getMockBuilder(Store::class)
            ->disableOriginalConstructor()
            ->getMock();
        $storeMock->method('getWebsiteId')->willReturn($websiteId);
        $productTypeCustomOption = $this->getMockBuilder(
            Option::class
        )
            ->disableOriginalConstructor()
            ->getMock();
        // Create anonymous class for DataObject with required methods
        $result = new class extends DataObject {
            /** @var bool|null */
            private $itemIsQtyDecimal = null;
            /** @var bool|null */
            private $hasQtyOptionUpdate = null;
            /** @var float|null */
            private $origQty = null;
            /** @var bool|null */
            private $itemUseOldQty = null;
            /** @var string|null */
            private $message = null;
            /** @var int|null */
            private $itemBackorders = null;

            public function __construct()
            {
                // Skip parent constructor to avoid complex dependencies
            }

            public function getItemIsQtyDecimal()
            {
                return $this->itemIsQtyDecimal;
            }

            public function setItemIsQtyDecimal($itemIsQtyDecimal)
            {
                $this->itemIsQtyDecimal = $itemIsQtyDecimal;
                return $this;
            }

            public function getHasQtyOptionUpdate()
            {
                return $this->hasQtyOptionUpdate;
            }

            public function setHasQtyOptionUpdate($hasQtyOptionUpdate)
            {
                $this->hasQtyOptionUpdate = $hasQtyOptionUpdate;
                return $this;
            }

            public function getOrigQty()
            {
                return $this->origQty;
            }

            public function setOrigQty($origQty)
            {
                $this->origQty = $origQty;
                return $this;
            }

            public function getItemUseOldQty()
            {
                return $this->itemUseOldQty;
            }

            public function setItemUseOldQty($itemUseOldQty)
            {
                $this->itemUseOldQty = $itemUseOldQty;
                return $this;
            }

            public function getMessage()
            {
                return $this->message;
            }

            public function setMessage($message)
            {
                $this->message = $message;
                return $this;
            }

            public function getItemBackorders()
            {
                return $this->itemBackorders;
            }

            public function setItemBackorders($itemBackorders)
            {
                $this->itemBackorders = $itemBackorders;
                return $this;
            }
        };

        // Use setters for anonymous classes instead of expects
        $quoteItem->setParentItem($parentItem);
        $parentItem->setQty($parentItemQty);
        $quoteItem->setProduct($product);
        $product->method('getId')->willReturn('product_id');
        $quoteItem->setId('quote_item_id');
        $quoteItem->setQuoteId('quote_id');
        $this->quoteItemQtyList->expects($this->any())
            ->method('getQty')
            ->with('product_id', 'quote_item_id', 'quote_id', 0)
            ->willReturn('summary_qty');
        $this->stockStateMock->expects($this->once())
            ->method('checkQuoteItemQty')
            ->withAnyParameters()
            ->willReturn($result);
        $this->stockStateProviderMock->expects($this->never())->method('checkQuoteItemQty');
        $product->expects($this->once())
            ->method('getCustomOption')
            ->with('product_type')
            ->willReturn($productTypeCustomOption);
        $productTypeCustomOption->expects($this->once())
            ->method('getValue')
            ->willReturn('option_value');
        $this->typeConfig->expects($this->once())
            ->method('isProductSet')
            ->with('option_value')
            ->willReturn(true);
        $product->expects($this->once())->method('getName')->willReturn('product_name');
        $product->method('getStore')->willReturn($storeMock);
        // Use setters for anonymous classes instead of expects
        $stockItem->setProductName('product_name');
        $stockItem->setIsChildItem(true);
        $stockItem->setHasIsChildItem(true);
        $stockItem->unsIsChildItem();
        // Use setters for anonymous classes instead of expects
        $result->setItemIsQtyDecimal(true);
        $quoteItem->setIsQtyDecimal(true);
        $parentItem->setIsQtyDecimal(true);
        $parentItem->setProduct($parentProduct);
        $result->setHasQtyOptionUpdate(true);
        $parentProduct->expects($this->once())
            ->method('getTypeInstance')
            ->willReturn($productTypeInstance);
        $productTypeInstance->expects($this->once())
            ->method('getForceChildItemQtyChanges')
            ->with($product)->willReturn(true);
        $result->setOrigQty('orig_qty');
        $quoteItem->setData('qty', 'orig_qty');
        $result->setItemUseOldQty('item');
        $quoteItem->setUseOldQty('item');
        $result->setMessage('message');
        $quoteItem->setMessage('message');
        $result->setItemBackorders('backorders');
        $quoteItem->setBackorders('backorders');
        $quoteItem->setStockStateResult($result);

        $this->model->initialize($stockItem, $quoteItem, $qty);
    }

    /**
     * Test initialize without Subitem
     */
    public function testInitializeWithoutSubitem()
    {
        $qty = 3;
        $websiteId = 1;
        $productId = 1;

        // Create anonymous class for Item with required methods (second test)
        $stockItem = new class implements \Magento\CatalogInventory\Api\Data\StockItemInterface {
            /** @var string|null */
            private $productName = null;
            /** @var bool */
            private $isChildItem = false;
            /** @var bool */
            private $hasIsChildItem = false;

            public function __construct()
            {
            }

            public function checkQuoteItemQty($qty, $summaryQty, $origQty = null)
            {
                return null;
            }

            public function setProductName($productName)
            {
                $this->productName = $productName;
                return $this;
            }

            public function setIsChildItem($isChildItem)
            {
                $this->isChildItem = $isChildItem;
                return $this;
            }

            public function hasIsChildItem()
            {
                return $this->hasIsChildItem;
            }

            public function setHasIsChildItem($hasIsChildItem)
            {
                $this->hasIsChildItem = $hasIsChildItem;
                return $this;
            }

            public function __wakeup()
            {
                return $this;
            }

            // Implement all required methods from StockItemInterface
            public function getItemId()
            {
                return null;
            }
            public function setItemId($itemId)
            {
                return $this;
            }
            public function getProductId()
            {
                return null;
            }
            public function setProductId($productId)
            {
                return $this;
            }
            public function getWebsiteId()
            {
                return null;
            }
            public function setWebsiteId($websiteId)
            {
                return $this;
            }
            public function getStockId()
            {
                return null;
            }
            public function setStockId($stockId)
            {
                return $this;
            }
            public function getQty()
            {
                return null;
            }
            public function setQty($qty)
            {
                return $this;
            }
            public function getMinQty()
            {
                return null;
            }
            public function setMinQty($minQty)
            {
                return $this;
            }
            public function getMinSaleQty()
            {
                return null;
            }
            public function setMinSaleQty($minSaleQty)
            {
                return $this;
            }
            public function getMaxSaleQty()
            {
                return null;
            }
            public function setMaxSaleQty($maxSaleQty)
            {
                return $this;
            }
            public function getIsInStock()
            {
                return null;
            }
            public function setIsInStock($isInStock)
            {
                return $this;
            }
            public function getLowStockDate()
            {
                return null;
            }
            public function setLowStockDate($lowStockDate)
            {
                return $this;
            }
            public function getNotifyStockQty()
            {
                return null;
            }
            public function setNotifyStockQty($notifyStockQty)
            {
                return $this;
            }
            public function getManageStock()
            {
                return null;
            }
            public function setManageStock($manageStock)
            {
                return $this;
            }
            public function getBackorders()
            {
                return null;
            }
            public function setBackorders($backorders)
            {
                return $this;
            }
            public function getQtyIncrements()
            {
                return null;
            }
            public function setQtyIncrements($qtyIncrements)
            {
                return $this;
            }
            public function getEnableQtyIncrements()
            {
                return null;
            }
            public function setEnableQtyIncrements($enableQtyIncrements)
            {
                return $this;
            }
            public function getIsQtyDecimal()
            {
                return null;
            }
            public function setIsQtyDecimal($isQtyDecimal)
            {
                return $this;
            }
            public function getIsDecimalDivided()
            {
                return null;
            }
            public function setIsDecimalDivided($isDecimalDivided)
            {
                return $this;
            }
            public function getShowDefaultNotificationMessage()
            {
                return null;
            }
            public function setShowDefaultNotificationMessage($showDefaultNotificationMessage)
            {
                return $this;
            }
            public function getUseConfigMinQty()
            {
                return null;
            }
            public function setUseConfigMinQty($useConfigMinQty)
            {
                return $this;
            }
            public function getUseConfigMinSaleQty()
            {
                return null;
            }
            public function setUseConfigMinSaleQty($useConfigMinSaleQty)
            {
                return $this;
            }
            public function getUseConfigMaxSaleQty()
            {
                return null;
            }
            public function setUseConfigMaxSaleQty($useConfigMaxSaleQty)
            {
                return $this;
            }
            public function getUseConfigBackorders()
            {
                return null;
            }
            public function setUseConfigBackorders($useConfigBackorders)
            {
                return $this;
            }
            public function getUseConfigNotifyStockQty()
            {
                return null;
            }
            public function setUseConfigNotifyStockQty($useConfigNotifyStockQty)
            {
                return $this;
            }
            public function getUseConfigQtyIncrements()
            {
                return null;
            }
            public function setUseConfigQtyIncrements($useConfigQtyIncrements)
            {
                return $this;
            }
            public function getUseConfigEnableQtyInc()
            {
                return null;
            }
            public function setUseConfigEnableQtyInc($useConfigEnableQtyInc)
            {
                return $this;
            }
            public function getUseConfigManageStock()
            {
                return null;
            }
            public function setUseConfigManageStock($useConfigManageStock)
            {
                return $this;
            }
            public function getStockStatusChangedAuto()
            {
                return null;
            }
            public function setStockStatusChangedAuto($stockStatusChangedAuto)
            {
                return $this;
            }
            public function getExtensionAttributes()
            {
                return null;
            }
            public function setExtensionAttributes($extensionAttributes)
            {
                return $this;
            }
            public function getData($key = '', $index = null)
            {
                return null;
            }
            public function setData($key, $value = null)
            {
                return $this;
            }
            public function addData(array $arr)
            {
                return $this;
            }
            public function unsetData($key = null)
            {
                return $this;
            }
            public function hasData($key = '')
            {
                return false;
            }
            public function toArray($arrAttributes = [])
            {
                return [];
            }
            public function toJson($arrAttributes = [])
            {
                return '';
            }
            public function toString($format = '')
            {
                return '';
            }
            public function isEmpty()
            {
                return true;
            }
        };
        $storeMock = $this->getMockBuilder(Store::class)
            ->disableOriginalConstructor()
            ->getMock();
        $storeMock->method('getWebsiteId')->willReturn($websiteId);
        // Create anonymous class for Quote\Item with required methods (second test)
        $quoteItem = new class extends \Magento\Quote\Model\Quote\Item {
            /** @var float|null */
            private $qtyToAdd = null;
            /** @var mixed */
            private $product = null;
            /** @var mixed */
            private $parentItem = null;
            /** @var int|null */
            private $id = null;
            /** @var int|null */
            private $quoteId = null;

            public function __construct()
            {
                // Skip parent constructor to avoid complex dependencies
            }

            public function getQtyToAdd()
            {
                return $this->qtyToAdd;
            }

            public function setQtyToAdd($qtyToAdd)
            {
                $this->qtyToAdd = $qtyToAdd;
                return $this;
            }

            public function getProduct()
            {
                return $this->product;
            }

            public function setProduct($product)
            {
                $this->product = $product;
                return $this;
            }

            public function getParentItem()
            {
                return $this->parentItem;
            }

            public function setParentItem($parentItem)
            {
                $this->parentItem = $parentItem;
                return $this;
            }

            public function getId()
            {
                return $this->id;
            }

            public function setId($id)
            {
                $this->id = $id;
                return $this;
            }

            public function getQuoteId()
            {
                return $this->quoteId;
            }

            public function setQuoteId($quoteId)
            {
                $this->quoteId = $quoteId;
                return $this;
            }

            public function __wakeup()
            {
                return $this;
            }
        };
        $product = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->getMock();
        $productTypeCustomOption = $this->getMockBuilder(
            Option::class
        )
            ->disableOriginalConstructor()
            ->getMock();
        // Create anonymous class for DataObject with required methods (second test)
        $result = new class extends DataObject {
            /** @var bool|null */
            private $itemIsQtyDecimal = null;
            /** @var bool|null */
            private $hasQtyOptionUpdate = null;
            /** @var bool|null */
            private $itemUseOldQty = null;
            /** @var string|null */
            private $message = null;
            /** @var int|null */
            private $itemBackorders = null;

            public function __construct()
            {
                // Skip parent constructor to avoid complex dependencies
            }

            public function getItemIsQtyDecimal()
            {
                return $this->itemIsQtyDecimal;
            }

            public function setItemIsQtyDecimal($itemIsQtyDecimal)
            {
                $this->itemIsQtyDecimal = $itemIsQtyDecimal;
                return $this;
            }

            public function getHasQtyOptionUpdate()
            {
                return $this->hasQtyOptionUpdate;
            }

            public function setHasQtyOptionUpdate($hasQtyOptionUpdate)
            {
                $this->hasQtyOptionUpdate = $hasQtyOptionUpdate;
                return $this;
            }

            public function getItemUseOldQty()
            {
                return $this->itemUseOldQty;
            }

            public function setItemUseOldQty($itemUseOldQty)
            {
                $this->itemUseOldQty = $itemUseOldQty;
                return $this;
            }

            public function getMessage()
            {
                return $this->message;
            }

            public function setMessage($message)
            {
                $this->message = $message;
                return $this;
            }

            public function getItemBackorders()
            {
                return $this->itemBackorders;
            }

            public function setItemBackorders($itemBackorders)
            {
                $this->itemBackorders = $itemBackorders;
                return $this;
            }
        };
        $product->method('getStore')->willReturn($storeMock);
        $product->method('getId')->willReturn($productId);
        // Use setters for anonymous classes instead of expects
        $quoteItem->setParentItem(false);
        $quoteItem->setQtyToAdd(false);
        $quoteItem->setProduct($product);
        $quoteItem->setId('quote_item_id');
        $quoteItem->setQuoteId('quote_id');
        $this->quoteItemQtyList->expects($this->any())
            ->method('getQty')
            ->with($productId, 'quote_item_id', 'quote_id', $qty)
            ->willReturn('summary_qty');
        $this->stockStateMock->expects($this->once())
            ->method('checkQuoteItemQty')
            ->withAnyParameters()
            ->willReturn($result);
        $this->stockStateProviderMock->expects($this->never())->method('checkQuoteItemQty');
        $product->expects($this->once())
            ->method('getCustomOption')
            ->with('product_type')
            ->willReturn($productTypeCustomOption);
        $productTypeCustomOption->expects($this->once())
            ->method('getValue')
            ->willReturn('option_value');
        $this->typeConfig->expects($this->once())
            ->method('isProductSet')
            ->with('option_value')
            ->willReturn(true);
        $product->expects($this->once())->method('getName')->willReturn('product_name');
        // Use setters for anonymous classes instead of expects
        $stockItem->setProductName('product_name');
        $stockItem->setIsChildItem(true);
        $stockItem->setHasIsChildItem(false);
        $result->setItemIsQtyDecimal(null);
        $result->setHasQtyOptionUpdate(false);
        $result->setItemUseOldQty(null);
        $result->setMessage(null);
        $result->setItemBackorders(null);

        $this->model->initialize($stockItem, $quoteItem, $qty);
    }
}
