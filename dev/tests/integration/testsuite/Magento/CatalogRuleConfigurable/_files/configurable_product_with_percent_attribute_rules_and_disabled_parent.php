<?php
/**
 * Copyright 2026 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\Area;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture(
    'Magento/CatalogRuleConfigurable/_files/configurable_product_with_percent_rules_for_children.php'
);

Bootstrap::getInstance()->loadArea(Area::AREA_ADMINHTML);

$objectManager = Bootstrap::getObjectManager();
/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->create(ProductRepositoryInterface::class);

/** @var Product $configurable */
$configurable = $productRepository->get('configurable');
$configurable->setStatus(2);
$productRepository->save($configurable);
