<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
namespace Magento\Payment\Gateway\Command\Result;

use Magento\Payment\Gateway\Command\ResultInterface;

/**
 * Container for array that should be returned as command result.
 *
 * @api
 * @since 100.0.2
 */
class ArrayResult implements ResultInterface
{
    /**
     * @var array
     */
    private $array;

    /**
     * @param array $array
     */
    public function __construct(array $array = [])
    {
        $this->array = $array;
    }

    /**
     * Returns result interpretation
     *
     * @return array
     */
    public function get()
    {
        return $this->array;
    }
}
