/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'wysiwygAdapter',
    'underscore',
    'tinymce'
], function (wysiwygAdapter, _, tinyMCE) {
    'use strict';

    var obj, originalVarienEvents;

    beforeEach(function () {

        /**
         * Dummy constructor to use for instantiation
         * @constructor
         */
        var Constr = function () {};

        Constr.prototype = wysiwygAdapter;

        obj = new Constr();
        
        // Store original varienEvents if it exists
        originalVarienEvents = window.varienEvents;
        
        // Ensure varienEvents is available for CI environments
        if (typeof window.varienEvents === 'undefined') {
            window.varienEvents = function() {
                this.arrEvents = {};
                this.attachEvent = function(eventName, callback) {
                    if (!this.arrEvents[eventName]) {
                        this.arrEvents[eventName] = [];
                    }
                    this.arrEvents[eventName].push(callback);
                };
                this.fireEvent = function(eventName, data) {
                    if (this.arrEvents[eventName]) {
                        this.arrEvents[eventName].forEach(function(callback) {
                            if (typeof callback === 'function') {
                                callback(data);
                            }
                        });
                    }
                };
            };
        }
        
        obj.eventBus = new window.varienEvents();
        obj.initialize('id', {
            'store_id': 0,
            'tinymce': {
                'content_css': ''
            },
            'files_browser_window_url': 'url'
        });
        obj.setup();
    });

    afterEach(function () {
        // Restore original varienEvents or remove mock
        if (originalVarienEvents) {
            window.varienEvents = originalVarienEvents;
        } else if (window.varienEvents) {
            delete window.varienEvents;
        }
    });

    describe('"openFileBrowser" method', function () {
        it('Opens file browser to given instance', function () {
            expect(_.size(obj.eventBus.arrEvents['open_browser_callback'])).toBe(1);
        });
    });

    describe('"triggerSave" method', function () {
        it('Check method call.', function () {
            spyOn(tinyMCE, 'triggerSave');
            obj.triggerSave();
            expect(tinyMCE.triggerSave).toHaveBeenCalled();
        });
    });
});
