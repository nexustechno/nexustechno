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

<section class="profit-section section-mlr">
    <div class="container">
        <div class="inner-title">
            <h2>Bet List</h2>
        </div>
	    <form method="post">
            <div class="multiple-radiobtn">
                <label for="radio1">
                    <input type="radio" name="radio" id="radio1" value="all" checked> All
                </label>
                @foreach($sports as $sport)
                <label for="radio2">
                    <input type="radio" name="radio" id="radio2" value="{{$sport->sId}}"> {{strtoupper($sport->sport_name)}}
                </label>
                @endforeach
            </div>
	
            <div class="timeblock light-grey-bg-2">
                <div class="timeblock-box">
                    <span>Bet Status:</span>
                    <select name="" id="" class="form-control">
                        <option>Settled</option>
                        <option>Voided</option>	                   
                    </select>
                    <div class="datebox">
                        <span>Period</span>
                        <div class="datediv1">
                            <div class="datediv">
                                <input type="text" name="date_from" id="date_from" class="form-control period_date5b" placeholder="{{date('d-m-Y')}}" value="{{date('d-m-Y')}}">
                                <img src="{{ URL::to('asset/img/calendar-icon.png')}}" class="calendar-icon" >
                            </div>
                            <input type="text" name="" id="" placeholder="09:00" maxlength="5" class="form-control disable" disabled>
                        
                            <div class="datediv">
                                <input type="text" name="date_to" id="date_to" class="form-control period_date6b" placeholder="{{Date('d-m-Y', strtotime('+1 days'))}}" value="{{Date('d-m-Y', strtotime('+1 days'))}}">
                                <img src="{{ URL::to('asset/img/calendar-icon.png')}}" class="calendar-icon">
                            </div>
                            <input type="text" name="" id="" placeholder="08:59" maxlength="5" class="form-control disable" disabled>
                        </div>
                    </div>
                </div>
                <div class="timeblock-box">
                    <ul>
                        <li> <a class="justbtn grey-gradient-bg text-color-black1"  onclick="getHistory('today')"> Just For Today </a> </li>
                        <li> <a class="submit-btn text-color-yellow" onclick="getHistory('history')"> Get History </a> </li>
                    </ul>
                </div>
            </div>
        </form>
        <p>
            Bet List enables you to review the bets you have placed. <br>
            Specify the time period during which your bets were placed, the type of markets on which the bets were placed, and the sport. <br>
            Bet List is available online for the past 30 days.
        </p>
        <div class="maintable-raju-block betlist-block" id="append_data">
        </div>
    </div>
</section>
<script type="text/javascript">
var _token = $("input[name='_token']").val();
function getHistory(val) {
    
    //document.getElementById("site_bet_loading1").style.display = "block";
	var sport = $('input[name="radio"]:checked').val();
	var date_to = $('#date_to').val();
	var date_from = $('#date_from').val();
    /*alert(date_from);
    alert(date_to);*/
	$.ajax({
        type: "POST",
        url: '{{route("getHistory")}}',
        data: {
            _token: _token,
            sport:sport,
            date_from:date_from,
            date_to:date_to,
            val:val,

        },  
        beforeSend:function(){
            $('#site_bet_loading1').show();
        },
        complete: function(){
            $('#site_bet_loading1').hide();
        },             
        success: function(data) {
            //document.getElementById("site_bet_loading1").style.display = "none";
        	$('#append_data').html(data.html);
        }
    });
}
</script>
@endsection