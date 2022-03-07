@extends('layouts.app')
@section('content')
<?php $loginUser = Auth::user(); 
use App\Match;
use App\User;
use App\UserHirarchy;
use App\MyBets;
?>

<link href="https://unpkg.com/video.js/dist/video-js.css" rel="stylesheet">
<script src="https://unpkg.com/video.js/dist/video.js"></script>
<script src="https://unpkg.com/videojs-contrib-hls/dist/videojs-contrib-hls.js"></script>

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

<section class="risk_management_details_wrapper white-bg ">
    <div class="container">
        @if($errors->any())
        <h4>{{$errors->first()}}</h4>
        @endif
        <div class="inner-title-2 text-color-blue-2">
            <h2>Risk Management Summary</h2>
        </div>
        <div></div>  
  
        <div class="row">
            <div class="col-md-12 p-0"> 
                <div class="riskmanage_content">
                    <div class="riskmanage_head green-bg-1">
                        <div class="btn-group">
                            <button type="button" class="btn yellow-gradient-bg text-color-black btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                User lock
                            </button>
                            <ul class="dropdown-menu">
                                <li class="dropdown-submenu">
                                    <a tabindex="-1" class="dropdown-item">All Block</a>
                                    <ul class="dropdown-menu">
                                        @if($matchList->status_m == 1 && $matchList->status_b == 1 && $matchList->status_b == 1)
                                           
                                        <li>
                                            <a class="dropdown-item" href="{{route('allBlock',$matchList->id)}}">All [Block]</a>
                                        </li>
                                        @else
                                            <li>
                                                <a class="dropdown-item" href="{{route('allunBlock',$matchList->id)}}">All [Un-Block]</a>
                                            </li>
                                        @endif
                                           
                                        @if($matchList->status_m == '1')
                                            <li><a class="dropdown-item" href="{{route('blockMatch',$matchList->id)}}"  id="{{$matchList->id}}">Match Odds [Block]</a></li>
                                        @else
                                            <li><a class="dropdown-item" href="{{route('unblockMatch',$matchList->id)}}">Match Odds [Un-Block]</a></li>
                                        @endif

                                        @if($matchList->status_b == '1')

                                            <li><a class="dropdown-item" href="{{route('blockBook',$matchList->id)}}">Bookmaker [Block]</a></li>
                                        @else
                                            <li><a class="dropdown-item" href="{{route('unblockBook',$matchList->id)}}">Bookmaker [Un-Block]</a></li>
                                        @endif

                                        @if($matchList->status_f == '1')
                                            <li><a class="dropdown-item" href="{{route('blockFancy',$matchList->id)}}">Fancy [Block]</a></li>
                                        @else
                                            <li><a class="dropdown-item" href="{{route('unblockFancy',$matchList->id)}}">Fancy [Un-Block]</a></li>
                                        @endif
                                    </ul>
                                </li>
                                    
                                <li>
                                    <a class="dropdown-item" data-toggle="modal" data-target="#myuserwise">User Wise</a>
                                </li>
                            </ul>
                        </div>

                        <h4 class="text-color-white">{{$matchList->match_name}}</h4>

                    </div>

                    <div class="riskmanage_body_content">
                        <div class="row">
                            <div class="col-7 col-xs-12 p-0">
                                <div class="risk_matchodds_left">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th class="r_match_title">
                                                        Match Odds <span class="allow_bets"></span>
                                                    </th>
                                                    <th width="8.33333333%" class=""></th>
                                                    @if($loginUser->agent_level=='COM')
                                                    @if($matchList->suspend_m==1)
                                                    <th width="8.33333333%" class="p-0"><a data-status="suspend_m" data-suspend="0" class="chkaction yellow-gradient-bg text-color-black" href="javascript:void(0);">Suspend</a></th>
                                                    @else
                                                    <th width="8.33333333%" class="p-0"><a data-status="suspend_m" data-suspend="1" class="chkaction yellow-gradient-bg text-color-black" href="javascript:void(0);">Unsuspend</a></th>
                                                    @endif
                                                    @else 
                                                    <th width="8.33333333%" class="p-0"></th> 
                                                    @endif
                                                    <th width="8.33333333%" class="cyan-bg text-color-white text-center btnbl bet_type_uppercase">Back</th>
                                                    <th width="8.33333333%" class="pink-bg text-color-white text-center btnbl bet_type_uppercase">Lay</th>
                                                    <th width="8.33333333%" class=""></th>
                                                    <th width="8.33333333%" class=""></th>
                                                </tr>
                                            </thead>
                                            <tbody id="inplay-tableblock" class="inplay-tableblock">
                                                <tr class="rf_tr">
                                                	<td colspan="7"><div id="site_statistics_loading" class="loaderimage"></div></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    @if($matchList->bookmaker==1)
                                    <div class="table-responsive noData bookmakerHide">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th class="r_match_title">
                                                       Bookmaker <span class="allow_bets"></span>
                                                    </th>
                                                    <th width="8.33333333%" class=""></th>
                                                    @if($loginUser->agent_level=='COM')
                                                    @if($matchList->suspend_b==1)
                                                    <th width="8.33333333%" class="p-0"><a data-status="suspend_b" data-suspend="0" class="chkaction yellow-gradient-bg  text-color-black" href="javascript:void(0);">Suspend</a></th>
                                                    @else
                                                    <th width="8.33333333%" class="p-0"><a  data-status="suspend_b" data-suspend="1" class="chkaction yellow-gradient-bg  text-color-black" href="javascript:void(0);">Unsuspend</a></th>
                                                    @endif
                                                    @endif
                                                    <th width="8.33333333%" class="cyan-bg text-color-white text-center btnbl bet_type_uppercase">Back</th>
                                                    <th width="8.33333333%" class="pink-bg text-color-white text-center btnbl bet_type_uppercase">Lay</th>
                                                    <th width="8.33333333%" class=""></th>
                                                    <th width="8.33333333%" class=""></th>
                                                </tr>
                                            </thead>
                                            <tbody id="inplay-tableblock-bookmaker" class="inplay-tableblock-bookmaker">
                                                <tr class="rf_tr">
                                                	<td colspan="7"><div id="site_statistics_loading" class="loaderimage"></div></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    @endif
                                    
                                    @if($matchList->fancy==1)
                                    <div class="table-responsive noData">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th class="r_match_title">Sessions</th>
                                                    <th width="8.33333333%" class=""></th>
                                                    @if($loginUser->agent_level=='COM')
                                                    @if($matchList->suspend_f==1)
                                                    <th width="8.33333333%" class="p-0"><a data-status="suspend_f"  data-suspend="0" class="chkaction yellow-gradient-bg  text-color-black" href="javascript:void(0);">Suspend</a></th>
                                                    @else
                                                    <th width="8.33333333%" class="p-0"><a data-status="suspend_f" data-suspend="1" class="chkaction yellow-gradient-bg  text-color-black" href="javascript:void(0);">Unsuspend</a></th>
                                                    @endif
                                                    @else
                                                     <th width="8.33333333%" class="p-0"></th>
                                                    @endif
                                                    <th width="8.33333333%" class="pink-bg text-color-white text-center btnbl bet_type_uppercase">No</th>
                                                    <th width="8.33333333%" class="cyan-bg text-color-white text-center btnbl bet_type_uppercase">Yes</th>
                                                    <th width="8.33333333%" class=""></th>
                                                    <th width="8.33333333%" class=""></th>
                                                </tr>
                                            </thead> 
                                            <tbody id="inplay-tableblock-fancy" class="inplay-tableblock-fancy">
                                                <tr class="rf_tr">
                                                	<td colspan="7"><div id="site_statistics_loading" class="loaderimage"></div></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-5 col-xs-12 p-0 risk_live_right">
                                <div class="panel panel-default">
                                    <div class="panel-heading darkblue-bg" role="tab">
                                        <h2 class="panel-title">
                                            <a class="text-color-white" role="button" data-toggle="collapse" data-parent="#accordion" href="#risk1" aria-expanded="true" aria-controls="risk1">
                                                <div class="w-100">Live TV
                                                    <span class="float-right pr-2"><i class="fas fa-tv"></i></span>
                                                </div>
                                            </a>
                                        </h2>
                                    </div>
                                    <div id="risk1" class="panel-collapse tv_tabs_block collapse" role="tabpanel">
                                       
                                        <div class="tab-content">
                                            <div class="tab-pane active" id="tabs-1" role="tabpanel">
                                                <?php 
                                                    $eventid= $matchList->event_id;
                                                    $sprtid= $matchList->sports_id;

                                                    $url='http://194.233.65.10/LiveTV/TVApi.svc/GetLiveTV?eventid='.$eventid;

                                                    $headers = array('Content-Type: application/json');
                                                    $process = curl_init();
                                                    curl_setopt($process, CURLOPT_URL, $url);
                                                    curl_setopt($process, CURLOPT_HTTPHEADER, $headers);
                                                    curl_setopt($process, CURLOPT_CUSTOMREQUEST, "GET");
                                                    curl_setopt($process, CURLOPT_TIMEOUT, 30);
                                                    curl_setopt($process, CURLOPT_HTTPGET, 1);
                                                    curl_setopt($process, CURLOPT_SSL_VERIFYPEER, false);
                                                    curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
                                                    $return = curl_exec($process);
                                                    curl_close($process);
                                                    
                                                    $match_data = json_decode($return, true);

                                                    //echo"<pre>"; print_r($match_data);

                                                    if(!empty($match_data['result']))
                                                    {
                                                        foreach ($match_data as $tvdata) 
                                                        {
                                                            foreach ($tvdata as $tvdata1) 
                                                            {
                                                                if($tvdata1['Message'] =='Successful' && $tvdata1['SportId'] == $sprtid)
                                                                { 
                                                                    ?>
                                                                    <video id="my_video_1" class="video-js vjs-fluid vjs-default-skin" controls autoplay loop muted data-setup='{"fluid": true, "autoplay": true}'>
                                                                        <source src="<?php echo $tvdata1['link'];?>" type="application/x-mpegURL">
                                                                    </video>
                                                                    <?php
                                                                }
                                                            }
                                                        }
                                                    }
                                                    else{
                                                        echo "Video Not Found";
                                                    }
                                                ?>
                                            </div>
                                                  
                                        </div>
                                    </div>
                                </div>

                                <div class="panel panel-default">
                                    <div class="panel-heading darkblue-bg" role="tab">
                                        <h2 class="panel-title">
                                            <a class="text-color-white" role="button" data-toggle="collapse" data-parent="#accordion" href="#risk3" aria-expanded="true" aria-controls="risk3">					
                                                Matched Bets [{{count($my_placed_bets)}}]

                                            </a>
                                        </h2>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4 col-xs-8">
                                          <form class="ng-pristine ng-valid">
                                            <input type="text" name="userId" class="form-control ng-pristine ng-untouched ng-valid ng-empty" id="userSearchodd" placeholder="Find Client Name" style="border-radius: 3px;">
                                          </form>
                                        </div>
                                        @if($loginUser->agent_level=='COM')
                                        <div class="col-md-4 col-xs-4">
                                          <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#Reject-Multiple-Bet">Reject All Bets</button>
                                        </div>
                                        @endif
                                      </div>
                                    <div id="risk3" class="panel-collapse collapse show" role="tabpanel">
                                        <div class="row unmatch_wrap">            
                                        </div>
                                        <div class="custom_table_scroll">
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th> 
                        <input type="checkbox" id="select_all" name="filter-checkbox" value="">
                      </th>
                        <th class="text-center">Client</th>
                        <th class="text-center">Selection</th>
                        <th class="text-center">B/L</th>
                        <th class="text-center">Odds</th>
                        <th class="text-center">Stake</th>
                        <th class="text-center">P&L</th>
                        <th>Placed Time</th>
                        <th class="text-center">Info</th>
                        @if($loginUser->agent_level =='COM')
                        <th>DLT</th>
                            @endif
                            @if($loginUser->agent_level=='COM')
                            <th class="light-grey-bg">COM</th>
                            @endif
                            @if($loginUser->agent_level=='AD' || $loginUser->agent_level=='COM')
                            <th class="light-grey-bg">AD</th>
                            @endif
                            @if($loginUser->agent_level=='SP' || $loginUser->agent_level=='COM' || $loginUser->agent_level=='AD')
                            <th class="light-grey-bg">SP</th>
                            @endif
                            @if($loginUser->agent_level=='SMDL' || $loginUser->agent_level=='COM' || $loginUser->agent_level=='AD' || $loginUser->agent_level=='SP')
                            <th class="light-grey-bg">SMDL</th>
                            @endif
                            @if($loginUser->agent_level=='MDL' || $loginUser->agent_level=='COM' || $loginUser->agent_level=='AD' || $loginUser->agent_level=='SP' || $loginUser->agent_level=='SMDL')
                            <th class="light-grey-bg">MDL</th>
                            @endif
                            @if($loginUser->agent_level=='DL' || $loginUser->agent_level=='COM' || $loginUser->agent_level=='AD' || $loginUser->agent_level=='SP' || $loginUser->agent_level=='SMDL' || $loginUser->agent_level=='MDL')
                            <th class="light-grey-bg">DL</th>  
                            @endif                         
                           
                                                        </tr>
                                                    </thead>
                                                    <tbody id="match_odds_bet">
                                                    	{!!$html!!}
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
								
                                @if($matchList->sports_id=='4')
                                    @if($matchList->bookmaker==1)
                                    <div class="panel panel-default">
                                        <div class="panel-heading darkblue-bg" role="tab">
                                            <h2 class="panel-title">
                                                <a class="text-color-white" role="button" data-toggle="collapse" data-parent="#accordion" href="#risk4" aria-expanded="true" aria-controls="risk4">
                                                    Book Making Bets [{{count($my_placed_bets_BM)}}]
                                                </a>
                                            </h2>
                                        </div>
                                        <div class="row">
                                        <div class="col-md-4 col-xs-8">
                                          <form class="ng-pristine ng-valid">
                                            <input type="text" name="userId" class="form-control ng-pristine ng-untouched ng-valid ng-empty" id="userSearchbm" placeholder="Find Client Name" style="border-radius: 3px;">
                                          </form>
                                        </div>
                                        @if($loginUser->agent_level=='COM')
                                        <div class="col-md-4 col-xs-4">
                                          <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#Reject-Multiple-Bet">Reject All Bets</button>
                                        </div>
                                        @endif
                                      </div>
                                        <div id="risk4" class="panel-collapse collapse show" role="tabpanel">
                                            <div class="row unmatch_wrap">             
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th> 
                        <input type="checkbox" id="select_all" name="filter-checkbox" value="">
                      </th>
                                                            <th>Client</th>
                                                            <th>Selection</th>
                                                            <th>B/L</th>
                                                            <th>Odds</th>
                                                            <th>Stake</th>
                                                            <th>P&L</th>
                                                            <th>Status</th>
                                                            <th>Placed Time</th>
                                                            <th>ID</th>
                                                            <th>Info</th>
                                                             @if($loginUser->agent_level =='COM')
                                                            <th>DLT</th>
                                                            @endif
                                                            @if($loginUser->agent_level=='COM')
                            <th class="light-grey-bg">COM</th>
                            @endif
                            @if($loginUser->agent_level=='AD' || $loginUser->agent_level=='COM')
                            <th class="light-grey-bg">AD</th>
                            @endif
                            @if($loginUser->agent_level=='SP' || $loginUser->agent_level=='COM' || $loginUser->agent_level=='AD')
                            <th class="light-grey-bg">SP</th>
                            @endif
                            @if($loginUser->agent_level=='SMDL' || $loginUser->agent_level=='COM' || $loginUser->agent_level=='AD' || $loginUser->agent_level=='SP')
                            <th class="light-grey-bg">SMDL</th>
                            @endif
                            @if($loginUser->agent_level=='MDL' || $loginUser->agent_level=='COM' || $loginUser->agent_level=='AD' || $loginUser->agent_level=='SP' || $loginUser->agent_level=='SMDL')
                            <th class="light-grey-bg">MDL</th>
                            @endif
                            @if($loginUser->agent_level=='DL' || $loginUser->agent_level=='COM' || $loginUser->agent_level=='AD' || $loginUser->agent_level=='SP' || $loginUser->agent_level=='SMDL' || $loginUser->agent_level=='MDL')
                            <th class="light-grey-bg">DL</th>  
                            @endif
                                                        </tr>
                                                    </thead>
                                                    <tbody id="match_bm_bet"> {!!$html_BM!!}</tbody>
                                                </table> 
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    @if($matchList->fancy==1)
                                    <div class="panel panel-default">
                                        <div class="panel-heading darkblue-bg" role="tab">
                                            <h2 class="panel-title">
                                                <a class="text-color-white" role="button" data-toggle="collapse" data-parent="#accordion" href="#risk3" aria-expanded="true" aria-controls="risk3">
                                                    Fancy Bets [{{count($my_placed_bets_fancy)}}]
                                                </a>                                                
                                            </h2>
                                        </div>
                                        <div class="row">
                                        <div class="col-md-4 col-xs-8">
                                          <form class="ng-pristine ng-valid">
                                            <input type="text" name="userId" class="form-control ng-pristine ng-untouched ng-valid ng-empty" id="userSearchfnc" placeholder="Find Client Name" style="border-radius: 3px;">
                                          </form>
                                        </div>
                                        @if($loginUser->agent_level=='COM')
                                        <div class="col-md-4 col-xs-4">
                                          <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#Reject-Multiple-Bet">Reject All Bets</button>
                                        </div>
                                        @endif
                                      </div>
                                        <div id="risk3" class="panel-collapse collapse show" role="tabpanel">
                                            <div class="row unmatch_wrap">
                                            </div>
                                            <div class="table-responsive">

                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th> 
                        <input type="checkbox" id="select_all" name="filter-checkbox" value="">
                      </th>
                                                            <th>Client</th>
                                                            <th>Selection</th>
                                                            <th>Y/N</th>
                                                            <th>R/S</th>
                                                            <th>Stake</th>
                                                            <th>Placed Time</th>
                                                            <th>Info</th>
                                                             @if($loginUser->agent_level =='COM')
                                                            <th>DLT</th>
                                                            @endif
                            @if($loginUser->agent_level=='COM')
                            <th class="light-grey-bg">COM</th>
                            @endif
                            @if($loginUser->agent_level=='AD' || $loginUser->agent_level=='COM')
                            <th class="light-grey-bg">AD</th>
                            @endif
                            @if($loginUser->agent_level=='SP' || $loginUser->agent_level=='COM' || $loginUser->agent_level=='AD')
                            <th class="light-grey-bg">SP</th>
                            @endif
                            @if($loginUser->agent_level=='SMDL' || $loginUser->agent_level=='COM' || $loginUser->agent_level=='AD' || $loginUser->agent_level=='SP')
                            <th class="light-grey-bg">SMDL</th>
                            @endif
                            @if($loginUser->agent_level=='MDL' || $loginUser->agent_level=='COM' || $loginUser->agent_level=='AD' || $loginUser->agent_level=='SP' || $loginUser->agent_level=='SMDL')
                            <th class="light-grey-bg">MDL</th>
                            @endif
                            @if($loginUser->agent_level=='DL' || $loginUser->agent_level=='COM' || $loginUser->agent_level=='AD' || $loginUser->agent_level=='SP' || $loginUser->agent_level=='SMDL' || $loginUser->agent_level=='MDL')
                            <th class="light-grey-bg">DL</th>  
                            @endif


                                                        </tr>
                                                    </thead>
                                                    <tbody id="match_fancy_bet"> {!!$html_Fancy!!}</tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                @endif
    <?php
    $matchname = explode('v', $matchList->match_name);
    if($loginUser->agent_level=='COM'){
        $user='AD';
    }elseif($loginUser->agent_level=='AD'){
        $user='SP';
    }elseif($loginUser->agent_level=='SP'){
        $user='SMDL';
    }elseif($loginUser->agent_level=='SMDL'){
        $user='MDL';
    }elseif($loginUser->agent_level=='MDL'){
        $user='DL';
    }elseif($loginUser->agent_level=='DL'){
        $user='--';
    }
    ?>
    <ul id="accordion" class="accordion">
        <li>
    <a class="link" role="button" data-toggle="collapse" data-parent="#accordion" href="#risk34" aria-expanded="true" aria-controls="risk34">Admin Book<i class="fa fa-chevron-down"></i></a>
    <ul id="risk34" class="panel-collapse collapse show submenu" role="tabpanel">
      <li>
        <div class="ibox float-e-margins" style="max-height: 300px;overflow-y: auto;padding: 5px">
          <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
              <thead>
                <!-- ngRepeat: MDLlist in MDLDataList track by MDLlist.mdlUsername --><!-- ngIf: $index==0 -->
                <tr data-ng-if="$index==0" data-ng-repeat="MDLlist in MDLDataList track by MDLlist.mdlUsername" class="ng-scope">
                  <th class="text-left"> {{$user}}&nbsp;<small>[&nbsp;UserName&nbsp;]</small></th>
                  <th class="text-center ng-binding">{{$matchname[0]}}</th>
                  <th class="text-center ng-binding">{{$matchname[1]}}</th>
                  @if($team_draw_bet_total != '' )
                  <th class="text-center ng-binding">The Draw</th>
                  @endif
                  <!-- ngIf: MDLlist.runner3Name!=null -->
                </tr>
              </thead>
              <tbody style="overflow-y: auto !important; max-height: 200px !important; background-color: rgba(0, 0, 0, 0);">
                <!-- ngRepeat: MDLlist in MDLDataList track by MDLlist.mdlUsername --><!-- ngIf: $index==0 -->
                <?php
                if($team1_bet_total>=0){
                $clsa1='text-color-green';
                }
                else{
                    $clsa1='text-color-red';
                }
                if($team2_bet_total>=0){
                    $clsa2='text-color-green';
                }
                else{
                    $clsa2='text-color-red';
                }
                if($team_draw_bet_total>=0){
                    $clsa3='text-color-green';
                }
                else{
                    $clsa3='text-color-red';
                }
                ?>
                <tr data-ng-if="$index==0" data-ng-repeat="MDLlist in MDLDataList track by MDLlist.mdlUsername" class="trMDl ng-scope">
                  <td class="text-right"> <b>Admin P&amp;L</b></td>
                  <td class="text-center {{$clsa1}}" ><b class="ng-binding">{{abs($team1_bet_total)}}</b></td>
                  <td class="text-center {{$clsa2}}" ><b class="ng-binding">{{abs($team2_bet_total)}}</b></td>
                  @if($team_draw_bet_total != '' )
                  <td class="text-center {{$clsa3}}"><b class="ng-binding">{{abs($team_draw_bet_total)}}</b></td>
                  @endif
                  <!-- ngIf: MDLlist.runner3Name!=null -->
                </tr>
              </tbody>
              <?php echo $adminBookUser; ?>
             
              <!-- end ngRepeat: MDLlist in MDLDataList track by MDLlist.mdlUsername -->
            </table>
          </div>
        </div>
      </li>
    </ul>
  </li>
  @if($matchList->sports_id==4)
  <?php
$clss1='text-color-red';
$clss2='text-color-red';
$clss3='text-color-red';
if($team1_bet_totalB>=0){
    $clss1='text-color-green';
}
if($team2_bet_totalB>=0){
    $clss2='text-color-green';
}           
if($team_draw_bet_totalB>=0){
    $clss3='text-color-green';
}
  ?>
  <li>   
    <a class="link" role="button" data-toggle="collapse" data-parent="#accordion" href="#risk35" aria-expanded="true" aria-controls="risk34">Admin BM Book<i class="fa fa-chevron-down"></i></a>
    <ul id="risk35" class="panel-collapse collapse show submenu" role="tabpanel">
      <li>
        <div class="ibox float-e-margins" style="max-height: 300px;overflow-y: auto;padding: 5px">
          <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
              <thead>
                <!-- ngRepeat: MDLlist in MDLDataList track by MDLlist.mdlUsername --><!-- ngIf: $index==0 -->
                <tr data-ng-if="$index==0" data-ng-repeat="MDLlist in MDLDataList track by MDLlist.mdlUsername" class="ng-scope">
                  <th class="text-left"> {{$user}}&nbsp;<small>[&nbsp;UserName&nbsp;]</small></th>
                  <th class="text-center ng-binding">{{$matchname[0]}}</th>
                  <th class="text-center ng-binding">{{$matchname[1]}}</th>
                  @if($team_draw_bet_totalB != '' )
                  <th class="text-center ng-binding">The Draw</th>
                  @endif
                  <!-- ngIf: MDLlist.runner3Name!=null -->
                </tr>
              </thead>
              <tbody style="overflow-y: auto !important; max-height: 200px !important; background-color: rgba(0, 0, 0, 0);">
                <tr data-ng-if="$index==0" data-ng-repeat="MDLlist in MDLDataList track by MDLlist.mdlUsername" class="trMDl ng-scope">
                  <td class="text-right"> <b>Admin P&amp;L</b></td>
                  <td class="text-center {{$clss1}}">{{$team1_bet_totalB}}</b></td>
                  <td class="text-center {{$clss2}}">{{$team2_bet_totalB}}</b></td>
                  @if($team_draw_bet_totalB != '' )
                  <td class="text-center {{$clss3}}">{{$team_draw_bet_totalB}}</b></td>
                  @endif
                  <!-- ngIf: MDLlist.runner3Name!=null -->
                </tr>
              </tbody>
              <?php echo $adminBookUserBM; ?>
            </table>
          </div>
        </div>
      </li>
    </ul>
</li>

  <li class="open">
    <a class="link match_heading ng-binding" role="button" data-toggle="collapse" data-parent="#accordion" href="#risk36" aria-expanded="true" aria-controls="risk36">Settled Markets [0]<i class="fa fa-chevron-down"></i></a>
    <ul id="risk36" class="panel-collapse collapse show submenu" role="tabpanel">
      <li>
        <div class="">
          <div class="row">
            <div class="col-md-4 col-xs-8">
              <form class="ng-pristine ng-valid">
                <input type="text" placeholder="Search here..." data-ng-model="searchpnl" class="form-control ng-pristine ng-untouched ng-valid ng-empty" style="height: 30px;">
              </form>
            </div>
          </div>
          <div class="ibox float-e-margins">
            <div class="table-responsive">
              <table data-ng-table="Sessionpnldata" class="table tblpad table-striped table-bordered table-hover ng-scope ng-table">
                <!-- ngInclude: templates.header -->
                <thead class="ng-scope">
                  <!-- ngIf: params.hasGroup() --> 
                  <tr class="ng-table-sort-header">
                    
                    <th class="header">
                      
                      <div class="ng-table-header ng-scope"> <span class="ng-binding sort-indicator">Market name</span> </div>
                       
                    </th>
                    
                    <th class="header">
                      
                      <div class="ng-table-header ng-scope"> <span class="ng-binding sort-indicator">Result</span> </div>
                       
                    </th>
                    
                    <th class="header">
                      
                      <div class="ng-table-header ng-scope"> <span class="ng-binding sort-indicator">P | L</span> </div>
                       
                    </th>
                    
                  </tr>
                  <tr ng-show="show_filter" class="ng-table-filters ng-hide">
                    <th class="filter"></th>
                    <th class="filter"></th>
                    <th class="filter"></th>
                  </tr>
                </thead>
                <tbody>
                  
                  <tr class="ng-scope">
                    <td colspan="3">No Data Found...</td>
                  </tr>
                  <!-- end ngIf: $data.length<1 -->
                  <tr>
                    <th>Declared Market Total</th>
                    <th></th>
                    <th class="ng-binding greenclass">0.00</th>
                  </tr>
                </tbody>
              </table>
              <div class="ng-scope ng-isolate-scope">
                <div class="ng-scope">
                </div>
              </div>
            </div>
          </div>
        </div>
      </li>
    </ul>
  </li>
  @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- User Wise Lock Modal -->
<div class="modal credit-modal" id="myuserwise">
    <div class="modal-dialog">
        <div class="modal-content light-grey-bg-1">
            <div class="modal-header border-0">
                <h4 class="modal-title text-color-blue-1">Active User</h4>
                <button type="button" class="close" data-dismiss="modal"><img src="{{ URL::to('asset/img/close-icon.png')}}"></button>
            </div>
            <div class="modal-body">
                <form method="post" action="" id="agentform">
                    @csrf
                    <div class="form-modal addform-modal">
                        @if(!empty($list))
                            @php $i=1; $count = 0;@endphp
                            <table class="table">
                                <thead>
                                  <tr>
                                    <th>Sr No.</th>
                                    <th>User Name</th>
                                    <th>Checked</th>
                                  </tr>
                                </thead>
                                <tbody>
                                <?php 
                                	$matchdata = Match::where('id',$matchList->id)->first();
                                	$ans = json_decode($matchdata->user_list);
                                ?>
                         
                                @foreach($list as $data)
                                <?php 
                                	$chk='';
                                	if(!empty($ans)){
                                		foreach($ans as $data1){
											if($data1 == $data->id){
												$chk ='checked';
											}
										}
                                	}
								?>
                                    <?php 
                                        if (0 == $i % 2){
                                            $trclr = 'even';
                                        }
                                        else{
                                            $trclr = 'odd';
                                        }
                                    ?>
                                    <tr class="{{$trclr}}">
                                        <td>{{$i}}</td>
                                        <td>{{$data->user_name}}</td>

                                        <td><input type="checkbox" name="usercheck[]" id="usercheck" value="{{$data->id}}" class="myuserwise userWiseLock" {{$chk}}></td>

                                       
                                    </tr>
                                    @php $i++; $count++;@endphp
                                @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- End User Wise Lock Modal -->

<div class="modal credit-modal" id="bookModal">
    <div class="modal-dialog">
        <div class="modal-content light-grey-bg-1">
            <div class="modal-header">
                <h4 class="modal-title text-color-blue-1">Fancy Book</h4>
                <button type="button" class="close" data-dismiss="modal"><img src="{{ URL::to('asset/img/close-icon.png')}}"></button>
            </div>
            <div class="modal-body table_book">
                <table class="table">
                    <thead>
                        <th><b>Score</b></th>
                        <th><b>Amount</b></th>
                    </thead>
                    <tbody>
                        <tr>
                            <td><b>285</b></td>
                            <td class="text-color-red"><b>-2308</b></td>
                        </tr>
                        <tr>
                            <td><b>286</b></td>
                            <td class="text-color-red"><b>-2308</b></td>
                        </tr>
                        <tr>
                            <td><b>287</b></td>
                            <td class="text-color-red"><b>-798</b></td>
                        </tr>
                        <tr>
                            <td><b>288</b></td>
                            <td class="text-color-red"><b>-792</b></td>
                        </tr>
                        <tr>
                            <td><b>289</b></td>
                            <td class="text-color-red"><b>-868</b></td>
                        </tr>
                        <tr>
                            <td><b>290</b></td>
                            <td class="text-color-red"><b>-566</b></td>
                        </tr>
                        <tr>
                            <td><b>291</b></td>
                            <td class="text-color-red"><b>-582</b></td>
                        </tr>
                        <tr>
                            <td><b>292</b></td>
                            <td class="text-color-red"><b>-478</b></td>
                        </tr>
                        <tr>
                            <td><b>293</b></td>
                            <td class="text-color-green"><b>222</b></td>
                        </tr>
                        <tr>
                            <td><b>294</b></td>
                            <td class="text-color-green"><b>806</b></td>
                        </tr>
                        <tr>
                            <td><b>295</b></td>
                            <td class="text-color-green"><b>1004</b></td>
                        </tr>
                        <tr>
                            <td><b>296</b></td>
                            <td class="text-color-green"><b>1648</b></td>
                        </tr>
                        <tr>
                            <td><b>297</b></td>
                            <td class="text-color-green"><b>1864</b></td>
                        </tr>
                        <tr>
                            <td><b>298</b></td>
                            <td class="text-color-green"><b>1566</b></td>
                        </tr>
                        <tr>
                            <td><b>299</b></td>
                            <td class="text-color-green"><b>2054</b></td>
                        </tr>
                        <tr>
                            <td><b>300</b></td>
                            <td class="text-color-green"><b>2054</b></td>
                        </tr>
                        <tr>
                            <td><b>301</b></td>
                            <td class="text-color-green"><b>2062</b></td>
                        </tr>
                        <tr>
                            <td><b>302</b></td>
                            <td class="text-color-green"><b>2062</b></td>
                        </tr>
                        <tr>
                            <td><b>303</b></td>
                            <td class="text-color-green"><b>2288</b></td>
                        </tr>
                        <tr>
                            <td><b>304</b></td>
                            <td class="text-color-green"><b>2302</b></td>
                        </tr>
                        <tr>
                            <td><b>305</b></td>
                            <td class="text-color-green"><b>2352</b></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal credit-modal" id="rejectModal">
    <div class="modal-dialog">
        <div class="modal-content light-grey-bg-1">
            <div class="modal-header">
                <h4 class="modal-title text-color-blue-1">Reject Bet</h4>
                <button type="button" class="close" data-dismiss="modal"><img src="{{ URL::to('asset/img/close-icon.png')}}"></button>
            </div>
            <div class="modal-body reject_wrap">
                <form action="" class="mt-4">
                    <div class="col-12">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4"><label class="label-control">Bet ID :</label></div>
                                <div class="col-md-6"><input type="text" class="form-control" id="" name=""></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-4"><label class="label-control">Password :</label></div>
                                <div class="col-md-6"><input type="password" maxlength="8" class="form-control" id="" name=""></div>
                            </div>
                        </div>
                        <div class="form-group text-center">
                            <button type="submit" class="submit-btn text-color-yellow">Reject Multiple Bets</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</section>

<input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}">

<script type="text/javascript">
var _token = $("input[name='_token']").val();
$(document).ready(function(){
    var matchid='{{$matchList->match_id}}';
    $('#userSearchodd').keyup(function() {
        var val = $('#userSearchodd').val();
        $.ajax({
            type: "post",
            url:'{{route("risk_management_odds_search")}}',
            data: {
                _token: _token,
                search:val,
                matchid:matchid,
            },    

            beforeSend:function(){
                $('#site_bet_loading1').show();
            },
            complete: function(){
                $('#site_bet_loading1').hide();
            },          
            success: function(data) 
            {
                $("#match_odds_bet").html(data);
            }
        });
    });
    $('#userSearchbm').keyup(function() {
        var val = $('#userSearchbm').val();
        $.ajax({
            type: "post",
            url:'{{route("risk_management_bm_search")}}',
            data: {
                _token: _token,
                search:val,
                matchid:matchid,
            },
            beforeSend:function(){
                $('#site_bet_loading1').show();
            },
            complete: function(){
                $('#site_bet_loading1').hide();
            },              
            success: function(data) 
            {
                $("#match_bm_bet").html(data);
            }
        });
    });
    $('#userSearchfnc').keyup(function() {
        var val = $('#userSearchfnc').val();
        $.ajax({
            type: "post",
            url:'{{route("risk_management_fancy_search")}}',
            data: {
                _token: _token,
                search:val,
                matchid:matchid,
            }, 
            beforeSend:function(){
                $('#site_bet_loading1').show();
            },
            complete: function(){
                $('#site_bet_loading1').hide();
            },             
            success: function(data) 
            {
                $("#match_fancy_bet").html(data);
            }
        });
    });
});
    $(".userWiseLock").change(function(){
        var _token = $("input[name='_token']").val();
        var matchid='{{$matchList->match_id}}';
        var event_id='{{$matchList->event_id}}';
        var mid='{{$matchList->id}}';
        var checks = $('input[type="checkbox"]:checked').map(function() {
            return $(this).val();
        }).get()

        $.ajax({
            type: "POST",
            url: '{{route("userWiseBlock")}}',
            data: {
                _token: _token,
                matchid: matchid,
                event_id: event_id,
                checks : checks,
                mid : mid
            },
            beforeSend:function(){
                $('#site_bet_loading1').show();
            },
            complete: function(){
                $('#site_bet_loading1').hide();
            },
            success: function(data) {
               toastr.success('Bet Lock Successfully !!!');
            }
        });
    });

    function delete_bet(bid)
    {
    	if(confirm("Are you sure you want to delete this bet?"))
    	{
    		var _token = $("input[name='_token']").val();
    		$.ajax({
    			type: "POST",
    			url: '{{route("delete_user_bet")}}',
    			data: {_token:_token,bid:bid},
                beforeSend:function(){
                    $('#site_bet_loading1').show();
                },
                complete: function(){
                    $('#site_bet_loading1').hide();
                },
    			success: function(data){
    				if(data.trim()!='Fail')
    				{	
    					alert("Bet deleted.");
    					$("#rollback_row_"+bid).show();
    					$("#delete_row_"+bid).hide();
    				}
    				else
    				{
    					alert("Problem in bet deleted. Try again.");
    					$("#rollback_row_"+bid).hide();
    					$("#delete_row_"+bid).show();
    				}
    			}
    		});
    	}
    	else
    		return false;
    }
    function rollback_bet(bid)
    {
    	if(confirm("Are you sure you want to rollback this bet?"))
    	{
    		var _token = $("input[name='_token']").val();
    		$.ajax({
    			type: "POST",
    			url: '{{route("rollback_user_bet")}}',
    			data: {_token:_token,bid:bid},
                beforeSend:function(){
                    $('#site_bet_loading1').show();
                },
                complete: function(){
                    $('#site_bet_loading1').hide();
                },
    			success: function(data){
    				if(data.trim()!='Fail')
    				{	
    					alert("Bet rollbacked.");
    					$("#rollback_row_"+bid).hide();
    					$("#delete_row_"+bid).show();
    				}
    				else
    				{
    					alert("Problem in bet deleted. Try again.");
    					$("#rollback_row_"+bid).show();
    					$("#delete_row_"+bid).hide();
    				}
    			}
    		});
    	}
    	else
    		return false;
    }
    function matchDeclareRedirect() 
    {
        var match_id = '{{$matchList->id}}';
        var _token = $("input[name='_token']").val();
        $.ajax({
            type: "POST",
            url: '{{route("matchDeclareRedirect")}}',
            data: {
                _token: _token,
                match_id:match_id,
            },
            /*beforeSend:function(){
                $('#site_bet_loading1').show();
            },
            complete: function(){
                $('#site_bet_loading1').hide();
            },*/
            success: function(data) {                
                if(data.result=='error'){
                    window.location.href = "{{ route('home')}}";
                }
            }
        });
    }
    $(document).ready(function(){
        setInterval(function() {
            matchDeclareRedirect(); 
        }, 10000);

    	//default call
    	var _token = $("input[name='_token']").val();
		var match_type='{{$matchList->sports_id}}';
		var matchid='{{$matchList->match_id}}';
		var matchname='{{$matchList->match_name}}';
		var event_id='{{$matchList->event_id}}';
        var match_m='{{$matchList->suspend_m}}';
        var match_b='{{$matchList->suspend_b}}';
        var match_f='{{$matchList->suspend_f}}';

		$.ajax({
			type: "POST",
			url: '{{route("risk_management_details_ajax",$matchList->match_id)}}',
			data: {_token:_token,matchtype:match_type,matchid:matchid,matchname:matchname,event_id:event_id,match_m:match_m},
			beforeSend:function(){
                $('#site_bet_loading1').show();
            },
            complete: function(){
                $('#site_bet_loading1').hide();
            },
			success: function(data){
				$("#inplay-tableblock").html(data);
			}
		});
    	
    	//fancy and bookmaker
    	$.ajax({
    		type: "POST",
    		url: '{{route("risk_management_matchCallForFancyNBM",$matchList->match_id)}}',
    		data: {_token:_token,matchtype:match_type,event_id:event_id,matchname:matchname,matchid:matchid,match_b:match_b,match_f:match_f},
    		beforeSend:function(){
                $('#site_bet_loading1').show();
            },
            complete: function(){
                $('#site_bet_loading1').hide();
            },
    		success: function(data){  
                if(data == '~~'){
                    $('.noData').css('display','none');
                }  
    			if(data!='')
    			{
    				var spl=data.split('~~');
                    if(spl[0]==''){
                        $('.bookmakerHide').css('display','none');
                    }
    				$("#inplay-tableblock-bookmaker").html(spl[0]);
    				$("#inplay-tableblock-fancy").html(spl[1]);
    			}
    		}
    	});
    		
    	setInterval(function(){
    	   var _token = $("input[name='_token']").val();
			var match_type='{{$matchList->sports_id}}';
			var matchid='{{$matchList->match_id}}';
			var matchname='{{$matchList->match_name}}';
			var event_id='{{$matchList->event_id}}';
			var match_m='{{$matchList->suspend_m}}';
            var match_b='{{$matchList->suspend_b}}';
            var match_f='{{$matchList->suspend_f}}'; 

			$.ajax({
    			type: "POST",
    			url: '{{route("risk_management_details_ajax",$matchList->match_id)}}',
    			data: {_token:_token,matchtype:match_type,matchid:matchid,matchname:matchname,event_id:event_id,match_m:match_m},
    			/*beforeSend:function(){
                    $('#site_bet_loading1').show();
                },
                complete: function(){
                    $('#site_bet_loading1').hide();
                },*/
    			success: function(data){
    				$("#inplay-tableblock").html(data);

    			}
    		});
    		
    		//BM and Fancy
    		$.ajax({
    			type: "POST",
    			url: '{{route("risk_management_matchCallForFancyNBM",$matchList->match_id)}}',
    			data: {_token:_token,matchtype:match_type,event_id:event_id,matchname:matchname,matchid:matchid,match_b:match_b,match_f:match_f},
    			/*beforeSend:function(){
                    $('#site_bet_loading1').show();
                },
                complete: function(){
                    $('#site_bet_loading1').hide();
                },*/
    			success: function(data){
    				if(data!='')
    				{
    					var spl=data.split('~~');
                    if(spl[0]==''){
                      $('.bookmakerHide').css('display','none');
                    }
    					$("#inplay-tableblock-bookmaker").html(spl[0]);
    					$("#inplay-tableblock-fancy").html(spl[1]);
    				}
    			}
    		});
    		
    		//odds bets
            var valodd = $('#userSearchodd').val();
            var valbm = $('#userSearchbm').val();
            var valfnc = $('#userSearchfnc').val();
    		$.ajax({
    			type: "POST",
    			url: '{{route("risk_management_odds_bet")}}',
    			data: {_token:_token,matchid:matchid,valodd:valodd,valbm:valbm,valfnc:valfnc},
    			/*beforeSend:function(){
                    $('#site_bet_loading1').show();
                },
                complete: function(){
                    $('#site_bet_loading1').hide();
                },*/
    			success: function(data){
    				if(data!='')
    				{
    					var spl=data.split('~~');
    					$("#match_odds_bet").html(spl[0]);
    					$("#match_bm_bet").html(spl[1]);
    					$("#match_fancy_bet").html(spl[2]);
    				}
    			}
    		});
    	},10000);
    });

    $(".chkaction").on('click', function(event){
        var _token = $("input[name='_token']").val();
        var status = $(this).attr('data-status');
        var suspend = $(this).attr('data-suspend');
        var chk=(this.checked ? $(this).val() : "");
        var fid='{{$matchList->id}}';
        $.ajax({
            type: "POST",
            url: '{{route("saveMatchSuspend")}}',
            data: {_token:_token,status:status,fid:fid,suspend:suspend},
            beforeSend:function(){
                $('#site_bet_loading1').show();
            },
            complete: function(){
                $('#site_bet_loading1').hide();
            },
            success: function(data){
                location.reload();
                if(data.success!='success')
                    alert('Problem in action update');
            }
        });
    });
    
    $(document).ready(function(){
      $('[data-toggle="tooltip"]').tooltip();
    });
</script>
@endsection