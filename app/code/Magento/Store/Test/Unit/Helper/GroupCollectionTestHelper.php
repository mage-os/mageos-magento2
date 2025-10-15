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
