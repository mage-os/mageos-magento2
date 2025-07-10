<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\QuoteGraphQl\Model;

use Magento\Framework\Exception\AuthorizationException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\GraphQl\Helper\Error\AggregateExceptionMessageFormatter;

class OrderErrorProcessor
{
    /**
     * @param AggregateExceptionMessageFormatter $errorMessageFormatter
     * @param ErrorMapper $errorMapper
     */
    public function __construct(
        private readonly AggregateExceptionMessageFormatter $errorMessageFormatter,
        private readonly ErrorMapper $errorMapper
    ) {
    }

    /**
     * @param LocalizedException $exception
     * @param Field $field
     * @param $context
     * @param ResolveInfo $info
     * @throws GraphQlAuthorizationException
     * @throws QuoteException
     */
    public function execute(
        LocalizedException $exception,
        Field $field,
        $context,
        ResolveInfo $info
    ): void {
        if ($exception instanceof AuthorizationException) {
            throw new GraphQlAuthorizationException(
                __($exception->getMessage())
            );
        }
        $exception = $this->errorMessageFormatter->getFormatted(
            $exception,
            __('A server error stopped your order from being placed. ' .
                'Please try to place your order again'),
            'Unable to place order',
            $field,
            $context,
            $info
        );
        $exceptionCode = $exception->getCode();
        if (!$exceptionCode) {
            $exceptionCode = $this->errorMapper->getErrorMessageId($exception->getRawMessage());
        }

        throw new QuoteException(__($exception->getMessage()), $exception, $exceptionCode);
    }
}
