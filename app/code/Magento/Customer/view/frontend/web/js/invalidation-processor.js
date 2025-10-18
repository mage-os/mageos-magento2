/**
 * Copyright 2017 Adobe
 * All Rights Reserved.
 */
define([
    'underscore',
    'uiElement',
    'Magento_Customer/js/customer-data'
], function (_, Element, customerData) {
    'use strict';

    return Element.extend({
        /**
         * Initialize object
         */
        initialize: function () {
            this._super();
            this.process(customerData);
        },

        /**
         * Process all rules in loop, each rule can invalidate some sections in customer data
         *
         * @param {Object} customerDataObject
         */
        process: function (customerDataObject) {
            _.each(this.invalidationRules, function (rule, ruleName) {
                _.each(rule, function (ruleArgs, rulePath) {
                    require([rulePath], function (Rule) {
                        var currentRule = new Rule(ruleArgs);

                        if (!_.isFunction(currentRule.process)) {
                            throw new Error('Rule ' + ruleName + ' should implement invalidationProcessor interface');
                        }
                        currentRule.process(customerDataObject);
                    });
                });
            });
        }
    });
});
