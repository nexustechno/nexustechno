@extends('layouts.front_layout')
@section('content')
<?php
use App\setting;
use App\User;
use App\CreditReference;
$settings = CreditReference::where('player_id',$loginuser->id)->first();
$balance=$settings['available_balance_for_D_W'];

?>
<style type="text/css">
    .credit-modal .modal-dialog {
        max-width: max-content;
    }
    .opnlink {
        color: #1a7fe3 !important;
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
#example123_paginate {
    height: 100px;
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

<style>
    table.dataTable, table.dataTable th, table.dataTable td {
        box-sizing: border-box !important;
    }
</style>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

{{--<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>--}}

<section>
    <div class="container-fluid">
        <div class="main-wrapper">
            <div class="col-md-12">
                <div class="listing-grid">
                    <div class="detail-row">
                        <h2>Account Statement</h2>
                        <form method="post" action="" class="ajaxFormSubmit">
                            <div class="m-t-20">
                                <div class="row datediv1">

                                    <div class="col-md-2 datediv">
                                        <input type="text" class="form-control" name="reportrange" value="" />
                                        <img src="{{ URL::to('asset/img/calendar-icon.png') }}" class="calendar-icon_2" style="top:5px;">
{{--                                    </div>--}}
{{--                                    <div class="col-md-2 datediv">--}}
                                        <input name="fromdate" id="fromdate" class="form-control period_date55" type="hidden" value="">
{{--                                        <img src="{{ URL::to('asset/img/calendar-icon.png') }}" class="calendar-icon_2" style="top:5px;">--}}
{{--                                    </div>--}}
{{--                                    <div class="col-md-2 datediv">--}}
                                        <input name="todate" id="todate" class="form-control period_date66" type="hidden" value="">
{{--                                        <img src="{{ URL::to('asset/img/calendar-icon.png') }}" class="calendar-icon_2" style="top:5px;">--}}
                                    </div>

                                    <div class="col-md-2" >
                                        <select name="drpval" id="drpval" class="form-control acc-filter">
                                            <option value="0"> All </option>
                                            <option value="1"> Deposit/Withdraw Report </option>
                                            <option value="2"> Game Report </option>
                                        </select>
                                    </div>

                                    <div class="col-md-1" >
                                        <input id="acntbtn" type="button" value="Submit" name="acntbtn" class="submit-btn text-color-yellow addrecord" onclick="getaccountreport()">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="table-responsive data-table mt-3 pb-5" id="tbldata">
{{--                        <div id="example_wrapper" class="dataTables_wrapper no-footer">--}}
                            <table id="example123" class="table table-striped table-bordered datatable dataTable no-footer" role="grid" aria-describedby="example_info">
                                <thead>
                                    <tr role="row">
                                        <th class="sorting_asc" rowspan="1" colspan="1" aria-label="Date" style="width: 150px;">Date</th>
                                        <th class="sorting_disabled" rowspan="1" colspan="1" aria-label="Credit" style="width: 50px;">Sr no</th>
                                        <th class="sorting_disabled" rowspan="1" colspan="1" aria-label="Credit" style="width: 130px;">Credit</th>
                                        <th class="sorting_disabled" rowspan="1" colspan="1" aria-label="Debit" style="width: 130px;">Debit</th>
                                        <th class="sorting_disabled" rowspan="1" colspan="1" aria-label="Closing" style="width: 130px;">Balance</th>
                                        <th class="sorting_disabled text-left" rowspan="1" colspan="1" aria-label="Fromto">Remark</th>
                                    </tr>
                                </thead>
                                <tbody id="tblData">

                                </tbody>
                            </table>
{{--                        </div>--}}

                    </div>
                </div>
            </div>

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

<script>
    $(function() {
        var start = moment().subtract(29, 'days');
        var end = moment();

        $("#fromdate").val(start.format('DD-MM-YYYY'));
        $("#todate").val(end.format('DD-MM-YYYY'));

        $('input[name="reportrange"]').daterangepicker({
            maxSpan: {
                "days": 30
            },
            maxDate: end,
            locale: {
                format: 'DD-MM-YYYY',
                firstDay: 1
            },
            opens: 'right',
            startDate: start,
            endDate: end,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, function(start, end, label) {
            $("#fromdate").val(start.format('DD-MM-YYYY'));
            $("#todate").val(end.format('DD-MM-YYYY'));
            $('input[name="reportrange"]').val(start.format('DD-MM-YYYY') + ' TO ' + end.format('DD-MM-YYYY'));
            // console.log("A new date selection was made: " + start.format('DD-MM-YYYY') + ' to ' + end.format('DD-MM-YYYY'));
        });
    });
</script>

<script type="text/javascript">
var _token = $("input[name='_token']").val();

// $('#site_bet_loading1').show();
setTimeout(()=>{
    getaccountreport()
}, 800);

function getaccountreport()
{
    var date_from = $('#fromdate').val();
    var date_to = $('#todate').val();
    var drp_value = $('#drpval').val();
    var mytable = $('#example123').DataTable({
        paging:   false,
        destroy: true,
        processing: true,
        serverSide: true,
        ajax: {
            type: "post",
            url:"{{route('accountstmtdata')}}",
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
                drpval:drp_value
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
        url: "{{route('accountstmtdata')}}",
        data: {
            "_token": "{{ csrf_token() }}",
            "datefrom":date_from,
            "dateto":date_to,
            "drpval":drp_value
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

    $.ajax({
        type: "POST",
        url: '{{route("getAccountPopup")}}',
        data: {
            "_token": "{{ csrf_token() }}",
            datefrom:date_from,
            dateto:date_to,
            drpval:drp_value,
            mid:mid,
            btyp:btyp,
            tnm:tnm
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
</script>

@include('layouts.footer')
@endsection
