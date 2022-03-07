@extends('layouts.front_layout')
@section('content')
<?php
use App\setting;
use App\User;
use App\CreditReference;
$settings = ""; $balance=0;
$loginuser = Session::get('playerUser');
$getUser = Session::get('playerUser');
$ttuser = User::where('id',$loginuser->id)->first();
//$settings = setting::latest('id')->first();
$auth_id =Session::get('playerUser')->id;
$auth_type =Session::get('playerUser')->agent_level;
if($auth_type=='COM'){
    $settings = setting::latest('id')->first();
    $balance=$settings->balance;
}
else
{
    $settings = CreditReference::where('player_id',$auth_id)->first();
    $balance=$settings['available_balance_for_D_W'];
}
?>
<section>
    <div class="container-fluid">
        <div class="main-wrapper">

            @include('front.leftpanel-account')

            <div class="dashboard-right-pannel">
                <div class="pagetitle text-color-blue-2">
                    <h1>Account Details</h1>
                </div>
                <div class="row mt-20 profile-wrap">
                    <div class="col-lg-6 col-md-6 col-sm-12 pl-0 pr-0">
                        <div class="profile-block">
                            <div class="grey-bg head text-color-white"> About You </div>
                            <div class="profile-detail white-bg">
                                <div class="profile-main">
                                    <div class="headlabel"> First Name </div>
                                    <div class="headdetail"> {{$user->first_name}} </div>
                                </div>
                                <div class="profile-main">
                                    <div class="headlabel"> Last Name </div>
                                    <div class="headdetail"> {{$user->last_name}}</div>
                                </div>
                                <div class="profile-main">
                                    <div class="headlabel"> Birthday </div>
                                    <div class="headdetail"> -- </div>
                                </div>
                                <div class="profile-main">
                                    <div class="headlabel"> E-mail </div>
                                    <div class="headdetail"> {{$user->email}} </div>
                                </div>
                                <div class="profile-main">
                                    <div class="headlabel"> Password </div>
                                    <div class="headdetail"> *************** <span class="text-color-blue-light "> <a data-toggle="modal" data-target="#mypwd"> Edit <i class="fas fa-pencil-alt"></i> </a> </span> </div>
                                </div>
                                <div class="profile-main">
                                    <div class="headlabel"> Languages </div>
                                    <div class="headdetail">
                                        <select id="lang">
                                            <option value="en">English</option>
                                            <option value="cn">中文</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="profile-block">
                            <div class="grey-bg head text-color-white"> Address </div>
                            <div class="profile-detail white-bg">
                                <div class="profile-main">
                                    <div class="headlabel"> Address </div>
                                    <div class="headdetail"> -- </div>
                                </div>
                                <div class="profile-main">
                                    <div class="headlabel"> Town/City </div>
                                    <div class="headdetail"> -- </div>
                                </div>
                                <div class="profile-main">
                                    <div class="headlabel"> Country </div>
                                    <div class="headdetail"> -- </div>
                                </div>
                                <div class="profile-main">
                                    <div class="headlabel"> Country/State </div>
                                    <div class="headdetail"> -- </div>
                                </div>
                                <div class="profile-main">
                                    <div class="headlabel"> Postcode </div>
                                    <div class="headdetail"> -- </div>
                                </div>
                                <div class="profile-main">
                                    <div class="headlabel"> Timezone </div>
                                    <div class="headdetail"> {{$user->time_zone}} </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 pr-0">
                        <div class="profile-block">
                            <div class="grey-bg head text-color-white"> Contact Details </div>
                            <div class="profile-detail white-bg">
                                <div class="profile-main">
                                    <div class="headlabel"> Primary number </div>
                                    <div class="headdetail"> -- </div>
                                </div>
                            </div>
                        </div>
                        <div class="profile-block">
                            <div class="grey-bg head text-color-white"> Setting </div>
                            <div class="profile-detail white-bg">
                                <div class="profile-main">
                                    <div class="headlabel"> Currency </div>
                                    <div class="headdetail"> {{ $website->currency }} {{$balance}} </div>
                                </div>
                                <div class="profile-main">
                                    <div class="headlabel"> Odds Format </div>
                                    <div class="headdetail"> -- </div>
                                </div>
                            </div>
                        </div>
                        <div class="profile-block">
                            <div class="grey-bg head text-color-white"> Commission </div>
                            <div class="profile-detail white-bg">
                                <div class="profile-main">
                                    <div class="headlabel"> Comm charged </div>
                                    <div class="headdetail"> {{$user->commission}}% </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@include('layouts.footer')
@endsection
