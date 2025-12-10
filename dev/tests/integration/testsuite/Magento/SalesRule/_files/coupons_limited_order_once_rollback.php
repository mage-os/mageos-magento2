<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Sales/_files/quote_with_two_simple_products_rollback.php');
Resolver::getInstance()->requireDataFixture('Magento/SalesRule/_files/coupons_limited_once_rollback.php');
