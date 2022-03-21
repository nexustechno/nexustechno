@extends('layouts.app')
@section('content')
    <?php
    use App\setting;
    use App\User;
    use App\CreditReference;
    $settings = ""; $balance = 0;
    $loginuser = Auth::user();
    $ttuser = User::where('id', $loginuser->id)->first();
    $auth_id = Auth::user()->id;
    $auth_type = Auth::user()->agent_level;
    if ($auth_type == 'COM') {
        $settings = setting::latest('id')->first();
        $balance = $settings->balance;
    } else {
        $settings = CreditReference::where('player_id', $auth_id)->first();
        $balance = $settings['available_balance_for_D_W'];
    }
    ?>

    <style type="text/css">
        .betloaderimage1 {
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

        .loading1 li {
            list-style: none;
            text-align: center;
            font-size: 11px;
        }
    </style>
    <div id="site_bet_loading1" class="betloaderimage1 loader-style1" style="display: none">
        <ul class="loading1">
            <li>
                <img src="/asset/front/img/loaderajaxbet.gif">
            </li>
            <li>Loading...</li>
        </ul>
    </div>

    <section class="myaccount-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-9 col-sm-12">
                    <div class="pagetitle text-color-blue-2 mb-10">
                        <h1> Account Statement </h1>
                    </div>
                </div>
            </div>

            <div class="row">
                @csrf
                <div class="col-md-2">
                    <label>Search by client: </label>
                    <select name="user" id="user" class="form-control acc-filter">
                        <option value="">Select</option>
                        @foreach($list as $data)
                            <option value="{{$data->id}}">{{$data->user_name}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label>From: </label>
                    <input name="fromdate" id="fromdate" class="form-control period_date1" type="text"
                           placeholder="{{date('d-m-Y')}}" value="{{date('d-m-Y')}}">
                </div>
                <div class="col-md-2">
                    <label>To: </label>
                    <input name="todate" id="todate" class="form-control period_date2" type="text"
                           placeholder="{{Date('d-m-Y', strtotime('+1 days'))}}"
                           value="{{Date('d-m-Y', strtotime('+1 days'))}}">
                </div>

                <div class="col-md-2" >
                    <label>Report Type: </label>
                    <select name="drpval" id="drpval" class="form-control acc-filter">
                        <option value="0"> All </option>
                        <option value="1"> Deposit/Withdraw Report </option>
                        <option value="2"> Game Report </option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label>Report For: </label>
                    <select name="report_for" id="report_for" class="form-control acc-filter">
                        <option value="all">Upline/Downline</option>
                        <option value="upper">Upline</option>
                        <option value="down">Downline</option>
                    </select>
                </div>

                <div class="col-md-1">
                    <label> &nbsp; </label>
                    <input id="acntbtn" type="button" value="Submit" name="acntbtn"
                           class="submit-btn text-color-yellow">
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 col-md-9 col-sm-12">
                    <div class="white-bg mt-20 acc-statement">
                        <table class="table custom-table white-bg text-color-blue-2" id="pager">
                            <thead>
                            <tr>
                                <th class="light-grey-bg">Sr no</th>
                                <th class="light-grey-bg">Date/Time</th>
                                <th class="light-grey-bg">Credit</th>
                                <th class="light-grey-bg">Debit</th>
                                <th class="light-grey-bg">Balance</th>
                                <th class="light-grey-bg">Remark</th>
                                <th class="light-grey-bg">From/To</th>
                            </tr>
                            </thead>
                            <tbody id="tbdata">
                                <tr>
                                    <td colspan="6" align="center" valign="middle" class="text-center">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="pagination-wrap light-grey-bg-1"></div>
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
                                <table role="table" aria-busy="false" aria-colcount="8" class="table b-table table-bordered" id="__BVID__533">
                                    <thead role="rowgroup" class="">
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
                                    <tbody role="rowgroup" id="appendpopup_data"></tbody>
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

    <script>

        loadData();

        function loadData(){
            var startdate = $("#fromdate").val();
            var todate = $("#todate").val();
            var user = $("#user").val();
            var drp_value = $('#drpval').val();
            var report_for = $("#report_for").val();
            $.ajax({
                type: "post",
                url: '{{route("account.statement.data")}}',
                data: {"_token": "{{ csrf_token() }}", "startdate": startdate, "todate": todate, "user": user,  drpval:drp_value,report_for:report_for},
                beforeSend: function () {
                    $('#site_bet_loading1').show();
                },
                complete: function () {
                    $('#site_bet_loading1').hide();
                },
                success: function (data) {
                    $("#tbdata").html(data);
                }
            });
        }

        function openMatchReport(val)
        {
            var startdate = $('#fromdate').val();
            var todate = $('#todate').val();
            var btyp = $(val).attr('data-type');
            var mid=$(val).attr('data-id');
            var tnm=$(val).attr('data-name');
            var betuserid=$(val).attr('data-betuserid');

            $.ajax({
                type: "POST",
                url: '{{route("account.statement.popup.data")}}',
                data: {
                    "_token": "{{ csrf_token() }}",
                    startdate:startdate,
                    todate:todate,
                    mid:mid,
                    btyp:btyp,
                    tnm:tnm,
                    betuserid:betuserid,
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

        $('#acntbtn').click(function () {
            loadData();
        });


        $(document).ready(function () {

            function enabelDisableReportForOption(){
                $("#report_for").prop('disabled', true);
                $("#report_for").val('all');
                if($('#drpval').val() == 1){
                    $("#report_for").prop('disabled', false);
                    $("#report_for").val('all');
                }
            }

            enabelDisableReportForOption();

            $("body").on('change','#drpval',function () {
                enabelDisableReportForOption();
            });


        });

    </script>
@endsection
