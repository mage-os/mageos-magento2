<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Bundle\Test\Unit\Helper\Catalog\Product;

use PHPUnit\Framework\Attributes\DataProvider;
use Magento\Bundle\Model\Product\Price;
use Magento\Bundle\Model\Product\Type;
use Magento\Bundle\Model\ResourceModel\Option\Collection;
use Magento\Bundle\Pricing\Price\TaxPrice;
use Magento\Catalog\Helper\Product\Configuration;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Configuration\Item\ItemInterface;
use Magento\Catalog\Model\Product\Configuration\Item\Option\OptionInterface;
use Magento\Catalog\Model\Product\Option;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Pricing\Helper\Data;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ConfigurationTest extends TestCase
{
    /**
     * @var Data|MockObject
     */
    private $pricingHelper;

    /**
     * @var Configuration|MockObject
     */
    private $productConfiguration;

    /**
     * @var Escaper|MockObject
     */
    private $escaper;

    /**
     * @var \Magento\Bundle\Helper\Catalog\Product\Configuration
     */
    private $helper;

    /**
     * @var ItemInterface|MockObject
     */
    private $item;

    /**
     * @var Json
     */
    private $serializer;

    /**
     * @var TaxPrice|MockObject
     */
    private $taxHelper;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->pricingHelper = $this->createPartialMock(Data::class, ['currency']);
        $this->productConfiguration = $this->createMock(Configuration::class);
        $this->escaper = $this->createPartialMock(Escaper::class, ['escapeHtml']);
        /** @var ItemInterface $this->item */
        $this->item = new class implements ItemInterface {
            private $qty;
            private $product;
            private $optionByCodeCallback;
            private $fileDownloadParams;
            
            public function getQty() { return $this->qty; }
            public function setQty($qty) { $this->qty = $qty; return $this; }
            
            public function getProduct() { return $this->product; }
            public function setProduct($product) { $this->product = $product; return $this; }
            
            public function getOptionByCode($code) { 
                if (is_callable($this->optionByCodeCallback)) {
                    return call_user_func($this->optionByCodeCallback, $code);
                }
                return $this->optionByCodeCallback; 
            }
            public function setOptionByCode($optionByCode) { $this->optionByCodeCallback = $optionByCode; return $this; }
            
            public function getFileDownloadParams() { return $this->fileDownloadParams; }
            public function setFileDownloadParams($fileDownloadParams) { $this->fileDownloadParams = $fileDownloadParams; return $this; }
        };
        $this->serializer = $this->createMock(Json::class);
        $this->taxHelper = $this->createPartialMock(TaxPrice::class, ['displayCartPricesBoth', 'getTaxPrice']);

        $this->serializer->expects($this->any())
            ->method('unserialize')
            ->willReturnCallback(
                function ($value) {
                    return json_decode($value, true);
                }
            );

        $this->helper = (new ObjectManager($this))->getObject(
            \Magento\Bundle\Helper\Catalog\Product\Configuration::class,
            [
                'pricingHelper' => $this->pricingHelper,
                'productConfiguration' => $this->productConfiguration,
                'escaper' => $this->escaper,
                'serializer' => $this->serializer,
                'taxHelper' => $this->taxHelper
            ]
        );
    }

    /**
     * @return void
     */
    public function testGetSelectionQty(): void
    {
        $selectionId = 15;
        $selectionQty = 35;
        $product = $this->createMock(Product::class);
        /** @var Option $option */
        $option = new class extends Option {
            private $value;
            
            public function __construct() {}
            
            public function getValue() { return $this->value; }
            public function setValue($value) { $this->value = $value; return $this; }
        };

        $product->expects($this->once())
            ->method('getCustomOption')
            ->with('selection_qty_' . $selectionId)
            ->willReturn($option);
        $option->setValue($selectionQty);

        $this->assertEquals($selectionQty, $this->helper->getSelectionQty($product, $selectionId));
    }

    /**
     * @return void
     */
    public function testGetSelectionQtyIfCustomOptionIsNotSet(): void
    {
        $selectionId = 15;
        $product = $this->createMock(Product::class);

        $product->expects($this->once())->method('getCustomOption')->with('selection_qty_' . $selectionId)
            ->willReturn(null);

        $this->assertEquals(0, $this->helper->getSelectionQty($product, $selectionId));
    }

    /**
     * @return void
     */
    public function testGetSelectionFinalPrice(): void
    {
        $itemQty = 2;

        $product = $this->createMock(Product::class);
        $price = $this->createMock(Price::class);
        $selectionProduct = $this->createMock(Product::class);

        $selectionProduct->expects($this->once())->method('unsetData')->with('final_price');
        $this->item->setProduct($product);
        $this->item->setQty($itemQty);
        $product->expects($this->once())->method('getPriceModel')->willReturn($price);
        $price->expects($this->once())->method('getSelectionFinalTotalPrice')
            ->with($product, $selectionProduct, $itemQty, 0, false, true);

        $this->helper->getSelectionFinalPrice($this->item, $selectionProduct);
    }

    /**
     * @return void
     */
    public function testGetBundleOptionsEmptyBundleOptionsIds(): void
    {
        $typeInstance = $this->createMock(Type::class);
        $product = $this->createPartialMock(Product::class, ['getTypeInstance', '__wakeup']);

        $product->expects($this->once())->method('getTypeInstance')->willReturn($typeInstance);
        $this->item->setProduct($product);
        $this->item->setOptionByCode(null);

        $this->assertEquals([], $this->helper->getBundleOptions($this->item));
    }

    /**
     * @return void
     * @throws LocalizedException
     */
    public function testGetBundleOptionsEmptyBundleSelectionIds(): void
    {
        $optionIds = '{"0":"1"}';
        $collection = $this->createMock(Collection::class);
        $product = $this->createPartialMock(Product::class, ['getTypeInstance', '__wakeup']);
        $typeInstance = $this->createPartialMock(Type::class, ['getOptionsByIds']);
        $selectionOption = $this->createPartialMock(
            OptionInterface::class,
            ['getValue']
        );
        $itemOption = $this->createPartialMock(
            OptionInterface::class,
            ['getValue']
        );

        $selectionOption->expects($this->once())
            ->method('getValue')
            ->willReturn('[]');
        $itemOption->expects($this->once())
            ->method('getValue')
            ->willReturn($optionIds);
        $typeInstance->expects($this->once())
            ->method('getOptionsByIds')
            ->with(
                json_decode($optionIds, true),
                $product
            )
            ->willReturn($collection);
        $product->expects($this->once())
            ->method('getTypeInstance')
            ->willReturn($typeInstance);
        $this->item->setProduct($product);
        $this->item->setOptionByCode(
            function ($arg1) use ($itemOption, $selectionOption) {
                if ($arg1 == 'bundle_option_ids') {
                    return $itemOption;
                } elseif ($arg1 == 'bundle_selection_ids') {
                    return $selectionOption;
                }
            }
        );

        $this->assertEquals([], $this->helper->getBundleOptions($this->item));
    }

    /**
     * @param $includingTax
     * @param $displayCartPriceBoth
     * @return void
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    #[DataProvider('getTaxConfiguration')]
    public function testGetOptions($includingTax, $displayCartPriceBoth): void
    {
        $optionIds = '{"0":"1"}';
        $selectionIds =  '{"0":"2"}';
        $selectionId = '2';
        /** @var Product $product */
        $product = new class extends Product {
            private $selectionId;
            private $typeInstance;
            private $customOption;
            private $name;
            private $priceModel;
            
            public function __construct() {}
            
            public function getSelectionId() { return $this->selectionId; }
            public function setSelectionId($selectionId) { $this->selectionId = $selectionId; return $this; }
            
            public function getTypeInstance() { return $this->typeInstance; }
            public function setTypeInstance($typeInstance) { $this->typeInstance = $typeInstance; return $this; }
            
            public function getCustomOption($code) { return $this->customOption; }
            public function setCustomOption($customOption) { $this->customOption = $customOption; return $this; }
            
            public function getName() { return $this->name; }
            public function setName($name) { $this->name = $name; return $this; }
            
            public function getPriceModel() { return $this->priceModel; }
            public function setPriceModel($priceModel) { $this->priceModel = $priceModel; return $this; }
        };
        $typeInstance = $this->createPartialMock(
            Type::class,
            ['getOptionsByIds', 'getSelectionsByIds']
        );
        $priceModel = $this->createPartialMock(
            Price::class,
            ['getSelectionFinalTotalPrice']
        );
        $selectionQty = $this->createPartialMock(
            \Magento\Quote\Model\Quote\Item\Option::class,
            ['getValue', '__wakeup']
        );
        /** @var \Magento\Bundle\Model\Option $bundleOption */
        $bundleOption = new class extends \Magento\Bundle\Model\Option {
            private $selections;
            private $title;
            
            public function __construct() {}
            
            public function getSelections() { return $this->selections; }
            public function setSelections($selections) { $this->selections = $selections; return $this; }
            
            public function getTitle() { return $this->title; }
            public function setTitle($title) { $this->title = $title; return $this; }
        };
        $selectionOption = $this->createPartialMock(
            OptionInterface::class,
            ['getValue']
        );
        $collection = $this->createPartialMock(
            Collection::class,
            ['appendSelections']
        );
        $itemOption = $this->createPartialMock(
            OptionInterface::class,
            ['getValue']
        );
        $collection2 = $this->createMock(\Magento\Bundle\Model\ResourceModel\Selection\Collection::class);

        $this->escaper->expects($this->once())
            ->method('escapeHtml')
            ->with('name')
            ->willReturn('name');
        if ($displayCartPriceBoth) {
            $this->taxHelper->expects($this->any())
                ->method('getTaxPrice')
                ->willReturnCallback(
                    function ($product, $amount, $includingTax) {
                        if ($product && $amount == 15.00 && ($includingTax || !$includingTax)) {
                            return 15.00;
                        }
                    }
                );

        } else {
            $this->taxHelper->expects($this->any())
                ->method('getTaxPrice')
                ->with($product, 15.00, $includingTax)
                ->willReturn(15.00);
        }
        $this->taxHelper->method('displayCartPricesBoth')->willReturn((bool)$displayCartPriceBoth);
        $this->pricingHelper->expects($this->atLeastOnce())->method('currency')->with(15.00)
            ->willReturn('<span class="price">$15.00</span>');
        $priceModel->expects($this->once())->method('getSelectionFinalTotalPrice')->willReturn(15.00);
        $selectionQty->method('getValue')->willReturn(1);
        $bundleOption->setSelections([$product]);
        $bundleOption->setTitle('title');
        $selectionOption->expects($this->once())->method('getValue')->willReturn($selectionIds);
        $collection->expects($this->once())->method('appendSelections')->with($collection2, true)
            ->willReturn([$bundleOption]);
        $itemOption->expects($this->once())->method('getValue')->willReturn($optionIds);
        $typeInstance->expects($this->once())
            ->method('getOptionsByIds')
            ->with(
                json_decode($optionIds, true),
                $product
            )
            ->willReturn($collection);
        $typeInstance->expects($this->once())
            ->method('getSelectionsByIds')
            ->with(json_decode($selectionIds, true), $product)
            ->willReturn($collection2);
        $product->setTypeInstance($typeInstance);
        $product->setCustomOption($selectionQty);
        $product->setSelectionId($selectionId);
        $product->setName('name');
        $product->setPriceModel($priceModel);
        $this->item->setProduct($product);
        $this->item->setOptionByCode(fn($param) => match ([$param]) {
            ['bundle_option_ids'] => $itemOption,
            ['bundle_selection_ids'] => $selectionOption
        });
        $this->productConfiguration->expects($this->once())->method('getCustomOptions')->with($this->item)
            ->willReturn([0 => ['label' => 'title', 'value' => 'value']]);

        if ($displayCartPriceBoth) {
            $value = '1 x name <span class="price">$15.00</span> Excl. tax: <span class="price">$15.00</span>';
        } else {
            $value = '1 x name <span class="price">$15.00</span>';
        }
        $this->assertEquals(
            [
                [
                    'label' => 'title',
                    'value' => [$value],
                    'has_html' => true
                ],
                ['label' => 'title', 'value' => 'value']
            ],
            $this->helper->getOptions($this->item)
        );
    }

    /**
     * Data provider for testGetOptions
     *
     * @return array
     */
    public static function getTaxConfiguration(): array
    {
        return [
            [null, false],
            [false, true]
        ];
    }
}
