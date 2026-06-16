$('.select2').select2();

// -- chart init
const chartBar = echarts.init(document.getElementById("echartBar"));
const chartPie = echarts.init(document.getElementById("echartPie"));
const chartLine1 = echarts.init(document.getElementById("echart1"));
const chartLine2 = echarts.init(document.getElementById("echart2"));

$(document).ready(function () {
    $('#filter-tahun').trigger('change');
});

$(window).on("resize", function () {
    setTimeout(function () {
        chartBar.resize();
        chartPie.resize();
        chartLine1.resize();
        chartLine2.resize();
    }, 500);
});

$('#filter-tahun').change(function () {
    getDataChart();
});

// -- get data
function getDataChart() {
    $.httpRequest({
        url: baseUrl('/dashboard/data'),
        method: "POST",
        data: {
            tahun: $('#filter-tahun').val(),
        },
        contentType: "application/x-www-form-urlencoded; charset=UTF-8",
        processData: true,
        response: (res) => {
            // -- parse
            const cardData = res.card_data;
            const chartBar = res.chart_bar;
            const chartPie = res.chart_pie;
            const chartLine1 = res.chart_line_1;
            const chartLine2 = res.chart_line_2;

            // -- data card
            $('#pemohon-count').text(cardData.pemohon);
            $('#transaksi-total').text(cardData.transaksi_total);
            $('#transaksi-pending').text(cardData.transaksi_pending);
            $('#transaksi-selesai').text(cardData.transaksi_selesai);

            chartBarSetter(chartBar.label, chartBar.data);
            chartPieSetter(chartPie);
            chartLine1Setter(chartLine1.value);
            chartLine2Setter(chartLine2.value);
        }
    });
}

// -- chart bar
function chartBarSetter(labels, data) {
    chartBar.setOption({
        legend: {
            borderRadius: 0,
            orient: "horizontal",
            x: "right",
            data: ["Transaksi Selesai", "Transaksi Pending"],
        },
        grid: {
            left: "8px",
            right: "8px",
            bottom: "0",
            containLabel: true,
        },
        tooltip: {
            show: true,
            backgroundColor: "rgba(0, 0, 0, .8)",
        },
        xAxis: [
            {
                type: "category",
                data: labels,
                axisTick: {
                    alignWithLabel: true,
                },
                splitLine: {
                    show: false,
                },
                axisLine: {
                    show: true,
                },
            },
        ],
        yAxis: [
            {
                type: "value",
                min: 0,
                max: 10,
                interval: 1,
                axisLine: {
                    show: false,
                },
                splitLine: {
                    show: true,
                    interval: "auto",
                },
            },
        ],
        series: [
            {
                name: "Transaksi Pending",
                data: data.pending,
                label: {
                    show: false,
                    color: "#0168c1",
                },
                type: "bar",
                barGap: 0,
                color: "#bcbbdd",
                smooth: true,
                itemStyle: {
                    emphasis: {
                        shadowBlur: 10,
                        shadowOffsetX: 0,
                        shadowOffsetY: -2,
                        shadowColor: "rgba(0, 0, 0, 0.3)",
                    },
                },
            },
            {
                name: "Transaksi Selesai",
                data: data.selesai,
                label: {
                    show: false,
                    color: "#639",
                },
                type: "bar",
                color: "#7569b3",
                smooth: true,
                itemStyle: {
                    emphasis: {
                        shadowBlur: 10,
                        shadowOffsetX: 0,
                        shadowOffsetY: -2,
                        shadowColor: "rgba(0, 0, 0, 0.3)",
                    },
                },
            },
        ],
    });
}

// -- chart pie
function chartPieSetter(data) {
    chartPie.setOption({
        color: [
            "#8e44ad",
            "#2ecc71",
        ],
        tooltip: {
            show: true,
            backgroundColor: "rgba(0, 0, 0, .8)",
        },
        series: [
            {
                name: "Data",
                type: "pie",
                radius: ["40%", "70%"],
                center: ["50%", "50%"],
                label: {
                    show: true,
                    formatter: '{b}\n {d}%'
                },
                data: data,
                itemStyle: {
                    emphasis: {
                        shadowBlur: 10,
                        shadowOffsetX: 0,
                        shadowColor: "rgba(0, 0, 0, 0.5)",
                    },
                },
            },
        ],
    });
}

// -- chart line 1
function chartLine1Setter(data) {
    chartLine1.setOption(
        _objectSpread(
            {},
            echartOptions.lineFullWidth,
            {},
            {
                series: [
                    _objectSpread(
                        {
                            data: data,
                        },
                        echartOptions.smoothLine,
                        {
                            markArea: {
                                label: {
                                    show: true,
                                },
                            },
                            areaStyle: {
                                color: "rgba(102, 51, 153, .2)",
                                origin: "start",
                            },
                            lineStyle: {
                                color: "#663399",
                            },
                            itemStyle: {
                                color: "#663399",
                            },
                        }
                    ),
                ],
            }
        )
    );
}

// -- chart line 2
function chartLine2Setter(data) {
    chartLine2.setOption(
        _objectSpread(
            {},
            echartOptions.lineFullWidth,
            {},
            {
                series: [
                    _objectSpread(
                        {
                            data: data,
                        },
                        echartOptions.smoothLine,
                        {
                            markArea: {
                                label: {
                                    show: true,
                                },
                            },
                            areaStyle: {
                                color: "rgba(255, 193, 7, 0.2)",
                                origin: "start",
                            },
                            lineStyle: {
                                color: "#FFC107",
                            },
                            itemStyle: {
                                color: "#FFC107",
                            },
                        }
                    ),
                ],
            }
        )
    );
}