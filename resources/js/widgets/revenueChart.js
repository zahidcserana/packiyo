window.RevenueChart = function () {
    $(document).ready(function() {
        let chart = $('#revenue-profit-chart')

        if (chart.length > 0) {
            $.ajax({
                url: "/dashboard/orders_revenue",
                context: document.body,
                data: {
                    startDate: $(document).find('input[name="dashboard_filter_date_start"]').val(),
                    endDate: $(document).find('input[name="dashboard_filter_date_end"]').val()
                }
            }).done(function(data) {
                function initRevenueProfitChart(chart) {
                    let labels = [];
                    let revenue = [];
                    let orders = [];

                    let newArrayDataOfOjbect = Object.values(data.data)

                    newArrayDataOfOjbect.map(function (item) {
                        labels.push(item.date)
                        revenue.push(item.revenue)
                        orders.push(item.orders)
                    });

                    var revenueProfitChart = new Chart(chart, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Revenue',
                                data: revenue,
                                fill: 'start',
                                borderColor: '#ECE9F1',
                                borderWidth: 3,
                                yAxisID: 'y-axis-1',
                                lineTension: 0.5,
                                pointBackgroundColor: '#fff'
                            },{
                                label: 'Orders',
                                data: orders,
                                fill: 'start',
                                borderColor: '#f39200',
                                borderWidth: 3,
                                yAxisID: 'y-axis-2',
                                lineTension: 0.5,
                                pointBackgroundColor: '#fff'
                            }]
                        },
                        options: {
                            elements: {
                                point:{
                                    radius: 4
                                }
                            },
                            hover: {
                                intersect: false
                            },
                            responsive: true,
                            hoverMode: 'index',
                            stacked: false,
                            scales: {
                                xAxes: [{
                                    stacked: true,
                                    display: true,
                                    scaleLabel: {
                                        display: true,
                                        labelString: 'Months'
                                    }
                                }],
                                yAxes: [{
                                    type: 'linear',
                                    display: true,
                                    position: 'left',
                                    id: 'y-axis-1',
                                    ticks: {
                                        callback: function(value, index, values) {
                                            if (data.currency) {
                                                return value + ' ' + data.currency;
                                            }

                                            return value;
                                        }
                                    }
                                }, {
                                    type: 'linear',
                                    display: true,
                                    position: 'right',
                                    id: 'y-axis-2',
                                    gridLines: {
                                        drawOnChartArea: false,
                                    },
                                }]
                            },
                            tooltips: {
                                callbacks: {
                                    bodyFontFamily: "Montserrat, sans-serif",
                                    label: function(tooltipItem, chartData) {
                                        let label = chartData.datasets[tooltipItem.datasetIndex].label || '';

                                        if (label) {
                                            label += ': ';
                                        }

                                        label += tooltipItem.yLabel;

                                        if (chartData.datasets[tooltipItem.datasetIndex].label == 'Revenue') {
                                            if (data.currency) {
                                                label += ' ' + data.currency;
                                            }
                                        }

                                        return label;
                                    }
                                }
                            }
                        }
                    });

                    chart.data('chart', revenueProfitChart);
                }

                initRevenueProfitChart(chart);
            });

        }

    });
};
