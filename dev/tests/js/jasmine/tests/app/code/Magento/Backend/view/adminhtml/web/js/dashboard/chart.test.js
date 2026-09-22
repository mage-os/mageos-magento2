/**
 * Copyright 2026 Adobe
 * All Rights Reserved.
 */
define([
    'jquery',
    'Magento_Backend/js/dashboard/chart'
], function ($) {
    'use strict';

    describe('Magento_Backend/js/dashboard/chart', function () {
        let chartContainer, canvas;
        const series = {
            'today': {
                orders: {label: 'Orders', data: [{'x': '2026-01-10 06:00', 'y': 2}]},
                amounts: {label: 'Revenue', data: [{'x': '2026-01-10 06:00', 'y': 80}]}
            },
            '1m': {
                orders: {label: 'Orders', data: [{'x': '2026-01-09', 'y': 1}, {'x': '2026-01-10', 'y': 3}]},
                amounts: {label: 'Revenue', data: [{'x': '2026-01-09', 'y': 120}, {'x': '2026-01-10', 'y': 80}]}
            },
            '1y': {
                orders: {label: 'Orders', data: [{'x': '2026-01', 'y': 5}]},
                amounts: {label: 'Revenue', data: [{'x': '2026-01', 'y': 200}]}
            }
        };

        /**
         * @param {String} period
         * @returns {Object}
         */
        function response(period) {
            return {period: period, series: series[period], currency: 'USD', locale: 'en-US'};
        }

        /**
         * Creates a new instance of the dashboard chart widget with a mocked _request method.
         *
         * @param {jQuery} element
         * @param {Object} [options={}]
         * @param {Object} [mocks={}]
         * @return {*}
         */
        function getWidgetInstance(element, options, mocks) {
            $.widget('test.dashboardChartTest', $.mage.dashboardChart, $.extend({
                _request: function (data) {
                    return $.Deferred().resolve(response(data.period || 'today'));
                }
            }, mocks || {}));

            return element.dashboardChartTest($.extend({
                updateUrl: '/test/url',
                type: 'bar',
                periodSelect: '#' + element.parent().parent().attr('id') + ' select',
                periodUnits: {
                    'today': 'hour',
                    '1m': 'day',
                    '1y': 'month'
                }
            }, options || {})).data('test-dashboardChartTest');
        }

        beforeEach(function () {
            chartContainer = $('<div id="' + Math.random().toString().substr(2) + '"></div>')
                .append(
                    '<select>' +
                        '<option value="today" selected>Today</option>' +
                        '<option value="1m">Current Month</option>' +
                        '<option value="1y">YTD</option>' +
                    '</select>'
                )
                .append('<div><canvas></canvas></div><div class="dashboard-diagram-nodata">No Data</div>')
                .appendTo($('body'));
            canvas = chartContainer.find('canvas');
        });

        afterEach(function () {
            chartContainer.remove();
        });

        it('should create dashboardChart widget', function () {
            expect($.fn.dashboardChart).toBeDefined();
        });

        it('should hide the chart and show "No Data" text if data is not available', () => {
            const period = 'today',
                chartWidget = getWidgetInstance(canvas, {}, {
                    _request: function () {
                        return $.Deferred().resolve({
                            period: 'today',
                            series: {orders: {label: 'Orders', data: []}, amounts: {label: 'Revenue', data: []}}
                        });
                    }
                });

            expect(chartWidget.period).toBe(period);
            expect(chartWidget.chart).toBeDefined();
            expect(chartWidget.chart.data.datasets[0].label).toBe('Revenue');
            expect(chartWidget.chart.data.datasets[0].data).toEqual([]);
            expect(chartWidget.chart.data.datasets[1].data).toEqual([]);
            expect(canvas.parent().is(':visible')).toBeFalse();
            expect(canvas.parent().next('.dashboard-diagram-nodata').is(':visible')).toBeTrue();
        });
        it('should create a chart with default period', () => {
            const period = 'today',
                chartWidget = getWidgetInstance(canvas);

            expect(chartWidget.period).toBe(period);
            expect(chartWidget.chart).toBeDefined();
            expect(chartWidget.chart.data.datasets[0].label).toBe('Revenue');
            expect(chartWidget.chart.data.datasets[0].data).toEqual(series[period].amounts.data);
            expect(chartWidget.chart.data.datasets[1].label).toBe('Orders');
            expect(chartWidget.chart.data.datasets[1].data).toEqual(series[period].orders.data);
            expect(chartWidget.chart.options.plugins.averageLine.value).toBe(80);
            expect(canvas.parent().is(':visible')).toBeTrue();
            expect(canvas.parent().next('.dashboard-diagram-nodata').is(':visible')).toBeFalse();
        });
        it('should format currency and counts for the active locale', () => {
            const chartWidget = getWidgetInstance(canvas);

            expect(chartWidget.formatCurrency(1234.5)).toBe('$1,234.50');
            expect(chartWidget.formatCurrency(1234.5, true)).toBe('$1,235');
            expect(chartWidget.formatCount(1234)).toBe('1,234');
        });
        it('should update the chart when period changes', () => {
            const period = '1m',
                chartWidget = getWidgetInstance(canvas);

            expect(chartWidget.period).toBe('today');
            expect(chartWidget.chart).toBeDefined();

            canvas.parent().parent().find('select').val(period).trigger('change');

            expect(chartWidget.period).toBe(period);
            expect(chartWidget.unit).toBe('day');
            expect(chartWidget.chart.data.datasets[0].data).toEqual(series[period].amounts.data);
            expect(chartWidget.chart.data.datasets[1].data).toEqual(series[period].orders.data);
            expect(chartWidget.chart.options.plugins.averageLine.value).toBe(100);
            expect(canvas.parent().is(':visible')).toBeTrue();
            expect(canvas.parent().next('.dashboard-diagram-nodata').is(':visible')).toBeFalse();
        });
    });
});
