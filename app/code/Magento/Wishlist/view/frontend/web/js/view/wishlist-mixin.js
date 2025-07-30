/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */

define(['jquery', 'Magento_Customer/js/customer-data'], function ($, customerData) {
    'use strict';

    return function (WishlistComponent) {
        return WishlistComponent.extend({
            initialize: function () {
                this._super();

                customerData.reload(['wishlist'], true);

                const wishlist = customerData.get('wishlist');
                const selector = '.wishlist .counter.qty, .customer-menu .wishlist .counter, .link.wishlist .counter';

                wishlist.subscribe(function (updatedWishlist) {
                    if (typeof updatedWishlist.counter !== 'undefined') {
                        const counters = $(selector);

                        if (counters.length && counters.first().text().trim() !== updatedWishlist.counter.toString()) {
                            counters.text(updatedWishlist.counter);
                        }
                    }
                });

                return this;
            }
        });
    };
});
