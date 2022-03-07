@extends('layouts.app')
@section('content')
<section>
<?php
$loginuser = Auth::user(); 
use App\CreditReference; 
use App\User;
?>
@if($errors->any())
<h4>{{$errors->first()}}</h4>
@endif

<style type="text/css">
.betloaderimage1{
    top: 50%;
    height: 135px;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 5px 10px rgb(0 0 0 / 50%);
    padding-top: 30px;
    z-index: 50;
    position: absolute;
    left: 50%;
    width: 190px;
    margin-left: -95px;
}

.loading1 img {
    background-position: -42px -365px;
    height: 51px;
    width: 51px;
}

.loading1 li{
    list-style: none;
    text-align: center;
    font-size: 11px;
}
</style>
<div id="site_bet_loading1" class="betloaderimage1 loader-style1" style="display: none" >
    <ul class="loading1">
        <li>
            <img src="/asset/front/img/loaderajaxbet.gif">
        </li>
        <li>Loading...</li>
    </ul>
</div>

<div class="container">
    <div class="findmember-section">
        <div class="search-wrap">
            <svg width="19" height="19" xmlns="http://www.w3.org/2000/svg">
                <path d="M12.547 11.543H12l-.205-.172a4.539 4.539 0 001.06-2.914A4.442 4.442 0 008.41 4C5.983 4 4 5.989 4 8.457a4.442 4.442 0 004.445 4.457c1.094 0 2.12-.411 2.905-1.062l.206.171v.548L14.974 16 16 14.971l-3.453-3.428zm-4.102 0a3.069 3.069 0 01-3.077-3.086 3.068 3.068 0 013.077-3.086 3.069 3.069 0 013.076 3.086 3.069 3.069 0 01-3.076 3.086z" fill="rgb(30,30,30" />
            </svg>
            <div>
                <input class="search-input" type="text" name="userId" id="userId" placeholder="Find member...">
                <button class="search-but yellow-bg1" id="searchUserId">Search</button>
            </div>
        </div>
    </div>
</div>
</section>
@include('backpanel.backend_balance_belt')
<!--agent list-->
<section>
    <div class="container">
        <table class="table custom-table white-bg text-color-blue-2">
            <thead>
                <tr>
                    <th class="light-grey-bg">Account(Agent)</th>
                    <th class="light-grey-bg">Credit Ref.</th>
                    <th class="light-grey-bg">Remaining bal.</th>
                    <th class="light-grey-bg">Total Agent bal.</th>
                    <th class="light-grey-bg">Total Client bal.</th>
                    <th class="light-grey-bg">Available bal.</th>
                    <th class="light-grey-bg">Exposure</th>
                    <th class="light-grey-bg">Ref. P/L</th>
                    <th class="light-grey-bg">Cumulative P/L</th>
                    <th class="light-grey-bg">Status</th>
                    <th class="light-grey-bg">Action</th>
                </tr>
               </thead>
              	<tbody id="agent_table">
				@foreach($agent as $agentData)
                <?php
                $totalClientBal=0;
                $totalAgentBal=0;
                    $subdatauser = User::where('parentid',$agentData->id)->get();
                    foreach ($subdatauser as $value) {
                         $subdata = User::where('id',$value->id)->first();
                        if($subdata->agent_level=='PL'){
                            $credit_data = CreditReference::where('player_id',$subdata->id)->select('available_balance_for_D_W')->first();                
                            if(!empty($credit_data)){
                                $totalClientBal += $credit_data->available_balance_for_D_W;
                            }
                        }else{
                            $credit_data = CreditReference::where('player_id',$subdata->id)->select('available_balance_for_D_W')->first();                
                            if(!empty($credit_data)){
                                $totalAgentBal += $credit_data->available_balance_for_D_W;
                            }
                        }
                    }
                ?>

                <?php
                $credit_data = CreditReference::where('player_id',$agentData->id)->select('available_balance_for_D_W')->first();
                $availableBalance='';
                if(!empty($credit_data)){
                    $availableBalance = $credit_data->available_balance_for_D_W;
                }

                $credit_data = CreditReference::where('player_id',$agentData->id)->select('remain_bal')->first();
                $remain_bal='';
                if(!empty($credit_data)){
                    $remain_bal = $credit_data->remain_bal;
                }
                ?>
                
                <?php
                if($agentData->agent_level == 'SA'){
                    $color = 'orange-bg';
                }else if($agentData->agent_level == 'AD'){
                    $color = 'black-bg';
                }else if($agentData->agent_level == 'SMDL'){
                    $color = 'green-bg';
                }else if($agentData->agent_level == 'MDL'){
                    $color = 'yellow-bg';
                }else if($agentData->agent_level == 'DL'){
                    $color = 'blue-bg';
                }else{
                    $color = 'red-bg';
                }?>
             
                <tr>
                    <td class="align-L white-bg">
                    	<a onclick="get_mychild({{$agentData->id}})" class="ico_account text-color-blue-light">
                    		<span class="{{$color}} text-color-white">{{$agentData->agent_level}}</span>{{$agentData->first_name}}({{$agentData->user_name}})
                        </a>
                    </td>
                    <?php
                    $credit_data = CreditReference::where('player_id',$agentData->id)->select('credit')->first();
                    $credit=0;
                    if(!empty($credit_data['credit'])){
                        $credit = $credit_data['credit'];
                    }
                    ?>
                    <td class="credit-amount-member white-bg"><a class="favor-set text-color-blue-light openCreditpopup" id="{{$agentData->id}}" data-credit="{{$credit}}">{{$credit}}</a></td>
                    <td class="white-bg">{{$availableBalance}}</td>
                    <td class="white-bg">{{$totalAgentBal}}</td>
                    <td class="white-bg">{{$totalClientBal}}</td>
                    <td class="white-bg">{{$remain_bal}}</td>
                    <td class="text-color-red white-bg" style="display: table-cell;">(0.00)</td>
                    <?php
                    $refPL = (int)$remain_bal-$credit;
                    if($refPL < 0){
                        $class="text-color-red";
                    }else{
                        $class="text-color-green";
                    }
                    ?>
                    <td class="{{$class}} white-bg">{{$refPL}}</td>
                    <td class="text-color-red white-bg">(0.00)</td>
                    <td class="white-bg">
                        <span class="status-active light-green-bg text-color-green"><span class="round-circle green-bg"></span>Active</span>
                    </td>
                    <td class="white-bg">
                        <ul class="action-ul">
                            <li><a class="grey-gradient-bg" data-toggle="modal" data-target="#myStatus"><img src="{{ URL::to('asset/img/setting-icon.png')}}"></a></li>
                            <li><a class="grey-gradient-bg" href="{{route('changePass',$agentData->id)}}"><img src="{{ URL::to('asset/img/user-icon.png')}}"></a></li>
                            
                        </ul>
                    </td>
                </tr>
				@endforeach
            </tbody>
        </table>
    </div>
</section>
<!--end for agent list-->

<section>
	<div class="container">
        <div class="pagination-wrap light-grey-bg-1">
            <ul class="pages">
                <li id="prev"><a class="disable disable-bg disable-color">Prev</a></li>
                <li id="pageNumber"><a class="active text-color-yellow">1</a></li>
                <li id="next"><a class="">Next</a></li>
                <input type="number" id="goToPageNumber_1" maxlength="6" size="4" class="pageinput white-bg"><a id="goPageBtn_1">GO</a>
            </ul>
        </div>
    </div>
</section>
<!-- Player Section -->
<section>
    <div class="container">
        <table class="table custom-table white-bg text-color-blue-2">
            <thead>
                <tr>
                    <th class="light-grey-bg">Account(Player)</th>
                    <th class="light-grey-bg">Credit Ref</th>
                    <th class="light-grey-bg">Remaining bal.</th>
                    <th class="light-grey-bg">Exposure</th>
                    <th class="light-grey-bg">Ref. P/L</th>
                    <th class="light-grey-bg">Cumulative P/L</th>
                    <th class="light-grey-bg">Status</th>
                    <th class="light-grey-bg">Action</th>
                </tr>
            </thead>
            <tbody id="agent-player-table">
                @foreach($player as $players)            
                <tr>
                    <td class="align-L white-bg"><a style="text-decoration:none !important" class="ico_account text-color-blue-light"><span class="orange-bg text-color-white">{{$players->agent_level}}</span>{{$players->first_name." ".$players->last_name}} [{{$players->user_name}}] </a></td>
                    <?php
                    $credit_data = CreditReference::where('player_id',$players->id)->select('credit')->first();
                    $credit=0;
                    if(!empty($credit_data['credit'])){
                        $credit = $credit_data['credit'];
                    }
                    ?>

                    <?php
                     $credit_data = CreditReference::where('player_id',$players->id)->select('available_balance_for_D_W')->first();
                        $availableBalance='';
                        if(!empty($credit_data)){
                            $availableBalance = $credit_data->available_balance_for_D_W;
                        }

                            $credit_data = CreditReference::where('player_id',$players->id)->select('remain_bal')->first();
                        $remain_bal='';
                        if(!empty($credit_data)){
                            $remain_bal = $credit_data->remain_bal;
                        }
                    ?>
                    <td class="white-bg"><a id="{{$players->id}}" data-credit="{{$credit}}"  class="openCreditpopup favor-set">{{$credit}}</a></td>
                    <td class="white-bg" style="display: table-cell;">{{$availableBalance}}</td>
                    <td class="text-color-green white-bg">(0.00)</td>
                     <?php
                    $refPL = (int)$remain_bal-$credit;
                    if($refPL < 0){
                        $class="text-color-red";
                    }else{
                        $class="text-color-green";
                    }?>
                     <td class="{{$class}} white-bg" style="display: table-cell;">{{$refPL}}</td>
                      <td class="text-color-red white-bg" style="display: table-cell;">0.00</td>
                       <td class="text-color-red white-bg" style="display: table-cell;">Active</td>
                    <td class="white-bg">
                        <ul class="action-ul">
                            <li><a class="grey-gradient-bg" data-toggle="modal" data-target="#myStatus"><img src="{{ URL::to('asset/img/setting-icon.png')}}"></a></li>
                            <li><a class="grey-gradient-bg" href="{{route('changePass',$players->id)}}"><img src="{{ URL::to('asset/img/user-icon.png')}}"></a></li>
                            <li><a class="grey-gradient-bg"><img src="{{ URL::to('asset/img/updown-arrow-icon.png')}}"></a></li>
                            <li><a class="grey-gradient-bg"><img src="{{ URL::to('asset/img/history-icon.png')}}"></a></li>
                        </ul>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>
<!-- Credit Reference model -->
<div class="modal credit-modal" id="openCreditpopup">
    <div class="modal-dialog">
        <div class="modal-content light-grey-bg-1">
            <div class="modal-header border-0">
                <h4 class="modal-title text-color-blue-1">Credit Reference Edit</h4>
                <button type="button" class="close" data-dismiss="modal"><img src="{{ URL::to('asset/img/close-icon.png')}}"></button>
            </div>
            <form method="post" action="{{route('storeReference')}}" id="balanceform">
                @csrf
                <input type="hidden" name="player_id" id="player_id" value="">
                <input type="hidden" name="route_name" value="downline-list">
                <div class="modal-body">
                    <div class="form-modal addform-modal">
                        <div class="addform-block">
                            <div>
                                <span>Current</span>
                                <span>
                                    <input type="text" id="creditapp" name=""  maxlength="16" class="form-control white-bg" readonly="" value="0">
                                    <em class="text-color-red">*</em>
                                </span>
                                <span class="text-danger cls-error"></span>
                            </div>
                            <div>
                                <span>New</span>
                                <span>
                                    <input type="text" id="credit" name="credit" placeholder="" maxlength="16" class="form-control white-bg" onkeypress="return isNumberKey(event)">
                                    <em class="text-color-red">*</em>
                                </span>
                                <span class="text-danger cls-error" id="errnew_amount"></span>
                            </div>
                            <div>
                                <span>Password</span>
                                <span>
                                    <input type="password" id="current_pass" name="current_pass" placeholder="" maxlength="16" class="form-control white-bg">
                                    <em class="text-color-red">*</em>
                                </span>
                                <span class="text-danger cls-error" id="errcurrent_pass"></span>
                            </div>
                        </div>
                        <div class="button-wrap pb-0">
                            <input type="submit" value="Submit" name="addreference_btn" id="addreference_btn"  class="submit-btn text-color-yellow">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}">
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script>
function get_mychild(mid)
{
	var _token = $("input[name='_token']").val();
	$.ajax({
        type: "POST",
        url: '{{route("getAgentChildAgent")}}',
        data: {_token:_token,mid:mid},
        beforeSend:function(){
            $('#site_bet_loading1').show();
        },
        complete: function(){
            $('#site_bet_loading1').hide();
        },
        success: function(data){
           var dt=data.split("~~");
		   $('#agent_table').html(dt[0]);
		   $('#agent-player-table').html(dt[1]);
        }
    });
}

$(".openCreditpopup").click(function(){
    $('#player_id').val(this.id);
    $('#creditapp').val($(this).attr("data-credit"));
    $('#openCreditpopup').modal('show');
});
</script>
@endsection