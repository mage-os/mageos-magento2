<?php
/**
 * Copyright © Mage-OS, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\DB\Adapter\Pdo;

use Magento\Framework\DB\LoggerInterface;
use Magento\Framework\DB\SelectFactory;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Stdlib\StringUtils;

/**
 * Factory class for SQLite adapter
 */
class SqliteFactory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var SelectFactory
     */
    private $selectFactory;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param SelectFactory $selectFactory
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        SelectFactory $selectFactory
    ) {
        $this->objectManager = $objectManager;
        $this->selectFactory = $selectFactory;
    }

    /**
     * Create SQLite adapter instance
     *
     * @param array $config
     * @param LoggerInterface $logger
     * @param StringUtils $string
     * @param DateTime $dateTime
     * @param array $data
     * @return Sqlite
     */
    public function create(
        array $config,
        LoggerInterface $logger,
        StringUtils $string,
        DateTime $dateTime,
        array $data = []
    ): Sqlite {
        return $this->objectManager->create(
            Sqlite::class,
            [
                'config' => $config,
                'logger' => $logger,
                'selectFactory' => $this->selectFactory,
                'string' => $string,
                'dateTime' => $dateTime,
                'data' => $data,
            ]
        );
    }
}
