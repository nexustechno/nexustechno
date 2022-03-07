@extends('layouts.front_layout')
@section('content')
<?php
use App\setting;
use App\User;
use App\CreditReference;
$settings = ""; $balance=0;
$loginuser = Session::get('playerUser');
$ttuser = User::where('id',$loginuser->id)->first();
$auth_id = Session::get('playerUser')->id;
$auth_type = Session::get('playerUser')->agent_level;
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
                    <h1>Summary</h1>
                </div>
                <div class="white-bg white-wrap mt-20">
					<div class="row">
						<div class="col-lg-3 col-md-4 col-sm-12 acc-balance">
							<h2 class="text-color-blue-2"> Your Balances </h2>
							<p class="text-color-blue-light"> {{$balance}} <span class="text-color-grey"> {{ $website->currency }} </span> </p>
						</div>
                        <div class="col-lg-7 col-md-8 col-sm-12 acc-balance2">
							<h2 class="text-color-blue-2"> Welcome, </h2>
							<p>
                                View your account details here. You can manage funds, review and change your settings and see the performance of your betting activity.
                            </p>
						</div>
					</div>
				</div>
                <div class="summery-table mt-3">
                    <table class="table custom-table">
                        <thead>
                            <tr class="light-grey-bg">
                                <th>Date</th>
                                <th>Transaction №</th>
                                <th>Debits</th>
                                <th>Credits</th>
                                <th>Balance</th>
                                <th>Remarks</th>
                                <th class="text-right">From/To</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
@include('layouts.footer')
@endsection
