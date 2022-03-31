
@if(count($records) > 0)
    @foreach($records as $match)
        <div class="panel panel-default panel_content beige-bg-1">

            <h6 class="d-inline-block"><a href="risk-management-details/{{ $match['match_detail']['id'] }}" class="text-color-black @if($match['inPlay']=='True'){{'inplaytext'}}@endif">{{ $match['match_detail']['match_name'] }} @if($match['inPlay']=='True')<span style='color:green'></span>@endif <b>[{{ $match['match_detail']['formatted_match_date'] }}]</b></a></h6>
            <p style="transform: inherit;" class="blinkbtn collapse in-play-status-{{$match['match_detail']['event_id']}}"> <span class="blink_me"> IN PLAY </span> </p>

            <div class="row panel_row white-bg">
                <div class="col-md-3 p-0 team1-{{$match['match_detail']['event_id']}}">
                    <div class="market_listitems">
                        <div class="runner_details">
                            <div class="r_title">{{ $match['match_detail']['team_one'] }}</div>
                            <div class="@if($match['match_detail']['team1_bet_total'] >= 0){{'text-color-green'}}@else{{'text-color-red'}}@endif"><b>» {{ ($match['match_detail']['team1_bet_total']) }}</b></div>
                        </div>

                        <div class="button_content">
                            <button class="backbtn cyan-bg">{{'--'}}</button>
                            <button class="laybtn pink-bg">{{'--'}}</button>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 p-0 team2-{{$match['match_detail']['event_id']}}">
                    <div class="market_listitems">
                        <div class="runner_details">
                            <div class="r_title">{{ $match['match_detail']['team_two'] }}</div>
                            <div class="@if($match['match_detail']['team2_bet_total'] >= 0){{'text-color-green'}}@else{{'text-color-red'}}@endif"><b>» {{ ($match['match_detail']['team2_bet_total']) }}</b></div>
                        </div>

                        <div class="button_content">
                            <button class="backbtn cyan-bg">{{'--'}}</button>
                            <button class="laybtn pink-bg">{{'--'}}</button>
                        </div>
                    </div>
                </div>

                @if(!empty($match['match_detail']['team_draw']))
                    <div class="col-md-3 p-0 team3-{{$match['match_detail']['event_id']}}">
                        <div class="market_listitems">
                            <div class="runner_details">
                                <div class="r_title">{{ $match['match_detail']['team_draw'] }}</div>
                                <div class="@if($match['match_detail']['team_draw_bet_total'] >= 0){{'text-color-green'}}@else{{'text-color-red'}}@endif"><b>» {{ ($match['match_detail']['team_draw_bet_total']) }}</b></div>
                            </div>

                            <div class="button_content">
                                <button class="backbtn cyan-bg">{{'--'}}</button>
                                <button class="laybtn pink-bg">{{'--'}}</button>
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
    @endforeach
@else
    <div class="panel panel-default panel_content beige-bg-1"><h6>No match found.</h6></div>
@endif
