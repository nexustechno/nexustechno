<div class="betslip-block mt-10"  id="bet_display_table">
    <a class="collape-link text-color-white blue-gradient-bg1" data-toggle="collapse" href="#collapseExample1" role="button" aria-expanded="false" aria-controls="collapseExample1">
        <img src="{{ URL::to('asset/front/img/refresh-white.png')}}" class="slip_refresh" alt=""> Open Bets <img src="{{ URL::to('asset/front/img/minus-icon.png')}}">
    </a>
    <div class="collapse show" id="collapseExample1">
        <div class="card card-body">
            <div class="open_bets_wrap betslip_board">
                <div class="slip_sort">
                    <select name="select_bet_on_match" id="select_bet_on_match" onchange="call_display_bet_list_mobile(this.value)">
                        <option value="{{$match->event_id}}~~All">All Bet</option>
                        @if(!empty($placed_bet_match_list))
                            @foreach($placed_bet_match_list as $matchs)
                                <option value="{{$matchs->match_id}}">{{$matchs->match_name}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <ul class="betslip_head lightblue-bg1">
                    <li class="col-bet"><strong>Matched</strong></li>
                </ul>
                <div id="divbetlistmobile" class="mobiledivbetlist divbetlistmobile">
                    @php
                    $j=0; $k=0;
                    @endphp
                    @foreach($my_placed_bets_all as $bet)
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
                                    @if($bet->bet_type=='ODDS' || $bet->bet_type=='BOOKMAKER')
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
                            <div class="col-odd text-color-blue-2 text-center">{{$bet->bet_odds}}</div>
                            <div class="col-stake text-color-blue-2 text-center">
                                {{($bet->bet_amount)}}
                            </div>
                            <div class="col-profit"><?php /*?>{{$bet->bet_odds}}<?php */?>
                                @if($bet->bet_type=='ODDS')
                                    {{($bet->bet_profit)}}
                                @elseif($bet->bet_type=='SESSION')
                                    {{($bet->bet_oddsk)}}
                                @elseif($bet->bet_type=='BOOKMAKER')
                                    {{($bet->bet_profit)}}
                                @endif
                            </div>
                        </div>
                    	@php $j++  @endphp
                    @endif
                    @endforeach
                    @foreach($my_placed_bets_all as $bet)
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
                                    @if($bet->bet_type=='ODDS' || $bet->bet_type=='BOOKMAKER')
                                    LAY
                                    @elseif($bet->bet_type=='SESSION')
                                    NO
                                    @endif</span>
                                <span class="shortamount">{{$bet->team_name}}</span>
                                <span>{{$bet->bet_type}}</span>
                            </div>
                            <div class="col-odd text-color-blue-2 text-center">{{$bet->bet_odds}}</div>
                            <div class="col-stake text-color-blue-2 text-center">
                                {{($bet->bet_amount)}}
                            </div>
                            <div class="col-profit">
                                @if($bet->bet_type=='ODDS')
                                	{{($bet->exposureAmt)}}
                                @elseif($bet->bet_type=='SESSION')
                               		{{($bet->bet_oddsk)}}
                                @elseif($bet->bet_type=='BOOKMAKER')
                               		{{($bet->exposureAmt)}}
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

<script type="text/javascript">
    function call_display_bet_list_mobile(dval) {
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
            $('.divbetlistmobile').html(data);
        }
    });
}
</script>