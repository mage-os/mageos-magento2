/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */

define([
    'jquery',
    'mage/translate'
], function ($) {
    'use strict';

    /**
     * Compare central cart subtotal with summary subtotal and click Update once if mismatched.
     */
    return function initEnsureSubtotalSync(config, element) {
        var $root = $(element || document);
        var clicked = false;

        function parsePrice(text) {
            if (!text) {
                return NaN;
            }
            // Remove non-numeric except . , - then normalize
            var cleaned = ('' + text).replace(/[^0-9,.-]/g, '');
            // If both , and . exist, assume , is thousands
            if (cleaned.indexOf(',') > -1 && cleaned.indexOf('.') > -1) {
                cleaned = cleaned.replace(/,/g, '');
            } else if (cleaned.indexOf(',') > -1 && cleaned.indexOf('.') === -1) {
                // European format: swap , to .
                cleaned = cleaned.replace(/,/g, '.');
            }
            var n = parseFloat(cleaned);
            return isNaN(n) ? NaN : Math.round(n * 100) / 100;
        }

        function getCentralSubtotal() {
            // Sum of row totals on the table
            var sum = 0;
            $root.find('#shopping-cart-table .col.subtotal .cart-price').each(function () {
                var text = $(this).text();
                var val = parsePrice(text);
                if (!isNaN(val)) {
                    sum += val;
                }
            });
            return Math.round(sum * 100) / 100;
        }

        function getSummarySubtotal() {
            // Summary subtotal in cart totals knockout template
            var text = $('#cart-totals .totals.sub .amount .price').first().text();
            return parsePrice(text);
        }

        function trySync() {
            if (clicked) {
                return;
            }
            var central = getCentralSubtotal();
            var summary = getSummarySubtotal();
            if (!isNaN(central) && !isNaN(summary) && central !== summary) {
                var $updateBtn = $root.find('.cart.main.actions button.action.update');
                if ($updateBtn.length) {
                    clicked = true;
                    $updateBtn.trigger('click');
                }
            }
        }

        // Initial attempt after DOM ready and after a short delay to allow KO to render totals
        $(function () {
            trySync();
            setTimeout(trySync, 300);
            // Observe changes in totals area to re-check once
            var totals = document.getElementById('cart-totals');
            if (totals && typeof MutationObserver !== 'undefined') {
                var obs = new MutationObserver(function () {
                    trySync();
                    if (clicked) {
                        obs.disconnect();
                    }
                });
                obs.observe(totals, { childList: true, subtree: true, characterData: true });
            }
        });
    };
});
