/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */

/**
 * @api
 */
define([
    'uiComponent'
], function (Component) {
    'use strict';

    return Component.extend({
        defaults: {
            opened: false,
            collapsible: true
        },

        /**
         * Initializes observable properties.
         *
         * @returns {Collapsible} Chainable.
         */
        initObservable: function () {
            this._super()
                .observe('opened');

            return this;
        },

        /**
         * Toggles value of the 'opened' property.
         *
         * @returns {Collapsible} Chainable.
         */
        toggleOpened: function () {
            this.opened() ?
                this.close() :
                this.open();

            return this;
        },

        /**
         * Sets 'opened' flag to false.
         *
         * @returns {Collapsible} Chainable.
         */
        close: function () {
            if (this.collapsible) {
                this.opened(false);
            }

            return this;
        },

        /**
         * Sets 'opened' flag to true.
         *
         * @returns {Collapsible} Chainable.
         */
        open: function () {
            if (this.collapsible) {
                this.opened(true);
            }

            return this;
        }
    });
});
