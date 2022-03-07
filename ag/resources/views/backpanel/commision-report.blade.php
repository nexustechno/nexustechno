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
        <div class="inner-title">
            <h2>Commission Report </h2>
        </div>
        <div class="timeblock light-grey-bg-2">
            <div class="timeblock-box">                
                <div class="datebox">
                    <span>Selecte From Date:</span>
                    <div class="datediv1">
                        <div class="datediv">
                            <input name="fromdate" id="fromdate" class="form-control period_date1" type="text" autocomplete="off" placeholder="{{date('d-m-Y')}}" value="{{date('d-m-Y')}}">
                            <img src="{{ URL::to('asset/img/calendar-icon.png')}}" class="calendar-icon">
                        </div>                                   
                       
                    </div>

                    <span>Select To Date:</span>
                    <div class="datediv1">
                        <div class="datediv">
                           <input name="todate" id="todate" class="form-control period_date2" type="text" readonly="" autocomplete="off" placeholder="{{Date('d-m-Y', strtotime('+1 days'))}}" value="{{Date('d-m-Y', strtotime('+1 days'))}}">
                            <img src="{{ URL::to('asset/img/calendar-icon.png')}}" class="calendar-icon">
                        </div>                                   
                       
                    </div>

                     <span>User : </span>
                    <div class="datediv1">
                        <div class="datediv">
                          <select name="userName" id="userName" class="form-control acc-filter">
                                <option value=""> All </option>
                                @foreach($user as $users)
                                <option value="{{$users->id}}">{{$users->user_name}}</option>
                                @endforeach
                                
                            </select>
                        </div>                                   
                       
                    </div>

                </div>
            </div>
            <div class="timeblock-box">
                <ul>
                    {{--<li> <a class="justbtn grey-gradient-bg text-color-black1" onclick="getHistoryPL('today')"> Just For Today </a> </li>
                    <li> <a class="justbtn grey-gradient-bg text-color-black1" onclick="getHistoryPL('yesterday')"> From Yesterday </a> </li>--}}
                    <li><input id="acntbtn" type="button" value="Submit" name="acntbtn" class="submit-btn text-color-yellow" onclick="getCommissionReport()"> </li>
                </ul>
            </div>
        </div>

        <div class="maintable-raju-block" id="downline-table">
            <div class="name-div">
                <div class="name-block light-grey-bg-3">
                    <ul class="agentlist" style="border:none;">
                        <li class="agentlistadmin" id=""></li>        
                    </ul>
                </div>
            </div>
            <table class="table custom-table white-bg text-color-blue-2">
                <table class="table custom-table white-bg text-color-blue-2" id="pager">
                        <thead>
                            <tr>
                                <th class="light-grey-bg">Sr. No</th>
                                <th class="light-grey-bg">User Name</th>
                                <th class="light-grey-bg">Com. Presentage</th>
                                <th class="light-grey-bg">Commission</th>
                            </tr>
                        </thead>
                        <tbody id="append_data">
                        </tbody>
                    </table>

                <tbody id="bodyData">
                </tbody>
                <tbody id="totallist"></tbody>
            </table>
             You Can Got Only Max 15 Days Report
        </div>
    </div>
</section>

<div class="modal credit-modal showForm" id="mycomreport">
    <div class="modal-dialog">
        <div class="modal-content white-bg">
            <div class="modal-header border-0">
                <h4 class="modal-title text-color-blue-1 user_name">(Pankaj)</h4>
                <button type="button" class="close" data-dismiss="modal"><img src="{{ URL::to('asset/img/close-icon.png')}}"></button>
            </div>
            <div class="modal-body">
                <table class="table custom-table white-bg text-color-blue-2">
                    <thead>
                        <tr>
                            <th class="light-grey-bg">Sr. No</th>
                            <th class="light-grey-bg">Match Name</th>
                            <th class="light-grey-bg">User Profit</th>
                            <th class="light-grey-bg">Commission</th>
                        </tr>
                    </thead>
                    <tbody id="appendpopup_data">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {  
    /*$('#pager').DataTable( {  
        initComplete: function () {  
            this.api().columns().every( function () {  
                var column = this;  
                var select = $('<select><option value=""></option></select>')  
                    .appendTo( $(column.footer()).empty() )  
                    .on( 'change', function () {  
                        var val = $.fn.dataTable.util.escapeRegex(  
                            $(this).val()  
                        );  
                        column  
                            .search( val ? '^'+val+'$' : '', true, false )  
                            .draw();  
                    } );  
                column.data().unique().sort().each( function ( d, j ) {  
                    select.append( '<option value="'+d+'">'+d+'</option>' )  
                } );  
            } ); 
        }  
    } );*/  
} ); 
</script>
<script type="text/javascript"> 

var _token = $("input[name='_token']").val();
function getCommissionReport() {    

   
   // DataTable
    var date_from = $('#fromdate').val();
    var todate = $('#todate').val();
    var userName = $('#userName').val();  
    /*alert(date_from);
    alert(todate);*/
    /*  $('#pager').DataTable({
         processing: true,
         serverSide: true,
         data: {
            date_from:date_from,
            todate:todate,
            userName:userName,
        }, 
         ajax: "{{route('getCommissionReport')}}",
         columns: [
            { data: 'id' },
            { data: 'username' },
            { data: 'ComPresentage' },
            { data: 'commission' },
         ]
      });
*/

  $('#pager').DataTable({
         destroy: true,
         processing: true,
         serverSide: true,
        ajax: {
    url:'{{ route("getCommissionReport") }}',
    data:{date_from:date_from, todate:todate,userName:userName}
   },
         columns: [
            { data: 'id' },
            { data: 'username' },
            { data: 'ComPresentage' },
            { data: 'commission' },
         ]
      });

}

function openReport(vl) {
    var userId = $(vl).data("id");
    var name = $(vl).data("name");
    var date_from = $('#fromdate').val();
    var todate = $('#todate').val();

    $.ajax({
        type: "POST",
        url: '{{route("getCommissionPopup")}}',
        data: {
            _token: _token,
            date_from:date_from,
            todate:todate,
            userId:userId,
        }, 
        beforeSend:function(){
            $('#site_bet_loading1').show();
        },
        complete: function(){
            $('#site_bet_loading1').hide();
        },             
        success: function(data) {
            $('.user_name').html(name);
            $(".showForm").modal('show');
            $('#appendpopup_data').html(data.html);
        }
    });
}
</script>
@endsection