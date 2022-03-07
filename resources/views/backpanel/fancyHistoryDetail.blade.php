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
      <h2>Fancy History</h2>
    </div>

    <div class="fancy-history-details">
      <table class="table custom-table white-bg text-color-blue-2 fancy_tablenew">
        <thead>
          <tr>
            <th class="white-bg text-left">Sr.No.</th>
            <th class="white-bg text-left">Fancy Name</th>
            <th class="white-bg text-center">Result</th>
            <th class="white-bg text-center">Action</th>
            <th class="white-bg text-center">Bet</th>
          </tr>
        </thead>

        <tbody id="appendBF">
          <?php $count=1; ?>
          @foreach($fancyResult as $fancyResults)
          <tr class="white-bg">
            <td class="text-center">{{$count}}</td>
            <td class="text-left">{{$fancyResults->fancy_name}}</td>
            <td class="text-center">{{$fancyResults->result}} </td>
            <td class="text-center"> <a href="javascript:void(0);" onclick="resultrollback('<?php echo $fancyResults->id; ?>');" class="text-color-blue-light">Result Rollback</a></td>
            <td class="text-center"> <a href="{{route('fancyuser',$fancyResults->id)}}" class="text-color-blue-light">Bet</a></td>
          </tr> 
            <?php $count++; ?>
          @endforeach  
        </tbody>
      </table>
    </div>
  </div>
</section>

<!-- ss comment -->
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script> -->
<script type="text/javascript">
  var _token = $("input[name='_token']").val();
  function resultrollback(val){
      if(!confirm('Are you Sure RollBack Result?')){
        return false;
      }
    $.ajax({
      url: "{{route('resultRollback')}}",
      type: "POST",
      data: {
          _token:_token,
          id:val,         
      },
      beforeSend:function(){
        $('#site_bet_loading1').show();
      },
      complete: function(){
        $('#site_bet_loading1').hide();
      },     
      success: function(data){
        if(data.success=='success'){
          
          toastr.success('RollBack Successfully!');   
		  setTimeout(function () {
		   location.reload(true);
		  }, 2000);      
        }
      },
    });
  }
</script>
@endsection