@extends('layouts.front_layout')
@section('content')
    <style>
        body {
            overflow: hidden;
        }

        .betloaderimage1 {
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

        .loading1 li {
            list-style: none;
            text-align: center;
            font-size: 11px;
        }
    </style>

    <div id="site_bet_loading1" class="betloaderimage1 loader-style1" style="display: none">
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
                @include('layouts.leftpanel')
                <div class="middle-section">
                    <div class="middle-wraper">
                        <div class="casinotrap-table blue-dark-bg">
                            <div id="gameHead" class="game-head">
                                <div class="game-team">
                                    <div class="game-name">TP2020 <span class="rules_underline" data-toggle="modal"
                                                                        data-target="#exampleModal2">Rules</span>
                                        <span class="float-right round"> Round ID: <span class="roundId">0</span> | Min: <span>1</span> | Max: <span>500000</span></span>
                                        <!----></div>
                                </div>
                            </div>

                            <div class="casino-video">
                                <div class="video-block">
                                    <iframe src="{{$casino->casino_link}}" title="YouTube video player" frameborder="0"
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                            allowfullscreen frameborder="0" allowtransparency="yes" scrolling="no"
                                            marginwidth="0" marginheight="0"></iframe>
                                </div>
                                <div class="casinocards">
                                    {{--                                    <div class="casinocards_shuffle"><i class="fas text-color-grey-3 fa-grip-lines-vertical"></i></div>--}}
                                    <div class="casinocards-container">
                                        <span class="text-color-white">PLAYER A</span>
                                        <div class="card_con" id="casinoCarda">
                                        </div>
                                        <span class="text-color-white mt-1">PLAYER B</span>
                                        <div class="card_con" id="casinoCardb">
                                        </div>
                                    </div>
                                </div>
                                <div class="casino_time">
                                    <div id="app"></div>
                                </div>
                            </div>
                            <div class="casino-videodetails" id="appendData">
                                <table id="fullMarketBoard" class="bets">
                                    <tbody>
                                    <tr class="bet-all">
                                        <td></td>
                                        <td colspan="2"><a id="backAll" class="back-allcasino"><span>Back</span></a>
                                        </td>
                                        <td colspan="2"><a id="backAll" class="back-allcasino"><span></span></a></td>
                                    </tr>
                                    <tr style="display: table-row;" id="fullSelection_1">
                                        <th><p>Player A</p><span
                                                id="withoutBet" class="win"
                                                style="display: none;"></span><span
                                                id="lossWithoutBet" class="win"
                                                style="display: none;"></span><span
                                                id="withBet" class="win"
                                                style="display: none;"></span><span
                                                id="lossWithBet" class="win"
                                                style="display: none;"></span><span
                                                id="zeroProfit" class="win"
                                                style="display: none;"></span><span
                                                id="zeroLiability" class="win"
                                                style="display: none;"></span><span
                                                id="zeroProfitWithBet" class="win"
                                                style="display: none;"></span><span
                                                id="zeroLiabilityWithBet"
                                                class="win"
                                                style="display: none;"></span></th>
                                        <td id="suspend" colspan="4" class="suspend"
                                            style="display: none;"><span>Suspend</span></td>
                                        <td id="back_1" colspan="2" class="back-1 suspended"><a
                                            >1.98<span
                                                    class="black">0</span></a></td>
                                        <td id="back_1" colspan="2" class="back-1 suspended"><a
                                            >Pair plus A<span
                                                    class="black">0</span></a></td>
                                    </tr><!----><!----><!----><!---->
                                    <tr style="display: table-row;" id="fullSelection_3">
                                        <th><p>Player B</p><span
                                                id="withoutBet" class="win"
                                                style="display: none;"></span><span
                                                id="lossWithoutBet" class="win"
                                                style="display: none;"></span><span
                                                id="withBet" class="win"
                                                style="display: none;"></span><span
                                                id="lossWithBet" class="win"
                                                style="display: none;"></span><span
                                                id="zeroProfit" class="win"
                                                style="display: none;"></span><span
                                                id="zeroLiability" class="win"
                                                style="display: none;"></span><span
                                                id="zeroProfitWithBet" class="win"
                                                style="display: none;"></span><span
                                                id="zeroLiabilityWithBet"
                                                class="win"
                                                style="display: none;"></span></th>
                                        <td id="suspend" colspan="4" class="suspend"
                                            style="display: none;"><span>Suspend</span></td>
                                        <td id="back_1" colspan="2" class="back-1 suspended"><a
                                            >1.98<span
                                                    class="black">0</span></a></td>
                                        <td id="back_1" colspan="2" class="back-1 suspended"><a
                                            >Pair plus B<span
                                                    class="black">0</span></a></td>
                                    </tr><!----><!----><!----><!----><!----></tbody>
                                </table>
                            </div>
                            <div class="mobile_res_data">

                                <div id="gameHead" class="game-head">
                                    <div class="game-team">
                                        <div class="game-name">Last Result</div>
                                    </div>
                                </div>
                                <div class="mb-10">
                                    <p id="last-result" class="text-right">
                                        <span class="ball-runs last-result playerb">B</span>
                                        <span class="ball-runs last-result playera">A</span>
                                        <span class="ball-runs last-result playerb">B</span>
                                        <span class="ball-runs last-result playerb">B</span>
                                        <span class="ball-runs last-result playerb">B</span>
                                        <span class="ball-runs last-result playera">A</span>
                                        <span class="ball-runs last-result playerb">B</span>
                                        <span class="ball-runs last-result playera">A</span>
                                        <span class="ball-runs last-result playera">A</span>
                                        <span class="ball-runs last-result playera">A</span>
                                    </p>
                                </div>

                                <div class="casino_rules_table mt-2">
                                    <div class="casinolay_bettitle black-bg-2 text-color-white">
                                        <span>Rules</span>
                                    </div>
                                    <div class="table-responsive">

                                        <table class="table table-bordered rules-table"
                                               style="background-color: white;">
                                            <tbody>
                                            <tr class="text-center">
                                                <th colspan="2">Pair Plus</th>
                                            </tr>
                                            <tr>
                                                <td width="60%">Pair (Double)</td>
                                                <td>1 To 1</td>
                                            </tr>
                                            <tr>
                                                <td width="60%">Flush (Color)</td>
                                                <td>1 To 4</td>
                                            </tr>
                                            <tr>
                                                <td width="60%">Straight (Rown)</td>
                                                <td>1 To 6</td>
                                            </tr>
                                            <tr>
                                                <td width="60%">Trio (Teen)</td>
                                                <td>1 To 35</td>
                                            </tr>
                                            <tr>
                                                <td width="60%">Straight Flush (Pakki Rown)</td>
                                                <td>1 To 45</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="casino_right_side showForm" style="">
                    <form class="">
                        <div class="casinolay_bettitle black-bg-2 text-color-white">
                            <span>Place Bet</span>
                            <span class="float-right casinomin_max">Range:<span>{{$casino->min_casino}}</span>-<span>{{$casino->max_casino}}K</span></span>
                        </div>
                        <div class="casinoplay-betheader light-grey-bg-4">
                            <div>(Bet for)</div>
                            <div>Odds</div>
                            <div>Stake</div>
                            <div>Profit</div>
                        </div>
                        <div class="casinoplay-box blue-dark-bg">
                            <div class="casinoplay_betinfo">
                                <div class="bet_player"><span id="team_name">PLAYER A</span></div>
                                <div class="odds_box">
                                    <input type="text" id="odds_val" value="" disabled="disabled"
                                           class="odds_val form-control">
                                    <img src="https://sitethemedata.com/v3/static/front/img/arrow-down.svg"
                                         class="arrow-up">
                                    <img src="https://sitethemedata.com/v3/static/front/img/arrow-down.svg"
                                         class="arrow-down">
                                </div>
                                <div class="bet_input back_border">
                                    <input type="text" id="stake_val" class="form-control input-stake"
                                           onkeypress="return isNumber(event)">
                                </div>
                                <div>0</div>
                            </div>
                            <div class="casinoplay_button">
                                <?php $stkval = array("100", "200", "300", "400", "500", "600", "700", "800"); ?>
                                @foreach($stkval as $data1)
                                    <button type="button" class="btn btn-bet green-bg-1 text-color-white casino_odds"
                                            data-odd="{{$data1}}"><span>{{$data1}}</span></button>
                                @endforeach
                            </div>
                            <div class="casinoplay_action_buttons">
                                <button class="btn btn-reset red-bg text-color-white" type="reset" value="Reset">Reset
                                </button>
                                <button class="btn btn-ok green-bg text-color-white casino_bet" type="button"
                                        value="Submit">Submit
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <div class="modal golden_modal1 fade" id="exampleModal2" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content light-grey-bg-2">
                <div class="modal-header blue-dark-bg-3">
                    <h5 class="modal-title text-color-yellow-1 w-100 text-center" id="exampleModalLabel">Rules</h5>
                    <button type="button" class="close text-color-grey-1" data-dismiss="modal" aria-label="Close">×
                    </button>
                </div>
                <div class="modal-body">
                    <div class="p-5 modal-plus-block text-center">
                        <img src="{{ URL::to('asset/front/img/teen.jpg') }}" class="img-fluid trapmodal_img">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal golden_modal1 current_modal fade" id="exampleModal3" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header darkblue-bg">
                    <h5 class="modal-title text-color-yellow1" id="exampleModalLabel">Details</h5>
                    <button type="button" class="close text-color-grey-2" data-dismiss="modal" aria-label="Close">×
                    </button>
                </div>
                <div class="modal-body teenpatti20_results" id="appnedLastResult">
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
                                            <div class="casino-result-cards-item"><img
                                                    src="{{ URL::to('asset/front/img/6CC.png') }}"></div>
                                            <div class="casino-result-cards-item"><img
                                                    src="{{ URL::to('asset/front/img/JHH.png') }}"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="casino-result-content-diveder darkblue-bg"></div>
                                <div class="casino-result-content-item text-center">
                                    <div class="casino-result-cards">
                                        <div class="casino-result-cards-item"><img
                                                src="{{ URL::to('asset/front/img/winner.png') }}" class="winner_icon">
                                        </div>
                                        <div class="d-inline-block">
                                            <h4>Player B</h4>
                                            <div class="casino-result-cards-item"><img
                                                    src="{{ URL::to('asset/front/img/4HH.png') }}"></div>
                                            <div class="casino-result-cards-item"><img
                                                    src="{{ URL::to('asset/front/img/QCC.png') }}"></div>
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

        function opnForm(vl) {
            var value = $(vl).data("val");
            $(".showForm").show();
            var teamName = $(vl).data("team");
            if (teamName == 'PAIR PLUS A' || teamName == 'PAIR PLUS B') {
                $('.odds_box').css('display', 'none');
            } else {
                $(".odds_val").val(value);
                $('.odds_box').css('display', 'block');
            }
            $('#team_name').html(teamName);
        }

        $(".casino_odds").click(function () {
            var oddval = $(this).data("odd");
            $('.input-stake').val(oddval);
        });
        // bet calculation
        $(".casino_bet").click(function () {
            var roundid = $('.roundId').text();
            var odds_value = $('#odds_val').val();
            var stake_value = $('#stake_val').val();
            var team_name = $('#team_name').html();
            $.ajax({
                type: "POST",
                url: '{{route("casino_bet")}}',
                data: {
                    _token: _token,
                    odds_value: odds_value,
                    stake_value: stake_value,
                    team_name: team_name,
                    roundid: roundid,

                },
                beforeSend: function () {
                    $('#site_bet_loading1').show();
                },
                complete: function () {
                    $('#site_bet_loading1').hide();
                },
                success: function (data) {
                    toastr.success('Bet placed successfully');
                }

            });
        });

        $(document).ready(function () {
            getCasinoteen20();
            setInterval(function () {
                // getCasinoteen20();
            }, 1000);

            // Default call
            function getCasinoteen20() {
                $.ajax({
                    type: "POST",
                    url: '{{route("casino.live.data-call",$casino->id)}}',
                    data: {
                        _token: _token
                    },
                    beforeSend: function () {
                        $('#site_statistics_loading').show();
                    },
                    complete: function () {
                        $('#site_statistics_loading').hide();
                    },
                    success: function (data) {
                        var myarr = [];
                        var spl = data.split('~~');
                        $('.roundId').html(spl[2]);
                        // $('#appendData').html(spl[0]);
                        $('#casinoCarda').html(spl[3]);
                        $('#casinoCardb').html(spl[4]);
                        if (spl[1] > 0) {
                            timerteen20(spl[1]);
                        }
                    }
                });
            }

            // timer
            function timerteen20(val) {
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
                url: '{{route("getteen20LastResult")}}',
                data: {
                    _token: _token
                },
                beforeSend: function () {
                    $('#site_bet_loading1').show();
                },
                complete: function () {
                    $('#site_bet_loading1').hide();
                },
                success: function (data) {
                    var spl = data.split('~~');
                    $("#last_result").html(spl[1]);
                    //$('#exampleModal3').modal('show');
                }
            });
        });

        function openLastPopup(round) {
            $.ajax({
                type: "POST",
                url: '{{route("getteen20LastResultpopup")}}',
                data: {
                    _token: _token,
                    round: round
                },
                beforeSend: function () {
                    $('#site_bet_loading1').show();
                },
                complete: function () {
                    $('#site_bet_loading1').hide();
                },
                success: function (data) {
                    var spl = data.split('~~');
                    $("#appnedLastResult").html(data);
                    $('#exampleModal3').modal('show');
                }
            });
        }
    </script>
@endsection
