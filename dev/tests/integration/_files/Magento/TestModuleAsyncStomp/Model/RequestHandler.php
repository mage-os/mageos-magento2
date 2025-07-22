<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
namespace Magento\TestModuleAsyncStomp\Model;

class RequestHandler
{
    /**
     * @param AsyncTestData $simpleDataItem
     */
    public function process(AsyncTestData $simpleDataItem)
    {
        file_put_contents(
            $simpleDataItem->getTextFilePath(),
            'InvokedFromRequestHandler-' . $simpleDataItem->getValue() . PHP_EOL,
            FILE_APPEND
        );
    }
}
