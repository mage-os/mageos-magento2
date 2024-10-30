<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\EncryptionKey\Model\Data\ReEncryptorList\ReEncryptor\Handler;

/**
 * Re-encryptor handler error data object.
 */
class Error
{
    /**
     * Name of the identifier field of a DB row an error relates to.
     *
     * @var string
     */
    private string $rowIdField;

    /**
     * Value of the identifier field of a DB row an error relates to.
     *
     * @var int
     */
    private int $rowIdValue;

    /**
     * Error message.
     *
     * @var string
     */
    private string $message;

    /**
     * @param string $rowIdField
     * @param int $rowIdValue
     * @param string $message
     */
    public function __construct(
        string $rowIdField,
        int $rowIdValue,
        string $message
    ) {
        $this->rowIdField = $rowIdField;
        $this->rowIdValue = $rowIdValue;
        $this->message = $message;
    }

    /**
     * Returns row ID field name.
     *
     * @return string
     */
    public function getRowIdField(): string
    {
        return $this->rowIdField;
    }

    /**
     * Returns row ID field value.
     *
     * @return int
     */
    public function getRowIdValue(): int
    {
        return $this->rowIdValue;
    }

    /**
     * Returns an error message.
     *
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }
}
