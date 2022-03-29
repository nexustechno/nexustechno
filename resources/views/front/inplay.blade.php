@extends('layouts.front_layout')
@push('page_css')
    <style>
        body {
            overflow: hidden;
        }

        .fir-col2.wd22 {
            width: 25%;
        }

        .fir-col3.pinimg img {
            width: 100%;
        }

        .betloaderimage1 {
            top: 50%;
            height: 135px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 5px 10px rgb(0 0 0 / 50%);
            padding-top: 30px;
            z-index: 50;
            position: absolute;
            left: 50%;
            width: 190px;
            margin-left: -95px;
        }

        .loading1 img {
            background-position: -42px -365px;
            height: 51px;
            width: 51px;
        }

        .loading1 li {
            list-style: none;
            text-align: center;
            font-size: 11px;
        }
    </style>
@endpush
@section('content')
    <div id="site_bet_loading1" class="betloaderimage1 loader-style1" style="display: none">
        <ul class="loading1">
            <li>
                <img src="/asset/front/img/loaderajaxbet.gif">
            </li>
            <li>Loading...</li>
        </ul>
    </div>
    <div id="app">
    <section>
        <div class="container-fluid">
            <div class="main-wrapper">
{{--                @include('layouts.leftpanel')--}}
                <div class="middle-section ml-0 pr-sm-3">
                    @if(!empty($settings->user_msg))
                        <div class="news-addvertisment black-gradient-bg text-color-white">
                            <h4>News</h4>
                            <marquee scrollamount="3">
                                <a href="#" class="text-color-blue">{{$settings->user_msg}}</a>
                            </marquee>
                        </div>
                    @endif
                    <div class="middle-wraper">
                            <div class="in_play_tabs" id="InplayData">
                                <ul class="nav nav-tabs" role="tablist">
                                    <li class="nav-item" data-id="inplay">
                                        <a class="nav-link text-color-blue-1 white-bg inplay active" href="#inplay"
                                           role="tab" data-toggle="tab" data-id="inplay">Inplay</a>
                                    </li>
                                    <li class="nav-item" data-id="today">
                                        <a class="nav-link text-color-blue-1 white-bg today" href="#today" role="tab"
                                           data-toggle="tab" data-id="today">Today</a>
                                    </li>
                                    <li class="nav-item" data-id="tomorrow">
                                        <a class="nav-link text-color-blue-1 white-bg tomorrow" href="#tomorrow"
                                           role="tab" data-toggle="tab" data-id="tomorrow"
                                           >Tomorrow</a>
                                    </li>
                                </ul>

                                <div class="tab-content" id="putInplayData">

                                    <div role="tabpanel" class="tab-pane active" id="inplay">

                                        <div class="programe-setcricket today_content">
                                            <a class="todaytitle darkblue-bg text-color-white" data-toggle="collapse"
                                               href="#inplay-cricket-collapse" role="button" aria-expanded="false"
                                               aria-controls="inplay-cricket-collapse">
                                                Cricket
                                            </a>
                                            <div class="collapse show" id="inplay-cricket-collapse">
                                                <div class="programe-setcricket">
                                                    <matches roundpin="{{ asset("asset/front/img/round-pin.png") }}" roundpin1="{{ asset("asset/front/img/round-pin1.png") }}" todaydate="{{ date('d-m-Y') }}" year="{{ date('Y') }}" tomorrowdate="{{ date('d-m-Y', strtotime("+1 day")) }}" :filtertype="'inplay'" :displaymatches="{{ $cricketMatches }}" :favmatches="{{ $favMatches }}" :matchtype="4"></matches>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="programe-setcricket today_content">
                                            <a class="todaytitle darkblue-bg text-color-white" data-toggle="collapse"
                                               href="#inplay-tennis-collapse" role="button" aria-expanded="false"
                                               aria-controls="inplay-tennis-collapse">
                                                Tennis
                                            </a>
                                            <!-- below class adding show to default open tab  kkkkk-->
                                            <div class="collapse show" id="inplay-tennis-collapse">
                                                <div class="programe-setcricket">
                                                    <matches roundpin="{{ asset("asset/front/img/round-pin.png") }}" roundpin1="{{ asset("asset/front/img/round-pin1.png") }}" todaydate="{{ date('d-m-Y') }}" year="{{ date('Y') }}" tomorrowdate="{{ date('d-m-Y', strtotime("+1 day")) }}" :filtertype="'inplay'" :displaymatches="{{ $tennisMatches }}" :favmatches="{{ $favMatches }}" :matchtype="2"></matches>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="programe-setcricket today_content">
                                            <a class="todaytitle darkblue-bg text-color-white" data-toggle="collapse"
                                               href="#inplay-soccer-collapse" role="button" aria-expanded="false"
                                               aria-controls="inplay-soccer-collapse">
                                                Soccer
                                            </a>
                                            <!-- below class adding show to default open tab  kkkkk-->
                                            <div class="collapse show" id="inplay-soccer-collapse">
                                                <div class="programe-setcricket">
                                                    <matches roundpin="{{ asset("asset/front/img/round-pin.png") }}" roundpin1="{{ asset("asset/front/img/round-pin1.png") }}" todaydate="{{ date('d-m-Y') }}" year="{{ date('Y') }}" tomorrowdate="{{ date('d-m-Y', strtotime("+1 day")) }}" :filtertype="'inplay'" :displaymatches="{{ $soccerMatches }}" :favmatches="{{ $favMatches }}" :matchtype="1"></matches>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div role="tabpanel" class="tab-pane " id="today">

                                        <div class="programe-setcricket today_content">
                                            <a class="todaytitle darkblue-bg text-color-white" data-toggle="collapse"
                                               href="#today-cricket-collapse" role="button" aria-expanded="false"
                                               aria-controls="today-cricket-collapse">
                                                Cricket
                                            </a>
                                            <div class="collapse show" id="today-cricket-collapse">
                                                <div class="programe-setcricket" id="today-cricket">
                                                    <matches roundpin="{{ asset("asset/front/img/round-pin.png") }}" roundpin1="{{ asset("asset/front/img/round-pin1.png") }}" todaydate="{{ date('d-m-Y') }}" year="{{ date('Y') }}" tomorrowdate="{{ date('d-m-Y', strtotime("+1 day")) }}" :filtertype="'today'" :displaymatches="{{ $cricketMatches }}" :favmatches="{{ $favMatches }}" :matchtype="4"></matches>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="programe-setcricket today_content">
                                            <a class="todaytitle darkblue-bg text-color-white" data-toggle="collapse"
                                               href="#today-tennis-collapse" role="button" aria-expanded="false"
                                               aria-controls="today-tennis-collapse">
                                                Tennis
                                            </a>
                                            <!-- below class adding show to default open tab  kkkkk-->
                                            <div class="collapse show" id="today-tennis-collapse">
                                                <div class="programe-setcricket" id="today-tennis">
                                                    <matches roundpin="{{ asset("asset/front/img/round-pin.png") }}" roundpin1="{{ asset("asset/front/img/round-pin1.png") }}" todaydate="{{ date('d-m-Y') }}" year="{{ date('Y') }}" tomorrowdate="{{ date('d-m-Y', strtotime("+1 day")) }}" :filtertype="'today'" :displaymatches="{{ $tennisMatches }}" :favmatches="{{ $favMatches }}" :matchtype="2"></matches>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="programe-setcricket today_content">
                                            <a class="todaytitle darkblue-bg text-color-white" data-toggle="collapse"
                                               href="#today-soccer-collapse" role="button" aria-expanded="false"
                                               aria-controls="today-soccer-collapse">
                                                Soccer
                                            </a>
                                            <!-- below class adding show to default open tab  kkkkk-->
                                            <div class="collapse show" id="today-soccer-collapse">
                                                <div class="programe-setcricket" id="today-soccer">
                                                    <matches roundpin="{{ asset("asset/front/img/round-pin.png") }}" roundpin1="{{ asset("asset/front/img/round-pin1.png") }}" todaydate="{{ date('d-m-Y') }}" year="{{ date('Y') }}" tomorrowdate="{{ date('d-m-Y', strtotime("+1 day")) }}" :filtertype="'today'" :displaymatches="{{ $soccerMatches }}" :favmatches="{{ $favMatches }}" :matchtype="1"></matches>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div role="tabpanel" class="tab-pane" id="tomorrow">
                                        <div class="programe-setcricket today_content">
                                            <a class="todaytitle darkblue-bg text-color-white" data-toggle="collapse"
                                               href="#tmr-cricket-collapse" role="button" aria-expanded="false"
                                               aria-controls="tmr-cricket-collapse">
                                                Cricket
                                            </a>
                                            <!-- below class adding show to default open tab  kkkkk-->
                                            <div class="collapse show" id="tmr-cricket-collapse">
                                                <div class="programe-setcricket" id="tmr-cricket">
                                                    <matches roundpin="{{ asset("asset/front/img/round-pin.png") }}" roundpin1="{{ asset("asset/front/img/round-pin1.png") }}" todaydate="{{ date('d-m-Y') }}" year="{{ date('Y') }}" tomorrowdate="{{ date('d-m-Y', strtotime("+1 day")) }}" :filtertype="'tomorrow'" :displaymatches="{{ $cricketMatches }}" :favmatches="{{ $favMatches }}" :matchtype="4"></matches>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="programe-setcricket today_content">
                                            <a class="todaytitle darkblue-bg text-color-white" data-toggle="collapse"
                                               href="#tmr-tennis-collapse" role="button" aria-expanded="false"
                                               aria-controls="tmr-tennis-collapse">
                                                Tennis
                                            </a>
                                            <!-- below class adding show to default open tab  kkkkk-->
                                            <div class="collapse show" id="tmr-tennis-collapse">
                                                <div class="programe-setcricket" id="tmr-tennis">
                                                    <matches roundpin="{{ asset("asset/front/img/round-pin.png") }}" roundpin1="{{ asset("asset/front/img/round-pin1.png") }}" todaydate="{{ date('d-m-Y') }}" year="{{ date('Y') }}" tomorrowdate="{{ date('d-m-Y', strtotime("+1 day")) }}" :filtertype="'tomorrow'" :displaymatches="{{ $tennisMatches }}" :favmatches="{{ $favMatches }}" :matchtype="2"></matches>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="programe-setcricket today_content">
                                            <a class="todaytitle darkblue-bg text-color-white" data-toggle="collapse"
                                               href="#tmr-soccer-collapse" role="button" aria-expanded="false"
                                               aria-controls="tmr-soccer-collapse">
                                                Soccer
                                            </a>
                                            <!-- below class adding show to default open tab  kkkkk-->
                                            <div class="collapse show" id="tmr-soccer-collapse">
                                                <div class="programe-setcricket" id="tmr-soccer">
                                                    <matches roundpin="{{ asset("asset/front/img/round-pin.png") }}" roundpin1="{{ asset("asset/front/img/round-pin1.png") }}" todaydate="{{ date('d-m-Y') }}" year="{{ date('Y') }}" tomorrowdate="{{ date('d-m-Y', strtotime("+1 day")) }}" :filtertype="'tomorrow'" :displaymatches="{{ $soccerMatches }}" :favmatches="{{ $favMatches }}" :matchtype="1"></matches>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>

                </div>
                @include('layouts.rightpanel')
            </div>
        </div>
    </section>
    </div>
@endsection

@push('third_party_scripts')
    <script src="{{ asset('js/app.js') }}"></script>
@endpush

@push('page_scripts')
    @include('front.common-script-for-list')

    <script>
        setInterval(function() {
            $(".backbtn").removeClass("spark");
            $(".laybtn").removeClass("sparkLay");
        }, 500);
    </script>

    <script type="text/javascript">
        $('.todaytitle').click(function () {
            $(this).find('i').toggleClass('fas fa-plus fas fa-minus')
        });
    </script>
@endpush
