@extends('layouts.app')
@section('content')
<?php 
$loginuser = Auth::user(); 
use App\Sport;
use App\Match;
use App\MyBets;
?>

<style type="text/css">
    /*.betloaderimage1 {
        background-image: url(../../asset/front/img/loaderajaxbet.gif);
        background-repeat: no-repeat;
        background-position: center;
        min-height: 100px;
        background-size: 60px;
    }*/
</style>
<!--<div id="site_bet_loading1" class="betloaderimage1 loader-style1" style="display: none;"></div>-->
<section class="myaccount-section">
    <div class="container">
        <div class="row">
            <div class="col-md-12 pl-0">
                <div class="downline-block">
                    {{--<div class="search-wrap">
                        <svg width="19" height="19" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12.547 11.543H12l-.205-.172a4.539 4.539 0 001.06-2.914A4.442 4.442 0 008.41 4C5.983 4 4 5.989 4 8.457a4.442 4.442 0 004.445 4.457c1.094 0 2.12-.411 2.905-1.062l.206.171v.548L14.974 16 16 14.971l-3.453-3.428zm-4.102 0a3.069 3.069 0 01-3.077-3.086 3.068 3.068 0 013.077-3.086 3.069 3.069 0 013.076 3.086 3.069 3.069 0 01-3.076 3.086z" fill="rgb(30,30,30"></path>
                        </svg>
                        <div>
                            <input class="search-input navy-light-bg" type="text" name="userId" id="userId" placeholder="Find member...">
                            <button class="search-but yellow-bg1" id="searchUserId">Search</button>
                        </div>
                    </div>--}}
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
                    <h1>My Bets</h1>
                </div>
                <div class="in_play_tabs-2 mb-0">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link text-color-blue-1 white-bg active" href="#exchange" data-toggle="tab">Current Bets</a>
                        </li>                       
                    </ul>
                </div>
                
                <div class="summery-table mt-3">
                    <table class="table custom-table mybets-table">
                        <thead>
                            <tr class="light-grey-bg">
                                <th width="9%">Bet ID</th>
                                <th width="9%" >PL ID</th>
                                <th>Market</th>
                                <th width="12%" class="text-right">Selection</th>
                                <th width="4%" class="text-right">Type</th>
                                <th width="9%" class="text-right">Bet placed</th>
                                <th width="8%" class="text-right">Stake</th>
                                <th width="8%" class="text-right">Avg. odds <br> matched</th>
                            </tr>
                        </thead>
                        <tbody id="bodyData">
                            @if(count($getresult) > 0)
                              @foreach($getresult as $data)
                                        
                                            <?php 
                                            $sports = Sport::where('sId', $data->sportID)->first();
                                            $matchdata = Match::where('event_id', $data->match_id)->first();
                                            ?>
                                            <tr class="white-bg">
                                                <td width="9%"><img src="{{ URL::to('asset/front/img/plus-icon.png') }}"> <a class="text-color-blue-light">{{$data->id}}</a></td>
                                                <td width="9%">{{$user->user_name}}</td>
                                                <td>{{$sports->sport_name}}<i class="fas fa-caret-right text-color-grey"></i> <strong>{{$matchdata->match_name}}</strong> <i class="fas fa-caret-right text-color-grey"></i>{{$data->bet_type}}</td>
                                                <td width="12%" class="text-right">{{$data->team_name}}</td>
                                                @if($data->bet_side == 'lay')
                                                    @if($data->bet_type=='SESSION')
                                                        <td width="4%" class="text-right bet_type_uppercase" style="color: #e33a5e !important;">no</td>
                                                    @else
                                                        <td width="4%" class="text-right bet_type_uppercase" style="color: #e33a5e !important;">{{$data->bet_side}}</td>
                                                    @endif
                                                @else
                                                    @if($data->bet_type=='SESSION')
                                                        <td width="4%" class="text-right bet_type_uppercase" style="color: #1f72ac !important;">yes</td>
                                                    @else
                                                        <td width="4%" class="text-right bet_type_uppercase" style="color: #1f72ac !important;">{{$data->bet_side}}</td>
                                                    @endif
                                                @endif
                                                <td width="8%" class="text-right">{{$data->created_at}}</td>
                                                <td width="8%" class="text-right">{{$data->bet_amount}}</td>
                                                <td width="8%" class="text-right">{{$data->bet_odds}}
                                                    @if($data->bet_type=='SESSION')
                                                        <br>
                                                        <span class="vlmtxt">({{$data->bet_oddsk}})</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        
                                    @endforeach
                                    @else
                                    <tr><td colspan="7">No bet found.</td></tr>
                                    @endif
                        </tbody>
                    </table>
                   
                </div>
            </div>
        </div>
    </div>
</section>
@endsection