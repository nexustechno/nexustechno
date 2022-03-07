@extends('layouts.app')

@section('content')

<!-- ss comment -->
<!-- <script type="text/javascript" src="https://code.jquery.com/jquery-1.12.3.js"></script> 
<script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>  
<link rel="Stylesheet" href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css"> -->

<?php
use App\Http\Controllers\AgentController;
$getUser = Auth::user();
?>

<section>
<?php
$loginuser = Auth::user(); 
use App\CreditReference;
use App\User; 
use App\UserExposureLog;
use App\UserHirarchy;
$total_ref_pl=0;
$myPl='';
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
                    <input class="search-input" type="text" name="userId" id="userSearch" placeholder="Find member...">
                    <button class="search-but yellow-bg1" id="searchUserId">Search</button>
                </div>
            </div>

            <ul class="agentlist" style="display: none;">
                <li class="agentlistadmin" id="{{$getUser->id}}"></li>         
            </ul>

            <div class="player-right">
                @if($loginuser->agent_level != 'SL')
                    @if($loginuser->agent_level != 'DL')
                    <a class="add_player grey-gradient-bg" data-toggle="modal" data-target="#myAddAgent">
                        <?php  $url=$_SERVER['REQUEST_URI']; ?>
                        <img src="{{ URL::to('asset/img/user-add.png') }}">Add Agent
                    </a>
                    @endif
                    <a class="add_player grey-gradient-bg" data-toggle="modal" data-target="#myAddPlayer">
                        <img src="{{ URL::to('asset/img/user-add.png') }}">Add Player
                    </a>
                @endif
                <a class="refreshbtn grey-gradient-bg" id="refreshpage">
                    <img src="{{ URL::to('asset/img/refresh.png') }}">
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Player Model -->

<div class="modal credit-modal" id="myAddPlayer">
    <div class="modal-dialog">
        <div class="modal-content light-grey-bg-1">
            <form method="post" action="{{route('addPlayer')}}" id="playerform">
            @csrf
                <div class="modal-header border-0">
                    <h4 class="modal-title text-color-blue-1">Add Player</h4>
                    <button type="button" class="close" data-dismiss="modal"><img src="{{ URL::to('asset/img/close-icon.png') }}"></button>
                </div>

                <div class="modal-body">
                    <div class="form-modal addform-modal">
                        <div class="addform-block">
                            <div>
                                <span>Username</span>
                                <span>
                                    <input type="text" id="puser_name" name="puser_name" placeholder="Enter" maxlength="16" class="form-control white-bg">
                                    <em class="text-color-red">*</em>
                                </span>
                            </div>
                            <span class="text-danger cls-error pusrnamecls" id="errplyrusername"></span>
                            <div>
                                <span>Password</span>
                                <span><input id="ppassword" name="ppassword" type="password" placeholder="Enter" class="form-control white-bg"><em class="text-color-red">*</em></span>
                            </div>

                            <span class="text-danger cls-error" id="errplyrpass"></span>

                            <div>
                                <span>Confirm Password</span>
                                <span><input id="pcpassword" name="pcpassword" type="password" placeholder="Enter" class="form-control white-bg"><em class="text-color-red">*</em></span>
                            </div>

                            <span class="text-danger cls-error" id="errplyrcpass"></span>    
                        </div>

                        <div class="addform-block">
                            <div>
                                <span>First Name</span>

                                <span>
                                    <input type="text" id="pfname" name="pfname" placeholder="Enter" maxlength="16" class="form-control white-bg">
                                    <em class="text-color-red">*</em>
                                </span>
                            </div>

                            <span class="text-danger cls-error" id="errplyrfname"></span>

                            <div>
                                <span>Last Name</span>
                                <span><input id="planame" name="planame" type="text" class="form-control white-bg"></span>
                            </div>

                            <span class="text-danger cls-error" id="errplyrlname"></span>

                            <div>
                                @php $readonly='readonly';  @endphp
                                @if(Auth::user()->agent_level == 'COM')
                                @php $readonly='';  @endphp
                                @endif
                                <span>Commission(%)</span>
                                <span><input id="pcommission" name="pcommission" type="text" value="{{$loginuser->commission}}" {{$readonly}} placeholder="Enter" class="form-control white-bg" onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57"><em class="text-color-red">*</em></span>
                            </div>

                            <span class="text-danger cls-error" id="errplyrerrcm"></span>

                            @if(Auth::user()->agent_level == 'COM')
                            <div>

                                <span>Rolling Delay</span>

                                <span>
                                    <label class="switch switch-label switch-primary pull-left">
                                        <input class="switch-input ng-untouched ng-valid ng-dirty" id="dealy_time1" name="dealy_time" type="checkbox" >
                                        <span class="switch-slider" data-checked="✓" data-unchecked="✕"></span>
                                    </label>
                                </span>

                            </div>

                            <span class="text-danger cls-error" id="errdly"></span>

                            <div class="checked-hide1" style="display: none;">
                                <div>
                                    <span>Odds</span>
                                    <span><input id="odds" name="odds" type="text"  placeholder="Enter" class="form-control white-bg" onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57"></span>
                                </div>

                                <span class="text-danger cls-error" id="errdly"></span>

                                <div>
                                    <span>Bookmaker</span>
                                    <span><input id="bookmaker" name="bookmaker" type="text"  placeholder="Enter" class="form-control white-bg" onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57"></span>
                                </div>

                                <span class="text-danger cls-error" id="errdly"></span>
                                <div>
                                    <span>Fancy</span>
                                    <span><input id="fancy" name="fancy" type="text"  placeholder="Enter" class="form-control white-bg" onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57"></span>
                                </div>

                                <span class="text-danger cls-error" id="errdly"></span>
                                <div>
                                    <span>Soccer</span>
                                    <span><input id="soccer" name="soccer" type="text"  placeholder="Enter" class="form-control white-bg" onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57"></span>
                                </div>

                                <span class="text-danger cls-error" id="errdly"></span>
                                <div>
                                    <span>Tennis</span>
                                    <span><input id="tennis" name="tennis" type="text"  placeholder="Enter" class="form-control white-bg" onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57"></span>
                                </div>

                                <span class="text-danger cls-error" id="errdly"></span>
                            </div>

                            @endif

                            <div>
                                <span>Time Zone</span>
                                <span>
                                    <select name="ptime" id="ptime" class="form-control white-bg">
                                        <option value="GMT+5:30">IST(Bangalore / Bombay / New Delhi) (GMT+5:30)</option>
                                    </select>

                                    <em class="text-color-red">*</em>
                                </span>
                            </div>
                            <span class="text-danger cls-error" id="errplyrtime"></span>
                        </div>

                        <div class="button-wrap pb-0">
                            <input type="submit" value="Create" name="addplayer" id="addplayer"  class="submit-btn text-color-yellow">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Agent Model -->

<div class="modal credit-modal" id="myAddAgent">
    <div class="modal-dialog">
        <div class="modal-content light-grey-bg-1">
            <div class="modal-header border-0">
                <h4 class="modal-title text-color-blue-1">Add Agent</h4>
                <button type="button" class="close" data-dismiss="modal"><img src="{{ URL::to('asset/img/close-icon.png') }}"></button>
            </div>

            <div class="modal-body">
                <form method="post" action="{{route('agent.store')}}" id="agentform">
                    @csrf

                    <div class="form-modal addform-modal">
                        <div class="addform-block">
                            <div>
                                <span>Agent Level</span>
                                <span>
                                    <select class="form-control white-bg" name="agent_level" id="agent_level">
                                        @if($loginuser->agent_level == 'MDL')
                                        <option value="DL">MASTER (DL)</option>
                                        @elseif($loginuser->agent_level == 'SMDL')
                                        <option value="MDL">SUPER MASTER (MDL)</option>
                                        <option value="DL">MASTER (DL)</option>
                                        @elseif($loginuser->agent_level == 'AD')
                                        <option value="SP">SUPER (SP)</option>
                                        <option value="SMDL">SUB ADMIN (SMDL)</option>
                                        <option value="MDL">SUPER MASTER (MDL)</option>
                                        <option value="DL">MASTER (DL)</option>
                                        @elseif($loginuser->agent_level == 'SP')
                                        <option value="SMDL">SUB ADMIN (SMDL)</option>
                                        <option value="MDL">SUPER MASTER (MDL)</option>
                                        <option value="DL">MASTER (DL)</option>
                                        @else
                                        <option value="AD">ADMIN (AD)</option>
                                        <option value="SP">SUPER (SP)</option>
                                        <option value="SMDL">SUB ADMIN (SMDL)</option>
                                        <option value="MDL">SUPER MASTER (MDL)</option>
                                        <option value="DL">MASTER (DL)</option>
                                        @endif
                                    </select>                                 
                                    <em class="text-color-red">*</em>
                                </span>
                            </div>
                            <span class="text-danger cls-error" id="errage"></span> 
                            <div>
                                <span>User Name</span>
                                <span><input id="user_name" type="text" name="user_name" placeholder="Enter" class="form-control white-bg user_name"><em class="text-color-red">*</em></span>
                            </div>
                            <span class="userNm text-danger cls-error" id="errsub"></span>
                            <div>
                                <span>Password</span>
                                <span><input id="password" name="password" type="password" placeholder="Enter" class="form-control white-bg"><em class="text-color-red">*</em></span>
                            </div>
                            <span class="text-danger cls-error" id="errpass"></span>
                            <div>
                                <span>Confirm Password</span>
                                <span><input id="confirm_password" name="confirm_password" type="password" placeholder="Enter" class="form-control white-bg"><em class="text-color-red">*</em></span>
                            </div>
                            <span class="text-danger cls-error" id="errcnpass"></span>
                        </div>
                        <div class="addform-block">
                            <div>
                                <span>First Name</span>
                                <span>
                                    <input type="text" id="first_name" name="first_name" placeholder="Enter" maxlength="16" class="form-control white-bg">
                                    <em class="text-color-red">*</em>
                                </span>
                            </div>
                            <span class="text-danger cls-error" id="errfn"></span>
                            <div>
                                <span>Last Name</span>
                                <span><input id="last_name" name="last_name" placeholder="Enter" type="text" class="form-control white-bg"></span>
                            </div>
                            <span class="text-danger cls-error" id="errln"></span>
                            <div>
                                <span>Commission(%)</span>
                                @if($loginuser->agent_level == 'COM')
                                <span><input id="commission" type="text" name="commission" placeholder="Enter" class="form-control white-bg" onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57"><em class="text-color-red">*</em></span>
                                @else
                                <span><input id="commission" type="text" name="commission" value="{{$loginuser->commission}}" readonly placeholder="Enter" class="form-control white-bg" onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57"><em class="text-color-red">*</em></span>
                                @endif
                            </div>
                            <span class="text-danger cls-error" id="errcm"></span>
                            @if($loginuser->agent_level == 'COM')
                                <div>

                                <span>Rolling Delay</span>

                                <span>
                                    <label class="switch switch-label switch-primary pull-left">
                                        <input class="switch-input ng-untouched ng-valid ng-dirty" id="dealy_time" name="dealy_time" type="checkbox" >
                                        <span class="switch-slider" data-checked="✓" data-unchecked="✕"></span>
                                    </label>
                                </span>

                            </div>
                                {{--<div>
                                    <span>Dealy Time</span>
                                    <span><input  type="text"  placeholder="Enter" class="form-control white-bg" value="{{$loginuser->dealy_time}}" onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57"></span>
                                </div>--}}
                                <span class="text-danger cls-error" id="errdly"></span>
                                <div class="checked-hide" style="display: none;">
                                    <div>
                                        <span>Odds</span>
                                        <span><input id="odds" name="odds" type="text"  placeholder="Enter" class="form-control white-bg" onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57"></span>
                                    </div>

                                    <span class="text-danger cls-error" id="errdly"></span>

                                    <div>
                                        <span>Bookmaker</span>
                                        <span><input id="bookmaker" name="bookmaker" type="text"  placeholder="Enter" class="form-control white-bg" onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57"></span>
                                    </div>

                                    <span class="text-danger cls-error" id="errdly"></span>
                                    <div>
                                        <span>Fancy</span>
                                        <span><input id="fancy" name="fancy" type="text"  placeholder="Enter" class="form-control white-bg" onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57"></span>
                                    </div>

                                    <span class="text-danger cls-error" id="errdly"></span>
                                    <div>
                                        <span>Soccer</span>
                                        <span><input id="soccer" name="soccer" type="text"  placeholder="Enter" class="form-control white-bg" onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57"></span>
                                    </div>

                                    <span class="text-danger cls-error" id="errdly"></span>
                                    <div>
                                        <span>Tennis</span>
                                        <span><input id="tennis" name="tennis" type="text"  placeholder="Enter" class="form-control white-bg" onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57"></span>
                                    </div>

                                    <span class="text-danger cls-error" id="errdly"></span>
                                </div>
                            @endif
                            <div>
                                <span>Time Zone</span>
                                <span>
                                    <select name="time_zone" id="time_zone" class="form-control white-bg">
                                        <option value="GMT+5:30">IST(Bangalore / Bombay / New Delhi) (GMT+5:30)</option>
                                    </select>
                                    <em class="text-color-red">*</em>
                                </span>
                            </div>
                            <span class="text-danger cls-error" id="errtim"></span>
                        </div>
                        <div class="button-wrap pb-0">
                            <input type="submit" value="Create" name="" id="agentSubmit" class="submit-btn text-color-yellow">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@include('backpanel.backend_balance_belt')
@if($loginuser->agent_level != 'DL')

<?php




$total_ref_pl=0;

?>

<section>
    <div class="container">
        <table class="table custom-table white-bg text-color-blue-2 search-result" id="pager">
            <thead>
                <tr>
                    <th class="light-grey-bg">Account(Agent)</th>
                    <th class="light-grey-bg">Credit Ref.</th>
                    <th class="light-grey-bg">Remaining bal.</th>
                    <th class="light-grey-bg">Total Agent bal.</th>
                    <th class="light-grey-bg">Total client bal.</th>                    
                    <th class="light-grey-bg">Available bal.</th>
                    <th class="light-grey-bg">Exposure</th>
                    <th class="light-grey-bg">Ref. P/L</th>
                    <th class="light-grey-bg">Cumulative P/L</th>
                    <th class="light-grey-bg">Status</th>
                    <th class="light-grey-bg">Action</th>
                </tr>
            </thead>

            <tbody id="bodyData">
                @foreach($agent as $agentData)
                <?php
                $class="text-color-green";
                $totalClientBal=0.00;
                $totalAgentBal=0.00;
                $totalExposure=0.00;
                $cumulative_pl=0.00;
                $total_Player_exposer=0.00;

                $depParent = AgentController::userBalance($agentData->id);
                $calData = explode("~",$depParent);
                $sum_credit=0;
                $credit_datamn = CreditReference::where('player_id',$agentData->id)->first();             
                $sum_credit = $credit_datamn->credit;
                ?>
                <?php

                $credit_data = CreditReference::where('player_id',$agentData->id)->select('available_balance_for_D_W')->first();
                $availableBalance=''; $total_calculated_available_balance=0;
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
                        <a class="ico_account text-color-blue-light" id="{{$agentData->id}}"   onclick="subpagedata(this.id);">
                            <span class="{{$color}} text-color-white">{{$agentData->agent_level}}</span>{{$agentData->user_name}} 
                            [{{$agentData->first_name}} {{$agentData->last_name}} ]
                        </a>
                    </td>
                    <?php
                        $credit_data = CreditReference::where('player_id',$agentData->id)->first();
                        $credit=0;
                        if(!empty($credit_data['credit'])){
                            $credit = $credit_data['credit'];
                        }
                    //$total_calculated_available_balance=$availableBalance+$totalAgentBal+$totalClientBal;
                    $total_calculated_available_balance=$availableBalance+$calData[0]+$calData[1];
                    ?>
                    <td class="white-bg"><a id="{{$agentData->id}}" data-credit="{{$credit}}"  class="openCreditpopup favor-set">{{$sum_credit}}</a></td>
                    <td class=" white-bg">{{number_format($availableBalance,2, '.', '')}}</td>
                    <td class=" white-bg">{{number_format($calData[0],2, '.', '')}}</td>
                    <td class=" white-bg">{{number_format($calData[1],2, '.', '')}}</td>
                    <td class=" white-bg"><?php /*?>{{$remain_bal}}<?php */?>{{number_format($total_calculated_available_balance,2, '.', '')}}</td>              
                    <td class="text-color-red white-bg"><?php /*?>{{$credit_data->exposure}}<?php */?>({{number_format(abs($calData[2]),2, '.', '')}})</td>
                    <?php
                        /*$refPL = (int)$remain_bal-$credit;*/

                        $refPL = $credit-$total_calculated_available_balance;
                        if($refPL < 0){
                            $class="text-color-green";
                        }else{
                            $class="text-color-red";
                        }
                        $total_ref_pl+=$calData[3];

                        if($total_ref_pl<=0){
                    $myPl='text-color-green';
                    }else{
                       $myPl='text-color-red';
                    }
                    
                    ?>

                    <td class="{{$class}} white-bg">({{number_format(abs($refPL),2, '.', '')}})</td>
                   
                    <td class="@if($calData[3]<0) text-color-green @else text-color-red @endif white-bg">({{number_format(abs($calData[3]),2, '.', '')}})</td>                    
                    <td class="text-color-red white-bg" style="display: table-cell;">
                        @if($agentData->status == 'active')
                            <span class="status-active light-green-bg text-color-green"><span class="round-circle green-bg"></span>{{ ucfirst(trans($agentData->status)) }}</span>
                        @endif
                        @if($agentData->status == 'suspend')
                            <span class="status-suspended light-red-bg text-color-red"><span class="round-circle red-bg"></span>{{ ucfirst(trans($agentData->status)) }}</span>
                        @endif
                        @if($agentData->status == 'locked')
                            <span class="status-locked light-blue-bg-2 text-color-darkblue"><span class="round-circle darkblue-bg1"></span>{{ ucfirst(trans($agentData->status)) }}</span>
                        @endif
                    </td>
                    <td class="white-bg">
                        <ul class="action-ul">
                            <li><a class="grey-gradient-bg setting" data-toggle="modal" data-target="#myStatus" data-id="{{$agentData->id}}" data-username="{{$agentData->user_name}}" data-agent="{{$agentData->agent_level}}" data-status="{{$agentData->status}}"><img src="{{ URL::to('asset/img/setting-icon.png') }}"></a></li>
                            <li><a class="grey-gradient-bg" href="{{route('changePass',$agentData->id)}}"><img src="{{ URL::to('asset/img/user-icon.png') }}"></a></li>
                            {{--<li><a class="grey-gradient-bg"><img src="{{ URL::to('asset/img/updown-arrow-icon.png') }}"></a></li>
                            <li><a class="grey-gradient-bg"><img src="{{ URL::to('asset/img/history-icon.png') }}"></a></li>--}}
                        </ul>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>
@endif


<!-- Setting Pop-up Start --> 
<div class="modal credit-modal" id="myStatus">
    <div class="modal-dialog">
        <div class="alert" style="color: red; font-weight: bold; text-align: center;font-size: x-large;"></div>
        <form id="changestatus">
            @csrf
            <div class="modal-content light-grey-bg-1">
                <div class="modal-header">
                    <h4 class="modal-title text-color-blue-1">Change Status</h4>
                    <button type="button" class="close" data-dismiss="modal"><img src="{{ URL::to('asset/img/close-icon.png') }}"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="user_id" id="user_id" value="">
                    <div class="status-block">
                        <div class="status_id white-bg">
                            <p>
                                <span class="highlight-1 purple-bg text-color-white" id="agent"></span>
                                <span id="username"></span>
                            </p>

                            <p class="status-active" id="status" style="text-transform: capitalize;"><span class="round-circle green-bg"></span></p>
                        </div>
                        <div class="status-button white-bg">
                            <ul>
                                <li>
                                    <a data-check="active" id="tagval1" class="but_active white-bg text-color-green check active-sts" >
                                        <img class="disable_img" src="{{ URL::to('asset/img/active-icon-disable.png') }}">
                                        <img class="" src="{{ URL::to('asset/img/active-icon.png') }}">
                                        <img class="white-icon" src="{{ URL::to('asset/img/active-white-icon.png') }}" >Active
                                    </a>
                                </li>

                                <li>
                                    <a data-check="suspend" class="but_suspend text-color-red check suspend-sts" id="tagval2">
                                        <img class="disable_img" src="{{ URL::to('asset/img/disable-icon-disable.png') }}">
                                        <img class="" src="{{ URL::to('asset/img/disable-icon.png') }}"><img class="white-icon" src="{{ URL::to('asset/img/disable-white-icon.png') }}" >Suspend
                                    </a>
                                </li>
                                <li>
                                    <a data-check="locked" class="but_locked text-color-1 check locked-sts" id="tagval3"><img class="" src="{{ URL::to('asset/img/lock-icon.png') }}"> <img class="white-icon" src="{{ URL::to('asset/img/lock-white-icon.png') }}" >Locked
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div class="buttton-change">
                            <dl class="form_list">
                                <span>Password</span>
                                <input id="spassword" name="spassword" type="password" placeholder="Enter" class="form-control white-bg">
                            </dl>
                            <input type="button" class="appendVal submit-btn text-color-yellow" value="Change" name="submit" onclick="chngstusval(this.id);">
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Setting Pop-up End -->

@if($loginuser->agent_level != 'DL')
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
@endif

<!-- Player Section -->

<section>
    <div class="container">
        <table class="table custom-table white-bg text-color-blue-2 search-result" id="playerpager">
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
            <tbody id="playerData">
                @foreach($player as $players)   
                <tr>
                    <td class="align-L white-bg">
                        <a style="text-decoration:none !important" class="ico_account text-color-blue-light"><span class="orange-bg text-color-white">{{$players->agent_level}}</span>{{$players->user_name}} [{{$players->first_name." ".$players->last_name}}] 
                        </a>
                    </td>
                    <?php
                    $credit_data = CreditReference::where('player_id',$players->id)->select('credit')->first();
                    $credit=0;

                    if(!empty($credit_data['credit'])){
                        $credit = $credit_data['credit'];
                    }
                    ?>
                    <?php
                        $credit_data = CreditReference::where('player_id',$players->id)->select('remain_bal')->first();
                        $availableBalance=0;
                        if(!empty($credit_data)){
                            $availableBalance = $credit_data->remain_bal;
                        }

                        $credit_data = CreditReference::where('player_id',$players->id)->select('remain_bal','exposure')->first();
                        $remain_bal='';
                        $exposure=0;
                        if(!empty($credit_data)){
                            $remain_bal = $credit_data->remain_bal;
                            $exposure = $credit_data->exposure;
                        }

                        //calculating cumulative p/l
                        $cumulative_pl=0; 
                        $cumulative_pl_profit_get = UserExposureLog::where('user_id',$players->id)->where('win_type','Profit')->where('bet_type','ODDS')->sum('profit');                      
                        $cumulative_pl_profit = UserExposureLog::where('user_id',$players->id)->where('win_type','Profit')->where('bet_type','!=','ODDS')->sum('profit');
                        $cumulative_pl_loss = UserExposureLog::where('user_id',$players->id)->where('win_type','Loss')->sum('loss'); 

                        $cumu = $cumulative_pl_profit_get*($players->commission)/100;
                        $cumuPL = ($cumulative_pl_profit_get+$cumulative_pl_profit)-$cumu;  
                        $cumulative_pl=$cumuPL-$cumulative_pl_loss;
                    ?>

                    <td class="white-bg"><a id="{{$players->id}}" data-credit="{{$credit}}"  class="openCreditpopup favor-set">{{$credit}}</a></td>
                    <td class="white-bg" style="display: table-cell;">{{number_format($availableBalance,2, '.', '')}}</td>
                    <td class="text-color-red white-bg">({{number_format(abs($exposure),2, '.', '')}})</td>
                    <?php
                    $refPL = $credit-$remain_bal;
                    if($refPL < 0){
                        $class="text-color-green";
                    }else{
                        $class="text-color-red";
                    }

                    $total_ref_pl+=$cumulative_pl;
                    if($cumulative_pl<0){
                    $cucClass='text-color-green';
                    }else{
                       $cucClass='text-color-red';
                    }

                    if($total_ref_pl<=0){
                    $myPl='text-color-green';
                    }else{
                       $myPl='text-color-red';
                    }

                    ?>

                    <td class="{{$class}} white-bg" style="display: table-cell;">({{number_format((abs($refPL)),2, '.', '')}})</td>
                    <?php
                 /*   if($refPL > 0){
                    $totCumu = $cumulative_pl;
                     $cucClass='text-color-green';
                    }else{
                     
                      $cumu = $cumulative_pl*($players->commission)/100;
                     $totCumu = $cumulative_pl-$cumu;
                     $cucClass='text-color-red';
                    }*/
                    if($cumulative_pl >=0){
                        $cucClass='text-color-green';
                        $totCumu = $cumulative_pl;
                    }else{
                        $cucClass='text-color-red';
                        $cumu = $cumulative_pl*($players->commission)/100;
                        $totCumu = $cumulative_pl-$cumu;
                    }
                   
                    ?>
                    <td class="{{$cucClass}} white-bg" style="display: table-cell;"><?php /*?>{{$sum_cnl}}<?php */?>({{number_format((abs($cumulative_pl)),2, '.', '')}})</td>
                    <td class="text-color-red white-bg" style="display: table-cell;"> 
                        @if($players->status == 'active')
                            <span class="status-active light-green-bg text-color-green"><span class="round-circle green-bg"></span>{{ ucfirst(trans($players->status)) }}</span>
                        @endif

                        @if($players->status == 'suspend')
                            <span class="status-suspended light-red-bg text-color-red"><span class="round-circle red-bg"></span>{{ ucfirst(trans($players->status)) }}</span>
                        @endif

                        @if($players->status == 'locked')
                            <span class="status-locked light-blue-bg-2 text-color-darkblue"><span class="round-circle darkblue-bg1"></span>{{ ucfirst(trans($players->status)) }}</span>
                        @endif
                    </td>
                    <td class="white-bg">
                        <ul class="action-ul">
                            <li>
                                <a class="grey-gradient-bg setting" data-toggle="modal" data-target="#myStatus" data-id="{{$players->id}}" data-username="{{$players->user_name}}" data-agent="{{$players->agent_level}}" data-status="{{$players->status}}">
                                    <img src="{{ URL::to('asset/img/setting-icon.png') }}">
                                </a>
                            </li>

                            <li><a class="grey-gradient-bg" href="{{route('changePass',$players->id)}}"><img src="{{ URL::to('asset/img/user-icon.png') }}"></a>
                            </li>

                            <li><a class="grey-gradient-bg" href="{{route('betHistoryBack',$players->id)}}"><img src="{{ URL::to('asset/img/updown-arrow-icon.png') }}"></a>
                            </li>

                            <li><a class="grey-gradient-bg" href="{{route('betHistoryPLBack',$players->id)}}"><img src="{{ URL::to('asset/img/history-icon.png') }}"></a>
                            </li>
                        </ul>
                    </td>
                </tr>
                @endforeach
                <script>
                    $('#ledger_exposure_div').text('{{number_format(abs($total_ref_pl),2, '.', '')}}');
                     $('.exp_div').addClass('{{$myPl}}');
                </script>
            </tbody>
        </table>
    </div>
</section>
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

<!-- Credit Reference model -->

<div class="modal credit-modal" id="openCreditpopup">
    <div class="modal-dialog">
        <div class="modal-content light-grey-bg-1">
            <div class="modal-header border-0">
                <h4 class="modal-title text-color-blue-1">Credit Reference Edit</h4>
                <button type="button" class="close" data-dismiss="modal"><img src="{{ URL::to('asset/img/close-icon.png') }}"></button>
            </div>
            <form method="post" action="{{route('storeReference')}}">
                @csrf
                <input type="hidden" name="player_id" id="player_id" value="">
                <input type="hidden" name="route_name" value="home">
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

<script type="text/javascript">
$(document).ready(function() { 
    var switchStatus = false;
    $("#dealy_time").on('change', function() {
        if ($(this).is(':checked')) {
            switchStatus = $(this).is(':checked');
            //alert("bhbjk"+switchStatus);// To verify
            if(switchStatus==true){
                //$('.checked-hide').show();
                $(".checked-hide").css("display", "block");
            }
        }
        else {
           switchStatus = $(this).is(':checked');
           //alert("5656"+switchStatus);// To verify
           if(switchStatus==false){
                $('.checked-hide').hide();
           }
        }
    });
});
$(document).ready(function() { 
    var switchStatus = false;
    $("#dealy_time1").on('change', function() {
        if ($(this).is(':checked')) {
            switchStatus = $(this).is(':checked');
            //alert("bhbjk"+switchStatus);// To verify
            if(switchStatus==true){
                //$('.checked-hide').show();
                $(".checked-hide1").css("display", "block");
            }
        }
        else {
           switchStatus = $(this).is(':checked');
           //alert("5656"+switchStatus);// To verify
           if(switchStatus==false){
                $('.checked-hide1').hide();
           }
        }
    });
});
// agent pagination 
$(document).ready(function() {  
    $('#pager').DataTable( {  
        initComplete: function () {  
            this.api().columns().every( function () {  
                var column = this;  
                var select = $('<select><option value=""></option></select>')  
                    .appendTo( $(column.footer()).empty() )  
                    .on( 'change', function () {  
                        var val = $.fn.dataTable.util.escapeRegex(  
                            $(this).val()  
                        );  
                        column  
                            .search( val ? '^'+val+'$' : '', true, false )  
                            .draw();  
                    } );  
                column.data().unique().sort().each( function ( d, j ) {  
                    select.append( '<option value="'+d+'">'+d+'</option>' )  
                } );  
            } ); 
        }  
    } );  
} ); 

// player pagination
$(document).ready(function() {  
    $('#playerpager').DataTable( {  
        initComplete: function () {  
            this.api().columns().every( function () {  
                var column = this;  
                var select = $('<select><option value=""></option></select>')  
                    .appendTo( $(column.footer()).empty() )  
                    .on( 'change', function () {  
                        var val = $.fn.dataTable.util.escapeRegex(  
                            $(this).val()  
                        );  
                        column  
                            .search( val ? '^'+val+'$' : '', true, false )  
                            .draw();  
                    } );  
                column.data().unique().sort().each( function ( d, j ) {  
                    select.append( '<option value="'+d+'">'+d+'</option>' )  
                } );  
            } );  
        }  
    } );  
} );

var $rows = $('.search-result tr');
$('#userSearch').keyup(function() {
    var val = $.trim($(this).val()).replace(/ +/g, ' ').toLowerCase();

    $rows.show().filter(function() {
        var text = $(this).text().replace(/\s+/g, ' ').toLowerCase();
            return !~text.indexOf(val);
        }).hide();
});


    $('.refreshbtn').click(function () {  
        window.location.href = "{{ route('home')}}";
    });


    $('input[name="dealy_time"]').keyup(function() {
        var dval = parseInt($('#dealy_time').val());
        var dbval = parseInt('{{$loginuser->dealy_time}}');

        $('#errdly').html('');           
        if(dval < dbval){
            $('#errdly').html('Enter Delay Time Greater than ' +dbval);
            return false;
        }
    });

    $('.setting').click(function () {  
        var user_id = $(this).attr("data-id");
        var agent = $(this).attr("data-agent");
        var username = $(this).attr("data-username");
        var status = $(this).attr("data-status");

        $(".modal-body #user_id").val( user_id );
        $(".status_id #agent").text( agent );
        $(".status_id #username").text( username );
        $(".status_id #status").text( status );
        if(status == 'active')
        {
            $(".status_id #status").addClass('text-color-green');
            $(".status_id #status").removeClass('text-color-red');
            $(".status_id #status").removeClass('text-color-darkblue');
        }
        if(status =='suspend')
        {
            $(".status_id #status").addClass('text-color-red');
            $(".status_id #status").removeClass('text-color-green');
            $(".status_id #status").removeClass('text-color-darkblue');
        }
        if(status =='locked')
        {
            $(".status_id #status").addClass('text-color-darkblue');
            $(".status_id #status").removeClass('text-color-red');
            $(".status_id #status").removeClass('text-color-green');
        }
        
        //
        $(".but_suspend").addClass( user_id );

        if(status == 'active'){
            $( ".active-sts" ).addClass( "disable" );
            $( ".suspend-sts" ).removeClass( "disable" );
            $( ".locked-sts" ).removeClass( "disable" );
        }

        if(status == 'suspend'){
            $( ".suspend-sts" ).addClass( "disable" );
            $( ".active-sts" ).removeClass( "disable" );
            $( ".locked-sts" ).removeClass( "disable" );
        }

        if(status == 'locked'){
            $( ".locked-sts" ).addClass( "disable" );
            $( ".active-sts" ).removeClass( "disable" );
            $( ".suspend-sts" ).removeClass( "disable" ); 
        }
    });

    $(".check").click(function(){
        var checkval = $(this).data('check');
        $(".appendVal").attr("id",checkval);
    });

    function chngstusval(val){
        //alert(val);
        var status = val;
        var password = $('#spassword').val();
        var user_id = $('#user_id').val();
        var _token = $("input[name='_token']").val();

        $.ajax({
            type: "post",
            url: '{{route("suspend_pa")}}',
            data: {"_token": "{{ csrf_token() }}","password":password,"user_id":user_id,"status":status},

            beforeSend:function(){
                $('#site_bet_loading1').show();
            },
            complete: function(){
                $('#site_bet_loading1').hide();
            },

            success: function(data){
                if(data.result=='success'){
               // window.location.href = "{{ route('home')}}";
                    toastr.success('Status Change Successfully');
                    location.reload();
                }

                if(data.result=='error'){
                    toastr.error('Password is Empty');
                    //$(".alert").append("");
                }
            }
        });
    }


    function backpagedata(val){
        var user_id = val;

        $.ajax({
            type: "post",
            url: '{{route("agentSubBackDetail")}}',
            data: {"_token": "{{ csrf_token() }}","user_id":user_id},

            beforeSend:function(){
                $('#site_bet_loading1').show();
            },
            complete: function(){
                $('#site_bet_loading1').hide();
            }, 
            success: function(data){
                var spl=data.split('~~');
                $('#bodyData').html(spl[0]);
                $(".agentlistadmin").html('<a href="{{route('home')}}"><span class="blue-bg text-color-white">{{$getUser->agent_level}}</span><strong id="{{$getUser->id}}" >{{$getUser->user_name}}</strong></a> <img src="/asset/img/arrow-right2.png">');
                $( ".agentlist" ).show();
                $(".agentlist").html(spl[1]);
                $("#playerData").html(spl[2]);
            }
        });
    }

    function subpagedata(val){
       var user_id = val;
        $.ajax({
            type: "post",
            url: '{{route("agentSubDetail")}}',
            data: {"_token": "{{ csrf_token() }}","user_id":user_id},

            beforeSend:function(){
                $('#site_bet_loading1').show();
            },
            complete: function(){
                $('#site_bet_loading1').hide();
            }, 

            success: function(data){
                var spl=data.split('~~');
                $('#bodyData').html(spl[0]);               
                $(".agentlistadmin").html('<a href="{{route('home')}}"><span class="blue-bg text-color-white">{{$getUser->agent_level}}</span><strong id="{{$getUser->id}}" >{{$getUser->user_name}}</strong></a> <img src="/asset/img/arrow-right2.png">');
                $( ".agentlist" ).show();
                $(".agentlist").append(spl[1]);
                $("#playerData").html(spl[2]);
            }
        });
    }

    $('#addreference_btn').click(function () {        
        var new_amount = $('#credit').val();
        var current_pass = $('#current_pass').val();
        $('#errnew_amount').html('');
        $('#errcurrent_pass').html('');
        if(new_amount == ''){
            $('#errnew_amount').html('This Field is required');
            return false;
        }

        if(current_pass == ''){
            $('#errcurrent_pass').html('This Field is required');
            return false;
        }
    });

    $(".user_name").keyup(function(){
        $('#errsub').html('');
        var uvalue =  this.value;
        $.ajax({
            type:'get',
            url:"{{route('getusername')}}",
            data:{uvalue:uvalue},
            
            success:function(data) {
                $('#errsub').html('');
                if(data.result != ''){
                    $(".userNm").addClass("text-danger");
                    $('#errsub').html('Username is not available');
                }else if(uvalue == ''){
                    $('#errsub').html('This Field is required');
                }else{
                    $(".userNm").removeClass("text-danger");
                    $(".userNm").css("color","green");
                    $('#errsub').html('Username is available');
                }
            }
        });
    });


    $('#agentSubmit').click(function () {
        var errsub = $('#errsub').text();
        var errdly = $('#errdly').text();
        var agent_level = $('#agent_level').val();
        var user_name = $('#user_name').val();
        var password = $('#password').val();
        var confirm_password = $('#confirm_password').val();
        var first_name = $('#first_name').val();
        var last_name = $('#last_name').val();
        var commission = $('#commission').val();
        var time_zone = $('#time_zone').val();

        $('#errage').html('');
        $('#errdly').html('');
        $('#errsub').html('');
        $('#errpass').html('');
        $('#errcnpass').html('');
        $('#errfn').html('');
        $('#errln').html('');
        $('#errcm').html('');
        $('#errtim').html('');

        if(errsub == 'Username is not available'){
            $('#errsub').html('Username is not available');
            return false;
        }

        if(agent_level == ''){
            $('#errage').html('This Field is required');
            return false;
        }

        if(user_name == ''){
            $('#errsub').html('This Field is required');
            return false;
        }

        if(password == ''){
            $('#errpass').html('This Field is required');
            return false;
        }

        if(password !=''){
            if(password.length < 4){   
                $('#errpass').html('Password must be atleast 4 char long!');
                return false;
            }
        }

        if(confirm_password !=''){          
            if(password != confirm_password){
                $('#errcnpass').html('Confirm password must match with password');
                return false;
            }
        }

        if(confirm_password == ''){
            $('#errcnpass').html('This Field is required');
            return false;
        }

        if(first_name == ''){
            $('#errfn').html('This Field is required');
            return false;
        }

        if(last_name == ''){
            $('#errln').html('This Field is required');
            return false;
        }

        if(commission == ''){
            $('#errcm').html('This Field is required');
            return false;
        }

        if(errdly != ''){
            $('#errdly').html(errdly);
            return false;
        }

        if(time_zone == ''){
            $('#errtim').html('This Field is required');
            return false;
        }
    });

    $("#puser_name").keyup(function(){
        $('#errplyrusername').html('');
        var uvalue =  this.value;
        $.ajax({
           type:'get',
           url:"{{route('getusername')}}",
           data:{uvalue:uvalue},
           success:function(data) {
                $('#errplyrusername').html('');
                if(data.result != ''){
                    $(".pusrnamecls").addClass("text-danger");
                    $('#errplyrusername').html('Username is not available');
                }else if(uvalue == ''){
                    $('#errplyrusername').html('This Field is required');
                }else{
                    $(".pusrnamecls").removeClass("text-danger");
                    $(".pusrnamecls").css("color","green");
                    $('#errplyrusername').html('Username is available');
                }
            }
        });
    });

    $('#addplayer').click(function () {
        var errsub = $('#errplyrusername').text();       
        var user_name = $('#puser_name').val();
        var password = $('#ppassword').val();
        var confirm_password = $('#pcpassword').val();
        var first_name = $('#pfname').val();
        var last_name = $('#planame').val();
        var commission = $('#pcommission').val();
        var time_zone = $('#ptime').val();

        $('#errage').html('');
        $('#errsub').html('');        
        $('#errplyrpass').html('');
        $('#errplyrcpass').html('');
        $('#errplyrfname').html('');
        $('#errplyrlname').html('');
        $('#errplyrerrcm').html('');
        $('#errplyrtime').html('');

        if(errsub == 'Username is not available'){
            $('#errsub').html('Username is not available');
            return false;
        }        

        if(user_name == ''){
            $('#errsub').html('This Field is required');
            return false;
        }

        if(password == ''){
            $('#errplyrpass').html('This Field is required');
            return false;
        }

        if(password !=''){
            if(password.length < 4){    
                $('#errplyrpass').html('Password must be atleast 4 char long!');
                return false;
            }
        }

        if(confirm_password !=''){          
            if(password != confirm_password){
                $('#errplyrpass').html('Confirm password must match with password');
                return false;
            }
        }

        if(confirm_password == ''){
            $('#errplyrcpass').html('This Field is required');
            return false;
        }

        if(first_name == ''){
            $('#errplyrfname').html('This Field is required');
            return false;
        }

        if(last_name == ''){
            $('#errplyrlname').html('This Field is required');
            return false;
        }

        if(commission == ''){
            $('#errplyrerrcm').html('This Field is required');
            return false;
        }

        if(time_zone == ''){
            $('#errplyrtime').html('This Field is required');
            return false;
        }
    });

    $(".openCreditpopup").click(function(){
        $('#player_id').val(this.id);
        $('#creditapp').val($(this).attr("data-credit"));
        $('#openCreditpopup').modal('show');
    });
</script>

@endsection