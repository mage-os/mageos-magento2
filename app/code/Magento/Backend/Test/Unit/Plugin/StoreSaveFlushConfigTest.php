<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Plugin;

use Magento\Backend\Plugin\StoreSaveFlushConfig;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Backend\Controller\Adminhtml\System\Store\Save;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Backend\Model\View\Result\Redirect;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class StoreSaveFlushConfigTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var StoreSaveFlushConfig
     */
    private $plugin;

    /**
     * @var TypeListInterface|MockObject
     */
    private $cacheTypeListMock;

    /**
     * @var Save|MockObject
     */
    private $subjectMock;

    /**
     * @var RequestInterface|MockObject
     */
    private $requestMock;

    /**
     * @var Redirect|MockObject
     */
    private $resultMock;

    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $this->cacheTypeListMock = $this->createMock(TypeListInterface::class);
        $this->subjectMock = $this->createMock(Save::class);
        $this->requestMock = $this->getMockBuilder(RequestInterface::class)
            ->addMethods(['getPostValue'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->resultMock = $this->createMock(Redirect::class);
        $this->plugin = $this->objectManager->getObject(
            StoreSaveFlushConfig::class,
            ['cacheTypeList' => $this->cacheTypeListMock]
        );
    }

    public function testAfterExecuteWithStoreTypeStoreClearsConfigCache()
    {
        $postData = ['store_type' => 'store'];
        $this->subjectMock->method('getRequest')->willReturn($this->requestMock);
        $this->requestMock->method('getPostValue')->willReturn($postData);
        $this->cacheTypeListMock->expects($this->once())->method('cleanType')->with('config');
        $result = $this->plugin->afterExecute($this->subjectMock, $this->resultMock);
        $this->assertSame($this->resultMock, $result);
    }

    public function testAfterExecuteWithNonStoreTypeDoesNotClearCache()
    {
        $postData = ['store_type' => 'website'];
        $this->subjectMock->method('getRequest')->willReturn($this->requestMock);
        $this->requestMock->method('getPostValue')->willReturn($postData);
        $this->cacheTypeListMock->expects($this->never())->method('cleanType');
        $result = $this->plugin->afterExecute($this->subjectMock, $this->resultMock);
        $this->assertSame($this->resultMock, $result);
    }
}
