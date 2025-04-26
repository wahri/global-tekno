@extends('layouts.app')

@push('css')
    <link href="{{ asset('assets/plugins/highcharts/css/highcharts.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/vectormap/jquery-jvectormap-2.0.2.css') }}" rel="stylesheet" />
@endpush

@section('content')
    <div class="page-content">

        <div class="row row-cols-1 row-cols-lg-2 row-cols-xl-4">
            <div class="col">
                <div class="card radius-10 overflow-hidden">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="mb-0">Jumlah Pesanan Bulan Ini</p>
                                <h5 class="mb-0">{{ $totalOrders }}</h5>
                            </div>
                            <div class="ms-auto"> <i class='bx bx-cart font-30'></i>
                            </div>
                        </div>
                    </div>
                    <div class="" id="chartTotalOrder"></div>
                </div>
            </div>
            <div class="col">
                <div class="card radius-10 overflow-hidden">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="mb-0">Pemasukan Bulan Ini</p>
                                <h5 class="mb-0">Rp. {{ number_format($totalIncome) }}</h5>
                            </div>
                            <div class="ms-auto"> <i class='bx bx-wallet font-30'></i>
                            </div>
                        </div>
                    </div>
                    <div class="" id="chartTotalIncome"></div>
                </div>
            </div>
            <div class="col">
                <div class="card radius-10 overflow-hidden">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="mb-0">Pengeluaran Bulan Ini</p>
                                <h5 class="mb-0">Rp. {{ number_format($totalExpenses) }}</h5>
                            </div>
                            <div class="ms-auto"> <i class='bx bx-group font-30'></i>
                            </div>
                        </div>
                    </div>
                    <div class="" id="chartTotalExpenses"></div>
                </div>
            </div>
            <div class="col">
                <div class="card radius-10 overflow-hidden">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="mb-0">Total Produk</p>
                                <h5 class="mb-0">{{ number_format($totalProducts) }}</h5>
                            </div>
                            <div class="ms-auto"> <i class='bx bx-chat font-30'></i>
                            </div>
                        </div>
                    </div>
                    <div class="" id="chartTotalProducts"></div>
                </div>
            </div>
        </div><!--end row-->

        <div class="row">
            <div class="col-12">
                <hr />
                <div class="card">
                    <div class="card-body">
                        <div id="summaryChart"></div>
                    </div>
                </div>
            </div>
        </div>


    </div>
@endsection

@push('js')
    <script src="{{ asset('assets/plugins/vectormap/jquery-jvectormap-2.0.2.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/vectormap/jquery-jvectormap-world-mill-en.js') }}"></script>
    <script src="{{ asset('assets/plugins/highcharts/js/highcharts.js') }}"></script>
    <script src="{{ asset('assets/plugins/highcharts/js/exporting.js') }}"></script>
    <script src="{{ asset('assets/plugins/highcharts/js/variable-pie.js') }}"></script>
    <script src="{{ asset('assets/plugins/highcharts/js/export-data.js') }}"></script>
    <script src="{{ asset('assets/plugins/highcharts/js/accessibility.js') }}"></script>
    <script src="{{ asset('assets/plugins/apexcharts-bundle/js/apexcharts.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
            ];

            const chartConfigs = [{
                    id: "#chartTotalOrder",
                    name: "Total Pesanan",
                    color: "#8833ff",
                    data: @json($totalOrdersPerMonth),
                },
                {
                    id: "#chartTotalIncome",
                    name: "Total Income",
                    color: "#29cc39",
                    data: @json($totalIncomePerMonth),
                },
                {
                    id: "#chartTotalExpenses",
                    name: "Total Expenses",
                    color: "#f41127",
                    data: @json($totalExpensesPerMonth),
                },
                {
                    id: "#chartTotalProducts",
                    name: "Total Products",
                    color: "#ffb207",
                    data: @json($totalProductsPerMonth),
                }
            ];

            function renderMiniChart({
                id,
                name,
                color,
                data
            }) {
                const options = {
                    series: [{
                        name: name,
                        data: data
                    }],
                    chart: {
                        type: 'area',
                        height: 65,
                        toolbar: {
                            show: false
                        },
                        zoom: {
                            enabled: false
                        },
                        dropShadow: {
                            enabled: true,
                            top: 3,
                            left: 14,
                            blur: 4,
                            opacity: 0.12,
                            color: color
                        },
                        sparkline: {
                            enabled: true
                        }
                    },
                    markers: {
                        size: 0,
                        colors: [color],
                        strokeColors: "#fff",
                        strokeWidth: 2,
                        hover: {
                            size: 7
                        }
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '45%',
                            endingShape: 'rounded'
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        show: true,
                        width: 2.4,
                        curve: 'smooth'
                    },
                    colors: [color],
                    xaxis: {
                        categories: months
                    },
                    fill: {
                        opacity: 1
                    },
                    tooltip: {
                        theme: 'dark',
                        fixed: {
                            enabled: false
                        },
                        x: {
                            show: false
                        },
                        y: {
                            title: {
                                formatter: function() {
                                    return ''
                                }
                            }
                        },
                        marker: {
                            show: false
                        }
                    }
                };

                const chart = new ApexCharts(document.querySelector(id), options);
                chart.render();
            }

            chartConfigs.forEach(renderMiniChart);


            var options = {
                series: [{
                    name: 'Penjualan',
                    type: 'column',
                    data: @json($totalIncomePerMonth)
                },{
                    name: 'Pembelian',
                    type: 'column',
                    data: @json($totalExpensesPerMonth)
                }, {
                    name: 'Laba Bersih',
                    type: 'line',
                    data: @json($totalProfitPerMonth)
                }],
                chart: {
                    foreColor: '#9ba7b2',
                    height: 350,
                    type: 'line',
                    zoom: {
                        enabled: false
                    },
                    toolbar: {
                        show: true
                    },
                },
                stroke: {
                    width: [0, 4]
                },
                plotOptions: {
                    bar: {
                        //horizontal: true,
                        columnWidth: '35%',
                        endingShape: 'rounded'
                    }
                },
                grid: {
                    show: true,
                    borderColor: 'rgba(0, 0, 0, 0.15)',
                    strokeDashArray: 4,
                },
                colors: ["#8833ff", "#f41127", "#29cc39"],
                title: {
                    text: 'Ringkasan Laporan Bulanan',
                },
                dataLabels: {
                    enabled: true,
                    enabledOnSeries: [1]
                },
                labels: months,
                xaxis: {
                    categories: months,
                    title: {
                        text: 'Bulan'
                    }
                },
                yaxis: [{
                    title: {
                        text: 'Total Penjualan',
                    },
                }, {
                    opposite: true,
                    title: {
                        text: 'Total Pembelian',
                    }
                }]
            };
            var chart = new ApexCharts(document.querySelector("#summaryChart"), options);
            chart.render();
        });
    </script>
@endpush
