<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Model\Plugin;

use Magento\Catalog\Api\Data\ProductCustomOptionInterface as ProductOption;
use Magento\Catalog\Model\Plugin\QuoteItemProductOption as QuoteItemProductOptionPlugin;
use Magento\Catalog\Model\Product;
use Magento\Framework\DataObject;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use Magento\Quote\Model\Quote\Item\AbstractItem as AbstractQuoteItem;
use Magento\Quote\Model\Quote\Item\Option as QuoteItemOption;
use Magento\Quote\Model\Quote\Item\ToOrderItem as QuoteToOrderItem;
use Magento\Quote\Test\Unit\Helper\AbstractItemTestHelper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class QuoteItemProductOptionTest extends TestCase
{
    /**
     * @var QuoteItemProductOptionPlugin
     */
    private $plugin;

    /**
     * @var ObjectManagerHelper
     */
    private $objectManagerHelper;

    /**
     * @var QuoteToOrderItem|MockObject
     */
    private $subjectMock;

    /**
     * @var AbstractQuoteItem|MockObject
     */
    private $quoteItemMock;

    /**
     * @var QuoteItemOption|MockObject
     */
    private $quoteItemOptionMock;

    /**
     * @var Product|MockObject
     */
    private $productMock;

    protected function setUp(): void
    {
        $this->subjectMock = $this->getMockBuilder(QuoteToOrderItem::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->quoteItemMock = new AbstractItemTestHelper();
        $this->quoteItemOptionMock = $this->createPartialMock(QuoteItemOption::class, []);
        $this->productMock = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->plugin = $this->objectManagerHelper->getObject(QuoteItemProductOptionPlugin::class);
    }

    public function testBeforeItemToOrderItemEmptyOptions()
    {
        $this->quoteItemMock->setOptions(null);

        $this->plugin->beforeConvert($this->subjectMock, $this->quoteItemMock);
    }

    public function testBeforeItemToOrderItemWithOptions()
    {
        // Create two option mocks with different codes
        $optionMock1 = $this->createPartialMock(QuoteItemOption::class, []);
        $optionMock1->setCode('someText_8');
        
        $optionMock2 = $this->createPartialMock(QuoteItemOption::class, []);
        $optionMock2->setCode('not_int_text');
        
        $this->quoteItemMock->setOptions([$optionMock1, $optionMock2]);
        $this->productMock->expects(static::once())
            ->method('getOptionById')
            ->willReturn(new DataObject(['type' => ProductOption::OPTION_TYPE_FILE]));
        $this->quoteItemMock->setProduct($this->productMock);

        $this->plugin->beforeConvert($this->subjectMock, $this->quoteItemMock);
    }
}
