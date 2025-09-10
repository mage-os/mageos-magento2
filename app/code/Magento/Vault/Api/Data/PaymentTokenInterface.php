<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
namespace Magento\Vault\Api\Data;

/**
 * Gateway vault payment token interface.
 *
 * @api
 * @since 100.1.0
 */
interface PaymentTokenInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case.
     */
    /*
     * Entity ID.
     */
    public const ENTITY_ID = 'entity_id';
    /*
     * Customer ID.
     */
    public const CUSTOMER_ID = 'customer_id';
    /*
     * Unique hash for frontend.
     */
    public const PUBLIC_HASH = 'public_hash';
    /*
     * Payment method code.
     */
    public const PAYMENT_METHOD_CODE = 'payment_method_code';
    /*
     * Token type.
     */
    public const TYPE = 'type';
    /*
     * Token creation timestamp.
     */
    public const CREATED_AT = 'created_at';
    /*
     * Token expiration timestamp.
     */
    public const EXPIRES_AT = 'expires_at';
    /*
     * Gateway token ID.
     */
    public const GATEWAY_TOKEN = 'gateway_token';
    /*
     * Additional details.
     */
    public const DETAILS = 'details';
    /*
     * Is vault payment record active.
     */
    public const IS_ACTIVE = 'is_active';
    /*
     * Is vault payment token visible.
     */
    public const IS_VISIBLE = 'is_visible';

    /*
     * Vault website id
     */
    public const WEBSITE_ID = 'website_id';

    /**
     * Gets the entity ID.
     *
     * @return int|null Entity ID.
     * @since 100.1.0
     */
    public function getEntityId();

    /**
     * Sets entity ID.
     *
     * @param int $entityId
     * @return $this
     * @since 100.1.0
     */
    public function setEntityId($entityId);

    /**
     * Gets the customer ID.
     *
     * @return int|null Customer ID.
     * @since 100.1.0
     */
    public function getCustomerId();

    /**
     * Sets customer ID.
     *
     * @param int $customerId
     * @return $this
     * @since 100.1.0
     */
    public function setCustomerId($customerId);

    /**
     * Get public hash
     *
     * @return string
     * @since 100.1.0
     */
    public function getPublicHash();

    /**
     * Set public hash
     *
     * @param string $hash
     * @return $this
     * @since 100.1.0
     */
    public function setPublicHash($hash);

    /**
     * Get payment method code
     *
     * @return string
     * @since 100.1.0
     */
    public function getPaymentMethodCode();

    /**
     * Set payment method code
     *
     * @param string $code
     * @return $this
     * @since 100.1.0
     */
    public function setPaymentMethodCode($code);

    /**
     * Get type
     *
     * @return string
     * @since 100.1.0
     */
    public function getType();

    /**
     * Set type
     *
     * @param string $type
     * @return $this
     * @since 100.1.0
     */
    public function setType($type);

    /**
     * Get token creation timestamp
     *
     * @return string|null
     * @since 100.1.0
     */
    public function getCreatedAt();

    /**
     * Set token creation timestamp
     *
     * @param string $timestamp
     * @return $this
     * @since 100.1.0
     */
    public function setCreatedAt($timestamp);

    /**
     * Get token expiration timestamp
     *
     * @return string|null
     * @since 100.1.0
     */
    public function getExpiresAt();

    /**
     * Set token expiration timestamp
     *
     * @param string $timestamp
     * @return $this
     * @since 100.1.0
     */
    public function setExpiresAt($timestamp);

    /**
     * Get gateway token ID
     *
     * @return string
     * @since 100.1.0
     */
    public function getGatewayToken();

    /**
     * Set gateway token ID
     *
     * @param string $token
     * @return $this
     * @since 100.1.0
     */
    public function setGatewayToken($token);

    /**
     * Get token details
     *
     * @return string
     * @since 100.1.0
     */
    public function getTokenDetails();

    /**
     * Set token details
     *
     * @param string $details
     * @return $this
     * @since 100.1.0
     */
    public function setTokenDetails($details);

    /**
     * Gets is vault payment record active.
     *
     * @return bool Is active.
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     * @since 100.1.0
     */
    public function getIsActive();

    /**
     * Sets is vault payment record active.
     *
     * @param bool $isActive
     * @return $this
     * @since 100.1.0
     */
    public function setIsActive($isActive);

    /**
     * Gets is vault payment record visible.
     *
     * @return bool Is visible.
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     * @since 100.1.0
     */
    public function getIsVisible();

    /**
     * Sets is vault payment record visible.
     *
     * @param bool $isVisible
     * @return $this
     * @since 100.1.0
     */
    public function setIsVisible($isVisible);

    /**
     * Gets vault payment website id.
     *
     * @return int website id.
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getWebsiteId();

    /**
     * Sets vault payment website id.
     *
     * @param int $websiteId
     * @return $this
     */
    public function setWebsiteId(int $websiteId);
}
