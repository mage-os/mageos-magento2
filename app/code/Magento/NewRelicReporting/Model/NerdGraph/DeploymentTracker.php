<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\NewRelicReporting\Model\NerdGraph;

use Magento\Framework\Exception\LocalizedException;
use Magento\NewRelicReporting\Model\Config;
use Psr\Log\LoggerInterface;

/**
 * NerdGraph-based deployment tracking service
 */
class DeploymentTracker
{
    /**
     * @var Client
     */
    private Client $nerdGraphClient;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * Constructor
     *
     * @param Client $nerdGraphClient
     * @param Config $config
     * @param LoggerInterface $logger
     */
    public function __construct(
        Client $nerdGraphClient,
        Config $config,
        LoggerInterface $logger
    ) {
        $this->nerdGraphClient = $nerdGraphClient;
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * Create a deployment marker via NerdGraph
     *
     * @param string $description Deployment description
     * @param string|null $changelog Changelog or commit message
     * @param string|null $user User who performed the deployment
     * @param string|null $version Version or revision
     * @param string|null $commit Git commit hash
     * @param string|null $deepLink Deep link to deployment details
     * @param string|null $groupId Group ID for organizing deployments
     * @return array|false Deployment data on success, false on failure
     * @throws LocalizedException
     */
    public function createDeployment(
        string $description,
        ?string $changelog = null,
        ?string $user = null,
        ?string $version = null,
        ?string $commit = null,
        ?string $deepLink = null,
        ?string $groupId = null
    ) {
        try {
            // Get entity GUID - this is required for NerdGraph deployment tracking
            $entityGuid = $this->getEntityGuid();
            if (!$entityGuid) {
                $this->logger->error('Cannot create NerdGraph deployment: Entity GUID not found');
                return false;
            }

            $mutation = '
                mutation CreateDeployment($deployment: ChangeTrackingDeploymentInput!) {
                    changeTrackingCreateDeployment(deployment: $deployment) {
                        deploymentId
                        entityGuid
                    }
                }
            ';

            $variables = [
                'deployment' => [
                    'entityGuid' => $entityGuid,
                    'version' => $version ?: $this->generateVersion(),
                    'description' => $description,
                    'deploymentType' => 'BASIC',
                    'timestamp' => time() * 1000 // NerdGraph expects milliseconds
                ]
            ];

            // Add optional fields if provided
            if ($changelog) {
                $variables['deployment']['changelog'] = $changelog;
            }

            if ($user) {
                $variables['deployment']['user'] = $user;
            }

            if ($commit) {
                $variables['deployment']['commit'] = $commit;
            }

            if ($deepLink) {
                $variables['deployment']['deepLink'] = $deepLink;
            }

            if ($groupId) {
                $variables['deployment']['groupId'] = $groupId;
            }

            $response = $this->nerdGraphClient->query($mutation, $variables);

            $deploymentData = $response['data']['changeTrackingCreateDeployment'] ?? null;
            if ($deploymentData) {
                $this->logger->info(
                    'NerdGraph deployment created successfully',
                    [
                        'deploymentId' => $deploymentData['deploymentId'],
                        'entityGuid' => $deploymentData['entityGuid'],
                        'version' => $version,
                        'description' => $description
                    ]
                );

                return [
                    'deploymentId' => $deploymentData['deploymentId'],
                    'entityGuid' => $deploymentData['entityGuid'],
                    'version' => $variables['deployment']['version'],
                    'description' => $description,
                    'changelog' => $changelog,
                    'user' => $user,
                    'commit' => $commit,
                    'deepLink' => $deepLink,
                    'groupId' => $groupId,
                    'timestamp' => $variables['deployment']['timestamp']
                ];
            }

            $this->logger->error('NerdGraph deployment creation failed: No deployment data in response');
            return false;

        } catch (LocalizedException $e) {
            // Re-throw configuration/migration errors so they reach the user
            throw $e;
        } catch (\Exception $e) {
            $this->logger->error('NerdGraph deployment creation failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get entity GUID for the configured application
     *
     * @return string|null Entity GUID or null if not found
     */
    private function getEntityGuid(): ?string
    {
        // First try to get configured Entity GUID directly
        $configuredEntityGuid = $this->config->getEntityGuid();
        if (!empty($configuredEntityGuid)) {
            return $configuredEntityGuid;
        }

        // Fallback: Try to resolve Entity GUID from application name/ID
        $appId = $this->config->getNewRelicAppId();
        $appName = $this->config->getNewRelicAppName();

        if (!$appId && !$appName) {
            $this->logger->error('No Entity GUID, New Relic application ID, or name configured');
            return null;
        }

        $this->logger->info('Entity GUID not configured, attempting to resolve from application name/ID');
        return $this->nerdGraphClient->getEntityGuidFromApplication($appName, (string)$appId);
    }

    /**
     * Generate a version string if none provided
     *
     * @return string Generated version string
     */
    private function generateVersion(): string
    {
        return date('Y-m-d_H-i-s') . '_' . substr(hash('sha256', (string)time()), 0, 8);
    }
}
