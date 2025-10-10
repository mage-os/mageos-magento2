<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\Model\ResourceModel\Type\Db;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Model\ResourceModel\Type\Db\Pdo\Mysql;
use Magento\Framework\Model\ResourceModel\Type\Db\Pdo\Sqlite;

/**
 * Connection adapter factory with multi-database support
 */
class ConnectionFactory implements ConnectionFactoryInterface
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Type mapping for connection adapters
     *
     * @var array
     */
    private $typeMapping = [
        'pdo_mysql' => Mysql::class,
        'pdo_sqlite' => Sqlite::class,
    ];

    /**
     * Constructor
     *
     * @param ObjectManagerInterface $objectManager
     * @param array $typeMapping
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        array $typeMapping = []
    ) {
        $this->objectManager = $objectManager;

        if (!empty($typeMapping)) {
            $this->typeMapping = array_merge($this->typeMapping, $typeMapping);
        }
    }

    /**
     * Create connection adapter instance based on type
     *
     * {@inheritdoc}
     */
    public function create(array $connectionConfig)
    {
        $connectionType = $connectionConfig['type'] ?? 'pdo_mysql';

        if (!isset($this->typeMapping[$connectionType])) {
            throw new \InvalidArgumentException(
                "Unknown connection type: {$connectionType}. Supported: " .
                implode(', ', array_keys($this->typeMapping))
            );
        }

        $adapterClass = $this->typeMapping[$connectionType];

        /** @var \Magento\Framework\App\ResourceConnection\ConnectionAdapterInterface $adapterInstance */
        $adapterInstance = $this->objectManager->create(
            $adapterClass,
            ['config' => $connectionConfig]
        );

        return $adapterInstance->getConnection();
    }
}
