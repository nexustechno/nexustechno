@extends('layouts.front_layout')
@section('content')
<style type="text/css">
    @media only screen and (max-width: 768px) {
      .mobile-multi {
        display: block !important;
      }
    }
    .multiimg img{
        width: 100%;
        border: 1px solid #00000052;
    }
    .multiimg {
        padding: 10px;
    }
</style>
<?php
use App\Match;
use App\MyBets;
use Carbon\Carbon;
use App\User;
$getUserCheck = Session::get('playerUser');
if(!empty($getUserCheck)){
  $gatdata = User::where('id',$getUserCheck->id)->where('check_login',1)->first();
}

if(!empty($gatdata)){
   $auth_id = $gatdata->id; 
}
use App\setting;
$getUser = Session::get('playerUser');
if(!empty($getUserCheck)){
  $getUser = User::where('id',$getUserCheck->id)->where('check_login',1)->first();
}

$settings =setting::first();

 //for match original date and time
        $get_match_type=app('App\Http\Controllers\RestApi')->GetAllMatch();
        $st_criket=array(); $ra_criket=0; $st_soccer=array(); $st_tennis=array(); $ra_soccer=0; $ra_tennis=0;
        foreach($get_match_type as $key2 => $value2)
        {
            $dt=''; $mid=''; $eid=''; 
            foreach (@$value2 as $key3 => $value3) 
            {
                if ($key3 == 'MarketId')
                {
                    $mid=$value3;
                }
                if ($key3 == 'EventId')
                {
                    $eid=$value3;
                }
                if ($key3 == 'StartTime')
                {
                    $dt=$value3;
                }
                if ($key3 == 'SportsId')
                {
                    if($value3==4)
                    {
                        $st_criket[$ra_criket]['StartTime']=$dt;
                        $st_criket[$ra_criket]['EventId']=$mid;
                        $st_criket[$ra_criket]['MarketId']=$eid;
                        $ra_criket++;
                    }
                    else if($value3==2)
                    {
                        $st_tennis[$ra_tennis]['StartTime']=$dt;
                        $st_tennis[$ra_tennis]['EventId']=$mid;
                        $st_tennis[$ra_tennis]['MarketId']=$eid;
                        $ra_tennis++;
                    }
                    else if($value3==1)
                    {
                        $st_soccer[$ra_soccer]['StartTime']=$dt;
                        $st_soccer[$ra_soccer]['EventId']=$mid;
                        $st_soccer[$ra_soccer]['MarketId']=$eid;
                        $ra_soccer++;
                    }
                }
            }
        }
?>
@if(!empty($gatdata))
<section>
    <div class="container-fluid">
        @if($errors->any())
        <h4>{{$errors->first()}}</h4>
        @endif
        <div class="main-wrapper">
            @include('layouts.leftpanel')
            <div class="middle-section">  
                @if(!empty($getUser))
                    @if(!empty($settings->user_msg))
                        <div class="news-addvertisment black-gradient-bg text-color-white">
                            <h4>News</h4>
                            <marquee scrollamount="3">
                                <a href="#" class="text-color-blue">{{$settings->user_msg}}</a>
                            </marquee>
                        </div>
                    @endif
                @endif
                <div class="middle-wraper">
                    <div class="home-carousel owl-carousel owl-theme">
                        @foreach($banner as $banners)
                        <div class="itemslider">
                            <img src="{{ URL::to('asset/upload')}}/{{$banners->banner_image}}" alt="Image">
                        </div>
                        @endforeach
                    </div>
                    <div class="maintabs">
                        <h3 class="yellow-bg2 text-color-black2 highligths-txt">Multi Market</h3>
                        <ul class="nav nav-tabs yellow-gradient-bg2" role="tablist">
                            <?php $count=1; ?>
                            @foreach($sports as $sport)
                            <li class="nav-item gettab{{$count}}" data-id="{{$sport->sport_name}}">
                                <a class="nav-link text-color-white darkblue-bg1 {{$sport->sport_name}}" href="#{{$sport->sport_name}}" role="tab" data-toggle="tab">{{$sport->sport_name}}</a>
                            </li>
                            <?php $count++; ?>
                            @endforeach
                        </ul>
                        <div class="tab-content" id="tabdatadiv">
                            <?php 
                            $i=0;                           
                            ?>
                            @foreach($sports as $sport)


                            <div role="tabpanel" class="tab-pane @if($i==0) active @endif tabname{{$i}}" id="{{$sport->sport_name}}">
                                <div class="programe-setcricket">
                                    <div class="firstblock-cricket lightblue-bg1">
                                        <span class="fir-col1"></span>
                                        <span class="fir-col2">1</span>
                                        <span class="fir-col2">X</span>
                                        <span class="fir-col2">2</span>
                                        <span class="fir-col3"></span>
                                    </div>
                            <?php
                            $match = MyBets::join('match','match.event_id','=','my_bets.match_id')->where('sportID',$sport->sId)->where('user_id',$auth_id)->where('active',1)->where('winner',null)->groupBy('my_bets.match_id')->get(); 
                            $count = count($match);
                            ?>
                            @if($count !=0)
                                @foreach($match as $matches)

                                 <?php 
                            $match_date=''; $dt='';

                            if($sport->sId==4){
                            $key = array_search($matches->event_id, array_column($st_criket, 'MarketId'));
                            if($key)
                                // ss for incorrect index
                                //$dt=$st_criket[$key+1]['StartTime'];  
                                $dt=$st_criket[$key]['StartTime'];

                            $new=explode("T",$dt);
                            $first=@$new[0];
                            $second =@$new[1];
                            $second=explode(".",$second);
                            $timestamp = $first. " ".@$second[0];

                            $date = Carbon::parse($timestamp);
                            $date->addMinutes(330);
                            
                            if (Carbon::parse($date)->isToday()){
                                $match_date = date('d-m-Y h:i A',strtotime($date));
                            }
                            else if (Carbon::parse($date)->isTomorrow())
                                $match_date ='Tomorrow '.date('d-m-Y h:i A',strtotime($date));
                            else
                                $match_date =date('d-m-Y h:i A',strtotime($date));
                        }elseif($sport->sId==2){
                            $key = array_search($matches->event_id, array_column($st_tennis, 'MarketId'));
                            if($key)
                                // ss for incorrect index
                                //$dt=$st_criket[$key+1]['StartTime'];  
                                $dt=$st_tennis[$key]['StartTime'];

                            $new=explode("T",$dt);
                            $first=@$new[0];
                            $second =@$new[1];
                            $second=explode(".",$second);
                            $timestamp = $first. " ".@$second[0];

                            $date = Carbon::parse($timestamp);
                            $date->addMinutes(330);
                            
                            if (Carbon::parse($date)->isToday()){
                                $match_date = date('d-m-Y h:i A',strtotime($date));
                            }
                            else if (Carbon::parse($date)->isTomorrow())
                                $match_date ='Tomorrow '.date('d-m-Y h:i A',strtotime($date));
                            else
                                $match_date =date('d-m-Y h:i A',strtotime($date));

                        }elseif($sport->sId==1){
                            $key = array_search($matches->event_id, array_column($st_soccer, 'MarketId'));
                            if($key)
                                // ss for incorrect index
                                //$dt=$st_criket[$key+1]['StartTime'];  
                                $dt=$st_soccer[$key]['StartTime'];

                            $new=explode("T",$dt);
                            $first=@$new[0];
                            $second =@$new[1];
                            $second=explode(".",$second);
                            $timestamp = $first. " ".@$second[0];

                            $date = Carbon::parse($timestamp);
                            $date->addMinutes(330);
                            
                            if (Carbon::parse($date)->isToday()){
                                $match_date = date('d-m-Y h:i A',strtotime($date));
                            }
                            else if (Carbon::parse($date)->isTomorrow())
                                $match_date ='Tomorrow '.date('d-m-Y h:i A',strtotime($date));
                            else
                                $match_date =date('d-m-Y h:i A',strtotime($date));

                        }
                                        
                            $match_date=$match_date;
                                   

                            ?>

                                    <div class="secondblock-cricket white-bg" id="div_mdata">
                                       <span class="fir-col1">
                                            <a href="{{route('matchDetail',$matches->id)}}" class="text-color-blue-light">{{$matches->match_name}} </a>
                                            <div>{{$match_date}}</div>
                                        </span>
                                    </div>
                                @endforeach
                            @else
                            <div class="secondblock-cricket white-bg" id="div_mdata">
                               <span class="fir-col1">
                                    <a href="javascript:void();" class="text-color-blue-light">No Record Found</a>
                                </span>
                            </div>
                            @endif
                                @php $i++; @endphp
                                </div>
                            </div>
                            @endforeach
                        </div>
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
            @include('layouts.rightpanel')
        </div>
    </div>
<input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}">
</section>
@endif

@if(empty(Session::get('playerUser')))
<section class="mobile-multi" style="display: none;">
    <div class="container-fluid">
        <div class="main-wrapper">
            <div class="multiimg">
                <img src="{{ URL::to('asset/front/img/multimarket.JPEG') }}">
            </div>
        </div>
    </div>
</section>
@endif
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
    var gettab =  $('.gettab1').attr("data-id");
    $("."+gettab).addClass("active");
});
</script>
@include('layouts.footer')
@endsection