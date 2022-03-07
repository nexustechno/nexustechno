@extends('layouts.front_layout')
@section('content')
<?php
use App\Match;
use App\setting;
use App\User;
$getUserCheck = Session::get('playerUser');
$getUser='';
if(!empty($getUserCheck)){
  $getUser = User::where('id',$getUserCheck->id)->where('check_login',1)->first();
}
$settings =setting::first();
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
                        <a class="res_search black-gradient-bg4"><img src="{{ URL::to('asset/front/img/search.svg') }}" alt=""></a>
                        <div class="mslide_nav">
                            <ul>
                                <li class="menu_casino">
                                    <span class="tagnew">New</span>
                                    <a href="{{route('casino')}}" class="text-color-white purple-blue-gradient-bg">
                                        <img src="{{ URL::to('asset/front/img/mslide_menu/mslide-icon-casino.svg') }}" alt=""> Casino
                                    </a>
                                </li>                             
                                <li>
                                    <span class="highlight-red grey-gradient-bg"> <span class="text-color-white red-gradient-bg cricketCount"></span> </span>
                                    <a href="{{route('cricket')}}" class="text-color-black1">
                                        <img src="{{ URL::to('asset/front/img/mslide_menu/mslide-icon-cricket-black.svg') }}" alt="" class="dactimg">
                                        <img src="{{ URL::to('asset/front/img/mslide_menu/mslide-icon-cricket-yellow.svg') }}" alt="" class="actimg">
                                        Cricket
                                    </a>
                                </li>
                                <li>
                                    <span class="highlight-red grey-gradient-bg"> <span class="text-color-white red-gradient-bg soccerCount"></span> </span>
                                    <a href="{{route('soccer')}}" class="text-color-black1">
                                        <img src="{{ URL::to('asset/front/img/mslide_menu/mslide-icon-soccer-black.svg') }}" alt="" class="dactimg">
                                        <img src="{{ URL::to('asset/front/img/mslide_menu/mslide-icon-soccer-yellow.svg') }}" alt="" class="actimg">
                                        Soccer
                                    </a>
                                </li>
                                <li class="active">
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
                            {!! $cricket_html !!}
                        </div>
                    
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
                </div>
            </div>
            @include('layouts.rightpanel')
        </div>
    </div>
</section>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script>
$(document).ready(function(){
     var getueer='<?php echo $getUserCheck; ?>';
     if(getueer != ''){
	setInterval(function(){
	   var _token = $("input[name='_token']").val();
			$.ajax({
			type: "POST",
			url: '{{route("getmatchdetailsOfTennis")}}',
			data: {_token:_token},
			beforeSend:function(){
           		$('#site_statistics_loading').show();
            },
            complete: function(){
            	$('#site_statistics_loading').hide();
            },
			success: function(data){
				$(".programe-setcricket").html(data);
			}
		});
	},1000);
}
});
</script>
@endsection