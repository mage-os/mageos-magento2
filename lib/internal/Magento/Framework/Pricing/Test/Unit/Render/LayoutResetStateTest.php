<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Pricing\Test\Unit\Render;

use Magento\Framework\Pricing\Render\Layout;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\View\LayoutFactory;
use Magento\Framework\View\LayoutInterface;
use PHPUnit\Framework\TestCase;

/**
 * Pricing render layout must follow the current page's cacheable flag after a state reset
 * (long-running application servers reuse the object across requests).
 */
class LayoutResetStateTest extends TestCase
{
    public function testResetStateRecreatesInnerLayoutWithCurrentCacheableFlag(): void
    {
        $cacheableLayout = $this->createMock(LayoutInterface::class);
        $nonCacheableLayout = $this->createMock(LayoutInterface::class);
        $generalLayout = $this->createMock(LayoutInterface::class);
        $generalLayout->expects($this->exactly(2))
            ->method('isCacheable')
            ->willReturnOnConsecutiveCalls(true, false);
        $layoutFactory = $this->getMockBuilder(LayoutFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['create'])
            ->getMock();
        $layoutFactory->expects($this->exactly(2))
            ->method('create')
            ->willReturnCallback(
                fn (array $data) => $data['cacheable'] ? $cacheableLayout : $nonCacheableLayout
            );
        $cacheableLayout->expects($this->once())->method('getBlock')->with('block.name');
        $nonCacheableLayout->expects($this->once())->method('getBlock')->with('block.name');

        $model = (new ObjectManager($this))->getObject(
            Layout::class,
            ['layoutFactory' => $layoutFactory, 'generalLayout' => $generalLayout]
        );

        $model->getBlock('block.name');
        $model->_resetState();
        $model->getBlock('block.name');
    }

    public function testInnerLayoutIsCreatedOncePerRequest(): void
    {
        $innerLayout = $this->createMock(LayoutInterface::class);
        $generalLayout = $this->createMock(LayoutInterface::class);
        $generalLayout->expects($this->once())->method('isCacheable')->willReturn(true);
        $layoutFactory = $this->getMockBuilder(LayoutFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['create'])
            ->getMock();
        $layoutFactory->expects($this->once())->method('create')->willReturn($innerLayout);
        $innerLayout->expects($this->exactly(2))->method('getBlock');

        $model = (new ObjectManager($this))->getObject(
            Layout::class,
            ['layoutFactory' => $layoutFactory, 'generalLayout' => $generalLayout]
        );

        $model->getBlock('a');
        $model->getBlock('b');
    }
}
