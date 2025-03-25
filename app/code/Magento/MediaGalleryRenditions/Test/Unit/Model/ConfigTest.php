<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryRenditions\Test\Unit\Model;

use Magento\Framework\App\Config\Initial;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use Magento\MediaGalleryRenditions\Model\Config;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    /**
     * @var ScopeConfigInterface|MockObject
     */
    private $scopeConfigMock;

    /**
     * @var Initial|MockObject
     */
    private $initialConfigMock;

    /**
     * @var ResourceConnection|MockObject
     */
    private $resourceConnectionMock;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var AdapterInterface|MockObject
     */
    private $connectionMock;

    /**
     * @var Select|MockObject
     */
    private $selectMock;

    protected function setUp(): void
    {
        $this->scopeConfigMock = $this->createMock(ScopeConfigInterface::class);
        $this->initialConfigMock = $this->createMock(Initial::class);
        $this->resourceConnectionMock = $this->createMock(ResourceConnection::class);
        $this->connectionMock = $this->getMockBuilder(AdapterInterface::class)
            ->addMethods(['fetchColumn'])
            ->getMockForAbstractClass();
        $this->selectMock = $this->createMock(Select::class);
        $this->resourceConnectionMock->method('getConnection')
            ->willReturn($this->connectionMock);
        $this->resourceConnectionMock->method('getTableName')
            ->with('core_config_data')
            ->willReturn('core_config_data');
        $this->config = new Config(
            $this->scopeConfigMock,
            $this->initialConfigMock,
            $this->resourceConnectionMock
        );
    }

    /**
     * Test getWidth() with successful database retrieval
     */
    public function testGetWidthSuccess(): void
    {
        $expectedWidth = 800;
        $this->connectionMock->method('select')
            ->willReturn($this->selectMock);
        $this->selectMock->method('from')
            ->willReturnSelf();
        $this->selectMock->method('where')
            ->willReturnSelf();
        $this->connectionMock->method('query')
            ->with($this->selectMock)
            ->willReturnSelf();
        $this->connectionMock->method('fetchColumn')
            ->willReturn((string)$expectedWidth);
        $result = $this->config->getWidth();
        $this->assertEquals($expectedWidth, $result);
    }

    /**
     * Test getWidth() with empty database result falling back to initial config
     */
    public function testGetWidthFallback(): void
    {
        $expectedWidth = 600;
        $this->connectionMock->method('select')
            ->willReturn($this->selectMock);
        $this->selectMock->method('from')
            ->willReturnSelf();
        $this->selectMock->method('where')
            ->willReturnSelf();
        $this->connectionMock->method('query')
            ->with($this->selectMock)
            ->willReturnSelf();
        $this->connectionMock->method('fetchColumn')
            ->willReturn(false);
        $this->initialConfigMock->method('getData')
            ->with('default')
            ->willReturn([
                'system' => [
                    'media_gallery_renditions' => [
                        'width' => $expectedWidth
                    ]
                ]
            ]);
        $result = $this->config->getWidth();
        $this->assertEquals($expectedWidth, $result);
    }

    /**
     * Test getHeight() with successful database retrieval
     */
    public function testGetHeightSuccess(): void
    {
        $expectedHeight = 600;
        $this->connectionMock->method('select')
            ->willReturn($this->selectMock);
        $this->selectMock->method('from')
            ->willReturnSelf();
        $this->selectMock->method('where')
            ->willReturnSelf();
        $this->connectionMock->method('query')
            ->with($this->selectMock)
            ->willReturnSelf();
        $this->connectionMock->method('fetchColumn')
            ->willReturn((string)$expectedHeight);
        $result = $this->config->getHeight();
        $this->assertEquals($expectedHeight, $result);
    }

    /**
     * Test getHeight() with empty database result falling back to initial config
     */
    public function testGetHeightFallback(): void
    {
        $expectedHeight = 400;
        $this->connectionMock->method('select')
            ->willReturn($this->selectMock);
        $this->selectMock->method('from')
            ->willReturnSelf();
        $this->selectMock->method('where')
            ->willReturnSelf();
        $this->connectionMock->method('query')
            ->with($this->selectMock)
            ->willReturnSelf();
        $this->connectionMock->method('fetchColumn')
            ->willReturn(null);
        $this->initialConfigMock->method('getData')
            ->with('default')
            ->willReturn([
                'system' => [
                    'media_gallery_renditions' => [
                        'height' => $expectedHeight
                    ]
                ]
            ]);
        $result = $this->config->getHeight();
        $this->assertEquals($expectedHeight, $result);
    }
}
