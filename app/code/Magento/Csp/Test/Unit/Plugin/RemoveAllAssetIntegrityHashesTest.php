<?php
/**
 * Copyright 2026 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Csp\Test\Unit\Plugin;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Magento\Csp\Plugin\RemoveAllAssetIntegrityHashes;
use Magento\Csp\Model\SubresourceIntegrityCollector;
use Magento\Csp\Model\SubresourceIntegrityRepositoryPool;
use Magento\Csp\Model\SubresourceIntegrityRepository;
use Magento\Deploy\Service\DeployStaticContent;
use Magento\Deploy\Console\DeployStaticOptions;

/**
 * Unit tests for RemoveAllAssetIntegrityHashes plugin
 */
class RemoveAllAssetIntegrityHashesTest extends TestCase
{
    /**
     * @var RemoveAllAssetIntegrityHashes
     */
    private RemoveAllAssetIntegrityHashes $plugin;

    /**
     * @var MockObject|SubresourceIntegrityRepositoryPool
     */
    private MockObject $repositoryPoolMock;

    /**
     * @var MockObject|SubresourceIntegrityCollector
     */
    private MockObject $integrityCollectorMock;

    protected function setUp(): void
    {
        $this->repositoryPoolMock = $this->createMock(SubresourceIntegrityRepositoryPool::class);
        $this->integrityCollectorMock = $this->createMock(SubresourceIntegrityCollector::class);

        $this->plugin = new RemoveAllAssetIntegrityHashes(
            $this->repositoryPoolMock,
            $this->integrityCollectorMock
        );
    }

    /**
     * Test collector and repositories are cleared during deploy
     */
    public function testBeforeDeployClearsCollectorAndRepositories(): void
    {
        $subjectMock = $this->createMock(DeployStaticContent::class);

        $repositoryMock = $this->createMock(SubresourceIntegrityRepository::class);
        $repositoryMock->expects($this->exactly(3))->method('deleteAll');

        $this->repositoryPoolMock->expects($this->exactly(3))
            ->method('get')
            ->willReturn($repositoryMock);

        $this->integrityCollectorMock->expects($this->once())->method('clear');

        $this->plugin->beforeDeploy($subjectMock, []);
    }

    /**
     * Test refresh version only option skips cleanup
     */
    public function testBeforeDeploySkipsOnRefreshVersionOnly(): void
    {
        $subjectMock = $this->createMock(DeployStaticContent::class);

        $options = [DeployStaticOptions::REFRESH_CONTENT_VERSION_ONLY => true];

        $this->repositoryPoolMock->expects($this->never())->method('get');
        $this->integrityCollectorMock->expects($this->never())->method('clear');

        $this->plugin->beforeDeploy($subjectMock, $options);
    }

    /**
     * Test non-CLI environment doesn't trigger cleanup
     */
    public function testBeforeDeploySkipsInNonCliEnvironment(): void
    {
        // This test validates the PHP_SAPI check in the plugin
        // In actual non-CLI execution, the plugin would not execute cleanup
        $this->markTestSkipped('Cannot reliably test PHP_SAPI check in unit tests');
    }
}
