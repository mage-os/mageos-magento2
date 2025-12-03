<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Block\Adminhtml\Form\Renderer\Fieldset;

use Magento\Catalog\Block\Adminhtml\Form\Renderer\Fieldset\Element;
use PHPUnit\Framework\TestCase;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\DataObject;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Escaper;
use Magento\Framework\Math\Random;
use Magento\Framework\View\Helper\SecureHtmlRenderer;

/**
 * @covers \Magento\Catalog\Block\Adminhtml\Form\Renderer\Fieldset\Element
 */
class ElementTest extends TestCase
{
    /** @var Element */
    private $block;

    /** @var AbstractElement */
    private $elementMock;

    /** @var DataObject */
    private $formDataObject;

    /** @var Product */
    private $dataObjectMock;

    /** @var Attribute */
    private $attributeMock;

    /** @var StoreManagerInterface */
    private $storeManagerMock;

    protected function setUp(): void
    {
        // Preserve real methods on AbstractElement so magic __call and setData work
        $this->elementMock = $this->getMockBuilder(AbstractElement::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        $this->dataObjectMock = $this->createMock(Product::class);
        $this->attributeMock = $this->createMock(Attribute::class);
        $this->storeManagerMock = $this->createMock(StoreManagerInterface::class);

        // Use DataObject to provide magic getDataObject() via __call
        $this->formDataObject = new DataObject([
            'data_object' => $this->dataObjectMock,
            'html_id_prefix' => '',
            'html_id_suffix' => ''
        ]);

        // Attach form and attribute to the element via real methods/data
        $this->elementMock->setForm($this->formDataObject);
        $this->elementMock->setData('entity_attribute', $this->attributeMock);
        $this->elementMock->setData('name', 'test_name');
        $this->elementMock->setData('html_id', 'test_id');

        // Inject Escaper, Random and SecureHtmlRenderer into AbstractElement (constructor is disabled)
        $escaper = new Escaper();
        $randomMock = $this->createMock(Random::class);
        $randomMock->method('getRandomString')->willReturn('abcdef1234');
        $secureRendererMock = $this->createMock(SecureHtmlRenderer::class);
        $secureRendererMock->method('renderStyleAsTag')->willReturn('');
        $secureRendererMock->method('renderEventListenerAsTag')->willReturn('');

        $abstractRef = new \ReflectionClass(AbstractElement::class);
        $escaperProp = $abstractRef->getProperty('_escaper');
        $escaperProp->setAccessible(true);
        $escaperProp->setValue($this->elementMock, $escaper);
        $randomProp = $abstractRef->getProperty('random');
        $randomProp->setAccessible(true);
        $randomProp->setValue($this->elementMock, $randomMock);
        $secureRendererProp = $abstractRef->getProperty('secureRenderer');
        $secureRendererProp->setAccessible(true);
        $secureRendererProp->setValue($this->elementMock, $secureRendererMock);

        // Create block mock with real methods (no constructor side effects)
        $this->block = $this->getMockBuilder(Element::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        // Inject _storeManager and _element via reflection
        $ref = new \ReflectionClass($this->block);
        $storeProp = $ref->getProperty('_storeManager');
        $storeProp->setAccessible(true);
        $storeProp->setValue($this->block, $this->storeManagerMock);

        $elementProp = $ref->getProperty('_element');
        $elementProp->setAccessible(true);
        $elementProp->setValue($this->block, $this->elementMock);
    }

    /**
     * Test getDataObject returns the form's data object
     */
    public function testGetDataObject(): void
    {
        $this->assertSame($this->dataObjectMock, $this->block->getDataObject());
    }

    /**
     * Test getAttribute returns the element's entity attribute
     */
    public function testGetAttribute(): void
    {
        $this->assertSame($this->attributeMock, $this->block->getAttribute());
    }

    /**
     * Test getAttributeCode proxies to attribute->getAttributeCode
     */
    public function testGetAttributeCode(): void
    {
        $this->attributeMock->method('getAttributeCode')->willReturn('test_code');
        $this->assertSame('test_code', $this->block->getAttributeCode());
    }

    /**
     * Test canDisplayUseDefault returns true when attribute not global and entity has id/store
     */
    public function testCanDisplayUseDefaultReturnsTrue(): void
    {
        $this->attributeMock->method('isScopeGlobal')->willReturn(false);
        $this->dataObjectMock->method('getId')->willReturn(1);
        $this->dataObjectMock->method('getStoreId')->willReturn(2);

        $this->assertTrue($this->block->canDisplayUseDefault());
    }

    /**
     * Test canDisplayUseDefault returns false when attribute is global
     */
    public function testCanDisplayUseDefaultReturnsFalse(): void
    {
        $this->attributeMock->method('isScopeGlobal')->willReturn(true);
        $this->assertFalse($this->block->canDisplayUseDefault());
    }

    /**
     * Test usedDefault returns true when no store value flag is set
     */
    public function testUsedDefaultReturnsTrueWhenNoStoreValueFlag(): void
    {
        $this->attributeMock->method('getAttributeCode')->willReturn('test_code');
        $this->dataObjectMock->method('getAttributeDefaultValue')->willReturn('default');
        $this->dataObjectMock->method('getExistsStoreValueFlag')->willReturn(false);

        $this->assertTrue($this->block->usedDefault());
    }

    /**
     * Test usedDefault returns false when value equals default for non-default store
     */
    public function testUsedDefaultReturnsFalseWhenValueEqualsDefault(): void
    {
        $this->attributeMock->method('getAttributeCode')->willReturn('test_code');
        $this->dataObjectMock->method('getAttributeDefaultValue')->willReturn('default');
        $this->dataObjectMock->method('getExistsStoreValueFlag')->willReturn(true);
        $this->dataObjectMock->method('getStoreId')->willReturn(2);
        $this->elementMock->setData('value', 'default');

        $this->assertFalse($this->block->usedDefault());
    }

    /**
     * Test checkFieldDisable sets element disabled when default is used
     */
    public function testCheckFieldDisableDisablesElement(): void
    {
        $this->attributeMock->method('isScopeGlobal')->willReturn(false);
        $this->dataObjectMock->method('getId')->willReturn(1);
        $this->dataObjectMock->method('getStoreId')->willReturn(2);
        $this->attributeMock->method('getAttributeCode')->willReturn('test_code');
        $this->dataObjectMock->method('getAttributeDefaultValue')->willReturn('default');
        $this->dataObjectMock->method('getExistsStoreValueFlag')->willReturn(false);

        $this->block->checkFieldDisable();
        $this->assertTrue((bool)$this->elementMock->getData('disabled'));
    }

    /**
     * Test getScopeLabel returns [GLOBAL] when attribute is global
     */
    public function testGetScopeLabelGlobal(): void
    {
        $this->storeManagerMock->method('isSingleStoreMode')->willReturn(false);
        $this->attributeMock->method('getFrontendInput')->willReturn('text');
        $this->attributeMock->method('isScopeGlobal')->willReturn(true);

        $this->assertSame('[GLOBAL]', (string)$this->block->getScopeLabel());
    }

    /**
     * Test getScopeLabel returns empty string in single store mode
     */
    public function testGetScopeLabelReturnsEmptyForSingleStoreMode(): void
    {
        $this->storeManagerMock->method('isSingleStoreMode')->willReturn(true);
        $this->assertSame('', (string)$this->block->getScopeLabel());
    }

    /**
     * Test getElementLabelHtml contains label text when label is set
     */
    public function testGetElementLabelHtmlWithLabel(): void
    {
        $this->elementMock->setData('label', 'Test Label');
        $html = $this->block->getElementLabelHtml();
        $this->assertStringContainsString('Test Label', $html);
    }

    /**
     * Test getElementLabelHtml returns string when label is empty
     */
    public function testGetElementLabelHtmlWithoutLabel(): void
    {
        $this->elementMock->setData('label', '');
        $html = $this->block->getElementLabelHtml();
        $this->assertIsString($html);
    }

    /**
     * Test getElementHtml renders input element markup
     */
    public function testGetElementHtml(): void
    {
        $this->elementMock->setData('value', '');
        $html = $this->block->getElementHtml();
        $this->assertStringContainsString('<input', $html);
    }
}
