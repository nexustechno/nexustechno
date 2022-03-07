@extends('layouts.front_layout')
@section('content')
<style>
    body {
        overflow: hidden;
    }
</style>
<section>
    <div class="container-fluid">
        <div class="main-wrapper">
            <div class="middle-section">
                <div class="middle-wraper">
                    <div class="casinotrap-table blue-dark-bg">
                        <div class="casino-video">
                            <div class="video-block">
                                <iframe src="{{$casino->casino_link}}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                            </div>
                            <div class="videotitle black-bg-rgb">
                                <span class="casino_name text-color-yellow1">20-20 Teenpatti</span>
                                <div class="casino_video_rid text-color-grey-2">Round ID: 8883791452411</div>
                            </div>
                            <div class="casinocards opencards">
                                <div class="casinocards_shuffle"><i class="fas text-color-grey-3 fa-grip-lines-vertical"></i></div>
                                <div class="casinocards-container">
                                    <div class="card_con">
                                        <span class="text-color-white"><img src="{{ URL::to('img/ADD.png')}}"></span>
                                        <span class="text-color-white"><img src="{{ URL::to('img/2CC.png')}}"></span>
                                        <span class="text-color-white"><img src="{{ URL::to('img/6SS.png')}}"></span>
                                    </div>
                                    <div class="card_con">
                                        <span class="text-color-white"><img src="{{ URL::to('img/QCC.png')}}"></span>
                                        <span class="text-color-white"><img src="{{ URL::to('img/2CC.png')}}"></span>
                                        <span class="text-color-white"><img src="{{ URL::to('img/2DD.png')}}"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="casino_time">
                                <div id="app"></div>
                            </div>
                            <div class="casino-icons">
                                <ul>
                                    <li class="black-rgb-1"> <a href="index1.php" class=" text-color-white"><i class="fas fa-home"></i></a> </li>
                                    <li class="black-rgb-1 text-color-white" data-toggle="modal" data-target="#exampleModal2"> <i class="fas fa-info-circle"></i> </li>
                                    <li class="black-rgb-1 text-color-white resicon"> <i class="fas fa-chevron-circle-up"></i> </li>
                                </ul>
                            </div>
                            <div class="casinolast_results black-bg-rgb">
                                <span class="resulta text-color-red-1">A</span>
                                <span class="resultb text-color-yellow1">B</span>
                                <span class="resulta text-color-red-1">A</span>
                                <span class="resultb text-color-yellow1">B</span>
                                <span class="resultb text-color-yellow1">B</span>
                                <span class="resultb text-color-yellow1">B</span>
                                <span class="resulta text-color-red-1">A</span>
                                <span class="resulta text-color-red-1">A</span>
                                <span class="resultb text-color-yellow1">B</span>
                                <span class="resulta text-color-red-1">A</span>
                                <a href="our-casino-results.php" class="resultmore text-color-white">...</a>
                            </div>
                        </div>
                        <div class="casino-videodetails">
                            <div class="playerblock">
                                <div class="player_left">
                                    <div class="casino-name text-color-red"><b>Player A</b></div>
                                    <div class="casino_box casino_title_box">
                                        <div class="casinobox-item"><span>Winner</span></div>
                                        <div class="casinobox-item"><span>Khal</span></div>
                                        <div class="casinobox-item"><span>Total</span></div>
                                        <div class="casinobox-item"><span>Pair Plus</span></div>
                                    </div>
                                    <div class="casino_box">
                                        <div class="casinobox-item backcasino lightblue-bg3 suspended-txt"><span class="casino_odd_txt">0</span></div>
                                        <div class="casinobox-item backcasino lightblue-bg3 suspended-txt"><span class="casino_odd_txt">0</span></div>
                                        <div class="casinobox-item backcasino lightblue-bg3 suspended-txt"><span class="casino_odd_txt">0</span></div>
                                        <div class="casinobox-item backcasino lightblue-bg3 suspended-txt"><span class="casino_odd_txt">0</span></div>
                                    </div>
                                    <div class="casino_rb_box">
                                        <div class="casinorb_items">
                                            <div class="casinorb_content backcasino lightblue-bg3 suspended-txt">
                                                <div>
                                                    <img src="{{ URL::to('img/spade.png')}}" alt="">
                                                    <img src="{{ URL::to('img/club.png')}}" alt="">
                                                </div>
                                                <div>
                                                    <span class="casino_odd_txt">0</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="casinorb_items">
                                            <div class="casinorb_content backcasino lightblue-bg3 suspended-txt">
                                                <div>
                                                    <img src="{{ URL::to('img/heart.png')}}" alt="">
                                                    <img src="{{ URL::to('img/diamond.png')}}" alt="">
                                                </div>
                                                <div>
                                                    <span class="casino_odd_txt">0</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="player_center grey-bg"></div>
                                <div class="player_right">
                                    <div class="casino-name text-color-yellow1"><b>Player B</b></div>
                                    <div class="casino_box casino_title_box">
                                        <div class="casinobox-item"><span>Winner</span></div>
                                        <div class="casinobox-item"><span>Khal</span></div>
                                        <div class="casinobox-item"><span>Total</span></div>
                                        <div class="casinobox-item"><span>Pair Plus</span></div>
                                    </div>
                                    <div class="casino_box">
                                        <div class="casinobox-item backcasino lightblue-bg3 suspended-txt"><span class="casino_odd_txt">0</span></div>
                                        <div class="casinobox-item backcasino lightblue-bg3 suspended-txt"><span class="casino_odd_txt">0</span></div>
                                        <div class="casinobox-item backcasino lightblue-bg3 suspended-txt"><span class="casino_odd_txt">0</span></div>
                                        <div class="casinobox-item backcasino lightblue-bg3 suspended-txt"><span class="casino_odd_txt">0</span></div>
                                    </div>
                                    <div class="casino_rb_box">
                                        <div class="casinorb_items">
                                            <div class="casinorb_content backcasino lightblue-bg3 suspended-txt">
                                                <div>
                                                    <img src="{{ URL::to('img/spade.png')}}" alt="">
                                                    <img src="{{ URL::to('img/club.png')}" alt="">
                                                </div>
                                                <div>
                                                    <span class="casino_odd_txt">0</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="casinorb_items">
                                            <div class="casinorb_content backcasino lightblue-bg3 suspended-txt">
                                                <div>
                                                    <img src="{{ URL::to('img/heart.png')}" alt="">
                                                    <img src="{{ URL::to('img/diamond.png')}}" alt="">
                                                </div>
                                                <div>
                                                    <span class="casino_odd_txt">0</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="casino_right_side">
                <form class="casinoplay-bet">
                    <div class="casinolay_bettitle black-bg-2 text-color-white">
                        <span>Place Bet</span>
                        <span class="float-right casinomin_max">Range:<span>100</span>-<span>100K</span></span>
                    </div>
                    <div class="casinoplay-betheader light-grey-bg-4">
                        <div>(Bet for)</div>
                        <div>Odds</div>
                        <div>Stake</div>
                        <div>Profit</div>
                    </div>
                    <div class="casinoplay-box blue-dark-bg">
                        <div class="casinoplay_betinfo">
                            <div class="bet_player"><span>Card 1 JQK</span></div>
                            <div class="odds_box">
                                <input type="text" value="4.17" disabled="disabled" class="form-control">
                                <img src="https://sitethemedata.com/v3/static/front/img/arrow-down.svg" class="arrow-up">
                                <img src="https://sitethemedata.com/v3/static/front/img/arrow-down.svg" class="arrow-down">
                            </div>
                            <div class="bet_input back_border">
                                <input type="text" class="form-control input-stake">
                            </div>
                            <div>0</div>
                        </div>
                        <div class="casinoplay_button">
                            <button class="btn btn-bet green-bg-1 text-color-white"><span>100</span></button>
                            <button class="btn btn-bet green-bg-1 text-color-white"><span>200</span></button>
                            <button class="btn btn-bet green-bg-1 text-color-white"><span>300</span></button>
                            <button class="btn btn-bet green-bg-1 text-color-white"><span>400</span></button>
                            <button class="btn btn-bet green-bg-1 text-color-white"><span>500</span></button>
                            <button class="btn btn-bet green-bg-1 text-color-white"><span>600</span></button>
                            <button class="btn btn-bet green-bg-1 text-color-white"><span>700</span></button>
                            <button class="btn btn-bet green-bg-1 text-color-white"><span>800</span></button>
                        </div>
                        <div class="casinoplay_action_buttons">
                            <button class="btn btn-reset red-bg text-color-white" type="reset" value="Reset">Reset</button>
                            <button class="btn btn-ok green-bg text-color-white" type="submit" value="Submit">Submit</button>
                        </div>
                    </div>
                </form>
                <div class="casino_rules_table mt-4">
                    <div class="casinolay_bettitle black-bg-2 text-color-white">
                        <span>Rules</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr class="dark-grey-bg-1">
                                    <th colspan="2" class="text-center">Pair Plus</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Pair</td>
                                    <td>1 TO 1</td>
                                </tr>
                                <tr>
                                    <td>Flush</td>
                                    <td>1 TO 4</td>
                                </tr>
                                <tr>
                                    <td>Straight</td>
                                    <td>1 TO 6</td>
                                </tr>
                                <tr>
                                    <td>Trio</td>
                                    <td>1 TO 30</td>
                                </tr>
                                <tr>
                                    <td>Straight Flush</td>
                                    <td>1 TO 40</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal golden_modal1 fade" id="exampleModal2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content light-grey-bg-2">
            <div class="modal-header blue-dark-bg-3">
                <h5 class="modal-title text-color-yellow-1" id="exampleModalLabel">Rules</h5>
                <button type="button" class="close text-color-grey-1" data-dismiss="modal" aria-label="Close">×</button>
            </div>
            <div class="modal-body">
                <div class="p-5 modal-plus-block text-center">
                    <img src="{{ URL::to('img/teen.jpg')}}" class="img-fluid trapmodal_img">
                </div>
            </div>
        </div>
    </div>
</div>
@endsection