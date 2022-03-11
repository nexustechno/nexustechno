@extends('layouts.front_layout')
@push('page_css')
    <style>
        .disabled-link {
            pointer-events: none;
        }
    </style>
@endpush
@section('content')
    <section>
        <div class="container-fluid">
            <div class="main-wrapper">
                @include('layouts.leftpanel')
                <div class="middle-section">

                    @php $getUser = Session::get('playerUser'); @endphp
                    @if(!empty($getUser))

                        @php $settings = \App\setting::first(); @endphp
                        @if(!empty($settings->user_msg))
{{--                            <div class="row">--}}
{{--                                <div class="col-12">--}}
                                    <div class="news-addvertisment black-gradient-bg text-color-white">
                                        <h4>News</h4>
                                        <marquee>
                                            <a href="#" class="text-color-blue">{{$settings->user_msg}}</a>
                                        </marquee>
                                    </div>
{{--                                </div>--}}
{{--                            </div>--}}
                        @endif
                    @endif
                    <div class="middle-wraper">
                        <div class="casino_result_section new_casino">
                            <div class="our_casino_list casino pt-0 mt-1">
                                <h4 class="sport-list-title w-100">Our Live Casinos</h4>

                                        <div class="row casino-list-items-block">
                                            @php $item = 0 @endphp
                                            @foreach($casino as $key => $casinos)
                                                <?php
                                                    $item++;
                                                    $class = "disabled-link";
                                                    if ($casinos->status == 1) {
                                                        $class = "";
                                                    }
                                                ?>

                                                <div class="col-6 col-sm-3 @if($key%2 === 0){{'odd'}}@else{{'even'}}@endif {{'box'.$item}}">
                                                    <div class="casino_list_items">
                                                        <a class="{{$class}}"
                                                           href="{{route('casinoDetail',[$casinos->id,$casinos->casino_name])}}">
                                                            <img src="{{$casinos->image_url}}" alt="img">
                                                        </a>
                                                        <dl class="entrance-title">
                                                            <dt>{{ $casinos->casino_title }}</dt>
                                                            <dd><span class="blink_me"> Play Now </span></dd>
                                                        </dl>
                                                    </div>
                                                </div>
                                                    @php

                                                        if($item == 4){
                                                            $item = 0;
                                                        }
                                                    @endphp
                                            @endforeach
                                        </div>
                            </div>
                        </div>
                    </div>
                </div>
                @include('layouts.rightpanel')
            </div>
        </div>
    </section>
@endsection
