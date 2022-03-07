"use strict";

// Shared Colors Definition
const primary = '#055cc0';
const success = '#1f8c53';
const info = '#8950FC';
const warning = '#FFA800';
const danger = '#fc4242';

// Class definition
function generateBubbleData(baseval, count, yrange) {
    var i = 0;
    var series = [];
    while (i < count) {
        var x = Math.floor(Math.random() * (750 - 1 + 1)) + 1;;
        var y = Math.floor(Math.random() * (yrange.max - yrange.min + 1)) + yrange.min;
        var z = Math.floor(Math.random() * (75 - 15 + 1)) + 15;

        series.push([x, y, z]);
        baseval += 86400000;
        i++;
    }
    return series;
}

function generateData(count, yrange) {
    var i = 0;
    var series = [];
    while (i < count) {
        var x = 'w' + (i + 1).toString();
        var y = Math.floor(Math.random() * (yrange.max - yrange.min + 1)) + yrange.min;

        series.push({
            x: x,
            y: y
        });
        i++;
    }
    return series;
}

var KTApexChartsDemo = function () {
    // Private functions
    var _demo12 = function () {
        const apexChart = "#chart_12";
        var options = {
            series: [54, 43, 3],
            chart: {
                width: 250,
                type: 'pie',
            },
            labels: ['Player', 'Banker', 'Tie'],
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 100
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
			}],
            colors: [primary, success, warning, danger, info]
        };

        var chart = new ApexCharts(document.querySelector(apexChart), options);
        chart.render();
    }

    return {
        // public functions
        init: function () {
            _demo12();
        }
    };
}();

jQuery(document).ready(function () {
    KTApexChartsDemo.init();
});
