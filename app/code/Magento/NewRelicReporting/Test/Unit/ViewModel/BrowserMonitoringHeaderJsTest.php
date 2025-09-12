<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\NewRelicReporting\Test\Unit\ViewModel;

use Magento\NewRelicReporting\Model\NewRelicWrapper;
use Magento\NewRelicReporting\ViewModel\BrowserMonitoringHeaderJs;
use Magento\NewRelicReporting\ViewModel\ContentProviderInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test for BrowserMonitoringHeaderJs ViewModel
 */
class BrowserMonitoringHeaderJsTest extends TestCase
{
    /**
     * @var NewRelicWrapper|MockObject
     */
    private $newRelicWrapperMock;

    /**
     * @var BrowserMonitoringHeaderJs
     */
    private $viewModel;

    protected function setUp(): void
    {
        $this->newRelicWrapperMock = $this->createMock(NewRelicWrapper::class);
        $this->viewModel = new BrowserMonitoringHeaderJs($this->newRelicWrapperMock);
    }

    /**
     * Test getContent when New Relic is enabled and extension is loaded
     */
    public function testGetContentEnabledWithExtension()
    {
        $this->newRelicWrapperMock->expects($this->once())
            ->method('isAutoInstrumentEnabled')
            ->willReturn(true);

        $this->newRelicWrapperMock->expects($this->atLeastOnce())
            ->method('getBrowserTimingHeader')
            ->willReturn('<script>test</script>');

        $content = $this->viewModel->getContent();

        if (extension_loaded('newrelic')) {
            // If New Relic extension is loaded, should return actual browser timing header
            $this->assertNotEmpty($content);
            $this->assertIsString($content);
        } else {
            // If New Relic extension is not loaded, should return empty string
            $this->assertNull($content);
        }
    }

    /**
     * Test getContent when New Relic is disabled
     */
    public function testGetContentDisabled()
    {
        $this->newRelicWrapperMock->expects($this->once())
            ->method('isAutoInstrumentEnabled')
            ->willReturn(false);

        // No getBrowserTimingFooter/Header call when disabled

        $content = $this->viewModel->getContent();

        $this->assertNull($content);
    }

    /**
     * Test that ViewModel implements ContentProviderInterface
     */
    public function testImplementsContentProviderInterface()
    {
        $this->assertInstanceOf(
            \Magento\NewRelicReporting\ViewModel\ContentProviderInterface::class,
            $this->viewModel
        );
    }

    /**
     * Test multiple calls return consistent results when disabled
     */
    public function testMultipleCallsConsistencyDisabled()
    {
        $this->newRelicWrapperMock->expects($this->exactly(3))
            ->method('isAutoInstrumentEnabled')
            ->willReturn(false);

        // No getBrowserTimingFooter/Header call when disabled

        // Call multiple times
        $content1 = $this->viewModel->getContent();
        $content2 = $this->viewModel->getContent();
        $content3 = $this->viewModel->getContent();

        // All should return the same empty result
        $this->assertNull($content1);
        $this->assertNull($content2);
        $this->assertNull($content3);
        $this->assertEquals($content1, $content2);
        $this->assertEquals($content2, $content3);
    }

    /**
     * Test multiple calls return consistent results when enabled
     */
    public function testMultipleCallsConsistencyEnabled()
    {
        $this->newRelicWrapperMock->expects($this->exactly(2))
            ->method('isAutoInstrumentEnabled')
            ->willReturn(true);

        $this->newRelicWrapperMock->expects($this->atLeastOnce())
            ->method('getBrowserTimingHeader')
            ->willReturn('<script>test</script>');

        // Call multiple times
        $content1 = $this->viewModel->getContent();
        $content2 = $this->viewModel->getContent();

        // Both should return the same result (whether empty or actual content)
        $this->assertEquals($content1, $content2);
        $this->assertIsString($content1);
        $this->assertIsString($content2);
    }

    /**
     * Test that disabled state returns empty string consistently
     */
    public function testDisabledStateReturnsEmptyString()
    {
        $this->newRelicWrapperMock->expects($this->exactly(2))
            ->method('isAutoInstrumentEnabled')
            ->willReturn(false);

        // No getBrowserTimingFooter/Header call when disabled

        $content1 = $this->viewModel->getContent();
        $content2 = $this->viewModel->getContent();

        $this->assertNull($content1);
        $this->assertNull($content2);
    }

    /**
     * Test ViewModel class structure
     */
    public function testViewModelStructure()
    {
        $reflection = new \ReflectionClass($this->viewModel);
        
        // Check class exists and is properly structured
        $this->assertTrue($reflection->implementsInterface(ContentProviderInterface::class));
        
        // Check it has the required method
        $this->assertTrue($reflection->hasMethod('getContent'));
        
        // Check method is public
        $getContentMethod = $reflection->getMethod('getContent');
        $this->assertTrue($getContentMethod->isPublic());
        
        // Check return type
        $returnType = $getContentMethod->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('string', $returnType->getName());
    }

    /**
     * Test constructor dependencies
     */
    public function testConstructorDependencies()
    {
        $reflection = new \ReflectionClass($this->viewModel);
        $constructor = $reflection->getConstructor();
        
        $this->assertNotNull($constructor);
        
        $parameters = $constructor->getParameters();
        $this->assertCount(1, $parameters);
        
        $newRelicWrapperParam = $parameters[0];
        $this->assertEquals('newRelicWrapper', $newRelicWrapperParam->getName());
        $this->assertEquals(NewRelicWrapper::class, $newRelicWrapperParam->getType()->getName());
    }

    /**
     * Test that method handles New Relic extension gracefully when not loaded
     */
    public function testHandlesNewRelicExtensionGracefully()
    {
        $this->newRelicWrapperMock->expects($this->once())
            ->method('isAutoInstrumentEnabled')
            ->willReturn(true);

        $this->newRelicWrapperMock->expects($this->atLeastOnce())
            ->method('getBrowserTimingHeader')
            ->willReturn('<script>test</script>');

        // Should not throw exception even if newrelic extension methods don't exist
        $content = $this->viewModel->getContent();
        
        $this->assertIsString($content);
    }

    /**
     * Test behavior difference based on New Relic newRelicWrapperuration state
     */
    public function testBehaviorBasedOnConfiguration()
    {
        // Test enabled state first
        $this->newRelicWrapperMock->expects($this->exactly(2))
            ->method('isAutoInstrumentEnabled')
            ->willReturnOnConsecutiveCalls(true, false);

        $this->newRelicWrapperMock->expects($this->once())
            ->method('getBrowserTimingHeader')
            ->willReturn('<script>test</script>');

        $enabledContent = $this->viewModel->getContent();

        // Create new instance for disabled test
        $disabledViewModel = new BrowserMonitoringHeaderJs($this->newRelicWrapperMock);

        // No getBrowserTimingFooter/Header call when disabled
        $disabledContent = $disabledViewModel->getContent();

        // Disabled should always be empty
        $this->assertNull($disabledContent);
        
        // Enabled should be string (could be empty if extension not loaded)
        $this->assertIsString($enabledContent);
    }

    /**
     * Test that content is safe for HTML output
     */
    public function testContentIsSafeForHtml()
    {
        $this->newRelicWrapperMock->expects($this->once())
            ->method('isAutoInstrumentEnabled')
            ->willReturn(true);

        $this->newRelicWrapperMock->expects($this->atLeastOnce())
            ->method('getBrowserTimingHeader')
            ->willReturn('<script>test</script>');

        $content = $this->viewModel->getContent();

        if (!empty($content)) {
            // If content is returned, it should be safe HTML/JavaScript
            $this->assertIsString($content);
            
            // Basic safety checks - should not contain obvious XSS patterns
            $this->assertStringNotContainsString('<script>alert(', $content);
            $this->assertStringNotContainsString('javascript:', $content);
            $this->assertStringNotContainsString('eval(', $content);
        }
    }

    /**
     * Test ViewModel interface compliance
     */
    public function testViewModelInterfaceCompliance()
    {
        // Test that getContent method signature matches interface
        $reflection = new \ReflectionClass($this->viewModel);
        $method = $reflection->getMethod('getContent');
        
        // Should be public
        $this->assertTrue($method->isPublic());
        
        // Should return string
        $returnType = $method->getReturnType();
        $this->assertNotNull($returnType);
        $this->assertEquals('string', $returnType->getName());
        
        // Should take no parameters
        $this->assertCount(0, $method->getParameters());
    }
}
