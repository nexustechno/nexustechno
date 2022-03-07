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
                        <div class="news-addvertisment black-gradient-bg text-color-white">
                            <h4>News</h4>
                            <marquee>
                                <a href="#" class="text-color-blue">{{$settings->user_msg}}</a>
                            </marquee>
                        </div>
                    @endif
                @endif
                        <div class="middle-wraper1">
                            <div class="home-carousel owl-reponsive owl-carousel owl-theme">
                                @foreach($banner as $banners)
                                <div class="itemslider">
                                    <img src="{{ URL::to('asset/upload')}}/{{$banners->banner_image}}" alt="Image">
                                </div>
                                @endforeach
                                <!-- <div class="itemslider">
                                    <img src="{{ URL::to('asset/front/img/slider/slider-2.png') }}" alt="Image">
                                </div>
                                <div class="itemslider">
                                    <img src="{{ URL::to('asset/front/img/slider/slider-3.jpg') }}" alt="Image">
                                </div> -->
                            </div>
                            <div class="our_casino_list">
                            	<div class="row justify-content-md-center">
                                	<div class="col-xl-6 col-lg-6 col-md-12 col-sm-12 pl-0">
                                    	<div class="casino_list_items">
                                            <a href="{{route('cricket')}}">
                                            <dl id="onLiveBoard" class="on_live"><dt><p class="live_icon"><span></span> LIVE</p></dt><dd id="onLiveCount_CRICKET"><p>Cricket</p><span class="cricketCount" id=""></span></dd><dd id="onLiveCount_SOCCER"><p>Soccer</p><span class="soccerCount" id=""></span></dd><dd id="onLiveCount_TENNIS"><p>Tennis</p><span class="tennisCount" id=""></span></dd></dl>
                                            <img src="{{ URL::to('asset/front/img/c1.png') }} " alt="">
                                            <dl class="entrance-title"><dt>Bet Games</dt><dd> <span class="blink_me"> Play Now </span> </dd></dl>
                                        </a>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-6 col-sm-6 pl-0 col-6">
                                    	<div class="casino_list_items">
                                            <a href="javascript:void(0);">
                                                <img src="{{ URL::to('asset/front/img/c2.jpg') }} " alt="">
                                                <dl class="entrance-title"><dt>Bet Games</dt><dd> <span class="blink_me"> Play Now </span> </dd></dl>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-md-6 col-sm-6 pl-0 col-6">
                                    	<div class="casino_list_items">
                                            <a href="javascript:void(0);">
                                                <img src="{{ URL::to('asset/front/img/c3.jpg') }} " alt="">
                                                <dl class="entrance-title"><dt>Bet Games</dt><dd> <span class="blink_me"> Play Now </span> </dd></dl>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="row justify-content-md-center">
                                @foreach($casino as $casinos)

                                <?php
                                $class="disabled-link";
                                if($casinos->status==1){
                                    $class="";
                                }
                                ?>
                                <div class="col-lg-3 col-md-6 col-sm-6 pl-0 col-6">
                                    <div class="casino_list_items">
                                        @if (!empty($getUser))
                                        <a class="{{$class}}" href="{{route($casinos->casino_name,$casinos->id)}}">
                                            <img src="{{ URL::to('asset/upload') }}/{{$casinos->casino_image}}" alt="img">
                                            <dl class="entrance-title"><dt>{{ucfirst($casinos->casino_name)}}</dt>
                                            <dd> <span class="blink_me"> Play Now </span> </dd></dl>
                                        </a>
                                        @else
                                        <a href="javascript:void(0);">
                                            <img src="{{ URL::to('asset/upload') }}/{{$casinos->casino_image}}" alt="img">
                                            <dl class="entrance-title"><dt>{{ucfirst($casinos->casino_name)}}</dt>
                                            <dd> <span class="blink_me"> Play Now </span> </dd></dl>
                                        </a>
                                        @endif
                                    </div>
                                </div>
                                @endforeach 
                                <div class="col-lg-3 col-md-6 col-sm-6 pl-0 col-6">
                                    <div class="casino_list_items">
                                        <a href="javascript:void(0);">
                                            <img src="{{ URL::to('asset/front/img/c4.jpg') }}" alt="img">
                                            <dl class="entrance-title"><dt>Bet Games</dt><dd> <span class="blink_me"> Play Now </span> </dd></dl>
                                        </a>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-6 pl-0 col-6">
                                    <div class="casino_list_items">
                                        <a href="javascript:void(0);">
                                            <img src="{{ URL::to('asset/front/img/c5.jpeg') }}" alt="img">
                                            <dl class="entrance-title"><dt>Bet Games</dt><dd> <span class="blink_me"> Play Now </span> </dd></dl>
                                        </a>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-6 pl-0 col-6">
                                    <div class="casino_list_items">
                                        <a href="javascript:void(0);">
                                            <img src="{{ URL::to('asset/front/img/c6.jpeg') }}" alt="img">
                                            <dl class="entrance-title"><dt>Bet Games</dt><dd> <span class="blink_me"> Play Now </span> </dd></dl>
                                        </a>
                                    </div>
                                </div> 
                                </div> <!-- row -->
                            </div>
                        </div>
                    </div>  <!--    middle-section -->  
                    <div class="foter-wraper">
                        <div class="social-block white-bg1">
                            <ul class="nav nav-pills" id="pills-tab" role="tablist" data-mouse="hover">
                                <li class="nav-item">
                                    <a class="nav-link bg-transparent email active" id="pills-email-tab" data-toggle="pill" href="#pills-email" role="tab" aria-controls="pills-email" aria-selected="true">
                                        <img src="{{ URL::to('asset/front/img/login/email.svg') }}" title="Email">
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link bg-transparent whatsapp" id="pills-whatsapp-tab" data-toggle="pill" href="#pills-whatsapp" role="tab" aria-controls="pills-whatsapp" aria-selected="false">
                                        <img src="{{ URL::to('asset/front/img/login/whatsapp.svg') }}" title="WhatsApp">
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link bg-transparent telegram" id="pills-telegram-tab" data-toggle="pill" href="#pills-telegram" role="tab" aria-controls="pills-telegram" aria-selected="false">
                                        <img src="{{ URL::to('asset/front/img/login/telegram.svg') }} " title="Telegram">
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link bg-transparent skype" id="pills-skype-tab" data-toggle="pill" href="#pills-skype" role="tab" aria-controls="pills-skype" aria-selected="false">
                                        <img src="{{ URL::to('asset/front/img/login/skype.svg') }} " title="Skype">
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link bg-transparent instagram" id="pills-instagram-tab" data-toggle="pill" href="#pills-instagram" role="tab" aria-controls="pills-instagram" aria-selected="false">
                                        <img src="{{ URL::to('asset/front/img/login/instagram.svg') }}" title="Instagram">
                                    </a>
                                </li>
                            </ul>
                            @if(!empty($socialdata))
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="pills-email" role="tabpanel" aria-labelledby="pills-email-tab">
                                    <a class="text-color-black" href="mailto:{{$socialdata->em1}}">{{$socialdata->em1}}</a>
                                    <a class="text-color-black" href="mailto:{{$socialdata->em2}}">{{$socialdata->em2}}</a>
                                    <a class="text-color-black" href="mailto:{{$socialdata->em3}}">{{$socialdata->em3}}</a>
                                </div>
                                <div class="tab-pane fade" id="pills-whatsapp" role="tabpanel" aria-labelledby="pills-whatsapp-tab">
                                    <a class="text-color-black" href="">{{$socialdata->wa1}}</a>
                                    <a class="text-color-black" href="">{{$socialdata->wa2}}</a>
                                    <a class="text-color-black" href="">{{$socialdata->wa3}}</a>
                                </div>
                                <div class="tab-pane fade" id="pills-telegram" role="tabpanel" aria-labelledby="pills-telegram-tab">
                                    <a class="text-color-black">{{$socialdata->tl1}}</a>
                                    <a class="text-color-black">{{$socialdata->tl2}}</a>
                                    <a class="text-color-black">{{$socialdata->tl3}}</a>
                                </div>
                                <div class="tab-pane fade" id="pills-skype" role="tabpanel" aria-labelledby="pills-skype-tab">
                                    <a class="text-color-black">{{$socialdata->sk1}}</a>
                                    <a class="text-color-black">{{$socialdata->sk2}}</a>
                                    <a class="text-color-black">{{$socialdata->sk2}}</a>
                                </div>
                                <div class="tab-pane fade" id="pills-instagram" role="tabpanel" aria-labelledby="pills-instagram-tab">
                                    <a class="text-color-black" target="_blank">{{$socialdata->ins1}}</a>
                                    <a class="text-color-black" target="_blank">{{$socialdata->ins2}}</a>
                                    <a class="text-color-black" target="_blank">{{$socialdata->ins3}}</a>
                                </div>
                            </div>
                            @endif
                        </div>
                        <div class="brand-wrap d-none d-lg-block">
                            <h3 class="text-color-rgb1"><span>Powered by</span> <img src="{{ URL::to('asset/front/img/betfair.png') }}"> </h3>
                        </div>
                        <div class="browser-wraper text-color-rgb2">
                            <i class="fab fa-chrome"></i>
                            <i class="fab fa-firefox-browser"></i> <br>
                            Our website works best in the newest and last prior version of these browsers: <br>
                            Google Chrome.
                        </div>
                        <div class="foter-links">
                            <ul>
                                <li><a class="text-color-rgb2">Privacy Policy</a></li>
                                <li><a class="text-color-rgb2">Terms and Conditions</a></li>
                                <li><a class="text-color-rgb2">Rules and Regulations</a></li>
                                <li><a class="text-color-rgb2">KYC</a></li>
                                <li><a class="text-color-rgb2">Responsible Gaming</a></li>
                                <li><a class="text-color-rgb2">About Us</a></li>
                                <li><a class="text-color-rgb2">Self-exclusion Policy</a></li>
                                <li><a class="text-color-rgb2">Underage Policy</a></li>
                            </ul>
                        </div>
                        <div class="extrab_wrap d-lg-none">
                            <div class="brand-wrap">
                                <h3 class="text-color-rgb1"><span>Powered by</span> <img src="{{ URL::to('asset/front/img/betfair.png') }}"> </h3>
                            </div>
                            <div class="app_android">
                                <a href="#"><img src="{{ URL::to('asset/front/img/app-android.png') }}" alt=""></a>
                                <p>v1.07 - 2020-11-11 - 8.2MB</p>
                            </div>
                        </div>
                    </div> 
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
                <button type="button" class="close text-color-grey-1" data-dismiss="modal" aria-label="Close">×</button>
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