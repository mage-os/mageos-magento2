<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Black list for the @see \Magento\Test\Integrity\DependencyTest::testUndeclared()
 */
return [
    // Dashboard::getWishlistUrl() only builds a link to the wish list page, so Customer works without Magento_Wishlist
    'app/code/Magento/Customer/Block/Account/Dashboard.php' => ['Magento\Wishlist'],
];
