<?php
/**
 * Copyright 2017 Adobe
 * All Rights Reserved.
 */
namespace Magento\Analytics\Model\Connector\ResponseHandler;

use Magento\Analytics\Model\Connector\Http\ResponseHandlerInterface;

/**
 * Return positive answer that request was finished successfully.
 */
class Update implements ResponseHandlerInterface
{
    /**
     * @param array $responseBody
     *
     * @return bool|string
     */
    public function handleResponse(array $responseBody)
    {
        return true;
    }
}
