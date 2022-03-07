@foreach($bets as $bet)
    <div class="betslip_box light-blue-bg-1 casino-bet-item" id="backbet">
        <div class="betn">
            <span class="slip_type lightblue-bg2 text-uppercase">{{$bet->bet_side}}</span>
            <span class="shortamount">{{ $bet->team_name }}</span>
        </div>
        <div class="col-odd text-color-blue-2 text-center">{{$bet->odds_value}}</div>
        <div class="col-stake text-color-blue-2 text-center">{{$bet->stake_value}}</div>
        <div class="col-profit">{{$bet->casino_profit}}</div>
    </div>
@endforeach
