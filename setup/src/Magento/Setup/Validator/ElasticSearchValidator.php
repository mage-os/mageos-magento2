<?php

namespace Magento\Setup\Validator;

use Magento\Framework\Config\ConfigOptionsListConstants;
use Magento\Setup\Model\Installer;
use Magento\Setup\Module\ConnectionFactory;

class ElasticSearchValidator
{
    /**
     * DB connection factory
     *
     * @var ConnectionFactory
     */
    private $connectionFactory;

    /**
     * Constructor
     *
     * @param ConnectionFactory $connectionFactory
     */
    public function __construct(ConnectionFactory $connectionFactory)
    {
        $this->connectionFactory = $connectionFactory;
    }

    /**
     * Checks Database Connection
     *
     * @param string $dbName
     * @param string $dbHost
     * @param string $dbUser
     * @param string $dbPass
     * @return bool
     * @throws \Magento\Setup\Exception
     * @deprecated
     */
    public function checkDatabaseConnection($dbName, $dbHost, $dbUser, $dbPass = '')
    {

    }

}