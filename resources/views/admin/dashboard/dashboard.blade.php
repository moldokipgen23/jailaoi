@extends('admin.layout.page-app')
@section('page_title', __('label.dashboard'))

@section('content')
@include('admin.layout.sidebar')

<div class="right-content">
    @include('admin.layout.header')

    <?php $settingData = Setting_Data(); ?>

    <div class="body-content">
        <!-- mobile title -->
        <h1 class="page-title-sm">{{__('label.dashboard')}}</h1>
        <!-- card row 1 -->
        <div class="row counter-row">
            <div class="col-xl-3 col-sm-6 col-12 mb-2">
                <div class="custom-card card-color-primary">
                    <div class="card-body">
                        <div class="card-icon-primary card-color-primary">
                            <i class="fa-solid fa-users fa-2x"></i>
                        </div>
                        <div class="text-right">
                            <h3>{{NO_Format($UserCount ?? 0)}}</h3>
                            <span>{{__('label.users')}}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12 mb-2">
                <div class="custom-card card-color-primary">
                    <div class="card-body">
                        <div class="card-icon-primary card-color-primary">
                            <i class="fa-solid fa-user-tie fa-2x"></i>
                        </div>
                        <div class="text-right">
                            <h3>{{No_Format($ArtistCount ?? 0)}}</h3>
                            <span>{{__('label.artist')}}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12 mb-2">
                <div class="custom-card card-color-primary">
                    <div class="card-body">
                        <div class="card-icon-primary card-color-primary">
                            <i class="fa-solid fa-globe fa-2x"></i>
                        </div>
                        <div class="text-right">
                            <h3>{{No_Format($LanguageCount ?? 0)}}</h3>
                            <span>{{__('label.language')}}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12 mb-2">
                <div class="custom-card card-color-primary">
                    <div class="card-body">
                        <div class="card-icon-primary card-color-primary">
                            <i class="fa-solid fa-list fa-2x"></i>
                        </div>
                        <div class="text-right">
                            <h3>{{No_Format($CategoryCount ?? 0)}}</h3>
                            <span>{{__('label.category')}}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- card row 2 -->
        <div class="row counter-row">
            <div class="col-xl-3 col-sm-6 col-12 mb-2">
                <div class="custom-card card-color-primary">
                    <div class="card-body">
                        <div class="card-icon-primary card-color-primary">
                            <i class="fa-solid fa-podcast fa-2x"></i>
                        </div>
                        <div class="text-right">
                            <h3>{{No_Format($PodcastCount ?? 0)}}</h3>
                            <span>{{__('label.podcast')}}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12 mb-2">
                <div class="custom-card card-color-primary">
                    <div class="card-body">
                        <div class="card-icon-primary card-color-primary">
                            <i class="fa-solid fa-film fa-2x"></i>
                        </div>
                        <div class="text-right">
                            <h3>{{No_Format($SongCount ?? 0)}}</h3>
                            <span>{{__('label.radio_station')}}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12 mb-2">
                <div class="custom-card card-color-primary">
                    <div class="card-body">
                        <div class="card-icon-primary card-color-primary">
                            <i class="fa-solid fa-music fa-2x"></i>
                        </div>
                        <div class="text-right">
                            <h3>{{No_Format($MusicCount ?? 0)}}</h3>
                            <span>{{__('label.music')}}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12 mb-2">
                <div class="custom-card card-color-primary">
                    <div class="card-body">
                        <div class="card-icon-primary card-color-primary">
                            <i class="fa-solid fa-box-archive fa-2x"></i>
                        </div>
                        <div class="text-right">
                            <h3>{{No_Format($PackageCount ?? 0)}}</h3>
                            <span>{{__('label.package')}}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- card row 3 -->
        <div class="row counter-row">
            <div class="col-xl-3 col-sm-6 col-12 mb-2">
                <div class="custom-card card-color-primary">
                    <div class="card-body">
                        <div class="card-icon-primary card-color-primary">
                            <i class="fa-solid fa-calendar-week fa-2x"></i>
                        </div>
                        <div class="text-right">
                            <h3>{{No_Format($LiveEventCount ?? 0)}}</h3>
                            <span>{{__('label.live_event')}}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12 mb-2">
                <div class="custom-card card-color-primary">
                    <div class="card-body">
                        <div class="card-icon-primary card-color-primary">
                            <i class="fa-solid fa-coins fa-2x"></i>
                        </div>
                        <div class="text-right">
                            <h3>{{No_Format($LiveEventEarningCount ?? 0)}}</h3>
                            <span>{{__('label.event_earning')}}({{Currency_Code()}})</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12 mb-2">
                <div class="custom-card card-color-primary">
                    <div class="card-body">
                        <div class="card-icon-primary card-color-primary">
                            <i class="fa-solid fa-calendar-days fa-2x"></i>
                        </div>
                        <div class="text-right">
                            <h3>{{No_Format($CurrentMounthCount ?? 0)}}</h3>
                            <span>{{__('label.month_earnings')}}({{Currency_Code()}})</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 col-12 mb-2">
                <div class="custom-card card-color-primary">
                    <div class="card-body">
                        <div class="card-icon-primary card-color-primary">
                            <i class="fa-solid fa-sack-dollar fa-2x"></i>
                        </div>
                        <div class="text-right">
                            <h3>{{No_Format($TransactionCount ?? 0)}}</h3>
                            <span>{{__('label.earnings')}}({{Currency_Code()}})</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- user & author statistice && best category -->
        <div class="row mb-3 pl-3">
            <div class="col-12 col-xl-8 cart-bg">
                <div class="box-title">
                    <h2 class="title"><i class="fa-solid fa-chart-column fa-lg mr-2"></i>{{__('label.join_users_statistics')}}</h2>
                </div>
                <div class="row mt-2">
                    <div class="col-12 col-sm-12">
                        <Button id="year" class="btn btn-default">{{__('label.this_year')}}</Button>
                        <Button id="month" class="btn btn-default">{{__('label.this_month')}}</Button>
                    </div>
                </div>
                <div class="summary-table-card mt-2">
                    <div id="User_Author_Chart"></div>
                </div>
            </div>
            <div class="col-12 col-xl-4">
                <div class="category-box">
                    <div class="box-title mt-0">
                        <h2 class="title"><i class="fa-solid fa-table-cells-large fa-lg mr-2"></i>{{__('label.best_city')}}</h2>
                        <a href="{{ route('city.index')}}" class="btn btn-link">{{__('label.view_all')}}</a>
                    </div>
                    <div class="pt-3 mt-0">
                        <div class="row pr-3">
                            @for ($i = 0; $i < count($best_city); $i++)
                                @if($i> 0 && (($i % 4) == 1 || ($i % 4) == 2))
                                <div class="col-5 mb-2 pr-0">
                                    <img src="{{$best_city[$i]['image']}}" class="category-image">
                                    <div class="centered">{{$best_city[$i]['name']}}</div>
                                </div>
                                @else
                                <div class="col-7 mb-2 pr-0">
                                    <img src="{{$best_city[$i]['image']}}" class="category-image">
                                    <div class="centered">{{$best_city[$i]['name']}}</div>
                                </div>
                                @endif
                                @endfor
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- most play song -->
        <div class="row mb-3 pl-3 mr-0">
            <div class="col-12 cart-bg">
                <div class="box-title">
                    <h2 class="title">{{__('label.most_played_radio_station')}}</h2>
                    <a href="{{ route('song.index')}}" class="btn btn-link">{{__('label.view_all')}}</a>
                </div>
                <div class="row artist-row">
                    @if(isset($most_play_song) && $most_play_song != null)
                    @foreach ($most_play_song as $value)
                    <div class="col-6 col-md-2">
                        <div class="artist-grid-card pl-3 pr-3 pt-3 pb-1 mt-3 mb-3 bg-white">
                            <span class="avatar-control">
                                <img src="{{ $value->image }}" class="img-thumbnail db-imgs" />
                            </span>
                            <p class="name mt-2 db-title">{{$value->name}}</p>
                            <div class="d-flex text-align-center">
                                <span class="d-flex align-items-center">
                                    <i class="fa-solid fa-play fa-xl mr-3 primary-color mb-1"></i>
                                    <h5 class="counting" data-count="{{No_Format($value->total_play ?? 0)}}">{{No_Format($value->total_play ?? 0)}}</h5>
                                </span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    @endif
                </div>
            </div>
        </div>
        <!-- plan earning statistice && best language -->
        <div class="row mb-3 pl-3">
            <div class="col-12 col-xl-8 cart-bg">
                <div class="box-title">
                    <h2 class="title"><i class="fa-solid fa-chart-column fa-lg mr-2"></i>{{__('label.plan_earning_statistics_current_year')}}</h2>
                </div>
                <div class="summary-table-card mt-2">
                    <div id="Package_Chart"></div>
                </div>
            </div>
            <div class="col-12 col-xl-4">
                <div class="category-box">
                    <div class="box-title mt-0">
                        <h2 class="title"><i class="fa-solid fa-table-cells-large fa-lg mr-2"></i>{{__('label.best_language')}}</h2>
                        <a href="{{ route('language.index')}}" class="btn btn-link">{{__('label.view_all')}}</a>
                    </div>
                    <div class="pt-3 mt-0">
                        <div class="row pr-3">
                            @for ($i = 0; $i < count($best_language); $i++)
                                @if($i> 0 && (($i % 4) == 1 || ($i % 4) == 2))
                                <div class="col-5 mb-2 pr-0">
                                    <img src="{{$best_language[$i]['image']}}" class="category-image">
                                    <div class="centered">{{$best_language[$i]['name']}}</div>
                                </div>
                                @else
                                <div class="col-7 mb-2 pr-0">
                                    <img src="{{$best_language[$i]['image']}}" class="category-image">
                                    <div class="centered">{{$best_language[$i]['name']}}</div>
                                </div>
                                @endif
                                @endfor
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- most play podcast -->
        <div class="row mb-3 mr-0 pl-3">
            <div class="col-12 cart-bg">
                <div class="box-title">
                    <h2 class="title">{{__('label.most_played_podcast')}}</h2>
                    <a href="{{ route('podcast.index')}}" class="btn btn-link">{{__('label.view_all')}}</a>
                </div>
                <div class="row artist-row">
                    @if(isset($most_play_podcasts) && $most_play_podcasts != null)
                    @foreach ($most_play_podcasts as $value)
                    <div class="col-6 col-md-2">
                        <div class="artist-grid-card pl-3 pr-3 pt-3 pb-1 mt-3 mb-3 bg-white">
                            <span class="avatar-control">
                                <img src="{{$value->portrait_img}}" class="img-thumbnail db-imgs" />
                            </span>
                            <p class="name mt-2 db-title">{{$value->title}}</p>
                            <div class="d-flex text-align-center">
                                <span class="d-flex align-items-center">
                                    <i class="fa-solid fa-play fa-xl mr-3 primary-color mb-1"></i>
                                    <h5 class="counting" data-count="{{No_Format($value->total_play ?? 0)}}">{{No_Format($value->total_play ?? 0)}}</h5>
                                </span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('pagescript')
<script>
    // Get data from your Laravel controller
    let userYear = JSON.parse(`<?php echo $user_year ?>`);
    let userMonth = JSON.parse(`<?php echo $user_month ?>`);
    let packageName = JSON.parse(`<?php echo $pack_name ?>`);
    let packageData = JSON.parse(`<?php echo $pack_data ?>`);

    let chartOptions = {
        chart: {
            type: 'bar',
            height: 400,
            toolbar: {
                show: false
            }
        },
        dataLabels: {
            enabled: false
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '70%',
                endingShape: 'rounded'
            }
        },
        fill: {
            type: 'gradient',
            gradient: {
                shade: 'light',
                type: 'vertical',
                shadeIntensity: 0.5,
                gradientToColors: ['#2d1944'],
                inverseColors: false,
                opacityFrom: 1,
                opacityTo: 0.8,
                stops: [0, 100]
            }
        },
        colors: ['#2d1944'],
        grid: {
            borderColor: '#9a9a9a',
            strokeDashArray: 4
        },
        tooltip: {
            theme: 'dark',
            style: {
                fontSize: '14px'
            }
        },
        series: [],
        xaxis: {
            categories: []
        },
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

    let chart = new ApexCharts(document.querySelector("#User_Author_Chart"), chartOptions);
    chart.render();

    // Function to load chart data
    function loadChartData(type) {
        if (type === 'year') {
            chart.updateOptions({
                series: [{
                    name: "{{ __('label.users') }}",
                    data: userYear.sum
                }, ],
                xaxis: {
                    categories: [
                        '{{__("label.jan")}}', '{{__("label.feb")}}', '{{__("label.mar")}}', '{{__("label.apr")}}',
                        '{{__("label.may")}}', '{{__("label.jun")}}', '{{__("label.jul")}}', '{{__("label.aug")}}',
                        '{{__("label.sep")}}', '{{__("label.oct")}}', '{{__("label.nov")}}', '{{__("label.dec")}}'
                    ],
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
            });
        } else {
            let daysInMonth = userMonth.sum.length;
            chart.updateOptions({
                series: [{
                    name: "{{ __('label.users') }}",
                    data: userMonth.sum
                }, ],
                xaxis: {
                    categories: Array.from({
                        length: daysInMonth
                    }, (_, i) => (i + 1).toString())
                },
            });
        }
    }

    loadChartData('year');
    document.getElementById('year').addEventListener('click', function() {
        loadChartData('year');
    });
    document.getElementById('month').addEventListener('click', function() {
        loadChartData('month');
    });

    var packageChartOptions = {
        chart: {
            type: 'bar',
            height: 320,
            width: '100%',
            toolbar: {
                show: false
            },
        },
        dataLabels: {
            enabled: false,
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '70%',
                endingShape: 'rounded'
            }
        },
        fill: {
            type: 'gradient',
            gradient: {
                shade: 'light',
                type: 'vertical',
                inverseColors: false,
                opacityFrom: 1,
                opacityTo: 0.9,
                stops: [40, 100]
            }
        },
        colors: ['#6d3a74', '#4e94a6', '#2f4252'],
        grid: {
            borderColor: '#9a9a9a',
            strokeDashArray: 4
        },
        tooltip: {
            theme: 'dark',
            style: {
                fontSize: '14px'
            }
        },
        series: packageName.map((name, index) => {
            return {
                name: name,
                data: packageData[name].sum,
            };
        }),
        xaxis: {
            categories: [
                '{{__("label.jan")}}', '{{__("label.feb")}}', '{{__("label.mar")}}', '{{__("label.apr")}}',
                '{{__("label.may")}}', '{{__("label.jun")}}', '{{__("label.jul")}}', '{{__("label.aug")}}',
                '{{__("label.sep")}}', '{{__("label.oct")}}', '{{__("label.nov")}}', '{{__("label.dec")}}'
            ],
            axisTicks: {
                show: false,
            },
        },
        legend: {
            show: true,
            position: 'top',
            fontSize: '18px',
            fontWeight: 'bold',
            labels: {
                useSeriesColors: true
            }
        },
    };

    let chart2 = new ApexCharts(document.querySelector('#Package_Chart'), packageChartOptions);
    chart2.render();
</script>
@endsection