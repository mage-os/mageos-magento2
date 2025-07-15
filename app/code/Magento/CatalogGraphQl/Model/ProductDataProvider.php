<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogGraphQl\Model;

use Magento\Catalog\Api\ProductRepositoryInterface;

/**
 * Product data provider
 *
 * TODO: will be replaces on deferred mechanism
 */
class ProductDataProvider
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * Get product data by id
     *
     * @param int $productId
     * @return array
     */
    public function getProductDataById(int $productId): array
    {
        $product = $this->productRepository->getById($productId);
        $productData = $product->toArray();
        $productData['model'] = $product;
        return $productData;
    }
}
