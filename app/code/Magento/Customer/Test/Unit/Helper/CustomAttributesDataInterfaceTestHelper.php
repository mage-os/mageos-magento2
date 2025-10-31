<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Helper;

use Magento\Framework\Api\CustomAttributesDataInterface;

/**
 * Test helper for CustomAttributesDataInterface to support custom methods
 */
class CustomAttributesDataInterfaceTestHelper implements CustomAttributesDataInterface
{
    /**
     * @var array
     */
    private array $data = [];

    /**
     * Constructor - skip parent
     */
    public function __construct()
    {
        // Skip parent constructor
    }

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->data['id'] ?? null;
    }

    /**
     * Set ID
     *
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        $this->data['id'] = $id;
        return $this;
    }

    /**
     * Get email
     *
     * @return string|null
     */
    public function getEmail()
    {
        return $this->data['email'] ?? null;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return $this
     */
    public function setEmail($email)
    {
        $this->data['email'] = $email;
        return $this;
    }

    /**
     * Get website ID
     *
     * @return int|null
     */
    public function getWebsiteId()
    {
        return $this->data['website_id'] ?? null;
    }

    /**
     * Set website ID
     *
     * @param int $websiteId
     * @return $this
     */
    public function setWebsiteId($websiteId)
    {
        $this->data['website_id'] = $websiteId;
        return $this;
    }

    /**
     * Get addresses
     *
     * @return array|null
     */
    public function getAddresses()
    {
        return $this->data['addresses'] ?? null;
    }

    /**
     * Set addresses
     *
     * @param array $addresses
     * @return $this
     */
    public function setAddresses($addresses)
    {
        $this->data['addresses'] = $addresses;
        return $this;
    }

    /**
     * Get group ID
     *
     * @return int|null
     */
    public function getGroupId()
    {
        return $this->data['group_id'] ?? null;
    }

    /**
     * Set group ID
     *
     * @param int $groupId
     * @return $this
     */
    public function setGroupId($groupId)
    {
        $this->data['group_id'] = $groupId;
        return $this;
    }

    /**
     * Get custom attributes
     *
     * @return \Magento\Framework\Api\AttributeInterface[]|null
     */
    public function getCustomAttributes()
    {
        return $this->data['custom_attributes'] ?? null;
    }

    /**
     * Set custom attributes
     *
     * @param \Magento\Framework\Api\AttributeInterface[] $attributes
     * @return $this
     */
    public function setCustomAttributes(array $attributes)
    {
        $this->data['custom_attributes'] = $attributes;
        return $this;
    }

    /**
     * Get custom attribute
     *
     * @param string $attributeCode
     * @return \Magento\Framework\Api\AttributeInterface|null
     */
    public function getCustomAttribute($attributeCode)
    {
        return $this->data['custom_attributes'][$attributeCode] ?? null;
    }

    /**
     * Set custom attribute
     *
     * @param string $attributeCode
     * @param mixed $attributeValue
     * @return $this
     */
    public function setCustomAttribute($attributeCode, $attributeValue)
    {
        $this->data['custom_attributes'][$attributeCode] = $attributeValue;
        return $this;
    }
}
