/**
 * Copyright © Mage-OS. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'Magento_Backend/js/dashboard/period-storage'
], function ($, periodStorage) {
    'use strict';

    describe('Magento_Backend/js/dashboard/period-storage', function () {
        let select;

        beforeEach(function () {
            window.localStorage.removeItem('mage-dashboard-period');
            select = $('<select><option value="today">Today</option><option value="7d">7 days</option></select>')
                .appendTo(document.body);
        });

        afterEach(function () {
            window.localStorage.removeItem('mage-dashboard-period');
            select.remove();
        });

        it('returns null when nothing is stored', function () {
            expect(periodStorage.get()).toBeNull();
            expect(periodStorage.apply(select)).toBeFalse();
            expect(select.val()).toBe('today');
        });

        it('applies a stored period that the select offers', function () {
            periodStorage.set('7d');

            expect(periodStorage.apply(select)).toBeTrue();
            expect(select.val()).toBe('7d');
            expect(periodStorage.apply(select)).toBeFalse();
        });

        it('ignores a stored period the select does not offer', function () {
            periodStorage.set('2y');

            expect(periodStorage.apply(select)).toBeFalse();
            expect(select.val()).toBe('today');
        });
    });
});
