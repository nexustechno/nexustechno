
@if(count($records) > 0)
    @foreach($records as $match)
        <div class="panel panel-default panel_content beige-bg-1">
            <h6>
                @if($match['inPlay']=='True')<p class="blinkbtn"> <span class="blink_me"> IN PLAY </span> </p>@endif
                <a href="risk-management-details/{{ $match['match_detail']['id'] }}" class="text-color-black @if($match['inPlay']=='True'){{'inplaytext'}}@endif">{{ $match['match_detail']['match_name'] }} @if($match['inPlay']=='True')<span style='color:green'></span>@endif <b>[{{ $match['match_detail']['formatted_match_date'] }}]</b></a>
            </h6>

            <div class="row panel_row white-bg">
                <div class="col-md-3 p-0">
                    <div class="market_listitems">
                        <div class="runner_details">
                            <div class="r_title">{{ $match['match_detail']['team_one'] }}</div>
                            <div class="@if($match['match_detail']['team1_bet_total'] >= 0){{'text-color-green'}}@else{{'text-color-red'}}@endif"><b>» {{ ($match['match_detail']['team1_bet_total']) }}</b></div>
                        </div>

                        <div class="button_content">
                            <button class="backbtn cyan-bg">@if($match['back1'] > 0){{ $match['back1'] }}@else{{'--'}}@endif</button>
                            <button class="laybtn pink-bg">@if($match['lay1'] > 0){{ $match['lay1'] }}@else{{'--'}}@endif</button>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 p-0">
                    <div class="market_listitems">
                        <div class="runner_details">
                            <div class="r_title">{{ $match['match_detail']['team_two'] }}</div>
                            <div class="@if($match['match_detail']['team2_bet_total'] >= 0){{'text-color-green'}}@else{{'text-color-red'}}@endif"><b>» {{ ($match['match_detail']['team2_bet_total']) }}</b></div>
                        </div>

                        <div class="button_content">
                            <button class="backbtn cyan-bg">@if($match['back11'] > 0){{ $match['back11'] }}@else{{'--'}}@endif</button>
                            <button class="laybtn pink-bg">@if($match['lay11'] > 0){{ $match['lay11'] }}@else{{'--'}}@endif</button>
                        </div>
                    </div>
                </div>

                @if($match['back12'] > 0 || $match['lay12'] > 0)
                    <div class="col-md-3 p-0">
                        <div class="market_listitems">
                            <div class="runner_details">
                                <div class="r_title">The Draw</div>
                                <div class="@if($match['match_detail']['team_draw_bet_total'] >= 0){{'text-color-green'}}@else{{'text-color-red'}}@endif"><b>» {{ ($match['match_detail']['team_draw_bet_total']) }}</b></div>
                            </div>

                            <div class="button_content">
                                <button class="backbtn cyan-bg">@if($match['back12'] > 0){{ $match['back12'] }}@else{{'--'}}@endif</button>
                                <button class="laybtn pink-bg">@if($match['lay12'] > 0){{ $match['lay12'] }}@else{{'--'}}@endif</button>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="col-md-3 p-0">
                    <div class="market_listitems">
                        <div class="runner_details">
                            <div class="r_title">Total Bets</div>
                            <div><b>» </b>{{ $match['match_detail']['total_bets'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

            </div>

        </div>
    @endforeach
@else
    <div class="panel panel-default panel_content beige-bg-1"><h6>No match found.</h6></div>
@endif
