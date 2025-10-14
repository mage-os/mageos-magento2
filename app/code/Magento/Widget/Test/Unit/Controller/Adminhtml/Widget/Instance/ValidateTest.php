<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Widget\Test\Unit\Controller\Adminhtml\Widget\Instance;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\ViewInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\View\Element\Messages;
use Magento\Framework\View\Layout;
use Magento\Framework\View\LayoutInterface;
use Magento\Widget\Controller\Adminhtml\Widget\Instance\Validate;
use Magento\Widget\Model\Widget\Instance;
use Magento\Widget\Model\Widget\InstanceFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test for \Magento\Widget\Controller\Adminhtml\Widget\Instance\Validate.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
class ValidateTest extends TestCase
{
    /**
     * @var string
     */
    private $errorMessage = 'We cannot create the widget instance because it is missing required information.';

    /**
     * @var Validate
     */
    private $model;

    /**
     * @var ManagerInterface|MockObject
     */
    private $messageManagerMock;

    /**
     * @var MockObject
     */
    private $responseMock;

    /**
     * @var MockObject
     */
    private $widgetMock;

    /**
     * @var Messages|MockObject
     */
    private $messagesBlock;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $objectManager = new ObjectManager($this);

        $request = $this->createMock(RequestInterface::class);
        $this->messageManagerMock = $this->createMock(ManagerInterface::class);
        $viewMock = $this->createMock(ViewInterface::class);
        $this->messagesBlock = $this->createMock(Messages::class);
        $layoutMock = $this->createPartialMock(Layout::class, ['getMessagesBlock', 'initMessages']);
        $layoutMock->method('getMessagesBlock')->willReturn($this->messagesBlock);
        $layoutMock->method('initMessages')->willReturnSelf();
        $viewMock->method('getLayout')->willReturn($layoutMock);
        $this->responseMock = $this->createMock(\Magento\Framework\App\Response\Http::class);
        $this->responseMock->method('representJson')->willReturnSelf();

        $context = $this->createMock(Context::class);
        $context->method('getRequest')->willReturn($request);
        $context->method('getMessageManager')->willReturn($this->messageManagerMock);
        $context->method('getView')->willReturn($viewMock);
        $context->method('getResponse')->willReturn($this->responseMock);

        $this->widgetMock = $this->createPartialMock(Instance::class, ['isCompleteToCreate']);
        $this->widgetMock->method('isCompleteToCreate')->willReturn(true);
        
        $widgetFactoryMock = $this->createMock(InstanceFactory::class);
        $widgetFactoryMock->method('create')->willReturn($this->widgetMock);

        $this->model = new Validate(
            $context,
            $this->createMock(\Magento\Framework\Registry::class),
            $widgetFactoryMock,
            $this->createMock(\Psr\Log\LoggerInterface::class),
            $this->createMock(\Magento\Framework\Math\Random::class),
            $this->createMock(\Magento\Framework\Translate\InlineInterface::class)
        );
    }

    /**
     * Test execute
     *
     * @return void
     */
    public function testExecute(): void
    {
        $this->messageManagerMock->expects($this->never())
            ->method('addErrorMessage')
            ->with($this->errorMessage);

        $this->model->execute();
    }

    /**
     * Test execute with Phrase object
     *
     * @return void
     */
    public function testExecutePhraseObject(): void
    {
        $failingWidgetMock = $this->createPartialMock(Instance::class, ['isCompleteToCreate']);
        $failingWidgetMock->method('isCompleteToCreate')->willReturn(false);

        $widgetFactoryMock = $this->createMock(InstanceFactory::class);
        $widgetFactoryMock->method('create')->willReturn($failingWidgetMock);

        $objectManager = new ObjectManager($this);
        $request = $this->createMock(RequestInterface::class);
        $messageManagerMock = $this->createMock(ManagerInterface::class);
        $viewMock = $this->createMock(ViewInterface::class);
        $messagesBlock = $this->createMock(Messages::class);
        $layoutMock = $this->createPartialMock(Layout::class, ['getMessagesBlock', 'initMessages']);
        $layoutMock->method('getMessagesBlock')->willReturn($this->messagesBlock);
        $layoutMock->method('initMessages')->willReturnSelf();
        $responseMock = $this->createMock(\Magento\Framework\App\Response\Http::class);
        $responseMock->method('representJson')->willReturnSelf();

        $viewMock->method('getLayout')->willReturn($layoutMock);

        $context = $this->createMock(Context::class);
        $context->method('getRequest')->willReturn($request);
        $context->method('getMessageManager')->willReturn($messageManagerMock);
        $context->method('getView')->willReturn($viewMock);
        $context->method('getResponse')->willReturn($responseMock);

        $this->model = new Validate(
            $context,
            $this->createMock(\Magento\Framework\Registry::class),
            $widgetFactoryMock,
            $this->createMock(\Psr\Log\LoggerInterface::class),
            $this->createMock(\Magento\Framework\Math\Random::class),
            $this->createMock(\Magento\Framework\Translate\InlineInterface::class)
        );

        $messageManagerMock->expects($this->once())
            ->method('addErrorMessage')
            ->with($this->errorMessage);
        $this->messagesBlock->expects($this->once())
            ->method('getGroupedHtml')
            ->willReturn($this->errorMessage);

        $this->model->execute();
    }
}
