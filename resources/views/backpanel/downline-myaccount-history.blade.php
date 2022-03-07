@extends('layouts.app')
@section('content')
<?php 
$loginuser = Auth::user(); 
?>

<style type="text/css">
    /*.betloaderimage1 {
        background-image: url(../../asset/front/img/loaderajaxbet.gif);
        background-repeat: no-repeat;
        background-position: center;
        min-height: 100px;
        background-size: 60px;
    }*/
</style>
<!--<div id="site_bet_loading1" class="betloaderimage1 loader-style1" style="display: none;"></div>-->

<style type="text/css">
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

<section class="myaccount-section">
    <div class="container">
        <div class="row">
            <div class="col-md-12 pl-0">
                <div class="downline-block">
                    {{--<div class="search-wrap">
                        <svg width="19" height="19" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12.547 11.543H12l-.205-.172a4.539 4.539 0 001.06-2.914A4.442 4.442 0 008.41 4C5.983 4 4 5.989 4 8.457a4.442 4.442 0 004.445 4.457c1.094 0 2.12-.411 2.905-1.062l.206.171v.548L14.974 16 16 14.971l-3.453-3.428zm-4.102 0a3.069 3.069 0 01-3.077-3.086 3.068 3.068 0 013.077-3.086 3.069 3.069 0 013.076 3.086 3.069 3.069 0 01-3.076 3.086z" fill="rgb(30,30,30"></path>
                        </svg>
                        <div>
                            <input class="search-input navy-light-bg" type="text" name="userId" id="userId" placeholder="Find member...">
                            <button class="search-but yellow-bg1" id="searchUserId">Search</button>
                        </div>
                    </div>--}}
                    <ul class="agentlist">
                        <li class="lastli"><a><span class="orange-bg text-color-white">{{$user->agent_level}}</span><strong>{{$user->user_name}}</strong></a></li>                        
                    </ul>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-12 pl-0">
                @include('backpanel/downline-account-menu')
            </div>
            <div class="col-lg-9 col-md-9 col-sm-12">
                <div class="pagetitle text-color-blue-2">
                    <h1>Betting History</h1>
                </div>
                <div class="in_play_tabs-2 mb-0">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link text-color-blue-1 white-bg active" href="#exchange" data-toggle="tab">Exchange</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-color-blue-1 white-bg" href="#sportsbook" data-toggle="tab">Sportsbook</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-color-blue-1 white-bg" href="#bookMaker" data-toggle="tab">BookMaker</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-color-blue-1 white-bg" href="#binary" data-toggle="tab">Binary</a>
                        </li>
                    </ul>
                </div>
                <div class="timeblock light-grey-bg-2">
                    <div class="timeblock-box">
                        <span>Bet Status:</span>
                        <select name="" id="" class="form-control">
                            <option>Settled</option>
                        </select>
                        <div class="datebox">
                            <span>Period</span>
                            <div class="datediv1">
                                <div class="datediv">
                                    <input type="text" name="date_from" id="date_from" class="form-control period_date1" placeholder="{{date('d-m-Y')}}" value="{{date('d-m-Y')}}">
                                    <img src="{{ URL::to('asset/img/calendar-icon.png')}}" class="calendar-icon">
                                </div>
                                <input type="text" name="" id="" placeholder="09:00" maxlength="5" class="form-control disable" disabled="">

                                <div class="datediv">
                                    <input type="text" name="date_to" id="date_to" class="form-control period_date1" placeholder="{{Date('d-m-Y', strtotime('+1 days'))}}" value="{{Date('d-m-Y', strtotime('+1 days'))}}">
                                    <img src="{{ URL::to('asset/img/calendar-icon.png')}}" class="calendar-icon">
                                </div>
                                <input type="text" name="" id="" placeholder="08:59" maxlength="5" class="form-control disable" disabled="">
                            </div>
                        </div>
                    </div>
                    <div class="timeblock-box">
                        <ul>
                            <input type="hidden" name="pid" id="pid" value="{{$id}}">
                            <li> <a class="justbtn grey-gradient-bg text-color-black1" onclick="getBetHistoryP('today')"> Just For Today </a> </li>
                            <li> <a class="justbtn grey-gradient-bg text-color-black1" onclick="getBetHistoryP('yesterday')"> From Yesterday </a> </li>
                            <li> <a class="submit-btn text-color-yellow black-gradient-bg1" onclick="getBetHistoryP('historypl')"> Get History </a> </li>
                        </ul>
                    </div>
                </div>
                <div class="summery-table mt-3">
                    <table class="table custom-table mybets-table">
                        <thead>
                            <tr class="light-grey-bg">
                                <th width="9%">Bet ID</th>
                                <th width="9%" class="text-left">PL ID</th>
                                <th class="text-left">Market</th>
                                <th width="12%" class="text-right">Selection</th>
                                <th width="4%" class="text-right">Type</th>
                                <th width="9%" class="text-right">Bet placed</th>
                                <th width="8%" class="text-right">Stake</th>
                                <th width="8%" class="text-right">Avg. odds <br> matched</th>
                                <th width="10%" class="text-right">Profit/Loss</th>
                            </tr>
                        </thead>
                        <tbody id="bodyData">
                        </tbody>
                    </table>
                    <p>
                        Betting History enables you to review the bets you have placed. <br>

                        Specify the time period during which your bets were placed, the type of markets on which the bets were placed, and the sport. <br>

                        Betting History is available online for the past 30 days.
                    </p>

                    <div class="pagination-wrap">
                        <ul class="pages">
                            <li id="prev"><a class="disable disable-bg disable-color">Prev</a></li>
                            <li id="pageNumber"><a class="active text-color-yellow">1</a></li>
                            <li id="next"><a class="disable disable-bg disable-color">Next</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">
function getBetHistoryP(val) {
    //document.getElementById("site_bet_loading1").style.display = "block";
    var date_to = $('#date_to').val();
    var date_from = $('#date_from').val();
    var pid = $("#pid").val();
    $.ajax({
        type: "POST",
        url: '{{route("getBetHistoryPL")}}',
        data: {
           "_token": "{{ csrf_token() }}",
            date_from:date_from,
            date_to:date_to,
            val:val,
            pid:pid,
        },
        beforeSend:function(){
            $('#site_bet_loading1').show();
        },
        complete: function(){
            $('#site_bet_loading1').hide();
        },              
        success: function(data) {
            //document.getElementById("site_bet_loading1").style.display = "none";
            $('#bodyData').html(data);
        }
    });
}
$('.period_date1').datepicker({
    dateFormat: "yy-mm-dd"
});
$('.period_date2').datepicker({
    dateFormat: "yy-mm-dd"
});
</script>
@endsection