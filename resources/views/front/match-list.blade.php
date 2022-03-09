@extends('layouts.front_layout')
@push('page_css')
    <style>
        body {
            overflow: hidden;
        }
    </style>
@endpush
@section('content')
<?php
use App\setting;

$settings =setting::first();
?>

<section>
    <div class="container-fluid">
        <div class="main-wrapper">
           @include('layouts.leftpanel')
            <div class="middle-section">
                <div id="broadcast"></div>
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
                        <img src="{{ URL::to('asset/front/img/cricket.jpg') }}">
                    </div>
                    <div class="mobileslide_menu yellow-gradient-bg3 d-lg-none">
                        <a class="res_search black-gradient-bg4"><img src="{{ URL::to('asset/front/img/search.svg') }}" alt=""></a>
                        <div class="mslide_nav">
                            <ul>
                                <li class="menu_casino {{ (request()->is('casino' )) ? 'active' : '' }}">
                                    <span class="tagnew">New</span>
                                    <a href="{{route('casino')}}" class="text-color-white purple-blue-gradient-bg">
                                        <img src="{{ URL::to('asset/front/img/mslide_menu/mslide-icon-casino.svg') }}" alt=""> Casino
                                    </a>
                                </li>
                                <li class="{{ (request()->is('cricket' )) ? 'active' : '' }}">
                                    <span class="highlight-red grey-gradient-bg"> <span class="text-color-white red-gradient-bg cricketCount"></span> </span>
                                    <a href="{{route('cricket')}}" class="text-color-black1">
                                        <img src="{{ URL::to('asset/front/img/mslide_menu/mslide-icon-cricket-black.svg') }}" alt="" class="dactimg">
                                        <img src="{{ URL::to('asset/front/img/mslide_menu/mslide-icon-cricket-yellow.svg') }}" alt="" class="actimg">
                                        Cricket
                                    </a>
                                </li>
                                <li class="{{ (request()->is('soccer' )) ? 'active' : '' }}">
                                    <span class="highlight-red grey-gradient-bg"> <span class="text-color-white red-gradient-bg soccerCount"></span> </span>
                                    <a href="{{route('soccer')}}" class="text-color-black1">
                                        <img src="{{ URL::to('asset/front/img/mslide_menu/mslide-icon-soccer-black.svg') }}" alt="" class="dactimg">
                                        <img src="{{ URL::to('asset/front/img/mslide_menu/mslide-icon-soccer-yellow.svg') }}" alt="" class="actimg">
                                        Soccer
                                    </a>
                                </li>
                                <li class="{{ (request()->is('tennis' )) ? 'active' : '' }}">
                                    <span class="highlight-red grey-gradient-bg"> <span class="text-color-white red-gradient-bg tennisCount"></span> </span>
                                    <a href="{{route('tennis')}}" class="text-color-black1">
                                        <img src="{{ URL::to('asset/front/img/mslide_menu/mslide-icon-tennis-black.svg') }}" alt="" class="dactimg">
                                        <img src="{{ URL::to('asset/front/img/mslide_menu/mslide-icon-tennis-yellow.svg') }}" alt="" class="actimg">
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
                                <input type="text" name="" id="" placeholder="Search Events" class="form-control" autocomplete="off" autocapitalize="off" autocorrect="off">
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
                            <div id="app">
                                <matches roundpin="{{ asset("asset/front/img/round-pin.png") }}" roundpin1="{{ asset("asset/front/img/round-pin1.png") }}" todaydate="{{ date('d-m-Y') }}" year="{{ date('Y') }}" tomorrowdate="{{ date('d-m-Y', strtotime("+1 day")) }}" :displaymatches="{{ $matches }}" :favmatches="{{ $favMatches }}" :matchtype="{{ $match_type }}"></matches>
                            </div>
{{--                            {!! $cricket_html !!}--}}
                        </div>

                        @include('front.social-footer-links')

                    </div>
                </div>
            </div>
            @include('layouts.rightpanel')
        </div>
    </div>
</section>

@endsection

@push('third_party_scripts')
    <script src="{{ asset('js/app.js') }}?v={{$vue_app_version}}"></script>
@endpush

@push('page_scripts')
    @include('front.common-script-for-list')
@endpush
