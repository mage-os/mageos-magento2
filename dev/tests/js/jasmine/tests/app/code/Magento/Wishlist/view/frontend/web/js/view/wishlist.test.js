/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */

define([
    'squire'
], function (Squire) {
    'use strict';

    describe('Magento_Wishlist/js/view/wishlist', function () {
        var wishlistComponent,
            mockWishlist,
            mockCompany,
            mockCustomerData,
            injector;

        function setupInjector() {
            injector = new Squire();

            mockWishlist = {
                counter: 1,
                items: [{ id: 1, name: 'Test Product' }],
                storeId: 1
            };

            mockCompany = {
                is_enabled: true
            };

            injector.clean();

            mockCustomerData = {
                get: jasmine.createSpy('customerDataGet').and.callFake(function (key) {
                    if (key === 'wishlist') {
                        return function () { return mockWishlist; };
                    } else if (key === 'company') {
                        return function () { return mockCompany; };
                    }
                }),
                reload: jasmine.createSpy('customerDataReload'),
                invalidate: jasmine.createSpy(),
                getExpiredSectionNames: jasmine.createSpy('getExpiredSectionNames').and.returnValue([])
            };
            injector.mock('Magento_Customer/js/customer-data', mockCustomerData);
        }

        function cleanupInjector() {
            try {
                injector.clean();
                injector.remove();
                delete window.checkout;
            } catch (e) { // eslint-disable-line no-unused-vars
                // Ignore cleanup errors
            }
        }

        function loadWishlistComponent() {
            return new Promise(resolve => {
                injector.require(['Magento_Wishlist/js/view/wishlist'], function (WishlistComponent) {
                    wishlistComponent = new WishlistComponent();
                    resolve();
                });
            });
        }

        beforeEach(function (done) {
            setupInjector();
            loadWishlistComponent().then(function () {
                done();
            });
        });

        afterEach(function () {
            cleanupInjector();
        });

        describe('Initialization', function () {
            it('should call customerData.get with "wishlist"', function () {
                expect(mockCustomerData.get).toHaveBeenCalledWith('wishlist');
            });

            it('should call customerData.get with "company"', function () {
                expect(mockCustomerData.get).toHaveBeenCalledWith('company');
            });

            it('should invalidate wishlist if storeIds do not match', function () {
                window.checkout = { storeId: 2 };
                wishlistComponent.initialize();
                expect(mockCustomerData.invalidate).toHaveBeenCalledWith(['wishlist']);
            });

            it('should not reload wishlist if storeIds match and company is disabled', function () {
                window.checkout = { storeId: 1 };
                mockCompany.is_enabled = false;
                wishlistComponent.initialize();
                expect(mockCustomerData.reload).not.toHaveBeenCalledWith(['wishlist'], false);
            });

            it('should reload wishlist if storeIds do not match', function () {
                window.checkout = { storeId: 2 };
                wishlistComponent.initialize();
                expect(mockCustomerData.reload).toHaveBeenCalledWith(['wishlist'], false);
            });

            it('should reload wishlist if storeIds match and company is enabled', function () {
                window.checkout = { storeId: 1 };
                mockCompany.is_enabled = true;
                wishlistComponent.initialize();
                expect(mockCustomerData.reload).toHaveBeenCalledWith(['wishlist'], false);
            });
        });

        describe('Core Methods', function () {
            it('should have ensureWishlistDataLoaded method', function () {
                expect(typeof wishlistComponent.ensureWishlistDataLoaded).toBe('function');
            });

            it('should have handleDepersonalization method', function () {
                expect(typeof wishlistComponent.handleDepersonalization).toBe('function');
            });

            it('should have updateWishlistUI method', function () {
                expect(typeof wishlistComponent.updateWishlistUI).toBe('function');
            });
        });

        describe('Data Handling', function () {
            it('should have wishlist data available', function () {
                expect(wishlistComponent.wishlist).toBeDefined();
                expect(wishlistComponent.wishlist()).toEqual(mockWishlist);
            });

            it('should have company data available', function () {
                expect(wishlistComponent.company).toBeDefined();
                expect(wishlistComponent.company()).toEqual(mockCompany);
            });

            it('should handle empty wishlist data', function () {
                mockWishlist.counter = 0;
                expect(wishlistComponent.wishlist().counter).toBe(0);
            });
        });

        describe('ensureWishlistDataLoaded', function () {
            it('should not call customerData.reload when wishlist has data', function () {
                mockWishlist.counter = 3;
                wishlistComponent.ensureWishlistDataLoaded();
                expect(mockCustomerData.reload).not.toHaveBeenCalled();
            });
        });

        describe('handleDepersonalization', function () {
            it('should set up timeout attempts', function () {
                spyOn(window, 'setTimeout');
                wishlistComponent.handleDepersonalization();
                expect(window.setTimeout).toHaveBeenCalledTimes(1);
            });

            it('should not call customerData.reload when wishlist has data', function () {
                // Reset mock and set wishlist to have data
                mockCustomerData.reload.calls.reset();
                mockWishlist.counter = 3;
                spyOn(window, 'setTimeout').and.callFake(function (callback) {
                    callback();
                });
                wishlistComponent.handleDepersonalization();
                expect(mockCustomerData.reload).not.toHaveBeenCalled();
            });
        });

        describe('updateWishlistUI', function () {
            it('should execute without errors when called', function () {
                expect(function () {
                    wishlistComponent.updateWishlistUI();
                }).not.toThrow();
            });

            it('should handle wishlist data with counter', function () {
                mockWishlist.counter = '5 items';
                expect(function () {
                    wishlistComponent.updateWishlistUI();
                }).not.toThrow();
            });

            it('should handle wishlist data without counter', function () {
                mockWishlist.counter = null;
                expect(function () {
                    wishlistComponent.updateWishlistUI();
                }).not.toThrow();
            });
        });
    });
});
