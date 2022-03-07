@extends('layouts.front_layout')
@section('content')
<style>
    body {
        overflow: hidden;
    }
</style>
<section>
    <div class="container-fluid">
        <div class="main-wrapper">
            @include('front/leftpanelcasino')
            <div class="middle-section">
                <div class="middle-wraper">
                    <div class="casinotrap-table card32b_table blue-dark-bg">
                        <div class="casino-video">
                            <div class="video-block">
                                <iframe src="{{$casino->casino_link}}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                            </div>
                            <div class="videotitle black-bg-rgb">
                                <span class="casino_name text-color-yellow1">32 Cards B</span>
                                <div class="casino_video_rid text-color-grey-2 roundId"></div>
                            </div>
                            <div class="casinocards" id="cardVideo">
                                <div class="casinocards_shuffle"><i class="fas text-color-grey-3 fa-grip-lines-vertical"></i></div>
                                <div class="casinocards-container text-color-white" id="casinoCard">
                                    <div>
                                        <div class="dealer_name w-100 mb-1">
                                            <span>Player 8: </span> <span>21</span>
                                        </div>
                                        <div><span><img src="{{ URL::to('asset/front/img/KHH.png')}} "></span></div>
                                    </div>
                                    <div>
                                        <div class="dealer_name w-100 mb-1">
                                            <span>Player 9: </span> <span>16</span>
                                        </div>
                                        <div><span><img src="{{ URL::to('asset/front/img/7SS.png')}} " alt="img"></span></div>
                                    </div>
                                    <div>
                                        <div class="dealer_name w-100 mb-1">
                                            <span>Player 10: </span> <span>32</span>
                                        </div>
                                        <div><span><img src="{{ URL::to('asset/front/img/QCC.png')}} "></span></div>
                                    </div>
                                    <div>
                                        <div class="dealer_name w-100 mb-1">
                                            <span class="text-color-yellow1">Player 11: </span> <span>24</span>
                                        </div>
                                        <div><span><img src="{{ URL::to('asset/front/img/KSS.png')}} "></span></div>
                                    </div>
                                </div>
                            </div>
                            <div class="casino_time">
                                <div id="app"></div>
                            </div>
                            <div class="casino-icons">
                                <ul>
                                    <li class="black-rgb-1"> <a href="{{route('casino')}}" class=" text-color-white"><i class="fas fa-home"></i></a> </li>
                                    <li class="black-rgb-1 text-color-white" data-toggle="modal" data-target="#exampleModal2"> <i class="fas fa-info-circle"></i> </li>
                                    <li class="black-rgb-1 text-color-white resicon"> <i class="fas fa-chevron-circle-up"></i> </li>
                                </ul>
                            </div>

                            <div class="casinolast_results black-bg-rgb d-none d-md-flex" id="last_result">
                                <a href="our-casino-results.php" class="resultmore text-color-white">...</a>
                            </div>
                        </div>
                        <div class="casino-videodetails dark-blue-bg-2 text-color-grey-2" id="appendData">
                        </div>
                        <div class="mobile_res_data">
                            <div class="casinolast_results d-md-none" id="last_result">
                                <a href="our-casino-results.php" class="resultmore text-color-white">...</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="casino_right_side">
                    <form class="casinoplay-bet">
                        <div class="casinolay_bettitle black-bg-2 text-color-white">
                            <span>Place Bet</span>
                            <span class="float-right casinomin_max">Range:<span>100</span>-<span>100K</span></span>
                        </div>
                        <div class="casinoplay-betheader light-grey-bg-4">
                            <div>(Bet for)</div>
                            <div>Odds</div>
                            <div>Stake</div>
                            <div>Profit</div>
                        </div>
                        <div class="casinoplay-box blue-dark-bg">
                            <div class="casinoplay_betinfo">
                                <div class="bet_player"><span>Player 8 Odd</span></div>
                                <div class="odds_box">
                                    <input type="text" value="1.97" disabled="disabled" class="form-control">
                                    <img src="https://sitethemedata.com/v3/static/front/img/arrow-down.svg" class="arrow-up">
                                    <img src="https://sitethemedata.com/v3/static/front/img/arrow-down.svg" class="arrow-down">
                                </div>
                                <div class="bet_input back_border">
                                    <input type="text" class="form-control input-stake">
                                </div>
                                <div>0</div>
                            </div>
                            <div class="casinoplay_button">
                                <button class="btn btn-bet green-bg-1 text-color-white"><span>100</span></button>
                                <button class="btn btn-bet green-bg-1 text-color-white"><span>200</span></button>
                                <button class="btn btn-bet green-bg-1 text-color-white"><span>300</span></button>
                                <button class="btn btn-bet green-bg-1 text-color-white"><span>400</span></button>
                                <button class="btn btn-bet green-bg-1 text-color-white"><span>500</span></button>
                                <button class="btn btn-bet green-bg-1 text-color-white"><span>600</span></button>
                                <button class="btn btn-bet green-bg-1 text-color-white"><span>700</span></button>
                                <button class="btn btn-bet green-bg-1 text-color-white"><span>800</span></button>
                            </div>
                            <div class="casinoplay_action_buttons">
                                <button class="btn btn-reset red-bg text-color-white" type="reset" value="Reset">Reset</button>
                                <button class="btn btn-ok green-bg text-color-white" type="submit" value="Submit">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal golden_modal1 fade" id="exampleModal2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content light-grey-bg-2">
            <div class="modal-header blue-dark-bg-3">
                <h5 class="modal-title text-color-yellow-1" id="exampleModalLabel">Rules</h5>
                <button type="button" class="close text-color-grey-1" data-dismiss="modal" aria-label="Close">×</button>
            </div>
            <div class="modal-body">
                <div class="p-5 modal-plus-block text-center">
                    <img src="{{ URL::to('asset/front/img/card32eu.jpg')}}" class="img-fluid trapmodal_img">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal golden_modal1 current_modal fade" id="exampleModal3" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content blue-dark-bg">
            <div class="modal-header darkblue-bg">
                <h5 class="modal-title text-color-yellow1" id="exampleModalLabel">Details</h5>
                <button type="button" class="close text-color-grey-1" data-dismiss="modal" aria-label="Close">×</button>
            </div>
            <div class="modal-body card32_results" id="appnedLastResult">
                <div class="casino_result_round">
                    <div>Round-Id: 6378481664424</div>
                    <div>Match Time: 24/06/2021 18:47:41</div>
                </div>
                <div class="row row1">
                    <div class="col-12 col-lg-9">
                        <div class="row row1">
                            <div class="col-12 col-lg-3">
                                <div class="casino-result-cards justify-content-start">
                                    <div class="d-inline-block">
                                        <h6>Player 8 - <span class="text-color-yellow1">17</span></h6>
                                        <div class="casino-result-cards-item"><img src="{{ URL::to('img/cards/6CC.png')}}"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-3">
                                <div class="casino-result-cards justify-content-start">
                                    <div class="d-inline-block">
                                        <h6>Player 9 - <span class="text-color-yellow1">15</span></h6>
                                        <div class="casino-result-cards-item"><img src="{{ URL::to('img/cards/4HH.png')}}"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-3">
                                <div class="casino-result-cards justify-content-start">
                                    <div class="d-inline-block">
                                        <h6>Player 10 - <span class="text-color-yellow1">23</span></h6>
                                        <div class="casino-result-cards-item"><img src="{{ URL::to('img/cards/JHH.png')}}"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-3">
                                <div class="casino-result-cards justify-content-start">
                                    <div class="casino-result-cards-item"><img src="{{ URL::to('img/winner.png')}}" class="winner_icon"></div>
                                    <div class="d-inline-block">
                                        <h6>Player 11 - <span class="text-color-yellow1">24</span></h6>
                                        <div class="casino-result-cards-item"><img src="{{ URL::to('img/cards/QCC.png')}}"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-3">
                        <div class="casino-result-desc">
                            <div class="casino-result-desc-item">
                                <div>Winner</div>
                                <div>Player 11</div>
                            </div>
                            <div class="casino-result-desc-item">
                                <div>Odd/Even</div>
                                <div>8 : Odd | 9 : Even</div>
                            </div>
                            <div class="casino-result-desc-item">
                                <div></div>
                                <div>10 : Odd | 11 : Odd</div>
                            </div>
                            <div class="casino-result-desc-item">
                                <div>Black/Red</div>
                                <div>2-2</div>
                            </div>
                            <div class="casino-result-desc-item">
                                <div>Total</div>
                                <div>10-11</div>
                            </div>
                            <div class="casino-result-desc-item">
                                <div>Single</div>
                                <div>1</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script>
var _token = $("input[name='_token']").val();
$(document).ready(function() {
    getResultCall();
    videopage32();
    setInterval(function() {
        getResultCall();
        videopage32();
    }, 1000);
        // Default call
    function getResultCall() {
        $.ajax({
            type: "POST",
            url: '{{route("getCasino32cardb")}}',
            data: {
                _token: _token
            },
            beforeSend: function() {
                $('#site_statistics_loading').show();
            },
            complete: function() {
                $('#site_statistics_loading').hide();
            },
            success: function(data) {
                var myarr = [];
                var spl = data.split('~~');
                $('#appendData').html(spl[0]);
                
            }
        });
    }

    // timer
    function cardb32(val){
                const FULL_DASH_ARRAY = 283;
                const WARNING_THRESHOLD = 10;
                const ALERT_THRESHOLD = 5;

                const COLOR_CODES = {
                    info: {
                        color: "green"
                    },
                    warning: {
                        color: "orange",
                        threshold: WARNING_THRESHOLD
                    },
                    alert: {
                        color: "red",
                        threshold: ALERT_THRESHOLD
                    }
                };

                const TIME_LIMIT = val;
                let timePassed = 0;
                let timeLeft = TIME_LIMIT;
                let timerInterval = null;
                let remainingPathColor = COLOR_CODES.info.color;

                document.getElementById("app").innerHTML = `
                    <div class="base-timer">
                      <svg class="base-timer__svg" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                        <g class="base-timer__circle">
                          <circle class="base-timer__path-elapsed" cx="50" cy="50" r="45"></circle>
                          <path
                            id="base-timer-path-remaining"
                            stroke-dasharray="283"
                            class="base-timer__path-remaining text-color-green-1 ${remainingPathColor}"
                            d="
                              M 50, 50
                              m -45, 0
                              a 45,45 0 1,0 90,0
                              a 45,45 0 1,0 -90,0
                            "
                          ></path>
                        </g>
                      </svg>
                      <span id="base-timer-label" class="base-timer__label text-color-green-1">${formatTime(
                        timeLeft
                      )}</span>
                    </div>
                    `;

                startTimer();

                function onTimesUp() {
                    clearInterval(timerInterval);
                }

                function startTimer() {
                    timerInterval = setInterval(() => {
                        timePassed = timePassed += 1;
                        timeLeft = TIME_LIMIT - timePassed;
                        document.getElementById("base-timer-label").innerHTML = formatTime(
                            timeLeft
                        );
                        setCircleDasharray();
                        setRemainingPathColor(timeLeft);

                        if (timeLeft === 0) {
                            onTimesUp();
                        }
                    }, 1000);
                }

                function formatTime(time) {
                    const minutes = Math.floor(time / 60);
                    let seconds = time % 60;
                    if (seconds < 0) {
                        seconds = `0`;
                    }
                    return `${seconds}`;
                }

                function setRemainingPathColor(timeLeft) {
                    const {
                        alert,
                        warning,
                        info
                    } = COLOR_CODES;
                    if (timeLeft <= alert.threshold) {
                        document
                            .getElementById("base-timer-path-remaining")
                            .classList.remove(warning.color);
                        document
                            .getElementById("base-timer-path-remaining")
                            .classList.add(alert.color);
                    } else if (timeLeft <= warning.threshold) {
                        document
                            .getElementById("base-timer-path-remaining")
                            .classList.remove(info.color);
                        document
                            .getElementById("base-timer-path-remaining")
                            .classList.add(warning.color);
                    }
                }

                function calculateTimeFraction() {
                    const rawTimeFraction = timeLeft / TIME_LIMIT;
                    return rawTimeFraction - (1 / TIME_LIMIT) * (1 - rawTimeFraction);
                }

                function setCircleDasharray() {
                    const circleDasharray = `${(
                        calculateTimeFraction() * FULL_DASH_ARRAY
                      ).toFixed(0)} 283`;
                    document
                        .getElementById("base-timer-path-remaining")
                        .setAttribute("stroke-dasharray", circleDasharray);
                }
    }

    // last result api    
    $.ajax({
        type: "POST",
        url: '{{route("get32cardLastResult")}}',
        data: {
            _token: _token
        },
        beforeSend: function() {
            $('#site_statistics_loading').show();
        },
        complete: function() {
            $('#site_statistics_loading').hide();
        },
        success: function(data) {
            $("#last_result").html(data);
        }
    });

        // video page api
    function videopage32(){
        $.ajax({
            type: "POST",
            url: '{{route("get32cardvideo")}}',
            data: {
                _token: _token
            },
            beforeSend: function() {
                $('#site_statistics_loading').show();
            },
            complete: function() {
                $('#site_statistics_loading').hide();
            },
            success: function(data) {
                var spl=data.split('~~');
                $('#cardVideo').html(spl[0]);
                $('.roundId').html("Round ID: "+spl[2]);
                if(spl[1] > 0){
                    cardb32(spl[1]);
                }
            }
        });
    }

});

function openLastPopup(round) {
    $.ajax({
        type: "POST",
        url: '{{route("get32cardbLastResultpopup")}}',
        data: {
            _token: _token,
            round: round
        },
        beforeSend: function() {
            $('#site_statistics_loading').show();
        },
        complete: function() {
            $('#site_statistics_loading').hide();
        },
        success: function(data) {
            var spl = data.split('~~');
            $("#appnedLastResult").html(data);
            $('#exampleModal3').modal('show');
        }
    });
}
</script>
@endsection