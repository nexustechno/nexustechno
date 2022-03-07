@extends('layouts.app')
@section('content')
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
        @if($errors->any())
        <h4>{{$errors->first()}}</h4>
        @endif
        <div class="inner-title-2 text-color-blue-2">
            <h2>Risk Management Summary</h2>
        </div>
        <div></div>  
    
        <div class="maintable-raju-block risk-accordion" id="loaddata">
      		@php $cnt=0; $collptrue=''; @endphp
      		@foreach($sports as $sport)

            <?php
            if($cnt==0){
            $collptrue='true';
            }else{
            $collptrue='false';
            }
             ?>
            
            
           
            <div class="panel panel-default">
                <div class="panel-heading black-bg-rgb" role="tab" id="headingOne">
                    <h2 class="panel-title">
                        <a class="text-color-white" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne{{$cnt}}" aria-expanded="{{$collptrue}}" aria-controls="collapseOne{{$cnt}}">
                            {{$sport->sport_name}}
                        </a>
                    </h2>
                </div>
                <!-- @if($cnt==0) show @endif hide show collapse condition remove  kkk-->
                <div id="collapseOne{{$cnt}}" class="panel-collapse collapse show " role="tabpanel" aria-labelledby="headingOne">
                </div>
            </div>
            <?php $cnt++; ?>
      		@endforeach
        </div>
    </div>
</section>


<script type="text/javascript">
$(document).ready(function(){
    var gettab =  $('.gettab1').attr("data-id");
    $("."+gettab).addClass("active");
    getriskdetail();
    setInterval(function(){
      getriskdetailTwo();
    },1000);

    function getriskdetail(){
          var _token = $("input[name='_token']").val();
            $.ajax({
            type: "POST",
            url: '{{route("getriskdetails")}}',
            data: {_token:_token},
            beforeSend:function(){
                $('#site_bet_loading1').show();
            },
            complete: function(){
                $('#site_bet_loading1').hide();
            },
            success: function(data){
                //$("#loaddata").html(data);
                var dt=data.split('~~');
                var i=0;
                for(i=0;i<dt.length;i++)
                {
                    if(dt[i]!='')
                        $('#collapseOne'+i).html(dt[i]);
                    else
                        $('#collapseOne'+i).html('<div class="panel panel-default panel_content beige-bg-1"><h6>No match found.</h6></div>');
                }
            }
        });
    }

    function getriskdetailTwo(){
          var _token = $("input[name='_token']").val();
            $.ajax({
            type: "POST",
            url: '{{route("getriskdetailTwo")}}',
            data: {_token:_token},
            /*beforeSend:function(){
                $('#site_bet_loading1').show();
            },
            complete: function(){
                $('#site_bet_loading1').hide();
            },*/
            success: function(data){
                //$("#loaddata").html(data);
                var dt=data.split('~~');
                var i=0;
                for(i=0;i<dt.length;i++)
                {
                    if(dt[i]!='')
                        $('#collapseOne'+i).html(dt[i]);
                    else
                        $('#collapseOne'+i).html('<div class="panel panel-default panel_content beige-bg-1"><h6>No match found.</h6></div>');
                }
            }
        });

    }
});
</script>
@endsection