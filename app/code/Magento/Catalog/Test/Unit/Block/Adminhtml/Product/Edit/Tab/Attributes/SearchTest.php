<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Block\Adminhtml\Product\Edit\Tab\Attributes;

use Magento\Backend\Block\Template\Context;
use Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Attributes\Search;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\DB\Helper;
use Magento\Framework\Escaper;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\Registry;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\UrlInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Unit test for Search block
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SearchTest extends TestCase
{
    /**
     * @var Search
     */
    private $block;

    /**
     * @var Context|MockObject
     */
    private $contextMock;

    /**
     * @var Helper|MockObject
     */
    private $resourceHelperMock;

    /**
     * @var CollectionFactory|MockObject
     */
    private $collectionFactoryMock;

    /**
     * @var Registry|MockObject
     */
    private $registryMock;

    /**
     * @var JsonHelper|MockObject
     */
    private $jsonHelperMock;

    /**
     * @var UrlInterface|MockObject
     */
    private $urlBuilderMock;

    /**
     * @var RequestInterface|MockObject
     */
    private $requestMock;

    /**
     * @var Escaper|MockObject
     */
    private $escaperMock;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $this->contextMock = $this->createMock(Context::class);
        $this->resourceHelperMock = $this->createMock(Helper::class);
        $this->collectionFactoryMock = $this->createPartialMock(CollectionFactory::class, ['create']);
        $this->registryMock = $this->createMock(Registry::class);
        $this->jsonHelperMock = $this->createMock(JsonHelper::class);
        $this->urlBuilderMock = $this->getMockForAbstractClass(UrlInterface::class);
        $this->requestMock = $this->getMockForAbstractClass(RequestInterface::class);
        $this->escaperMock = $this->createMock(Escaper::class);

        $this->contextMock->expects($this->any())
            ->method('getUrlBuilder')
            ->willReturn($this->urlBuilderMock);

        $this->contextMock->expects($this->any())
            ->method('getRequest')
            ->willReturn($this->requestMock);

        $this->contextMock->expects($this->any())
            ->method('getEscaper')
            ->willReturn($this->escaperMock);

        // Prepare ObjectManager to handle JsonHelper and DirectoryHelper fallback
        $objects = [
            [
                JsonHelper::class,
                $this->jsonHelperMock
            ],
            [
                DirectoryHelper::class,
                $this->createMock(DirectoryHelper::class)
            ]
        ];
        $objectManager->prepareObjectManager($objects);

        $this->block = $objectManager->getObject(
            Search::class,
            [
                'context' => $this->contextMock,
                'resourceHelper' => $this->resourceHelperMock,
                'collectionFactory' => $this->collectionFactoryMock,
                'registry' => $this->registryMock,
                'jsonHelper' => $this->jsonHelperMock
            ]
        );
    }

    /**
     * Test getSelectorOptions returns array with required structure
     *
     * @return void
     */
    public function testGetSelectorOptionsReturnsArrayWithRequiredKeys(): void
    {
        $templateId = 4;
        $groupId = 10;
        $suggestedAttributes = [
            ['id' => 1, 'label' => 'Color', 'code' => 'color'],
            ['id' => 2, 'label' => 'Size', 'code' => 'size']
        ];

        // Mock product
        $productMock = $this->createMock(Product::class);
        $productMock->expects($this->once())
            ->method('getAttributeSetId')
            ->willReturn($templateId);

        // Mock registry
        $this->registryMock->expects($this->once())
            ->method('registry')
            ->with('product')
            ->willReturn($productMock);

        // Mock URL builder
        $this->urlBuilderMock->expects($this->once())
            ->method('getUrl')
            ->willReturn('http://example.com/catalog/product/suggestAttributes');

        // Mock escaper
        $this->escaperMock->expects($this->once())
            ->method('escapeUrl')
            ->willReturn('http://example.com/catalog/product/suggestAttributes');

        // Set group ID
        $this->block->setGroupId($groupId);

        // Mock getSuggestedAttributes
        $collectionMock = $this->createMock(Collection::class);
        $this->setupCollectionMock($collectionMock, '', $templateId, $suggestedAttributes);

        $result = $this->block->getSelectorOptions();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('source', $result);
        $this->assertArrayHasKey('minLength', $result);
        $this->assertArrayHasKey('ajaxOptions', $result);
        $this->assertArrayHasKey('template', $result);
        $this->assertArrayHasKey('data', $result);
    }

    /**
     * Test getSelectorOptions returns correct source URL
     *
     * @return void
     */
    public function testGetSelectorOptionsReturnsCorrectSourceUrl(): void
    {
        $templateId = 4;
        $expectedUrl = 'http://example.com/catalog/product/suggestAttributes';
        $escapedUrl = 'http://example.com/catalog/product/suggestAttributes';

        // Mock product
        $productMock = $this->createMock(Product::class);
        $productMock->expects($this->once())
            ->method('getAttributeSetId')
            ->willReturn($templateId);

        // Mock registry
        $this->registryMock->expects($this->once())
            ->method('registry')
            ->with('product')
            ->willReturn($productMock);

        // Mock URL builder
        $this->urlBuilderMock->expects($this->once())
            ->method('getUrl')
            ->with('catalog/product/suggestAttributes')
            ->willReturn($expectedUrl);

        // Mock escaper
        $this->escaperMock->expects($this->once())
            ->method('escapeUrl')
            ->with($expectedUrl)
            ->willReturn($escapedUrl);

        // Mock getSuggestedAttributes
        $collectionMock = $this->createMock(Collection::class);
        $this->setupCollectionMock($collectionMock, '', $templateId, []);

        $result = $this->block->getSelectorOptions();

        $this->assertEquals($escapedUrl, $result['source']);
    }

    /**
     * Test getSelectorOptions returns correct min length
     *
     * @return void
     */
    public function testGetSelectorOptionsReturnsCorrectMinLength(): void
    {
        $templateId = 4;

        // Mock product
        $productMock = $this->createMock(Product::class);
        $productMock->expects($this->once())
            ->method('getAttributeSetId')
            ->willReturn($templateId);

        // Mock registry
        $this->registryMock->expects($this->once())
            ->method('registry')
            ->with('product')
            ->willReturn($productMock);

        // Mock URL builder
        $this->urlBuilderMock->expects($this->once())
            ->method('getUrl')
            ->willReturn('http://example.com/url');

        // Mock escaper
        $this->escaperMock->expects($this->once())
            ->method('escapeUrl')
            ->willReturn('http://example.com/url');

        // Mock getSuggestedAttributes
        $collectionMock = $this->createMock(Collection::class);
        $this->setupCollectionMock($collectionMock, '', $templateId, []);

        $result = $this->block->getSelectorOptions();

        $this->assertEquals(0, $result['minLength']);
    }

    /**
     * Test getSelectorOptions returns correct AJAX options with template ID
     *
     * @return void
     */
    public function testGetSelectorOptionsReturnsCorrectAjaxOptions(): void
    {
        $templateId = 4;

        // Mock product
        $productMock = $this->createMock(Product::class);
        $productMock->expects($this->once())
            ->method('getAttributeSetId')
            ->willReturn($templateId);

        // Mock registry
        $this->registryMock->expects($this->once())
            ->method('registry')
            ->with('product')
            ->willReturn($productMock);

        // Mock URL builder
        $this->urlBuilderMock->expects($this->once())
            ->method('getUrl')
            ->willReturn('http://example.com/url');

        // Mock escaper
        $this->escaperMock->expects($this->once())
            ->method('escapeUrl')
            ->willReturn('http://example.com/url');

        // Mock getSuggestedAttributes
        $collectionMock = $this->createMock(Collection::class);
        $this->setupCollectionMock($collectionMock, '', $templateId, []);

        $result = $this->block->getSelectorOptions();

        $this->assertEquals(['data' => ['template_id' => $templateId]], $result['ajaxOptions']);
    }

    /**
     * Test getSelectorOptions returns correct template selector
     *
     * @return void
     */
    public function testGetSelectorOptionsReturnsCorrectTemplateSelector(): void
    {
        $templateId = 4;
        $groupId = 10;

        // Mock product
        $productMock = $this->createMock(Product::class);
        $productMock->expects($this->once())
            ->method('getAttributeSetId')
            ->willReturn($templateId);

        // Mock registry
        $this->registryMock->expects($this->once())
            ->method('registry')
            ->with('product')
            ->willReturn($productMock);

        // Mock URL builder
        $this->urlBuilderMock->expects($this->once())
            ->method('getUrl')
            ->willReturn('http://example.com/url');

        // Mock escaper
        $this->escaperMock->expects($this->once())
            ->method('escapeUrl')
            ->willReturn('http://example.com/url');

        // Set group ID
        $this->block->setGroupId($groupId);

        // Mock getSuggestedAttributes
        $collectionMock = $this->createMock(Collection::class);
        $this->setupCollectionMock($collectionMock, '', $templateId, []);

        $result = $this->block->getSelectorOptions();

        $this->assertEquals(
            '[data-template-for="product-attribute-search-' . $groupId . '"]',
            $result['template']
        );
    }

    /**
     * Test getSelectorOptions returns suggested attributes data
     *
     * @return void
     */
    public function testGetSelectorOptionsReturnsSuggestedAttributesData(): void
    {
        $templateId = 4;
        $suggestedAttributes = [
            ['id' => 1, 'label' => 'Color', 'code' => 'color'],
            ['id' => 2, 'label' => 'Size', 'code' => 'size']
        ];

        // Mock product
        $productMock = $this->createMock(Product::class);
        $productMock->expects($this->once())
            ->method('getAttributeSetId')
            ->willReturn($templateId);

        // Mock registry
        $this->registryMock->expects($this->once())
            ->method('registry')
            ->with('product')
            ->willReturn($productMock);

        // Mock URL builder
        $this->urlBuilderMock->expects($this->once())
            ->method('getUrl')
            ->willReturn('http://example.com/url');

        // Mock escaper
        $this->escaperMock->expects($this->once())
            ->method('escapeUrl')
            ->willReturn('http://example.com/url');

        // Mock getSuggestedAttributes
        $collectionMock = $this->createMock(Collection::class);
        $this->setupCollectionMock($collectionMock, '', $templateId, $suggestedAttributes);

        $result = $this->block->getSelectorOptions();

        $this->assertEquals($suggestedAttributes, $result['data']);
    }

    /**
     * Test getSuggestedAttributes method with label part
     *
     * @return void
     */
    public function testGetSuggestedAttributesWithLabelPart(): void
    {
        $labelPart = 'col';
        $escapedLabelPart = '%col%';
        $templateId = 4;
        $expectedAttributes = [
            ['id' => 1, 'label' => 'Color', 'code' => 'color'],
            ['id' => 2, 'label' => 'Column', 'code' => 'column']
        ];

        // Mock resource helper
        $this->resourceHelperMock->expects($this->once())
            ->method('addLikeEscape')
            ->with($labelPart, ['position' => 'any'])
            ->willReturn($escapedLabelPart);

        // Setup collection mock
        $collectionMock = $this->createMock(Collection::class);
        $this->setupCollectionMock($collectionMock, $escapedLabelPart, $templateId, $expectedAttributes);

        $result = $this->block->getSuggestedAttributes($labelPart, $templateId);

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertEquals($expectedAttributes, $result);
    }

    /**
     * Test getSuggestedAttributes method with empty label part
     *
     * @return void
     */
    public function testGetSuggestedAttributesWithEmptyLabelPart(): void
    {
        $labelPart = '';
        $escapedLabelPart = '%%';
        $templateId = 4;
        $expectedAttributes = [
            ['id' => 1, 'label' => 'Color', 'code' => 'color'],
            ['id' => 2, 'label' => 'Size', 'code' => 'size'],
            ['id' => 3, 'label' => 'Material', 'code' => 'material']
        ];

        // Mock resource helper
        $this->resourceHelperMock->expects($this->once())
            ->method('addLikeEscape')
            ->with($labelPart, ['position' => 'any'])
            ->willReturn($escapedLabelPart);

        // Setup collection mock
        $collectionMock = $this->createMock(Collection::class);
        $this->setupCollectionMock($collectionMock, $escapedLabelPart, $templateId, $expectedAttributes);

        $result = $this->block->getSuggestedAttributes($labelPart, $templateId);

        $this->assertIsArray($result);
        $this->assertCount(3, $result);
        $this->assertEquals($expectedAttributes, $result);
    }

    /**
     * Test getSuggestedAttributes method without template ID uses request parameter
     *
     * @return void
     */
    public function testGetSuggestedAttributesWithoutTemplateIdUsesRequestParam(): void
    {
        $labelPart = 'test';
        $escapedLabelPart = '%test%';
        $templateId = 5;
        $expectedAttributes = [
            ['id' => 10, 'label' => 'Test Attribute', 'code' => 'test_attribute']
        ];

        // Mock resource helper
        $this->resourceHelperMock->expects($this->once())
            ->method('addLikeEscape')
            ->with($labelPart, ['position' => 'any'])
            ->willReturn($escapedLabelPart);

        // Mock request to return template_id parameter
        $this->requestMock->expects($this->once())
            ->method('getParam')
            ->with('template_id')
            ->willReturn($templateId);

        // Setup collection mock
        $collectionMock = $this->createMock(Collection::class);
        $this->setupCollectionMock($collectionMock, $escapedLabelPart, $templateId, $expectedAttributes);

        // Call without template ID
        $result = $this->block->getSuggestedAttributes($labelPart);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals($expectedAttributes, $result);
    }

    /**
     * Test getSuggestedAttributes method returns empty array when no attributes found
     *
     * @return void
     */
    public function testGetSuggestedAttributesReturnsEmptyArrayWhenNoAttributesFound(): void
    {
        $labelPart = 'nonexistent';
        $escapedLabelPart = '%nonexistent%';
        $templateId = 4;

        // Mock resource helper
        $this->resourceHelperMock->expects($this->once())
            ->method('addLikeEscape')
            ->with($labelPart, ['position' => 'any'])
            ->willReturn($escapedLabelPart);

        // Setup collection mock with no results
        $collectionMock = $this->createMock(Collection::class);
        $this->setupCollectionMock($collectionMock, $escapedLabelPart, $templateId, []);

        $result = $this->block->getSuggestedAttributes($labelPart, $templateId);

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    /**
     * Test getAddAttributeUrl method
     *
     * @return void
     */
    public function testGetAddAttributeUrl(): void
    {
        $expectedUrl = 'http://example.com/catalog/product/addAttributeToTemplate';

        $this->urlBuilderMock->expects($this->once())
            ->method('getUrl')
            ->with('catalog/product/addAttributeToTemplate')
            ->willReturn($expectedUrl);

        $result = $this->block->getAddAttributeUrl();

        $this->assertEquals($expectedUrl, $result);
    }

    /**
     * Setup collection mock helper
     *
     * @param Collection|MockObject $collectionMock
     * @param string $escapedLabelPart
     * @param int $templateId
     * @param array $attributesData
     * @return void
     */
    private function setupCollectionMock(
        MockObject $collectionMock,
        string $escapedLabelPart,
        int $templateId,
        array $attributesData
    ): void {
        $this->collectionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($collectionMock);

        $collectionMock->expects($this->once())
            ->method('addFieldToFilter')
            ->with('frontend_label', ['like' => $escapedLabelPart])
            ->willReturnSelf();

        $collectionMock->expects($this->once())
            ->method('setExcludeSetFilter')
            ->with($templateId)
            ->willReturnSelf();

        $collectionMock->expects($this->once())
            ->method('setPageSize')
            ->with(20)
            ->willReturnSelf();

        // Create attribute mocks
        $attributeMocks = [];
        foreach ($attributesData as $attrData) {
            $attributeMock = $this->getMockBuilder(Attribute::class)
                ->disableOriginalConstructor()
                ->addMethods(['getFrontendLabel'])
                ->onlyMethods(['getId', 'getAttributeCode'])
                ->getMock();
            $attributeMock->expects($this->once())
                ->method('getId')
                ->willReturn($attrData['id']);
            $attributeMock->expects($this->once())
                ->method('getFrontendLabel')
                ->willReturn($attrData['label']);
            $attributeMock->expects($this->once())
                ->method('getAttributeCode')
                ->willReturn($attrData['code']);
            $attributeMocks[] = $attributeMock;
        }

        $collectionMock->expects($this->once())
            ->method('getItems')
            ->willReturn($attributeMocks);
    }
}
