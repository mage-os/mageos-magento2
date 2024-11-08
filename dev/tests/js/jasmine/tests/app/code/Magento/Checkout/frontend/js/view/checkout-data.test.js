/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */

define([
    'Magento_Checkout/js/checkout-data',
    'Magento_Customer/js/customer-data'
], function (checkoutData, storage) {
    'use strict';

    describe('Magento_Checkout/js/checkout-data', function () {
        let cacheKey = 'checkout-data',
            testData = {
                shippingAddressFromData: {base: {address1: 'address1'}}
            },

            /** Stub */
            getStorageData = function () {
                return testData;
            };

        window.checkoutConfig = {
            websiteCode: 'base'
        };

        beforeEach(function () {
            spyOn(storage, 'set');
        });

        it('should save selected shipping address per website', function () {
            checkoutData.setShippingAddressFromData({address1: 'address1'});
            expect(storage.set).toHaveBeenCalledWith(cacheKey, jasmine.objectContaining(testData));
        });

        it('should return null if no shipping address data exists', function () {
            expect(checkoutData.getShippingAddressFromData()).toBeNull();
        });

        it('should get shipping address from data per website', function () {
            spyOn(storage, 'get').and.returnValue(getStorageData);
            let address = checkoutData.getShippingAddressFromData();

            expect(address).toEqual(testData.shippingAddressFromData.base);
        });
    });
});
