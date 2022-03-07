@extends('layouts.app')
@section('content')

<style>
    body {
        overflow: hidden;
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
                    <div class="casinotrap-table baccarat_table blue-dark-bg">
                        <div class="casino-video">
                            <div class="video-block">
                                <iframe src="{{$casino->casino_link}}" title="YouTube video player" frameborder="0" allowtransparency="yes" scrolling="no" marginwidth="0" marginheight="0" allowfullscreen></iframe>
                            </div>
                            <div class="videotitle black-bg-rgb">
                                <span class="casino_name text-color-yellow1">Baccarat</span>
                                <div class="casino_video_rid text-color-grey-2 roundId"> </div>
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
                        <div class="casino-videodetails blue-gradient-bg2 text-color-white" id="appendData">                            
                            <div class="statistics_block">
                                <div class="text-color-white" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="true" aria-controls="collapseExample">
                                    Statistics <i class="fas fa-angle-up"></i>
                                </div>
                                <div class="collapse show" id="collapseExample">
                                    <div id="chart_12" class="d-flex justify-content-center"></div>
                                </div>
                            </div>
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
                                <div class="bet_player"><span>Either Pair</span></div>
                                <div class="odds_box">
                                    <input type="text" value="5" disabled="disabled" class="form-control">
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
                    <img src="{{ URL::to('asset/front/img/baccarat.jpg')}}" class="">
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
            <div class="modal-body" id="appnedLastResult">
                <div class="casino_result_round">
                    <div>Round-Id: 6378481664424</div>
                    <div>Match Time: 24/06/2021 18:47:41</div>
                </div>
                <div class="row row1">
                    <div class="col-12 col-lg-8">
                        <div class="casino-result-content">
                            <div class="casino-result-content-item text-center">
                                <div class="casino-result-cards">
                                    <div class="d-inline-block">
                                        <h4>Player A</h4>
                                        <div class="casino-result-cards-item"><img src="{{ URL::to('asset/front/img/6CC.png') }}"></div>
                                        <div class="casino-result-cards-item"><img src="{{ URL::to('asset/front/img/JHH.png') }}"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="casino-result-content-diveder darkblue-bg"></div>
                            <div class="casino-result-content-item text-center">
                                <div class="casino-result-cards">
                                    <div class="casino-result-cards-item"><img src="{{ URL::to('asset/front/img/winner.png') }}" class="winner_icon"></div>
                                    <div class="d-inline-block">
                                        <h4>Player B</h4>
                                        <div class="casino-result-cards-item"><img src="{{ URL::to('asset/front/img/4HH.png') }}"></div>
                                        <div class="casino-result-cards-item"><img src="{{ URL::to('asset/front/img/QCC.png') }}"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4">
                        <div class="casino-result-desc blue-dark-bg-1">
                            <div class="casino-result-desc-item">
                                <div>Winner</div>
                                <div>Player B</div>
                            </div>
                            <div class="casino-result-desc-item">
                                <div>Mini Baccarat</div>
                                <div>Player A (A : 6 | B : 5)</div>
                            </div>
                            <div class="casino-result-desc-item">
                                <div>Total</div>
                                <div>A : 17 | B : 17</div>
                            </div>
                            <div class="casino-result-desc-item">
                                <div>Color Plus</div>
                                <div>No</div>
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
$(document).ready(function(){
    setInterval(function(){
getResultCall();
},1000);
   // Default call
getResultCall();
function getResultCall(){
    $.ajax({
    type: "POST",
    url: '{{route("getbaccarat")}}',
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
        $('.roundId').html("ROUND ID: "+spl[2]);                            
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
    // last result api
    $.ajax({
    type: "POST",
    url: '{{route("getbaccaratLastResult")}}',
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
         
});

function openLastPopup(round){
var _token = $("input[name='_token']").val();
    $.ajax({
        type: "POST",
        url: '{{route("getbaccaratLastResultpopup")}}',
        data: {_token:_token,round:round},
        beforeSend:function(){
            $('#site_bet_loading1').show();
        },
        complete: function(){
            $('#site_bet_loading1').hide();
        },
        success: function(data){
            $("#appnedLastResult").html(data);
            $('#exampleModal3').modal('show');
        }
    });
}
</script>
@endsection