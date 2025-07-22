/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */

define([
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'underscore',
    'jquery'
], function (Component, customerData, _, $) {
    'use strict';

    var wishlistReloaded = false;

    return Component.extend({
        /** @inheritdoc */
        initialize: function () {
            this._super();
            this.wishlist = customerData.get('wishlist');
            this.company = customerData.get('company');

            if (!wishlistReloaded
                && !_.isEmpty(this.wishlist())
                // Expired section names are reloaded on page load.
                && _.indexOf(customerData.getExpiredSectionNames(), 'wishlist') === -1
                && window.checkout
                && window.checkout.storeId
                && (window.checkout.storeId !== this.wishlist().storeId || this.company().is_enabled)
            ) {
                //set count to 0 to prevent "Wishlist products" blocks and count to show with wrong count and items
                this.wishlist().counter = 0;
                customerData.invalidate(['wishlist']);
                customerData.reload(['wishlist'], false);
                wishlistReloaded = true;
            }

            // Ensure wishlist data is loaded on initialization
            this.ensureWishlistDataLoaded();

            // Always try depersonalization for cached pages
            this.handleDepersonalization();
        },

        /**
         * Ensure wishlist data is loaded
         */
        ensureWishlistDataLoaded: function () {
            var self = this;

            // Check if wishlist data is empty
            if (_.isEmpty(this.wishlist())) {
                // Load wishlist data
                customerData.reload(['wishlist'], false).done(function (data) {
                    if (data.wishlist && data.wishlist.counter) {
                        self.updateWishlistUI();
                    }
                });
            } else {
                self.updateWishlistUI();
            }
        },

        /**
         * Handle depersonalization scenarios
         */
        handleDepersonalization: function () {
            var self = this,
                attempts = [1000, 3000, 5000]; // Try at 1s, 3s, and 5s

            function onWishlistReloaded(data) {
                if (data.wishlist && data.wishlist.counter) {
                    self.updateWishlistUI();
                }
            }

            function reloadIfEmpty() {
                // Only reload if data is still empty
                if (_.isEmpty(self.wishlist())) {
                    customerData.reload(['wishlist'], false).done(onWishlistReloaded);
                } else {
                    self.updateWishlistUI();
                }
            }

            // Listen for page load events to handle depersonalized pages
            $(function () {
                attempts.forEach(function (delay) {
                    setTimeout(reloadIfEmpty, delay);
                });
            });
        },

        /**
         * Update wishlist UI elements
         */
        updateWishlistUI: function () {
            var wishlistData = this.wishlist(),
                selectors = [
                    '.wishlist .counter.qty',
                    '.customer-menu .wishlist .counter',
                    '[data-bind*="wishlist"] .counter',
                    '.link.wishlist .counter'
                ];

            if (wishlistData && wishlistData.counter) {
                // Try multiple selectors to find wishlist counters
                selectors.forEach(function (selector) {
                    var counters = $(selector);

                    if (counters.length > 0) {
                        counters.each(function () {
                            var $counter = $(this);

                            if ($counter.text() !== wishlistData.counter) {
                                $counter.text(wishlistData.counter);
                            }
                        });
                    }
                });
            }
        }
    });
});
