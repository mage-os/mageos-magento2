<?php
/**
 * Copyright 2026 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Checkout\Test\Unit\Model\Cart;

use Magento\Checkout\Model\Cart\AjaxMessageResponse;
use Magento\Framework\Message\Collection;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\View\Layout;
use Magento\Framework\View\LayoutFactory;
use Magento\Framework\View\Element\Messages;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AjaxMessageResponseTest extends TestCase
{
    /**
     * @var ManagerInterface&MockObject
     */
    private ManagerInterface $messageManager;

    /**
     * @var LayoutFactory&MockObject
     */
    private LayoutFactory $layoutFactory;

    /**
     * @var AjaxMessageResponse
     */
    private AjaxMessageResponse $model;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);
        $this->messageManager = $this->createMock(ManagerInterface::class);
        $this->layoutFactory = $this->createMock(LayoutFactory::class);
        $this->model = $objectManager->getObject(
            AjaxMessageResponse::class,
            [
                'messageManager' => $this->messageManager,
                'layoutFactory' => $this->layoutFactory,
            ]
        );
    }

    /**
     * @return void
     */
    public function testShouldDisplayInlineWithoutBackUrl(): void
    {
        $this->assertTrue($this->model->shouldDisplayInline(null, 'https://example.com/product.html'));
    }

    /**
     * @return void
     */
    public function testShouldDisplayInlineForSamePageRedirect(): void
    {
        $this->assertTrue(
            $this->model->shouldDisplayInline(
                'https://example.com/product.html',
                'https://example.com/product.html?foo=bar'
            )
        );
    }

    /**
     * @return void
     */
    public function testShouldNotDisplayInlineForCrossPageRedirect(): void
    {
        $this->assertFalse(
            $this->model->shouldDisplayInline(
                'https://example.com/product.html',
                'https://example.com/category.html'
            )
        );
    }

    /**
     * @return void
     */
    public function testGetInlineMessagesClearsMessagesWhenRequested(): void
    {
        $messages = $this->createMock(Collection::class);
        $messages->expects($this->once())->method('getCount')->willReturn(1);

        $messagesBlock = $this->createMock(Messages::class);
        $messagesBlock->expects($this->once())->method('setMessages')->with($messages)->willReturnSelf();
        $messagesBlock->expects($this->once())->method('getGroupedHtml')->willReturn('<div>error</div>');

        $layout = $this->createMock(Layout::class);
        $layout->expects($this->once())->method('getMessagesBlock')->willReturn($messagesBlock);

        $this->messageManager->expects($this->once())
            ->method('getMessages')
            ->with(true)
            ->willReturn($messages);
        $this->layoutFactory->expects($this->once())->method('create')->willReturn($layout);

        $this->assertSame(
            ['html' => '<div>error</div>'],
            $this->model->getInlineMessages(true)
        );
    }
}
