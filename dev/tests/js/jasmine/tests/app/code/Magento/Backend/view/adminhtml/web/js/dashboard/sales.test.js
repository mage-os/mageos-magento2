/**
 * Copyright © Mage-OS. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'Magento_Backend/js/dashboard/sales'
], function ($) {
    'use strict';

    describe('Magento_Backend/js/dashboard/sales', function () {
        let element, ajaxSpy;

        beforeEach(function () {
            window.FORM_KEY = 'test-form-key';
            element = $('<div id="dashboard_sales_deferred">' +
                '<span class="dashboard-sales-loading" aria-busy="true">Loading...</span>' +
                '</div>').appendTo(document.body);
            ajaxSpy = spyOn($, 'ajax');
        });

        afterEach(function () {
            $('#dashboard_sales_deferred').remove();
            $('#dashboard_sales_loaded').remove();
        });

        it('requests the figures once on creation', function () {
            element.dashboardSales({
                updateUrl: '/admin/dashboard/ajaxBlock/block/sales/'
            });

            expect(ajaxSpy).toHaveBeenCalledTimes(1);
            expect(ajaxSpy.calls.mostRecent().args[0].url).toBe('/admin/dashboard/ajaxBlock/block/sales/');
            expect(ajaxSpy.calls.mostRecent().args[0].type).toBe('POST');
            expect(ajaxSpy.calls.mostRecent().args[0].data).toEqual({
                'form_key': 'test-form-key'
            });
        });

        it('replaces the placeholder with the response', function () {
            ajaxSpy.and.callFake(function (settings) {
                settings.success.call(settings.context, '<div id="dashboard_sales_loaded">$1.00</div>');
            });

            element.dashboardSales({
                updateUrl: '/test'
            });

            expect($('#dashboard_sales_deferred').length).toBe(0);
            expect($('#dashboard_sales_loaded').text()).toBe('$1.00');
        });

        it('marks the placeholder unavailable when the request fails', function () {
            ajaxSpy.and.callFake(function (settings) {
                settings.error.call(settings.context);
            });

            element.dashboardSales({
                updateUrl: '/test'
            });

            const placeholder = $('#dashboard_sales_deferred .dashboard-sales-loading');

            expect(placeholder.text()).toBe('Unavailable');
            expect(placeholder.attr('aria-busy')).toBe('false');
        });
    });
});
