@extends('layouts.front_layout')
@push('page_css')
    <style>
        body {
            overflow: hidden;
        }
    </style>
@endpush
@section('content')
<section>
    <div class="container-fluid">
        <div class="main-wrapper">
            @include('layouts.leftpanel')
            <div id="app">
                <div class="middle-section">
                    <div class="middle-wraper">
                        <casino :playerprofit="{{json_encode($playerProfit)}}" basepath="{{asset('asset/front/img/cards')}}" :casino="{{ json_encode($casino) }}"></casino>
                    </div>
                </div>
            </div>
            <div class="casino_right_side rightblock-games white-bg first">
                <!-- Bet Slip-->
                <div class="betslip-block betform-section">
                    <a class="collape-link text-color-white blue-gradient-bg1" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
                        Bet Slip <img src="http://richexchange.test/asset/front/img/minus-icon.png ">
                    </a>
                    <div id="site_bet_loading" class="betloaderimage" style="display: none;"></div>
                    <div class="collapse show" id="collapseExample">
                        <div class="card card-body showForm" id="betslip-block" style="display: none;">
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
                                        <div class="bet_player"><span id="team_name" class="team_name">PLAYER A</span></div>
                                        <div class="odds_box">
                                            <input type="hidden" id="team_sid" value="" disabled="disabled" class="team_sid">
                                            <input type="text" id="odds_val" value="" disabled="disabled" class="odds_val form-control">
                                            <img src="https://sitethemedata.com/v3/static/front/img/arrow-down.svg" class="arrow-up">
                                            <img src="https://sitethemedata.com/v3/static/front/img/arrow-down.svg" class="arrow-down">
                                        </div>
                                        <div class="bet_input back_border">
                                            <input type="text" id="stake_val" class="form-control input-stake" onkeypress="return isNumber(event)">
                                        </div>
                                        <div>0</div>
                                    </div>
                                    <div class="casinoplay_button">
                                        <?php $stkval = array("100", "200", "300", "400", "500", "600", "700", "800"); ?>
                                        @foreach($stkval as $data1)
                                            <button type="button" class="btn btn-bet green-bg-1 text-color-white casino_odds"  data-odd="{{$data1}}"><span>{{$data1}}</span></button>
                                        @endforeach
                                    </div>
                                    <div class="casinoplay_action_buttons">
                                        <button class="btn btn-reset red-bg text-color-white" type="button" onclick="cancelBet()" value="Cancel">Cancel </button>
                                        <button class="btn btn-ok green-bg text-color-white casino_bet" type="button" value="Submit">Submit </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="betslip-block mt-10" id="bet_display_table">
                    <a class="collape-link text-color-white blue-gradient-bg1" data-toggle="collapse" href="#collapseExample1" role="button" aria-expanded="false" aria-controls="collapseExample1">
                        <img src="http://richexchange.test/asset/front/img/refresh-white.png" class="slip_refresh" alt=""> Open Bets <img src="http://richexchange.test/asset/front/img/minus-icon.png">
                    </a>
                    <div class="collapse show" id="collapseExample1"><div class="collapse show" id="collapseExample1">
                            <div class="card card-body">
                                <div class="open_bets_wrap betslip_board">
                                    <ul class="betslip_head lightblue-bg1">
                                        <li class="col-bet"><strong>Matched</strong></li>
                                    </ul>
                                    <div id="divbetlist">
                                        <ul class="betslip_head">
                                            <li class="col-bet bet_type_uppercase">Back (Bet For)</li>
                                            <li class="col-odd">Odds</li>
                                            <li class="col-stake">Stake</li>
                                            <li class="col-profit">Profit</li>
                                        </ul>
                                        <div id="bet-list-section">
                                            @include('front.ajax.casino_bet')
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div></div>
                    <!--end for bet display table-->
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
                <div class=" modal-plus-block text-center">
                    <img src="{{ asset('asset/front/img/cards/'.$casino->casino_name.'.jpg')}}" class="img-fluid trapmodal_img1">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal golden_modal fade " id="card-result-dialog" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content light-grey-bg-2">
            <div class="modal-header blue-dark-bg-3">
                <h5 class="modal-title text-color-yellow-1" id="exampleModalLabel">Result</h5>
                <button type="button" class="close text-color-grey-1" data-dismiss="modal" aria-label="Close">×</button>
            </div>
            <div class="modal-body">
                <div class=" modal-plus-block text-center">
                    <div class="result-cards-html-section">
                        <h6 class="text-right round-id"><b>Round Id:</b> <span>220203182434</span></h6>
                        <div class="row">
                            <div class="col br1 text-center playera"><h4>Player A</h4>
                                <div class="result-image"> </div>
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
    <script src="{{ asset('js/app.js') }}"></script>

    <script>
        var _token = $("input[name='_token']").val();

        function isNumber(evt) {
            evt = (evt) ? evt : window.event;
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                return false;
            }
            return true;
        }

        function cancelBet() {
            if ($(window).width() < 990) {
                $(".mobile-casino-bet-tr .casino_right_side form").remove();
            }else {
                $(".showForm").hide();
            }
        }

        function opnForm(vl) {
            var value = $(vl).attr("data-val");
            console.log("value: ",value);
            var teamName = $(vl).attr("data-team");

            var teamSID = $(vl).attr('data-team-sid');

            if ($(window).width() < 990) {
                $(".mobile-casino-bet-tr .casino_right_side form").remove();
                $("#mobile-casino-bet-td-"+teamSID).html($(".betform-section .showForm").html());
                $("#mobile-casino-bet-tr-"+teamSID).show();
                $(".casinoplay-box .odds_val").val(value);
                $('.casinoplay-box .odds_box').css('display', 'block');
                $('.casinoplay-box .team_name').html(teamName);
                $('.casinoplay-box .team_sid').val(teamSID);
            }else{
                $(".showForm").show();
                $(".casinoplay-box .odds_val").val(value);
                $('.casinoplay-box .odds_box').css('display', 'block');
                $('.casinoplay-box .team_name').html(teamName);
                $('.casinoplay-box .team_sid').val(teamSID);
            }
        }

        $("body").on('click','.casino_odds',function () {
            var oddval = $(this).data("odd");
            $('.input-stake').val(oddval);
        });
        // bet calculation
        $("body").on('click','.casino_bet',function () {

            if($(".roundId").attr('data-status') == 0) {
                toastr.error('Bet Not Confirm Reason Game Suspended.');
                $(".showForm").hide();
            }else {
                // console.log("round id: ", $('.roundId').attr('data-round-id'));
                var roundid = $('.roundId').attr('data-round-id');
                var odds_value = $('#odds_val').val();
                var stake_value = $('#stake_val').val();
                var team_name = $('#team_name').html();
                var team_sid = $('#team_sid').val();
                var casino_name = '{{ $casino->casino_name }}';
                $.ajax({
                    type: "POST",
                    url: '{{route("casino_bet")}}',
                    data: {
                        _token: _token,
                        odds_value: odds_value,
                        stake_value: stake_value,
                        team_name: team_name,
                        team_sid: team_sid,
                        roundid: roundid,
                        casino_name: casino_name,
                        bet_side: 'back'
                    },
                    beforeSend: function () {
                        $('#site_bet_loading1').show();
                    },
                    complete: function () {
                        $('#site_bet_loading1').hide();
                    },
                    success: function (data) {
                        if (data.status == true) {
                            toastr.success(data.message);
                            cancelBet();

                            for (const property in data.playerProfit) {
                                $("#"+property+"-profit").html(data.playerProfit[property]);
                            }

                            // if ($(window).width() < 990) {
                            //
                            // }else{
                                $("#bet-list-section").html(data.betHtml);
                            // }

                        } else {
                            toastr.error(data.message);
                        }
                    }

                });
            }
        });
    </script>
@endpush
