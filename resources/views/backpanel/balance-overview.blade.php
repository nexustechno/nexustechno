@extends('layouts.app')
@section('content')
<?php 
use App\setting;
use App\User;
use App\CreditReference;
$settings = ""; $balance=0;

 $settings = CreditReference::where('player_id',$chkid)->first();
    $balance=$settings['available_balance_for_D_W'];

?>

<section>
    <div class="container">
        <div class="main-wrapper">
            <div class="row">
                <div class="col-lg-3 col-md-3 col-sm-12 pl-0">
                    @include('backpanel/downline-account-menu')
                </div>
                <div class="col-lg-9 col-md-9 col-sm-12">
                    <div class="pagetitle text-color-blue-2">
                        <h1>Account Details</h1>

                        <div class="dashboard-right-pannel">
                            <div class="white-bg white-wrap mt-20">
                                <div class="row">
                                    <div class="col-lg-3 col-md-4 col-sm-12 acc-balance">
                                        <h2 class="text-color-blue-2"> Your Balances </h2>
                                        <p class="text-color-blue-light"> <?php echo round($balance,2) ;?><span class="text-color-grey"> PTH </span> </p>
                                    </div>
                                    <div class="col-lg-7 col-md-8 col-sm-12 acc-balance2">
                                        <h2 class="text-color-blue-2"> Welcome, </h2>
                                        <p>
                                            View your account details here. You can manage funds, review and change your settings and see the performance of your betting activity.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection