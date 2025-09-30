<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Review\Test\Unit\Helper;

use Magento\Review\Model\Rating;

/**
 * Test helper for Rating
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class RatingTestHelper extends Rating
{
    /**
     * @var mixed
     */
    private $data = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get data
     *
     * @param mixed $key
     * @param mixed $index
     * @return mixed
     */
    public function getData($key = '', $index = null)
    {
        return $this->data[$key] ?? null;
    }

    /**
     * Set data
     *
     * @param mixed $key
     * @param mixed $value
     * @return $this
     */
    public function setData($key, $value = null)
    {
        if (is_array($key)) {
            $this->data = $key;
        } else {
            $this->data[$key] = $value;
        }
        return $this;
    }

    /**
     * Save method
     *
     * @return $this
     */
    public function save()
    {
        return $this;
    }

    /**
     * Load method
     *
     * @param mixed $modelId
     * @param mixed $field
     * @return $this
     */
    public function load($modelId, $field = null)
    {
        return $this;
    }

    /**
     * Add option vote
     *
     * @param mixed $optionId
     * @param mixed $entityPkValue
     * @return $this
     */
    public function addOptionVote($optionId, $entityPkValue)
    {
        return $this;
    }

    /**
     * Set rating ID
     *
     * @param mixed $ratingId
     * @return $this
     */
    public function setRatingId($ratingId)
    {
        return $this;
    }

    /**
     * Set review ID
     *
     * @param mixed $reviewId
     * @return $this
     */
    public function setReviewId($reviewId)
    {
        return $this;
    }

    /**
     * Set customer ID
     *
     * @param mixed $customerId
     * @return $this
     */
    public function setCustomerId($customerId)
    {
        return $this;
    }
}
