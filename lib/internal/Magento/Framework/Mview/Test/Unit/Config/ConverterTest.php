<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Mview\Test\Unit\Config;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Mview\Config\Converter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ConverterTest extends TestCase
{
    /**
     * @var Converter|MockObject
     */
    protected $_model;

    /**
     * @var ResourceConnection|MockObject
     */
    protected $resourceConnectionMock;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface|MockObject
     */
    protected $connectionMock;

    protected function setUp(): void
    {
        $this->connectionMock = $this->createMock(AdapterInterface::class);
        $this->resourceConnectionMock = $this->createMock(ResourceConnection::class);
        $this->_model = new Converter($this->resourceConnectionMock);
    }

    public function testConvert()
    {
        $this->resourceConnectionMock->method('getConnection')
            ->willReturn($this->connectionMock);
        $data = include __DIR__ . '/../_files/mview_config.php';
        $dom = new \DOMDocument();
        $dom->loadXML($data['inputXML']);

        $this->assertEquals($data['expected'], $this->_model->convert($dom));
    }
}
