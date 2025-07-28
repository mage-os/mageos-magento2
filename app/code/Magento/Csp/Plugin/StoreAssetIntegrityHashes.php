<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Csp\Plugin;

use Magento\Deploy\Service\DeployStaticContent;
use Magento\Csp\Model\SubresourceIntegrityCollector;
use Magento\Csp\Model\SubresourceIntegrityRepositoryPool;

/**
 * Plugin that stores generated integrity hashes for all assets.
 */
class StoreAssetIntegrityHashes
{
    /**
     * @var SubresourceIntegrityCollector
     */
    private SubresourceIntegrityCollector $integrityCollector;

    /**
     * @var SubresourceIntegrityRepositoryPool
     */
    private SubresourceIntegrityRepositoryPool $integrityRepositoryPool;

    /**
     * @param SubresourceIntegrityCollector $integrityCollector
     * @param SubresourceIntegrityRepositoryPool $integrityRepositoryPool
     */
    public function __construct(
        SubresourceIntegrityCollector $integrityCollector,
        SubresourceIntegrityRepositoryPool $integrityRepositoryPool
    ) {
        $this->integrityCollector = $integrityCollector;
        $this->integrityRepositoryPool = $integrityRepositoryPool;
    }

    /**
     * Stores generated integrity hashes after static content deploy
     *
     * @param DeployStaticContent $subject
     * @param mixed $result
     * @param array $options
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterDeploy(
        DeployStaticContent $subject,
        mixed $result,
        array $options
    ): void {
        $this->logger->info('SRI Store: Starting deployment storage (PID: ' . getmypid() . ')');
        
        $bunches = [];
        $integrityHashes = $this->integrityCollector->release();
        
        $this->logger->info('SRI Store: Released ' . count($integrityHashes) . ' objects from collector (PID: ' . getmypid() . ')');

        foreach ($integrityHashes as $integrity) {
            $area = explode("/", $integrity->getPath())[0];
            $bunches[$area][] = $integrity;
        }

        $this->logger->info('SRI Store: Grouped into areas: ' . implode(', ', array_map(function($area, $bunch) {
            return $area . '(' . count($bunch) . ')';
        }, array_keys($bunches), $bunches)) . ' (PID: ' . getmypid() . ')');

        foreach ($bunches as $area => $bunch) {
            try {
                $this->integrityRepositoryPool->get($area)->saveBunch($bunch);
                $this->logger->info('SRI Store: ✓ Saved ' . count($bunch) . ' objects for ' . $area . ' (PID: ' . getmypid() . ')');
            } catch (\Exception $e) {
                $this->logger->error('SRI Store: ✗ Failed saving ' . $area . ': ' . $e->getMessage() . ' (PID: ' . getmypid() . ')');
            }
        }
        
        $this->logger->info('SRI Store: Deployment storage complete (PID: ' . getmypid() . ')');
    }
}
