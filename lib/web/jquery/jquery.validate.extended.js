/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
define(['jquery', 'jquery/jquery.validate'], function ($) {
    'use strict';

    $.validator.prototype.errorsFor = function (element) {
        let name = this.escapeCssMeta(this.idOrName(element)),
            describer = $(element).attr('aria-describedby'),
            selector = 'label[for=\'' + name + '\'], label[for=\'' + name + '\'] *';

        // 'aria-describedby' should directly reference the error element
        if (describer) {
            selector = selector + ', #' + this.escapeCssMeta(describer)
                .replace(/\s+/g, ', #');
            selector += ':visible';
        }

        return this
            .errors()
            .filter(selector);
    };

    return $;
});
