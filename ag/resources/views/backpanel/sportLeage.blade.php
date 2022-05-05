@extends('layouts.app')
@push('page_css')
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
@endpush
@section('content')
    <div id="site_bet_loading1" class="betloaderimage1 loader-style1" style="display: none">
        <ul class="loading1">
            <li>
                <img src="/asset/front/img/loaderajaxbet.gif">
            </li>
            <li>Loading...</li>
        </ul>
    </div>

    <section class="profit-section section-mlr pb-5">
        <div class="container">
            <div class="inner-title">
                <h2>Sports Leage</h2>
            </div>

            <div class="sports_leage">
                <div class="w-100 row">
                    <form id="frmsp" name="frmsp" class="timeblock-box">
                        <div class="datediv2">
                            <label for="">Sports</label>
                            <select name="sports" id="sports" class="form-control" style="display:inline-block">
                                <option value="">-Select Sport-</option>
                                <option value="cricket">Cricket</option>
                                <option value="tennis">Tennis</option>
                                <option value="soccer">Soccer</option>
                            </select>
                        </div>

                        <button type="button" onclick="checkAll()" class="btn btn-success btn-sm">Check All</button>
                    </form>
                </div>
                <div id="site_statistics_loading" class="loaderimage"></div>
                <table class="table custom-table white-bg text-color-blue-2">
                    <thead>
                        <tr class="light-grey-bg">
                            <th>Match Id</th>
                            <th>Event Id</th>
                            <th class="text-left">Match Name</th>
                            <th class="text-left">Match Date</th>
                            <th class="text-left">Action</th>
                        </tr>
                    </thead>
                    <tbody id="addNEwRowBody">
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection

@push('third_party_scripts')

    <script src="https://cdn.socket.io/socket.io-1.4.5.js"></script>
    <script src="{{ asset('js/laravel-echo-server.js') }}"></script>

    <script type="text/javascript">
        var sports_events = @json($sports_events);
        var htmlRender = false;
        $(document).ready(function () {

            $("#sports").on('change', function () {
                $("#addNEwRowBody").html('<tr role="row" class="even"><td class="notLink text-center" colspan="5">Please wait........</td><tr>'
                );
                htmlRender = false;
                window.Echo.leave('matches')
                getMatchDataFromEcho()
            });
        });


        function checkAll() {
            $('input[type=checkbox]').each(function (value,i) {
                if(this.checked){}else{
                    setTimeout(()=>{
                        $(this).trigger('click');
                    },i);
                    // $(this).checked = true;
                }
            });
        }

        function getMatchDataFromEcho(){
            var sports = $('#sports').val();
            const channel = window.Echo.channel('matches').listen('.' + sports, (data) => {

                var sports_id = 0;
                if(sports == 'cricket'){
                    sports_id = 4;
                }else if(sports == 'tennis'){
                    sports_id = 2;
                }else if(sports == 'soccer'){
                    sports_id = 1;
                }

                var htmldata = '';
                if(htmlRender == false) {

                    if(data.server == 1) {
                        for (var i = 0; i < data.records.length; i++) {

                            var checked = '';
                            if (jQuery.inArray(data.records[i].gameId, sports_events) != -1) {
                                checked = 'checked';
                            }

                            var isDraw = 0;
                            if (data.records[i].back12 > 0 && data.records[i].lay12 > 0) {
                                isDraw = 1;
                            }

                            var bookmaker = 0;
                            if (data.records[i].m1 == 'True') {
                                bookmaker = 1;
                            }

                            var fancy = 0;
                            if (data.records[i].f == 'True') {
                                fancy = 1;
                            }

                            var eventNameString = data.records[i].eventName.split('/');
                            if (data.records[i].gameId.charAt(0) == 3) {
                                htmldata += '<tr role="row" class="even">' +
                                    '<td class="notLink">' + data.records[i].marketId + '</td> ' +
                                    '<td class="notLink">' + data.records[i].gameId + '</td>' +
                                    '<td class="notLink">' + eventNameString[0] + '</td>' +
                                    '<td class="notLink">' + eventNameString[1] + '</td>' +
                                    '<td class="text-center">' +
                                    '<input type="checkbox" ' + checked + ' name="sportLeage" data-sports_id="' + sports_id + '" data-isDraw="' + isDraw + '" data-fancy="' + fancy + '" data-bookmaker="' + bookmaker + '" data-matchId="' + data.records[i].marketId + '" data-eventId="' + data.records[i].gameId + '" data-matchName="' + eventNameString[0] + '" data-game="' + sports + '" data-matchDate="' + eventNameString[1] + '" onclick="onClickSportsLeage(this)">' +
                                    '</td>' +
                                    '</tr>'
                            }
                        }
                    }
                    else if(data.server == 2) {

                        for (var i = 0; i < data.records.length; i++) {

                            var checked = '';
                            if (sports_events.indexOf(data.records[i].gameId.toString()) != -1) {
                                checked = 'checked';
                            }

                            var isDraw = 0;
                            if (data.records[i].back2 > 0 && data.records[i].lay2 > 0) {
                                isDraw = 1;
                            }

                            var bookmaker = 0;
                            if (data.records[i].m1 == true) {
                                bookmaker = 1;
                            }

                            var fancy = 0;
                            if (data.records[i].f == true) {
                                fancy = 1;
                            }

                            htmldata += '<tr role="row" class="even">' +
                                '<td class="notLink">' + data.records[i].marketId + '</td> ' +
                                '<td class="notLink">' + data.records[i].gameId + '</td>' +
                                '<td class="notLink">' + data.records[i].eventName + '</td>' +
                                '<td class="notLink">' + data.records[i].openDate + '</td>' +
                                '<td class="text-center">' +
                                '<input type="checkbox" ' + checked + ' name="sportLeage" data-sports_id="' + sports_id + '" data-isDraw="' + isDraw + '" data-fancy="' + fancy + '" data-bookmaker="' + bookmaker + '" data-matchId="' + data.records[i].marketId + '" data-eventId="' + data.records[i].gameId + '" data-matchName="' + data.records[i].eventName + '" data-game="' + sports + '" data-matchDate="' + data.records[i].openDate + '" onclick="onClickSportsLeage(this)">' +
                                '</td>' +
                                '</tr>'
                        }
                    }
                    else {
                        for (var i = 0; i < data.records.events.length; i++) {
                            if (data.records.events[i].markets[0] != undefined) {
                                var checked = '';
                                if (jQuery.inArray(data.records.events[i].id, sports_events) != -1) {
                                    checked = 'checked';
                                }

                                var isDraw = 0;
                                if (data.records.events[i].markets[0].numberOfRunners >= 3) {
                                    isDraw = 1;
                                }

                                var bookmaker = 0;
                                if (data.records.events[i].hasSportsBookMarkets == true || data.records.events[i].hasInPlayBookMakerMarkets == true) {
                                    bookmaker = 1;
                                }

                                var fancy = 0;
                                if (data.records.events[i].hasFancyBetMarkets == true || data.records.events[i].hasInPlayFancyBetMarkets == true) {
                                    fancy = 1;
                                }


                                htmldata += '<tr role="row" class="even">' +
                                    '<td class="notLink">' + data.records.events[i].markets[0].marketId + '</td> ' +
                                    '<td class="notLink">' + data.records.events[i].id + '</td>' +
                                    '<td class="notLink">' + data.records.events[i].name + '</td>' +
                                    '<td class="notLink">' + data.records.events[i].openDate + '</td>' +
                                    '<td class="text-center">' +
                                    '<input type="checkbox" ' + checked + ' name="sportLeage" data-sports_id="' + sports_id + '" data-isDraw="' + isDraw + '" data-fancy="' + fancy + '" data-bookmaker="' + bookmaker + '" data-matchId="' + data.records.events[i].markets[0].marketId + '" data-eventId="' + data.records.events[i].id + '" data-matchName="' + data.records.events[i].name + '" data-game="' + sports + '" data-matchDate="' + data.records.events[i].openDate + '" onclick="onClickSportsLeage(this)">' +
                                    '</td>' +
                                    '</tr>'

                            }
                        }
                    }
                    $("#addNEwRowBody").html(htmldata);
                }

                htmlRender = true;
            });
        }

        function onClickSportsLeage(obj) {
            if ($(obj).prop('checked') == true) {
                var isStore = 1;
            } else {
                var isStore = 0;
            }

            var matchId = $(obj).attr('data-matchId');
            var eventId = $(obj).attr('data-eventId');
            var matchName = $(obj).attr('data-matchName');
            var game = $(obj).attr('data-game');
            var matchDate = $(obj).attr('data-matchDate');
            var isDraw = $(obj).attr('data-isDraw');
            var fancy = $(obj).attr('data-fancy');
            var bookmaker = $(obj).attr('data-bookmaker');
            var sports_id = $(obj).attr('data-sports_id');
            console.log(matchName);
            $.ajax({
                type: "post",
                url: "{{ route('addMatchFromAPI') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    match_name: matchName,
                    match_id: matchId,
                    match_date: matchDate,
                    event_id: eventId,
                    sports_id: sports_id,
                    leage: '',
                    is_draw:isDraw,
                    bookmaker:bookmaker,
                    fancy:fancy,
                },
                beforeSend: function() {
                    $('#site_bet_loading1').show();
                },
                complete: function() {
                    $('#site_bet_loading1').hide();
                },
                success: function(data) {
                    if (data.result == 'error') {
                        toastr.error(data.message);
                        // $(obj).trigger('click');
                    }
                    if (data.result == 'success') {
                        toastr.success(data.message);
                    }
                }
            });
        }
        $(document).ready(function() {
            $('#frmsp').each(function() {
                this.reset()
            });
        });
    </script>
@endpush
