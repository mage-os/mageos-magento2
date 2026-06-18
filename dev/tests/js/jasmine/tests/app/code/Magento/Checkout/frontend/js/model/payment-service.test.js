/**
 * Copyright 2022 Adobe
 * All Rights Reserved.
 */

define([
    'squire',
    'ko'
], function (Squire, ko) {
    'use strict';

    let injector = new Squire(),
        paymentService,
        methods = [
            {title: 'Credit Card', method: 'credit_card'},
            {title: 'Stored Cards', method: 'credit_card_vault'}
        ],
        mocksPaymentMethodCheckmo = {
            'Magento_Checkout/js/model/quote': {
                paymentMethod: ko.observable({
                    'method': 'checkmo'
                })
            }
        },
        mocksPaymentMethodVault = {
            'Magento_Checkout/js/model/quote': {
                paymentMethod: ko.observable({
                    'method': 'credit_card_vault_1'
                })
            }
        },
        mocksPaymentMethodSingle = {
            'Magento_Checkout/js/model/quote': {
                totals: ko.observable({'grand_total': 10}),
                paymentMethod: ko.observable(null)
            },
            'Magento_Checkout/js/checkout-data': {
                setSelectedPaymentMethod: jasmine.createSpy('setSelectedPaymentMethod'),
                getSelectedPaymentMethod: jasmine.createSpy('getSelectedPaymentMethod')
            },
            'Magento_Checkout/js/model/payment/method-list': ko.observableArray([]),
            'Magento_Checkout/js/action/select-payment-method': jasmine.createSpy('selectPaymentMethod')
        },
        mocksPaymentMethodFree = {
            'Magento_Checkout/js/model/quote': {
                totals: ko.observable({'grand_total': 0}),
                paymentMethod: ko.observable(null)
            },
            'Magento_Checkout/js/checkout-data': {
                setSelectedPaymentMethod: jasmine.createSpy('setSelectedPaymentMethod'),
                getSelectedPaymentMethod: jasmine.createSpy('getSelectedPaymentMethod')
            },
            'Magento_Checkout/js/model/payment/method-list': ko.observableArray([]),
            'Magento_Checkout/js/action/select-payment-method': jasmine.createSpy('selectPaymentMethod')
        };

    beforeEach(function (done) {
        window.checkoutConfig = {
            vault: {
                credit_card_vault: {}
            },
            payment: {
                vault: {
                    credit_card_vault_1: {},
                    credit_card_vault_2: {}
                }
            }
        };
        done();
    });

    afterEach(function () {
        try {
            injector.remove();
            injector.clean();
        } catch (e) { // eslint-disable-line no-unused-vars
        }
    });

    describe('Magento_Checkout/js/model/payment-service', function () {
        beforeEach(function (done) {
            injector.mock(mocksPaymentMethodCheckmo);
            // eslint-disable-next-line max-nested-callbacks
            injector.require(['Magento_Checkout/js/model/payment-service'], function (instance) {
                paymentService = instance;
                done();
            });
        });
        it('payment method is not enabled', function () {
            paymentService.setPaymentMethods(methods);
            expect(mocksPaymentMethodCheckmo['Magento_Checkout/js/model/quote'].paymentMethod()).toBeNull();
        });
    });

    describe('Magento_Checkout/js/model/payment-service single method persistence', function () {
        beforeEach(function (done) {
            injector = new Squire();
            mocksPaymentMethodSingle['Magento_Checkout/js/checkout-data'].setSelectedPaymentMethod =
                jasmine.createSpy('setSelectedPaymentMethod');
            injector.mock(mocksPaymentMethodSingle);
            // eslint-disable-next-line max-nested-callbacks
            injector.require(['Magento_Checkout/js/model/payment-service'], function (instance) {
                paymentService = instance;
                done();
            });
        });

        it('persists the selected payment method to checkout data when only one method is available', function () {
            var singleMethod = [{title: 'Check / Money Order', method: 'checkmo'}];

            paymentService.setPaymentMethods(singleMethod);
            expect(mocksPaymentMethodSingle['Magento_Checkout/js/checkout-data'].setSelectedPaymentMethod)
                .toHaveBeenCalledWith('checkmo');
        });
    });

    describe('Magento_Checkout/js/model/payment-service free method persistence', function () {
        beforeEach(function (done) {
            injector = new Squire();
            mocksPaymentMethodFree['Magento_Checkout/js/checkout-data'].setSelectedPaymentMethod =
                jasmine.createSpy('setSelectedPaymentMethod');
            injector.mock(mocksPaymentMethodFree);
            // eslint-disable-next-line max-nested-callbacks
            injector.require(['Magento_Checkout/js/model/payment-service'], function (instance) {
                paymentService = instance;
                done();
            });
        });

        it('persists the free payment method to checkout data when grand total is zero', function () {
            var methods = [
                {title: 'Check / Money Order', method: 'checkmo'},
                {title: 'Free', method: 'free'}
            ];

            paymentService.setPaymentMethods(methods);
            expect(mocksPaymentMethodFree['Magento_Checkout/js/checkout-data'].setSelectedPaymentMethod)
                .toHaveBeenCalledWith('free');
        });
    });

    describe('Magento_Checkout/js/model/payment-service vault methods', function () {
        beforeEach(function (done) {
            injector.mock(mocksPaymentMethodVault);
            // eslint-disable-next-line max-nested-callbacks
            injector.require(['Magento_Checkout/js/model/payment-service'], function (instance) {
                paymentService = instance;
                done();
            });
        });
        it('payment method is stored credit card', function () {
            paymentService.setPaymentMethods(methods);
            expect(mocksPaymentMethodVault['Magento_Checkout/js/model/quote'].paymentMethod().method)
                .toEqual('credit_card_vault_1');
        });
    });
});
