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

    <section>
        <div class="container">
            <div class="breadcrumbs">
                <ul>
                    <li><a href="#" class="text-color-black"> <span
                                class="red-bg text-color-white">{{$user->agent_level}}</span> {{$user->user_name}} </a>
                    </li>
                </ul>
            </div>
        </div>
    </section>
    <section class="myaccount-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-3 col-sm-12 pl-0">
                    @include('backpanel/account-menu-sidebar')
                </div>
                <div class="col-lg-9 col-md-9 col-sm-12">
                    <div class="row">
                        <div class="col-12">
                            <div class="pagetitle text-color-blue-2 mb-10">
                                <h1> Account Statement </h1>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        @csrf
                        <div class="col-md-3">
                            <label>Client: </label>
                            <select name="user" id="user" class="form-control acc-filter">
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
                        <div class="col-md-2">
                            <label> &nbsp; </label>
                            <input style="width: 100%" id="acntbtn" type="button" value="Submit" name="acntbtn"
                                   class="submit-btn text-color-yellow">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="white-bg mt-20 acc-statement">
                                <table class="table custom-table white-bg text-color-blue-2" id="pager">
                                    <thead>
                                    <tr>
                                        <th class="light-grey-bg">Date/Time</th>
                                        <th class="light-grey-bg">Deposit</th>
                                        <th class="light-grey-bg">Withdraw</th>
                                        <th class="light-grey-bg">Balance</th>
                                        <th class="light-grey-bg">Remark</th>
                                        <th class="light-grey-bg">From/To</th>
                                    </tr>
                                    </thead>
                                    <tbody id="tbdata">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>

        function loadData() {
            var startdate = $("#fromdate").val();
            var todate = $("#todate").val();
            var user = $("#user").val();

            $.ajax({
                type: "post",
                url: '{{route("data-myaccount-statement")}}',
                data: {"_token": "{{ csrf_token() }}", "startdate": startdate, "todate": todate, "user": user},
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

        loadData();

        $('#acntbtn').click(function () {
            loadData();
        });

        // $(document).ready(function () {
        //     $('#pager').DataTable({
        //         initComplete: function () {
        //             this.api().columns().every(function () {
        //                 var column = this;
        //                 var select = $('<select><option value=""></option></select>')
        //                     .appendTo($(column.footer()).empty())
        //                     .on('change', function () {
        //                         var val = $.fn.dataTable.util.escapeRegex(
        //                             $(this).val()
        //                         );
        //                         column
        //                             .search(val ? '^' + val + '$' : '', true, false)
        //                             .draw();
        //                     });
        //                 column.data().unique().sort().each(function (d, j) {
        //                     select.append('<option value="' + d + '">' + d + '</option>')
        //                 });
        //             });
        //         }
        //     });
        // });
    </script>
@endsection
