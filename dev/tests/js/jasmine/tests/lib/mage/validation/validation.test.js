/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
define([
    'jquery',
    'mage/validation/validation',
    'mage/translate'
], function ($) {
    'use strict';

    describe('Custom Validation: validate-grouped-qty', function () {
        let element, params;

        beforeEach(function () {
            // Create the current element to simulate individual input
            element = $('<input>', {
                type: 'text',
                'data-validate': '{"validate-grouped-qty": true}',
                value: '1'
            });

            // Create a container for grouped inputs with only two inputs
            params = $('<div>').append(
                $('<input>', { type: 'text', value: '1', 'data-validate': '{"validate-grouped-qty": true}' }),
                $('<input>', { type: 'text', value: '0', 'data-validate': '{"validate-grouped-qty": true}' })
            );
        });

        afterEach(function () {
            element.remove();
            params.remove();
        });

        function setCurrentElement(val) {
            element.val(val);
        }

        it('should return true when total grouped quantity is greater than 0', function () {
            let isValid = $.validator.methods['validate-grouped-qty'](
                element.val(),
                element[0],
                params
            );

            expect(isValid).toBe(true);
        });

        it('should return false when the total grouped quantity is 0', function () {
            setCurrentElement('0'); // Set the current input to 0
            params.find('input').each(function () {
                $(this).val('0'); // Set both grouped inputs to 0
            });

            let isValid = $.validator.methods['validate-grouped-qty'](
                element.val(),
                element[0],
                params
            );

            expect(isValid).toBe(false);
        });

        it('should return false if any input has a negative value', function () {
            setCurrentElement(-1);// Set the current input to -1
            params.find('input').first().val('-1'); // Set the first input to a negative value

            let isValid = $.validator.methods['validate-grouped-qty'](
                element.val(),
                element[0],
                params
            );

            expect(isValid).toBe(false);
        });

        it('should return true when one input is empty and total is valid', function () {
            params.find('input').eq(1).val(''); // Leave one input empty

            let isValid = $.validator.methods['validate-grouped-qty'](
                element.val(),
                element[0],
                params
            );

            expect(isValid).toBe(true);
        });

        it('should return false when both inputs are empty', function () {
            setCurrentElement('');
            params.find('input').each(function () {
                $(this).val(''); // Set both inputs to empty
            });

            let isValid = $.validator.methods['validate-grouped-qty'](
                element.val(),
                element[0],
                params
            );

            expect(isValid).toBe(false);
        });

        it('should return false if one input is negative and the other is 0', function () {
            setCurrentElement(-1);
            params.find('input').first().val('-1'); // Set one input to negative
            params.find('input').eq(1).val('0');   // Set second input to 0

            let isValid = $.validator.methods['validate-grouped-qty'](
                element.val(),
                element[0],
                params
            );

            expect(isValid).toBe(false);
        });
    });
});
