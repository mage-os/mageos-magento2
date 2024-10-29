<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Config\Model\Data\ReEncryptorList\CoreConfigDataReEncryptor;

use Magento\Framework\App\Config\Initial;
use Magento\Framework\App\ResourceConnection;
use Magento\Config\Model\Config\Backend\Encrypted;
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
    private const TABLE_NAME = "core_config_data";

    /**
     * @var string
     */
    private const BACKEND_MODEL = Encrypted::class;

    /**
     * @var Initial
     */
    private Initial $config;

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
     * @param Initial $config
     * @param EncryptorInterface $encryptor
     * @param ResourceConnection $resourceConnection
     * @param ErrorFactory $errorFactory
     */
    public function __construct(
        Initial $config,
        EncryptorInterface $encryptor,
        ResourceConnection $resourceConnection,
        ErrorFactory $errorFactory
    ) {
        $this->config = $config;
        $this->encryptor = $encryptor;
        $this->resourceConnection = $resourceConnection;
        $this->errorFactory = $errorFactory;
    }

    /**
     * @inheritDoc
     */
    public function reEncrypt(): array
    {
        $paths = [];
        $errors = [];

        foreach ($this->config->getMetadata() as $path => $processor) {
            if (isset($processor['backendModel']) &&
                $processor['backendModel'] === self::BACKEND_MODEL
            ) {
                $paths[] = $path;
            }
        }

        if ($paths) {
            $tableName = $this->resourceConnection->getTableName(
                self::TABLE_NAME
            );

            $connection = $this->resourceConnection->getConnection();

            $select = $connection->select()
                ->from($tableName, ['config_id', 'value'])
                ->where('path IN (?)', $paths)
                ->where('value != ?', '')
                ->where('value IS NOT NULL');

            foreach ($connection->fetchPairs($select) as $configId => $value) {
                try {
                    $connection->update(
                        $tableName,
                        ['value' => $this->encryptor->encrypt($this->encryptor->decrypt($value))],
                        ['config_id = ?' => (int) $configId]
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
        }

        return $errors;
    }
}
