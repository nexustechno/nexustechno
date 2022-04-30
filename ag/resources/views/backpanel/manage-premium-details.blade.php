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
                                <td class="text-center">
                                    <button type="button" data-row-class="{{"tr_row_".$count}}" data-market-id="{{$bet->market_id}}" data-market-name="{{$bet->market_name}}" data-event-id="{{$bet->match_id}}" data-teams="{{ implode("##",$teams) }}" class="submit-btn text-color-yellow match_view">Declare Result</button>
                                </td>
                            </tr>
                            <?php $count++; ?>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <div class="modal fade" id="modal-secondary" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header black-gradient-bg text-color-yellow">
                    <h4 class="modal-title">Secondary Modal</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body append-checkboxes-here">
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-outline-warning save-changes">Save changes</button>
                </div>
            </div>

        </div>
    </div>


    <script type="text/javascript">
        var _token = $("input[name='_token']").val();

        $(document).ready(function () {

            var rowClass = '';

            $("body").on('click', '.match_view', function () {
                var teams = $(this).attr('data-teams');
                var event_id = $(this).attr('data-event-id');
                var match_name = $(this).attr('data-market-name');
                var market_id = $(this).attr('data-market-id');
                rowClass = $(this).attr('data-row-class');

                var teamArray = teams.split("##");

                var html = '';
                for (var i = 0; i < teamArray.length; i++) {
                    html += '<div class="form-group" style="display: inline-flex;min-width: 50%;">\n' +
                        '<div class="custom-control custom-radio">\n' +
                        '<input class="custom-control-input" type="radio" id="team_winner' + i + '" name="team_winner" value="' + teamArray[i] + '">\n' +
                        '<label for="team_winner' + i + '" class="custom-control-label">' + teamArray[i] + '</label>\n' +
                        '</div></div>';
                }

                html+="<input type='hidden' name='event_id' value='"+event_id+"'/>";
                html+="<input type='hidden' name='match_name' value='"+match_name+"'/>";
                html+="<input type='hidden' name='market_id' value='"+market_id+"'/>";

                $("#modal-secondary .modal-title").html(match_name);
                $("#modal-secondary .append-checkboxes-here").html(html);
                $("#modal-secondary .save-changes").prop("disable",false);
                $("#modal-secondary .save-changes").html("Save changes");
                $("#modal-secondary").modal("show");
            });


            $("body").on('click','#modal-secondary .save-changes',function () {

                if($("#modal-secondary input[name='team_winner']:checked").val()==undefined || $("#modal-secondary input[name='team_winner']:checked").val() == null || $("#modal-secondary input[name='team_winner']:checked").val() == ''){
                    toastr.warning('Select Winner');
                }else {
                    $(this).html("Loading...");
                    $(this).prop("disable", true);
                    $.ajax({
                        url: "{{ route('premiumResultDeclare') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            event_id: $("#modal-secondary input[name='event_id']").val(),
                            market_id: $("#modal-secondary input[name='market_id']").val(),
                            team_winner: $("#modal-secondary input[name='team_winner']:checked").val(),
                        },
                        beforeSend: function () {
                        },
                        complete: function () {
                        },
                        success: function (data) {
                            if (data.status == true) {
                                toastr.success(data.message);
                                setTimeout(() => {
                                    $("."+rowClass).remove();
                                    $("#modal-secondary").modal("hide");
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
