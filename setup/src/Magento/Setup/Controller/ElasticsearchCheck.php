<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Controller;

use Laminas\Json\Json;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\JsonModel;
use Magento\Setup\Validator\DbValidator;

class ElasticsearchCheck extends AbstractActionController
{
    /**
     * @var DbValidator
     */
    private $dbValidator;

    /** @var \Magento\Elasticsearch7\Model\Client\Elasticsearch  */
    private \Magento\Elasticsearch7\Model\Client\Elasticsearch $esClient;

    /**
     * Constructor
     *
     * @param DbValidator $dbValidator
     */
    public function __construct(
        DbValidator $dbValidator
    ) {
        $this->dbValidator = $dbValidator;
    }

    /**
     * Result of checking DB credentials
     *
     * @return JsonModel
     */
    public function indexAction()
    {
        try {
            $params = Json::decode($this->getRequest()->getContent(), Json::TYPE_ARRAY);
            $config = $this->buildESConfig($params);
            $esClient = \Elasticsearch\ClientBuilder::fromConfig($config, true);
            $esClient->info();
            return new JsonModel(['success' => true]);
        } catch (\Exception $e) {
            return new JsonModel(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * @param array $options
     * @return array
     */
    private function buildESConfig(array $options = []): array
    {
        $hostname = preg_replace('/http[s]?:\/\//i', '', $options['hostname']);
        // @codingStandardsIgnoreStart
        $protocol = parse_url($options['hostname'], PHP_URL_SCHEME);
        // @codingStandardsIgnoreEnd
        if (!$protocol) {
            $protocol = 'http';
        }

        $authString = '';
        if (!empty($options['enableAuth']) && (int)$options['enableAuth'] === 1) {
            $authString = "{$options['username']}:{$options['password']}@";
        }

        $portString = '';
        if (!empty($options['port'])) {
            $portString = ':' . $options['port'];
        }

        $host = $protocol . '://' . $authString . $hostname . $portString;

        $options['hosts'] = [$host];

        return $options;
    }
}
