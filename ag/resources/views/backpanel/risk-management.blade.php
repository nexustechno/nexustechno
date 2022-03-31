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
                        if ($cnt == 0) {
                            $collptrue = 'true';
                        } else {
                            $collptrue = 'false';
                        }
                    ?>

                    <div class="panel panel-default">
                        <div class="panel-heading black-bg-rgb" role="tab" id="headingOne">
                            <h2 class="panel-title">
                                <a class="text-color-white" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne{{$sport->sId}}" aria-expanded="{{$collptrue}}" aria-controls="collapseOne{{$sport->sId}}">
                                    {{$sport->sport_name}}
                                </a>
                            </h2>
                        </div>

                        <div id="collapseOne{{$sport->sId}}" class="panel-collapse show " role="tabpanel" aria-labelledby="headingOne"></div>
                    </div>
                    <?php $cnt++; ?>
                @endforeach
            </div>
        </div>
    </section>

@endsection

@push('third_party_scripts')
    <script src="https://cdn.socket.io/socket.io-1.4.5.js"></script>
    <script src="{{ asset('js/laravel-echo-server.js') }}"></script>

    <script type="text/javascript">

        function loadData(record){
            if (record.markets != undefined && record.markets[0] != undefined && record.markets[0].selections != undefined) {
                var selections = record.markets[0].selections;
                var inPlay = record.markets[0].inPlay;

                var eventId = record.id;

                if (inPlay == 1) {
                    $(".in-play-status-" + eventId).removeClass('collapse');
                } else {
                    $(".in-play-status-" + eventId).addClass('collapse');
                }

                if (selections.length > 0 && selections[0] != undefined) {
                    var team = 1;
                    for (var j = selections.length - 1; j >= 0; j--) {
                        if(selections[j].availableToBack[0]!=undefined && selections[j].availableToBack[0].price!=undefined) {
                            $(".team" + team + "-" + eventId + " .button_content .backbtn").html(selections[j].availableToBack[0].price);
                        }
                        if(selections[j].availableToLay[0]!=undefined && selections[j].availableToLay[0].price!=undefined) {
                            $(".team" + team + "-" + eventId + " .button_content .laybtn").html(selections[j].availableToLay[0].price);
                        }
                        team++;
                    }
                }
            }
        }

        window.Echo.channel('matches').listen('.tennis', (data) => {
            for (var i = 0; i < data.records.events.length; i++) {
                loadData(data.records.events[i]);
            }
        });
        window.Echo.channel('matches').listen('.cricket', (data) => {
            for (var i = 0; i < data.records.events.length; i++) {
                loadData(data.records.events[i]);
            }
        });
        window.Echo.channel('matches').listen('.soccer', (data) => {
            for (var i = 0; i < data.records.events.length; i++) {
                loadData(data.records.events[i]);
            }
        });
    </script>

    <script type="text/javascript">
        $(document).ready(function () {
            var gettab = $('.gettab1').attr("data-id");
            $("." + gettab).addClass("active");
            $('#site_bet_loading1').show();
            getriskdetail();
            setInterval(function () {
                // getriskdetail();
            }, 2000);

            function getriskdetail() {
                var _token = $("input[name='_token']").val();
                $.ajax({
                    type: "GET",
                    url: '{{route("risk-management-data")}}',
                    beforeSend: function () {
                        // $('#site_bet_loading1').show();
                    },
                    complete: function () {
                        $('#site_bet_loading1').hide();
                    },
                    success: function (data){
                        $('#collapseOne4').html(data.html[4]);
                        $('#collapseOne2').html(data.html[2]);
                        $('#collapseOne1').html(data.html[1]);
                        // getriskdetail();
                    }
                });
            }
        });
    </script>
@endpush
