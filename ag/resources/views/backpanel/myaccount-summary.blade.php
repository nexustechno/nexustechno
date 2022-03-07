@extends('layouts.app')
@section('content')

<?php
use App\setting;
use App\User;
use App\CreditReference;
$settings = ""; $balance=0;
$loginuser = Auth::user();
$ttuser = User::where('id',$loginuser->id)->first();
$auth_id = Auth::user()->id;
$auth_type = Auth::user()->agent_level;
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

	<div class="container">
    	<div class="breadcrumbs">
        	<ul>
            	<li> <a href="index.php" class="text-color-black" > <span class="red-bg text-color-white">{{$user->agent_level}}</span> {{$user->user_name}}</a> </li>
            </ul>
    	</div>
    </div>
</section>
<section class="myaccount-section">
	<div class="container">
		<div class="row">
			<div class="col-lg-3 col-md-3 col-sm-12 pl-0">
				@include('backpanel/account-menu-sidebar')
			</div>
			<div class="col-lg-9 col-md-9 col-sm-12">
				<div class="pagetitle text-color-blue-2">
					<h1> Account Summary </h1>
				</div>
				<div class="white-bg mt-20">
					<div class="row">
						<div class="col-lg-3 col-md-4 col-sm-12 acc-balance">
							<h2 class="text-color-blue-2"> Your Balances </h2>
							<p class="text-color-blue-light"> {{$balance}} <span class="text-color-grey"> {{ $website->currency }} </span> </p>
						</div>
					</div>
				</div>
			</div>
		</div>
    </div>
</section>
@endsection
