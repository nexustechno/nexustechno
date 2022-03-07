@extends('layouts.front_layout')
@section('content')
<section class="profit-section section-mlr">
    <div class="inner-title">
        <h2>Casino Results Report</h2>
    </div>
    <form method="post">
        <div class="timeblock light-grey-bg-2">
            <div class="timeblock-box">
                <div class="datebox">
                    <span>Date</span>
                    <div class="datediv1">
                        <div class="datediv">
                            <input type="text" name="fromdate" id="fromdate" class="form-control period_date3" placeholder="<?php echo date('Y/m/d');?>">
                            <img src="{{ URL::to('asset/img/calendar-icon.png')}}" class="calendar-icon">
                        </div>                   
                    </div>
                </div>
                <div class="datediv2">
                    <span>Type</span>
                    <select name="list" id="list" class="form-control">
                        <option value="0">Select Type</option>
                        @if(!empty($getdata))
                            @foreach($getdata as $data)
                            <option value="{{$data->casino_name}}">{{$data->casino_name}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="timeblock-box">
	                <a class="submit-btn text-color-yellow" id="casinoresult" name="casinoresult" onclick="getCasino()">Submit</a> 
	            </div>
            </div>
        </div>
    </form>
    <table class="table custom-table white-bg text-color-blue-2 search-result" id="pager">
        <thead>
            <tr>
                <th>Round ID</th>
                <th>Winner</th>
            </tr>
        </thead>
        <tbody id="casinoData">
        </tbody>
    </table>
</section>
<div class="modal golden_modal1 current_modal fade" id="exampleModal3" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content blue-dark-bg">
            <div class="modal-header darkblue-bg">
                <h5 class="modal-title text-color-yellow1" id="exampleModalLabel">Details</h5>
                <button type="button" class="close text-color-grey-1" data-dismiss="modal" aria-label="Close">×</button>
            </div>
            <div class="modal-body card32_results" id="appnedLastResult">
                <div class="casino_result_round">
                    <div>Round-Id: 6378481664424</div>
                    <div>Match Time: 24/06/2021 18:47:41</div>
                </div>
                <div class="row row1">
                    <div class="col-12 col-lg-9">
                        <div class="row row1">
                            <div class="col-12 col-lg-3">
                                <div class="casino-result-cards justify-content-start">
                                    <div class="d-inline-block">
                                        <h6>Player 8 - <span class="text-color-yellow1">17</span></h6>
                                        <div class="casino-result-cards-item"><img src="{{ URL::to('img/cards/6CC.png')}}"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-3">
                                <div class="casino-result-cards justify-content-start">
                                    <div class="d-inline-block">
                                        <h6>Player 9 - <span class="text-color-yellow1">15</span></h6>
                                        <div class="casino-result-cards-item"><img src="{{ URL::to('img/cards/4HH.png')}}"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-3">
                                <div class="casino-result-cards justify-content-start">
                                    <div class="d-inline-block">
                                        <h6>Player 10 - <span class="text-color-yellow1">23</span></h6>
                                        <div class="casino-result-cards-item"><img src="{{ URL::to('img/cards/JHH.png')}"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-3">
                                <div class="casino-result-cards justify-content-start">
                                    <div class="casino-result-cards-item"><img src="{{ URL::to('img/winner.png')}" class="winner_icon"></div>
                                    <div class="d-inline-block">
                                        <h6>Player 11 - <span class="text-color-yellow1">24</span></h6>
                                        <div class="casino-result-cards-item"><img src="{{ URL::to('img/cards/QCC.png')}"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-3">
                        <div class="casino-result-desc">
                            <div class="casino-result-desc-item">
                                <div>Winner</div>
                                <div>Player 11</div>
                            </div>
                            <div class="casino-result-desc-item">
                                <div>Odd/Even</div>
                                <div>8 : Odd | 9 : Even</div>
                            </div>
                            <div class="casino-result-desc-item">
                                <div></div>
                                <div>10 : Odd | 11 : Odd</div>
                            </div>
                            <div class="casino-result-desc-item">
                                <div>Black/Red</div>
                                <div>2-2</div>
                            </div>
                            <div class="casino-result-desc-item">
                                <div>Total</div>
                                <div>10-11</div>
                            </div>
                            <div class="casino-result-desc-item">
                                <div>Single</div>
                                <div>1</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
	function getCasino(){
		var type = $('#list').find(":selected").val();
        var fdate = $('#fromdate').val();
        var _token = $("input[name='_token']").val();
       
		if(type == 0){
            alert("Select Casino Type");
        }
        else{
            $.ajax({
                type: "POST",
                url: '{{route("dataCasinoReport")}}',
                data: {
                    _token: _token,
                    type: type,
                    fdate: fdate
                },
                success: function(data) {
                    $("#casinoData").html(data);
                   //toastr.success('Bet Lock Successfully !!!');
                }
            });
        }
	}
    function openLastPopup(round) {
        var _token = $("input[name='_token']").val();
        $.ajax({
            type: "POST",
            url: '{{route("teen20LastResultpopup")}}',
            data: {
                _token: _token,
                round: round
            },
            success: function(data) {
                var spl = data.split('~~');
                $("#appnedLastResult").html(data);
                $('#exampleModal3').modal('show');
            }
        });   
    }
</script>
@include('layouts.footer')
@endsection