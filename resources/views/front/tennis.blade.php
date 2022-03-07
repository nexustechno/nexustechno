@extends('layouts.front_layout')
@section('content')
    <?php
    use App\Match;
    use App\setting;
    use App\User;
    $settings = setting::first();
    ?>
    <style>
        body {
            overflow: hidden;
        }
    </style>
    <section>
        <div class="container-fluid">
            <div class="main-wrapper">
                @include('layouts.leftpanel')
                <div class="middle-section">
                    @if(!empty($getUser))
                        @if(!empty($settings->user_msg))
                            <div class="news-addvertisment black-gradient-bg text-color-white">
                                <h4>News</h4>
                                <marquee>
                                    <a href="#" class="text-color-blue">{{$settings->user_msg}}</a>
                                </marquee>
                            </div>
                        @endif
                    @endif
                    <div class="middle-wraper">
                        <div class="cricket-bg">
                            <img src="{{ URL::to('asset/front/img/tennisFront.jpg') }}">
                        </div>
                        <div class="mobileslide_menu yellow-gradient-bg3 d-lg-none">
                            <a class="res_search black-gradient-bg4"><img
                                    src="{{ URL::to('asset/front/img/search.svg') }}" alt=""></a>
                            <div class="mslide_nav">
                                <ul>
                                    <li class="menu_casino">
                                        <span class="tagnew">New</span>
                                        <a href="{{route('casino')}}" class="text-color-white purple-blue-gradient-bg">
                                            <img
                                                src="{{ URL::to('asset/front/img/mslide_menu/mslide-icon-casino.svg') }}"
                                                alt=""> Casino
                                        </a>
                                    </li>
                                    <li>
                                        <span class="highlight-red grey-gradient-bg"> <span
                                                class="text-color-white red-gradient-bg cricketCount"></span> </span>
                                        <a href="{{route('cricket')}}" class="text-color-black1">
                                            <img
                                                src="{{ URL::to('asset/front/img/mslide_menu/mslide-icon-cricket-black.svg') }}"
                                                alt="" class="dactimg">
                                            <img
                                                src="{{ URL::to('asset/front/img/mslide_menu/mslide-icon-cricket-yellow.svg') }}"
                                                alt="" class="actimg">
                                            Cricket
                                        </a>
                                    </li>
                                    <li>
                                        <span class="highlight-red grey-gradient-bg"> <span
                                                class="text-color-white red-gradient-bg soccerCount"></span> </span>
                                        <a href="{{route('soccer')}}" class="text-color-black1">
                                            <img
                                                src="{{ URL::to('asset/front/img/mslide_menu/mslide-icon-soccer-black.svg') }}"
                                                alt="" class="dactimg">
                                            <img
                                                src="{{ URL::to('asset/front/img/mslide_menu/mslide-icon-soccer-yellow.svg') }}"
                                                alt="" class="actimg">
                                            Soccer
                                        </a>
                                    </li>
                                    <li class="active">
                                        <span class="highlight-red grey-gradient-bg"> <span
                                                class="text-color-white red-gradient-bg tennisCount"></span> </span>
                                        <a href="{{route('tennis')}}" class="text-color-black1">
                                            <img
                                                src="{{ URL::to('asset/front/img/mslide_menu/mslide-icon-tennis-black.svg') }}"
                                                alt="" class="dactimg">
                                            <img
                                                src="{{ URL::to('asset/front/img/mslide_menu/mslide-icon-tennis-yellow.svg') }}"
                                                alt="" class="actimg">
                                            Tennis
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="search_wrapm d-lg-none" id="searchWrap">
                            <div class="searwrp_popup white-bg">
                                <form action="">
                                    <a id="serback" class="search_backm">
                                        <img src="{{ URL::to('asset/front/img/mslide_menu/search-back.svg') }}" alt="">
                                    </a>
                                    <input type="text" name="" id="" placeholder="Search Events" class="form-control"
                                           autocomplete="off" autocapitalize="off" autocorrect="off">
                                    <button id="searchcelar" type="reset" class="btnsearch clearm">
                                        <img src="{{ URL::to('asset/front/img/mslide_menu/close-icon.svg') }}" alt="">
                                    </button>
                                    <button id="search" type="submit" class="btnsearch serachm">
                                        <img src="{{ URL::to('asset/front/img/mslide_menu/search-black.svg') }}" alt="">
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="maintabs cricket-section">
                            <h3 class="yellow-bg2 text-color-black2 highligths-txt">Highlights</h3>
                            <div class="programe-setcricket">
                                <div class="firstblock-cricket lightblue-bg1">
                                    <span class="fir-col1"></span>
                                    <span class="fir-col2">1</span>
                                    <span class="fir-col2">X</span>
                                    <span class="fir-col2">2</span>
                                    <span class="fir-col3"></span>
                                </div>
                                {!! $tennis_html !!}
                            </div>

                            @include('front.social-footer-links')
                        </div>
                    </div>
                </div>
                @include('layouts.rightpanel')
            </div>
        </div>
    </section>
{{--    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>--}}
    @include('front.common-script-for-list')
    <script>
        function loadTennisData() {
            var _token = $("input[name='_token']").val();
            $.ajax({
                type: "POST",
                url: '{{route("getmatchdetailsOfTennis")}}',
                data: {_token: _token},
                beforeSend: function () {
                    $('#site_statistics_loading').show();
                },
                complete: function () {
                    $('#site_statistics_loading').hide();
                },
                success: function (data) {
                    $(".programe-setcricket").html(data);
                }
            });
        }

        $(document).ready(function () {
            // loadTennisData();
            if (getUser != '') {
                setInterval(function () {
                    loadTennisData();
                }, 1000);
            }
        });
    </script>
@endsection
