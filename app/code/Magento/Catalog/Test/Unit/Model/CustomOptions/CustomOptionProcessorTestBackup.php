<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Model\CustomOptions;

use PHPUnit\Framework\Attributes\CoversClass;
use Magento\Catalog\Api\Data\CustomOptionInterface;
use Magento\Catalog\Model\CustomOptions\CustomOption;
use Magento\Catalog\Model\CustomOptions\CustomOptionFactory;
use Magento\Catalog\Model\CustomOptions\CustomOptionProcessor;
use Magento\Framework\DataObject;
use Magento\Framework\DataObject\Factory;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Quote\Api\Data\ProductOptionExtensionFactory;
use Magento\Quote\Api\Data\ProductOptionExtensionInterface;
use Magento\Quote\Model\Quote\Item\Option;
use Magento\Quote\Model\Quote\ProductOption;
use Magento\Quote\Model\Quote\ProductOptionFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
#[CoversClass(\Magento\Catalog\Model\CustomOptions\CustomOptionProcessor::class)]
class CustomOptionProcessorTest extends TestCase
{
    /**
     * @var Factory|MockObject
     */
    protected $objectFactory;

    /**
     * @var ProductOptionFactory|MockObject
     */
    protected $productOptionFactory;

    /**
     * @var ProductOptionExtensionFactory|MockObject
     */
    protected $extensionFactory;

    /**
     * @var CustomOptionFactory|MockObject
     */
    protected $customOptionFactory;

    /** @var CartItemInterface|MockObject */
    protected $cartItem;

    /** @var ProductOptionExtensionInterface|MockObject */
    protected $extensibleAttribute;

    /** @var ProductOption|MockObject */
    protected $productOption;

    /** @var CustomOption|MockObject */
    protected $customOption;

    /** @var DataObject|MockObject */
    protected $buyRequest;

    /** @var CustomOptionProcessor */
    protected $processor;

    /** @var Json */
    private $serializer;

    protected function setUp(): void
    {
        $this->objectFactory = $this->getMockBuilder(Factory::class)
            ->onlyMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->productOptionFactory = $this->getMockBuilder(ProductOptionFactory::class)
            ->onlyMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->extensionFactory = $this->getMockBuilder(ProductOptionExtensionFactory::class)
            ->onlyMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->customOptionFactory = $this->getMockBuilder(
            CustomOptionFactory::class
        )
            ->onlyMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        /** @var CartItemInterface $this->cartItem */
        $this->cartItem = new class implements CartItemInterface {
            private $optionsByCode = [];
            private $productOption = false;
            
            public function __construct()
            {
            }
            
            public function getOptionByCode($code)
            {
                return $this->optionsByCode[$code] ?? null;
            }
            
            public function setOptionByCode($code, $option)
            {
                $this->optionsByCode[$code] = $option;
                return $this;
            }
            
            public function getProductOption()
            {
                return $this->productOption;
            }
            
            public function setProductOption($productOption)
            {
                $this->productOption = $productOption;
                return $this;
            }
            
            // Implement all required interface methods with default implementations
            public function getItemId()
            {
                return null;
            }
            public function setItemId($itemId)
            {
                return $this;
            }
            public function getSku()
            {
                return null;
            }
            public function setSku($sku)
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
            public function getName()
            {
                return null;
            }
            public function setName($name)
            {
                return $this;
            }
            public function getPrice()
            {
                return null;
            }
            public function setPrice($price)
            {
                return $this;
            }
            public function getProductType()
            {
                return null;
            }
            public function setProductType($productType)
            {
                return $this;
            }
            public function getQuoteId()
            {
                return null;
            }
            public function setQuoteId($quoteId)
            {
                return $this;
            }
            public function getProduct()
            {
                return null;
            }
            public function setProduct($product)
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
        };
        /** @var ProductOptionExtensionInterface $this->extensibleAttribute */
        $this->extensibleAttribute = new class implements ProductOptionExtensionInterface {
            private $customOptions = null;
            
            public function __construct()
            {
            }
            
            public function setCustomOptions($customOptions)
            {
                $this->customOptions = $customOptions;
                return $this;
            }
            
            public function getCustomOptions()
            {
                return $this->customOptions;
            }
        };
        $this->productOption = $this->getMockBuilder(ProductOption::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->customOption = $this->createMock(CustomOptionInterface::class);
        $this->buyRequest = $this->getMockBuilder(DataObject::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->serializer = $this->createMock(Json::class);

        $this->processor = new CustomOptionProcessor(
            $this->objectFactory,
            $this->productOptionFactory,
            $this->extensionFactory,
            $this->customOptionFactory,
            $this->serializer
        );
    }

    public function testConvertToBuyRequest()
    {
        $optionId = 23;
        $optionValue = 'Option value';
        $this->objectFactory->expects($this->once())
            ->method('create')
            ->willReturn($this->buyRequest);
        $this->cartItem->setProductOption($this->productOption);
        $this->productOption->method('getExtensionAttributes')->willReturn($this->extensibleAttribute);
        $this->extensibleAttribute->setCustomOptions([$this->customOption]);
        $this->customOption->expects($this->once())
            ->method('getOptionId')
            ->willReturn($optionId);
        $this->customOption->expects($this->once())
            ->method('getOptionValue')
            ->willReturn($optionValue);

        $this->assertSame($this->buyRequest, $this->processor->convertToBuyRequest($this->cartItem));
    }

    public function testProcessCustomOptions()
    {
        $optionId = 23;
        $quoteItemOption = $this->getMockBuilder(Option::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->cartItem->setOptionByCode('info_buyRequest', $quoteItemOption);
        $quoteItemOption->method('getValue')->willReturn('{"options":{"' . $optionId . '":["5","6"]}}');
        $this->serializer->method('unserialize')->willReturn(json_decode($quoteItemOption->getValue(), true));
        $this->customOptionFactory->expects($this->once())
            ->method('create')
            ->willReturn($this->customOption);
        $this->customOption->expects($this->once())
            ->method('setOptionId')
            ->with($optionId);
        $this->customOption->expects($this->once())
            ->method('setOptionValue')
            ->with('5,6');
        // productOption is already initialized as false
        $this->productOptionFactory->expects($this->once())
            ->method('create')
            ->willReturn($this->productOption);
        $this->productOption->expects($this->once())
            ->method('getExtensionAttributes')
            ->willReturn(false);
        $this->extensionFactory->expects($this->once())
            ->method('create')
            ->willReturn($this->extensibleAttribute);
        $this->extensibleAttribute->setCustomOptions([$optionId => $this->customOption]);
        $this->productOption->expects($this->once())
            ->method('setExtensionAttributes')
            ->with($this->extensibleAttribute);
        $this->cartItem->setProductOption($this->productOption);

        $this->assertSame($this->cartItem, $this->processor->processOptions($this->cartItem));
    }
}
