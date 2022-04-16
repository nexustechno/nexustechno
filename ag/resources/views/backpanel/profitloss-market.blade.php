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
        .white-bg {
            font-weight: bold;
        }
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
                <h2>Profit/Loss Report by Market</h2>
            </div>
            <form method="post">
                <div class="timeblock light-grey-bg-2">
                    <div class="timeblock-box">
                        <span>Sports</span>
                        <select name="sportsevent" id="sportsevent" class="form-control">
                            <option value="0">All</option>
                            @foreach($sports as $data)
                                <option value="{{$data->sId}}">{{strtoupper($data->sport_name)}}</option>
                            @endforeach
                        </select>
                        <div class="datediv2">
                            <span>Time Zone</span>
                            <select name="" id="" class="form-control timezone-select">
                                <option value="IST">IST(Bangalore / Bombay / New Delhi) (GMT+5:30)</option>
                            </select>
                        </div>
                        <div class="datebox">
                            <span>Period</span>
                            <div class="datediv1">
                                <div class="datediv">
                                    <input type="text" name="fromdate" id="fromdate"
                                           class="form-control period_date_plf" placeholder="{{Date('d-m-Y')}}"
                                           value="{{Date('d-m-Y')}}">
                                    <img src="{{ URL::to('asset/img/calendar-icon.png')}}" class="calendar-icon">
                                </div>
                                <input type="text" name="" id="" placeholder="09:00" maxlength="5"
                                       class="form-control disable" disabled>
                                <div class="datediv">
                                    <input type="text" name="todate" id="todate" class="form-control period_date_plt"
                                           placeholder="{{Date('d-m-Y', strtotime('+1 days'))}}"
                                           value="{{Date('d-m-Y', strtotime('+1 days'))}}">
                                    <img src="{{ URL::to('asset/img/calendar-icon.png')}}" class="calendar-icon">
                                </div>
                                <input type="text" name="" id="" placeholder="08:59" maxlength="5"
                                       class="form-control disable" disabled>
                            </div>
                        </div>
                        <div class="datediv2">
                            <span>Agent/Player</span>
                            <select name="childlist" id="childlist" class="form-control">
                                <option value="0">All</option>
                                @foreach($users as $data1)
                                    <option value="{{$data1->id}}">{{$data1->agent_level}}
                                        - {{$data1->user_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="timeblock-box">
                        <ul>
                            <li><a class="justbtn grey-gradient-bg text-color-black1" onclick="getHistory('today',1)">
                                    Just For Today </a></li>
                            <li><a class="justbtn grey-gradient-bg text-color-black1" onclick="getHistory('yesterday',1)">
                                    From Yesterday </a></li>
                            <li><a class="submit-btn text-color-yellow" id="" onclick="getHistory('history',1)"> Get P &
                                    L </a></li>
                        </ul>
                    </div>
                </div>
            </form>

            <div class="maintable-raju-block" id="market-table">
                <table class="table custom-table white-bg text-color-blue-2">
                    <thead>
                    <tr>
                        <th class="light-grey-bg">UID</th>
                        <th class="light-grey-bg">Match P/L</th>
                        <th class="light-grey-bg">Match Stake</th>
                        <th class="light-grey-bg">BM P/L</th>
                        <th class="light-grey-bg">BM Stake</th>
                        <th class="light-grey-bg">Fancy P/L</th>
                        <th class="light-grey-bg">Fancy Stake</th>
                        <th class="light-grey-bg">Net P/L</th>
                    </tr>
                    </thead>
                    <tbody id="PLdata">
                    </tbody>
                    <tbody id="totalcnt"></tbody>
                </table>
                <div class="player pagination-wrap light-grey-bg-1"></div>
            </div>
        </div>
    </section>

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script type="text/javascript">
        var _token = $("input[name='_token']").val();
        var selectedValue = '';
        function getHistory(val, page) {
            selectedValue = val;
            //document.getElementById("site_bet_loading1").style.display = "block";
            var sport = $('#sportsevent').find(":selected").val();
            var fromdate = $("#fromdate").val();
            var todate = $("#todate").val();
            var childlist = $('#childlist').find(":selected").val();
            $.ajax({
                type: "post",
                url: '{{route("marketPLdata")}}',
                data: {
                    "_token": "{{ csrf_token() }}",
                    "sport": sport,
                    "fromdate": fromdate,
                    "todate": todate,
                    "childlist": childlist,
                    "val": val,
                    page: page
                },
                beforeSend: function () {
                    $('#site_bet_loading1').show();
                },
                complete: function () {
                    $('#site_bet_loading1').hide();
                },
                success: function (data) {
                    //document.getElementById("site_bet_loading1").style.display = "none";
                    var spl = data.split('~~');
                    $(".cnd").show();
                    $('#PLdata').html(spl[0]);
                    $("#totalcnt").html(spl[1]);
                    $(".player.pagination-wrap").html(spl[2]);
                }
            });
        }

        $(document).on('click', '.player.pagination-wrap .pagination a', function (event) {
            event.preventDefault();
            var page = $(this).attr('href').split('page=')[1];

            getHistory(selectedValue,page);
        });
    </script>
@endsection
