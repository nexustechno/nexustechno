<?php
use App\Sport;
$sports = Sport::all();
?>
<div class="left-menuga white-bg">
    <div class="topmenu-left black-bg-2">
        <div class="barsicon text-color-yellow1">
            <a><img src="{{ URL::to('asset/front/img/leftmenu-arrow1.png') }} "><img class="hover-img" src="{{ URL::to('asset/front/img/leftmenu-arrow2.png') }} "></a>
        </div>
        <div class="soprts-link text-color-yellow1" onclick="clickLeftMenuRest()"><a>Sports</a></div>
    </div>
    <ul class="leftul" >
        @foreach($sports as $sport)
        <li>
            <a href="#homeSubmenu_{{$sport->sId}}" class="text-color-black2" data-toggle="collapse" aria-expanded="false">{{$sport->sport_name}}</a>
            <a href="#homeSubmenu_{{$sport->sId}}" data-toggle="collapse" aria-expanded="false">
                <img src="{{ URL::to('asset/front/img/leftmenu-arrow3.png') }} " class="hoverleft">
                <img class="hover-img" src="{{ URL::to('asset/front/img/leftmenu-arrow4.png') }} ">
            </a>
            <ul class="dropul white-bg list-unstyled collapse" id="homeSubmenu_{{$sport->sId}}">

            </ul>
        </li>
        @endforeach

        @php
            $casino = \App\Casino::where("status",1)->get();
        @endphp

            <li>
                <a href="#homeSubmenu_casino" class="text-color-black2" data-toggle="collapse" aria-expanded="false">Casino</a>
                <a href="#homeSubmenu_casino" data-toggle="collapse" aria-expanded="false">
                    <img src="{{ URL::to('asset/front/img/leftmenu-arrow3.png') }} " class="hoverleft">
                    <img class="hover-img" src="{{ URL::to('asset/front/img/leftmenu-arrow4.png') }} ">
                </a>

                <ul class="dropul white-bg list-unstyled collapse" id="homeSubmenu_casino">
                    @foreach($casino as $item)
                        <li><a href="{{route('casinoDetail',[$item->id,$item->casino_name])}}" class="text-color-black2">{{$item->casino_title}}</a></li>
                    @endforeach
                </ul>
            </li>

    </ul>
</div>
<input type="hidden" name="_token_footer" id="_token_footer" value="{!! csrf_token() !!}">
@push('page_scripts')
    <script>
        function clickLeftMenuRest() {
            $(".leftul ul").removeClass('show');
        }
    </script>
@endpush
