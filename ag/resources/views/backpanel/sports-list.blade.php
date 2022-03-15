@extends('layouts.app')
@section('content')
<?php
use Carbon\Carbon;
use App\Match;
?>

    <style type="text/css">
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

    <section class="pb-5">
        <div class="container">
            <div class="inner-title player-right justify-content-between py-2">
                <h2>Sports Main Market</h2>
            </div>

            <div class="alert alert-success fade in alert-dismissible show statusMsg" style="display: none;">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"
                    style="line-height:23px">
                    <span aria-hidden="true" style="font-size:20px">×</span>
                </button> Status updated successfully
            </div>

            <div class="in_play_tabs-2 sports_tablenew  mb-0">
                <ul class="nav nav-tabs" role="tablist">
                    @php
                        $i = 0;
                    @endphp
                    @foreach ($sports as $sport)
                        <li class="nav-item">
                            <a class="nav-link text-color-blue-1 white-bg {{ $i == 0 ? 'active' : '' }}"
                                href="#{{ $sport->sport_name }}" data-toggle="tab">{{ $sport->sport_name }}</a>
                        </li>
                        @php $i++ @endphp
                    @endforeach
                </ul>
                <div class="tab-content">
                    @php
                        $i = 0;
                    @endphp
                    @foreach ($sports as $sport)
                        <div role="tabpanel" class="tab-pane {{ $i == 0 ? 'active show' : '' }}"
                            id="{{ $sport->sport_name }}">
                            <div class="table-responsive table_cricketn">
                                <table class="table text-color-blue-2">
                                    <thead>
                                        <tr class="white-bg">
                                            <th width="25%">Match Name</th>
                                            @if ($sport->sId == 4)
                                                <th class="text-center">Bookmaker</th>
                                                <th class="text-center">Fancy</th>
                                            @endif
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Odds Limit</th>
                                            <th class="text-center">Bet Odds Limit</th>
                                            @if ($sport->id == 1)
                                                <th class="text-center">Bet Bookmaker Limit</th>
                                                <th class="text-center">Bet Fancy Limit</th>
                                            @endif
                                            <th class="text-center">Winner</th>
                                        </tr>
                                    </thead>

                                    <tbody class="white-bg">
                                        @foreach ($sport->matches as $match)
                                            @if ($sport->sId == $match->sports_id)
                                                @php
                                                    $status = '';
                                                    $active = '';
                                                    $bmStatus = '';
                                                    $fStatus = '';
                                                @endphp
                                                @if ($match->status == 1)
                                                    @php
                                                        $status = 'checked';
                                                    @endphp
                                                @endif
                                                @if ($match->action == 1)
                                                    @php
                                                        $active = 'checked';
                                                    @endphp
                                                @endif

                                                @if ($match->bookmaker == 1)
                                                    @php
                                                        $bmStatus = 'checked';
                                                    @endphp
                                                @endif

                                                @if ($match->fancy == 1)
                                                    @php
                                                        $fStatus = 'checked';
                                                    @endphp
                                                @endif
                                                <?php

                                                $match_date = $match->match_date;
                                                $dt = '';

                                                $date = Carbon::parse(strtotime($match_date));
                                                $date->addMinutes(330);

                                                if (Carbon::parse($date)->isToday()) {
                                                    $match_date = date('d-m-Y h:i A', strtotime($match_date));
                                                } elseif (Carbon::parse($date)->isTomorrow()) {
                                                    $match_date = 'Tomorrow ' . date('d-m-Y h:i A', strtotime($match_date));
                                                } else {
                                                    $match_date = date('d-m-Y h:i A', strtotime($match_date));
                                                }

                                                if ($match->match_finish == 1) {
                                                    $match_date = 'Suspend';
                                                    $cls = 'text-color-red';
                                                } else {
                                                    $match_date = $match_date;
                                                    $cls = '';
                                                }
                                                ?>
                                                <tr>
                                                    <td>
                                                        <a href="#" class="text-color-blue-light">{{ $match->match_name }}</a>
                                                        <div class="{{ $cls }}">{{ $match_date }}</div>
                                                    </td>

                                                    @if ($sport->sId == 4)
                                                        <td class="text-center">
                                                            <label for="checkactive">
                                                                <input type="checkbox" {{ $bmStatus }}
                                                                    id="checkbookmaker{{ $match->id }}"
                                                                    class="chkstatusbm" data-fid="{{ $match->id }}"
                                                                    value="1"><br /> Bookmaker
                                                            </label>
                                                        </td>

                                                        <td class="text-center">
                                                            <label for="checkactive">
                                                                <input type="checkbox" {{ $fStatus }}
                                                                    id="checkfancy{{ $match->id }}"
                                                                    class="chkstatusfancy" data-fid="{{ $match->id }}"
                                                                    value="1"><br /> Fancy
                                                            </label>
                                                        </td>
                                                    @endif

                                                    <td class="text-center">
                                                        <label for="checkactive">
                                                            <input type="checkbox" {{ $status }}
                                                                id="checkactive{{ $match->id }}" class="chkstatus"
                                                                data-fid="{{ $match->id }}" value="1"><br /> Active /
                                                            Inactive
                                                        </label>
                                                    </td>

                                                    <td class="text-center">
                                                        <div class="minmax_input">
                                                            Max <input type="text" id="oddsmax{{ $match->id }}"
                                                                name="max" class="form-control txtoddsmax allowNumeric"
                                                                data-fid="{{ $match->id }}"
                                                                value="{{ $match->odds_limit }}">
                                                        </div>
                                                    </td>

                                                    <td class="text-center">
                                                        <div class="minmax_input">
                                                            Min <input type="text" id="betmin{{ $match->id }}"
                                                                name="min" class="form-control txtbetmin allowNumeric"
                                                                data-fid="{{ $match->id }}"
                                                                value="{{ $match->min_bet_odds_limit }}">

                                                            Max <input type="text" id="betmax{{ $match->id }}"
                                                                name="max" class="form-control txtbetmax allowNumeric"
                                                                data-fid="{{ $match->id }}"
                                                                value="{{ $match->max_bet_odds_limit }}">

                                                        </div>
                                                    </td>

                                                    @if ($sport->id == 1)

                                                        <td class="text-center">
                                                            <div class="minmax_input">
                                                                Min <input type="text" id="bmmin{{ $match->id }}"
                                                                    name="min" class="form-control txtbmmin allowNumeric"
                                                                    data-fid="{{ $match->id }}"
                                                                    value="{{ $match->min_bookmaker_limit }}">

                                                                Max <input type="text" id="bmmax{{ $match->id }}"
                                                                    name="max" class="form-control txtbmmax allowNumeric"
                                                                    data-fid="{{ $match->id }}"
                                                                    value="{{ $match->max_bookmaker_limit }}">
                                                            </div>
                                                        </td>

                                                        <td class="text-center">
                                                            <div class="minmax_input">
                                                                Min <input type="text" id="fancymin{{ $match->id }}"
                                                                    name="min" class="form-control txtfancymin allowNumeric"
                                                                    data-fid="{{ $match->id }}"
                                                                    value="{{ $match->min_fancy_limit }}">

                                                                Max <input type="text" id="fancymax{{ $match->id }}"
                                                                    name="max" class="form-control txtfancymax allowNumeric"
                                                                    data-fid="{{ $match->id }}"
                                                                    value="{{ $match->max_fancy_limit }}">
                                                            </div>
                                                        </td>
                                                    @endif

                                                    <td class="text-center">
                                                        <a href="#" class="logbtn text-color-black grey-gradient-bg"
                                                            data-toggle="modal"
                                                            data-target="#myWinner{{ $match->id }}">View</a>
                                                        <div class="modal credit-modal" id="myWinner{{ $match->id }}">
                                                            <div class="modal-dialog modal-lg">
                                                                <div class="modal-content winner_block light-grey-bg-1">
                                                                    <div class="modal-header border-0">
                                                                        <h4 class="modal-title text-color-blue-1">
                                                                            {{ $match->match_name }}</h4>
                                                                        <button type="button" class="close"
                                                                            data-dismiss="modal"><img
                                                                                src="{{ URL::to('asset/img/close-icon.png') }}"></button>
                                                                        <input type="hidden" name="hidden_match_id"
                                                                            id="hidden_match_id"
                                                                            value="{{ $match->id }}" />
                                                                    </div>

                                                                    <div class="modal-body">
                                                                        <form action="" class="captcha_form d-flex">
                                                                            <?php
                                                                            $team = explode(' v ', ($match->match_name));
                                                                            ?>
                                                                            <table>
                                                                                <tr>
                                                                                    <td>{{ @ucfirst($team[0]) }}</td>
                                                                                    <td><input type="radio"
                                                                                            @if ($match->winner == (@$team[0])) checked @endif
                                                                                            class="team_winner"
                                                                                            data-matchid="{{ $match->id }}"
                                                                                            data-winner="{{ @$team[0] }}"
                                                                                            data-team="team1"
                                                                                            name="check_winner{{ $match->id }}"
                                                                                            id="check_winner{{ $match->id }}"
                                                                                            value="{{ @$team[0] }}" />
                                                                                    </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>{{ ucfirst(@$team[1]) }}</td>
                                                                                    <td><input type="radio"
                                                                                            @if ($match->winner == (@$team[1])) checked @endif
                                                                                            class="team_winner"
                                                                                            data-matchid="{{ $match->id }}"
                                                                                            data-winner="{{ @$team[1] }}"
                                                                                            data-team="team2"
                                                                                            name="check_winner{{ $match->id }}"
                                                                                            id="check_winner{{ $match->id }}"
                                                                                            value="{{ @$team[1] }}" />
                                                                                    </td>
                                                                                </tr>

                                                                                @if ($match->is_draw == '1' || $match->sports_id == '1')
                                                                                    <tr>
                                                                                        <td>The Draw</td>
                                                                                        <td><input type="radio"
                                                                                                @if ($match->winner == 'The Draw') checked @endif
                                                                                                class="team_winner"
                                                                                                data-matchid="{{ $match->id }}"
                                                                                                data-winner="The Draw"
                                                                                                data-team="draw"
                                                                                                name="check_winner{{ $match->id }}"
                                                                                                id="check_winner{{ $match->id }}"
                                                                                                value="The Draw" /></td>
                                                                                    </tr>
                                                                                @endif
                                                                                <tr>
                                                                                    <td>CANCEL</td>
                                                                                    <td><input @if ($match->winner == 'TIE') checked @endif
                                                                                            type="radio"
                                                                                            class="team_winner"
                                                                                            data-matchid="{{ $match->id }}"
                                                                                            data-winner="TIE"
                                                                                            data-team="draw"
                                                                                            name="check_winner{{ $match->id }}"
                                                                                            id="check_winner{{ $match->id }}"
                                                                                            value="TIE" /></td>
                                                                                </tr>
                                                                            </table>

                                                                            <?php
                                                                            $randomNumber = random_int(1000, 9999);
                                                                            ?>

                                                                            <div class="pl-4">
                                                                                <div class="form-group">
                                                                                    <input type="text" name="validationcode"
                                                                                        id="validationcode"
                                                                                        placeholder="Validation Code"
                                                                                        class="validationcode{{ $match->id }} form-control"
                                                                                        onkeypress="return (event.charCode == 8 || event.charCode == 0 || event.charCode == 13) ? null : event.charCode >= 48 && event.charCode <= 57">

                                                                                    <span
                                                                                        class="randomNum{{ $match->id }} validation-txt text-color-black">{{ $randomNumber }}</span>

                                                                                    <span class="text-danger cls-error"
                                                                                        id="errvalid{{ $match->id }}"></span>
                                                                                </div>

                                                                                <button type="button"
                                                                                    data-count="{{ $match->id }}"
                                                                                    class="login-btn text-color-yellow team_winner_set">
                                                                                    Submit <img
                                                                                        src="{{ URL::to('asset/img/login/logout-yellow.svg') }}">
                                                                                </button>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php //}
                                                ?>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        @php $i++ @endphp
                    @endforeach
                </div>
            </div>
        </div>
        </div>
    </section>

    <input type="hidden" name="_token" value="{{ csrf_token() }}">


    <script>
        $(document).ready(function() {
            $("#myWinner").modal({
                show: false,
                backdrop: 'static'
            });
        });

        //decide winner

        $(".team_winner_set").on('click', function(event) {
            var count = $(this).data('count');
            var randomNumber = $('.randomNum' + count).text();
            var validationcode = $('.validationcode' + count).val();
            var retu = 1;

            if (validationcode == '') {
                toastr.error('Captcha can not be blank!');
                retu = 0;
            }

            if (validationcode != randomNumber) {
                toastr.error('Captcha is not valid!');
                retu = false;
            }

            if (retu == 1) {
                var chk_name = "check_winner" + count;
                var winner = $('input:radio[name=' + chk_name + ']:checked').val()
                var matchid = count;

                if (winner != '') {
                    var _token = $("input[name='_token']").val();
                    $.ajax({
                        type: "POST",
                        url: '{{ route('decideMatchWinner') }}',
                        data: {
                            _token: _token,
                            winner: winner,
                            matchid: matchid
                        },
                        beforeSend: function() {
                            $('#site_bet_loading1').show();
                        },
                        complete: function() {
                            $('#site_bet_loading1').hide();
                        },
                        success: function(data) { //alert(data);
                            if (data.trim() == 'Success') {
                                toastr.success('Winner set successfully!');
                                setTimeout(function() {
                                    location.reload();
                                }, 100);

                            } else if (data.trim() == 'Fail') {
                                toastr.error('First decide result of fancy!');
                                return false;
                            } else {
                                toastr.error('Problem in winner setting!');
                            }
                        }
                    });
                }
            }

            if (retu == 1) {
                return true;
            } else {
                return false;
            }
        });

        $(".chkstatusbm").on('click', function(event) {
            var _token = $("input[name='_token']").val();
            var fid = $(this).attr('data-fid');
            var chk = (this.checked ? $(this).val() : "");
            //alert(chk);

            $.ajax({
                type: "POST",
                url: '{{ route('chkstatusbm') }}',
                data: {
                    _token: _token,
                    fid: fid,
                    chk: chk
                },
                beforeSend: function() {
                    $('#site_bet_loading1').show();
                },
                complete: function() {
                    $('#site_bet_loading1').hide();
                },
                success: function(data) {
                    if (data.result == 'error') {
                        toastr.error(data.message);
                        $('#checkbookmaker' + fid).prop('checked', false); // Unchecks it
                    }
                    if (data.result == 'success') {
                        toastr.success(data.message);
                    }
                }
            });
        });

        $(".chkstatusfancy").on('click', function(event) {
            var _token = $("input[name='_token']").val();
            var fid = $(this).attr('data-fid');
            var chk = (this.checked ? $(this).val() : "");
            $.ajax({
                type: "POST",
                url: '{{ route('chkstatusfancy') }}',
                data: {
                    _token: _token,
                    fid: fid,
                    chk: chk
                },
                beforeSend: function() {
                    $('#site_bet_loading1').show();
                },
                complete: function() {
                    $('#site_bet_loading1').hide();
                },
                success: function(data) {
                    if (data.result == 'error') {
                        toastr.error(data.message);
                        $('#checkfancy' + fid).prop('checked', false); // Unchecks it
                    }
                    if (data.result == 'success') {
                        toastr.success(data.message);
                    }
                }
            });
        });

        $(".chkstatus").on('click', function(event) {
            var _token = $("input[name='_token']").val();
            var fid = $(this).attr('data-fid');
            var chk = (this.checked ? $(this).val() : "");
            $.ajax({
                type: "POST",
                url: '{{ route('saveMatchStatus') }}',
                data: {
                    _token: _token,
                    fid: fid,
                    chk: chk
                },
                beforeSend: function() {
                    $('#site_bet_loading1').show();
                },
                complete: function() {
                    $('#site_bet_loading1').hide();
                },
                success: function(data) {
                    if (data.trim() == 'Fail')
                        toastr.error('Problem in status update!');
                    else
                        toastr.success('Status changed successfully!');
                }
            });
        });

        $(".chkaction").on('click', function(event) {
            var _token = $("input[name='_token']").val();
            var fid = $(this).attr('data-fid');
            var chk = (this.checked ? $(this).val() : "");
            $.ajax({
                type: "POST",
                url: '{{ route('saveMatchAction') }}',
                data: {
                    _token: _token,
                    fid: fid,
                    chk: chk
                },
                beforeSend: function() {
                    $('#site_bet_loading1').show();
                },
                complete: function() {
                    $('#site_bet_loading1').hide();
                },
                success: function(data) {
                    if (data.trim() == 'Fail')
                        toastr.error('Problem in action update!');
                }
            });
        });

        //odds limit

        $(".txtoddsmax").blur(function() {
            var _token = $("input[name='_token']").val();
            var fid = $(this).attr('data-fid');
            var chk = $(this).val();
            $.ajax({
                type: "POST",
                url: '{{ route('saveMatchOddsLimit') }}',
                data: {
                    _token: _token,
                    fid: fid,
                    chk: chk
                },
                beforeSend: function() {
                    $('#site_bet_loading1').show();
                },
                complete: function() {
                    $('#site_bet_loading1').hide();
                },
                success: function(data) {
                    if (data.trim() == 'Fail')
                        toastr.error('Problem in updating odds limit!');
                    else
                        toastr.success('Status changed successfully!');
                }
            });
        });

        //betlimit

        $(".txtbetmin").blur(function() {
            var _token = $("input[name='_token']").val();
            var fid = $(this).attr('data-fid');
            var chk = $(this).val();
            $.ajax({
                type: "POST",
                url: '{{ route('saveMatchBetsMinLimit') }}',
                data: {
                    _token: _token,
                    fid: fid,
                    chk: chk
                },
                beforeSend: function() {
                    $('#site_bet_loading1').show();
                },
                complete: function() {
                    $('#site_bet_loading1').hide();
                },
                success: function(data) {
                    if (data.trim() == 'Fail')
                        toastr.error('Problem in updating odds limit!');
                    else
                        toastr.success('Status changed successfully!');
                }
            });
        });

        $(".txtbetmax").blur(function() {
            var _token = $("input[name='_token']").val();
            var fid = $(this).attr('data-fid');
            var chk = $(this).val();
            $.ajax({
                type: "POST",
                url: '{{ route('saveMatchBetsMaxLimit') }}',
                data: {
                    _token: _token,
                    fid: fid,
                    chk: chk
                },
                beforeSend: function() {
                    $('#site_bet_loading1').show();
                },
                complete: function() {
                    $('#site_bet_loading1').hide();
                },
                success: function(data) {
                    if (data.trim() == 'Fail')
                        toastr.error('Problem in updating odds limit!');
                    else
                        toastr.success('Status changed successfully!');
                }
            });
        });

        //bm limit

        $(".txtbmmin").blur(function() {
            var _token = $("input[name='_token']").val();
            var fid = $(this).attr('data-fid');
            var chk = $(this).val();
            $.ajax({
                type: "POST",
                url: '{{ route('saveMatchBmMinLimit') }}',
                data: {
                    _token: _token,
                    fid: fid,
                    chk: chk
                },
                beforeSend: function() {
                    $('#site_bet_loading1').show();
                },
                complete: function() {
                    $('#site_bet_loading1').hide();
                },
                success: function(data) {
                    if (data.trim() == 'Fail')
                        toastr.error('Problem in updating bookmaker min limit!');
                    else
                        toastr.success('Status changed successfully!');
                }
            });
        });

        $(".txtbmmax").blur(function() {
            var _token = $("input[name='_token']").val();
            var fid = $(this).attr('data-fid');
            var chk = $(this).val();
            $.ajax({
                type: "POST",
                url: '{{ route('saveMatchBmMaxLimit') }}',
                data: {
                    _token: _token,
                    fid: fid,
                    chk: chk
                },
                beforeSend: function() {
                    $('#site_bet_loading1').show();
                },
                complete: function() {
                    $('#site_bet_loading1').hide();
                },
                success: function(data) {
                    if (data.trim() == 'Fail')
                        toastr.error('Problem in updating bookmaker max limit!');
                    else
                        toastr.success('Status changed successfully!');
                }
            });
        });

        //fancy limit

        $(".txtfancymin").blur(function() {
            var _token = $("input[name='_token']").val();
            var fid = $(this).attr('data-fid');
            var chk = $(this).val();
            $.ajax({
                type: "POST",
                url: '{{ route('saveMatchFancyMinLimit') }}',
                data: {
                    _token: _token,
                    fid: fid,
                    chk: chk
                },
                beforeSend: function() {
                    $('#site_bet_loading1').show();
                },
                complete: function() {
                    $('#site_bet_loading1').hide();
                },
                success: function(data) {
                    if (data.trim() == 'Fail')
                        toastr.error('Problem in updating fancy min limit!');
                    else
                        toastr.success('Status changed successfully!');
                }
            });
        });

        $(".txtfancymax").blur(function() {
            var _token = $("input[name='_token']").val();
            var fid = $(this).attr('data-fid');
            var chk = $(this).val();
            $.ajax({
                type: "POST",
                url: '{{ route('saveMatchFancyMaxLimit') }}',
                data: {
                    _token: _token,
                    fid: fid,
                    chk: chk
                },
                beforeSend: function() {
                    $('#site_bet_loading1').show();
                },
                complete: function() {
                    $('#site_bet_loading1').hide();
                },
                success: function(data) {
                    if (data.trim() == 'Fail')
                        toastr.error('Problem in updating fancy max limit!');
                    else
                        toastr.success('Status changed successfully!');
                }
            });
        });

        $(".allowNumeric").keypress(function(e) {
            if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                $("#errmsg").html("Digits Only").show().fadeOut("slow");
                return false;
            }
        });
    </script>
@endsection
