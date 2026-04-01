<?php
/**
 * Copyright 2026 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Csp\Test\Unit\Block\Sri;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Magento\Csp\Block\Sri\Hashes;
use Magento\Csp\Model\SubresourceIntegrityRepositoryPool;
use Magento\Csp\Model\SubresourceIntegrityRepository;
use Magento\Csp\Model\SubresourceIntegrity;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\State;

/**
 * Unit tests for Hashes block
 */
class HashesTest extends TestCase
{
    /**
     * @var Hashes
     */
    private Hashes $block;

    /**
     * @var MockObject|SerializerInterface
     */
    private MockObject $serializerMock;

    /**
     * @var MockObject|Context
     */
    private MockObject $contextMock;

    /**
     * @var MockObject|SubresourceIntegrityRepositoryPool
     */
    private MockObject $repositoryPoolMock;

    /**
     * @var MockObject|UrlInterface
     */
    private MockObject $urlBuilderMock;

    /**
     * @var MockObject|State
     */
    private MockObject $appStateMock;

    protected function setUp(): void
    {
        $this->serializerMock = $this->createMock(SerializerInterface::class);
        $this->repositoryPoolMock = $this->createMock(SubresourceIntegrityRepositoryPool::class);
        $this->urlBuilderMock = $this->createMock(UrlInterface::class);
        $this->appStateMock = $this->createMock(State::class);

        $this->contextMock = $this->createMock(Context::class);
        $this->contextMock->method('getUrlBuilder')->willReturn($this->urlBuilderMock);
        $this->contextMock->method('getAppState')->willReturn($this->appStateMock);

        $this->block = new Hashes(
            $this->contextMock,
            [],
            $this->repositoryPoolMock,
            $this->serializerMock
        );
    }

    /**
     * Test getSerialized returns serialized hashes from repository
     */
    public function testGetSerializedReturnsHashesFromRepository(): void
    {
        $baseUrl = 'https://example.com/static/';

        $integrity1 = $this->createMock(SubresourceIntegrity::class);
        $integrity1->method('getPath')->willReturn('frontend/Magento/luma/en_US/js/file1.js');
        $integrity1->method('getHash')->willReturn('sha256-abc123');

        $integrity2 = $this->createMock(SubresourceIntegrity::class);
        $integrity2->method('getPath')->willReturn('frontend/Magento/luma/en_US/js/file2.js');
        $integrity2->method('getHash')->willReturn('sha256-def456');

        $repositoryMock = $this->createMock(SubresourceIntegrityRepository::class);
        $repositoryMock->method('getAll')->willReturn([$integrity1, $integrity2]);

        $this->urlBuilderMock->expects($this->once())
            ->method('getBaseUrl')
            ->with(['_type' => UrlInterface::URL_TYPE_STATIC])
            ->willReturn($baseUrl);

        $this->appStateMock->expects($this->once())
            ->method('getAreaCode')
            ->willReturn('frontend');

        $this->repositoryPoolMock->expects($this->exactly(2))
            ->method('get')
            ->willReturn($repositoryMock);

        $expectedHashes = [
            'https://example.com/static/frontend/Magento/luma/en_US/js/file1.js' => 'sha256-abc123',
            'https://example.com/static/frontend/Magento/luma/en_US/js/file2.js' => 'sha256-def456'
        ];

        $expectedJson = json_encode($expectedHashes);
        $this->serializerMock->expects($this->once())
            ->method('serialize')
            ->with($expectedHashes)
            ->willReturn($expectedJson);

        $result = $this->block->getSerialized();
        $this->assertEquals($expectedJson, $result);
    }

    /**
     * Test getSerialized with empty hashes
     */
    public function testGetSerializedWithEmptyHashes(): void
    {
        $baseUrl = 'https://example.com/static/';

        $repositoryMock = $this->createMock(SubresourceIntegrityRepository::class);
        $repositoryMock->method('getAll')->willReturn([]);

        $this->urlBuilderMock->expects($this->once())
            ->method('getBaseUrl')
            ->willReturn($baseUrl);

        $this->appStateMock->expects($this->once())
            ->method('getAreaCode')
            ->willReturn('frontend');

        $this->repositoryPoolMock->expects($this->exactly(2))
            ->method('get')
            ->willReturn($repositoryMock);

        $this->serializerMock->expects($this->once())
            ->method('serialize')
            ->with([])
            ->willReturn('{}');

        $result = $this->block->getSerialized();
        $this->assertEquals('{}', $result);
    }
}
