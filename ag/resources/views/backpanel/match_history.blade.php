@extends('layouts.app')
@section('content')
    <?php
    use App\Sport;
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
        .modal-header{
            display: block;
        }

        .modal-title{
            display: flex;
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
            <div class="inner-title player-right justify-content-between py-2">
                <h2>Match History</h2>
            </div>

            <!-- Match History Tab  -->
            <div class="match_history_tabs" id="matchHistoryData" style="margin-bottom: 15px;">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item" data-id="cricket">
                        <a class="nav-link text-color-blue-1 white-bg inplay active" href="#cricket" role="tab"
                           data-toggle="tab" data-id="cricket" onclick="historyData('cricket')">Cricket</a>
                    </li>
                    <li class="nav-item" data-id="tennis">
                        <a class="nav-link text-color-blue-1 white-bg tennis" href="#tennis" role="tab"
                           data-toggle="tab" data-id="tennis" onclick="historyData('tennis')">Tennis</a>
                    </li>
                    <li class="nav-item" data-id="soccer">
                        <a class="nav-link text-color-blue-1 white-bg soccer" href="#soccer" role="tab"
                           data-toggle="tab" data-id="soccer" onclick="historyData('soccer')">Soccer</a>
                    </li>
                </ul>
            </div>
            <!-- End Match History Tab -->
            <div role="tabpanel" class="tab-pane active" id="cricket">
                <div class="list-games-block match_history_table">
                    <table id="ctable" class="display nowrap dataTable no-footer" style="width:100%">
                        <thead>
                        <tr class="light-grey-bg">
                            <th>Sr.No.</th>
                            <th>Open Date</th>
                            <th>Sport Name</th>
                            <th>Match Name</th>
                            <th>Match Id</th>
                            <th>Event Id</th>
                            <th>Winner</th>
                            <th>Action</th>
                            <th>Bet</th>
                        </tr>
                        </thead>
                        <tbody id="cData"></tbody>
                    </table>
                </div>
            </div>

            <div role="tabpanel" class="tab-pane" id="tennis">
                <div class="list-games-block match_history_table">
                    <table id="ttable" class="display nowrap dataTable no-footer" style="width:100%">
                        <thead>
                        <tr class="light-grey-bg">
                            <th>Sr.No.</th>
                            <th>Open Date</th>
                            <th>Sport Name</th>
                            <th>Match Name</th>
                            <th>Match Id</th>
                            <th>Event Id</th>
                            <th>Winner</th>
                            <th>Action</th>
                            <th>Bet</th>
                        </tr>
                        </thead>
                        <tbody id="tData"></tbody>
                    </table>
                </div>
            </div>

            <div role="tabpanel" class="tab-pane" id="soccer">
                <div class="list-games-block match_history_table">
                    <table id="stable" class="display nowrap dataTable no-footer" style="width:100%">
                        <thead>
                        <tr class="light-grey-bg">
                            <th>Sr.No.</th>
                            <th>Open Date</th>
                            <th>Sport Name</th>
                            <th>Match Name</th>
                            <th>Match Id</th>
                            <th>Event Id</th>
                            <th>Winner</th>
                            <th>Action</th>
                            <th>Bet</th>
                        </tr>
                        </thead>
                        <tbody id="sData"></tbody>
                    </table>
                </div>
            </div>

        </div>
    </section>

    <!-- Modal -->
    <div id="rollbackModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <!-- Modal content-->
            <div class="modal-content light-grey-bg-1">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <p class="modal-title m-0 font-weight-bold"></p>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" value="">
                    <div class="form-group">
                        <label for="">Master Code</label><br/>
                        <input type="password" name="password" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default submit-btn text-color-yellow" onclick="updateResult()">Confirm</button>
                </div>
            </div>

        </div>
    </div>


    <script type="text/javascript">
        $(document).ready(function () {
            $.ajax({
                url: "{{route('matchHistoryCricket')}}",
                type: "POST",
                data: {
                    _token: _token,
                },
                beforeSend: function () {
                    $('#site_bet_loading1').show();
                },
                complete: function () {
                    $('#site_bet_loading1').hide();
                },
                success: function (data) {
                    $('#cData').html(data);
                    $('#tennis').hide();
                    $('#soccer').hide();
                },
            });
        });

        var _token = $("input[name='_token']").val();

        function resultRollbackMatch(val) {

            var id = $(val).data('id');
            var fancyName = $(val).data('match-name');

            $("#rollbackModal .modal-title").html(fancyName+" Rollback Action");
            $("#rollbackModal .modal-body input[name='id']").val(id);
            $("#rollbackModal").modal('show');
        }

        function updateResult() {

            var val = $("#rollbackModal .modal-body input[name='id']").val();
            var password = $("#rollbackModal .modal-body input[name='password']").val();

            if(password=='' || password == null || password == undefined){
                toastr.error('Enter master Code');
            }else {
                $("#rollbackModal .submit-btn").html("Loading...");
                $("#rollbackModal .submit-btn").attr("disabled",true);
                $.ajax({
                    url: "{{route('resultRollbackMatch')}}",
                    type: "POST",
                    data: {
                        _token: _token,
                        id: val,
                        password: password,
                    },
                    beforeSend: function () {
                        // $('#site_bet_loading1').show();
                    },
                    complete: function () {
                        // $('#site_bet_loading1').hide();
                    },
                    success: function (data) {
                        if (data.success == 'success') {
                            toastr.success('RollBack Successfully!');
                            setTimeout(function () {
                                location.reload(true);
                            }, 1000);
                        }else if (data.success == 'error') {
                            $("#rollbackModal .submit-btn").attr("disabled",false);
                            $("#rollbackModal .submit-btn").html("Confirm");
                            toastr.error('Invalid Master Code!');
                        }
                    },
                });
            }
        }

        function historyData(val) {
            $.ajax({
                url: "{{route('matchHistoryData')}}",
                type: "POST",
                data: {
                    _token: _token,
                    val: val,
                },
                beforeSend: function () {
                    $('#site_bet_loading1').show();
                },
                complete: function () {
                    $('#site_bet_loading1').hide();
                },
                success: function (data) {
                    var dt = data.split("~~");
                    if (dt[1] == 'cricket') {
                        $("#cData").html(dt[0]);
                        $('#cricket').show();
                        $('#soccer').hide();
                        $('#tennis').hide();
                    }
                    if (dt[1] == 'tennis') {
                        $('#tData').html(dt[0]);
                        $('#tennis').show();
                        $('#soccer').hide();
                        $('#cricket').hide();
                    }
                    if (dt[1] == 'soccer') {
                        $('#sData').html(dt[0]);
                        $('#soccer').show();
                        $('#tennis').hide();
                        $('#cricket').hide();
                    }

                },
            });
        }
    </script>
@endsection
