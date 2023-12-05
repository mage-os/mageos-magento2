/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'jquery/validate',
    'Magento_Payment/js/model/credit-card-validation/validator'
], function ($) {
    'use strict';

    describe('Magento_Payment/js/model/credit-card-validation/validator', function () {

        it('Check credit card expiration year validator.', function () {
            var year = new Date().getFullYear();

            expect($.validator.methods['validate-card-year']('1234')).toBeFalsy();
            expect($.validator.methods['validate-card-year']('')).toBeFalsy();
            expect($.validator.methods['validate-card-year']((year - 1).toString())).toBeFalsy();
            expect($.validator.methods['validate-card-year']((year + 1).toString())).toBeTruthy();
        });

        it('Check credit card type validator.', function () {
            var typeValidator = $.validator.methods['validate-card-type'];

            expect(typeValidator('4111111111111111', null, [{type: 'Visa'}])).toBeTruthy();
            expect(typeValidator('1111111111111111', null, [{type: 'Visa'}])).toBeFalsy();
            expect(typeValidator('6759411100000008', null, [{type: 'Maestro Domestic'}])).toBeTruthy();
            expect(typeValidator('4111676770111115', null, [{type: 'Maestro Domestic'}])).toBeFalsy();
        });
    });
});
