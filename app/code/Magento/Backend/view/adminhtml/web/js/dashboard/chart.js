/**
 * Copyright 2020 Adobe
 * All Rights Reserved.
 */

/*global FORM_KEY*/
define([
    'jquery',
    'chartJs',
    'moment',
    'Magento_Backend/js/dashboard/period-storage',
    'mage/translate',
    'jquery-ui-modules/widget',
    'chartjs/chartjs-adapter-moment',
    'chartjs/es6-shim.min'
], function ($, Chart, moment, periodStorage, $t) {
    'use strict';

    var TOOLTIP_FORMATS = {
        hour: 'MMM D, h A',
        day: 'MMM D, YYYY',
        month: 'MMM YYYY'
    };

    /**
     * Draws a dashed horizontal line at the average of the revenue series.
     */
    var averageLinePlugin = {
        id: 'averageLine',

        /**
         * @param {Chart} chart
         * @param {Object} args
         * @param {Object} options
         */
        afterDatasetsDraw: function (chart, args, options) {
            var meta = chart.getDatasetMeta(1),
                scale = chart.scales.yAmounts,
                ctx = chart.ctx,
                y;

            if (!options.value || !scale || meta.hidden || !chart.isDatasetVisible(1)) {
                return;
            }
            y = scale.getPixelForValue(options.value);
            ctx.save();
            ctx.setLineDash([6, 4]);
            ctx.strokeStyle = options.color;
            ctx.lineWidth = 1;
            ctx.beginPath();
            ctx.moveTo(chart.chartArea.left, y);
            ctx.lineTo(chart.chartArea.right, y);
            ctx.stroke();
            ctx.setLineDash([]);
            ctx.fillStyle = options.color;
            ctx.font = '11px sans-serif';
            ctx.textAlign = 'right';
            ctx.textBaseline = 'bottom';
            ctx.fillText(options.label, chart.chartArea.right - 4, y - 3);
            ctx.restore();
        }
    };

    $.widget('mage.dashboardChart', {
        options: {
            updateUrl: '',
            responsive: true,
            maintainAspectRatio: false,
            periodSelect: null,
            periodUnits: [],
            currency: 'USD',
            locale: 'en-US',
            colors: {
                orders: '#f1d4b3',
                ordersBorder: '#eb5202',
                amounts: '#303030',
                average: '#8c8c8c'
            }
        },
        chart: null,
        period: null,
        unit: 'hour',

        /**
         * @private
         */
        _create: function () {
            if (this.options.periodSelect) {
                periodStorage.apply(this.options.periodSelect);
            }

            this.createChart();

            if (this.options.periodSelect) {
                $(document).on('change', this.options.periodSelect, this.refreshChartData.bind(this));

                this.period = $(this.options.periodSelect).val();
            }
        },

        /**
         * @public
         */
        createChart: function () {
            this.chart = new Chart(this.element, this.getChartSettings());
            this.refreshChartData();
        },

        /**
         * @public
         */
        refreshChartData: function () {
            var data = {};

            if (this.options.periodSelect) {
                this.period = data.period = $(this.options.periodSelect).val();
            }

            this._request(data).done(this.updateChart.bind(this));
        },

        /**
         * @param {Object} data
         * @returns {jqXHR}
         * @private
         */
        _request: function (data) {
            return $.ajax({
                url: this.options.updateUrl,
                showLoader: true,
                data: $.extend({form_key: FORM_KEY}, data),
                dataType: 'json',
                type: 'POST'
            });
        },

        /**
         * Apply a response of the form {period, series: {orders: {label, data}, amounts: {label, data}}, currency, locale}
         *
         * @public
         * @param {Object} response
         */
        updateChart: function (response) {
            var orders = response.series.orders,
                amounts = response.series.amounts,
                hasData = orders.data.length > 0;

            if (response.period) {
                this.period = response.period;
            }
            if (response.currency) {
                this.options.currency = response.currency;
            }
            if (response.locale) {
                this.options.locale = response.locale;
            }
            this.unit = this.options.periodUnits[this.period] ? this.options.periodUnits[this.period] : 'hour';

            $(this.element).parent()
                .toggle(hasData)
                .siblings('.dashboard-diagram-nodata')
                .toggle(!hasData);

            this.chart.options.scales.xAxis.time.unit = this.unit;
            this.chart.options.scales.xAxis.time.tooltipFormat = TOOLTIP_FORMATS[this.unit] || TOOLTIP_FORMATS.day;
            this.chart.data.datasets[0].data = orders.data;
            this.chart.data.datasets[0].label = orders.label;
            this.chart.data.datasets[1].data = amounts.data;
            this.chart.data.datasets[1].label = amounts.label;
            this.chart.options.plugins.averageLine.value = this.getAverage(amounts.data);
            this.chart.options.plugins.averageLine.label = $t('Avg %1').replace(
                '%1',
                this.formatCurrency(this.chart.options.plugins.averageLine.value)
            );
            this.chart.update();
        },

        /**
         * Mean of the y values, or 0 when there is nothing to average.
         *
         * @param {Array} points
         * @returns {Number}
         */
        getAverage: function (points) {
            var sum = 0;

            if (!points.length) {
                return 0;
            }
            points.forEach(function (point) {
                sum += Number(point.y) || 0;
            });

            return sum / points.length;
        },

        /**
         * @param {Number} value
         * @param {Boolean} [compact]
         * @returns {String}
         */
        formatCurrency: function (value, compact) {
            try {
                return new Intl.NumberFormat(this.options.locale, $.extend({
                    style: 'currency',
                    currency: this.options.currency
                }, compact ? {maximumFractionDigits: 0} : {})).format(value);
            } catch (e) {
                return String(Math.round(value * 100) / 100) + ' ' + this.options.currency;
            }
        },

        /**
         * @param {Number} value
         * @returns {String}
         */
        formatCount: function (value) {
            try {
                return new Intl.NumberFormat(this.options.locale, {maximumFractionDigits: 0}).format(value);
            } catch (e) {
                return String(value);
            }
        },

        /**
         * @returns {Object}
         */
        getChartSettings: function () {
            var self = this;

            return {
                type: 'bar',
                data: {
                    datasets: [{
                        type: 'bar',
                        yAxisID: 'yOrders',
                        xAxisID: 'xAxis',
                        order: 2,
                        data: [],
                        backgroundColor: this.options.colors.orders,
                        borderColor: this.options.colors.ordersBorder,
                        borderWidth: 1
                    }, {
                        type: 'line',
                        yAxisID: 'yAmounts',
                        xAxisID: 'xAxis',
                        order: 1,
                        data: [],
                        borderColor: this.options.colors.amounts,
                        backgroundColor: this.options.colors.amounts,
                        borderWidth: 2,
                        pointRadius: 3,
                        pointHoverRadius: 5,
                        cubicInterpolationMode: 'monotone',
                        spanGaps: true
                    }]
                },
                plugins: [averageLinePlugin],
                options: {
                    responsive: this.options.responsive,
                    maintainAspectRatio: this.options.maintainAspectRatio,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                /**
                                 * Keep legend entries in dataset order (Orders, then Revenue).
                                 *
                                 * @param {Object} a
                                 * @param {Object} b
                                 * @returns {Number}
                                 */
                                sort: function (a, b) {
                                    return a.datasetIndex - b.datasetIndex;
                                }
                            }
                        },
                        averageLine: {
                            value: 0,
                            label: '',
                            color: this.options.colors.average
                        },
                        tooltip: {
                            callbacks: {
                                /**
                                 * @param {Object} item
                                 * @returns {String}
                                 */
                                label: function (item) {
                                    var value = item.parsed.y;

                                    return item.dataset.label + ': ' + (item.datasetIndex === 1 ?
                                        self.formatCurrency(value) : self.formatCount(value));
                                }
                            }
                        }
                    },
                    scales: {
                        xAxis: {
                            offset: true,
                            type: 'time',
                            time: {
                                unit: 'hour',
                                tooltipFormat: TOOLTIP_FORMATS.hour
                            },
                            ticks: {
                                source: 'data'
                            }
                        },
                        yOrders: {
                            position: 'left',
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: $t('Orders')
                            },
                            ticks: {
                                precision: 0,

                                /**
                                 * @param {Number} value
                                 * @returns {String}
                                 */
                                callback: function (value) {
                                    return self.formatCount(value);
                                }
                            }
                        },
                        yAmounts: {
                            position: 'right',
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: $t('Revenue')
                            },
                            grid: {
                                drawOnChartArea: false
                            },
                            ticks: {
                                /**
                                 * @param {Number} value
                                 * @returns {String}
                                 */
                                callback: function (value) {
                                    return self.formatCurrency(value, true);
                                }
                            }
                        }
                    }
                }
            };
        }
    });

    return $.mage.dashboardChart;
});
