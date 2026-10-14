/**
 * Copyright © Mage-OS. All rights reserved.
 * See COPYING.txt for license details.
 */

/*global FORM_KEY*/
define([
    'jquery',
    'mage/translate',
    'jquery-ui-modules/widget'
], function ($, $t) {
    'use strict';

    /**
     * Loads the Lifetime Sales / Average Order figures after the dashboard has rendered,
     * so a cold statistics cache never blocks the page.
     */
    $.widget('mage.dashboardSales', {
        options: {
            updateUrl: ''
        },

        /**
         * @private
         */
        _create: function () {
            this.loadSales();
        },

        /**
         * @public
         */
        loadSales: function () {
            $.ajax({
                url: this.options.updateUrl,
                data: {
                    'form_key': FORM_KEY
                },
                dataType: 'html',
                type: 'POST',
                context: this,
                success: function (response) {
                    this.element.replaceWith(response);
                },
                error: function () {
                    this.element.find('.dashboard-sales-loading')
                        .text($t('Unavailable'))
                        .attr('aria-busy', 'false');
                }
            });
        }
    });

    return $.mage.dashboardSales;
});
