<?php
use App\Sport;
$sports = Sport::all();
?>
<div class="left-menuga white-bg">
    <div class="topmenu-left black-bg-2">
        <div class="barsicon text-color-yellow1">
            <a><img src="{{ URL::to('asset/front/img/leftmenu-arrow1.png') }} "><img class="hover-img" src="{{ URL::to('asset/front/img/leftmenu-arrow2.png') }} "></a>
        </div>
        <div class="soprts-link text-color-yellow1"><a>Sports</a></div>
    </div>
    <ul class="leftul" >
        @foreach($sports as $sport)
        <li>
            <a href="#homeSubmenu_{{$sport->sId}}" class="text-color-black2" data-toggle="collapse" aria-expanded="false">{{$sport->sport_name}}</a>
            <a href="#homeSubmenu_{{$sport->sId}}" data-toggle="collapse" aria-expanded="false">
                <img src="{{ URL::to('asset/front/img/leftmenu-arrow3.png') }} " class="hoverleft"><img class="hover-img" src="{{ URL::to('asset/front/img/leftmenu-arrow4.png') }} ">
            </a>
            <ul class="dropul white-bg list-unstyled collapse" id="homeSubmenu_{{$sport->sId}}">
            </ul>
        </li>
        @endforeach
        
    </ul>
</div>
<input type="hidden" name="_token_footer" id="_token_footer" value="{!! csrf_token() !!}">
@push('scripts')
@endpush