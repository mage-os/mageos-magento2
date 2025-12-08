/**
 * Copyright 2014 Adobe
 * All Rights Reserved.
 */

define([
    'jquery',
    'mage/adminhtml/form'
], function ($) {
    'use strict';

    describe('mage/adminhtml/form', function () {
        var id = 'edit_form',
            elementId = '#' + id;

        beforeEach(function () {
            var element = $('<form id="' + id + '" action="action/url" method="GET" target="_self" ></form>');

            element.appendTo('body');
        });
        afterEach(function () {
            $(elementId).remove();
        });

        it('should not enable inputs that have the disabled CSS class when dependencies are satisfied', function () {
            // Container that will be traversed with levels_up = 1
            var container = document.createElement('div');
            document.body.appendChild(container);

            // Target element (idTo) inside the container
            var target = document.createElement('input');
            target.id = 'test_input_dependent';
            container.appendChild(target);

            // Sibling input that carries CSS class "disabled" and is disabled initially
            var cssDisabled = document.createElement('input');
            cssDisabled.id = 'css_disabled_input';
            cssDisabled.className = 'disabled';
            cssDisabled.disabled = true;
            container.appendChild(cssDisabled);

            // Another sibling input which is disabled initially but should be enabled after dependencies are met
            var normalInput = document.createElement('input');
            normalInput.id = 'normal_input';
            normalInput.disabled = true;
            container.appendChild(normalInput);

            // Dependency source element that satisfies the map
            var source = document.createElement('input');
            source.id = 'dep_source';
            source.value = '1';
            document.body.appendChild(source);

            // Initialize controller with dependency that evaluates to true (shouldShowUp = true)
            /* eslint-disable no-new */
            new window.FormElementDependenceController({
                'test_input_dependent': {
                    'dep_source': { values: ['1'] }
                }
            }, {
                levels_up: 1
            });
            /* eslint-enable no-new */

            // The element with CSS class "disabled" must remain disabled
            expect(cssDisabled.disabled).toBe(true);
            // The normal input should be enabled because dependencies are satisfied
            expect(normalInput.disabled).toBe(false);

            // Cleanup
            document.body.removeChild(container);
            document.body.removeChild(source);
        });
    });
});
