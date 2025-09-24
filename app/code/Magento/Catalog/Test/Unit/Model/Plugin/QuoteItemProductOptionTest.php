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
        $this->quoteItemMock = new class extends AbstractQuoteItem {
            private $options = null;
            private $product = null;
            private $quote = null;
            private $address = null;
            
            public function __construct()
            {
                // Don't call parent constructor to avoid dependencies
            }
            
            public function getOptions()
            {
                return $this->options;
            }
            
            public function setOptions($options)
            {
                $this->options = $options;
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
            
            public function getQuote()
            {
                return $this->quote;
            }
            
            public function setQuote($quote)
            {
                $this->quote = $quote;
                return $this;
            }
            
            public function getAddress()
            {
                return $this->address;
            }
            
            public function setAddress($address)
            {
                $this->address = $address;
                return $this;
            }
            
            public function getOptionByCode($code)
            {
                return null;
            }
        };
        $this->quoteItemOptionMock = new class extends QuoteItemOption {
            private $code = null;
            
            public function __construct()
            {
                // Don't call parent constructor to avoid dependencies
            }
            
            public function getCode()
            {
                return $this->code;
            }
            
            public function setCode($code)
            {
                $this->code = $code;
                return $this;
            }
        };
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
        $optionMock1 = new class extends QuoteItemOption {
            private $code = 'someText_8';
            
            public function __construct()
            {
                // Don't call parent constructor to avoid dependencies
            }
            
            public function getCode()
            {
                return $this->code;
            }
            
            public function setCode($code)
            {
                $this->code = $code;
                return $this;
            }
        };
        
        $optionMock2 = new class extends QuoteItemOption {
            private $code = 'not_int_text';
            
            public function __construct()
            {
                // Don't call parent constructor to avoid dependencies
            }
            
            public function getCode()
            {
                return $this->code;
            }
            
            public function setCode($code)
            {
                $this->code = $code;
                return $this;
            }
        };
        
        $this->quoteItemMock->setOptions([$optionMock1, $optionMock2]);
        $this->productMock->expects(static::once())
            ->method('getOptionById')
            ->willReturn(new DataObject(['type' => ProductOption::OPTION_TYPE_FILE]));
        $this->quoteItemMock->setProduct($this->productMock);

        $this->plugin->beforeConvert($this->subjectMock, $this->quoteItemMock);
    }
}
