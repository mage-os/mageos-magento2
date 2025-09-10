<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\NewRelicReporting\Model\Apm;

use Laminas\Http\Exception\RuntimeException;
use Laminas\Http\Request;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\LaminasClient;
use Magento\Framework\HTTP\LaminasClientFactory;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\NewRelicReporting\Model\Config;
use Magento\NewRelicReporting\Model\NerdGraph\Client as NerdGraphClient;
use Magento\NewRelicReporting\Model\NerdGraph\DeploymentTracker;
use Psr\Log\LoggerInterface;

/**
 * Performs the request to make the deployment
 */
class Deployments
{
    /**
     * API mode constant for NerdGraph (GraphQL) API
     */
    private const NERDGRAPH = 'nerdgraph';
    /**
     * API URL for New Relic deployments
     */
    private const API_URL = 'https://api.newrelic.com/v2/applications/%s/deployments.json';

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var LaminasClientFactory $clientFactory
     */
    protected $clientFactory;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var NerdGraphClient
     */
    private $nerdGraphClient;

    /**
     * @var DeploymentTracker
     */
    private $deploymentTracker;

    /**
     * Constructor
     *
     * @param Config $config
     * @param LoggerInterface $logger
     * @param LaminasClientFactory $clientFactory
     * @param SerializerInterface $serializer
     * @param NerdGraphClient $nerdGraphClient
     * @param DeploymentTracker $deploymentTracker
     */
    public function __construct(
        Config $config,
        LoggerInterface $logger,
        LaminasClientFactory $clientFactory,
        SerializerInterface $serializer,
        NerdGraphClient $nerdGraphClient,
        DeploymentTracker $deploymentTracker
    ) {
        $this->config = $config;
        $this->logger = $logger;
        $this->clientFactory = $clientFactory;
        $this->serializer = $serializer;
        $this->nerdGraphClient = $nerdGraphClient;
        $this->deploymentTracker = $deploymentTracker;
    }

    /**
     * Performs the request to make the deployment
     *
     * Supports both v2 REST and NerdGraph APIs based on configuration
     *
     * @param string $description
     * @param bool|string $change
     * @param bool|string $user
     * @param string|null $revision
     * @param string|null $commit Git commit hash (NerdGraph only)
     * @param string|null $deepLink Deep link URL (NerdGraph only)
     * @param string|null $groupId Group ID (NerdGraph only)
     *
     * @return bool|string|array
     */
    public function setDeployment(
        string      $description,
        bool|string $change = false,
        bool|string $user = false,
        string      $revision = null,
        string      $commit = null,
        string      $deepLink = null,
        string $groupId = null
    ) {
        // Check API mode configuration
        $apiMode = $this->config->getApiMode();

        if ($apiMode === self::NERDGRAPH) {
            return $this->createNerdGraphDeployment(
                $description,
                $change,
                $user,
                $revision,
                $commit,
                $deepLink,
                $groupId
            );
        } else {
            return $this->createV2RestDeployment($description, $change, $user, $revision);
        }
    }

    /**
     * Create deployment using v2 REST API (legacy)
     *
     * @param string $description
     * @param bool|string $change
     * @param bool|string $user
     * @param string|null $revision
     * @return bool|string
     */
    private function createV2RestDeployment($description, $change, $user, $revision)
    {
        $apiUrl = $this->config->getNewRelicApiUrl();
        if (empty($apiUrl)) {
            $this->logger->notice('New Relic API URL is blank, using fallback URL');
            $apiUrl = self::API_URL;
        }

        $apiUrl = sprintf($apiUrl, $this->config->getNewRelicAppId());

        /** @var LaminasClient $client */
        $client = $this->clientFactory->create();
        $client->setUri($apiUrl);
        $client->setMethod(Request::METHOD_POST);
        $client->setHeaders(
            [
                'Api-Key' => $this->config->getNewRelicApiKey(),
                'Content-Type' => 'application/json'
            ]
        );

        if (!$revision) {
            $revision = hash('sha256', time());
        }

        $params = [
            'deployment' => [
                'description' => $description,
                'changelog' => $change,
                'user' => $user,
                'revision' => $revision
            ]
        ];
        $client->setRawBody($this->serializer->serialize($params));

        try {
            $response = $client->send();
        } catch (RuntimeException $e) {
            $this->logger->critical($e);
            return false;
        }

        if ($response->getStatusCode() < 200 || $response->getStatusCode() > 210) {
            $this->logger->warning('Deployment marker request did not send a 200 status code.');
            return false;
        }

        return $response->getBody();
    }

    /**
     * Create deployment using NerdGraph (GraphQL) API
     *
     * @param string $description
     * @param bool|string $change
     * @param bool|string $user
     * @param string|null $revision
     * @param string|null $commit
     * @param string|null $deepLink
     * @param string|null $groupId
     * @return array|false
     * @throws LocalizedException
     */
    private function createNerdGraphDeployment($description, $change, $user, $revision, $commit, $deepLink, $groupId)
    {
        return $this->deploymentTracker->createDeployment(
            $description,
            $change ? (string)$change : null,
            $user ? (string)$user : null,
            $revision,
            $commit,
            $deepLink,
            $groupId
        );
    }
}
