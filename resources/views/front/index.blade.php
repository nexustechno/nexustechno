@extends('layouts.front_layout')
<?php
use App\User;
$getUserCheck = session('playerUser');
if(!empty($getUserCheck)){
$getUser = User::where('id',$getUserCheck->id)->where('check_login',1)->first();
}
?>
@section('content')

<?php
//check is mobile or desktop
$useragent = $_SERVER['HTTP_USER_AGENT'];
$iPod = stripos($useragent, "iPod");
$iPad = stripos($useragent, "iPad");
$iPhone = stripos($useragent, "iPhone");
$Android = stripos($useragent, "Android");
$iOS = stripos($useragent, "iOS");

$DEVICE = ($iPod||$iPad||$iPhone||$Android||$iOS);
$is_agent='';
if (!$DEVICE) {
    $is_agent='desktop';
}
else{
    $is_agent='mobile';
}
//end for check is mobile or desktop
?>
<style type="text/css">
    .disabled-link {
    pointer-events: none;
}
</style>
<section>
    <div class="container-fluid">
        <div class="main-wrapper">
            <div class="row justify-content-md-center casino-wrap">
                <div class="col"> </div>
                <?php
                $coldiv='';
                if($is_agent=='mobile')
                    $coldiv = "col-12";
                else
                    $coldiv = "col-9";
                ?>
                <div class="{{$coldiv}}">
                    <div class="casino_result_section new_casino">
                        @if(!empty($getUser))
                            @if(!empty($settings->user_msg))
                                <div class="news-addvertisment ml-1 mr-1 black-gradient-bg text-color-white">
                                    <h4>News</h4>
                                    <marquee>
                                        <a href="#" class="text-color-blue">{{$settings->user_msg}}</a>
                                    </marquee>
                                </div>
                            @endif
                        @endif
                        <div class="middle-wraper1">
                            <div class="home-carousel pl-1 pr-1 owl-reponsive owl-carousel owl-theme">
                                @foreach($banner as $banners)
                                    <div class="itemslider">
                                        <img src="{{ URL::to('ag/asset/upload')}}/{{$banners->banner_image}}" alt="Image">
                                    </div>
                                @endforeach
                                <!-- <div class="itemslider">
                                    <img src="{{ URL::to('asset/front/img/slider/slider-2.png') }}" alt="Image">
                                </div>
                                <div class="itemslider">
                                    <img src="{{ URL::to('asset/front/img/slider/slider-3.jpg') }}" alt="Image">
                                </div> -->
                            </div>

                            @php
                                $images = \App\Dashboard::orderBy("id","ASC")->get();
                            @endphp

                            <div class="our_casino_list1 mt-1">
                            	<div class="row">

                                    @foreach($images as $key => $image)
                                        @if($image->width_type == 'column12')
                                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 p-1">
                                        @elseif($image->width_type == 'column6')
                                            <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 p-1">
                                        @elseif($image->width_type == 'column3')
                                            <div class="col-lg-3 col-md-6 col-sm-6 col-6  p-1">
                                        @endif
                                            <div class="casino_list_items">
                                                <a href="{{$image->link}}">
                                                    @if($key == 0)
                                                        <dl id="onLiveBoard" class="on_live"><dt><p class="live_icon"><span></span> LIVE</p></dt><dd id="onLiveCount_CRICKET"><p>Cricket</p><span class="cricketCount" id=""></span></dd><dd id="onLiveCount_SOCCER"><p>Soccer</p><span class="soccerCount" id=""></span></dd><dd id="onLiveCount_TENNIS"><p>Tennis</p><span class="tennisCount" id=""></span></dd></dl>
                                                    @endif
                                                    <img src="{{ URL::to('ag/asset/upload')}}/{{$image->file_name}} " alt="">
                                                    <dl class="entrance-title"><dt>{{$image->title}}</dt><dd> <span class="blink_me"> Play Now </span> </dd></dl>
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>  <!--    middle-section -->
                    @include('front.social-footer-links')
                </div>
                <div class="col"></div>
            </div>
        </div>
    </div>
</section>

<div class="modal golden_modal1 fade" id="exampleModal2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content light-grey-bg-2">
            <div class="modal-header blue-dark-bg-3">
                <h5 class="modal-title text-color-yellow-1" id="exampleModalLabel">Rules</h5>
                <button type="button" class="close text-color-grey-1" data-dismiss="modal" aria-label="Close">??</button>
            </div>
            <div class="modal-body">
                <div class="p-5 modal-plus-block text-center">
                    <img src="{{ URL::to('img/trap.jpg') }}" class="img-fluid trapmodal_img">
                </div>
            </div>
        </div>
    </div>
</div>
@include('layouts.footer')
@endsection
