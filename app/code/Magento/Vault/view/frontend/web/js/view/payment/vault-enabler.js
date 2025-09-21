/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
/*browser:true*/

/* @api */
define(
    [
        'uiElement'
    ],
    function (
        Component
    ) {
        'use strict';

        return Component.extend({
            defaults: {
                isActivePaymentTokenEnabler: true
            },

            /**
             * @param {String} paymentCode
             */
            setPaymentCode: function (paymentCode) {
                this.paymentCode = paymentCode;
            },

            /**
             * @returns {Object}
             */
            initObservable: function () {
                this._super()
                    .observe([
                        'isActivePaymentTokenEnabler'
                    ]);

                return this;
            },

            /**
             * @param {Object} data
             */
            visitAdditionalData: function (data) {
                if (!this.isVaultEnabled()) {
                    return;
                }

                if (!('additional_data' in data)) {
                    data['additional_data'] = {};
                }

                data['additional_data']['is_active_payment_token_enabler'] = this.isActivePaymentTokenEnabler();
            },

            /**
             * @returns {Boolean}
             */
            isVaultEnabled: function () {
                return typeof window.checkoutConfig.vault[this.paymentCode] !== 'undefined' &&
                    window.checkoutConfig.vault[this.paymentCode]['is_enabled'] === true;
            }
        });
    }
);
