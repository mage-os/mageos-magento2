<?php
/**
 * Copyright 2020 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogGraphQl\Model\Resolver\Type;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Query\Resolver\TypeResolverInterface;

/**
 * Resolves the concrete type of a WishlistItemInterface object from its product type
 *
 * Product type modules register their wish list item type in the supportedTypes argument.
 * It lives in CatalogGraphQl together with the base WishlistItemInterface, so the schema
 * still builds when Magento_WishlistGraphQl is not installed.
 */
class WishlistItemType implements TypeResolverInterface
{
    /**
     * @var array
     */
    private $supportedTypes = [];

    /**
     * @param array $supportedTypes
     */
    public function __construct(array $supportedTypes = [])
    {
        $this->supportedTypes = $supportedTypes;
    }

    /**
     * Resolving wishlist item type
     *
     * @param array $data
     *
     * @return string
     *
     * @throws LocalizedException
     */
    public function resolveType(array $data): string
    {
        if (!$data['model'] instanceof ProductInterface) {
            throw new LocalizedException(__('"model" should be a "%instance" instance', [
                'instance' => ProductInterface::class
            ]));
        }

        $productTypeId = $data['model']->getTypeId();

        if (!isset($this->supportedTypes[$productTypeId])) {
            throw new LocalizedException(
                __('Product "%product_type" type is not supported', ['product_type' => $productTypeId])
            );
        }

        return $this->supportedTypes[$productTypeId];
    }
}
