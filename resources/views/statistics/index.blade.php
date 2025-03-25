@extends('layouts.master')
@section('title', __('statistics_title'))
@section('content')
    <x-page-title title="{{ __('statistics_title') }}" pagetitle="{{ __('statistics_title') }}" settings="statistics" :stores="$storesWithPermission" :months="$monthsSelect" />

    <div class="row">
        <div class="col-12 col-xl-4 d-flex">
            <div class="card rounded-4 w-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <div class="">
                            <h2 class="mb-0" id="average_profit">{{ number_format($averageProfit, 0, ',', ' ') }} RON</h2>
                        </div>
                    </div>
                    <p class="mb-0">{{ __('statistics_average_profit_label') }}</p>
                    <div id="chart1"></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-8 d-flex">
            <div class="card rounded-4 w-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-around flex-wrap gap-4 p-4">
                        <div class="d-flex flex-column align-items-center justify-content-center gap-2">
                            <a href="javascript:;" class="pe-none mb-2 wh-48 bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-question-circle fs-4" style="-webkit-text-stroke: 0.6px"></i>
                            </a>
                            <h3 class="mb-0" id="total_queries">{{ $totalQueries }}</h3>
                            <p class="mb-0">{{ __('statistics_total_queries_label') }}</p>
                        </div>
                        <div class="vr"></div>
                        <div class="d-flex flex-column align-items-center justify-content-center gap-2">
                            <a href="javascript:;" class="pe-none mb-2 wh-48 bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-ban fs-4" style="-webkit-text-stroke: 0.8px"></i>
                            </a>
                            <h3 class="mb-0" id="blocked_order">{{ $blockedOrder }}</h3>
                            <p class="mb-0">{{ __('statistics_blocked_label') }}</p>
                        </div>
                        <div class="vr"></div>
                        <div class="d-flex flex-column align-items-center justify-content-center gap-2">
                            <a href="javascript:;" class="pe-none mb-2 wh-48 bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-bag-x fs-4" style="-webkit-text-stroke: 0.6px"></i>
                            </a>
                            <h3 class="mb-0" id="is_not_delivery">{{ $isNotReceivedCount }}</h3>
                            <p class="mb-0">{{ __('statistics_not_received_label') }}</p>
                        </div>
                        <div class="vr"></div>

                        <div class="d-flex flex-column align-items-center justify-content-center gap-2">
                            <a href="javascript:;" class="pe-none mb-2 wh-48 bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center">
                                <i class="bi bi-bag-check fs-4" style="-webkit-text-stroke: 0.6px"></i>
                            </a>
                            <h3 class="mb-0" id="is_delivery">{{ $isReceivedCount }}</h3>
                            <p class="mb-0">{{ __('statistics_received_label') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div><!--end row-->

    <div class="row">
        <div class="col-12 col-xl-5 col-xxl-4 d-flex">
            <div class="card rounded-4 w-100 shadow-none bg-transparent border-0">
                <div class="card-body p-0">
                    <div class="row g-4">
                        <div class="col-12 col-xl-6 d-flex">
                            <div class="card mb-0 rounded-4 w-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-start justify-content-between mb-3">
                                        <div class="">
                                            <h4 class="mb-0" id="totalFeedbacks">{{ $totalFeedbacks }}</h4>
                                            <p class="mb-0">{{ __('statistics_total_feedbacks_label') }}</p>
                                        </div>
                                    </div>
                                    <div class="chart-container2">
                                        <div id="chart3"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-xl-6 d-flex">
                            <div class="card mb-0 rounded-4 w-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-start justify-content-between mb-1">
                                        <div class="">
                                            <h4 class="mb-0" id="averageQueriesPerMonth">{{ $averageQueriesPerMonth }}</h4>
                                            <p class="mb-0">{{ __('statistics_average_queries_per_month_label') }}</p>
                                        </div>
                                    </div>
                                    <div class="chart-container2">
                                        <div id="chart2"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div><!--end row-->
                </div>
            </div>
        </div>
        <div class="col-12 col-xl-7 col-xxl-8 d-flex">
            <div class="card w-100 rounded-4">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div class="">
                            <h5 class="mb-0 fw-bold">{{ __('statistics_received_and_not_received_chart_title') }}</h5>
                        </div>
                    </div>
                    <div id="chart4"></div>
                </div>
            </div>
        </div>
    </div><!--end row-->

@endsection
        @section('scripts')

            <script src="{{ URL::asset('build/plugins/apexchart/apexcharts.min.js') }}"></script>
            <script src="{{ URL::asset('build/plugins/peity/jquery.peity.min.js') }}"></script>
            <script>
                $(".data-attributes span").peity("donut")
                $(function () {
                    "use strict";


                    // chart 1
                    // Get the PHP data passed to the view
                    let monthlyProfits = @json($monthlyProfits);
                    let months = Object.keys(monthlyProfits);

                    let data = Object.values(monthlyProfits);

                    var options = {
                        series: [{
                            name: "Havi profit",
                            data: data.length <= 1 ?  [0, data] : data
                        }],
                        chart: {
                            id: "chart1",
                            //width:150,
                            height: 105,
                            type: 'area',
                            toolbar: {
                                show: false
                            },
                            sparkline: {
                                enabled: false
                            },
                            zoom: {
                                enabled: false
                            }
                        },
                        dataLabels: {
                            enabled: false
                        },
                        stroke: {
                            width: 1.7,
                            curve: 'smooth'
                        },
                        fill: {
                            type: 'gradient',
                            gradient: {
                                shade: 'dark',
                                gradientToColors: ['#02c27a'],
                                shadeIntensity: 1,
                                type: 'vertical',
                                opacityFrom: 0.5,
                                opacityTo: 0.0,
                                //stops: [0, 100, 100, 100]
                            },
                        },

                        colors: ["#02c27a"],
                        tooltip: {
                            theme: "dark",
                            x: {
                                show: true
                            },
                            y: {
                                formatter: function (val) {
                                    return formatNumber(val) + " RON";
                                }
                            },
                            marker: {
                                show: false
                            }
                        },
                        yaxis: {
                            show: false,
                            labels: {
                                show: false
                            },
                            axisBorder: {
                                show: false
                            },
                            axisTicks: {
                                show: false
                            },
                        },
                        grid: {
                            show: false
                        },
                        xaxis: {
                            categories: months,
                            show: false,
                            tooltip: {
                                enabled: false
                            },
                            labels: {
                                show: false
                            },
                            axisBorder: {
                                show: false
                            },
                            axisTicks: {
                                show: false
                            }
                        }
                    };

                    var chart = new ApexCharts(document.querySelector("#chart1"), options);
                    chart.render();



                    let averageUsagePercentage = @json($averageUsagePercentage);

                    // chart 2

                    var options = {
                        series: [averageUsagePercentage],
                        chart: {
                            id: "chart2",
                            height: 180,
                            type: 'radialBar',
                            toolbar: {
                                show: false
                            }
                        },
                        plotOptions: {
                            radialBar: {
                                startAngle: -115,
                                endAngle: 115,
                                hollow: {
                                    margin: 0,
                                    size: '80%',
                                    background: 'transparent',
                                    image: undefined,
                                    imageOffsetX: 0,
                                    imageOffsetY: 0,
                                    position: 'front',
                                    dropShadow: {
                                        enabled: false,
                                        top: 3,
                                        left: 0,
                                        blur: 4,
                                        opacity: 0.24
                                    }
                                },
                                track: {
                                    background: 'rgba(0, 0, 0, 0.1)',
                                    strokeWidth: '67%',
                                    margin: 0, // margin is in pixels
                                    dropShadow: {
                                        enabled: false,
                                        top: -3,
                                        left: 0,
                                        blur: 4,
                                        opacity: 0.35
                                    }
                                },

                                dataLabels: {
                                    show: true,
                                    name: {
                                        offsetY: -10,
                                        show: false,
                                        color: '#888',
                                        fontSize: '17px'
                                    },
                                    value: {
                                        offsetY: 10,
                                        color: '#111',
                                        fontSize: '24px',
                                        show: true,
                                    }
                                }
                            }
                        },
                        fill: {
                            type: 'gradient',
                            gradient: {
                                shade: 'dark',
                                type: 'horizontal',
                                shadeIntensity: 0.5,
                                gradientToColors: ['#0866ff'],
                                inverseColors: true,
                                opacityFrom: 1,
                                opacityTo: 1,
                                stops: [0, 100]
                            }
                        },
                        colors: ["#fc185a"],
                        stroke: {
                            lineCap: 'round'
                        },
                        labels: ['Total Orders'],
                    };
                    var chart = new ApexCharts(document.querySelector("#chart2"), options);
                    chart.render();


                    let monthlyFeedbacks = @json($monthlyFeedbacks);
                    let feedbackMonths = Object.keys(monthlyFeedbacks);


                    // chart 3
                    let data1 = Object.values(monthlyFeedbacks);
                    var options = {
                        series: [{
                            name: "Havi visszajelzések",
                            data: data1
                        }],
                        chart: {
                            id: "chart3",
                            //width:150,
                            height: 120,
                            type: 'bar',
                            sparkline: {
                                enabled: !0
                            },
                            zoom: {
                                enabled: false
                            }
                        },
                        dataLabels: {
                            enabled: false
                        },
                        stroke: {
                            width: 1,
                            curve: 'smooth',
                            color: ['transparent']
                        },
                        fill: {
                            type: 'gradient',
                            gradient: {
                                shade: 'dark',
                                gradientToColors: ['#fc6718'],
                                shadeIntensity: 1,
                                type: 'vertical',
                                //opacityFrom: 0.8,
                                //opacityTo: 0.1,
                                //stops: [0, 100, 100, 100]
                            },
                        },
                        colors: ["#fc185a"],
                        plotOptions: {
                            bar: {
                                horizontal: false,
                                borderRadius: 4,
                                borderRadiusApplication: 'around',
                                borderRadiusWhenStacked: 'last',
                                columnWidth: '45%',
                            }
                        },

                        tooltip: {
                            theme: "dark",
                            fixed: {
                                enabled: false
                            },
                            x: {
                                show: true
                            },
                            y: {
                                formatter: function (val) {
                                    return Math.round(val);  // Kerekítés egész számra
                                }
                            },
                            marker: {
                                show: !1
                            }
                        },
                        xaxis: {
                            categories: feedbackMonths,
                        }
                    };

                    var chart = new ApexCharts(document.querySelector("#chart3"), options);
                    chart.render();



                    // chart 4

                    let monthlyData = @json($monthlyData);

                    let sortedMonths = Object.keys(monthlyData).sort();
                    let isDeliveredSeries = sortedMonths.map(month => monthlyData[month].is_delivered);
                    let isNotDeliveredSeries = sortedMonths.map(month => monthlyData[month].is_not_delivered);

                    let categories = sortedMonths.map(month => {
                        let date = new Date(month + "-01");
                        return date.toLocaleString('default', { month: 'short' });
                    });
                    var options = {
                        series: [{
                            name: "Átvett",
                            data: isDeliveredSeries
                        },
                            {
                                name: "Nem átvett",
                                data: isNotDeliveredSeries
                            }],
                        chart: {
                            id: "chart4",
                            //width:150,
                            foreColor: "#9ba7b2",
                            height: 235,
                            type: 'bar',
                            toolbar: {
                                show: !1,
                            },
                            sparkline: {
                                enabled: !1
                            },
                            zoom: {
                                enabled: false
                            }
                        },
                        dataLabels: {
                            enabled: false
                        },
                        stroke: {
                            width: 4,
                            curve: 'smooth',
                            colors: ['transparent']
                        },
                        fill: {
                            type: 'gradient',
                            gradient: {
                                shade: 'dark',
                                gradientToColors: ['#0d6efd', '#6f42c1'],
                                shadeIntensity: 1,
                                type: 'vertical',
                                //opacityFrom: 0.8,
                                //opacityTo: 0.1,
                                stops: [0, 100, 100, 100]
                            },
                        },
                        colors: ['#0d6efd', "#6f42c1"],
                        plotOptions: {
                            // bar: {
                            //   horizontal: !1,
                            //   columnWidth: "55%",
                            //   endingShape: "rounded"
                            // }
                            bar: {
                                horizontal: false,
                                borderRadius: 4,
                                borderRadiusApplication: 'around',
                                borderRadiusWhenStacked: 'last',
                                columnWidth: '55%',
                            }
                        },
                        grid: {
                            show: false,
                            borderColor: 'rgba(0, 0, 0, 0.15)',
                            strokeDashArray: 4,
                        },
                        tooltip: {
                            theme: "dark",
                            fixed: {
                                enabled: !0
                            },
                            x: {
                                show: !0
                            },
                            y: {
                                title: {
                                    formatter: function (e) {
                                        return ""
                                    }
                                }
                            },
                            marker: {
                                show: !1
                            }
                        },
                        xaxis: {
                            categories: categories,
                        }
                    };

                    var chart = new ApexCharts(document.querySelector("#chart4"), options);
                    chart.render();




                    document.getElementById('storeSelect').addEventListener('change', updateFilter);
                    // document.getElementById('monthSelect').addEventListener('change', updateFilter);

                    function updateFilter() {
                        const storeId = document.getElementById('storeSelect').value;
                        // const month = document.getElementById('monthSelect').value;

                        // Mutassuk a loading állapotot
                        document.body.style.cursor = 'wait';

                        // AJAX kérés küldése a kiválasztott szűrő értékekkel
                        fetch('/statistics/update-charts', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            },
                            body: JSON.stringify({
                                store_id: storeId,
                                // month: month
                            }),
                        })
                        .then(response => response.json())
                        .then(data => {
                            updateCharts(data);
                        })
                        .catch(error => {
                            console.error('Error updating charts:', error);
                        })
                        .finally(() => {
                            document.body.style.cursor = 'default';
                        });
                    }


                    function updateCharts(data) {
                        console.log(data);
                        document.getElementById('average_profit').innerHTML = formatNumber(data.averageProfit) + " RON";

                        document.getElementById('total_queries').innerHTML = data.totalQueries
                        document.getElementById('blocked_order').innerHTML = data.blockedOrder
                        document.getElementById('is_not_delivery').innerHTML = data.isNotReceivedCount
                        document.getElementById('is_delivery').innerHTML = data.isReceivedCount
                        document.getElementById('totalFeedbacks').innerHTML = data.totalFeedbacks
                        document.getElementById('averageQueriesPerMonth').innerHTML = data.averageQueriesPerMonth
                        // Chart 1 frissítése
                        let chart1 = ApexCharts.getChartByID("chart1");

                        // Chart 2 frissítése
                        let chart2 = ApexCharts.getChartByID("chart2");

                        let chart3 = ApexCharts.getChartByID("chart3");

                        // Chart 4 frissítése
                        let chart4 = ApexCharts.getChartByID("chart4");


                        // Handle monthlyProfits
                        if (data.monthlyProfits && typeof data.monthlyProfits === 'object') {
                            const monthlyProfitsArray = Object.values(data.monthlyProfits);
                            const updatedMonths = Object.keys(data.monthlyProfits);

                            chart1.updateSeries([
                                {
                                    name: "Havi profit",
                                    data: monthlyProfitsArray.length > 1 ? monthlyProfitsArray : [0, ...monthlyProfitsArray]
                                }
                            ]);

                            chart1.updateOptions({
                                xaxis: { categories: updatedMonths }
                            });
                        } else {
                            console.error("monthlyProfits is not a valid object:", data.monthlyProfits);
                        }
                        let percentage = 0;

                        // Handle usagePercentage
                        chart2.updateSeries([data.averageUsagePercentage]);

                        const feedbackData = Object.values(data.monthlyFeedbacks);
                        const feedbackMonths = Object.keys(data.monthlyFeedbacks);

                        chart3.updateSeries([
                            {
                                data: feedbackData
                            }
                        ])

                        chart3.updateOptions({
                            xaxis: { categories: feedbackMonths }
                        });

                        let monthlyData = data.monthlyData;

                        let sortedMonths = Object.keys(monthlyData).sort();
                        let isDeliveredSeries = sortedMonths.map(month => monthlyData[month].is_delivered);
                        let isNotDeliveredSeries = sortedMonths.map(month => monthlyData[month].is_not_delivered);


                        chart4.updateSeries([
                            {
                                name: "Átvett",
                                data: isDeliveredSeries
                            },
                            {
                                name: "Nem átvett",
                                data: isNotDeliveredSeries
                            }
                        ]);

                        const updatedCategories = sortedMonths.map(month => {
                            let date = new Date(month + "-01");
                            return date.toLocaleString('default', { month: 'short' });
                        });

                        chart4.updateOptions({
                            xaxis: { categories: updatedCategories }
                        });

                    }

                    function formatNumber(value) {
                        return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
                    }
                });


            </script>
@endsection
