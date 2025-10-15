<?php
/**
 * Copyright 2020 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Wishlist\Model\Wishlist\Data;

/**
 * DTO represents entered options
 */
class EnteredOption
{
    /**
     * @var string
     */
    private $uid;

    /**
     * @var string
     */
    private $value;

    /**
     * @param string $uid
     * @param string $value
     */
    public function __construct(string $uid, string $value)
    {
        $this->uid = $uid;
        $this->value = $value;
    }

    /**
     * Get entered option id
     *
     * @return string
     */
    public function getUid(): string
    {
        return $this->uid;
    }

    /**
     * Get entered option value
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }
}
