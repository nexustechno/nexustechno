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
                <div class="middle-wraper">
                    <div class="casino_result_section new_casino">
                    <div class="our_casino_list casino">
                        <div class="row">
                        @foreach($casino as $casinos)
                            <?php
                            $class="disabled-link";
                            if($casinos->status==1){
                                $class="";
                            }
                             ?>

                            <div class="col-6 col-sm-3">
                                <div class="casino_list_items">
                                    <a class="{{$class}}" href="{{route('casinoDetail',[$casinos->id,$casinos->casino_name])}}">
                                        <img src="{{$casinos->image_url}}" alt="img">
                                    </a>
                                    <dl class="entrance-title"><dt>{{ $casinos->casino_title }}</dt><dd> <span class="blink_me"> Play Now </span> </dd></dl>
                                </div>
                            </div>
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
