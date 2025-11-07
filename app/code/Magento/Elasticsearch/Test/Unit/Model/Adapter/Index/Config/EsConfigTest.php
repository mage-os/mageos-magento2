<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Elasticsearch\Test\Unit\Model\Adapter\Index\Config;

use Magento\Elasticsearch\Model\Adapter\Index\Config\EsConfig;
use Magento\Framework\Config\CacheInterface;
use Magento\Framework\Config\ReaderInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManagerHelper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Unit test for Magento\Elasticsearch\Model\Adapter\Index\Config\EsConfigTest
 */
class EsConfigTest extends TestCase
{
    /**
     * @var ObjectManagerHelper
     */
    private $objectManager;

    /**
     * @var EsConfig
     */
    protected $config;

    /**
     * @var \Magento\Elasticsearch\Model\Adapter\Index\Config\Reader|MockObject
     */
    protected $reader;

    /**
     * @var CacheInterface|MockObject
     */
    protected $cache;

    /**
     * @var SerializerInterface|MockObject
     */
    private $serializerMock;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->objectManager = new ObjectManagerHelper($this);
        $this->reader = $this->createMock(ReaderInterface::class);

        $this->cache = $this->createMock(CacheInterface::class);

        $this->cache->expects($this->any())
            ->method('load')
            ->willReturn('serializedData');

        $this->serializerMock = $this->createMock(SerializerInterface::class);

        $this->serializerMock->expects($this->once())
            ->method('unserialize')
            ->with('serializedData')
            ->willReturn([
                'stemmerInfo' => [],
                'stopwordsInfo' => []
            ]);

        $this->config = $this->objectManager->getObject(
            EsConfig::class,
            [
                'reader' => $this->reader,
                'cache' => $this->cache,
                'serializer' => $this->serializerMock,
            ]
        );
    }

    /**
     * Test getStemmerInfo method
     */
    public function testGetStemmerInfo(): void
    {
        $result = $this->config->getStemmerInfo();
        $this->assertIsArray($result);
    }

    /**
     * Test getStopwordsInfo method
     */
    public function testGetStopwordsInfo(): void
    {
        $result = $this->config->getStopwordsInfo();
        $this->assertIsArray($result);
    }
}
