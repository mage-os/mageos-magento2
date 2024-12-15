<?php
/**
 * Copyright 2011 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\GroupedProduct\ViewModel;

use Magento\CatalogInventory\Model\Product\QuantityValidator;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * ViewModel for Grouped Products Block
 */
class ValidateQuantity implements ArgumentInterface
{
    /**
     * @var Json
     */
    private $serializer;

    /**
     * @var QuantityValidator
     */
    private $productQuantityValidator;

    /**
     * @param Json $serializer
     * @param QuantityValidator $productQuantityValidator
     */
    public function __construct(
        Json $serializer,
        QuantityValidator $productQuantityValidator,
    ) {
        $this->serializer = $serializer;
        $this->productQuantityValidator = $productQuantityValidator;
    }

    /**
     * To get the quantity validators
     *
     * @param int $productId
     * @param int $websiteId
     *
     * @return string
     */
    public function getQuantityValidators($productId, $websiteId): string
    {
        return $this->serializer->serialize(
            array_merge(
                ['validate-grouped-qty' => '#super-product-table'],
                $this->productQuantityValidator->getData($productId, $websiteId)
            )
        );
    }
}
