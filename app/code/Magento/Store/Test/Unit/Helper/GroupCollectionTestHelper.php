<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Store\Test\Unit\Helper;

use Magento\Store\Model\ResourceModel\Group\Collection;

/**
 * Test helper for Group Collection with custom methods for testing
 */
class GroupCollectionTestHelper extends Collection
{
    /**
     * @var array
     */
    private array $data = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
        // Initialize required properties to prevent null pointer errors in interceptors
        $this->_conn = null;
        $this->_resource = null;
    }

    /**
     * Override setConnection to handle null values in test environment
     *
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null $conn
     * @return self
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setConnection($conn): self
    {
        // In unit test environment, we don't need an actual connection
        // Just return self to maintain fluent interface
        return $this;
    }

    /**
     * Override _resetState to prevent issues with null connection
     *
     * @return void
     */
    public function _resetState(): void
    {
        // Skip parent _resetState which calls setConnection with potentially null _conn
        // In unit tests, we don't need to reset database-related state
    }

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->data['id'] ?? null;
    }

    /**
     * Set ID
     *
     * @param int $id
     * @return $this
     */
    public function setId(int $id): self
    {
        $this->data['id'] = $id;
        return $this;
    }

    /**
     * Get name
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->data['name'] ?? null;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->data['name'] = $name;
        return $this;
    }

    /**
     * Get website ID
     *
     * @return int|null
     */
    public function getWebsiteId(): ?int
    {
        return $this->data['website_id'] ?? null;
    }

    /**
     * Set website ID
     *
     * @param int $websiteId
     * @return $this
     */
    public function setWebsiteId(int $websiteId): self
    {
        $this->data['website_id'] = $websiteId;
        return $this;
    }
}
