@extends('admin.layout.page-app')
@section('page_title', __('label.earning_dashboard'))
@section('tab_title', __('label.earning_dashboard'))

@section('content')
    @include('admin.layout.sidebar')

    <div class="right-content">
        @include('admin.layout.header')

        <div class="body-content">
            <!-- mobile title -->
            <h1 class="page-title-sm">{{__('label.earning_dashboard')}}</h1>

            <!-- Card -->
            <div class="row counter-row">
                <div class="col-xl-3 col-sm-6 col-12">
                    <div class="card custom-card card-color-primary">
                        <div class="card-body">
                            <div class="card-icon-primary card-color-primary">
                                <i class="fa-solid fa-box-archive fa-2x"></i>
                            </div>
                            <div class="text-right">
                                <h3>{{ No_Format($PackageCount ?? 0) }}</h3>
                                <span>{{__('label.subscription_package')}}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 col-12">
                    <div class="card custom-card card-color-primary">
                        <div class="card-body">
                            <div class="card-icon-primary card-color-primary">
                                <i class="fa-solid fa-circle-dollar-to-slot fa-2x"></i>
                            </div>
                            <div class="text-right">
                                <h3>{{ No_Format($CoinPackageCount ?? 0) }}</h3>
                                <span>{{__('label.coin_package')}}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 col-12">
                    <div class="card custom-card card-color-primary">
                        <div class="card-body">
                            <div class="card-icon-primary card-color-primary">
                                <i class="fa-solid fa-sack-dollar fa-2x"></i>
                            </div>
                            <div class="text-right">
                                <h3>{{ No_Format($TotalMonthRentRevenueCount ?? 0) }} {{ Currency_Code() }}</h3>
                                <span>{{__('label.rent_revenue')}} ({{ date('M') }})</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 col-12">
                    <div class="card custom-card card-color-primary">
                        <div class="card-body">
                            <div class="card-icon-primary card-color-primary">
                                <i class="fa-solid fa-sack-dollar fa-2x"></i> 
                            </div>
                            <div class="text-right">
                                <h3>{{ No_Format($TotalRentRevenueCount ?? 0) }} {{ Currency_Code() }}</h3>
                                <span>{{__('label.rent_revenue')}} ({{ date('Y') }})</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row counter-row">
                <div class="col-xl-3 col-sm-6 col-12">
                    <div class="card custom-card card-color-primary">
                        <div class="card-body">
                            <div class="card-icon-primary card-color-primary">
                                <i class="fa-solid fa-money-bill-trend-up fa-2x"></i>
                            </div>
                            <div class="text-right">
                                <h3>{{ No_Format($CurrentMonthCount ?? 0) }} {{ Currency_Code() }}</h3>
                                <span>{{__('label.package_earning')}} ({{ date('M') }})</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 col-12">
                    <div class="card custom-card card-color-primary">
                        <div class="card-body">
                            <div class="card-icon-primary card-color-primary">
                                <i class="fa-solid fa-coins fa-2x"></i> 
                            </div>
                            <div class="text-right">
                                <h3>{{ No_Format($CurrentMonthCoinCount ?? 0) }} {{ Currency_Code() }}</h3>
                                <span>{{__('label.coin_pkg_earning')}} ({{ date('M') }})</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 col-12">
                    <div class="card custom-card card-color-primary">
                        <div class="card-body">
                            <div class="card-icon-primary card-color-primary">
                                <i class="fa-solid fa-solid fa-money-check-dollar fa-2x"></i> 
                            </div>
                            <div class="text-right">
                                <h3>{{ No_Format($TotalMonthRentEarningCount ?? 0) }} {{ Currency_Code() }}</h3>
                                <span>{{__('label.rent_earning')}} ({{ date('M') }})</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 col-12">
                    <div class="card custom-card card-color-primary">
                        <div class="card-body">
                            <div class="card-icon-primary card-color-primary">
                                <i class="fa-solid fa-solid fa-money-check-dollar fa-2x"></i> 
                            </div>
                            <div class="text-right">
                                <h3>{{ No_Format($TotalRentEarningCount ?? 0) }} {{ Currency_Code() }}</h3>
                                <span>{{__('label.rent_earning')}} ({{ date('Y') }})</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row counter-row">
                <div class="col-xl-3 col-sm-6 col-12">
                    <div class="card custom-card card-color-primary">
                        <div class="card-body">
                            <div class="card-icon-primary card-color-primary">
                                <i class="fa-solid fa-money-bill-trend-up fa-2x"></i>
                            </div>
                            <div class="text-right">
                                <h3>{{ No_Format($TransactionCount ?? 0) }} {{ Currency_Code() }}</h3>
                                <span>{{__('label.package_earning')}} ({{ date('Y') }})</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 col-12">
                    <div class="card custom-card card-color-primary">
                        <div class="card-body">
                            <div class="card-icon-primary card-color-primary">
                                <i class="fa-solid fa-coins fa-2x"></i> 
                            </div>
                            <div class="text-right">
                                <h3>{{ No_Format($CoinTransactionCount ?? 0) }} {{ Currency_Code() }}</h3>
                                <span>{{__('label.coin_pkg_earning')}} ({{ date('Y') }})</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 col-12">
                    <div class="card custom-card card-color-primary">
                        <div class="card-body">
                            <div class="card-icon-primary card-color-primary">
                                <i class="fa-solid fa-arrow-trend-up fa-2x"></i> 
                            </div>
                            <div class="text-right">
                                <h3>{{ No_Format($PendingWithdrawalCount ?? 0) }} {{ Currency_Code() }}</h3>
                                <span>{{__('label.pending_withdrawal')}}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 col-12">
                    <div class="card custom-card card-color-primary">
                        <div class="card-body">
                            <div class="card-icon-primary card-color-primary">
                                <i class="fa-solid fa-arrow-trend-down fa-2x"></i> 
                            </div>
                            <div class="text-right">
                                <h3>{{ No_Format($CompletedWithdrawalCount ?? 0) }} {{ Currency_Code() }}</h3>
                                <span>{{__('label.completed_withdrawal')}}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Plan Earning && Rent Earning Statistice -->
            <div class="row pl-3">
                <div class="col-12 col-xl-8 cart-bg">
                    <div class="box-title">
                        <h2 class="title"><i class="fa-solid fa-chart-column fa-lg mr-2"></i>{{__('label.subscription_package_earning_statistice_current_year')}}</h2>
                        <a href="{{ route('admin.transaction.index') }}" class="btn btn-link">{{__('label.view_all')}}</a>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div id="PlanEarningChart"></div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-xl-4">
                    <div class="video-box pb-2">
                        <div class="box-title mt-0">
                            <h2 class="title"><i class="fa-solid fa-chart-pie fa-lg mr-2"></i>{{__('label.rent_earning_current_year')}}</h2>
                            <a href="{{ route('admin.renttransaction.index') }}" class="btn btn-link">{{__('label.view_all')}}</a>
                        </div>
                        <div class="summary-table-card mt-2">
                              <div id="RentEarningChart"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ads Plan Earning && Withdrawal Statistice -->
            <div class="row mt-3 pl-3">
                <div class="col-12 col-xl-8 cart-bg">
                    <div class="box-title">
                        <h2 class="title"><i class="fa-solid fa-chart-column fa-lg mr-2"></i>{{__('label.coin_package_earning_statistice_current_year')}}</h2>
                        <a href="{{ route('admin.cointransaction.index') }}" class="btn btn-link">{{__('label.view_all')}}</a>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div id="CoinPlanChart"></div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-xl-4">
                    <div class="video-box pb-2">
                        <div class="box-title mt-0">
                            <h2 class="title"><i class="fa-solid fa-chart-pie fa-lg mr-2"></i>{{__('label.withdrawal_current_year')}}</h2>
                            <a href="{{ route('admin.withdrawal.index') }}" class="btn btn-link">{{__('label.view_all')}}</a>
                        </div>
                        <div class="summary-table-card mt-2">
                            <div id="WithdrawalChart"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('pagescript')
    <!-- Chart -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>

        const currencySymbol = `{{ Currency_Code() }}`;
        document.addEventListener("DOMContentLoaded", function () {
            // Plan Earning
            const cData = JSON.parse(`<?php echo $package ?>`);
            const months = ['{{__("label.jan")}}', '{{__("label.feb")}}', '{{__("label.mar")}}', '{{__("label.apr")}}',
                            '{{__("label.may")}}', '{{__("label.jun")}}', '{{__("label.jul")}}', '{{__("label.aug")}}',
                            '{{__("label.sep")}}', '{{__("label.oct")}}', '{{__("label.nov")}}', '{{__("label.dec")}}'];

            const series = [];
            for (let i = 0; i < cData.label.length; i++) {
                series.push({
                    name: cData.label[i],
                    data: cData.sum[i]
                });
            }
            const options = {
                chart: {
                    type: 'line',
                    height: 400,
                    toolbar: { show: false }
                },
                series: series,
                xaxis: {
                    categories: months,
                    labels: {
                        style: {
                            fontSize: '14px',
                            fontWeight: 'bold'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            fontSize: '14px',
                            fontWeight: 'bold'
                        }
                    }
                },
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                grid: {
                    borderColor: '#9a9a9a',
                    strokeDashArray: 4
                },
                tooltip: {
                    theme: 'dark',
                    style: {
                        fontSize: '14px'
                    },
                    y: {
                        formatter: function (value) {
                            return value + ' ' + currencySymbol;
                        }
                    }
                },
                colors: ['#4e45b8', '#ff9700', '#058f00', '#3498db', '#9b19f5', '#1abc9c', '#ff6f61', '#692705'],
                legend: {
                    position: 'bottom',
                    fontSize: '16px',
                    fontWeight: 'bold',
                    labels: {
                        colors: ['#333'],
                        useSeriesColors: false
                    }
                },
            };
            new ApexCharts(document.querySelector("#PlanEarningChart"), options).render();

            // Rent Earning
            const rentData = JSON.parse(`<?php echo $rent_earning ?>`);
            const month = JSON.parse(`<?php echo $months ?>`);
            const rent_option = {
                chart: {
                    type: 'donut',
                    height: 400,
                },
                series: rentData.sum,
                labels: month,
                colors: ['#f39c12', '#1abc9c', '#4e45b8', '#7f8c8d', '#c0392b', '#2980b9', '#2ecc71', '#8e44ad', '#a19135', '#34495e', '#35b03b', '#9b59b6'],
                plotOptions: {
                    pie: {
                        startAngle: -90,
                        endAngle: 270
                    }
                },
                dataLabels: {
                    enabled: false
                },
                fill: {
                    type: 'gradient',
                },
                tooltip: {
                    y: {
                        formatter: function (value) {
                            return value + ' ' + currencySymbol;
                        }
                    }
                },
                legend: {
                    position: 'bottom',
                    fontSize: '14px',
                    fontWeight: 'bold',
                    labels: {
                        colors: ['#333'],
                        useSeriesColors: false
                    }
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: { width: 300 },
                        legend: { position: 'bottom' }
                    }
                }]
            };
            new ApexCharts(document.querySelector("#RentEarningChart"), rent_option).render();

            // Coin Plan Earning
            const coinData = JSON.parse(`<?php echo $coin_package ?>`);
            const coin_series = [];
            for (let i = 0; i < coinData.label.length; i++) {
                coin_series.push({
                    name: coinData.label[i],
                    data: coinData.sum[i]
                });
            }
            const coin_options = {
                chart: {
                    type: 'line',
                    height: 400,
                    toolbar: { show: false }
                },
                series: coin_series,
                xaxis: {
                    categories: months,
                    labels: {
                        style: {
                            fontSize: '14px',
                            fontWeight: 'bold'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            fontSize: '14px',
                            fontWeight: 'bold'
                        }
                    }
                },
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                grid: {
                    borderColor: '#9a9a9a',
                    strokeDashArray: 4
                },
                tooltip: {
                    theme: 'dark',
                    style: {
                        fontSize: '14px'
                    },
                    y: {
                        formatter: function (value) {
                            return value + ' ' + currencySymbol;
                        }
                    }
                },
                colors: ['#4e45b8', '#ff9700', '#058f00', '#3498db', '#9b19f5', '#1abc9c', '#ff6f61', '#692705'],
                legend: {
                    position: 'bottom',
                    fontSize: '16px',
                    fontWeight: 'bold',
                    labels: {
                        colors: ['#333'],
                        useSeriesColors: false
                    }
                },
            };
            new ApexCharts(document.querySelector("#CoinPlanChart"), coin_options).render();

            // Withdrawal
            const withdrawalData = JSON.parse(`<?php echo $withdrawal_earning ?>`);
            const withdrawal_option = {
                chart: {
                    type: 'donut',
                    height: 400,
                },
                series: withdrawalData.sum,
                labels: ["Pending", "Completed"],
                colors: ['#e3000b', '#058f00'],
                plotOptions: {
                    pie: {
                        startAngle: -90,
                        endAngle: 90,
                        offsetY: 10
                    }
                },
                dataLabels: {
                    enabled: false
                },
                fill: {
                    type: 'gradient',
                },
                tooltip: {
                    y: {
                        formatter: function (value) {
                            return value + ' ' + currencySymbol;
                        }
                    }
                },
                legend: {
                    position: 'bottom',
                    fontSize: '18px',
                    fontWeight: 'bold',
                    offsetY: -90,
                    labels: {
                        colors: ['#333'],
                        useSeriesColors: false
                    }
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: { width: 300 },
                        legend: {
                            position: 'bottom',
                            offsetY: -5
                        }
                    }
                }]
            };
            new ApexCharts(document.querySelector("#WithdrawalChart"), withdrawal_option).render();
        });
    </script>
@endsection