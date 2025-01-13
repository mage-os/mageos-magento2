<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
namespace Magento\Theme\Test\Unit\Plugin;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\View\DesignLoader;
use Magento\Theme\Plugin\LoadDesignPlugin;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class LoadDesignPluginTest extends TestCase
{
    /**
     * @return void
     * @throws Exception
     */
    public function testBeforeExecute(): void
    {
        /** @var MockObject|ActionInterface $actionMock */
        $actionMock = $this->createMock(Action::class);

        /** @var MockObject|DesignLoader $designLoaderMock */
        $designLoaderMock = $this->createMock(DesignLoader::class);

        /** @var MockObject|ManagerInterface $messageManagerMock */
        $messageManagerMock = $this->createMock(ManagerInterface::class);

        $plugin = new LoadDesignPlugin($designLoaderMock, $messageManagerMock);

        $designLoaderMock->expects($this->once())->method('load');
        $plugin->beforeExecute($actionMock);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testBeforeExecuteNotThrowException(): void
    {
        /** @var MockObject|ActionInterface $actionMock */
        $actionMock = $this->createMock(Action::class);

        /** @var MockObject|DesignLoader $designLoaderMock */
        $designLoaderMock = $this->createMock(DesignLoader::class);

        /** @var MockObject|ManagerInterface $messageManagerMock */
        $messageManagerMock = $this->createMock(ManagerInterface::class);

        $plugin = new LoadDesignPlugin($designLoaderMock, $messageManagerMock);

        $exceptionMessage = 'test message';
        $designLoaderMock->expects($this->once())
            ->method('load')
            ->willThrowException(new \InvalidArgumentException($exceptionMessage));
        $messageManagerMock->expects($this->once())
            ->method('addErrorMessage')
            ->with($exceptionMessage);

        $plugin->beforeExecute($actionMock);
    }
}
