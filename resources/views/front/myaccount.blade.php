@extends('layouts.front_layout')
@section('content')
<?php 
use App\setting;
use App\User;
$settings = setting::first();
$getUserCheck = Session::get('playerUser');
$timeZone='';
$userNme='';
if(!empty($getUserCheck)){
  $getUser = User::where('id',$getUserCheck->id)->where('check_login',1)->first();
  if(!empty($getUser)){
    $userNme = $getUser->user_name;
  $timeZone=$getUser->time_zone;
  }
  
}
?>
<style>
    body {
        overflow: hidden;
    }
</style>
<section>
    <div class="container-fluid">
        <div class="main-wrapper">
            <div class="news-addvertisment black-gradient-bg text-color-white">
                <h4>News</h4>
                <marquee scrollamount="3">
                    <a href="#" class="text-color-blue">{{$settings->user_msg}}</a>
                </marquee>
            </div>
            <div class="middle-wraper">
                <div class="account_wrap">
                    <div class="path_wrap black-bg1 text-color-white">
                        <span class="name_mac"><img src="{{ URL::to('asset/front/img/user-round.svg') }}" alt="Image"> {{$userNme}}</span>
                        <span class="timezone_mac">{{$timeZone}}</span>
                    </div>
                    <ul class="acmenu_list white-bg">
                        <li>
                            <a href="{{route('myprofile')}}" class="text-color-blue-light">My Profile <img src="{{ URL::to('asset/front/img/arrow-right.svg') }}" alt=""></a>
                        </li>
                        <li>
                            <a href="{{route('balance-overview')}}" class="text-color-blue-light">Balance Overview <img src="{{ URL::to('asset/front/img/arrow-right.svg') }}" alt=""></a>
                        </li>
                        <li>
                            <a href="{{route('account-statement')}}" class="text-color-blue-light">Account Statement <img src="{{ URL::to('asset/front/img/arrow-right.svg') }}" alt=""></a>
                        </li>
                        <li>
                            <a href="{{route('my-bets')}}" class="text-color-blue-light">My Bets <img src="{{ URL::to('asset/front/img/arrow-right.svg') }}" alt=""></a>
                        </li>
                        <li>
                            <a href="{{route('my-bets')}}" class="text-color-blue-light">Bets History <img src="{{ URL::to('asset/front/img/arrow-right.svg') }}" alt=""></a>
                        </li>
                        <li>
                            <a href="{{route('my-bets')}}" class="text-color-blue-light">Profit & Loss <img src="{{ URL::to('asset/front/img/arrow-right.svg') }}" alt=""></a>
                        </li>
                        <li>
                            <a href="{{route('activity-log')}}" class="text-color-blue-light">Activity Log <img src="{{ URL::to('asset/front/img/arrow-right.svg') }}" alt=""></a>
                        </li>
                        {{--<li>
                            <a href="{{route('casinoreport')}}" class="text-color-blue-light">Casino Result <img src="{{ URL::to('asset/front/img/arrow-right.svg') }}" alt=""></a>
                        </li>--}}
                    </ul>
                    
                    <a href="{{ route('frontLogout') }}"  class="asmenu_red red-gradient-bg text-color-white">Logout <img src="{{ URL::to('asset/front/img/logout-white.svg') }}" alt=""></a>
                    
                </div>
            </div>
        </div>
    </div>
</section>
@endsection