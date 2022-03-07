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

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>

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

<input type="hidden" name="pid" id="pid" value="{{$id}}">
<div id="site_bet_loading1" class="betloaderimage1 loader-style1" style="display: none;"></div>
<body onload=display_ct();>
<section class="myaccount-section">
    <div class="container">
        <div class="row">
            <div class="col-md-12 pl-0">
                <div class="downline-block">                  
                    <ul class="agentlist">
                        <li class="lastli"><a><span class="orange-bg text-color-white">{{$user->agent_level}}</span><strong>{{$user->user_name}}</strong></a></li>                       
                    </ul>
                </div>
            </div>
        </div>
            <div class="row">
                <div class="col-lg-3 col-md-3 col-sm-12 pl-0">
                    @include('backpanel/downline-account-menu')
                </div>
                    <!-- <div class=""> -->
                        <div class="col-lg-9 col-md-9 col-sm-12">
                            <div class="pagetitle text-color-blue-2 mb-10">
                        <h1> Account Statement </h1>
                    </div>
                                <div class="detail-row">
                                    <form method="post" action="" class="ajaxFormSubmit">
                                        <div class="m-t-20">
                                            <div class="row datediv1">
                                                <div class="col-md-2 datediv">
                                                    <input name="fromdate" id="fromdate" class="form-control period_date5a" type="text" autocomplete="off" placeholder="{{date('d-m-Y')}}" value="{{date('d-m-Y')}}">
                                                    <img src="{{ URL::to('asset/img/calendar-icon.png') }}" class="calendar-icon_2" style="top:5px;">
                                                </div>

                                                <div class="col-md-2 datediv">
                                                    <input name="todate" id="todate" class="form-control period_date6a" type="text" readonly="" autocomplete="off" placeholder="{{Date('d-m-Y', strtotime('+1 days'))}}" value="{{Date('d-m-Y', strtotime('+1 days'))}}">
                                                    <img src="{{ URL::to('asset/img/calendar-icon.png') }}" class="calendar-icon_2" style="top:5px;">
                                                </div>

                                                <div class="col-md-3">
                                                    <select name="drpval" id="drpval" class="form-control acc-filter">
                                                        <option value="0"> All </option>
                                                        <option value="1"> Deposit/Withdraw Report </option>
                                                        <option value="2"> Game Report </option>
                                                    </select>
                                                </div>

                                                <div class="col-md-1">
                                                    <input id="acntbtn" type="button" value="Submit" name="acntbtn" class="submit-btn text-color-yellow" onclick="getaccountreport()">
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <div class="table-responsive data-table mt-3" id="tbldata">
                                    <div id="example_wrapper" class="dataTables_wrapper no-footer">
                                        <table id="example123" class="table table-striped table-bordered datatable dataTable no-footer" style="width: 100%;" role="grid" aria-describedby="example_info">
                                            <thead>
                                                <tr role="row">
                                                    <th class="sorting_asc" rowspan="1" colspan="1" aria-label="Date" style="width: 90px;">Date</th>
                                                    <th class="sorting_disabled" rowspan="1" colspan="1" aria-label="Credit" style="width: 50px;">Sr no</th>
                                                    <th class="sorting_disabled" rowspan="1" colspan="1" aria-label="Credit" style="width: 130px;">Credit</th>
                                                    <th class="sorting_disabled" rowspan="1" colspan="1" aria-label="Debit" style="width: 130px;">Debit</th>
                                                    <th class="sorting_disabled" rowspan="1" colspan="1" aria-label="Closing" style="width: 130px;">Balance</th>
                                                    <th class="sorting_disabled" rowspan="1" colspan="1" aria-label="Fromto">Remark</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tblData">
                                                
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            
                        </div>
                    <!-- </div> -->
            </div>
        
    </div>
</section>

<div class="modal fade credit-modal showForm" id="ReportMatchbetModalthree">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <div id="match_delete" role="radiogroup" tabindex="-1" style="margin: 0 auto;">
                    <div class="custom-control custom-control-inline custom-radio">
                        <input id="matched" type="radio" name="match_delete" autocomplete="off" value="1" class="custom-control-input"> 
                        <label for="matched" class="custom-control-label" style="line-height: 24px;"><span>Matched</span></label>
                    </div> 
                    <div class="custom-control custom-control-inline custom-radio">
                        <input id="deleteed" type="radio" name="match_delete" autocomplete="off" value="2" class="custom-control-input"> 
                        {{--<label for="deleteed" class="custom-control-label" style="line-height: 24px;"><span>Deleted</span></label>--}}
                    </div>
                </div>
            </div>

            <div class="modal-body">
                <div class="row row5">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table role="table" aria-busy="false" aria-colcount="8" class="table b-table table-bordered" id="__BVID__533"><!----><!---->
                                <thead role="rowgroup" class=""><!---->
                                    <tr role="row" class="">
                                        <th role="columnheader" scope="col" aria-colindex="1" class="text-right">No</th>
                                        <th role="columnheader" scope="col" aria-colindex="2" class="text-center">Nation</th>
                                        <th role="columnheader" scope="col" aria-colindex="2" class="text-center">Type</th>
                                        <th role="columnheader" scope="col" aria-colindex="3" class="text-center">Side</th>
                                        <th role="columnheader" scope="col" aria-colindex="4" class="text-right">Rate</th>
                                        <th role="columnheader" scope="col" aria-colindex="5" class="text-right">Amount</th>
                                        <th role="columnheader" scope="col" aria-colindex="6" class="text-right">Win/Loss</th>
                                        <th role="columnheader" scope="col" aria-colindex="8" class="text-center">Place Date</th>
                                    </tr>
                                </thead>
                                <tbody role="rowgroup" id="appendpopup_data"><!---->
                                    <!----><!---->
                                </tbody><!---->
                            </table>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer text-right">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src = "https://cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js" defer ></script>

<script type="text/javascript">

    function getaccountreport()
    {  
        var pid = $("#pid").val();
        var date_from = $('#fromdate').val();
        var date_to = $('#todate').val();
        var drp_value = $('#drpval').val();

        var mytable = $('#example123').DataTable({
        destroy: true,
        processing: true,
        serverSide: true,
       
        ajax: {
            type: "post",
            url:"{{route('back_accountstmtdata')}}",
            beforeSend:function(){
                $('#site_bet_loading1').show();
            },
            complete: function(){
                $('#site_bet_loading1').hide();
            },
            data:{
                "_token": "{{ csrf_token() }}",
                datefrom:date_from,
                dateto:date_to,
                drpval:drp_value,
                pid:pid
            }
        },
        columns: 
        [
            { data: 'date' },
            { data: 'srno' },
            { data: 'credit' },
            { data: 'debit' },
            { data: 'balance' },
            { data: 'remark' },
        ]    
    });

        /*$.ajax({
            type: "post",
            url: "{{route('back_accountstmtdata')}}",
            data: {
                "_token": "{{ csrf_token() }}",
                "datefrom":date_from,
                "dateto":date_to,
                "drpval":drp_value,
                "pid":pid
            },
            success: function(data){
                $('#tblData').html(data);
            }
        });*/
    }

    function openMatchReport(val)
    {
        
        var date_from = $('#fromdate').val();
        var date_to = $('#todate').val();
        var drp_value = $('#drpval').val();
        var btyp = $(val).attr('data-type');
        var mid=$(val).attr('data-id');
        var tnm=$(val).attr('data-name');
        var pid = $("#pid").val();
       
        $.ajax({
            type: "POST",
            url: '{{route("back_getAccountPopup")}}',
            data: {
                "_token": "{{ csrf_token() }}",
                datefrom:date_from,
                dateto:date_to,
                drpval:drp_value,
                mid:mid,
                btyp:btyp,
                tnm:tnm,
                pid:pid
            }, 

            beforeSend:function(){
                $('#site_bet_loading1').show();
            },
            complete: function(){
                $('#site_bet_loading1').hide();
            },             
            success: function(data) {
                //$('.user_name').html(name);
                $(".showForm").modal('show');
                $('#appendpopup_data').html(data);
            }
        });
    }

   /* function getHistoryPL(val) {
        //document.getElementById("site_bet_loading1").style.display = "block";
        var date_to = $('#date_to').val();
        var date_from = $('#date_from').val();
        var pid = $("#pid").val();
        var sport = $('#sportsevent').find(":selected").val();

        $.ajax({
            type: "POST",
            url: '{{route("getBetHistoryPLBack")}}',
            data: {
               "_token": "{{ csrf_token() }}",
                date_from:date_from,
                date_to:date_to,
                val:val,
                pid:pid,
                sport:sport,
            },              
            success: function(data) {
                //document.getElementById("site_bet_loading1").style.display = "none";
                var spl=data.split('~~');
                $( ".cnd" ).show();
                $('#PLdata').html(spl[0]);

                $(".amt").html(spl[1]);
            }
        });
    }

    $('.period_date3').datepicker({
        dateFormat: "yy-mm-dd"
    });
    $('.period_date4').datepicker({
        dateFormat: "yy-mm-dd"
    });

    function display_c(){
        var refresh=1000; // Refresh rate in milli seconds
        mytime=setTimeout('display_ct()',refresh)
    }

    function display_ct() {
        var x = new Date()
        var x1=x.getMonth() + 1+ "-" + x.getDate() + "-" + x.getFullYear(); 
        x1 = x1 + "   " +  x.getHours( )+ ":" +  x.getMinutes() + ":" +  x.getSeconds();
        document.getElementById('ct').innerHTML = x1;
        display_c();
    }*/
</script>
@endsection
