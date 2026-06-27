/**
 * Copyright 2026 Adobe
 * All Rights Reserved.
 */

/* eslint-disable max-nested-callbacks */
define([
    'squire'
], function (Squire) {
    'use strict';

    describe('Magento_Theme/js/view/messages', function () {
        var injector, customerDataMock, currentMessages, purgeMessages;

        beforeEach(function (done) {
            injector = new Squire();
            currentMessages = {};
            customerDataMock = {
                get: function () {
                    return function () {
                        return currentMessages;
                    };
                },
                set: jasmine.createSpy('set'),
                preReloadSectionDataIds: undefined
            };

            injector.mock({
                'uiComponent': {
                    extend: function (definition) {
                        return definition;
                    }
                },
                'Magento_Ui/js/lib/core/collection': {
                    extend: function (definition) {
                        return definition;
                    }
                },
                'Magento_Customer/js/customer-data': customerDataMock,
                'escaper': {
                    escapeHtml: function (s) {
                        return s;
                    }
                },
                'jquery': {
                    cookieStorage: {
                        get: function () {
                            return [];
                        },
                        set: function () {}
                    },
                    mage: {
                        cookies: {
                            set: function () {}
                        }
                    }
                },
                'jquery/jquery-storageapi': {}
            });

            injector.require(['Magento_Theme/js/view/messages'], function (definition) {
                purgeMessages = definition.purgeMessages.bind({
                    messages: function () {
                        return currentMessages;
                    }
                });
                done();
            });
        });

        afterEach(function () {
            customerDataMock.preReloadSectionDataIds = undefined;

            try {
                injector.clean();
                injector.remove();
            } catch (e) { // eslint-disable-line no-unused-vars
            }
        });

        describe('"purgeMessages" method', function () {
            it('Does not clear messages when data_id differs from pre-reload snapshot (fresh XHR data)', function () {
                currentMessages = {
                    'data_id': 200,
                    'messages': [{'type': 'error', 'text': 'Not enough items for sale'}]
                };
                customerDataMock.preReloadSectionDataIds = {'messages': 100};

                purgeMessages();

                expect(customerDataMock.set).not.toHaveBeenCalled();
            });

            it('Clears messages when data_id matches pre-reload snapshot (carry-over localStorage data)', function () {
                currentMessages = {
                    'data_id': 100,
                    'messages': [{'type': 'error', 'text': 'Not enough items for sale'}]
                };
                customerDataMock.preReloadSectionDataIds = {'messages': 100};

                purgeMessages();

                expect(customerDataMock.set).toHaveBeenCalledWith('messages', {});
            });
        });
    });
});
