@foreach($bets as $bet)
    <div class="betslip_box mt-1 @if($bet->bet_side=='lay'){{'light-pink-bg-2'}}@else{{'light-blue-bg-1'}}@endif casino-bet-item" id="backbet">
        <p class="m-0 text-left font-weight-bold p-2">
            <span style="background: blanchedalmond;padding: 6px;">{{ $bet->user->user_name }}({{ $bet->user->first_name." ".$bet->user->last_name }})</span>
            <span style="background: aliceblue;padding: 6px;">RoundID: ({{ explode(".",$bet->roundid)[1] }})</span>
        </p>

        <div class="betn">
            <span class="slip_type @if($bet->bet_side=='lay'){{'lightpink-bg2'}}@else{{'lightblue-bg2'}}@endif text-uppercase">{{$bet->bet_side}}</span>
            <span class="shortamount">{{ $bet->team_name }}</span>
        </div>
        <div class="col-odd text-color-blue-2 text-center">{{$bet->odds_value}}</div>
        <div class="col-stake text-color-blue-2 text-center">{{$bet->stake_value}}</div>
        <div class="col-profit">{{$bet->casino_profit}}</div>
    </div>
@endforeach
