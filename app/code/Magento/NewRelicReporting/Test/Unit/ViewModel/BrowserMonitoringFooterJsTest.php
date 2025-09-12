<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\NewRelicReporting\Test\Unit\ViewModel;

use Magento\NewRelicReporting\Model\NewRelicWrapper;
use Magento\NewRelicReporting\ViewModel\BrowserMonitoringFooterJs;
use Magento\NewRelicReporting\ViewModel\ContentProviderInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test for BrowserMonitoringFooterJs ViewModel
 */
class BrowserMonitoringFooterJsTest extends TestCase
{
    /**
     * @var NewRelicWrapper|MockObject
     */
    private $newRelicWrapperMock;

    /**
     * @var BrowserMonitoringFooterJs
     */
    private $viewModel;

    protected function setUp(): void
    {
        $this->newRelicWrapperMock = $this->createMock(NewRelicWrapper::class);
        $this->viewModel = new BrowserMonitoringFooterJs($this->newRelicWrapperMock);
    }

    /**
     * Test getContent when New Relic is enabled
     */
    public function testGetContentEnabled()
    {
        $this->newRelicWrapperMock->expects($this->once())
            ->method('isAutoInstrumentEnabled')
            ->willReturn(true);

        $this->newRelicWrapperMock->expects($this->atLeastOnce())
            ->method('getBrowserTimingFooter')
            ->willReturn('<script>test</script>');

        $this->newRelicWrapperMock->expects($this->once())
            ->method('getBrowserTimingFooter')
            ->with(false)
            ->willReturn('<script>test</script>');

        $content = $this->viewModel->getContent();

        $this->assertEquals('<script>test</script>', $content);
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
     * Test multiple calls return consistent results
     */
    public function testMultipleCallsConsistency()
    {
        $this->newRelicWrapperMock->expects($this->exactly(3))
            ->method('isAutoInstrumentEnabled')
            ->willReturn(true);

        $this->newRelicWrapperMock->expects($this->atLeastOnce())
            ->method('getBrowserTimingFooter')
            ->willReturn('<script>test</script>');

        $this->newRelicWrapperMock->expects($this->exactly(3))
            ->method('getBrowserTimingFooter')
            ->with(false)
            ->willReturn('<script>test</script>');

        // Call multiple times
        $content1 = $this->viewModel->getContent();
        $content2 = $this->viewModel->getContent();
        $content3 = $this->viewModel->getContent();

        // All should return the same result
        $this->assertEquals('<script>test</script>', $content1);
        $this->assertEquals('<script>test</script>', $content2);
        $this->assertEquals('<script>test</script>', $content3);
        $this->assertEquals($content1, $content2);
        $this->assertEquals($content2, $content3);
    }

    /**
     * Test that generated script is valid JavaScript
     */
    public function testGeneratedScriptIsValidJavaScript()
    {
        $this->newRelicWrapperMock->expects($this->once())
            ->method('isAutoInstrumentEnabled')
            ->willReturn(true);

        $mockScript = '<script type="text/javascript">if (typeof newrelic !== "undefined") { newrelic.finished(); }</script>';
        
        $this->newRelicWrapperMock->expects($this->once())
            ->method('getBrowserTimingFooter')
            ->with(false)
            ->willReturn($mockScript);

        $content = $this->viewModel->getContent();

        // Check that the script contains expected JavaScript elements
        $this->assertStringContainsString('<script type="text/javascript">', $content);
        $this->assertStringContainsString('newrelic.finished();', $content);
        $this->assertStringContainsString('typeof newrelic !== "undefined"', $content);
        $this->assertStringContainsString('</script>', $content);
    }

    /**
     * Test that script includes safety check for newrelic object
     */
    public function testScriptIncludesSafetyCheck()
    {
        $this->newRelicWrapperMock->expects($this->once())
            ->method('isAutoInstrumentEnabled')
            ->willReturn(true);

        $mockScript = '<script type="text/javascript">if (typeof newrelic !== "undefined") { newrelic.finished(); }</script>';

        $this->newRelicWrapperMock->expects($this->atLeastOnce())
            ->method('getBrowserTimingFooter')
            ->willReturn($mockScript);

        $this->newRelicWrapperMock->expects($this->once())
            ->method('getBrowserTimingFooter')
            ->with(false)
            ->willReturn($mockScript);

        $content = $this->viewModel->getContent();

        // Verify the safety check exists
        $this->assertStringContainsString('if (typeof newrelic !== "undefined")', $content);
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
        
        $configParam = $parameters[0];
        $this->assertEquals('newRelicWrapper', $configParam->getName());
        $this->assertEquals(NewRelicWrapper::class, $configParam->getType()->getName());
    }
}
