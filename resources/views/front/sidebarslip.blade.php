<!-- Bet Slip-->
<div class="betslip-block betform-section">
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
                                    @php
                                        $i=1;
                                    @endphp
                                    @foreach($stkval as $data1)
                                        <li><a class="btn light-grey-bg-6 text-color-black1 match_odd" data-odd="{{$data1}}" id="selectStake_{{$i}}">{{$data1}}</a></li>
                                    @php $i++; @endphp
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="betslip_bottom">
                        <div class="sumtxt">
                            <div>Liability</div>
                            <div><span class="text-color-red">0.00</span></div>
                        </div>
                        <ul class="btn-wrap">
                            <li><a class="add_player grey-gradient-bg" id="cancel_bet_form">Cancel All</a></li>
                            <li><a class="submit-btn text-color-yellow" onclick="saveBetcall(true)" style="cursor:pointer">Place Bet</a></li>
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
                    <input type="hidden" id="odds_position" value="" />
                </form>
            </div>
        </div>
    </div>
</div>
