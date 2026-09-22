/**
 * Copyright © Mage-OS. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery'
], function ($) {
    'use strict';

    var KEY = 'mage-dashboard-period';

    /**
     * Remembers the dashboard period selection in this browser.
     */
    return {
        /**
         * @returns {String|null}
         */
        get: function () {
            try {
                return window.localStorage.getItem(KEY);
            } catch (e) {
                return null;
            }
        },

        /**
         * @param {String} period
         */
        set: function (period) {
            try {
                window.localStorage.setItem(KEY, period);
            } catch (e) {
                // storage unavailable; selection simply is not remembered
            }
        },

        /**
         * Select the remembered period in the given select element without firing change events.
         *
         * @param {String|jQuery} select
         * @returns {Boolean} whether the selection was changed
         */
        apply: function (select) {
            var $select = $(select),
                period = this.get();

            if (!$select.length || !period || $select.val() === period ||
                !$select.find('option[value="' + period + '"]').length) {
                return false;
            }
            $select.val(period);

            return true;
        }
    };
});
