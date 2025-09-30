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
use Magento\CatalogInventory\Test\Unit\Helper\StockItemInterfaceTestHelper;
use Magento\CatalogInventory\Api\StockStateInterface;
use Magento\CatalogInventory\Model\Quote\Item\QuantityValidator\Initializer\StockItem;
use Magento\CatalogInventory\Model\Quote\Item\QuantityValidator\QuoteItemQtyList;
use Magento\CatalogInventory\Model\Stock\Item;
use Magento\CatalogInventory\Model\StockStateProvider;
use Magento\Framework\DataObject;
use Magento\Framework\Test\Unit\Helper\DataObjectTestHelper;
use Magento\Quote\Test\Unit\Helper\QuoteItemTestHelper;

use Magento\Store\Model\Store;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 * @SuppressWarnings(PHPMD.TooManyFields)
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

        // Create StockItemInterfaceTestHelper for Item with required methods
        $stockItem = new StockItemInterfaceTestHelper();
        // Create QuoteItemTestHelper for Quote\Item with required methods
        $quoteItem = new QuoteItemTestHelper();
        // Create QuoteItemTestHelper for parent Quote\Item with required methods
        $parentItem = new QuoteItemTestHelper();
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
        // Create DataObjectTestHelper for DataObject with required methods
        $result = new DataObjectTestHelper();

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

        // Create StockItemInterfaceTestHelper for Item with required methods (second test)
        $stockItem = new StockItemInterfaceTestHelper();
        $storeMock = $this->getMockBuilder(Store::class)
            ->disableOriginalConstructor()
            ->getMock();
        $storeMock->method('getWebsiteId')->willReturn($websiteId);
        // Create QuoteItemTestHelper for Quote\Item with required methods (second test)
        $quoteItem = new QuoteItemTestHelper();
        $product = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->getMock();
        $productTypeCustomOption = $this->getMockBuilder(
            Option::class
        )
            ->disableOriginalConstructor()
            ->getMock();
        // Create DataObjectTestHelper for DataObject with required methods (second test)
        $result = new DataObjectTestHelper();
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
