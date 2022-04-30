@extends('layouts.app')
@section('content')

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
            <div class="inner-title">
                <h2 style="font-weight: normal"><strong>{{ $match->match_name }}</strong> Premium Detail <span class="float-right font-weight-bold">{{ $match->match_date }}</span></h2>
            </div>

            <div class="fancy-history-details">
                <table class="table custom-table white-bg text-color-blue-2 fancy_tablenew">
                    <thead>
                    <tr>
                        <th class="white-bg text-left">Sr.No.</th>
                        <th class="white-bg text-left">Market Name</th>
                        <th class="white-bg text-left">Result</th>
                        <th class="white-bg text-center">Action</th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php $count = 1; ?>
                        @foreach($bets as $bet)
                            <?php
                                $teams = json_decode($bet->extra,true);
                            ?>
                            <tr class="white-bg {{ "tr_row_".$count }}">
                                <td class="text-center">{{ $count }}</td>
                                <td class="text-left">{{ $bet->market_name }}</td>
                                <td class="text-left">{{ $bet->winner }}</td>
                                <td class="text-center">
                                    <button type="button" data-row-class="{{"tr_row_".$count}}" data-market-id="{{$bet->market_id}}" data-market-name="{{$bet->market_name}}" data-event-id="{{$bet->match_id}}" class="submit-btn text-color-yellow match_view">Result Rollback</button>
                                </td>
                            </tr>
                            <?php $count++; ?>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>


    <script type="text/javascript">
        var _token = $("input[name='_token']").val();

        $(document).ready(function () {

            var rowClass = '';

            $("body").on('click', '.match_view', function () {
                var match_name = $(this).attr('data-market-name');
                if(confirm("Are you sure "+match_name+" rollback result?")) {
                    var event_id = $(this).attr('data-event-id');
                    var market_id = $(this).attr('data-market-id');
                    rowClass = $(this).attr('data-row-class');

                    $(this).html("Loading...");
                    $(this).prop("disable", true);
                    $.ajax({
                        url: "{{ route('premiumResultRollback') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            event_id: event_id,
                            market_id: market_id,
                        },
                        beforeSend: function () {
                        },
                        complete: function () {
                        },
                        success: function (data) {
                            if (data.status == true) {
                                toastr.success(data.message);
                                setTimeout(() => {
                                    $("." + rowClass).remove();
                                    rowClass = '';
                                }, 500);
                            } else {
                                toastr.error(data.message);
                            }
                        },
                    });
                }
            });
        });
    </script>
@endsection
