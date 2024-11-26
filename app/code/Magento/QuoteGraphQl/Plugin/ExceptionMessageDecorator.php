<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\QuoteGraphQl\Plugin;

use Magento\GraphQl\Helper\Error\AggregateExceptionMessageFormatter;
use Magento\QuoteGraphQl\Model\ErrorMapper;

class ExceptionMessageDecorator
{
    /**
     * @param ErrorMapper $errorDecorator
     */
    public function __construct(readonly ErrorMapper $errorDecorator)
    {
    }

    /**
     * Add error id to an exception if it is not set
     *
     * @param AggregateExceptionMessageFormatter $subject
     * @param mixed $result
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetFormatted(AggregateExceptionMessageFormatter $subject, mixed $result): mixed
    {
        if (!$result->getCode() && ($errorId = $this->errorDecorator->getErrorMessageId($result->getMessage()))) {
            $exceptionType = get_class($result);
            $result = new $exceptionType(
                __($result->getMessage()),
                $result,
                $errorId
            );
        }

        return $result;
    }
}
