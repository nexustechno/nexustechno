@extends('layouts.front_layout')
@section('content')
<?php
use App\setting;

$settings =setting::first();
?>
<style>
    body {
        overflow: hidden;
    }
</style>

<section>
    <div class="container-fluid">
        <div class="main-wrapper">
           @include('layouts.leftpanel')
            <div class="middle-section">
                @if(!empty($getUser))
                    @if(!empty($settings->user_msg))
                    <div class="news-addvertisment black-gradient-bg text-color-white">
                        <h4>News</h4>
                        <marquee>
                            <a href="#" class="text-color-blue">{{$settings->user_msg}}</a>
                        </marquee>
                    </div>
                    @endif
                @endif

                <div class="middle-wraper">
                    <div class="cricket-bg">
                        <img src="{{ URL::to('asset/front/img/cricket.jpg') }}">
                    </div>
                    <div class="mobileslide_menu yellow-gradient-bg3 d-lg-none">
                        <a class="res_search black-gradient-bg4"><img src="{{ URL::to('asset/front/img/search.svg') }}" alt=""></a>
                        <div class="mslide_nav">
                            <ul>
                                <li class="menu_casino">
                                    <span class="tagnew">New</span>
                                    <a href="{{route('casino')}}" class="text-color-white purple-blue-gradient-bg">
                                        <img src="{{ URL::to('asset/front/img/mslide_menu/mslide-icon-casino.svg') }}" alt=""> Casino
                                    </a>
                                </li>
                                <li class="active">
                                    <span class="highlight-red grey-gradient-bg"> <span class="text-color-white red-gradient-bg cricketCount"></span> </span>
                                    <a href="{{route('cricket')}}" class="text-color-black1">
                                        <img src="{{ URL::to('asset/front/img/mslide_menu/mslide-icon-cricket-black.svg') }}" alt="" class="dactimg">
                                        <img src="{{ URL::to('asset/front/img/mslide_menu/mslide-icon-cricket-yellow.svg') }}" alt="" class="actimg">
                                        Cricket
                                    </a>
                                </li>
                                <li>
                                    <span class="highlight-red grey-gradient-bg"> <span class="text-color-white red-gradient-bg soccerCount"></span> </span>
                                    <a href="{{route('soccer')}}" class="text-color-black1">
                                        <img src="{{ URL::to('asset/front/img/mslide_menu/mslide-icon-soccer-black.svg') }}" alt="" class="dactimg">
                                        <img src="{{ URL::to('asset/front/img/mslide_menu/mslide-icon-soccer-yellow.svg') }}" alt="" class="actimg">
                                        Soccer
                                    </a>
                                </li>
                                <li>
                                    <span class="highlight-red grey-gradient-bg"> <span class="text-color-white red-gradient-bg tennisCount"></span> </span>
                                    <a href="{{route('tennis')}}" class="text-color-black1">
                                        <img src="{{ URL::to('asset/front/img/mslide_menu/mslide-icon-tennis-black.svg') }}" alt="" class="dactimg">
                                        <img src="{{ URL::to('asset/front/img/mslide_menu/mslide-icon-tennis-yellow.svg') }}" alt="" class="actimg">
                                        Tennis
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="search_wrapm d-lg-none" id="searchWrap">
                        <div class="searwrp_popup white-bg">
                            <form action="">
                                <a id="serback" class="search_backm">
                                    <img src="{{ URL::to('asset/front/img/mslide_menu/search-back.svg') }}" alt="">
                                </a>
                                <input type="text" name="" id="" placeholder="Search Events" class="form-control" autocomplete="off" autocapitalize="off" autocorrect="off">
                                <button id="searchcelar" type="reset" class="btnsearch clearm">
                                    <img src="{{ URL::to('asset/front/img/mslide_menu/close-icon.svg') }}" alt="">
                                </button>
                                <button id="search" type="submit" class="btnsearch serachm">
                                    <img src="{{ URL::to('asset/front/img/mslide_menu/search-black.svg') }}" alt="">
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="maintabs cricket-section">
                        <h3 class="yellow-bg2 text-color-black2 highligths-txt">Highlights</h3>
                        <div class="programe-setcricket">
                            <div class="firstblock-cricket lightblue-bg1">
                                <span class="fir-col1"></span>
                                <span class="fir-col2">1</span>
                                <span class="fir-col2">X</span>
                                <span class="fir-col2">2</span>
                                <span class="fir-col3"></span>
                            </div>
                            {!! $cricket_html !!}
                        </div>

                        @include('front.social-footer-links')

                    </div>
                </div>
            </div>
            @include('layouts.rightpanel')
        </div>
    </div>
</section>
<input type="hidden" name="url" id="url" value="ws://3.109.100.146:8000/oddslist" />
{{--<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>--}}
@include('front.common-script-for-list')


<script>
    function loadCricketData(){
        var _token = $("input[name='_token']").val();
        $.ajax({
            type: "POST",
            url: '{{route("getmatchdetailsOfCricket")}}',
            data: {_token:_token},
            beforeSend:function(){
                $('#site_statistics_loading').show();
            },
            complete: function(){
                $('#site_statistics_loading').hide();
            },
            success: function(data){
                $(".programe-setcricket").html(data);
            }
        });
    }

$(document).ready(function(){

    // loadCricketData();

    if(getUser!='') {
    	setInterval(function(){
            loadCricketData();
    	},1000);
    }
});
</script>
<script src="{{asset('asset/front/js/signalr.min.js')}}"></script>
<script>
var closing_rate='';
var connection=null;
var obj='';

function init()
{
	//console.log('tickaaaa');
	var url = $('#url').val();

	localStorage.setItem('url', url);

	connection = new signalR.HubConnectionBuilder()
    .withUrl(url)
	.configureLogging(signalR.LogLevel.Trace)
    .build();
	var allhtml='';

	// override update handler
    connection.on('tickEvent', function (quote)
	{
		var tick = quote.tick;
        /*var id = tick.symbol.replace(/[^a-zA-Z0-9]/g, "_");
		//console.log(id);
        if ($('#' + id).length === 0)
		{
			var div='#livequotetag'+$('#currenttab').val();
			//console.log(div);
            //$(div).append('<tr id="' + id + '"></tr>');
       	}
		var date = new Date(tick.time * 1000 + new Date().getTimezoneOffset() * 60 * 1000);
        var $el = $('#' + id);


		var exch=$('#exchange_name').val();
		var ask=tick.ask;
		var bid= tick.bid;
		var ind=ask.toString().indexOf(".");
		var strchar=ask.toString().substring(ind);
		var substr_=strchar.substr(1);
		var spreadLen=substr_.length;

		//calculate percentage change
		var sym_close_rate=obj[id];
		var bid=tick.bid;
		var incr=parseFloat(bid)-parseFloat(sym_close_rate);
		var incr_per=parseFloat(parseFloat(incr)/parseFloat(bid))*parseFloat(100);

		var changecolor='';
		var img='';
		if(incr_per<0)
		{
			incr_per=parseFloat(incr_per)*parseFloat(-1);
			changecolor='style="color:#ee5454"';
			img="imgs/landing/down-arrow.png";
		}
		else
		{
			changecolor='style="color:#4bc196"';
			img="imgs/landing/up-arrow.png";
		}
		var incr_per=incr_per.toFixed(3);

		var total_digits=$('#' + id).data("minfluctuation");
		var digit_place=$('#' + id).data("digit");

		var bid_val=tick.bid;
		var ask_val=tick.ask;

		if(total_digits!='' && total_digits>0){
			bid_val=bid_val.toFixed(digit_place);
			//console.log(bid_val);
		}
		if(total_digits!='' && total_digits>0){
			ask_val=ask_val.toFixed(digit_place);
		}
		//console.log(id+'---'+digit_place+'----'+bid_val);
		$el.html('<td class="f-pop-reg"><a href="trade/'+exch+'/'+tick.symbol+'">' + tick.symbol + '</a></td><td class="f-pop-bol"><span>'+spread+'</span></td><td class="f-pop-reg" '+changecolor+'>' + bid_val+'/'+ask_val+ '</td><td '+changecolor+'><img width="9" height="9" class="img-fluid mr-2" src="'+img+'" alt="">' + incr_per +'%</td>');*/

	});

	//start
	connection
	.start()
	.then(function () {
		var $status = $('#status');
		$status.removeClass('alert-warning');
		$status.addClass('alert-success');
		$("#status").html("Connected");

		var delay = 1000;
		setTimeout(function() {
			$('#ticksSubscribe').click();
		}, delay);
	})
	.catch(err => console.error(err.toString()));
	}

	$(function () {
		$("#url").val(localStorage.getItem('url'));

		$("#clearTicks").click(function(ev) {
			$("#quotes").empty()
		});

		$("#ticksUnsubscribe").click(function(evt) {
			evt.preventDefault();
			connection.invoke('ticksSubscription',false, null);
			$("#ticksStatus").text('Unsubscribed');
		});
	});
	$(document).ready(function(){
		// init();
	});
	$("#ticksSubscribe").click(function(e){
		e.preventDefault();
  		connection.invoke('ticksSubscription',true, $("#tickMask").val());
		$("#ticksStatus").text('Subscribed');
	});
	/* end for websocket programming */
</script>
@endsection

@push('third_party_scripts')

    <script src="https://cdn.socket.io/socket.io-1.4.5.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>
@endpush

@push('page_scripts')

    <script type="text/javascript">
        // var i = 0;
        // window.Echo.channel('matches')
        //     .listen('.cricket', (data) => {
        //         i++;
        //         $("#broadcast").append('<div class="alert alert-success">'+i+'.'+data+'</div>');
        //         console.log(data)
        //     });
    </script>
@endpush
