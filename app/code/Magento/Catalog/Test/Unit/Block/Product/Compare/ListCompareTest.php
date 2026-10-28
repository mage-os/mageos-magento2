<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Block\Product\Compare;

use PHPUnit\Framework\Attributes\DataProvider;
use Magento\Catalog\Block\Product\Compare\ListCompare;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\Compare\Item\Collection;
use Magento\Catalog\Model\ResourceModel\Product\Compare\Item\CollectionFactory;
use Magento\Eav\Model\Entity\Attribute\AttributeInterface;
use Magento\Eav\Model\Entity\Attribute\Frontend\AbstractFrontend;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\Pricing\Render;
use Magento\Framework\TestFramework\Unit\Helper\MockCreationTrait;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\View\Layout;
use Magento\Framework\View\LayoutInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ListCompareTest extends TestCase
{
    use MockCreationTrait;
    /**
     * @var ListCompare
     */
    protected $block;

    /**
     * @var LayoutInterface|MockObject
     */
    protected $layout;

    protected function setUp(): void
    {
        $this->layout = $this->createPartialMock(Layout::class, ['getBlock']);

        $context = $this->createPartialMock(Context::class, ['getLayout']);
        $context->method('getLayout')->willReturn($this->layout);

        $objectManager = new ObjectManager($this);
        $this->block = $objectManager->getObject(
            ListCompare::class,
            ['context' => $context]
        );
    }

    protected function tearDown(): void
    {
        $this->block = null;
    }

    /**
     * @param array $attributeData
     * @param string $expectedResult
     */
    #[DataProvider('attributeDataProvider')]
    public function testProductAttributeValue($attributeData, $expectedResult)
    {
        $attribute = $this->createPartialMockWithReflection(AttributeInterface::class, [
            'getAttributeCode', 'getSource', 'getSourceModel', 'getFrontendInput', 'getFrontend'
        ]);
        $attribute->method('getAttributeCode')->willReturn($attributeData['attribute_code']);
        $attribute->method('getSource')->willReturn(null);
        $attribute->method('getSourceModel')->willReturn($attributeData['source_model']);
        $attribute->method('getFrontendInput')->willReturn($attributeData['frontend_input']);
        
        $frontEndModel = $this->createPartialMock(AbstractFrontend::class, ['getValue']);
        $frontEndModel->expects($this->any())
            ->method('getValue')
            ->with($this->anything())
            ->willReturn($attributeData['attribute_value']);
        $attribute->method('getFrontend')->willReturn($frontEndModel);
        
        $productMock = $this->createPartialMock(Product::class, ['getId', 'getData', 'hasData']);
        $productMock->expects($this->any())
            ->method('hasData')
            ->with($attributeData['attribute_code'])
            ->willReturn(true);
        $productMock->expects($this->any())
            ->method('getData')
            ->with($attributeData['attribute_code'])
            ->willReturn($attributeData['attribute_value']);
        
        $this->assertEquals(
            $expectedResult,
            $this->block->getProductAttributeValue($productMock, $attribute)
        );
    }

    public function testGetProductPrice()
    {
        //Data
        $expectedResult = 'html';
        $blockName = 'product.price.render.default';
        $productId = 1;

        //Verification
        $product = $this->createPartialMock(Product::class, ['getId']);
        $product->expects($this->once())
            ->method('getId')
            ->willReturn($productId);

        $blockMock = $this->createPartialMock(Render::class, ['render']);
        $blockMock->expects($this->once())
            ->method('render')
            ->with(
                'final_price',
                $product,
                [
                    'price_id' => 'product-price-' . $productId . '-compare-list-top',
                    'display_minimal_price' => true,
                    'zone' => Render::ZONE_ITEM_LIST
                ]
            )
            ->willReturn($expectedResult);

        $this->layout->expects($this->once())
            ->method('getBlock')
            ->with($blockName)
            ->willReturn($blockMock);

        $this->assertEquals($expectedResult, $this->block->getProductPrice($product, '-compare-list-top'));
    }

    public function testGetItemsOrdersByCompareItemIdAscending(): void
    {
        $collectionMock = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['useProductItem', 'setStoreId', 'setCustomerId', 'setVisitorId',
                'addAttributeToSelect', 'loadComparableAttributes', 'addMinimalPrice',
                'addTaxPercents', 'setVisibility', 'addOrder'])
            ->getMock();

        $collectionMock->method('useProductItem')->willReturnSelf();
        $collectionMock->method('setStoreId')->willReturnSelf();
        $collectionMock->method('setVisitorId')->willReturnSelf();
        $collectionMock->method('addAttributeToSelect')->willReturnSelf();
        $collectionMock->method('loadComparableAttributes')->willReturnSelf();
        $collectionMock->method('addMinimalPrice')->willReturnSelf();
        $collectionMock->method('addTaxPercents')->willReturnSelf();
        $collectionMock->method('setVisibility')->willReturnSelf();

        $collectionMock->expects($this->once())
            ->method('addOrder')
            ->with('catalog_compare_item_id', 'ASC')
            ->willReturnSelf();

        $collectionFactory = $this->createMock(CollectionFactory::class);
        $collectionFactory->method('create')->willReturn($collectionMock);

        $httpContext = $this->createMock(HttpContext::class);
        $httpContext->method('getValue')->willReturn(false);

        $objectManager = new ObjectManager($this);
        $block = $objectManager->getObject(
            ListCompare::class,
            [
                'context'               => $this->createPartialMock(Context::class, ['getLayout']),
                'itemCollectionFactory' => $collectionFactory,
                'httpContext'           => $httpContext,
            ]
        );

        // Inject the required dependencies that come from Context
        $compareProductMock = $this->getMockBuilder(\Magento\Catalog\Helper\Product\Compare::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['setAllowUsedFlat'])
            ->getMock();
        $compareProductMock->method('setAllowUsedFlat')->willReturnSelf();

        $storeMock = $this->createMock(\Magento\Store\Model\Store::class);
        $storeMock->method('getId')->willReturn(1);
        $storeManagerMock = $this->createMock(\Magento\Store\Model\StoreManagerInterface::class);
        $storeManagerMock->method('getStore')->willReturn($storeMock);

        $catalogVisibilityMock = $this->createMock(\Magento\Catalog\Model\Product\Visibility::class);
        $catalogVisibilityMock->method('getVisibleInSiteIds')->willReturn([1, 2]);

        $catalogConfigMock = $this->createMock(\Magento\Catalog\Model\Config::class);
        $catalogConfigMock->method('getProductAttributes')->willReturn([]);

        $objectManager->setBackwardCompatibleProperty($block, '_compareProduct', $compareProductMock);
        $objectManager->setBackwardCompatibleProperty($block, '_storeManager', $storeManagerMock);
        $objectManager->setBackwardCompatibleProperty($block, '_catalogProductVisibility', $catalogVisibilityMock);
        $objectManager->setBackwardCompatibleProperty($block, '_catalogConfig', $catalogConfigMock);

        $block->getItems();
    }

    /**
     * @return array
     */
    public static function attributeDataProvider(): array
    {
        return [
            [
                'attributeData' => [
                    'attribute_code' => 'tier_price',
                    'source_model' => null,
                    'frontend_input' => 'text',
                    'attribute_value' => []
                ],
                'expectedResult' => __('N/A')
            ],
            [
                'attributeData' => [
                    'attribute_code' => 'special_price',
                    'source_model' => null,
                    'frontend_input' => 'decimal',
                    'attribute_value' => 50.00
                ],
                'expectedResult' => '50.00'
            ]
        ];
    }
}
