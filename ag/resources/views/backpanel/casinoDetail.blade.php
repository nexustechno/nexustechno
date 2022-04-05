@extends('layouts.app')
@push('page_css')
    <style type="text/css">
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

        /*casino detail page css which copy from front end css file     :: JEET DUMS*/
        .casinotrap-table {
            border-radius: 8px;
            padding-bottom: 0;
            position: relative;
            transform: translateZ(0);
        }

        .game-head {
            margin-bottom: 5px;
        }

        .game-scores, .game-team, .casinolay_bettitle {
            width: 100%;
            background-color: #1b2d38;
            color: #fff;
            line-height: 22px;
            font-size: 15px;
            font-weight: 700;
            padding: 5px 5px 5px 10px;
        }

        .game-scores .game-name span, .game-team .game-name span, .casinolay_bettitle span {
            background-color: #fff;
            color: #243a48;
            padding: 1px 8px;
            border-radius: 4px;
            margin: 0 5px;
        }

        .round {
            background: transparent !important;
            color: #fff !important;
        }

        .casino-video {
            position: relative;
        }

        .casino-video .video-block {
            height: 100%;
            width: 100%;
            position: relative;
            /* padding-bottom: 32.55%; */
        }

        .casino-video .video-block iframe {
            width: 100%;
            height: 350px;
            /* position: absolute; */
            /* top: 0px; */
            /* left: 0px; */
        }

        .bets {
            position: relative;
            background-color: #fff;
            /* border-top: 1px solid #7e97a7; */
            margin-top: 0px;
            margin-bottom: 20px;
            width: 100%;
        }

        .bets .bet-all {
            line-height: 22px;
        }

        .bets .bet-all td {
            background-color: initial;
            border-left-color: #0000;
            padding-bottom: 1px;
        }

        .bets td {
            /* width: 20%; */
            border-bottom: 1px solid #7e97a7;
            border-left: 1px solid #ddd;
            font-weight: 700;
            vertical-align: top;
        }

        .fancy-suspend-tr td {
            padding: 0px !important;
            border: 0px !important;
        }

        .cyan-bg {
            background: #72bbef;
        }

        .pink-bg {
            background: #faa9ba;
        }

        .bets th {
            position: relative;
            padding: 6px 10px;
            border-bottom: 1px solid #7e97a7;
        }

        .bets th p {
            /* width: 292px; */
            margin-bottom: 0;
        }

        .bets th span {
            font-weight: 400;
        }

        .towin {
            background-image: url('{{ asset('asset/img/arrow-green.png') }}');
            background-position: center left;
            background-repeat: no-repeat;
        }

        .tolose {
            background-image: url('{{ asset('asset/img/arrow-red.jpg') }}');
            background-position: center left;
            background-repeat: no-repeat;
        }

        .tolose, .towin {
            margin: 3px 5px 0 0;
            font-weight: 400;
            white-space: nowrap;
{{--            background-image: url('{{ asset('asset/img/arrow-red.jpg') }}');--}}
            background-position: center left;
            background-repeat: no-repeat;
            padding-left: 13px;
            display: inline-block;
        }

        .text-color-green {
            color: #508d0e !important;
        }

        .bets .bet-all a {
            box-sizing: unset;
            width: 100%;
            height: 30px;
            /* line-height: 17px; */
            cursor: pointer;
            display: block;
            text-align: center;
        }

        a:not([href]):not([tabindex]) {
            color: inherit;
            text-decoration: none;
        }

        .bets td a {
            position: relative;
            height: 35px;
            color: #1e1e1e;
            padding: 3px 0 2px;
            cursor: pointer;
        }

        .bets td a {
            position: relative;
            height: 35px;
            color: #1e1e1e;
            padding: 3px 0 2px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-flow: column;
        }

        .ball-runs.playerb {
            background: #355e3b;
            color: #ff3;
        }

        .ball-runs.playera {
            background: #355e3b;
        }

        .ball-runs.playerc {
            background: #355e3b;
            color: #ff3;
        }

        .ball-runs {
            display: inline-block;
            height: 25px;
            line-height: 25px;
            width: 25px;
            border-radius: 50%;
            font-size: var(--font-small);
            background-color: #08c;
            color: #fff;
            text-align: center;
        }
    </style>
@endpush
@section('content')

    <section>
        <div class="container">
            <div class="row">
                <div class="col-sm-8 p-0">
                    <div id="app">
                        <div class="middle1-section casino">
                            <div class="middle-wraper">
                                <casino :admin="true" today="{{ date('Y-m-d H:i:s') }}"
                                        :playerprofit="{{json_encode($playerProfit)}}"
                                        basepath="{{asset('asset/front/img/cards')}}"
                                        :casino="{{ json_encode($casino) }}"></casino>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4 p-0">
                    <div class="casino_right_side rightblock-games white-bg first">
                        <div class="betslip-block mt-10" id="bet_display_table">
                            <a class="collape-link text-color-white blue-gradient-bg1" data-toggle="collapse"
                               href="#collapseExample1" role="button" aria-expanded="false"
                               aria-controls="collapseExample1">
                                <img src="{{ asset('asset/front/img/refresh-white.png')  }}" class="slip_refresh"
                                     alt=""> Open Bets <img src="{{ asset('asset/front/img/minus-icon.png') }}">
                            </a>
                            <div class="collapse show" id="collapseExample1">
                                <div class="collapse show" id="collapseExample1">
                                    <div class="card card-body">
                                        <div class="open_bets_wrap betslip_board">
                                            <ul class="betslip_head lightblue-bg1">
                                                <li class="col-bet"><strong>Matched</strong></li>
                                            </ul>
                                            <div id="divbetlist">
                                                <ul class="betslip_head">
                                                    <li class="col-bet bet_type_uppercase">(Bet For)</li>
                                                    <li class="col-odd">Odds</li>
                                                    <li class="col-stake">Stake</li>
                                                    <li class="col-profit">Profit</li>
                                                </ul>
                                                <div id="bet-list-section">
                                                    @include('backpanel.ajax.casino_bet')
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end for bet display table-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal golden_modal1 fade" id="exampleModal2" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content light-grey-bg-2">
                <div class="modal-header blue-dark-bg-3">
                    <h5 class="modal-title text-color-yellow-1" id="exampleModalLabel">Rules</h5>
                    <button type="button" class="close text-color-grey-1" data-dismiss="modal" aria-label="Close">×
                    </button>
                </div>
                <div class="modal-body">
                    <div class=" modal-plus-block text-center">
                        <img src="{{ asset('asset/front/img/cards/'.$casino->casino_name.'.jpg')}}"
                             class="img-fluid trapmodal_img1">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal golden_modal fade " id="card-result-dialog" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content light-grey-bg-2">
                <div class="modal-header blue-dark-bg-3">
                    <h5 class="modal-title text-color-yellow-1" id="exampleModalLabel">Result</h5>
                    <button type="button" class="close text-color-grey-1" data-dismiss="modal" aria-label="Close">×
                    </button>
                </div>
                <div class="modal-body">
                    <div class=" modal-plus-block text-center">
                        <div class="result-cards-html-section">
                            <h6 class="text-right round-id"><b>Round Id:</b> <span>220203182434</span></h6>
                            <div class="row">
                                <div class="col br1 text-center playera"><h4>Player A</h4>
                                    <div class="result-image"></div>
                                    <div class="winner-icon collapse mt-3"><i class="fas fa-trophy mr-2"></i></div>
                                </div>
                                <div class="col text-center playerb"><h4>Player B</h4>
                                    <div class="result-image"></div>
                                    <div class="winner-icon collapse mt-3"><i class="fas fa-trophy mr-2"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('third_party_scripts')

    <script src="https://cdn.socket.io/socket.io-1.4.5.js"></script>
    <script src="{{ asset('js/laravel-echo-server2.js') }}"></script>

    <script src="{{ asset('js/app.js') }}"></script>
@endpush
@push('page_scripts')
    <script>
        function opnForm() {}

        function loadAllUserBet() {
            var casino_name = '{{ $casino->casino_name }}';
            var _token = '{{ csrf_token() }}';
            $.ajax({
                type: "POST",
                url: '{{route("all_user_casino_bet")}}',
                data: {
                    _token: _token,
                    casino_name: casino_name,
                    roundid: $("#roundId").attr('data-round-id')
                },
                beforeSend: function () {
                },
                complete: function () {
                },
                success: function (data) {
                    if (data.status == true) {
                        console.log("data.playerProfit ",data.playerProfit.length);
                        if(data.playerProfit.length==undefined) {
                            for (const property in data.playerProfit) {

                                $("#" + property + "-profit").removeClass('towin text-color-green');
                                $("#" + property + "-profit").removeClass('tolose text-color-red');

                                if (data.playerProfit[property] > 0) {
                                    $("#" + property + "-profit").addClass('towin text-color-green');
                                } else if (data.playerProfit[property] < 0) {
                                    $("#" + property + "-profit").addClass('tolose text-color-red');
                                }

                                $("#" + property + "-profit").html(data.playerProfit[property]);
                            }
                        }else{
                            $("#fullMarketBoard .profit-lose").removeClass('tolose text-color-red');
                            $("#fullMarketBoard .profit-lose").removeClass('towin text-color-green');
                            $("#fullMarketBoard .profit-lose").addClass('towin text-color-green');
                            $("#fullMarketBoard .profit-lose").html(0);
                        }

                        $("#bet-list-section").html(data.betHtml);

                    }
                }

            });
        }

        $(document).ready(function () {
            setInterval(function () {
                loadAllUserBet()
            }, 5000);
        });
    </script>
@endpush
