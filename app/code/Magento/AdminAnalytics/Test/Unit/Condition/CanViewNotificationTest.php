<?php
/**
 * Copyright 2024 Adobe
 * All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\AdminAnalytics\Test\Unit\Condition;

use Magento\AdminAnalytics\Model\Condition\CanViewNotification;
use Magento\AdminAnalytics\Model\ResourceModel\Viewer\Logger;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CanViewNotificationTest extends TestCase
{
    /** @var CanViewNotification */
    private $canViewNotification;

    /** @var  Logger|MockObject */
    private $viewerLoggerMock;

    /** @var ProductMetadataInterface|MockObject */
    private $productMetadataMock;

    /** @var MockObject|CacheInterface $cacheStorageMock */
    private $cacheStorageMock;

    /** @var (ScopeConfigInterface&MockObject)  */
    private $scopeConfigMock;

    protected function setUp(): void
    {
        $this->cacheStorageMock = $this->getMockBuilder(CacheInterface::class)
            ->getMockForAbstractClass();
        $this->viewerLoggerMock = $this->createMock(Logger::class);
        $this->productMetadataMock = $this->getMockForAbstractClass(ProductMetadataInterface::class);
        $this->scopeConfigMock = $this->createMock(ScopeConfigInterface::class);
        $objectManager = new ObjectManager($this);
        $this->canViewNotification = $objectManager->getObject(
            CanViewNotification::class,
            [
                'viewerLogger' => $this->viewerLoggerMock,
                'productMetadata' => $this->productMetadataMock,
                'cacheStorage' => $this->cacheStorageMock,
                'scopeConfig' => $this->scopeConfigMock,
            ]
        );
    }

    /**
     * @param $expected
     * @param $cacheResponse
     * @param $logExists
     * @dataProvider isVisibleProvider
     */
    public function testIsVisibleLoadDataFromLog($expected, $cacheResponse, $logExists, $configEnabled)
    {
        $this->cacheStorageMock->expects($this->once())
            ->method('load')
            ->with('admin-usage-notification-popup')
            ->willReturn($cacheResponse);
        $this->viewerLoggerMock
            ->method('checkLogExists')
            ->willReturn($logExists);
        $this->cacheStorageMock
            ->method('save')
            ->with('log-exists', 'admin-usage-notification-popup');
        $this->scopeConfigMock->method('isSetFlag')->with('admin/usage/enabled')->willReturn($configEnabled);
        $this->assertEquals($expected, $this->canViewNotification->isVisible([]));
    }

    /**
     * @return array
     */
    public static function isVisibleProvider()
    {
        return [
            [true, false, false, true], // first login, no cache, config enabled
            [false, false, false, false], // first login, no cache, config disabled
            [false, 'log-exists', true, true], // first login, cache exists, config enabled
            [false, false, true, true], // first login, cache exists, config enabled
        ];
    }
}
