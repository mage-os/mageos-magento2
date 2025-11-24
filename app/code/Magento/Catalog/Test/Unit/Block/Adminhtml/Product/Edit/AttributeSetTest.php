<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Block\Adminhtml\Product\Edit;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Element\ElementCreator;
use Magento\Catalog\Block\Adminhtml\Product\Edit\AttributeSet;
use Magento\Catalog\Model\Product;
use Magento\Framework\Escaper;
use Magento\Framework\Filesystem\Directory\ReadInterface as DirectoryHelper;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\Registry;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\UrlInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Unit test for AttributeSet block.
 */
class AttributeSetTest extends TestCase
{
    /**
     * @var AttributeSet
     */
    private AttributeSet $block;

    /**
     * @var Registry|MockObject
     */
    private Registry|MockObject $registry;

    /**
     * @var Product|MockObject
     */
    private Product|MockObject $product;

    /**
     * @var UrlInterface|MockObject
     */
    private UrlInterface|MockObject $urlBuilder;

    /**
     * @var Escaper|MockObject
     */
    private Escaper|MockObject $escaper;

    /**
     * @var JsonHelper|MockObject
     */
    private JsonHelper|MockObject $jsonHelper;

    protected function setUp(): void
    {
        $objectManagerHelper = new ObjectManager($this);
        $objectManagerHelper->prepareObjectManager();

        $this->registry = $this->createMock(Registry::class);
        $this->product = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getAttributeSetId'])
            ->getMock();
        $this->urlBuilder = $this->getMockForAbstractClass(UrlInterface::class);
        $this->escaper = $this->createMock(Escaper::class);
        $this->jsonHelper = $this->createMock(JsonHelper::class);

        $context = $this->createMock(Context::class);
        $context->method('getUrlBuilder')->willReturn($this->urlBuilder);
        $context->method('getEscaper')->willReturn($this->escaper);

        $this->block = new AttributeSet(
            $context,
            $this->registry,
            ['jsonHelper' => $this->jsonHelper]
        );

        // Default: product in registry
        $this->registry->method('registry')->with('product')->willReturn($this->product);
    }

    public function testConstructorWithJsonHelper(): void
    {
        $context = $this->createMock(Context::class);
        $registry = $this->createMock(Registry::class);
        $jsonHelper = $this->createMock(JsonHelper::class);

        $block = new AttributeSet($context, $registry, [], $jsonHelper);

        $this->assertInstanceOf(AttributeSet::class, $block);
    }

    public function testGetSelectorOptions(): void
    {
        // Setup product with attribute set ID
        $this->product->method('getAttributeSetId')->willReturn(123);

        // Setup URL builder
        $this->urlBuilder->method('getUrl')
            ->with('catalog/product/suggestAttributeSets')
            ->willReturn('http://example.com/admin/catalog/product/suggestAttributeSets');

        // Setup escaper - return arguments as-is for testing
        $this->escaper->method('escapeUrl')
            ->willReturnCallback(function ($url) {
                return 'escaped_' . $url;
            });
        $this->escaper->method('escapeHtml')
            ->willReturnCallback(function ($value) {
                return 'escaped_' . $value;
            });

        // Act
        $options = $this->block->getSelectorOptions();

        // Assert
        $this->assertIsArray($options);
        $this->assertArrayHasKey('source', $options);
        $this->assertArrayHasKey('className', $options);
        $this->assertArrayHasKey('showRecent', $options);
        $this->assertArrayHasKey('storageKey', $options);
        $this->assertArrayHasKey('minLength', $options);
        $this->assertArrayHasKey('currentlySelected', $options);

        // Assert values
        $this->assertStringContainsString('escaped_', $options['source']);
        $this->assertStringContainsString('catalog/product/suggestAttributeSets', $options['source']);
        $this->assertSame('category-select', $options['className']);
        $this->assertTrue($options['showRecent']);
        $this->assertSame('product-template-key', $options['storageKey']);
        $this->assertSame(0, $options['minLength']);
        $this->assertSame('escaped_123', $options['currentlySelected']);
    }

    public function testGetSelectorOptionsWithDifferentAttributeSetId(): void
    {
        // Setup product with different attribute set ID
        $this->product->method('getAttributeSetId')->willReturn(456);

        $this->urlBuilder->method('getUrl')->willReturn('http://example.com/admin/url');
        $this->escaper->method('escapeUrl')->willReturnArgument(0);
        $this->escaper->method('escapeHtml')->willReturnArgument(0);

        $options = $this->block->getSelectorOptions();

        $this->assertSame(456, $options['currentlySelected']);
    }

    public function testGetSelectorOptionsEscapesUrl(): void
    {
        $this->product->method('getAttributeSetId')->willReturn(1);

        // URL with special characters
        $rawUrl = 'http://example.com/admin/catalog?test=1&special=<script>';
        $escapedUrl = 'http://example.com/admin/catalog?test=1&amp;special=&lt;script&gt;';

        $this->urlBuilder->method('getUrl')->willReturn($rawUrl);
        $this->escaper->method('escapeUrl')->with($rawUrl)->willReturn($escapedUrl);
        $this->escaper->method('escapeHtml')->willReturnArgument(0);

        $options = $this->block->getSelectorOptions();

        $this->assertSame($escapedUrl, $options['source']);
    }

    public function testGetSelectorOptionsEscapesAttributeSetId(): void
    {
        // Attribute set ID that needs escaping
        $attributeSetId = '<script>alert("xss")</script>';
        $escapedAttributeSetId = '&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;';

        $this->product->method('getAttributeSetId')->willReturn($attributeSetId);
        $this->urlBuilder->method('getUrl')->willReturn('http://example.com/admin/url');
        $this->escaper->method('escapeUrl')->willReturnArgument(0);
        $this->escaper->method('escapeHtml')->with($attributeSetId)->willReturn($escapedAttributeSetId);

        $options = $this->block->getSelectorOptions();

        $this->assertSame($escapedAttributeSetId, $options['currentlySelected']);
    }

    public function testGetSelectorOptionsStructure(): void
    {
        $this->product->method('getAttributeSetId')->willReturn(10);
        $this->urlBuilder->method('getUrl')->willReturn('http://test.com');
        $this->escaper->method('escapeUrl')->willReturnArgument(0);
        $this->escaper->method('escapeHtml')->willReturnArgument(0);

        $options = $this->block->getSelectorOptions();

        // Assert all required keys exist
        $expectedKeys = ['source', 'className', 'showRecent', 'storageKey', 'minLength', 'currentlySelected'];
        foreach ($expectedKeys as $key) {
            $this->assertArrayHasKey($key, $options, "Key '$key' should exist in selector options");
        }

        // Assert correct types
        $this->assertIsString($options['source']);
        $this->assertIsString($options['className']);
        $this->assertIsBool($options['showRecent']);
        $this->assertIsString($options['storageKey']);
        $this->assertIsInt($options['minLength']);
    }
}
