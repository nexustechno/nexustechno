<div class="programe-setcricket inplay-section">
    <div class="firstblock-cricket lightblue-bg1">
        <span class="fir-col1"></span>
        <span class="fir-col2">1</span>
        <span class="fir-col2">X</span>
        <span class="fir-col2">2</span>
        <span class="fir-col3"></span>
    </div>
    @if (count($records) > 0)
        @foreach($records as $match)
            <div class="secondblock-cricket active-tag white-bg">
                <div class="mblinplay">

                    @if($match['f'] == 'True' && $sport->sId == 4)
                        <span style="color:green" class="game-fancy inplay in-play blue-bg-3 text-color-white">F</span>
                    @endif

                    @if($match['m1'] == 'True' && $sport->sId == 4)
                        <span style="color:green;" class="game-fancy inplay in-play blue-bg-3 text-color-white">B</span>
                    @endif

                    @if($type == 'inplay')<span style="color:green" class="deskinplay">In-Play</span>@endif

                </div>
                <span class="{{ 'fir-col1' }} @if($type == 'inplay'){{ 'green' }}@endif desk">
                    <a href="matchDetail/{{ $match['match_detail']['id'] }}" class="text-color-blue-light">{{ $match['match_detail']['match_name'] }}
                        @if($type == 'inplay')<span style="color:green" class="deskinplay">In-Play</span>@endif
                    </a>

                    @if($type == 'tomorrow' || $type == 'today')
                        <div class="mobileDate">{{ $match['match_detail']['formatted_match_date'] }}</div>
                    @endif

                    @if($match['f'] == 'True' && $sport->sId == 4)
                        <span style="color:green" class="game-fancy in-play blue-bg-3 text-color-white">F</span>
                    @endif

                    @if($match['m1'] == 'True' && $sport->sId == 4)
                        <span style="color:green;margin-right: 40px;" class="game-fancy in-play blue-bg-3 text-color-white">B</span>
                    @endif

                </span>

                <span class="fir-col2">
                    <a class="backbtn lightblue-bg2">
                        @if(isset($match['back1']) && $match['back1'] > 0) {{ $match['back1'] }} @else {{ "--" }} @endif
                    </a>
                    <a class="laybtn lightpink-bg1">
                        @if(isset($match['lay1']) && $match['lay1'] > 0) {{ $match['lay1'] }} @else {{ "--" }} @endif
                    </a>
                </span>

                <span class="fir-col2">
                    <a class="backbtn lightblue-bg2">
                        @if(isset($match['back12']) && $match['back12'] > 0) {{ $match['back12'] }} @else {{ '--' }}@endif
                    </a>
                    <a class="laybtn lightpink-bg1">
                        @if(isset($match['lay12']) && $match['lay12'] > 0) {{ $match['lay12'] }} @else {{ '--' }}@endif
                    </a>
                </span>

                <span class="fir-col2">
                    <a class="backbtn lightblue-bg2">
                        @if(isset($match['back11']) && $match['back11'] > 0) {{ $match['back11'] }} @else {{ '--' }}@endif
                    </a>
                    <a class="laybtn lightpink-bg1">
                        @if(isset($match['lay11']) && $match['lay11'] > 0) {{ $match['lay11'] }} @else {{ '--' }}@endif
                    </a>
                </span>

                <span class="fir-col3 text-center">
                    @php
                        $imagePath = "asset/front/img/round-pin.png";
                            $getUserCheck = \Illuminate\Support\Facades\Session::get('playerUser');
                            if(!empty($getUserCheck)){
                                $isFav = \App\UsersFavMatch::where("user_id",$getUserCheck->id)->where("match_id",$match['match_detail']['id'])->first();
                                if(!empty($isFav)){
                                    $imagePath = "asset/front/img/round-pin1.png";
                                }
                            }
                    @endphp

                    <a data-id="{{ $match['match_detail']['id'] }}" class="cricket-pin make-fav-match pin_{{ $match['match_detail']['id'] }}"><img class="unpin-img" src="{{ asset($imagePath) }}"><img class="pin-img hover-img" src="{{ asset('asset/front/img/round-pin1.png') }}"></a>
                </span>
            </div>
        @endforeach
    @else
        <p>There are no events to be displayed.</p>
    @endif
</div>
