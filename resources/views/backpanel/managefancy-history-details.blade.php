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

<section>
  <div class="container">
    <div class="inner-title">
      <h2>Manage Fancy Detail</h2>
    </div>

    <div class="fancy-history-details">
      <table class="table custom-table white-bg text-color-blue-2 fancy_tablenew">
        <thead>
          <tr>
            <th class="white-bg text-left">Sr.No.</th>
            <th class="white-bg text-left">Fancy Name</th>
            <th class="white-bg text-center">Declare Run</th>
            <th class="white-bg text-center">Action</th>
          </tr>
        </thead>

        <tbody id="appendBF">
        </tbody>
      </table>
    </div>
  </div>
</section>


<script type="text/javascript">
var _token = $("input[name='_token']").val();
$(document).ready(function() {
	$.ajax({
    type: "POST",
    url: '{{route("getFancy",$match->id)}}',
    data: {
        _token: _token,                   
    },
    beforeSend:function(){
      $('#site_bet_loading1').show();
    },
    complete: function(){
      $('#site_bet_loading1').hide();
    }, 
    success: function(data) {
        $("#appendBF").html(data);
    }
  });
});

function resultDeclare(val){
  if(!confirm('Are you Sure?')){
    return false;
  }
  var fancyname = $(val).data('fancy');
  var match_id = $(val).data('match');
  var eventid = $(val).data('eventid');
  var betId = $(val).data('betid');
  var aa = $(val).data('fancyre');
  var fancy_result = $('#fancy_result'+aa).val();
  $.ajax({
    url: "{{route('resultDeclare')}}",
    type: "POST",
    data: {
      _token:_token,
      fancyname:fancyname,
      fancy_result:fancy_result,
      match_id:match_id,
      betId:betId,
      eventid:eventid,
    }, 
    beforeSend:function(){
      $('#site_bet_loading1').show();
    },
    complete: function(){
      $('#site_bet_loading1').hide();
    },     
    success: function(response){ 
      toastr.success('Result declare successfully!');  
     
      setTimeout(function () {
       location.reload(true);
      }, 3000);          
    },
  });
}

function resultDeclarecancel(val){
  if(!confirm('Are you Sure?')){
    return false;
  }
  var _token = $("input[name='_token']").val();
  var fancyname = $(val).data('fancy');
  var match_id = $(val).data('match');
  var betId = $(val).data('betid');
  var eventid = $(val).data('eventid');
  $.ajax({
    url: "{{route('resultDeclarecancel')}}",
    type: "POST",
    data: {
        _token:_token,   
        fancyname:fancyname,
        match_id:match_id,
        betId:betId,
        eventid:eventid,
    }, 
    beforeSend:function(){
      $('#site_bet_loading1').show();
    },
    complete: function(){
      $('#site_bet_loading1').hide();
    },     
    success: function(response){
      toastr.success('Result declare successfully!');
     // location.reload();   

    },
  });
}
</script>
@endsection