<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\EntityManager\Test\Unit\Helper;

use Magento\Framework\EntityManager\MetadataPool;

/**
 * Test helper for MetadataPool
 * Provides getIdentifierField() method that doesn't exist on the class
 */
class MetadataPoolTestHelper extends MetadataPool
{
    /**
     * @var string
     */
    private $identifierField = 'entity_id';

    /**
     * @var mixed
     */
    protected $metadataReturn;

    /**
     * Skip constructor
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get metadata (chainable method)
     *
     * @param string $entityType
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getMetadata($entityType = null)
    {
        return $this;
    }

    /**
     * Get identifier field
     *
     * @return string
     */
    public function getIdentifierField(): string
    {
        return $this->identifierField;
    }

    /**
     * Set identifier field
     *
     * @param string $field
     * @return $this
     */
    public function setIdentifierField(string $field): self
    {
        $this->identifierField = $field;
        return $this;
    }

    /**
     * Set metadata for returning
     *
     * @param mixed $metadata
     * @return $this
     */
    public function setMetadataReturn($metadata): self
    {
        $this->metadataReturn = $metadata;
        return $this;
    }
}
