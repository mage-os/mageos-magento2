<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\WeeeGraphQl\Model\Resolver;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Sales\Model\Order\Item;
use Magento\Store\Api\Data\StoreInterface;
use Magento\WeeeGraphQl\Model\FixedProductTaxes\PricesProvider;

/**
 * Resolver for the fixed_product_taxes in OrderItemPrices
 */
class FixedProductTaxes implements ResolverInterface
{
    /**
     * @param PricesProvider $pricesProvider
     */
    public function __construct(
        private readonly PricesProvider $pricesProvider
    ) {
    }

    /**
     * @inheritDoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, ?array $value = null, ?array $args = null): array
    {
        if (!isset($value['model']) || !($value['model'] instanceof Item)) {
            throw new LocalizedException(__('"model" value should be specified'));
        }

        /** @var Item $orderItem */
        $orderItem = $value['model'];
        /** @var StoreInterface $store */
        $store = $context->getExtensionAttributes()->getStore();

        return $this->pricesProvider->execute($orderItem, $store);
    }
}
