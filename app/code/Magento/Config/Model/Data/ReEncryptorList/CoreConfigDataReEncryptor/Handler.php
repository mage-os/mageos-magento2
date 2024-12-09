<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Config\Model\Data\ReEncryptorList\CoreConfigDataReEncryptor;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\EncryptionKey\Model\Data\ReEncryptorList\ReEncryptor\HandlerInterface;
use Magento\EncryptionKey\Model\Data\ReEncryptorList\ReEncryptor\Handler\ErrorFactory;

/**
 * Handler for core configuration re-encryption.
 */
class Handler implements HandlerInterface
{
    /**
     * @var string
     */
    private const PATTERN = "^[[:digit:]]+:[[:digit:]]+:.*$";

    /**
     * @var string
     */
    private const TABLE_NAME = "core_config_data";

    /**
     * @var int
     */
    private const QUERY_SIZE = 1000;

    /**
     * @var EncryptorInterface
     */
    private EncryptorInterface $encryptor;

    /**
     * @var ResourceConnection
     */
    private ResourceConnection $resourceConnection;

    /**
     * @var ErrorFactory
     */
    private ErrorFactory $errorFactory;

    /**
     * @param EncryptorInterface $encryptor
     * @param ResourceConnection $resourceConnection
     * @param ErrorFactory $errorFactory
     */
    public function __construct(
        EncryptorInterface $encryptor,
        ResourceConnection $resourceConnection,
        ErrorFactory $errorFactory
    ) {
        $this->encryptor = $encryptor;
        $this->resourceConnection = $resourceConnection;
        $this->errorFactory = $errorFactory;
    }

    /**
     * @inheritDoc
     */
    public function reEncrypt(): array
    {
        $errors = [];
        $tableName = $this->resourceConnection->getTableName(
            self::TABLE_NAME
        );
        $connection = $this->resourceConnection->getConnection();

        $select = $connection->select()
            ->from($tableName, ['config_id', 'value'])
            ->where('value != ?', '')
            ->where('value IS NOT NULL')
            ->where('value REGEXP ?', self::PATTERN)
            ->limit(self::QUERY_SIZE);

        foreach ($connection->fetchPairs($select) as $configId => $value) {
            try {
                $connection->update(
                    $tableName,
                    ['value' => $this->encryptor->encrypt($this->encryptor->decrypt($value))],
                    ['config_id = ?' => (int)$configId]
                );
            } catch (\Throwable $e) {
                $errors[] = $this->errorFactory->create(
                    "config_id",
                    $configId,
                    $e->getMessage()
                );

                continue;
            }
        }

        return $errors;
    }
}
