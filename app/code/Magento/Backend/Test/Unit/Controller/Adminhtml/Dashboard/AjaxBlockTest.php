<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Controller\Adminhtml\Dashboard;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Block\Dashboard\Sales;
use Magento\Backend\Block\Dashboard\Totals;
use Magento\Backend\Controller\Adminhtml\Dashboard\AjaxBlock;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Layout;
use Magento\Framework\View\LayoutFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class AjaxBlockTest extends TestCase
{
    #[DataProvider('blockProvider')]
    public function testExecuteRendersRequestedBlock(string $param, ?string $blockClass, array $blockArgs): void
    {
        $request = $this->createMock(RequestInterface::class);
        $request->method('getParam')->with('block')->willReturn($param);

        $context = $this->createMock(Context::class);
        $context->method('getRequest')->willReturn($request);

        $block = $this->createMock(AbstractBlock::class);
        $block->method('toHtml')->willReturn('<div>rendered</div>');

        $layout = $this->createMock(Layout::class);
        if ($blockClass === null) {
            $layout->expects($this->never())->method('createBlock');
        } else {
            $layout->expects($this->once())
                ->method('createBlock')
                ->with($blockClass, '', $blockArgs)
                ->willReturn($block);
        }
        $layoutFactory = $this->createMock(LayoutFactory::class);
        $layoutFactory->method('create')->willReturn($layout);

        $raw = $this->createMock(Raw::class);
        $raw->expects($this->once())
            ->method('setContents')
            ->with($blockClass === null ? '' : '<div>rendered</div>')
            ->willReturnSelf();
        $rawFactory = $this->createMock(RawFactory::class);
        $rawFactory->method('create')->willReturn($raw);

        $controller = (new ObjectManager($this))->getObject(
            AjaxBlock::class,
            ['context' => $context, 'resultRawFactory' => $rawFactory, 'layoutFactory' => $layoutFactory]
        );

        $this->assertSame($raw, $controller->execute());
    }

    public static function blockProvider(): array
    {
        return [
            'totals' => ['totals', Totals::class, []],
            'sales renders synchronously' => ['sales', Sales::class, ['data' => ['render_sync' => true]]],
            'unknown block' => ['bogus', null, []],
        ];
    }
}
