@extends('layouts.front_layout')

@section('content')
<style>
    body {
        overflow: hidden;
    }

</style>
<?php
use Carbon\Carbon;
use App\User;
$getUser = Session::get('playerUser');
$delayTime=0;
if(!empty($getUser)){
	$delay = User::where('id',$getUser->id)->first();
	$delayTime = ($delay->dealy_time)*1000;
    
}
?>
<section>

    <div class="container-fluid">

        <div class="main-wrapper">
            @include('layouts.leftpanel')

            <!-- end right panel -->

            <div class="middle-section">

                <div class="middle-wraper">

                    <div class="game_info_match blue-gradient-bg4 text-color-grey d-lg-none">
                        <span>Cricket</span>
                        <ul>
                            <li><img src="{{ URL::to('asset/front/img/clock-green-icon.png') }}" alt=""> In-Play</li>
                        </ul>
                    </div>

                    <div class="match-tracktop black-bg1 text-color-white">
                        <div class="three-tag">
                            <?php $split=explode(" v ",strtolower($match->match_name)); ?>
                            <h4 class="d-none d-lg-block">
                                <span @if($inplay=='True' ) class="inplay_active" @else class="inplay_deactive" @endif> &nbsp; </span>
                                &nbsp;&nbsp;&nbsp;
                                {{strtoupper($split[0])}}
                            </h4>
                            <h4 class="d-lg-none">
                                <span @if($inplay=='True' ) class="inplay_active" @else class="inplay_deactive" @endif> &nbsp; </span>
                                &nbsp;&nbsp;&nbsp;
                                {{substr($split[0],0,3)}}
                            </h4>
                            <span class="span1">
                                <div>INN 2 | 4.2 OV</div>
                                <span class="text-color-white span3">
                                    <div>9.30 CRR | 8.73 RRR</div> 93/4 : 44/3 <div>9.42 CRR | 9.38 RRR</div>
                                </span>
                            </span>
                            <h4 class="text-right d-none d-lg-block"> {{strtoupper($split[1])}}</h4>
                            <h4 class="text-right d-lg-none"> {{substr($split[1],0,3)}}</h4>
                        </div>
                        <div class="arrowup-icon"> <i class="fas fa-chevron-up text-fill-yellow"></i> </div>
                    </div>


                    <div class="match-track-block">

                        <div class="toprisk-block white-bg">

                            <ul class="d-none d-lg-flex">
                                <li>
                                    <a href="#"> </a>
                                    <svg class="bg-shape" width="100" height="25" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M120 0l-8.293 17.752C109.837 21.755 104.738 25 100.33 25H19.669c-4.413 0-9.506-3.243-11.377-7.248L-.001 0h120z"></path>
                                    </svg>
                                </li>
                                <li>
                                    <a href="#"> </a>
                                    <svg class="bg-shape" width="100" height="25" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M100 0l-8.293 17.752C89.837 21.755 84.738 25 80.33 25H-.331c-4.413 0-9.506-3.243-11.377-7.248L-20.001 0h120z"></path>
                                    </svg>
                                </li>
                            </ul>
                            
                            <ul class="toprisk_pinrefresh d-lg-none">
                                <li>
                                    <a id="pinrisk" class="text-color-white"><img src="{{ URL::to('asset/front/img/pin.svg') }}" alt=""> Pin</a>
                                </li>
                                <li>
                                    <a class="text-color-white"><img src="{{ URL::to('asset/front/img/refresh.svg') }}" alt=""> Refresh</a>
                                </li>
                            </ul>

                            <div class="twodiv-ireland">
                                <div class="ireland-txt dark-grey-bg-1 text-color-blue-2">Match Odds</div>
                                <?php
								$match_date='';
								if (Carbon::parse($match['match_date'])->isToday())
									$match_date = date('H:i A',strtotime($match['match_date']));
								else if (Carbon::parse($match['match_date'])->isTomorrow())
									$match_date ='Tomorrow '.date('H:i A',strtotime($match['match_date']));
								else
									$match_date =$match['match_date'];
								?>

                                <div class="timeblockireland"> <img src="{{ URL::to('asset/front/img/clock-icon.png') }} "> {{$match_date}} </div>
                                <div class="minmax-txt light-grey-bg-5">
                                    <span class="text-color-blue-3">Min</span>
                                    <span id="div_min_bet_odds_limit">{{$match->min_bet_odds_limit}}</span>
                                    <span class="text-color-blue-3">Max</span>
                                    <span id="div_max_bet_odds_limit">{{$match->max_bet_odds_limit}}</span>

                                </div>
                            </div>

                        </div>

                    </div>

                    <div class="inplay-tableblock" id="inplay-tableblock">

                        <!--<div id="site_statistics_loading" class="loaderimage"></div>-->
                        {!!$html!!}
                    </div>
                    @if($match->sports_id=='4')
                    <table class="table custom-table inplay-table-1 w1-table cricket-table1" id="inplay-tableblock-bookmaker">
                        {!!$html_bm!!}
                    </table>

                    <div id="fancybetdiv" class="fancy-bet-txt" style="display:none">
                        <h4>
                            <span class="blue-bg-3 text-color-white"> <img src="{{ URL::to('asset/front/img/clock-green-icon.png') }} "> <b> Fancy Bet </b> </span>
                            <a data-toggle="modal" data-target="#rulesFancyBetsModal"> <img src="{{ URL::to('asset/front/img/info-icon.png') }}"> </a>
                        </h4>
                    </div>

                    <table class="table custom-table inplay-table w1-table " id="inplay-tableblock-fancy">
                        {!!$html_two!!}
                    </table>
                    @endif

                    <div class="mb-5"></div>

                </div>

            </div>

            <div class="rightblock-games white-bg">
                <div class="betslip-block">
                    <a class="collape-link text-color-white blue-gradient-bg1" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
                        Bet Slip <img src="{{ URL::to('asset/front/img/minus-icon.png') }} ">
                    </a>
                    <div id="site_bet_loading" class="betloaderimage" style="display: none;"></div>
                    <div class="collapse show" id="collapseExample">
                        <div class="card card-body" id="betslip-block">
                            <!--  Click on the odds to add selections to the betslip. -->
                            <div id="betMsgALL"></div>
                            <div class="betslip_board showForm" style="display: none;">
                                <ul class="betslip_head lightblue-bg1">
                                    <li class="col-bet" id="back_or_lay">Back (Bet For)</li>
                                    <li class="col-odd">Odds</li>
                                    <li class="col-stake">Stake</li>
                                    <li class="col-profit" id="profit_liability">Profit</li>
                                </ul>
                                <form id="betform">
                                    <div class="betslip_middle">
                                        <h4 class="active_dots" id="team_name_div">{{$match->match_name}}</h4>
                                        <div class="betslip_box ">
                                            <div class="betn">
                                                <a class="delete text-color-red remove_new_bet_fancy" style="cursor: pointer;">
                                                    <i class="fas fa-window-close"></i></a>
                                                <span class="shortamount" id="bet_for">England</span>
                                                <span>Match Odds</span>
                                            </div>
                                            <div class="col-odd text-color-blue-2">
                                                <input id="odds_val" type="number" step="0.01" maxlength="6" tabindex="0" value="" class="form-control" required="required" onkeypress="return isNumber(event)">
                                            </div>
                                            <div class="col-stake text-color-blue-2">
                                                <input id="inputStake" type="text" maxlength="7" tabindex="1" value="" class="form-control" onkeyup="getCalculated(this.value)" required="required" onkeypress="return isNumber(event)">
                                            </div>
                                            <div class="col-profit profil amountint">0</div>
                                            <div class="col-stake_list ">
                                                <ul>
                                                    <li><a class="btn light-grey-bg-6 text-color-black1 match_odd" data-odd="10" id="selectStake_1">10</a></li>
                                                    <li><a class="btn light-grey-bg-6 text-color-black1 match_odd" data-odd="20" id="selectStake_2">20</a></li>
                                                    <li><a class="btn light-grey-bg-6 text-color-black1 match_odd" data-odd="50" id="selectStake_3">50</a></li>
                                                    <li><a class="btn light-grey-bg-6 text-color-black1 match_odd" data-odd="100" id="selectStake_4">100</a></li>
                                                    <li><a class="btn light-grey-bg-6 text-color-black1 match_odd" data-odd="200" id="selectStake_5">200</a></li>
                                                    <li><a class="btn light-grey-bg-6 text-color-black1 match_odd" data-odd="500" id="selectStake_6">500</a></li>
                                                </ul>
                                            </div>
                                            <!--<div class="keep-option">
                                <p class="dynamic-min-bet">Min Bet: <strong>1</strong></p>
                            </div>-->
                                        </div>
                                    </div>
                                    <div class="betslip_bottom">
                                        <div class="sumtxt">
                                            <div>Liability</div>
                                            <div><span class="text-color-red">0.00</span></div>
                                        </div>
                                        <ul class="btn-wrap">
                                            <li><a class="add_player grey-gradient-bg" id="cancel_bet_form">Cancel All</a></li>
                                            <li><a class="submit-btn text-color-yellow" onclick="saveBetcall()" style="cursor:pointer">Place Bet</a></li>
                                        </ul>
                                        <ul class="slip-option">
                                            <li>
                                                <input checked="checked" required="required" id="comfirmBets" type="checkbox"><label for="comfirmBets">Please confirm your bets.</label>
                                            </li>
                                        </ul>
                                    </div>
                                    <input type="hidden" id="betTypeAdd" value="" />
                                    <input type="hidden" id="betSide" value="" />
                                    <input type="hidden" id="teamNameBet" value="" />
                                    <input type="hidden" id="bet-profit" value="" />
                                    <input type="hidden" id="team1" value="" />
                                    <input type="hidden" id="team2" value="" />
                                    <input type="hidden" id="team3" value="" />
                                    <input type="hidden" id="tot_expo" value="" />
                                    <input type="hidden" id="tot_bal" value="" />
                                    <input type="hidden" id="odds_volume" value="" />
                                    <input type="hidden" id="odds_limit" value="{{$match->odds_limit}}" />
                                    <input type="hidden" id="team_id" value="" />
                                    <input type="hidden" id="final_odds_val_team1" value="" />
                                    <input type="hidden" id="final_odds_val_team2" value="" />
                                    <input type="hidden" id="final_odds_val_team3" value="" />
                                    <input type="hidden" id="final_odds_lay_val_team1" value="" />
                                    <input type="hidden" id="final_odds_lay_val_team2" value="" />
                                    <input type="hidden" id="final_odds_lay_val_team3" value="" />


                                </form>
                            </div>



                        </div>
                    </div>
                </div>

                <!--bet display table-->

                <div class="betslip-block mt-10" @if($total_todays_bet==0) style='display:none' @endif id="bet_display_table">
                    <a class="collape-link text-color-white blue-gradient-bg1" data-toggle="collapse" href="#collapseExample1" role="button" aria-expanded="false" aria-controls="collapseExample1">
                        <img src="{{ URL::to('asset/front/img/refresh-white.png')}}" class="slip_refresh" alt=""> Open Bets <img src="{{ URL::to('asset/front/img/minus-icon.png')}}">
                    </a>
                    <div class="collapse show" id="collapseExample1">
                        <div class="card card-body">
                            <div class="open_bets_wrap betslip_board">
                                <div class="slip_sort">
                                    <?php /*?><select name="select_bet_on_match" id="select_bet_on_match" onchange="call_display_bet_list(this.value)">
                                        @php $first_match_bet=''; @endphp
                                        @for($i=0;$i<sizeof($match_name_bet);$i++) @if($i==0) @php $first_match_bet=$match_name_bet[$i]['event_id']; @endphp 
                                        <option value="{{$match_name_bet[$i]['event_id']}}~~All">{{$match_name_bet[$i]['match_name']}} - All</option>
                                            @endif
                                            <option value="{{$match_name_bet[$i]['event_id']}}~~{{$match_name_bet[$i]['bet_for']}}">{{$match_name_bet[$i]['match_name']}} - Match {{$match_name_bet[$i]['bet_for']}}</option>
                                            @endfor
                                    </select><?php */?>
                                    <select name="select_bet_on_match" id="select_bet_on_match" onchange="call_display_bet_list(this.value)">
                                        <option value="{{$match->event_id}}~~All">All Bet</option>
                                    </select>
                                </div>
                                <ul class="betslip_head lightblue-bg1">
                                    <li class="col-bet"><strong>Matched</strong></li>
                                </ul>
                                <div id="divbetlist">
                                	
                                    @php
                                    $j=0; $k=0;
                                    @endphp
                                    @foreach($my_placed_bets as $bet)
                                    <?php /*?>@if($first_match_bet==$bet->match_id && $bet->bet_side=='back')<?php */?>
                                    @if($bet->bet_side=='back')
                                        @if($j==0)
                                        <ul class="betslip_head">
                                            <li class="col-bet">Back (Bet For)</li>
                                            <li class="col-odd">Odds</li>
                                            <li class="col-stake">Stake</li>
                                            <li class="col-profit">Profit</li>
                                        </ul>
                                        @endif
                                        <div class="betslip_box light-blue-bg-1" id="backbet">
                                                <div class="betn">
                                                    <span class="slip_type lightblue-bg2">
                                                        @if($bet->bet_type=='ODDS')
                                                            BACK
                                                        @elseif($bet->bet_type=='SESSION')
                                                            YES
                                                        @endif
                                                    </span>
                                                    <span class="shortamount">{{$bet->team_name}}</span>
                                                    <span>
                                                        {{$bet->bet_type}}
                                                    </span>
                                                </div>
                                                <div class="col-odd text-color-blue-2 text-center">{{$bet->bet_odds}}<?php /*?>{{($bet->bet_amount)}}<?php */?></div>
                                                <div class="col-stake text-color-blue-2 text-center">
                                                    <?php /*?>@if($bet->bet_type=='ODDS')
                                                    {{$bet->bet_odds/($bet->bet_amount-1)}}
                                                    @elseif($bet->bet_type=='BOOKMAKER')
                                                    {{($bet->bet_odds/$bet->bet_amount)*100}}
                                                    @endif<?php */?>
                                                    {{($bet->bet_amount)}}
                                                </div>
                                                <div class="col-profit"><?php /*?>{{$bet->bet_odds}}<?php */?>
                                                    @if($bet->bet_type=='ODDS')
                                                        {{($bet->bet_profit)}}
                                                    @elseif($bet->bet_type=='SESSION')
                                                        {{($bet->bet_oddsk)}}
                                                    @endif
                                                </div>
                                        </div>
                                    	@php $j++  @endphp
                                    @endif
                                    @endforeach
                                    @foreach($my_placed_bets as $bet)
                                   
                                   
                                    <?php /*?>@if($first_match_bet==$bet->match_id && $bet->bet_side=='lay')<?php */?>
                                    @if($bet->bet_side=='lay')
                                        @if($k==0)
                                        <ul class="betslip_head">
                                            <li class="col-bet">Lay (Bet Against)</li>
                                            <li class="col-odd">Odds</li>
                                            <li class="col-stake">Stake</li>
                                            <li class="col-profit">Liability</li>
                                        </ul>
                                        @endif
                                        <div class="betslip_box lightpink-bg2" id="laybet">
                                            <div class="betn">
                                                <span class="slip_type lightpink-bg1">
                                                    @if($bet->bet_type=='ODDS')
                                                    LAY
                                                    @elseif($bet->bet_type=='SESSION')
                                                    NO
                                                    @endif</span>
                                                <span class="shortamount">{{$bet->team_name}}</span>
                                                <span>{{$bet->bet_type}}</span>
                                            </div>
                                            <div class="col-odd text-color-blue-2 text-center"><?php /*?>{{($bet->bet_amount)}}<?php */?>{{$bet->bet_odds}}</div>
                                            <div class="col-stake text-color-blue-2 text-center">
                                                <?php /*?>@if($bet->bet_type=='ODDS')
                                                {{$bet->bet_odds/($bet->bet_amount-1)}}
                                                @elseif($bet->bet_type=='BOOKMAKER')
                                                {{($bet->bet_odds/$bet->bet_amount)*100}}
                                                @endif<?php */?>
                                                {{($bet->bet_amount)}}
                                            </div>
                                            <div class="col-profit"><?php /*?>{{$bet->bet_odds}}<?php */?>
                                                @if($bet->bet_type=='ODDS')
                                                {{($bet->exposureAmt)}}
                                                @elseif($bet->bet_type=='SESSION')
                                                {{($bet->bet_oddsk)}}
                                                @endif
                                            </div>
                                        </div>
                                        @php $k++ @endphp
                                    @endif
                                    @endforeach
                                </div>
                                <ul class="slip-option">
                                    <li>
                                        <input id="showBetInfo" type="checkbox"><label for="showBetInfo">Bet Info</label>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <!--end for bet display table-->
                </div>
            </div>

        </div>
    </div>

<div class="modal rulesfancy_betsmodal" id="rulesFancyBetsModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header black-bg3">
                <h4 class="modal-title text-color-yellow1">Rules of Fancy Bets</h4>
                <button type="button" class="close" data-dismiss="modal"><img src="{{ URL::to('asset/front/img/icon-close-yellow.svg') }}" alt=""></button>
            </div>
            <div class="modal-body white-bg">
                <div class="rules_fancy_bets">
                    <ol>
                        <li>Once all session/fancy bets are completed and settled there will be no reversal even if the Match is Tied or is Abandoned.</li>
                        <li>Advance Session or Player Runs and all Fancy Bets are only valid for 20/50 overs full match each side. (Please Note this condition is applied only in case of Advance Fancy Bets only).</li>
                        <li>All advance fancy bets market will be suspended 60 mins prior to match and will be settled.</li>
                        <li>Under the rules of Session/Fancy Bets if a market gets Suspended for any reason whatsoever and does not resume then all previous Bets will remain Valid and become HAAR/JEET bets.</li>
                        <li>Incomplete Session/Fancy Bet will be cancelled but Complete Session will be settled.</li>
                        <li>In the case of Running Match getting Cancelled/ No Result/ Abandoned but the session is complete it will still be settled. Player runs / fall of wicket will be also settled at the figures where match gets stopped due to rain for the inning (D/L) , cancelled , abandoned , no result.</li>
                        <li>If a player gets Retired Hurt and one ball is completed after you place your bets then all the betting till then is and will remain valid.</li>
                        <li>Should a Technical Glitch in Software occur, we will not be held responsible for any losses.</li>
                        <li>Should there be a power failure or a problem with the Internet connection at our end and session/fancy market does not get suspended then our decision on the outcome is final.</li>
                        <li>All decisions relating to settlement of wrong market being offered will be taken by management. Management will consider all actual facts and decision taken will be full in final.</li>
                        <li>Any bets which are deemed of being suspicious, including bets which have been placed from the stadium or from a source at the stadium maybe voided at anytime. The decision of whether to void the particular bet in question or to void the entire market will remain at the discretion of Company. The final decision of whether bets are suspicious will be taken by Company and that decision will be full and final.</li>
                        <li>Any sort of cheating bet , any sort of Matching (Passing of funds), Court Siding (Ghaobaazi on commentary), Sharpening, Commission making is not allowed in Company, If any company User is caught in any of such act then all the funds belonging that account would be seized and confiscated. No argument or claim in that context would be entertained and the decision made by company management will stand as final authority.</li>
                        <li>Fluke hunting/Seeking is prohibited in Company , All the fluke bets will be reversed. Cricket commentary is just an additional feature and facility for company user but company is not responsible for any delay or mistake in commentary.</li>
                        <li>Valid for only 1st inning.<ul>• Highest Inning Run :- This fancy is valid only for first inning of the match.</ul>
                            <ul>• Lowest Inning Run :- This fancy is valid only for first inning of the match.</ul>
                        </li>
                        <li>If any fancy value gets passed, we will settle that market after that match gets over. For example :- If any market value is ( 22-24 ) and incase the result is 23 than that market will be continued, but if the result is 24 or above then we will settle that market. This rule is for the following market.<ul>• Total Sixes In Single Match</ul>
                            <ul>• Total Fours In Single Match</ul>
                            <ul>• Highest Inning Run</ul>
                            <ul>• Highest Over Run In Single Match</ul>
                            <ul>• Highest Individual Score By Batsman</ul>
                            <ul>• Highest Individual Wickets By Bowler</ul>
                        </li>
                        <li>If any fancy value gets passed, we will settle that market after that match gets over. For example :- If any market value is ( 22-24 ) and incase the result is 23 than that market will be continued, but if the result is 22 or below then we will settle that market. This rule is for the following market.<ul>• Lowest Inning Run</ul>
                            <ul>• Fastest Fifty</ul>
                            <ul>• Fastest Century</ul>
                        </li>
                        <li>If any case wrong rate has been given in fancy ,that particular bets will be cancelled (Wrong Commentary).</li>
                        <li>In case customer make bets in wrong fancy we are not liable to delete, no changes will be made and bets will be considered as confirm bet.</li>
                        <li>Dot Ball Market Rules<ul>Wides Ball - Not Count</ul>
                            <ul>No Ball - Not Count</ul>
                            <ul>Leg Bye - Not Count as A Dot Ball</ul>
                            <ul>Bye Run - Not Count as A Dot Ball</ul>
                            <ul>Run Out - On 1st Run Count as A Dot Ball</ul>
                            <ul>Run Out - On 2nd n 3rd Run Not Count as a Dot Ball</ul>
                            <ul>Out - Catch Out, Bowled, Stumped n LBW Count as A Dot Ball</ul>
                        </li>
                        <li>Bookmaker Rules<ul>• Due to any reason any team will be getting advantage or disadvantage we are not concerned.</ul>
                            <ul>• We will simply compare both teams 25 overs score higher score team will be declared winner in ODI.</ul>
                            <ul>• We will simply compare both teams 10 overs higher score team will be declared winner in T20 matches.</ul>
                        </li>
                        <li>Penalty Runs - Any Penalty Runs Awarded in the Match (In Any Running Fancy or ADV Fancy) Will Not be Counted While Settling in our Exchange.</li>
                        <li>LIVE STREAMING OF ALL VIRTUAL CRICKET MATCHES IS AVAILABLE HERE <a href="https://www.youtube.com/channel/UCd837ZyyiO5KAPDXibynq_Q/featured"> https://www.youtube.com/channel/UCd837ZyyiO5KAPDXibynq_Q/featured</a></li>
                        <li>CHECK SCORE OF VIRTUAL CRICKET ON <a href="https://sportcenter.sir.sportradar.com/simulated-reality/cricket"> https://sportcenter.sir.sportradar.com/simulated-reality/cricket</a></li>
                        <li>Comparison Market<ul>In Comparison Market We Don't Consider Tie or Equal Runs on Both the Innings While Settling . Second Batting Team Must need to Surpass 1st Batting's team Total to win otherwise on Equal Score or Below We declare 1st Batting Team as Winner .</ul>
                        </li>
                        <li>If match is abandoned or over reduced. This rule is for the following market ( ENTIRE IPL 2020 )<ul>• Total Fours :- Average 27 fours will be given if the match is abandoned or over reduced.</ul>
                            <ul>• Total Sixes :- Average 11 sixes will be given if the match is abandoned or over reduced.</ul>
                            <ul>• Total Caught &amp; Bowled Out :- Average 0 Caught &amp; Bowled Out will be given if the match is abandoned or over reduced.</ul>
                            <ul>• Total Wide :- Average 8 wides will be given if the match is abandoned or over reduced.</ul>
                            <ul>• Total Extra :- Average 14 extras will be given if the match is abandoned or over reduced.</ul>
                            <ul>• Total No Ball :- Average 1 no ball will be given if the match is abandoned or over reduced.</ul>
                            <ul>• Total duck :- Average 1 duck will be given if the match is abandoned or over reduced.</ul>
                            <ul>• Total Fifties :- Average 2 fifties will be given if the match is abandoned or over reduced.</ul>
                            <ul>• Total Century :-Average 0 century will be given if the match is abandoned or over reduced.</ul>
                            <ul>• Total Run Out :- Average 1 run out will be given if the match is abandoned or over reduced.</ul>
                            <ul>• Total Caught out :- Average 8 caught out will be given if the match is abandoned or over reduced.</ul>
                            <ul>• Total Stump Out :- Average 0 stump out out will be given if the match is abandoned or over reduced.</ul>
                            <ul>• Total Maiden Over :- Average 0 maiden over will be given if the match is abandoned or over reduced.</ul>
                            <ul>• Total LBW :- Average 1 LBW will be given if the match is abandoned or over reduced.</ul>
                            <ul>• Total Bowled :- Average 2 bowled will be given if the match is abandoned or over reduced.</ul>
                        </li>
                        <li>Player Boundaries Fancy :- Both Four and six are valid</li>
                        <li>BOWLER RUN SESSION RULE :-<ul>IF BOWLER BOWL 1.1 OVER,THEN VALID ( FOR BOWLER 2 OVER RUNS SESSION )</ul>
                            <ul>IF BOWLER BOWL 2.1 OVER,THEN VALID ( FOR BOWLER 3 OVER RUNS SESSION )</ul>
                            <ul>IF BOWLER BOWL 3.1 OVER,THEN VALID ( FOR BOWLER 4 OVER RUNS SESSION )</ul>
                            <ul>IF BOWLER BOWL 4.1 OVER,THEN VALID ( FOR BOWLER 5 OVER RUNS SESSION )</ul>
                            <ul>IF BOWLER BOWL 9.1 OVER,THEN VALID ( FOR BOWLER 10 OVER RUNS SESSION )</ul>
                        </li>
                        <li>Total Match Playing Over ADV :- We Will Settle this Market after Whole Match gets Completed<ul>Criteria :- We Will Count Only Round- Off Over For Both the Innings While Settling (For Ex :- If 1st Batting team gets all out at 17.3 , 18.4 or 19.5 we Will Count Such Overs as 17, 18 and 19 Respectively and if Match gets Ended at 17.2 , 18.3 or 19.3 Overs then we will Count that as 17 , 18 and 19 Over Respectively... and this Will Remain Same For Both the Innings ..</ul>
                            <ul>In Case Of Rain or if Over gets Reduced then this Market will get Voided</ul>
                        </li>
                        <li>3 WKT OR MORE BY BOWLER IN MATCH ADV :-<ul>We Will Settle this Market after Whole Match gets Completed .</ul>
                            <ul>In Case Of Rain or if Over Gets Reduced then this Market Will get Voided</ul>
                        </li>
                    </ol>
                    <button type="button" class="grey-gradient-bg1 text-color-black1 btnok btn-block" data-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>
</div>

</section>
<input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>

<script>
    function isNumber(evt) {
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        return true;
    }
    $("#cancel_bet_form").click(function() {
        $(".showForm").hide();
    });

    function get_oddstable() {
        var _token = $("input[name='_token']").val();
        var match_type = '{{$match->sports_id}}';
        var matchid = '{{$match->match_id}}';
        var matchname = '{{$match->match_name}}';
        var event_id = '{{$match->event_id}}';
        var match_id = '{{$match->id}}';
        var match_m = '{{$match->suspend_m}}';
        var match_b = '{{$match->suspend_b}}';
        var match_f = '{{$match->suspend_f}}';
        //alert('aaaa'+match_id);
        $.ajax({
            type: "POST",
            url: '{{route("matchCallOdds",$match->match_id)}}',
            data: {
                _token: _token,
                matchtype: match_type,
                event_id: event_id,
                matchname: matchname,
                matchid: matchid,
                match_m: match_m,
                match_id: match_id,
            },
            success: function(data) {
				$("#inplay-tableblock").html(data);
            }
        });
    }

    function bet_Fancytable() {
       
	    //url: '{{route("matchCallForFancyNBM",$match->match_id)}}',
	   
        var _token = $("input[name='_token']").val();
		var match_type = '{{$match->sports_id}}';
        var matchid = '{{$match->match_id}}';
        var matchname = '{{$match->match_name}}';
        var event_id = '{{$match->event_id}}';
        var match_id = '{{$match->id}}';
        $.ajax({
            type: "POST",
            url: '{{route("matchCallFor_FANCY",$match->match_id)}}',
            data: {
                _token: _token,
                matchtype: match_type,
                event_id: event_id,
                match_b: '{{$match->suspend_b}}',
                match_f: '{{$match->suspend_f}}',
                match_id: match_id
            },
            success: function(data) { 
				if (data != '') {
                    $("#inplay-tableblock-fancy").html(data);
                }
            }
        });
    }

    function get_BMtable() {
        var _token = $("input[name='_token']").val();

        var match_type = '{{$match->sports_id}}';
        var matchid = '{{$match->match_id}}';
        var matchname = '{{$match->match_name}}';
        var event_id = '{{$match->event_id}}';
        var match_id = '{{$match->id}}';
        $.ajax({
            type: "POST",
            url: '{{route("matchCallForFancyNBM",$match->match_id)}}',
            data: {
                _token: _token,
                matchtype: match_type,
                event_id: event_id,
                match_f: '{{$match->suspend_f}}',
                match_b: '{{$match->suspend_b}}',
                match_id: match_id
            },
            success: function(data) {
                if (data != '') {
                    //var spl = data.split('~~');
                    $("#inplay-tableblock-bookmaker").html(data);
                }
            }
        });
    }
    $(document).ready(function() {
        setInterval(function() {
            var _token = $("input[name='_token']").val();

            var match_type = '{{$match->sports_id}}';
            var matchid = '{{$match->match_id}}';
            var matchname = '{{$match->match_name}}';
            var event_id = '{{$match->event_id}}';
            var match_m = '{{$match->suspend_m}}';
            var match_b = '{{$match->suspend_b}}';
            var match_f = '{{$match->suspend_f}}';
            var match_id = '{{$match->id}}';
            $.ajax({
                type: "POST",
                url: '{{route("matchCall",$match->match_id)}}',
                data: {
                    _token: _token,
                    matchtype: match_type,
                    event_id: event_id,
                    matchname: matchname,
                    matchid: matchid,
                    match_m: match_m,
                    match_id: match_id,
                },
                success: function(data) {
					if(data=='inactive')
						window.location="/";
					else
					{
						var main = data.split('===');
						for (var i = 0; i < main.length; i++) {
							if (main[i] != '') {
								var sub_ = main[i].split('***');
								if (i == 0) {
									if (sub_[1]) {
										$(".tr_team1").before(sub_[1]);
										if (sub_[0]) {
											var sub_sub = sub_[0].split('~');
											$('.td_team1_back_2').html(sub_sub[0]);
											$('.td_team1_back_1').html(sub_sub[1]);
											$('.td_team1_back_0').html(sub_sub[2]);
											$('.td_team1_lay_0').html(sub_sub[3]);
											$('.td_team1_lay_1').html(sub_sub[4]);
											$('.td_team1_lay_2').html(sub_sub[5]);
										}
	
									} else {
										if (sub_[0]) {
											var sub_sub = sub_[0].split('~');
											$('.td_team1_back_2').addClass("spark");
											$('.td_team1_back_2').html(sub_sub[0]);
	
											$('.td_team1_back_1').addClass("spark");
											$('.td_team1_back_1').html(sub_sub[1]);
	
											$('.td_team1_back_0').addClass("spark");
											$('.td_team1_back_0').html(sub_sub[2]);
	
											$('.td_team1_lay_0').addClass("sparkLay");
											$('.td_team1_lay_0').html(sub_sub[3]);
	
											$('.td_team1_lay_1').addClass("sparkLay");
											$('.td_team1_lay_1').html(sub_sub[4]);
	
											$('.td_team1_lay_2').addClass("sparkLay");
											$('.td_team1_lay_2').html(sub_sub[5]);
										}
									}
								} else if (i == 1) {
									if (sub_[1]) {
										$(".tr_team2").before(sub_[1]);
										if (sub_[0]) {
											var sub_sub = sub_[0].split('~');
	
											$('.td_team2_back_2').html(sub_sub[0]);
											$('.td_team2_back_1').html(sub_sub[1]);
											$('.td_team2_back_0').html(sub_sub[2]);
											$('.td_team2_lay_0').html(sub_sub[3]);
											$('.td_team2_lay_1').html(sub_sub[4]);
											$('.td_team2_lay_2').html(sub_sub[5]);
										}
									} else {
										if (sub_[0]) {
											var sub_sub = sub_[0].split('~');
	
											$('.td_team2_back_2').addClass("spark");
											$('.td_team2_back_2').html(sub_sub[0]);
	
											$('.td_team2_back_1').addClass("spark");
											$('.td_team2_back_1').html(sub_sub[1]);
	
											$('.td_team2_back_0').addClass("spark");
											$('.td_team2_back_0').html(sub_sub[2]);
	
											$('.td_team2_lay_0').addClass("sparkLay");
											$('.td_team2_lay_0').html(sub_sub[3]);
	
											$('.td_team2_lay_1').addClass("sparkLay");
											$('.td_team2_lay_1').html(sub_sub[4]);
	
											$('.td_team2_lay_2').addClass("sparkLay");
											$('.td_team2_lay_2').html(sub_sub[5]);
										}
									}
								} else if (i == 2) {
									if (sub_[1]) {
										$(".tr_team3").before(sub_[1]);
										if (sub_[0]) {
											var sub_sub = sub_[0].split('~');
	
											$('.td_team3_back_2').html(sub_sub[0]);
											$('.td_team3_back_1').html(sub_sub[1]);
											$('.td_team3_back_0').html(sub_sub[2]);
											$('.td_team3_lay_0').html(sub_sub[3]);
											$('.td_team3_lay_1').html(sub_sub[4]);
											$('.td_team3_lay_2').html(sub_sub[5]);
										}
									} else {
										if (sub_[0]) {
											var sub_sub = sub_[0].split('~');
	
											$('.td_team3_back_2').addClass("spark");
											$('.td_team3_back_2').html(sub_sub[0]);
	
											$('.td_team3_back_1').addClass("spark");
											$('.td_team3_back_1').html(sub_sub[1]);
	
											$('.td_team3_back_0').addClass("spark");
											$('.td_team3_back_0').html(sub_sub[2]);
	
											$('.td_team3_lay_0').addClass("sparkLay");
											$('.td_team3_lay_0').html(sub_sub[3]);
	
											$('.td_team3_lay_1').addClass("sparkLay");
											$('.td_team3_lay_1').html(sub_sub[4]);
	
											$('.td_team3_lay_2').addClass("sparkLay");
											$('.td_team3_lay_2').html(sub_sub[5]);
										}
									}
								}
							}
						}
					}
                }
            });

            //for fancy and bookmaker
            var fancy_row = $('#hid_fancy').val();
            $.ajax({
                type: "POST",
                url: '{{route("matchCallForFancyNBM",$match->match_id)}}',
                data: {
                    _token: _token,
                    matchtype: match_type,
                    event_id: event_id,
                    matchname: matchname,
                    matchid: matchid,
                    match_b: match_b,
                    match_f: match_f,
                    match_id: match_id,
                    fancy_row: fancy_row
                },
                success: function(data) {
                    /*if (data != '') {
                        var spl = data.split('~~');
                        $("#inplay-tableblock-bookmaker").html(spl[0]);
						if(spl[1]!='')
						{
							$('#fancybetdiv').show();
						}
                        $("#inplay-tableblock-fancy").html(spl[1]);

                    }*/
				
                    var main_main = data.split('####');
					var main = main_main[0].split('===');
                    for (var i = 0; i < main.length; i++) {
                        if (main[i] != '') {
                            var sub_ = main[i].split('***');
                            if (i == 0) {
                                if (sub_[1]) {
                                    $(".tr_bm_team1").before(sub_[1]);
                                    if (sub_[0]) {
                                        var sub_sub = sub_[0].split('~');
                                        $('.td_team1_bm_back_2').html(sub_sub[0]);
                                        $('.td_team1_bm_back_1').html(sub_sub[1]);
                                        $('.td_team1_bm_back_0').html(sub_sub[2]);
                                        $('.td_team1_bm_lay_0').html(sub_sub[3]);
                                        $('.td_team1_bm_lay_1').html(sub_sub[4]);
                                        $('.td_team1_bm_lay_2').html(sub_sub[5]);
                                    }

                                } else {
                                    if (sub_[0]) {
                                        var sub_sub = sub_[0].split('~');
                                        $('.td_team1_bm_back_2').addClass("spark");
                                        $('.td_team1_bm_back_2').html(sub_sub[0]);

                                        $('.td_team1_bm_back_1').addClass("spark");
                                        $('.td_team1_bm_back_1').html(sub_sub[1]);

                                        $('.td_team1_bm_back_0').addClass("spark");
                                        $('.td_team1_bm_back_0').html(sub_sub[2]);

                                        $('.td_team1_bm_lay_0').addClass("sparkLay");
                                        $('.td_team1_bm_lay_0').html(sub_sub[3]);

                                        $('.td_team1_bm_lay_1').addClass("sparkLay");
                                        $('.td_team1_bm_lay_1').html(sub_sub[4]);

                                        $('.td_team1_bm_lay_2').addClass("sparkLay");
                                        $('.td_team1_bm_lay_2').html(sub_sub[5]);
                                    }
                                }
                            } else if (i == 1) {
                                if (sub_[1]) {
                                    $(".tr_bm_team2").before(sub_[1]);
                                    if (sub_[0]) {
                                        var sub_sub = sub_[0].split('~');

                                        $('.td_team2_bm_back_2').html(sub_sub[0]);
                                        $('.td_team2_bm_back_1').html(sub_sub[1]);
                                        $('.td_team2_bm_back_0').html(sub_sub[2]);
                                        $('.td_team2_bm_lay_0').html(sub_sub[3]);
                                        $('.td_team2_bm_lay_1').html(sub_sub[4]);
                                        $('.td_team2_bm_lay_2').html(sub_sub[5]);
                                    }
                                } else {
                                    if (sub_[0]) {
                                        var sub_sub = sub_[0].split('~');

                                        $('.td_team2_bm_back_2').addClass("spark");
                                        $('.td_team2_bm_back_2').html(sub_sub[0]);

                                        $('.td_team2_bm_back_1').addClass("spark");
                                        $('.td_team2_bm_back_1').html(sub_sub[1]);

                                        $('.td_team2_bm_back_0').addClass("spark");
                                        $('.td_team2_bm_back_0').html(sub_sub[2]);

                                        $('.td_team2_bm_lay_0').addClass("sparkLay");
                                        $('.td_team2_bm_lay_0').html(sub_sub[3]);

                                        $('.td_team2_bm_lay_1').addClass("sparkLay");
                                        $('.td_team2_bm_lay_1').html(sub_sub[4]);

                                        $('.td_team2_bm_lay_2').addClass("sparkLay");
                                        $('.td_team2_bm_lay_2').html(sub_sub[5]);
                                    }
                                }
                            } else if (i == 2) {
                                if (sub_[1]) {
                                    $(".tr_bm_team3").before(sub_[1]);
                                    if (sub_[0]) {
                                        var sub_sub = sub_[0].split('~');

                                        $('.td_team3_bm_back_2').html(sub_sub[0]);
                                        $('.td_team3_bm_back_1').html(sub_sub[1]);
                                        $('.td_team3_bm_back_0').html(sub_sub[2]);
                                        $('.td_team3_bm_lay_0').html(sub_sub[3]);
                                        $('.td_team3_bm_lay_1').html(sub_sub[4]);
                                        $('.td_team3_bm_lay_2').html(sub_sub[5]);
                                    }
                                } else {
                                    if (sub_[0]) {
                                        var sub_sub = sub_[0].split('~');

                                        $('.td_team3_bm_back_2').addClass("spark");
                                        $('.td_team3_bm_back_2').html(sub_sub[0]);

                                        $('.td_team3_bm_back_1').addClass("spark");
                                        $('.td_team3_bm_back_1').html(sub_sub[1]);

                                        $('.td_team3_bm_back_0').addClass("spark");
                                        $('.td_team3_bm_back_0').html(sub_sub[2]);

                                        $('.td_team3_bm_lay_0').addClass("sparkLay");
                                        $('.td_team3_bm_lay_0').html(sub_sub[3]);

                                        $('.td_team3_bm_lay_1').addClass("sparkLay");
                                        $('.td_team3_bm_lay_1').html(sub_sub[4]);

                                        $('.td_team3_bm_lay_2').addClass("sparkLay");
                                        $('.td_team3_bm_lay_2').html(sub_sub[5]);
                                    }
                                }
                            }
                        }
                    }
					var main = main_main[1].split('==');
                    for (var i = 0; i < main.length; i++) {
                        if (i < fancy_row) {
                            if (main[i] != '') {
                                var sub_ = main[i].split('~');
                                $('.td_fancy_lay_' + i).html(sub_[0]);
                                $('.td_fancy_back_' + i).html(sub_[1]);
                            }
                        } else {
                            var tr = '.tr_fancy_' + (parseInt(i) - parseInt(1)) + ':last';
                            $(tr).after(main[i]).show().fadeIn("slow");
                        }
                    }
                }

            });
            getBalance();
        }, 10000)

        //default call
        var _token = $("input[name='_token']").val();
        var match_type = '{{$match->sports_id}}';
        var matchid = '{{$match->match_id}}';
        var matchname = '{{$match->match_name}}';
        var event_id = '{{$match->event_id}}';
        var match_id = '{{$match->id}}';
        var match_m = '{{$match->suspend_m}}';
        var match_b = '{{$match->suspend_b}}';
        var match_f = '{{$match->suspend_f}}';
        //alert(event_id + '---' + match_type + '---' + matchid)
        /*$.ajax({
            type: "POST",
            url: '{{route("matchCall",$match->match_id)}}',
            data: {
                _token: _token,
                matchtype: match_type,
                event_id: event_id,
                matchname: matchname,
                matchid: matchid,
                match_m: match_m,
                match_id:match_id,
            },
            beforeSend: function() {
                $('#loaderimagealldiv').show();
            },
            complete: function() {
                $('#loaderimagealldiv').hide();
            },
            success: function(data) {
                //$("#inplay-tableblock").html(data);
            }
        });*/

        //default call for fancy and bookmaker
        /*var _token = $("input[name='_token']").val();

        var match_type = '{{$match->sports_id}}';
        var matchid = '{{$match->match_id}}';
        var matchname = '{{$match->match_name}}';
        var event_id = '{{$match->event_id}}';
        var match_id='{{$match->id}}';
        $.ajax({
            type: "POST",
            url: '{{route("matchCallForFancyNBM",$match->match_id)}}',
            data: {
                _token: _token,
                matchtype: match_type,
                event_id: event_id,
                match_b: match_b,
                match_f: match_f,
                match_b:match_b,
                match_f:match_f,
                match_id:match_id
            },
            beforeSend: function() {
                $('#loaderimagealldiv').show();
            },
            complete: function() {
                $('#loaderimagealldiv').hide();
            },
            success: function(data) {
                if (data != '') {
                    var spl = data.split('~~');
                    $("#inplay-tableblock-bookmaker").html(spl[0]);
                    $("#inplay-tableblock-fancy").html(spl[1]);
                }
            }
        });
*/
        setInterval(function() {
            $("td").removeClass("spark");
            $("td").removeClass("sparkLay");
        }, 500);
        setInterval(function() {
            $(".alert").alert('close');
        }, 150000);

        $('#odds_val').val('');
        $('#inputStake').val('');
        document.getElementById('betform').reset();
        $("#collapseExample1").load(location.href + " #collapseExample1");
    });

    function opnForm(vl) {

        var cls_name = $(vl).data("cls");
        var value = $(vl).data("val");
        var volume = $(vl).data("volume");
        $("#odds_volume").val(volume);

        var tm = $('#team_id').val();

        $('#final_odds_back_val_team1').val($('.td_team1_back_0').children('a').attr('data-val'));
        $('#final_odds_back_val_team2').val($('.td_team2_back_0').children('a').attr('data-val'));
        if ($('#team3').val() != '')
            $('#final_odds_back_val_team3').val($('.td_team3_back_0').children('a').attr('data-val'));

        $('#final_odds_lay_val_team1').val($('.td_team1_back_0').children('a').attr('data-val'));
        $('#final_odds_lay_val_team2').val($('.td_team2_back_0').children('a').attr('data-val'));
        if ($('#team3').val() != '')
            $('#final_odds_lay_val_team3').val($('.td_team3_back_0').children('a').attr('data-val'));

        $("#odds_val").val(value);
        if (value > 0) {
            if (cls_name == 'pink-bg') {
                $(".betslip_box").addClass('pink-bg');
                $(".col-stake_list").addClass('pink-bg');
                $(".keep-option").addClass('pink-bg');

                $(".betslip_box").removeClass('cyan-bg');
                $(".col-stake_list").removeClass('cyan-bg');
                $(".keep-option").removeClass('cyan-bg');
            }
            if (cls_name == 'cyan-bg') {
                $(".betslip_box").addClass('cyan-bg');
                $(".col-stake_list").addClass('cyan-bg');
                $(".keep-option").addClass('cyan-bg');

                $(".betslip_box").removeClass('pink-bg');
                $(".col-stake_list").removeClass('pink-bg');
                $(".keep-option").removeClass('pink-bg');
            }
            $(".showForm").show();
        } else
            $(".showForm").hide();
    }
    $('.remove_new_bet').click(function() {
        $(".showForm").hide();
    });
    $(".match_odd").click(function() {
        var oddval = $(this).data("odd");
        $('#inputStake').val(oddval);
        var fval = $('#inputStake').val();
        var matchVal = $("#odds_val").val();

        finalValue = '';
        if ($('#betTypeAdd').val() == 'ODDS' && $('#betTypeAdd').val() != 'SESSION') {
            matchVal = parseFloat(matchVal) - parseInt(1);
            finalValue = parseFloat(oddval) * parseFloat(matchVal);
        } else if ($('#betTypeAdd').val() == 'BOOKMAKER' && $('#betTypeAdd').val() != 'SESSION') {
            finalValue = (parseFloat(oddval) * parseFloat(matchVal)) / parseFloat(100);
        }
        if ($('#betTypeAdd').val() != 'SESSION')
            $('.profil').html(finalValue.toFixed(2));
        var team = $('#teamNameBet').val();

        var old_team1 = $('#team1').val();
        var old_team2 = $('#team2').val();
        var old_team3 = $('#team3').val();
       
        if ($('#betTypeAdd').val() == 'ODDS' && $('#betTypeAdd').val() != 'SESSION') {
            if ($('#betSide').val() == 'back') {
                if (old_team1.trim() == team.trim()) {
                    $('#team1_bet_count_new').show();
                    var old_value = $('#team1_total').text();
                    if (old_value != '') {
                        finalValue = parseFloat(old_value) + parseFloat(finalValue);
                    }
                    if (finalValue > 0) {
						$('#team1_bet_count_new').text(finalValue.toFixed(2));
                        $('#team1_bet_count_new').removeClass('tolose text-color-red');
                        $('#team1_bet_count_new').addClass('towin text-color-green');
                    } else {
						$('#team1_bet_count_new').text(parseFloat(finalValue).toFixed(2));
                        $('#team1_bet_count_new').addClass('tolose text-color-red');
                        $('#team1_bet_count_new').removeClass('towin text-color-green');
                    }
                    fval_new = '';
                    var old_value_team2 = $('#team2_total').text();
                    if (old_value_team2 != '') {
                        fval_new = parseFloat(old_value_team2) - parseFloat(fval);
                    }
                    if (fval_new > 0) {
                        $('#team2_bet_count_new').removeClass('tolose text-color-red');
                        $('#team2_bet_count_new').addClass('towin text-color-green');
                        $('#team2_bet_count_new').show();
                        $('#team2_bet_count_new').text(fval_new);
                    } else {
                        $('#team2_bet_count_new').addClass('tolose text-color-red');
                        $('#team2_bet_count_new').removeClass('towin text-color-green');
                        $('#team2_bet_count_new').show();
                        $('#team2_bet_count_new').text(fval_new);
                    }
                    var old_value_team3 = $('#draw_total').text();
                    if ($('#team3').val() != '') {
                        if (old_value_team3 != '') {
                            fval = parseFloat(old_value_team3) - parseFloat(fval);
                        }
                        if (fval > 0) {
                            $('#draw_bet_count_new').removeClass('tolose text-color-red');
                            $('#draw_bet_count_new').addClass('towin text-color-green');
                            $('#draw_bet_count_new').show();
                            $('#draw_bet_count_new').text(fval);
                        } else {
                            $('#draw_bet_count_new').addClass('tolose text-color-red');
                            $('#draw_bet_count_new').removeClass('towin text-color-green');
                            $('#draw_bet_count_new').show();
                            $('#draw_bet_count_new').text(fval);
                        }
                    }
                } else if (old_team2.trim() == team.trim()) {
                    $('#team2_bet_count_new').show();

                    var old_value = $('#team2_total').text();
                    if (old_value != '') {
                        finalValue = parseFloat(old_value) + parseFloat(finalValue);
                    }
                    if (finalValue > 0) {
                        $('#team2_bet_count_new').text(finalValue.toFixed(2));
                        $('#team2_bet_count_new').removeClass('tolose text-color-red');
                        $('#team2_bet_count_new').addClass('towin text-color-green');
                    } else {
                        $('#team2_bet_count_new').text(finalValue.toFixed(2));
                        $('#team2_bet_count_new').removeClass('tolose text-color-red');
                        $('#team2_bet_count_new').addClass('towin text-color-green');
                    }
                    fval_new = '';
                    var old_value_team1 = $('#team1_total').text();
                    if (old_value_team1 != '') {
                        fval_new = parseFloat(old_value_team1) - parseFloat(fval);
                    }
                    if (fval_new > 0) {
						$('#team1_bet_count_new').removeClass('tolose text-color-red');
                        $('#team1_bet_count_new').addClass('towin text-color-green');
                        $('#team1_bet_count_new').show();
                        $('#team1_bet_count_new').text(fval_new.toFixed(2));
                    } else {
						$('#team1_bet_count_new').addClass('tolose text-color-red');
                        $('#team1_bet_count_new').removeClass('towin text-color-green');
                        $('#team1_bet_count_new').show();
                        $('#team1_bet_count_new').text(fval_new.toFixed(2));
                    }
                    var old_value_team3 = $('#draw_total').text();
                    if ($('#team3').val() != '') {
                        if (old_value_team3 != '') {
                            fval = parseFloat(old_value_team3) - parseFloat(fval);
                        }
                        if (fval > 0) {
							$('#draw_bet_count_new').removeClass('tolose text-color-red');
                            $('#draw_bet_count_new').addClass('towin text-color-green');
                            $('#draw_bet_count_new').show();
                            $('#draw_bet_count_new').text(fval.toFixed(2));
                        } else {
                            $('#draw_bet_count_new').addClass('tolose text-color-red');
                            $('#draw_bet_count_new').removeClass('towin text-color-green');
                            $('#draw_bet_count_new').show();
                            $('#draw_bet_count_new').text(fval.toFixed(2));
                        }
                    }
                } else if (old_team3.trim() == team.trim()) {
                    if ($('#team3').val() != '') {
                        $('#draw_bet_count_new').show();
                        var old_value = $('#draw_total').text();
                        if (old_value != '') {
                            finalValue = parseFloat(old_value) + parseFloat(finalValue);
                        }
                        if (finalValue > 0) {
                            $('#draw_bet_count_new').removeClass('tolose text-color-red');
                            $('#draw_bet_count_new').addClass('towin text-color-green');
                            $('#draw_bet_count_new').show();
                            $('#draw_bet_count_new').text(finalValue.toFixed(2));
                        } else {
                            $('#draw_bet_count_new').addClass('tolose text-color-red');
                            $('#draw_bet_count_new').removeClass('towin text-color-green');
                            $('#draw_bet_count_new').show();
                            $('#draw_bet_count_new').text(finalValue.toFixed(2));
                        }

                        fval_new = '';
                        var old_value_team1 = $('#team1_total').text();
                        if (old_value_team1 != '') {
                            fval_new = parseFloat(old_value_team1) - parseFloat(fval);
                        }
                        if (fval_new > 0) {
							$('#team1_bet_count_new').show();
                            $('#team1_bet_count_new').removeClass('tolose text-color-red');
                            $('#team1_bet_count_new').addClass('towin text-color-green');
                            $('#team1_bet_count_new').text(fval_new.toFixed(2));
                        } else {
							$('#team1_bet_count_new').show();
                            $('#team1_bet_count_new').addClass('tolose text-color-red');
                            $('#team1_bet_count_new').removeClass('towin text-color-green');
                            $('#team1_bet_count_new').text(fval_new.toFixed(2));
                        }

                        fval_new = '';
                        var old_value_team2 = $('#team2_total').text();
                        if (old_value_team2 != '') {
                            fval_new = parseFloat(old_value_team2) - parseFloat(fval);
                        }
                        if (fval_new > 0) {
                            $('#team2_bet_count_new').removeClass('tolose text-color-red');
                            $('#team2_bet_count_new').addClass('towin text-color-green');
                            $('#team2_bet_count_new').show();
                            $('#team2_bet_count_new').text(fval_new.toFixed(2));
                        } else {
                            $('#team2_bet_count_new').addClass('tolose text-color-red');
                            $('#team2_bet_count_new').removeClass('towin text-color-green');
                            $('#team2_bet_count_new').show();
                            $('#team2_bet_count_new').text(fval_new.toFixed(2));
                        }
                    }
                }
            } else if ($('#betSide').val() == 'lay') {
                if (old_team1.trim() == team.trim()) {
                    $('#team1_bet_count_new').show();
                    var old_value = $('#team1_total').text();
                    if (old_value != '') {
                        finalValue = parseFloat(old_value) - parseFloat(finalValue);
                    }
                    if (finalValue > 0) {
						$('#team1_bet_count_new').show();
                        $('#team1_bet_count_new').text(finalValue.toFixed(2));
                        $('#team1_bet_count_new').removeClass('tolose text-color-red');
                        $('#team1_bet_count_new').addClass('towin text-color-green');
                    } else {
						$('#team1_bet_count_new').show();
                        $('#team1_bet_count_new').text(finalValue.toFixed(2));
                        $('#team1_bet_count_new').addClass('tolose text-color-red');
                        $('#team1_bet_count_new').removeClass('towin text-color-green');
                    }
                    fval_new = '';
                    var old_value_team2 = $('#team2_total').text();
                    if (old_value_team2 != '') {
                        fval_new = parseFloat(old_value_team2) + parseFloat(fval);
                    }
                    if (fval_new > 0) {
                        $('#team2_bet_count_new').removeClass('tolose text-color-red');
                        $('#team2_bet_count_new').addClass('towin text-color-green');
                        $('#team2_bet_count_new').show();
                        $('#team2_bet_count_new').text(fval_new.toFixed(2));
                    } else {
                        $('#team2_bet_count_new').addClass('tolose text-color-red');
                        $('#team2_bet_count_new').removeClass('towin text-color-green');
                        $('#team2_bet_count_new').show();
                        $('#team2_bet_count_new').text(fval_new.toFixed(2));
                    }
                    if ($('#team3').val() != '') {
                        var old_value_team3 = $('#draw_total').text();
                        if ($('#team3').val() != '') {
                            if (old_value_team3 != '') {
                                fval = parseFloat(old_value_team3) + parseFloat(fval);
                            }
                            if (fval > 0) {
                                $('#draw_bet_count_new').removeClass('tolose text-color-red');
                                $('#draw_bet_count_new').addClass('towin text-color-green');
                                $('#draw_bet_count_new').show();
                                $('#draw_bet_count_new').text(fval.toFixed(2));
                            } else {
                                $('#draw_bet_count_new').addClass('tolose text-color-red');
                                $('#draw_bet_count_new').removeClass('towin text-color-green');
                                $('#draw_bet_count_new').show();
                                $('#draw_bet_count_new').text(fval.toFixed(2));
                            }
                        }
                    }
                } else if (old_team2.trim() == team.trim()) {
                    var old_value = $('#team2_total').text();
                    if (old_value != '') {
                        finalValue = parseFloat(old_value) - parseFloat(finalValue);
                    }
                    if (finalValue > 0) {
                        $('#team2_bet_count_new').show();
                        $('#team2_bet_count_new').text(finalValue.toFixed(2));
                        $('#team2_bet_count_new').removeClass('tolose text-color-red');
                        $('#team2_bet_count_new').addClass('towin text-color-green');
                    } else {
                        $('#team2_bet_count_new').show();
                        $('#team2_bet_count_new').text(finalValue.toFixed(2));
                        $('#team2_bet_count_new').addClass('tolose text-color-red');
                        $('#team2_bet_count_new').removeClass('towin text-color-green');
                    }
                    fval_new = '';
                    var old_value_team1 = $('#team1_total').text();
                    if (old_value_team1 != '') {
                        fval_new = parseFloat(old_value_team1) + parseFloat(fval);
                    }
                    if (fval_new > 0) {
						$('#team1_bet_count_new').removeClass('tolose text-color-red');
                        $('#team1_bet_count_new').addClass('towin text-color-green');
                        $('#team1_bet_count_new').show();
                        $('#team1_bet_count_new').text(fval_new.toFixed(2));
                    } else {
						$('#team1_bet_count_new').addClass('tolose text-color-red');
                        $('#team1_bet_count_new').removeClass('towin text-color-green');
                        $('#team1_bet_count_new').show();
                        $('#team1_bet_count_new').text(fval_new.toFixed(2));
                    }

                    if ($('#team3').val() != '') {
                        fval_new = '';
                        var old_value_draw = $('#draw_total').text();
                        if (old_value_draw != '') {
                            fval_new = parseFloat(old_value_draw) + parseFloat(fval);
                        }
                        if (fval_new > 0) {
                            $('#draw_bet_count_new').removeClass('tolose text-color-red');
                            $('#draw_bet_count_new').addClass('towin text-color-green');
                            $('#draw_bet_count_new').show();
                            $('#draw_bet_count_new').text(fval_new.toFixed(2));
                        } else {
                            $('#draw_bet_count_new').addClass('tolose text-color-red');
                            $('#draw_bet_count_new').removeClass('towin text-color-green');
                            $('#draw_bet_count_new').show();
                            $('#draw_bet_count_new').text(fval_new.toFixed(2));
                        }
                    }
                } else if (old_team3.trim() == team.trim()) {
                    fval_new = '';
                    var old_value_team1 = $('#team1_total').text();
                    if (old_value_team1 != '') {
                        fval_new = parseFloat(old_value_team1) + parseFloat(fval);
                    }
                    if (fval_new > 0) {
						$('#team1_bet_count_new').show();
                        $('#team1_bet_count_new').text(fval_new.toFixed(2));
                        $('#team1_bet_count_new').removeClass('tolose text-color-red');
                        $('#team1_bet_count_new').addClass('towin text-color-green');
                    } else {
						$('#team1_bet_count_new').show();
                        $('#team1_bet_count_new').text(fval_new.toFixed(2));
                        $('#team1_bet_count_new').addClass('tolose text-color-red');
                        $('#team1_bet_count_new').removeClass('towin text-color-green');
                    }
                    fval_new = '';
                    var old_value_team2 = $('#team2_total').text();
                    if (old_value_team2 != '') {
                        fval_new = parseFloat(old_value_team2) + parseFloat(fval);
                    }
                    if (fval_new > 0) {
                        $('#team2_bet_count_new').removeClass('tolose text-color-red');
                        $('#team2_bet_count_new').addClass('towin text-color-green');
                        $('#team2_bet_count_new').show();
                        $('#team2_bet_count_new').text(fval_new.toFixed(2));
                    } else {
                        $('#team2_bet_count_new').addClass('tolose text-color-red');
                        $('#team2_bet_count_new').removeClass('towin text-color-green');
                        $('#team2_bet_count_new').show();
                        $('#team2_bet_count_new').text(fval_new.toFixed(2));
                    }

                    if ($('#team3').val() != '') {
                        var old_value = $('#draw_total').text();
                        if (old_value != '') {
                            finalValue = parseFloat(old_value) - parseFloat(finalValue);
                        }
                        if (finalValue > 0) {
                            $('#draw_bet_count_new').addClass('towin text-color-green');
                            $('#draw_bet_count_new').removeClass('tolose text-color-red');
                            $('#draw_bet_count_new').show();
                            $('#draw_bet_count_new').text(finalValue.toFixed(2));
                        } else {
                            $('#draw_bet_count_new').removeClass('towin text-color-green');
                            $('#draw_bet_count_new').addClass('tolose text-color-red');
                            $('#draw_bet_count_new').show();
                            $('#draw_bet_count_new').text(finalValue.toFixed(2));
                        }
                    }
                }
            }
        } else if ($('#betTypeAdd').val() == 'BOOKMAKER' && $('#betTypeAdd').val() != 'SESSION') {
            if ($('#betSide').val() == 'back') {
                if (old_team1.trim() == team.trim()) {
                    $('#team1_betBM_count_new').show();
                    var old_value = $('#team1_BM_total').text();
                    if (old_value != '') {
                        finalValue = parseFloat(old_value) + parseFloat(finalValue);
                    }
                    if (finalValue > 0) {
                        $('#team1_betBM_count_new').text(finalValue.toFixed(2));
                        $('#team1_betBM_count_new').removeClass('tolose text-color-red');
                        $('#team1_betBM_count_new').addClass('towin text-color-green');
                    } else {
                        $('#team1_betBM_count_new').text(parseFloat(finalValue).toFixed(2));
                        $('#team1_betBM_count_new').addClass('tolose text-color-red');
                        $('#team1_betBM_count_new').removeClass('towin text-color-green');
                    }
                    fval_new = '';
                    var old_value_team2 = $('#team2_BM_total').text();
                    if (old_value_team2 != '') {
                        fval_new = parseFloat(old_value_team2) - parseFloat(fval);
                    }
                    if (fval_new > 0) {
                        $('#team2_betBM_count_new').removeClass('tolose text-color-red');
                        $('#team2_betBM_count_new').addClass('towin text-color-green');
                        $('#team2_betBM_count_new').show();
                        $('#team2_beBMt_count_new').text(fval_new);
                    } else {
                        $('#team2_betBM_count_new').addClass('tolose text-color-red');
                        $('#team2_betBM_count_new').removeClass('towin text-color-green');
                        $('#team2_betBM_count_new').show();
                        $('#team2_betBM_count_new').text(fval_new);
                    }
                    var old_value_team3 = $('#draw_BM_total').text();
                    if ($('#team3').val() != '') {
                        if (old_value_team3 != '') {
                            fval = parseFloat(old_value_team3) - parseFloat(fval);
                        }
                        if (fval > 0) {
                            $('#draw_betBM_count_new').removeClass('tolose text-color-red');
                            $('#draw_betBM_count_new').addClass('towin text-color-green');
                            $('#draw_betBM_count_new').show();
                            $('#draw_betBM_count_new').text(fval);
                        } else {
                            $('#draw_betBM_count_new').addClass('tolose text-color-red');
                            $('#draw_betBM_count_new').removeClass('towin text-color-green');
                            $('#draw_betBM_count_new').show();
                            $('#draw_betBM_count_new').text(fval);
                        }
                    }
                } else if (old_team2.trim() == team.trim()) {
                    $('#team2_betBM_count_new').show();

                    var old_value = $('#team2_BM_total').text();
                    if (old_value != '') {
                        finalValue = parseFloat(old_value) + parseFloat(finalValue);
                    }
                    if (finalValue > 0) {
                        $('#team2_betBM_count_new').text(finalValue.toFixed(2));
                        $('#team2_betBM_count_new').removeClass('tolose text-color-red');
                        $('#team2_betBM_count_new').addClass('towin text-color-green');
                    } else {
                        $('#team2_betBM_count_new').text(finalValue.toFixed(2));
                        $('#team2_betBM_count_new').removeClass('tolose text-color-red');
                        $('#team2_betBM_count_new').addClass('towin text-color-green');
                    }
                    fval_new = '';
                    var old_value_team1 = $('#team1_BM_total').text();
                    if (old_value_team1 != '') {
                        fval_new = parseFloat(old_value_team1) - parseFloat(fval);
                    }
                    if (fval_new > 0) {
                        $('#team1_betBM_count_new').removeClass('tolose text-color-red');
                        $('#team1_betBM_count_new').addClass('towin text-color-green');
                        $('#team1_betBM_count_new').show();
                        $('#team1_betBM_count_new').text(fval_new.toFixed(2));
                    } else {
                        $('#team1_betBM_count_new').addClass('tolose text-color-red');
                        $('#team1_betBM_count_new').removeClass('towin text-color-green');
                        $('#team1_betBM_count_new').show();
                        $('#team1_betBM_count_new').text(fval_new.toFixed(2));
                    }
                    var old_value_team3 = $('#draw_BM_total').text();
                    if ($('#team3').val() != '') {
                        if (old_value_team3 != '') {
                            fval = parseFloat(old_value_team3) - parseFloat(fval);
                        }
                        if (fval > 0) {
                            $('#draw_betBM_count_new').removeClass('tolose text-color-red');
                            $('#draw_betBM_count_new').addClass('towin text-color-green');
                            $('#draw_betBM_count_new').show();
                            $('#draw_betBM_count_new').text(fval.toFixed(2));
                        } else {
                            $('#draw_betBM_count_new').addClass('tolose text-color-red');
                            $('#draw_betBM_count_new').removeClass('towin text-color-green');
                            $('#draw_betBM_count_new').show();
                            $('#draw_betBM_count_new').text(fval.toFixed(2));
                        }
                    }
                } else if (old_team3.trim() == team.trim()) {
                    if ($('#team3').val() != '') {
                        $('#draw_betBM_count_new').show();
                        var old_value = $('#draw_BM_total').text();
                        if (old_value != '') {
                            finalValue = parseFloat(old_value) + parseFloat(finalValue);
                        }
                        if (finalValue > 0) {
                            $('#draw_betBM_count_new').removeClass('tolose text-color-red');
                            $('#draw_betBM_count_new').addClass('towin text-color-green');
                            $('#draw_betBM_count_new').show();
                            $('#draw_betBM_count_new').text(finalValue.toFixed(2));
                        } else {
                            $('#draw_betBM_count_new').addClass('tolose text-color-red');
                            $('#draw_betBM_count_new').removeClass('towin text-color-green');
                            $('#draw_betBM_count_new').show();
                            $('#draw_betBM_count_new').text(finalValue.toFixed(2));
                        }

                        fval_new = '';
                        var old_value_team1 = $('#team1_total').text();
                        if (old_value_team1 != '') {
                            fval_new = parseFloat(old_value_team1) - parseFloat(fval);
                        }
                        if (fval_new > 0) {
                            $('#team1_betBM_count_new').show();
                            $('#team1_betBM_count_new').removeClass('tolose text-color-red');
                            $('#team1_betBM_count_new').addClass('towin text-color-green');
                            $('#team1_betBM_count_new').text(fval_new.toFixed(2));
                        } else {
                            $('#team1_betBM_count_new').show();
                            $('#team1_betBM_count_new').addClass('tolose text-color-red');
                            $('#team1_betBM_count_new').removeClass('towin text-color-green');
                            $('#team1_betBM_count_new').text(fval_new.toFixed(2));
                        }

                        fval_new = '';
                        var old_value_team2 = $('#team2_BM_total').text();
                        if (old_value_team2 != '') {
                            fval_new = parseFloat(old_value_team2) - parseFloat(fval);
                        }
                        if (fval_new > 0) {
                            $('#team2_betBM_count_new').removeClass('tolose text-color-red');
                            $('#team2_betBM_count_new').addClass('towin text-color-green');
                            $('#team2_betBM_count_new').show();
                            $('#team2_betBM_count_new').text(fval_new.toFixed(2));
                        } else {
                            $('#team2_betBM_count_new').addClass('tolose text-color-red');
                            $('#team2_betBM_count_new').removeClass('towin text-color-green');
                            $('#team2_betBM_count_new').show();
                            $('#team2_betBM_count_new').text(fval_new.toFixed(2));
                        }
                    }
                }
            } else if ($('#betSide').val() == 'lay') {
                if (old_team1.trim() == team.trim()) {
                    $('#team1_betBM_count_new').show();
                    var old_value = $('#team1_BM_total').text();
                    if (old_value != '') {
                        finalValue = parseFloat(old_value) - parseFloat(finalValue);
                    }
                    if (finalValue > 0) {
                        $('#team1_betBM_count_new').show();
                        $('#team1_betBM_count_new').text(finalValue.toFixed(2));
                        $('#team1_betBM_count_new').removeClass('tolose text-color-red');
                        $('#team1_betBM_count_new').addClass('towin text-color-green');
                    } else {
                        $('#team1_betBM_count_new').show();
                        $('#team1_betBM_count_new').text(finalValue.toFixed(2));
                        $('#team1_betBM_count_new').addClass('tolose text-color-red');
                        $('#team1_betBM_count_new').removeClass('towin text-color-green');
                    }
                    fval_new = '';
                    var old_value_team2 = $('#team2_BM_total').text();
                    if (old_value_team2 != '') {
                        fval_new = parseFloat(old_value_team2) + parseFloat(fval);
                    }
                    if (fval_new > 0) {
                        $('#team2_betBM_count_new').removeClass('tolose text-color-red');
                        $('#team2_betBM_count_new').addClass('towin text-color-green');
                        $('#team2_betBM_count_new').show();
                        $('#team2_betBM_count_new').text(fval_new.toFixed(2));
                    } else {
                        $('#team2_betBM_count_new').addClass('tolose text-color-red');
                        $('#team2_betBM_count_new').removeClass('towin text-color-green');
                        $('#team2_betBM_count_new').show();
                        $('#team2_betBM_count_new').text(fval_new.toFixed(2));
                    }
                    if ($('#team3').val() != '') {
                        var old_value_team3 = $('#draw_BM_total').text();
                        if ($('#team3').val() != '') {
                            if (old_value_team3 != '') {
                                fval = parseFloat(old_value_team3) + parseFloat(fval);
                            }
                            if (fval > 0) {
                                $('#draw_betBM_count_new').removeClass('tolose text-color-red');
                                $('#draw_betBM_count_new').addClass('towin text-color-green');
                                $('#draw_betBM_count_new').show();
                                $('#draw_betBM_count_new').text(fval.toFixed(2));
                            } else {
                                $('#draw_betBM_count_new').addClass('tolose text-color-red');
                                $('#draw_betBM_count_new').removeClass('towin text-color-green');
                                $('#draw_betBM_count_new').show();
                                $('#draw_betBM_count_new').text(fval.toFixed(2));
                            }
                        }
                    }
                } else if (old_team2.trim() == team.trim()) {
                    var old_value = $('#team2_BM_total').text();
                    if (old_value != '') {
                        finalValue = parseFloat(old_value) - parseFloat(finalValue);
                    }
                    if (finalValue > 0) {
                        $('#team2_betBM_count_new').show();
                        $('#team2_betBM_count_new').text(finalValue.toFixed(2));
                        $('#team2_betBM_count_new').removeClass('tolose text-color-red');
                        $('#team2_betBM_count_new').addClass('towin text-color-green');
                    } else {
                        $('#team2_betBM_count_new').show();
                        $('#team2_betBM_count_new').text(finalValue.toFixed(2));
                        $('#team2_betBM_count_new').addClass('tolose text-color-red');
                        $('#team2_betBM_count_new').removeClass('towin text-color-green');
                    }
                    fval_new = '';
                    var old_value_team1 = $('#team1_BM_total').text();
                    if (old_value_team1 != '') {
                        fval_new = parseFloat(old_value_team1) + parseFloat(fval);
                    }
                    if (fval_new > 0) {
                        $('#team1_betBM_count_new').removeClass('tolose text-color-red');
                        $('#team1_betBM_count_new').addClass('towin text-color-green');
                        $('#team1_betBM_count_new').show();
                        $('#team1_betBM_count_new').text(fval_new.toFixed(2));
                    } else {
                        $('#team1_betBM_count_new').addClass('tolose text-color-red');
                        $('#team1_betBM_count_new').removeClass('towin text-color-green');
                        $('#team1_betBM_count_new').show();
                        $('#team1_betBM_count_new').text(fval_new.toFixed(2));
                    }

                    if ($('#team3').val() != '') {
                        fval_new = '';
                        var old_value_draw = $('#draw_BM_total').text();
                        if (old_value_draw != '') {
                            fval_new = parseFloat(old_value_draw) + parseFloat(fval);
                        }
                        if (fval_new > 0) {
                            $('#draw_betBM_count_new').removeClass('tolose text-color-red');
                            $('#draw_betBM_count_new').addClass('towin text-color-green');
                            $('#draw_betBM_count_new').show();
                            $('#draw_betBM_count_new').text(fval_new.toFixed(2));
                        } else {
                            $('#draw_betBM_count_new').addClass('tolose text-color-red');
                            $('#draw_betBM_count_new').removeClass('towin text-color-green');
                            $('#draw_betBM_count_new').show();
                            $('#draw_betBM_count_new').text(fval_new.toFixed(2));
                        }
                    }
                } else if (old_team3.trim() == team.trim()) {
                    fval_new = '';
                    var old_value_team1 = $('#team1_BM_total').text();
                    if (old_value_team1 != '') {
                        fval_new = parseFloat(old_value_team1) + parseFloat(fval);
                    }
                    if (fval_new > 0) {
                        $('#team1_betBM_count_new').show();
                        $('#team1_betBM_count_new').text(fval_new.toFixed(2));
                        $('#team1_betBM_count_new').removeClass('tolose text-color-red');
                        $('#team1_betBM_count_new').addClass('towin text-color-green');
                    } else {
                        $('#team1_betBM_count_new').show();
                        $('#team1_betBM_count_new').text(fval_new.toFixed(2));
                        $('#team1_betBM_count_new').addClass('tolose text-color-red');
                        $('#team1_betBM_count_new').removeClass('towin text-color-green');
                    }
                    fval_new = '';
                    var old_value_team2 = $('#team2_BM_total').text();
                    if (old_value_team2 != '') {
                        fval_new = parseFloat(old_value_team2) + parseFloat(fval);
                    }
                    if (fval_new > 0) {
                        $('#team2_betBM_count_new').removeClass('tolose text-color-red');
                        $('#team2_betBM_count_new').addClass('towin text-color-green');
                        $('#team2_betBM_count_new').show();
                        $('#team2_betBM_count_new').text(fval_new.toFixed(2));
                    } else {
                        $('#team2_betBM_count_new').addClass('tolose text-color-red');
                        $('#team2_betBM_count_new').removeClass('towin text-color-green');
                        $('#team2_betBM_count_new').show();
                        $('#team2_betBM_count_new').text(fval_new.toFixed(2));
                    }

                    if ($('#team3').val() != '') {
                        var old_value = $('#draw_BM_total').text();
                        if (old_value != '') {
                            finalValue = parseFloat(old_value) - parseFloat(finalValue);
                        }
                        if (finalValue > 0) {
                            $('#draw_betBM_count_new').addClass('towin text-color-green');
                            $('#draw_betBM_count_new').removeClass('tolose text-color-red');
                            $('#draw_betBM_count_new').show();
                            $('#draw_betBM_count_new').text(finalValue.toFixed(2));
                        } else {
                            $('#draw_betBM_count_new').removeClass('towin text-color-green');
                            $('#draw_betBM_count_new').addClass('tolose text-color-red');
                            $('#draw_betBM_count_new').show();
                            $('#draw_betBM_count_new').text(finalValue.toFixed(2));
                        }
                    }
                }
            }
        }
    });

    function getCalculated(fval) {
        var oddval = $('#odds_val').val();
        if ($('#betTypeAdd').val() == 'ODDS')
            oddval = parseFloat(oddval) - parseInt(1);
        var finalValue = parseFloat(fval) * parseFloat(oddval);
        if ($('#betTypeAdd').val() == 'BOOKMAKER')
            finalValue = parseFloat(finalValue) / parseInt(100);
        if ($('#betTypeAdd').val() != 'SESSION')
            $('.profil').html(finalValue.toFixed(2));
        var team = $('#teamNameBet').val();

        var old_team1 = $('#team1').val();
        var old_team2 = $('#team2').val();
        var old_team3 = $('#team3').val();
        if ($('#betTypeAdd').val() == 'ODDS' && $('#betTypeAdd').val() != 'SESSION') {
            if ($('#betSide').val() == 'back') {
                if (old_team1.trim() == team.trim()) {
                    $('#team1_bet_count_new').show();
                    var old_value = $('#team1_total').text();
                    if (old_value != '') {
                        finalValue = parseFloat(old_value) + parseFloat(finalValue);
                    }
                    if (finalValue > 0) {
						$('#team1_bet_count_new').text(finalValue.toFixed(2));
                        $('#team1_bet_count_new').removeClass('tolose text-color-red');
                        $('#team1_bet_count_new').addClass('towin text-color-green');
                    } else {
						$('#team1_bet_count_new').text(parseFloat(finalValue).toFixed(2));
                        $('#team1_bet_count_new').addClass('tolose text-color-red');
                        $('#team1_bet_count_new').removeClass('towin text-color-green');
                    }
                    fval_new = '';
                    var old_value_team2 = $('#team2_total').text();
                    if (old_value_team2 != '') {
                        fval_new = parseFloat(old_value_team2) - parseFloat(fval);
                    }
                    if (fval_new > 0) {
                        $('#team2_bet_count_new').removeClass('tolose text-color-red');
                        $('#team2_bet_count_new').addClass('towin text-color-green');
                        $('#team2_bet_count_new').show();
                        $('#team2_bet_count_new').text(fval_new);
                    } else {
                        $('#team2_bet_count_new').addClass('tolose text-color-red');
                        $('#team2_bet_count_new').removeClass('towin text-color-green');
                        $('#team2_bet_count_new').show();
                        $('#team2_bet_count_new').text(fval_new);
                    }
                    var old_value_team3 = $('#draw_total').text();
                    if ($('#team3').val() != '') {
                        if (old_value_team3 != '') {
                            fval = parseFloat(old_value_team3) - parseFloat(fval);
                        }
                        if (fval > 0) {
                            $('#draw_bet_count_new').removeClass('tolose text-color-red');
                            $('#draw_bet_count_new').addClass('towin text-color-green');
                            $('#draw_bet_count_new').show();
                            $('#draw_bet_count_new').text(fval);
                        } else {
                            $('#draw_bet_count_new').addClass('tolose text-color-red');
                            $('#draw_bet_count_new').removeClass('towin text-color-green');
                            $('#draw_bet_count_new').show();
                            $('#draw_bet_count_new').text(fval);
                        }
                    }
                } else if (old_team2.trim() == team.trim()) {
                    $('#team2_bet_count_new').show();

                    var old_value = $('#team2_total').text();
                    if (old_value != '') {
                        finalValue = parseFloat(old_value) + parseFloat(finalValue);
                    }
                    if (finalValue > 0) {
                        $('#team2_bet_count_new').text(finalValue.toFixed(2));
                        $('#team2_bet_count_new').removeClass('tolose text-color-red');
                        $('#team2_bet_count_new').addClass('towin text-color-green');
                    } else {
                        $('#team2_bet_count_new').text(finalValue.toFixed(2));
                        $('#team2_bet_count_new').addClass('tolose text-color-red');
                        $('#team2_bet_count_new').removeClass('towin text-color-green');
                    }
                    fval_new = '';
                    var old_value_team1 = $('#team1_total').text();
                    if (old_value_team1 != '') {
                        fval_new = parseFloat(old_value_team1) - parseFloat(fval);
                    }
                    if (fval_new > 0) {
						$('#team1_bet_count_new').removeClass('tolose text-color-red');
                        $('#team1_bet_count_new').addClass('towin text-color-green');
                        $('#team1_bet_count_new').show();
                        $('#team1_bet_count_new').text(fval_new.toFixed(2));
                    } else {
						$('#team1_bet_count_new').addClass('tolose text-color-red');
                        $('#team1_bet_count_new').removeClass('towin text-color-green');
                        $('#team1_bet_count_new').show();
                        $('#team1_bet_count_new').text(fval_new.toFixed(2));
                    }
                    var old_value_team3 = $('#draw_total').text();
                    if ($('#team3').val() != '') {
                        if (old_value_team3 != '') {
                            fval = parseFloat(old_value_team3) - parseFloat(fval);
                        }
                        if (fval > 0) {
                            $('#draw_bet_count_new').removeClass('tolose text-color-red');
                            $('#draw_bet_count_new').addClass('towin text-color-green');
                            $('#draw_bet_count_new').show();
                            $('#draw_bet_count_new').text(fval.toFixed(2));
                        } else {
                            $('#draw_bet_count_new').addClass('tolose text-color-red');
                            $('#draw_bet_count_new').removeClass('towin text-color-green');
                            $('#draw_bet_count_new').show();
                            $('#draw_bet_count_new').text(fval.toFixed(2));
                        }
                    }
                } else if (old_team3.trim() == team.trim()) {
                    if ($('#team3').val() != '') {
                        $('#draw_bet_count_new').show();
                        var old_value = $('#draw_total').text();
                        if (old_value != '') {
                            finalValue = parseFloat(old_value) + parseFloat(finalValue);
                        }
                        if (finalValue > 0) {
                            $('#draw_bet_count_new').removeClass('tolose text-color-red');
                            $('#draw_bet_count_new').addClass('towin text-color-green');
                            $('#draw_bet_count_new').show();
                            $('#draw_bet_count_new').text(finalValue.toFixed(2));
                        } else {
                            $('#draw_bet_count_new').addClass('tolose text-color-red');
                            $('#draw_bet_count_new').removeClass('towin text-color-green');
                            $('#draw_bet_count_new').show();
                            $('#draw_bet_count_new').text(finalValue.toFixed(2));
                        }

                        fval_new = '';
                        var old_value_team1 = $('#team1_total').text();
                        if (old_value_team1 != '') {
                            fval_new = parseFloat(old_value_team1) - parseFloat(fval);
                        }
                        if (fval_new > 0) {
							$('#team1_bet_count_new').show();
                            $('#team1_bet_count_new').removeClass('tolose text-color-red');
                            $('#team1_bet_count_new').addClass('towin text-color-green');
                            $('#team1_bet_count_new').text(fval_new.toFixed(2));
                        } else {
							$('#team1_bet_count_new').show();
                            $('#team1_bet_count_new').addClass('tolose text-color-red');
                            $('#team1_bet_count_new').removeClass('towin text-color-green');
                            $('#team1_bet_count_new').text(fval_new.toFixed(2));
                        }

                        fval_new = '';
                        var old_value_team2 = $('#team2_total').text();
                        if (old_value_team2 != '') {
                            fval_new = parseFloat(old_value_team2) - parseFloat(fval);
                        }
                        if (fval_new > 0) {
                            $('#team2_bet_count_new').removeClass('tolose text-color-red');
                            $('#team2_bet_count_new').addClass('towin text-color-green');
                            $('#team2_bet_count_new').show();
                            $('#team2_bet_count_new').text(fval_new.toFixed(2));
                        } else {
                            $('#team2_bet_count_new').addClass('tolose text-color-red');
                            $('#team2_bet_count_new').removeClass('towin text-color-green');
                            $('#team2_bet_count_new').show();
                            $('#team2_bet_count_new').text(fval_new.toFixed(2));
                        }
                    }
                }
            } else if ($('#betSide').val() == 'lay') {
                if (old_team1.trim() == team.trim()) {
                    $('#team1_bet_count_new').show();
                    var old_value = $('#team1_total').text();
                    if (old_value != '') {
                        finalValue = parseFloat(old_value) - parseFloat(finalValue);
                    }
                    if (finalValue > 0) {
						$('#team1_bet_count_new').show();
                        $('#team1_bet_count_new').text(finalValue.toFixed(2));
                        $('#team1_bet_count_new').removeClass('tolose text-color-red');
                        $('#team1_bet_count_new').addClass('towin text-color-green');
                    } else {
						$('#team1_bet_count_new').show();
                        $('#team1_bet_count_new').text(finalValue.toFixed(2));
                        $('#team1_bet_count_new').addClass('tolose text-color-red');
                        $('#team1_bet_count_new').removeClass('towin text-color-green');
                    }
                    fval_new = '';
                    var old_value_team2 = $('#team2_total').text();
                    if (old_value_team2 != '') {
                        fval_new = parseFloat(old_value_team2) + parseFloat(fval);
                    }
                    if (fval_new > 0) {
                        $('#team2_bet_count_new').removeClass('tolose text-color-red');
                        $('#team2_bet_count_new').addClass('towin text-color-green');
                        $('#team2_bet_count_new').show();
                        $('#team2_bet_count_new').text(fval_new.toFixed(2));
                    } else {
                        $('#team2_bet_count_new').addClass('tolose text-color-red');
                        $('#team2_bet_count_new').removeClass('towin text-color-green');
                        $('#team2_bet_count_new').show();
                        $('#team2_bet_count_new').text(fval_new.toFixed(2));
                    }
                    if ($('#team3').val() != '') {
                        var old_value_team3 = $('#draw_total').text();
                        if ($('#team3').val() != '') {
                            if (old_value_team3 != '') {
                                fval = parseFloat(old_value_team3) + parseFloat(fval);
                            }
                            if (fval > 0) {
                                $('#draw_bet_count_new').removeClass('tolose text-color-red');
                                $('#draw_bet_count_new').addClass('towin text-color-green');
                                $('#draw_bet_count_new').show();
                                $('#draw_bet_count_new').text(fval.toFixed(2));
                            } else {
                                $('#draw_bet_count_new').addClass('tolose text-color-red');
                                $('#draw_bet_count_new').removeClass('towin text-color-green');
                                $('#draw_bet_count_new').show();
                                $('#draw_bet_count_new').text(fval.toFixed(2));
                            }
                        }
                    }
                } else if (old_team2.trim() == team.trim()) {
                    var old_value = $('#team2_total').text();
                    if (old_value != '') {
                        finalValue = parseFloat(old_value) - parseFloat(finalValue);
                    }
                    if (finalValue > 0) {
                        $('#team2_bet_count_new').show();
                        $('#team2_bet_count_new').text(finalValue.toFixed(2));
                        $('#team2_bet_count_new').removeClass('tolose text-color-red');
                        $('#team2_bet_count_new').addClass('towin text-color-green');
                    } else {
                        $('#team2_bet_count_new').show();
                        $('#team2_bet_count_new').text(finalValue.toFixed(2));
                        $('#team2_bet_count_new').addClass('tolose text-color-red');
                        $('#team2_bet_count_new').removeClass('towin text-color-green');
                    }
                    fval_new = '';
                    var old_value_team1 = $('#team1_total').text();
                    if (old_value_team1 != '') {
                        fval_new = parseFloat(old_value_team1) + parseFloat(fval);
                    }
                    if (fval_new > 0) {
						$('#team1_bet_count_new').removeClass('tolose text-color-red');
                        $('#team1_bet_count_new').addClass('towin text-color-green');
                        $('#team1_bet_count_new').show();
                        $('#team1_bet_count_new').text(fval_new.toFixed(2));
                    } else {
						$('#team1_bet_count_new').addClass('tolose text-color-red');
                        $('#team1_bet_count_new').removeClass('towin text-color-green');
                        $('#team1_bet_count_new').show();
                        $('#team1_bet_count_new').text(fval_new.toFixed(2));
                    }

                    if ($('#team3').val() != '') {
                        fval_new = '';
                        var old_value_draw = $('#draw_total').text();
                        if (old_value_draw != '') {
                            fval_new = parseFloat(old_value_draw) + parseFloat(fval);
                        }
                        if (fval_new > 0) {
                            $('#draw_bet_count_new').removeClass('tolose text-color-red');
                            $('#draw_bet_count_new').addClass('towin text-color-green');
                            $('#draw_bet_count_new').show();
                            $('#draw_bet_count_new').text(fval_new.toFixed(2));
                        } else {
                            $('#draw_bet_count_new').addClass('tolose text-color-red');
                            $('#draw_bet_count_new').removeClass('towin text-color-green');
                            $('#draw_bet_count_new').show();
                            $('#draw_bet_count_new').text(fval_new.toFixed(2));
                        }
                    }
                } else if (old_team3.trim() == team.trim()) {
                    fval_new = '';
                    var old_value_team1 = $('#team1_total').text();
                    if (old_value_team1 != '') {
                        fval_new = parseFloat(old_value_team1) + parseFloat(fval);
                    }
                    if (fval_new > 0) {
						$('#team1_bet_count_new').show();
                        $('#team1_bet_count_new').text(fval_new.toFixed(2));
                        $('#team1_bet_count_new').removeClass('tolose text-color-red');
                        $('#team1_bet_count_new').addClass('towin text-color-green');
                    } else {
						$('#team1_bet_count_new').show();
                        $('#team1_bet_count_new').text(fval_new.toFixed(2));
                        $('#team1_bet_count_new').addClass('tolose text-color-red');
                        $('#team1_bet_count_new').removeClass('towin text-color-green');
                    }
                    fval_new = '';
                    var old_value_team2 = $('#team2_total').text();
                    if (old_value_team2 != '') {
                        fval_new = parseFloat(old_value_team2) + parseFloat(fval);
                    }
                    if (fval_new > 0) {
                        $('#team2_bet_count_new').removeClass('tolose text-color-red');
                        $('#team2_bet_count_new').addClass('towin text-color-green');
                        $('#team2_bet_count_new').show();
                        $('#team2_bet_count_new').text(fval_new.toFixed(2));
                    } else {
                        $('#team2_bet_count_new').addClass('tolose text-color-red');
                        $('#team2_bet_count_new').removeClass('towin text-color-green');
                        $('#team2_bet_count_new').show();
                        $('#team2_bet_count_new').text(fval_new.toFixed(2));
                    }

                    if ($('#team3').val() != '') {
                        var old_value = $('#draw_total').text();
                        if (old_value != '') {
                            finalValue = parseFloat(old_value) - parseFloat(finalValue);
                        }
                        if (finalValue > 0) {
                            $('#draw_bet_count_new').addClass('towin text-color-green');
                            $('#draw_bet_count_new').removeClass('tolose text-color-red');
                            $('#draw_bet_count_new').show();
                            $('#draw_bet_count_new').text(finalValue.toFixed(2));
                        } else {
                            $('#draw_bet_count_new').removeClass('towin text-color-green');
                            $('#draw_bet_count_new').addClass('tolose text-color-red');
                            $('#draw_bet_count_new').show();
                            $('#draw_bet_count_new').text(finalValue.toFixed(2));
                        }
                    }
                }
            }
        } else if ($('#betTypeAdd').val() == 'BOOKMAKER' && $('#betTypeAdd').val() != 'SESSION') {
            if ($('#betSide').val() == 'back') {
                if (old_team1.trim() == team.trim()) {
                    $('#team1_betBM_count_new').show();
                    var old_value = $('#team1_BM_total').text();
                    if (old_value != '') {
                        finalValue = parseFloat(old_value) + parseFloat(finalValue);
                    }
                    if (finalValue > 0) {
                        $('#team1_betBM_count_new').text(finalValue.toFixed(2));
                        $('#team1_betBM_count_new').removeClass('tolose text-color-red');
                        $('#team1_betBM_count_new').addClass('towin text-color-green');
                    } else {
                        $('#team1_betBM_count_new').text(parseFloat(finalValue).toFixed(2));
                        $('#team1_betBM_count_new').addClass('tolose text-color-red');
                        $('#team1_betBM_count_new').removeClass('towin text-color-green');
                    }
                    fval_new = '';
                    var old_value_team2 = $('#team2_BM_total').text();
                    if (old_value_team2 != '') {
                        fval_new = parseFloat(old_value_team2) - parseFloat(fval);
                    }
                    if (fval_new > 0) {
                        $('#team2_betBM_count_new').removeClass('tolose text-color-red');
                        $('#team2_betBM_count_new').addClass('towin text-color-green');
                        $('#team2_betBM_count_new').show();
                        $('#team2_beBMt_count_new').text(fval_new);
                    } else {
                        $('#team2_betBM_count_new').addClass('tolose text-color-red');
                        $('#team2_betBM_count_new').removeClass('towin text-color-green');
                        $('#team2_betBM_count_new').show();
                        $('#team2_betBM_count_new').text(fval_new);
                    }
                    var old_value_team3 = $('#draw_BM_total').text();
                    if ($('#team3').val() != '') {
                        if (old_value_team3 != '') {
                            fval = parseFloat(old_value_team3) - parseFloat(fval);
                        }
                        if (fval > 0) {
                            $('#draw_betBM_count_new').removeClass('tolose text-color-red');
                            $('#draw_betBM_count_new').addClass('towin text-color-green');
                            $('#draw_betBM_count_new').show();
                            $('#draw_betBM_count_new').text(fval);
                        } else {
                            $('#draw_betBM_count_new').addClass('tolose text-color-red');
                            $('#draw_betBM_count_new').removeClass('towin text-color-green');
                            $('#draw_betBM_count_new').show();
                            $('#draw_betBM_count_new').text(fval);
                        }
                    }
                } else if (old_team2.trim() == team.trim()) {
                    $('#team2_betBM_count_new').show();

                    var old_value = $('#team2_BM_total').text();
                    if (old_value != '') {
                        finalValue = parseFloat(old_value) + parseFloat(finalValue);
                    }
                    if (finalValue > 0) {
                        $('#team2_betBM_count_new').text(finalValue.toFixed(2));
                        $('#team2_betBM_count_new').removeClass('tolose text-color-red');
                        $('#team2_betBM_count_new').addClass('towin text-color-green');
                    } else {
                        $('#team2_betBM_count_new').text(finalValue.toFixed(2));
                        $('#team2_betBM_count_new').removeClass('tolose text-color-red');
                        $('#team2_betBM_count_new').addClass('towin text-color-green');
                    }
                    fval_new = '';
                    var old_value_team1 = $('#team1_BM_total').text();
                    if (old_value_team1 != '') {
                        fval_new = parseFloat(old_value_team1) - parseFloat(fval);
                    }
                    if (fval_new > 0) {
                        $('#team1_betBM_count_new').removeClass('tolose text-color-red');
                        $('#team1_betBM_count_new').addClass('towin text-color-green');
                        $('#team1_betBM_count_new').show();
                        $('#team1_betBM_count_new').text(fval_new.toFixed(2));
                    } else {
                        $('#team1_betBM_count_new').addClass('tolose text-color-red');
                        $('#team1_betBM_count_new').removeClass('towin text-color-green');
                        $('#team1_betBM_count_new').show();
                        $('#team1_betBM_count_new').text(fval_new.toFixed(2));
                    }
                    var old_value_team3 = $('#draw_BM_total').text();
                    if ($('#team3').val() != '') {
                        if (old_value_team3 != '') {
                            fval = parseFloat(old_value_team3) - parseFloat(fval);
                        }
                        if (fval > 0) {
                            $('#draw_betBM_count_new').removeClass('tolose text-color-red');
                            $('#draw_betBM_count_new').addClass('towin text-color-green');
                            $('#draw_betBM_count_new').show();
                            $('#draw_betBM_count_new').text(fval.toFixed(2));
                        } else {
                            $('#draw_betBM_count_new').addClass('tolose text-color-red');
                            $('#draw_betBM_count_new').removeClass('towin text-color-green');
                            $('#draw_betBM_count_new').show();
                            $('#draw_betBM_count_new').text(fval.toFixed(2));
                        }
                    }
                } else if (old_team3.trim() == team.trim()) {
                    if ($('#team3').val() != '') {
                        $('#draw_betBM_count_new').show();
                        var old_value = $('#draw_BM_total').text();
                        if (old_value != '') {
                            finalValue = parseFloat(old_value) + parseFloat(finalValue);
                        }
                        if (finalValue > 0) {
                            $('#draw_betBM_count_new').removeClass('tolose text-color-red');
                            $('#draw_betBM_count_new').addClass('towin text-color-green');
                            $('#draw_betBM_count_new').show();
                            $('#draw_betBM_count_new').text(finalValue.toFixed(2));
                        } else {
                            $('#draw_betBM_count_new').addClass('tolose text-color-red');
                            $('#draw_betBM_count_new').removeClass('towin text-color-green');
                            $('#draw_betBM_count_new').show();
                            $('#draw_betBM_count_new').text(finalValue.toFixed(2));
                        }

                        fval_new = '';
                        var old_value_team1 = $('#team1_total').text();
                        if (old_value_team1 != '') {
                            fval_new = parseFloat(old_value_team1) - parseFloat(fval);
                        }
                        if (fval_new > 0) {
                            $('#team1_betBM_count_new').show();
                            $('#team1_betBM_count_new').removeClass('tolose text-color-red');
                            $('#team1_betBM_count_new').addClass('towin text-color-green');
                            $('#team1_betBM_count_new').text(fval_new.toFixed(2));
                        } else {
                            $('#team1_betBM_count_new').show();
                            $('#team1_betBM_count_new').addClass('tolose text-color-red');
                            $('#team1_betBM_count_new').removeClass('towin text-color-green');
                            $('#team1_betBM_count_new').text(fval_new.toFixed(2));
                        }

                        fval_new = '';
                        var old_value_team2 = $('#team2_BM_total').text();
                        if (old_value_team2 != '') {
                            fval_new = parseFloat(old_value_team2) - parseFloat(fval);
                        }
                        if (fval_new > 0) {
                            $('#team2_betBM_count_new').removeClass('tolose text-color-red');
                            $('#team2_betBM_count_new').addClass('towin text-color-green');
                            $('#team2_betBM_count_new').show();
                            $('#team2_betBM_count_new').text(fval_new.toFixed(2));
                        } else {
                            $('#team2_betBM_count_new').addClass('tolose text-color-red');
                            $('#team2_betBM_count_new').removeClass('towin text-color-green');
                            $('#team2_betBM_count_new').show();
                            $('#team2_betBM_count_new').text(fval_new.toFixed(2));
                        }
                    }
                }
            } else if ($('#betSide').val() == 'lay') {
                if (old_team1.trim() == team.trim()) {
                    $('#team1_betBM_count_new').show();
                    var old_value = $('#team1_BM_total').text();
                    if (old_value != '') {
                        finalValue = parseFloat(old_value) - parseFloat(finalValue);
                    }
                    if (finalValue > 0) {
                        $('#team1_betBM_count_new').show();
                        $('#team1_betBM_count_new').text(finalValue.toFixed(2));
                        $('#team1_betBM_count_new').removeClass('tolose text-color-red');
                        $('#team1_betBM_count_new').addClass('towin text-color-green');
                    } else {
                        $('#team1_betBM_count_new').show();
                        $('#team1_betBM_count_new').text(finalValue.toFixed(2));
                        $('#team1_betBM_count_new').addClass('tolose text-color-red');
                        $('#team1_betBM_count_new').removeClass('towin text-color-green');
                    }
                    fval_new = '';
                    var old_value_team2 = $('#team2_BM_total').text();
                    if (old_value_team2 != '') {
                        fval_new = parseFloat(old_value_team2) + parseFloat(fval);
                    }
                    if (fval_new > 0) {
                        $('#team2_betBM_count_new').removeClass('tolose text-color-red');
                        $('#team2_betBM_count_new').addClass('towin text-color-green');
                        $('#team2_betBM_count_new').show();
                        $('#team2_betBM_count_new').text(fval_new.toFixed(2));
                    } else {
                        $('#team2_betBM_count_new').addClass('tolose text-color-red');
                        $('#team2_betBM_count_new').removeClass('towin text-color-green');
                        $('#team2_betBM_count_new').show();
                        $('#team2_betBM_count_new').text(fval_new.toFixed(2));
                    }
                    if ($('#team3').val() != '') {
                        var old_value_team3 = $('#draw_BM_total').text();
                        if ($('#team3').val() != '') {
                            if (old_value_team3 != '') {
                                fval = parseFloat(old_value_team3) + parseFloat(fval);
                            }
                            if (fval > 0) {
                                $('#draw_betBM_count_new').removeClass('tolose text-color-red');
                                $('#draw_betBM_count_new').addClass('towin text-color-green');
                                $('#draw_betBM_count_new').show();
                                $('#draw_betBM_count_new').text(fval.toFixed(2));
                            } else {
                                $('#draw_betBM_count_new').addClass('tolose text-color-red');
                                $('#draw_betBM_count_new').removeClass('towin text-color-green');
                                $('#draw_betBM_count_new').show();
                                $('#draw_betBM_count_new').text(fval.toFixed(2));
                            }
                        }
                    }
                } else if (old_team2.trim() == team.trim()) {
                    var old_value = $('#team2_BM_total').text();
                    if (old_value != '') {
                        finalValue = parseFloat(old_value) - parseFloat(finalValue);
                    }
                    if (finalValue > 0) {
                        $('#team2_betBM_count_new').show();
                        $('#team2_betBM_count_new').text(finalValue.toFixed(2));
                        $('#team2_betBM_count_new').removeClass('tolose text-color-red');
                        $('#team2_betBM_count_new').addClass('towin text-color-green');
                    } else {
                        $('#team2_betBM_count_new').show();
                        $('#team2_betBM_count_new').text(finalValue.toFixed(2));
                        $('#team2_betBM_count_new').addClass('tolose text-color-red');
                        $('#team2_betBM_count_new').removeClass('towin text-color-green');
                    }
                    fval_new = '';
                    var old_value_team1 = $('#team1_BM_total').text();
                    if (old_value_team1 != '') {
                        fval_new = parseFloat(old_value_team1) + parseFloat(fval);
                    }
                    if (fval_new > 0) {
                        $('#team1_betBM_count_new').removeClass('tolose text-color-red');
                        $('#team1_betBM_count_new').addClass('towin text-color-green');
                        $('#team1_betBM_count_new').show();
                        $('#team1_betBM_count_new').text(fval_new.toFixed(2));
                    } else {
                        $('#team1_betBM_count_new').addClass('tolose text-color-red');
                        $('#team1_betBM_count_new').removeClass('towin text-color-green');
                        $('#team1_betBM_count_new').show();
                        $('#team1_betBM_count_new').text(fval_new.toFixed(2));
                    }

                    if ($('#team3').val() != '') {
                        fval_new = '';
                        var old_value_draw = $('#draw_BM_total').text();
                        if (old_value_draw != '') {
                            fval_new = parseFloat(old_value_draw) + parseFloat(fval);
                        }
                        if (fval_new > 0) {
                            $('#draw_betBM_count_new').removeClass('tolose text-color-red');
                            $('#draw_betBM_count_new').addClass('towin text-color-green');
                            $('#draw_betBM_count_new').show();
                            $('#draw_betBM_count_new').text(fval_new.toFixed(2));
                        } else {
                            $('#draw_betBM_count_new').addClass('tolose text-color-red');
                            $('#draw_betBM_count_new').removeClass('towin text-color-green');
                            $('#draw_betBM_count_new').show();
                            $('#draw_betBM_count_new').text(fval_new.toFixed(2));
                        }
                    }
                } else if (old_team3.trim() == team.trim()) {
                    fval_new = '';
                    var old_value_team1 = $('#team1_BM_total').text();
                    if (old_value_team1 != '') {
                        fval_new = parseFloat(old_value_team1) + parseFloat(fval);
                    }
                    if (fval_new > 0) {
                        $('#team1_betBM_count_new').show();
                        $('#team1_betBM_count_new').text(fval_new.toFixed(2));
                        $('#team1_betBM_count_new').removeClass('tolose text-color-red');
                        $('#team1_betBM_count_new').addClass('towin text-color-green');
                    } else {
                        $('#team1_betBM_count_new').show();
                        $('#team1_betBM_count_new').text(fval_new.toFixed(2));
                        $('#team1_betBM_count_new').addClass('tolose text-color-red');
                        $('#team1_betBM_count_new').removeClass('towin text-color-green');
                    }
                    fval_new = '';
                    var old_value_team2 = $('#team2_BM_total').text();
                    if (old_value_team2 != '') {
                        fval_new = parseFloat(old_value_team2) + parseFloat(fval);
                    }
                    if (fval_new > 0) {
                        $('#team2_betBM_count_new').removeClass('tolose text-color-red');
                        $('#team2_betBM_count_new').addClass('towin text-color-green');
                        $('#team2_betBM_count_new').show();
                        $('#team2_betBM_count_new').text(fval_new.toFixed(2));
                    } else {
                        $('#team2_betBM_count_new').addClass('tolose text-color-red');
                        $('#team2_betBM_count_new').removeClass('towin text-color-green');
                        $('#team2_betBM_count_new').show();
                        $('#team2_betBM_count_new').text(fval_new.toFixed(2));
                    }

                    if ($('#team3').val() != '') {
                        var old_value = $('#draw_BM_total').text();
                        if (old_value != '') {
                            finalValue = parseFloat(old_value) - parseFloat(finalValue);
                        }
                        if (finalValue > 0) {
                            $('#draw_betBM_count_new').addClass('towin text-color-green');
                            $('#draw_betBM_count_new').removeClass('tolose text-color-red');
                            $('#draw_betBM_count_new').show();
                            $('#draw_betBM_count_new').text(finalValue.toFixed(2));
                        } else {
                            $('#draw_betBM_count_new').removeClass('towin text-color-green');
                            $('#draw_betBM_count_new').addClass('tolose text-color-red');
                            $('#draw_betBM_count_new').show();
                            $('#draw_betBM_count_new').text(finalValue.toFixed(2));
                        }
                    }
                }
            }
        }
    }


    //$(".ODDSBack").live( "click", function() {
    $(document).on('click', '.ODDSBack', function() {
        var teamname1 = $('.team1').text();
        var teamname2 = $('.team2').text();
        var teamname3 = $('.team3').text();

        $('#team1').val(teamname1.trim());
        $('#team2').val(teamname2.trim());
        $('#team3').val(teamname3.trim());
        var team = $('#' + $(this).attr("data-team")).val();
        $('#team_id').val($(this).attr("data-team"));

        $('#teamNameBet').val(team);
        $('#betTypeAdd').val('ODDS');
        $('#betSide').val('back');

        $('#profit_liability').text('Profit');
        $('#back_or_lay').text('Back (Bet For)');
        $('#bet_for').text(team);
    });
    //$(".ODDSLay").live( "click", function() {
    $(document).on('click', '.ODDSLay', function() {

        var teamname1 = $('.team1').text();
        var teamname2 = $('.team2').text();
        var teamname3 = $('.team3').text();

        $('#team1').val(teamname1.trim());
        $('#team2').val(teamname2.trim());
        $('#team3').val(teamname3.trim());

        var team = $('#' + $(this).attr("data-team")).val();
        $('#team_id').val($(this).attr("data-team"));

        $('#teamNameBet').val(team);

        $('#betTypeAdd').val('ODDS');
        $('#betSide').val('lay');

        $('#profit_liability').text('Liability');
        $('#back_or_lay').text('Lay (Bet Against)');
        $('#bet_for').text(team);
    });
    //for BM
    $(document).on('click', '.BmBack', function() {
        var teamname1 = $('.team1').text();
        var teamname2 = $('.team2').text();
        var teamname3 = $('.team3').text();

        $('#team1').val(teamname1.trim());
        $('#team2').val(teamname2.trim());
        $('#team3').val(teamname3.trim());
        var team = $('#' + $(this).attr("data-team")).val();
        $('#teamNameBet').val(team);
        $('#betTypeAdd').val('BOOKMAKER');
        $('#betSide').val('back');

        $('#profit_liability').text('Profit');
        $('#back_or_lay').text('Back (Bet For)');
        $('#bet_for').text(team);
    });
    $(document).on('click', '.BmLay', function() {

        var teamname1 = $('.team1').text();
        var teamname2 = $('.team2').text();
        var teamname3 = $('.team3').text();

        $('#team1').val(teamname1.trim());
        $('#team2').val(teamname2.trim());
        $('#team3').val(teamname3.trim());

        var team = $('#' + $(this).attr("data-team")).val();
        $('#teamNameBet').val(team);

        $('#betTypeAdd').val('BOOKMAKER');
        $('#betSide').val('lay');

        $('#profit_liability').text('Liability');
        $('#back_or_lay').text('Lay (Bet Against)');
        $('#bet_for').text(team);
    });
    //for fancy
    $(document).on('click', '.FancyBack', function() {
        var teamname1 = $('.team1').text();
        var teamname2 = $('.team2').text();
        var teamname3 = $('.team3').text();

        $('#team1').val(teamname1.trim());
        $('#team2').val(teamname2.trim());
        $('#team3').val(teamname3.trim());
        var team = $(this).attr("data-team");
        $('#teamNameBet').val(team);
        $('#betTypeAdd').val('SESSION');
        $('#betSide').val('back');

        $('#profit_liability').text('Profit');
        $('#back_or_lay').text('Back (Bet For)');
        $('#bet_for').text(team);
    });
    $(document).on('click', '.FancyLay', function() {

        var teamname1 = $('.team1').text();
        var teamname2 = $('.team2').text();
        var teamname3 = $('.team3').text();

        $('#team1').val(teamname1.trim());
        $('#team2').val(teamname2.trim());
        $('#team3').val(teamname3.trim());

        var team = $(this).attr("data-team");
        
        $('#teamNameBet').val(team);

        $('#betTypeAdd').val('SESSION');
        $('#betSide').val('lay');

        $('#profit_liability').text('Liability');
        $('#back_or_lay').text('Lay (Bet Against)');
        $('#bet_for').text(team);
    });


    function getBalance() {
        //console.log('get balance');
        var _token = $("input[name='_token']").val();
        $.ajax({
            type: "GET",
            url: '{{route("getPlayerBalance")}}',
            data: {
                _token: _token
            },
            success: function(data) {
                if (data != '') {
                    var spl = data.split('~~');
                    $("#main_balance_div").html('INR ' + spl[0]);
                    $("#exposer_div").html(spl[1]);
                    $('#tot_bal').val(spl[0]);
                    $('#tot_expo').val(spl[1]);
                }
            }
        });
    }

    function call_display_bet_list(dval) {
       
        var _token = $("input[name='_token']").val();
        $.ajax({
            type: "POST",
            url: '{{route("GetOtherMatchBet")}}',
            data: {
                match_id: dval,
                _token: _token
            },
            timeout: 10000,
            success: function(data) {
				 
                $('#divbetlist').html(data);
            }
        });
    }

    function saveBetcall() {

        if ($('#odds_val').val() == '') {
            alert('Odds amount required');
            return false;
        }
        if ($('#inputStake').val() == '') {
            alert('Stack value required');
            return false;
        }
        if (!($('#comfirmBets').prop('checked'))) {
            alert('Please confirm that you are agree to place this bet.');
            return false;
        }

        $('.btn-success').prop("disabled", false);
        //var opttext = $('#betoption').val();
        getBalance();
        var player_balance = $('#tot_bal').val();

        var bet_type = $('#betTypeAdd').val();
        var bet_site = $('#betSide').val();
        var bet_odds = $('.amountint').text();
        var bet_amount = $('#odds_val').val();
        var stack = $('#inputStake').val();
        var team_name = $('#teamNameBet').val();
        var bet_profit = $('#bet-profit').val();
        var bet_cal_amt = $('.amountint').text();
        var odds_limit = $('#odds_limit').val();

        var suspendedMsg = '<div class="alert alert-danger">Game Suspended Bet Not Allowed</div>';
        if (bet_amount == '' || bet_amount <= 0 || isNaN(bet_amount)) {
            $('#betMsgALL').html('<div  id="msg-alert" class="alert alert-danger"><button type="button" class="close" style="margin-top: -7px;" data-dismiss="alert">x</button>Min Max Bet Limit Exceed</div>');
            //betMsgEmpty();
            //hideLoading('showBet');
            $(".amountint").text("");
            $('#inputStake').val("");
            $('#odds_val').val("");
            $(".showForm").hide();
            return false;

        }
        if (bet_type == 'ODDS' || bet_type == 'BOOKMAKER') {
            if (bet_odds == '' || bet_odds <= 0 || isNaN(bet_odds)) {
                $('#betMsgALL').html('<div  id="msg-alert" class="alert alert-danger"><button type="button" class="close" style="margin-top: -7px;" data-dismiss="alert">x</button>Bet Odds changed</div>');
                $(".amountint").text("");
                $('#inputStake').val("");
                $('#odds_val').val("");
                return false;
            }
        }
        if (bet_type == 'ODDS') {

            var stack = $('#inputStake').val();

            if (parseInt(stack) < parseInt($('#div_min_bet_odds_limit').text())) {
                $('#betMsgALL').html('<div  id="msg-alert" class="alert alert-danger"><button type="button" class="close" style="margin-top: -7px;" data-dismiss="alert">x</button>Minimum bets is ' + $('#div_min_bet_odds_limit').text() + '</div>');
                $(".showForm").hide();
                $(".amountint").text("");
                $('#inputStake').val("");
                $('#odds_val').val("");
                return false;
            }
            if (parseInt(stack) > parseInt($('#div_max_bet_odds_limit').text())) {
                $('#betMsgALL').html('<div  id="msg-alert" class="alert alert-danger"><button type="button" class="close" style="margin-top: -7px;" data-dismiss="alert">x</button>Maximum bets is--- ' + $('#div_max_bet_odds_limit').text() + '</div>');
                $(".showForm").hide();
                $(".amountint").text("");
                $('#inputStake').val("");
                $('#odds_val').val("");
                return false;
            }
            $('.amountint').text(bet_odds);
            //betODDCalculation(bet_amount);
            //	var opttext = $('#betoption').val();
            var bet_type = $('#betTypeAdd').val();
            var bet_site = $('#betSide').val();
            var bet_odds = $('.amountint').text();

            var bet_amount = $('#odds_val').val();
            var team_name = $('#teamNameBet').val();

            var bet_profit = $('#bet-profit').val();
            //var  bet_profit = $('.amountint').text();
            var parameter = "";
            var teamname1 = $('#team1').val();
            var teamname2 = $('#team2').val();
            var teamname3 = $('#team3').val();

            var team1_total = $('#team1_total').text();
            var team2_total = $('#team2_total').text();
            var team3_total = $('#draw_total').text();

            var team1_BM_total = $('#team1_BM_total').text();
            var team2_BM_total = $('#team2_BM_total').text();
            var draw_BM_total = $('#draw_BM_total').text();
			
			var hid_fancy=$('#hid_fancy').val();
			var fancy_total=0;
			for(var f=0;f<hid_fancy;f++)
			{
				if($('#Fancy_Total_'+f))
				{
					fancy_total=parseFloat(fancy_total)+parseFloat($('#Fancy_Total_'+f).text());
				}
			}

            var back_team_0 = $('#final_odds_back_val_team1').val();
            var back_team_1 = $('#final_odds_back_val_team2').val();
            var back_team_2 = $('#final_odds_back_val_team3').val();

            var lay_team_0 = $('#final_odds_lay_val_team1').val();
            var lay_team_1 = $('#final_odds_lay_val_team2').val();
            var lay_team_2 = $('#final_odds_lay_val_team3').val();
            //ancy_Total_Div
		

            if (teamname1 == team_name) {
                parameter = "&teamname2=" + encodeURIComponent(teamname2) + "&teamname3=" + encodeURIComponent(teamname3);
            } else if (teamname2 == team_name) {
                parameter = "&teamname1=" + encodeURIComponent(teamname1) + "&teamname3=" + encodeURIComponent(teamname3);
            } else {
                parameter = "&teamname1=" + encodeURIComponent(teamname1) + "&teamname2=" + encodeURIComponent(teamname2);
            }
            //alert('bet_profit--'+bet_profit+'&& bet_odds---'+bet_amount+'&& stack---'+stack+'&& bet_cal_amt---'+bet_cal_amt);
            /*  */
            document.getElementById("site_bet_loading").style.display = "block";
            document.getElementById("betslip-block").style.display = "none";
            var delay = '<?php echo $delayTime; ?>';
			
			//var delay = delayTime;
            setTimeout(function() {
                //showLoader = setTimeout("$('#site_bet_loading').show()", 2000);
                $.ajax({
                    url: '{{route("MyBetStore")}}',
                    dataType: 'json',
                    type: "POST",
                    data: "sportID={{$match->sports_id}}&match_id={{$match->event_id}}&_token={{csrf_token()}}&bet_profit=" + bet_profit + "&bet_type=" + bet_type + "&bet_side=" + bet_site + "&bet_odds=" + bet_amount + "&bet_amount=" + stack + "&team_name=" + team_name + parameter + '&stack=' + stack + '&bet_cal_amt=' + bet_cal_amt + '&team1_total=' + team1_total + '&team2_total=' + team2_total + '&team3_total=' + team3_total + '&team1=' + teamname1 + '&team2=' + teamname2 + '&team3=' + teamname3 + '&team1_BM_total=' + team1_BM_total + '&team2_BM_total=' + team2_BM_total + '&team3_BM_total=' + draw_BM_total + '&back_team_0=' + back_team_0 + '&back_team_1=' + back_team_1 + '&back_team_2=' + back_team_2 + '&lay_team_0=' + lay_team_0 + '&lay_team_1=' + lay_team_1 + '&lay_team_2=' + lay_team_2+'&fancy_total='+fancy_total,
                    beforeSend: function() {
                        //setTimeoutajax();
                    },
                    complete: function() {
                        //$('#site_bet_loading').hide();
                    },
                    success: function(data) {

                        $('#betMsgALL').html(data.message);
                        if (data.status == true) {
                            $('#odds_val').val('');
                            $('#inputStake').val('');
                            //alert('bet placed succesfully');
                            $('.amountint').text('');
                            $(".showForm").hide();
                            $('#bet_display_table').show();
                            //$('#bet-profit').text('');
                            //$('#betAmount').val('');
                            get_oddstable();
                            var match_sel = $('#select_bet_on_match').val();
                            if (match_sel == "" || match_sel == null)
                                match_sel = '{{$match->event_id}}~~' + 'All';
							
                            call_display_bet_list(match_sel);
                            getBalance();
                            document.getElementById("site_bet_loading").style.display = "none";
                            document.getElementById("betslip-block").style.display = "block";
                        } else {

                            $(".showForm").hide();
                            document.getElementById("site_bet_loading").style.display = "none";
                            document.getElementById("betslip-block").style.display = "block";
                        }
                        //betMsgEmpty();
                        //hideLoading('showBet');
                        /*if(isMobile){
                        	setTimeout(function(){ $('#myModalBetView').modal('hide'); }, 1000);
                        }*/
                        //getBetsList();
                    }
                });
            }, delay);

            /*setTimeoutajax(function(){
           document.getElementById("site_bet_loading").style.display="none";
        }, 50000);  */
        } else if (bet_type == 'BOOKMAKER') {
            $('.amountint').text(bet_odds);
            var bet_type = $('#betTypeAdd').val();
            var bet_site = $('#betSide').val();
            var bet_odds = $('.amountint').text();

            var bet_amount = $('#odds_val').val();
            var team_name = $('#teamNameBet').val();

            var bet_profit = $('#bet-profit').text();

            var parameter = "";
            var teamname1 = $('#team1').val();
            var teamname2 = $('#team2').val();
            var teamname3 = $('#team3').val();

            var team1_total = $('#team1_total').text();
            var team2_total = $('#team2_total').text();
            var team3_total = $('#draw_total').text();

            var team1_BM_total = $('#team1_BM_total').text();
            var team2_BM_total = $('#team2_BM_total').text();
            var draw_BM_total = $('#draw_BM_total').text();
			
			var hid_fancy=$('#hid_fancy').val();
			var fancy_total=0;
			for(var f=0;f<hid_fancy;f++)
			{
				if($('#Fancy_Total_'+f))
				{
					fancy_total=parseFloat(fancy_total)+parseFloat($('#Fancy_Total_'+f).text());
				}
			}

            var stack = $('#inputStake').val();
            if (parseInt(stack) <= parseInt($('#div_min_bet_bm_limit').text())) {
                $('#betMsgALL').html('<div  id="msg-alert" class="alert alert-danger"><button type="button" class="close" style="margin-top: -7px;" data-dismiss="alert">x</button>Minimum bets is ' + $('#div_min_bet_bm_limit').text() + '</div>');
                $(".showForm").hide();
                $(".amountint").text("");
                $('#inputStake').val("");
                $('#odds_val').val("");
                return false;
            }
            if (parseInt(stack) >= parseInt($('#div_max_bet_bm_limit').text())) {
                $('#betMsgALL').html('<div  id="msg-alert" class="alert alert-danger"><button type="button" class="close" style="margin-top: -7px;" data-dismiss="alert">x</button>Maximum bets is ' + $('#div_max_bet_bm_limit').text() + '</div>');
                $(".showForm").hide();
                $(".amountint").text("");
                $('#inputStake').val("");
                $('#odds_val').val("");
                return false;
            }


            if (teamname1 == team_name) {
                parameter = "&teamname2=" + encodeURIComponent(teamname2) + "&teamname3=" + encodeURIComponent(teamname3);
            } else if (teamname2 == team_name) {
                parameter = "&teamname1=" + encodeURIComponent(teamname1) + "&teamname3=" + encodeURIComponent(teamname3);
            } else {
                parameter = "&teamname1=" + encodeURIComponent(teamname1) + "&teamname2=" + encodeURIComponent(teamname2);
            }

            $.ajax({
                url: '{{route("MyBetStore")}}',
                dataType: 'json',
                type: "POST",
                data: "sportID={{$match->sports_id}}&match_id={{$match->event_id}}&_token={{csrf_token()}}&bet_profit=" + bet_profit + "&bet_type=" + bet_type + "&bet_side=" + bet_site + "&bet_odds=" + bet_amount + "&bet_amount=" + stack + "&team_name=" + team_name + parameter + '&stack=' + stack + '&bet_cal_amt=' + bet_cal_amt + '&team1_total=' + team1_total + '&team2_total=' + team2_total + '&team3_total=' + team3_total + '&team1=' + teamname1 + '&team2=' + teamname2 + '&team3=' + teamname3 + '&team1_BM_total=' + team1_BM_total + '&team2_BM_total=' + team2_BM_total + '&team3_BM_total=' + draw_BM_total+'&fancy_total='+fancy_total,
                success: function(data) {
                    $('#betMsgALL').html(data.message);
                    if (data.status == true) {
                        $('#odds_val').val('');
                        $('#inputStake').val('');
                        //alert('bet placed succesfully');
                        $('.amountint').text('');
                        $(".showForm").hide();
                        $('#bet_display_table').show();
                        //$('#bet-profit').text('');
                        //$('#betAmount').val('');
                        get_BMtable();
                        var match_sel = $('#select_bet_on_match').val();
                        if (match_sel == "")
                            match_sel = '{{$match->event_id}}~~' + 'All';
                        call_display_bet_list(match_sel);
                        getBalance();
                    } else
                        $(".showForm").hide();
                }
            });
        } else {
            var parameter = "";
            var bet_type = $('#betTypeAdd').val();
            var bet_site = $('#betSide').val();
            //var bet_odds = $('#odds_val').val();///nnn
            var bet_odds = $('.amountint').text();

            //var bet_amount = $('#inputStake').val(); ///nnn
            var bet_amount = $('#odds_val').val();

            var stack = $('#inputStake').val();
            if (parseInt(stack) < parseInt($('#div_min_bet_fancy_limit').text())) {
                $('#betMsgALL').html('<div  id="msg-alert" class="alert alert-danger"><button type="button" class="close" style="margin-top: -7px;" data-dismiss="alert">x</button>Minimum bets is ' + $('#div_min_bet_fancy_limit').text() + '</div>');
                $(".showForm").hide();
                $('#inputStake').val("");
                $('#odds_val').val("");
                return false;
            }
            if (parseInt(stack) > parseInt($('#div_max_bet_fancy_limit').text())) {
                $('#betMsgALL').html('<div  id="msg-alert" class="alert alert-danger"><button type="button" class="close" style="margin-top: -7px;" data-dismiss="alert">x</button>Maximum bets is ' + $('#div_max_bet_fancy_limit').text() + '</div>');
                $(".showForm").hide();
                $(".amountint").text("");
                $('#inputStake').val("");
                $('#odds_val').val("");
                return false;
            }

            var team_name = $('#teamNameBet').val();
            var bet_profit = $('#bet-profit').text();
            var parameter = "";
            var teamname1 = $('#team1').val();
            var teamname2 = $('#team2').val();
            var teamname3 = $('#team3').val();
            var odds_volume = $('#odds_volume').val();
			
			var team1_total = $('#team1_total').text();
            var team2_total = $('#team2_total').text();
            var team3_total = $('#draw_total').text();

            var team1_BM_total = $('#team1_BM_total').text();
            var team2_BM_total = $('#team2_BM_total').text();
            var draw_BM_total = $('#draw_BM_total').text();
			
			var hid_fancy=$('#hid_fancy').val();
			var fancy_total=0;
			for(var f=0;f<hid_fancy;f++)
			{
				if($('#Fancy_Total_'+f))
				{
					fancy_total=parseFloat(fancy_total)+parseFloat($('#Fancy_Total_'+f).text());
				}
			}

            var bet_cal_amt = '';
            $.ajax({
                url: '{{route("MyBetStore")}}',
                dataType: 'json',
                type: "POST",
                data: "sportID={{$match->sports_id}}&match_id={{$match->event_id}}&_token={{csrf_token()}}&bet_profit=" + bet_profit + "&bet_type=" + bet_type + "&bet_side=" + bet_site + "&bet_odds=" + bet_amount + "&bet_amount=" + stack + "&team_name=" + team_name + parameter + '&stack=' + stack + '&odds_volume=' + odds_volume + '&bet_cal_amt=' + bet_cal_amt + '&team1=' + teamname1 + '&team2=' + teamname2 + '&team3=' + teamname3+ '&team1_total=' + team1_total + '&team2_total=' + team2_total + '&team3_total=' + team3_total + '&team1=' + teamname1 + '&team2=' + teamname2 + '&team3=' + teamname3 + '&team1_BM_total=' + team1_BM_total + '&team2_BM_total=' + team2_BM_total + '&team3_BM_total=' + draw_BM_total+'&fancy_total='+fancy_total,
                success: function(data) {
                    $('#betMsgALL').html(data.message);
                    if (data.status == true) {
                        $('#odds_val').val('');
                        $('#inputStake').val('');
                        $('.amountint').text('');
                        $(".showForm").hide();
                        $('#bet_display_table').show();
                       
						bet_Fancytable();
                        var match_sel = $('#select_bet_on_match').val();
                        if (match_sel == "")
                            match_sel = '{{$match->event_id}}~~' + 'All';
                        call_display_bet_list(match_sel);
                        getBalance();
                    } else
                        $(".showForm").hide();
                }
            });
        }
    }

</script>
@include('layouts.footer')
@endsection
