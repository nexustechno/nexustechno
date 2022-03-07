@extends('layouts.front_layout')
@section('content')
<style>
    body {
        overflow: hidden;
    }
    .fir-col2.wd22{
        width: 25%;
    }
    .fir-col3.pinimg img{
        width: 100%;
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
   /* .todaytitle::before 
    {
        content:'+' ;
        display:block;
        margin-top: 7px;
        margin-right: 5px;
    }
    .todaytitle.collapsed::before
    {
        content:'-' ;
        display:block;
        margin-top: 7px;
        margin-right: 5px;
    }*/
    
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
            @include('layouts.leftpanel')
            <div class="middle-section">
                @if(!empty($settings->user_msg))
                <div class="news-addvertisment black-gradient-bg text-color-white">
                    <h4>News</h4>
                    <marquee scrollamount="3">
                        <a href="#" class="text-color-blue">{{$settings->user_msg}}</a>
                    </marquee>
                </div>
                @endif
                <div class="middle-wraper">
                    <div class="in_play_tabs" id="InplayData">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item" data-id="inplay">
                                <a class="nav-link text-color-blue-1 white-bg inplay active" href="#inplay" role="tab" data-toggle="tab" data-id="inplay" onclick="getInplay('inplay')">Inplay</a>
                            </li>
                            <li class="nav-item" data-id="today">
                                <a class="nav-link text-color-blue-1 white-bg today" href="#today" role="tab" data-toggle="tab" data-id="today" onclick="getInplay('today')">Today</a>
                            </li>
                            <li class="nav-item" data-id="tomorrow">
                                <a class="nav-link text-color-blue-1 white-bg tomorrow" href="#tomorrow" role="tab" data-toggle="tab" data-id="tomorrow" onclick="getInplay('tomorrow')">Tomorrow</a>
                            </li>
                        </ul>
                        
                        <div class="tab-content" id="putInplayData">

                            <div role="tabpanel" class="tab-pane active" id="inplay">
                                
                                <div class="programe-setcricket today_content">
                                    <a class="todaytitle darkblue-bg text-color-white" data-toggle="collapse" href="#inplay-cricket-collapse" role="button" aria-expanded="false" aria-controls="inplay-cricket-collapse">
                                        Cricket 
                                        <!-- remove + , - icon kkk -->
                                        {{--<i class="fas fa-minus float-right" style="margin-top: 7px;margin-right: 5px;"></i>--}}
                                    </a>        
                                    <div class="collapse show" id="inplay-cricket-collapse">
                                        <div class="programe-setcricket">
                                            <div class="firstblock-cricket lightblue-bg1">
                                                <span class="fir-col1"></span>
                                                <span class="fir-col2">1</span>
                                                <span class="fir-col2">X</span>
                                                <span class="fir-col2">2</span>
                                                <span class="fir-col3"></span>
                                            </div>
                                            @if($cricket_html!='')
                                            {!!$cricket_html!!}
                                            @else
                                                No match found.
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="programe-setcricket today_content">
                                    <a class="todaytitle darkblue-bg text-color-white" data-toggle="collapse" href="#inplay-tennis-collapse" role="button" aria-expanded="false" aria-controls="inplay-tennis-collapse">
                                        Tennis 
                                        <!-- remove + , - icon kkk -->
                                        {{--<i class="fas fa-plus float-right" style="margin-top: 7px;margin-right: 5px;"></i>--}}
                                    </a> 
                                    <!-- below class adding show to default open tab  kkkkk-->       
                                    <div class="collapse show" id="inplay-tennis-collapse">
                                        <div class="programe-setcricket">
                                            <div class="firstblock-cricket lightblue-bg1">
                                                <span class="fir-col1"></span>
                                                <span class="fir-col2">1</span>
                                                <span class="fir-col2">X</span>
                                                <span class="fir-col2">2</span>
                                                <span class="fir-col3"></span>
                                            </div>
                                            @if($tennis_html!='')
                                            {!! $tennis_html !!}
                                            @else
                                                No match found.
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="programe-setcricket today_content">
                                    <a class="todaytitle darkblue-bg text-color-white" data-toggle="collapse" href="#inplay-soccer-collapse" role="button" aria-expanded="false" aria-controls="inplay-soccer-collapse">
                                        Soccer 
                                        <!-- remove + , - icon kkk -->
                                        {{--<i class="fas fa-plus float-right" style="margin-top: 7px;margin-right: 5px;"></i>--}}
                                    </a>  
                                    <!-- below class adding show to default open tab  kkkkk-->      
                                    <div class="collapse show" id="inplay-soccer-collapse">
                                        <div class="programe-setcricket">
                                            <div class="firstblock-cricket lightblue-bg1">
                                                <span class="fir-col1"></span>
                                                <span class="fir-col2">1</span>
                                                <span class="fir-col2">X</span>
                                                <span class="fir-col2">2</span>
                                                <span class="fir-col3"></span>
                                            </div>
                                            @if($soccer_html!='')
                                            {!! $soccer_html !!}
                                            @else
                                                No match found.
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div role="tabpanel" class="tab-pane " id="today">
                                
                                <div class="programe-setcricket today_content">
                                    <a class="todaytitle darkblue-bg text-color-white" data-toggle="collapse" href="#today-cricket-collapse" role="button" aria-expanded="false" aria-controls="today-cricket-collapse">
                                        Cricket 
                                        <!-- remove + , - icon kkk -->
                                        {{--<i class="fas fa-minus float-right" style="margin-top: 7px;margin-right: 5px;"></i>--}}
                                    </a>        
                                    <div class="collapse show" id="today-cricket-collapse">
                                        <div class="programe-setcricket" id="today-cricket">
                                        </div>
                                    </div>
                                </div>

                                <div class="programe-setcricket today_content">
                                    <a class="todaytitle darkblue-bg text-color-white" data-toggle="collapse" href="#today-tennis-collapse" role="button" aria-expanded="false" aria-controls="today-tennis-collapse">
                                        Soccer
                                        <!-- remove + , - icon kkk --> 
                                        {{--<i class="fas fa-plus float-right" style="margin-top: 7px;margin-right: 5px;"></i>--}}
                                    </a>
                                    <!-- below class adding show to default open tab  kkkkk-->           
                                    <div class="collapse show" id="today-tennis-collapse">
                                        <div class="programe-setcricket" id="today-tennis">
                                        </div>
                                    </div>
                                </div>

                                <div class="programe-setcricket today_content">
                                    <a class="todaytitle darkblue-bg text-color-white" data-toggle="collapse" href="#today-soccer-collapse" role="button" aria-expanded="false" aria-controls="today-soccer-collapse">
                                        Tennis 
                                        <!-- remove + , - icon kkk -->
                                        {{--<i class="fas fa-plus float-right" style="margin-top: 7px;margin-right: 5px;"></i>--}}
                                    </a>  
                                    <!-- below class adding show to default open tab  kkkkk-->         
                                    <div class="collapse show" id="today-soccer-collapse">
                                        <div class="programe-setcricket" id="today-soccer">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div role="tabpanel" class="tab-pane" id="tomorrow">
                                <div class="programe-setcricket today_content">
                                    <a class="todaytitle darkblue-bg text-color-white" data-toggle="collapse" href="#tmr-cricket-collapse" role="button" aria-expanded="false" aria-controls="tmr-cricket-collapse">
                                        Cricket 
                                        <!-- remove + , - icon kkk -->
                                        {{--<i class="fas fa-minus float-right" style="margin-top: 7px;margin-right: 5px;"></i>--}}
                                    </a>  
                                    <!-- below class adding show to default open tab  kkkkk-->         
                                    <div class="collapse show" id="tmr-cricket-collapse">
                                        <div class="programe-setcricket" id="tmr-cricket">
                                        </div>
                                    </div>
                                </div>

                                <div class="programe-setcricket today_content">
                                    <a class="todaytitle darkblue-bg text-color-white" data-toggle="collapse" href="#tmr-tennis-collapse" role="button" aria-expanded="false" aria-controls="tmr-tennis-collapse">
                                        Soccer 
                                        <!-- remove + , - icon kkk -->
                                        {{--<i class="fas fa-plus float-right" style="margin-top: 7px;margin-right: 5px;"></i>--}}
                                    </a> 
                                    <!-- below class adding show to default open tab  kkkkk-->          
                                    <div class="collapse show" id="tmr-tennis-collapse">
                                        <div class="programe-setcricket" id="tmr-tennis">
                                        </div>
                                    </div>
                                </div>

                                <div class="programe-setcricket today_content">
                                    <a class="todaytitle darkblue-bg text-color-white" data-toggle="collapse" href="#tmr-soccer-collapse" role="button" aria-expanded="false" aria-controls="tmr-soccer-collapse">
                                       Tennis  
                                        <!-- remove + , - icon kkk -->
                                        {{--<i class="fas fa-plus float-right" style="margin-top: 7px;margin-right: 5px;"></i>--}}
                                    </a> 
                                    <!-- below class adding show to default open tab  kkkkk-->          
                                    <div class="collapse show" id="tmr-soccer-collapse">
                                        <div class="programe-setcricket" id="tmr-soccer">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @include('layouts.rightpanel')
        </div>
    </div>
</section>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>

<script type="text/javascript">
function getInplay(val)
{
    $.ajax({
        type: "post",
        url: '{{route("getInplaydata")}}',
        data: {"_token": "{{ csrf_token() }}","val":val},
        beforeSend:function(){
            $('#site_bet_loading1').show();
        },
        complete: function(){
            $('#site_bet_loading1').hide();
        },
        success: function(data){

            var dt=data.split("~~");
            if(dt[3] == 'today')
            {
                $("#today-cricket").html(dt[0]);
                $("#today-soccer").html(dt[1]);
                $("#today-tennis").html(dt[2]);
            }  
            if(dt[3] == 'tomorrow')
            {
                $("#tmr-cricket").html(dt[0]);
                $("#tmr-soccer").html(dt[1]);
                $("#tmr-tennis").html(dt[2]);
            }
        }
    });
}
</script>
<script type="text/javascript">
$(document).ready(function(){
    setInterval(function(){
        var _token = $("input[name='_token']").val();

        $.ajax({
            type: "POST",
            url: '{{route("getmatchdetailsOfInplay")}}',
            data: {_token:_token},
            /*beforeSend:function(){
                $('#site_bet_loading1').show();
            },
            complete: function(){
                $('#site_bet_loading1').hide();
            },*/
            success: function(data){
                //alert(data);
                var dt=data.split("~~");
                var i=0;
                for(i=0;i<dt.length;i++)
                {
                    if(i==0)
                        $("#inplay-cricket-collapse").html(dt[i]);
                    else if(i==2)
                        $("#inplay-soccer-collapse").html(dt[i]);
                    else if(i==1)
                        $("#inplay-tennis-collapse").html(dt[i]);   
                }
            }
        });

        $.ajax({
            type: "post",
            url: '{{route("getInplayToday")}}',
            data: {"_token": "{{ csrf_token() }}"},
            /*beforeSend:function(){
                $('#site_bet_loading1').show();
            },
            complete: function(){
                $('#site_bet_loading1').hide();
            },*/
            success: function(data){
                var dt=data.split("~~");
                $("#today-cricket").html(dt[0]);
                $("#today-soccer").html(dt[1]);
                $("#today-tennis").html(dt[2]);
            }
        });

        $.ajax({
            type: "post",
            url: '{{route("getInplayTomrw")}}',
            data: {"_token": "{{ csrf_token() }}"},
            /*beforeSend:function(){
                $('#site_bet_loading1').show();
            },
            complete: function(){
                $('#site_bet_loading1').hide();
            },*/
            success: function(data){
                var dt=data.split("~~");
                $("#tmr-cricket").html(dt[0]);
                $("#tmr-soccer").html(dt[1]);
                $("#tmr-tennis").html(dt[2]);
            }
        });

    },10000);
});
$('.todaytitle').click(function() {
    $(this).find('i').toggleClass('fas fa-plus fas fa-minus')
});
</script>
@endsection