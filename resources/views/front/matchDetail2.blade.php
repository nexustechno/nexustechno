@extends('layouts.front_layout')
@push('page_css')
    <style>
        .pink-dark {
            background-color: #f4496d !important;
            color: #fff !important;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, .5);
        }

        .blue-dark {
            background-color: #1a8ee1 !important;
            color: #fff !important;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, .5);
        }

        body {
            overflow: hidden;
        }

        .match-finished span {
            color: #0c0;
            font-size: 26px;
        }

        #snackbar {
            visibility: hidden;
            min-width: 250px;
            margin-left: -125px;
            background-color: #333;
            color: #fff;
            text-align: center;
            border-radius: 2px;
            padding: 16px;
            position: fixed;
            z-index: 1;
            left: 50%;
            bottom: 30px;
            font-size: 17px;
        }

        #snackbar.show {
            visibility: visible;
            -webkit-animation: fadein 0.5s, fadeout 0.5s 2.5s;
            animation: fadein 0.5s, fadeout 0.5s 2.5s;
        }

        @-webkit-keyframes fadein {
            from {
                bottom: 0;
                opacity: 0;
            }
            to {
                bottom: 30px;
                opacity: 1;
            }
        }

        @keyframes fadein {
            from {
                bottom: 0;
                opacity: 0;
            }
            to {
                bottom: 30px;
                opacity: 1;
            }
        }

        @-webkit-keyframes fadeout {
            from {
                bottom: 30px;
                opacity: 1;
            }
            to {
                bottom: 0;
                opacity: 0;
            }
        }

        @keyframes fadeout {
            from {
                bottom: 30px;
                opacity: 1;
            }
            to {
                bottom: 0;
                opacity: 0;
            }
        }
    </style>
@endpush
@push('php_code')
    <?php
    use Carbon\Carbon;
    use App\User;
    use App\Match;
    use App\ManageTv;
    function FindParentDelayTime($pid)
    {
        $test = User::where('id', $pid)->first();
        if (!empty($test->dealy_time)) {

            $delay = $bookmaker = $fancy = $tennis = $soccer = 0;
            $delay = ($test->dealy_time) * 1000;
            $bookmaker = ($test->bookmaker) * 1000;
            $fancy = ($test->fancy) * 1000;
            $tennis = ($test->tennis) * 1000;
            $soccer = ($test->soccer) * 1000;

            //return $delayTime = ($test->dealy_time)*1000;
            return $delayTime = $delay . ',' . $bookmaker . ',' . $fancy . ',' . $tennis . ',' . $soccer;
        } else {
            if ($test->parentid != 1)
                $delayTime = FindParentDelayTime($test->parentid);
            else
                return 0;
        }
    }
    $getUserCheck = Session::get('playerUser');
    //echo $getUserCheck; exit;
    if (!empty($getUserCheck)) {
        $getUser = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
    }
    $delayTime = 0; $delayTime_BM = 0; $delayTime_Fancy = 0; $delayTime_tenis = 0; $delayTime_soccer = 0;
    if (!empty($getUser)) {
        $delay = User::where('id', $getUser->id)->first();

        if ($delay->parentid == 1 || $delay->bookmaker != '') {
            $delayTime_BM = ($delay->bookmaker) * 1000;
        }
        if ($delay->parentid == 1 || $delay->fancy != '') {
            $delayTime_Fancy = ($delay->fancy) * 1000;
        }
        if ($delay->parentid == 1 || $delay->tennis != '') {
            $delayTime_tenis = ($delay->tennis) * 1000;
        }
        if ($delay->parentid == 1 || $delay->soccer != '') {
            $delayTime_soccer = ($delay->soccer) * 1000;
        }

        if ($delay->parentid == 1 || $delay->dealy_time != '') {
            $delayTime = ($delay->dealy_time) * 1000;
        } else {
            $delay1 = User::where('id', $delay->parentid)->first();
            if (empty($delay1->dealy_time)) {
                if ($delay1->parentid != 1) {
                    $delayTime_all = FindParentDelayTime($delay1->parentid);
                    $delayTime_all = explode(",", $delayTime_all);
                    $delayTime = $delayTime_all[0];
                    $delayTime_BM = @$delayTime_all[1];
                    $delayTime_Fancy = @$delayTime_all[2];
                    $delayTime_tenis = @$delayTime_all[3];
                    $delayTime_soccer = @$delayTime_all[4];
                } else {
                    $delayTime = 0;
                    $delayTime_BM = 0;
                    $delayTime_Fancy = 0;
                    $delayTime_tenis = 0;
                    $delayTime_soccer = 0;
                }
            } else {
                $delayTime = ($delay1->dealy_time) * 1000;
                $delayTime_BM = ($delay1->bookmaker) * 1000;
                $delayTime_Fancy = ($delay1->fancy) * 1000;
                $delayTime_tenis = ($delay1->tennis) * 1000;
                $delayTime_soccer = ($delay1->soccer) * 1000;
            }
        }

    }
    $managetv = ManageTv::latest()->first();
    $mdata = Match::where('id', $match->id)->first();
    $chk = $mdata->status_m;
    $chkb = $mdata->status_b;
    $chkf = $mdata->status_f;
    $settings =\App\setting::first();
    ?>
@endpush
@section('content')

    <section>
        <div id="snackbar">Something Wrong Please Contact to Upline</div>
        <div class="container-fluid">
            <div class="main-wrapper match-bet-res">


            @include('layouts.leftpanel')
            <!-- end right panel -->
                <?php
                //check is mobile or desktop
                $useragent = $_SERVER['HTTP_USER_AGENT'];
                $iPod = stripos($useragent, "iPod");
                $iPad = stripos($useragent, "iPad");
                $iPhone = stripos($useragent, "iPhone");
                $Android = stripos($useragent, "Android");
                $iOS = stripos($useragent, "iOS");

                $DEVICE = ($iPod || $iPad || $iPhone || $Android || $iOS);
                $is_agent = '';
                if (!$DEVICE) {
                    $is_agent = 'desktop';
                } else {
                    $is_agent = 'mobile';
                }
                //end for check is mobile or desktop

                if ($match->sports_id == 1)
                    $delayTime = $delayTime_soccer;
                if ($match->sports_id == 2)
                    $delayTime = $delayTime_tenis;
                if ($delayTime == '')
                    $delayTime = 1;

                if ($delayTime_BM == '')
                    $delayTime_BM = 1;
                if ($delayTime_Fancy == '')
                    $delayTime_Fancy = 1;

                ?>
                @if($is_agent=='mobile')
                    @if(!empty($logindata))
                        <div class="betslip-block mLiveTv fxsrc">
                            <!-- <a class="collape-link text-color-white blue-gradient-bg1" data-toggle="collapse" href="#live_tv" role="button" aria-expanded="false" aria-controls="collapseExample">
                                <div class="w-100">Live TV
                                    <span class="float-right pr-2"><i class="fas fa-tv"></i></span>
                                </div>
                            </a> -->
                            <a class="collape-link text-color-white blue-gradient-bg1" data-toggle="collapse"
                               href="#live_tv" role="button" aria-expanded="false" aria-controls="collapseExample">
                                <div class="w-100">Live TV
                                    <span class="float-right pr-2"><i class="fas fa-tv"></i></span>
                                </div>
                            </a>
                            <div class="collapse show" id="live_tv">
                                <div class="card card-body tv_tabs_block">
                                    {{--<ul class="nav nav-tabs darkblue-bg" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link text-color-white red-bg active" data-toggle="tab" href="#tabs-1" role="tab">TV1</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link text-color-white red-bg" data-toggle="tab" href="#tabs-2" role="tab">TV2</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link text-color-white red-bg" data-toggle="tab" href="#tabs-3" role="tab">TV3</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link text-color-white red-bg" data-toggle="tab" href="#tabs-4" role="tab">TV4</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link text-color-white red-bg" data-toggle="tab" href="#tabs-5" role="tab">TV5</a>
                                        </li>
                                    </ul>--}}
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="tabs-1" role="tabpanel">
                                            <?php
                                            $eventid = $match->event_id;
                                            $sprtid = $match->sports_id;
                                            ?>
                                            @if($inplay === 'True')
                                                <iframe
                                                    src="https://richexchange.live/nexus/nexus.php?eventid=<?php echo $eventid;?>&sports_id=1"
                                                    height="270" title="YouTube video player" frameborder="0"
                                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                                    id="iframe"></iframe>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif

                <div class="middle-section second">
                    @if(!empty($getUser))
                        @if(!empty($settings->user_msg))
                            <div class="news-addvertisment black-gradient-bg text-color-white">
                                <h4>News</h4>
                                <marquee>
                                    <a href="#" class="text-color-blue">{{$settings->user_msg}}</a>
                                </marquee>
                            </div>
                        @endif
                    @endif
                    <div class="middle-wraper">

                        <?php /*?>@if($match->sports_id == '1')
                        <div class="match-innerbg-detail soccerbg" style="display:none;background-image: url('http://betexchange.govindcrankrod.com/asset/front/img/soccer.png');min-height: 150px;background-repeat: no-repeat;background-position: inherit;background-size: cover;justify-content: center;align-items: center;padding-top: 20px;" id="scoredata">

                        </div>
                        @endif
                        @if($match->sports_id == '2')
                        <div class="match-innerbg-detail tennisbg" style="display:none;">
                            <div class="match-banner tennis" id="scoredata">

                            </div>
                        </div>
                        @endif
                        @if($match->sports_id == '4')
                        <div class="match-innerbg-detail cricketbg" style="display:none; ">
                            <div class="match-banner cricket" id="scoredata">
                            </div> <!-- match-banner -->
                        </div>
                        @endif<?php */?>
                        <?php
                        $match_date = '';
                        if ($match_updated_date != '') {
                            $dt = Carbon::parse($match_updated_date);
                            // $dt->addMinutes(330);
                            if (Carbon::parse($dt)->isToday())
                                $match_date = date('h:i A', strtotime($dt));
                            else if (Carbon::parse($dt)->isTomorrow())
                                $match_date = 'Tomorrow ' . date('h:i A', strtotime($dt));
                            else
                                $match_date = $dt;
                        } else {
                            if (Carbon::parse($match['match_date'])->isToday())
                                $match_date = date('h:i A', strtotime($match['match_date']));
                            else if (Carbon::parse($match['match_date'])->isTomorrow())
                                $match_date = 'Tomorrow ' . date('h:i A', strtotime($match['match_date']));
                            else
                                $match_date = $match['match_date'];
                        }

                        if ($match['sports_id'] == 4 && $inplay === 'True') {
                            $match_time = " <span style='color:green' class='deskinplay' >In-Play</span>";
                        } else if ($match['sports_id'] != 4 && $inplay == 1) {
                            $match_time = " <span style='color:green' class='deskinplay' >In-Play</span>";
                        } else {
                            $match_time = "<span>" . date('h:i A', strtotime($match['match_date'])) . "</span>";
                        }
                        ?>
                        @if($inplay == 'True')
                            <div class="match-innerbg-detail soccerbg live_score_card_{{ $match->sports_id }}">
                                @if($match->sports_id == 4)
                                    <iframe id="LiveScoreCard"
                                            src="https://richexchange.live/nexus/scorboard.php?date=<?php echo date('d-m-Y', strtotime($match['match_date'])) ?>&time=<?php echo date('H:i:s', strtotime($match['match_date'])) ?>&event_id=<?php echo $match->event_id;?>&match_type=cricket"
                                            title="YouTube video player" frameborder="0"
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                            allowfullscreen></iframe>
                                @elseif($match->sports_id == 2)
                                    <iframe id="LiveScoreCard"
                                            src="https://richexchange.live/nexus/scorboard.php?event_id=<?php echo $match->event_id;?>&match_type=tennis"
                                            title="YouTube video player" frameborder="0"
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                            allowfullscreen></iframe>
                                @elseif($match->sports_id == 1)
                                    <iframe id="LiveScoreCard"
                                            src="https://richexchange.live/nexus/scorboard.php?event_id=<?php echo $match->event_id;?>&match_type=soccer"
                                            title="YouTube video player" frameborder="0"
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                            allowfullscreen></iframe>
                                @endif
                            </div>
                        @endif

                    <!-- match-innerbg-detail-->
                        <div class="game_info_match blue-gradient-bg4 text-color-grey d-lg-none">
                            <span></span>
                            <ul>
                                <li><img src="{{ URL::to('asset/front/img/clock-green-icon.png') }}" alt=""> In-Play
                                </li>
                            </ul>
                        </div>

                        <div class="match-tracktop black-bg1 text-color-white">
                            <div class="three-tag">
                                <?php $split = explode(" v ", strtolower($match->match_name)); ?>
                                <h4 class="d-none d-lg-block">
                                    <span @if($inplay=='True' ) class="inplay_active" @else class="inplay_deactive" @endif> &nbsp; </span>
                                    &nbsp;&nbsp;&nbsp;
                                    {{strtoupper($split[0])}}
                                    V
                                    {{strtoupper($split[1])}}
                                </h4>
                                <h4 class="d-lg-none">
                                    <span @if($inplay=='True' ) class="inplay_active" @else class="inplay_deactive" @endif> &nbsp; </span>
                                    &nbsp;&nbsp;&nbsp;
                                    {{substr($split[0],0,3)}}
                                </h4>
                                <h4 class="text-right d-lg-none"> {{substr($split[1],0,3)}}</h4>

                                <div class="timeblockireland"><img
                                        src="{{ URL::to('asset/front/img/clock-icon.png') }} "> {!! $match_time !!}
                                </div>
                            </div>
                            {{--<div class="arrowup-icon"> <i class="fas fa-chevron-up text-fill-yellow"></i> </div>--}}
                        </div>
                        <div class="match-track-block">
                            <div class="toprisk-block white-bg">
                                <?php
                                if (!empty($getUserCheck) && isset($getUserCheck)) {
                                    $isFav = \App\UsersFavMatch::where("user_id", $getUserCheck->id)->where("match_id", $match->id)->first();
                                }
                                ?>

                                <ul class="toprisk_pinrefresh d-lg-none1">
                                    <li>
                                        <a id="pinrisk" data-id="{{ $match->id }}"
                                           class="text-color-white @if(isset($isFav) && !empty($isFav)){{'active'}}@endif"><img
                                                src="{{ URL::to('asset/front/img/pin.svg') }}" alt=""> Pin</a>
                                    </li>
                                    <li>
                                        <a href="" class="text-color-white"><img
                                                src="{{ URL::to('asset/front/img/refresh.svg') }}" alt=""> Refresh</a>
                                    </li>
                                </ul>
                                <!-- Live TV Start -->
                                @if($is_agent=='desktop')
                                    @if(!empty($logindata))
                                        <div class="betslip-block mb-2 mt-10">
                                            <a class="collape-link text-color-white blue-gradient-bg1"
                                               data-toggle="collapse" href="#live_tv" role="button"
                                               aria-expanded="false" aria-controls="collapseExample">
                                                <div class="w-100">Live TV
                                                    <span class="float-right pr-2"><i class="fas fa-tv"></i></span>
                                                </div>
                                            </a>
                                            <?php
                                            $eventid = $match->event_id;
                                            $sprtid = $match->sports_id;
                                            ?>
                                            <div class="collapse show" id="live_tv">
                                                <div class="card card-body tv_tabs_block">

                                                    <div class="tab-content">
                                                        <div class="tab-pane active" id="tabs-1" role="tabpanel">

                                                            @if($inplay == 'True')
                                                                <iframe
                                                                    src="https://richexchange.live/nexus/nexus.php?eventid=<?php echo $eventid;?>&sports_id=1"
                                                                    height="270" title="YouTube video player"
                                                                    frameborder="0"
                                                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                                                    id="iframe"></iframe>
                                                            @endif
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                @endif
                            @endif
                            <!-- Live TV End -->

                                <div class="twodiv-ireland">
                                    <div class="ireland-txt dark-grey-bg-1 text-color-blue-2">Match Odds</div>
                                    <?php
                                    $match_date = '';
                                    if ($match_updated_date != '') {
                                        $dt = Carbon::parse($match_updated_date);
                                        // $dt->addMinutes(330);

                                        if (Carbon::parse($dt)->isToday())
                                            $match_date = date('h:i A', strtotime($dt));
                                        else if (Carbon::parse($dt)->isTomorrow())
                                            $match_date = 'Tomorrow ' . date('h:i A', strtotime($dt));
                                        else
                                            $match_date = $dt;
                                    } else {
                                        if (Carbon::parse($match['match_date'])->isToday())
                                            $match_date = date('h:i A', strtotime($match['match_date']));
                                        else if (Carbon::parse($match['match_date'])->isTomorrow())
                                            $match_date = 'Tomorrow ' . date('h:i A', strtotime($match['match_date']));
                                        else
                                            $match_date = $match['match_date'];
                                    }

                                    if ($match['sports_id'] == 4 && $inplay === 'True') {
                                        $match_time = " <span style='color:green' class='deskinplay' >In-Play</span>";
                                    } else if ($match['sports_id'] != 4 && $inplay == 1) {
                                        $match_time = " <span style='color:green' class='deskinplay' >In-Play</span>";
                                    } else {
                                        $match_time = "<span>" . date('h:i A', strtotime($match['match_date'])) . "</span>";
                                    }

                                    ?>
                                    <div class="timeblockireland"><img
                                            src="{{ URL::to('asset/front/img/clock-icon.png') }} ">{!! $match_time !!}
                                    </div>
                                    <div class="minmax-txt light-grey-bg-5 ">
                                        <span class="text-color-blue-3">Min</span>
                                        <span id="div_min_bet_odds_limit"
                                              class="oddMin">{{$match->min_bet_odds_limit}}</span>
                                        <span class="text-color-blue-3 ">Max</span>
                                        <span id="div_max_bet_odds_limit"
                                              class="oddMax">{{$match->max_bet_odds_limit}}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="app">
                            @if(isset($match_data[0]))
                            <tennissoccerodds status_m="{{$match->status_m}}" sports_id="{{$match->sports_id}}" bet_total="{{json_encode($bet_total)}}"
                                              pinbg="{{ asset('asset/front/img/pin-bg.png') }}"
                                              pinbg1="{{ asset('asset/front/img/pin-bg-1.png') }}"
                                              pinkbg1="{{asset('asset/front/img/pinkbg1.png')}}"
                                              bluebg1="{{ asset('asset/front/img/bluebg1.png') }}"
                                              max_bet_odds_limit="{{ $oddsLimit['max_bet_odds_limit'] }}"
                                              min_bet_odds_limit="{{ $oddsLimit['min_bet_odds_limit'] }}"
                                              bar_image="{{ asset('asset/front/img/bars.png') }}"
                                              :event_id="'{{ $match->event_id }}'"></tennissoccerodds>

                            @if($match->sports_id=='4')

                                <cricketoddsbookmarks status_b="{{$match->status_b}}" sports_id="{{$match->sports_id}}" bet_total="{{json_encode($bet_total)}}"
                                                      pinbg="{{ asset('asset/front/img/pin-bg.png') }}"
                                                      pinbg1="{{ asset('asset/front/img/pin-bg-1.png') }}"
                                                      pinkbg1="{{asset('asset/front/img/pinkbg1.png')}}"
                                                      bluebg1="{{ asset('asset/front/img/bluebg1.png') }}"
                                                      min_bookmaker_limit="{{ $oddsLimit['min_bookmaker_limit'] }}"
                                                      max_bookmaker_limit="{{ $oddsLimit['max_bookmaker_limit'] }}"
                                                      bar_image="{{ asset('asset/front/img/bars.png') }}"
                                                      :event_id="'{{ $match->event_id }}'"></cricketoddsbookmarks>

                                <cricketoddsfancy status_f="{{$match->status_f}}" sports_id="{{$match->sports_id}}" bet_total="{{json_encode($bet_total)}}"
                                                  pinkbg1_fancy="{{ asset('asset/front/img/pinkbg1_fancy.png') }}"
                                                  bluebg1_fancy="{{ asset('asset/front/img/bluebg1_fancy.png') }}"
                                                  clockgreenicon="{{ asset('asset/front/img/clock-green-icon.png') }}"
                                                  infoicon="{{ asset('asset/front/img/info-icon.png') }}"
                                                  min_bet_fancy_limit="{{$oddsLimit['min_fancy_limit']}}"
                                                  max_bet_fancy_limit="{{ $oddsLimit['max_fancy_limit'] }}"
                                                  bar_image="{{ asset('asset/front/img/bars.png') }}"
                                                  :event_id="'{{ $match->event_id }}'"></cricketoddsfancy>

                            @endif
                            @else
                                <p class="text-center p-2">No data available!</p>
                            @endif
                        </div>
                        <div class="mb-5"></div>
                    </div>
                </div>
                <div class="rightblock-games white-bg first">

                    <?php

                    //check is mobile or desktop
                    $useragent = $_SERVER['HTTP_USER_AGENT'];
                    $iPod = stripos($useragent, "iPod");
                    $iPad = stripos($useragent, "iPad");
                    $iPhone = stripos($useragent, "iPhone");
                    $Android = stripos($useragent, "Android");
                    $iOS = stripos($useragent, "iOS");

                    $DEVICE = ($iPod || $iPad || $iPhone || $Android || $iOS);
                    $is_agent = '';
                    if (!$DEVICE) {
                        $is_agent = 'desktop';
                    } else {
                        $is_agent = 'mobile';
                    }
                    //end for check is mobile or desktop
                    ?>
                    @if($is_agent=='desktop')
                        @include('front/sidebarslip')
                    @endif
                    <div class="betslip-block mt-10" id="bet_display_table">
                        <a class="collape-link text-color-white blue-gradient-bg1" data-toggle="collapse"
                           href="#collapseExample1" role="button" aria-expanded="false"
                           aria-controls="collapseExample1">
                            <img src="{{ URL::to('asset/front/img/refresh-white.png')}}" class="slip_refresh" alt="">
                            Open Bets <img src="{{ URL::to('asset/front/img/minus-icon.png')}}">
                        </a>
                        <div class="collapse show" id="collapseExample1">
                            <div class="card card-body">
                                <div class="open_bets_wrap betslip_board">
                                    <div class="slip_sort">
                                        <select name="select_bet_on_match" id="select_bet_on_match"
                                                onchange="call_display_bet_list(this.value)">
                                            <option value="{{$match->event_id}}~~All">All Bet</option>
                                            @if(!empty($placed_bet_match_list))
                                                @foreach($placed_bet_match_list as $matchs)
                                                    <option
                                                        value="{{$matchs->match_id}}">{{$matchs->match_name}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <ul class="betslip_head lightblue-bg1">
                                        <li class="col-bet"><strong>Matched</strong></li>
                                    </ul>
                                    <div id="divbetlist">

                                        @php
                                            $j=0; $k=0;
                                        @endphp
                                        @foreach($my_placed_bets_all as $bet)
                                            @if($bet->bet_side=='back')
                                                @if($j==0)
                                                    <ul class="betslip_head">
                                                        <li class="col-bet bet_type_uppercase">Back (Bet For)</li>
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
                                                    <div class="col-odd text-color-blue-2 text-center">
                                                        @if(!empty($bet->bet_oddsk))
                                                            {{$bet->bet_odds}}
                                                            <br>
                                                            ({{$bet->bet_oddsk}})
                                                        @else
                                                            {{$bet->bet_odds}}
                                                        @endif

                                                    </div>
                                                    <div class="col-stake text-color-blue-2 text-center">
                                                        {{($bet->bet_amount)}}
                                                    </div>
                                                    <div class="col-profit">
                                                        @if($bet->bet_type=='ODDS')
                                                            {{($bet->bet_profit)}}
                                                        @elseif($bet->bet_type=='SESSION')
                                                            {{($bet->bet_profit)}}
                                                        @elseif($bet->bet_type=='BOOKMAKER')
                                                            {{(number_format($bet->bet_profit,2))}}
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
                                                        <li class="col-bet bet_type_uppercase">Lay (Bet Against)</li>
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
                                                    <div class="col-odd text-color-blue-2 text-center">
                                                        @if(!empty($bet->bet_oddsk))
                                                            {{$bet->bet_odds}}
                                                            <br>
                                                            ({{$bet->bet_oddsk}})
                                                        @else
                                                            {{$bet->bet_odds}}
                                                        @endif
                                                    </div>
                                                    <div class="col-stake text-color-blue-2 text-center">
                                                        {{($bet->bet_amount)}}
                                                    </div>
                                                    <div class="col-profit">
                                                        @if($bet->bet_type=='ODDS')
                                                            {{($bet->exposureAmt)}}
                                                        @elseif($bet->bet_type=='SESSION')
                                                            {{($bet->exposureAmt)}}
                                                            {{--                                                        {{$bet->bet_oddsk}}--}}
                                                        @elseif($bet->bet_type=='BOOKMAKER')
                                                            {{(number_format($bet->exposureAmt,2))}}
                                                        @endif
                                                    </div>
                                                </div>
                                                @php $k++ @endphp
                                            @endif
                                        @endforeach
                                    </div>
                                    <ul class="slip-option">
                                        <li>
                                            <input id="showBetInfo" type="checkbox"><label for="showBetInfo">Bet
                                                Info</label>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <!--end for bet display table-->
                    </div>
                </div>
            </div>
        </div>
        <div class="modal rulesfancy_betsmodal" id="rulesFancyBetsModal">
            <div class="modal-dialog">
                <div class="modal-content p-0">
                    <div class="modal-header black-bg3">
                        <h4 class="modal-title text-color-yellow1">Rules of Fancy Bets</h4>
                        <button type="button" class="close" data-dismiss="modal"><img
                                src="{{ URL::to('asset/front/img/icon-close-yellow.svg') }}" alt=""></button>
                    </div>
                    <div class="modal-body white-bg">
                        <div class="rules_fancy_bets">
                            <ol>
                                <li>Once all session/fancy bets are completed and settled there will be no reversal even
                                    if the Match is Tied or is Abandoned.
                                </li>
                                <li>Advance Session or Player Runs and all Fancy Bets are only valid for 20/50 overs
                                    full match each side. (Please Note this condition is applied only in case of Advance
                                    Fancy Bets only).
                                </li>
                                <li>All advance fancy bets market will be suspended 60 mins prior to match and will be
                                    settled.
                                </li>
                                <li>Under the rules of Session/Fancy Bets if a market gets Suspended for any reason
                                    whatsoever and does not resume then all previous Bets will remain Valid and become
                                    HAAR/JEET bets.
                                </li>
                                <li>Incomplete Session/Fancy Bet will be cancelled but Complete Session will be
                                    settled.
                                </li>
                                <li>In the case of Running Match getting Cancelled/ No Result/ Abandoned but the session
                                    is complete it will still be settled. Player runs / fall of wicket will be also
                                    settled at the figures where match gets stopped due to rain for the inning (D/L) ,
                                    cancelled , abandoned , no result.
                                </li>
                                <li>If a player gets Retired Hurt and one ball is completed after you place your bets
                                    then all the betting till then is and will remain valid.
                                </li>
                                <li>Should a Technical Glitch in Software occur, we will not be held responsible for any
                                    losses.
                                </li>
                                <li>Should there be a power failure or a problem with the Internet connection at our end
                                    and session/fancy market does not get suspended then our decision on the outcome is
                                    final.
                                </li>
                                <li>All decisions relating to settlement of wrong market being offered will be taken by
                                    management. Management will consider all actual facts and decision taken will be
                                    full in final.
                                </li>
                                <li>Any bets which are deemed of being suspicious, including bets which have been placed
                                    from the stadium or from a source at the stadium maybe voided at anytime. The
                                    decision of whether to void the particular bet in question or to void the entire
                                    market will remain at the discretion of Company. The final decision of whether bets
                                    are suspicious will be taken by Company and that decision will be full and final.
                                </li>
                                <li>Any sort of cheating bet , any sort of Matching (Passing of funds), Court Siding
                                    (Ghaobaazi on commentary), Sharpening, Commission making is not allowed in Company,
                                    If any company User is caught in any of such act then all the funds belonging that
                                    account would be seized and confiscated. No argument or claim in that context would
                                    be entertained and the decision made by company management will stand as final
                                    authority.
                                </li>
                                <li>Fluke hunting/Seeking is prohibited in Company , All the fluke bets will be
                                    reversed. Cricket commentary is just an additional feature and facility for company
                                    user but company is not responsible for any delay or mistake in commentary.
                                </li>
                                <li>Valid for only 1st inning.
                                    <ul>• Highest Inning Run :- This fancy is valid only for first inning of the
                                        match.
                                    </ul>
                                    <ul>• Lowest Inning Run :- This fancy is valid only for first inning of the match.
                                    </ul>
                                </li>
                                <li>If any fancy value gets passed, we will settle that market after that match gets
                                    over. For example :- If any market value is ( 22-24 ) and incase the result is 23
                                    than that market will be continued, but if the result is 24 or above then we will
                                    settle that market. This rule is for the following market.
                                    <ul>• Total Sixes In Single Match</ul>
                                    <ul>• Total Fours In Single Match</ul>
                                    <ul>• Highest Inning Run</ul>
                                    <ul>• Highest Over Run In Single Match</ul>
                                    <ul>• Highest Individual Score By Batsman</ul>
                                    <ul>• Highest Individual Wickets By Bowler</ul>
                                </li>
                                <li>If any fancy value gets passed, we will settle that market after that match gets
                                    over. For example :- If any market value is ( 22-24 ) and incase the result is 23
                                    than that market will be continued, but if the result is 22 or below then we will
                                    settle that market. This rule is for the following market.
                                    <ul>• Lowest Inning Run</ul>
                                    <ul>• Fastest Fifty</ul>
                                    <ul>• Fastest Century</ul>
                                </li>
                                <li>If any case wrong rate has been given in fancy ,that particular bets will be
                                    cancelled (Wrong Commentary).
                                </li>
                                <li>In case customer make bets in wrong fancy we are not liable to delete, no changes
                                    will be made and bets will be considered as confirm bet.
                                </li>
                                <li>Dot Ball Market Rules
                                    <ul>Wides Ball - Not Count</ul>
                                    <ul>No Ball - Not Count</ul>
                                    <ul>Leg Bye - Not Count as A Dot Ball</ul>
                                    <ul>Bye Run - Not Count as A Dot Ball</ul>
                                    <ul>Run Out - On 1st Run Count as A Dot Ball</ul>
                                    <ul>Run Out - On 2nd n 3rd Run Not Count as a Dot Ball</ul>
                                    <ul>Out - Catch Out, Bowled, Stumped n LBW Count as A Dot Ball</ul>
                                </li>
                                <li>Bookmaker Rules
                                    <ul>• Due to any reason any team will be getting advantage or disadvantage we are
                                        not concerned.
                                    </ul>
                                    <ul>• We will simply compare both teams 25 overs score higher score team will be
                                        declared winner in ODI.
                                    </ul>
                                    <ul>• We will simply compare both teams 10 overs higher score team will be declared
                                        winner in T20 matches.
                                    </ul>
                                </li>
                                <li>Penalty Runs - Any Penalty Runs Awarded in the Match (In Any Running Fancy or ADV
                                    Fancy) Will Not be Counted While Settling in our Exchange.
                                </li>
                                <li>LIVE STREAMING OF ALL VIRTUAL CRICKET MATCHES IS AVAILABLE HERE <a
                                        href="https://www.youtube.com/channel/UCd837ZyyiO5KAPDXibynq_Q/featured">
                                        https://www.youtube.com/channel/UCd837ZyyiO5KAPDXibynq_Q/featured</a></li>
                                <li>CHECK SCORE OF VIRTUAL CRICKET ON <a
                                        href="https://sportcenter.sir.sportradar.com/simulated-reality/cricket">
                                        https://sportcenter.sir.sportradar.com/simulated-reality/cricket</a></li>
                                <li>Comparison Market
                                    <ul>In Comparison Market We Don't Consider Tie or Equal Runs on Both the Innings
                                        While Settling . Second Batting Team Must need to Surpass 1st Batting's team
                                        Total to win otherwise on Equal Score or Below We declare 1st Batting Team as
                                        Winner .
                                    </ul>
                                </li>
                                <li>If match is abandoned or over reduced. This rule is for the following market (
                                    ENTIRE IPL 2020 )
                                    <ul>• Total Fours :- Average 27 fours will be given if the match is abandoned or
                                        over reduced.
                                    </ul>
                                    <ul>• Total Sixes :- Average 11 sixes will be given if the match is abandoned or
                                        over reduced.
                                    </ul>
                                    <ul>• Total Caught &amp; Bowled Out :- Average 0 Caught &amp; Bowled Out will be
                                        given if the match is abandoned or over reduced.
                                    </ul>
                                    <ul>• Total Wide :- Average 8 wides will be given if the match is abandoned or over
                                        reduced.
                                    </ul>
                                    <ul>• Total Extra :- Average 14 extras will be given if the match is abandoned or
                                        over reduced.
                                    </ul>
                                    <ul>• Total No Ball :- Average 1 no ball will be given if the match is abandoned or
                                        over reduced.
                                    </ul>
                                    <ul>• Total duck :- Average 1 duck will be given if the match is abandoned or over
                                        reduced.
                                    </ul>
                                    <ul>• Total Fifties :- Average 2 fifties will be given if the match is abandoned or
                                        over reduced.
                                    </ul>
                                    <ul>• Total Century :-Average 0 century will be given if the match is abandoned or
                                        over reduced.
                                    </ul>
                                    <ul>• Total Run Out :- Average 1 run out will be given if the match is abandoned or
                                        over reduced.
                                    </ul>
                                    <ul>• Total Caught out :- Average 8 caught out will be given if the match is
                                        abandoned or over reduced.
                                    </ul>
                                    <ul>• Total Stump Out :- Average 0 stump out out will be given if the match is
                                        abandoned or over reduced.
                                    </ul>
                                    <ul>• Total Maiden Over :- Average 0 maiden over will be given if the match is
                                        abandoned or over reduced.
                                    </ul>
                                    <ul>• Total LBW :- Average 1 LBW will be given if the match is abandoned or over
                                        reduced.
                                    </ul>
                                    <ul>• Total Bowled :- Average 2 bowled will be given if the match is abandoned or
                                        over reduced.
                                    </ul>
                                </li>
                                <li>Player Boundaries Fancy :- Both Four and six are valid</li>
                                <li>BOWLER RUN SESSION RULE :-
                                    <ul>IF BOWLER BOWL 1.1 OVER,THEN VALID ( FOR BOWLER 2 OVER RUNS SESSION )</ul>
                                    <ul>IF BOWLER BOWL 2.1 OVER,THEN VALID ( FOR BOWLER 3 OVER RUNS SESSION )</ul>
                                    <ul>IF BOWLER BOWL 3.1 OVER,THEN VALID ( FOR BOWLER 4 OVER RUNS SESSION )</ul>
                                    <ul>IF BOWLER BOWL 4.1 OVER,THEN VALID ( FOR BOWLER 5 OVER RUNS SESSION )</ul>
                                    <ul>IF BOWLER BOWL 9.1 OVER,THEN VALID ( FOR BOWLER 10 OVER RUNS SESSION )</ul>
                                </li>
                                <li>Total Match Playing Over ADV :- We Will Settle this Market after Whole Match gets
                                    Completed
                                    <ul>Criteria :- We Will Count Only Round- Off Over For Both the Innings While
                                        Settling (For Ex :- If 1st Batting team gets all out at 17.3 , 18.4 or 19.5 we
                                        Will Count Such Overs as 17, 18 and 19 Respectively and if Match gets Ended at
                                        17.2 , 18.3 or 19.3 Overs then we will Count that as 17 , 18 and 19 Over
                                        Respectively... and this Will Remain Same For Both the Innings ..
                                    </ul>
                                    <ul>In Case Of Rain or if Over gets Reduced then this Market will get Voided</ul>
                                </li>
                                <li>3 WKT OR MORE BY BOWLER IN MATCH ADV :-
                                    <ul>We Will Settle this Market after Whole Match gets Completed .</ul>
                                    <ul>In Case Of Rain or if Over Gets Reduced then this Market Will get Voided</ul>
                                </li>
                            </ol>
                            <button type="button" class="grey-gradient-bg1 text-color-black1 btnok btn-block"
                                    data-dismiss="modal">OK
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @if($is_agent=="mobile")
        <div class="modal fade" id="betmobileModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content abc" style="padding: 0 0rem;">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Placebet</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        @include('front/sidebarslip')
                        <div class="row">
                            <div class="col-lg-4 col-sm-4 col-4"> {{strtoupper($split[0])}} </div>
                            <div class="col-lg-4 col-sm-4 col-4 text-center"> 0</div>
                            <div class="col-lg-4 col-sm-4 col-4 text-right"> 0.00</div>
                            <div class="col-lg-4 col-sm-4 col-4">  {{strtoupper($split[1])}} </div>
                            <div class="col-lg-4 col-sm-4 col-4 text-center"> 0</div>
                            <div class="col-lg-4 col-sm-4 col-4 text-right font-green"> 0.00</div>
                            @if($match->is_draw==1)
                                <div class="col-lg-4 col-sm-4 col-4"> The Draw</div>
                                <div class="col-lg-4 col-sm-4 col-4 text-center"> 0</div>
                                <div class="col-lg-4 col-sm-4 col-4 text-right font-red"> 0.00</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="betConfirmationForMobileModal" tabindex="-1" role="dialog"
             aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content abc" style="padding: 0 0rem;">
                    <div class="modal-header">
                        <h6 class="modal-title">Please confirm your bet</h6>
                        {{--        <button type="button" class="close" data-dismiss="modal" aria-label="Close">--}}
                        {{--          <span aria-hidden="true">&times;</span>--}}
                        {{--        </button>--}}
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-3 pr-0">
                                <p class="odds-bet-type">
                                    <a class="btn blue-dark back collapse">Back</a>
                                    <a class="btn pink-bg lay">Lay</a>
                                </p>
                            </div>
                            <div class="col-8">
                                <p class="odds-title p-1">Title</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4 col-sm-4 col-4 odds border p-2">
                                <span class="title w-100 d-block">Odds</span>
                                <span class="value w-100 d-block font-weight-bold">0.00</span>
                            </div>
                            <div class="col-lg-4 col-sm-4 col-4 stake border p-2">
                                <span class="title w-100 d-block">Stake</span>
                                <span class="value w-100 d-block font-weight-bold">0.00</span>
                            </div>
                            <div class="col-lg-4 col-sm-4 col-4 profit border p-2">
                                <span class="title w-100 d-block">Profit</span>
                                <span class="value w-100 d-block font-weight-bold">0.00</span>
                            </div>
                        </div>
                        {{--      </div>--}}
                        {{--        <div class="modal-footer">--}}
                        <div class="row mt-3">
                            <div class="col-6 text-left">
                                <button type="button" class="add_player grey-gradient-bg w-100" data-dismiss="modal"
                                        aria-label="Close">
                                    <span aria-hidden="true">Cancel</span>
                                </button>
                                {{--                    <a class="add_player grey-gradient-bg w-100" onclick="cancleBetConfirm()">Cancel</a>--}}
                            </div>
                            <div class="col-6 text-right">
                                <a class="submit-btn text-color-yellow w-100" onclick="saveBetcall(true)"
                                   style="cursor:pointer">Confirm</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <?php /* for mobile view bet box for odds*/ ?>
    <div id="mobile_invisible_div" style="display:none">
        <div class="keyboard-wrape">
            <div class="row">
                <div class="col-6 pl-0">
                    <div class="qty qty1 mt-3">
                        <span class="minusodds clsplusminusodds">-</span>
                        <input type="text" class="count count2" readonly="readonly" name="mobile_odds" id="mobile_odds"
                               value="" required="required">
                        <input type="text" class="count2" style="display: none;width: 100%;" readonly="readonly"
                               name="mobile_odds_display" id="mobile_odds_display" value="" required="required">
                        <span class="plusodds clsplusminusodds">+</span>
                    </div>
                </div>
                <div class="col-6 pr-0 pl-0">
                    <div class="qty mt-3">
                        <span class="minus clsplusminus">-</span><input type="text" step="1" class="count1"
                                                                        name="inputStake_mobile" id="inputStake_mobile"
                                                                        value="" maxlength="7" tabindex="1"
                                                                        onkeyup="getCalculated(this.value)"
                                                                        required="required"
                                                                        onkeypress="return isNumber(event)"><span
                            class="plus clsplusminus">+</span>
                    </div>
                </div>
            </div>
            <div class="row mbt-10">
                @php
                    $i=1;
                @endphp
                @foreach($stkval as $data1)
                    <div data-odd="{{$data1}}" data-val="{{$data1}}" id="selectStakeMobile_{{$i}}"
                         class="col-2 mobileodds_detail match_odd_mobile"
                         style="color: #fff;line-height: 2.46;background-image: linear-gradient(-180deg, #32617f 20%, #1f4258 91%); text-align:center;border-right: 1px solid rgba(255, 255, 255, 0.15); ">
                        {{$data1}}
                    </div>
                    @php $i++; @endphp
                @endforeach
            </div>
            <div class="row">
                <div id="keyboard" class="keyboard-wrap">
                    <ul id="numPad" class="btn-tel">
                        <li><a data-val="1" class="dynamicoddsval match_odd_mobile">1</a></li>
                        <li><a data-val="2" class="dynamicoddsval match_odd_mobile">2</a></li>
                        <li><a data-val="3" class="dynamicoddsval match_odd_mobile">3</a></li>
                        <li><a data-val="4" class="dynamicoddsval match_odd_mobile">4</a></li>
                        <li><a data-val="5" class="dynamicoddsval match_odd_mobile">5</a></li>
                        <li><a data-val="6" class="dynamicoddsval match_odd_mobile">6</a></li>
                        <li><a data-val="7" class="dynamicoddsval match_odd_mobile">7</a></li>
                        <li><a data-val="8" class="dynamicoddsval match_odd_mobile">8</a></li>
                        <li><a data-val="9" class="dynamicoddsval match_odd_mobile">9</a></li>
                        <li><a data-val="0" class="dynamicoddsval match_odd_mobile">0</a></li>
                        <li><a data-val="00" class="dynamicoddsval match_odd_mobile">00</a></li>
                        <li><a style="pointer-events: none" class="dynamicoddsval match_odd_mobile">.</a></li>
                        <?php /*?><li><a data-val="." class="dynamicoddsval match_odd_mobile">.</a></li><?php */?>
                    </ul>
                    <a id="delete" class="btn-delete hide_mobile_bet_model"> <img
                            src="{{asset('asset/front/img/delete.png')}}"/> </a>
                </div>
            </div>
            <div class="row">
                <div class="col-6 text-left">
                    <a class="add_player grey-gradient-bg" id="cancel_bet_form">Cancel All</a>
                </div>
                <div class="col-6 text-right">
                    <a class="submit-btn text-color-yellow" onclick="saveBetcall(false)" style="cursor:pointer">Place
                        Bet</a>
                </div>
            </div>
            <div class="row"></div>
        </div>
    </div>

    <?php /* end for mobile view bet box for odds */ ?>

    <?php /* for mobile alert message*/ ?>
    <div id="success_mobile_message" style="display:none">
        <div class="success_alertmessage"></div>
    </div>
    <div id="fail_mobile_message" style="display:none">
        <div class="fail_alertmessage"></div>
    </div>
    <?php /* end for mobile alert message*/ ?>

    <div id="all_fancy_model">
        <div class="modal credit-modal" id="runPosition">
            <div class="modal-dialog">
                <div class="modal-content light-grey-bg-1">
                    <div class="modal-header">
                        <h4 class="modal-title text-color-blue-1">Run Position</h4>
                        <button type="button" class="close modelclose" data-dismiss="modal"><img
                                src="{{ asset('asset/front/img/close-icon.png') }}" alt=""></button>
                    </div>
                    <div class="modal-body white-bg p-3">
                        <table class="table table-bordered w-100 fonts-1 mb-0">
                            <thead>
                            <tr>
                                <th width="50%" class="text-center">Run</th>
                                <th width="50%" class="text-right">Amount</th>
                            </tr>
                            </thead>
                            <tbody class="position"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" id="opened_fancy_model_id" value=""/>
    <input type="hidden" name="_token" id="_token" value="{!! csrf_token() !!}">
    <input type="hidden" id="session_position" value=""/>
    <input type="hidden" id="session_mobile_val_position" value=""/>
    <input type="hidden" id="session_mobile_odds_position" value=""/>
    <input type="hidden" id="all_session_total" value=""/>
    <input type="hidden" id="is_session_open_position" value=""/>

    @include('layouts.footer')
@endsection

@push('third_party_scripts')
    <script src="{{ asset('js/laravel-echo-server2.js') }}"></script>
    <script src="{{ asset('js/app.js') }}?v={{$vue_app_version}}"></script>
@endpush

@push('page_scripts')
    <script>
        function isNumber(evt) {
            evt = (evt) ? evt : window.event;
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                return false;
            }
            return true;
        }

        $("#cancel_bet_form").click(function () {
            console.log('cancel1');
            $(".showForm").hide();
            // odds
            $('#team1_bet_count_new').hide();
            $('#team2_bet_count_new').hide();
            $('#draw_bet_count_new').hide();
            // bookmaker
            $('#team1_betBM_count_new').hide();
            $('#team2_betBM_count_new').hide();
            $('#draw_betBM_count_new').hide();

            $('#team1_bet_count_new').text('');
            $('#team1_bet_count_new').hide();
            $('#team2_bet_count_new').text('');
            $('#team2_bet_count_new').hide();
            $('#draw_bet_count_new').text('');
            $('#draw_bet_count_new').hide();

            $('#session_mobile_val_position').val(' ');
            $('#session_position').val(' ');
            $('#is_session_open_position').val(' ');
            $('#session_mobile_odds_position').val(' ');
            $('#inputStake_mobile').val(0);

            //$(".mobile_bet_model_div").hide();

        });

        $(".remove_new_bet_fancy").click(function () {
            $("#inputStake").val('');
        });

        function get_oddstable() {
            var _token = $("input[name='_token']").val();
            var match_type = '{{$match->sports_id}}';
            var matchid = '{{$match->match_id}}';
            var matchname = '{{$match->match_name}}';
            var event_id = '{{$match->event_id}}';
            var match_id = '{{$match->id}}';
            var match_m = '{{$match->suspend_m}}';
            var match_b = '{{$match->suspend_b}}';
            var match_f = '{{$match->suspend_f}}';
            $.ajax({
                type: "POST",
                url: '{{route("matchCallOdds",$match->match_id)}}',
                data: {
                    _token: _token,
                    matchtype: match_type,
                    event_id: event_id,
                    matchname: matchname,
                    matchid: matchid,
                    match_m: match_m,
                    match_id: match_id,
                },
                timeout: 10000,
                success: function (data) {
                    console.log(data);
                    if (data != 'No data found.')
                        $("#inplay-tableblock").html(data);
                }
            });
        }

        function bet_Fancytable() {
            var _token = $("input[name='_token']").val();
            var match_type = '{{$match->sports_id}}';
            var matchid = '{{$match->match_id}}';
            var matchname = '{{$match->match_name}}';
            var event_id = '{{$match->event_id}}';
            var match_id = '{{$match->id}}';
            $.ajax({
                type: "POST",
                url: '{{route("matchCallFor_FANCY",$match->match_id)}}',
                data: {
                    _token: _token,
                    matchtype: match_type,
                    event_id: event_id,
                    match_b: '{{$match->suspend_b}}',
                    match_f: '{{$match->suspend_f}}',
                    match_id: match_id
                },
                timeout: 10000,
                success: function (data) {
                    if (data != '') {
                        var spl = data.split('#######');
                        $('#fancybetdiv').show();
                        $("#inplay-tableblock-fancy").html(spl[0]);
                        $("#all_fancy_model").html(spl[1]);
                        //$("#inplay-tableblock-fancy").html(data);
                    }
                }
            });
        }

        function get_BMtable() {
            var _token = $("input[name='_token']").val();
            var match_type = '{{$match->sports_id}}';
            var matchid = '{{$match->match_id}}';
            var matchname = '{{$match->match_name}}';
            var event_id = '{{$match->event_id}}';
            var match_id = '{{$match->id}}';
            $.ajax({
                type: "POST",
                url: '{{route("matchCallFor_BM",$match->match_id)}}',
                data: {
                    _token: _token,
                    matchtype: match_type,
                    event_id: event_id,
                    match_f: '{{$match->suspend_f}}',
                    match_b: '{{$match->suspend_b}}',
                    match_id: match_id
                },
                timeout: 10000,
                success: function (data) {
                    if (data != '') {
                        $("#inplay-tableblock-bookmaker").html(data);
                    }
                }
            });
        }

        function matchDeclareRedirect() {
            var match_id = '{{$match->id}}';
            var _token = $("input[name='_token']").val();
            $.ajax({
                type: "POST",
                url: '{{route("matchDeclareRedirect")}}',
                data: {
                    _token: _token,
                    match_id: match_id,
                },
                success: function (data) {
                    //alert(data);
                    if (data.result == 'error') {
                        window.location.href = "{{ route('front')}}";
                    }
                }

            });
        }

        function loadDetailPageContent() {
            var fancystack = $('#inputStake_mobile').val();

            var _token = $("input[name='_token']").val();
            var match_type = '{{$match->sports_id}}';
            var matchid = '{{$match->match_id}}';
            var matchname = '{{$match->match_name}}';
            var event_id = '{{$match->event_id}}';
            var match_m = '{{$match->suspend_m}}';
            var match_b = '{{$match->suspend_b}}';
            var match_f = '{{$match->suspend_f}}';
            var match_id = '{{$match->id}}';
            $.ajax({
                type: "POST",
                url: '{{route("matchCall",$match->match_id)}}',
                data: {
                    _token: _token,
                    matchtype: match_type,
                    event_id: event_id,
                    matchname: matchname,
                    matchid: matchid,
                    match_m: match_m,
                    match_id: match_id,
                },
                timeout: 1000,
                success: function (data) {
                    var width = $(window).width();
                    if (width < 990) {
                        if ($('#betTypeAdd').val() == 'ODDS') {
                            //$('#inputStake_mobile').val(fancystack);
                            $('#mobile_odds').css("width", "40%");
                        }
                    }
                    if (data == 'inactive') {
                        window.location = "/";
                    } else {
                        var alldata = data.split('@@@@');
                        var team1total = '';
                        var team2total = '';
                        var team3total = '';
                        var allteamtotal = alldata[1].split('---');

                        var team1total = allteamtotal[0];
                        var team2total = allteamtotal[1];

                        var main = alldata[0].split('===');

                        if ($('#team1_total').text() != team1total && team1total != '') {
                            var newVal = parseFloat(team1total).toFixed(2);
                            $('#team1_total').text(newVal);
                            if (newVal < 0) {
                                $("#team1_bet_count_old").removeClass('towin');
                                $("#team1_bet_count_old").removeClass('text-color-green');
                                $("#team1_bet_count_old").addClass('lose');
                                $("#team1_bet_count_old").addClass('text-color-red');
                            } else {
                                $("#team1_bet_count_old").removeClass('lose');
                                $("#team1_bet_count_old").removeClass('text-color-red');
                                $("#team1_bet_count_old").addClass('towin');
                                $("#team1_bet_count_old").addClass('text-color-green');
                            }
                        }
                        if ($('#team2_total').text() != team2total && team2total != '') {
                            var newVal = parseFloat(team2total).toFixed(2);
                            $('#team2_total').text(newVal);
                            if (newVal < 0) {
                                $("#team2_bet_count_old").removeClass('towin');
                                $("#team2_bet_count_old").removeClass('text-color-green');
                                $("#team2_bet_count_old").addClass('lose');
                                $("#team2_bet_count_old").addClass('text-color-red');
                            } else {
                                $("#team2_bet_count_old").removeClass('lose');
                                $("#team2_bet_count_old").removeClass('text-color-red');
                                $("#team2_bet_count_old").addClass('towin');
                                $("#team2_bet_count_old").addClass('text-color-green');
                            }
                        }
                        if ($('.totselection').text() == '3 Selections') {
                            var team3total = allteamtotal[2];
                            console.log("team3total: ", team3total);
                            if ($('#draw_total').text() != team3total && team3total != '') {
                                var newVal = parseFloat(team3total).toFixed(2);

                                $('#draw_total').text(newVal);

                                if (newVal < 0) {
                                    $("#draw_bet_count_old").removeClass('towin');
                                    $("#draw_bet_count_old").removeClass('text-color-green');
                                    $("#draw_bet_count_old").addClass('lose');
                                    $("#draw_bet_count_old").addClass('text-color-red');
                                } else {
                                    $("#draw_bet_count_old").removeClass('lose');
                                    $("#draw_bet_count_old").removeClass('text-color-red');
                                    $("#draw_bet_count_old").addClass('towin');
                                    $("#draw_bet_count_old").addClass('text-color-green');
                                }
                            }
                        }

                        for (var i = 0; i < main.length; i++) {
                            if (main[i] != '') {
                                var sub_ = main[i].split('***');
                                if (i == 0) {
                                    if (sub_[1]) {

                                        if ($('.tr_team1').prevAll(".team1_fancy:first")) {
                                            $('.team1_fancy').remove();
                                            $(".tr_team1").before('<tr class="fancy-suspend-tr team1_fancy"><td></td><td class="fancy-suspend-td" colspan="6"><div class="fancy-suspend black-bg-5 text-color-white"><span>SUSPENDED</span></div></td></tr>');
                                        } else {
                                            $(".tr_team1").before(sub_[1]);
                                        }

                                        if (sub_[0]) {
                                            var sub_sub = sub_[0].split('~');
                                            $('.td_team1_back_2').html(sub_sub[0]);
                                            $('.td_team1_back_1').html(sub_sub[1]);
                                            $('.td_team1_back_0').html(sub_sub[2]);
                                            $('.td_team1_lay_0').html(sub_sub[3]);
                                            $('.td_team1_lay_1').html(sub_sub[4]);
                                            $('.td_team1_lay_2').html(sub_sub[5]);
                                        }
                                    } else {
                                        if (sub_[0]) {
                                            $('.team1_fancy').remove();
                                            var sub_sub = sub_[0].split('~');
                                            $('.td_team1_back_2').addClass("spark");
                                            $('.td_team1_back_2').html(sub_sub[0]);

                                            $('.td_team1_back_1').addClass("spark");
                                            $('.td_team1_back_1').html(sub_sub[1]);

                                            $('.td_team1_back_0').addClass("spark");
                                            $('.td_team1_back_0').html(sub_sub[2]);

                                            $('.td_team1_lay_0').addClass("sparkLay");
                                            $('.td_team1_lay_0').html(sub_sub[3]);

                                            $('.td_team1_lay_1').addClass("sparkLay");
                                            $('.td_team1_lay_1').html(sub_sub[4]);

                                            $('.td_team1_lay_2').addClass("sparkLay");
                                            $('.td_team1_lay_2').html(sub_sub[5]);
                                        }
                                    }
                                } else if (i == 1) {
                                    if (sub_[1]) {
                                        if ($('.tr_team2').prevAll(".team2_fancy:first")) {
                                            $('.team2_fancy').remove();
                                            $(".tr_team2").before('<tr class="fancy-suspend-tr team2_fancy"><td></td><td class="fancy-suspend-td" colspan="6"><div class="fancy-suspend black-bg-5 text-color-white"><span>SUSPENDED</span></div></td></tr>');
                                        } else {
                                            $(".tr_team2").before(sub_[1]);
                                        }

                                        if (sub_[0]) {
                                            var sub_sub = sub_[0].split('~');

                                            $('.td_team2_back_2').html(sub_sub[0]);
                                            $('.td_team2_back_1').html(sub_sub[1]);
                                            $('.td_team2_back_0').html(sub_sub[2]);
                                            $('.td_team2_lay_0').html(sub_sub[3]);
                                            $('.td_team2_lay_1').html(sub_sub[4]);
                                            $('.td_team2_lay_2').html(sub_sub[5]);
                                        }
                                    } else {
                                        if (sub_[0]) {
                                            $('.team2_fancy').remove();
                                            var sub_sub = sub_[0].split('~');

                                            $('.td_team2_back_2').addClass("spark");
                                            $('.td_team2_back_2').html(sub_sub[0]);

                                            $('.td_team2_back_1').addClass("spark");
                                            $('.td_team2_back_1').html(sub_sub[1]);

                                            $('.td_team2_back_0').addClass("spark");
                                            $('.td_team2_back_0').html(sub_sub[2]);

                                            $('.td_team2_lay_0').addClass("sparkLay");
                                            $('.td_team2_lay_0').html(sub_sub[3]);

                                            $('.td_team2_lay_1').addClass("sparkLay");
                                            $('.td_team2_lay_1').html(sub_sub[4]);

                                            $('.td_team2_lay_2').addClass("sparkLay");
                                            $('.td_team2_lay_2').html(sub_sub[5]);
                                        }
                                    }
                                } else if (i == 2) {
                                    if (sub_[1]) {
                                        if ($('.tr_team3').prevAll(".team3_fancy:first")) {
                                            $('.team3_fancy').remove();
                                            $(".tr_team3").before('<tr class="fancy-suspend-tr team3_fancy"><td></td><td class="fancy-suspend-td" colspan="6"><div class="fancy-suspend black-bg-5 text-color-white"><span>SUSPENDED</span></div></td></tr>');
                                        } else {
                                            $(".tr_team3").before(sub_[1]);
                                        }
                                        if (sub_[0]) {
                                            var sub_sub = sub_[0].split('~');

                                            $('.td_team3_back_2').html(sub_sub[0]);
                                            $('.td_team3_back_1').html(sub_sub[1]);
                                            $('.td_team3_back_0').html(sub_sub[2]);
                                            $('.td_team3_lay_0').html(sub_sub[3]);
                                            $('.td_team3_lay_1').html(sub_sub[4]);
                                            $('.td_team3_lay_2').html(sub_sub[5]);
                                        }
                                    } else {
                                        if (sub_[0]) {
                                            $('.team3_fancy').remove();
                                            var sub_sub = sub_[0].split('~');

                                            $('.td_team3_back_2').addClass("spark");
                                            $('.td_team3_back_2').html(sub_sub[0]);

                                            $('.td_team3_back_1').addClass("spark");
                                            $('.td_team3_back_1').html(sub_sub[1]);

                                            $('.td_team3_back_0').addClass("spark");
                                            $('.td_team3_back_0').html(sub_sub[2]);

                                            $('.td_team3_lay_0').addClass("sparkLay");
                                            $('.td_team3_lay_0').html(sub_sub[3]);

                                            $('.td_team3_lay_1').addClass("sparkLay");
                                            $('.td_team3_lay_1').html(sub_sub[4]);

                                            $('.td_team3_lay_2').addClass("sparkLay");
                                            $('.td_team3_lay_2').html(sub_sub[5]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            });

            if (match_type == 4) {
                //for fancy only
                // console.log('chk-'+$('#is_session_open_position').val());
                if ($('#is_session_open_position').val() == '' || $('#is_session_open_position').val() == ' ') {
                    $.ajax({
                        type: "POST",
                        url: '{{route("matchCallForFancyOnly",$match->match_id)}}',
                        data: {
                            _token: _token,
                            matchtype: match_type,
                            event_id: event_id,
                            matchname: matchname,
                            matchid: matchid,
                            match_b: match_b,
                            match_f: match_f,
                            match_id: match_id
                        },
                        timeout: 10000,
                        success: function (data) {
                            if (data != '') {
                                var spl = data.split('#######');
                                $('#fancybetdiv').show();
                                $("#inplay-tableblock-fancy").html(spl[0]);
                                var mid = $('#opened_fancy_model_id').val();
                                if (mid != '')
                                    $(mid).modal('hide');
                                $("#all_fancy_model").html(spl[1]);
                                if (mid != '')
                                    $(mid).modal('show');
                                var width = $(window).width();
                                if (width < 990) {
                                    var div = $('#mobile_invisible_div').html();
                                    var posi = $('#session_mobile_val_position').val();
                                    $('.tr_team' + posi + '_fancy').show();
                                    $('.tr_team' + posi + '_fancy_td_mobile').show();
                                    $('.tr_team' + posi + '_fancy_td_mobile').html(div);
                                    // console.log('odds'+$('#mobile_odds').val());
                                    //$('#session_mobile_odds_position').val(value);

                                    if ($('#betTypeAdd').val() == 'SESSION') {
                                        var volume = $("#odds_volume").val();
                                        $('#mobile_odds_display').val($('#session_mobile_odds_position').val() + "/" + volume);
                                        $('#mobile_odds').hide();
                                        $('#mobile_odds_display').show();

                                        $('#mobile_odds').css("width", "100%");
                                        $('#inputStake_mobile').val(fancystack);
                                    }
                                    /*if($('#session_mobile_val_position').val()!='')
                                        {
                                            var pos=$('#session_mobile_val_position').val();
                                            console.log('aaaaa'+pos);
                                            var valall='mobileBack tr_team'+pos+'_fancy mobile_bet_model_div';
                                    $('html, body').animate({
                                                scrollTop: $(".tr_team4_fancy").offset().top
                                            },500);
                                        }*/
                                    // $('.count').val($('#session_mobile_odds_position').val());
                                }
                            }
                        }
                    });
                }

                //for bookmaker
                var fancy_row = $('#hid_fancy').val();
                $.ajax({
                    type: "POST",
                    url: '{{route("matchCallForFancyNBM",$match->match_id)}}',
                    data: {
                        _token: _token,
                        matchtype: match_type,
                        event_id: event_id,
                        matchname: matchname,
                        matchid: matchid,
                        match_b: match_b,
                        match_f: match_f,
                        match_id: match_id,
                        fancy_row: fancy_row
                    },
                    timeout: 1000,
                    success: function (data) {
                        var width = $(window).width();
                        if (width < 990) {
                            if ($('#betTypeAdd').val() == 'BOOKMAKER') {
                                //$('#inputStake_mobile').val(fancystack);
                                $('#mobile_odds').css("width", "100%");
                            }
                        }
                        var main_main = data.split('####');
                        var main = main_main[0].split('===');
                        for (var i = 0; i < main.length; i++) {
                            if (main[i] != '') {
                                var sub_ = main[i].split('***');
                                if (i == 0) {
                                    if (sub_[1]) {

                                        //$(".tr_bm_team1").before(sub_[1]);
                                        $(".tr_bm_team1").remove(sub_[1]);
                                        if (sub_[0]) {
                                            var sub_sub = sub_[0].split('~');
                                            if (sub_sub[0] == "" && sub_sub[1] == "" && sub_sub[2] == "" && sub_sub[3] == "" && sub_sub[4] == "" && sub_sub[5] == "") {
                                                console.log('ccc');
                                                if ($('.tr_bm_team1').prevAll(".team1_bm_fancy:first")) {
                                                    $('.team1_bm_fancy').remove();
                                                    $(".tr_bm_team1").before('<tr class="fancy-suspend-tr team1_bm_fancy"> <td></td> <td class="fancy-suspend-td" colspan="6"> <div class="fancy-suspend black-bg-5 text-color-white"><span>SUSPENDED</span></div> </td> </tr>');
                                                } else {
                                                    $(".tr_bm_team1").before('<tr class="fancy-suspend-tr team1_bm_fancy"> <td></td> <td class="fancy-suspend-td" colspan="6"> <div class="fancy-suspend black-bg-5 text-color-white"><span>SUSPENDED</span></div> </td> </tr>');
                                                }

                                                $('.td_team1_bm_back_2').html('<div class="back-gradient text-color-black"><div id="back_3" class="light-blue-bg-2"><a>  </a></div></div>');
                                                $('.td_team1_bm_back_1').html('<div class="back-gradient text-color-black"> <div id="back_2" class="light-blue-bg-3"> <a>  </a> </div> </div>');
                                                $('.td_team1_bm_back_0').html('<div class="back-gradient text-color-black"> <div id="back_1"><a class="cyan-bg">  </a></div> </div>');
                                                $('.td_team1_bm_lay_0').html('<div class="lay-gradient text-color-black"> <div id="lay_1"><a class="pink-bg">  </a></div> </div>');
                                                $('.td_team1_bm_lay_1').html('<div class="lay-gradient text-color-black"><div id="lay_2" class="light-pink-bg-2"> <a>  </a> </div> </div>');
                                                $('.td_team1_bm_lay_2').html('<div class="lay-gradient text-color-black"><div id="lay_3" class="light-pink-bg-3"><a>  </a></div></div>');
                                            } else {
                                                $('.team1_bm_fancy').remove();
                                                $('.td_team1_bm_back_2').html(sub_sub[0]);
                                                $('.td_team1_bm_back_1').html(sub_sub[1]);
                                                $('.td_team1_bm_back_0').html(sub_sub[2]);
                                                $('.td_team1_bm_lay_0').html(sub_sub[3]);
                                                $('.td_team1_bm_lay_1').html(sub_sub[4]);
                                                $('.td_team1_bm_lay_2').html(sub_sub[5]);
                                            }
                                        }

                                    } else {
                                        if (sub_[0]) {

                                            if (sub_[0] != 'SUSPENDED') {

                                                $('.team1_bm_fancy').before();
                                                var sub_sub = sub_[0].split('~');


                                                if ((typeof sub_sub[0].trim() === 'undefined' || sub_sub[0].trim() == "SUSPENDED" || sub_sub[0] == "")) {
                                                    if ($('.tr_bm_team1').prevAll(".team1_bm_fancy:first")) {
                                                        $('.team1_bm_fancy').remove();
                                                        $(".tr_bm_team1").before('<tr class="fancy-suspend-tr team1_bm_fancy"> <td></td> <td class="fancy-suspend-td" colspan="6"> <div class="fancy-suspend black-bg-5 text-color-white"><span>SUSPENDED</span></div> </td> </tr>');
                                                    } else {
                                                        $(".tr_bm_team1").before('<tr class="fancy-suspend-tr team1_bm_fancy"> <td></td> <td class="fancy-suspend-td" colspan="6"> <div class="fancy-suspend black-bg-5 text-color-white"><span>SUSPENDED</span></div> </td> </tr>');
                                                    }

                                                    $('.td_team1_bm_back_2').html('<div class="back-gradient text-color-black"><div id="back_3" class="light-blue-bg-2"><a>  </a></div></div>');
                                                    $('.td_team1_bm_back_1').html('<div class="back-gradient text-color-black"> <div id="back_2" class="light-blue-bg-3"> <a>  </a> </div> </div>');
                                                    $('.td_team1_bm_back_0').html('<div class="back-gradient text-color-black"> <div id="back_1"><a class="cyan-bg">  </a></div> </div>');
                                                    $('.td_team1_bm_lay_0').html('<div class="lay-gradient text-color-black"> <div id="lay_1"><a class="pink-bg">  </a></div> </div>');
                                                    $('.td_team1_bm_lay_1').html('<div class="lay-gradient text-color-black"><div id="lay_2" class="light-pink-bg-2"> <a>  </a> </div> </div>');
                                                    $('.td_team1_bm_lay_2').html('<div class="lay-gradient text-color-black"><div id="lay_3" class="light-pink-bg-3"><a>  </a></div></div>');


                                                } else {
                                                    $('.team1_bm_fancy').remove();

                                                    $('.td_team1_bm_back_2').addClass("spark");
                                                    $('.td_team1_bm_back_2').html(sub_sub[0]);

                                                    $('.td_team1_bm_back_1').addClass("spark");
                                                    $('.td_team1_bm_back_1').html(sub_sub[1]);

                                                    $('.td_team1_bm_back_0').addClass("spark");
                                                    $('.td_team1_bm_back_0').html(sub_sub[2]);

                                                    $('.td_team1_bm_lay_0').addClass("sparkLay");
                                                    $('.td_team1_bm_lay_0').html(sub_sub[3]);

                                                    $('.td_team1_bm_lay_1').addClass("sparkLay");
                                                    $('.td_team1_bm_lay_1').html(sub_sub[4]);

                                                    $('.td_team1_bm_lay_2').addClass("sparkLay");
                                                    $('.td_team1_bm_lay_2').html(sub_sub[5]);
                                                }
                                            } else {
                                                if ($('.tr_bm_team1').prevAll(".team1_bm_fancy:first")) {
                                                    $('.team1_bm_fancy').remove();
                                                    $(".tr_bm_team1").before('<tr class="fancy-suspend-tr team1_bm_fancy"> <td></td> <td class="fancy-suspend-td" colspan="6"> <div class="fancy-suspend black-bg-5 text-color-white"><span>SUSPENDED</span></div> </td> </tr>');
                                                } else {
                                                    $(".tr_bm_team1").before('<tr class="fancy-suspend-tr team1_bm_fancy"> <td></td> <td class="fancy-suspend-td" colspan="6"> <div class="fancy-suspend black-bg-5 text-color-white"><span>SUSPENDED</span></div> </td> </tr>');
                                                }
                                                $('.td_team1_bm_back_2').html('<div class="back-gradient text-color-black"><div id="back_3" class="light-blue-bg-2"><a>  </a></div></div>');
                                                $('.td_team1_bm_back_1').html('<div class="back-gradient text-color-black"> <div id="back_2" class="light-blue-bg-3"> <a>  </a> </div> </div>');
                                                $('.td_team1_bm_back_0').html('<div class="back-gradient text-color-black"> <div id="back_1"><a class="cyan-bg">  </a></div> </div>');
                                                $('.td_team1_bm_lay_0').html('<div class="lay-gradient text-color-black"> <div id="lay_1"><a class="pink-bg">  </a></div> </div>');
                                                $('.td_team1_bm_lay_1').html('<div class="lay-gradient text-color-black"><div id="lay_2" class="light-pink-bg-2"> <a>  </a> </div> </div>');
                                                $('.td_team1_bm_lay_2').html('<div class="lay-gradient text-color-black"><div id="lay_3" class="light-pink-bg-3"><a>  </a></div></div>');
                                            }
                                        }
                                    }
                                } else if (i == 1) {
                                    if (sub_[1]) {
                                        $(".tr_bm_team2").before(sub_[1]);
                                        if (sub_[0]) {
                                            var sub_sub = sub_[0].split('~');
                                            //if(sub_sub[0]=="" && sub_sub[1]=="" && sub_sub[2]=="" && sub_sub[3]=="" && sub_sub[4]=="" && sub_sub[5]=="")
                                            if ((typeof sub_sub[0].trim() === 'undefined' || sub_sub[0].trim() == "SUSPENDED" || sub_sub[0] == "")) {
                                                if ($('.tr_bm_team2').prevAll(".team2_bm_fancy:first")) {
                                                    $('.team2_bm_fancy').remove();
                                                    $(".tr_bm_team2").before('<tr class="fancy-suspend-tr team2_bm_fancy"> <td></td> <td class="fancy-suspend-td" colspan="6"> <div class="fancy-suspend black-bg-5 text-color-white"><span>SUSPENDED</span></div> </td> </tr>');
                                                } else {
                                                    $(".tr_bm_team2").before('<tr class="fancy-suspend-tr team2_bm_fancy"> <td></td> <td class="fancy-suspend-td" colspan="6"> <div class="fancy-suspend black-bg-5 text-color-white"><span>SUSPENDED</span></div> </td> </tr>');
                                                }
                                                $('.td_team2_bm_back_2').html('<div class="back-gradient text-color-black"><div id="back_3" class="light-blue-bg-2"><a>  </a></div></div>');
                                                $('.td_team2_bm_back_1').html('<div class="back-gradient text-color-black"> <div id="back_2" class="light-blue-bg-3"> <a>  </a> </div> </div>');
                                                $('.td_team2_bm_back_0').html('<div class="back-gradient text-color-black"> <div id="back_1"><a class="cyan-bg">  </a></div> </div>');
                                                $('.td_team2_bm_lay_0').html('<div class="lay-gradient text-color-black"> <div id="lay_1"><a class="pink-bg">  </a></div> </div>');
                                                $('.td_team2_bm_lay_1').html('<div class="lay-gradient text-color-black"><div id="lay_2" class="light-pink-bg-2"> <a>  </a> </div> </div>');
                                                $('.td_team2_bm_lay_2').html('<div class="lay-gradient text-color-black"><div id="lay_3" class="light-pink-bg-3"><a>  </a></div></div>');
                                            } else {
                                                $('.team2_bm_fancy').remove();
                                                $('.td_team2_bm_back_2').html(sub_sub[0]);
                                                $('.td_team2_bm_back_1').html(sub_sub[1]);
                                                $('.td_team2_bm_back_0').html(sub_sub[2]);
                                                $('.td_team2_bm_lay_0').html(sub_sub[3]);
                                                $('.td_team2_bm_lay_1').html(sub_sub[4]);
                                                $('.td_team2_bm_lay_2').html(sub_sub[5]);
                                            }
                                        }
                                    } else {
                                        if (sub_[0]) {
                                            if (sub_[0] != 'SUSPENDED') {
                                                $('.team2_bm_fancy').remove();
                                                var sub_sub = sub_[0].split('~');

                                                //if(sub_sub[0]=="" && sub_sub[1]=="" && sub_sub[2]=="" && sub_sub[3]=="" && sub_sub[4]=="" && sub_sub[5]=="")
                                                if ((typeof sub_sub[0].trim() === 'undefined' || sub_sub[0].trim() == "SUSPENDED" || sub_sub[0] == "")) {
                                                    if ($('.tr_bm_team2').prevAll(".team2_bm_fancy:first")) {
                                                        $('.team2_bm_fancy').remove();
                                                        $(".tr_bm_team2").before('<tr class="fancy-suspend-tr team2_bm_fancy"> <td></td> <td class="fancy-suspend-td" colspan="6"> <div class="fancy-suspend black-bg-5 text-color-white"><span>SUSPENDED</span></div> </td> </tr>');
                                                    } else {
                                                        $(".tr_bm_team2").before('<tr class="fancy-suspend-tr team2_bm_fancy"> <td></td> <td class="fancy-suspend-td" colspan="6"> <div class="fancy-suspend black-bg-5 text-color-white"><span>SUSPENDED</span></div> </td> </tr>');
                                                    }
                                                    $('.td_team2_bm_back_2').html('<div class="back-gradient text-color-black"><div id="back_3" class="light-blue-bg-2"><a>  </a></div></div>');
                                                    $('.td_team2_bm_back_1').html('<div class="back-gradient text-color-black"> <div id="back_2" class="light-blue-bg-3"> <a>  </a> </div> </div>');
                                                    $('.td_team2_bm_back_0').html('<div class="back-gradient text-color-black"> <div id="back_1"><a class="cyan-bg">  </a></div> </div>');
                                                    $('.td_team2_bm_lay_0').html('<div class="lay-gradient text-color-black"> <div id="lay_1"><a class="pink-bg">  </a></div> </div>');
                                                    $('.td_team2_bm_lay_1').html('<div class="lay-gradient text-color-black"><div id="lay_2" class="light-pink-bg-2"> <a>  </a> </div> </div>');
                                                    $('.td_team2_bm_lay_2').html('<div class="lay-gradient text-color-black"><div id="lay_3" class="light-pink-bg-3"><a>  </a></div></div>');
                                                } else {
                                                    $('.team2_bm_fancy').remove();

                                                    $('.td_team2_bm_back_2').addClass("spark");
                                                    $('.td_team2_bm_back_2').html(sub_sub[0]);

                                                    $('.td_team2_bm_back_1').addClass("spark");
                                                    $('.td_team2_bm_back_1').html(sub_sub[1]);

                                                    $('.td_team2_bm_back_0').addClass("spark");
                                                    $('.td_team2_bm_back_0').html(sub_sub[2]);

                                                    $('.td_team2_bm_lay_0').addClass("sparkLay");
                                                    $('.td_team2_bm_lay_0').html(sub_sub[3]);

                                                    $('.td_team2_bm_lay_1').addClass("sparkLay");
                                                    $('.td_team2_bm_lay_1').html(sub_sub[4]);

                                                    $('.td_team2_bm_lay_2').addClass("sparkLay");
                                                    $('.td_team2_bm_lay_2').html(sub_sub[5]);
                                                }
                                            } else {
                                                if ($('.tr_bm_team2').prevAll(".team2_bm_fancy:first")) {
                                                    $('.team2_bm_fancy').remove();
                                                    $(".tr_bm_team2").before('<tr class="fancy-suspend-tr team2_bm_fancy"> <td></td> <td class="fancy-suspend-td" colspan="6"> <div class="fancy-suspend black-bg-5 text-color-white"><span>SUSPENDED</span></div> </td> </tr>');
                                                } else {
                                                    $(".tr_bm_team2").before('<tr class="fancy-suspend-tr team2_bm_fancy"> <td></td> <td class="fancy-suspend-td" colspan="6"> <div class="fancy-suspend black-bg-5 text-color-white"><span>SUSPENDED</span></div> </td> </tr>');
                                                }
                                                $('.td_team2_bm_back_2').html('<div class="back-gradient text-color-black"><div id="back_3" class="light-blue-bg-2"><a>  </a></div></div>');
                                                $('.td_team2_bm_back_1').html('<div class="back-gradient text-color-black"> <div id="back_2" class="light-blue-bg-3"> <a>  </a> </div> </div>');
                                                $('.td_team2_bm_back_0').html('<div class="back-gradient text-color-black"> <div id="back_1"><a class="cyan-bg">  </a></div> </div>');
                                                $('.td_team2_bm_lay_0').html('<div class="lay-gradient text-color-black"> <div id="lay_1"><a class="pink-bg">  </a></div> </div>');
                                                $('.td_team2_bm_lay_1').html('<div class="lay-gradient text-color-black"><div id="lay_2" class="light-pink-bg-2"> <a>  </a> </div> </div>');
                                                $('.td_team2_bm_lay_2').html('<div class="lay-gradient text-color-black"><div id="lay_3" class="light-pink-bg-3"><a>  </a></div></div>');
                                            }
                                        }
                                    }
                                } else if (i == 2) {
                                    if (sub_[1]) {
                                        $(".tr_bm_team3").before(sub_[1]);
                                        if (sub_[0]) {
                                            var sub_sub = sub_[0].split('~');

                                            //if(sub_sub[0]=="" && sub_sub[1]=="" && sub_sub[2]=="" && sub_sub[3]=="" && sub_sub[4]=="" && sub_sub[5]=="")
                                            if ((typeof sub_sub[0].trim() === 'undefined' || sub_sub[0].trim() == "SUSPENDED" || sub_sub[0] == "")) {
                                                if ($('.tr_bm_team3').prevAll(".team3_bm_fancy:first")) {
                                                    $('.team3_bm_fancy').remove();
                                                    $(".tr_bm_team3").before('<tr class="fancy-suspend-tr team3_bm_fancy"> <td></td> <td class="fancy-suspend-td" colspan="6"> <div class="fancy-suspend black-bg-5 text-color-white"><span>SUSPENDED</span></div> </td> </tr>');
                                                } else {
                                                    $(".tr_bm_team3").before('<tr class="fancy-suspend-tr team3_bm_fancy"> <td></td> <td class="fancy-suspend-td" colspan="6"> <div class="fancy-suspend black-bg-5 text-color-white"><span>SUSPENDED</span></div> </td> </tr>');
                                                }
                                                $('.td_team3_bm_back_2').html('<div class="back-gradient text-color-black"><div id="back_3" class="light-blue-bg-2"><a>  </a></div></div>');
                                                $('.td_team3_bm_back_1').html('<div class="back-gradient text-color-black"> <div id="back_2" class="light-blue-bg-3"> <a>  </a> </div> </div>');
                                                $('.td_team3_bm_back_0').html('<div class="back-gradient text-color-black"> <div id="back_1"><a class="cyan-bg">  </a></div> </div>');
                                                $('.td_team3_bm_lay_0').html('<div class="lay-gradient text-color-black"> <div id="lay_1"><a class="pink-bg">  </a></div> </div>');
                                                $('.td_team3_bm_lay_1').html('<div class="lay-gradient text-color-black"><div id="lay_2" class="light-pink-bg-2"> <a>  </a> </div> </div>');
                                                $('.td_team3_bm_lay_2').html('<div class="lay-gradient text-color-black"><div id="lay_3" class="light-pink-bg-3"><a>  </a></div></div>');
                                            } else {
                                                $('.team3_bm_fancy').remove();
                                                $('.td_team3_bm_back_2').html(sub_sub[0]);
                                                $('.td_team3_bm_back_1').html(sub_sub[1]);
                                                $('.td_team3_bm_back_0').html(sub_sub[2]);
                                                $('.td_team3_bm_lay_0').html(sub_sub[3]);
                                                $('.td_team3_bm_lay_1').html(sub_sub[4]);
                                                $('.td_team3_bm_lay_2').html(sub_sub[5]);
                                            }
                                        }
                                    } else {
                                        if (sub_[0]) {
                                            if (sub_[0] != 'SUSPENDED') {
                                                var sub_sub = sub_[0].split('~');
                                                $('.team3_bm_fancy').remove();

                                                //if(sub_sub[0]=="" && sub_sub[1]=="" && sub_sub[2]=="" && sub_sub[3]=="" && sub_sub[4]=="" && sub_sub[5]=="")
                                                if ((typeof sub_sub[0].trim() === 'undefined' || sub_sub[0].trim() == "SUSPENDED" || sub_sub[0] == "")) {
                                                    if ($('.tr_bm_team3').prevAll(".team3_bm_fancy:first")) {
                                                        $('.team3_bm_fancy').remove();
                                                        $(".tr_bm_team3").before('<tr class="fancy-suspend-tr team3_bm_fancy"> <td></td> <td class="fancy-suspend-td" colspan="6"> <div class="fancy-suspend black-bg-5 text-color-white"><span>SUSPENDED</span></div> </td> </tr>');
                                                    } else {
                                                        $(".tr_bm_team3").before('<tr class="fancy-suspend-tr team3_bm_fancy"> <td></td> <td class="fancy-suspend-td" colspan="6"> <div class="fancy-suspend black-bg-5 text-color-white"><span>SUSPENDED</span></div> </td> </tr>');
                                                    }
                                                    $('.td_team3_bm_back_2').html('<div class="back-gradient text-color-black"><div id="back_3" class="light-blue-bg-2"><a>  </a></div></div>');
                                                    $('.td_team3_bm_back_1').html('<div class="back-gradient text-color-black"> <div id="back_2" class="light-blue-bg-3"> <a>  </a> </div> </div>');
                                                    $('.td_team3_bm_back_0').html('<div class="back-gradient text-color-black"> <div id="back_1"><a class="cyan-bg">  </a></div> </div>');
                                                    $('.td_team3_bm_lay_0').html('<div class="lay-gradient text-color-black"> <div id="lay_1"><a class="pink-bg">  </a></div> </div>');
                                                    $('.td_team3_bm_lay_1').html('<div class="lay-gradient text-color-black"><div id="lay_2" class="light-pink-bg-2"> <a>  </a> </div> </div>');
                                                    $('.td_team3_bm_lay_2').html('<div class="lay-gradient text-color-black"><div id="lay_3" class="light-pink-bg-3"><a>  </a></div></div>');
                                                } else {

                                                    $('.team3_bm_fancy').remove();

                                                    $('.td_team3_bm_back_2').addClass("spark");
                                                    $('.td_team3_bm_back_2').html(sub_sub[0]);

                                                    $('.td_team3_bm_back_1').addClass("spark");
                                                    $('.td_team3_bm_back_1').html(sub_sub[1]);

                                                    $('.td_team3_bm_back_0').addClass("spark");
                                                    $('.td_team3_bm_back_0').html(sub_sub[2]);

                                                    $('.td_team3_bm_lay_0').addClass("sparkLay");
                                                    $('.td_team3_bm_lay_0').html(sub_sub[3]);

                                                    $('.td_team3_bm_lay_1').addClass("sparkLay");
                                                    $('.td_team3_bm_lay_1').html(sub_sub[4]);

                                                    $('.td_team3_bm_lay_2').addClass("sparkLay");
                                                    $('.td_team3_bm_lay_2').html(sub_sub[5]);
                                                }
                                            } else {
                                                if ($('.tr_bm_team3').prevAll(".team3_bm_fancy:first")) {
                                                    $('.team3_bm_fancy').remove();
                                                    $(".tr_bm_team3").before('<tr class="fancy-suspend-tr team3_bm_fancy"> <td></td> <td class="fancy-suspend-td" colspan="6"> <div class="fancy-suspend black-bg-5 text-color-white"><span>SUSPENDED</span></div> </td> </tr>');
                                                } else {
                                                    $(".tr_bm_team3").before('<tr class="fancy-suspend-tr team3_bm_fancy"> <td></td> <td class="fancy-suspend-td" colspan="6"> <div class="fancy-suspend black-bg-5 text-color-white"><span>SUSPENDED</span></div> </td> </tr>');
                                                }
                                                $('.td_team3_bm_back_2').html('<div class="back-gradient text-color-black"><div id="back_3" class="light-blue-bg-2"><a>  </a></div></div>');
                                                $('.td_team3_bm_back_1').html('<div class="back-gradient text-color-black"> <div id="back_2" class="light-blue-bg-3"> <a>  </a> </div> </div>');
                                                $('.td_team3_bm_back_0').html('<div class="back-gradient text-color-black"> <div id="back_1"><a class="cyan-bg">  </a></div> </div>');
                                                $('.td_team3_bm_lay_0').html('<div class="lay-gradient text-color-black"> <div id="lay_1"><a class="pink-bg">  </a></div> </div>');
                                                $('.td_team3_bm_lay_1').html('<div class="lay-gradient text-color-black"><div id="lay_2" class="light-pink-bg-2"> <a>  </a> </div> </div>');
                                                $('.td_team3_bm_lay_2').html('<div class="lay-gradient text-color-black"><div id="lay_3" class="light-pink-bg-3"><a>  </a></div></div>');
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }

                });
            }

            if (getUser != '') {
                getBalance();
                var match_sel = $('#select_bet_on_match').val();
                if (match_sel == "") {
                    match_sel = '{{$match->event_id}}~~' + 'All';
                }
                call_display_bet_list(match_sel);
            }
            var hid_fancy = $('#hid_fancy').val();
            var fancy_total = 0;
            for (var f = 0; f < hid_fancy; f++) {
                if ($('#Fancy_Total_' + f)) {
                    if ($('#Fancy_Total_' + f).text() != '')
                        fancy_total = parseFloat(fancy_total) + parseFloat($('#Fancy_Total_' + f).text());
                }
            }
            if (fancy_total > 0 && fancy_total != '' && fancy_total != 0)
                $('#all_session_total').val(fancy_total);
        }

        $(document).ready(function () {
            $("body").on('click', '#pinrisk', function () {
                var id = $(this).attr('data-id');
                if (getUser == undefined && getUser == null && getUser == '') {
                    $("#myLoginModal").modal('show');
                } else {
                    var _token = $("input[name='_token']").val();
                    $.ajax({
                        type: "POST",
                        url: '{{route("user.fav.match")}}',
                        data: {_token: _token, id: id},
                        beforeSend: function () {
                        },
                        complete: function () {
                        },
                        success: function (data) {
                            if (data.result == 'login') {
                                $("#myLoginModal").modal('show');
                            } else {
                                if (data.result == 'remove') {
                                    $("#pinrisk").removeClass("active");
                                } else {
                                    $("#pinrisk").addClass("active");
                                }
                            }
                        }
                    });
                }
            });

            $("body").on('click', '.fancy-calculation-exposer', function () {
                if (getUser == undefined && getUser == null && getUser == '') {
                    $("#myLoginModal").modal('show');
                } else {

                    $("#all_fancy_model #runPosition tbody.position").html('');

                    var target = $(this).attr('data-target');
                    var fancyName = $(this).attr('data-fancy-name');
                    var event_id = '{{$match->event_id}}';

                    var _token = '{{csrf_token()}}';

                    $.ajax({
                        type: "POST",
                        url: '/fancy/calculation',
                        data: {_token: _token, event_id: event_id, fancy_name: fancyName},
                        beforeSend: function () {
                        },
                        complete: function () {
                        },
                        success: function (data) {
                            if (data.action == 'login') {
                                $("#myLoginModal").modal('show');
                            } else {
                                $("#all_fancy_model #runPosition tbody.position").html(data.html);
                                $("#all_fancy_model #runPosition").modal("show");
                            }
                        }
                    });
                }
            });
        });

        $(document).ready(function () {
            //ScoreBoard();
            // loadDetailPageContent();

            if (getUser != '') {

                setInterval(function () {
                    matchDeclareRedirect();
                    //ScoreBoard();

                    var match_sel = $('#select_bet_on_match').val();
                    if (match_sel == "") {
                        match_sel = '{{$match->event_id}}~~' + 'All';
                    }
                    call_display_bet_list(match_sel);

                }, 1000);

                // setInterval(function () {
                //     loadDetailPageContent();
                // }, 1000);
            }

            $(document).on("click", '.openfancymodel_dynamic', function (event) {
                var id = $(this).data("target");
                $('#opened_fancy_model_id').val(id);
            });
            $(document).on("click", '.modelclose', function (event) {
                $('#opened_fancy_model_id').val('');
                $(mid).modal('hide');
            });
            //default call
            var _token = $("input[name='_token']").val();
            var match_type = '{{$match->sports_id}}';
            var matchid = '{{$match->match_id}}';
            var matchname = '{{$match->match_name}}';
            var event_id = '{{$match->event_id}}';
            var match_id = '{{$match->id}}';
            var match_m = '{{$match->suspend_m}}';
            var match_b = '{{$match->suspend_b}}';
            var match_f = '{{$match->suspend_f}}';

            setInterval(function () {
                $("td").removeClass("spark");
                $("td").removeClass("sparkLay");
            }, 500);
            /*setInterval(function() {
            $(".alert").alert('close');
        }, 1000);*/

            $('#odds_val').val(' ');
            $('#inputStake').val(' ');
            document.getElementById('betform').reset();
            $("#collapseExample1").load(location.href + " #collapseExample1");
        });

        //function ScoreBoard()
        //{

        //}

        function opnForm(vl) {
            if (getUser == undefined || getUser == null || getUser == '') {
                $("#myLoginModal").modal('show');
            } else {

                $('#team1_bet_count_new').text('');
                $('#team1_bet_count_new').hide();
                $('#team2_bet_count_new').text('');
                $('#team2_bet_count_new').hide();
                $('#draw_bet_count_new').text('');
                $('#draw_bet_count_new').hide();
                $('.new-fancy-total').text();
                $('.new-fancy-total').hide();

                $(".amountint").text('0');
                $("#inputStake").val('');

                $('#is_session_open_position').val(' ');
                var cls_name = $(vl).attr("data-cls");
                var value = $(vl).attr("data-val");
                var volume = $(vl).attr("data-volume");
                var odds_position = $(vl).attr("data-position");
                $('#odds_position').val(odds_position);
                $("#odds_volume").val(volume);
                var tm = $('#team_id').val();
                $('#final_odds_back_val_team1').val($('.td_team1_back_0').children('a').attr('data-val'));
                $('#final_odds_back_val_team2').val($('.td_team2_back_0').children('a').attr('data-val'));
                if ($('#team3').val() != '')
                    $('#final_odds_back_val_team3').val($('.td_team3_back_0').children('a').attr('data-val'));
                $('#final_odds_lay_val_team1').val($('.td_team1_back_0').children('a').attr('data-val'));
                $('#final_odds_lay_val_team2').val($('.td_team2_back_0').children('a').attr('data-val'));
                if ($('#team3').val() != '')
                    $('#final_odds_lay_val_team3').val($('.td_team3_back_0').children('a').attr('data-val'));
                $("#odds_val").val(value);
                if (value > 0) {
                    if (cls_name == 'pink-bg') {
                        $(".betslip_box").addClass('pink-bg-light');
                        $(".col-stake_list").addClass('pink-bg-light');
                        $(".keep-option").addClass('pink-bg-light');
                        $(".betslip_box").removeClass('cyan-bg-light');
                        $(".col-stake_list").removeClass('cyan-bg-light');
                        $(".keep-option").removeClass('cyan-bg-light');

                        $(".mobileBack").addClass('pink-bg-light');
                        $(".mobileBack").removeClass('cyan-bg-light');
                    }
                    if (cls_name == 'cyan-bg') {
                        $(".betslip_box").addClass('cyan-bg-light');
                        $(".col-stake_list").addClass('cyan-bg-light');
                        $(".keep-option").addClass('cyan-bg-light');
                        $(".betslip_box").removeClass('pink-bg-light');
                        $(".col-stake_list").removeClass('pink-bg-light');
                        $(".keep-option").removeClass('pink-bg-light');

                        $(".mobileBack").addClass('cyan-bg-light');
                        $(".mobileBack").removeClass('pink-bg-light');
                    }
                    var width = $(window).width();
                    if (width < 990) {
                        $('#session_mobile_odds_position').val('');

                        $('.mobile_tr_common_class').html('');

                        //if($(vl).data("bettype")=='ODDS')
                        //{
                        //$('.tr_team1_td_mobile').hide();
                        //$('.tr_team2_td_mobile').hide();
                        //$('.tr_team3_td_mobile').hide();

                        $('.tr_team1_td_mobile').html('');
                        $('.tr_team2_td_mobile').html('');
                        $('.tr_team3_td_mobile').html('');
                        //$('#betmobileModal').modal('show');
                        //}
                        //else if($(vl).data("bettype")=='BOOKMAKER')
                        //{
                        //$('.tr_team1_BM_td_mobile').hide();
                        //$('.tr_team2_BM_td_mobile').hide();
                        //$('.tr_team3_BM_td_mobile').hide();

                        $('.tr_team1_BM_td_mobile').html('');
                        $('.tr_team2_BM_td_mobile').html('');
                        $('.tr_team3_BM_td_mobile').html('');
                        //$('#betmobileModal').modal('show');
                        //}
                        $('#team_id').val($(vl).data("team"));
                        var tm = $('#team_id').val();

                        //alert(tm+'.tr_'+tm+'_td_mobile');
                        var div = $('#mobile_invisible_div').html();
                        //alert(div);
                        //$(".mobile_bet_model_div").show();
                        if ($(vl).data("bettype") == 'ODDS') {
                            $('.tr_' + tm).show();
                            $('.tr_' + tm + '_td_mobile').show();
                            $('.tr_' + tm + '_td_mobile').html(div);
                            $('#session_mobile_odds_position').val(value);
                            $('.minusodds').show();
                            $('.plusodds').show();
                            $('#mobile_odds').css("width", "40%");
                            $('#mobile_odds').val(value);
                            $('#mobile_odds_display').hide();
                            $('#mobile_odds').show();
                        } else if ($(vl).data("bettype") == 'BOOKMAKER') {
                            $('.tr_' + tm + '_BM').show();
                            $('.tr_' + tm + '_BM_td_mobile').show();
                            $('.tr_' + tm + '_BM_td_mobile').html(div);
                            $('#session_mobile_odds_position').val(value);
                            $('.minusodds').hide();
                            $('.plusodds').hide();
                            $('#mobile_odds').css("width", "100%");
                            $('#mobile_odds').val(value);
                            $('#mobile_odds_display').hide();
                            $('#mobile_odds').show();
                        } else if ($(vl).data("bettype") == 'SESSION') {
                            //
                            $('.mobile_tr_common_class').html('');
                            var posi = $(vl).data("position");
                            $('#session_mobile_val_position').val(posi);
                            $('.tr_team' + posi + '_fancy').show();
                            $('.tr_team' + posi + '_fancy_td_mobile').show();
                            $('.tr_team' + posi + '_fancy_td_mobile').html(div);
                            $('#session_position').val(posi);
                            $('#is_session_open_position').val(posi);
                            //alert('aaa'+value);
                            //document.getElementById('mobile_odds').value=value;
                            $('.minusodds').hide();
                            $('.plusodds').hide();
                            $('#mobile_odds').css("width", "100%");
                            $('#session_mobile_odds_position').val(value);
                            $('#mobile_odds').val($('#session_mobile_odds_position').val());
                            $('#mobile_odds_display').val($('#session_mobile_odds_position').val() + "/" + volume);
                            $('#mobile_odds').hide();
                            $('#mobile_odds_display').show();
                        }
                    }
                    $(".showForm").show();
                } else
                    $(".showForm").hide();
            }
        }

        $(document).on("click", '#cancel_bet_form', function (event) {
            console.log('cancel2');
            $(".showForm").hide();
            // odds
            $('#team1_bet_count_new').hide();
            $('#team2_bet_count_new').hide();
            $('#draw_bet_count_new').hide();
            // bookmaker
            $('#team1_betBM_count_new').hide();
            $('#team2_betBM_count_new').hide();
            $('#draw_betBM_count_new').hide();

            $('#team1_bet_count_new').text('');
            $('#team1_bet_count_new').hide();
            $('#team2_bet_count_new').text('');
            $('#team2_bet_count_new').hide();
            $('#draw_bet_count_new').text('');
            $('#draw_bet_count_new').hide();

            $(".mobile_tr_common_class").html('');

            $('#session_mobile_val_position').val(' ');
            $('#session_position').val(' ');
            $('#is_session_open_position').val(' ');
            $('#session_mobile_odds_position').val(' ');
            $('#inputStake_mobile').val(0);

        });
        $(document).on("click", '.hide_mobile_bet_model', function (event) {
            var $myInput = $('#inputStake_mobile');
            $myInput.val($myInput.val().slice(0, -1));

            var oddval = $('#inputStake_mobile').val();
            var fval = $('#inputStake_mobile').val();
            var matchVal = $("#mobile_odds").val();


            $('#inputStake').val(oddval);
            $('#inputStake_mobile').val($myInput.val());

            finalValue = '';
            if ($('#betTypeAdd').val() == 'ODDS' && $('#betTypeAdd').val() != 'SESSION') {
                matchVal = parseFloat(matchVal) - parseInt(1);
                finalValue = parseFloat(oddval) * parseFloat(matchVal);
            } else if ($('#betTypeAdd').val() == 'BOOKMAKER' && $('#betTypeAdd').val() != 'SESSION') {
                finalValue = (parseFloat(oddval) * parseFloat(matchVal)) / parseFloat(100);
            }
            if ($('#betTypeAdd').val() != 'SESSION')
                $('.profil').html(finalValue.toFixed(2));
            var team = $('#teamNameBet').val();
            var old_team1 = $('#team1').val();
            var old_team2 = $('#team2').val();
            var old_team3 = $('#team3').val();

            if ($('#betTypeAdd').val() == 'ODDS' && $('#betTypeAdd').val() != 'SESSION') {
                if ($('#betSide').val() == 'back') {
                    if (old_team1.trim() == team.trim()) {
                        $('#team1_bet_count_new').show();
                        var old_value = $('#team1_total').text();
                        if (old_value != '') {
                            finalValue = parseFloat(old_value) + parseFloat(finalValue);
                        }
                        if (finalValue > 0) {
                            $('#team1_bet_count_new').text(finalValue.toFixed(2));
                            $('#team1_bet_count_new').removeClass('tolose text-color-red');
                            $('#team1_bet_count_new').addClass('towin text-color-green');
                        } else {
                            $('#team1_bet_count_new').text(parseFloat(finalValue).toFixed(2));
                            $('#team1_bet_count_new').addClass('tolose text-color-red');
                            $('#team1_bet_count_new').removeClass('towin text-color-green');
                        }
                        fval_new = '';
                        var old_value_team2 = $('#team2_total').text();
                        if (old_value_team2 != '') {
                            fval_new = parseFloat(old_value_team2) - parseFloat(fval);
                        }
                        if (fval_new > 0) {
                            $('#team2_bet_count_new').removeClass('tolose text-color-red');
                            $('#team2_bet_count_new').addClass('towin text-color-green');
                            $('#team2_bet_count_new').show();
                            $('#team2_bet_count_new').text(fval_new);
                        } else {
                            $('#team2_bet_count_new').addClass('tolose text-color-red');
                            $('#team2_bet_count_new').removeClass('towin text-color-green');
                            $('#team2_bet_count_new').show();
                            $('#team2_bet_count_new').text(fval_new);
                        }
                        var old_value_team3 = $('#draw_total').text();
                        if ($('#team3').val() != '') {
                            if (old_value_team3 != '') {
                                fval = parseFloat(old_value_team3) - parseFloat(fval);
                            }
                            if (fval > 0) {
                                $('#draw_bet_count_new').removeClass('tolose text-color-red');
                                $('#draw_bet_count_new').addClass('towin text-color-green');
                                $('#draw_bet_count_new').show();
                                $('#draw_bet_count_new').text(fval);
                            } else {
                                $('#draw_bet_count_new').addClass('tolose text-color-red');
                                $('#draw_bet_count_new').removeClass('towin text-color-green');
                                $('#draw_bet_count_new').show();
                                $('#draw_bet_count_new').text(fval);
                            }
                        }
                    } else if (old_team2.trim() == team.trim()) {
                        $('#team2_bet_count_new').show();

                        var old_value = $('#team2_total').text();
                        if (old_value != '') {
                            finalValue = parseFloat(old_value) + parseFloat(finalValue);
                        }
                        if (finalValue > 0) {
                            $('#team2_bet_count_new').text(finalValue.toFixed(2));
                            $('#team2_bet_count_new').removeClass('tolose text-color-red');
                            $('#team2_bet_count_new').addClass('towin text-color-green');
                        } else {
                            $('#team2_bet_count_new').text(finalValue.toFixed(2));
                            $('#team2_bet_count_new').removeClass('tolose text-color-red');
                            $('#team2_bet_count_new').addClass('towin text-color-green');
                        }
                        fval_new = '';
                        var old_value_team1 = $('#team1_total').text();
                        if (old_value_team1 != '') {
                            fval_new = parseFloat(old_value_team1) - parseFloat(fval);
                        }
                        if (fval_new > 0) {
                            $('#team1_bet_count_new').removeClass('tolose text-color-red');
                            $('#team1_bet_count_new').addClass('towin text-color-green');
                            $('#team1_bet_count_new').show();
                            $('#team1_bet_count_new').text(fval_new.toFixed(2));
                        } else {
                            $('#team1_bet_count_new').addClass('tolose text-color-red');
                            $('#team1_bet_count_new').removeClass('towin text-color-green');
                            $('#team1_bet_count_new').show();
                            $('#team1_bet_count_new').text(fval_new.toFixed(2));
                        }
                        var old_value_team3 = $('#draw_total').text();
                        if ($('#team3').val() != '') {
                            if (old_value_team3 != '') {
                                fval = parseFloat(old_value_team3) - parseFloat(fval);
                            }
                            if (fval > 0) {
                                $('#draw_bet_count_new').removeClass('tolose text-color-red');
                                $('#draw_bet_count_new').addClass('towin text-color-green');
                                $('#draw_bet_count_new').show();
                                $('#draw_bet_count_new').text(fval.toFixed(2));
                            } else {
                                $('#draw_bet_count_new').addClass('tolose text-color-red');
                                $('#draw_bet_count_new').removeClass('towin text-color-green');
                                $('#draw_bet_count_new').show();
                                $('#draw_bet_count_new').text(fval.toFixed(2));
                            }
                        }
                    } else if (old_team3.trim() == team.trim()) {
                        if ($('#team3').val() != '') {
                            $('#draw_bet_count_new').show();
                            var old_value = $('#draw_total').text();
                            if (old_value != '') {
                                finalValue = parseFloat(old_value) + parseFloat(finalValue);
                            }
                            if (finalValue > 0) {
                                $('#draw_bet_count_new').removeClass('tolose text-color-red');
                                $('#draw_bet_count_new').addClass('towin text-color-green');
                                $('#draw_bet_count_new').show();
                                $('#draw_bet_count_new').text(finalValue.toFixed(2));
                            } else {
                                $('#draw_bet_count_new').addClass('tolose text-color-red');
                                $('#draw_bet_count_new').removeClass('towin text-color-green');
                                $('#draw_bet_count_new').show();
                                $('#draw_bet_count_new').text(finalValue.toFixed(2));
                            }

                            fval_new = '';
                            var old_value_team1 = $('#team1_total').text();
                            if (old_value_team1 != '') {
                                fval_new = parseFloat(old_value_team1) - parseFloat(fval);
                            }
                            if (fval_new > 0) {
                                $('#team1_bet_count_new').show();
                                $('#team1_bet_count_new').removeClass('tolose text-color-red');
                                $('#team1_bet_count_new').addClass('towin text-color-green');
                                $('#team1_bet_count_new').text(fval_new.toFixed(2));
                            } else {
                                $('#team1_bet_count_new').show();
                                $('#team1_bet_count_new').addClass('tolose text-color-red');
                                $('#team1_bet_count_new').removeClass('towin text-color-green');
                                $('#team1_bet_count_new').text(fval_new.toFixed(2));
                            }

                            fval_new = '';
                            var old_value_team2 = $('#team2_total').text();
                            if (old_value_team2 != '') {
                                fval_new = parseFloat(old_value_team2) - parseFloat(fval);
                            }
                            if (fval_new > 0) {
                                $('#team2_bet_count_new').removeClass('tolose text-color-red');
                                $('#team2_bet_count_new').addClass('towin text-color-green');
                                $('#team2_bet_count_new').show();
                                $('#team2_bet_count_new').text(fval_new.toFixed(2));
                            } else {
                                $('#team2_bet_count_new').addClass('tolose text-color-red');
                                $('#team2_bet_count_new').removeClass('towin text-color-green');
                                $('#team2_bet_count_new').show();
                                $('#team2_bet_count_new').text(fval_new.toFixed(2));
                            }
                        }
                    }
                } else if ($('#betSide').val() == 'lay') {
                    if (old_team1.trim() == team.trim()) {
                        $('#team1_bet_count_new').show();
                        var old_value = $('#team1_total').text();
                        if (old_value != '') {
                            finalValue = parseFloat(old_value) - parseFloat(finalValue);
                        }
                        if (finalValue > 0) {
                            $('#team1_bet_count_new').show();
                            $('#team1_bet_count_new').text(finalValue.toFixed(2));
                            $('#team1_bet_count_new').removeClass('tolose text-color-red');
                            $('#team1_bet_count_new').addClass('towin text-color-green');
                        } else {
                            $('#team1_bet_count_new').show();
                            $('#team1_bet_count_new').text(finalValue.toFixed(2));
                            $('#team1_bet_count_new').addClass('tolose text-color-red');
                            $('#team1_bet_count_new').removeClass('towin text-color-green');
                        }
                        fval_new = '';
                        var old_value_team2 = $('#team2_total').text();
                        if (old_value_team2 != '') {
                            fval_new = parseFloat(old_value_team2) + parseFloat(fval);
                        }
                        if (fval_new > 0) {
                            $('#team2_bet_count_new').removeClass('tolose text-color-red');
                            $('#team2_bet_count_new').addClass('towin text-color-green');
                            $('#team2_bet_count_new').show();
                            $('#team2_bet_count_new').text(fval_new.toFixed(2));
                        } else {
                            $('#team2_bet_count_new').addClass('tolose text-color-red');
                            $('#team2_bet_count_new').removeClass('towin text-color-green');
                            $('#team2_bet_count_new').show();
                            $('#team2_bet_count_new').text(fval_new.toFixed(2));
                        }
                        if ($('#team3').val() != '') {
                            var old_value_team3 = $('#draw_total').text();
                            if ($('#team3').val() != '') {
                                if (old_value_team3 != '') {
                                    fval = parseFloat(old_value_team3) + parseFloat(fval);
                                }
                                if (fval > 0) {
                                    $('#draw_bet_count_new').removeClass('tolose text-color-red');
                                    $('#draw_bet_count_new').addClass('towin text-color-green');
                                    $('#draw_bet_count_new').show();
                                    $('#draw_bet_count_new').text(fval.toFixed(2));
                                } else {
                                    $('#draw_bet_count_new').addClass('tolose text-color-red');
                                    $('#draw_bet_count_new').removeClass('towin text-color-green');
                                    $('#draw_bet_count_new').show();
                                    $('#draw_bet_count_new').text(fval.toFixed(2));
                                }
                            }
                        }
                    } else if (old_team2.trim() == team.trim()) {
                        var old_value = $('#team2_total').text();
                        if (old_value != '') {
                            finalValue = parseFloat(old_value) - parseFloat(finalValue);
                        }
                        if (finalValue > 0) {
                            $('#team2_bet_count_new').show();
                            $('#team2_bet_count_new').text(finalValue.toFixed(2));
                            $('#team2_bet_count_new').removeClass('tolose text-color-red');
                            $('#team2_bet_count_new').addClass('towin text-color-green');
                        } else {
                            $('#team2_bet_count_new').show();
                            $('#team2_bet_count_new').text(finalValue.toFixed(2));
                            $('#team2_bet_count_new').addClass('tolose text-color-red');
                            $('#team2_bet_count_new').removeClass('towin text-color-green');
                        }
                        fval_new = '';
                        var old_value_team1 = $('#team1_total').text();
                        if (old_value_team1 != '') {
                            fval_new = parseFloat(old_value_team1) + parseFloat(fval);
                        }
                        if (fval_new > 0) {
                            $('#team1_bet_count_new').removeClass('tolose text-color-red');
                            $('#team1_bet_count_new').addClass('towin text-color-green');
                            $('#team1_bet_count_new').show();
                            $('#team1_bet_count_new').text(fval_new.toFixed(2));
                        } else {
                            $('#team1_bet_count_new').addClass('tolose text-color-red');
                            $('#team1_bet_count_new').removeClass('towin text-color-green');
                            $('#team1_bet_count_new').show();
                            $('#team1_bet_count_new').text(fval_new.toFixed(2));
                        }

                        if ($('#team3').val() != '') {
                            fval_new = '';
                            var old_value_draw = $('#draw_total').text();
                            if (old_value_draw != '') {
                                fval_new = parseFloat(old_value_draw) + parseFloat(fval);
                            }
                            if (fval_new > 0) {
                                $('#draw_bet_count_new').removeClass('tolose text-color-red');
                                $('#draw_bet_count_new').addClass('towin text-color-green');
                                $('#draw_bet_count_new').show();
                                $('#draw_bet_count_new').text(fval_new.toFixed(2));
                            } else {
                                $('#draw_bet_count_new').addClass('tolose text-color-red');
                                $('#draw_bet_count_new').removeClass('towin text-color-green');
                                $('#draw_bet_count_new').show();
                                $('#draw_bet_count_new').text(fval_new.toFixed(2));
                            }
                        }
                    } else if (old_team3.trim() == team.trim()) {
                        fval_new = '';
                        var old_value_team1 = $('#team1_total').text();
                        if (old_value_team1 != '') {
                            fval_new = parseFloat(old_value_team1) + parseFloat(fval);
                        }
                        if (fval_new > 0) {
                            $('#team1_bet_count_new').show();
                            $('#team1_bet_count_new').text(fval_new.toFixed(2));
                            $('#team1_bet_count_new').removeClass('tolose text-color-red');
                            $('#team1_bet_count_new').addClass('towin text-color-green');
                        } else {
                            $('#team1_bet_count_new').show();
                            $('#team1_bet_count_new').text(fval_new.toFixed(2));
                            $('#team1_bet_count_new').addClass('tolose text-color-red');
                            $('#team1_bet_count_new').removeClass('towin text-color-green');
                        }
                        fval_new = '';
                        var old_value_team2 = $('#team2_total').text();
                        if (old_value_team2 != '') {
                            fval_new = parseFloat(old_value_team2) + parseFloat(fval);
                        }
                        if (fval_new > 0) {
                            $('#team2_bet_count_new').removeClass('tolose text-color-red');
                            $('#team2_bet_count_new').addClass('towin text-color-green');
                            $('#team2_bet_count_new').show();
                            $('#team2_bet_count_new').text(fval_new.toFixed(2));
                        } else {
                            $('#team2_bet_count_new').addClass('tolose text-color-red');
                            $('#team2_bet_count_new').removeClass('towin text-color-green');
                            $('#team2_bet_count_new').show();
                            $('#team2_bet_count_new').text(fval_new.toFixed(2));
                        }

                        if ($('#team3').val() != '') {
                            var old_value = $('#draw_total').text();
                            if (old_value != '') {
                                finalValue = parseFloat(old_value) - parseFloat(finalValue);
                            }
                            if (finalValue > 0) {
                                $('#draw_bet_count_new').addClass('towin text-color-green');
                                $('#draw_bet_count_new').removeClass('tolose text-color-red');
                                $('#draw_bet_count_new').show();
                                $('#draw_bet_count_new').text(finalValue.toFixed(2));
                            } else {
                                $('#draw_bet_count_new').removeClass('towin text-color-green');
                                $('#draw_bet_count_new').addClass('tolose text-color-red');
                                $('#draw_bet_count_new').show();
                                $('#draw_bet_count_new').text(finalValue.toFixed(2));
                            }
                        }
                    }
                }
            } else if ($('#betTypeAdd').val() == 'BOOKMAKER' && $('#betTypeAdd').val() != 'SESSION') {
                if ($('#betSide').val() == 'back') {
                    if (old_team1.trim() == team.trim()) {
                        $('#team1_betBM_count_new').show();
                        var old_value = $('#team1_BM_total').text();
                        if (old_value != '') {
                            finalValue = parseFloat(old_value) + parseFloat(finalValue);
                        }
                        if (finalValue > 0) {
                            $('#team1_betBM_count_new').text(finalValue.toFixed(2));
                            $('#team1_betBM_count_new').removeClass('tolose text-color-red');
                            $('#team1_betBM_count_new').addClass('towin text-color-green');
                        } else {
                            $('#team1_betBM_count_new').text(parseFloat(finalValue).toFixed(2));
                            $('#team1_betBM_count_new').addClass('tolose text-color-red');
                            $('#team1_betBM_count_new').removeClass('towin text-color-green');
                        }
                        fval_new = '';
                        var old_value_team2 = $('#team2_BM_total').text();
                        if (old_value_team2 != '') {
                            fval_new = parseFloat(old_value_team2) - parseFloat(fval);
                        }
                        if (fval_new > 0) {
                            $('#team2_betBM_count_new').removeClass('tolose text-color-red');
                            $('#team2_betBM_count_new').addClass('towin text-color-green');
                            $('#team2_betBM_count_new').show();
                            $('#team2_beBMt_count_new').text(fval_new);
                        } else {
                            $('#team2_betBM_count_new').addClass('tolose text-color-red');
                            $('#team2_betBM_count_new').removeClass('towin text-color-green');
                            $('#team2_betBM_count_new').show();
                            $('#team2_betBM_count_new').text(fval_new);
                        }
                        var old_value_team3 = $('#draw_BM_total').text();
                        if ($('#team3').val() != '') {
                            if (old_value_team3 != '') {
                                fval = parseFloat(old_value_team3) - parseFloat(fval);
                            }
                            if (fval > 0) {
                                $('#draw_betBM_count_new').removeClass('tolose text-color-red');
                                $('#draw_betBM_count_new').addClass('towin text-color-green');
                                $('#draw_betBM_count_new').show();
                                $('#draw_betBM_count_new').text(fval);
                            } else {
                                $('#draw_betBM_count_new').addClass('tolose text-color-red');
                                $('#draw_betBM_count_new').removeClass('towin text-color-green');
                                $('#draw_betBM_count_new').show();
                                $('#draw_betBM_count_new').text(fval);
                            }
                        }
                    } else if (old_team2.trim() == team.trim()) {
                        $('#team2_betBM_count_new').show();

                        var old_value = $('#team2_BM_total').text();
                        if (old_value != '') {
                            finalValue = parseFloat(old_value) + parseFloat(finalValue);
                        }
                        if (finalValue > 0) {
                            $('#team2_betBM_count_new').text(finalValue.toFixed(2));
                            $('#team2_betBM_count_new').removeClass('tolose text-color-red');
                            $('#team2_betBM_count_new').addClass('towin text-color-green');
                        } else {
                            $('#team2_betBM_count_new').text(finalValue.toFixed(2));
                            $('#team2_betBM_count_new').removeClass('tolose text-color-red');
                            $('#team2_betBM_count_new').addClass('towin text-color-green');
                        }
                        fval_new = '';
                        var old_value_team1 = $('#team1_BM_total').text();
                        if (old_value_team1 != '') {
                            fval_new = parseFloat(old_value_team1) - parseFloat(fval);
                        }
                        if (fval_new > 0) {
                            $('#team1_betBM_count_new').removeClass('tolose text-color-red');
                            $('#team1_betBM_count_new').addClass('towin text-color-green');
                            $('#team1_betBM_count_new').show();
                            $('#team1_betBM_count_new').text(fval_new.toFixed(2));
                        } else {
                            $('#team1_betBM_count_new').addClass('tolose text-color-red');
                            $('#team1_betBM_count_new').removeClass('towin text-color-green');
                            $('#team1_betBM_count_new').show();
                            $('#team1_betBM_count_new').text(fval_new.toFixed(2));
                        }
                        var old_value_team3 = $('#draw_BM_total').text();
                        if ($('#team3').val() != '') {
                            if (old_value_team3 != '') {
                                fval = parseFloat(old_value_team3) - parseFloat(fval);
                            }
                            if (fval > 0) {
                                $('#draw_betBM_count_new').removeClass('tolose text-color-red');
                                $('#draw_betBM_count_new').addClass('towin text-color-green');
                                $('#draw_betBM_count_new').show();
                                $('#draw_betBM_count_new').text(fval.toFixed(2));
                            } else {
                                $('#draw_betBM_count_new').addClass('tolose text-color-red');
                                $('#draw_betBM_count_new').removeClass('towin text-color-green');
                                $('#draw_betBM_count_new').show();
                                $('#draw_betBM_count_new').text(fval.toFixed(2));
                            }
                        }
                    } else if (old_team3.trim() == team.trim()) {
                        if ($('#team3').val() != '') {
                            $('#draw_betBM_count_new').show();
                            var old_value = $('#draw_BM_total').text();
                            if (old_value != '') {
                                finalValue = parseFloat(old_value) + parseFloat(finalValue);
                            }
                            if (finalValue > 0) {
                                $('#draw_betBM_count_new').removeClass('tolose text-color-red');
                                $('#draw_betBM_count_new').addClass('towin text-color-green');
                                $('#draw_betBM_count_new').show();
                                $('#draw_betBM_count_new').text(finalValue.toFixed(2));
                            } else {
                                $('#draw_betBM_count_new').addClass('tolose text-color-red');
                                $('#draw_betBM_count_new').removeClass('towin text-color-green');
                                $('#draw_betBM_count_new').show();
                                $('#draw_betBM_count_new').text(finalValue.toFixed(2));
                            }

                            fval_new = '';
                            var old_value_team1 = $('#team1_total').text();
                            if (old_value_team1 != '') {
                                fval_new = parseFloat(old_value_team1) - parseFloat(fval);
                            }
                            if (fval_new > 0) {
                                $('#team1_betBM_count_new').show();
                                $('#team1_betBM_count_new').removeClass('tolose text-color-red');
                                $('#team1_betBM_count_new').addClass('towin text-color-green');
                                $('#team1_betBM_count_new').text(fval_new.toFixed(2));
                            } else {
                                $('#team1_betBM_count_new').show();
                                $('#team1_betBM_count_new').addClass('tolose text-color-red');
                                $('#team1_betBM_count_new').removeClass('towin text-color-green');
                                $('#team1_betBM_count_new').text(fval_new.toFixed(2));
                            }

                            fval_new = '';
                            var old_value_team2 = $('#team2_BM_total').text();
                            if (old_value_team2 != '') {
                                fval_new = parseFloat(old_value_team2) - parseFloat(fval);
                            }
                            if (fval_new > 0) {
                                $('#team2_betBM_count_new').removeClass('tolose text-color-red');
                                $('#team2_betBM_count_new').addClass('towin text-color-green');
                                $('#team2_betBM_count_new').show();
                                $('#team2_betBM_count_new').text(fval_new.toFixed(2));
                            } else {
                                $('#team2_betBM_count_new').addClass('tolose text-color-red');
                                $('#team2_betBM_count_new').removeClass('towin text-color-green');
                                $('#team2_betBM_count_new').show();
                                $('#team2_betBM_count_new').text(fval_new.toFixed(2));
                            }
                        }
                    }
                } else if ($('#betSide').val() == 'lay') {
                    if (old_team1.trim() == team.trim()) {
                        $('#team1_betBM_count_new').show();
                        var old_value = $('#team1_BM_total').text();
                        if (old_value != '') {
                            finalValue = parseFloat(old_value) - parseFloat(finalValue);
                        }
                        if (finalValue > 0) {
                            $('#team1_betBM_count_new').show();
                            $('#team1_betBM_count_new').text(finalValue.toFixed(2));
                            $('#team1_betBM_count_new').removeClass('tolose text-color-red');
                            $('#team1_betBM_count_new').addClass('towin text-color-green');
                        } else {
                            $('#team1_betBM_count_new').show();
                            $('#team1_betBM_count_new').text(finalValue.toFixed(2));
                            $('#team1_betBM_count_new').addClass('tolose text-color-red');
                            $('#team1_betBM_count_new').removeClass('towin text-color-green');
                        }
                        fval_new = '';
                        var old_value_team2 = $('#team2_BM_total').text();
                        if (old_value_team2 != '') {
                            fval_new = parseFloat(old_value_team2) + parseFloat(fval);
                        }
                        if (fval_new > 0) {
                            $('#team2_betBM_count_new').removeClass('tolose text-color-red');
                            $('#team2_betBM_count_new').addClass('towin text-color-green');
                            $('#team2_betBM_count_new').show();
                            $('#team2_betBM_count_new').text(fval_new.toFixed(2));
                        } else {
                            $('#team2_betBM_count_new').addClass('tolose text-color-red');
                            $('#team2_betBM_count_new').removeClass('towin text-color-green');
                            $('#team2_betBM_count_new').show();
                            $('#team2_betBM_count_new').text(fval_new.toFixed(2));
                        }
                        if ($('#team3').val() != '') {
                            var old_value_team3 = $('#draw_BM_total').text();
                            if ($('#team3').val() != '') {
                                if (old_value_team3 != '') {
                                    fval = parseFloat(old_value_team3) + parseFloat(fval);
                                }
                                if (fval > 0) {
                                    $('#draw_betBM_count_new').removeClass('tolose text-color-red');
                                    $('#draw_betBM_count_new').addClass('towin text-color-green');
                                    $('#draw_betBM_count_new').show();
                                    $('#draw_betBM_count_new').text(fval.toFixed(2));
                                } else {
                                    $('#draw_betBM_count_new').addClass('tolose text-color-red');
                                    $('#draw_betBM_count_new').removeClass('towin text-color-green');
                                    $('#draw_betBM_count_new').show();
                                    $('#draw_betBM_count_new').text(fval.toFixed(2));
                                }
                            }
                        }
                    } else if (old_team2.trim() == team.trim()) {
                        var old_value = $('#team2_BM_total').text();
                        if (old_value != '') {
                            finalValue = parseFloat(old_value) - parseFloat(finalValue);
                        }
                        if (finalValue > 0) {
                            $('#team2_betBM_count_new').show();
                            $('#team2_betBM_count_new').text(finalValue.toFixed(2));
                            $('#team2_betBM_count_new').removeClass('tolose text-color-red');
                            $('#team2_betBM_count_new').addClass('towin text-color-green');
                        } else {
                            $('#team2_betBM_count_new').show();
                            $('#team2_betBM_count_new').text(finalValue.toFixed(2));
                            $('#team2_betBM_count_new').addClass('tolose text-color-red');
                            $('#team2_betBM_count_new').removeClass('towin text-color-green');
                        }
                        fval_new = '';
                        var old_value_team1 = $('#team1_BM_total').text();
                        if (old_value_team1 != '') {
                            fval_new = parseFloat(old_value_team1) + parseFloat(fval);
                        }
                        if (fval_new > 0) {
                            $('#team1_betBM_count_new').removeClass('tolose text-color-red');
                            $('#team1_betBM_count_new').addClass('towin text-color-green');
                            $('#team1_betBM_count_new').show();
                            $('#team1_betBM_count_new').text(fval_new.toFixed(2));
                        } else {
                            $('#team1_betBM_count_new').addClass('tolose text-color-red');
                            $('#team1_betBM_count_new').removeClass('towin text-color-green');
                            $('#team1_betBM_count_new').show();
                            $('#team1_betBM_count_new').text(fval_new.toFixed(2));
                        }

                        if ($('#team3').val() != '') {
                            fval_new = '';
                            var old_value_draw = $('#draw_BM_total').text();
                            if (old_value_draw != '') {
                                fval_new = parseFloat(old_value_draw) + parseFloat(fval);
                            }
                            if (fval_new > 0) {
                                $('#draw_betBM_count_new').removeClass('tolose text-color-red');
                                $('#draw_betBM_count_new').addClass('towin text-color-green');
                                $('#draw_betBM_count_new').show();
                                $('#draw_betBM_count_new').text(fval_new.toFixed(2));
                            } else {
                                $('#draw_betBM_count_new').addClass('tolose text-color-red');
                                $('#draw_betBM_count_new').removeClass('towin text-color-green');
                                $('#draw_betBM_count_new').show();
                                $('#draw_betBM_count_new').text(fval_new.toFixed(2));
                            }
                        }
                    } else if (old_team3.trim() == team.trim()) {
                        fval_new = '';
                        var old_value_team1 = $('#team1_BM_total').text();
                        if (old_value_team1 != '') {
                            fval_new = parseFloat(old_value_team1) + parseFloat(fval);
                        }
                        if (fval_new > 0) {
                            $('#team1_betBM_count_new').show();
                            $('#team1_betBM_count_new').text(fval_new.toFixed(2));
                            $('#team1_betBM_count_new').removeClass('tolose text-color-red');
                            $('#team1_betBM_count_new').addClass('towin text-color-green');
                        } else {
                            $('#team1_betBM_count_new').show();
                            $('#team1_betBM_count_new').text(fval_new.toFixed(2));
                            $('#team1_betBM_count_new').addClass('tolose text-color-red');
                            $('#team1_betBM_count_new').removeClass('towin text-color-green');
                        }
                        fval_new = '';
                        var old_value_team2 = $('#team2_BM_total').text();
                        if (old_value_team2 != '') {
                            fval_new = parseFloat(old_value_team2) + parseFloat(fval);
                        }
                        if (fval_new > 0) {
                            $('#team2_betBM_count_new').removeClass('tolose text-color-red');
                            $('#team2_betBM_count_new').addClass('towin text-color-green');
                            $('#team2_betBM_count_new').show();
                            $('#team2_betBM_count_new').text(fval_new.toFixed(2));
                        } else {
                            $('#team2_betBM_count_new').addClass('tolose text-color-red');
                            $('#team2_betBM_count_new').removeClass('towin text-color-green');
                            $('#team2_betBM_count_new').show();
                            $('#team2_betBM_count_new').text(fval_new.toFixed(2));
                        }

                        if ($('#team3').val() != '') {
                            var old_value = $('#draw_BM_total').text();
                            if (old_value != '') {
                                finalValue = parseFloat(old_value) - parseFloat(finalValue);
                            }
                            if (finalValue > 0) {
                                $('#draw_betBM_count_new').addClass('towin text-color-green');
                                $('#draw_betBM_count_new').removeClass('tolose text-color-red');
                                $('#draw_betBM_count_new').show();
                                $('#draw_betBM_count_new').text(finalValue.toFixed(2));
                            } else {
                                $('#draw_betBM_count_new').removeClass('towin text-color-green');
                                $('#draw_betBM_count_new').addClass('tolose text-color-red');
                                $('#draw_betBM_count_new').show();
                                $('#draw_betBM_count_new').text(finalValue.toFixed(2));
                            }
                        }
                    }
                }
            }
        });
        /*$(document).on("click", '.dynamicoddsval', function(event) {
		var $myInput = $('#inputStake_mobile');
		$myInput.val($myInput.val()+$(this).data("val"));

	});
	*/

        $(document).on("click", '.mobileodds_detail', function (event) {
            $('#inputStake_mobile').val($(this).data("odd"));
        });
        $('.remove_new_bet').click(function () {
            $(".showForm").hide();
        });

        //$(".match_odd_mobile").click(function() {
        $(document).on("click", '.match_odd_mobile', function (event) {

            var $myInput = $('#inputStake_mobile');
            if ($(this).hasClass('mobileodds_detail')) {
                if (($(this).data("val") == '0' || $(this).data("val") == '00') && $myInput.val() == '') {
                } else
                    $myInput.val($(this).data("val"));
            } else {
                if (($(this).data("val") == '0' || $(this).data("val") == '00') && $myInput.val() == '') {
                } else
                    $myInput.val($myInput.val() + $(this).data("val"));
            }
            //var oddval = $(this).data("val");
            //$('#inputStake_mobile').val(oddval);

            var oddval = $('#inputStake_mobile').val();
            var fval = $('#inputStake_mobile').val();
            var matchVal = $("#mobile_odds").val();
            $('#inputStake').val(oddval);

            finalValue = '';
            if ($('#betTypeAdd').val() == 'ODDS' && $('#betTypeAdd').val() != 'SESSION') {
                matchVal = parseFloat(matchVal) - parseInt(1);
                finalValue = parseFloat(oddval) * parseFloat(matchVal);
            } else if ($('#betTypeAdd').val() == 'BOOKMAKER' && $('#betTypeAdd').val() != 'SESSION') {
                finalValue = (parseFloat(oddval) * parseFloat(matchVal)) / parseFloat(100);
            }
            if ($('#betTypeAdd').val() != 'SESSION')
                $('.profil').html(finalValue.toFixed(2));
            var team = $('#teamNameBet').val();
            var old_team1 = $('#team1').val();
            var old_team2 = $('#team2').val();
            var old_team3 = $('#team3').val();

            if ($('#betTypeAdd').val() == 'ODDS' && $('#betTypeAdd').val() != 'SESSION') {
                if ($('#betSide').val() == 'back') {
                    if (old_team1.trim() == team.trim()) {
                        $('#team1_bet_count_new').show();
                        var old_value = $('#team1_total').text();
                        if (old_value != '') {
                            finalValue = parseFloat(old_value) + parseFloat(finalValue);
                        }
                        if (finalValue > 0) {
                            $('#team1_bet_count_new').text(finalValue.toFixed(2));
                            $('#team1_bet_count_new').removeClass('tolose text-color-red');
                            $('#team1_bet_count_new').addClass('towin text-color-green');
                        } else {
                            $('#team1_bet_count_new').text(parseFloat(finalValue).toFixed(2));
                            $('#team1_bet_count_new').addClass('tolose text-color-red');
                            $('#team1_bet_count_new').removeClass('towin text-color-green');
                        }
                        fval_new = '';
                        var old_value_team2 = $('#team2_total').text();
                        if (old_value_team2 != '') {
                            fval_new = parseFloat(old_value_team2) - parseFloat(fval);
                        }
                        if (fval_new > 0) {
                            $('#team2_bet_count_new').removeClass('tolose text-color-red');
                            $('#team2_bet_count_new').addClass('towin text-color-green');
                            $('#team2_bet_count_new').show();
                            $('#team2_bet_count_new').text(fval_new);
                        } else {
                            $('#team2_bet_count_new').addClass('tolose text-color-red');
                            $('#team2_bet_count_new').removeClass('towin text-color-green');
                            $('#team2_bet_count_new').show();
                            $('#team2_bet_count_new').text(fval_new);
                        }
                        var old_value_team3 = $('#draw_total').text();
                        if ($('#team3').val() != '') {
                            if (old_value_team3 != '') {
                                fval = parseFloat(old_value_team3) - parseFloat(fval);
                            }
                            if (fval > 0) {
                                $('#draw_bet_count_new').removeClass('tolose text-color-red');
                                $('#draw_bet_count_new').addClass('towin text-color-green');
                                $('#draw_bet_count_new').show();
                                $('#draw_bet_count_new').text(fval);
                            } else {
                                $('#draw_bet_count_new').addClass('tolose text-color-red');
                                $('#draw_bet_count_new').removeClass('towin text-color-green');
                                $('#draw_bet_count_new').show();
                                $('#draw_bet_count_new').text(fval);
                            }
                        }
                    } else if (old_team2.trim() == team.trim()) {
                        $('#team2_bet_count_new').show();

                        var old_value = $('#team2_total').text();
                        if (old_value != '') {
                            finalValue = parseFloat(old_value) + parseFloat(finalValue);
                        }
                        if (finalValue > 0) {
                            $('#team2_bet_count_new').text(finalValue.toFixed(2));
                            $('#team2_bet_count_new').removeClass('tolose text-color-red');
                            $('#team2_bet_count_new').addClass('towin text-color-green');
                        } else {
                            $('#team2_bet_count_new').text(finalValue.toFixed(2));
                            $('#team2_bet_count_new').removeClass('tolose text-color-red');
                            $('#team2_bet_count_new').addClass('towin text-color-green');
                        }
                        fval_new = '';
                        var old_value_team1 = $('#team1_total').text();
                        if (old_value_team1 != '') {
                            fval_new = parseFloat(old_value_team1) - parseFloat(fval);
                        }
                        if (fval_new > 0) {
                            $('#team1_bet_count_new').removeClass('tolose text-color-red');
                            $('#team1_bet_count_new').addClass('towin text-color-green');
                            $('#team1_bet_count_new').show();
                            $('#team1_bet_count_new').text(fval_new.toFixed(2));
                        } else {
                            $('#team1_bet_count_new').addClass('tolose text-color-red');
                            $('#team1_bet_count_new').removeClass('towin text-color-green');
                            $('#team1_bet_count_new').show();
                            $('#team1_bet_count_new').text(fval_new.toFixed(2));
                        }
                        var old_value_team3 = $('#draw_total').text();
                        if ($('#team3').val() != '') {
                            if (old_value_team3 != '') {
                                fval = parseFloat(old_value_team3) - parseFloat(fval);
                            }
                            if (fval > 0) {
                                $('#draw_bet_count_new').removeClass('tolose text-color-red');
                                $('#draw_bet_count_new').addClass('towin text-color-green');
                                $('#draw_bet_count_new').show();
                                $('#draw_bet_count_new').text(fval.toFixed(2));
                            } else {
                                $('#draw_bet_count_new').addClass('tolose text-color-red');
                                $('#draw_bet_count_new').removeClass('towin text-color-green');
                                $('#draw_bet_count_new').show();
                                $('#draw_bet_count_new').text(fval.toFixed(2));
                            }
                        }
                    } else if (old_team3.trim() == team.trim()) {
                        if ($('#team3').val() != '') {
                            $('#draw_bet_count_new').show();
                            var old_value = $('#draw_total').text();
                            if (old_value != '') {
                                finalValue = parseFloat(old_value) + parseFloat(finalValue);
                            }
                            if (finalValue > 0) {
                                $('#draw_bet_count_new').removeClass('tolose text-color-red');
                                $('#draw_bet_count_new').addClass('towin text-color-green');
                                $('#draw_bet_count_new').show();
                                $('#draw_bet_count_new').text(finalValue.toFixed(2));
                            } else {
                                $('#draw_bet_count_new').addClass('tolose text-color-red');
                                $('#draw_bet_count_new').removeClass('towin text-color-green');
                                $('#draw_bet_count_new').show();
                                $('#draw_bet_count_new').text(finalValue.toFixed(2));
                            }

                            fval_new = '';
                            var old_value_team1 = $('#team1_total').text();
                            if (old_value_team1 != '') {
                                fval_new = parseFloat(old_value_team1) - parseFloat(fval);
                            }
                            if (fval_new > 0) {
                                $('#team1_bet_count_new').show();
                                $('#team1_bet_count_new').removeClass('tolose text-color-red');
                                $('#team1_bet_count_new').addClass('towin text-color-green');
                                $('#team1_bet_count_new').text(fval_new.toFixed(2));
                            } else {
                                $('#team1_bet_count_new').show();
                                $('#team1_bet_count_new').addClass('tolose text-color-red');
                                $('#team1_bet_count_new').removeClass('towin text-color-green');
                                $('#team1_bet_count_new').text(fval_new.toFixed(2));
                            }

                            fval_new = '';
                            var old_value_team2 = $('#team2_total').text();
                            if (old_value_team2 != '') {
                                fval_new = parseFloat(old_value_team2) - parseFloat(fval);
                            }
                            if (fval_new > 0) {
                                $('#team2_bet_count_new').removeClass('tolose text-color-red');
                                $('#team2_bet_count_new').addClass('towin text-color-green');
                                $('#team2_bet_count_new').show();
                                $('#team2_bet_count_new').text(fval_new.toFixed(2));
                            } else {
                                $('#team2_bet_count_new').addClass('tolose text-color-red');
                                $('#team2_bet_count_new').removeClass('towin text-color-green');
                                $('#team2_bet_count_new').show();
                                $('#team2_bet_count_new').text(fval_new.toFixed(2));
                            }
                        }
                    }
                } else if ($('#betSide').val() == 'lay') {
                    if (old_team1.trim() == team.trim()) {
                        $('#team1_bet_count_new').show();
                        var old_value = $('#team1_total').text();
                        if (old_value != '') {
                            finalValue = parseFloat(old_value) - parseFloat(finalValue);
                        }
                        if (finalValue > 0) {
                            $('#team1_bet_count_new').show();
                            $('#team1_bet_count_new').text(finalValue.toFixed(2));
                            $('#team1_bet_count_new').removeClass('tolose text-color-red');
                            $('#team1_bet_count_new').addClass('towin text-color-green');
                        } else {
                            $('#team1_bet_count_new').show();
                            $('#team1_bet_count_new').text(finalValue.toFixed(2));
                            $('#team1_bet_count_new').addClass('tolose text-color-red');
                            $('#team1_bet_count_new').removeClass('towin text-color-green');
                        }
                        fval_new = '';
                        var old_value_team2 = $('#team2_total').text();
                        if (old_value_team2 != '') {
                            fval_new = parseFloat(old_value_team2) + parseFloat(fval);
                        }
                        if (fval_new > 0) {
                            $('#team2_bet_count_new').removeClass('tolose text-color-red');
                            $('#team2_bet_count_new').addClass('towin text-color-green');
                            $('#team2_bet_count_new').show();
                            $('#team2_bet_count_new').text(fval_new.toFixed(2));
                        } else {
                            $('#team2_bet_count_new').addClass('tolose text-color-red');
                            $('#team2_bet_count_new').removeClass('towin text-color-green');
                            $('#team2_bet_count_new').show();
                            $('#team2_bet_count_new').text(fval_new.toFixed(2));
                        }
                        if ($('#team3').val() != '') {
                            var old_value_team3 = $('#draw_total').text();
                            if ($('#team3').val() != '') {
                                if (old_value_team3 != '') {
                                    fval = parseFloat(old_value_team3) + parseFloat(fval);
                                }
                                if (fval > 0) {
                                    $('#draw_bet_count_new').removeClass('tolose text-color-red');
                                    $('#draw_bet_count_new').addClass('towin text-color-green');
                                    $('#draw_bet_count_new').show();
                                    $('#draw_bet_count_new').text(fval.toFixed(2));
                                } else {
                                    $('#draw_bet_count_new').addClass('tolose text-color-red');
                                    $('#draw_bet_count_new').removeClass('towin text-color-green');
                                    $('#draw_bet_count_new').show();
                                    $('#draw_bet_count_new').text(fval.toFixed(2));
                                }
                            }
                        }
                    } else if (old_team2.trim() == team.trim()) {
                        var old_value = $('#team2_total').text();
                        if (old_value != '') {
                            finalValue = parseFloat(old_value) - parseFloat(finalValue);
                        }
                        if (finalValue > 0) {
                            $('#team2_bet_count_new').show();
                            $('#team2_bet_count_new').text(finalValue.toFixed(2));
                            $('#team2_bet_count_new').removeClass('tolose text-color-red');
                            $('#team2_bet_count_new').addClass('towin text-color-green');
                        } else {
                            $('#team2_bet_count_new').show();
                            $('#team2_bet_count_new').text(finalValue.toFixed(2));
                            $('#team2_bet_count_new').addClass('tolose text-color-red');
                            $('#team2_bet_count_new').removeClass('towin text-color-green');
                        }
                        fval_new = '';
                        var old_value_team1 = $('#team1_total').text();
                        if (old_value_team1 != '') {
                            fval_new = parseFloat(old_value_team1) + parseFloat(fval);
                        }
                        if (fval_new > 0) {
                            $('#team1_bet_count_new').removeClass('tolose text-color-red');
                            $('#team1_bet_count_new').addClass('towin text-color-green');
                            $('#team1_bet_count_new').show();
                            $('#team1_bet_count_new').text(fval_new.toFixed(2));
                        } else {
                            $('#team1_bet_count_new').addClass('tolose text-color-red');
                            $('#team1_bet_count_new').removeClass('towin text-color-green');
                            $('#team1_bet_count_new').show();
                            $('#team1_bet_count_new').text(fval_new.toFixed(2));
                        }

                        if ($('#team3').val() != '') {
                            fval_new = '';
                            var old_value_draw = $('#draw_total').text();
                            if (old_value_draw != '') {
                                fval_new = parseFloat(old_value_draw) + parseFloat(fval);
                            }
                            if (fval_new > 0) {
                                $('#draw_bet_count_new').removeClass('tolose text-color-red');
                                $('#draw_bet_count_new').addClass('towin text-color-green');
                                $('#draw_bet_count_new').show();
                                $('#draw_bet_count_new').text(fval_new.toFixed(2));
                            } else {
                                $('#draw_bet_count_new').addClass('tolose text-color-red');
                                $('#draw_bet_count_new').removeClass('towin text-color-green');
                                $('#draw_bet_count_new').show();
                                $('#draw_bet_count_new').text(fval_new.toFixed(2));
                            }
                        }
                    } else if (old_team3.trim() == team.trim()) {
                        fval_new = '';
                        var old_value_team1 = $('#team1_total').text();
                        if (old_value_team1 != '') {
                            fval_new = parseFloat(old_value_team1) + parseFloat(fval);
                        }
                        if (fval_new > 0) {
                            $('#team1_bet_count_new').show();
                            $('#team1_bet_count_new').text(fval_new.toFixed(2));
                            $('#team1_bet_count_new').removeClass('tolose text-color-red');
                            $('#team1_bet_count_new').addClass('towin text-color-green');
                        } else {
                            $('#team1_bet_count_new').show();
                            $('#team1_bet_count_new').text(fval_new.toFixed(2));
                            $('#team1_bet_count_new').addClass('tolose text-color-red');
                            $('#team1_bet_count_new').removeClass('towin text-color-green');
                        }
                        fval_new = '';
                        var old_value_team2 = $('#team2_total').text();
                        if (old_value_team2 != '') {
                            fval_new = parseFloat(old_value_team2) + parseFloat(fval);
                        }
                        if (fval_new > 0) {
                            $('#team2_bet_count_new').removeClass('tolose text-color-red');
                            $('#team2_bet_count_new').addClass('towin text-color-green');
                            $('#team2_bet_count_new').show();
                            $('#team2_bet_count_new').text(fval_new.toFixed(2));
                        } else {
                            $('#team2_bet_count_new').addClass('tolose text-color-red');
                            $('#team2_bet_count_new').removeClass('towin text-color-green');
                            $('#team2_bet_count_new').show();
                            $('#team2_bet_count_new').text(fval_new.toFixed(2));
                        }

                        if ($('#team3').val() != '') {
                            var old_value = $('#draw_total').text();
                            if (old_value != '') {
                                finalValue = parseFloat(old_value) - parseFloat(finalValue);
                            }
                            if (finalValue > 0) {
                                $('#draw_bet_count_new').addClass('towin text-color-green');
                                $('#draw_bet_count_new').removeClass('tolose text-color-red');
                                $('#draw_bet_count_new').show();
                                $('#draw_bet_count_new').text(finalValue.toFixed(2));
                            } else {
                                $('#draw_bet_count_new').removeClass('towin text-color-green');
                                $('#draw_bet_count_new').addClass('tolose text-color-red');
                                $('#draw_bet_count_new').show();
                                $('#draw_bet_count_new').text(finalValue.toFixed(2));
                            }
                        }
                    }
                }
            } else if ($('#betTypeAdd').val() == 'BOOKMAKER' && $('#betTypeAdd').val() != 'SESSION') {
                if ($('#betSide').val() == 'back') {
                    if (old_team1.trim() == team.trim()) {
                        $('#team1_betBM_count_new').show();
                        var old_value = $('#team1_BM_total').text();
                        if (old_value != '') {
                            finalValue = parseFloat(old_value) + parseFloat(finalValue);
                        }
                        if (finalValue > 0) {
                            $('#team1_betBM_count_new').text(finalValue.toFixed(2));
                            $('#team1_betBM_count_new').removeClass('tolose text-color-red');
                            $('#team1_betBM_count_new').addClass('towin text-color-green');
                        } else {
                            $('#team1_betBM_count_new').text(parseFloat(finalValue).toFixed(2));
                            $('#team1_betBM_count_new').addClass('tolose text-color-red');
                            $('#team1_betBM_count_new').removeClass('towin text-color-green');
                        }
                        fval_new = '';
                        var old_value_team2 = $('#team2_BM_total').text();
                        if (old_value_team2 != '') {
                            fval_new = parseFloat(old_value_team2) - parseFloat(fval);
                        }
                        if (fval_new > 0) {
                            $('#team2_betBM_count_new').removeClass('tolose text-color-red');
                            $('#team2_betBM_count_new').addClass('towin text-color-green');
                            $('#team2_betBM_count_new').show();
                            $('#team2_beBMt_count_new').text(fval_new);
                        } else {
                            $('#team2_betBM_count_new').addClass('tolose text-color-red');
                            $('#team2_betBM_count_new').removeClass('towin text-color-green');
                            $('#team2_betBM_count_new').show();
                            $('#team2_betBM_count_new').text(fval_new);
                        }
                        var old_value_team3 = $('#draw_BM_total').text();
                        if ($('#team3').val() != '') {
                            if (old_value_team3 != '') {
                                fval = parseFloat(old_value_team3) - parseFloat(fval);
                            }
                            if (fval > 0) {
                                $('#draw_betBM_count_new').removeClass('tolose text-color-red');
                                $('#draw_betBM_count_new').addClass('towin text-color-green');
                                $('#draw_betBM_count_new').show();
                                $('#draw_betBM_count_new').text(fval);
                            } else {
                                $('#draw_betBM_count_new').addClass('tolose text-color-red');
                                $('#draw_betBM_count_new').removeClass('towin text-color-green');
                                $('#draw_betBM_count_new').show();
                                $('#draw_betBM_count_new').text(fval);
                            }
                        }
                    } else if (old_team2.trim() == team.trim()) {
                        $('#team2_betBM_count_new').show();

                        var old_value = $('#team2_BM_total').text();
                        if (old_value != '') {
                            finalValue = parseFloat(old_value) + parseFloat(finalValue);
                        }
                        if (finalValue > 0) {
                            $('#team2_betBM_count_new').text(finalValue.toFixed(2));
                            $('#team2_betBM_count_new').removeClass('tolose text-color-red');
                            $('#team2_betBM_count_new').addClass('towin text-color-green');
                        } else {
                            $('#team2_betBM_count_new').text(finalValue.toFixed(2));
                            $('#team2_betBM_count_new').removeClass('tolose text-color-red');
                            $('#team2_betBM_count_new').addClass('towin text-color-green');
                        }
                        fval_new = '';
                        var old_value_team1 = $('#team1_BM_total').text();
                        if (old_value_team1 != '') {
                            fval_new = parseFloat(old_value_team1) - parseFloat(fval);
                        }
                        if (fval_new > 0) {
                            $('#team1_betBM_count_new').removeClass('tolose text-color-red');
                            $('#team1_betBM_count_new').addClass('towin text-color-green');
                            $('#team1_betBM_count_new').show();
                            $('#team1_betBM_count_new').text(fval_new.toFixed(2));
                        } else {
                            $('#team1_betBM_count_new').addClass('tolose text-color-red');
                            $('#team1_betBM_count_new').removeClass('towin text-color-green');
                            $('#team1_betBM_count_new').show();
                            $('#team1_betBM_count_new').text(fval_new.toFixed(2));
                        }
                        var old_value_team3 = $('#draw_BM_total').text();
                        if ($('#team3').val() != '') {
                            if (old_value_team3 != '') {
                                fval = parseFloat(old_value_team3) - parseFloat(fval);
                            }
                            if (fval > 0) {
                                $('#draw_betBM_count_new').removeClass('tolose text-color-red');
                                $('#draw_betBM_count_new').addClass('towin text-color-green');
                                $('#draw_betBM_count_new').show();
                                $('#draw_betBM_count_new').text(fval.toFixed(2));
                            } else {
                                $('#draw_betBM_count_new').addClass('tolose text-color-red');
                                $('#draw_betBM_count_new').removeClass('towin text-color-green');
                                $('#draw_betBM_count_new').show();
                                $('#draw_betBM_count_new').text(fval.toFixed(2));
                            }
                        }
                    } else if (old_team3.trim() == team.trim()) {
                        if ($('#team3').val() != '') {
                            $('#draw_betBM_count_new').show();
                            var old_value = $('#draw_BM_total').text();
                            if (old_value != '') {
                                finalValue = parseFloat(old_value) + parseFloat(finalValue);
                            }
                            if (finalValue > 0) {
                                $('#draw_betBM_count_new').removeClass('tolose text-color-red');
                                $('#draw_betBM_count_new').addClass('towin text-color-green');
                                $('#draw_betBM_count_new').show();
                                $('#draw_betBM_count_new').text(finalValue.toFixed(2));
                            } else {
                                $('#draw_betBM_count_new').addClass('tolose text-color-red');
                                $('#draw_betBM_count_new').removeClass('towin text-color-green');
                                $('#draw_betBM_count_new').show();
                                $('#draw_betBM_count_new').text(finalValue.toFixed(2));
                            }

                            fval_new = '';
                            var old_value_team1 = $('#team1_total').text();
                            if (old_value_team1 != '') {
                                fval_new = parseFloat(old_value_team1) - parseFloat(fval);
                            }
                            if (fval_new > 0) {
                                $('#team1_betBM_count_new').show();
                                $('#team1_betBM_count_new').removeClass('tolose text-color-red');
                                $('#team1_betBM_count_new').addClass('towin text-color-green');
                                $('#team1_betBM_count_new').text(fval_new.toFixed(2));
                            } else {
                                $('#team1_betBM_count_new').show();
                                $('#team1_betBM_count_new').addClass('tolose text-color-red');
                                $('#team1_betBM_count_new').removeClass('towin text-color-green');
                                $('#team1_betBM_count_new').text(fval_new.toFixed(2));
                            }

                            fval_new = '';
                            var old_value_team2 = $('#team2_BM_total').text();
                            if (old_value_team2 != '') {
                                fval_new = parseFloat(old_value_team2) - parseFloat(fval);
                            }
                            if (fval_new > 0) {
                                $('#team2_betBM_count_new').removeClass('tolose text-color-red');
                                $('#team2_betBM_count_new').addClass('towin text-color-green');
                                $('#team2_betBM_count_new').show();
                                $('#team2_betBM_count_new').text(fval_new.toFixed(2));
                            } else {
                                $('#team2_betBM_count_new').addClass('tolose text-color-red');
                                $('#team2_betBM_count_new').removeClass('towin text-color-green');
                                $('#team2_betBM_count_new').show();
                                $('#team2_betBM_count_new').text(fval_new.toFixed(2));
                            }
                        }
                    }
                } else if ($('#betSide').val() == 'lay') {
                    if (old_team1.trim() == team.trim()) {
                        $('#team1_betBM_count_new').show();
                        var old_value = $('#team1_BM_total').text();
                        if (old_value != '') {
                            finalValue = parseFloat(old_value) - parseFloat(finalValue);
                        }
                        if (finalValue > 0) {
                            $('#team1_betBM_count_new').show();
                            $('#team1_betBM_count_new').text(finalValue.toFixed(2));
                            $('#team1_betBM_count_new').removeClass('tolose text-color-red');
                            $('#team1_betBM_count_new').addClass('towin text-color-green');
                        } else {
                            $('#team1_betBM_count_new').show();
                            $('#team1_betBM_count_new').text(finalValue.toFixed(2));
                            $('#team1_betBM_count_new').addClass('tolose text-color-red');
                            $('#team1_betBM_count_new').removeClass('towin text-color-green');
                        }
                        fval_new = '';
                        var old_value_team2 = $('#team2_BM_total').text();
                        if (old_value_team2 != '') {
                            fval_new = parseFloat(old_value_team2) + parseFloat(fval);
                        }
                        if (fval_new > 0) {
                            $('#team2_betBM_count_new').removeClass('tolose text-color-red');
                            $('#team2_betBM_count_new').addClass('towin text-color-green');
                            $('#team2_betBM_count_new').show();
                            $('#team2_betBM_count_new').text(fval_new.toFixed(2));
                        } else {
                            $('#team2_betBM_count_new').addClass('tolose text-color-red');
                            $('#team2_betBM_count_new').removeClass('towin text-color-green');
                            $('#team2_betBM_count_new').show();
                            $('#team2_betBM_count_new').text(fval_new.toFixed(2));
                        }
                        if ($('#team3').val() != '') {
                            var old_value_team3 = $('#draw_BM_total').text();
                            if ($('#team3').val() != '') {
                                if (old_value_team3 != '') {
                                    fval = parseFloat(old_value_team3) + parseFloat(fval);
                                }
                                if (fval > 0) {
                                    $('#draw_betBM_count_new').removeClass('tolose text-color-red');
                                    $('#draw_betBM_count_new').addClass('towin text-color-green');
                                    $('#draw_betBM_count_new').show();
                                    $('#draw_betBM_count_new').text(fval.toFixed(2));
                                } else {
                                    $('#draw_betBM_count_new').addClass('tolose text-color-red');
                                    $('#draw_betBM_count_new').removeClass('towin text-color-green');
                                    $('#draw_betBM_count_new').show();
                                    $('#draw_betBM_count_new').text(fval.toFixed(2));
                                }
                            }
                        }
                    } else if (old_team2.trim() == team.trim()) {
                        var old_value = $('#team2_BM_total').text();
                        if (old_value != '') {
                            finalValue = parseFloat(old_value) - parseFloat(finalValue);
                        }
                        if (finalValue > 0) {
                            $('#team2_betBM_count_new').show();
                            $('#team2_betBM_count_new').text(finalValue.toFixed(2));
                            $('#team2_betBM_count_new').removeClass('tolose text-color-red');
                            $('#team2_betBM_count_new').addClass('towin text-color-green');
                        } else {
                            $('#team2_betBM_count_new').show();
                            $('#team2_betBM_count_new').text(finalValue.toFixed(2));
                            $('#team2_betBM_count_new').addClass('tolose text-color-red');
                            $('#team2_betBM_count_new').removeClass('towin text-color-green');
                        }
                        fval_new = '';
                        var old_value_team1 = $('#team1_BM_total').text();
                        if (old_value_team1 != '') {
                            fval_new = parseFloat(old_value_team1) + parseFloat(fval);
                        }
                        if (fval_new > 0) {
                            $('#team1_betBM_count_new').removeClass('tolose text-color-red');
                            $('#team1_betBM_count_new').addClass('towin text-color-green');
                            $('#team1_betBM_count_new').show();
                            $('#team1_betBM_count_new').text(fval_new.toFixed(2));
                        } else {
                            $('#team1_betBM_count_new').addClass('tolose text-color-red');
                            $('#team1_betBM_count_new').removeClass('towin text-color-green');
                            $('#team1_betBM_count_new').show();
                            $('#team1_betBM_count_new').text(fval_new.toFixed(2));
                        }

                        if ($('#team3').val() != '') {
                            fval_new = '';
                            var old_value_draw = $('#draw_BM_total').text();
                            if (old_value_draw != '') {
                                fval_new = parseFloat(old_value_draw) + parseFloat(fval);
                            }
                            if (fval_new > 0) {
                                $('#draw_betBM_count_new').removeClass('tolose text-color-red');
                                $('#draw_betBM_count_new').addClass('towin text-color-green');
                                $('#draw_betBM_count_new').show();
                                $('#draw_betBM_count_new').text(fval_new.toFixed(2));
                            } else {
                                $('#draw_betBM_count_new').addClass('tolose text-color-red');
                                $('#draw_betBM_count_new').removeClass('towin text-color-green');
                                $('#draw_betBM_count_new').show();
                                $('#draw_betBM_count_new').text(fval_new.toFixed(2));
                            }
                        }
                    } else if (old_team3.trim() == team.trim()) {
                        fval_new = '';
                        var old_value_team1 = $('#team1_BM_total').text();
                        if (old_value_team1 != '') {
                            fval_new = parseFloat(old_value_team1) + parseFloat(fval);
                        }
                        if (fval_new > 0) {
                            $('#team1_betBM_count_new').show();
                            $('#team1_betBM_count_new').text(fval_new.toFixed(2));
                            $('#team1_betBM_count_new').removeClass('tolose text-color-red');
                            $('#team1_betBM_count_new').addClass('towin text-color-green');
                        } else {
                            $('#team1_betBM_count_new').show();
                            $('#team1_betBM_count_new').text(fval_new.toFixed(2));
                            $('#team1_betBM_count_new').addClass('tolose text-color-red');
                            $('#team1_betBM_count_new').removeClass('towin text-color-green');
                        }
                        fval_new = '';
                        var old_value_team2 = $('#team2_BM_total').text();
                        if (old_value_team2 != '') {
                            fval_new = parseFloat(old_value_team2) + parseFloat(fval);
                        }
                        if (fval_new > 0) {
                            $('#team2_betBM_count_new').removeClass('tolose text-color-red');
                            $('#team2_betBM_count_new').addClass('towin text-color-green');
                            $('#team2_betBM_count_new').show();
                            $('#team2_betBM_count_new').text(fval_new.toFixed(2));
                        } else {
                            $('#team2_betBM_count_new').addClass('tolose text-color-red');
                            $('#team2_betBM_count_new').removeClass('towin text-color-green');
                            $('#team2_betBM_count_new').show();
                            $('#team2_betBM_count_new').text(fval_new.toFixed(2));
                        }

                        if ($('#team3').val() != '') {
                            var old_value = $('#draw_BM_total').text();
                            if (old_value != '') {
                                finalValue = parseFloat(old_value) - parseFloat(finalValue);
                            }
                            if (finalValue > 0) {
                                $('#draw_betBM_count_new').addClass('towin text-color-green');
                                $('#draw_betBM_count_new').removeClass('tolose text-color-red');
                                $('#draw_betBM_count_new').show();
                                $('#draw_betBM_count_new').text(finalValue.toFixed(2));
                            } else {
                                $('#draw_betBM_count_new').removeClass('towin text-color-green');
                                $('#draw_betBM_count_new').addClass('tolose text-color-red');
                                $('#draw_betBM_count_new').show();
                                $('#draw_betBM_count_new').text(finalValue.toFixed(2));
                            }
                        }
                    }
                }
            }
        });

        $(".match_odd").click(function () {
            var oddval = $(this).data("odd");
            $('#inputStake').val(oddval);
            var fval = $('#inputStake').val();
            var matchVal = $("#odds_val").val();

            finalValue = '';
            if ($('#betTypeAdd').val() == 'ODDS' && $('#betTypeAdd').val() != 'SESSION') {
                matchVal = parseFloat(matchVal) - parseInt(1);
                finalValue = parseFloat(oddval) * parseFloat(matchVal);
            } else if ($('#betTypeAdd').val() == 'BOOKMAKER' && $('#betTypeAdd').val() != 'SESSION') {
                finalValue = (parseFloat(oddval) * parseFloat(matchVal)) / parseFloat(100);
            } else if ($('#betTypeAdd').val() == 'SESSION') {
                matchVal = $("#odds_volume").val();

                finalValue = (parseFloat(oddval) * parseFloat(matchVal)) / parseFloat(100);
            }

            $('.profil').html(finalValue.toFixed(2));

            var team = $('#teamNameBet').val();
            var old_team1 = $('#team1').val();
            var old_team2 = $('#team2').val();
            var old_team3 = $('#team3').val();
            var odds_position = $('#odds_position').val();

            if ($('#betTypeAdd').val() == 'ODDS' && $('#betTypeAdd').val() != 'SESSION') {
                if ($('#betSide').val() == 'back') {
                    if (old_team1.trim() == team.trim()) {
                        $('#team1_bet_count_new').show();
                        var old_value = $('#team1_total').text();
                        if (old_value != '') {
                            finalValue = parseFloat(old_value) + parseFloat(finalValue);
                        }
                        if (finalValue > 0) {
                            $('#team1_bet_count_new').text(finalValue.toFixed(2));
                            $('#team1_bet_count_new').removeClass('tolose text-color-red');
                            $('#team1_bet_count_new').addClass('towin text-color-green');
                        } else {
                            $('#team1_bet_count_new').text(parseFloat(finalValue).toFixed(2));
                            $('#team1_bet_count_new').addClass('tolose text-color-red');
                            $('#team1_bet_count_new').removeClass('towin text-color-green');
                        }
                        fval_new = '';
                        var old_value_team2 = $('#team2_total').text();
                        if (old_value_team2 != '') {
                            fval_new = parseFloat(old_value_team2) - parseFloat(fval);
                        }
                        if (fval_new > 0) {
                            $('#team2_bet_count_new').removeClass('tolose text-color-red');
                            $('#team2_bet_count_new').addClass('towin text-color-green');
                            $('#team2_bet_count_new').show();
                            $('#team2_bet_count_new').text(fval_new);
                        } else {
                            $('#team2_bet_count_new').addClass('tolose text-color-red');
                            $('#team2_bet_count_new').removeClass('towin text-color-green');
                            $('#team2_bet_count_new').show();
                            $('#team2_bet_count_new').text(fval_new);
                        }
                        var old_value_team3 = $('#draw_total').text();
                        if ($('#team3').val() != '') {
                            if (old_value_team3 != '') {
                                fval = parseFloat(old_value_team3) - parseFloat(fval);
                            }
                            if (fval > 0) {
                                $('#draw_bet_count_new').removeClass('tolose text-color-red');
                                $('#draw_bet_count_new').addClass('towin text-color-green');
                                $('#draw_bet_count_new').show();
                                $('#draw_bet_count_new').text(fval);
                            } else {
                                $('#draw_bet_count_new').addClass('tolose text-color-red');
                                $('#draw_bet_count_new').removeClass('towin text-color-green');
                                $('#draw_bet_count_new').show();
                                $('#draw_bet_count_new').text(fval);
                            }
                        }
                    } else if (old_team2.trim() == team.trim()) {
                        $('#team2_bet_count_new').show();

                        var old_value = $('#team2_total').text();
                        if (old_value != '') {
                            finalValue = parseFloat(old_value) + parseFloat(finalValue);
                        }
                        if (finalValue > 0) {
                            $('#team2_bet_count_new').text(finalValue.toFixed(2));
                            $('#team2_bet_count_new').removeClass('tolose text-color-red');
                            $('#team2_bet_count_new').addClass('towin text-color-green');
                        } else {
                            $('#team2_bet_count_new').text(finalValue.toFixed(2));
                            $('#team2_bet_count_new').removeClass('tolose text-color-red');
                            $('#team2_bet_count_new').addClass('towin text-color-green');
                        }
                        fval_new = '';
                        var old_value_team1 = $('#team1_total').text();
                        if (old_value_team1 != '') {
                            fval_new = parseFloat(old_value_team1) - parseFloat(fval);
                        }
                        if (fval_new > 0) {
                            $('#team1_bet_count_new').removeClass('tolose text-color-red');
                            $('#team1_bet_count_new').addClass('towin text-color-green');
                            $('#team1_bet_count_new').show();
                            $('#team1_bet_count_new').text(fval_new.toFixed(2));
                        } else {
                            $('#team1_bet_count_new').addClass('tolose text-color-red');
                            $('#team1_bet_count_new').removeClass('towin text-color-green');
                            $('#team1_bet_count_new').show();
                            $('#team1_bet_count_new').text(fval_new.toFixed(2));
                        }
                        var old_value_team3 = $('#draw_total').text();
                        if ($('#team3').val() != '') {
                            if (old_value_team3 != '') {
                                fval = parseFloat(old_value_team3) - parseFloat(fval);
                            }
                            if (fval > 0) {
                                $('#draw_bet_count_new').removeClass('tolose text-color-red');
                                $('#draw_bet_count_new').addClass('towin text-color-green');
                                $('#draw_bet_count_new').show();
                                $('#draw_bet_count_new').text(fval.toFixed(2));
                            } else {
                                $('#draw_bet_count_new').addClass('tolose text-color-red');
                                $('#draw_bet_count_new').removeClass('towin text-color-green');
                                $('#draw_bet_count_new').show();
                                $('#draw_bet_count_new').text(fval.toFixed(2));
                            }
                        }
                    } else if (old_team3.trim() == team.trim()) {
                        if ($('#team3').val() != '') {
                            $('#draw_bet_count_new').show();
                            var old_value = $('#draw_total').text();
                            if (old_value != '') {
                                finalValue = parseFloat(old_value) + parseFloat(finalValue);
                            }
                            if (finalValue > 0) {
                                $('#draw_bet_count_new').removeClass('tolose text-color-red');
                                $('#draw_bet_count_new').addClass('towin text-color-green');
                                $('#draw_bet_count_new').show();
                                $('#draw_bet_count_new').text(finalValue.toFixed(2));
                            } else {
                                $('#draw_bet_count_new').addClass('tolose text-color-red');
                                $('#draw_bet_count_new').removeClass('towin text-color-green');
                                $('#draw_bet_count_new').show();
                                $('#draw_bet_count_new').text(finalValue.toFixed(2));
                            }

                            fval_new = '';
                            var old_value_team1 = $('#team1_total').text();
                            if (old_value_team1 != '') {
                                fval_new = parseFloat(old_value_team1) - parseFloat(fval);
                            }
                            if (fval_new > 0) {
                                $('#team1_bet_count_new').show();
                                $('#team1_bet_count_new').removeClass('tolose text-color-red');
                                $('#team1_bet_count_new').addClass('towin text-color-green');
                                $('#team1_bet_count_new').text(fval_new.toFixed(2));
                            } else {
                                $('#team1_bet_count_new').show();
                                $('#team1_bet_count_new').addClass('tolose text-color-red');
                                $('#team1_bet_count_new').removeClass('towin text-color-green');
                                $('#team1_bet_count_new').text(fval_new.toFixed(2));
                            }

                            fval_new = '';
                            var old_value_team2 = $('#team2_total').text();
                            if (old_value_team2 != '') {
                                fval_new = parseFloat(old_value_team2) - parseFloat(fval);
                            }
                            if (fval_new > 0) {
                                $('#team2_bet_count_new').removeClass('tolose text-color-red');
                                $('#team2_bet_count_new').addClass('towin text-color-green');
                                $('#team2_bet_count_new').show();
                                $('#team2_bet_count_new').text(fval_new.toFixed(2));
                            } else {
                                $('#team2_bet_count_new').addClass('tolose text-color-red');
                                $('#team2_bet_count_new').removeClass('towin text-color-green');
                                $('#team2_bet_count_new').show();
                                $('#team2_bet_count_new').text(fval_new.toFixed(2));
                            }
                        }
                    }
                } else if ($('#betSide').val() == 'lay') {
                    if (old_team1.trim() == team.trim()) {
                        $('#team1_bet_count_new').show();
                        var old_value = $('#team1_total').text();
                        if (old_value != '') {
                            finalValue = parseFloat(old_value) - parseFloat(finalValue);
                        }
                        if (finalValue > 0) {
                            $('#team1_bet_count_new').show();
                            $('#team1_bet_count_new').text(finalValue.toFixed(2));
                            $('#team1_bet_count_new').removeClass('tolose text-color-red');
                            $('#team1_bet_count_new').addClass('towin text-color-green');
                        } else {
                            $('#team1_bet_count_new').show();
                            $('#team1_bet_count_new').text(finalValue.toFixed(2));
                            $('#team1_bet_count_new').addClass('tolose text-color-red');
                            $('#team1_bet_count_new').removeClass('towin text-color-green');
                        }
                        fval_new = '';
                        var old_value_team2 = $('#team2_total').text();
                        if (old_value_team2 != '') {
                            fval_new = parseFloat(old_value_team2) + parseFloat(fval);
                        }
                        if (fval_new > 0) {
                            $('#team2_bet_count_new').removeClass('tolose text-color-red');
                            $('#team2_bet_count_new').addClass('towin text-color-green');
                            $('#team2_bet_count_new').show();
                            $('#team2_bet_count_new').text(fval_new.toFixed(2));
                        } else {
                            $('#team2_bet_count_new').addClass('tolose text-color-red');
                            $('#team2_bet_count_new').removeClass('towin text-color-green');
                            $('#team2_bet_count_new').show();
                            $('#team2_bet_count_new').text(fval_new.toFixed(2));
                        }
                        if ($('#team3').val() != '') {
                            var old_value_team3 = $('#draw_total').text();
                            if ($('#team3').val() != '') {
                                if (old_value_team3 != '') {
                                    fval = parseFloat(old_value_team3) + parseFloat(fval);
                                }
                                if (fval > 0) {
                                    $('#draw_bet_count_new').removeClass('tolose text-color-red');
                                    $('#draw_bet_count_new').addClass('towin text-color-green');
                                    $('#draw_bet_count_new').show();
                                    $('#draw_bet_count_new').text(fval.toFixed(2));
                                } else {
                                    $('#draw_bet_count_new').addClass('tolose text-color-red');
                                    $('#draw_bet_count_new').removeClass('towin text-color-green');
                                    $('#draw_bet_count_new').show();
                                    $('#draw_bet_count_new').text(fval.toFixed(2));
                                }
                            }
                        }
                    } else if (old_team2.trim() == team.trim()) {
                        var old_value = $('#team2_total').text();
                        if (old_value != '') {
                            finalValue = parseFloat(old_value) - parseFloat(finalValue);
                        }
                        if (finalValue > 0) {
                            $('#team2_bet_count_new').show();
                            $('#team2_bet_count_new').text(finalValue.toFixed(2));
                            $('#team2_bet_count_new').removeClass('tolose text-color-red');
                            $('#team2_bet_count_new').addClass('towin text-color-green');
                        } else {
                            $('#team2_bet_count_new').show();
                            $('#team2_bet_count_new').text(finalValue.toFixed(2));
                            $('#team2_bet_count_new').addClass('tolose text-color-red');
                            $('#team2_bet_count_new').removeClass('towin text-color-green');
                        }
                        fval_new = '';
                        var old_value_team1 = $('#team1_total').text();
                        if (old_value_team1 != '') {
                            fval_new = parseFloat(old_value_team1) + parseFloat(fval);
                        }
                        if (fval_new > 0) {
                            $('#team1_bet_count_new').removeClass('tolose text-color-red');
                            $('#team1_bet_count_new').addClass('towin text-color-green');
                            $('#team1_bet_count_new').show();
                            $('#team1_bet_count_new').text(fval_new.toFixed(2));
                        } else {
                            $('#team1_bet_count_new').addClass('tolose text-color-red');
                            $('#team1_bet_count_new').removeClass('towin text-color-green');
                            $('#team1_bet_count_new').show();
                            $('#team1_bet_count_new').text(fval_new.toFixed(2));
                        }

                        if ($('#team3').val() != '') {
                            fval_new = '';
                            var old_value_draw = $('#draw_total').text();
                            if (old_value_draw != '') {
                                fval_new = parseFloat(old_value_draw) + parseFloat(fval);
                            }
                            if (fval_new > 0) {
                                $('#draw_bet_count_new').removeClass('tolose text-color-red');
                                $('#draw_bet_count_new').addClass('towin text-color-green');
                                $('#draw_bet_count_new').show();
                                $('#draw_bet_count_new').text(fval_new.toFixed(2));
                            } else {
                                $('#draw_bet_count_new').addClass('tolose text-color-red');
                                $('#draw_bet_count_new').removeClass('towin text-color-green');
                                $('#draw_bet_count_new').show();
                                $('#draw_bet_count_new').text(fval_new.toFixed(2));
                            }
                        }
                    } else if (old_team3.trim() == team.trim()) {
                        fval_new = '';
                        var old_value_team1 = $('#team1_total').text();
                        if (old_value_team1 != '') {
                            fval_new = parseFloat(old_value_team1) + parseFloat(fval);
                        }
                        if (fval_new > 0) {
                            $('#team1_bet_count_new').show();
                            $('#team1_bet_count_new').text(fval_new.toFixed(2));
                            $('#team1_bet_count_new').removeClass('tolose text-color-red');
                            $('#team1_bet_count_new').addClass('towin text-color-green');
                        } else {
                            $('#team1_bet_count_new').show();
                            $('#team1_bet_count_new').text(fval_new.toFixed(2));
                            $('#team1_bet_count_new').addClass('tolose text-color-red');
                            $('#team1_bet_count_new').removeClass('towin text-color-green');
                        }
                        fval_new = '';
                        var old_value_team2 = $('#team2_total').text();
                        if (old_value_team2 != '') {
                            fval_new = parseFloat(old_value_team2) + parseFloat(fval);
                        }
                        if (fval_new > 0) {
                            $('#team2_bet_count_new').removeClass('tolose text-color-red');
                            $('#team2_bet_count_new').addClass('towin text-color-green');
                            $('#team2_bet_count_new').show();
                            $('#team2_bet_count_new').text(fval_new.toFixed(2));
                        } else {
                            $('#team2_bet_count_new').addClass('tolose text-color-red');
                            $('#team2_bet_count_new').removeClass('towin text-color-green');
                            $('#team2_bet_count_new').show();
                            $('#team2_bet_count_new').text(fval_new.toFixed(2));
                        }

                        if ($('#team3').val() != '') {
                            var old_value = $('#draw_total').text();
                            if (old_value != '') {
                                finalValue = parseFloat(old_value) - parseFloat(finalValue);
                            }
                            if (finalValue > 0) {
                                $('#draw_bet_count_new').addClass('towin text-color-green');
                                $('#draw_bet_count_new').removeClass('tolose text-color-red');
                                $('#draw_bet_count_new').show();
                                $('#draw_bet_count_new').text(finalValue.toFixed(2));
                            } else {
                                $('#draw_bet_count_new').removeClass('towin text-color-green');
                                $('#draw_bet_count_new').addClass('tolose text-color-red');
                                $('#draw_bet_count_new').show();
                                $('#draw_bet_count_new').text(finalValue.toFixed(2));
                            }
                        }
                    }
                }
            } else if ($('#betTypeAdd').val() == 'BOOKMAKER' && $('#betTypeAdd').val() != 'SESSION') {
                if ($('#betSide').val() == 'back') {
                    if (old_team1.trim() == team.trim()) {
                        $('#team1_betBM_count_new').show();
                        var old_value = $('#team1_BM_total').text();
                        if (old_value != '') {
                            finalValue = parseFloat(old_value) + parseFloat(finalValue);
                        }
                        if (finalValue > 0) {
                            $('#team1_betBM_count_new').text(finalValue.toFixed(2));
                            $('#team1_betBM_count_new').removeClass('tolose text-color-red');
                            $('#team1_betBM_count_new').addClass('towin text-color-green');
                        } else {
                            $('#team1_betBM_count_new').text(parseFloat(finalValue).toFixed(2));
                            $('#team1_betBM_count_new').addClass('tolose text-color-red');
                            $('#team1_betBM_count_new').removeClass('towin text-color-green');
                        }
                        fval_new = '';
                        var old_value_team2 = $('#team2_BM_total').text();
                        if (old_value_team2 != '') {
                            fval_new = parseFloat(old_value_team2) - parseFloat(fval);
                        }
                        if (fval_new > 0) {
                            $('#team2_betBM_count_new').removeClass('tolose text-color-red');
                            $('#team2_betBM_count_new').addClass('towin text-color-green');
                            $('#team2_betBM_count_new').show();
                            $('#team2_beBMt_count_new').text(fval_new);
                        } else {
                            $('#team2_betBM_count_new').addClass('tolose text-color-red');
                            $('#team2_betBM_count_new').removeClass('towin text-color-green');
                            $('#team2_betBM_count_new').show();
                            $('#team2_betBM_count_new').text(fval_new);
                        }
                        var old_value_team3 = $('#draw_BM_total').text();
                        if ($('#team3').val() != '') {
                            if (old_value_team3 != '') {
                                fval = parseFloat(old_value_team3) - parseFloat(fval);
                            }
                            if (fval > 0) {
                                $('#draw_betBM_count_new').removeClass('tolose text-color-red');
                                $('#draw_betBM_count_new').addClass('towin text-color-green');
                                $('#draw_betBM_count_new').show();
                                $('#draw_betBM_count_new').text(fval);
                            } else {
                                $('#draw_betBM_count_new').addClass('tolose text-color-red');
                                $('#draw_betBM_count_new').removeClass('towin text-color-green');
                                $('#draw_betBM_count_new').show();
                                $('#draw_betBM_count_new').text(fval);
                            }
                        }
                    } else if (old_team2.trim() == team.trim()) {
                        $('#team2_betBM_count_new').show();

                        var old_value = $('#team2_BM_total').text();
                        if (old_value != '') {
                            finalValue = parseFloat(old_value) + parseFloat(finalValue);
                        }
                        if (finalValue > 0) {
                            $('#team2_betBM_count_new').text(finalValue.toFixed(2));
                            $('#team2_betBM_count_new').removeClass('tolose text-color-red');
                            $('#team2_betBM_count_new').addClass('towin text-color-green');
                        } else {
                            $('#team2_betBM_count_new').text(finalValue.toFixed(2));
                            $('#team2_betBM_count_new').removeClass('tolose text-color-red');
                            $('#team2_betBM_count_new').addClass('towin text-color-green');
                        }
                        fval_new = '';
                        var old_value_team1 = $('#team1_BM_total').text();
                        if (old_value_team1 != '') {
                            fval_new = parseFloat(old_value_team1) - parseFloat(fval);
                        }
                        if (fval_new > 0) {
                            $('#team1_betBM_count_new').removeClass('tolose text-color-red');
                            $('#team1_betBM_count_new').addClass('towin text-color-green');
                            $('#team1_betBM_count_new').show();
                            $('#team1_betBM_count_new').text(fval_new.toFixed(2));
                        } else {
                            $('#team1_betBM_count_new').addClass('tolose text-color-red');
                            $('#team1_betBM_count_new').removeClass('towin text-color-green');
                            $('#team1_betBM_count_new').show();
                            $('#team1_betBM_count_new').text(fval_new.toFixed(2));
                        }
                        var old_value_team3 = $('#draw_BM_total').text();
                        if ($('#team3').val() != '') {
                            if (old_value_team3 != '') {
                                fval = parseFloat(old_value_team3) - parseFloat(fval);
                            }
                            if (fval > 0) {
                                $('#draw_betBM_count_new').removeClass('tolose text-color-red');
                                $('#draw_betBM_count_new').addClass('towin text-color-green');
                                $('#draw_betBM_count_new').show();
                                $('#draw_betBM_count_new').text(fval.toFixed(2));
                            } else {
                                $('#draw_betBM_count_new').addClass('tolose text-color-red');
                                $('#draw_betBM_count_new').removeClass('towin text-color-green');
                                $('#draw_betBM_count_new').show();
                                $('#draw_betBM_count_new').text(fval.toFixed(2));
                            }
                        }
                    } else if (old_team3.trim() == team.trim()) {
                        if ($('#team3').val() != '') {
                            $('#draw_betBM_count_new').show();
                            var old_value = $('#draw_BM_total').text();
                            if (old_value != '') {
                                finalValue = parseFloat(old_value) + parseFloat(finalValue);
                            }
                            if (finalValue > 0) {
                                $('#draw_betBM_count_new').removeClass('tolose text-color-red');
                                $('#draw_betBM_count_new').addClass('towin text-color-green');
                                $('#draw_betBM_count_new').show();
                                $('#draw_betBM_count_new').text(finalValue.toFixed(2));
                            } else {
                                $('#draw_betBM_count_new').addClass('tolose text-color-red');
                                $('#draw_betBM_count_new').removeClass('towin text-color-green');
                                $('#draw_betBM_count_new').show();
                                $('#draw_betBM_count_new').text(finalValue.toFixed(2));
                            }

                            fval_new = '';
                            var old_value_team1 = $('#team1_total').text();
                            if (old_value_team1 != '') {
                                fval_new = parseFloat(old_value_team1) - parseFloat(fval);
                            }
                            if (fval_new > 0) {
                                $('#team1_betBM_count_new').show();
                                $('#team1_betBM_count_new').removeClass('tolose text-color-red');
                                $('#team1_betBM_count_new').addClass('towin text-color-green');
                                $('#team1_betBM_count_new').text(fval_new.toFixed(2));
                            } else {
                                $('#team1_betBM_count_new').show();
                                $('#team1_betBM_count_new').addClass('tolose text-color-red');
                                $('#team1_betBM_count_new').removeClass('towin text-color-green');
                                $('#team1_betBM_count_new').text(fval_new.toFixed(2));
                            }

                            fval_new = '';
                            var old_value_team2 = $('#team2_BM_total').text();
                            if (old_value_team2 != '') {
                                fval_new = parseFloat(old_value_team2) - parseFloat(fval);
                            }
                            if (fval_new > 0) {
                                $('#team2_betBM_count_new').removeClass('tolose text-color-red');
                                $('#team2_betBM_count_new').addClass('towin text-color-green');
                                $('#team2_betBM_count_new').show();
                                $('#team2_betBM_count_new').text(fval_new.toFixed(2));
                            } else {
                                $('#team2_betBM_count_new').addClass('tolose text-color-red');
                                $('#team2_betBM_count_new').removeClass('towin text-color-green');
                                $('#team2_betBM_count_new').show();
                                $('#team2_betBM_count_new').text(fval_new.toFixed(2));
                            }
                        }
                    }
                } else if ($('#betSide').val() == 'lay') {
                    if (old_team1.trim() == team.trim()) {
                        $('#team1_betBM_count_new').show();
                        var old_value = $('#team1_BM_total').text();
                        if (old_value != '') {
                            finalValue = parseFloat(old_value) - parseFloat(finalValue);
                        }
                        if (finalValue > 0) {
                            $('#team1_betBM_count_new').show();
                            $('#team1_betBM_count_new').text(finalValue.toFixed(2));
                            $('#team1_betBM_count_new').removeClass('tolose text-color-red');
                            $('#team1_betBM_count_new').addClass('towin text-color-green');
                        } else {
                            $('#team1_betBM_count_new').show();
                            $('#team1_betBM_count_new').text(finalValue.toFixed(2));
                            $('#team1_betBM_count_new').addClass('tolose text-color-red');
                            $('#team1_betBM_count_new').removeClass('towin text-color-green');
                        }
                        fval_new = '';
                        var old_value_team2 = $('#team2_BM_total').text();
                        if (old_value_team2 != '') {
                            fval_new = parseFloat(old_value_team2) + parseFloat(fval);
                        }
                        if (fval_new > 0) {
                            $('#team2_betBM_count_new').removeClass('tolose text-color-red');
                            $('#team2_betBM_count_new').addClass('towin text-color-green');
                            $('#team2_betBM_count_new').show();
                            $('#team2_betBM_count_new').text(fval_new.toFixed(2));
                        } else {
                            $('#team2_betBM_count_new').addClass('tolose text-color-red');
                            $('#team2_betBM_count_new').removeClass('towin text-color-green');
                            $('#team2_betBM_count_new').show();
                            $('#team2_betBM_count_new').text(fval_new.toFixed(2));
                        }
                        if ($('#team3').val() != '') {
                            var old_value_team3 = $('#draw_BM_total').text();
                            if ($('#team3').val() != '') {
                                if (old_value_team3 != '') {
                                    fval = parseFloat(old_value_team3) + parseFloat(fval);
                                }
                                if (fval > 0) {
                                    $('#draw_betBM_count_new').removeClass('tolose text-color-red');
                                    $('#draw_betBM_count_new').addClass('towin text-color-green');
                                    $('#draw_betBM_count_new').show();
                                    $('#draw_betBM_count_new').text(fval.toFixed(2));
                                } else {
                                    $('#draw_betBM_count_new').addClass('tolose text-color-red');
                                    $('#draw_betBM_count_new').removeClass('towin text-color-green');
                                    $('#draw_betBM_count_new').show();
                                    $('#draw_betBM_count_new').text(fval.toFixed(2));
                                }
                            }
                        }
                    } else if (old_team2.trim() == team.trim()) {
                        var old_value = $('#team2_BM_total').text();
                        if (old_value != '') {
                            finalValue = parseFloat(old_value) - parseFloat(finalValue);
                        }
                        if (finalValue > 0) {
                            $('#team2_betBM_count_new').show();
                            $('#team2_betBM_count_new').text(finalValue.toFixed(2));
                            $('#team2_betBM_count_new').removeClass('tolose text-color-red');
                            $('#team2_betBM_count_new').addClass('towin text-color-green');
                        } else {
                            $('#team2_betBM_count_new').show();
                            $('#team2_betBM_count_new').text(finalValue.toFixed(2));
                            $('#team2_betBM_count_new').addClass('tolose text-color-red');
                            $('#team2_betBM_count_new').removeClass('towin text-color-green');
                        }
                        fval_new = '';
                        var old_value_team1 = $('#team1_BM_total').text();
                        if (old_value_team1 != '') {
                            fval_new = parseFloat(old_value_team1) + parseFloat(fval);
                        }
                        if (fval_new > 0) {
                            $('#team1_betBM_count_new').removeClass('tolose text-color-red');
                            $('#team1_betBM_count_new').addClass('towin text-color-green');
                            $('#team1_betBM_count_new').show();
                            $('#team1_betBM_count_new').text(fval_new.toFixed(2));
                        } else {
                            $('#team1_betBM_count_new').addClass('tolose text-color-red');
                            $('#team1_betBM_count_new').removeClass('towin text-color-green');
                            $('#team1_betBM_count_new').show();
                            $('#team1_betBM_count_new').text(fval_new.toFixed(2));
                        }

                        if ($('#team3').val() != '') {
                            fval_new = '';
                            var old_value_draw = $('#draw_BM_total').text();
                            if (old_value_draw != '') {
                                fval_new = parseFloat(old_value_draw) + parseFloat(fval);
                            }
                            if (fval_new > 0) {
                                $('#draw_betBM_count_new').removeClass('tolose text-color-red');
                                $('#draw_betBM_count_new').addClass('towin text-color-green');
                                $('#draw_betBM_count_new').show();
                                $('#draw_betBM_count_new').text(fval_new.toFixed(2));
                            } else {
                                $('#draw_betBM_count_new').addClass('tolose text-color-red');
                                $('#draw_betBM_count_new').removeClass('towin text-color-green');
                                $('#draw_betBM_count_new').show();
                                $('#draw_betBM_count_new').text(fval_new.toFixed(2));
                            }
                        }
                    } else if (old_team3.trim() == team.trim()) {
                        fval_new = '';
                        var old_value_team1 = $('#team1_BM_total').text();
                        if (old_value_team1 != '') {
                            fval_new = parseFloat(old_value_team1) + parseFloat(fval);
                        }
                        if (fval_new > 0) {
                            $('#team1_betBM_count_new').show();
                            $('#team1_betBM_count_new').text(fval_new.toFixed(2));
                            $('#team1_betBM_count_new').removeClass('tolose text-color-red');
                            $('#team1_betBM_count_new').addClass('towin text-color-green');
                        } else {
                            $('#team1_betBM_count_new').show();
                            $('#team1_betBM_count_new').text(fval_new.toFixed(2));
                            $('#team1_betBM_count_new').addClass('tolose text-color-red');
                            $('#team1_betBM_count_new').removeClass('towin text-color-green');
                        }
                        fval_new = '';
                        var old_value_team2 = $('#team2_BM_total').text();
                        if (old_value_team2 != '') {
                            fval_new = parseFloat(old_value_team2) + parseFloat(fval);
                        }
                        if (fval_new > 0) {
                            $('#team2_betBM_count_new').removeClass('tolose text-color-red');
                            $('#team2_betBM_count_new').addClass('towin text-color-green');
                            $('#team2_betBM_count_new').show();
                            $('#team2_betBM_count_new').text(fval_new.toFixed(2));
                        } else {
                            $('#team2_betBM_count_new').addClass('tolose text-color-red');
                            $('#team2_betBM_count_new').removeClass('towin text-color-green');
                            $('#team2_betBM_count_new').show();
                            $('#team2_betBM_count_new').text(fval_new.toFixed(2));
                        }

                        if ($('#team3').val() != '') {
                            var old_value = $('#draw_BM_total').text();
                            if (old_value != '') {
                                finalValue = parseFloat(old_value) - parseFloat(finalValue);
                            }
                            if (finalValue > 0) {
                                $('#draw_betBM_count_new').addClass('towin text-color-green');
                                $('#draw_betBM_count_new').removeClass('tolose text-color-red');
                                $('#draw_betBM_count_new').show();
                                $('#draw_betBM_count_new').text(finalValue.toFixed(2));
                            } else {
                                $('#draw_betBM_count_new').removeClass('towin text-color-green');
                                $('#draw_betBM_count_new').addClass('tolose text-color-red');
                                $('#draw_betBM_count_new').show();
                                $('#draw_betBM_count_new').text(finalValue.toFixed(2));
                            }
                        }
                    }
                }
            }
        });

        $(document).ready(function () {
            $('.count').prop('disabled', true);
            $('.count1').prop('disabled', true);
            $(document).on('click', '.plus', function () {
                if ($('.count1').val() != '')
                    $('.count1').val(parseInt($('.count1').val()) + 1);
                else
                    $('.count1').val(parseInt(1));
            });
            $(document).on('click', '.minus', function () {
                var oldValue = parseInt($('.count1').val());
                var newVal = '';
                if (oldValue != '') {
                    $('.count1').val(parseInt($('.count1').val()) - 1);
                    if (oldValue > 0) {
                        newVal = parseInt(oldValue) - 1;
                    } else {
                        newVal = 0;
                    }
                } else {
                    newVal = 0;
                }


                $('#odds_val').val(newVal);
                $('.count1').val(newVal);
            });

            // plus minus mobile odds
            $('.count').prop('disabled', true);
            $('.count2').prop('disabled', true);
            $(document).on('click', '.plusodds', function () {
                var newVal = parseFloat(parseFloat($('.count2').val()) + 0.1).toFixed(2);
                $('#odds_val').val(newVal);
                $('.count2').val(newVal);
            });
            $(document).on('click', '.minusodds', function () {
                var oldValue = parseFloat($('.count2').val());
                $('.count2').val(parseFloat(parseFloat($('.count2').val()) - 0.1).toFixed(2));

                if (oldValue > 0) {
                    var newVal = parseFloat(parseFloat(oldValue) - 0.1).toFixed(2);
                } else {
                    newVal = 0;
                }

                $('#odds_val').val(newVal);
                $('.count2').val(newVal);

            });
        });

        $(document).on("click", '.clsplusminus', function (event) {
            setTimeout(() => {
                var fval = $('.mobile_tr_common_class #inputStake_mobile').val();

                var oddval = $('#mobile_odds').val();
                if ($('#betTypeAdd').val() == 'ODDS')
                    oddval = parseFloat(oddval) - parseInt(1);

                var finalValue = parseFloat(fval) * parseFloat(oddval);

                if ($('#betTypeAdd').val() == 'BOOKMAKER')
                    finalValue = parseFloat(finalValue) / parseInt(100);
                if ($('#betTypeAdd').val() != 'SESSION')
                    $('.profil').html(finalValue.toFixed(2));


                var team = $('#teamNameBet').val();

                // console.log("finalValue: ",finalValue);
                // console.log("fval: ",fval);
                // console.log("team: ",team);
                // console.log("oddval: ",oddval);

                var old_team1 = $('#team1').val();
                var old_team2 = $('#team2').val();
                var old_team3 = $('#team3').val();

                $('#inputStake').val(fval);

                if ($('#betTypeAdd').val() == 'ODDS' && $('#betTypeAdd').val() != 'SESSION') {
                    if ($('#betSide').val() == 'back') {
                        if (old_team1.trim() == team.trim()) {
                            $('#team1_bet_count_new').show();
                            var old_value = $('#team1_total').text();
                            if (old_value != '') {
                                finalValue = parseFloat(old_value) + parseFloat(finalValue);
                            }
                            if (finalValue > 0) {
                                $('#team1_bet_count_new').text(finalValue.toFixed(2));
                                $('#team1_bet_count_new').removeClass('tolose text-color-red');
                                $('#team1_bet_count_new').addClass('towin text-color-green');
                            } else {
                                $('#team1_bet_count_new').text(parseFloat(finalValue).toFixed(2));
                                $('#team1_bet_count_new').addClass('tolose text-color-red');
                                $('#team1_bet_count_new').removeClass('towin text-color-green');
                            }
                            fval_new = '';
                            var old_value_team2 = $('#team2_total').text();
                            if (old_value_team2 != '') {
                                fval_new = parseFloat(old_value_team2) - parseFloat(fval);
                            }
                            if (fval_new > 0) {
                                $('#team2_bet_count_new').removeClass('tolose text-color-red');
                                $('#team2_bet_count_new').addClass('towin text-color-green');
                                $('#team2_bet_count_new').show();
                                $('#team2_bet_count_new').text(fval_new);
                            } else {
                                $('#team2_bet_count_new').addClass('tolose text-color-red');
                                $('#team2_bet_count_new').removeClass('towin text-color-green');
                                $('#team2_bet_count_new').show();
                                $('#team2_bet_count_new').text(fval_new);
                            }
                            var old_value_team3 = $('#draw_total').text();
                            if ($('#team3').val() != '') {
                                if (old_value_team3 != '') {
                                    fval = parseFloat(old_value_team3) - parseFloat(fval);
                                }
                                if (fval > 0) {
                                    $('#draw_bet_count_new').removeClass('tolose text-color-red');
                                    $('#draw_bet_count_new').addClass('towin text-color-green');
                                    $('#draw_bet_count_new').show();
                                    $('#draw_bet_count_new').text(fval);
                                } else {
                                    $('#draw_bet_count_new').addClass('tolose text-color-red');
                                    $('#draw_bet_count_new').removeClass('towin text-color-green');
                                    $('#draw_bet_count_new').show();
                                    $('#draw_bet_count_new').text(fval);
                                }
                            }
                        } else if (old_team2.trim() == team.trim()) {
                            $('#team2_bet_count_new').show();

                            var old_value = $('#team2_total').text();
                            if (old_value != '') {
                                finalValue = parseFloat(old_value) + parseFloat(finalValue);
                            }
                            if (finalValue > 0) {
                                $('#team2_bet_count_new').text(finalValue.toFixed(2));
                                $('#team2_bet_count_new').removeClass('tolose text-color-red');
                                $('#team2_bet_count_new').addClass('towin text-color-green');
                            } else {
                                $('#team2_bet_count_new').text(finalValue.toFixed(2));
                                $('#team2_bet_count_new').removeClass('tolose text-color-red');
                                $('#team2_bet_count_new').addClass('towin text-color-green');
                            }
                            fval_new = '';
                            var old_value_team1 = $('#team1_total').text();
                            if (old_value_team1 != '') {
                                fval_new = parseFloat(old_value_team1) - parseFloat(fval);
                            }
                            if (fval_new > 0) {
                                $('#team1_bet_count_new').removeClass('tolose text-color-red');
                                $('#team1_bet_count_new').addClass('towin text-color-green');
                                $('#team1_bet_count_new').show();
                                $('#team1_bet_count_new').text(fval_new.toFixed(2));
                            } else {
                                $('#team1_bet_count_new').addClass('tolose text-color-red');
                                $('#team1_bet_count_new').removeClass('towin text-color-green');
                                $('#team1_bet_count_new').show();
                                $('#team1_bet_count_new').text(fval_new.toFixed(2));
                            }
                            var old_value_team3 = $('#draw_total').text();
                            if ($('#team3').val() != '') {
                                if (old_value_team3 != '') {
                                    fval = parseFloat(old_value_team3) - parseFloat(fval);
                                }
                                if (fval > 0) {
                                    $('#draw_bet_count_new').removeClass('tolose text-color-red');
                                    $('#draw_bet_count_new').addClass('towin text-color-green');
                                    $('#draw_bet_count_new').show();
                                    $('#draw_bet_count_new').text(fval.toFixed(2));
                                } else {
                                    $('#draw_bet_count_new').addClass('tolose text-color-red');
                                    $('#draw_bet_count_new').removeClass('towin text-color-green');
                                    $('#draw_bet_count_new').show();
                                    $('#draw_bet_count_new').text(fval.toFixed(2));
                                }
                            }
                        } else if (old_team3.trim() == team.trim()) {
                            if ($('#team3').val() != '') {
                                $('#draw_bet_count_new').show();
                                var old_value = $('#draw_total').text();
                                if (old_value != '') {
                                    finalValue = parseFloat(old_value) + parseFloat(finalValue);
                                }
                                if (finalValue > 0) {
                                    $('#draw_bet_count_new').removeClass('tolose text-color-red');
                                    $('#draw_bet_count_new').addClass('towin text-color-green');
                                    $('#draw_bet_count_new').show();
                                    $('#draw_bet_count_new').text(finalValue.toFixed(2));
                                } else {
                                    $('#draw_bet_count_new').addClass('tolose text-color-red');
                                    $('#draw_bet_count_new').removeClass('towin text-color-green');
                                    $('#draw_bet_count_new').show();
                                    $('#draw_bet_count_new').text(finalValue.toFixed(2));
                                }

                                fval_new = '';
                                var old_value_team1 = $('#team1_total').text();
                                if (old_value_team1 != '') {
                                    fval_new = parseFloat(old_value_team1) - parseFloat(fval);
                                }
                                if (fval_new > 0) {
                                    $('#team1_bet_count_new').show();
                                    $('#team1_bet_count_new').removeClass('tolose text-color-red');
                                    $('#team1_bet_count_new').addClass('towin text-color-green');
                                    $('#team1_bet_count_new').text(fval_new.toFixed(2));
                                } else {
                                    $('#team1_bet_count_new').show();
                                    $('#team1_bet_count_new').addClass('tolose text-color-red');
                                    $('#team1_bet_count_new').removeClass('towin text-color-green');
                                    $('#team1_bet_count_new').text(fval_new.toFixed(2));
                                }

                                fval_new = '';
                                var old_value_team2 = $('#team2_total').text();
                                if (old_value_team2 != '') {
                                    fval_new = parseFloat(old_value_team2) - parseFloat(fval);
                                }
                                if (fval_new > 0) {
                                    $('#team2_bet_count_new').removeClass('tolose text-color-red');
                                    $('#team2_bet_count_new').addClass('towin text-color-green');
                                    $('#team2_bet_count_new').show();
                                    $('#team2_bet_count_new').text(fval_new.toFixed(2));
                                } else {
                                    $('#team2_bet_count_new').addClass('tolose text-color-red');
                                    $('#team2_bet_count_new').removeClass('towin text-color-green');
                                    $('#team2_bet_count_new').show();
                                    $('#team2_bet_count_new').text(fval_new.toFixed(2));
                                }
                            }
                        }
                    } else if ($('#betSide').val() == 'lay') {
                        if (old_team1.trim() == team.trim()) {
                            $('#team1_bet_count_new').show();
                            var old_value = $('#team1_total').text();
                            if (old_value != '') {
                                finalValue = parseFloat(old_value) - parseFloat(finalValue);
                            }
                            if (finalValue > 0) {
                                $('#team1_bet_count_new').show();
                                $('#team1_bet_count_new').text(finalValue.toFixed(2));
                                $('#team1_bet_count_new').removeClass('tolose text-color-red');
                                $('#team1_bet_count_new').addClass('towin text-color-green');
                            } else {
                                $('#team1_bet_count_new').show();
                                $('#team1_bet_count_new').text(finalValue.toFixed(2));
                                $('#team1_bet_count_new').addClass('tolose text-color-red');
                                $('#team1_bet_count_new').removeClass('towin text-color-green');
                            }
                            fval_new = '';
                            var old_value_team2 = $('#team2_total').text();
                            if (old_value_team2 != '') {
                                fval_new = parseFloat(old_value_team2) + parseFloat(fval);
                            }
                            if (fval_new > 0) {
                                $('#team2_bet_count_new').removeClass('tolose text-color-red');
                                $('#team2_bet_count_new').addClass('towin text-color-green');
                                $('#team2_bet_count_new').show();
                                $('#team2_bet_count_new').text(fval_new.toFixed(2));
                            } else {
                                $('#team2_bet_count_new').addClass('tolose text-color-red');
                                $('#team2_bet_count_new').removeClass('towin text-color-green');
                                $('#team2_bet_count_new').show();
                                $('#team2_bet_count_new').text(fval_new.toFixed(2));
                            }
                            if ($('#team3').val() != '') {
                                var old_value_team3 = $('#draw_total').text();
                                if ($('#team3').val() != '') {
                                    if (old_value_team3 != '') {
                                        fval = parseFloat(old_value_team3) + parseFloat(fval);
                                    }
                                    if (fval > 0) {
                                        $('#draw_bet_count_new').removeClass('tolose text-color-red');
                                        $('#draw_bet_count_new').addClass('towin text-color-green');
                                        $('#draw_bet_count_new').show();
                                        $('#draw_bet_count_new').text(fval.toFixed(2));
                                    } else {
                                        $('#draw_bet_count_new').addClass('tolose text-color-red');
                                        $('#draw_bet_count_new').removeClass('towin text-color-green');
                                        $('#draw_bet_count_new').show();
                                        $('#draw_bet_count_new').text(fval.toFixed(2));
                                    }
                                }
                            }
                        } else if (old_team2.trim() == team.trim()) {
                            var old_value = $('#team2_total').text();
                            if (old_value != '') {
                                finalValue = parseFloat(old_value) - parseFloat(finalValue);
                            }
                            if (finalValue > 0) {
                                $('#team2_bet_count_new').show();
                                $('#team2_bet_count_new').text(finalValue.toFixed(2));
                                $('#team2_bet_count_new').removeClass('tolose text-color-red');
                                $('#team2_bet_count_new').addClass('towin text-color-green');
                            } else {
                                $('#team2_bet_count_new').show();
                                $('#team2_bet_count_new').text(finalValue.toFixed(2));
                                $('#team2_bet_count_new').addClass('tolose text-color-red');
                                $('#team2_bet_count_new').removeClass('towin text-color-green');
                            }
                            fval_new = '';
                            var old_value_team1 = $('#team1_total').text();
                            if (old_value_team1 != '') {
                                fval_new = parseFloat(old_value_team1) + parseFloat(fval);
                            }
                            if (fval_new > 0) {
                                $('#team1_bet_count_new').removeClass('tolose text-color-red');
                                $('#team1_bet_count_new').addClass('towin text-color-green');
                                $('#team1_bet_count_new').show();
                                $('#team1_bet_count_new').text(fval_new.toFixed(2));
                            } else {
                                $('#team1_bet_count_new').addClass('tolose text-color-red');
                                $('#team1_bet_count_new').removeClass('towin text-color-green');
                                $('#team1_bet_count_new').show();
                                $('#team1_bet_count_new').text(fval_new.toFixed(2));
                            }

                            if ($('#team3').val() != '') {
                                fval_new = '';
                                var old_value_draw = $('#draw_total').text();
                                if (old_value_draw != '') {
                                    fval_new = parseFloat(old_value_draw) + parseFloat(fval);
                                }
                                if (fval_new > 0) {
                                    $('#draw_bet_count_new').removeClass('tolose text-color-red');
                                    $('#draw_bet_count_new').addClass('towin text-color-green');
                                    $('#draw_bet_count_new').show();
                                    $('#draw_bet_count_new').text(fval_new.toFixed(2));
                                } else {
                                    $('#draw_bet_count_new').addClass('tolose text-color-red');
                                    $('#draw_bet_count_new').removeClass('towin text-color-green');
                                    $('#draw_bet_count_new').show();
                                    $('#draw_bet_count_new').text(fval_new.toFixed(2));
                                }
                            }
                        } else if (old_team3.trim() == team.trim()) {
                            fval_new = '';
                            var old_value_team1 = $('#team1_total').text();
                            if (old_value_team1 != '') {
                                fval_new = parseFloat(old_value_team1) + parseFloat(fval);
                            }
                            if (fval_new > 0) {
                                $('#team1_bet_count_new').show();
                                $('#team1_bet_count_new').text(fval_new.toFixed(2));
                                $('#team1_bet_count_new').removeClass('tolose text-color-red');
                                $('#team1_bet_count_new').addClass('towin text-color-green');
                            } else {
                                $('#team1_bet_count_new').show();
                                $('#team1_bet_count_new').text(fval_new.toFixed(2));
                                $('#team1_bet_count_new').addClass('tolose text-color-red');
                                $('#team1_bet_count_new').removeClass('towin text-color-green');
                            }
                            fval_new = '';
                            var old_value_team2 = $('#team2_total').text();
                            if (old_value_team2 != '') {
                                fval_new = parseFloat(old_value_team2) + parseFloat(fval);
                            }
                            if (fval_new > 0) {
                                $('#team2_bet_count_new').removeClass('tolose text-color-red');
                                $('#team2_bet_count_new').addClass('towin text-color-green');
                                $('#team2_bet_count_new').show();
                                $('#team2_bet_count_new').text(fval_new.toFixed(2));
                            } else {
                                $('#team2_bet_count_new').addClass('tolose text-color-red');
                                $('#team2_bet_count_new').removeClass('towin text-color-green');
                                $('#team2_bet_count_new').show();
                                $('#team2_bet_count_new').text(fval_new.toFixed(2));
                            }

                            if ($('#team3').val() != '') {
                                var old_value = $('#draw_total').text();
                                if (old_value != '') {
                                    finalValue = parseFloat(old_value) - parseFloat(finalValue);
                                }
                                if (finalValue > 0) {
                                    $('#draw_bet_count_new').addClass('towin text-color-green');
                                    $('#draw_bet_count_new').removeClass('tolose text-color-red');
                                    $('#draw_bet_count_new').show();
                                    $('#draw_bet_count_new').text(finalValue.toFixed(2));
                                } else {
                                    $('#draw_bet_count_new').removeClass('towin text-color-green');
                                    $('#draw_bet_count_new').addClass('tolose text-color-red');
                                    $('#draw_bet_count_new').show();
                                    $('#draw_bet_count_new').text(finalValue.toFixed(2));
                                }
                            }
                        }
                    }
                } else if ($('#betTypeAdd').val() == 'BOOKMAKER' && $('#betTypeAdd').val() != 'SESSION') {
                    if ($('#betSide').val() == 'back') {
                        if (old_team1.trim() == team.trim()) {
                            $('#team1_betBM_count_new').show();
                            var old_value = $('#team1_BM_total').text();
                            if (old_value != '') {
                                finalValue = parseFloat(old_value) + parseFloat(finalValue);
                            }
                            if (finalValue > 0) {
                                $('#team1_betBM_count_new').text(finalValue.toFixed(2));
                                $('#team1_betBM_count_new').removeClass('tolose text-color-red');
                                $('#team1_betBM_count_new').addClass('towin text-color-green');
                            } else {
                                $('#team1_betBM_count_new').text(parseFloat(finalValue).toFixed(2));
                                $('#team1_betBM_count_new').addClass('tolose text-color-red');
                                $('#team1_betBM_count_new').removeClass('towin text-color-green');
                            }
                            fval_new = '';
                            var old_value_team2 = $('#team2_BM_total').text();
                            if (old_value_team2 != '') {
                                fval_new = parseFloat(old_value_team2) - parseFloat(fval);
                            }
                            if (fval_new > 0) {
                                $('#team2_betBM_count_new').removeClass('tolose text-color-red');
                                $('#team2_betBM_count_new').addClass('towin text-color-green');
                                $('#team2_betBM_count_new').show();
                                $('#team2_beBMt_count_new').text(fval_new);
                            } else {
                                $('#team2_betBM_count_new').addClass('tolose text-color-red');
                                $('#team2_betBM_count_new').removeClass('towin text-color-green');
                                $('#team2_betBM_count_new').show();
                                $('#team2_betBM_count_new').text(fval_new);
                            }
                            var old_value_team3 = $('#draw_BM_total').text();
                            if ($('#team3').val() != '') {
                                if (old_value_team3 != '') {
                                    fval = parseFloat(old_value_team3) - parseFloat(fval);
                                }
                                if (fval > 0) {
                                    $('#draw_betBM_count_new').removeClass('tolose text-color-red');
                                    $('#draw_betBM_count_new').addClass('towin text-color-green');
                                    $('#draw_betBM_count_new').show();
                                    $('#draw_betBM_count_new').text(fval);
                                } else {
                                    $('#draw_betBM_count_new').addClass('tolose text-color-red');
                                    $('#draw_betBM_count_new').removeClass('towin text-color-green');
                                    $('#draw_betBM_count_new').show();
                                    $('#draw_betBM_count_new').text(fval);
                                }
                            }
                        } else if (old_team2.trim() == team.trim()) {
                            $('#team2_betBM_count_new').show();

                            var old_value = $('#team2_BM_total').text();
                            if (old_value != '') {
                                finalValue = parseFloat(old_value) + parseFloat(finalValue);
                            }
                            if (finalValue > 0) {
                                $('#team2_betBM_count_new').text(finalValue.toFixed(2));
                                $('#team2_betBM_count_new').removeClass('tolose text-color-red');
                                $('#team2_betBM_count_new').addClass('towin text-color-green');
                            } else {
                                $('#team2_betBM_count_new').text(finalValue.toFixed(2));
                                $('#team2_betBM_count_new').removeClass('tolose text-color-red');
                                $('#team2_betBM_count_new').addClass('towin text-color-green');
                            }
                            fval_new = '';
                            var old_value_team1 = $('#team1_BM_total').text();
                            if (old_value_team1 != '') {
                                fval_new = parseFloat(old_value_team1) - parseFloat(fval);
                            }
                            if (fval_new > 0) {
                                $('#team1_betBM_count_new').removeClass('tolose text-color-red');
                                $('#team1_betBM_count_new').addClass('towin text-color-green');
                                $('#team1_betBM_count_new').show();
                                $('#team1_betBM_count_new').text(fval_new.toFixed(2));
                            } else {
                                $('#team1_betBM_count_new').addClass('tolose text-color-red');
                                $('#team1_betBM_count_new').removeClass('towin text-color-green');
                                $('#team1_betBM_count_new').show();
                                $('#team1_betBM_count_new').text(fval_new.toFixed(2));
                            }
                            var old_value_team3 = $('#draw_BM_total').text();
                            if ($('#team3').val() != '') {
                                if (old_value_team3 != '') {
                                    fval = parseFloat(old_value_team3) - parseFloat(fval);
                                }
                                if (fval > 0) {
                                    $('#draw_betBM_count_new').removeClass('tolose text-color-red');
                                    $('#draw_betBM_count_new').addClass('towin text-color-green');
                                    $('#draw_betBM_count_new').show();
                                    $('#draw_betBM_count_new').text(fval.toFixed(2));
                                } else {
                                    $('#draw_betBM_count_new').addClass('tolose text-color-red');
                                    $('#draw_betBM_count_new').removeClass('towin text-color-green');
                                    $('#draw_betBM_count_new').show();
                                    $('#draw_betBM_count_new').text(fval.toFixed(2));
                                }
                            }
                        } else if (old_team3.trim() == team.trim()) {
                            if ($('#team3').val() != '') {
                                $('#draw_betBM_count_new').show();
                                var old_value = $('#draw_BM_total').text();
                                if (old_value != '') {
                                    finalValue = parseFloat(old_value) + parseFloat(finalValue);
                                }
                                if (finalValue > 0) {
                                    $('#draw_betBM_count_new').removeClass('tolose text-color-red');
                                    $('#draw_betBM_count_new').addClass('towin text-color-green');
                                    $('#draw_betBM_count_new').show();
                                    $('#draw_betBM_count_new').text(finalValue.toFixed(2));
                                } else {
                                    $('#draw_betBM_count_new').addClass('tolose text-color-red');
                                    $('#draw_betBM_count_new').removeClass('towin text-color-green');
                                    $('#draw_betBM_count_new').show();
                                    $('#draw_betBM_count_new').text(finalValue.toFixed(2));
                                }

                                fval_new = '';
                                var old_value_team1 = $('#team1_total').text();
                                if (old_value_team1 != '') {
                                    fval_new = parseFloat(old_value_team1) - parseFloat(fval);
                                }
                                if (fval_new > 0) {
                                    $('#team1_betBM_count_new').show();
                                    $('#team1_betBM_count_new').removeClass('tolose text-color-red');
                                    $('#team1_betBM_count_new').addClass('towin text-color-green');
                                    $('#team1_betBM_count_new').text(fval_new.toFixed(2));
                                } else {
                                    $('#team1_betBM_count_new').show();
                                    $('#team1_betBM_count_new').addClass('tolose text-color-red');
                                    $('#team1_betBM_count_new').removeClass('towin text-color-green');
                                    $('#team1_betBM_count_new').text(fval_new.toFixed(2));
                                }

                                fval_new = '';
                                var old_value_team2 = $('#team2_BM_total').text();
                                if (old_value_team2 != '') {
                                    fval_new = parseFloat(old_value_team2) - parseFloat(fval);
                                }
                                if (fval_new > 0) {
                                    $('#team2_betBM_count_new').removeClass('tolose text-color-red');
                                    $('#team2_betBM_count_new').addClass('towin text-color-green');
                                    $('#team2_betBM_count_new').show();
                                    $('#team2_betBM_count_new').text(fval_new.toFixed(2));
                                } else {
                                    $('#team2_betBM_count_new').addClass('tolose text-color-red');
                                    $('#team2_betBM_count_new').removeClass('towin text-color-green');
                                    $('#team2_betBM_count_new').show();
                                    $('#team2_betBM_count_new').text(fval_new.toFixed(2));
                                }
                            }
                        }
                    } else if ($('#betSide').val() == 'lay') {
                        if (old_team1.trim() == team.trim()) {
                            $('#team1_betBM_count_new').show();
                            var old_value = $('#team1_BM_total').text();
                            if (old_value != '') {
                                finalValue = parseFloat(old_value) - parseFloat(finalValue);
                            }
                            if (finalValue > 0) {
                                $('#team1_betBM_count_new').show();
                                $('#team1_betBM_count_new').text(finalValue.toFixed(2));
                                $('#team1_betBM_count_new').removeClass('tolose text-color-red');
                                $('#team1_betBM_count_new').addClass('towin text-color-green');
                            } else {
                                $('#team1_betBM_count_new').show();
                                $('#team1_betBM_count_new').text(finalValue.toFixed(2));
                                $('#team1_betBM_count_new').addClass('tolose text-color-red');
                                $('#team1_betBM_count_new').removeClass('towin text-color-green');
                            }
                            fval_new = '';
                            var old_value_team2 = $('#team2_BM_total').text();
                            if (old_value_team2 != '') {
                                fval_new = parseFloat(old_value_team2) + parseFloat(fval);
                            }
                            if (fval_new > 0) {
                                $('#team2_betBM_count_new').removeClass('tolose text-color-red');
                                $('#team2_betBM_count_new').addClass('towin text-color-green');
                                $('#team2_betBM_count_new').show();
                                $('#team2_betBM_count_new').text(fval_new.toFixed(2));
                            } else {
                                $('#team2_betBM_count_new').addClass('tolose text-color-red');
                                $('#team2_betBM_count_new').removeClass('towin text-color-green');
                                $('#team2_betBM_count_new').show();
                                $('#team2_betBM_count_new').text(fval_new.toFixed(2));
                            }
                            if ($('#team3').val() != '') {
                                var old_value_team3 = $('#draw_BM_total').text();
                                if ($('#team3').val() != '') {
                                    if (old_value_team3 != '') {
                                        fval = parseFloat(old_value_team3) + parseFloat(fval);
                                    }
                                    if (fval > 0) {
                                        $('#draw_betBM_count_new').removeClass('tolose text-color-red');
                                        $('#draw_betBM_count_new').addClass('towin text-color-green');
                                        $('#draw_betBM_count_new').show();
                                        $('#draw_betBM_count_new').text(fval.toFixed(2));
                                    } else {
                                        $('#draw_betBM_count_new').addClass('tolose text-color-red');
                                        $('#draw_betBM_count_new').removeClass('towin text-color-green');
                                        $('#draw_betBM_count_new').show();
                                        $('#draw_betBM_count_new').text(fval.toFixed(2));
                                    }
                                }
                            }
                        } else if (old_team2.trim() == team.trim()) {
                            var old_value = $('#team2_BM_total').text();
                            if (old_value != '') {
                                finalValue = parseFloat(old_value) - parseFloat(finalValue);
                            }
                            if (finalValue > 0) {
                                $('#team2_betBM_count_new').show();
                                $('#team2_betBM_count_new').text(finalValue.toFixed(2));
                                $('#team2_betBM_count_new').removeClass('tolose text-color-red');
                                $('#team2_betBM_count_new').addClass('towin text-color-green');
                            } else {
                                $('#team2_betBM_count_new').show();
                                $('#team2_betBM_count_new').text(finalValue.toFixed(2));
                                $('#team2_betBM_count_new').addClass('tolose text-color-red');
                                $('#team2_betBM_count_new').removeClass('towin text-color-green');
                            }
                            fval_new = '';
                            var old_value_team1 = $('#team1_BM_total').text();
                            if (old_value_team1 != '') {
                                fval_new = parseFloat(old_value_team1) + parseFloat(fval);
                            }
                            if (fval_new > 0) {
                                $('#team1_betBM_count_new').removeClass('tolose text-color-red');
                                $('#team1_betBM_count_new').addClass('towin text-color-green');
                                $('#team1_betBM_count_new').show();
                                $('#team1_betBM_count_new').text(fval_new.toFixed(2));
                            } else {
                                $('#team1_betBM_count_new').addClass('tolose text-color-red');
                                $('#team1_betBM_count_new').removeClass('towin text-color-green');
                                $('#team1_betBM_count_new').show();
                                $('#team1_betBM_count_new').text(fval_new.toFixed(2));
                            }

                            if ($('#team3').val() != '') {
                                fval_new = '';
                                var old_value_draw = $('#draw_BM_total').text();
                                if (old_value_draw != '') {
                                    fval_new = parseFloat(old_value_draw) + parseFloat(fval);
                                }
                                if (fval_new > 0) {
                                    $('#draw_betBM_count_new').removeClass('tolose text-color-red');
                                    $('#draw_betBM_count_new').addClass('towin text-color-green');
                                    $('#draw_betBM_count_new').show();
                                    $('#draw_betBM_count_new').text(fval_new.toFixed(2));
                                } else {
                                    $('#draw_betBM_count_new').addClass('tolose text-color-red');
                                    $('#draw_betBM_count_new').removeClass('towin text-color-green');
                                    $('#draw_betBM_count_new').show();
                                    $('#draw_betBM_count_new').text(fval_new.toFixed(2));
                                }
                            }
                        } else if (old_team3.trim() == team.trim()) {
                            fval_new = '';
                            var old_value_team1 = $('#team1_BM_total').text();
                            if (old_value_team1 != '') {
                                fval_new = parseFloat(old_value_team1) + parseFloat(fval);
                            }
                            if (fval_new > 0) {
                                $('#team1_betBM_count_new').show();
                                $('#team1_betBM_count_new').text(fval_new.toFixed(2));
                                $('#team1_betBM_count_new').removeClass('tolose text-color-red');
                                $('#team1_betBM_count_new').addClass('towin text-color-green');
                            } else {
                                $('#team1_betBM_count_new').show();
                                $('#team1_betBM_count_new').text(fval_new.toFixed(2));
                                $('#team1_betBM_count_new').addClass('tolose text-color-red');
                                $('#team1_betBM_count_new').removeClass('towin text-color-green');
                            }
                            fval_new = '';
                            var old_value_team2 = $('#team2_BM_total').text();
                            if (old_value_team2 != '') {
                                fval_new = parseFloat(old_value_team2) + parseFloat(fval);
                            }
                            if (fval_new > 0) {
                                $('#team2_betBM_count_new').removeClass('tolose text-color-red');
                                $('#team2_betBM_count_new').addClass('towin text-color-green');
                                $('#team2_betBM_count_new').show();
                                $('#team2_betBM_count_new').text(fval_new.toFixed(2));
                            } else {
                                $('#team2_betBM_count_new').addClass('tolose text-color-red');
                                $('#team2_betBM_count_new').removeClass('towin text-color-green');
                                $('#team2_betBM_count_new').show();
                                $('#team2_betBM_count_new').text(fval_new.toFixed(2));
                            }

                            if ($('#team3').val() != '') {
                                var old_value = $('#draw_BM_total').text();
                                if (old_value != '') {
                                    finalValue = parseFloat(old_value) - parseFloat(finalValue);
                                }
                                if (finalValue > 0) {
                                    $('#draw_betBM_count_new').addClass('towin text-color-green');
                                    $('#draw_betBM_count_new').removeClass('tolose text-color-red');
                                    $('#draw_betBM_count_new').show();
                                    $('#draw_betBM_count_new').text(finalValue.toFixed(2));
                                } else {
                                    $('#draw_betBM_count_new').removeClass('towin text-color-green');
                                    $('#draw_betBM_count_new').addClass('tolose text-color-red');
                                    $('#draw_betBM_count_new').show();
                                    $('#draw_betBM_count_new').text(finalValue.toFixed(2));
                                }
                            }
                        }
                    }
                }
            }, 100);
        });

        function getCalculated(fval) {
            // var width = $(window).width();
            // if (width < 990){
            //
            // }else
            {
                var oddval = $('#odds_val').val();
                if ($('#betTypeAdd').val() == 'ODDS')
                    oddval = parseFloat(oddval) - parseInt(1);

                var finalValue = parseFloat(fval) * parseFloat(oddval);

                if ($('#betTypeAdd').val() == 'BOOKMAKER')
                    finalValue = parseFloat(finalValue) / parseInt(100);
                if ($('#betTypeAdd').val() != 'SESSION')
                    $('.profil').html(finalValue.toFixed(2));
            }

            var team = $('#teamNameBet').val();
            var odds_position = $('#odds_position').val();
            // console.log("finalValue: ",finalValue);
            // console.log("fval: ",fval);
            // console.log("team: ",team);
            // console.log("oddval: ",oddval);

            var old_team1 = $('#team1').val();
            var old_team2 = $('#team2').val();
            var old_team3 = $('#team3').val();
            if ($('#betTypeAdd').val() == 'ODDS') {
                if ($('#betSide').val() == 'back') {
                    if (old_team1.trim() == team.trim()) {
                        $('#team1_bet_count_new').show();
                        var old_value = $('#team1_total').text();
                        if (old_value != '') {
                            finalValue = parseFloat(old_value) + parseFloat(finalValue);
                        }
                        if (finalValue > 0) {
                            $('#team1_bet_count_new').text(finalValue.toFixed(2));
                            $('#team1_bet_count_new').removeClass('tolose text-color-red');
                            $('#team1_bet_count_new').addClass('towin text-color-green');
                        } else {
                            $('#team1_bet_count_new').text(parseFloat(finalValue).toFixed(2));
                            $('#team1_bet_count_new').addClass('tolose text-color-red');
                            $('#team1_bet_count_new').removeClass('towin text-color-green');
                        }
                        fval_new = '';
                        var old_value_team2 = $('#team2_total').text();
                        if (old_value_team2 != '') {
                            fval_new = parseFloat(old_value_team2) - parseFloat(fval);
                        }
                        if (fval_new > 0) {
                            $('#team2_bet_count_new').removeClass('tolose text-color-red');
                            $('#team2_bet_count_new').addClass('towin text-color-green');
                            $('#team2_bet_count_new').show();
                            $('#team2_bet_count_new').text(fval_new);
                        } else {
                            $('#team2_bet_count_new').addClass('tolose text-color-red');
                            $('#team2_bet_count_new').removeClass('towin text-color-green');
                            $('#team2_bet_count_new').show();
                            $('#team2_bet_count_new').text(fval_new);
                        }
                        var old_value_team3 = $('#draw_total').text();
                        if ($('#team3').val() != '') {
                            if (old_value_team3 != '') {
                                fval = parseFloat(old_value_team3) - parseFloat(fval);
                            }
                            if (fval > 0) {
                                $('#draw_bet_count_new').removeClass('tolose text-color-red');
                                $('#draw_bet_count_new').addClass('towin text-color-green');
                                $('#draw_bet_count_new').show();
                                $('#draw_bet_count_new').text(fval);
                            } else {
                                $('#draw_bet_count_new').addClass('tolose text-color-red');
                                $('#draw_bet_count_new').removeClass('towin text-color-green');
                                $('#draw_bet_count_new').show();
                                $('#draw_bet_count_new').text(fval);
                            }
                        }
                    } else if (old_team2.trim() == team.trim()) {
                        $('#team2_bet_count_new').show();

                        var old_value = $('#team2_total').text();
                        if (old_value != '') {
                            finalValue = parseFloat(old_value) + parseFloat(finalValue);
                        }
                        if (finalValue > 0) {
                            $('#team2_bet_count_new').text(finalValue.toFixed(2));
                            $('#team2_bet_count_new').removeClass('tolose text-color-red');
                            $('#team2_bet_count_new').addClass('towin text-color-green');
                        } else {
                            $('#team2_bet_count_new').text(finalValue.toFixed(2));
                            $('#team2_bet_count_new').addClass('tolose text-color-red');
                            $('#team2_bet_count_new').removeClass('towin text-color-green');
                        }
                        fval_new = '';
                        var old_value_team1 = $('#team1_total').text();
                        if (old_value_team1 != '') {
                            fval_new = parseFloat(old_value_team1) - parseFloat(fval);
                        }
                        if (fval_new > 0) {
                            $('#team1_bet_count_new').removeClass('tolose text-color-red');
                            $('#team1_bet_count_new').addClass('towin text-color-green');
                            $('#team1_bet_count_new').show();
                            $('#team1_bet_count_new').text(fval_new.toFixed(2));
                        } else {
                            $('#team1_bet_count_new').addClass('tolose text-color-red');
                            $('#team1_bet_count_new').removeClass('towin text-color-green');
                            $('#team1_bet_count_new').show();
                            $('#team1_bet_count_new').text(fval_new.toFixed(2));
                        }
                        var old_value_team3 = $('#draw_total').text();
                        if ($('#team3').val() != '') {
                            if (old_value_team3 != '') {
                                fval = parseFloat(old_value_team3) - parseFloat(fval);
                            }
                            if (fval > 0) {
                                $('#draw_bet_count_new').removeClass('tolose text-color-red');
                                $('#draw_bet_count_new').addClass('towin text-color-green');
                                $('#draw_bet_count_new').show();
                                $('#draw_bet_count_new').text(fval.toFixed(2));
                            } else {
                                $('#draw_bet_count_new').addClass('tolose text-color-red');
                                $('#draw_bet_count_new').removeClass('towin text-color-green');
                                $('#draw_bet_count_new').show();
                                $('#draw_bet_count_new').text(fval.toFixed(2));
                            }
                        }
                    } else if (old_team3.trim() == team.trim()) {
                        if ($('#team3').val() != '') {
                            $('#draw_bet_count_new').show();
                            var old_value = $('#draw_total').text();
                            if (old_value != '') {
                                finalValue = parseFloat(old_value) + parseFloat(finalValue);
                            }
                            if (finalValue > 0) {
                                $('#draw_bet_count_new').removeClass('tolose text-color-red');
                                $('#draw_bet_count_new').addClass('towin text-color-green');
                                $('#draw_bet_count_new').show();
                                $('#draw_bet_count_new').text(finalValue.toFixed(2));
                            } else {
                                $('#draw_bet_count_new').addClass('tolose text-color-red');
                                $('#draw_bet_count_new').removeClass('towin text-color-green');
                                $('#draw_bet_count_new').show();
                                $('#draw_bet_count_new').text(finalValue.toFixed(2));
                            }

                            fval_new = '';
                            var old_value_team1 = $('#team1_total').text();
                            if (old_value_team1 != '') {
                                fval_new = parseFloat(old_value_team1) - parseFloat(fval);
                            }
                            if (fval_new > 0) {
                                $('#team1_bet_count_new').show();
                                $('#team1_bet_count_new').removeClass('tolose text-color-red');
                                $('#team1_bet_count_new').addClass('towin text-color-green');
                                $('#team1_bet_count_new').text(fval_new.toFixed(2));
                            } else {
                                $('#team1_bet_count_new').show();
                                $('#team1_bet_count_new').addClass('tolose text-color-red');
                                $('#team1_bet_count_new').removeClass('towin text-color-green');
                                $('#team1_bet_count_new').text(fval_new.toFixed(2));
                            }

                            fval_new = '';
                            var old_value_team2 = $('#team2_total').text();
                            if (old_value_team2 != '') {
                                fval_new = parseFloat(old_value_team2) - parseFloat(fval);
                            }
                            if (fval_new > 0) {
                                $('#team2_bet_count_new').removeClass('tolose text-color-red');
                                $('#team2_bet_count_new').addClass('towin text-color-green');
                                $('#team2_bet_count_new').show();
                                $('#team2_bet_count_new').text(fval_new.toFixed(2));
                            } else {
                                $('#team2_bet_count_new').addClass('tolose text-color-red');
                                $('#team2_bet_count_new').removeClass('towin text-color-green');
                                $('#team2_bet_count_new').show();
                                $('#team2_bet_count_new').text(fval_new.toFixed(2));
                            }
                        }
                    }
                } else if ($('#betSide').val() == 'lay') {
                    if (old_team1.trim() == team.trim()) {
                        $('#team1_bet_count_new').show();
                        var old_value = $('#team1_total').text();
                        if (old_value != '') {
                            finalValue = parseFloat(old_value) - parseFloat(finalValue);
                        }
                        if (finalValue > 0) {
                            $('#team1_bet_count_new').show();
                            $('#team1_bet_count_new').text(finalValue.toFixed(2));
                            $('#team1_bet_count_new').removeClass('tolose text-color-red');
                            $('#team1_bet_count_new').addClass('towin text-color-green');
                        } else {
                            $('#team1_bet_count_new').show();
                            $('#team1_bet_count_new').text(finalValue.toFixed(2));
                            $('#team1_bet_count_new').addClass('tolose text-color-red');
                            $('#team1_bet_count_new').removeClass('towin text-color-green');
                        }
                        fval_new = '';
                        var old_value_team2 = $('#team2_total').text();
                        if (old_value_team2 != '') {
                            fval_new = parseFloat(old_value_team2) + parseFloat(fval);
                        }
                        if (fval_new > 0) {
                            $('#team2_bet_count_new').removeClass('tolose text-color-red');
                            $('#team2_bet_count_new').addClass('towin text-color-green');
                            $('#team2_bet_count_new').show();
                            $('#team2_bet_count_new').text(fval_new.toFixed(2));
                        } else {
                            $('#team2_bet_count_new').addClass('tolose text-color-red');
                            $('#team2_bet_count_new').removeClass('towin text-color-green');
                            $('#team2_bet_count_new').show();
                            $('#team2_bet_count_new').text(fval_new.toFixed(2));
                        }
                        if ($('#team3').val() != '') {
                            var old_value_team3 = $('#draw_total').text();
                            if ($('#team3').val() != '') {
                                if (old_value_team3 != '') {
                                    fval = parseFloat(old_value_team3) + parseFloat(fval);
                                }
                                if (fval > 0) {
                                    $('#draw_bet_count_new').removeClass('tolose text-color-red');
                                    $('#draw_bet_count_new').addClass('towin text-color-green');
                                    $('#draw_bet_count_new').show();
                                    $('#draw_bet_count_new').text(fval.toFixed(2));
                                } else {
                                    $('#draw_bet_count_new').addClass('tolose text-color-red');
                                    $('#draw_bet_count_new').removeClass('towin text-color-green');
                                    $('#draw_bet_count_new').show();
                                    $('#draw_bet_count_new').text(fval.toFixed(2));
                                }
                            }
                        }
                    } else if (old_team2.trim() == team.trim()) {
                        var old_value = $('#team2_total').text();
                        if (old_value != '') {
                            finalValue = parseFloat(old_value) - parseFloat(finalValue);
                        }
                        if (finalValue > 0) {
                            $('#team2_bet_count_new').show();
                            $('#team2_bet_count_new').text(finalValue.toFixed(2));
                            $('#team2_bet_count_new').removeClass('tolose text-color-red');
                            $('#team2_bet_count_new').addClass('towin text-color-green');
                        } else {
                            $('#team2_bet_count_new').show();
                            $('#team2_bet_count_new').text(finalValue.toFixed(2));
                            $('#team2_bet_count_new').addClass('tolose text-color-red');
                            $('#team2_bet_count_new').removeClass('towin text-color-green');
                        }
                        fval_new = '';
                        var old_value_team1 = $('#team1_total').text();
                        if (old_value_team1 != '') {
                            fval_new = parseFloat(old_value_team1) + parseFloat(fval);
                        }
                        if (fval_new > 0) {
                            $('#team1_bet_count_new').removeClass('tolose text-color-red');
                            $('#team1_bet_count_new').addClass('towin text-color-green');
                            $('#team1_bet_count_new').show();
                            $('#team1_bet_count_new').text(fval_new.toFixed(2));
                        } else {
                            $('#team1_bet_count_new').addClass('tolose text-color-red');
                            $('#team1_bet_count_new').removeClass('towin text-color-green');
                            $('#team1_bet_count_new').show();
                            $('#team1_bet_count_new').text(fval_new.toFixed(2));
                        }
                        if ($('#team3').val() != '') {
                            fval_new = '';
                            var old_value_draw = $('#draw_total').text();
                            if (old_value_draw != '') {
                                fval_new = parseFloat(old_value_draw) + parseFloat(fval);
                            }
                            if (fval_new > 0) {
                                $('#draw_bet_count_new').removeClass('tolose text-color-red');
                                $('#draw_bet_count_new').addClass('towin text-color-green');
                                $('#draw_bet_count_new').show();
                                $('#draw_bet_count_new').text(fval_new.toFixed(2));
                            } else {
                                $('#draw_bet_count_new').addClass('tolose text-color-red');
                                $('#draw_bet_count_new').removeClass('towin text-color-green');
                                $('#draw_bet_count_new').show();
                                $('#draw_bet_count_new').text(fval_new.toFixed(2));
                            }
                        }
                    } else if (old_team3.trim() == team.trim()) {
                        fval_new = '';
                        var old_value_team1 = $('#team1_total').text();
                        if (old_value_team1 != '') {
                            fval_new = parseFloat(old_value_team1) + parseFloat(fval);
                        }
                        if (fval_new > 0) {
                            $('#team1_bet_count_new').show();
                            $('#team1_bet_count_new').text(fval_new.toFixed(2));
                            $('#team1_bet_count_new').removeClass('tolose text-color-red');
                            $('#team1_bet_count_new').addClass('towin text-color-green');
                        } else {
                            $('#team1_bet_count_new').show();
                            $('#team1_bet_count_new').text(fval_new.toFixed(2));
                            $('#team1_bet_count_new').addClass('tolose text-color-red');
                            $('#team1_bet_count_new').removeClass('towin text-color-green');
                        }
                        fval_new = '';
                        var old_value_team2 = $('#team2_total').text();
                        if (old_value_team2 != '') {
                            fval_new = parseFloat(old_value_team2) + parseFloat(fval);
                        }
                        if (fval_new > 0) {
                            $('#team2_bet_count_new').removeClass('tolose text-color-red');
                            $('#team2_bet_count_new').addClass('towin text-color-green');
                            $('#team2_bet_count_new').show();
                            $('#team2_bet_count_new').text(fval_new.toFixed(2));
                        } else {
                            $('#team2_bet_count_new').addClass('tolose text-color-red');
                            $('#team2_bet_count_new').removeClass('towin text-color-green');
                            $('#team2_bet_count_new').show();
                            $('#team2_bet_count_new').text(fval_new.toFixed(2));
                        }

                        if ($('#team3').val() != '') {
                            var old_value = $('#draw_total').text();
                            if (old_value != '') {
                                finalValue = parseFloat(old_value) - parseFloat(finalValue);
                            }
                            if (finalValue > 0) {
                                $('#draw_bet_count_new').addClass('towin text-color-green');
                                $('#draw_bet_count_new').removeClass('tolose text-color-red');
                                $('#draw_bet_count_new').show();
                                $('#draw_bet_count_new').text(finalValue.toFixed(2));
                            } else {
                                $('#draw_bet_count_new').removeClass('towin text-color-green');
                                $('#draw_bet_count_new').addClass('tolose text-color-red');
                                $('#draw_bet_count_new').show();
                                $('#draw_bet_count_new').text(finalValue.toFixed(2));
                            }
                        }
                    }
                }
            } else if ($('#betTypeAdd').val() == 'BOOKMAKER') {
                if ($('#betSide').val() == 'back') {
                    if (old_team1.trim() == team.trim()) {
                        $('#team1_betBM_count_new').show();
                        var old_value = $('#team1_BM_total').text();
                        if (old_value != '') {
                            finalValue = parseFloat(old_value) + parseFloat(finalValue);
                        }
                        if (finalValue > 0) {
                            $('#team1_betBM_count_new').text(finalValue.toFixed(2));
                            $('#team1_betBM_count_new').removeClass('tolose text-color-red');
                            $('#team1_betBM_count_new').addClass('towin text-color-green');
                        } else {
                            $('#team1_betBM_count_new').text(parseFloat(finalValue).toFixed(2));
                            $('#team1_betBM_count_new').addClass('tolose text-color-red');
                            $('#team1_betBM_count_new').removeClass('towin text-color-green');
                        }
                        fval_new = '';
                        var old_value_team2 = $('#team2_BM_total').text();
                        if (old_value_team2 != '') {
                            fval_new = parseFloat(old_value_team2) - parseFloat(fval);
                        }
                        if (fval_new > 0) {
                            $('#team2_betBM_count_new').removeClass('tolose text-color-red');
                            $('#team2_betBM_count_new').addClass('towin text-color-green');
                            $('#team2_betBM_count_new').show();
                            $('#team2_beBMt_count_new').text(fval_new);
                        } else {
                            $('#team2_betBM_count_new').addClass('tolose text-color-red');
                            $('#team2_betBM_count_new').removeClass('towin text-color-green');
                            $('#team2_betBM_count_new').show();
                            $('#team2_betBM_count_new').text(fval_new);
                        }
                        var old_value_team3 = $('#draw_BM_total').text();
                        if ($('#team3').val() != '') {
                            if (old_value_team3 != '') {
                                fval = parseFloat(old_value_team3) - parseFloat(fval);
                            }
                            if (fval > 0) {
                                $('#draw_betBM_count_new').removeClass('tolose text-color-red');
                                $('#draw_betBM_count_new').addClass('towin text-color-green');
                                $('#draw_betBM_count_new').show();
                                $('#draw_betBM_count_new').text(fval);
                            } else {
                                $('#draw_betBM_count_new').addClass('tolose text-color-red');
                                $('#draw_betBM_count_new').removeClass('towin text-color-green');
                                $('#draw_betBM_count_new').show();
                                $('#draw_betBM_count_new').text(fval);
                            }
                        }
                    } else if (old_team2.trim() == team.trim()) {
                        $('#team2_betBM_count_new').show();

                        var old_value = $('#team2_BM_total').text();
                        if (old_value != '') {
                            finalValue = parseFloat(old_value) + parseFloat(finalValue);
                        }
                        if (finalValue > 0) {
                            $('#team2_betBM_count_new').text(finalValue.toFixed(2));
                            $('#team2_betBM_count_new').removeClass('tolose text-color-red');
                            $('#team2_betBM_count_new').addClass('towin text-color-green');
                        } else {
                            $('#team2_betBM_count_new').text(finalValue.toFixed(2));
                            $('#team2_betBM_count_new').removeClass('tolose text-color-red');
                            $('#team2_betBM_count_new').addClass('towin text-color-green');
                        }
                        fval_new = '';
                        var old_value_team1 = $('#team1_BM_total').text();
                        if (old_value_team1 != '') {
                            fval_new = parseFloat(old_value_team1) - parseFloat(fval);
                        }
                        if (fval_new > 0) {
                            $('#team1_betBM_count_new').removeClass('tolose text-color-red');
                            $('#team1_betBM_count_new').addClass('towin text-color-green');
                            $('#team1_betBM_count_new').show();
                            $('#team1_betBM_count_new').text(fval_new.toFixed(2));
                        } else {
                            $('#team1_betBM_count_new').addClass('tolose text-color-red');
                            $('#team1_betBM_count_new').removeClass('towin text-color-green');
                            $('#team1_betBM_count_new').show();
                            $('#team1_betBM_count_new').text(fval_new.toFixed(2));
                        }
                        var old_value_team3 = $('#draw_BM_total').text();
                        if ($('#team3').val() != '') {
                            if (old_value_team3 != '') {
                                fval = parseFloat(old_value_team3) - parseFloat(fval);
                            }
                            if (fval > 0) {
                                $('#draw_betBM_count_new').removeClass('tolose text-color-red');
                                $('#draw_betBM_count_new').addClass('towin text-color-green');
                                $('#draw_betBM_count_new').show();
                                $('#draw_betBM_count_new').text(fval.toFixed(2));
                            } else {
                                $('#draw_betBM_count_new').addClass('tolose text-color-red');
                                $('#draw_betBM_count_new').removeClass('towin text-color-green');
                                $('#draw_betBM_count_new').show();
                                $('#draw_betBM_count_new').text(fval.toFixed(2));
                            }
                        }
                    } else if (old_team3.trim() == team.trim()) {
                        if ($('#team3').val() != '') {
                            $('#draw_betBM_count_new').show();
                            var old_value = $('#draw_BM_total').text();
                            if (old_value != '') {
                                finalValue = parseFloat(old_value) + parseFloat(finalValue);
                            }
                            if (finalValue > 0) {
                                $('#draw_betBM_count_new').removeClass('tolose text-color-red');
                                $('#draw_betBM_count_new').addClass('towin text-color-green');
                                $('#draw_betBM_count_new').show();
                                $('#draw_betBM_count_new').text(finalValue.toFixed(2));
                            } else {
                                $('#draw_betBM_count_new').addClass('tolose text-color-red');
                                $('#draw_betBM_count_new').removeClass('towin text-color-green');
                                $('#draw_betBM_count_new').show();
                                $('#draw_betBM_count_new').text(finalValue.toFixed(2));
                            }

                            fval_new = '';
                            var old_value_team1 = $('#team1_total').text();
                            if (old_value_team1 != '') {
                                fval_new = parseFloat(old_value_team1) - parseFloat(fval);
                            }
                            if (fval_new > 0) {
                                $('#team1_betBM_count_new').show();
                                $('#team1_betBM_count_new').removeClass('tolose text-color-red');
                                $('#team1_betBM_count_new').addClass('towin text-color-green');
                                $('#team1_betBM_count_new').text(fval_new.toFixed(2));
                            } else {
                                $('#team1_betBM_count_new').show();
                                $('#team1_betBM_count_new').addClass('tolose text-color-red');
                                $('#team1_betBM_count_new').removeClass('towin text-color-green');
                                $('#team1_betBM_count_new').text(fval_new.toFixed(2));
                            }

                            fval_new = '';
                            var old_value_team2 = $('#team2_BM_total').text();
                            if (old_value_team2 != '') {
                                fval_new = parseFloat(old_value_team2) - parseFloat(fval);
                            }
                            if (fval_new > 0) {
                                $('#team2_betBM_count_new').removeClass('tolose text-color-red');
                                $('#team2_betBM_count_new').addClass('towin text-color-green');
                                $('#team2_betBM_count_new').show();
                                $('#team2_betBM_count_new').text(fval_new.toFixed(2));
                            } else {
                                $('#team2_betBM_count_new').addClass('tolose text-color-red');
                                $('#team2_betBM_count_new').removeClass('towin text-color-green');
                                $('#team2_betBM_count_new').show();
                                $('#team2_betBM_count_new').text(fval_new.toFixed(2));
                            }
                        }
                    }
                } else if ($('#betSide').val() == 'lay') {
                    if (old_team1.trim() == team.trim()) {
                        $('#team1_betBM_count_new').show();
                        var old_value = $('#team1_BM_total').text();
                        if (old_value != '') {
                            finalValue = parseFloat(old_value) - parseFloat(finalValue);
                        }
                        if (finalValue > 0) {
                            $('#team1_betBM_count_new').show();
                            $('#team1_betBM_count_new').text(finalValue.toFixed(2));
                            $('#team1_betBM_count_new').removeClass('tolose text-color-red');
                            $('#team1_betBM_count_new').addClass('towin text-color-green');
                        } else {
                            $('#team1_betBM_count_new').show();
                            $('#team1_betBM_count_new').text(finalValue.toFixed(2));
                            $('#team1_betBM_count_new').addClass('tolose text-color-red');
                            $('#team1_betBM_count_new').removeClass('towin text-color-green');
                        }
                        fval_new = '';
                        var old_value_team2 = $('#team2_BM_total').text();
                        if (old_value_team2 != '') {
                            fval_new = parseFloat(old_value_team2) + parseFloat(fval);
                        }
                        if (fval_new > 0) {
                            $('#team2_betBM_count_new').removeClass('tolose text-color-red');
                            $('#team2_betBM_count_new').addClass('towin text-color-green');
                            $('#team2_betBM_count_new').show();
                            $('#team2_betBM_count_new').text(fval_new.toFixed(2));
                        } else {
                            $('#team2_betBM_count_new').addClass('tolose text-color-red');
                            $('#team2_betBM_count_new').removeClass('towin text-color-green');
                            $('#team2_betBM_count_new').show();
                            $('#team2_betBM_count_new').text(fval_new.toFixed(2));
                        }
                        if ($('#team3').val() != '') {
                            var old_value_team3 = $('#draw_BM_total').text();
                            if ($('#team3').val() != '') {
                                if (old_value_team3 != '') {
                                    fval = parseFloat(old_value_team3) + parseFloat(fval);
                                }
                                if (fval > 0) {
                                    $('#draw_betBM_count_new').removeClass('tolose text-color-red');
                                    $('#draw_betBM_count_new').addClass('towin text-color-green');
                                    $('#draw_betBM_count_new').show();
                                    $('#draw_betBM_count_new').text(fval.toFixed(2));
                                } else {
                                    $('#draw_betBM_count_new').addClass('tolose text-color-red');
                                    $('#draw_betBM_count_new').removeClass('towin text-color-green');
                                    $('#draw_betBM_count_new').show();
                                    $('#draw_betBM_count_new').text(fval.toFixed(2));
                                }
                            }
                        }
                    } else if (old_team2.trim() == team.trim()) {
                        var old_value = $('#team2_BM_total').text();
                        if (old_value != '') {
                            finalValue = parseFloat(old_value) - parseFloat(finalValue);
                        }
                        if (finalValue > 0) {
                            $('#team2_betBM_count_new').show();
                            $('#team2_betBM_count_new').text(finalValue.toFixed(2));
                            $('#team2_betBM_count_new').removeClass('tolose text-color-red');
                            $('#team2_betBM_count_new').addClass('towin text-color-green');
                        } else {
                            $('#team2_betBM_count_new').show();
                            $('#team2_betBM_count_new').text(finalValue.toFixed(2));
                            $('#team2_betBM_count_new').addClass('tolose text-color-red');
                            $('#team2_betBM_count_new').removeClass('towin text-color-green');
                        }
                        fval_new = '';
                        var old_value_team1 = $('#team1_BM_total').text();
                        if (old_value_team1 != '') {
                            fval_new = parseFloat(old_value_team1) + parseFloat(fval);
                        }
                        if (fval_new > 0) {
                            $('#team1_betBM_count_new').removeClass('tolose text-color-red');
                            $('#team1_betBM_count_new').addClass('towin text-color-green');
                            $('#team1_betBM_count_new').show();
                            $('#team1_betBM_count_new').text(fval_new.toFixed(2));
                        } else {
                            $('#team1_betBM_count_new').addClass('tolose text-color-red');
                            $('#team1_betBM_count_new').removeClass('towin text-color-green');
                            $('#team1_betBM_count_new').show();
                            $('#team1_betBM_count_new').text(fval_new.toFixed(2));
                        }

                        if ($('#team3').val() != '') {
                            fval_new = '';
                            var old_value_draw = $('#draw_BM_total').text();
                            if (old_value_draw != '') {
                                fval_new = parseFloat(old_value_draw) + parseFloat(fval);
                            }
                            if (fval_new > 0) {
                                $('#draw_betBM_count_new').removeClass('tolose text-color-red');
                                $('#draw_betBM_count_new').addClass('towin text-color-green');
                                $('#draw_betBM_count_new').show();
                                $('#draw_betBM_count_new').text(fval_new.toFixed(2));
                            } else {
                                $('#draw_betBM_count_new').addClass('tolose text-color-red');
                                $('#draw_betBM_count_new').removeClass('towin text-color-green');
                                $('#draw_betBM_count_new').show();
                                $('#draw_betBM_count_new').text(fval_new.toFixed(2));
                            }
                        }
                    } else if (old_team3.trim() == team.trim()) {
                        fval_new = '';
                        var old_value_team1 = $('#team1_BM_total').text();
                        if (old_value_team1 != '') {
                            fval_new = parseFloat(old_value_team1) + parseFloat(fval);
                        }
                        if (fval_new > 0) {
                            $('#team1_betBM_count_new').show();
                            $('#team1_betBM_count_new').text(fval_new.toFixed(2));
                            $('#team1_betBM_count_new').removeClass('tolose text-color-red');
                            $('#team1_betBM_count_new').addClass('towin text-color-green');
                        } else {
                            $('#team1_betBM_count_new').show();
                            $('#team1_betBM_count_new').text(fval_new.toFixed(2));
                            $('#team1_betBM_count_new').addClass('tolose text-color-red');
                            $('#team1_betBM_count_new').removeClass('towin text-color-green');
                        }
                        fval_new = '';
                        var old_value_team2 = $('#team2_BM_total').text();
                        if (old_value_team2 != '') {
                            fval_new = parseFloat(old_value_team2) + parseFloat(fval);
                        }
                        if (fval_new > 0) {
                            $('#team2_betBM_count_new').removeClass('tolose text-color-red');
                            $('#team2_betBM_count_new').addClass('towin text-color-green');
                            $('#team2_betBM_count_new').show();
                            $('#team2_betBM_count_new').text(fval_new.toFixed(2));
                        } else {
                            $('#team2_betBM_count_new').addClass('tolose text-color-red');
                            $('#team2_betBM_count_new').removeClass('towin text-color-green');
                            $('#team2_betBM_count_new').show();
                            $('#team2_betBM_count_new').text(fval_new.toFixed(2));
                        }
                        if ($('#team3').val() != '') {
                            var old_value = $('#draw_BM_total').text();
                            if (old_value != '') {
                                finalValue = parseFloat(old_value) - parseFloat(finalValue);
                            }
                            if (finalValue > 0) {
                                $('#draw_betBM_count_new').addClass('towin text-color-green');
                                $('#draw_betBM_count_new').removeClass('tolose text-color-red');
                                $('#draw_betBM_count_new').show();
                                $('#draw_betBM_count_new').text(finalValue.toFixed(2));
                            } else {
                                $('#draw_betBM_count_new').removeClass('towin text-color-green');
                                $('#draw_betBM_count_new').addClass('tolose text-color-red');
                                $('#draw_betBM_count_new').show();
                                $('#draw_betBM_count_new').text(finalValue.toFixed(2));
                            }
                        }
                    }
                }
            } else if ($('#betTypeAdd').val() == 'SESSION') {
                matchVal = $("#odds_volume").val();

                finalValue = (parseFloat(fval) * parseFloat(matchVal)) / parseFloat(100);
                $('.profil').html(finalValue.toFixed(2));
            }
        }

        $(document).on('click', '.ODDSBack', function () {
            $("#odds_val").attr('readonly', false);
            $(".amountint").text('0');
            $("#inputStake").val('');

            var teamname1 = $('.team1').text();
            var teamname2 = $('.team2').text();
            var teamname3 = $('.team3').text();

            $('#team1').val(teamname1.trim());
            $('#team2').val(teamname2.trim());
            $('#team3').val(teamname3.trim());
            var team = $('#' + $(this).attr("data-team")).val();
            $('#team_id').val($(this).attr("data-team"));
            $('#teamNameBet').val(team);
            $('#betTypeAdd').val('ODDS');
            $('#betSide').val('back');
            $('#profit_liability').text('Profit');
            $('#back_or_lay').text('Back (Bet For)');
            $('#bet_for').text(team);

        });
        $(document).on('click', '.ODDSLay', function () {
            $("#odds_val").attr('readonly', false);
            $(".amountint").text('0');
            $("#inputStake").val('');

            var teamname1 = $('.team1').text();
            var teamname2 = $('.team2').text();
            var teamname3 = $('.team3').text();

            $('#team1').val(teamname1.trim());
            $('#team2').val(teamname2.trim());
            $('#team3').val(teamname3.trim());
            var team = $('#' + $(this).attr("data-team")).val();
            $('#team_id').val($(this).attr("data-team"));
            $('#teamNameBet').val(team);
            $('#betTypeAdd').val('ODDS');
            $('#betSide').val('lay');
            $('#profit_liability').text('Liability');
            $('#back_or_lay').text('Lay (Bet Against)');
            $('#bet_for').text(team);
        });
        //for BM
        $(document).on('click', '.BmBack', function () {

            var teamname1 = $('.team1').text();
            var teamname2 = $('.team2').text();
            var teamname3 = $('.team3').text();

            $('#team1').val(teamname1.trim());
            $('#team2').val(teamname2.trim());
            $('#team3').val(teamname3.trim());
            var team = $('#' + $(this).attr("data-team")).val();
            $('#teamNameBet').val(team);
            $('#betTypeAdd').val('BOOKMAKER');
            $('#betSide').val('back');
            $('#profit_liability').text('Profit');
            $('#back_or_lay').text('Back (Bet For)');
            $('#bet_for').text(team);

            $("#odds_val").attr('readonly', true);
        });
        $(document).on('click', '.BmLay', function () {

            var teamname1 = $('.team1').text();
            var teamname2 = $('.team2').text();
            var teamname3 = $('.team3').text();

            $('#team1').val(teamname1.trim());
            $('#team2').val(teamname2.trim());
            $('#team3').val(teamname3.trim());
            var team = $('#' + $(this).attr("data-team")).val();
            $('#teamNameBet').val(team);
            $('#betTypeAdd').val('BOOKMAKER');
            $('#betSide').val('lay');
            $('#profit_liability').text('Liability');
            $('#back_or_lay').text('Lay (Bet Against)');
            $('#bet_for').text(team);
            $("#odds_val").attr('readonly', true);
        });
        //for fancy
        $(document).on('click', '.FancyBack', function () {
            $(".amountint").text('0');
            $("#inputStake").val('');

            var teamname1 = $('.team1').text();
            var teamname2 = $('.team2').text();
            var teamname3 = $('.team3').text();

            $('#team1').val(teamname1.trim());
            $('#team2').val(teamname2.trim());
            $('#team3').val(teamname3.trim());
            var team = $(this).attr("data-team");
            $('#teamNameBet').val(team);
            $('#betTypeAdd').val('SESSION');
            $('#betSide').val('back');
            $('#profit_liability').text('Profit');
            $('#back_or_lay').text('Back (Bet For)');
            $('#bet_for').text(team);

            $("#odds_val").attr('readonly', true);
        });
        $(document).on('click', '.FancyLay', function () {

            var teamname1 = $('.team1').text();
            var teamname2 = $('.team2').text();
            var teamname3 = $('.team3').text();

            $('#team1').val(teamname1.trim());
            $('#team2').val(teamname2.trim());
            $('#team3').val(teamname3.trim());
            var team = $(this).attr("data-team");
            $('#teamNameBet').val(team);
            $('#betTypeAdd').val('SESSION');
            $('#betSide').val('lay');
            $('#profit_liability').text('Liability');
            $('#back_or_lay').text('Lay (Bet Against)');
            $('#bet_for').text(team);

            $("#odds_val").attr('readonly', true);
        });

        function call_display_bet_list(dval) {
            var _token = $("input[name='_token']").val();
            $.ajax({
                type: "POST",
                url: '{{route("GetOtherMatchBet")}}',
                data: {
                    match_id: dval,
                    _token: _token
                },
                timeout: 10000,
                success: function (data) {

                    $('#divbetlist').html(data);
                }
            });
        }

        if (getUser != '') {
            setTimeout(() => {
                var match_sel = $('#select_bet_on_match').val();
                if (match_sel == "" || match_sel == null)
                    match_sel = '{{$match->event_id}}~~' + 'All';

                call_display_bet_list(match_sel);
            }, 10);
        }

        function saveBetcall(confirmBet) {
            var bet_type = $('#betTypeAdd').val();
            var tm = $('#team_id').val();
            var width = $(window).width();
            if (width < 990) {
                if ($('#mobile_odds').val() == '') {
                    if (bet_type == 'ODDS') {
                        $('.fail_alertmessage').show();
                        $('.fail_alertmessage').text('Odds Amount Required!');
                        var div = $('#fail_mobile_message').html();
                        $('.tr_' + tm + '_td_mobile').show();
                        $('.tr_' + tm + '_td_mobile').html(div);
                        setTimeout(function () {
                            $('.fail_alertmessage').hide();
                            $('.tr_' + tm + '_td_mobile').html('')
                        }, 5000);
                        return false;
                    } else if (bet_type == 'BOOKMAKER') {
                        $('.tr_' + tm + '_BM_td_mobile').show();
                        $('.fail_alertmessage').show();
                        $('.fail_alertmessage').text('Odds Amount Required!');
                        var div = $('#fail_mobile_message').html();
                        $('.tr_' + tm + '_BM_td_mobile').show();
                        $('.tr_' + tm + '_BM_td_mobile').html(div);
                        setTimeout(function () {
                            $('.fail_alertmessage').hide();
                            $('.tr_' + tm + '_BM_td_mobile').html('')
                        }, 5000);
                        return false;
                    } else {
                        var posi = $('#session_position').val();
                        $('.fail_alertmessage').show();
                        $('.tr_team' + posi + '_fancy_td_mobile').show();
                        $('.fail_alertmessage').text('Odds Amount Required!');
                        var div = $('#fail_mobile_message').html();
                        $('.tr_team' + posi + '_fancy_td_mobile').show();
                        $('.tr_team' + posi + '_fancy_td_mobile').html(div);
                        setTimeout(function () {
                            $('.fail_alertmessage').hide();
                            $('.tr_team' + posi + '_fancy_td_mobile').html('')
                        }, 5000);
                        return false;
                    }
                }

                if ($('#inputStake_mobile').val() == '') {

                    if (bet_type == 'ODDS') {
                        $('.fail_alertmessage').show();
                        $('.fail_alertmessage').text('Stack Value Required!');
                        var div = $('#fail_mobile_message').html();
                        $('.tr_' + tm + '_td_mobile').show();
                        $('.tr_' + tm + '_td_mobile').html(div);
                        setTimeout(function () {
                            $('.fail_alertmessage').hide();
                            $('.tr_' + tm + '_td_mobile').html('')
                        }, 5000);
                        return false;
                    } else if (bet_type == 'BOOKMAKER') {
                        $('.fail_alertmessage').show();
                        $('.tr_' + tm + '_BM_td_mobile').show();
                        $('.fail_alertmessage').text('Stack Value Required!');
                        var div = $('#fail_mobile_message').html();
                        $('.tr_' + tm + '_BM_td_mobile').show();
                        $('.tr_' + tm + '_BM_td_mobile').html(div);
                        setTimeout(function () {
                            $('.fail_alertmessage').hide();
                            $('.tr_' + tm + '_BM_td_mobile').html('')
                        }, 5000);
                        return false;
                    } else {
                        $('.fail_alertmessage').show();
                        var posi = $('#session_position').val();
                        $('.tr_team' + posi + '_fancy_td_mobile').show();
                        $('.fail_alertmessage').text('Stack Value Required!');
                        var div = $('#fail_mobile_message').html();
                        $('.tr_team' + posi + '_fancy_td_mobile').show();
                        $('.tr_team' + posi + '_fancy_td_mobile').html(div);
                        setTimeout(function () {
                            $('.fail_alertmessage').hide();
                            $('.tr_team' + posi + '_fancy_td_mobile').html('')
                        }, 5000);
                        return false;
                    }
                }

            } else {
                if ($('#odds_val').val() == '') {
                    toastr.error('Odds Amount Required!');
                    return false;
                }
                if ($('#inputStake').val() == '') {
                    toastr.error('Stack Value Required!');
                    return false;
                }
                if (!($('#comfirmBets').prop('checked'))) {
                    toastr.error('Please Confirm That You Are Agree To Place This Bet!');
                    return false;
                }
            }

            $('.btn-success').prop("disabled", false);
            getBalance();
            var player_balance = $('#tot_bal').val();
            var bet_type = $('#betTypeAdd').val();
            var bet_site = $('#betSide').val();
            var bet_odds = $('.amountint').text();
            var bet_amount = $('#odds_val').val();
            var stack = $('#inputStake').val();
            var team_name = $('#teamNameBet').val();
            var bet_profit = $('#bet-profit').val();
            var bet_cal_amt = $('.amountint').text();
            var odds_limit = $('#odds_limit').val();

            if (bet_amount == '' || bet_amount <= 0 || isNaN(bet_amount)) {
                var width = $(window).width();
                if (width < 990) {
                    var tm = $('#team_id').val();
                    if (bet_type == 'ODDS') {
                        $('.fail_alertmessage').show();
                        $('.fail_alertmessage').text('Min Max Bet Limit Exceed!');
                        var div = $('#fail_mobile_message').html();
                        $('.tr_' + tm + '_td_mobile').show();
                        $('.tr_' + tm + '_td_mobile').html(div);
                        setTimeout(function () {
                            $('.fail_alertmessage').hide();
                            $('.tr_' + tm + '_td_mobile').html('')
                        }, 5000);
                        $(".amountint").text(" ");
                        $('#inputStake').val(" ");
                        $('#odds_val').val(" ");
                        $(".showForm").hide();
                        return false;
                    } else if (bet_type == 'BOOKMAKER') {
                        $('.fail_alertmessage').show();
                        $('.tr_' + tm + '_BM_td_mobile').show();
                        $('.fail_alertmessage').text('Min Max Bet Limit Exceed!');
                        var div = $('#fail_mobile_message').html();
                        $('.tr_' + tm + '_BM_td_mobile').show();
                        $('.tr_' + tm + '_BM_td_mobile').html(div);
                        setTimeout(function () {
                            $('.fail_alertmessage').hide();
                            $('.tr_' + tm + '_BM_td_mobile').html('')
                        }, 5000);
                        $(".amountint").text(" ");
                        $('#inputStake').val(" ");
                        $('#odds_val').val(" ");
                        $(".showForm").hide();
                        return false;
                    } else {
                        $('.fail_alertmessage').show();
                        var posi = $('#session_position').val();
                        $('.tr_team' + posi + '_fancy_td_mobile').show();
                        $('.fail_alertmessage').text('Min Max Bet Limit Exceed!');
                        var div = $('#fail_mobile_message').html();
                        $('.tr_team' + posi + '_fancy_td_mobile').show();
                        $('.tr_team' + posi + '_fancy_td_mobile').html(div);
                        setTimeout(function () {
                            $('.fail_alertmessage').hide();
                            $('.tr_team' + posi + '_fancy_td_mobile').html('')
                        }, 5000);
                        $(".amountint").text(" ");
                        $('#inputStake').val(" ");
                        $('#odds_val').val(" ");
                        $(".showForm").hide();
                        return false;
                    }
                } else {
                    toastr.error('Min Max Bet Limit Exceed!');
                    $(".amountint").text(" ");
                    $('#inputStake').val(" ");
                    $('#odds_val').val(" ");
                    $(".showForm").hide();
                    return false;
                }
            }
            if (bet_type == 'ODDS' || bet_type == 'BOOKMAKER') {
                if (bet_odds == '' || bet_odds <= 0 || isNaN(bet_odds)) {
                    var width = $(window).width();
                    if (width < 990) {
                        var tm = $('#team_id').val();
                        if (bet_type == 'ODDS') {
                            $('.fail_alertmessage').show();
                            $('.fail_alertmessage').text('Bet Odds changed!');
                            var div = $('#fail_mobile_message').html();
                            $('.tr_' + tm + '_td_mobile').show();
                            $('.tr_' + tm + '_td_mobile').html(div);
                            setTimeout(function () {
                                $('.fail_alertmessage').hide();
                                $('.tr_' + tm + '_td_mobile').html('')
                            }, 5000);
                            $(".amountint").text(" ");
                            $('#inputStake').val(" ");
                            $('#odds_val').val(" ");
                            return false;
                        } else if (bet_type == 'BOOKMAKER') {
                            $('.fail_alertmessage').show();
                            $('.tr_' + tm + '_BM_td_mobile').show();
                            $('.fail_alertmessage').text('Bet Odds changed!');
                            var div = $('#fail_mobile_message').html();
                            $('.tr_' + tm + '_BM_td_mobile').show();
                            $('.tr_' + tm + '_BM_td_mobile').html(div);
                            setTimeout(function () {
                                $('.fail_alertmessage').hide();
                                $('.tr_' + tm + '_BM_td_mobile').html('')
                            }, 5000);
                            $(".amountint").text(" ");
                            $('#inputStake').val(" ");
                            $('#odds_val').val(" ");
                            return false;
                        } else {
                            $('.fail_alertmessage').show();
                            var posi = $('#session_position').val();
                            $('.tr_team' + posi + '_fancy_td_mobile').show();
                            $('.fail_alertmessage').text('Bet Odds changed!');
                            var div = $('#fail_mobile_message').html();
                            $('.tr_team' + posi + '_fancy_td_mobile').show();
                            $('.tr_team' + posi + '_fancy_td_mobile').html(div);
                            setTimeout(function () {
                                $('.fail_alertmessage').hide();
                                $('.tr_team' + posi + '_fancy_td_mobile').html('')
                            }, 5000);
                            $(".amountint").text(" ");
                            $('#inputStake').val(" ");
                            $('#odds_val').val(" ");
                            return false;
                        }
                    } else {
                        toastr.error('Bet Odds changed!');
                        $(".amountint").text(" ");
                        $('#inputStake').val(" ");
                        $('#odds_val').val(" ");
                        return false;
                    }
                }
            }
            if (bet_type == 'ODDS') {
                var chk = '<?php echo $chk; ?>';
                if (chk == 1) {
                    var stack = $('#inputStake').val();
                    if (parseInt(stack) < parseInt($('#div_min_bet_odds_limit').text())) {
                        var width = $(window).width();
                        if (width < 990) {
                            var tm = $('#team_id').val();
                            if (bet_type == 'ODDS') {
                                $('.fail_alertmessage').show();
                                $('.fail_alertmessage').text('Minimum bets is ' + $('#div_min_bet_odds_limit').text() + '!');
                                var div = $('#fail_mobile_message').html();
                                $('.tr_' + tm + '_td_mobile').show();
                                $('.tr_' + tm + '_td_mobile').html(div);
                                setTimeout(function () {
                                    $('.fail_alertmessage').hide();
                                    $('.tr_' + tm + '_td_mobile').html('')
                                }, 5000);
                                $(".showForm").hide();
                                $(".amountint").text(" ");
                                $('#inputStake').val(" ");
                                $('#odds_val').val(" ");
                                return false;
                            } else if (bet_type == 'BOOKMAKER') {
                                $('.fail_alertmessage').show();
                                $('.tr_' + tm + '_BM_td_mobile').show();
                                $('.fail_alertmessage').text('Minimum bets is ' + $('#div_min_bet_odds_limit').text() + '!');
                                var div = $('#fail_mobile_message').html();
                                $('.tr_' + tm + '_BM_td_mobile').show();
                                $('.tr_' + tm + '_BM_td_mobile').html(div);
                                setTimeout(function () {
                                    $('.fail_alertmessage').hide();
                                    $('.tr_' + tm + '_BM_td_mobile').html('')
                                }, 5000);
                                $(".showForm").hide();
                                $(".amountint").text(" ");
                                $('#inputStake').val(" ");
                                $('#odds_val').val(" ");
                                return false;
                            } else {
                                $('.fail_alertmessage').show();
                                var posi = $('#session_position').val();
                                $('.tr_team' + posi + '_fancy_td_mobile').show();
                                $('.fail_alertmessage').text('Minimum bets is ' + $('#div_min_bet_odds_limit').text() + '!');
                                var div = $('#fail_mobile_message').html();
                                $('.tr_team' + posi + '_fancy_td_mobile').show();
                                $('.tr_team' + posi + '_fancy_td_mobile').html(div);
                                setTimeout(function () {
                                    $('.fail_alertmessage').hide();
                                    $('.tr_team' + posi + '_fancy_td_mobile').html('')
                                }, 5000);
                                $(".showForm").hide();
                                $(".amountint").text(" ");
                                $('#inputStake').val(" ");
                                $('#odds_val').val(" ");
                                return false;
                            }
                        } else {
                            toastr.error('Minimum bets is ' + $('#div_min_bet_odds_limit').text() + '!');
                            $(".showForm").hide();
                            $(".amountint").text(" ");
                            $('#inputStake').val(" ");
                            $('#odds_val').val(" ");
                            return false;
                        }


                    }

                    if (parseInt(stack) > parseInt($('#div_max_bet_odds_limit').text())) {
                        var width = $(window).width();
                        if (width < 990) {
                            var tm = $('#team_id').val();
                            if (bet_type == 'ODDS') {
                                $('.fail_alertmessage').show();
                                $('.fail_alertmessage').text('Maximum bets is ' + $('#div_max_bet_odds_limit').text() + '!');
                                var div = $('#fail_mobile_message').html();
                                $('.tr_' + tm + '_td_mobile').show();
                                $('.tr_' + tm + '_td_mobile').html(div);
                                setTimeout(function () {
                                    $('.fail_alertmessage').hide();
                                    $('.tr_' + tm + '_td_mobile').html('')
                                }, 5000);
                                $(".showForm").hide();
                                $(".amountint").text(" ");
                                $('#inputStake').val(" ");
                                $('#odds_val').val(" ");
                                return false;
                            } else if (bet_type == 'BOOKMAKER') {
                                $('.fail_alertmessage').show();
                                $('.tr_' + tm + '_BM_td_mobile').show();
                                $('.fail_alertmessage').text('Maximum bets is ' + $('#div_max_bet_odds_limit').text() + '!');
                                var div = $('#fail_mobile_message').html();
                                $('.tr_' + tm + '_BM_td_mobile').show();
                                $('.tr_' + tm + '_BM_td_mobile').html(div);
                                setTimeout(function () {
                                    $('.fail_alertmessage').hide();
                                    $('.tr_' + tm + '_BM_td_mobile').html('')
                                }, 5000);
                                $(".showForm").hide();
                                $(".amountint").text(" ");
                                $('#inputStake').val(" ");
                                $('#odds_val').val(" ");
                                return false;
                            } else {
                                $('.fail_alertmessage').show();
                                var posi = $('#session_position').val();
                                $('.tr_team' + posi + '_fancy_td_mobile').show();
                                $('.fail_alertmessage').text('Maximum bets is ' + $('#div_max_bet_odds_limit').text() + '!');
                                var div = $('#fail_mobile_message').html();
                                $('.tr_team' + posi + '_fancy_td_mobile').show();
                                $('.tr_team' + posi + '_fancy_td_mobile').html(div);
                                setTimeout(function () {
                                    $('.fail_alertmessage').hide();
                                    $('.tr_team' + posi + '_fancy_td_mobile').html('')
                                }, 5000);
                                $(".showForm").hide();
                                $(".amountint").text(" ");
                                $('#inputStake').val(" ");
                                $('#odds_val').val(" ");
                                return false;
                            }
                        } else {
                            toastr.error('Maximum bets is ' + $('#div_max_bet_odds_limit').text() + '!');
                            $(".showForm").hide();
                            $(".amountint").text(" ");
                            $('#inputStake').val(" ");
                            $('#odds_val').val(" ");
                            return false;
                        }
                    }

                    if (confirmBet == false) {

                        if (bet_site == 'back') {
                            $("#betConfirmationForMobileModal .odds-bet-type .lay").hide();
                            $("#betConfirmationForMobileModal .odds-bet-type .back").show();
                        } else {
                            $("#betConfirmationForMobileModal .odds-bet-type .back").hide();
                            $("#betConfirmationForMobileModal .odds-bet-type .lay").show();
                        }
                        $("#betConfirmationForMobileModal .odds-title").html(team_name);

                        $("#betConfirmationForMobileModal .odds .value").html($(".mobile_tr_common_class #mobile_odds").val());

                        $("#betConfirmationForMobileModal .stake .value").html($(".mobile_tr_common_class #inputStake_mobile").val());

                        if (bet_site == 'back') {
                            $("#betConfirmationForMobileModal .profit .title").html('Profit');
                        } else {
                            $("#betConfirmationForMobileModal .profit .title").html('Liability');
                        }

                        $("#betConfirmationForMobileModal .profit .value").html($('.amountint').text());

                        $("#betConfirmationForMobileModal").modal("show");
                        return false;
                    } else {
                        if (width < 990) {
                            $("#betConfirmationForMobileModal").modal("hide");
                        }

                        $('.amountint').text(bet_odds);
                        var bet_type = $('#betTypeAdd').val();
                        var bet_site = $('#betSide').val();
                        var bet_odds = $('.amountint').text();
                        var bet_amount = $('#odds_val').val();
                        var team_name = $('#teamNameBet').val();
                        var bet_profit = $('#bet-profit').val();
                        var parameter = "";
                        var teamname1 = $('#team1').val();
                        var teamname2 = $('#team2').val();
                        var teamname3 = $('#team3').val();
                        var team1_total = $('#team1_total').text();
                        var team2_total = $('#team2_total').text();
                        var team3_total = $('#draw_total').text();
                        var team1_BM_total = $('#team1_BM_total').text();
                        var team2_BM_total = $('#team2_BM_total').text();
                        var draw_BM_total = $('#draw_BM_total').text();

                        var hid_fancy = $('#hid_fancy').val();

                        var fancy_total = 0;
                        for (var f = 0; f < hid_fancy; f++) {
                            if ($('#Fancy_Total_' + f)) {
                                if ($('#Fancy_Total_' + f).text() != '')
                                    fancy_total = parseFloat(fancy_total) + parseFloat($('#Fancy_Total_' + f).text());
                            }
                        }

                        if ($('#all_session_total').val() > fancy_total || fancy_total == 0)
                            fancy_total = $('#all_session_total').val();

                        var back_team_0 = $('#final_odds_back_val_team1').val();
                        var back_team_1 = $('#final_odds_back_val_team2').val();
                        var back_team_2 = $('#final_odds_back_val_team3').val();
                        var lay_team_0 = $('#final_odds_lay_val_team1').val();
                        var lay_team_1 = $('#final_odds_lay_val_team2').val();
                        var lay_team_2 = $('#final_odds_lay_val_team3').val();
                        //ancy_Total_Div

                        if (teamname1 == team_name) {
                            parameter = "&teamname2=" + encodeURIComponent(teamname2) + "&teamname3=" + encodeURIComponent(teamname3);
                        } else if (teamname2 == team_name) {
                            parameter = "&teamname1=" + encodeURIComponent(teamname1) + "&teamname3=" + encodeURIComponent(teamname3);
                        } else {
                            parameter = "&teamname1=" + encodeURIComponent(teamname1) + "&teamname2=" + encodeURIComponent(teamname2);
                        }
                        var tm = $('#team_id').val();
                        var width = $(window).width();
                        if (width < 990) {
                            var div = '<div class="betloaderimage"></div>';
                            $('.tr_' + tm + '_td_mobile').show();
                            $('.tr_' + tm + '_td_mobile').html(div);
                            $(".betloaderimage").show();
                        } else {
                            document.getElementById("site_bet_loading").style.display = "block";
                            document.getElementById("betslip-block").style.display = "none";
                        }
                        //document.getElementById("site_bet_loading").style.display = "block";
                        //document.getElementById("betslip-block").style.display = "none";


                        var delay = '<?php echo $delayTime; ?>';
                        setTimeout(function () {
                            $.ajax({
                                url: '{{route("MyBetStore")}}',
                                dataType: 'json',
                                type: "POST",
                                data: "sportID={{$match->sports_id}}&match_id={{$match->event_id}}&_token={{csrf_token()}}&bet_profit=" + bet_profit + "&bet_type=" + bet_type + "&bet_side=" + bet_site + "&bet_odds=" + bet_amount + "&bet_amount=" + stack + "&team_name=" + team_name + parameter + '&stack=' + stack + '&bet_cal_amt=' + bet_cal_amt + '&team1_total=' + team1_total + '&team2_total=' + team2_total + '&team3_total=' + team3_total + '&team1=' + teamname1 + '&team2=' + teamname2 + '&team3=' + teamname3 + '&team1_BM_total=' + team1_BM_total + '&team2_BM_total=' + team2_BM_total + '&team3_BM_total=' + draw_BM_total + '&back_team_0=' + back_team_0 + '&back_team_1=' + back_team_1 + '&back_team_2=' + back_team_2 + '&lay_team_0=' + lay_team_0 + '&lay_team_1=' + lay_team_1 + '&lay_team_2=' + lay_team_2 + '&fancy_total=' + fancy_total,
                                success: function (data) {
                                    if (data.status.trim() == 'true') {
                                        var width = $(window).width();
                                        if (width < 990) {
                                            //fail_mobile_message
                                            $('.success_alertmessage').show();
                                            $('.success_alertmessage').text(data.msg);
                                            var div = $('#success_mobile_message').html();
                                            $('.tr_' + tm + '_td_mobile').show();
                                            $('.tr_' + tm + '_td_mobile').html(div);
                                            setTimeout(function () {
                                                $('.success_alertmessage').hide();
                                                $('.tr_' + tm + '_td_mobile').html('')
                                            }, 5000);
                                        } else
                                            toastr.success(data.msg);

                                        $('#odds_val').val(' ');
                                        $('#inputStake').val(' ');
                                        $('.amountint').text(' ');
                                        $(".showForm").hide();

                                        $('#bet_display_table').show();

                                        if (bet_type == 'ODDS') {
                                            $("#team1_bet_count_new").hide();
                                            $('#team2_bet_count_new').hide();
                                            $('#draw_bet_count_new').hide();

                                            var finalValue = $("#team1_bet_count_new").html();
                                            if (finalValue > 0) {
                                                $('#team1_bet_count_old #team1_total').text(finalValue);
                                                $('#team1_bet_count_old').removeClass('tolose text-color-red');
                                                $('#team1_bet_count_old').addClass('towin text-color-green');
                                            } else {
                                                $('#team1_bet_count_old #team1_total').text(finalValue);
                                                $('#team1_bet_count_old').addClass('tolose text-color-red');
                                                $('#team1_bet_count_old').removeClass('towin text-color-green');
                                            }

                                            var finalValue = $("#team2_bet_count_new").html();
                                            if (finalValue > 0) {
                                                $('#team2_bet_count_old #team2_total').text(finalValue);
                                                $('#team2_bet_count_old').removeClass('tolose text-color-red');
                                                $('#team2_bet_count_old').addClass('towin text-color-green');
                                            } else {
                                                $('#team2_bet_count_old #team2_total').text(finalValue);
                                                $('#team2_bet_count_old').addClass('tolose text-color-red');
                                                $('#team2_bet_count_old').removeClass('towin text-color-green');
                                            }

                                            var finalValue = $("#draw_bet_count_new").html();
                                            if (finalValue > 0) {
                                                $('#draw_bet_count_old #draw_total').text(finalValue);
                                                $('#draw_bet_count_old').removeClass('tolose text-color-red');
                                                $('#draw_bet_count_old').addClass('towin text-color-green');
                                            } else {
                                                $('#draw_bet_count_old #draw_total').text(finalValue);
                                                $('#draw_bet_count_old').addClass('tolose text-color-red');
                                                $('#draw_bet_count_old').removeClass('towin text-color-green');
                                            }
                                        } else if (bet_type == 'BOOKMAKER') {
                                            $("#team1_betBM_count_new").hide();
                                            $('#team2_betBM_count_new').hide();
                                            $('#draw_betBM_count_new').hide();

                                            var finalValue = $("#team1_betBM_count_new").html();
                                            if (finalValue > 0) {
                                                $('#team1_betBM_count_old #team1_BM_total').text(finalValue);
                                                $('#team1_betBM_count_old').removeClass('tolose text-color-red');
                                                $('#team1_betBM_count_old').addClass('towin text-color-green');
                                            } else {
                                                $('#team1_betBM_count_old #team1_BM_total').text(finalValue);
                                                $('#team1_betBM_count_old').addClass('tolose text-color-red');
                                                $('#team1_betBM_count_old').removeClass('towin text-color-green');
                                            }

                                            var finalValue = $("#team2_betBM_count_new").html();
                                            if (finalValue > 0) {
                                                $('#team2_betBM_count_old #team2_BM_total').text(finalValue);
                                                $('#team2_betBM_count_old').removeClass('tolose text-color-red');
                                                $('#team2_betBM_count_old').addClass('towin text-color-green');
                                            } else {
                                                $('#team2_betBM_count_old #team2_BM_total').text(finalValue);
                                                $('#team2_betBM_count_old').addClass('tolose text-color-red');
                                                $('#team2_betBM_count_old').removeClass('towin text-color-green');
                                            }

                                            var finalValue = $("#draw_betBM_count_new").html();
                                            if (finalValue > 0) {
                                                $('#draw_betBM_count_old #draw_BM_total').text(finalValue);
                                                $('#draw_betBM_count_old').removeClass('tolose text-color-red');
                                                $('#draw_betBM_count_old').addClass('towin text-color-green');
                                            } else {
                                                $('#draw_betBM_count_old #draw_BM_total').text(finalValue);
                                                $('#draw_betBM_count_old').addClass('tolose text-color-red');
                                                $('#draw_betBM_count_old').removeClass('towin text-color-green');
                                            }
                                        }

                                        // get_oddstable();
                                        var match_sel = $('#select_bet_on_match').val();
                                        if (match_sel == "" || match_sel == null)
                                            match_sel = '{{$match->event_id}}~~' + 'All';
                                        call_display_bet_list(match_sel);
                                        getBalance();

                                        var width = $(window).width();
                                        if (width < 990) {
                                            //for mobile delay hide
                                            $(".betloaderimage").hide();

                                        } else {
                                            document.getElementById("site_bet_loading").style.display = "none";
                                            document.getElementById("betslip-block").style.display = "block";
                                        }
                                        //for mobile responsive

                                    } else {
                                        var width = $(window).width();
                                        if (width < 990) {
                                            //
                                            $('.fail_alertmessage').show();
                                            $('.fail_alertmessage').text(data.msg);
                                            var div = $('#fail_mobile_message').html();
                                            $('.tr_' + tm + '_td_mobile').show();
                                            $('.tr_' + tm + '_td_mobile').html(div);
                                            setTimeout(function () {
                                                $('.fail_alertmessage').hide();
                                                $('.tr_' + tm + '_td_mobile').html('')
                                            }, 5000);
                                        } else
                                            toastr.error(data.msg);

                                        $(".showForm").hide();
                                        $('#odds_val').val(' ');
                                        $('#inputStake').val(' ');
                                        $('.amountint').text(' ');


                                        var width = $(window).width();
                                        if (width < 990) {
                                            //for mobile delay hide
                                            $(".betloaderimage").hide();
                                        } else {
                                            document.getElementById("site_bet_loading").style.display = "none";
                                            document.getElementById("betslip-block").style.display = "block";
                                        }
                                        $('#team1_bet_count_new').text('');
                                        $('#team1_bet_count_new').hide();
                                        $('#team2_bet_count_new').text('');
                                        $('#team2_bet_count_new').hide();
                                        $('#draw_bet_count_new').text('');
                                        $('#draw_bet_count_new').hide();
                                    }
                                }
                            });
                        }, delay);
                    }
                } else {
                    var x = document.getElementById("snackbar");
                    x.className = "show";
                    setTimeout(function () {
                        x.className = x.className.replace("show", "");
                    }, 1000);
                }
            } else if (bet_type == 'BOOKMAKER') {
                var chkb = '<?php echo $chkb; ?>';
                if (chkb == 1) {

                    $('.amountint').text(bet_odds);
                    var bet_type = $('#betTypeAdd').val();
                    var bet_site = $('#betSide').val();
                    var bet_odds = $('.amountint').text();
                    var bet_position = $('#odds_position').val();
                    var bet_amount = $('#odds_val').val();
                    var team_name = $('#teamNameBet').val();
                    var bet_profit = $('#bet-profit').text();
                    var parameter = "";
                    var teamname1 = $('#team1').val();
                    var teamname2 = $('#team2').val();
                    var teamname3 = $('#team3').val();
                    var team1_total = $('#team1_total').text();
                    var team2_total = $('#team2_total').text();
                    var team3_total = $('#draw_total').text();
                    var team1_BM_total = $('#team1_BM_total').text();
                    var team2_BM_total = $('#team2_BM_total').text();
                    var draw_BM_total = $('#draw_BM_total').text();

                    var hid_fancy = $('#hid_fancy').val();

                    var fancy_total = 0;
                    for (var f = 0; f < hid_fancy; f++) {
                        if ($('#Fancy_Total_' + f)) {
                            if ($('#Fancy_Total_' + f).text() != '')
                                fancy_total = parseFloat(fancy_total) + parseFloat($('#Fancy_Total_' + f).text());
                        }
                    }
                    if ($('#all_session_total').val() > fancy_total || fancy_total == 0)
                        fancy_total = $('#all_session_total').val();

                    var stack = $('#inputStake').val();
                    if (parseInt(stack) < parseInt($('#div_min_bet_bm_limit').text())) {
                        var width = $(window).width();
                        if (width < 990) {
                            var tm = $('#team_id').val();
                            if (bet_type == 'ODDS') {
                                $('.fail_alertmessage').show();
                                $('.fail_alertmessage').text('Minimum bets is ' + $('#div_min_bet_bm_limit').text() + '!');
                                var div = $('#fail_mobile_message').html();
                                $('.tr_' + tm + '_td_mobile').show();
                                $('.tr_' + tm + '_td_mobile').html(div);
                                setTimeout(function () {
                                    $('.fail_alertmessage').hide();
                                    $('.tr_' + tm + '_td_mobile').html('')
                                }, 5000);
                                $(".showForm").hide();
                                $(".amountint").text(" ");
                                $('#inputStake').val(" ");
                                $('#odds_val').val(" ");
                                return false;
                            } else if (bet_type == 'BOOKMAKER') {
                                $('.fail_alertmessage').show();
                                $('.tr_' + tm + '_BM_td_mobile').show();
                                $('.fail_alertmessage').text('Minimum bets is ' + $('#div_min_bet_bm_limit').text() + '!');
                                var div = $('#fail_mobile_message').html();
                                $('.tr_' + tm + '_BM_td_mobile').show();
                                $('.tr_' + tm + '_BM_td_mobile').html(div);
                                setTimeout(function () {
                                    $('.fail_alertmessage').hide();
                                    $('.tr_' + tm + '_BM_td_mobile').html('')
                                }, 5000);
                                $(".showForm").hide();
                                $(".amountint").text(" ");
                                $('#inputStake').val(" ");
                                $('#odds_val').val(" ");
                                return false;
                            } else {
                                $('.fail_alertmessage').show();
                                var posi = $('#session_position').val();
                                $('.tr_team' + posi + '_fancy_td_mobile').show();
                                $('.fail_alertmessage').text('Minimum bets is ' + $('#div_min_bet_bm_limit').text() + '!');
                                var div = $('#fail_mobile_message').html();
                                $('.tr_team' + posi + '_fancy_td_mobile').show();
                                $('.tr_team' + posi + '_fancy_td_mobile').html(div);
                                setTimeout(function () {
                                    $('.fail_alertmessage').hide();
                                    $('.tr_team' + posi + '_fancy_td_mobile').html('')
                                }, 5000);
                                $(".showForm").hide();
                                $(".amountint").text(" ");
                                $('#inputStake').val(" ");
                                $('#odds_val').val(" ");
                                return false;
                            }
                        } else {
                            toastr.error('Minimum bets is ' + $('#div_min_bet_bm_limit').text() + '!');
                            $(".showForm").hide();
                            $(".amountint").text(" ");
                            $('#inputStake').val(" ");
                            $('#odds_val').val(" ");
                            return false;
                        }
                    }
                    if (parseInt(stack) > parseInt($('#div_max_bet_bm_limit').text())) {
                        var width = $(window).width();
                        if (width < 990) {
                            var tm = $('#team_id').val();
                            if (bet_type == 'ODDS') {
                                $('.fail_alertmessage').show();
                                $('.fail_alertmessage').text('Maximum bets is ' + $('#div_max_bet_bm_limit').text() + '!');
                                var div = $('#fail_mobile_message').html();
                                $('.tr_' + tm + '_td_mobile').show();
                                $('.tr_' + tm + '_td_mobile').html(div);
                                setTimeout(function () {
                                    $('.fail_alertmessage').hide();
                                    $('.tr_' + tm + '_td_mobile').html('')
                                }, 5000);
                                $(".showForm").hide();
                                $(".amountint").text(" ");
                                $('#inputStake').val(" ");
                                $('#odds_val').val(" ");
                                return false;
                            } else if (bet_type == 'BOOKMAKER') {
                                $('.fail_alertmessage').show();
                                $('.tr_' + tm + '_BM_td_mobile').show();
                                $('.fail_alertmessage').text('Maximum bets is ' + $('#div_max_bet_bm_limit').text() + '!');
                                var div = $('#fail_mobile_message').html();
                                $('.tr_' + tm + '_BM_td_mobile').show();
                                $('.tr_' + tm + '_BM_td_mobile').html(div);
                                setTimeout(function () {
                                    $('.fail_alertmessage').hide();
                                    $('.tr_' + tm + '_BM_td_mobile').html('')
                                }, 5000);
                                $(".showForm").hide();
                                $(".amountint").text(" ");
                                $('#inputStake').val(" ");
                                $('#odds_val').val(" ");
                                return false;
                            } else {
                                $('.fail_alertmessage').show();
                                var posi = $('#session_position').val();
                                $('.tr_team' + posi + '_fancy_td_mobile').show();
                                $('.fail_alertmessage').text('Maximum bets is ' + $('#div_max_bet_bm_limit').text() + '!');
                                var div = $('#fail_mobile_message').html();
                                $('.tr_team' + posi + '_fancy_td_mobile').show();
                                $('.tr_team' + posi + '_fancy_td_mobile').html(div);
                                setTimeout(function () {
                                    $('.fail_alertmessage').hide();
                                    $('.tr_team' + posi + '_fancy_td_mobile').html('')
                                }, 5000);
                                $(".showForm").hide();
                                $(".amountint").text(" ");
                                $('#inputStake').val(" ");
                                $('#odds_val').val(" ");
                                return false;
                            }
                        } else {
                            toastr.error('Maximum bets is ' + $('#div_max_bet_bm_limit').text() + '!');
                            $(".showForm").hide();
                            $(".amountint").text(" ");
                            $('#inputStake').val(" ");
                            $('#odds_val').val(" ");
                            return false;
                        }
                    }
                    if (teamname1 == team_name) {
                        parameter = "&teamname2=" + encodeURIComponent(teamname2) + "&teamname3=" + encodeURIComponent(teamname3);
                    } else if (teamname2 == team_name) {
                        parameter = "&teamname1=" + encodeURIComponent(teamname1) + "&teamname3=" + encodeURIComponent(teamname3);
                    } else {
                        parameter = "&teamname1=" + encodeURIComponent(teamname1) + "&teamname2=" + encodeURIComponent(teamname2);
                    }
                    var tm = $('#team_id').val();
                    var width = $(window).width();
                    if (width < 990) {
                        var div = '<div class="betloaderimage"></div>';
                        $('.tr_' + tm + '_BM_td_mobile').show();
                        $('.tr_' + tm + '_BM_td_mobile').html(div);
                        $(".betloaderimage").show();
                    } else {
                        document.getElementById("site_bet_loading").style.display = "block";
                        document.getElementById("betslip-block").style.display = "none";
                    }


                    var delay = '<?php echo $delayTime_BM; ?>';

                    setTimeout(function () {
                        $.ajax({
                            url: '{{route("MyBetStore")}}',
                            dataType: 'json',
                            type: "POST",
                            data: "sportID={{$match->sports_id}}&match_id={{$match->event_id}}&_token={{csrf_token()}}&bet_profit=" + bet_profit + "&bet_type=" + bet_type + "&bet_side=" + bet_site + "&bet_odds=" + bet_amount + "&bet_amount=" + stack + "&team_name=" + team_name + parameter + '&stack=' + stack + '&bet_cal_amt=' + bet_cal_amt + '&team1_total=' + team1_total + '&team2_total=' + team2_total + '&team3_total=' + team3_total + '&team1=' + teamname1 + '&team2=' + teamname2 + '&team3=' + teamname3 + '&team1_BM_total=' + team1_BM_total + '&team2_BM_total=' + team2_BM_total + '&team3_BM_total=' + draw_BM_total + '&fancy_total=' + fancy_total + '&bet_position=' + bet_position,
                            success: function (data) {
                                if (data.status.trim() == 'true') {

                                    if (bet_type == 'BOOKMAKER') {
                                        $("#team1_betBM_count_new").hide();
                                        $('#team2_betBM_count_new').hide();
                                        $('#draw_betBM_count_new').hide();

                                        var finalValue = $("#team1_betBM_count_new").html();
                                        if (finalValue > 0) {
                                            $('#team1_betBM_count_old #team1_BM_total').text(finalValue);
                                            $('#team1_betBM_count_old').removeClass('tolose text-color-red');
                                            $('#team1_betBM_count_old').addClass('towin text-color-green');
                                        } else {
                                            $('#team1_betBM_count_old #team1_BM_total').text(finalValue);
                                            $('#team1_betBM_count_old').addClass('tolose text-color-red');
                                            $('#team1_betBM_count_old').removeClass('towin text-color-green');
                                        }

                                        var finalValue = $("#team2_betBM_count_new").html();
                                        if (finalValue > 0) {
                                            $('#team2_betBM_count_old #team2_BM_total').text(finalValue);
                                            $('#team2_betBM_count_old').removeClass('tolose text-color-red');
                                            $('#team2_betBM_count_old').addClass('towin text-color-green');
                                        } else {
                                            $('#team2_betBM_count_old #team2_BM_total').text(finalValue);
                                            $('#team2_betBM_count_old').addClass('tolose text-color-red');
                                            $('#team2_betBM_count_old').removeClass('towin text-color-green');
                                        }

                                        var finalValue = $("#draw_betBM_count_new").html();
                                        if (finalValue > 0) {
                                            $('#draw_betBM_count_old #draw_BM_total').text(finalValue);
                                            $('#draw_betBM_count_old').removeClass('tolose text-color-red');
                                            $('#draw_betBM_count_old').addClass('towin text-color-green');
                                        } else {
                                            $('#draw_betBM_count_old #draw_BM_total').text(finalValue);
                                            $('#draw_betBM_count_old').addClass('tolose text-color-red');
                                            $('#draw_betBM_count_old').removeClass('towin text-color-green');
                                        }
                                    }


                                    var width = $(window).width();
                                    if (width < 990) {

                                        $('.success_alertmessage').show();
                                        $('.success_alertmessage').text(data.msg);
                                        var div = $('#success_mobile_message').html();
                                        $('.tr_' + tm + '_BM_td_mobile').show();
                                        $('.tr_' + tm + '_BM_td_mobile').html(div);
                                        setTimeout(function () {
                                            $('.success_alertmessage').hide();
                                            $('.tr_' + tm + '_BM_td_mobile').hide()
                                        }, 5000);
                                    } else
                                        toastr.success(data.msg);

                                    $('#odds_val').val(' ');
                                    $('#inputStake').val(' ');
                                    $('.amountint').text(' ');
                                    $(".showForm").hide();
                                    $('#bet_display_table').show();
                                    // get_BMtable();
                                    var match_sel = $('#select_bet_on_match').val();
                                    if (match_sel == "")
                                        match_sel = '{{$match->event_id}}~~' + 'All';
                                    call_display_bet_list(match_sel);
                                    getBalance();


                                } else {
                                    var width = $(window).width();
                                    if (width < 990) {

                                        $('.fail_alertmessage').show();
                                        $('.tr_' + tm + '_BM_td_mobile').show();
                                        $('.fail_alertmessage').text(data.msg);
                                        var div = $('#fail_mobile_message').html();
                                        $('.tr_' + tm + '_BM_td_mobile').show();
                                        $('.tr_' + tm + '_BM_td_mobile').html(div);
                                        setTimeout(function () {
                                            $('.fail_alertmessage').hide();
                                            $('.tr_' + tm + '_BM_td_mobile').html('')
                                        }, 5000);
                                    } else
                                        toastr.error(data.msg);
                                    $(".showForm").hide();
                                    $('#odds_val').val(' ');
                                    $('#inputStake').val(' ');
                                    $('.amountint').text(' ');
                                    $('#team1_betBM_count_new').text('');
                                    $('#team1_betBM_count_new').hide();
                                    $('#team2_betBM_count_new').text('');
                                    $('#team2_betBM_count_new').hide();
                                    $('#draw_betBM_count_new').text('');
                                    $('#draw_betBM_count_new').hide();
                                }
                                var width = $(window).width();
                                if (width < 990) {
                                    //for mobile delay hide
                                    $(".betloaderimage").hide();
                                } else {
                                    document.getElementById("site_bet_loading").style.display = "none";
                                    document.getElementById("betslip-block").style.display = "block";
                                }
                            }
                        });
                    }, delay);
                } else {
                    var x = document.getElementById("snackbar");
                    x.className = "show";
                    setTimeout(function () {
                        x.className = x.className.replace("show", "");
                    }, 1000);
                }
            } else {
                var chkf = '<?php echo $chkf; ?>';
                if (chkf == 1) {
                    var parameter = "";
                    var bet_type = $('#betTypeAdd').val();
                    var bet_site = $('#betSide').val();
                    var bet_odds = $('.amountint').text();
                    var bet_amount = $('#odds_val').val();

                    var stack = $('#inputStake').val();
                    if (parseInt(stack) < parseInt($('#div_min_bet_fancy_limit').text())) {
                        var width = $(window).width();
                        if (width < 990) {
                            var tm = $('#team_id').val();
                            if (bet_type == 'ODDS') {
                                $('.fail_alertmessage').show();
                                $('.fail_alertmessage').text('Minimum bets is ' + $('#div_min_bet_fancy_limit').text() + '!');
                                var div = $('#fail_mobile_message').html();
                                $('.tr_' + tm + '_td_mobile').show();
                                $('.tr_' + tm + '_td_mobile').html(div);
                                setTimeout(function () {
                                    $('.fail_alertmessage').hide();
                                    $('.tr_' + tm + '_td_mobile').html('')
                                }, 5000);
                                $(".showForm").hide();
                                $('#inputStake').val(" ");
                                $('#odds_val').val(" ");
                                return false;

                            } else if (bet_type == 'BOOKMAKER') {
                                $('.fail_alertmessage').show();
                                $('.tr_' + tm + '_BM_td_mobile').show();
                                $('.fail_alertmessage').text('Minimum bets is ' + $('#div_min_bet_fancy_limit').text() + '!');
                                var div = $('#fail_mobile_message').html();
                                $('.tr_' + tm + '_BM_td_mobile').show();
                                $('.tr_' + tm + '_BM_td_mobile').html(div);
                                setTimeout(function () {
                                    $('.fail_alertmessage').hide();
                                    $('.tr_' + tm + '_BM_td_mobile').html('')
                                }, 5000);
                                $(".showForm").hide();
                                $('#inputStake').val(" ");
                                $('#odds_val').val(" ");
                                return false;

                            } else {
                                $('.fail_alertmessage').show();
                                var posi = $('#session_position').val();
                                $('.tr_team' + posi + '_fancy_td_mobile').show();
                                $('.fail_alertmessage').text('Minimum bets is ' + $('#div_min_bet_fancy_limit').text() + '!');
                                var div = $('#fail_mobile_message').html();
                                $('.tr_team' + posi + '_fancy_td_mobile').show();
                                $('.tr_team' + posi + '_fancy_td_mobile').html(div);
                                setTimeout(function () {
                                    $('.fail_alertmessage').hide();
                                    $('.tr_team' + posi + '_fancy_td_mobile').html('')
                                }, 5000);
                                $(".showForm").hide();
                                $('#inputStake').val(" ");
                                $('#odds_val').val(" ");
                                return false;

                            }
                        } else {
                            toastr.error('Minimum bets is ' + $('#div_min_bet_fancy_limit').text() + '!');
                            $(".showForm").hide();
                            $('#inputStake').val(" ");
                            $('#odds_val').val(" ");
                            return false;
                        }
                    }
                    if (parseInt(stack) > parseInt($('#div_max_bet_fancy_limit').text())) {
                        var width = $(window).width();
                        if (width < 990) {
                            var tm = $('#team_id').val();
                            if (bet_type == 'ODDS') {
                                $('.fail_alertmessage').show();
                                $('.fail_alertmessage').text('Maximum bets is ' + $('#div_max_bet_fancy_limit').text() + '!');
                                var div = $('#fail_mobile_message').html();
                                $('.tr_' + tm + '_td_mobile').show();
                                $('.tr_' + tm + '_td_mobile').html(div);
                                setTimeout(function () {
                                    $('.fail_alertmessage').hide();
                                    $('.tr_' + tm + '_td_mobile').html('')
                                }, 5000);
                                $(".showForm").hide();
                                $(".amountint").text(" ");
                                $('#inputStake').val(" ");
                                $('#odds_val').val(" ");
                                return false;
                            } else if (bet_type == 'BOOKMAKER') {
                                $('.fail_alertmessage').show();
                                $('.tr_' + tm + '_BM_td_mobile').show();
                                $('.fail_alertmessage').text('Maximum bets is ' + $('#div_max_bet_fancy_limit').text() + '!');
                                var div = $('#fail_mobile_message').html();
                                $('.tr_' + tm + '_BM_td_mobile').show();
                                $('.tr_' + tm + '_BM_td_mobile').html(div);
                                setTimeout(function () {
                                    $('.fail_alertmessage').hide();
                                    $('.tr_' + tm + '_BM_td_mobile').html('')
                                }, 5000);
                                $(".showForm").hide();
                                $(".amountint").text(" ");
                                $('#inputStake').val(" ");
                                $('#odds_val').val(" ");
                                return false;
                            } else {
                                $('.fail_alertmessage').show();
                                var posi = $('#session_position').val();
                                $('.tr_team' + posi + '_fancy_td_mobile').show();
                                $('.fail_alertmessage').text('Maximum bets is ' + $('#div_max_bet_fancy_limit').text() + '!');
                                var div = $('#fail_mobile_message').html();
                                $('.tr_team' + posi + '_fancy_td_mobile').show();
                                $('.tr_team' + posi + '_fancy_td_mobile').html(div);
                                setTimeout(function () {
                                    $('.fail_alertmessage').hide();
                                    $('.tr_team' + posi + '_fancy_td_mobile').html('')
                                }, 5000);
                                $(".showForm").hide();
                                $(".amountint").text(" ");
                                $('#inputStake').val(" ");
                                $('#odds_val').val(" ");
                                return false;
                            }
                        } else {
                            toastr.error('Maximum bets is ' + $('#div_max_bet_fancy_limit').text() + '!');
                            $(".showForm").hide();
                            $(".amountint").text(" ");
                            $('#inputStake').val(" ");
                            $('#odds_val').val(" ");
                            return false;
                        }
                    }

                    var team_name = $('#teamNameBet').val();
                    var bet_profit = $('#bet-profit').text();
                    var parameter = "";
                    var teamname1 = $('#team1').val();
                    var teamname2 = $('#team2').val();
                    var teamname3 = $('#team3').val();
                    var odds_volume = $('#odds_volume').val();
                    var team1_total = $('#team1_total').text();
                    var team2_total = $('#team2_total').text();
                    var team3_total = $('#draw_total').text();
                    var team1_BM_total = $('#team1_BM_total').text();
                    var team2_BM_total = $('#team2_BM_total').text();
                    var draw_BM_total = $('#draw_BM_total').text();
                    var bet_position = $('#odds_position').val();

                    $('#hid_fancy').val($(".fancy-total-amount").length);

                    var hid_fancy = $(".fancy-total-amount").length;
                    var fancy_total = 0;
                    for (var f = 0; f < hid_fancy; f++) {
                        if ($('#Fancy_Total_' + f)) {
                            if ($('#Fancy_Total_' + f).text() != '')
                                fancy_total = parseFloat(fancy_total) + parseFloat($('#Fancy_Total_' + f).text());
                        }
                    }
                    if ($('#all_session_total').val() > fancy_total || fancy_total == 0)
                        fancy_total = $('#all_session_total').val();

                    var width = $(window).width();
                    if (width < 990) {
                        var posi = $('#session_position').val();
                        var div = '<div class="betloaderimage"></div>';
                        //$('.tr_'+posi+'_fancy_td_mobile').show();
                        //$('.tr_'+posi+'_fancy_td_mobile').html(div);
                        //var td='<td class colspan="6">'+div+'</td>';
                        //$('.tr_team'+posi+'_fancy').html(td);
                        $('.tr_team' + posi + '_fancy_td_mobile').html(div);
                        $(".betloaderimage").show();
                    } else {
                        document.getElementById("site_bet_loading").style.display = "block";
                        document.getElementById("betslip-block").style.display = "none";
                    }

                    var tm = $('#team_id').val();
                    var bet_cal_amt = '';


                    var delay = '<?php echo $delayTime_Fancy; ?>';
                    setTimeout(function () {
                        $.ajax({
                            url: '{{route("MyBetStore")}}',
                            dataType: 'json',
                            type: "POST",
                            data: "sportID={{$match->sports_id}}&match_id={{$match->event_id}}&_token={{csrf_token()}}&bet_profit=" + bet_profit + "&bet_type=" + bet_type + "&bet_side=" + bet_site + "&bet_odds=" + bet_amount + "&bet_amount=" + stack + "&team_name=" + team_name + parameter + '&stack=' + stack + '&odds_volume=' + odds_volume + '&bet_cal_amt=' + bet_cal_amt + '&team1=' + teamname1 + '&team2=' + teamname2 + '&team3=' + teamname3 + '&team1_total=' + team1_total + '&team2_total=' + team2_total + '&team3_total=' + team3_total + '&team1=' + teamname1 + '&team2=' + teamname2 + '&team3=' + teamname3 + '&team1_BM_total=' + team1_BM_total + '&team2_BM_total=' + team2_BM_total + '&team3_BM_total=' + draw_BM_total + '&fancy_total=' + fancy_total + '&bet_position=' + bet_position,
                            success: function (data) {
                                if (data.status.trim() == 'true') {
                                    var width = $(window).width();
                                    if (width < 990) {
                                        $('.success_alertmessage').show();
                                        var posi = $('#session_position').val();
                                        $('.tr_team' + posi + '_fancy_td_mobile').show();
                                        $('.success_alertmessage').text(data.msg);
                                        var div = $('#success_mobile_message').html();
                                        $('.tr_team' + posi + '_fancy_td_mobile').show();
                                        $('.tr_team' + posi + '_fancy_td_mobile').html(div);
                                        setTimeout(function () {
                                            $('.success_alertmessage').hide();
                                            $('.tr_team' + posi + '_fancy_td_mobile').html('')
                                        }, 5000);
                                    } else
                                        toastr.success(data.msg);

                                    if (bet_site == 'lay') {
                                        var finalValue = -(parseFloat(stack) * parseFloat(odds_volume)) / parseFloat(100);
                                    } else {
                                        var finalValue = -(parseFloat(stack));
                                    }


                                    var currentSessionBetTotalExposer = data.currentSessionBetTotalExposer;

                                    console.log("currentSessionBetTotalExposer: ", currentSessionBetTotalExposer);
                                    if (width < 990) {
                                        $('.mobile-ui-tr .Fancy_Total_' + bet_position).html(currentSessionBetTotalExposer.toFixed(2));
                                    } else {
                                        $('.desktop-ui-tr .Fancy_Total_' + bet_position).html(currentSessionBetTotalExposer.toFixed(2));
                                    }

                                    $('#odds_val').val(' ');
                                    $('#inputStake').val(' ');
                                    $('.amountint').text(' ');
                                    $(".showForm").hide();
                                    $('#bet_display_table').show();
                                    $('#session_mobile_val_position').val('');
                                    $('#session_mobile_odds_position').val('');

                                    // bet_Fancytable();
                                    var match_sel = $('#select_bet_on_match').val();
                                    if (match_sel == "")
                                        match_sel = '{{$match->event_id}}~~' + 'All';
                                    call_display_bet_list(match_sel);
                                    getBalance();


                                } else {
                                    var width = $(window).width();
                                    if (width < 990) {
                                        $('.fail_alertmessage').show();
                                        var posi = $('#session_position').val();
                                        $('.tr_team' + posi + '_fancy_td_mobile').show();
                                        $('.fail_alertmessage').text(data.msg);
                                        var div = $('#fail_mobile_message').html();
                                        $('.tr_team' + posi + '_fancy_td_mobile').show();
                                        $('.tr_team' + posi + '_fancy_td_mobile').html(div);
                                        setTimeout(function () {
                                            $('.fail_alertmessage').hide();
                                            $('.tr_team' + posi + '_fancy_td_mobile').html('')
                                        }, 5000);
                                        console.log('failure');

                                        //cancel popup

                                        // odds
                                        $('#team1_bet_count_new').hide();
                                        $('#team2_bet_count_new').hide();
                                        $('#draw_bet_count_new').hide();
                                        // bookmaker
                                        $('#team1_betBM_count_new').hide();
                                        $('#team2_betBM_count_new').hide();
                                        $('#draw_betBM_count_new').hide();

                                        $('#team1_bet_count_new').text('');
                                        $('#team1_bet_count_new').hide();
                                        $('#team2_bet_count_new').text('');
                                        $('#team2_bet_count_new').hide();
                                        $('#draw_bet_count_new').text('');
                                        $('#draw_bet_count_new').hide();

                                        /*$(".mobile_tr_common_class").html('');

								$('#session_mobile_val_position').val(' ');
								$('#session_position').val(' ');
								$('#is_session_open_position').val(' ');
								$('#session_mobile_odds_position').val(' ');
								$('#inputStake_mobile').val(0);*/
                                        //end for cancelling popup

                                        $('#is_session_open_position').val(' ');
                                    } else
                                        toastr.error(data.msg);
                                    $(".showForm").hide();
                                    $('#odds_val').val(' ');
                                    $('#inputStake').val(' ');
                                    $('.amountint').text(' ');
                                }


                                $('.pink-bg').removeClass('pink-dark');
                                $('.cyan-bg').removeClass('blue-dark');

                                $('#is_session_open_position').val(' ');
                                var width = $(window).width();
                                if (width < 990) {
                                    //for mobile delay hide
                                    $(".betloaderimage").hide();
                                    //cancel popup
                                    $(".showForm").hide();
                                    // odds
                                    $('#team1_bet_count_new').hide();
                                    $('#team2_bet_count_new').hide();
                                    $('#draw_bet_count_new').hide();
                                    // bookmaker
                                    $('#team1_betBM_count_new').hide();
                                    $('#team2_betBM_count_new').hide();
                                    $('#draw_betBM_count_new').hide();

                                    $('#team1_bet_count_new').text('');
                                    $('#team1_bet_count_new').hide();
                                    $('#team2_bet_count_new').text('');
                                    $('#team2_bet_count_new').hide();
                                    $('#draw_bet_count_new').text('');
                                    $('#draw_bet_count_new').hide();

                                    /*$(".mobile_tr_common_class").html('');

								$('#session_mobile_val_position').val(' ');
								$('#session_position').val(' ');
								$('#is_session_open_position').val(' ');
								$('#session_mobile_odds_position').val(' ');
								$('#inputStake_mobile').val(0);*/
                                    //end for cancelling popup
                                } else {
                                    document.getElementById("site_bet_loading").style.display = "none";
                                    document.getElementById("betslip-block").style.display = "block";
                                }
                            }
                        });
                    }, delay);
                } else {
                    var x = document.getElementById("snackbar");
                    x.className = "show";
                    setTimeout(function () {
                        x.className = x.className.replace("show", "");
                    }, 1000);
                }
            }
        }

        $("#openBetsBtn").click(function () {

            var event_id = '{{$match->event_id}}~~' + 'All';
            var _token = $("input[name='_token']").val();
            $.ajax({
                type: "POST",
                url: '{{route("GetOtherMatchBet")}}',
                data: {
                    match_id: event_id,
                    _token: _token
                },
                timeout: 10000,
                success: function (data) {

                    $('.mobiledivbetlist').html(data);
                }
            });
        });

        $("#validationcode_popup").keypress(function (e) {
            if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                $("#errmsg").html("Digits Only").show().fadeOut("slow");
                return false;
            }
        });
    </script>
    <script type="text/javascript">

        // odds
        $('.td_team1_back_2').on('click', function () {
            $('.td_team1_back_0').addClass('blue-dark');
            $('.td_team2_back_0').removeClass('blue-dark');
            $('.td_team3_back_0').removeClass('blue-dark');
            $('.td_team1_lay_0').removeClass('pink-dark');
            $('.td_team2_lay_0').removeClass('pink-dark');
            $('.td_team3_lay_0').removeClass('pink-dark');
            $('.td_team1_bm_back_0 #back_1 .cyan-bg').removeClass('blue-dark');
            $('.td_team2_bm_back_0 #back_1 .cyan-bg').removeClass('blue-dark');
            $('.td_team1_bm_lay_0 #lay_1').removeClass('pink-dark');
            $('.td_team2_bm_lay_0 #lay_1').removeClass('pink-dark');
        });
        $('.td_team1_back_1').on('click', function () {
            $('.td_team1_back_0').addClass('blue-dark');
            $('.td_team2_back_0').removeClass('blue-dark');
            $('.td_team3_back_0').removeClass('blue-dark');
            $('.td_team1_lay_0').removeClass('pink-dark');
            $('.td_team2_lay_0').removeClass('pink-dark');
            $('.td_team3_lay_0').removeClass('pink-dark');
            $('.td_team1_bm_back_0 #back_1 .cyan-bg').removeClass('blue-dark');
            $('.td_team2_bm_back_0 #back_1 .cyan-bg').removeClass('blue-dark');
            $('.td_team1_bm_lay_0 #lay_1').removeClass('pink-dark');
            $('.td_team2_bm_lay_0 #lay_1').removeClass('pink-dark');
        });
        $('.td_team1_back_0').on('click', function () {
            $('.td_team1_back_0').addClass('blue-dark');
            $('.td_team2_back_0').removeClass('blue-dark');
            $('.td_team3_back_0').removeClass('blue-dark');
            $('.td_team1_lay_0').removeClass('pink-dark');
            $('.td_team2_lay_0').removeClass('pink-dark');
            $('.td_team3_lay_0').removeClass('pink-dark');
            $('.td_team1_bm_back_0 #back_1 .cyan-bg').removeClass('blue-dark');
            $('.td_team2_bm_back_0 #back_1 .cyan-bg').removeClass('blue-dark');
            $('.td_team1_bm_lay_0 #lay_1').removeClass('pink-dark');
            $('.td_team2_bm_lay_0 #lay_1').removeClass('pink-dark');
        });


        $('.td_team2_back_2').on('click', function () {
            $('.td_team2_back_0').addClass('blue-dark');
            $('.td_team1_back_0').removeClass('blue-dark');
            $('.td_team3_back_0').removeClass('blue-dark');
            $('.td_team1_lay_0').removeClass('pink-dark');
            $('.td_team2_lay_0').removeClass('pink-dark');
            $('.td_team3_lay_0').removeClass('pink-dark');
            $('.td_team1_bm_back_0 #back_1 .cyan-bg').removeClass('blue-dark');
            $('.td_team2_bm_back_0 #back_1 .cyan-bg').removeClass('blue-dark');
            $('.td_team1_bm_lay_0 #lay_1').removeClass('pink-dark');
            $('.td_team2_bm_lay_0 #lay_1').removeClass('pink-dark');
        });
        $('.td_team2_back_1').on('click', function () {
            $('.td_team2_back_0').addClass('blue-dark');
            $('.td_team1_back_0').removeClass('blue-dark');
            $('.td_team3_back_0').removeClass('blue-dark');
            $('.td_team1_lay_0').removeClass('pink-dark');
            $('.td_team2_lay_0').removeClass('pink-dark');
            $('.td_team3_lay_0').removeClass('pink-dark');
            $('.td_team1_bm_back_0 #back_1 .cyan-bg').removeClass('blue-dark');
            $('.td_team2_bm_back_0 #back_1 .cyan-bg').removeClass('blue-dark');
            $('.td_team1_bm_lay_0 #lay_1').removeClass('pink-dark');
            $('.td_team2_bm_lay_0 #lay_1').removeClass('pink-dark');
        });
        $('.td_team2_back_0').on('click', function () {
            $('.td_team2_back_0').addClass('blue-dark');
            $('.td_team1_back_0').removeClass('blue-dark');
            $('.td_team3_back_0').removeClass('blue-dark');
            $('.td_team1_lay_0').removeClass('pink-dark');
            $('.td_team2_lay_0').removeClass('pink-dark');
            $('.td_team3_lay_0').removeClass('pink-dark');
            $('.td_team1_bm_back_0 #back_1 .cyan-bg').removeClass('blue-dark');
            $('.td_team2_bm_back_0 #back_1 .cyan-bg').removeClass('blue-dark');
            $('.td_team1_bm_lay_0 #lay_1').removeClass('pink-dark');
            $('.td_team2_bm_lay_0 #lay_1').removeClass('pink-dark');
        });


        $('.td_team3_back_2').on('click', function () {
            $('.td_team3_back_0').addClass('blue-dark');
            $('.td_team2_back_0').removeClass('blue-dark');
            $('.td_team1_back_0').removeClass('blue-dark');
            $('.td_team1_lay_0').removeClass('pink-dark');
            $('.td_team2_lay_0').removeClass('pink-dark');
            $('.td_team3_lay_0').removeClass('pink-dark');
            $('.td_team1_bm_back_0 #back_1 .cyan-bg').removeClass('blue-dark');
            $('.td_team2_bm_back_0 #back_1 .cyan-bg').removeClass('blue-dark');
            $('.td_team1_bm_lay_0 #lay_1').removeClass('pink-dark');
            $('.td_team2_bm_lay_0 #lay_1').removeClass('pink-dark');
        });
        $('.td_team3_back_1').on('click', function () {
            $('.td_team3_back_0').addClass('blue-dark');
            $('.td_team2_back_0').removeClass('blue-dark');
            $('.td_team1_back_0').removeClass('blue-dark');
            $('.td_team1_lay_0').removeClass('pink-dark');
            $('.td_team2_lay_0').removeClass('pink-dark');
            $('.td_team3_lay_0').removeClass('pink-dark');
            $('.td_team1_bm_back_0 #back_1 .cyan-bg').removeClass('blue-dark');
            $('.td_team2_bm_back_0 #back_1 .cyan-bg').removeClass('blue-dark');
            $('.td_team1_bm_lay_0 #lay_1').removeClass('pink-dark');
            $('.td_team2_bm_lay_0 #lay_1').removeClass('pink-dark');
        });
        $('.td_team3_back_0').on('click', function () {
            $('.td_team3_back_0').addClass('blue-dark');
            $('.td_team2_back_0').removeClass('blue-dark');
            $('.td_team1_back_0').removeClass('blue-dark');
            $('.td_team1_lay_0').removeClass('pink-dark');
            $('.td_team2_lay_0').removeClass('pink-dark');
            $('.td_team3_lay_0').removeClass('pink-dark');
            $('.td_team1_bm_back_0 #back_1 .cyan-bg').removeClass('blue-dark');
            $('.td_team2_bm_back_0 #back_1 .cyan-bg').removeClass('blue-dark');
            $('.td_team1_bm_lay_0 #lay_1').removeClass('pink-dark');
            $('.td_team2_bm_lay_0 #lay_1').removeClass('pink-dark');
        });


        $('.td_team1_lay_2').on('click', function () {
            $('.td_team1_lay_0').addClass('pink-dark');
            $('.td_team3_back_0').removeClass('blue-dark');
            $('.td_team2_back_0').removeClass('blue-dark');
            $('.td_team1_back_0').removeClass('blue-dark');
            $('.td_team2_lay_0').removeClass('pink-dark');
            $('.td_team3_lay_0').removeClass('pink-dark');
            $('.td_team1_bm_back_0 #back_1 .cyan-bg').removeClass('blue-dark');
            $('.td_team2_bm_back_0 #back_1 .cyan-bg').removeClass('blue-dark');
            $('.td_team1_bm_lay_0 #lay_1').removeClass('pink-dark');
            $('.td_team2_bm_lay_0 #lay_1').removeClass('pink-dark');
        });
        $('.td_team1_lay_1').on('click', function () {
            $('.td_team1_lay_0').addClass('pink-dark');
            $('.td_team3_back_0').removeClass('blue-dark');
            $('.td_team2_back_0').removeClass('blue-dark');
            $('.td_team1_back_0').removeClass('blue-dark');
            $('.td_team2_lay_0').removeClass('pink-dark');
            $('.td_team3_lay_0').removeClass('pink-dark');
            $('.td_team1_bm_back_0 #back_1 .cyan-bg').removeClass('blue-dark');
            $('.td_team2_bm_back_0 #back_1 .cyan-bg').removeClass('blue-dark');
            $('.td_team1_bm_lay_0 #lay_1').removeClass('pink-dark');
            $('.td_team2_bm_lay_0 #lay_1').removeClass('pink-dark');
        });
        $('.td_team1_lay_0').on('click', function () {
            $('.td_team1_lay_0').addClass('pink-dark');
            $('.td_team3_back_0').removeClass('blue-dark');
            $('.td_team2_back_0').removeClass('blue-dark');
            $('.td_team1_back_0').removeClass('blue-dark');
            $('.td_team2_lay_0').removeClass('pink-dark');
            $('.td_team3_lay_0').removeClass('pink-dark');
            $('.td_team1_bm_back_0 #back_1 .cyan-bg').removeClass('blue-dark');
            $('.td_team2_bm_back_0 #back_1 .cyan-bg').removeClass('blue-dark');
            $('.td_team1_bm_lay_0 #lay_1').removeClass('pink-dark');
            $('.td_team2_bm_lay_0 #lay_1').removeClass('pink-dark');
        });

        $('.td_team2_lay_2').on('click', function () {
            $('.td_team2_lay_0').addClass('pink-dark');
            $('.td_team1_lay_0').removeClass('pink-dark');
            $('.td_team3_back_0').removeClass('blue-dark');
            $('.td_team2_back_0').removeClass('blue-dark');
            $('.td_team1_back_0').removeClass('blue-dark');
            $('.td_team3_lay_0').removeClass('pink-dark');
            $('.td_team1_bm_back_0 #back_1 .cyan-bg').removeClass('blue-dark');
            $('.td_team2_bm_back_0 #back_1 .cyan-bg').removeClass('blue-dark');
            $('.td_team1_bm_lay_0 #lay_1').removeClass('pink-dark');
            $('.td_team2_bm_lay_0 #lay_1').removeClass('pink-dark');
        });
        $('.td_team2_lay_1').on('click', function () {
            $('.td_team2_lay_0').addClass('pink-dark');
            $('.td_team1_lay_0').removeClass('pink-dark');
            $('.td_team3_back_0').removeClass('blue-dark');
            $('.td_team2_back_0').removeClass('blue-dark');
            $('.td_team1_back_0').removeClass('blue-dark');
            $('.td_team3_lay_0').removeClass('pink-dark');
            $('.td_team1_bm_back_0 #back_1 .cyan-bg').removeClass('blue-dark');
            $('.td_team2_bm_back_0 #back_1 .cyan-bg').removeClass('blue-dark');
            $('.td_team1_bm_lay_0 #lay_1').removeClass('pink-dark');
            $('.td_team2_bm_lay_0 #lay_1').removeClass('pink-dark');
        });
        $('.td_team2_lay_0').on('click', function () {
            $('.td_team2_lay_0').addClass('pink-dark');
            $('.td_team1_lay_0').removeClass('pink-dark');
            $('.td_team3_back_0').removeClass('blue-dark');
            $('.td_team2_back_0').removeClass('blue-dark');
            $('.td_team1_back_0').removeClass('blue-dark');
            $('.td_team3_lay_0').removeClass('pink-dark');
            $('.td_team1_bm_back_0 #back_1 .cyan-bg').removeClass('blue-dark');
            $('.td_team2_bm_back_0 #back_1 .cyan-bg').removeClass('blue-dark');
            $('.td_team1_bm_lay_0 #lay_1').removeClass('pink-dark');
            $('.td_team2_bm_lay_0 #lay_1').removeClass('pink-dark');
        });

        $('.td_team3_lay_2').on('click', function () {
            $('.td_team3_lay_0').addClass('pink-dark');
            $('.td_team2_lay_0').removeClass('pink-dark');
            $('.td_team1_lay_0').removeClass('pink-dark');
            $('.td_team3_back_0').removeClass('blue-dark');
            $('.td_team2_back_0').removeClass('blue-dark');
            $('.td_team1_back_0').removeClass('blue-dark');
            $('.td_team1_bm_back_0 #back_1 .cyan-bg').removeClass('blue-dark');
            $('.td_team2_bm_back_0 #back_1 .cyan-bg').removeClass('blue-dark');
            $('.td_team1_bm_lay_0 #lay_1').removeClass('pink-dark');
            $('.td_team2_bm_lay_0 #lay_1').removeClass('pink-dark');
        });
        $('.td_team3_lay_1').on('click', function () {
            $('.td_team3_lay_0').addClass('pink-dark');
            $('.td_team2_lay_0').removeClass('pink-dark');
            $('.td_team1_lay_0').removeClass('pink-dark');
            $('.td_team3_back_0').removeClass('blue-dark');
            $('.td_team2_back_0').removeClass('blue-dark');
            $('.td_team1_back_0').removeClass('blue-dark');
            $('.td_team1_bm_back_0 #back_1 .cyan-bg').removeClass('blue-dark');
            $('.td_team2_bm_back_0 #back_1 .cyan-bg').removeClass('blue-dark');
            $('.td_team1_bm_lay_0 #lay_1').removeClass('pink-dark');
            $('.td_team2_bm_lay_0 #lay_1').removeClass('pink-dark');
        });
        $('.td_team3_lay_0').on('click', function () {
            $('.td_team3_lay_0').addClass('pink-dark');
            $('.td_team2_lay_0').removeClass('pink-dark');
            $('.td_team1_lay_0').removeClass('pink-dark');
            $('.td_team3_back_0').removeClass('blue-dark');
            $('.td_team2_back_0').removeClass('blue-dark');
            $('.td_team1_back_0').removeClass('blue-dark');
            $('.td_team1_bm_back_0 #back_1 .cyan-bg').removeClass('blue-dark');
            $('.td_team2_bm_back_0 #back_1 .cyan-bg').removeClass('blue-dark');
            $('.td_team1_bm_lay_0 #lay_1').removeClass('pink-dark');
            $('.td_team2_bm_lay_0 #lay_1').removeClass('pink-dark');
        });

        // bookmaker
        $('.td_team1_bm_back_2').on('click', function () {
            $('.td_team1_bm_back_0 #back_1 .cyan-bg').addClass('blue-dark');
            $('.td_team2_bm_back_0 #back_1 .cyan-bg').removeClass('blue-dark');
            $('.td_team1_bm_lay_0 #lay_1').removeClass('pink-dark');
            $('.td_team2_bm_lay_0 #lay_1').removeClass('pink-dark');
            $('.td_team1_back_0').removeClass('blue-dark');
            $('.td_team2_back_0').removeClass('blue-dark');
            $('.td_team3_back_0').removeClass('blue-dark');
            $('.td_team1_lay_0').removeClass('pink-dark');
            $('.td_team2_lay_0').removeClass('pink-dark');
            $('.td_team3_lay_0').removeClass('pink-dark');
        });
        $('.td_team1_bm_back_1').on('click', function () {
            $('.td_team1_bm_back_0 #back_1 .cyan-bg').addClass('blue-dark');
            $('.td_team2_bm_back_0 #back_1 .cyan-bg').removeClass('blue-dark');
            $('.td_team1_bm_lay_0 #lay_1').removeClass('pink-dark');
            $('.td_team2_bm_lay_0 #lay_1').removeClass('pink-dark');
            $('.td_team1_back_0').removeClass('blue-dark');
            $('.td_team2_back_0').removeClass('blue-dark');
            $('.td_team3_back_0').removeClass('blue-dark');
            $('.td_team1_lay_0').removeClass('pink-dark');
            $('.td_team2_lay_0').removeClass('pink-dark');
            $('.td_team3_lay_0').removeClass('pink-dark');
        });
        $('.td_team1_bm_back_0').on('click', function () {
            $('.td_team1_bm_back_0 #back_1 .cyan-bg').addClass('blue-dark');
            $('.td_team2_bm_back_0 #back_1 .cyan-bg').removeClass('blue-dark');
            $('.td_team1_bm_lay_0 #lay_1').removeClass('pink-dark');
            $('.td_team2_bm_lay_0 #lay_1').removeClass('pink-dark');
            $('.td_team1_back_0').removeClass('blue-dark');
            $('.td_team2_back_0').removeClass('blue-dark');
            $('.td_team3_back_0').removeClass('blue-dark');
            $('.td_team1_lay_0').removeClass('pink-dark');
            $('.td_team2_lay_0').removeClass('pink-dark');
            $('.td_team3_lay_0').removeClass('pink-dark');
        });

        $('.td_team2_bm_back_2').on('click', function () {
            $('.td_team2_bm_back_0 #back_1 .cyan-bg').addClass('blue-dark');
            $('.td_team1_bm_back_0 #back_1 .cyan-bg').removeClass('blue-dark');
            $('.td_team1_bm_lay_0 #lay_1').removeClass('pink-dark');
            $('.td_team2_bm_lay_0 #lay_1').removeClass('pink-dark');
            $('.td_team1_back_0').removeClass('blue-dark');
            $('.td_team2_back_0').removeClass('blue-dark');
            $('.td_team3_back_0').removeClass('blue-dark');
            $('.td_team1_lay_0').removeClass('pink-dark');
            $('.td_team2_lay_0').removeClass('pink-dark');
            $('.td_team3_lay_0').removeClass('pink-dark');
        });
        $('.td_team2_bm_back_1').on('click', function () {
            $('.td_team2_bm_back_0 #back_1 .cyan-bg').addClass('blue-dark');
            $('.td_team1_bm_back_0 #back_1 .cyan-bg').removeClass('blue-dark');
            $('.td_team1_bm_lay_0 #lay_1').removeClass('pink-dark');
            $('.td_team2_bm_lay_0 #lay_1').removeClass('pink-dark');
            $('.td_team1_back_0').removeClass('blue-dark');
            $('.td_team2_back_0').removeClass('blue-dark');
            $('.td_team3_back_0').removeClass('blue-dark');
            $('.td_team1_lay_0').removeClass('pink-dark');
            $('.td_team2_lay_0').removeClass('pink-dark');
            $('.td_team3_lay_0').removeClass('pink-dark');
        });
        $('.td_team2_bm_back_0').on('click', function () {
            $('.td_team2_bm_back_0 #back_1 .cyan-bg').addClass('blue-dark');
            $('.td_team1_bm_back_0 #back_1 .cyan-bg').removeClass('blue-dark');
            $('.td_team1_bm_lay_0 #lay_1').removeClass('pink-dark');
            $('.td_team2_bm_lay_0 #lay_1').removeClass('pink-dark');
            $('.td_team1_back_0').removeClass('blue-dark');
            $('.td_team2_back_0').removeClass('blue-dark');
            $('.td_team3_back_0').removeClass('blue-dark');
            $('.td_team1_lay_0').removeClass('pink-dark');
            $('.td_team2_lay_0').removeClass('pink-dark');
            $('.td_team3_lay_0').removeClass('pink-dark');
        });

        $('.td_team1_bm_lay_2').on('click', function () {
            $('.td_team1_bm_lay_0 #lay_1').addClass('pink-dark');
            $('.td_team2_bm_back_0 #back_1 .cyan-bg').removeClass('blue-dark');
            $('.td_team1_bm_back_0 #back_1 .cyan-bg').removeClass('blue-dark');
            $('.td_team2_bm_lay_0 #lay_1').removeClass('pink-dark');
            $('.td_team1_back_0').removeClass('blue-dark');
            $('.td_team2_back_0').removeClass('blue-dark');
            $('.td_team3_back_0').removeClass('blue-dark');
            $('.td_team1_lay_0').removeClass('pink-dark');
            $('.td_team2_lay_0').removeClass('pink-dark');
            $('.td_team3_lay_0').removeClass('pink-dark');
        });
        $('.td_team1_bm_lay_1').on('click', function () {
            $('.td_team1_bm_lay_0 #lay_1').addClass('pink-dark');
            $('.td_team2_bm_back_0 #back_1 .cyan-bg').removeClass('blue-dark');
            $('.td_team1_bm_back_0 #back_1 .cyan-bg').removeClass('blue-dark');
            $('.td_team2_bm_lay_0 #lay_1').removeClass('pink-dark');
            $('.td_team1_back_0').removeClass('blue-dark');
            $('.td_team2_back_0').removeClass('blue-dark');
            $('.td_team3_back_0').removeClass('blue-dark');
            $('.td_team1_lay_0').removeClass('pink-dark');
            $('.td_team2_lay_0').removeClass('pink-dark');
            $('.td_team3_lay_0').removeClass('pink-dark');
        });
        $('.td_team1_bm_lay_0').on('click', function () {
            $('.td_team1_bm_lay_0 #lay_1').addClass('pink-dark');
            $('.td_team2_bm_back_0 #back_1 .cyan-bg').removeClass('blue-dark');
            $('.td_team1_bm_back_0 #back_1 .cyan-bg').removeClass('blue-dark');
            $('.td_team2_bm_lay_0 #lay_1').removeClass('pink-dark');
            $('.td_team1_back_0').removeClass('blue-dark');
            $('.td_team2_back_0').removeClass('blue-dark');
            $('.td_team3_back_0').removeClass('blue-dark');
            $('.td_team1_lay_0').removeClass('pink-dark');
            $('.td_team2_lay_0').removeClass('pink-dark');
            $('.td_team3_lay_0').removeClass('pink-dark');
        });

        $('.td_team2_bm_lay_2').on('click', function () {
            $('.td_team2_bm_lay_0 #lay_1').addClass('pink-dark');
            $('.td_team1_bm_lay_0 #lay_1').removeClass('pink-dark');
            $('.td_team2_bm_back_0 #back_1 .cyan-bg').removeClass('blue-dark');
            $('.td_team1_bm_back_0 #back_1 .cyan-bg').removeClass('blue-dark');
            $('.td_team1_back_0').removeClass('blue-dark');
            $('.td_team2_back_0').removeClass('blue-dark');
            $('.td_team3_back_0').removeClass('blue-dark');
            $('.td_team1_lay_0').removeClass('pink-dark');
            $('.td_team2_lay_0').removeClass('pink-dark');
            $('.td_team3_lay_0').removeClass('pink-dark');
        });
        $('.td_team2_bm_lay_1').on('click', function () {
            $('.td_team2_bm_lay_0 #lay_1').addClass('pink-dark');
            $('.td_team1_bm_lay_0 #lay_1').removeClass('pink-dark');
            $('.td_team2_bm_back_0 #back_1 .cyan-bg').removeClass('blue-dark');
            $('.td_team1_bm_back_0 #back_1 .cyan-bg').removeClass('blue-dark');
            $('.td_team1_back_0').removeClass('blue-dark');
            $('.td_team2_back_0').removeClass('blue-dark');
            $('.td_team3_back_0').removeClass('blue-dark');
            $('.td_team1_lay_0').removeClass('pink-dark');
            $('.td_team2_lay_0').removeClass('pink-dark');
            $('.td_team3_lay_0').removeClass('pink-dark');
        });
        $('.td_team2_bm_lay_0').on('click', function () {
            $('.td_team2_bm_lay_0 #lay_1').addClass('pink-dark');
            $('.td_team1_bm_lay_0 #lay_1').removeClass('pink-dark');
            $('.td_team2_bm_back_0 #back_1 .cyan-bg').removeClass('blue-dark');
            $('.td_team1_bm_back_0 #back_1 .cyan-bg').removeClass('blue-dark');
            $('.td_team1_back_0').removeClass('blue-dark');
            $('.td_team2_back_0').removeClass('blue-dark');
            $('.td_team3_back_0').removeClass('blue-dark');
            $('.td_team1_lay_0').removeClass('pink-dark');
            $('.td_team2_lay_0').removeClass('pink-dark');
            $('.td_team3_lay_0').removeClass('pink-dark');
        });

        function colorclick(val) {
            $('.pink-bg').removeClass('pink-dark');
            $('.cyan-bg').removeClass('blue-dark');
            $('.' + val).addClass('pink-dark');
        }

        function colorclickback(val) {
            $('.pink-bg').removeClass('pink-dark');
            $('.cyan-bg').removeClass('blue-dark');
            $('.' + val).addClass('blue-dark');
        }

    </script>
@endpush
