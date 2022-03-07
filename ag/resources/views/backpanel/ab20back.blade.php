@extends('layouts.app')
@section('content')
<style>
    body {
        overflow: hidden;
        padding: 0;
    margin: 0;
    }
.betloaderimage1{
    top: 50%;
    height: 135px;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 5px 10px rgb(0 0 0 / 50%);
    padding-top: 30px;
    z-index: 50;
    position: absolute;
    left: 50%;
    width: 190px;
    margin-left: -95px;
}

.loading1 img {
    background-position: -42px -365px;
    height: 51px;
    width: 51px;
}

.loading1 li{
    list-style: none;
    text-align: center;
    font-size: 11px;
}
</style>
<div id="site_bet_loading1" class="betloaderimage1 loader-style1" style="display: none" >
    <ul class="loading1">
        <li>
            <img src="/asset/front/img/loaderajaxbet.gif">
        </li>
        <li>Loading...</li>
    </ul>
</div>

<section>
    <div class="container-fluid">
        <div class="main-wrapper">
            @include('backpanel/leftpanelcasino')
            <div class="middle-section">
                <div class="middle-wraper">
                    <div class="casinotrap-table andarbahar_table blue-dark-bg">
                        <div class="casino-video">
                            <div class="video-block">
                                <iframe src="{{$casino->casino_link}}" title="YouTube video player" frameborder="0" allowtransparency="yes" scrolling="no" marginwidth="0" marginheight="0" allowfullscreen></iframe>
                            </div>
                            <div class="videotitle black-bg-rgb">
                                <span class="casino_name text-color-yellow1">Andar Bahar</span>
                                <div class="casino_video_rid text-color-grey-2 roundId"></div>
                            </div>
                            <div class="casinocards">
                                <div class="casinocards_shuffle"><i class="fas text-color-grey-3 fa-grip-lines-vertical"></i></div>
                                <div class="casinocards-container text-color-white">
                                    <div class="andar-carousel ab_slider_main owl-carousel owl-theme">
                                        <span><img src="{{ URL::to('asset/front/img/2CC.png') }} " alt=""></span>
                                    </div>
                                    <div class="bahar-carousel ab_slider_main owl-carousel owl-theme">
                                        <span><img src="{{ URL::to('asset/front/img/2CC.png') }} " alt=""></span>
                                    </div>
                                </div>
                            </div>
                            <div class="casino_time">
                                <div id="app"></div>
                            </div>
                            <div class="casino-icons">
                                <ul>
                                    <li class="black-rgb-1"> <a href="{{route('casino')}}" class=" text-color-white"><i class="fas fa-home"></i></a> </li>
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
                        <br>
                        <br>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal golden_modal1 current_modal fade" id="exampleModal3" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header darkblue-bg">
                <h5 class="modal-title text-color-yellow1" id="exampleModalLabel">Details</h5>
                <button type="button" class="close text-color-grey-2" data-dismiss="modal" aria-label="Close">×</button>
            </div>
            <div class="modal-body" id="appnedLastResultab">
                <div class="casino_result_round">
                    <div>Round-Id: 6378481664424</div>
                    <div>Match Time: 24/06/2021 18:47:41</div>
                </div>
                <div class="row row1">
                    <div class="col-12 modalslider_bahar">
                        <h5 class="text-center">Andarss</h5>
                        <div class="ab_slider_main text-center">
                            <span style="width:35px;"><img src="{{ URL::to('asset/front/img/2CC.png') }} " alt="img"></span>
                        </div>
                    </div>
                    <div class="col-12 modalslider_bahar">
                        <h5 class="text-center">Bahar</h5>
                        <div class="ab_slider_main text-center">
                            <span style="width:35px;"><img src="{{ URL::to('asset/front/img/2CC.png') }} " alt="img"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</section>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script>
var _token = $("input[name='_token']").val();
$(document).ready(function(){
   setInterval(function(){
      getResultCall();
    },10000);
    getResultCall();
    function getResultCall(){
        $.ajax({
            type: "POST",
            url: '{{route("getCasinoab20")}}',
            data: {_token:_token},
            beforeSend:function(){
                $('#site_bet_loading1').show();
            },
            complete: function(){
                $('#site_bet_loading1').hide();
            },
            success: function(data){
                var myarr=[];
                var spl=data.split('~~');
                $('.roundId').html("Round ID: "+spl[2]);
                $('#appendData').html(spl[0]);

                // Timer
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

                const TIME_LIMIT = spl[1];
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
        });
    }
});

   // last result api

    $.ajax({
    type: "POST",
    url: '{{route("getab20LastResult")}}',
    data: {_token:_token},
    beforeSend:function(){
        $('#site_bet_loading1').show();
    },
    complete: function(){
        $('#site_bet_loading1').hide();
    },
    success: function(data){
        $("#last_result").html(data);
    }
});

function openLastPopup(cou){
    $.ajax({
        type: "POST",
        url: '{{route("getab20LastResultpopup")}}',
        data: {_token:_token,cou:cou},
        beforeSend:function(){
            $('#site_bet_loading1').show();
        },
        complete: function(){
            $('#site_bet_loading1').hide();
        },
        success: function(data){
            var spl=data.split('~~');
            $("#appnedLastResultab").html(data);
            $('#exampleModal3').modal('show');
        }
    });
}
</script>
@endsection