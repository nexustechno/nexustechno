@extends('layouts.app')
@section('content')
    <style type="text/css">
        /*.betloaderimage1 {
            background-image: url(../asset/front/img/loaderajaxbet.gif);
            background-repeat: no-repeat;
            background-position: center;
            min-height: 100px;
            background-size: 60px;
        }*/
    </style>
    <!--<div id="site_bet_loading1" class="betloaderimage1 loader-style1" style="display: none;"></div>-->

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

    <section class="profit-section section-mlr">
        <div class="container">
            <div class="inner-title">
                <h2>Bet List Live</h2>
            </div>
            <div class="timeblock light-grey-bg-2">
                <div class="multiple-radiobtn pl-0 pr-0">
                    <label for="radio1">
                        <input type="radio" name="radio" id="radio1" value="all" checked> All
                    </label>
                    @foreach($sports as $sport)
                        <label for="radio2">
                            <input type="radio" name="radio" id="radio2"
                                   value="{{$sport->sId}}"> {{strtoupper($sport->sport_name)}}
                        </label>
                    @endforeach
                </div>
                <div class="timeblock-box">
                    <div class="datediv2">
                        <span>Order of display:</span>
                        <select name="" id="" class="form-control">
                            <option>Stake</option>
                            <option>Time</option>
                        </select>
                    </div>
                    <div class="datediv2">
                        <span>Last:</span>
                        <select name="" id="" class="form-control">
                            <option>All</option>
                            <option>100 Txn</option>
                            <option>50 Txn</option>
                            <option>25 Txn</option>
                        </select>
                    </div>
                    <div class="datediv2">
                        <span>Bet Status:</span>
                        <select name="" id="" class="form-control">
                            <option>Matched</option>
                            <option>Declared</option>
                        </select>
                    </div>
                    <input class="search-input" type="text" name="userId" id="userSearch" placeholder="Find Player Name"
                           style="padding: 5px;border-radius: 2px; border: 1px solid #000;">
                    <a class="submit-btn text-color-yellow btn1" onclick="getHistorylive(1)"> Refresh </a>
                </div>
            </div>
            <div class="maintable-raju-block betlistlive-block">
                <table class="table custom-table white-bg">
                    <caption class="caption-highlight blue-bg-1 text-color-white">Bets</caption>
                    <table class="table custom-table white-bg text-color-blue-2">
                        <thead>
                        <tr>
                            @if($loginUser->agent_level=='COM')
                                <th class="light-grey-bg">COM</th>
                            @endif
                            @if($loginUser->agent_level=='AD' || $loginUser->agent_level=='COM')
                                <th class="light-grey-bg">AD</th>
                            @endif
                            @if($loginUser->agent_level=='SP' || $loginUser->agent_level=='COM' || $loginUser->agent_level=='AD')
                                <th class="light-grey-bg">SP</th>
                            @endif
                            @if($loginUser->agent_level=='SMDL' || $loginUser->agent_level=='COM' || $loginUser->agent_level=='AD' || $loginUser->agent_level=='SP')
                                <th class="light-grey-bg">SMDL</th>
                            @endif
                            @if($loginUser->agent_level=='MDL' || $loginUser->agent_level=='COM' || $loginUser->agent_level=='AD' || $loginUser->agent_level=='SP' || $loginUser->agent_level=='SMDL')
                                <th class="light-grey-bg">MDL</th>
                            @endif
                            @if($loginUser->agent_level=='DL' || $loginUser->agent_level=='COM' || $loginUser->agent_level=='AD' || $loginUser->agent_level=='SP' || $loginUser->agent_level=='SMDL' || $loginUser->agent_level=='MDL')
                                <th class="light-grey-bg">DL</th>
                            @endif
                            <th class="light-grey-bg">PL</th>
                            <th class="light-grey-bg">Bet ID</th>
                            <th class="light-grey-bg">Bet taken</th>
                            <th class="light-grey-bg">Market</th>
                            <th class="light-grey-bg">Selection</th>
                            <th class="light-grey-bg">Type</th>
                            <th class="light-grey-bg">Odds req.</th>
                            <th class="light-grey-bg">Stake</th>
                            <th class="light-grey-bg">Exposure</th>
                        </tr>
                        </thead>
                        <tbody id="append_data">
                        </tbody>
                    </table>
                </table>
                <div class="player pagination-wrap light-grey-bg-1"></div>
            </div>
        </div>
    </section>
    <script type="text/javascript">
        var _token = $("input[name='_token']").val();
        $(document).ready(function () {
            getHistorylive(1);

            $('#userSearch').keyup(function () {
                getHistorylive(1);
                {{--var val = $('#userSearch').val();--}}
                {{--$.ajax({--}}
                {{--    type: "post",--}}
                {{--    url: '{{route("playersearch")}}',--}}
                {{--    data: {--}}
                {{--        _token: _token,--}}
                {{--        search: val,--}}
                {{--    },--}}
                {{--    beforeSend: function () {--}}
                {{--        $('#site_bet_loading1').show();--}}
                {{--    },--}}
                {{--    complete: function () {--}}
                {{--        $('#site_bet_loading1').hide();--}}
                {{--    },--}}
                {{--    success: function (data) {--}}
                {{--        //document.getElementById("site_bet_loading1").style.display = "none";--}}
                {{--        $('#append_data').html(data.html);--}}
                {{--    }--}}
                {{--});--}}
            });
        });

        function getHistorylive(page) {
            //document.getElementById("site_bet_loading1").style.display = "block";
            var sport = $('input[name="radio"]:checked').val();
            var search = $('#userSearch').val();
            $.ajax({
                type: "POST",
                url: '{{route("getHistorylive")}}',
                data: {
                    _token: _token,
                    sport: sport,
                    search:search,
                    page:page
                },
                beforeSend: function () {
                    $('#site_bet_loading1').show();
                },
                complete: function () {
                    $('#site_bet_loading1').hide();
                },
                success: function (data) {
                    //document.getElementById("site_bet_loading1").style.display = "none";
                    $('#append_data').html(data.html);
                    $(".player.pagination-wrap").html(data.pagination);
                }
            });
        }

        $(document).on('click', '.player.pagination-wrap .pagination a', function (event) {
            event.preventDefault();
            var page = $(this).attr('href').split('page=')[1];

            getHistorylive(page);
        });
    </script>
@endsection
