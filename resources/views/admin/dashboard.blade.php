@extends('admin.layout.page-app')
@section('page_title', __('Label.Dashboard'))

@section('content')
    @include('admin.layout.sidebar')

    <div class="right-content">
        @include('admin.layout.header')

        <?php $settingData = Setting_Data(); ?>

        <div class="body-content">
            <!-- mobile title -->
            <h1 class="page-title-sm">{{__('Label.Dashboard')}}</h1>

            <!-- Counter -->
            <div class="row counter-row">
                <div class="col-6 col-sm-4 col-md col-lg-4 col-xl">
                    <div class="db-color-card color1-card">
                        <i class="fa-solid fa-users fa-4x card-icon"></i>
                        <div class="dropdown dropright">
                            <a href="#" class="btn head-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa-solid fa-ellipsis-vertical fa-xl text-dark dot-icon mr-2"></i>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                <a class="dropdown-item color1-viewall" href="{{ route('user.index')}}">{{__('Label.View_All')}}</a>
                            </div>
                        </div>
                        <h2 class="counter">
                            <p class="p-0 m-0 counting" data-count="{{No_Format($UserCount ?? 0)}}">{{No_Format($UserCount ?? 0)}}</p>
                            <span>{{__('Label.Users')}}</span>
                        </h2>
                    </div>
                </div>
                <div class="col-6 col-sm-4 col-md col-lg-4 col-xl">
                    <div class="db-color-card color2-card">
                        <i class="fa-solid fa-user-tie fa-4x card-icon"></i>
                        <div class="dropdown dropright">
                            <a href="#" class="btn head-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa-solid fa-ellipsis-vertical fa-xl text-dark dot-icon mr-2"></i>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                <a class="dropdown-item color2-viewall" href="{{ route('artist.index')}}">{{__('Label.View_All')}}</a>
                            </div>
                        </div>
                        <h2 class="counter">
                            <p class="p-0 m-0 counting" data-count="{{No_Format($ArtistCount ?? 0)}}">{{No_Format($ArtistCount ?? 0)}}</p>
                            <span>{{__('Label.Artist')}}</span>
                        </h2>
                    </div>
                </div>
                <div class="col-6 col-sm-4 col-md col-lg-4 col-xl">
                    <div class="db-color-card color3-card">
                        <i class="fa-solid fa-film fa-4x card-icon"></i>
                        <div class="dropdown dropright">
                            <a href="#" class="btn head-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa-solid fa-ellipsis-vertical fa-xl text-dark dot-icon mr-2"></i>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                <a class="dropdown-item color3-viewall" href="{{ route('song.index') }}">{{__('Label.View_All')}}</a>
                            </div>
                        </div>
                        <h2 class="counter mt-4">
                            <p class="p-0 m-0 counting" data-count="{{No_Format($SongCount ?? 0)}}">{{No_Format($SongCount ?? 0)}}</p>
                            <span>Radio Station</span>
                        </h2>
                    </div>
                </div>
                <div class="col-6 col-sm-4 col-md col-lg-4 col-xl">
                    <div class="db-color-card color4-card">
                        <i class="fa-solid fa-list fa-4x card-icon"></i>
                        <div class="dropdown dropright">
                            <a href="#" class="btn head-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa-solid fa-ellipsis-vertical fa-xl text-dark dot-icon mr-2"></i>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                <a class="dropdown-item color4-viewall" href="{{ route('category.index') }}">{{__('Label.View_All')}}</a>
                            </div>
                        </div>
                        <h2 class="counter">
                            <p class="p-0 m-0 counting" data-count="{{No_Format($CategoryCount ?? 0)}}">{{No_Format($CategoryCount ?? 0)}}</p>
                            <span>{{__('Label.Category')}}</span>
                        </h2>
                    </div>
                </div>
                <div class="col-6 col-sm-4 col-md col-lg-4 col-xl">
                    <div class="db-color-card color5-card">
                        <i class="fa-solid fa-podcast fa-4x card-icon"></i>
                        <div class="dropdown dropright">
                            <a href="#" class="btn head-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa-solid fa-ellipsis-vertical fa-xl text-dark dot-icon mr-2"></i>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                <a class="dropdown-item color5-viewall" href="{{ route('podcast.index') }}">{{__('Label.View_All')}}</a>
                            </div>
                        </div>
                        <h2 class="counter">
                            <p class="p-0 m-0 counting" data-count="{{No_Format($PodcastCount ?? 0)}}">{{No_Format($PodcastCount ?? 0)}}</p>
                            <span>Podcasts</span>
                        </h2>
                    </div>
                </div>
            </div>

            <!-- Counter -->
            <div class="row counter-row">
                <div class="col-6 col-sm-4 col-md col-lg-4 col-xl">
                    <div class="db-color-card color6-card">
                        <i class="fa-solid fa-money-bill-1 fa-4x card-icon"></i>
                        <div class="dropdown dropright">
                            <a href="#" class="btn head-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa-solid fa-ellipsis-vertical fa-xl text-dark dot-icon mr-2"></i>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                <a class="dropdown-item color6-viewall" href="{{ route('transaction.index') }}">{{__('Label.View_All')}}</a>
                            </div>
                        </div>
                        <h2 class="counter mt-0">
                            <p class="p-0 m-0 counting" data-count="{{No_Format($CurrentMounthCount ?? 00)}}">{{No_Format($CurrentMounthCount ?? 00)}}</p>
                            <span style="font-size: 20px;">Month Earnings({{Currency_Code()}})</span>
                        </h2>
                    </div>
                </div>
                <div class="col-6 col-sm-4 col-md col-lg-4 col-xl">
                    <div class="db-color-card color7-card">
                        <i class="fa-solid fa-money-bill fa-4x card-icon"></i>
                        <div class="dropdown dropright">
                            <a href="#" class="btn head-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa-solid fa-ellipsis-vertical fa-xl text-dark dot-icon mr-2"></i>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                <a class="dropdown-item color7-viewall" href="{{ route('transaction.index') }}">{{__('Label.View_All')}}</a>
                            </div>
                        </div>
                        <h2 class="counter mt-4">
                            <p class="p-0 m-0 counting" data-count="{{No_Format($TransactionCount ?? 00)}}">{{No_Format($TransactionCount ?? 00)}}</p>
                            <span>Earnings ({{Currency_Code()}})</span>
                        </h2>
                    </div>
                </div>
                <div class="col-6 col-sm-4 col-md col-lg-4 col-xl">
                    <div class="db-color-card color8-card">
                        <i class="fa-solid fa-box-archive fa-4x card-icon"></i>
                        <div class="dropdown dropright">
                            <a href="#" class="btn head-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa-solid fa-ellipsis-vertical fa-xl text-dark dot-icon mr-2"></i>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                <a class="dropdown-item color8-viewall" href="{{ route('package.index') }}">{{__('Label.View_All')}}</a>
                            </div>
                        </div>
                        <h2 class="counter">
                            <p class="p-0 m-0 counting" data-count="{{No_Format($PackageCount ?? 00)}}">{{No_Format($PackageCount ?? 00)}}</p>
                            <span>Package</span>
                        </h2>
                    </div>
                </div>
                <div class="col-6 col-sm-4 col-md col-lg-4 col-xl">
                    <div class="db-color-card color9-card">
                        <i class="fa-solid fa-calendar-week fa-4x card-icon"></i>
                        <div class="dropdown dropright">
                            <a href="#" class="btn head-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa-solid fa-ellipsis-vertical fa-xl text-dark dot-icon mr-2"></i>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                <a class="dropdown-item color9-viewall" href="{{ route('liveevent.index') }}">{{__('Label.View_All')}}</a>
                            </div>
                        </div>
                        <h2 class="counter">
                            <p class="p-0 m-0 counting" data-count="{{No_Format($LiveEventCount ?? 00)}}">{{No_Format($LiveEventCount?? 00)}}</p>
                            <span>Live Event</span>
                        </h2>
                    </div>
                </div>
                <div class="col-6 col-sm-4 col-md col-lg-4 col-xl">
                    <div class="db-color-card color10-card">
                        <i class="fa-solid fa-money-bill-wave fa-4x card-icon"></i>
                        <div class="dropdown dropright">
                            <a href="#" class="btn head-btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa-solid fa-ellipsis-vertical fa-xl text-dark dot-icon mr-2"></i>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                <a class="dropdown-item color10-viewall" href="{{ route('city.index') }}">{{__('Label.View_All')}}</a>
                            </div>
                        </div>
                        <h2 class="counter mt-4">
                            <p class="p-0 m-0 counting" data-count="{{No_Format($LiveEventEarningCount ?? 00)}}">{{No_Format($LiveEventEarningCount?? 00)}}</p>
                            <span>Event Earning({{Currency_Code()}})</span>
                        </h2>
                    </div>
                </div>
            </div>

            <!-- Join User Statistice && Best City -->
            <div class="row mb-2">
                <div class="col-12 col-xl-8 cart-bg">
                    <div class="box-title">
                        <h2 class="title"><i class="fa-solid fa-chart-column fa-lg mr-2"></i>Join Users Statistice (Current Year)</h2>
                        <a href="{{ route('user.index') }}" class="btn btn-link">{{__('Label.View_All')}}</a>
                    </div>
                    <div class="row mt-2 mb-2">
                        <div class="col-12 col-sm-12">
                            <Button id="year" class="btn btn-default">This Year</Button>
                            <Button id="month" class="btn btn-default">This Month</Button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-sm-12">
                            <canvas id="UserChart" width="100%" height="40px"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-xl-4">
                    <div class="category-box">
                        <div class="box-title mt-0">
                            <h2 class="title"><i class="fa-solid fa-table-cells-large fa-lg mr-2"></i>Best City</h2>
                            <a href="{{ route('city.index')}}" class="btn btn-link">{{__('Label.View_All')}}</a>
                        </div>
                        <div class="pt-3 mt-0">
                            <div class="row pr-3">
                                @for ($i = 0; $i < count($best_city); $i++)
                                    @if($i > 0 && (($i % 4) == 1 || ($i % 4) == 2))
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

            <!-- Most Play Song -->
            <div class="row mb-2 mr-0">
                <div class="col-12 cart-bg">
                    <div class="box-title">
                        <h2 class="title">Most Play Radio Station</h2>
                        <a href="{{ route('song.index')}}" class="btn btn-link">{{__('Label.View_All')}}</a>
                    </div>
                    <div class="row artist-row">
                        @if(isset($most_play_song) && $most_play_song != null)
                            @foreach ($most_play_song as $value)
                            <div class="col-6 col-md-2">
                                <div class="artist-grid-card pl-3 pr-3 pt-3 pb-1 mt-3 mb-3 bg-white">
                                    <span class="avatar-control">
                                        <img src="{{ $value->image }}" class="img-thumbnail" style="height: 150px; width: 100%; border-radius: 5px" />
                                    </span>
                                    <p class="name mt-2" style="display: inline-block; text-overflow:ellipsis; white-space:nowrap; overflow:hidden; width:100%;font-size: 16px;">{{$value->name}}</p>
                                    <div class="d-flex text-align-center">
                                        <span class="d-flex text-align-center">
                                            <i class="fa-solid fa-play fa-xl mr-3" style="color:#4e45b8; margin-top:12px"></i>
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

            <!-- Plan Earning Statistice && Best Language -->
            <div class="row mb-2">
                <div class="col-12 col-xl-8 cart-bg">
                    <div class="box-title">
                        <h2 class="title"><i class="fa-solid fa-chart-column fa-lg mr-2"></i>Plan Earning Statistice (Current Year)</h2>
                        <a href="{{ route('transaction.index') }}" class="btn btn-link">{{__('Label.View_All')}}</a>
                    </div>
                    <div class="row">
                        <div class="col-12 col-sm-12">
                            <canvas id="MyChart" width="100%" height="40px"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-xl-4">
                    <div class="category-box">
                        <div class="box-title mt-0">
                            <h2 class="title"><i class="fa-solid fa-table-cells-large fa-lg mr-2"></i>Best Language</h2>
                            <a href="{{ route('language.index')}}" class="btn btn-link">{{__('Label.View_All')}}</a>
                        </div>
                        <div class="pt-3 mt-0">
                            <div class="row pr-3">
                                @for ($i = 0; $i < count($best_language); $i++)
                                    @if($i > 0 && (($i % 4) == 1 || ($i % 4) == 2))
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

            <!-- Most Play Podcast -->
            <div class="row mb-2 mr-0">
                <div class="col-12 cart-bg">
                    <div class="box-title">
                        <h2 class="title">Most Play Podcast</h2>
                        <a href="{{ route('podcast.index')}}" class="btn btn-link">{{__('Label.View_All')}}</a>
                    </div>
                    <div class="row artist-row">
                        @if(isset($most_play_podcasts) && $most_play_podcasts != null)
                            @foreach ($most_play_podcasts as $value)
                            <div class="col-6 col-md-2">
                                <div class="artist-grid-card pl-3 pr-3 pt-3 pb-1 mt-3 mb-3 bg-white">
                                    <span class="avatar-control">
                                        <img src="{{$value->portrait_img}}" class="img-thumbnail" style="height: 150px; width: 100%; border-radius: 5px" />
                                    </span>
                                    <p class="name mt-2" style="display: inline-block; text-overflow:ellipsis; white-space:nowrap; overflow:hidden; width:100%;font-size: 16px;">{{$value->title}}</p>
                                    <div class="d-flex text-align-center">
                                        <span class="d-flex text-align-center">
                                            <i class="fa-solid fa-play fa-xl mr-3" style="color:#4e45b8; margin-top:12px"></i>
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
        // User Statistice
        var cData = JSON.parse(`<?php echo $user_year; ?>`);
        var ctx = $("#UserChart");
        var month = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];

        var data = {
            labels: month,
            datasets: [{
                label: 'Users',
                data: cData['sum'],
                backgroundColor: '#4e45b8',
            }],
        };
        var options = {
            responsive: true,
            title: {
                display: true,
                position: "top",
                text: "Join Users Statistice (Current Year)",
                fontSize: 18,
                fontColor: "#000"
            },
            legend: {
                title: "text",
                display: true,
                position: 'top',
                labels: {
                    fontSize: 16,
                    fontColor: "#000000",
                }
            },
            scales: {
                yAxes: [{
                    scaleLabel: {
                        display: true,
                        labelString: 'Total Count',
                        fontSize: 16,
                        fontColor: "#000000",
                    },
                }],
                xAxes: [{
                    scaleLabel: {
                        display: true,
                        labelString: 'Month',
                        fontSize: 16,
                        fontColor: "#000000",
                    }
                }]
            }
        };
        var chart1 = new Chart(ctx, {
            type: "bar",
            data: data,
            options: options
        });

        $("#year").on("click", function() {
            chart1.destroy();

            chart1 = new Chart(ctx, {
                type: "bar",
                data: data,
                options: options

            });
        });
        $("#month").on("click", function() {

            var date = new Date();
            var currentYear = date.getFullYear();
            var currentMonth = date.getMonth() + 1;
            const getDays = (year, month) => new Date(year, month, 0).getDate();
            const days = getDays(currentYear, currentMonth);

            var all1 = [];
            for (let i = 0; i < days; i++) {
                all1.push(i + 1);
            }

            chart1.destroy();
            var cData = JSON.parse(`<?php echo $user_month ?>`);

            var data = {
                labels: all1,
                datasets: [{
                    label: 'Users',
                    data: cData['sum'],
                    backgroundColor: '#4e45b8',
                }],
            };
            var options = {
                responsive: true,
                title: {
                    display: true,
                    position: "top",
                    text: "Join Users Statistice (Current Month)",
                    fontSize: 18,
                    fontColor: "#000"
                },
                legend: {
                    title: "text",
                    display: true,
                    position: 'top',
                    labels: {
                        fontSize: 16,
                        fontColor: "#000000",
                    }
                },
                scales: {
                    yAxes: [{
                        scaleLabel: {
                            display: true,
                            labelString: 'Total Count',
                            fontSize: 16,
                            fontColor: "#000000",
                        },
                    }],
                    xAxes: [{
                        scaleLabel: {
                            display: true,
                            labelString: 'Date',
                            fontSize: 16,
                            fontColor: "#000000",
                        }
                    }]
                }
            };
            chart1 = new Chart(ctx, {
                type: "bar",
                data: data,
                options: options,
            });
        });

        // Package Earning Statistice
        $(function() {
            //get the pie chart canvas
            var cData = JSON.parse(`<?php echo $package; ?>`);
            var ctx = $("#MyChart");
            var backcolor = ["#6D3A74", "#528BA6", "#2A445E", "#E99E75", "#00bfa0", "#9b19f5", "#ffa300", "#dc0ab4", "#7c1158", "#b30000"];

            const datasetValue = [];
            for (let i = 0; i < cData['label'].length; i++) {
                datasetValue[i] = {
                    label: cData['label'][i],
                    data: cData['sum'][i],
                    backgroundColor: backcolor[i],
                }
            }

            //bar chart data
            var data = {
                labels: month,
                datasets: datasetValue
            };

            //options
            var options = {
                responsive: true,
                title: {
                    display: true,
                    position: "top",
                    text: "Package Earning Statistice (Current Year)",
                    fontSize: 18,
                    fontColor: "#000"
                },
                legend: {
                    title: "text",
                    display: true,
                    position: 'top',
                    labels: {
                        fontSize: 16,
                        fontColor: "#000000",
                    }
                },
                scales: {
                    yAxes: [{
                        scaleLabel: {
                            display: true,
                            labelString: 'Amount',
                            fontSize: 16,
                            fontColor: "#000000",
                        },
                    }],
                    xAxes: [{
                        scaleLabel: {
                            display: true,
                            labelString: 'Month',
                            fontSize: 16,
                            fontColor: "#000000",
                        }
                    }]
                }
            };

            //create bar Chart class object
            var chart1 = new Chart(ctx, {
                type: "bar",
                data: data,
                options: options
            });
        });
    </script>
@endsection