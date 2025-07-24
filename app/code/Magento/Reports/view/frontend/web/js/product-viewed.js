/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */

define([
    'jquery'
], function ($) {
    'use strict';

    return function (data) {
        $.ajax({
            url: 'reports/report_product/view',
            type: 'POST',
            data: {'product_id': data.product_id },
            dataType: 'json'
        });
    };
});
