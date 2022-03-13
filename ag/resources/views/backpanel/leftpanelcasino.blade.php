<?php
use App\Casino;
$casino = Casino::get();
?>
<div class="left-menuga white-bg">
    <div class="casino_leftpanel black-bg1">
        <ul>
            @foreach($casino as $casinos)
            <li class="{{ (request()->is('live/casino' )) ? 'active' : '' }}"><a href="{{route('casinoDetail',$casinos->casino_name)}}" class="text-color-white">{{$casinos->casino_title}}</a></li>
            @endforeach
        </ul>
    </div>
</div>
