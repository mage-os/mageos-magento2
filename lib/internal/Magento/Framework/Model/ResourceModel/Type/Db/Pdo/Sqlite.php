<?php
/**
 * Copyright © Mage-OS, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\Model\ResourceModel\Type\Db\Pdo;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ResourceConnection\ConnectionAdapterInterface;
use Magento\Framework\DB;
use Magento\Framework\DB\Adapter\Pdo\SqliteFactory;
use Magento\Framework\DB\SelectFactory;

/**
 * SQLite database connection adapter
 *
 * WARNING: This adapter is intended for DEVELOPMENT MODE ONLY.
 * Do not use in production environments.
 */
class Sqlite extends \Magento\Framework\Model\ResourceModel\Type\Db implements
    ConnectionAdapterInterface
{
    /**
     * @var array
     */
    protected $connectionConfig;

    /**
     * @var SqliteFactory
     */
    private $sqliteFactory;

    /**
     * Constructor
     *
     * @param array $config
     * @param SqliteFactory|null $sqliteFactory
     */
    public function __construct(
        array $config,
        ?SqliteFactory $sqliteFactory = null
    ) {
        $this->connectionConfig = $this->getValidConfig($config);
        $this->sqliteFactory = $sqliteFactory ?: ObjectManager::getInstance()->get(SqliteFactory::class);
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    public function getConnection(?DB\LoggerInterface $logger = null, ?SelectFactory $selectFactory = null)
    {
        $connection = $this->getDbConnectionInstance($logger, $selectFactory);

        $profiler = $connection->getProfiler();
        if ($profiler instanceof DB\Profiler) {
            $profiler->setType($this->connectionConfig['type']);
            $profiler->setHost($this->connectionConfig['dbname'] ?? 'sqlite_dev');
        }

        return $connection;
    }

    /**
     * Create and return database connection object instance
     *
     * @param DB\LoggerInterface|null $logger
     * @param SelectFactory|null $selectFactory
     * @return \Magento\Framework\DB\Adapter\Pdo\Sqlite
     */
    protected function getDbConnectionInstance(?DB\LoggerInterface $logger = null, ?SelectFactory $selectFactory = null)
    {
        return $this->sqliteFactory->create(
            $this->connectionConfig,
            $logger,
            ObjectManager::getInstance()->get(\Magento\Framework\Stdlib\StringUtils::class),
            ObjectManager::getInstance()->get(\Magento\Framework\Stdlib\DateTime::class),
            []
        );
    }

    /**
     * Validates the config and adds default options, if any is missing
     *
     * @param array $config
     * @return array
     */
    private function getValidConfig(array $config)
    {
        $default = [
            'type' => 'pdo_sqlite',
            'active' => false,
            'driver_options' => [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
            ]
        ];

        foreach ($default as $key => $value) {
            if (!isset($config[$key])) {
                $config[$key] = $value;
            }
        }

        // SQLite uses 'dbname' for the file path
        if (!isset($config['dbname'])) {
            // Default to var/dev.sqlite
            $config['dbname'] = BP . '/var/dev.sqlite';
        }

        // Ensure absolute path
        if (!str_starts_with($config['dbname'], '/')) {
            $config['dbname'] = BP . '/' . ltrim($config['dbname'], '/');
        }

        // Create directory if it doesn't exist
        $dir = dirname($config['dbname']);
        if (!is_dir($dir)) {
            mkdir($dir, 0770, true);
        }

        $config['active'] = !(
            $config['active'] === 'false'
            || $config['active'] === false
            || $config['active'] === '0'
        );

        // SQLite-specific: set host to dbname for profiler
        $config['host'] = $config['dbname'];

        return $config;
    }
}
