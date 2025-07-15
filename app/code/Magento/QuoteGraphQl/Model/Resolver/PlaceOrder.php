<?php
/**
 * Copyright 2019 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\QuoteGraphQl\Model\Resolver;

use Magento\Framework\Exception\AuthorizationException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\GraphQl\Helper\Error\AggregateExceptionMessageFormatter;
use Magento\QuoteGraphQl\Model\Cart\GetCartForCheckout;
use Magento\QuoteGraphQl\Model\Cart\PlaceOrder as PlaceOrderModel;
use Magento\QuoteGraphQl\Model\ErrorMapper;
use Magento\QuoteGraphQl\Model\QuoteException;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\SalesGraphQl\Model\Formatter\Order as OrderFormatter;

/**
 * Resolver for placing order after payment method has already been set
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PlaceOrder implements ResolverInterface
{
    /**
     * @param GetCartForCheckout $getCartForCheckout
     * @param PlaceOrderModel $placeOrder
     * @param OrderRepositoryInterface $orderRepository
     * @param OrderFormatter $orderFormatter
     * @param AggregateExceptionMessageFormatter $errorMessageFormatter
     * @param ErrorMapper $errorMapper
     */
    public function __construct(
        private readonly GetCartForCheckout $getCartForCheckout,
        private readonly PlaceOrderModel $placeOrder,
        private readonly OrderRepositoryInterface $orderRepository,
        private readonly OrderFormatter $orderFormatter,
        private readonly AggregateExceptionMessageFormatter $errorMessageFormatter,
        private readonly ErrorMapper $errorMapper
    ) {
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, ?array $value = null, ?array $args = null)
    {
        if (empty($args['input']['cart_id'])) {
            throw new GraphQlInputException(__('Required parameter "cart_id" is missing'));
        }

        $maskedCartId = $args['input']['cart_id'];
        $userId = (int)$context->getUserId();
        $storeId = (int)$context->getExtensionAttributes()->getStore()->getId();
        try {
            $cart = $this->getCartForCheckout->execute($maskedCartId, $userId, $storeId);
            $orderId = $this->placeOrder->execute($cart, $maskedCartId, $userId);
            $order = $this->orderRepository->get($orderId);
        } catch (AuthorizationException $exception) {
            throw new GraphQlAuthorizationException(
                __($exception->getMessage())
            );
        } catch (LocalizedException $exception) {
            $exception = $this->errorMessageFormatter->getFormatted(
                $exception,
                __('Unable to place order: A server error stopped your order from being placed. ' .
                    'Please try to place your order again'),
                'Unable to place order',
                $field,
                $context,
                $info
            );
            $exceptionCode = $exception->getCode();
            if (!$exceptionCode) {
                $exceptionCode = $this->errorMapper->getErrorMessageId($exception->getMessage());
            }

            throw new QuoteException(__($exception->getMessage()), $exception, $exceptionCode);
        }

        return [
            'order' => [
                'order_number' => $order?->getIncrementId(),
                // @deprecated The order_id field is deprecated, use order_number instead
                'order_id' => $order?->getIncrementId(),
            ],
            'orderV2' => $order ? $this->orderFormatter->format($order) : null
        ];
    }
}
