@extends('layouts.app')
@section('content')

<?php
$getUser = Auth::user();
?>
 <!-- <script type="text/javascript">
    $(document).ajaxStart(function() {
    $(".betloaderimage1").show();  
    }).ajaxStop(function() {
    $(".betloaderimage1").hide();
    });
     </script> -->
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
<!--<div id="site_bet_loading1" class="betloaderimage1 loader-style1" style="display: none;"></div>-->


<section class="profit-section section-mlr">
    <div class="container">
        <div class="inner-title">
            <h2>Profit/Loss Report by Downline</h2>
        </div>
        <div class="timeblock light-grey-bg-2">
            <div class="timeblock-box">
                <span>Time Zone</span>
                <select name="" id="" class="form-control timezone-select">
                    <option>IST(Bangalore / Bombay / New Delhi) (GMT+5:30)</option>
                </select>
                <div class="datebox">
                    <span>Period</span>
                    <div class="datediv1">
                        <div class="datediv">
                            <input type="text" name="date_from" id="date_from" class="form-control period_date1" placeholder="{{date('d-m-Y')}}" value="{{date('d-m-Y')}}">
                            <img src="{{ URL::to('asset/img/calendar-icon.png')}}" class="calendar-icon">
                        </div>
                         <input type="text" name="" id="" placeholder="09:00" maxlength="5" class="form-control disable" disabled>                  
                        <div class="datediv">
                            <input type="text" name="date_to" id="date_to" class="form-control period_date2" placeholder="{{Date('d-m-Y', strtotime('+1 days'))}}" value="{{Date('d-m-Y', strtotime('+1 days'))}}">
                            <img src="{{ URL::to('asset/img/calendar-icon.png')}}" class="calendar-icon">
                        </div>
                        <input type="text" name="" id="" placeholder="08:59" maxlength="5" class="form-control disable" disabled>
                    </div>
                </div>
            </div>
            <div class="timeblock-box">
                <ul>
                    {{--<li> <a class="justbtn grey-gradient-bg text-color-black1" onclick="getHistoryPL('today')"> Just For Today </a> </li>
                    <li> <a class="justbtn grey-gradient-bg text-color-black1" onclick="getHistoryPL('yesterday')"> From Yesterday </a> </li>--}}
                    <li> <a class="submit-btn text-color-yellow"  id="buttonpl" onclick="getHistoryPL('historypl')"> Get P & L </a> </li>
                </ul>
            </div>
        </div>
       
        <div class="maintable-raju-block" id="downline-table">
            <div class="name-div">
                <div class="name-block light-grey-bg-3">
                    <ul class="agentlist" style="border:none;">
                        <li class="agentlistadmin" id="{{$getUser->id}}"></li>        
                    </ul>
                </div>
            </div>
            <table class="table custom-table white-bg text-color-blue-2">
                <thead>
                    <tr>
                        <th class="light-grey-bg">UID</th>
                        <th class="light-grey-bg">Player P/L</th>
                        <th class="light-grey-bg">Downline P/L</th>
                        <th class="light-grey-bg">MA PT</th>
                        <th class="light-grey-bg">MA Comm.</th>
                        <th class="light-grey-bg">MA Rebate</th>
                        <th class="light-grey-bg">MA Total</th>
                        <th class="light-grey-bg">Upline P/L</th>
                    </tr>
                </thead>

                <tbody id="bodyData">
                </tbody>
                <tbody id="totallist"></tbody>
            </table>
        </div>
    </div>
</section>



<script type="text/javascript">
    var _token = $("input[name='_token']").val();
    function getHistoryPL(val)
    {
        //document.getElementById("site_bet_loading1").style.display = "block";
        var date_to = $('#date_to').val();
        var date_from = $('#date_from').val();

        //console.log(date_from +"***"+ date_to);
        $.ajax({
            type: "POST",
            url: '{{route("getHistoryPL")}}',
            data: {
               "_token": "{{ csrf_token() }}",
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
                var spl=data.split('~~');
                $('#bodyData').html(spl[0]);
                $("#totallist").html(spl[1]);
            }
        });
    }
    
    function subpagedata(val){
        var user_id = val;
        var date_to = $('#date_to').val();
        var date_from = $('#date_from').val();
        $.ajax({
            type: "post",
            url: '{{route("SubDetail")}}',
            data: {"_token": "{{ csrf_token() }}","user_id":user_id,"date_to":date_to,"date_from":date_from},
            beforeSend:function(){
                $('#site_bet_loading1').show();
            },
            complete: function(){
                $('#site_bet_loading1').hide();
            }, 
            success: function(data){
                var spl=data.split('~~');
                $('#bodyData').html(spl[0]);
                $(".agentlistadmin").html('<a href="{{route('profitloss-downline')}}"><span class="blue-bg text-color-white">{{$getUser->agent_level}}</span><strong id="{{$getUser->id}}" >{{$getUser->user_name}}</strong></a> <img src="/asset/img/arrow-right2.png">');
                $(".agentlist").append(spl[1]);
                $("#totallist").html(spl[2]);
            }
        });
    }

    function backpagedata(val){
        var user_id = val;
        var date_to = $('#date_to').val();
        var date_from = $('#date_from').val();
        $.ajax({
            type: "post",
            url: '{{route("SubBackDetail")}}',
            data: {"_token": "{{ csrf_token() }}","user_id":user_id,"date_to":date_to,"date_from":date_from},
            beforeSend:function(){
                $('#site_bet_loading1').show();
            },
            complete: function(){
                $('#site_bet_loading1').hide();
            }, 
            success: function(data){
                var spl=data.split('~~');
                $('#bodyData').html(spl[0]);
                $(".agentlistadmin").html('<a href="{{route('profitloss-downline')}}"><span class="blue-bg text-color-white">{{$getUser->agent_level}}</span><strong id="{{$getUser->id}}" >{{$getUser->user_name}}</strong></a> <img src="/asset/img/arrow-right2.png">');
                $(".agentlist").html(spl[1]);
                $("#totallist").html(spl[2]);
            }

        });
    }
</script>
@endsection