@extends('layouts.front_layout')
@section('content')
    <style type="text/css">
        @media only screen and (max-width: 768px) {
            .mobile-multi {
                display: block !important;
            }
        }

        .multiimg img {
            width: 100%;
            border: 1px solid #00000052;
        }

        .multiimg {
            padding: 10px;
        }
    </style>
    <?php
    use Carbon\Carbon;
    use App\User;

    use App\setting;
    $getUser = Session::get('playerUser');
    if (!empty($getUserCheck)) {
        $getUser = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
    }

    if (!empty($getUser)) {
        $auth_id = $getUser->id;
    }

    $settings = setting::first();

    ?>
    @if(!empty($getUser))
        <section>
            <div class="container-fluid">
                @if($errors->any())
                    <h4>{{$errors->first()}}</h4>
                @endif
                <div class="main-wrapper">
                    @include('layouts.leftpanel')
                    <div class="middle-section">
                        @if(!empty($getUser))
                            @if(!empty($settings->user_msg))
                                <div class="news-addvertisment black-gradient-bg text-color-white">
                                    <h4>News</h4>
                                    <marquee scrollamount="3">
                                        <a href="#" class="text-color-blue">{{$settings->user_msg}}</a>
                                    </marquee>
                                </div>
                            @endif
                        @endif
                        <div class="middle-wraper">
                            <div class="home-carousel owl-carousel owl-theme">
                                @foreach($banner as $banners)
                                    <div class="itemslider">
                                        <img src="{{ URL::to('ag/asset/upload')}}/{{$banners->banner_image}}" alt="Image">
                                    </div>
                                @endforeach
                            </div>
                            <div class="maintabs">
                                <h3 class="yellow-bg2 text-color-black2 highligths-txt">Multi Market</h3>
                                <ul class="nav nav-tabs yellow-gradient-bg2" role="tablist">
                                    <?php $count = 1; ?>
                                    @foreach($sports as $sport)
                                        <li class="nav-item gettab{{$count}}" data-id="{{$sport->sport_name}}">
                                            <a class="nav-link text-color-white darkblue-bg1 {{$sport->sport_name}}"
                                               href="#{{$sport->sport_name}}" role="tab"
                                               data-toggle="tab">{{$sport->sport_name}}</a>
                                        </li>
                                        <?php $count++; ?>
                                    @endforeach
                                </ul>
                                <div class="tab-content" id="tabdatadiv">
                                    <?php
                                    $i = 0;
                                    ?>
                                    @foreach($sports as $sport)


                                        <div role="tabpanel" class="tab-pane @if($i==0) active @endif tabname{{$i}}"
                                             id="{{$sport->sport_name}}">
                                            <div class="programe-setcricket">
                                                <div class="firstblock-cricket lightblue-bg1">
                                                    <span class="fir-col1"></span>
                                                    <span class="fir-col2">1</span>
                                                    <span class="fir-col2">X</span>
                                                    <span class="fir-col2">2</span>
                                                    <span class="fir-col3"></span>
                                                </div>
                                                <?php
                                                $match = \App\UsersFavMatch::join('match', 'match.id', '=', 'users_fav_matches.match_id')->where('sports_id', $sport->sId)->where('user_id', $auth_id)->where('status', 1)->where('winner', null)->groupBy('users_fav_matches.match_id')->get();
                                                $count = count($match);
                                                ?>
                                                @if($count !=0)
                                                    @foreach($match as $matches)
                                                        <?php
                                                        $date = Carbon::parse(strftime($matches->match_date));
                                                        $date->addMinutes(330);

                                                        if (Carbon::parse($date)->isToday()) {
                                                            $match_date = date('d-m-Y h:i A', strtotime($date));
                                                        } else if (Carbon::parse($date)->isTomorrow())
                                                            $match_date = 'Tomorrow ' . date('d-m-Y h:i A', strtotime($date));
                                                        else
                                                            $match_date = date('d-m-Y h:i A', strtotime($date));

                                                        ?>

                                                        <div class="secondblock-cricket white-bg" id="div_mdata">
                                                           <span class="fir-col1">
                                                                <a href="{{route('matchDetail',$matches->event_id)}}"
                                                                   class="text-color-blue-light">{{$matches->match_name}} </a>
                                                                <div>{{$match_date}}</div>
                                                            </span>
                                                            <span class="fir-col2">&nbsp;</span>
                                                            <span class="fir-col2">&nbsp;</span>
                                                            <span class="fir-col2">&nbsp;</span>
                                                            <span class="fir-col3 text-center">
                                                                <a data-id="{{ $matches->id }}" data-action="multimarket" class="cricket-pin make-fav-match pin_{{ $matches->id }}">
                                                                    <img class="unpin-img" src="{{ asset('asset/front/img/round-pin1.png') }}">
                                                                    <img class="pin-img hover-img" src="{{ asset('asset/front/img/round-pin.png') }}">
                                                                </a>
                                                            </span>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <div class="secondblock-cricket white-bg" id="div_mdata">
                                                       <span class="fir-col1">
                                                            <a href="javascript:void();" class="text-color-blue-light">No Record Found</a>
                                                        </span>
                                                    </div>
                                                @endif
                                                @php $i++; @endphp
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @include('front.social-footer-links')
                        </div>
                    </div>
                    @include('layouts.rightpanel')
                </div>
            </div>
            <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}">
        </section>
    @endif

    @if(empty(Session::get('playerUser')))
        <section class="mobile-multi" style="display: none;">
            <div class="container-fluid">
                <div class="main-wrapper">
                    <div class="multiimg">
                        <img src="{{ URL::to('asset/front/img/multimarket.JPEG') }}">
                    </div>
                </div>
            </div>
        </section>
    @endif
    @include('front.common-script-for-list')
    <script type="text/javascript">
        $(document).ready(function () {
            var gettab = $('.gettab1').attr("data-id");
            $("." + gettab).addClass("active");
        });
    </script>
    @include('layouts.footer')
@endsection
