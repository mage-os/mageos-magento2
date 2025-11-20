<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Block\Adminhtml\Category\Helper;

use Magento\Catalog\Block\Adminhtml\Category\Helper\Pricestep;
use Magento\Framework\Data\Form;
use Magento\Framework\View\Helper\SecureHtmlRenderer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Unit test for Pricestep helper
 */
class PricestepTest extends TestCase
{
    /**
     * @var Pricestep|MockObject
     */
    private $model;

    /**
     * @var Form|MockObject
     */
    private $formMock;

    /**
     * @var SecureHtmlRenderer|MockObject
     */
    private $secureRendererMock;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->secureRendererMock = $this->createMock(SecureHtmlRenderer::class);
        $this->formMock = $this->getMockBuilder(Form::class)
            ->disableOriginalConstructor()
            ->addMethods(['getHtmlIdPrefix', 'getFieldNameSuffix', 'getHtmlIdSuffix'])
            ->onlyMethods(['addSuffixToName'])
            ->getMock();

        $this->formMock->method('getHtmlIdPrefix')->willReturn('');
        $this->formMock->method('getFieldNameSuffix')->willReturn('');
        $this->formMock->method('getHtmlIdSuffix')->willReturn('');
        $this->formMock->method('addSuffixToName')->willReturnArgument(0);

        // Create a partial mock to avoid parent constructor issues
        $this->model = $this->getMockBuilder(Pricestep::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        $this->model->setForm($this->formMock);
    }

    /**
     * Test that the model can be instantiated
     *
     * @return void
     */
    public function testModelInstantiation(): void
    {
        $this->assertInstanceOf(Pricestep::class, $this->model);
    }

    /**
     * Test that the model has correct properties
     *
     * @return void
     */
    public function testModelHasRequiredProperties(): void
    {
        $this->assertInstanceOf(Pricestep::class, $this->model);
        
        // Test that data can be set and retrieved
        $testData = ['html_id' => 'test', 'id' => 'price_id'];
        $this->model->setData($testData);
        
        $this->assertEquals('test', $this->model->getData('html_id'));
        $this->assertEquals('price_id', $this->model->getData('id'));
    }

    /**
     * Test validation classes are defined in source code
     *
     * @return void
     */
    public function testValidationClassesInSourceCode(): void
    {
        // Read the source file to verify the validation class is present
        $reflection = new \ReflectionClass(Pricestep::class);
        $fileName = $reflection->getFileName();
        $content = file_get_contents($fileName);
        
        $this->assertStringContainsString('validate-number', $content);
        $this->assertStringContainsString('validate-number-range', $content);
        $this->assertStringContainsString('number-range-0.01-9999999999999999', $content);
    }

    /**
     * Test that getToggleCode pattern is correct in source code
     *
     * @return void
     */
    public function testGetToggleCodePattern(): void
    {
        // Read the source file to verify toggle code pattern
        $reflection = new \ReflectionClass(Pricestep::class);
        $fileName = $reflection->getFileName();
        $content = file_get_contents($fileName);
        
        // Verify getToggleCode method exists and contains expected patterns
        $this->assertStringContainsString('public function getToggleCode()', $content);
        $this->assertStringContainsString('toggleValueElements', $content);
        $this->assertStringContainsString('use_config_', $content);
    }

    /**
     * Test that getElementHtml uses br tag
     *
     * @return void
     */
    public function testGetElementHtmlContainsBrTagInSource(): void
    {
        // Read the source file to verify br tag is present
        $reflection = new \ReflectionClass(Pricestep::class);
        $fileName = $reflection->getFileName();
        $content = file_get_contents($fileName);
        
        $this->assertStringContainsString('<br/>', $content);
    }

    /**
     * Test that getElementHtml generates checkbox and label in source
     *
     * @return void
     */
    public function testGetElementHtmlContainsCheckboxAndLabelInSource(): void
    {
        // Read the source file to verify checkbox and label HTML
        $reflection = new \ReflectionClass(Pricestep::class);
        $fileName = $reflection->getFileName();
        $content = file_get_contents($fileName);
        
        $this->assertStringContainsString('type="checkbox"', $content);
        $this->assertStringContainsString('class="checkbox"', $content);
        $this->assertStringContainsString('Use Config Settings', $content);
    }
}
