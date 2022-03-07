<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Sport;
use App\Match;
use App\CasinoBet;
use App\UserExposureLog;
use GuzzleHttp\Client;
use Carbon\Carbon;
use App\Casino;
use App\User;
use Session;
use Auth;
use DB;

class CasinoFrontController extends Controller
{
    public function getab20LastResultpopup(Request $request)
    {
        $cou = $request->cou;
        $html = '';
        $i = 1;
        $last_result = app('App\Http\Controllers\RestApi')->getteen20LastResult("ab20");

        foreach ($last_result as $key => $value) {
            if ($cou == $last_result[$i]['round_id']) {
                $html .= ' <div class="casino_result_round">
                    <div>Round-Id: ' . $last_result[$i]['round_id'] . '</div>
                    <div>Match Time: ' . $last_result[$i]['match_time'] . '</div>
                </div>

                <div class="row row1">
                    <div class="col-12 modalslider_bahar">
                        <h5 class="text-center">Andar</h5>
                        <div class="ab_slider_main text-center">';
                foreach ($last_result[$i]['andar_cards'] as $key => $value) {
                    $html .= '<span style="width:35px;"><img src="' . $value . '" alt="img"></span>';
                }
                $html .= '</div>
                    </div>
                    <div class="col-12 modalslider_bahar">
                        <h5 class="text-center">Bahar</h5>
                        <div class="ab_slider_main text-center">';
                foreach ($last_result[$i]['bahar_cards'] as $key => $value) {
                    $html .= '<span style="width:35px;"><img src="' . $value . '" alt="img"></span>';
                }
                $html .= '</div>
                    </div>
                </div>';
            }
            $i++;
        }
        $html .= '';
        return $html;
    }

    public function getab20LastResult()
    {
        $i = 1;
        $last_resultData = '';
        $count = 0;
        $html = '';
        $last_result = app('App\Http\Controllers\RestApi')->getteen20LastResult("ab20");
        $last_resultData = '';
        foreach ($last_result as $key => $value) {
            $last_resultData .= '<span class="resulta text-color-yellow1" onclick="openLastPopup(' . $last_result[$i]['round_id'] . ');">' . $last_result[$i]['type'] . '</span>';
            $i++;
            $count++;
        }
        $html .= $last_resultData;
        return $html;
    }

    public function getbaccaratLastResult()
    {
        $i = 1;
        $last_resultData = '';
        $count = 0;
        $html = '';
        $last_result = app('App\Http\Controllers\RestApi')->getteen20LastResult("baccarat");
        $last_resultData = '';
        foreach ($last_result as $key => $value) {
            if ($last_result[$i]['type'] == 'P') {
                $cls = 'text-color-blue';
                $aa = 'a';
            } elseif ($last_result[$i]['type'] == 'B') {
                $cls = 'text-color-red-1';
                $aa = 'b';
            } else {
                $cls = 'text-color-green';
                $aa = 'a';
            }
            $last_resultData .= '<span class="result' . $aa . ' ' . $cls . '" onclick="openLastPopup(' . $last_result[$i]['round_id'] . ');">' . $last_result[$i]['type'] . '</span>';
            $i++;
            $count++;
        }
        $html .= $last_resultData;
        return $html;
    }

    public function getdt202LastResult()
    {
        $i = 1;
        $last_resultData = '';
        $count = 0;
        $html = '';
        $last_result = app('App\Http\Controllers\RestApi')->getteen20LastResult("dt202");
        $last_resultData = '';
        foreach ($last_result as $key => $value) {
            if ($count % 3 === 0) {
                $cls = 'text-color-red-1';
            } else {
                $cls = 'text-color-yellow1';
            }
            $last_resultData .= '<span class="resulta ' . $cls . '" onclick="openLastPopup(' . $last_result[$i]['round_id'] . ');">' . $last_result[$i]['type'] . '</span>';
            $i++;
            $count++;
        }
        $html .= $last_resultData;
        return $html;
    }

    public function get32cardLastResult()
    {
        $i = 1;
        $last_resultData = '';
        $html = '';
        $last_result = app('App\Http\Controllers\RestApi')->getteen20LastResult("32card");
        $last_resultData = '';
        foreach ($last_result as $key => $value) {
            $last_resultData .= '<span class="resulta text-color-yellow1" onclick="openLastPopup(' . $last_result[$i]['round_id'] . ');">' . $last_result[$i]['type'] . '</span>';
            $i++;
        }
        $html .= $last_resultData;
        return $html;
    }

    public function get32cardbLastResultpopup(Request $request)
    {
        $round = $request->round;
        $html = '';
        $i = 1;
        $last_result = app('App\Http\Controllers\RestApi')->getteen20LastResult("32card");
        foreach ($last_result as $key => $value) {
            $player1 = explode('-', $last_result[$i][1]['name']);
            $player2 = explode('-', $last_result[$i][2]['name']);
            $player3 = explode('-', $last_result[$i][3]['name']);
            $player4 = explode('-', $last_result[$i][4]['name']);
            $winnerclass = '';
            if ($last_result[$i]['round_id'] == $round) {
                $html .= ' <div class="casino_result_round">
                    <div>Round-Id: ' . $round . '</div>
                    <div>Match Time: ' . $last_result[$i]['match_time'] . '</div>
                </div>
                <div class="row row1">
                    <div class="col-12 col-lg-9">
                        <div class="row row1">
                            <div class="col-12 col-lg-3">
                                <div class="casino-result-cards justify-content-start">
                                    <div class="d-inline-block">
                                        <h6>Player 8 - <span class="text-color-yellow1">' . $player1[1] . '</span></h6>';
                foreach ($last_result[$i][1]['img'] as $value1) {
                    if ($value1 == 'https://sitethemedata.com/v3/static/front/img/winner.png') {
                        $winnerclass = "winner_icon";
                        $html .= '<div class="casino-result-cards-item"><img class="' . $winnerclass . '" src="' . $value1 . '"></div>';
                    } else {
                        $html .= '<div class="casino-result-cards-item"><img src="' . $value1 . '"></div>';
                    }
                }

                $html .= '</div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-3">
                                <div class="casino-result-cards justify-content-start">
                                    <div class="d-inline-block">
                                        <h6>Player 9 - <span class="text-color-yellow1">' . $player2[1] . '</span></h6>';
                foreach ($last_result[$i][2]['img'] as $value2) {
                    if ($value2 == 'https://sitethemedata.com/v3/static/front/img/winner.png') {
                        $winnerclass = "winner_icon";
                        $html .= '<div class="casino-result-cards-item"><img class="' . $winnerclass . '" src="' . $value2 . '"></div>';
                    } else {
                        $html .= '<div class="casino-result-cards-item"><img src="' . $value2 . '"></div>';
                    }
                }
                $html .= '</div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-3">
                                <div class="casino-result-cards justify-content-start">
                                    <div class="d-inline-block">
                                        <h6>Player 10 - <span class="text-color-yellow1">' . $player3[1] . '</span></h6>';
                foreach ($last_result[$i][3]['img'] as $value3) {
                    if ($value3 == 'https://sitethemedata.com/v3/static/front/img/winner.png') {
                        $winnerclass = "winner_icon";
                        $html .= '<div class="casino-result-cards-item"><img class="' . $winnerclass . '" src="' . $value3 . '"></div>';
                    } else {
                        $html .= '<div class="casino-result-cards-item"><img src="' . $value3 . '"></div>';
                    }
                }
                $html .= '</div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-3">
                                <div class="casino-result-cards justify-content-start">
                                    <div class="d-inline-block">
                                        <h6>Player 11 - <span class="text-color-yellow1">' . $player4[1] . '</span></h6>';
                foreach ($last_result[$i][4]['img'] as $value4) {
                    if ($value4 == 'https://sitethemedata.com/v3/static/front/img/winner.png') {
                        $winnerclass = "winner_icon";
                        $html .= '<div class="casino-result-cards-item"><img class="' . $winnerclass . '" src="' . $value4 . '"></div>';
                    } else {
                        $html .= '<div class="casino-result-cards-item"><img src="' . $value4 . '"></div>';
                    }
                }
                $html .= '</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-3">
                        <div class="casino-result-desc">
                            <div class="casino-result-desc-item">
                                <div>Winner</div>
                                <div>' . $last_result[$i]['description']['winner'] . '</div>
                            </div>
                            <div class="casino-result-desc-item">
                                <div>Odd/Even</div>
                                <div>' . $last_result[$i]['description']['odd/even'][1] . '</div>
                            </div>
                            <div class="casino-result-desc-item">
                                <div></div>
                                <div>' . $last_result[$i]['description']['odd/even'][2] . '</div>
                            </div>
                            <div class="casino-result-desc-item">
                                <div>Black/Red</div>
                                <div>' . $last_result[$i]['description']['black/red'] . '</div>
                            </div>
                            <div class="casino-result-desc-item">
                                <div>Total</div>
                                <div>' . $last_result[$i]['description']['total'] . '</div>
                            </div>
                            <div class="casino-result-desc-item">
                                <div>Single</div>
                                <div>' . $last_result[$i]['description']['single'] . '</div>
                            </div>
                        </div>
                    </div>
                </div>';
            }
            $i++;
        }
        return $html;
    }

    public function getbaccaratLastResultpopup(Request $request)
    {
        $round = $request->round;
        $html = '';
        $i = 1;
        $last_result = app('App\Http\Controllers\RestApi')->getteen20LastResult("baccarat");
        foreach ($last_result as $key => $value) {
            if ($last_result[$i]['round_id'] == $round) {
                $html .= '<div class="casino_result_round">
                    <div>Round-Id: ' . $round . '</div>
                    <div>Match Time: ' . $last_result[$i]['match_time'] . '</div>
                </div>
                <div class="row row1">
                    <div class="col-12 col-lg-8">
                        <div class="casino-result-content">
                            <div class="casino-result-content-item text-center">
                                <div class="casino-result-cards">
                                    <div class="d-inline-block">
                                        <h4>Player</h4>';
                $winnerclass = '';
                foreach ($last_result[$i]['player_card'] as $value) {
                    if ($value == 'https://sitethemedata.com/v3/static/front/img/winner.png') {
                        $winnerclass = "winner_icon";
                        $html .= '<div class="casino-result-cards-item"><img class="' . $winnerclass . '"  src="' . $value . '"></div>';
                    } else {
                        $html .= '<div class="casino-result-cards-item"><img  src="' . $value . '"></div>';
                    }
                }
                $html .= '</div>
                                </div>
                            </div>
                            <div class="casino-result-content-diveder darkblue-bg"></div>
                            <div class="casino-result-content-item text-center">
                                <div class="casino-result-cards">
                                    <div class="d-inline-block">
                                        <h4>Banker</h4>';
                $winnerclass = '';
                foreach ($last_result[$i]['banker_card'] as $key => $value) {
                    if ($value == 'https://sitethemedata.com/v3/static/front/img/winner.png') {
                        $winnerclass = "winner_icon";
                        $html .= '<div class="casino-result-cards-item"><img class="' . $winnerclass . '"  src="' . $value . '"></div>';
                    } else {
                        $html .= '<div class="casino-result-cards-item"><img src="' . $value . '"></div>';
                    }
                }

                $html .= '</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4">
                        <div class="casino-result-desc blue-dark-bg-1">
                            <div class="casino-result-desc-item">
                                <div>Winner</div>
                                <div>' . $last_result[$i]['description']['Winner'] . '</div>
                            </div>
                            <div class="casino-result-desc-item">
                                <div>Winner Pair</div>
                                <div>' . $last_result[$i]['description']['Winner_pair'] . '</div>
                            </div>
                            <div class="casino-result-desc-item">
                                <div>Perfect</div>
                                <div>' . $last_result[$i]['description']['Perfect'] . '</div>
                            </div>
                            <div class="casino-result-desc-item">
                                <div>Either</div>
                                <div>' . $last_result[$i]['description']['Either'] . '</div>
                            </div>
                            <div class="casino-result-desc-item">
                                <div>Big/Small</div>
                                <div>' . $last_result[$i]['description']['Big/Small'] . '</div>
                            </div>
                        </div>
                    </div>
                </div>';
            }
            $i++;
        }
        return $html;
    }

    public function getdt202LastResultpopup(Request $request)
    {
        $round = $request->round;
        $html = '';
        $i = 1;
        $last_result = app('App\Http\Controllers\RestApi')->getteen20LastResult("dt202");
        foreach ($last_result as $key => $value) {
            if ($last_result[$i]['round_id'] == $round) {
                $html .= ' <div class="casino_result_round">
                    <div>Round-Id: ' . $round . '</div>
                    <div>Match Time: ' . $last_result[$i]['match_time'] . '</div>
                </div>

                <div class="row row1">
                    <div class="col-12 col-lg-8">
                        <div class="casino-result-content">
                            <div class="casino-result-content-item text-center">
                                <div class="casino-result-cards">
                                    <div class="d-inline-block">
                                        <h4>Dragon</h4>';
                $winnerclass = '';
                foreach ($last_result[$i]['cards']['a'] as $value) {
                    if ($value == 'https://sitethemedata.com/v3/static/front/img/winner.png') {
                        $winnerclass = "winner_icon";
                        $html .= '<div class="casino-result-cards-item"><img class="' . $winnerclass . '" src="' . $value . '"></div>';
                    } else {
                        $html .= '<div class="casino-result-cards-item"><img src="' . $value . '"></div>';
                    }
                }
                $html .= '</div>
                                </div>
                            </div>
                            <div class="casino-result-content-diveder darkblue-bg"></div>
                            <div class="casino-result-content-item text-center">
                                <div class="casino-result-cards">
                                <h4>Tiger</h4>';
                $winnerclass = '';
                foreach ($last_result[$i]['cards']['b'] as $value) {
                    if ($value == 'https://sitethemedata.com/v3/static/front/img/winner.png') {
                        $winnerclass = "winner_icon";
                        $html .= '<div class="d-inline-block">

                                        <div class="casino-result-cards-item"><img class="' . $winnerclass . '" src="' . $value . '"></div>
                                    </div>';
                    } else {
                        $html .= '<div class="d-inline-block">

                                        <div class="casino-result-cards-item"><img src="' . $value . '"></div>
                                    </div>';
                    }
                }
                $html .= '</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4">
                        <div class="casino-result-desc blue-dark-bg-1">
                            <div class="casino-result-desc-item">
                                <div>Winner</div>
                                <div>' . $last_result[$i]['description']['winner'] . '</div>
                            </div>
                            <div class="casino-result-desc-item">
                                <div>Pair</div>
                                <div>' . $last_result[$i]['description']['pair'] . '</div>
                            </div>
                            <div class="casino-result-desc-item">
                                <div>Odd/Even</div>
                                <div>' . $last_result[$i]['description']['odd/even'] . '</div>
                            </div>
                            <div class="casino-result-desc-item">
                                <div>Color</div>
                                <div>' . $last_result[$i]['description']['color'] . '</div>
                            </div>
                        </div>
                    </div>
                </div>';
            }
            $i++;
        }
        return $html;
    }

    public function getteen20LastResultpopup(Request $request)
    {
        $round = $request->round;
        $html = '';
        $i = 1;
        $last_result = app('App\Http\Controllers\RestApi')->getteen20LastResult("teen20");
        foreach ($last_result as $key => $value) {
            if ($last_result[$i]['round_id'] == $round) {
                $html .= '<div class="modal-body" id="appnedLastResult">
                    <div class="casino_result_round">
                        <div>Round-Id: ' . $round . '</div>
                        <div>Match Time: ' . $last_result[$i]['match_time'] . '</div>
                    </div>

                    <div class="row row1">
                        <div class="col-12 col-lg-8">
                            <div class="casino-result-content">
                                <div class="casino-result-content-item text-center">
                                    <div class="casino-result-cards">
                                        <div class="d-inline-block">
                                            <h4>Player A</h4>
                                            ';
                $winnerclass = '';
                foreach ($last_result[$i]['cards']['a'] as $key => $value) {
                    if ($value == 'https://sitethemedata.com/v3/static/front/img/winner.png') {
                        $winnerclass = "winner_icon";
                        $html .= '<div class="casino-result-cards-item "><img class="' . $winnerclass . '" src="' . $value . '"></div>';
                    } else {
                        $html .= '<div class="casino-result-cards-item "><img src="' . $value . '"></div>';
                    }
                }
                $html .= '
                                        </div>
                                    </div>
                                </div>
                                <div class="casino-result-content-diveder darkblue-bg"></div>
                                <div class="casino-result-content-item text-center">
                                    <div class="casino-result-cards">
                                        <div class="casino-result-cards-item"><img src="" class="winner_icon"></div>
                                        <div class="d-inline-block">
                                            <h4>Player B</h4>';
                $winnerclass = '';
                foreach ($last_result[$i]['cards']['b'] as $key => $value) {
                    if ($value == 'https://sitethemedata.com/v3/static/front/img/winner.png') {
                        $winnerclass = "winner_icon";
                        $html .= '<div class="casino-result-cards-item"><img class="' . $winnerclass . '" src="' . $value . '"></div>';
                    } else {
                        $html .= '<div class="casino-result-cards-item"><img src="' . $value . '"></div>';
                    }
                }
                $html .= '</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-4">
                            <div class="casino-result-desc blue-dark-bg-1">';
                foreach ($last_result[$i]['description'] as $key => $value) {
                    $html .= '<div class="casino-result-desc-item">
                                    <div>' . str_replace('_', ' ', $key) . '</div>
                                    <div>' . $value . '</div>
                                </div>';
                }
                $html .= ' </div>
                        </div>
                    </div>
                </div>';
            }
            $i++;
        }
        return $html;
    }

    public function getteen20LastResult()
    {
        $last_result = app('App\Http\Controllers\RestApi')->getteen20LastResult("teen20");
        $i = 1;
        $last_resultData = '';
        $count = 0;
        foreach ($last_result as $key => $value) {
            if ($count % 3 === 0) {
                $cls = 'text-color-red-1';
            } else {
                $cls = 'text-color-yellow1';
            }
            $last_resultData .= '<span class="resulta ' . $cls . '" onclick="openLastPopup(' . $last_result[$i]['round_id'] . ');">' . $last_result[$i]['type'] . '</span>';
            $i++;
            $count++;
        }
        $html = '';
        $html .= '';

        $html .= '~~';
        $html .= $last_resultData;
        return $html;
    }

    public function getCasino32cardb()
    {
        $casino_data = app('App\Http\Controllers\RestApi')->GetTeen20Data("32card");

        $html = '';

        if (!empty($casino_data) && $casino_data != 0) {
            $lockCls = '';
            $casinoCard = '';
            if ($casino_data['single']['switch'] == '0') {
                $lockCls = 'suspended-txt';
            }

            $html .= '<div class="playerblock">
                <div class="player_left">
                    <div class="casino_row_card32">
                        <div class="casino-name"></div>
                        <div class="casino_box casino_title_box">
                            <div class="casinobox-item"><span>Back</span></div>
                            <div class="casinobox-item"><span>Lay</span></div>
                        </div>
                    </div>
                    <div class="casino_row_card32">
                        <div class="casino-name white-bg2">
                            <b>Player 8</b>
                        </div>
                        <div class="casino_box">
                            <div class="casinobox-item backcasino lightblue-bg3 ' . $lockCls . '">
                                <span class="casino_odd_txt">' . $casino_data['Player8']['back'] . '</span>
                            </div>
                            <div class="casinobox-item laycasino lightpink-bg2 ' . $lockCls . '">
                                <span class="casino_odd_txt">' . $casino_data['Player8']['lay'] . '</span>
                            </div>
                        </div>
                    </div>
                    <div class="casino_row_card32">
                        <div class="casino-name white-bg2">
                            <b>Player 9</b>
                        </div>
                        <div class="casino_box">
                            <div class="casinobox-item backcasino lightblue-bg3 ' . $lockCls . '">
                                <span class="casino_odd_txt">' . $casino_data['Player9']['back'] . '</span>
                            </div>
                            <div class="casinobox-item laycasino lightpink-bg2 ' . $lockCls . '">
                                <span class="casino_odd_txt">' . $casino_data['Player9']['lay'] . '</span>
                            </div>
                        </div>
                    </div>
                    <div class="casino_row_card32">
                        <div class="casino-name white-bg2">
                            <b>Player 10</b>
                        </div>
                        <div class="casino_box">
                            <div class="casinobox-item backcasino lightblue-bg3 ' . $lockCls . '">
                                <span class="casino_odd_txt">' . $casino_data['Player10']['back'] . '</span>
                            </div>
                            <div class="casinobox-item laycasino lightpink-bg2 ' . $lockCls . '">
                                <span class="casino_odd_txt">' . $casino_data['Player10']['lay'] . '</span>
                            </div>
                        </div>
                    </div>
                    <div class="casino_row_card32">
                        <div class="casino-name white-bg2">
                            <b>Player 11</b>
                        </div>
                        <div class="casino_box">
                            <div class="casinobox-item backcasino lightblue-bg3 ' . $lockCls . '">
                                <span class="casino_odd_txt">' . $casino_data['Player11']['back'] . '</span>
                            </div>
                            <div class="casinobox-item laycasino lightpink-bg2 ' . $lockCls . '">
                                <span class="casino_odd_txt">' . $casino_data['Player11']['lay'] . '</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="player_center grey-bg"></div>

                <div class="player_right">
                    <div class="casino_row_card32">
                        <div class="casino-name"></div>
                        <div class="casino_box casino_title_box">
                            <div class="casinobox-item"><span>Odd</span></div>
                            <div class="casinobox-item"><span>Even</span></div>
                        </div>
                    </div>
                    <div class="casino_row_card32">
                        <div class="casino-name white-bg2">
                            <b>Player 8</b>
                        </div>
                        <div class="casino_box">
                            <div class="casinobox-item backcasino lightblue-bg3 ' . $lockCls . '">
                                <span class="casino_odd_txt">' . $casino_data['Player8']['odd'] . '</span>
                            </div>
                            <div class="casinobox-item laycasino lightpink-bg2 ' . $lockCls . '">
                                <span class="casino_odd_txt">' . $casino_data['Player8']['even'] . '</span>
                            </div>
                        </div>
                    </div>
                    <div class="casino_row_card32">
                        <div class="casino-name white-bg2">
                            <b>Player 9</b>
                        </div>
                        <div class="casino_box">
                            <div class="casinobox-item backcasino lightblue-bg3 ' . $lockCls . '">
                                <span class="casino_odd_txt">' . $casino_data['Player9']['odd'] . '</span>
                            </div>
                            <div class="casinobox-item laycasino lightpink-bg2 ' . $lockCls . '">
                                <span class="casino_odd_txt">' . $casino_data['Player9']['even'] . '</span>
                            </div>
                        </div>
                    </div>
                    <div class="casino_row_card32">
                        <div class="casino-name white-bg2">
                            <b>Player 10</b>
                        </div>
                        <div class="casino_box">
                            <div class="casinobox-item backcasino lightblue-bg3 ' . $lockCls . '">
                                <span class="casino_odd_txt">' . $casino_data['Player10']['odd'] . '</span>
                            </div>
                            <div class="casinobox-item laycasino lightpink-bg2 ' . $lockCls . '">
                                <span class="casino_odd_txt">' . $casino_data['Player10']['even'] . '</span>
                            </div>
                        </div>
                    </div>
                    <div class="casino_row_card32">
                        <div class="casino-name white-bg2">
                            <b>Player 11</b>
                        </div>
                        <div class="casino_box">
                            <div class="casinobox-item backcasino lightblue-bg3 ' . $lockCls . '">
                                <span class="casino_odd_txt">' . $casino_data['Player11']['odd'] . '</span>
                            </div>
                            <div class="casinobox-item laycasino lightpink-bg2 ' . $lockCls . '">
                                <span class="casino_odd_txt">' . $casino_data['Player11']['even'] . '</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>';
            $html .= '<div class="playerblock mt-4">
                <div class="player_left">
                    <div class="casino_row_card32">
                        <div class="casino-name"></div>
                        <div class="casino_box casino_title_box">
                            <div class="casinobox-item"><span>Back</span></div>
                            <div class="casinobox-item"><span>Lay</span></div>
                        </div>
                    </div>
                    <div class="casino_row_card32">
                        <div class="casino-name white-bg2">
                            <b>Any 3 Card Black</b>
                        </div>
                        <div class="casino_box">
                            <div class="casinobox-item backcasino lightblue-bg3 ' . $lockCls . '">
                                <span class="casino_odd_txt">' . $casino_data['Any 3 Card Black']['back'] . '</span>
                            </div>
                            <div class="casinobox-item laycasino lightpink-bg2 ' . $lockCls . '">
                                <span class="casino_odd_txt">' . $casino_data['Any 3 Card Black']['lay'] . '</span>
                            </div>
                        </div>
                    </div>
                    <div class="casino_row_card32">
                        <div class="casino-name white-bg2">
                            <b>Any 3 Card Red</b>
                        </div>
                        <div class="casino_box">
                            <div class="casinobox-item backcasino lightblue-bg3 ' . $lockCls . '">
                                <span class="casino_odd_txt">' . $casino_data['Any 3 Card Red']['back'] . '</span>
                            </div>
                            <div class="casinobox-item laycasino lightpink-bg2 ' . $lockCls . '">
                                <span class="casino_odd_txt">' . $casino_data['Any 3 Card Red']['lay'] . '</span>
                            </div>
                        </div>
                    </div>
                    <div class="casino_row_card32">
                        <div class="casino-name white-bg2">
                            <b>Two Black Two Red</b>
                        </div>
                        <div class="casino_box">
                            <div class="casinobox-item backcasino lightblue-bg3 ' . $lockCls . '">
                                <span class="casino_odd_txt">' . $casino_data['Two Black Two Red']['back'] . '</span>
                            </div>
                            <div class="casinobox-item laycasino lightpink-bg2 ' . $lockCls . '">
                                <span class="casino_odd_txt">' . $casino_data['Two Black Two Red']['lay'] . '</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="player_center grey-bg"></div>

                <div class="player_right">
                    <div class="casino_row_card32">
                        <div class="casino-name"></div>
                        <div class="casino_box casino_title_box">
                            <div class="casinobox-item"><span>Back</span></div>
                            <div class="casinobox-item"><span>Back</span></div>
                        </div>
                    </div>
                    <div class="casino_row_card32">
                        <div class="casino-name white-bg2">
                            <b>8 &amp; 9 Total</b>
                        </div>
                        <div class="casino_box">
                            <div class="casinobox-item backcasino lightblue-bg3 ' . $lockCls . '">
                                <span class="casino_odd_txt">' . $casino_data['8 & 9 Total']['back'][1] . '</span>
                            </div>
                            <div class="casinobox-item backcasino lightblue-bg3 ' . $lockCls . '">
                                <span class="casino_odd_txt">' . $casino_data['8 & 9 Total']['back'][2] . '</span>
                            </div>
                        </div>
                    </div>
                    <div class="casino_row_card32">
                        <div class="casino-name white-bg2">
                            <b>10 &amp; 11 Total</b>
                        </div>
                        <div class="casino_box">
                            <div class="casinobox-item backcasino lightblue-bg3 ' . $lockCls . '">
                                <span class="casino_odd_txt">' . $casino_data['10 & 11 Total']['back'][1] . '</span>
                            </div>
                            <div class="casinobox-item backcasino lightblue-bg3 ' . $lockCls . '">
                                <span class="casino_odd_txt">' . $casino_data['10 & 11 Total']['back'][2] . '</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>';
            $html .= '<div class="text-center mt-4"><b>' . $casino_data['single']['switch'] . '</b></div>
            <div class="card32_extrabox playerblock">
                <div class="casino_box">';
            foreach ($casino_data['single']['odd'] as $value) {


                $html .= '<div class="casinobox-item backcasino lightblue-bg-3 ' . $lockCls . '">
                        <span class="casino_odd_txt">' . $value . '</span>
                    </div>';
            }
            $html .= '</div>
            </div>';
        }
        $html .= '~~';
        $html .= $casinoCard;
        return $html;
    }

    public function get32cardvideo()
    {
        $casino_data = app('App\Http\Controllers\RestApi')->Get32cardvideo();
        $html = '';

        if (!empty($casino_data) && $casino_data != 0) {
            $name1 = '';
            $name2 = '';
            $name3 = '';
            $name4 = '';

            if ($casino_data['cards'][1]['name'] != null) {
                $name = explode(':', $casino_data['cards'][1]['name']);
                $name1 = $name[1];
            }
            if ($casino_data['cards'][2]['name'] != null) {
                $name = explode(':', $casino_data['cards'][2]['name']);
                $name2 = $name[1];
            }
            if ($casino_data['cards'][3]['name'] != null) {
                $name = explode(':', $casino_data['cards'][3]['name']);
                $name3 = $name[1];
            }
            if ($casino_data['cards'][4]['name'] != null) {
                $name = explode(':', $casino_data['cards'][4]['name']);
                $name4 = $name[1];
            }
            $timer = $casino_data['timer'];
            $roundId = $casino_data['game_round_id'];
            $html .= '<div class="casinocards_shuffle"><i class="fas text-color-grey-3 fa-grip-lines-vertical"></i></div>

            <div class="casinocards-container text-color-white" id="casinoCard">
                <div>
                    <div class="dealer_name w-100 mb-1">
                        <span>Player 8: </span> <span>' . $name1 . '</span>
                    </div>
                <div><span>';
            foreach ($casino_data['cards'][1]['card'] as $value) {

                $html .= '<img src="' . $value . '">';
            }
            $html .= '</span></div>
                </div>
                <div>
                    <div class="dealer_name w-100 mb-1">
                        <span>Player 9: </span> <span>' . $name2 . '</span>
                    </div>
                    <div><span>';
            foreach ($casino_data['cards'][2]['card'] as $value) {

                $html .= '<img src="' . $value . '" alt="">';
            }
            $html .= '</span></div>
                </div>
                <div>
                    <div class="dealer_name w-100 mb-1">
                        <span>Player 10: </span> <span>' . $name3 . '</span>
                    </div>
                    <div><span>';
            foreach ($casino_data['cards'][3]['card'] as $value) {
                $html .= '<img src="' . $value . '">';
            }
            $html .= '</span></div>
                </div>
                <div>
                    <div class="dealer_name w-100 mb-1">
                        <span class="text-color-yellow1">Player 11: </span> <span>' . $name4 . '</span>
                    </div>
                    <div><span>';
            foreach ($casino_data['cards'][4]['card'] as $value) {
                $html .= '<img src="' . $value . '">';
            }
            $html .= '</span></div>
                </div>
            </div>';
        }
        return $html . '~~' . $timer . '~~' . $roundId;
    }

    public function getbaccarat()
    {
        $casino_data = app('App\Http\Controllers\RestApi')->GetTeen20Data("baccarat");
        $html = '';
        $timer = $casino_data['timer'];
        $roundId = $casino_data['game_round_id'];

        if (!empty($casino_data) && $casino_data != 0) {
            $lockCls = '';
            if ($casino_data['timer'] <= '4') {
                $lockCls = 'suspended-txt';
            }
            $html .= '';
            $html .= '<div class="baccarat_bets_block" >
            <div class="baccarat_odds">
                <div class="baccarat_odds_items casinobox-item ' . $lockCls . '">
                    <div class="baccarat_odd_name dark-green-bg1">Perferct Pair ' . $casino_data['baccarat_odds']['prefect_pair'] . '</div>
                </div>
                <div class="baccarat_odds_items casinobox-item ' . $lockCls . '">
                    <div class="baccarat_odd_name dark-green-bg1">Big  ' . $casino_data['baccarat_odds']['big'] . '</div>
                </div>
                <div class="baccarat_odds_items casinobox-item ' . $lockCls . '">
                    <div class="baccarat_odd_name dark-green-bg1">Small  ' . $casino_data['baccarat_odds']['small'] . '</div>
                </div>
                <div class="baccarat_odds_items casinobox-item ' . $lockCls . '">
                    <div class="baccarat_odd_name dark-green-bg1">Either Pair  ' . $casino_data['baccarat_odds']['either Pair'] . '</div>
                </div>
            </div>
            <div class="baccarat_bets">
                <div class="player_pair_box">
                    <div class="baccarat_bets_name cyan-bg casinobox-item ' . $lockCls . '">
                        <div>Player Pair</div>
                        <div class="mb-0">' . $casino_data['player_pair'] . '</div>
                    </div>
                </div>
                <div class="player_box">
                    <div class="baccarat_bets_name cyan-bg casinobox-item">
                        <div class="' . $lockCls . '">' . $casino_data['bet_name_1'] . '</div>
                        <div class="mb-0">';
            foreach ($casino_data['player_cards'] as $key => $value) {
                $html .= '<img src="' . $value . '" alt="img">';
            }

            $html .= '</div>
                    </div>
                </div>
                <div class="tie_box">
                    <div class="baccarat_bets_name green-bg-1 casinobox-item ' . $lockCls . '">
                        <div>TIE</div>
                        <div class="mb-0">' . $casino_data['tie'] . '</div>
                    </div>
                </div>
                <div class="banker_box">
                    <div class="baccarat_bets_name red-bg1 casinobox-item">
                        <div class="' . $lockCls . '">' . $casino_data['bet_name_2'] . '</div>
                        <div class="mb-0">';
            foreach ($casino_data['banker_cards'] as $key => $value) {
                $html .= '<img src="' . $value . '" alt="img">';
            }
            $html .= '</div>
                    </div>
                </div>
                <div class="banker_pair_box">
                    <div class="baccarat_bets_name red-bg1 casinobox-item ' . $lockCls . '">
                        <div>Banker Pair</div>
                        <div class="mb-0">' . $casino_data['banker_pair'] . '</div>
                    </div>
                </div>
            </div>
        </div>';
        }

        $html .= '~~';
        $html .= $timer;
        $html .= '~~';
        $html .= $roundId;
        return $html;
    }

    public function getCasinoab20()
    {
        $casino = Casino::where('casino_name', 'ab20')->first();
        $casino_data = app('App\Http\Controllers\RestApi')->GetTeen20Data("ab20");
        $html = '';
        $casinoCard = '';
        $casinoCardb = '';

        $timer = $casino_data['timer'];
        $roundId = $casino_data['game_round_id'];

        foreach ($casino_data['andar_cards'] as $value) {
            $casinoCard .= '<span><img src="' . $value . '"></span>';
        }
        foreach ($casino_data['bahar_cards'] as $value) {
            $casinoCardb .= '<span><img src="' . $value . '"></span>';
        }

        if (!empty($casino_data) && $casino_data != 0) {
            $html .= '<div class="andarbahar_block">
                <div class="andar_card_box">
                    <h5 class="w-100 text-center text-color-red1">Andar</h5>';
            foreach ($casino_data['andar_odds'] as $key => $value) {
                $html .= '<div class="ab_card_items">
                            <div class="card_image">
                                <img src="' . $value . '" alt="img">
                            </div>
                        </div>';
            }

            $html .= '</div>
                <div class="bahar_card_box">
                    <h5 class="w-100 text-center text-color-red1">Bahar</h5>';
            foreach ($casino_data['bahar_odds'] as $key => $value) {
                $html .= '<div class="ab_card_items">
                        <div class="card_image">
                            <img src="' . $value . '" alt="img">
                        </div>
                    </div>';
            }
            $html .= '</div>
            </div>';
        }

        $html .= '~~';
        $html .= $timer;
        $html .= '~~';
        $html .= $roundId;
        $html .= '~~';
        $html .= $casinoCard;
        $html .= '~~';
        $html .= $casinoCardb;
        return $html;
    }

    public function getCasinodt202()
    {
        $casino_data = app('App\Http\Controllers\RestApi')->GetTeen20Data("dt202");
        $html = '';

        $timer = $casino_data['timer'];
        $roundId = $casino_data['game_round_id'];
        $lockCls = '';
        $casinoCard = '';
        if ($casino_data['timer'] <= '4') {
            $lockCls = 'suspended-txt';
        }
        if ($casino_data['cards'] != null) {
            foreach ($casino_data['cards'] as $value) {
                $casinoCard .= '<span class="text-color-white"><img src="' . $value . '"></span>';
            }
        }
        if (!empty($casino_data) && $casino_data != 0) {
            $html .= '<div class="casino_dragontiger_block">
                    <div class="dragon_box red-rgb ' . $lockCls . '">
                        <div class="dflex_book">
                            <b>Dragon</b>
                            <span>' . $casino_data['dragon']['odds1'][0] . '</span>
                        </div>
                        <div class="dflex_odds">
                            <span><b>' . $casino_data['dragon']['odds1'][1] . '</b></span>
                        </div>
                    </div>
                    <div class="tie_box green-bg-1 ' . $lockCls . '">
                        <b>Tie</b>
                        <div class="dflex_odds">
                            <span><b>' . $casino_data['tie'][0] . '</b></span>
                            <span>' . $casino_data['tie'][1] . '</span>
                        </div>
                    </div>
                    <div class="tiger_box yellow-rgb ' . $lockCls . '">
                        <div class="dflex_book">
                            <b>Tiger</b>
                            <span>' . $casino_data['tiger']['odds1'][0] . '</span>
                        </div>
                        <div class="dflex_odds">
                            <span><b>' . $casino_data['tiger']['odds1'][1] . '</b></span>
                        </div>
                    </div>
                    <div class="pair_box dark-green-bg ' . $lockCls . '">
                        <div><b>Pair</b></div>
                        <div class="text-center dflex_odds">
                            <span><b>' . $casino_data['pair'][0] . '</b></span>
                            <span>' . $casino_data['pair'][1] . '</span>
                        </div>
                    </div>
                </div>

                <div class="playerblock">
                    <div class="player_left">
                        <div class="casino-name text-color-red-1"><b>Dragon</b></div>
                        <div class="casino_box casino_title_box">
                            <div class="casinobox-item"><span>' . $casino_data['dragon']['odds2'][1]['name'] . '</span></div>
                            <div class="casinobox-item"><span>' . $casino_data['dragon']['odds2'][2]['name'] . '</span></div>
                            <div class="casinobox-item"><span>' . $casino_data['dragon']['odds2'][3]['name'] . '</span></div>
                            <div class="casinobox-item"><span>' . $casino_data['dragon']['odds2'][4]['name'] . '</span></div>
                        </div>
                        <div class="casino_box">
                            <div class="casinobox-item backcasino lightblue-bg3 ' . $lockCls . '">
                                <span class="casino_odd_txt">' . $casino_data['dragon']['odds2'][1]['value'] . '</span>
                            </div>
                            <div class="casinobox-item backcasino lightblue-bg3 ' . $lockCls . '">
                                <span class="casino_odd_txt">' . $casino_data['dragon']['odds2'][2]['value'] . '</span>
                            </div>
                            <div class="casinobox-item backcasino lightblue-bg3 ' . $lockCls . '">
                                <span>';
            foreach ($casino_data['dragon']['odds2'][3]['value'] as $value) {
                $html .= '<img src="' . $value . '" alt="img">';
            }
            $html .= '</span>
                            </div>
                            <div class="casinobox-item backcasino lightblue-bg3 ' . $lockCls . '">
                                <span>';
            foreach ($casino_data['dragon']['odds2'][4]['value'] as $value) {
                $html .= '<img src="' . $value . '" alt="img">';
            }
            $html .= '</span>
                            </div>
                        </div>
                        <div class="casino_cards_block w-100 mt-3">
                            <div class="casino_cards_title text-center">
                                <b>' . $casino_data['dragon']['odds3']['switch'] . '</b>
                            </div>
                            <div class="casino_card_content mt-1">';
            foreach ($casino_data['dragon']['odds3']['cards'] as $value) {
                $html .= '<div class="casino_card_items ' . $lockCls . '">
                                    <div class="card_image">
                                        <img src="' . $value . '" alt="">
                                    </div>
                                </div>';
            }
            $html .= '</div>
                        </div>
                    </div>

                    <div class="player_center grey-bg"></div>

                    <div class="player_right">
                        <div class="casino-name text-color-yellow1"><b>Tigar</b></div>
                        <div class="casino_box casino_title_box">
                            <div class="casinobox-item"><span>' . $casino_data['tiger']['odds2'][1]['name'] . '</span></div>
                            <div class="casinobox-item"><span>' . $casino_data['tiger']['odds2'][2]['name'] . '</span></div>
                            <div class="casinobox-item"><span>' . $casino_data['tiger']['odds2'][3]['name'] . '</span></div>
                            <div class="casinobox-item"><span>' . $casino_data['tiger']['odds2'][4]['name'] . '</span></div>
                        </div>
                        <div class="casino_box">
                            <div class="casinobox-item backcasino lightblue-bg3 ' . $lockCls . '">
                                <span class="casino_odd_txt">' . $casino_data['tiger']['odds2'][1]['value'] . '</span>
                            </div>
                            <div class="casinobox-item backcasino lightblue-bg3 ' . $lockCls . '">
                                <span class="casino_odd_txt">' . $casino_data['tiger']['odds2'][2]['value'] . '</span>
                            </div>
                            <div class="casinobox-item backcasino lightblue-bg3 ' . $lockCls . '">
                                <span>';
            foreach ($casino_data['tiger']['odds2'][3]['value'] as $key => $value) {

                $html .= '<img src="' . $value . '" alt="img">';
            }
            $html .= '</span>
                            </div>
                            <div class="casinobox-item backcasino lightblue-bg3 ' . $lockCls . '">
                                <span>';
            foreach ($casino_data['tiger']['odds2'][4]['value'] as $key => $value) {
                $html .= '<img src="' . $value . '" alt="img">';
            }
            $html .= '</span>
                            </div>
                        </div>
                        <div class="casino_cards_block w-100 mt-3">
                            <div class="casino_cards_title text-center">
                                <b>' . $casino_data['tiger']['odds3']['switch'] . '</b>
                            </div>
                            <div class="casino_card_content mt-1">';
            foreach ($casino_data['tiger']['odds3']['cards'] as $value) {
                $html .= '<div class="casino_card_items ' . $lockCls . '">
                                    <div class="card_image">
                                        <img src="' . $value . '" alt="img">
                                    </div>
                                </div>';
            }
            $html .= '</div>
                        </div>
                    </div>
                </div>';
        }

        $html .= '~~';
        $html .= $timer;
        $html .= '~~';
        $html .= $roundId;
        $html .= '~~';
        $html .= $casinoCard;
        return $html;
    }

    public function getCasinoDetailAjaxCall($id)
    {
        $getUserCheck = Session::get('playerUser');
        if (!empty($getUserCheck)) {
            $getUser = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
        }

        $casino = Casino::where('id', $id)->first();
        $casino_data = app('App\Http\Controllers\RestApi')->GetCasinoData($casino->casino_name);

//        echo __FILE__." at line ".__LINE__."<br>";echo "<pre>";print_r($casino_data);die();
        $html = '';
        $lockCls = '';
        $casinoCard = '';
        $casinoCardb = '';
        if (!empty($casino_data) && isset($casino_data['data']) && isset($casino_data['data']['t1']) && isset($casino_data['data']['t1'][0])) {
            $timer = $casino_data['data']['t1'][0]['autotime'];
            $miD = explode(".",$casino_data['data']['t1'][0]['mid']);

            $roundId = end($miD);

            if ($casino_data['data']['t2'][0]['gstatus'] == '0') {
                $lockCls = 'suspended-txt';
            }

            $cardBasePath = asset('asset/front/img/cards')."/";

            $casinoCard .= '<span class="text-color-white"><img src="' . $cardBasePath.$casino_data['data']['t1'][0]['C1'] . '.png"></span>';
            $casinoCard .= '<span class="text-color-white"><img src="' . $cardBasePath.$casino_data['data']['t1'][0]['C2'] . '.png"></span>';
            $casinoCard .= '<span class="text-color-white"><img src="' . $cardBasePath.$casino_data['data']['t1'][0]['C3'] . '.png"></span>';
            $casinoCardb .= '<div class="card_con">';
            $casinoCardb .= '<span class="text-color-white"><img src="' . $cardBasePath. $casino_data['data']['t1'][0]['C4'] . '.png"></span>';
            $casinoCardb .= '<span class="text-color-white"><img src="' . $cardBasePath. $casino_data['data']['t1'][0]['C5'] . '.png"></span>';
            $casinoCardb .= '<span class="text-color-white"><img src="' . $cardBasePath. $casino_data['data']['t1'][0]['C6'] . '.png"></span>';
            $casinoCardb .= '</div>';

            $html .= ' <div class="playerblock" >
            <div class="player_left">
                <div class="casino-name text-color-red"><b>'.$casino_data['data']['t2'][0]['nation'].'</b></div>
                <div class="casino_box casino_title_box">';
//            foreach ($casino_data['player_a_odds_1'] as $key => $value) {
//                $html .= ' <div class="casinobox-item"><span>' . str_replace("_", ' ', $key) . '</span></div>';
//            }

            $html .= '</div>
                <div class="casino_box">';
                $casino_bet = CasinoBet::where('casino_name', 'teen20')->where('user_id', $getUser->id)->where('team_name', 'player_a')->first();
                $bet_stake = '';
                if (!empty($casino_bet)) {
                    $bet_stake = $casino_bet->stake_value;
                }
                $html .= '<div class="casinobox-item backcasino lightblue-bg3 ' . $lockCls . '"><span class="casino_odd_txt" data-team="player_a" data-val="' . $casino_data['data']['t2'][0]['rate'] . '"  onclick="opnForm(this);">' . $casino_data['data']['t2'][0]['rate'] . '</span> <span class="bet_PLAYER_A towin text-color-green" id="bet_PLAYER_A">' . $bet_stake . '</span></div>';
            $html .= '</div>
                <div class="casino_rb_box">
                    <div class="casinorb_items">
                        <div class="casinorb_content backcasino lightblue-bg3 ' . $lockCls . '">
                            <div data-team="PLAYER A BLACK" data-val="' . $casino_data['data']['t2'][1]['rate'] . '" onclick="opnForm(this);">
                                <img src="' . asset('asset/front/img/spade.png') . '" alt="">
                                <img src="' . asset('asset/front/img/club.png') . '" alt="">
                            </div>
                            <div data-team="PLAYER A BLACK" data-val="' . $casino_data['data']['t2'][1]['rate'] . '" onclick="opnForm(this);">
                                <span class="casino_odd_txt">' . $casino_data['data']['t2'][1]['rate'] . '</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="player_center grey-bg"></div>
            <div class="player_right">
                <div class="casino-name text-color-yellow1"><b>'.$casino_data['data']['t2'][2]['nation'].'</b></div>
                <div class="casino_box casino_title_box">';
//            foreach ($casino_data['player_b_odds_1'] as $key => $value) {
//                $html .= ' <div onclick="opnForm(this);" class="casinobox-item"><span>' . str_replace("_", ' ', $key) . '</span></div>';
//            }
            $html .= '</div>
                <div class="casino_box">';
                $html .= '<div class="casinobox-item backcasino lightblue-bg3 ' . $lockCls . '"><span class="casino_odd_txt" data-team="player_b" data-val="' . $casino_data['data']['t2'][2]['rate'] . '"  onclick="opnForm(this);">' . $casino_data['data']['t2'][2]['rate'] . '</span></div>';

            $html .= '</div>
                <div class="casino_rb_box">
                    <div class="casinorb_items">
                        <div class="casinorb_content backcasino lightblue-bg3 ' . $lockCls . '">
                            <div data-team="PLAYER B BLACK" data-val="' . $casino_data['data']['t2'][3]['rate'] . '" onclick="opnForm(this);">
                                <img src="' . asset('asset/front/img/spade.png') . '" alt="">
                                <img src="' . asset('asset/front/img/club.png') . '" alt="">
                            </div>
                            <div data-team="PLAYER B BLACK" data-val="' . $casino_data['data']['t2'][3]['rate'] . '" onclick="opnForm(this);">
                                <span class="casino_odd_txt">' .    $casino_data['data']['t2'][3]['rate'] . '</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>';
        }
        $html .= '~~';
        $html .= $timer;
        $html .= '~~';
        $html .= $roundId;
        $html .= '~~';
        $html .= $casinoCard;
        $html .= '~~';
        $html .= $casinoCardb;
        return $html;
    }
}
