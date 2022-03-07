@extends('layouts.app')
@section('content')
<?php 
$loginuser = Auth::user(); 
use App\User;
?>
<section class="myaccount-section">
    <div class="container">
        <div class="row">
            <div class="col-md-12 pl-0">
                <div class="downline-block">
                    <div class="search-wrap">
                        <svg width="19" height="19" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12.547 11.543H12l-.205-.172a4.539 4.539 0 001.06-2.914A4.442 4.442 0 008.41 4C5.983 4 4 5.989 4 8.457a4.442 4.442 0 004.445 4.457c1.094 0 2.12-.411 2.905-1.062l.206.171v.548L14.974 16 16 14.971l-3.453-3.428zm-4.102 0a3.069 3.069 0 01-3.077-3.086 3.068 3.068 0 013.077-3.086 3.069 3.069 0 013.076 3.086 3.069 3.069 0 01-3.076 3.086z" fill="rgb(30,30,30"></path>
                        </svg>
                        <div>
                            <input class="search-input navy-light-bg" type="text" name="userId" id="userId" placeholder="Find member...">
                            <button class="search-but yellow-bg1" id="searchUserId">Search</button>
                        </div>
                    </div>
                    <ul class="agentlist">
                        <li class="lastli"><a><span class="orange-bg text-color-white">{{$user->agent_level}}</span><strong>{{$user->user_name}}</strong></a></li>                        
                    </ul>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-12 pl-0">
                @include('backpanel/downline-account-menu')
            </div>
            <div class="col-lg-9 col-md-9 col-sm-12">
                <div class="pagetitle text-color-blue-2">
                    <h1>Transaction History</h1>
                </div>
                <div class="in_play_tabs-2 mb-0">
                <div class="summery-table mt-3">
                   <table class="table custom-table" id="table">
                        <thead>
                            <tr class="light-grey-bg">
                                <th>Date/Time</th>
                                <th class="text-right">Deposit</th>
                                <th class="text-right">Withdraw</th>
                                <th class="text-right">Balance</th>
                                <th class="text-right">Remark</th>
                                <th class="text-right">From/To</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $i = 0; $previousValue = null; $prev_bal=0; $chk_ori=0; $closing_balance=0; $next_row_balance=0; $balance=0; @endphp
                            
                            @foreach($credit as $data)
                                <?php 
                                    $from_data = User::where('id',$data->parent_id)->first();
                                    $todatasds = User::where('id',$data->child_id)->first();
                                ?>
                                <tr>
                                    <td>{{$data->created_at}}</td>
                                    <td class="text-color-green text-right">
                                        <?php 
                                        if($data->balanceType == 'DEPOSIT'){
                                            echo $data->amount;
                                        }
                                        ?>
                                    </td>
                                    <td class="text-color-red text-right">
                                        <?php 
                                        if($data->balanceType == 'WITHDRAW'){
                                            echo $data->amount;
                                        }
                                        ?>
                                    </td>
                                    <td class="text-right">
                                        <?php 
                                        if ($i == 0){
                                            $prev_bal=$balance; 

                                            if($data->balanceType == 'DEPOSIT'){
                                                $closing_balance=$player_balance;
                                                echo $closing_balance;
                                                $next_row_balance=$player_balance-$data->amount;
                                            }

                                            if($data->balanceType == 'WITHDRAW'){
                                                $closing_balance=$player_balance;
                                                echo $closing_balance;
                                                $next_row_balance =$player_balance+$data->amount;
                                            }
                                        }
                                        else{
                                            if($data->balanceType == 'DEPOSIT'){
                                                $closing_balance=$player_balance-$data->amount;
                                                echo $next_row_balance;
                                                $next_row_balance=$next_row_balance-$data->amount;
                                            }
                                            if($data->balanceType == 'WITHDRAW'){
                                                $closing_balance=$player_balance+$data->amount;
                                                echo $next_row_balance;
                                                $next_row_balance=$next_row_balance+$data->amount;
                                            }
                                        }
                                        ?>
                                    </td>
                                    <td class="text-right">{{$data->extra}}</td>
                                    <?php
                                    $uname=''; 
                                    if(!empty($todatasds)){
                                        $uname=$todatasds->user_name;
                                    }
                                    ?>
                                    <td class="text-right">{{$from_data->user_name}}  <i class="fas fa-caret-right text-color-grey"></i> {{$uname}}</td>
                                </tr>
                                @php 
                                $i++; 
                                $previousValue = $data;
                                @endphp
                            @endforeach
                        </tbody>
                    </table>
                </div>
                </div>
               
            </div>
        </div>
    </div>
</section>
@endsection