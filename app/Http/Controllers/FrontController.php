<?php
namespace App\Http\Controllers;

use App\CasinoBet;
use App\UsersFavMatch;
use Illuminate\Http\Request;
use App\Sport;
use App\setting;
use App\Match;
use App\Casino;
use GuzzleHttp\Client;
use Carbon\Carbon;
use App\MyBets;
use Auth;
use DB;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Session;
use App\User;
use Illuminate\Support\Facades\Hash;
use App\ManageTv;
use App\UserDeposit;
use App\FancyResult;
use App\UserStake;
use App\CreditReference;
use App\SocialMedia;
use App\UserExposureLog;
use App\Banner;

class FrontController extends Controller
{
    public function index()
    {
        $mntnc = setting::first();
        $banner = Banner::get();
        $settings = setting::first();
        if (!empty($mntnc->maintanence_msg)) {
            $msg = $mntnc->maintanence_msg;
            return view('backpanel/maintanence', compact('msg'));
        } else {
            $casino = Casino::get();
            $socialdata = SocialMedia::first();
            return view('front.index', compact('casino', 'socialdata', 'banner', 'settings'));
        }

    }

    public function setMinMax(Request $request)
    {
        $matchId = $request->match_id;
        $matchdata = Match::find($matchId);
        return response()->json(array('result' => $matchdata));
    }

    public function myaccount(Request $request)
    {
        $getUserCheck = Session::get('playerUser');
        if (!empty($getUserCheck)) {
            $logindata = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
        }
        if (!empty($logindata)) {
            return view('front/myaccount');

        } else {
            return redirect()->route('front');
        }

    }

    public function multiaccountlogout(Request $request)
    {
        $sessionData = Session::get('playerUser');
        $checkstatus = User::where('id', $sessionData->id)->first();

        if ($checkstatus->token_val != $sessionData->token_val) {
            $request->session()->forget(['playerUser']);
            return response()->json(array('result' => 'error'));
        }

        if ($checkstatus->status == 'suspend') {
            Session::forget('playerUser');
            return response()->json(array('result' => 'error'));
        }

        return response()->json(array('result' => 'success'));
    }

    public function frontLogout(Request $request)
    {
        $sessionData = Session::get('playerUser');
        if (!empty($sessionData)) {
            $checkstatus = User::where('id', $sessionData->id)->where('check_login', 1)->first();
            $checkstatus->check_login = 0;
            $checkstatus->update();


        }
        session()->forget('playerUser');
        //$checkstatus=User::where('id',$sessionData->id)->first();

        //return view('front.index',compact('casino','banner'));
        return redirect()->route('front');
    }

    public function casinoDetail($id)
    {
        $casino = Casino::find($id);

        $getUser = Session::get('playerUser');
        if (empty($getUser)) {
            return redirect('/');
        }

        $bets = CasinoBet::where("user_id",$getUser->id)->where('casino_name',$casino->casino_name)->whereNull('winner')->get();


        $playerProfit = [];
        $totalProfitPlayers =  CasinoBet::where("user_id",$getUser->id)->where('casino_name',$casino->casino_name)->whereNull('winner')->groupBy('team_name')->get();
        foreach ($totalProfitPlayers as $team){
            $playerProfit[$team->team_sid] = CasinoBet::where("user_id",$getUser->id)->where('casino_name',$casino->casino_name)->where('team_name',$team->team_name)->whereNull('winner')->sum('casino_profit');
        }

        return view('front.casinoDetail', compact('casino','bets','playerProfit'));
    }

    public function getCasinoteen20()
    {
        $casino_data = app('App\Http\Controllers\RestApi')->GetTeen20Data();
        return view('front.' . $casino->casino_name, compact('casino'));
    }

    function number_format_short($n, $precision = 1)
    {
        if ($n < 900) {
            // 0 - 900
            $n_format = number_format($n, $precision);
            $suffix = '';
        } else if ($n < 900000) {
            // 0.9k-850k
            $n_format = number_format($n / 1000, $precision);
            $suffix = 'K';
        } else if ($n < 900000000) {
            // 0.9m-850m
            $n_format = number_format($n / 1000000, $precision);
            $suffix = 'M';
        } else if ($n < 900000000000) {
            // 0.9b-850b
            $n_format = number_format($n / 1000000000, $precision);
            $suffix = 'B';
        } else {
            // 0.9t+
            $n_format = number_format($n / 1000000000000, $precision);
            $suffix = 'T';
        }
        // Remove unecessary zeroes after decimal. "1.0" -> "1"; "1.00" -> "1"
        // Intentionally does not affect partials, eg "1.50" -> "1.50"
        if ($precision > 0) {
            $dotzero = '.' . str_repeat('0', $precision);
            $n_format = str_replace($dotzero, '', $n_format);
        }
        return $n_format . $suffix;
    }

    public function matchDetail($id)
    {
        //echo"dfdf";exit;
        $getUserCheck = Session::get('playerUser');

        $logindata = '';
        if (!empty($getUserCheck)) {
            $logindata = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
        }

        $bet_model = '';
        if ($logindata) {
            $stkdata = UserStake::where('user_id', $logindata->id)->first();
            $stkval = json_decode($stkdata->stake);
        } else {
            $stkval = array('100', '200', '300', '400', '500', '600');
        }


        $match = Match::where('event_id',$id)->first();

        if(empty($match)){
            return redirect('/');
        }

        $inplay = '';
        $match_data = '';
        $matchId = $match->match_id;
        $matchname = $match->matchname;
        $eventId = $match->event_id;

        // $match_data=app('App\Http\Controllers\RestApi')->DetailCall($matchId,$eventId,$match->sports_id);

        $match_data = app('App\Http\Controllers\RestApi')->getSingleMatchData($eventId, $matchId, $match->sports_id);

        if (empty($match_data)) {
            return redirect()->back()->with('error', 'No data found!');
        }

        if($match->sports_id == 4){
            if (!isset($match_data['t1']) || (isset($match_data['t1']) && $match_data['t1']==null)) {
                return redirect()->back()->with('error', 'No data found!');
            }
        }else if($match->sports_id == 4 || $match->sports_id == 1){
            if (!isset($match_data[0]) || (isset($match_data[0]) && !isset($match_data[0]['runners']))) {
                return redirect()->back()->with('error', 'No data found!');
            }
        }

        $my_placed_bets = array();
        $total_todays_bet = 0;
        $match_name_bet = array();
        $my_placed_bets_all = array();
        $placed_bet_match_list = '';
        $getUserCheck = Session::get('playerUser');
        if (!empty($getUserCheck)) {
            $sessionData = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
        }

        if (!empty($sessionData)) {
            $getUserCheck = Session::get('playerUser');
            if (!empty($getUserCheck)) {
                $getUser = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
            }

            $userId = $getUser->id;
            //echo $userId;
            //$my_placed_bets_all = MyBets::where('user_id',$userId)->where('match_id',$match->event_id)->where('isDeleted',0)->where('result_declare',0)->orderby('id','DESC')->get(); //for particular match's bet
            //DB::enableQueryLog();
            $placed_bet_match_list = DB::table('my_bets')
                ->select('my_bets.match_id', 'match.match_name', 'match.id')
                ->join('match', 'match.event_id', '=', 'my_bets.match_id')
                ->where('my_bets.user_id', $userId)->where('my_bets.isDeleted', 0)->where('my_bets.result_declare', 0)
                ->whereNull('match.winner')
                ->groupBy("my_bets.match_id")
                ->orderby('id', 'DESC')
                ->get();
            //dd(DB::getQueryLog());

            //echo"<pre>";print_r($placed_bet_match_list);exit;

            $my_placed_bets_all = MyBets::where('user_id', $userId)->where('isDeleted', 0)->where('result_declare', 0)->orderby('id', 'DESC')->get();

            $match_name_bet = array();
            $i = 0;
            $event_array = array();
            $bet_type = array();
        }

        //odds data
        $matchtype = $match->sports_id;
        $match_id = $match->id;
        $sport = Sport::where('sId', $matchtype)->first();
        $matchList = Match::where('id', $match_id)->where('status', 1)->first();
        $match_m = $matchList->suspend_m;
        $matchtype = $sport->id;
        $matchId = $match->match_id;
        $matchname = $match->match_name;
        $event_id = $match->event_id;
        $team = explode(" v ", strtolower($matchname));
        $sport_id = $matchList->sports_id;

        $min_bet_odds_limit = $matchList->min_bet_odds_limit;
        $max_bet_odds_limit = $matchList->max_bet_odds_limit;

        $oddsLimit = [];
        $oddsLimit['min_bet_odds_limit'] = $matchList->min_bet_odds_limit;
        $oddsLimit['max_bet_odds_limit'] = $matchList->max_bet_odds_limit;


        $team1_bet_total = '';
        $team1_bet_class = '';

        $team2_bet_total = '';
        $team2_bet_class = '';

        $team_draw_bet_total = '';
        $team_draw_bet_class = '';

        $getUserCheck = Session::get('playerUser');
        if (!empty($getUserCheck)) {
            $sessionData = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
        }

        $total_team_count = 0;
        if (!empty($sessionData)) {
            $getUserCheck = Session::get('playerUser');
            if (!empty($getUserCheck)) {
                $getUser = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
            }

            $userId = $getUser->id;
            $my_placed_bets = MyBets::where('user_id', $userId)->where('match_id', $event_id)->where('bet_type', 'ODDS')->where('isDeleted', 0)->where('result_declare', 0)->orderby('id', 'DESC')->get();
            $team2_bet_total = 0;
            $team1_bet_total = 0;
            $team_draw_bet_total = 0;

            if (sizeof($my_placed_bets) > 0) {
                foreach ($my_placed_bets as $bet) {
                    $abc = json_decode($bet->extra, true);
                    $total_team_count = count($abc);
                    if (!empty($abc)) {
                        if (count($abc) >= 2) {
                            if (array_key_exists("teamname1", $abc) && array_key_exists("teamname2", $abc)) {
                                //bet on draw
                                if ($bet->bet_side == 'back') {
                                    $team1_bet_total = $team1_bet_total - $bet->exposureAmt;
                                    if (count($abc) >= 2) {
                                        $team_draw_bet_total = $team_draw_bet_total + $bet->bet_profit;
                                    }
                                    $team2_bet_total = $team2_bet_total - $bet->exposureAmt;
                                }
                                if ($bet->bet_side == 'lay') {
                                    $team1_bet_total = $team1_bet_total + $bet->bet_amount;
                                    if (count($abc) >= 2) {
                                        $team_draw_bet_total = $team_draw_bet_total - $bet->exposureAmt;
                                    }
                                    $team2_bet_total = $team2_bet_total + $bet->bet_amount;
                                }
                            } else if (array_key_exists("teamname3", $abc) && array_key_exists("teamname2", $abc)) {
                                //bet on team1
                                if ($bet->bet_side == 'back') {
                                    $team1_bet_total = $team1_bet_total + $bet->bet_profit;

                                    if (count($abc) >= 2) {
                                        $team_draw_bet_total = $team_draw_bet_total - $bet->exposureAmt;
                                    }
                                    $team2_bet_total = $team2_bet_total - $bet->exposureAmt;
                                }
                                if ($bet->bet_side == 'lay') {
                                    $team1_bet_total = $team1_bet_total - $bet->exposureAmt;
                                    if (count($abc) >= 2) {
                                        $team_draw_bet_total = $team_draw_bet_total + $bet->bet_amount;
                                    }
                                    $team2_bet_total = $team2_bet_total + $bet->bet_amount;
                                }
                            } else if (array_key_exists("teamname3", $abc) && array_key_exists("teamname1", $abc)) {
                                //bet on team2
                                if ($bet->bet_side == 'back') {
                                    $team2_bet_total = $team2_bet_total + $bet->bet_profit; ///nnn 16-7-2021
                                    if (count($abc) >= 2) {
                                        $team_draw_bet_total = $team_draw_bet_total - $bet->exposureAmt;
                                    }
                                    $team1_bet_total = $team1_bet_total - $bet->exposureAmt;
                                }
                                if ($bet->bet_side == 'lay') {
                                    $team2_bet_total = $team2_bet_total - $bet->exposureAmt;
                                    if (count($abc) >= 2) {
                                        $team_draw_bet_total = $team_draw_bet_total + $bet->bet_amount;
                                    }
                                    $team1_bet_total = $team1_bet_total + $bet->bet_amount;
                                }
                            }
                        } else if (count($abc) == 1) {
                            if (array_key_exists("teamname1", $abc)) {
                                //bet on team2
                                if ($bet->bet_side == 'back') {
                                    $team2_bet_total = $team2_bet_total + $bet->bet_profit;
                                    $team1_bet_total = $team1_bet_total - $bet->exposureAmt;
                                }
                                if ($bet->bet_side == 'lay') {
                                    $team2_bet_total = $team2_bet_total - $bet->exposureAmt;
                                    $team1_bet_total = $team1_bet_total + $bet->bet_amount;
                                }
                            } else {
                                //bet on team1
                                if ($bet->bet_side == 'back') {
                                    $team1_bet_total = $team1_bet_total + $bet->bet_profit;
                                    $team2_bet_total = $team2_bet_total - $bet->exposureAmt;
                                }
                                if ($bet->bet_side == 'lay') {
                                    $team1_bet_total = $team1_bet_total - $bet->exposureAmt;
                                    $team2_bet_total = $team2_bet_total + $bet->bet_amount;
                                }
                            }
                        }
                    }
                }
            }
        }

        $bet_total = [];
        $bet_total['team1_bet_total'] = round($team1_bet_total,2);
        $bet_total['team2_bet_total'] = round($team2_bet_total,2);
        $bet_total['team_draw_bet_total'] = round($team_draw_bet_total,2);


//        dd($match_data);

        $section = '';
        $match_updated_date = '';
        if ($sport_id == '1') { //soccer
            $section = '3';
            $cricketSec = '3';
            $match_updated_date = strtotime($match_data[0]['updateTime']);
        } elseif ($sport_id == '2') { //tennis
            $section = '2';
            $cricketSec = '2';
            $match_updated_date = strtotime($match_data[0]['updateTime']);
        } elseif ($sport_id == '4') { //cricket
            $section = 4;
            $cricketSec = count($match_data['t1'][0]);
            $match_updated_date = $match_data['starttime'];
        }

        $inplay = 'False';

        if($section == 4 && isset($match_data['t1'][0][0]['iplay']) && $match_data['t1'][0][0]['iplay'] === 'True'){
            $inplay = 'True';
        }else if (isset($match_data[0]['inplay']) != '') {
            $inplay = $match_data[0]['inplay'];
            if ($inplay == 1)
                $inplay = 'True';
            else
                $inplay = 'false';
        }

//        dd(date('y-m-d H:i A',$match_updated_date));

        $Selections = $cricketSec;

        $login_check = '';
        //$sessionData = Session::get('playerUser');
        $getUserCheck = Session::get('playerUser');
        if (!empty($getUserCheck)) {
            $sessionData = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
        }

        //for bm
        $matchtype = $match->sports_id;
        $match_id = $match->id;
        $sport = Sport::where('sId', $matchtype)->first();
        $matchList = Match::where('id', $match_id)->where('status', 1)->first();

        $min_bet_odds_limit = $matchList->min_bookmaker_limit;
        $max_bet_odds_limit = $matchList->max_bookmaker_limit;
        $min_bookmaker_limit = $matchList->min_bookmaker_limit;
        $max_bookmaker_limit = $matchList->max_bookmaker_limit;
        $min_bet_fancy_limit = $matchList->min_fancy_limit;
        $max_bet_fancy_limit = $matchList->max_fancy_limit;

        $oddsLimit['min_bookmaker_limit'] = $matchList->min_bookmaker_limit;
        $oddsLimit['max_bookmaker_limit'] = $matchList->max_bookmaker_limit;
        $oddsLimit['min_fancy_limit'] = $matchList->min_fancy_limit;
        $oddsLimit['max_fancy_limit'] = $matchList->max_fancy_limit;

        $matchtype = $sport->id;
        $eventId = $matchList->event_id;
        $matchname = $matchList->match_name;
        $match_b = $matchList->suspend_b;
        $match_f = $matchList->suspend_f;
        $html_bm = '';
        $html_bm_team = "";

        @$team_name = explode(" v ", strtolower($matchname));
        $team1_name = @$team_name[0];
        if (@$team_name[1])
            @$team2_name = $team_name[1];
        else
            $team2_name = '';

        // $match_detail = Match::where('event_id',$matchList->event_id)->where('status',1)->first();
        $match_detail = $matchList;

        $team2_bet_total = 0;
        $team1_bet_total = 0;
        $team_draw_bet_total = 0;

        $getUserCheck = Session::get('playerUser');
        if (!empty($getUserCheck)) {
            $sessionData = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
        }

        if (!empty($sessionData)) {
            $getUserCheckuser = Session::get('playerUser');
            if (!empty($getUserCheckuser)) {
                $getUser = User::where('id', $getUserCheckuser->id)->where('check_login', 1)->first();
            }

            $userId = $getUser->id;
            $my_placed_bets_bm = MyBets::where('user_id', $userId)->where('match_id', $eventId)->where('bet_type', 'BOOKMAKER')->where('isDeleted', 0)->where('result_declare', 0)->orderby('id', 'DESC')->get();

            if (sizeof($my_placed_bets_bm) > 0) {
                foreach ($my_placed_bets_bm as $bet) {
                    $abc = json_decode($bet->extra, true);
                    if (!empty($abc)) {
                        if (count($abc) >= 2) {
                            if (array_key_exists("teamname1", $abc) && array_key_exists("teamname2", $abc)) {
                                //bet on draw
                                if ($bet->bet_side == 'back') {
                                    $team1_bet_total = $team1_bet_total - $bet->exposureAmt;
                                    if (count($abc) >= 2) {
                                        $team_draw_bet_total = $team_draw_bet_total + $bet->bet_profit;
                                    }
                                    $team2_bet_total = $team2_bet_total - $bet->exposureAmt;
                                }
                                if ($bet->bet_side == 'lay') {
                                    $team1_bet_total = $team1_bet_total + $bet->bet_amount;
                                    if (count($abc) >= 2) {
                                        $team_draw_bet_total = $team_draw_bet_total - $bet->bet_profit;
                                    }
                                    $team2_bet_total = $team2_bet_total + $bet->bet_amount;
                                }
                            } else if (array_key_exists("teamname3", $abc) && array_key_exists("teamname2", $abc)) {
                                //bet on team1
                                if ($bet->bet_side == 'back') {
                                    $team1_bet_total = $team1_bet_total + $bet->bet_profit;
                                    if (count($abc) >= 2) {
                                        $team_draw_bet_total = $team_draw_bet_total - $bet->exposureAmt;
                                    }
                                    $team2_bet_total = $team2_bet_total - $bet->exposureAmt;
                                }
                                if ($bet->bet_side == 'lay') {
                                    $team1_bet_total = $team1_bet_total - $bet->bet_profit;
                                    if (count($abc) >= 2) {
                                        $team_draw_bet_total = $team_draw_bet_total + $bet->bet_amount;
                                    }
                                    $team2_bet_total = $team2_bet_total + $bet->bet_amount;
                                }
                            } else if (array_key_exists("teamname3", $abc) && array_key_exists("teamname1", $abc)) {
                                //bet on team2
                                if ($bet->bet_side == 'back') {
                                    $team2_bet_total = $team2_bet_total + $bet->bet_profit;
                                    if (count($abc) >= 2) {
                                        $team_draw_bet_total = $team_draw_bet_total - $bet->exposureAmt;
                                    }
                                    $team1_bet_total = $team1_bet_total - $bet->exposureAmt;
                                }
                                if ($bet->bet_side == 'lay') {
                                    $team2_bet_total = $team2_bet_total - $bet->bet_profit;
                                    if (count($abc) >= 2) {
                                        $team_draw_bet_total = $team_draw_bet_total + $bet->bet_amount;
                                    }
                                    $team1_bet_total = $team1_bet_total + $bet->bet_amount;
                                }
                            }
                        } else if (count($abc) == 1) {
                            if (array_key_exists("teamname1", $abc)) {
                                //bet on team2
                                if ($bet->bet_side == 'back') {
                                    $team2_bet_total = $team2_bet_total + $bet->bet_profit;
                                    $team1_bet_total = $team1_bet_total - $bet->exposureAmt;
                                }
                                if ($bet->bet_side == 'lay') {
                                    $team2_bet_total = $team2_bet_total - $bet->bet_profit;
                                    $team1_bet_total = $team1_bet_total + $bet->bet_amount;
                                }
                            } else {
                                //bet on team1
                                if ($bet->bet_side == 'back') {
                                    $team1_bet_total = $team1_bet_total + $bet->bet_profit;
                                    $team2_bet_total = $team2_bet_total - $bet->exposureAmt;
                                }
                                if ($bet->bet_side == 'lay') {
                                    $team1_bet_total = $team1_bet_total - $bet->bet_profit;
                                    $team2_bet_total = $team2_bet_total + $bet->bet_amount;
                                }
                            }
                        }
                    }
                }
            }
        }

        $bet_total['team1_BM_total']    = round($team1_bet_total,2);
        $bet_total['team2_BM_total']    = round($team2_bet_total,2);
        $bet_total['draw_BM_total']     = round($team_draw_bet_total,2);

        $html_two = '';
        $html_two_team = "";

        $login_check = '';

        $getUserCheck = Session::get('playerUser');
        if (!empty($getUserCheck)) {
            $sessionData = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
        }

        //for fancy
        $all_bet_model = '';
        $login_check = '';
        $final_exposer = 0;
        $getUserCheck = Session::get('playerUser');
        if (!empty($getUserCheck)) {
            $sessionData = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
        }

        if (!empty($sessionData)) {
            if ($min_bet_fancy_limit > 0 && $min_bet_fancy_limit != "" && $max_bet_fancy_limit > 0 && $max_bet_fancy_limit != "")
                $login_check = 'onclick="opnForm(this);"';
        } else {
            $useragent = $_SERVER['HTTP_USER_AGENT'];
            $iPod = stripos($useragent, "iPod");
            $iPad = stripos($useragent, "iPad");
            $iPhone = stripos($useragent, "iPhone");
            $Android = stripos($useragent, "Android");
            $iOS = stripos($useragent, "iOS");

            $DEVICE = ($iPod || $iPad || $iPhone || $Android || $iOS);
            $is_agent = '';
            if (!$DEVICE) {
                $is_agent = 'desktop';
                $login_check = 'data-toggle="modal" data-target="#myLoginModal"';
            } else {
                $is_agent = 'mobile';
                $login_check = 'data-toggle="modal" data-target="#myLoginModalFront"';
            }
        }

        $nat = array();
        $gstatus = array();
        $b = array();
        $l = array();
        $bs = array();
        $ls = array();
        $min = array();
        $max = array();
        $sid = array();
        if (@$match_data['t3']) {
            foreach ($match_data['t3'] as $key => $value) {
                $sid_val = '';
                foreach ($value as $key1 => $value1) {
                    if ($key1 == 'sid') {
                        $sid_val = $value1;
                        $sid[] = $value1;
                    }
                    if ($key1 == 'nat')
                        $nat[$sid_val] = $value1;
                    if ($key1 == 'gstatus') {
                        $gstatus[$sid_val] = $value1;
                    }
                    if ($key1 == 'b1')
                        $b[$sid_val] = $value1;
                    if ($key1 == 'l1')
                        $l[$sid_val] = $value1;
                    if ($key1 == 'bs1')
                        $bs[$sid_val] = $value1;
                    if ($key1 == 'ls1')
                        $ls[$sid_val] = $value1;
                    if ($key1 == 'min')
                        $min[$sid_val] = $value1;
                    if ($key1 == 'max')
                        $max[$sid_val] = $value1;
                }
            }
            sort($sid);
            for ($i = 0; $i < sizeof($sid); $i++) {
                $max_val = 0;
                if ($max[$sid[$i]] > 999) {
                    $input = number_format($max[$sid[$i]]);
                    $input_count = substr_count($input, ',');
                    $arr = array(1 => 'K', 'M', 'B', 'T');
                    if (isset($arr[(int)$input_count]))
                        $max_val = substr($input, 0, (-1 * $input_count) * 4) . $arr[(int)$input_count];
                    else
                        $max_val = $input;
                }

                if ($match_f == '0') {
                    $getUserCheck = Session::get('playerUser');
                    if (!empty($getUserCheck)) {
                        $sessionData = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
                    }

                    if (!empty($sessionData)) {
                        $getUserCheckuser = Session::get('playerUser');
                        if (!empty($getUserCheckuser)) {
                            $getUser = User::where('id', $getUserCheckuser->id)->where('check_login', 1)->first();
                        }

                        $userId = $getUser->id;

                        $my_placed_bets = MyBets::where('user_id', $userId)->where('match_id', $eventId)->where('team_name', @$nat[$sid[$i]])->where('bet_type', 'SESSION')->where('isDeleted', 0)->where('result_declare', 0)->orderBy('created_at', 'asc')->get();

                        $abc = sizeof($my_placed_bets);
                        if (sizeof($my_placed_bets) > 0) {
                            $run_arr = array();
                            foreach ($my_placed_bets as $bet) {
                                $down_position = $bet->bet_odds - 1;
                                if (!in_array($down_position, $run_arr)) {
                                    $run_arr[] = $down_position;
                                }
                                $level_position = $bet->bet_odds;
                                if (!in_array($level_position, $run_arr)) {
                                    $run_arr[] = $level_position;
                                }
                                $up_position = $bet->bet_odds + 1;
                                if (!in_array($up_position, $run_arr)) {
                                    $run_arr[] = $up_position;
                                }
                            }
                            array_unique($run_arr);
                            sort($run_arr);

                            $min_val = min($run_arr);
                            $max_val = max($run_arr);

                            $newArr = array();

                            for ($z = $min_val; $z <= $max_val; ++$z) {
                                $new = $z;
                                $newArr[] = $new;
                            }

                            $run_arr = array();
                            $run_arr = $newArr;

                            $bet_chk = '';
                            $bet_model = '';
                            for ($kk = 0; $kk < sizeof($run_arr); $kk++) {
                                $bet_deduct_amt = 0;
                                $placed_bet_type = '';
                                foreach ($my_placed_bets as $bet) {
//                                    if ($bet->bet_side == 'back') {
//                                        if ($bet->bet_odds == $run_arr[$kk]) {
//                                            $bet_deduct_amt = $bet_deduct_amt + $bet->bet_profit;
//                                        } else if ($bet->bet_odds < $run_arr[$kk]) {
//                                            $bet_deduct_amt = $bet_deduct_amt + $bet->bet_profit;
//                                        } else if ($bet->bet_odds > $run_arr[$kk]) {
//                                            $bet_deduct_amt = $bet_deduct_amt - $bet->exposureAmt;
//                                        }
//                                    } else if ($bet->bet_side == 'lay')
                                    {
                                        if ($bet->bet_odds == $run_arr[$kk]) {
                                            $bet_deduct_amt = $bet_deduct_amt - $bet->exposureAmt;
                                        } else if ($bet->bet_odds < $run_arr[$kk]) {
                                            $bet_deduct_amt = $bet_deduct_amt - $bet->exposureAmt;
                                        } else if ($bet->bet_odds > $run_arr[$kk]) {
                                            $bet_deduct_amt = $bet_deduct_amt + $bet->bet_amount;
                                        }
                                    }
                                }
                                if ($final_exposer == "")
                                    $final_exposer = $bet_deduct_amt;
                                else {
                                    if ($final_exposer > $bet_deduct_amt)
                                        $final_exposer = $bet_deduct_amt;
                                }

                                if($final_exposer != 0) {
                                    $bet_total['fancy_' . $sid[$i]] = round($final_exposer,2);
                                }

                                if ($bet_deduct_amt > 0) {
                                    $position .= '<tr>
										<td class="text-center cyan-bg">' . $run_arr[$kk] . '</td>
										<td class="text-right cyan-bg">' . $bet_deduct_amt . '</td>
										</tr>';
                                } else {
                                    $position .= '<tr>
										<td class="text-center pink-bg">' . $run_arr[$kk] . '</td>
										<td class="text-right pink-bg">' . $bet_deduct_amt . '</td>
										</tr>';
                                }
                            }
                            if ($position != '') {
                                $bet_model = '<div class="modal credit-modal" id="runPosition' . $i . '">
										<div class="modal-dialog">
											<div class="modal-content light-grey-bg-1">
												<div class="modal-header">
													<h4 class="modal-title text-color-blue-1">Run Position</h4>
													<button type="button" class="close modelclose" data-dismiss="modal"><img src="' . asset('asset/front/img/close-icon.png') . '" alt=""></button>
												</div>
												<div class="modal-body white-bg p-3">
													<table class="table table-bordered w-100 fonts-1 mb-0">
														<thead>
															<tr>
																<th width="50%" class="text-center">Run</th>
																<th width="50%" class="text-right">Amount</th>
															</tr>
														</thead>
														<tbody> ' . $position . '</tbody>
													</table>
												</div>
											</div>
										</div>
									</div>';
                            }
                        }
                    }

                    if ($bet_model != '') {
                        $all_bet_model .= $bet_model;
                    }
                    //end for bet calculation

                }
                else {
                    $placed_bet = '';
                    $position = '';
                    $bet_model = '';
                    $abc = '';
                    $final_exposer = '';
                    if ($gstatus[$sid[$i]] != 'Ball Running' && $gstatus[$sid[$i]] != 'Suspended' && $l[$sid[$i]] != 0 && round($b[$sid[$i]]) != 0) {
                        if ($l[$sid[$i]] != 0 && round($b[$sid[$i]]) != 0 && $l[$sid[$i]] != '' && $b[$sid[$i]] != '') {
                            //bet calculation
                            $getUserCheck = Session::get('playerUser');
                            if (!empty($getUserCheck)) {
                                $sessionData = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
                            }

                            if (!empty($sessionData)) {
                                $getUserCheckuser = Session::get('playerUser');
                                if (!empty($getUserCheckuser)) {
                                    $getUser = User::where('id', $getUserCheckuser->id)->where('check_login', 1)->first();
                                }

                                $userId = $getUser->id;

                                $my_placed_bets = MyBets::where('user_id', $userId)->where('match_id', $eventId)->where('team_name', @$nat[$sid[$i]])->where('bet_type', 'SESSION')->where('isDeleted', 0)->where('result_declare', 0)->orderBy('created_at', 'asc')->get();

                                $abc = sizeof($my_placed_bets);
                                if (sizeof($my_placed_bets) > 0) {
                                    $run_arr = array();
                                    foreach ($my_placed_bets as $bet) {
                                        $down_position = $bet->bet_odds - 1;
                                        if (!in_array($down_position, $run_arr)) {
                                            $run_arr[] = $down_position;
                                        }
                                        $level_position = $bet->bet_odds;
                                        if (!in_array($level_position, $run_arr)) {
                                            $run_arr[] = $level_position;
                                        }
                                        $up_position = $bet->bet_odds + 1;
                                        if (!in_array($up_position, $run_arr)) {
                                            $run_arr[] = $up_position;
                                        }
                                    }
                                    array_unique($run_arr);
                                    sort($run_arr);

                                    $min_val = min($run_arr);
                                    $max_val = max($run_arr);

                                    $newArr = array();

                                    for ($z = $min_val; $z <= $max_val; ++$z) {
                                        $new = $z;
                                        $newArr[] = $new;
                                    }

                                    $run_arr = array();
                                    $run_arr = $newArr;

                                    $bet_chk = '';
                                    for ($kk = 0; $kk < sizeof($run_arr); $kk++) {
                                        $bet_deduct_amt = 0;
                                        $placed_bet_type = '';
                                        foreach ($my_placed_bets as $bet) {
//                                            if ($bet->bet_side == 'back') {
//                                                if ($bet->bet_odds == $run_arr[$kk]) {
//
//                                                    $bet_deduct_amt = $bet_deduct_amt + $bet->bet_profit;
//                                                } else if ($bet->bet_odds < $run_arr[$kk]) {
//
//                                                    $bet_deduct_amt = $bet_deduct_amt + $bet->bet_profit;
//                                                } else if ($bet->bet_odds > $run_arr[$kk]) {
//
//                                                    $bet_deduct_amt = $bet_deduct_amt - $bet->exposureAmt;
//                                                }
//                                            } else if ($bet->bet_side == 'lay')
                                            {
                                                if ($bet->bet_odds == $run_arr[$kk]) {

                                                    $bet_deduct_amt = $bet_deduct_amt - $bet->exposureAmt;
                                                } else if ($bet->bet_odds < $run_arr[$kk]) {

                                                    $bet_deduct_amt = $bet_deduct_amt - $bet->exposureAmt;
                                                } else if ($bet->bet_odds > $run_arr[$kk]) {

                                                    $bet_deduct_amt = $bet_deduct_amt + $bet->bet_amount;
                                                }
                                            }
                                        }
                                        if ($final_exposer == "")
                                            $final_exposer = $bet_deduct_amt;
                                        else {
                                            if ($final_exposer > $bet_deduct_amt)
                                                $final_exposer = $bet_deduct_amt;
                                        }
                                        if ($bet_deduct_amt > 0) {
                                            $position .= '<tr>
												<td class="text-center cyan-bg">' . $run_arr[$kk] . '</td>
												<td class="text-right cyan-bg">' . $bet_deduct_amt . '</td>
												</tr>';
                                        } else {
                                            $position .= '<tr>
												<td class="text-center pink-bg">' . $run_arr[$kk] . '</td>
												<td class="text-right pink-bg">' . $bet_deduct_amt . '</td>
												</tr>';
                                        }
                                    }
                                    if ($position != '') {
                                        $bet_model = '<div class="modal credit-modal" id="runPosition' . $i . '">
												<div class="modal-dialog">
													<div class="modal-content light-grey-bg-1">
														<div class="modal-header">
															<h4 class="modal-title text-color-blue-1">Run Position</h4>
															<button type="button" class="close modelclose" data-dismiss="modal"><img src="' . asset('asset/front/img/close-icon.png') . '" alt=""></button>
														</div>
														<div class="modal-body white-bg p-3">
															<table class="table table-bordered w-100 fonts-1 mb-0">
																<thead>
																	<tr>
																		<th width="50%" class="text-center">Run</th>
																		<th width="50%" class="text-right">Amount</th>
																	</tr>
																</thead>
																<tbody> ' . $position . '</tbody>
															</table>
														</div>
													</div>
												</div>
											</div>';
                                    }
                                }

                                if($final_exposer != 0) {
                                    $bet_total['fancy_' . $sid[$i]] = round($final_exposer,2);
                                }
                            }

                            if ($bet_model != '') {
                                $all_bet_model .= $bet_model;
                            }
                        }
                        else {
                            //for bet calculation
                            $getUserCheck = Session::get('playerUser');
                            if (!empty($getUserCheck)) {
                                $sessionData = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
                            }

                            if (!empty($sessionData)) {
                                $getUserCheckuser = Session::get('playerUser');
                                if (!empty($getUserCheckuser)) {
                                    $getUser = User::where('id', $getUserCheckuser->id)->where('check_login', 1)->first();
                                }

                                $userId = $getUser->id;
                                $my_placed_bets = MyBets::where('user_id', $userId)->where('match_id', $eventId)->where('team_name', @$nat[$sid[$i]])->where('bet_type', 'SESSION')->where('isDeleted', 0)->where('result_declare', 0)->orderBy('created_at', 'asc')->get();
                                $abc = sizeof($my_placed_bets);
                                if (sizeof($my_placed_bets) > 0) {
                                    $run_arr = array();
                                    foreach ($my_placed_bets as $bet) {
                                        $down_position = $bet->bet_odds - 1;
                                        if (!in_array($down_position, $run_arr)) {
                                            $run_arr[] = $down_position;
                                        }
                                        $level_position = $bet->bet_odds;
                                        if (!in_array($level_position, $run_arr)) {
                                            $run_arr[] = $level_position;
                                        }
                                        $up_position = $bet->bet_odds + 1;
                                        if (!in_array($up_position, $run_arr)) {
                                            $run_arr[] = $up_position;
                                        }
                                    }
                                    array_unique($run_arr);
                                    sort($run_arr);

                                    $min_val = min($run_arr);
                                    $max_val = max($run_arr);

                                    $newArr = array();

                                    for ($z = $min_val; $z <= $max_val; ++$z) {
                                        $new = $z;
                                        $newArr[] = $new;
                                    }

                                    $run_arr = array();
                                    $run_arr = $newArr;

                                    $bet_chk = '';
                                    for ($kk = 0; $kk < sizeof($run_arr); $kk++) {
                                        $bet_deduct_amt = 0;
                                        $placed_bet_type = '';
                                        foreach ($my_placed_bets as $bet) {
//                                            if ($bet->bet_side == 'back') {
//                                                if ($bet->bet_odds == $run_arr[$kk]) {
//
//                                                    $bet_deduct_amt = $bet_deduct_amt + $bet->bet_profit;
//                                                } else if ($bet->bet_odds < $run_arr[$kk]) {
//
//                                                    $bet_deduct_amt = $bet_deduct_amt + $bet->bet_profit;
//                                                } else if ($bet->bet_odds > $run_arr[$kk]) {
//
//                                                    $bet_deduct_amt = $bet_deduct_amt - $bet->exposureAmt;
//                                                }
//                                            } else if ($bet->bet_side == 'lay')
                                            {
                                                if ($bet->bet_odds == $run_arr[$kk]) {
                                                    $bet_deduct_amt = $bet_deduct_amt - $bet->exposureAmt;
                                                } else if ($bet->bet_odds < $run_arr[$kk]) {
                                                    $bet_deduct_amt = $bet_deduct_amt - $bet->exposureAmt;
                                                } else if ($bet->bet_odds > $run_arr[$kk]) {
                                                    $bet_deduct_amt = $bet_deduct_amt + $bet->bet_amount;
                                                }
                                            }
                                        }
                                        if ($final_exposer == "")
                                            $final_exposer = $bet_deduct_amt;
                                        else {
                                            if ($final_exposer > $bet_deduct_amt)
                                                $final_exposer = $bet_deduct_amt;
                                        }

                                        if($final_exposer != 0) {
                                            $bet_total['fancy_' . $sid[$i]] = round($final_exposer,2);
                                        }

                                        if ($bet_deduct_amt > 0) {
                                            $position .= '<tr>
												<td class="text-center cyan-bg">' . $run_arr[$kk] . '</td>
												<td class="text-right cyan-bg">' . $bet_deduct_amt . '</td>
												</tr>';
                                        } else {
                                            $position .= '<tr>
												<td class="text-center pink-bg">' . $run_arr[$kk] . '</td>
												<td class="text-right pink-bg">' . $bet_deduct_amt . '<br>' . $bet_chk . '</td>
												</tr>';
                                        }
                                    }
                                    if ($position != '') {
                                        $bet_model = '<div class="modal credit-modal" id="runPosition' . $i . '">
												<div class="modal-dialog">
													<div class="modal-content light-grey-bg-1">
														<div class="modal-header">
															<h4 class="modal-title text-color-blue-1">Run Position</h4>
															<button type="button" class="close modelclose" data-dismiss="modal"><img src="' . asset('asset/front/img/close-icon.png') . '" alt=""></button>
														</div>
														<div class="modal-body white-bg p-3">
															<table class="table table-bordered w-100 fonts-1 mb-0">
																<thead>
																	<tr>
																		<th width="50%" class="text-center">Run' . $abc . '</th>
																		<th width="50%" class="text-right">Amount</th>
																	</tr>
																</thead>
																<tbody> ' . $position . '</tbody>
															</table>
														</div>
													</div>
												</div>
											</div>';
                                    }
                                }
                            }

                            if ($bet_model != '') {
                                $all_bet_model .= $bet_model;
                            }
                        }
                    }
                    else {
                        //for bet calculation
                        $getUserCheck = Session::get('playerUser');
                        if (!empty($getUserCheck)) {
                            $sessionData = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
                        }

                        if (!empty($sessionData)) {
                            $getUserCheckuser = Session::get('playerUser');
                            if (!empty($getUserCheckuser)) {
                                $getUser = User::where('id', $getUserCheckuser->id)->where('check_login', 1)->first();
                            }

                            $userId = $getUser->id;
                            $my_placed_bets_session = MyBets::where('user_id', $userId)->where('match_id', $eventId)->where('team_name', @$nat[$sid[$i]])->where('bet_type', 'SESSION')->where('isDeleted', 0)->where('result_declare', 0)->orderBy('created_at', 'asc')->get();
                            $abc = sizeof($my_placed_bets_session);
                            if (sizeof($my_placed_bets_session) > 0) {
                                $run_arr = array();
                                foreach ($my_placed_bets_session as $bet) {
                                    $down_position = $bet->bet_odds - 1;
                                    if (!in_array($down_position, $run_arr)) {
                                        $run_arr[] = $down_position;
                                    }
                                    $level_position = $bet->bet_odds;
                                    if (!in_array($level_position, $run_arr)) {
                                        $run_arr[] = $level_position;
                                    }
                                    $up_position = $bet->bet_odds + 1;
                                    if (!in_array($up_position, $run_arr)) {
                                        $run_arr[] = $up_position;
                                    }
                                }
                                array_unique($run_arr);
                                sort($run_arr);

                                $min_val = min($run_arr);
                                $max_val = max($run_arr);

                                $newArr = array();

                                for ($z = $min_val; $z <= $max_val; ++$z) {
                                    $new = $z;
                                    $newArr[] = $new;
                                }

                                $run_arr = array();
                                $run_arr = $newArr;

                                $bet_chk = '';
                                for ($kk = 0; $kk < sizeof($run_arr); $kk++) {
                                    $bet_deduct_amt = 0;
                                    $placed_bet_type = '';
                                    foreach ($my_placed_bets_session as $bet) {
//                                        if ($bet->bet_side == 'back') {
//                                            if ($bet->bet_odds == $run_arr[$kk]) {
//                                                $bet_deduct_amt = $bet_deduct_amt + $bet->bet_profit;
//                                            } else if ($bet->bet_odds < $run_arr[$kk]) {
//                                                $bet_deduct_amt = $bet_deduct_amt + $bet->bet_profit;
//                                            } else if ($bet->bet_odds > $run_arr[$kk]) {
//                                                $bet_deduct_amt = $bet_deduct_amt - $bet->exposureAmt;
//                                            }
//                                        } else if ($bet->bet_side == 'lay')
                                        {
                                            if ($bet->bet_odds == $run_arr[$kk]) {
                                                $bet_deduct_amt = $bet_deduct_amt - $bet->exposureAmt;
                                            } else if ($bet->bet_odds < $run_arr[$kk]) {
                                                $bet_deduct_amt = $bet_deduct_amt - $bet->exposureAmt;
                                            } else if ($bet->bet_odds > $run_arr[$kk]) {
                                                $bet_deduct_amt = $bet_deduct_amt + $bet->bet_amount;
                                            }
                                        }
                                    }
                                    if ($final_exposer == "")
                                        $final_exposer = $bet_deduct_amt;
                                    else {
                                        if ($final_exposer > $bet_deduct_amt)
                                            $final_exposer = $bet_deduct_amt;
                                    }

                                    if($final_exposer != 0) {
                                        $bet_total['fancy_' . $sid[$i]] = round($final_exposer,2);
                                    }

                                    if ($bet_deduct_amt > 0) {
                                        $position .= '<tr>
												<td class="text-center cyan-bg">' . $run_arr[$kk] . '</td>
												<td class="text-right cyan-bg">' . $bet_deduct_amt . '</td>
												</tr>';
                                    } else {
                                        $position .= '<tr>
												<td class="text-center pink-bg">' . $run_arr[$kk] . '</td>
												<td class="text-right pink-bg">' . $bet_deduct_amt . '<br>' . $bet_chk . '</td>
												</tr>';
                                    }
                                }
                                if ($position != '') {
                                    $bet_model = '<div class="modal credit-modal" id="runPosition' . $i . '">
												<div class="modal-dialog">
													<div class="modal-content light-grey-bg-1">
														<div class="modal-header">
															<h4 class="modal-title text-color-blue-1">Run Position</h4>
															<button type="button" class="close modelclose" data-dismiss="modal"><img src="' . asset('asset/front/img/close-icon.png') . '" alt=""></button>
														</div>
														<div class="modal-body white-bg p-3">
															<table class="table table-bordered w-100 fonts-1 mb-0">
																<thead>
																	<tr>
																		<th width="50%" class="text-center">Run' . $abc . '</th>
																		<th width="50%" class="text-right">Amount</th>
																	</tr>
																</thead>
																<tbody> ' . $position . '</tbody>
															</table>
														</div>
													</div>
												</div>
											</div>';
                                }
                            }
                        }
                        if ($bet_model != '') {
                            $all_bet_model .= $bet_model;
                        }
                    }
                } // end suspended if
            }
        }


        return view('front.matchDetail', compact('match', 'match_data', 'inplay', 'my_placed_bets_all', 'total_todays_bet', 'match_name_bet',
            'match_updated_date', 'stkval', 'all_bet_model', 'placed_bet_match_list','logindata','max_bet_odds_limit','min_bet_odds_limit','Selections','min_bookmaker_limit','max_bookmaker_limit','min_bet_fancy_limit','max_bet_fancy_limit','team',
            'bet_total','oddsLimit'
        ));

    }

    public function matchCallOdds($eventId, Request $request)
    {
        $matchtype = $request->matchtype;

        $match_id = $request->match_id;
        $sport = Sport::where('sId', $matchtype)->first();
        $matchList = Match::where('id', $match_id)->where('status', 1)->first();
        if (!$matchList) {
            return 'inactive';
        }
        $match_m = $matchList->suspend_m;
        $matchtype = $sport->id;
        $matchId = $request->matchid;
        $matchname = $request->matchname;
        $event_id = $request->event_id;
        $team = explode(" v ", strtolower($matchname));
        $sport_id = $matchList->sports_id;

        $min_bet_odds_limit = $matchList->min_bet_odds_limit;
        $max_bet_odds_limit = $matchList->max_bet_odds_limit;

        $team1_bet_total = '';
        $team1_bet_class = '';

        $team2_bet_total = '';
        $team2_bet_class = '';

        $team_draw_bet_total = '';
        $team_draw_bet_class = '';


        $match_data = app('App\Http\Controllers\RestApi')->DetailCall($matchId, $event_id, $matchtype);
        $getUserCheck = Session::get('playerUser');
        if (!empty($getUserCheck)) {
            $sessionData = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
        }
        if (!empty($sessionData)) {
            $getUserCheckuser = Session::get('playerUser');
            if (!empty($getUserCheckuser)) {
                $getUser = User::where('id', $getUserCheckuser->id)->where('check_login', 1)->first();
            }
            $userId = $getUser->id;

            $my_placed_bets = MyBets::where('user_id', $userId)->where('match_id', $event_id)->where('bet_type', 'ODDS')->where('isDeleted', 0)->where('result_declare', 0)->orderby('id', 'DESC')->get();

            $team2_bet_total = 0;
            $team1_bet_total = 0;
            $team_draw_bet_total = 0;
            if (sizeof($my_placed_bets) > 0) {
                foreach ($my_placed_bets as $bet) {
                    $abc = json_decode($bet->extra, true);
                    if (count($abc) >= 2) {
                        if (array_key_exists("teamname1", $abc) && array_key_exists("teamname2", $abc)) {
                            //bet on draw
                            if ($bet->bet_side == 'back') {
                                $team1_bet_total = $team1_bet_total - $bet->exposureAmt;
                                if (count($abc) >= 2) {
                                    $team_draw_bet_total = $team_draw_bet_total + $bet->bet_profit;

                                }
                                $team2_bet_total = $team2_bet_total - $bet->exposureAmt;
                            }
                            if ($bet->bet_side == 'lay') {
                                $team1_bet_total = $team1_bet_total + $bet->bet_amount;
                                if (count($abc) >= 2) {

                                    $team_draw_bet_total = $team_draw_bet_total - $bet->exposureAmt;
                                }
                                $team2_bet_total = $team2_bet_total + $bet->bet_amount;
                            }
                        } else if (array_key_exists("teamname3", $abc) && array_key_exists("teamname2", $abc)) {
                            //bet on team1
                            if ($bet->bet_side == 'back') {
                                $team1_bet_total = $team1_bet_total + $bet->bet_profit;

                                if (count($abc) >= 2) {

                                    $team_draw_bet_total = $team_draw_bet_total - $bet->exposureAmt;
                                }
                                $team2_bet_total = $team2_bet_total - $bet->exposureAmt;
                            }
                            if ($bet->bet_side == 'lay') {
                                $team1_bet_total = $team1_bet_total - $bet->exposureAmt;
                                if (count($abc) >= 2) {
                                    $team_draw_bet_total = $team_draw_bet_total + $bet->bet_amount;
                                }
                                $team2_bet_total = $team2_bet_total + $bet->bet_amount;
                            }
                        } else if (array_key_exists("teamname3", $abc) && array_key_exists("teamname1", $abc)) {
                            //bet on team2
                            if ($bet->bet_side == 'back') {
                                $team2_bet_total = $team2_bet_total + $bet->bet_profit; ///nnn 16-7-2021
                                if (count($abc) >= 2) {
                                    $team_draw_bet_total = $team_draw_bet_total - $bet->exposureAmt;
                                }
                                $team1_bet_total = $team1_bet_total - $bet->exposureAmt;
                            }
                            if ($bet->bet_side == 'lay') {
                                $team2_bet_total = $team2_bet_total - $bet->exposureAmt;
                                if (count($abc) >= 2) {
                                    $team_draw_bet_total = $team_draw_bet_total + $bet->bet_amount;
                                }
                                $team1_bet_total = $team1_bet_total + $bet->bet_amount;
                            }
                        }
                    } else if (count($abc) == 1) {
                        if (array_key_exists("teamname1", $abc)) {
                            //bet on team2
                            if ($bet->bet_side == 'back') {
                                $team2_bet_total = $team2_bet_total + $bet->bet_profit;
                                $team1_bet_total = $team1_bet_total - $bet->exposureAmt;
                            }
                            if ($bet->bet_side == 'lay') {
                                $team2_bet_total = $team2_bet_total - $bet->exposureAmt;
                                $team1_bet_total = $team1_bet_total + $bet->bet_amount;
                            }
                        } else {
                            //bet on team1
                            if ($bet->bet_side == 'back') {
                                $team1_bet_total = $team1_bet_total + $bet->bet_profit;
                                $team2_bet_total = $team2_bet_total - $bet->exposureAmt;
                            }
                            if ($bet->bet_side == 'lay') {
                                $team1_bet_total = $team1_bet_total - $bet->exposureAmt;
                                $team2_bet_total = $team2_bet_total + $bet->bet_amount;
                            }
                        }
                    }
                }
            }
        }
        //echo $match_data;
        $section = '3';
        if ($sport_id == '1') {
            $section = '2';
        }
        $html = '';
        $html .= '<table class="table custom-table inplay-table w1-table">
		<tr class="betstr">
        	<td class="text-color-grey">' . $section . ' Selections</td>
            <td colspan="2">101.7%</td>
            <td>
            	<a class="backall">
                	<img src="' . asset('asset/front/img/bluebg1.png') . '">
                    <span>Back all</span>
               	</a>
           	</td>
            <td>
            	<a class="layall">
                	<img src="' . asset('asset/front/img/pinkbg1.png') . '">
                    <span>Lay all</span>
              	</a>
           	</td>
          	<td colspan="2">97.9%</td>
            </tr>';

        $login_check = '';
        $getUserCheck = Session::get('playerUser');
        if (!empty($getUserCheck)) {
            $sessionData = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
        }

        if (!empty($sessionData)) {
            if ($min_bet_odds_limit > 0 && $min_bet_odds_limit != "" && $max_bet_odds_limit > 0 && $max_bet_odds_limit != "")
                $login_check = 'onclick="opnForm(this);"';
        } else {
            $useragent = $_SERVER['HTTP_USER_AGENT'];
            $iPod = stripos($useragent, "iPod");
            $iPad = stripos($useragent, "iPad");
            $iPhone = stripos($useragent, "iPhone");
            $Android = stripos($useragent, "Android");
            $iOS = stripos($useragent, "iOS");

            $DEVICE = ($iPod || $iPad || $iPhone || $Android || $iOS);
            $is_agent = '';
            if (!$DEVICE) {
                $is_agent = 'desktop';
                $login_check = 'data-toggle="modal" data-target="#myLoginModal"';
            } else {
                $is_agent = 'mobile';
                $login_check = 'data-toggle="modal" data-target="#myLoginModalFront"';
            }
        }

        if ($match_data != 0) {
            $html_chk = '';
            if ($match_m == '0') {
                if (@$match_data[0]['runners'][2]['ex']['availableToBack'][0]['price'] != '') {
                    $html .= '
				 		<tr class="fancy-suspend-tr">
							<td></td>
							<td class="fancy-suspend-td" colspan="6">
								<div class="fancy-suspend black-bg-5 text-color-white"><span>SUSPENDED</span></div>
							</td>
						</tr>
				 		<tr class="white-bg tr_team3">
							<td> <img src="' . asset('asset/front/img/bars.png') . '"> <b class="team1">The Draw</b>
								<div>
									<span class="lose " id="team1_bet_count_old"></span>
									<span class="towin text-color-green" style="display:none" id="team1_bet_count_new">10.00</span>
								</div>
							</td>
							<td class="light-blue-bg-2 spark opnForm ODDSBack td_team3_back_2" >
								<a class="back1btn text-color-black">--</span></a>
							</td>
							<td class="link(target, link)ght-blue-bg-3 spark ODDSBack td_team3_back_1" data-team="team1">
								<a  class="back1btn text-color-black"> --<br><span>--</span></a>
							</td>
							<td class="cyan-bg spark ODDSBack td_team3_back_0" >
								<a  class="back1btn text-color-black"> -- <br><span>--</span></a>
							</td>
							<td class="pink-bg sparkLay ODDSLay td_team3_lay_0" >
								<a  data-cls="pink-bg" class="lay1btn text-color-black"> --</span></a>
							</td>
							<td class="light-pink-bg-2 sparkLay ODDSLay td_team3_lay_1" >
								<a data-cls="pink-bg" class="lay1btn text-color-black"> -- <br><span>--</span></a>
							</td>
							<td class="light-pink-bg-3 sparkLay ODDSLay td_team3_lay_2" >
								<a  data-cls="pink-bg" class="lay1btn text-color-black"> -- <br><span>--</span></a>
							</td>
						</tr>';
                }
                $html_chk .= '
					<tr class="fancy-suspend-tr">
						<td></td>
						<td class="fancy-suspend-td" colspan="6">
							<div class="fancy-suspend black-bg-5 text-color-white"><span>SUSPENDED</span></div>
						</td>
					</tr>
					<tr class="white-bg tr_team1">
						<td> <img src="' . asset('asset/front/img/bars.png') . '"> <b class="team1">' . ucfirst($team[0]) . ' </b>
							<div>
								<span class="lose " id="team1_bet_count_old"></span>
								<span class="towin text-color-green" style="display:none" id="team1_bet_count_new">10.00</span>
							</div>
						</td>
						<td class="light-blue-bg-2 spark opnForm ODDSBack td_team1_back_2" >
							<a class="back1btn text-color-black">--</span></a>
						</td>
						<td class="link(target, link)ght-blue-bg-3 spark ODDSBack td_team1_back_1" data-team="team1">
							<a  class="back1btn text-color-black"> --<br><span>--</span></a>
						</td>
						<td class="cyan-bg spark ODDSBack td_team1_back_0" >
							<a  class="back1btn text-color-black"> -- <br><span>--</span></a>
						</td>
						<td class="pink-bg sparkLay ODDSLay td_team1_lay_0" >
							<a  data-cls="pink-bg" class="lay1btn text-color-black"> --</span></a>
						</td>
						<td class="light-pink-bg-2 sparkLay ODDSLay td_team1_lay_1" >
							<a data-cls="pink-bg" class="lay1btn text-color-black"> -- <br><span>--</span></a>
						</td>
						<td class="light-pink-bg-3 sparkLay ODDSLay td_team1_lay_2" >
							<a  data-cls="pink-bg" class="lay1btn text-color-black"> -- <br><span>--</span></a>
						</td>
					</tr>
					<tr class="fancy-suspend-tr">
						<td></td>
						<td class="fancy-suspend-td" colspan="6">
							<div class="fancy-suspend black-bg-5 text-color-white"><span>SUSPENDED</span></div>
						</td>
					</tr>
					<tr class="white-bg tr_team2">
						<td> <img src="' . asset('asset/front/img/bars.png') . '"> <b class="team1">' . ucfirst($team[1]) . ' </b>
							<div>
								<span class="lose " id="team1_bet_count_old"></span>
								<span class="towin text-color-green" style="display:none" id="team1_bet_count_new">10.00</span>
							</div>
						</td>
						<td class="light-blue-bg-2 spark opnForm ODDSBack td_team2_back_2" >
							<a class="back1btn text-color-black">--</span></a>
						</td>
						<td class="link(target, link)ght-blue-bg-3 spark ODDSBack td_team2_back_1" data-team="team1">
							<a  class="back1btn text-color-black"> --<br><span>--</span></a>
						</td>
						<td class="cyan-bg spark ODDSBack td_team2_back_0" >
							<a  class="back1btn text-color-black"> -- <br><span>--</span></a>
						</td>
						<td class="pink-bg sparkLay ODDSLay td_team2_lay_0" >
							<a  data-cls="pink-bg" class="lay1btn text-color-black"> --</span></a>
						</td>
						<td class="light-pink-bg-2 sparkLay ODDSLay td_team2_lay_1" >
							<a data-cls="pink-bg" class="lay1btn text-color-black"> -- <br><span>--</span></a>
						</td>
						<td class="light-pink-bg-3 sparkLay ODDSLay td_team2_lay_2" >
							<a  data-cls="pink-bg" class="lay1btn text-color-black"> -- <br><span>--</span></a>
						</td>
					</tr>';
            } else {
                if (@$match_data[0]['runners'][2]['ex']['availableToBack'][0]['price'] != '') {
                    $display = '';
                    $cls = '';
                    if ($team_draw_bet_total == '' && $team1_bet_total == "" && $team2_bet_total == "") {
                        $display = 'style="display:none"';
                    } else {
                        if ($team_draw_bet_total == "")
                            $team_draw_bet_total = 0.00;
                    }
                    if ($team_draw_bet_total != '' && $team_draw_bet_total >= 0) {
                        $cls = 'text-color-green';
                    } else if ($team_draw_bet_total != '' && $team_draw_bet_total < 0) {
                        $cls = 'text-color-red';
                    }

                    $html_chk .= '<tr class="white-bg tr_team3">
						<td> <img src="' . asset('asset/front/img/bars.png') . '"> <b class="team3"> The Draw </b>
							<div>
								<span class="lose ' . $cls . '" ' . $display . ' id="draw_bet_count_old">(<span id="draw_total">' . round($team_draw_bet_total, 2) . '</span>)</span>
								<span class="tolose text-color-red" style="display:none" id="draw_bet_count_new">0.00</span>
							</div>
						</td>
						<td class="light-blue-bg-2  spark ODDSBack td_team3_back_2" data-team="team3">
							<a data-bettype="ODDS" data-team="team3" onclick="opnForm(this);" ' . $login_check . ' data-val="' .
                        @$match_data[0]['runners'][2]['ex']['availableToBack'][2]['price'] . '" data-cls="cyan-bg" class="back1btn text-color-black">
								' .
                        @$match_data[0]['runners'][2]['ex']['availableToBack'][2]['price'] . ' <br><span>' .
                        @$match_data[0]['runners'][2]['ex']['availableToBack'][2]['size'] . '</span>
							</a>
						</td>
						<td class="link(target, link)ght-blue-bg-3 spark ODDSBack td_team3_back_1" data-team="team3">
							<a data-bettype="ODDS" data-team="team3" onclick="opnForm(this);" ' . $login_check . '  data-cls="cyan-bg" data-val="' . @$match_data[0]['runners'][2]['ex']['availableToBack'][1]['price'] . '" class="text-color-black back1btn"> ' .
                        $match_data[0]['runners'][2]['ex']['availableToBack'][1]['price'] . ' <br><span>' .
                        @$match_data[0]['runners'][2]['ex']['availableToBack'][1]['size'] . '</span>
							</a>
						</td>
						<td class="cyan-bg spark ODDSBack td_team3_back_0" data-team="team3">
							<a data-bettype="ODDS" data-team="team3" onclick="opnForm(this);" ' . $login_check . ' data-val="' .
                        @$match_data[0]['runners'][2]['ex']['availableToBack'][0]['price'] . '" data-cls="cyan-bg" class="back1btn text-color-black"> ' .
                        @$match_data[0]['runners'][2]['ex']['availableToBack'][0]['price'] . ' <br><span>' .
                        @$match_data[0]['runners'][2]['ex']['availableToBack'][0]['size'] . '</span>
							</a>
						</td>
						<td class="pink-bg sparkLay ODDSLay td_team3_lay_0" data-team="team3">
							<a data-bettype="ODDS" data-team="team3" onclick="opnForm(this);" ' . $login_check . '  data-val="' .
                        @$match_data[0]['runners'][2]['ex']['availableToLay'][0]['price'] . '" data-cls="pink-bg" class="lay1btn text-color-black"> ' .
                        @$match_data[0]['runners'][2]['ex']['availableToLay'][0]['price'] . ' <br><span>' .
                        @$match_data[0]['runners'][2]['ex']['availableToLay'][0]['size'] . '</span>
							</a>
						</td>
						<td class="light-pink-bg-2 sparkLay ODDSLay td_team3_lay_1" data-team="team3">
							<a data-bettype="ODDS" data-team="team3" onclick="opnForm(this);" ' . $login_check . '  data-val="' .
                        @$match_data[0]['runners'][2]['ex']['availableToLay'][1]['price'] . '" data-cls="pink-bg" class="lay1btn text-color-black"> ' .
                        @$match_data[0]['runners'][2]['ex']['availableToLay'][1]['price'] . ' <br><span>' .
                        @$match_data[0]['runners'][2]['ex']['availableToLay'][1]['size'] . '</span>
							</a>
						</td>
						<td class="light-pink-bg-3 sparkLay ODDSLay td_team3_lay_2" data-team="team3">
							<a data-bettype="ODDS" data-team="team3" onclick="opnForm(this);" ' . $login_check . '  data-val="' .
                        @$match_data[0]['runners'][2]['ex']['availableToLay'][2]['price'] . '" data-cls="pink-bg" class="lay1btn text-color-black"> ' .
                        @$match_data[0]['runners'][2]['ex']['availableToLay'][2]['price'] . ' <br><span>' .
                        @$match_data[0]['runners'][2]['ex']['availableToLay'][2]['size'] . '</span>
							</a>
						</td>
					</tr>
					<tr class="mobileBack tr_team3 mobile_bet_model_div" id="mobile_tr">
						<td colspan="7" class="tr_team3_td_mobile mobile_tr_common_class"></td>
					</tr>';
                }
                //check status
                if (@$match_data[0]['status'] == 'OPEN') {
                    if (isset($match_data[0]['runners'][0]['ex']['availableToBack'][2]['price'])) {
                        $display = '';
                        $cls = '';
                        if ($team_draw_bet_total == '' && $team1_bet_total == "" && $team2_bet_total == "")
                            $display = 'style="display:none"';
                        else {
                            if ($team1_bet_total == '')
                                $team1_bet_total = 0.00;
                        }
                        if ($team1_bet_total != '' && $team1_bet_total >= 0) {
                            $cls = 'text-color-green';
                        } else if ($team1_bet_total != '' && $team1_bet_total < 0) {
                            $cls = 'text-color-red';
                        }
                        $html .= '<tr class="white-bg tr_team1">
									<td> <img src="' . asset('asset/front/img/bars.png') . '"> <b class="team1">' . ucfirst($team[0]) . ' </b>
										<div>
											<span class="lose ' . $cls . '" ' . $display . ' id="team1_bet_count_old">(<span id="team1_total">' . round($team1_bet_total, 2) . '</span>)</span>
											<span class="towin text-color-green" style="display:none" id="team1_bet_count_new">0.00</span>
										</div>
									</td>
									<td class="light-blue-bg-2 spark opnForm ODDSBack td_team1_back_2" data-team="team1">
										<a data-bettype="ODDS" data-team="team1" onclick="opnForm(this);" ' . $login_check . '  data-val="' .
                            @$match_data[0]['runners'][0]['ex']['availableToBack'][2]['price'] . '" data-cls="cyan-bg" class="back1btn text-color-black">' .
                            @$match_data[0]['runners'][0]['ex']['availableToBack'][2]['price'] . ' <br><span>' . @$match_data[0]['runners'][0]['ex']['availableToBack'][2]['size'] . '</span></a>
									</td>
									<td class="link(target, link)ght-blue-bg-3 spark ODDSBack td_team1_back_1" data-team="team1">
										<a data-bettype="ODDS" data-team="team1" onclick="opnForm(this);" ' . $login_check . '  data-cls="cyan-bg" data-val="' .
                            @$match_data[0]['runners'][0]['ex']['availableToBack'][1]['price'] . '" class="back1btn text-color-black"> ' .
                            @$match_data[0]['runners'][0]['ex']['availableToBack'][1]['price'] . '<br><span>' . @$match_data[0]['runners'][0]['ex']['availableToBack'][1]['size'] . '</span></a>
									</td>
									<td class="cyan-bg spark ODDSBack td_team1_back_0" data-team="team1">
										<a data-bettype="ODDS" data-team="team1" onclick="opnForm(this);" ' . $login_check . '  data-val="' .
                            @$match_data[0]['runners'][0]['ex']['availableToBack'][0]['price'] . '" data-cls="cyan-bg" class="back1btn text-color-black"> ' .
                            @$match_data[0]['runners'][0]['ex']['availableToBack'][0]['price'] . ' <br><span>' . @$match_data[0]['runners'][0]['ex']['availableToBack'][0]['size'] . '</span></a>
									</td>
									<td class="pink-bg sparkLay ODDSLay td_team1_lay_0" data-team="team1">
										<a data-bettype="ODDS" data-team="team1" onclick="opnForm(this);" ' . $login_check . '  data-val="' .
                            @$match_data[0]['runners'][0]['ex']['availableToLay'][0]['price'] . '" data-cls="pink-bg" class="lay1btn text-color-black"> ' .
                            @$match_data[0]['runners'][0]['ex']['availableToLay'][0]['price'] . ' <br><span>' . @$match_data[0]['runners'][0]['ex']['availableToLay'][0]['size'] . '</span></a>
									</td>
									<td class="light-pink-bg-2 sparkLay ODDSLay td_team1_lay_1" data-team="team1">
										<a data-bettype="ODDS" data-team="team1" onclick="opnForm(this);" ' . $login_check . ' href="javascript:void(0)" data-val="' .
                            @$match_data[0]['runners'][0]['ex']['availableToLay'][1]['price'] . '" data-cls="pink-bg" class="lay1btn text-color-black"> ' .
                            @$match_data[0]['runners'][0]['ex']['availableToLay'][1]['price'] . ' <br><span>' . @$match_data[0]['runners'][0]['ex']['availableToLay'][1]['size'] . '</span></a>
									</td>
									<td class="light-pink-bg-3 sparkLay ODDSLay td_team1_lay_2" data-team="team1">
										<a data-bettype="ODDS" data-team="team1" onclick="opnForm(this);" ' . $login_check . '  data-val="' .
                            @$match_data[0]['runners'][0]['ex']['availableToLay'][2]['price'] . '" data-cls="pink-bg" class="lay1btn text-color-black"> ' .
                            @$match_data[0]['runners'][0]['ex']['availableToLay'][2]['price'] . ' <br><span>' . @$match_data[0]['runners'][0]['ex']['availableToLay'][2]['size'] . '</span></a>
									</td>
							</tr>
							<tr class="mobileBack tr_team1 mobile_bet_model_div" id="mobile_tr">
								<td colspan="7" class="tr_team1_td_mobile mobile_tr_common_class"></td>
							</tr>';

                    } else {
                        $html .= '<tr class="white-bg tr_team1">
							<td> <img src="' . asset('asset/front/img/bars.png') . '"> <b class="team1">' . ucfirst($team[0]) . ' </b> </td>
							<td class="light-blue-bg-2 td_team1_back_2"><a class="back1btn">
								--</span></a></td>
							<td class="link(target, link)ght-blue-bg-3 td_team1_back_1"><a class="back1btn">--</span></a></td>
							<td class="cyan-bg td_team1_back_0"><a class="back1btn">--</a></td>
							<td class="pink-bg td_team1_lay_0"><a class="lay1btn">--</td>
							<td class="light-pink-bg-2 td_team1_lay_1"><a class="lay1btn">--</td>
							<td class="light-pink-bg-3 td_team1_lay_2"><a class="lay1btn">--</td>
						</tr>';
                    }
                } else {
                    $html_chk .= '
							<tr class="fancy-suspend-tr">
								<td></td>
								<td class="fancy-suspend-td" colspan="6">
									<div class="fancy-suspend black-bg-5 text-color-white"><span>' . @$match_data[0]['status'] . '</span></div>
								</td>

							</tr>
							<tr class="white-bg tr_team1">
									<td> <img src="' . asset('asset/front/img/bars.png') . '"> <b class="team1">' . ucfirst($team[0]) . ' </b>
										<div>
											<span class="lose " id="team1_bet_count_old"></span>
											<span class="towin text-color-green" style="display:none" id="team1_bet_count_new">10.00</span>
										</div>
									</td>
									<td class="light-blue-bg-2 spark opnForm ODDSBack td_team1_back_2" ><a class="back1btn text-color-black">--</span></a></td>
									<td class="link(target, link)ght-blue-bg-3 spark ODDSBack td_team1_back_1" data-team="team1"><a  class="back1btn text-color-black"> --<br><span>--</span></a></td>
									<td class="cyan-bg spark ODDSBack td_team1_back_0" ><a  class="back1btn text-color-black"> -- <br><span>--</span></a></td>
									<td class="pink-bg sparkLay ODDSLay td_team1_lay_0" ><a  data-cls="pink-bg" class="lay1btn text-color-black"> --</span></a></td>
									<td class="light-pink-bg-2 sparkLay ODDSLay td_team1_lay_1" ><a data-cls="pink-bg" class="lay1btn text-color-black"> -- <br><span>--</span></a></td>
									<td class="light-pink-bg-3 sparkLay ODDSLay td_team1_back_2" ><a  data-cls="pink-bg" class="lay1btn text-color-black"> -- <br><span>--</span></a></td>
							</tr>';
                }
                //end for status
                if (@$match_data[0]['status'] == 'OPEN') {
                    if (isset($match_data[0]['runners'][1]['ex']['availableToBack'][2]['price'])) {
                        $display = '';
                        $cls = '';
                        if ($team_draw_bet_total == '' && $team1_bet_total == "" && $team2_bet_total == "")
                            $display = 'style="display:none"';
                        else {
                            if ($team2_bet_total == "")
                                $team2_bet_total = 0.00;
                        }
                        if ($team2_bet_total != '' && $team2_bet_total >= 0) {
                            $cls = 'text-color-green';
                        } else if ($team2_bet_total != '' && $team2_bet_total < 0) {
                            $cls = 'text-color-red';
                        }
                        $html .= '<tr class="white-bg tr_team2">
							<td> <img src="' . asset('asset/front/img/bars.png') . '"> <b class="team2">  ' . ucfirst($team[1]) . ' </b>
								<div>
									<span class="lose ' . $cls . '" ' . $display . ' id="team2_bet_count_old">(<span id="team2_total">' . round($team2_bet_total, 2) . '</span>)</span>
									<span class="towin text-color-green" style="display:none" id="team2_bet_count_new">0.00</span>
								</div>
							</td>
							<td class="light-blue-bg-2 spark opnForm ODDSBack td_team2_back_2" data-team="team2">
								<a data-bettype="ODDS" data-team="team2" onclick="opnForm(this);" ' . $login_check . ' href="javascript:void(0)" data-cls="cyan-bg" data-val="' .
                            @$match_data[0]['runners'][1]['ex']['availableToBack'][2]['price'] . '" class="back1btn text-color-black">' .
                            @$match_data[0]['runners'][1]['ex']['availableToBack'][2]['price'] . ' <br><span>' .
                            @$match_data[0]['runners'][1]['ex']['availableToBack'][2]['size'] . '</span></a></td>
							<td class="link(target, link)ght-blue-bg-3 spark ODDSBack td_team2_back_1" data-team="team2">
								<a data-bettype="ODDS" data-team="team2" onclick="opnForm(this);" ' . $login_check . ' href="javascript:void(0)" data-cls="cyan-bg" data-val="' .
                            @$match_data[0]['runners'][1]['ex']['availableToBack'][1]['price'] . '" class="back1btn text-color-black"> ' .
                            @$match_data[0]['runners'][1]['ex']['availableToBack'][1]['price'] . ' <br><span>' .
                            @$match_data[0]['runners'][1]['ex']['availableToBack'][1]['size'] . '</span>
								</a>
							</td>
							<td class="cyan-bg spark ODDSBack td_team2_back_0" data-team="team2">
								<a data-bettype="ODDS" data-team="team2" onclick="opnForm(this);" ' . $login_check . ' href="javascript:void(0)" data-val="' .
                            @$match_data[0]['runners'][1]['ex']['availableToBack'][0]['price'] . '" data-cls="cyan-bg" class="back1btn text-color-black"> ' .
                            @$match_data[0]['runners'][1]['ex']['availableToBack'][0]['price'] . ' <br><span>' .
                            @$match_data[0]['runners'][1]['ex']['availableToBack'][0]['size'] . '</span>
								</a>
							</td>
							<td class="pink-bg sparkLay ODDSLay td_team2_lay_0" data-team="team2">
								<a data-bettype="ODDS" data-team="team2" onclick="opnForm(this);" ' . $login_check . ' href="javascript:void(0)" data-val="' .
                            @$match_data[0]['runners'][1]['ex']['availableToLay'][0]['price'] . '" data-cls="pink-bg" class="lay1btn text-color-black"> ' .
                            @$match_data[0]['runners'][1]['ex']['availableToLay'][0]['price'] . ' <br><span>' .
                            @$match_data[0]['runners'][1]['ex']['availableToLay'][0]['size'] . '</span>
								</a>
							</td>
							<td class="light-pink-bg-2 sparkLay ODDSLay td_team2_lay_1" data-team="team2">
								<a data-bettype="ODDS" data-team="team2" onclick="opnForm(this);" ' . $login_check . ' href="javascript:void(0)" data-val="' .
                            @$match_data[0]['runners'][1]['ex']['availableToLay'][1]['price'] . '" data-cls="pink-bg" class="lay1btn text-color-black"> ' .
                            @$match_data[0]['runners'][1]['ex']['availableToLay'][1]['price'] . ' <br><span>' .
                            @$match_data[0]['runners'][1]['ex']['availableToLay'][1]['size'] . '</span>
								</a>
							</td>
							<td class="light-pink-bg-3 sparkLay ODDSLay td_team2_lay_2" data-team="team2">
								<a data-bettype="ODDS" data-team="team2" onclick="opnForm(this);" ' . $login_check . ' href="javascript:void(0)" data-val="' .
                            @$match_data[0]['runners'][1]['ex']['availableToLay'][2]['price'] . '" data-cls="pink-bg" class="lay1btn text-color-black"> ' .
                            @$match_data[0]['runners'][1]['ex']['availableToLay'][2]['price'] . ' <br><span>' .
                            @$match_data[0]['runners'][1]['ex']['availableToLay'][2]['size'] . '</span></a>
							</td>
						</tr>
						<tr class="mobileBack tr_team2 mobile_bet_model_div" id="mobile_tr">
									<td colspan="7" class="tr_team2_td_mobile mobile_tr_common_class"></td>
								</tr>
						';
                    } else {
                        $html .= '<tr class="white-bg tr_team2">
								<td> <img src="' . asset('asset/front/img/bars.png') . '"> <b class="team2">  ' . ucfirst($team[1]) . ' </b> </td>
								<td class="light-blue-bg-2 td_team2_back_2"><a class="back1btn">--</a></td>
								<td class="link(target, link)ght-blue-bg-3 td_team2_back_1"><a class="back1btn">
								--</a></td>
								<td class="cyan-bg td_team2_back_0"><a class="back1btn">--</a></td>
								<td class="pink-bg td_team2_lay_0"><a class="lay1btn">--</a></td>
								<td class="light-pink-bg-2 td_team2_lay_1"><a class="lay1btn">--</a></td>
								<td class="light-pink-bg-3 td_team2_lay_2"><a class="lay1btn">--</a></td>
							</tr>';
                    }
                } else {
                    $html_chk .= '
							<tr class="fancy-suspend-tr">
								<td></td>
								<td class="fancy-suspend-td" colspan="6">
									<div class="fancy-suspend black-bg-5 text-color-white"><span>' . @$match_data[0]['status'] . '</span></div>
								</td>
							</tr>
							<tr class="white-bg tr_team2">
									<td> <img src="' . asset('asset/front/img/bars.png') . '"> <b class="team1">' . ucfirst($team[1]) . ' </b>
										<div>
											<span class="lose " id="team1_bet_count_old"></span>
											<span class="towin text-color-green" style="display:none" id="team1_bet_count_new">10.00</span>
										</div>
									</td>
									<td class="light-blue-bg-2 spark opnForm ODDSBack td_team2_back_2" ><a class="back1btn text-color-black">--</span></a></td>
									<td class="link(target, link)ght-blue-bg-3 spark ODDSBack td_team2_back_1" data-team="team1"><a  class="back1btn text-color-black"> --<br><span>--</span></a></td>
									<td class="cyan-bg spark ODDSBack td_team2_back_0" ><a  class="back1btn text-color-black"> -- <br><span>--</span></a></td>
									<td class="pink-bg sparkLay ODDSLay td_team2_lay_0" ><a  data-cls="pink-bg" class="lay1btn text-color-black"> --</span></a></td>
									<td class="light-pink-bg-2 sparkLay ODDSLay td_team2_lay_1" ><a data-cls="pink-bg" class="lay1btn text-color-black"> -- <br><span>--</span></a></td>
									<td class="light-pink-bg-3 sparkLay ODDSLay td_team2_lay_2" ><a  data-cls="pink-bg" class="lay1btn text-color-black"> -- <br><span>--</span></a></td>
							</tr>';
                }
            } // end suspended if
            $html .= $html_chk;
            $html .= '</table>';
        } else {
            $html = 'No data found.';
        }
        return $html;
    }

    public function matchCall($eventId, Request $request)
    {
        $matchtype = $request->matchtype;
        $match_id = $request->match_id;
        $sport = Sport::where('sId', $matchtype)->first();
        $matchList = Match::where('id', $match_id)->where('status', 1)->first();

        if (!$matchList)
            return 'inactive';
        $match_m = @$matchList->suspend_m;
        $matchtype = $sport->id;
        $matchId = $request->matchid;
        $matchname = $request->matchname;
        $event_id = $request->event_id;
        $team = explode(" v ", strtolower($matchname));
        $sport_id = $matchList->sports_id;

        $min_bet_odds_limit = $matchList->min_bet_odds_limit;
        $max_bet_odds_limit = $matchList->max_bet_odds_limit;

        $team1_bet_total = '';
        $team1_bet_class = '';

        $team2_bet_total = '';
        $team2_bet_class = '';

        $team_draw_bet_total = '';
        $team_draw_bet_class = '';

        $match_data = app('App\Http\Controllers\RestApi')->getSingleMatchData($event_id, $matchList->match_id, $request->matchtype);
        $getUserCheck = Session::get('playerUser');
        if (!empty($getUserCheck)) {
            $sessionData = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
        }

        $total_team_count = 0;

        // if(isset($match_data['t1']) && $match_data['t1'][0][0]['b1'] != '')
        // {
        //   $cricketSec = '3';
        // }else{
        // 	$cricketSec = '2';
        // }
        $section = '';
        if ($sport_id == '1') {
            $section = '3';
        } elseif ($sport_id == '2') {
            $section = '2';
        } elseif ($sport_id == '4') {
            $section = 4;
        }

        if (!empty($sessionData)) {
            $getUserCheckuser = Session::get('playerUser');
            if (!empty($getUserCheckuser)) {
                $getUser = User::where('id', $getUserCheckuser->id)->where('check_login', 1)->first();
            }

            $userId = $getUser->id;
            $my_placed_bets = MyBets::where('user_id', $userId)->where('match_id', $event_id)->where('bet_type', 'ODDS')->where('isDeleted', 0)->where('result_declare', 0)->orderby('id', 'DESC')->get();
            $team2_bet_total = 0;
            $team1_bet_total = 0;
            $team_draw_bet_total = 0;
            if (sizeof($my_placed_bets) > 0) {
                foreach ($my_placed_bets as $bet) {
                    $abc = json_decode($bet->extra, true);
                    $total_team_count = count($abc);
                    if (!empty($abc)) {
                        if (count($abc) >= 2) {
                            if (array_key_exists("teamname1", $abc) && array_key_exists("teamname2", $abc)) {
                                //bet on draw
                                if ($bet->bet_side == 'back') {
                                    $team1_bet_total = $team1_bet_total - $bet->exposureAmt;
                                    if (count($abc) >= 2) {
                                        $team_draw_bet_total = $team_draw_bet_total + $bet->bet_profit; ///nnn 16-7-2021
                                    }
                                    $team2_bet_total = $team2_bet_total - $bet->exposureAmt;
                                }
                                if ($bet->bet_side == 'lay') {
                                    $team1_bet_total = $team1_bet_total + $bet->bet_amount;
                                    if (count($abc) >= 2) {
                                        $team_draw_bet_total = $team_draw_bet_total - $bet->exposureAmt;

                                    }
                                    $team2_bet_total = $team2_bet_total + $bet->bet_amount;
                                }
                            } else if (array_key_exists("teamname3", $abc) && array_key_exists("teamname2", $abc)) {
                                //bet on team1
                                if ($bet->bet_side == 'back') {
                                    $team1_bet_total = $team1_bet_total + $bet->bet_profit; ///nnn 16-7-2021
                                    if (count($abc) >= 2) {
                                        $team_draw_bet_total = $team_draw_bet_total - $bet->exposureAmt;
                                    }
                                    $team2_bet_total = $team2_bet_total - $bet->exposureAmt;
                                }
                                if ($bet->bet_side == 'lay') {
                                    $team1_bet_total = $team1_bet_total - $bet->exposureAmt;
                                    if (count($abc) >= 2) {
                                        $team_draw_bet_total = $team_draw_bet_total + $bet->bet_amount;
                                    }
                                    $team2_bet_total = $team2_bet_total + $bet->bet_amount;
                                }
                            } else if (array_key_exists("teamname3", $abc) && array_key_exists("teamname1", $abc)) {
                                //bet on team2
                                if ($bet->bet_side == 'back') {
                                    $team2_bet_total = $team2_bet_total + $bet->bet_profit; ///nnn 16-7-2021
                                    if (count($abc) >= 2) {
                                        $team_draw_bet_total = $team_draw_bet_total - $bet->exposureAmt;
                                    }
                                    $team1_bet_total = $team1_bet_total - $bet->exposureAmt;
                                }
                                if ($bet->bet_side == 'lay') {
                                    $team2_bet_total = $team2_bet_total - $bet->exposureAmt;
                                    if (count($abc) >= 2) {
                                        $team_draw_bet_total = $team_draw_bet_total + $bet->bet_amount;
                                    }
                                    $team1_bet_total = $team1_bet_total + $bet->bet_amount;
                                }
                            }
                        } else if (count($abc) == 1) {
                            if (array_key_exists("teamname1", $abc)) {
                                //bet on team2
                                if ($bet->bet_side == 'back') {
                                    $team2_bet_total = $team2_bet_total + $bet->bet_profit;
                                    $team1_bet_total = $team1_bet_total - $bet->exposureAmt;
                                }
                                if ($bet->bet_side == 'lay') {
                                    $team2_bet_total = $team2_bet_total - $bet->exposureAmt;
                                    $team1_bet_total = $team1_bet_total + $bet->bet_amount;
                                }
                            } else {
                                //bet on team1
                                if ($bet->bet_side == 'back') {
                                    $team1_bet_total = $team1_bet_total + $bet->bet_profit;
                                    $team2_bet_total = $team2_bet_total - $bet->exposureAmt;
                                }
                                if ($bet->bet_side == 'lay') {
                                    $team1_bet_total = $team1_bet_total - $bet->exposureAmt;
                                    $team2_bet_total = $team2_bet_total + $bet->bet_amount;
                                }
                            }
                        }
                    }
                }
            }
        }

        $section = '';
        if ($sport_id == '1') {
            $section = '3';
        } elseif ($sport_id == '2') {
            $section = '2';
        } elseif ($sport_id == '4') {
            $section = 4;
        }

        $team1 = '';
        $team2 = '';
        $team3 = '';
        $html = '';
        $html .= '<table class="table custom-table inplay-table w1-table">
				<tr class="betstr">
				<td class="text-color-grey">' . $section . ' Selections</td>
				<td colspan="2">101.7%</td>
				<td>
					<a class="backall">
						<img src="' . asset('asset/front/img/bluebg1.png') . '">
						<span>Back all</span>
					</a>
				</td>
				<td>
					<a class="layall">
						<img src="' . asset('asset/front/img/pinkbg1.png') . '">
						<span>Lay all</span>
					</a>
				</td>
				<td colspan="2">97.9%</td>
				</tr>';
        $login_check = '';
        $getUserCheck = Session::get('playerUser');
        if (!empty($getUserCheck)) {
            $sessionData = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
        }

        if (!empty($sessionData)) {
            if ($min_bet_odds_limit > 0 && $min_bet_odds_limit != "" && $max_bet_odds_limit > 0 && $max_bet_odds_limit != "")
                $login_check = 'onclick="opnForm(this);"';
        } else {
            $useragent = $_SERVER['HTTP_USER_AGENT'];
            $iPod = stripos($useragent, "iPod");
            $iPad = stripos($useragent, "iPad");
            $iPhone = stripos($useragent, "iPhone");
            $Android = stripos($useragent, "Android");
            $iOS = stripos($useragent, "iOS");

            $DEVICE = ($iPod || $iPad || $iPhone || $Android || $iOS);
            $is_agent = '';
            if (!$DEVICE) {
                $is_agent = 'desktop';
                $login_check = 'data-toggle="modal" data-target="#myLoginModal"';
            } else {
                $is_agent = 'mobile';
                $login_check = 'data-toggle="modal" data-target="#myLoginModalFront"';
            }
        }

        if ($match_data != 0) {
            $html_chk = '';
            if ($match_m == '0') {
                $team1 .= '<a class="back1btn text-color-black">--</span></a>~';
                $team1 .= '<a  class="back1btn text-color-black"> --<br><span>--</span></a>~';
                $team1 .= '<a  class="back1btn text-color-black"> -- <br><span>--</span></a>~';
                $team1 .= '<a  data-cls="pink-bg" class="lay1btn text-color-black"> --</span></a>~';
                $team1 .= '<a data-cls="pink-bg" class="lay1btn text-color-black"> -- <br><span>--</span></a>~';
                $team1 .= '<a  data-cls="pink-bg" class="lay1btn text-color-black"> -- <br><span>--</span></a>';
                $team1 .= '***<tr class="fancy-suspend-tr team1_fancy">
							<td></td>
							<td class="fancy-suspend-td" colspan="6">
								<div class="fancy-suspend black-bg-5 text-color-white"><span>SUSPENDED</span></div>
							</td>
						</tr>';

                $team2 .= '<a class="back1btn text-color-black">--</span></a>~';
                $team2 .= '<a  class="back1btn text-color-black"> --<br><span>--</span></a>~';
                $team2 .= '<a  class="back1btn text-color-black"> -- <br><span>--</span></a>~';
                $team2 .= '<a  data-cls="pink-bg" class="lay1btn text-color-black"> --</span></a>~';
                $team2 .= '<a data-cls="pink-bg" class="lay1btn text-color-black"> -- <br><span>--</span></a>~';
                $team2 .= '<a  data-cls="pink-bg" class="lay1btn text-color-black"> -- <br><span>--</span></a>';

                $team2 .= '***<tr class="fancy-suspend-tr team2_fancy">
							<td></td>
							<td class="fancy-suspend-td" colspan="6">
								<div class="fancy-suspend black-bg-5 text-color-white"><span>SUSPENDED</span></div>
							</td>
						</tr>';

                // if($match_data['t1'][0][2]['b1'] != '')
                // {
                // 	$team3.='<a class="back1btn text-color-black">--</span></a>~';
                // 	$team3.='<a  class="back1btn text-color-black">--<br><span>--</span></a>~';
                // 	$team3.='<a  class="back1btn text-color-black"> -- <br><span>--</span></a>~';
                // 	$team3.='<a  data-cls="pink-bg" class="lay1btn text-color-black"> --</span></a>~';
                // 	$team3.='<a data-cls="pink-bg" class="lay1btn text-color-black"> -- <br><span>--</span></a>~';
                // 	$team3.='<a  data-cls="pink-bg" class="lay1btn text-color-black"> -- <br><span>--</span></a>';

                // 	$team3.='***<tr class="fancy-suspend-tr team3_fancy">
                // 		<td></td>
                // 		<td class="fancy-suspend-td" colspan="6">
                // 			<div class="fancy-suspend black-bg-5 text-color-white"><span>SUSPENDED</span></div>
                // 		</td>
                // 	</tr>';
                // }
            }
            else {
                if ($section == 4) { // cricket
                    if (@$match_data['t1'][0][2]['mstatus'] == 'OPEN') {
                        if (isset($match_data['t1'][0][2]['b1'])) {
                            $team3 .= '<a data-bettype="ODDS" data-team="team3" ' . $login_check . ' data-val="' .
                                @$match_data['t1'][0][2]['b3'] . '" data-cls="cyan-bg" class="back1btn text-color-black">
												' .
                                @$match_data['t1'][0][2]['b3'] . ' <br><span>' .
                                $this->number_format_short(@$match_data['t1'][0][2]['bs3']) . '</span>
											</a>~';
                            $team3 .= '<a data-bettype="ODDS"  data-team="team3" ' . $login_check . '  data-cls="cyan-bg" data-val="' . @$match_data['t1'][0][2]['b2'] . '" class="text-color-black back1btn"> ' .
                                @$match_data['t1'][0][2]['b2'] . ' <br><span>' .
                                $this->number_format_short(@$match_data['t1'][0][2]['bs2']) . '</span>
												</a>~';
                            $team3 .= '<a data-bettype="ODDS" data-team="team3" ' . $login_check . ' data-val="' .
                                @$match_data['t1'][0][2]['b1'] . '" data-cls="cyan-bg" class="back1btn text-color-black"> ' .
                                @$match_data['t1'][0][2]['b1'] . ' <br><span>' .
                                $this->number_format_short(@$match_data['t1'][0][2]['bs1']) . '</span></a>~';

                            $team3 .= '<a data-bettype="ODDS" data-team="team3" ' . $login_check . '  data-val="' .
                                @$match_data['t1'][0][2]['l1'] . '" data-cls="pink-bg" class="lay1btn text-color-black"> ' .
                                @$match_data['t1'][0][2]['l1'] . ' <br><span>' .
                                $this->number_format_short(@$match_data['t1'][0][2]['ls1']) . '</span></a>~';
                            $team3 .= '<a data-bettype="ODDS" data-team="team3" ' . $login_check . '  data-val="' .
                                @$match_data['t1'][0][2]['l2'] . '" data-cls="pink-bg" class="lay1btn text-color-black"> ' .
                                @$match_data['t1'][0][2]['l2'] . ' <br><span>' .
                                $this->number_format_short(@$match_data['t1'][0][2]['ls2']) . '</span></a>~';
                            $team3 .= '<a data-bettype="ODDS" data-team="team3" ' . $login_check . '  data-val="' .
                                @$match_data['t1'][0][2]['l3'] . '" data-cls="pink-bg" class="lay1btn text-color-black"> ' .
                                @$match_data['t1'][0][2]['l3'] . ' <br><span>' .
                                $this->number_format_short(@$match_data['t1'][0][2]['ls3']) . '</span></a>';
                        } else {
                            $team3 .= '<a class="back1btn">--</a>~';
                            $team3 .= '<a class="back1btn">--</a>~';
                            $team3 .= '<a class="back1btn">--</a>~';
                            $team3 .= '<a class="lay1btn">--</a>~';
                            $team3 .= '<a class="lay1btn">--</a>~';
                            $team3 .= '<a class="lay1btn">--</a>';
                        }
                    }
                    // else
                    //  {
                    // 	$team3.='<a class="back1btn text-color-black">--</span></a>~';
                    // 	$team3.='<a  class="back1btn text-color-black"> --<br><span>--</span></a>~';
                    // 	$team3.='<a  class="back1btn text-color-black"> -- <br><span>--</span></a>~';
                    // 	$team3.='<a  data-cls="pink-bg" class="lay1btn text-color-black"> --</span></a>~';
                    // 	$team3.='<a data-cls="pink-bg" class="lay1btn text-color-black"> -- <br><span>--</span></a>~';
                    // 	$team3.='<a  data-cls="pink-bg" class="lay1btn text-color-black"> -- <br><span>--</span></a>';
                    // 	$team3.='***<tr class="fancy-suspend-tr team3_fancy">
                    // 				<td></td>
                    // 				<td class="fancy-suspend-td" colspan="6">
                    // 					<div class="fancy-suspend black-bg-5 text-color-white"><span>'.@$match_data['t1'][0][2]['mstatus'].'</span></div>
                    // 				</td>
                    // 			</tr>';

                    // }
                    //check status
                    if (@$match_data['t1'][0][0]['mstatus'] == 'OPEN') {
                        if (isset($match_data['t1'][0][0]['b1'])) {
                            $team1 .= '<a data-bettype="ODDS" data-team="team1" ' . $login_check . '  data-val="' .
                                @$match_data['t1'][0][0]['b3'] . '" data-cls="cyan-bg" class="back1btn text-color-black">' .
                                @$match_data['t1'][0][0]['b3'] . ' <br><span>' . $this->number_format_short(@$match_data['t1'][0][0]['bs3']) . '</span></a>~';
                            $team1 .= '<a data-bettype="ODDS" data-team="team1" ' . $login_check . '  data-cls="cyan-bg" data-val="' .
                                @$match_data['t1'][0][0]['b2'] . '" class="back1btn text-color-black"> ' .
                                @$match_data['t1'][0][0]['b2'] . '<br><span>' . $this->number_format_short(@$match_data['t1'][0][0]['bs2']) . '</span></a>~';
                            $team1 .= '<a data-bettype="ODDS" data-team="team1" ' . $login_check . '  data-val="' .
                                @$match_data['t1'][0][0]['b1'] . '" data-cls="cyan-bg" class="back1btn text-color-black"> ' .
                                @$match_data['t1'][0][0]['b1'] . ' <br><span>' . $this->number_format_short(@$match_data['t1'][0][0]['bs1']) . '</span></a>~';

                            $team1 .= '<a data-bettype="ODDS" data-team="team1" ' . $login_check . '  data-val="' .
                                @$match_data['t1'][0][0]['l1'] . '" data-cls="pink-bg" class="lay1btn text-color-black"> ' .
                                @$match_data['t1'][0][0]['l1'] . ' <br><span>' . $this->number_format_short(@$match_data['t1'][0][0]['ls1']) . '</span></a>~';
                            $team1 .= '<a data-bettype="ODDS" data-team="team1" ' . $login_check . ' href="javascript:void(0)" data-val="' .
                                @$match_data['t1'][0][0]['l2'] . '" data-cls="pink-bg" class="lay1btn text-color-black"> ' .
                                @$match_data['t1'][0][0]['l2'] . ' <br><span>' . $this->number_format_short(@$match_data['t1'][0][0]['ls2']) . '</span></a>~';
                            $team1 .= '<a data-bettype="ODDS" data-team="team1" ' . $login_check . '  data-val="' .
                                @$match_data['t1'][0][0]['l3'] . '" data-cls="pink-bg" class="lay1btn text-color-black"> ' .
                                @$match_data['t1'][0][0]['l3'] . ' <br><span>' . $this->number_format_short(@$match_data['t1'][0][0]['ls3']) . '</span></a>';
                        } else {
                            $team1 .= '<a class="back1btn">--</span></a>~';
                            $team1 .= '<a class="back1btn">--</span></a>~';
                            $team1 .= '<a class="back1btn">--</a>~';
                            $team1 .= '<a class="lay1btn">--</a>~';
                            $team1 .= '<a class="lay1btn">--</a>~';
                            $team1 .= '<a class="lay1btn">--</a>';

                        }
                    } else {
                        $team1 .= '<a class="back1btn text-color-black">--</span></a>~';
                        $team1 .= '<a  class="back1btn text-color-black"> --<br><span>--</span></a>~';
                        $team1 .= '<a  class="back1btn text-color-black"> -- <br><span>--</span></a>~';
                        $team1 .= '<a  data-cls="pink-bg" class="lay1btn text-color-black"> --</span></a>~';
                        $team1 .= '<a data-cls="pink-bg" class="lay1btn text-color-black"> -- <br><span>--</span></a>~';
                        $team1 .= '<a  data-cls="pink-bg" class="lay1btn text-color-black"> -- <br><span>--</span></a>';
                        $team1 .= '***<tr class="fancy-suspend-tr team1_fancy">
											<td></td>
											<td class="fancy-suspend-td" colspan="6">
												<div class="fancy-suspend black-bg-5 text-color-white"><span>' . @$match_data['t1'][0][0]['mstatus'] . '</span></div>
											</td>
										</tr>';

                    }
                    //end for status
                    if (@$match_data['t1'][0][1]['mstatus'] == 'OPEN') {
                        if (isset($match_data['t1'][0][1]['b1'])) {
                            $team2 .= '<a data-bettype="ODDS" data-team="team2" ' . $login_check . ' href="javascript:void(0)" data-cls="cyan-bg" data-val="' .
                                @$match_data['t1'][0][1]['b3'] . '" class="back1btn text-color-black">' .
                                @$match_data['t1'][0][1]['b3'] . ' <br><span>' .
                                $this->number_format_short(@$match_data['t1'][0][1]['bs3']) . '</span></a>~';
                            $team2 .= '<a data-bettype="ODDS" data-team="team2"  ' . $login_check . ' href="javascript:void(0)" data-cls="cyan-bg" data-val="' .
                                @$match_data['t1'][0][1]['b2'] . '" class="back1btn text-color-black"> ' .
                                @$match_data['t1'][0][1]['b2'] . ' <br><span>' .
                                $this->number_format_short(@$match_data['t1'][0][1]['bs2']) . '</span></a>~';
                            $team2 .= '<a data-bettype="ODDS" data-team="team2" ' . $login_check . ' href="javascript:void(0)" data-val="' .
                                @$match_data['t1'][0][1]['b1'] . '" data-cls="cyan-bg" class="back1btn text-color-black"> ' .
                                @$match_data['t1'][0][1]['b1'] . ' <br><span>' .
                                $this->number_format_short(@$match_data['t1'][0][1]['bs1']) . '</span></a>~';

                            $team2 .= '<a data-bettype="ODDS" data-team="team2" ' . $login_check . ' href="javascript:void(0)" data-val="' .
                                @$match_data['t1'][0][1]['l1'] . '" data-cls="pink-bg" class="lay1btn text-color-black"> ' .
                                @$match_data['t1'][0][1]['l1'] . ' <br><span>' .
                                $this->number_format_short(@$match_data['t1'][0][1]['ls1']) . '</span></a>~';
                            $team2 .= '<a data-bettype="ODDS" data-team="team2" ' . $login_check . ' href="javascript:void(0)" data-val="' .
                                @$match_data['t1'][0][1]['l2'] . '" data-cls="pink-bg" class="lay1btn text-color-black"> ' .
                                @$match_data['t1'][0][1]['l2'] . ' <br><span>' .
                                $this->number_format_short(@$match_data['t1'][0][1]['ls2']) . '</span></a>~';
                            $team2 .= '<a data-bettype="ODDS" data-team="team2" ' . $login_check . ' href="javascript:void(0)" data-val="' .
                                @$match_data['t1'][0][1]['l3'] . '" data-cls="pink-bg" class="lay1btn text-color-black"> ' .
                                @$match_data['t1'][0][1]['l3'] . ' <br><span>' .
                                $this->number_format_short(@$match_data['t1'][0][1]['ls3']) . '</span></a>';
                        } else {
                            $team2 .= '<a class="back1btn">--</a>~';
                            $team2 .= '<a class="back1btn">--</a>~';
                            $team2 .= '<a class="back1btn">--</a>~';
                            $team2 .= '<a class="lay1btn">--</a>~';
                            $team2 .= '<a class="lay1btn">--</a>~';
                            $team2 .= '<a class="lay1btn">--</a>';
                        }
                    } else {
                        $team2 .= '<a class="back1btn text-color-black">--</span></a>~';
                        $team2 .= '<a  class="back1btn text-color-black"> --<br><span>--</span></a>~';
                        $team2 .= '<a  class="back1btn text-color-black"> -- <br><span>--</span></a>~';
                        $team2 .= '<a  data-cls="pink-bg" class="lay1btn text-color-black"> --</span></a>~';
                        $team2 .= '<a data-cls="pink-bg" class="lay1btn text-color-black"> -- <br><span>--</span></a>~';
                        $team2 .= '<a  data-cls="pink-bg" class="lay1btn text-color-black"> -- <br><span>--</span></a>';
                        $team2 .= '***<tr class="fancy-suspend-tr team2_fancy">
											<td></td>
											<td class="fancy-suspend-td" colspan="6">
												<div class="fancy-suspend black-bg-5 text-color-white"><span>' . @$match_data['t1'][0][1]['mstatus'] . '</span></div>
											</td>
										</tr>';
                    }
                }

                if ($section == 2 || $section == 3) { //tennis
                    //check status
                    if (@$match_data[0]['status'] == 'OPEN') {
                        if (isset($match_data[0]['runners'][0])) {
                            $team1 .= '<a data-bettype="ODDS" data-team="team1" ' . $login_check . '  data-val="' .
                                @$match_data[0]['runners'][0]['ex']['availableToBack'][2]['price'] . '" data-cls="cyan-bg" class="back1btn text-color-black">' .
                                @$match_data[0]['runners'][0]['ex']['availableToBack'][2]['price'] . ' <br><span>' . $this->number_format_short(@$match_data[0]['runners'][0]['ex']['availableToBack'][2]['size']) . '</span></a>~';
                            $team1 .= '<a data-bettype="ODDS" data-team="team1" ' . $login_check . '  data-cls="cyan-bg" data-val="' .
                                @$match_data[0]['runners'][0]['ex']['availableToBack'][1]['price'] . '" class="back1btn text-color-black"> ' .
                                @$match_data[0]['runners'][0]['ex']['availableToBack'][1]['price'] . '<br><span>' . $this->number_format_short(@$match_data[0]['runners'][0]['ex']['availableToBack'][1]['size']) . '</span></a>~';
                            $team1 .= '<a data-bettype="ODDS" data-team="team1" ' . $login_check . '  data-val="' .
                                @$match_data[0]['runners'][0]['ex']['availableToBack'][0]['price'] . '" data-cls="cyan-bg" class="back1btn text-color-black"> ' .
                                @$match_data[0]['runners'][0]['ex']['availableToBack'][0]['price'] . ' <br><span>' . $this->number_format_short(@$match_data[0]['runners'][0]['ex']['availableToBack'][0]['size']) . '</span></a>~';

                            $team1 .= '<a data-bettype="ODDS" data-team="team1" ' . $login_check . '  data-val="' .
                                @$match_data[0]['runners'][0]['ex']['availableToLay'][0]['price'] . '" data-cls="pink-bg" class="lay1btn text-color-black"> ' .
                                @$match_data[0]['runners'][0]['ex']['availableToLay'][0]['price'] . ' <br><span>' . $this->number_format_short(@$match_data[0]['runners'][0]['ex']['availableToLay'][0]['size']) . '</span></a>~';
                            $team1 .= '<a data-bettype="ODDS" data-team="team1" ' . $login_check . ' href="javascript:void(0)" data-val="' .
                                @$match_data[0]['runners'][0]['ex']['availableToLay'][1]['price'] . '" data-cls="pink-bg" class="lay1btn text-color-black"> ' .
                                @$match_data[0]['runners'][0]['ex']['availableToLay'][1]['price'] . ' <br><span>' . $this->number_format_short(@$match_data[0]['runners'][0]['ex']['availableToLay'][1]['size']) . '</span></a>~';
                            $team1 .= '<a data-bettype="ODDS" data-team="team1" ' . $login_check . '  data-val="' .
                                @$match_data[0]['runners'][0]['ex']['availableToLay'][2]['price'] . '" data-cls="pink-bg" class="lay1btn text-color-black"> ' .
                                @$match_data[0]['runners'][0]['ex']['availableToLay'][2]['price'] . ' <br><span>' . $this->number_format_short(@$match_data[0]['runners'][0]['ex']['availableToLay'][2]['size']) . '</span></a>';
                        } else {
                            $team1 .= '<a class="back1btn">--</span></a>~';
                            $team1 .= '<a class="back1btn">--</span></a>~';
                            $team1 .= '<a class="back1btn">--</a>~';
                            $team1 .= '<a class="lay1btn">--</a>~';
                            $team1 .= '<a class="lay1btn">--</a>~';
                            $team1 .= '<a class="lay1btn">--</a>';

                        }
                    } else {
                        $team1 .= '<a class="back1btn text-color-black">--</span></a>~';
                        $team1 .= '<a  class="back1btn text-color-black"> --<br><span>--</span></a>~';
                        $team1 .= '<a  class="back1btn text-color-black"> -- <br><span>--</span></a>~';
                        $team1 .= '<a  data-cls="pink-bg" class="lay1btn text-color-black"> --</span></a>~';
                        $team1 .= '<a data-cls="pink-bg" class="lay1btn text-color-black"> -- <br><span>--</span></a>~';
                        $team1 .= '<a  data-cls="pink-bg" class="lay1btn text-color-black"> -- <br><span>--</span></a>';
                        $team1 .= '***<tr class="fancy-suspend-tr team1_fancy">
											<td></td>
											<td class="fancy-suspend-td" colspan="6">
												<div class="fancy-suspend black-bg-5 text-color-white"><span>' . @$match_data['t1'][0][0]['mstatus'] . '</span></div>
											</td>
										</tr>';

                    }

                    //end for status
                    //team2
                    if (@$match_data[0]['status'] == 'OPEN') {
                        if (isset($match_data[0]['runners'][1]['ex']['availableToBack'][2]['price'])) {
                            $team2 .= '<a data-bettype="ODDS" data-team="team2" ' . $login_check . ' href="javascript:void(0)" data-cls="cyan-bg" data-val="' .
                                @$match_data[0]['runners'][1]['ex']['availableToBack'][2]['price'] . '" class="back1btn text-color-black">' .
                                @$match_data[0]['runners'][1]['ex']['availableToBack'][2]['price'] . ' <br><span>' .
                                $this->number_format_short(@$match_data[0]['runners'][1]['ex']['availableToBack'][2]['size']) . '</span></a>~';
                            $team2 .= '<a data-bettype="ODDS" data-team="team2"  ' . $login_check . ' href="javascript:void(0)" data-cls="cyan-bg" data-val="' .
                                @$match_data[0]['runners'][1]['ex']['availableToBack'][1]['price'] . '" class="back1btn text-color-black"> ' .
                                @$match_data[0]['runners'][1]['ex']['availableToBack'][1]['price'] . ' <br><span>' .
                                $this->number_format_short(@$match_data[0]['runners'][1]['ex']['availableToBack'][1]['size']) . '</span></a>~';
                            $team2 .= '<a data-bettype="ODDS" data-team="team2" ' . $login_check . ' href="javascript:void(0)" data-val="' .
                                @$match_data[0]['runners'][1]['ex']['availableToBack'][0]['price'] . '" data-cls="cyan-bg" class="back1btn text-color-black"> ' .
                                @$match_data[0]['runners'][1]['ex']['availableToBack'][0]['price'] . ' <br><span>' .
                                $this->number_format_short(@$match_data[0]['runners'][1]['ex']['availableToBack'][0]['size']) . '</span></a>~';

                            $team2 .= '<a data-bettype="ODDS" data-team="team2" ' . $login_check . ' href="javascript:void(0)" data-val="' .
                                @$match_data[0]['runners'][1]['ex']['availableToLay'][0]['price'] . '" data-cls="pink-bg" class="lay1btn text-color-black"> ' .
                                @$match_data[0]['runners'][1]['ex']['availableToLay'][0]['price'] . ' <br><span>' .
                                $this->number_format_short(@$match_data[0]['runners'][1]['ex']['availableToLay'][0]['size']) . '</span></a>~';
                            $team2 .= '<a data-bettype="ODDS" data-team="team2" ' . $login_check . ' href="javascript:void(0)" data-val="' .
                                @$match_data[0]['runners'][1]['ex']['availableToLay'][1]['price'] . '" data-cls="pink-bg" class="lay1btn text-color-black"> ' .
                                @$match_data[0]['runners'][1]['ex']['availableToLay'][1]['price'] . ' <br><span>' .
                                $this->number_format_short(@$match_data[0]['runners'][1]['ex']['availableToLay'][1]['size']) . '</span></a>~';
                            $team2 .= '<a data-bettype="ODDS" data-team="team2" ' . $login_check . ' href="javascript:void(0)" data-val="' .
                                @$match_data[0]['runners'][1]['ex']['availableToLay'][2]['price'] . '" data-cls="pink-bg" class="lay1btn text-color-black"> ' .
                                @$match_data[0]['runners'][1]['ex']['availableToLay'][2]['price'] . ' <br><span>' .
                                $this->number_format_short($match_data[0]['runners'][1]['ex']['availableToLay'][2]['size']) . '</span></a>';
                        } else {
                            $team2 .= '<a class="back1btn">--</a>~';
                            $team2 .= '<a class="back1btn">--</a>~';
                            $team2 .= '<a class="back1btn">--</a>~';
                            $team2 .= '<a class="lay1btn">--</a>~';
                            $team2 .= '<a class="lay1btn">--</a>~';
                            $team2 .= '<a class="lay1btn">--</a>';
                        }
                    } else {
                        $team2 .= '<a class="back1btn text-color-black">--</span></a>~';
                        $team2 .= '<a  class="back1btn text-color-black"> --<br><span>--</span></a>~';
                        $team2 .= '<a  class="back1btn text-color-black"> -- <br><span>--</span></a>~';
                        $team2 .= '<a  data-cls="pink-bg" class="lay1btn text-color-black"> --</span></a>~';
                        $team2 .= '<a data-cls="pink-bg" class="lay1btn text-color-black"> -- <br><span>--</span></a>~';
                        $team2 .= '<a  data-cls="pink-bg" class="lay1btn text-color-black"> -- <br><span>--</span></a>';
                        $team2 .= '***<tr class="fancy-suspend-tr team2_fancy">
											<td></td>
											<td class="fancy-suspend-td" colspan="6">
												<div class="fancy-suspend black-bg-5 text-color-white"><span>' . @$match_data['t1'][0][1]['mstatus'] . '</span></div>
											</td>
										</tr>';
                    }

                    //team3
                    if (@$match_data[0]['status'] == 'OPEN') {
                        if (isset($match_data[0]['runners'][2]['ex']['availableToBack'][2]['price'])) {
                            $team3 .= '<a data-bettype="ODDS" data-team="team3" ' . $login_check . ' data-val="' .
                                @$match_data[0]['runners'][2]['ex']['availableToBack'][2]['price'] . '" data-cls="cyan-bg" class="back1btn text-color-black">
												' .
                                @$match_data[0]['runners'][2]['ex']['availableToBack'][2]['price'] . ' <br><span>' .
                                $this->number_format_short(@$match_data[0]['runners'][2]['ex']['availableToBack'][2]['size']) . '</span>
											</a>~';
                            $team3 .= '<a data-bettype="ODDS"  data-team="team3" ' . $login_check . '  data-cls="cyan-bg" data-val="' . @$match_data[0]['runners'][2]['ex']['availableToBack'][1]['price'] . '" class="text-color-black back1btn"> ' .
                                @$match_data[0]['runners'][2]['ex']['availableToBack'][1]['price'] . ' <br><span>' .
                                $this->number_format_short(@$match_data[0]['runners'][2]['ex']['availableToBack'][1]['size']) . '</span>
												</a>~';
                            $team3 .= '<a data-bettype="ODDS" data-team="team3" ' . $login_check . ' data-val="' .
                                @$match_data[0]['runners'][2]['ex']['availableToBack'][0]['price'] . '" data-cls="cyan-bg" class="back1btn text-color-black"> ' .
                                @$match_data[0]['runners'][2]['ex']['availableToBack'][0]['price'] . ' <br><span>' .
                                $this->number_format_short(@$match_data[0]['runners'][2]['ex']['availableToBack'][0]['size']) . '</span></a>~';

                            $team3 .= '<a data-bettype="ODDS" data-team="team3" ' . $login_check . '  data-val="' .
                                @$match_data[0]['runners'][2]['ex']['availableToLay'][0]['price'] . '" data-cls="pink-bg" class="lay1btn text-color-black"> ' .
                                @$match_data[0]['runners'][2]['ex']['availableToLay'][0]['price'] . ' <br><span>' .
                                $this->number_format_short(@$match_data[0]['runners'][2]['ex']['availableToLay'][0]['size']) . '</span></a>~';
                            $team3 .= '<a data-bettype="ODDS" data-team="team3" ' . $login_check . '  data-val="' .
                                @$match_data[0]['runners'][2]['ex']['availableToLay'][1]['price'] . '" data-cls="pink-bg" class="lay1btn text-color-black"> ' .
                                @$match_data[0]['runners'][2]['ex']['availableToLay'][1]['price'] . ' <br><span>' .
                                $this->number_format_short(@$match_data[0]['runners'][2]['ex']['availableToLay'][1]['size']) . '</span></a>~';
                            $team3 .= '<a data-bettype="ODDS" data-team="team3" ' . $login_check . '  data-val="' .
                                @$match_data[0]['runners'][2]['ex']['availableToLay'][2]['price'] . '" data-cls="pink-bg" class="lay1btn text-color-black"> ' .
                                @$match_data[0]['runners'][2]['ex']['availableToLay'][2]['price'] . ' <br><span>' .
                                $this->number_format_short(@$match_data[0]['runners'][2]['ex']['availableToLay'][2]['size']) . '</span></a>';
                        } else {
                            $team3 .= '<a class="back1btn">--</a>~';
                            $team3 .= '<a class="back1btn">--</a>~';
                            $team3 .= '<a class="back1btn">--</a>~';
                            $team3 .= '<a class="lay1btn">--</a>~';
                            $team3 .= '<a class="lay1btn">--</a>~';
                            $team3 .= '<a class="lay1btn">--</a>';
                        }
                    } else {
                        $team3 .= '<a class="back1btn text-color-black">--</span></a>~';
                        $team3 .= '<a  class="back1btn text-color-black"> --<br><span>--</span></a>~';
                        $team3 .= '<a  class="back1btn text-color-black"> -- <br><span>--</span></a>~';
                        $team3 .= '<a  data-cls="pink-bg" class="lay1btn text-color-black"> --</span></a>~';
                        $team3 .= '<a data-cls="pink-bg" class="lay1btn text-color-black"> -- <br><span>--</span></a>~';
                        $team3 .= '<a  data-cls="pink-bg" class="lay1btn text-color-black"> -- <br><span>--</span></a>';
                        $team3 .= '***<tr class="fancy-suspend-tr team3_fancy">
											<td></td>
											<td class="fancy-suspend-td" colspan="6">
												<div class="fancy-suspend black-bg-5 text-color-white"><span>' . @$match_data['t1'][0][2]['mstatus'] . '</span></div>
											</td>
										</tr>';

                    }
                }
            }
        } else {
        }


        return $team1 . '===' . $team2 . '===' . $team3 . "@@@@" . $team1_bet_total . '---' . $team2_bet_total . '---' . $team_draw_bet_total;
    }

    public function getmatchdetails()
    {

        $sports = Sport::all();
        $useragent = $_SERVER['HTTP_USER_AGENT'];
        $iPod = stripos($useragent, "iPod");
        $iPad = stripos($useragent, "iPad");
        $iPhone = stripos($useragent, "iPhone");
        $Android = stripos($useragent, "Android");
        $iOS = stripos($useragent, "iOS");

        $DEVICE = ($iPod || $iPad || $iPhone || $Android || $iOS);
        $is_agent = '';
        if (!$DEVICE) {
            $is_agent = 'desktop';
        } else {
            $is_agent = 'mobile';
        }

        $html = '';
        $i = 0;
        $final_html = '';
        $cricket_final_html = '';
        $tennis_final_html = '';
        $soccer_final_html = '';

        $match_array_data_cricket = array();
        $match_array_data_tenis = array();
        $match_array_data_soccer = array();

        foreach ($sports as $sport) {
            $match_link = Match::where('sports_id', $sport->sId)->where('status', 1)->where('suspend_m', 1)->where('status_m', 1)->where('isDeleted', 0)->where('winner', NULL)->orderBy('match_date', 'ASC')->get();
            foreach ($match_link as $match) {
                if (@$match->match_id != '') {
                    if ($match->sports_id == 4)
                        $match_array_data_cricket[] = $match->match_id;
                    else if ($match->sports_id == 2)
                        $match_array_data_tenis[] = $match->match_id;
                    else if ($match->sports_id == 1)
                        $match_array_data_soccer[] = $match->match_id;
                }
            }
        }
        $imp_match_array_data_cricket = @implode(",", $match_array_data_cricket);
        $imp_match_array_data_tenis = @implode(",", $match_array_data_tenis);
        $imp_match_array_data_soccer = @implode(",", $match_array_data_soccer);

        $mdata = array();
        $inplay = 0;

        //for match original date and time
        $get_match_type = app('App\Http\Controllers\RestApi')->GetAllMatch();
        $st_criket = array();
        $ra_criket = 0;
        $st_soccer = array();
        $st_tennis = array();
        $ra_soccer = 0;
        $ra_tennis = 0;
        foreach ($get_match_type as $key2 => $value2) {
            $dt = '';
            $mid = '';
            $eid = '';
            foreach (@$value2 as $key3 => $value3) {
                if ($key3 == 'MarketId') {
                    $mid = $value3;
                }
                if ($key3 == 'EventId') {
                    $eid = $value3;
                }
                if ($key3 == 'StartTime') {
                    $dt = $value3;
                }
                if ($key3 == 'SportsId') {
                    if ($value3 == 4) {
                        $st_criket[$ra_criket]['StartTime'] = $dt;
                        $st_criket[$ra_criket]['EventId'] = $mid;
                        $st_criket[$ra_criket]['MarketId'] = $eid;
                        $ra_criket++;
                    } else if ($value3 == 2) {
                        $st_tennis[$ra_tennis]['StartTime'] = $dt;
                        $st_tennis[$ra_tennis]['EventId'] = $mid;
                        $st_tennis[$ra_tennis]['MarketId'] = $eid;
                        $ra_tennis++;
                    } else if ($value3 == 1) {
                        $st_soccer[$ra_soccer]['StartTime'] = $dt;
                        $st_soccer[$ra_soccer]['EventId'] = $mid;
                        $st_soccer[$ra_soccer]['MarketId'] = $eid;
                        $ra_soccer++;
                    }
                }
            }
        }
        if (!empty($imp_match_array_data_cricket)) {
            $url = 'http://69.30.238.2:3644/odds/multiple?ids=' . $imp_match_array_data_cricket;
            $headers = array('Content-Type: application/json');
            $process = curl_init();
            curl_setopt($process, CURLOPT_URL, $url);
            curl_setopt($process, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($process, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($process, CURLOPT_TIMEOUT, 30);
            curl_setopt($process, CURLOPT_HTTPGET, 1);
            curl_setopt($process, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
            $return = curl_exec($process);
            curl_close($process);

            $arrayA = json_decode($return, true);
            $arrayB = $this->search($arrayA, 'inplay', '1');
            $match_data_merge = array_merge($arrayB, $arrayA);
            $match_data_arrange = array_unique($match_data_merge, SORT_REGULAR);
            $match_data = array_values($match_data_arrange);


            if (!empty($match_data)) {
                $cricket = '<div class="programe-setcricket">
			<div class="firstblock-cricket lightblue-bg1">
			<span class="fir-col1"></span>
			<span class="fir-col2">1</span>
			<span class="fir-col2">X</span>
			<span class="fir-col2">2</span>
			<span class="fir-col3"></span>
			</div>';

                $html = '';
                for ($j = 0; $j < sizeof($match_data); $j++) {
                    $inplay_game = '';
                    $mobileInplay = '';
                    $match_detail = Match::where('match_id', $match_data[$j]['marketId'])->where('status', 1)->first();
                    $match_data_status = app('App\Http\Controllers\RestApi')->Fancy_and_Bookmaker_DetailCall($match_detail->event_id, $match_detail->match_id, 4);

                    if (isset($match_data[$j]['inplay'])) {
                        if ($match_data[$j]['inplay'] == 1) {
                            $dt = '';
                            $style = "fir-col1-green";
                            $inplay_game = " <span style='color:green' class='deskinplay'>In-Play</span>";
                            $mobileInplay = '<span style="color:green" class="mplay">In-Play</span>';
                        } else {
                            $match_date = '';
                            $dt = '';
                            $key = array_search($match_detail['event_id'], array_column($st_criket, 'MarketId'));
                            if ($key)
                                // ss comment for incorrect index
                                //$dt=$st_criket[$key+1]['StartTime'];
                                $dt = $st_criket[$key]['StartTime'];

                            $new = explode("T", $dt);
                            $first = @$new[0];
                            $second = @$new[1];
                            $second = explode(".", $second);
                            $timestamp = $first . " " . @$second[0];
                            $date = Carbon::parse($timestamp);
                            $date->addMinutes(330);

                            if (Carbon::parse($date)->isToday()) {
                                $match_date = date('h:i A', strtotime($date));
                            } else if (Carbon::parse($date)->isTomorrow())
                                $match_date = 'Tomorrow ' . date('h:i A', strtotime($date));
                            else
                                $match_date = date('d-m-Y h:i A', strtotime($date));

                            $dt = $match_date;
                            $style = "fir-col1";
                            $inplay_game = '';
                            $mobileInplay = '';
                        }
                    } else {
                        $match_date = '';
                        if (Carbon::parse($match_detail['match_date'])->isToday())
                            $match_date = date('h:i A', strtotime($match_detail['match_date']));
                        else if (Carbon::parse($match_detail['match_date'])->isTomorrow())
                            $match_date = 'Tomorrow ' . date('h:i A', strtotime($match_detail['match_date']));
                        else
                            $match_date = date('d-m-Y h:i A', strtotime($match_detail['match_date']));

                        $dt = $match_date;
                        $style = "fir-col1";
                        $inplay_game = '';
                        $mobileInplay = '';
                    }

                    $fancy = '';
                    $mobileFancy = '';
                    if (!empty($match_data_status['fancy'][0]) && $inplay_game != '')
                        $fancy = '<span style="color:green" class="game-fancy in-play blue-bg-3 text-color-white">F</span>';
                    elseif (!empty($match_data_status['fancy'][0]) && $inplay_game == '')
                        $fancy = '<span style="color:green" class="game-fancy blue-bg-3 text-color-white">F</span>';

                    if (!empty($match_data_status['fancy'][0]) && $mobileInplay != '')
                        $mobileFancy = '<span style="color:green" class="game-fancy in-play blue-bg-3 text-color-white">F</span>';
                    elseif (!empty($match_data_status['fancy'][0]) && $mobileInplay == '')
                        $mobileFancy = '<span style="color:green" class="game-fancy in-play blue-bg-3 text-color-white">F</span>';


                    $bookmaker = '';
                    $mobileBookmaker = '';
                    if (!empty($match_data_status['bm'][0])) {
                        /*$bookmaker='<img class="bmclass" src="'.asset('asset/front/img/bm.png').'">';*/
                        $bookmaker = '<span style="color:green;margin-right: 40px;" class="game-fancy in-play blue-bg-3 text-color-white">B</span>';

                        $mobileBookmaker = '<span style="color:green" class="game-fancy in-play blue-bg-3 text-color-white">B</span>';
                    }
                    if ($is_agent == 'mobile') {
                        $matchName = substr($match_detail['match_name'], 0, 36) . '...';
                    } else {
                        $matchName = $match_detail['match_name'];
                    }

                    $check_closed_for_cricket_start = '';
                    $check_closed_for_cricket_end = '';
                    if (!isset($match_data[$j]['runners'][0]['ex']['availableToBack'][0]['price']) && !isset($match_data[$j]['runners'][0]['ex']['availableToLay'][0]['price'])) {
                    }
                    if (isset($match_data[$j]['runners'][0]['ex']['availableToBack'][0]['price']) && isset($match_data[$j]['runners'][0]['ex']['availableToLay'][0]['price'])) {

                        $html .= '
				<div class="secondblock-cricket white-bg" style="position:relative">
					<div class="mblinplay">
						' . $mobileFancy . '
						' . $mobileBookmaker . '
						' . $mobileInplay . '
					</div>
					<span class="' . $style . ' desk">
						<a href="matchDetail/' . $match_detail['id'] . '" class="text-color-blue-light">' . $matchName . $inplay_game . '</a>
						<div>' . $dt . '</div>' . $bookmaker . $fancy . '
					</span>
					<span class="fir-col2">
						<a class="backbtn lightblue-bg2">' . $match_data[$j]['runners'][0]['ex']['availableToBack'][0]['price'] . '</a>
						<a class="laybtn lightpink-bg1">' . $match_data[$j]['runners'][0]['ex']['availableToLay'][0]['price'] . '</a>
					</span>
					';
                    } else {
                        $html .= '
					<div class="secondblock-cricket white-bg" style="position:relative">
						<span class="' . $style . ' desk">
							<a href="matchDetail/' . $match_detail['id'] . '" class="text-color-blue-light">' . $matchName . $inplay_game . '</a>
							<div>' . $dt . '</div>' . $bookmaker . $fancy . '
						</span>
						' . $check_closed_for_cricket_start . '
						<span class="fir-col2">
							<a class="backbtn lightblue-bg2">--</a>
							<a class="laybtn lightpink-bg1">--</a>
						</span>';
                    }
                    if (isset($match_data[$j]['runners'][2])) {
                        if (@$match_data[$j]['runners'][2]['ex']['availableToBack'][0]['price'] != '' && @$match_data[$j]['runners'][2]['ex']['availableToLay'][0]['price'] != '') {
                            $html .= '<span class="fir-col2">
					<a class="backbtn lightblue-bg2">' . @$match_data[$j]['runners'][2]['ex']['availableToBack'][0]['price'] . '</a>
					<a class="laybtn lightpink-bg1">' . @$match_data[$j]['runners'][2]['ex']['availableToLay'][0]['price'] . '</a>
					</span>';
                        } else {
                            $html .= '<span class="fir-col2">
						<a class="backbtn lightblue-bg2">--</a>
						<a class="laybtn lightpink-bg1">--</a>
					</span>';
                        }
                    } else {
                        $html .= '<span class="fir-col2">
						<a class="backbtn lightblue-bg2">--</a>
						<a class="laybtn lightpink-bg1">--</a>
					</span>';
                    }
                    if (isset($match_data[$j]['runners'][1]['ex']['availableToBack'][0]['price']) && isset($match_data[$j]['runners'][1]['ex']['availableToLay'][0]['price'])) {
                        if (@$match_data[$j]['runners'][1]['ex']['availableToBack'][0]['price'] != '' && @$match_data[$j]['runners'][1]['ex']['availableToLay'][0]['price'] != "") {
                            $html .= '<span class="fir-col2">
						<a class="backbtn lightblue-bg2">' . @$match_data[$j]['runners'][1]['ex']['availableToBack'][0]['price'] . '</a>
						<a class="laybtn lightpink-bg1">' . @$match_data[$j]['runners'][1]['ex']['availableToLay'][0]['price'] . '</a>
						</span>
						<span class="fir-col3">
							<a><img src="' . asset('asset/front/img/round-pin.png') . '"> <img class="hover-img" src="public/asset/front/img/round-pin1.png"></a>
						</span>
						</div>';
                        } else {
                            $html .= '<span class="fir-col2">
						<a class="backbtn lightblue-bg2">--</a>
						<a class="laybtn lightpink-bg1">--</a>
						</span>
						<span class="fir-col3">
							<a><img src="' . asset('asset/front/img/round-pin.png') . '"> <img class="hover-img" src="public/asset/front/img/round-pin1.png"></a>
						</span>
						' . $check_closed_for_cricket_end . '</div>
						';
                        }
                    } else {
                        $html .= '<span class="fir-col2">
					<a class="backbtn lightblue-bg2">--</a>
					<a class="laybtn lightpink-bg1">--</a>
					</span>
					<span class="fir-col3">
						<a><img src="' . asset('asset/front/img/round-pin.png') . '"> <img class="hover-img" src="public/asset/front/img/round-pin1.png"></a>
					</span>
					' . $check_closed_for_cricket_end . '</div>';
                    }

                }
                $cricket_final_html .= $html;
                $final_html .= $cricket . $cricket_final_html . '</div>';
            }
            return $final_html;
        } else {
            return "No match found.";
        }
    }

    function array_sort_by_column(&$array, $column, $direction = SORT_ASC)
    {
        $reference_array = array();

        foreach ($array as $key => $row) {
            $new = explode("T", $row[$column]);
            $first = @$new[0];
            $second = @$new[1];
            $second = explode(".", $second);
            $timestamp = $first . " " . @$second[0];

            $date = Carbon::parse($timestamp);
            $date->addMinutes(330);
            $m_date = date('d-m-Y H:i', strtotime($date));
            $reference_array[$key] = $m_date;
        }

        array_multisort($reference_array, $direction, $array);
        /*  echo "<pre>";
    print_r($array);
    exit;*/
    }

    public function getmatchdetailTwo()
    {
        $sports = Sport::all();
        //check is mobile or desktop
        $useragent = $_SERVER['HTTP_USER_AGENT'];
        $iPod = stripos($useragent, "iPod");
        $iPad = stripos($useragent, "iPad");
        $iPhone = stripos($useragent, "iPhone");
        $Android = stripos($useragent, "Android");
        $iOS = stripos($useragent, "iOS");

        $DEVICE = ($iPod || $iPad || $iPhone || $Android || $iOS);
        $is_agent = '';
        if (!$DEVICE) {
            $is_agent = 'desktop';
        } else {
            $is_agent = 'mobile';
        }

        $html = '';
        $i = 0;
        $final_html = '';
        $cricket_final_html = '';
        $tennis_final_html = '';
        $soccer_final_html = '';

        $match_array_data_cricket = array();
        $match_array_data_tenis = array();
        $match_array_data_soccer = array();

        foreach ($sports as $sport) {
            $match_link = Match::where('sports_id', $sport->sId)->where('status', 1)->where('suspend_m', 1)->where('status_m', 1)->where('isDeleted', 0)->where('winner', NULL)->orderBy('match_date', 'ASC')->get();
            foreach ($match_link as $match) {
                if (@$match->match_id != '') {
                    if ($match->sports_id == 4)
                        $match_array_data_cricket[] = $match->match_id;
                    else if ($match->sports_id == 2)
                        $match_array_data_tenis[] = $match->match_id;
                    else if ($match->sports_id == 1)
                        $match_array_data_soccer[] = $match->match_id;
                }
            }
        }
        $imp_match_array_data_cricket = @implode(",", $match_array_data_cricket);
        $imp_match_array_data_tenis = @implode(",", $match_array_data_tenis);
        $imp_match_array_data_soccer = @implode(",", $match_array_data_soccer);

        $mdata = array();
        $inplay = 0;
        /*echo $imp_match_array_data_soccer;
		exit;*/
        //for match original date and time
        $get_match_type = app('App\Http\Controllers\RestApi')->GetAllMatch();
        $st_criket = array();
        $ra_criket = 0;
        $st_soccer = array();
        $st_tennis = array();
        $ra_soccer = 0;
        $ra_tennis = 0;
        /*echo "<pre>";
		print_r($get_match_type);
		exit;*/
        foreach ($get_match_type as $key2 => $value2) {
            $dt = '';
            $mid = '';
            $eid = '';
            foreach (@$value2 as $key3 => $value3) {
                if ($key3 == 'MarketId') {
                    $mid = $value3;
                }
                if ($key3 == 'EventId') {
                    $eid = $value3;
                }
                if ($key3 == 'StartTime') {
                    $dt = $value3;
                }
                if ($key3 == 'SportsId') {
                    if ($value3 == 4) {
                        $st_criket[$ra_criket]['StartTime'] = $dt;
                        $st_criket[$ra_criket]['EventId'] = $mid;
                        $st_criket[$ra_criket]['MarketId'] = $eid;
                        $ra_criket++;
                    } else if ($value3 == 2) {
                        $st_tennis[$ra_tennis]['StartTime'] = $dt;
                        $st_tennis[$ra_tennis]['EventId'] = $mid;
                        $st_tennis[$ra_tennis]['MarketId'] = $eid;
                        $ra_tennis++;
                    } else if ($value3 == 1) {
                        $st_soccer[$ra_soccer]['StartTime'] = $dt;
                        $st_soccer[$ra_soccer]['EventId'] = $mid;
                        $st_soccer[$ra_soccer]['MarketId'] = $eid;
                        $ra_soccer++;
                    }
                }
            }
        }

        if (!empty($imp_match_array_data_cricket)) {
            $url = 'http://69.30.238.2:3644/odds/multiple?ids=' . $imp_match_array_data_cricket;
            $headers = array('Content-Type: application/json');
            $process = curl_init();
            curl_setopt($process, CURLOPT_URL, $url);
            curl_setopt($process, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($process, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($process, CURLOPT_TIMEOUT, 30);
            curl_setopt($process, CURLOPT_HTTPGET, 1);
            curl_setopt($process, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
            $return = curl_exec($process);
            curl_close($process);

            $arrayA = json_decode($return, true);
            $arrayB = $this->search($arrayA, 'inplay', '1');
            $match_data_merge = array_merge($arrayB, $arrayA);
            $match_data_arrange = array_unique($match_data_merge, SORT_REGULAR);
            $match_data = array_values($match_data_arrange);
            /*echo "<pre>";
		print_r($arrayA);
		exit;*/

            $jk = 0;
            $new_match_data = array();
            $c = array();
            $cccarray = array();
            $timeArray = array();
            foreach ($arrayA as $value) {
                $match_detailtes = Match::where('match_id', $value['marketId'])->where('status', 1)->first();
                $key = array_search($match_detailtes['event_id'], array_column($st_criket, 'MarketId'));
                if ($key) {
                    if ($value['status'] != 'CLOSED') {
                        $dt = $st_criket[$key]['StartTime'];
                        $new = explode("T", $dt);
                        $first = @$new[0];
                        $second = @$new[1];
                        $second = explode(".", $second);
                        $timestamp = $first . " " . @$second[0];

                        $date = Carbon::parse($timestamp);
                        $date->addMinutes(330);
                        $m_date = date('d-m-Y h:i A', strtotime($date));

                        $timeArray[] = $m_date;
                        //array_push($match_data[$jj],$dt);
                        $arrayA[$jk]['matchTime'] = $m_date;
                        /*echo "<pre>";
				print_r($arrayA);
				exit;*/
                        $cccarray[] = $arrayA[$jk];
                        /*echo "<pre>";
				print_r($cccarray);
				exit;*/
                    }
                    $jk++;
                }

            }

            foreach ($cccarray as $key => $part) {
                $sort[$key] = strtotime($part['matchTime']);
            }
            array_multisort($sort, SORT_ASC, $cccarray);

            $match_data = $cccarray;
            if (!empty($match_data)) {
                $cricket = '<div class="programe-setcricket">
		<div class="firstblock-cricket lightblue-bg1">
		<span class="fir-col1"></span>
        <span class="fir-col2">1</span>
        <span class="fir-col2">X</span>
        <span class="fir-col2">2</span>
        <span class="fir-col3"></span>
        </div>';

                $html = '';
                for ($j = 0; $j < sizeof($match_data); $j++) {
                    $inplay_game = '';
                    $match_detail = Match::where('match_id', $match_data[$j]['marketId'])->where('status', 1)->first();
                    /*echo $match_detail->event_id;
			exit;*/
                    // status suspend
                    if ($match_data[$j]['status'] == 'CLOSED') {
                        $susUpd = Match::where('id', $match_detail->id)->where('match_finish', '!=', 1)->first();
                        if (!empty($susUpd)) {
                            $susUpd->match_finish = 1;
                            $susUpd->update();
                        }

                    }
                    $match_data_status = app('App\Http\Controllers\RestApi')->Fancy_and_Bookmaker_DetailCall($match_detail->event_id, $match_detail->match_id, 4);
                    /*echo "<pre>";
			print_r($match_data_status['fancy'][0]);
			exit;	*/

                    if (isset($match_data[$j]['inplay'])) {
                        if ($match_data[$j]['inplay'] == 1) {
                            $dt = '';
                            $style = "fir-col1-green";
                            $inplay_game = " <span style='color:green' class='deskinplay'>In-Play</span>";
                            $mobileInplay = '<span style="color:green" class="mplay">In-Play</span>';
                        } else {
                            $match_date = '';
                            $dt = '';
                            $key = array_search($match_detail['event_id'], array_column($st_criket, 'MarketId'));
                            /*echo $match_detail['event_id'];
					exit;*/
                            if ($key)
                                // ss for incorrect index
                                //$dt=$st_criket[$key+1]['StartTime'];
                                $dt = $st_criket[$key]['StartTime'];

                            $new = explode("T", $dt);
                            $first = @$new[0];
                            $second = @$new[1];
                            $second = explode(".", $second);
                            $timestamp = $first . " " . @$second[0];

                            $date = Carbon::parse($timestamp);
                            $date->addMinutes(330);

                            if (Carbon::parse($date)->isToday()) {
                                $match_date = date('h:i A', strtotime($date));
                            } else if (Carbon::parse($date)->isTomorrow())
                                $match_date = 'Tomorrow ' . date('h:i A', strtotime($date));
                            else
                                $match_date = date('d-m-Y h:i A', strtotime($date));

                            $dt = $match_date;
                            $style = "fir-col1";
                            $inplay_game = '';
                            $mobileInplay = '';
                        }
                    } else {
                        $match_date = '';
                        if (Carbon::parse($match_detail['match_date'])->isToday())
                            $match_date = date('h:i A', strtotime($match_detail['match_date']));
                        else if (Carbon::parse($match_detail['match_date'])->isTomorrow())
                            $match_date = 'Tomorrow ' . date('h:i A', strtotime($match_detail['match_date']));
                        else
                            $match_date = date('d-m-Y h:i A', strtotime($match_detail['match_date']));

                        $dt = $match_date;
                        $style = "fir-col1";
                        $inplay_game = '';
                        $mobileInplay = '';
                    }

                    // Match date update
                    /*foreach ($match_array_data_cricket as $value) {

				$matchDateUpdate = Match::where('match_id',$value)->first();
				$matchDateUpdate->match_date = date('d-m-Y h:i A',strtotime($date));
				$matchDateUpdate->update();
			}*/

                    $fancy = '';
                    $mobileFancy = '';
                    if (!empty($match_data_status['fancy'][0]) && $inplay_game != '')
                        $fancy = '<span style="color:green" class="game-fancy in-play blue-bg-3 text-color-white">F</span>';
                    elseif (!empty($match_data_status['fancy'][0]) && $inplay_game == '')
                        $fancy = '<span style="color:green" class="game-fancy blue-bg-3 text-color-white">F</span>';
                    if (!empty($match_data_status['fancy'][0]) && $mobileInplay != '')
                        $mobileFancy = '<span style="color:green" class="game-fancy in-play blue-bg-3 text-color-white">F</span>';
                    elseif (!empty($match_data_status['fancy'][0]) && $mobileInplay == '')
                        $mobileFancy = '<span style="color:green" class="game-fancy in-play blue-bg-3 text-color-white">F</span>';

                    $bookmaker = '';
                    $mobileBookmaker = '';
                    if (!empty($match_data_status['bm'][0])) {
                        /*$bookmaker='<img class="bmclass" src="'.asset('asset/front/img/bm.png').'">';*/
                        //$bookmaker='<span class="yellow-bg text-color-white">B</span>';
                        $bookmaker = '<span style="color:green;margin-right: 40px;" class="game-fancy in-play blue-bg-3 text-color-white">B</span>';
                        $mobileBookmaker = '<span style="color:green" class="game-fancy in-play blue-bg-3 text-color-white">B</span>';
                    }
                    if ($is_agent == 'mobile') {
                        $matchName = substr($match_detail['match_name'], 0, 36) . '...';
                    } else {
                        $matchName = $match_detail['match_name'];
                    }
                    $check_closed_for_cricket_start = '';
                    $check_closed_for_cricket_end = '';
                    if (!isset($match_data[$j]['runners'][0]['ex']['availableToBack'][0]['price']) && !isset($match_data[$j]['runners'][0]['ex']['availableToLay'][0]['price'])) {
                    }
                    if (isset($match_data[$j]['runners'][0]['ex']['availableToBack'][0]['price']) && isset($match_data[$j]['runners'][0]['ex']['availableToLay'][0]['price'])) {

                        $html .= '
			<div class="secondblock-cricket white-bg" style="position:relative">
			<div class="mblinplay">
					' . $mobileFancy . '
					' . $mobileBookmaker . '
					' . $mobileInplay . '
				</div>
				<span class="' . $style . ' desk"  >
					<a href="matchDetail/' . $match_detail['id'] . '" class="text-color-blue-light">' . $matchName . $inplay_game . '</a>
					<div>' . $dt . '</div>' . $bookmaker . $fancy . '
				</span>
				<span class="fir-col2">
					<a class="backbtn lightblue-bg2">' . $match_data[$j]['runners'][0]['ex']['availableToBack'][0]['price'] . '</a>
					<a class="laybtn lightpink-bg1">' . $match_data[$j]['runners'][0]['ex']['availableToLay'][0]['price'] . '</a>
				</span>
				';
                    } else {

                        $html .= '
				<div class="secondblock-cricket white-bg" style="position:relative">
					<span class="' . $style . '"  >
						<a href="matchDetail/' . $match_detail['id'] . '" class="text-color-blue-light">' . $matchName . $inplay_game . '</a>
						<div>' . $dt . '</div>' . $bookmaker . $fancy . '
					</span>
					' . $check_closed_for_cricket_start . '
					<span class="fir-col2">
						<a class="backbtn lightblue-bg2">--</a>
						<a class="laybtn lightpink-bg1">--</a>
					</span>';
                    }
                    if (isset($match_data[$j]['runners'][2])) {
                        if (@$match_data[$j]['runners'][2]['ex']['availableToBack'][0]['price'] != '' && @$match_data[$j]['runners'][2]['ex']['availableToLay'][0]['price'] != '') {
                            $html .= '<span class="fir-col2">
				<a class="backbtn lightblue-bg2">' . @$match_data[$j]['runners'][2]['ex']['availableToBack'][0]['price'] . '</a>
				<a class="laybtn lightpink-bg1">' . @$match_data[$j]['runners'][2]['ex']['availableToLay'][0]['price'] . '</a>
				</span>';
                        } else {
                            $html .= '<span class="fir-col2">
					<a class="backbtn lightblue-bg2">--</a>
					<a class="laybtn lightpink-bg1">--</a>
				</span>';
                        }
                    } else {
                        $html .= '<span class="fir-col2">
					<a class="backbtn lightblue-bg2">--</a>
					<a class="laybtn lightpink-bg1">--</a>
				</span>';
                    }
                    if (isset($match_data[$j]['runners'][1]['ex']['availableToBack'][0]['price']) && isset($match_data[$j]['runners'][1]['ex']['availableToLay'][0]['price'])) {
                        if (@$match_data[$j]['runners'][1]['ex']['availableToBack'][0]['price'] != '' && @$match_data[$j]['runners'][1]['ex']['availableToLay'][0]['price'] != "") {
                            $html .= '<span class="fir-col2">
					<a class="backbtn lightblue-bg2">' . @$match_data[$j]['runners'][1]['ex']['availableToBack'][0]['price'] . '</a>
					<a class="laybtn lightpink-bg1">' . @$match_data[$j]['runners'][1]['ex']['availableToLay'][0]['price'] . '</a>
					</span>
					<span class="fir-col3">
	                    <a><img src="' . asset('asset/front/img/round-pin.png') . '"> <img class="hover-img" src="public/asset/front/img/round-pin1.png"></a>
	                </span>
					</div>';
                        } else {
                            $html .= '<span class="fir-col2">
					<a class="backbtn lightblue-bg2">--</a>
					<a class="laybtn lightpink-bg1">--</a>
					</span>
					<span class="fir-col3">
	                    <a><img src="' . asset('asset/front/img/round-pin.png') . '"> <img class="hover-img" src="public/asset/front/img/round-pin1.png"></a>
	                </span>
					' . $check_closed_for_cricket_end . '</div>
					';
                        }
                    } else {
                        $html .= '<span class="fir-col2">
				<a class="backbtn lightblue-bg2">--</a>
				<a class="laybtn lightpink-bg1">--</a>
				</span>
				<span class="fir-col3">
                    <a><img src="' . asset('asset/front/img/round-pin.png') . '"> <img class="hover-img" src="public/asset/front/img/round-pin1.png"></a>
                </span>
				' . $check_closed_for_cricket_end . '</div>';
                    }

                }
                $cricket_final_html .= $html;
                $final_html .= $cricket . $cricket_final_html . '</div>';
            }
        } else {
            $final_html .= 'No match found.';
        }

        //for tennis
        if (!empty($imp_match_array_data_tenis)) {
            $url = 'http://69.30.238.2:3644/odds/multiple?ids=' . $imp_match_array_data_tenis;
            $headers = array('Content-Type: application/json');
            $process = curl_init();
            curl_setopt($process, CURLOPT_URL, $url);
            curl_setopt($process, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($process, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($process, CURLOPT_TIMEOUT, 30);
            curl_setopt($process, CURLOPT_HTTPGET, 1);
            curl_setopt($process, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
            $return = curl_exec($process);
            curl_close($process);

            $arrayA = json_decode($return, true);
            $arrayB = $this->search($arrayA, 'inplay', '1');
            $match_data_merge = array_merge($arrayB, $arrayA);
            $match_data_arrange = array_unique($match_data_merge, SORT_REGULAR);
            $match_data = array_values($match_data_arrange);

            $jjj = 0;
            $new_match_data = array();
            $c = array();
            $cccarray = array();
            $timeArray = array();
            foreach ($arrayA as $value) {
                $match_detailtes = Match::where('match_id', $value['marketId'])->where('status', 1)->first();
                $key = array_search($match_detailtes['event_id'], array_column($st_tennis, 'MarketId'));
                if ($key) {
                    if ($value['status'] != 'CLOSED') {
                        $dt = $st_tennis[$key]['StartTime'];
                        //array_push($match_data[$jj],$dt);
                        $arrayA[$jjj]['matchTime'] = $dt;
                        $cccarray[] = $arrayA[$jjj];
                    }
                    $jjj++;
                }

            }


            /*foreach ($cccarray as $key => $part) {
		$matDate = date("d-m-Y h:i:s",strtotime($part['matchTime']));
       	$sort[$key] = ($matDate);
		}

		array_multisort($sort, SORT_ASC, $cccarray);*/
            $reference_array = array();

            foreach ($cccarray as $key => $row) {
                $new = explode("T", $row['matchTime']);
                $first = @$new[0];
                $second = @$new[1];
                $second = explode(".", $second);
                $timestamp = $first . " " . @$second[0];

                $date = Carbon::parse($timestamp);
                $date->addMinutes(330);
                $m_date = date('d-m-Y H:i', strtotime($date));
                $reference_array[$key] = $m_date;
            }

            array_multisort($reference_array, SORT_ASC, $cccarray);
            $match_data = $cccarray;


            $tennis = '<div class="programe-setcricket">
				<div class="firstblock-cricket lightblue-bg1">
				<span class="fir-col1"></span>
                <span class="fir-col2">1</span>
                <span class="fir-col2">X</span>
                <span class="fir-col2">2</span>
                <span class="fir-col3"></span>
                </div>';

            if (!empty($match_data)) {
                for ($j = 0; $j < sizeof($match_data); $j++) {
                    $inplay_game = '';
                    $mobileInplay = '';
                    $match_detail = Match::where('match_id', $match_data[$j]['marketId'])->where('status', 1)->first();
                    // status suspend
                    if ($match_data[$j]['status'] == 'CLOSED') {
                        $susUpd = Match::where('id', $match_detail->id)->where('match_finish', '!=', 1)->first();
                        if (!empty($susUpd)) {
                            $susUpd->match_finish = 1;
                            $susUpd->update();
                        }

                    }
                    if (isset($match_data[$j]['inplay'])) {
                        if ($match_data[$j]['inplay'] == 1) {
                            $dt = '';
                            $style = "fir-col1-green";
                            $inplay_game = " <span style='color:green' class='deskinplay'>In-Play</span>";
                            $mobileInplay = '<span style="color:green" class="mplay">In-Play</span>';
                        } else {
                            $match_date = '';
                            $dt = '';
                            $key = array_search($match_detail['event_id'], array_column($st_tennis, 'MarketId'));
                            if ($key)
                                // ss for incorrect index
                                //$dt=$st_criket[$key+1]['StartTime'];
                                $dt = $st_tennis[$key]['StartTime'];

                            $new = explode("T", $dt);
                            $first = @$new[0];
                            $second = @$new[1];
                            $second = explode(".", $second);
                            $timestamp = $first . " " . @$second[0];

                            $date = Carbon::parse($timestamp);
                            $date->addMinutes(330);

                            if (Carbon::parse($date)->isToday()) {
                                $match_date = date('h:i A', strtotime($date));
                            } else if (Carbon::parse($date)->isTomorrow())
                                $match_date = 'Tomorrow ' . date('h:i A', strtotime($date));
                            else
                                $match_date = date('d-m-Y h:i A', strtotime($date));

                            $dt = $match_date;
                            $style = "fir-col1";
                            $inplay_game = '';
                        }
                    } else {
                        //$dt=$match_detail['match_date'];
                        $dt = date('d-m-Y h:i A', strtotime($match_detail['match_date']));
                        $style = "fir-col1";
                        $inplay_game = '';
                        $mobileInplay = '';
                    }
                    if (isset($match_data[$j]['runners'][0]['ex']['availableToBack'][0]['price']) && isset($match_data[$j]['runners'][0]['ex']['availableToLay'][0]['price'])) {
                        $tennis_final_html .= '
							<div class="secondblock-cricket white-bg">
							<div class="mblinplay">
							' . $mobileInplay . '
							</div>
								<span class="' . $style . ' desk"  >
								<a href="matchDetail/' . $match_detail['id'] . '" class="text-color-blue-light">' . $match_detail['match_name'] . $inplay_game . '</a>								<div>' . $dt . '</div>			</span>
							<span class="fir-col2">
							<a class="backbtn lightblue-bg2">' . $match_data[$j]['runners'][0]['ex']['availableToBack'][0]['price'] . '</a>
							<a class="laybtn lightpink-bg1">' . $match_data[$j]['runners'][0]['ex']['availableToLay'][0]['price'] . '</a>
							</span>';
                    } else {

                        $tennis_final_html .= '
							<div class="secondblock-cricket white-bg">
							<div class="mblinplay">
							' . $mobileInplay . '
							</div>
								<span class="' . $style . ' desk"  >
								<a href="matchDetail/' . $match_detail['id'] . '" class="text-color-blue-light">' . $match_detail['match_name'] . $inplay_game . '</a>								<div>' . $dt . '</div>			</span>
							<span class="fir-col2">
							<a class="backbtn lightblue-bg2">--</a>
							<a class="laybtn lightpink-bg1">--</a>
							</span>';
                    }
                    if (isset($match_data[$j]['runners'][2])) {
                        $tennis_final_html .= '<span class="fir-col2">
							<a class="backbtn lightblue-bg2">' . $match_data[$j]['runners'][2]['ex']['availableToBack'][0]['price'] . '</a>
							<a class="laybtn lightpink-bg1">' . $match_data[$j]['runners'][2]['ex']['availableToLay'][0]['price'] . '</a>
							</span>';
                    } else {
                        $tennis_final_html .= '<span class="fir-col2">
							<a class="backbtn lightblue-bg2">--</a>
							<a class="laybtn lightpink-bg1">--</a>
							</span>';
                    }
                    if (isset($match_data[$j]['runners'][1]['ex']['availableToBack'][0]['price']) && isset($match_data[$j]['runners'][1]['ex']['availableToLay'][0]['price'])) {
                        $tennis_final_html .= '<span class="fir-col2">
							<a class="backbtn lightblue-bg2">' . $match_data[$j]['runners'][1]['ex']['availableToBack'][0]['price'] . '</a>
							<a class="laybtn lightpink-bg1">' . $match_data[$j]['runners'][1]['ex']['availableToLay'][0]['price'] . '</a>
							</span>
							<span class="fir-col3">
			                    <a><img src="' . asset('asset/front/img/round-pin.png') . '"> <img class="hover-img" src="public/asset/front/img/round-pin1.png"></a>
			                </span></div>';
                    } else {
                        $tennis_final_html .= '<span class="fir-col2">
							<a class="backbtn lightblue-bg2">--</a>
							<a class="laybtn lightpink-bg1">--</a>
							</span>
							<span class="fir-col3">
			                    <a><img src="' . asset('asset/front/img/round-pin.png') . '"> <img class="hover-img" src="public/asset/front/img/round-pin1.png"></a>
			                </span></div>';
                    }
                }
                $final_html .= "~~" . $tennis . $tennis_final_html . '</div>';
            }
        } else {
            $final_html .= "~~" . 'No match found.';
        }


        //for soccer
        if (!empty($imp_match_array_data_soccer)) {

            $url = 'http://69.30.238.2:3644/odds/multiple?ids=' . $imp_match_array_data_soccer;
            $headers = array('Content-Type: application/json');
            $process = curl_init();
            curl_setopt($process, CURLOPT_URL, $url);
            curl_setopt($process, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($process, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($process, CURLOPT_TIMEOUT, 30);
            curl_setopt($process, CURLOPT_HTTPGET, 1);
            curl_setopt($process, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
            $return = curl_exec($process);
            curl_close($process);

            $arrayA = json_decode($return, true);
            $arrayB = $this->search($arrayA, 'inplay', '1');

            $match_data_merge = array_merge($arrayB, $arrayA);
            $match_data_arrange = array_unique($match_data_merge, SORT_REGULAR);
            $match_data = array_values($match_data_arrange);
            $jjj = 0;
            $new_match_data = array();
            $c = array();
            $cccsoccerarray = array();
            $timeArray = array();
            foreach ($arrayA as $value) {
                $match_detailtes = Match::where('match_id', $value['marketId'])->where('status', 1)->first();
                $key = array_search($match_detailtes['event_id'], array_column($st_soccer, 'MarketId'));
                if ($key) {
                    if ($value['status'] != 'CLOSED') {
                        $dt = $st_soccer[$key]['StartTime'];
                        $arrayA[$jjj]['matchTime'] = $dt;
                        $cccsoccerarray[] = $arrayA[$jjj];
                    }
                    $jjj++;
                }

            }

            $referencesoccer_array = array();

            foreach ($cccsoccerarray as $key => $row) {
                $new = explode("T", $row['matchTime']);
                $first = @$new[0];
                $second = @$new[1];
                $second = explode(".", $second);
                $timestamp = $first . " " . @$second[0];

                $date = Carbon::parse($timestamp);
                $date->addMinutes(330);
                $m_date = date('d-m-Y H:i', strtotime($date));
                $referencesoccer_array[$key] = $m_date;
            }
            array_multisort($referencesoccer_array, SORT_ASC, $cccsoccerarray);
            $match_data = $cccsoccerarray;
            $soccer = '<div class="programe-setcricket">
		<div class="firstblock-cricket lightblue-bg1">
		<span class="fir-col1"></span>
        <span class="fir-col2">1</span>
        <span class="fir-col2">X</span>
        <span class="fir-col2">2</span>
        <span class="fir-col3"></span>
        </div>';
            if (!empty($match_data)) {
                for ($k = 0; $k < sizeof($match_data); $k++) {
                    $html = '';

                    if (@$match_data[$k]['marketId'] != '' && $match_data[$k]['marketId'] > 0) {
                        $match_detail = Match::where('match_id', @$match_data[$k]['marketId'])->where('status', 1)->first();
                        // status suspend
                        if ($match_data[$k]['status'] == 'CLOSED') {
                            $susUpd = Match::where('id', $match_detail->id)->where('match_finish', '!=', 1)->first();
                            if (!empty($susUpd)) {
                                $susUpd->match_finish = 1;
                                $susUpd->update();
                            }

                        }
                        $inplay_game = '';
                        $mobileInplay = '';
                        if (isset($match_data[$k]['inplay'])) {
                            if ($match_data[$k]['inplay'] == 1) {
                                $dt = '';
                                $style = "fir-col1-green";
                                $inplay_game = " <span style='color: green;font-weight: bold;' class='deskinplay'>In-Play</span>";
                                $mobileInplay = '<span style="color:green" class="mplay">In-Play</span>';
                            } else {
                                $match_date = '';

                                $dt = '';
                                $key = array_search($match_detail['event_id'], array_column($st_soccer, 'MarketId'));
                                if ($key)
                                    $dt = $st_soccer[$key]['StartTime'];

                                $new = explode("T", $dt);
                                $first = $new[0];
                                $second = @$new[1];
                                $second = explode(".", $second);
                                $timestamp = $first . " " . $second[0];

                                $date = Carbon::parse($timestamp);
                                $date->addMinutes(330);

                                if (Carbon::parse($date)->isToday())
                                    $match_date = date('h:i A', strtotime($date));
                                else if (Carbon::parse($date)->isTomorrow())
                                    $match_date = 'Tomorrow ' . date('h:i A', strtotime($date));
                                else
                                    $match_date = date('d-m-Y h:i A', strtotime($date));

                                $dt = $match_date;
                                $style = "fir-col1";
                                $inplay_game = '';
                                $mobileInplay = '';
                            }
                        } else {
                            $dt = date('d-m-Y h:i A', strtotime($match_detail['match_date']));
                            $style = "fir-col1";
                            $inplay_game = '';
                            $mobileInplay = '';
                        }
                        $check_closed_for_soccer_start = '';
                        $check_closed_for_soccer_end = '';
                        if (!isset($match_data[$j]['runners'][0]['ex']['availableToBack'][0]['price']) && !isset($match_data[$j]['runners'][2]) && !isset($match_data[$j]['runners'][1]['ex']['availableToBack'][0]['price'])) {
                        }

                        if ($is_agent == 'mobile') {
                            $matchName = substr($match_detail['match_name'], 0, 36) . '...';
                        } else {
                            $matchName = $match_detail['match_name'];
                        }
                        if (isset($match_data[$k]['runners'][0]['ex']['availableToBack'][0]['price'])) {
                            $html .= '
						<div class="secondblock-cricket white-bg" style="position:relative">
						<div class="mblinplay">
							' . $mobileInplay . '
						</div>
							<span class="' . $style . ' desk"  >
							<a href="matchDetail/' . $match_detail['id'] . '" class="text-color-blue-light">' . $matchName . $inplay_game . '</a>								<div>' . $dt . '</div>		 </span>
						<span class="fir-col2">
						<a class="backbtn lightblue-bg2">' . @$match_data[$k]['runners'][0]['ex']['availableToBack'][0]['price'] . '</a>
						<a class="laybtn lightpink-bg1">' . @$match_data[$k]['runners'][0]['ex']['availableToLay'][0]['price'] . '</a>
						</span>';
                        } else {


                            $html .= '
						<div class="secondblock-cricket white-bg" style="position:relative">
						<div class="mblinplay">
							' . $mobileInplay . '
						</div>
							<span class="' . $style . ' desk"  >
							<a href="matchDetail/' . $match_detail['id'] . '" class="text-color-blue-light">' . $matchName . $inplay_game . '</a>								<div>' . $dt . '</div>		 </span>						' . $check_closed_for_soccer_start . '
						<span class="fir-col2">
						<a class="backbtn lightblue-bg2">--</a>
						<a class="laybtn lightpink-bg1">--</a>
						</span>';
                        }
                        if (isset($match_data[$k]['runners'][2]['ex']['availableToBack'][0]['price'])) {
                            $html .= '<span class="fir-col2">
						<a class="backbtn lightblue-bg2">' . @$match_data[$k]['runners'][2]['ex']['availableToBack'][0]['price'] . '</a>
						<a class="laybtn lightpink-bg1">' . @$match_data[$k]['runners'][2]['ex']['availableToLay'][0]['price'] . '</a>
						</span>';
                        } else {
                            $html .= '<span class="fir-col2">
						<a class="backbtn lightblue-bg2">--</a>
						<a class="laybtn lightpink-bg1">--</a>
						</span>';
                        }
                        if (isset($match_data[$k]['runners'][0]['ex']['availableToBack'][0]['price'])) {
                            $html .= '<span class="fir-col2">
						<a class="backbtn lightblue-bg2">' . @$match_data[$k]['runners'][1]['ex']['availableToBack'][0]['price'] . '</a>
						<a class="laybtn lightpink-bg1">' . @$match_data[$k]['runners'][1]['ex']['availableToLay'][0]['price'] . '</a>
						</span>
						<span class="fir-col3">
		                    <a><img src="' . asset('asset/front/img/round-pin.png') . '"> <img class="hover-img" src="public/asset/front/img/round-pin1.png"></a>
		                </span>
						</div>';
                        } else {
                            $html .= '<span class="fir-col2">
						<a class="backbtn lightblue-bg2">--</a>
						<a class="laybtn lightpink-bg1">--</a>
						</span>
						<span class="fir-col3">
		                    <a><img src="' . asset('asset/front/img/round-pin.png') . '"> <img class="hover-img" src="public/asset/front/img/round-pin1.png"></a>
		                </span>
						' . $check_closed_for_soccer_end . '</div>';
                        }
                    }
                    $soccer_final_html .= $html;
                }
                $final_html .= "~~" . $soccer . $soccer_final_html . '</div>';
            }
        } else {
            $final_html .= "~~" . 'No match found.';
        }
        return $final_html;

    }

    public function getmatchdetails_NEW()
    {
        $sports = Sport::all();
        $html = '';
        $i = 0;
        foreach ($sports as $sport) {
            $html .= '<div class="programe-setcricket">
					<div class="firstblock-cricket lightblue-bg1">
						<span class="fir-col1"></span>
						<span class="fir-col2">1</span>
						<span class="fir-col2">X</span>
						<span class="fir-col2">2</span>
						<span class="fir-col3"></span>
					</div>';
            $match_array_data_cricket[] = array();
            $match_array_data_tenis[] = array();
            $match_array_data_soccer[] = array();
            $match_link = Match::where('sports_id', $sport->sId)->where('status', 1)->orderBy('match_date', 'DESC')->get();

            foreach ($match_link as $match) {
                if (@$match->match_id != '') {
                    if ($match->sports_id == 4) {
                        $match_array_data_cricket[] = $match->match_id;
                    } else if ($match->sports_id == 2)
                        $match_array_data_tenis[] = $match->match_id;
                    else if ($match->sports_id == 1)
                        $match_array_data_soccer[] = $match->match_id;
                }
            }
        }

        $imp_match_array_data_cricket = @implode(",", $match_array_data_cricket);
        $imp_match_array_data_tenis = @implode(",", $match_array_data_tenis);
        $imp_match_array_data_soccer = @implode(",", $match_array_data_soccer);
        $mdata = array();
        $inplay = 0;

        $imp_match_array_data_cricket;
        $url = "http://69.30.238.2:3644/odds/multiple?ids=" . $imp_match_array_data_cricket;

        $headers = array('Content-Type: application/json');
        $process = curl_init();
        curl_setopt($process, CURLOPT_URL, $url);
        curl_setopt($process, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($process, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($process, CURLOPT_TIMEOUT, 30);
        curl_setopt($process, CURLOPT_HTTPGET, 1);
        curl_setopt($process, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
        $return = curl_exec($process);
        curl_close($process);

        $return_array = json_decode($return, true);
        print_r($return_array);
    }

    public function matchCallForFancyNBM($matchId, Request $request)
    {
        $matchtype = $request->matchtype;
        $match_id = $request->match_id;
        $sport = Sport::where('sId', $matchtype)->first();
        $matchList = Match::where('id', $match_id)->where('status', 1)->first();
        $min_bet_odds_limit = @$matchList->min_bookmaker_limit;
        $max_bet_odds_limit = @$matchList->max_bookmaker_limit;

        $min_bet_fancy_limit = @$matchList->min_fancy_limit;
        $max_bet_fancy_limit = @$matchList->max_fancy_limit;

        // $matchtype=$sport->id;
        $eventId = $request->event_id;
        $matchname = $request->matchname;
        $match_b = @$matchList->suspend_b;
        $match_f = @$matchList->suspend_f;
        $html = '';
        $html_bm_team = "";

        @$team_name = explode(" v ", strtolower($matchname));
        $team1_name = @$team_name[0];
        if (@$team_name[1])
            @$team2_name = $team_name[1];
        else
            $team2_name = '';

        $match_detail = Match::where('event_id', $request->event_id)->where('status', 1)->first();
        $match_data = app('App\Http\Controllers\RestApi')->getSingleMatchData($eventId, $matchId, $matchtype);
        $team2_bet_total = 0;
        $team1_bet_total = 0;
        $team_draw_bet_total = 0;

        //for bm
        $getUserCheck = Session::get('playerUser');
        if (!empty($getUserCheck)) {
            $sessionData = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
        }
        if (!empty($sessionData)) {
            $getUserCheckuser = Session::get('playerUser');
            if (!empty($getUserCheckuser)) {
                $getUser = User::where('id', $getUserCheckuser->id)->where('check_login', 1)->first();
            }
            $userId = $getUser->id;
            $my_placed_bets = MyBets::where('user_id', $userId)->where('match_id', $eventId)->where('bet_type', 'BOOKMAKER')->where('isDeleted', 0)->where('result_declare', 0)->orderby('id', 'DESC')->get();

            if (sizeof($my_placed_bets) > 0) {
                foreach ($my_placed_bets as $bet) {
                    $abc = json_decode($bet->extra, true);
                    if (!empty($abc)) {
                        if (count($abc) >= 2) {
                            if (array_key_exists("teamname1", $abc) && array_key_exists("teamname2", $abc)) {
                                //bet on draw
                                if ($bet->bet_side == 'back') {
                                    $team1_bet_total = $team1_bet_total - $bet->exposureAmt;
                                    if (count($abc) >= 2) {
                                        $team_draw_bet_total = $team_draw_bet_total + $bet->bet_profit;
                                    }
                                    $team2_bet_total = $team2_bet_total - $bet->exposureAmt;
                                }
                                if ($bet->bet_side == 'lay') {
                                    $team1_bet_total = $team1_bet_total + $bet->bet_amount;
                                    if (count($abc) >= 2) {
                                        $team_draw_bet_total = $team_draw_bet_total - $bet->bet_profit;
                                    }
                                    $team2_bet_total = $team2_bet_total + $bet->bet_amount;
                                }
                            } else if (array_key_exists("teamname3", $abc) && array_key_exists("teamname2", $abc)) {
                                //bet on team1
                                if ($bet->bet_side == 'back') {
                                    $team1_bet_total = $team1_bet_total + $bet->bet_profit;
                                    if (count($abc) >= 2) {
                                        $team_draw_bet_total = $team_draw_bet_total - $bet->exposureAmt;
                                    }
                                    $team2_bet_total = $team2_bet_total - $bet->exposureAmt;
                                }
                                if ($bet->bet_side == 'lay') {
                                    $team1_bet_total = $team1_bet_total - $bet->bet_profit;
                                    if (count($abc) >= 2) {
                                        $team_draw_bet_total = $team_draw_bet_total + $bet->bet_amount;
                                    }
                                    $team2_bet_total = $team2_bet_total + $bet->bet_amount;
                                }
                            } else if (array_key_exists("teamname3", $abc) && array_key_exists("teamname1", $abc)) {
                                //bet on team2
                                if ($bet->bet_side == 'back') {
                                    $team2_bet_total = $team2_bet_total + $bet->bet_profit;
                                    if (count($abc) >= 2) {
                                        $team_draw_bet_total = $team_draw_bet_total - $bet->exposureAmt;
                                    }
                                    $team1_bet_total = $team1_bet_total - $bet->exposureAmt;
                                }
                                if ($bet->bet_side == 'lay') {
                                    $team2_bet_total = $team2_bet_total - $bet->bet_profit;
                                    if (count($abc) >= 2) {
                                        $team_draw_bet_total = $team_draw_bet_total + $bet->bet_amount;
                                    }
                                    $team1_bet_total = $team1_bet_total + $bet->bet_amount;
                                }
                            }
                        } else if (count($abc) == 1) {
                            if (array_key_exists("teamname1", $abc)) {
                                //bet on team2
                                if ($bet->bet_side == 'back') {
                                    $team2_bet_total = $team2_bet_total + $bet->bet_profit;
                                    $team1_bet_total = $team1_bet_total - $bet->exposureAmt;
                                }
                                if ($bet->bet_side == 'lay') {
                                    $team2_bet_total = $team2_bet_total - $bet->bet_profit;
                                    $team1_bet_total = $team1_bet_total + $bet->bet_amount;
                                }
                            } else {
                                //bet on team1
                                if ($bet->bet_side == 'back') {
                                    $team1_bet_total = $team1_bet_total + $bet->bet_profit;
                                    $team2_bet_total = $team2_bet_total - $bet->exposureAmt;
                                }
                                if ($bet->bet_side == 'lay') {
                                    $team1_bet_total = $team1_bet_total - $bet->bet_profit;
                                    $team2_bet_total = $team2_bet_total + $bet->bet_amount;
                                }
                            }
                        }
                    }
                }
            }
        }

        $html_two = '';
        $html_two_team = "";
        $back = '';
        $login_check = '';
        $getUserCheck = Session::get('playerUser');
        if (!empty($getUserCheck)) {
            $sessionData = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
        }

        if (!empty($sessionData)) {
            if ($min_bet_odds_limit > 0 && $min_bet_odds_limit != "" && $max_bet_odds_limit > 0 && $max_bet_odds_limit != "")
                $login_check = 'onclick="opnForm(this);"';
        } else {
            $useragent = $_SERVER['HTTP_USER_AGENT'];
            $iPod = stripos($useragent, "iPod");
            $iPad = stripos($useragent, "iPad");
            $iPhone = stripos($useragent, "iPhone");
            $Android = stripos($useragent, "Android");
            $iOS = stripos($useragent, "iOS");

            $DEVICE = ($iPod || $iPad || $iPhone || $Android || $iOS);
            $is_agent = '';
            if (!$DEVICE) {
                $is_agent = 'desktop';
                $login_check = 'data-toggle="modal" data-target="#myLoginModal"';
            } else {
                $is_agent = 'mobile';
                $login_check = 'data-toggle="modal" data-target="#myLoginModalFront"';
            }
        }

        if (!empty($match_data) && $match_data != 0) {
            //for bookmaker
            $html_bm_team .= '
			<tr>
                <td class="text-color-grey fancybet-block" colspan="7">
                    <div class="dark-blue-bg-1 text-color-white">
                        <a> <img src="' . asset('asset/front/img/pin-bg.png') . '"> <img src="' . asset('asset/front/img/pin-bg-1.png') . '" class="hover-img"> </a>
                        Bookmaker Market <span class="zeroopa">| Zero Commission</span>
                    </div>
                    <div class="fancy_info text-color-white">
                        <span class="light-grey-bg-5 text-color-blue-1">Min</span> <span id="div_min_bet_bm_limit">' . $match_detail['min_bookmaker_limit'] . '</span>
                        <span class="light-grey-bg-5 text-color-blue-1">Max</span> <span id="div_max_bet_bm_limit">' . $match_detail['max_bookmaker_limit'] . '</span>
                    </div>
                </td>
            </tr>

			<tr class="bets-fancy white-bg">
				<td colspan="3" style="width:170px"></td>
				<td class="text-right">Back</td>
				<td class="text-left">Lay</td>
				<td colspan="2"></td>
			</tr>';

            $team_name_array = array();
            $team_name_array[] = @$match_data['t2'][0]['bm1']['0']['nat'];
            $team_name_array[] = @$match_data['t2'][0]['bm1']['1']['nat'];
            $team_name_array[] = @$match_data['t2'][0]['bm1']['2']['nat'];

            $team1_name = $arry_position = array_search(ucwords($team1_name), $team_name_array);
            $team2_name = $arry_position = array_search(ucwords($team2_name), $team_name_array);
            $team3_name = 0;
            if ($team1_name == 0 && $team2_name == 1)
                $team3_name = 2;
            else if ($team1_name == 1 && $team2_name == 0)
                $team3_name = 2;
            else if ($team1_name == 2 && $team2_name == 1)
                $team3_name = 0;
            else if ($team1_name == 1 && $team2_name == 2)
                $team3_name = 0;
            else if ($team1_name == 0 && $team2_name == 2)
                $team3_name = 1;
            else if ($team1_name == 2 && $team2_name == 0)
                $team3_name = 1;

            $team1 = $team2 = $team3 = '';
            if ($team1_name != '' || $team2_name != '') {
                if ($match_b == '0') {
                    $team1 .= 'SUSPENDED';
                    $team2 .= 'SUSPENDED';
                } else {

//                    echo __FILE__." at line ".__LINE__."<br>";echo "<pre>";print_r($match_data);die();

                    if (isset($match_data['t2'][0]['bm1'][$team1_name]['nat']) && $match_data['t2'][0]['bm1'][$team1_name]['s'] != 'SUSPENDED') {
                        $display = '';
                        $cls = '';
                        if ($team1_bet_total == '')
                            $display = 'style="display:none"';
                        if ($team1_bet_total != '' && $team1_bet_total >= 0) {
                            $cls = 'text-color-green';
                        } else if ($team1_bet_total != '' && $team1_bet_total < 0) {
                            $cls = 'text-color-red';
                        }
                        $team1 .= '<div class="back-gradient text-color-black">
									<div id="back_3" class="BmBack light-blue-bg-2" data-team="team' . @$match_data['t2'][0]['bm1'][$team1_name]['sid'] . '">
										<a data-bettype="BOOKMAKER" data-team="team' . @$match_data['t2'][0]['bm1'][$team1_name]['sid'] . '" data-cls="cyan-bg" ' . $login_check . ' data-position="2" data-val="' . round(@$match_data['t2'][0]['bm1'][$team1_name]['b3'], 2) . '">' . round(@$match_data['t2'][0]['bm1'][$team1_name]['b3'], 2) . '<br> <span>100</span></a>
									</div>

								</div>~';
                        $team1 .= '<div class="back-gradient text-color-black">

									<div id="back_2" class="BmBack light-blue-bg-3" data-team="team' . @$match_data['t2'][0]['bm1'][$team1_name]['sid'] . '">
										<a data-bettype="BOOKMAKER" data-team="team' . @$match_data['t2'][0]['bm1'][$team1_name]['sid'] . '" data-cls="cyan-bg" ' . $login_check . ' data-position="1"  data-val="' . round(@$match_data['t2'][0]['bm1'][$team1_name]['b2'], 2) . '">' . round(@$match_data['t2'][0]['bm1'][$team1_name]['b2'], 2) . '<br> <span>100</span></a>
									</div>
								</div>~';
                        $team1 .= '<div class="back-gradient text-color-black">

									<div id="back_1" class="BmBack" data-team="team' . @$match_data['t2'][0]['bm1'][$team1_name]['sid'] . '">
										<a data-bettype="BOOKMAKER" data-team="team' . @$match_data['t2'][0]['bm1'][$team1_name]['sid'] . '" data-cls="cyan-bg" class="cyan-bg" ' . $login_check . ' data-position="0" data-val="' . round(@$match_data['t2'][0]['bm1'][$team1_name]['b1'], 2) . '">' . round(@$match_data['t2'][0]['bm1'][$team1_name]['b1'], 2) . '<br> <span>100</span></a>
									</div>
								</div>~';
                        $team1 .= '<div class="lay-gradient text-color-black">
									<div id="lay_1"  class="BmLay pink-bg" data-team="team' . @$match_data['t2'][0]['bm1'][$team1_name]['sid'] . '">
										<a data-bettype="BOOKMAKER" data-team="team' . @$match_data['t2'][0]['bm1'][$team1_name]['sid'] . '" data-cls="pink-bg" ' . $login_check . ' data-position="0" data-val="' . round(@$match_data['t2'][0]['bm1'][$team1_name]['l1'], 2) . '">' . @round($match_data['t2'][0]['bm1'][$team1_name]['l1'], 2) . '<br> <span>100</span></a></div>

								</div>~';
                        $team1 .= '<div class="lay-gradient text-color-black">

									<div id="lay_2" class="BmLay light-pink-bg-2" data-team="team' . @$match_data['t2'][0]['bm1'][$team1_name]['sid'] . '">
										<a data-bettype="BOOKMAKER" data-team="team' . @$match_data['t2'][0]['bm1'][$team1_name]['sid'] . '" data-cls="pink-bg" ' . $login_check . ' data-position="1" data-val="' . round(@$match_data['t2'][0]['bm1'][$team1_name]['l2'], 2) . '">' . round(@$match_data['t2'][0]['bm1'][$team1_name]['l2'], 2) . '<br> <span>100</span></a>
									</div>
								</div>~';
                        $team1 .= '<div class="lay-gradient text-color-black">

									<div id="lay_3" class="BmLay light-pink-bg-3" data-team="team' . @$match_data['t2'][0]['bm1'][$team1_name]['sid'] . '">
										<a data-bettype="BOOKMAKER" data-team="team' . @$match_data['t2'][0]['bm1'][$team1_name]['sid'] . '" data-cls="pink-bg" ' . $login_check . ' data-position="2" data-val="' . round(@$match_data['t2'][0]['bm1'][$team1_name]['l3'], 2) . '">' . round(@$match_data['t2'][0]['bm1'][$team1_name]['l3'], 2) . '<br> <span>100</span></a>
									</div>
								</div>';
                    } else {
                        $team1 .= 'SUSPENDED';
                    }
                    if (isset($match_data['t2'][0]['bm1'][$team2_name]['s']) && @$match_data['t2'][0]['bm1'][$team2_name]['s'] != 'SUSPENDED') {
                        $display = '';
                        $cls = '';
                        if ($team2_bet_total == '')
                            $display = 'style="display:none"';
                        if ($team2_bet_total != '' && $team2_bet_total >= 0) {
                            $cls = 'text-color-green';
                        } else if ($team2_bet_total != '' && $team2_bet_total < 0) {
                            $cls = 'text-color-red';
                        }

                        $team2 .= '<div class="back-gradient text-color-black">
										<div id="back_3" class="BmBack light-blue-bg-2" data-team="team' . @$match_data['t2'][0]['bm1'][$team2_name]['sid'] . '">
											<a data-bettype="BOOKMAKER" data-team="team' . @$match_data['t2'][0]['bm1'][$team2_name]['sid'] . '" data-cls="cyan-bg" ' . $login_check . ' data-position="2" data-val="' . round(@$match_data['t2'][0]['bm1'][$team2_name]['b3'], 2) . '">' . round(@$match_data['t2'][0]['bm1'][$team2_name]['b3'], 2) . '<br> <span>100</span></a>
										</div>
									</div>~';
                        $team2 .= '<div class="back-gradient text-color-black">

										<div id="back_2" class="BmBack light-blue-bg-3" data-team="team' . @$match_data['t2'][0]['bm1'][$team2_name]['sid'] . '">
											<a data-bettype="BOOKMAKER" data-team="team' . @$match_data['t2'][0]['bm1'][$team2_name]['sid'] . '" data-cls="cyan-bg" ' . $login_check . ' data-position="1" data-val="' . round(@$match_data['t2'][0]['bm1'][$team2_name]['b2'], 2) . '">' . round(@$match_data['t2'][0]['bm1'][$team2_name]['b2'], 2) . '<br> <span>100</span></a>
										</div>
									</div>~';
                        $team2 .= '<div class="back-gradient text-color-black">

										<div id="back_1" class="BmBack" data-team="team' . @$match_data['t2'][0]['bm1'][$team2_name]['sid'] . '">
											<a data-bettype="BOOKMAKER" data-team="team' . @$match_data['t2'][0]['bm1'][$team2_name]['sid'] . '" data-cls="cyan-bg" class="cyan-bg" ' . $login_check . ' data-position="0" data-val="' . round(@$match_data['t2'][0]['bm1'][$team2_name]['b1'], 2) . '">' . round(@$match_data['t2'][0]['bm1'][$team2_name]['b1'], 2) . '<br> <span>100</span></a>
										</div>
									</div>~';
                        $team2 .= '<div class="lay-gradient text-color-black">
										<div id="lay_1" class="BmLay pink-bg" data-team="team' . @$match_data['t2'][0]['bm1'][$team2_name]['sid'] . '">
											<a data-bettype="BOOKMAKER" data-team="team' . @$match_data['t2'][0]['bm1'][$team2_name]['sid'] . '" data-cls="pink-bg" ' . $login_check . ' data-position="0" data-val="' . round(@$match_data['t2'][0]['bm1'][$team2_name]['l1'], 2) . '">' . round(@$match_data['t2'][0]['bm1'][$team2_name]['l1'], 2) . '<br> <span>100</span></a></div>
									</div>~';
                        $team2 .= '<div class="lay-gradient text-color-black">

										<div id="lay_2" class="BmLay light-pink-bg-2" data-team="team' . @$match_data['t2'][0]['bm1'][$team2_name]['sid'] . '">
											<a data-bettype="BOOKMAKER" data-team="team' . @$match_data['t2'][0]['bm1'][$team2_name]['sid'] . '" data-cls="pink-bg" ' . $login_check . ' data-position="1" data-val="' . round(@$match_data['t2'][0]['bm1'][$team2_name]['l2'], 2) . '">' . round(@$match_data['t2'][0]['bm1'][$team2_name]['l2'], 2) . '<br> <span>100</span></a>
										</div>
									</div>~';
                        $team2 .= '<div class="lay-gradient text-color-black">

										<div id="lay_3" class="BmLay light-pink-bg-3" data-team="team' . @$match_data['t2'][0]['bm1'][$team2_name]['sid'] . '">
											<a data-bettype="BOOKMAKER" data-team="team' . @$match_data['t2'][0]['bm1'][$team2_name]['sid'] . '"  data-cls="pink-bg" ' . $login_check . ' data-position="2" data-val="' . round(@$match_data['t2'][0]['bm1'][$team2_name]['l3'], 2) . '">' . round(@$match_data['t2'][0]['bm1'][$team2_name]['l3'], 2) . '<br> <span>100</span></a>
										</div>
									</div>';
                    } else {
                        $team2 .= 'SUSPENDED';
                    }
                    if (isset($match_data['t2'][0]['bm1'][$team3_name]['s'])) {
                        if (@$match_data['t2'][0]['bm1'][$team3_name]['s'] != 'SUSPENDED') {
                            $display = '';
                            $cls = '';
                            if ($team_draw_bet_total == '') {
                                $display = 'style="display:none"';
                            }
                            if ($team_draw_bet_total != '' && $team_draw_bet_total >= 0) {
                                $cls = 'text-color-green';
                            } else if ($team_draw_bet_total != '' && $team_draw_bet_total < 0) {
                                $cls = 'text-color-red';
                            }
                            $team3 .= '<div class="back-gradient text-color-black">
											<div id="back_3" class="BmBack light-blue-bg-2" data-team="team' . @$match_data['t2'][0]['bm1'][$team3_name]['sid'] . '">
												<a data-bettype="BOOKMAKER" data-team="team' . @$match_data['t2'][0]['bm1'][$team3_name]['sid'] . '" data-cls="cyan-bg" ' . $login_check . ' data-position="2" data-val="' . round(@$match_data['t2'][0]['bm1'][$team3_name]['b3'], 2) . '">' . round(@$match_data['t2'][0]['bm1'][$team3_name]['b3'], 2) . '<br> <span>100</span></a>
											</div>
									</div>~';
                            $team3 .= '<div class="back-gradient text-color-black">

											<div id="back_2" class="BmBack light-blue-bg-3" data-team="team' . @$match_data['t2'][0]['bm1'][$team3_name]['sid'] . '">
												<a data-bettype="BOOKMAKER" data-team="team' . @$match_data['t2'][0]['bm1'][$team3_name]['sid'] . '" data-cls="cyan-bg" ' . $login_check . ' data-position="1" data-val="' . round(@$match_data['t2'][0]['bm1'][$team3_name]['b2'], 2) . '">' . round(@$match_data['t2'][0]['bm1'][$team3_name]['b2'], 2) . '<br> <span>100</span></a>
											</div>

										</div>~';
                            $team3 .= '<div class="back-gradient text-color-black">

											<div id="back_1" class="BmBack" data-team="team' . @$match_data['t2'][0]['bm1'][$team3_name]['sid'] . '">
												<a data-bettype="BOOKMAKER" data-team="team' . @$match_data['t2'][0]['bm1'][$team3_name]['sid'] . '" data-cls="cyan-bg" class="cyan-bg" ' . $login_check . ' data-position="0" data-val="' . round(@$match_data['t2'][0]['bm1'][$team3_name]['b1'], 2) . '">' . round(@$match_data['t2'][0]['bm1'][$team3_name]['b1'], 2) . '<br> <span>100</span></a>
											</div>
										</div>~';
                            $team3 .= '<div class="lay-gradient text-color-black">
											<div id="lay_1" class="BmLay pink-bg" data-team="team' . @$match_data['t2'][0]['bm1'][$team3_name]['sid'] . '">
												<a data-bettype="BOOKMAKER" data-team="team' . @$match_data['t2'][0]['bm1'][$team3_name]['sid'] . '" data-cls="pink-bg" ' . $login_check . ' data-position="0" data-val="' . round(@$match_data['t2'][0]['bm1'][$team3_name]['l1'], 2) . '">' . round(@$match_data['t2'][0]['bm1'][$team3_name]['l1'], 2) . '<br> <span>100</span></a>
											</div>
										</div>~';
                            $team3 .= '<div class="lay-gradient text-color-black">

											<div id="lay_2" class="BmLay light-pink-bg-2" data-team="team' . @$match_data['t2'][0]['bm1'][$team3_name]['sid'] . '">
												<a data-bettype="BOOKMAKER" data-team="team' . @$match_data['t2'][0]['bm1'][$team3_name]['sid'] . '" data-cls="pink-bg" ' . $login_check . ' data-position="1" data-val="' . round(@$match_data['t2'][0]['bm1'][$team3_name]['l2'], 2) . '">' . round(@$match_data['t2'][0]['bm1'][$team3_name]['l2'], 2) . '<br> <span>100</span></a>
											</div>
										</div>~';
                            $team3 .= '<div class="lay-gradient text-color-black">

											<div id="lay_3" class="BmLay light-pink-bg-3" data-team="team' . @$match_data['t2'][0]['bm1'][$team3_name]['sid'] . '">
												<a data-bettype="BOOKMAKER" data-team="team' . @$match_data['t2'][0]['bm1'][$team3_name]['sid'] . '" data-cls="pink-bg" ' . $login_check . ' data-position="2" data-val="' . round(@$match_data['t2'][0]['bm1'][$team3_name]['l3'], 2) . '">' . round(@$match_data['t2'][0]['bm1'][$team3_name]['l3'], 2) . '<br> <span>100</span></a>
											</div>
										</div>';
                        } else {
                            $team3 .= 'SUSPENDED';
                        }
                    }
                } // end suspended if
            }
            if ($team1 != '' || $team2 != '' || $team3 != '') {
                $html = $team1 . '===' . $team2 . '===' . $team3;
            }
        }
        if ($html == '')
            $html = '';
        echo $html . '####' . $back;
    }

    public function matchCallForFancyOnly($matchId, Request $request)
    {
        $matchtype = $request->matchtype;
        $match_id = $request->match_id;
        $sport = Sport::where('sId', $matchtype)->first();
        $matchList = Match::where('id', $match_id)->where('status', 1)->first();

        $min_bet_odds_limit = $matchList->min_bookmaker_limit;
        $max_bet_odds_limit = $matchList->max_bookmaker_limit;

        $min_bet_fancy_limit = $matchList->min_fancy_limit;
        $max_bet_fancy_limit = $matchList->max_fancy_limit;

        // $matchtype=$sport->id;
        $eventId = $request->event_id;
        $matchname = $request->matchname;
        $match_b = $matchList->suspend_b;
        $match_f = $matchList->suspend_f;
        $html = '';
        $html_bm_team = "";

        @$team_name = explode(" v ", strtolower($matchname));
        $team1_name = @$team_name[0];
        if (@$team_name[1])
            @$team2_name = $team_name[1];
        else
            $team2_name = '';

        $match_detail = Match::where('event_id', $request->event_id)->where('status', 1)->first();

        $match_data = app('App\Http\Controllers\RestApi')->getSingleMatchData($eventId, $matchId, $matchtype);

        // echo "ASASasASASA<pre>"; print_r($match_data); exit;

        $team2_bet_total = 0;
        $team1_bet_total = 0;
        $team_draw_bet_total = 0;

        $html_two = '';
        $html_two_team = "";

        $login_check = '';
        $getUserCheck = Session::get('playerUser');
        if (!empty($getUserCheck)) {
            $sessionData = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
        }
        if (!empty($sessionData)) {
            if ($min_bet_odds_limit > 0 && $min_bet_odds_limit != "" && $max_bet_odds_limit > 0 && $max_bet_odds_limit != "")
                $login_check = 'onclick="opnForm(this);"';
        } else {
            $useragent = $_SERVER['HTTP_USER_AGENT'];
            $iPod = stripos($useragent, "iPod");
            $iPad = stripos($useragent, "iPad");
            $iPhone = stripos($useragent, "iPhone");
            $Android = stripos($useragent, "Android");
            $iOS = stripos($useragent, "iOS");

            $DEVICE = ($iPod || $iPad || $iPhone || $Android || $iOS);
            $is_agent = '';
            if (!$DEVICE) {
                $is_agent = 'desktop';
                $login_check = 'data-toggle="modal" data-target="#myLoginModal"';
            } else {
                $is_agent = 'mobile';
                $login_check = 'data-toggle="modal" data-target="#myLoginModalFront"';
            }
        }
        $all_bet_model = '';
        if (!empty($match_data) && $match_data != 0) {
            //for fancy
            $login_check = '';
            $getUserCheck = Session::get('playerUser');
            if (!empty($getUserCheck)) {
                $sessionData = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
            }
            if (!empty($sessionData)) {
                if ($min_bet_fancy_limit > 0 && $min_bet_fancy_limit != "" && $max_bet_fancy_limit > 0 && $max_bet_fancy_limit != "")
                    $login_check = 'onclick="opnForm(this);"';
            } else {
                $useragent = $_SERVER['HTTP_USER_AGENT'];
                $iPod = stripos($useragent, "iPod");
                $iPad = stripos($useragent, "iPad");
                $iPhone = stripos($useragent, "iPhone");
                $Android = stripos($useragent, "Android");
                $iOS = stripos($useragent, "iOS");

                $DEVICE = ($iPod || $iPad || $iPhone || $Android || $iOS);
                $is_agent = '';
                if (!$DEVICE) {
                    $is_agent = 'desktop';
                    $login_check = 'data-toggle="modal" data-target="#myLoginModal"';
                } else {
                    $is_agent = 'mobile';
                    $login_check = 'data-toggle="modal" data-target="#myLoginModalFront"';
                }
            }
            /*$html_two_team.='
				<tr>
                	<td class="text-color-grey fancybet-block" colspan="7">
                    	<div class="dark-blue-bg-1 text-color-white">
                        	<a> <img src="'.asset('asset/front/img/pin-bg.png').'"> <img src="'.asset('asset/front/img/pin-bg-1.png').'" class="hover-img"> </a>
                            Fancy Bet <span id="div_min_bet_fancy_limit" style="display:none">'.$min_bet_fancy_limit.'</span> <span id="div_max_bet_fancy_limit" style="display:none">'.$max_bet_fancy_limit.'</span>
                       	</div>
                  	</td>
              	</tr>';*/
            $html_two_team .= '<tr class="bets-fancy desktop-ui-tr white-bg">
            	<td colspan="3">
					<div class="minmax-txt minmaxmobile" style="padding-left:0px">
                    	<span>Min</span>
						<span id="div_min_bet_odds_limit">' . $min_bet_fancy_limit . '</span>
						<span>Max</span>
						<span id="div_max_bet_odds_limit">' . $max_bet_fancy_limit . '</span>
					</div>
				</td>
                <td style="padding-left: 0px;
				padding-right: 0px;
				padding-bottom: 0px;
				vertical-align: bottom;">
				<a class="layall_fancy" style="position: relative;
				line-height: 17px;
				cursor: pointer;">
				<img src="' . asset('asset/front/img/pinkbg1_fancy.png') . '" style="width: 100%;
				height: 25px;">
				<span style="position: absolute;
				top: 0;
				left: 5%;
				width: 90%;
				text-align: center;
				font-weight: 700;">No</span>
				</a></td>
				<td style="padding-left: 0px;
				padding-right: 0px;
				padding-bottom: 0px;
				vertical-align: bottom;">
				<a class="backall_fancy" style="position: relative;
				line-height: 17px;
				cursor: pointer;">
				<img src="' . asset('asset/front/img/bluebg1_fancy.png') . '" style="width: 100%;
				height: 25px;">
				<span style="position: absolute;
				top: 0;
				left: 5%;
				width: 90%;
				text-align: center;
				font-weight: 700;">Yes</span>
					</a>
				</td>
                <td colspan="1"></td>
            </tr>';

            $html_two_team .= '<tr class="bets-fancy mobile-ui-tr collapse white-bg">
            	<td colspan="3"></td>
                <td style="min-width: 70px;">
                    <span style="">No</span>
                </td>
                <td style="min-width: 70px;">
                    <span style="">Yes</span>
				</td>
                <td></td>
            </tr>';

            $nat = array();
            $gstatus = array();
            $b = array();
            $l = array();
            $bs = array();
            $ls = array();
            $min = array();
            $max = array();
            $sid = array();
            if (@$match_data['t3']) {
                foreach ($match_data['t3'] as $key => $value) {
                    $sid_val = '';
                    foreach ($value as $key1 => $value1) {
                        if ($key1 == 'sid') {
                            $sid_val = $value1;
                            $sid[] = $value1;
                        }
                        if ($key1 == 'nat')
                            $nat[$sid_val] = $value1;
                        if ($key1 == 'gstatus') {
                            $gstatus[$sid_val] = $value1;
                        }
                        if ($key1 == 'b1')
                            $b[$sid_val] = $value1;
                        if ($key1 == 'l1')
                            $l[$sid_val] = $value1;
                        if ($key1 == 'bs1')
                            $bs[$sid_val] = $value1;
                        if ($key1 == 'ls1')
                            $ls[$sid_val] = $value1;
                        if ($key1 == 'min')
                            $min[$sid_val] = $value1;
                        if ($key1 == 'max')
                            $max[$sid_val] = $value1;
                    }
                }

                sort($sid);
                for ($i = 0; $i < sizeof($sid); $i++) {
                    $max_val = 0;
                    if ($max[$sid[$i]] > 999) {
                        $input = number_format($max[$sid[$i]]);
                        $input_count = substr_count($input, ',');
                        $arr = array(1 => 'K', 'M', 'B', 'T');
                        if (isset($arr[(int)$input_count]))
                            $max_val = substr($input, 0, (-1 * $input_count) * 4) . $arr[(int)$input_count];
                        else
                            $max_val = $input;
                    }

                    if ($match_f == '0') {
                        $getUserCheck = Session::get('playerUser');
                        if (!empty($getUserCheck)) {
                            $sessionData = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
                        }
                        if (!empty($sessionData)) {
                            $getUserCheckuser = Session::get('playerUser');
                            if (!empty($getUserCheckuser)) {
                                $getUser = User::where('id', $getUserCheckuser->id)->where('check_login', 1)->first();
                            }
                            $userId = $getUser->id;
                            $my_placed_bets = MyBets::where('user_id', $userId)->where('match_id', $eventId)->where('team_name', @$nat[$sid[$i]])->where('bet_type', 'SESSION')->where('isDeleted', 0)->where('result_declare', 0)->orderBy('created_at', 'asc')->get();
                            $abc = sizeof($my_placed_bets);
                            if (sizeof($my_placed_bets) > 0) {
                                $run_arr = array();
                                foreach ($my_placed_bets as $bet) {
                                    $down_position = $bet->bet_odds - 1;
                                    if (!in_array($down_position, $run_arr)) {
                                        $run_arr[] = $down_position;
                                    }
                                    $level_position = $bet->bet_odds;
                                    if (!in_array($level_position, $run_arr)) {
                                        $run_arr[] = $level_position;
                                    }
                                    $up_position = $bet->bet_odds + 1;
                                    if (!in_array($up_position, $run_arr)) {
                                        $run_arr[] = $up_position;
                                    }
                                }
                                array_unique($run_arr);
                                sort($run_arr);

                                $min_val = min($run_arr);
                                $max_val = max($run_arr);

                                $newArr = array();

                                for ($z = $min_val; $z <= $max_val; ++$z) {
                                    $new = $z;
                                    $newArr[] = $new;
                                }

                                $run_arr = array();
                                $run_arr = $newArr;

                                $bet_chk = '';
                                $bet_model = '';
                                for ($kk = 0; $kk < sizeof($run_arr); $kk++) {
                                    $bet_deduct_amt = 0;
                                    $placed_bet_type = '';
                                    foreach ($my_placed_bets as $bet) {
                                        if ($bet->bet_side == 'back') {
                                            if ($bet->bet_odds == $run_arr[$kk]) {
                                                $bet_deduct_amt = $bet_deduct_amt + $bet->bet_profit;
                                            } else if ($bet->bet_odds < $run_arr[$kk]) {
                                                $bet_deduct_amt = $bet_deduct_amt + $bet->bet_profit;
                                            } else if ($bet->bet_odds > $run_arr[$kk]) {
                                                $bet_deduct_amt = $bet_deduct_amt - $bet->exposureAmt;
                                            }
                                        } else if ($bet->bet_side == 'lay') {
                                            if ($bet->bet_odds == $run_arr[$kk]) {
                                                $bet_deduct_amt = $bet_deduct_amt - $bet->exposureAmt;
                                            } else if ($bet->bet_odds < $run_arr[$kk]) {
                                                $bet_deduct_amt = $bet_deduct_amt - $bet->exposureAmt;
                                            } else if ($bet->bet_odds > $run_arr[$kk]) {
                                                $bet_deduct_amt = $bet_deduct_amt + $bet->bet_amount;
                                            }
                                        }
                                    }
                                    if ($final_exposer == "")
                                        $final_exposer = $bet_deduct_amt;
                                    else {
                                        if ($final_exposer > $bet_deduct_amt)
                                            $final_exposer = $bet_deduct_amt;
                                    }

                                    if ($bet_deduct_amt > 0) {
                                        $position .= '<tr>
											<td class="text-center cyan-bg">' . $run_arr[$kk] . '</td>
											<td class="text-right cyan-bg">' . $bet_deduct_amt . '</td>
											</tr>';
                                    } else {
                                        $position .= '<tr>
											<td class="text-center pink-bg">' . $run_arr[$kk] . '</td>
											<td class="text-right pink-bg">' . $bet_deduct_amt . '</td>
											</tr>';
                                    }
                                }
                                if ($position != '') {
                                    $bet_model = '<div class="modal credit-modal" id="runPosition' . $i . '">
											<div class="modal-dialog">
												<div class="modal-content light-grey-bg-1">
													<div class="modal-header">
														<h4 class="modal-title text-color-blue-1">Run Position</h4>
														<button type="button" class="close modelclose" data-dismiss="modal"><img src="' . asset('asset/front/img/close-icon.png') . '" alt=""></button>
													</div>
													<div class="modal-body white-bg p-3">
														<table class="table table-bordered w-100 fonts-1 mb-0">
															<thead>
																<tr>
																	<th width="50%" class="text-center">Run</th>
																	<th width="50%" class="text-right">Amount</th>
																</tr>
															</thead>
															<tbody> ' . $position . '</tbody>
														</table>
													</div>
												</div>
											</div>
										</div>';
                                }
                            }
                        }

                        $display = '';
                        $cls = '';
                        if ($bet_model == '') {
                            $display = 'style="display:block"';
                        }
                        if ($bet_model != '') {
                            $cls = 'text-color-red';
                            $all_bet_model .= $bet_model;
                        }
                        //end for bet calculation

                        $html_two .= '
						<tr class="fancy-suspend-tr desktop-ui-tr">
                            <td colspan="3"></td>
                            <td class="fancy-suspend-td" colspan="2">
                                <div class="fancy-suspend black-bg-5 text-color-white"><span>SUSPENDED</span></div>
                            </td>
						</tr>
						<tr class="white-bg desktop-ui-tr">
                            <td colspan="3"><b>' . $nat[$sid[$i]] . '</b>
                                <div>
                                <a class="openfancymodel_dynamic" data-toggle="modal" data-target="#runPosition' . $i . '">
                                    <span class="lose ' . $cls . '" ' . $display . ' id="Fancy_Total_Div"><span id="Fancy_Total_' . $i . '">' . abs($final_exposer) . '</span></span>
                                </a>
                                </div>
                            </td>
                            <td class="pink-bg back1btn text-center FancyLay" id="td_fancy_lay_' . $i . '" onClick="colorclick(this.id)">
                                <a><br> <span>--</span></a></td>
                            <td class="lay1btn cyan-bg text-center FancyBack" id="td_fancy_back_' . $i . '" onClick="colorclickback(this.id)">
                                <a>--<br> <span>--</span></a>
                            </td>
                            <td class="zeroopa1" colspan="1"> <span></span> <br></td>
                        </tr>';

                        $html_two .= '
						<tr class="white-bg mobile-ui-tr light-bg-tr-fancy collapse">
                            <td colspan="3"><b>' . $nat[$sid[$i]] . '</b>
                                <div>
                                <a class="openfancymodel_dynamic" data-toggle="modal" data-target="#runPosition' . $i . '">
                                    <span class="lose ' . $cls . '" ' . $display . ' id="Fancy_Total_Div"><span id="Fancy_Total_' . $i . '">' . abs($final_exposer) . '</span></span>
                                </a>
                                </div>
                            </td>
                            <td colspan="2" class="text-right" align="right" valign="middle">
                                <a href="#feeds7_' . $i . '" class="" data-toggle="collapse">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15"><path fill="%233B5160" fill-rule="evenodd" d="M6.76 5.246V3.732h1.48v1.514H6.76zm.74 8.276a5.86 5.86 0 0 0 3.029-.83 5.839 5.839 0 0 0 2.163-2.163 5.86 5.86 0 0 0 .83-3.029 5.86 5.86 0 0 0-.83-3.029 5.839 5.839 0 0 0-2.163-2.163 5.86 5.86 0 0 0-3.029-.83 5.86 5.86 0 0 0-3.029.83A5.839 5.839 0 0 0 2.308 4.47a5.86 5.86 0 0 0-.83 3.029 5.86 5.86 0 0 0 .83 3.029 5.839 5.839 0 0 0 2.163 2.163 5.86 5.86 0 0 0 3.029.83zM7.5 0c1.37 0 2.638.343 3.804 1.028a7.108 7.108 0 0 1 2.668 2.668A7.376 7.376 0 0 1 15 7.5c0 1.37-.343 2.638-1.028 3.804a7.108 7.108 0 0 1-2.668 2.668A7.376 7.376 0 0 1 7.5 15a7.376 7.376 0 0 1-3.804-1.028 7.243 7.243 0 0 1-2.668-2.686A7.343 7.343 0 0 1 0 7.5c0-1.358.343-2.62 1.028-3.786a7.381 7.381 0 0 1 2.686-2.686A7.343 7.343 0 0 1 7.5 0zm-.74 11.268V6.761h1.48v4.507H6.76z"/></svg>
                                </a>
                                    <div id="feeds7_' . $i . '" class="collapse fancy_minmax_info text-let">
                                        <dl>
                                            <dt>Min / Max</dt>
                                            <dd id="minMax"> ' . $match_detail['min_fancy_limit'] . ' / ' . $match_detail['max_fancy_limit'] . '</dd>
                                        </dl>
                                    </div>
                            </td>
                            <td class="zeroopa1" colspan="1"> <span></span> <br></td>
                        </tr>';

                        $html_two .= '
						<tr class="white-bg white-bg-tr-fancy mobile-ui-tr collapse">
                            <td colspan="3"></td>
                            <td class="pink-bg back1btn text-center FancyLay" id="td_fancy_lay_' . $i . '" onClick="colorclick(this.id)">
                                <a><br> <span>--</span></a></td>
                            <td class="lay1btn cyan-bg text-center FancyBack" id="td_fancy_back_' . $i . '" onClick="colorclickback(this.id)">
                                <a>--<br> <span>--</span></a>
                            </td>
                            <td class="zeroopa1" colspan="1"> <span></span> <br></td>
                        </tr>
                        <tr class="fancy-suspend-tr white-bg-tr-fancy mobile-ui-tr collapse">
                            <td colspan="3"></td>
                            <td class="fancy-suspend-td" colspan="2">
                                <div class="fancy-suspend black-bg-5 text-color-white"><span>SUSPENDED</span></div>
                            </td>
						</tr>
                        ';
                    } else {
                        $placed_bet = '';
                        $position = '';
                        $bet_model = '';
                        $abc = '';
                        $final_exposer = '';
                        if ($gstatus[$sid[$i]] != 'Ball Running' && $gstatus[$sid[$i]] != 'Suspended' && $l[$sid[$i]] != 0 && round($b[$sid[$i]]) != 0) {
                            if ($l[$sid[$i]] != 0 && round($b[$sid[$i]]) != 0 && $l[$sid[$i]] != '' && $b[$sid[$i]] != '') {
                                //bet calculation
                                $getUserCheck = Session::get('playerUser');
                                if (!empty($getUserCheck)) {
                                    $sessionData = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
                                }
                                if (!empty($sessionData)) {
                                    $getUserCheckuser = Session::get('playerUser');
                                    if (!empty($getUserCheckuser)) {
                                        $getUser = User::where('id', $getUserCheckuser->id)->where('check_login', 1)->first();
                                    }

                                    $userId = $getUser->id;
                                    $my_placed_bets = MyBets::where('user_id', $userId)->where('match_id', $eventId)->where('team_name', @$nat[$sid[$i]])->where('bet_type', 'SESSION')->where('isDeleted', 0)->where('result_declare', 0)->orderBy('created_at', 'asc')->get();
                                    $abc = sizeof($my_placed_bets);
                                    if (sizeof($my_placed_bets) > 0) {
                                        $run_arr = array();
                                        foreach ($my_placed_bets as $bet) {
                                            $down_position = $bet->bet_odds - 1;
                                            if (!in_array($down_position, $run_arr)) {
                                                $run_arr[] = $down_position;
                                            }
                                            $level_position = $bet->bet_odds;
                                            if (!in_array($level_position, $run_arr)) {
                                                $run_arr[] = $level_position;
                                            }
                                            $up_position = $bet->bet_odds + 1;
                                            if (!in_array($up_position, $run_arr)) {
                                                $run_arr[] = $up_position;
                                            }
                                        }
                                        array_unique($run_arr);
                                        sort($run_arr);

                                        $min_val = min($run_arr);
                                        $max_val = max($run_arr);

                                        $newArr = array();

                                        for ($z = $min_val; $z <= $max_val; ++$z) {
                                            $new = $z;
                                            $newArr[] = $new;
                                        }

                                        $run_arr = array();
                                        $run_arr = $newArr;

                                        $bet_chk = '';
                                        $bet_model = '';
                                        for ($kk = 0; $kk < sizeof($run_arr); $kk++) {
                                            $bet_deduct_amt = 0;
                                            $placed_bet_type = '';
                                            foreach ($my_placed_bets as $bet) {
                                                if ($bet->bet_side == 'back') {
                                                    if ($bet->bet_odds == $run_arr[$kk]) {
                                                        $bet_deduct_amt = $bet_deduct_amt + $bet->bet_profit;
                                                    } else if ($bet->bet_odds < $run_arr[$kk]) {
                                                        $bet_deduct_amt = $bet_deduct_amt + $bet->bet_profit;
                                                    } else if ($bet->bet_odds > $run_arr[$kk]) {
                                                        $bet_deduct_amt = $bet_deduct_amt - $bet->exposureAmt;
                                                    }
                                                } else if ($bet->bet_side == 'lay') {
                                                    if ($bet->bet_odds == $run_arr[$kk]) {
                                                        $bet_deduct_amt = $bet_deduct_amt - $bet->exposureAmt;
                                                    } else if ($bet->bet_odds < $run_arr[$kk]) {
                                                        $bet_deduct_amt = $bet_deduct_amt - $bet->exposureAmt;
                                                    } else if ($bet->bet_odds > $run_arr[$kk]) {
                                                        $bet_deduct_amt = $bet_deduct_amt + $bet->bet_amount;
                                                    }
                                                }
                                            }
                                            if ($final_exposer == "")
                                                $final_exposer = $bet_deduct_amt;
                                            else {
                                                if ($final_exposer > $bet_deduct_amt)
                                                    $final_exposer = $bet_deduct_amt;
                                            }

                                            if ($bet_deduct_amt > 0) {
                                                $position .= '<tr>
												<td class="text-center cyan-bg">' . $run_arr[$kk] . '</td>
												<td class="text-right cyan-bg">' . $bet_deduct_amt . '</td>
												</tr>';
                                            } else {
                                                $position .= '<tr>
												<td class="text-center pink-bg">' . $run_arr[$kk] . '</td>
												<td class="text-right pink-bg">' . $bet_deduct_amt . '</td>
												</tr>';
                                            }
                                        }

                                        //$abc=sizeof($my_placed_bets);
                                        /*if(sizeof($my_placed_bets)>0)
									{
										$run_arr=array();
										foreach($my_placed_bets as $bet)
										{
											$down_position=$bet->bet_odds-1;
											if(!in_array($down_position,$run_arr))
											{
												$run_arr[]=$down_position;
											}
											$level_position=$bet->bet_odds;
											if(!in_array($level_position,$run_arr))
											{
												$run_arr[]=$level_position;
											}
											$up_position=$bet->bet_odds+1;
											if(!in_array($up_position,$run_arr))
											{
												$run_arr[]=$up_position;
											}
										}
										array_unique($run_arr);
										sort($run_arr);
										$bet_chk='';
										for($kk=0;$kk<sizeof($run_arr);$kk++)
										{
											$bet_deduct_amt=0; $placed_bet_type='';
											foreach($my_placed_bets as $bet)
											{
												if($bet->bet_side=='back')
												{
													if($bet->bet_odds==$run_arr[$kk])
													{
														$bet_deduct_amt=$bet_deduct_amt+$bet->exposureAmt;
													}
													else if($bet->bet_odds<$run_arr[$kk])
													{
														$bet_deduct_amt=$bet_deduct_amt+$bet->exposureAmt;
													}
													else if($bet->bet_odds>$run_arr[$kk])
													{
														$bet_deduct_amt=$bet_deduct_amt-$bet->bet_amount;
													}
												}
												else if($bet->bet_side=='lay')
												{
													if($bet->bet_odds==$run_arr[$kk])
													{
														$bet_deduct_amt=$bet_deduct_amt-$bet->exposureAmt;
													}
													else if($bet->bet_odds<$run_arr[$kk])
													{
														$bet_deduct_amt=$bet_deduct_amt-$bet->exposureAmt;
													}
													else if($bet->bet_odds>$run_arr[$kk])
													{
														$bet_deduct_amt=$bet_deduct_amt+$bet->bet_amount;
													}
												}
											}
											if($final_exposer=="")
												$final_exposer=$bet_deduct_amt;
											else
											{
												if($final_exposer>$bet_deduct_amt)
												$final_exposer=$bet_deduct_amt;
											}

											if($bet_deduct_amt>0) {
												$position.='<tr>
												<td class="text-center cyan-bg">'.$run_arr[$kk].'333</td>
												<td class="text-right cyan-bg">'.$bet_deduct_amt.'</td>
												</tr>';
											}
											else
											{
												$position.='<tr>
												<td class="text-center pink-bg">'.$run_arr[$kk].'444</td>
												<td class="text-right pink-bg">'.$bet_deduct_amt.'</td>
												</tr>';
											}
										}*/
                                        if ($position != '') {
                                            $bet_model = '<div class="modal credit-modal" id="runPosition' . $i . '">
												<div class="modal-dialog">
													<div class="modal-content light-grey-bg-1">
														<div class="modal-header">
															<h4 class="modal-title text-color-blue-1">Run Position</h4>
															<button type="button" class="close modelclose" data-dismiss="modal"><img src="' . asset('asset/front/img/close-icon.png') . '" alt=""></button>
														</div>
														<div class="modal-body white-bg p-3">
															<table class="table table-bordered w-100 fonts-1 mb-0">
																<thead>
																	<tr>
																		<th width="50%" class="text-center">Run</th>
																		<th width="50%" class="text-right">Amount</th>
																	</tr>
																</thead>
																<tbody> ' . $position . '</tbody>
															</table>
														</div>
													</div>
												</div>
											</div>';
                                        }
                                    }
                                }

                                $display = '';
                                $cls = '';
                                if ($bet_model == '') {
                                    $display = 'style="display:block"';
                                }
                                if ($bet_model != '') {
                                    $cls = 'text-color-red';
                                    $all_bet_model .= $bet_model;

                                }
                                //end for bet calculation

                                $html_two .= '<tr class="white-bg desktop-ui-tr">
									<td colspan="3"><b>' . $nat[$sid[$i]] . '</b>
										<div>
										<a class="openfancymodel_dynamic" data-toggle="modal" data-target="#runPosition' . $i . '">
											<span class="lose ' . $cls . '" ' . $display . ' id="Fancy_Total_Div"><span id="Fancy_Total_' . $i . '">' . abs($final_exposer) . '</span></span>
										</a>
										</div>

									</td>
									<td class="pink-bg back1btn text-center FancyLay" id="td_fancy_lay_' . $i . '" data-team="' . $nat[$sid[$i]] . '" onClick="colorclick(this.id)">
										<a data-bettype="SESSION" data-position="' . $i . '" data-team="' . $nat[$sid[$i]] . '" ' . $login_check . ' data-cls="pink-bg" data-volume="' . round($ls[$sid[$i]]) . '" data-val="' . round($l[$sid[$i]]) . '">' . round($l[$sid[$i]]) . '<br> <span>' . round($ls[$sid[$i]]) . '</span></a></td>
									<td class="lay1btn cyan-bg text-center FancyBack" id="td_fancy_back_' . $i . '" data-team="' . $nat[$sid[$i]] . '" onClick="colorclickback(this.id)">
										<a data-bettype="SESSION" data-position="' . $i . '" data-team="' . $nat[$sid[$i]] . '" ' . $login_check . ' data-cls="cyan-bg" data-volume="' . round($bs[$sid[$i]]) . '" data-val="' . round($b[$sid[$i]]) . '">' . round($b[$sid[$i]]) . '<br> <span>' . round($bs[$sid[$i]]) . '</span></a>
									</td>
									<td class="zeroopa1" colspan="1"> <span>Min/Max</span> <br> ' . $match_detail['min_fancy_limit'] . ' / ' . $match_detail['max_fancy_limit'] . '</td>
								</tr>';
                                $html_two .= '<tr class="white-bg light-bg-tr-fancy mobile-ui-tr collapse tr_fancy_' . $i . '">
									<td colspan="3"><b>' . $nat[$sid[$i]] . '</b>
										<div><a class="openfancymodel_dynamic" data-toggle="modal" data-target="#runPosition' . $i . '">
											<span class="lose ' . $cls . '" ' . $display . ' id="Fancy_Total_Div"><span id="Fancy_Total_' . $i . '">' . abs($final_exposer) . '</span></span>
										</a>
										</div>
									</td>
									<td colspan="2" class="td_fancy_lay_colspan_' . $i . ' text-right" align="right" valign="middle">
										<a href="#feeds8_' . $i . '" class="" data-toggle="collapse">
										<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15"><path fill="%233B5160" fill-rule="evenodd" d="M6.76 5.246V3.732h1.48v1.514H6.76zm.74 8.276a5.86 5.86 0 0 0 3.029-.83 5.839 5.839 0 0 0 2.163-2.163 5.86 5.86 0 0 0 .83-3.029 5.86 5.86 0 0 0-.83-3.029 5.839 5.839 0 0 0-2.163-2.163 5.86 5.86 0 0 0-3.029-.83 5.86 5.86 0 0 0-3.029.83A5.839 5.839 0 0 0 2.308 4.47a5.86 5.86 0 0 0-.83 3.029 5.86 5.86 0 0 0 .83 3.029 5.839 5.839 0 0 0 2.163 2.163 5.86 5.86 0 0 0 3.029.83zM7.5 0c1.37 0 2.638.343 3.804 1.028a7.108 7.108 0 0 1 2.668 2.668A7.376 7.376 0 0 1 15 7.5c0 1.37-.343 2.638-1.028 3.804a7.108 7.108 0 0 1-2.668 2.668A7.376 7.376 0 0 1 7.5 15a7.376 7.376 0 0 1-3.804-1.028 7.243 7.243 0 0 1-2.668-2.686A7.343 7.343 0 0 1 0 7.5c0-1.358.343-2.62 1.028-3.786a7.381 7.381 0 0 1 2.686-2.686A7.343 7.343 0 0 1 7.5 0zm-.74 11.268V6.761h1.48v4.507H6.76z"/></svg>
										</a>
											<div id="feeds8_' . $i . '" class="collapse fancy_minmax_info text-let">
											    <dl>
													<dt>Min / Max</dt>
													<dd id="minMax"> ' . $match_detail['min_fancy_limit'] . ' / ' . $match_detail['max_fancy_limit'] . '</dd>
												</dl>
											</div>
									</td>
									<td class="zeroopa1" colspan="1"> <span>Min/Max</span> <br> ' . $match_detail['min_fancy_limit'] . ' / ' . $match_detail['max_fancy_limit'] . '</td>
								</tr>';
                                $html_two .= '<tr class="white-bg white-bg-tr-fancy mobile-ui-tr collapse tr_fancy_' . $i . '">
									<td colspan="3"></td>
									<td class="pink-bg back1btn text-center FancyLay td_fancy_lay_' . $i . '" data-team="' . $nat[$sid[$i]] . '" id="td_fancy_lay_' . $i . '" onClick="colorclick(this.id)">
										<a data-bettype="SESSION" data-position="' . $i . '" data-team="' . $nat[$sid[$i]] . '" ' . $login_check . ' data-cls="pink-bg" data-volume="' . round($ls[$sid[$i]]) . '" data-val="' . round($l[$sid[$i]]) . '">' . round($l[$sid[$i]]) . '<br> <span>' . round($ls[$sid[$i]]) . '</span></a></td>
									<td class="lay1btn cyan-bg text-center FancyBack td_fancy_back_' . $i . '" data-team="' . $nat[$sid[$i]] . '" id="td_fancy_back_' . $i . '" onClick="colorclickback(this.id)">
										<a data-bettype="SESSION" data-position="' . $i . '" data-team="' . $nat[$sid[$i]] . '" ' . $login_check . ' data-cls="cyan-bg" data-volume="' . round($bs[$sid[$i]]) . '" data-val="' . round($b[$sid[$i]]) . '">' . round($b[$sid[$i]]) . '<br> <span>' . round($bs[$sid[$i]]) . '</span></a>
									</td>
									<td class="zeroopa1" colspan="1"> <span>Min/Max</span> <br> ' . $match_detail['min_fancy_limit'] . ' / ' . $match_detail['max_fancy_limit'] . '</td>
								</tr>
								<tr class="mobileBack tr_team' . $i . '_fancy mobile_bet_model_div" id="mobile_tr">
									<td colspan="6" class="tr_team' . $i . '_fancy_td_mobile mobile_tr_common_class"></td>
								</tr>';
                            } else {
                                //for bet calculation
                                $getUserCheck = Session::get('playerUser');
                                if (!empty($getUserCheck)) {
                                    $sessionData = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
                                }
                                if (!empty($sessionData)) {
                                    $getUserCheckuser = Session::get('playerUser');
                                    if (!empty($getUserCheckuser)) {
                                        $getUser = User::where('id', $getUserCheckuser->id)->where('check_login', 1)->first();
                                    }

                                    $userId = $getUser->id;
                                    $my_placed_bets = MyBets::where('user_id', $userId)->where('match_id', $eventId)->where('team_name', @$nat[$sid[$i]])->where('bet_type', 'SESSION')->where('isDeleted', 0)->where('result_declare', 0)->orderBy('created_at', 'asc')->get();
                                    $abc = sizeof($my_placed_bets);
                                    if (sizeof($my_placed_bets) > 0) {
                                        $run_arr = array();
                                        foreach ($my_placed_bets as $bet) {
                                            $down_position = $bet->bet_odds - 1;
                                            if (!in_array($down_position, $run_arr)) {
                                                $run_arr[] = $down_position;
                                            }
                                            $level_position = $bet->bet_odds;
                                            if (!in_array($level_position, $run_arr)) {
                                                $run_arr[] = $level_position;
                                            }
                                            $up_position = $bet->bet_odds + 1;
                                            if (!in_array($up_position, $run_arr)) {
                                                $run_arr[] = $up_position;
                                            }
                                        }
                                        array_unique($run_arr);
                                        sort($run_arr);

                                        $min_val = min($run_arr);
                                        $max_val = max($run_arr);

                                        $newArr = array();

                                        for ($z = $min_val; $z <= $max_val; ++$z) {
                                            $new = $z;
                                            $newArr[] = $new;
                                        }

                                        $run_arr = array();
                                        $run_arr = $newArr;

                                        $bet_chk = '';
                                        $bet_model = '';
                                        for ($kk = 0; $kk < sizeof($run_arr); $kk++) {
                                            $bet_deduct_amt = 0;
                                            $placed_bet_type = '';
                                            foreach ($my_placed_bets as $bet) {
                                                if ($bet->bet_side == 'back') {
                                                    if ($bet->bet_odds == $run_arr[$kk]) {
                                                        $bet_deduct_amt = $bet_deduct_amt + $bet->bet_profit;
                                                    } else if ($bet->bet_odds < $run_arr[$kk]) {
                                                        $bet_deduct_amt = $bet_deduct_amt + $bet->bet_profit;
                                                    } else if ($bet->bet_odds > $run_arr[$kk]) {
                                                        $bet_deduct_amt = $bet_deduct_amt - $bet->exposureAmt;
                                                    }
                                                } else if ($bet->bet_side == 'lay') {
                                                    if ($bet->bet_odds == $run_arr[$kk]) {
                                                        $bet_deduct_amt = $bet_deduct_amt - $bet->exposureAmt;
                                                    } else if ($bet->bet_odds < $run_arr[$kk]) {
                                                        $bet_deduct_amt = $bet_deduct_amt - $bet->exposureAmt;
                                                    } else if ($bet->bet_odds > $run_arr[$kk]) {
                                                        $bet_deduct_amt = $bet_deduct_amt + $bet->bet_amount;
                                                    }
                                                }
                                            }
                                            if ($final_exposer == "")
                                                $final_exposer = $bet_deduct_amt;
                                            else {
                                                if ($final_exposer > $bet_deduct_amt)
                                                    $final_exposer = $bet_deduct_amt;
                                            }

                                            if ($bet_deduct_amt > 0) {
                                                $position .= '<tr>
												<td class="text-center cyan-bg">' . $run_arr[$kk] . '</td>
												<td class="text-right cyan-bg">' . $bet_deduct_amt . '</td>
												</tr>';
                                            } else {
                                                $position .= '<tr>
												<td class="text-center pink-bg">' . $run_arr[$kk] . '</td>
												<td class="text-right pink-bg">' . $bet_deduct_amt . '</td>
												</tr>';
                                            }
                                        }
                                        /*$abc=sizeof($my_placed_bets);
									if(sizeof($my_placed_bets)>0)
									{
										$run_arr=array();
										foreach($my_placed_bets as $bet)
										{
											$down_position=$bet->bet_odds-1;
											if(!in_array($down_position,$run_arr))
											{
												$run_arr[]=$down_position;
											}
											$level_position=$bet->bet_odds;
											if(!in_array($level_position,$run_arr))
											{
												$run_arr[]=$level_position;
											}
											$up_position=$bet->bet_odds+1;
											if(!in_array($up_position,$run_arr))
											{
												$run_arr[]=$up_position;
											}
										}
										array_unique($run_arr);
										sort($run_arr);
										$bet_chk='';
										for($kk=0;$kk<sizeof($run_arr);$kk++)
										{
											$bet_deduct_amt=0; $placed_bet_type='';
											foreach($my_placed_bets as $bet)
											{
												if($bet->bet_side=='back')
												{
													if($bet->bet_odds==$run_arr[$kk])
													{
														$bet_deduct_amt=$bet_deduct_amt+$bet->exposureAmt;

													}
													else if($bet->bet_odds<$run_arr[$kk])
													{
														$bet_deduct_amt=$bet_deduct_amt+$bet->exposureAmt;
													}
													else if($bet->bet_odds>$run_arr[$kk])
													{
														$bet_deduct_amt=$bet_deduct_amt-$bet->bet_amount;
													}
												}
												else if($bet->bet_side=='lay')
												{
													if($bet->bet_odds==$run_arr[$kk])
													{
														$bet_deduct_amt=$bet_deduct_amt-$bet->exposureAmt;
													}
													else if($bet->bet_odds<$run_arr[$kk])
													{
														$bet_deduct_amt=$bet_deduct_amt-$bet->exposureAmt;
													}
													else if($bet->bet_odds>$run_arr[$kk])
													{
														$bet_deduct_amt=$bet_deduct_amt+$bet->bet_amount;
													}
												}
											}
											if($final_exposer=="")
												$final_exposer=$bet_deduct_amt;
											else
											{
												if($final_exposer>$bet_deduct_amt)
												$final_exposer=$bet_deduct_amt;
											}

											if($bet_deduct_amt>0) {
												$position.='<tr>
												<td class="text-center cyan-bg">'.$run_arr[$kk].'555</td>
												<td class="text-right cyan-bg">'.$bet_deduct_amt.'666</td>
												</tr>';
											}
											else
											{
												$position.='<tr>
												<td class="text-center pink-bg">'.$run_arr[$kk].'777</td>
												<td class="text-right pink-bg">'.$bet_deduct_amt.'888</td>
												</tr>';
											}
										}*/
                                        if ($position != '') {
                                            $bet_model = '<div class="modal credit-modal" id="runPosition' . $i . '">
												<div class="modal-dialog">
													<div class="modal-content light-grey-bg-1">
														<div class="modal-header">
															<h4 class="modal-title text-color-blue-1">Run Position</h4>
															<button type="button" class="close modelclose" data-dismiss="modal"><img src="' . asset('asset/front/img/close-icon.png') . '" alt=""></button>
														</div>
														<div class="modal-body white-bg p-3">
															<table class="table table-bordered w-100 fonts-1 mb-0">
																<thead>
																	<tr>
																		<th width="50%" class="text-center">Run' . $abc . '</th>
																		<th width="50%" class="text-right">Amount</th>
																	</tr>
																</thead>
																<tbody> ' . $position . '</tbody>
															</table>
														</div>
													</div>
												</div>
											</div>';
                                        }
                                    }
                                }

                                $display = '';
                                $cls = '';
                                if ($bet_model == '') {
                                    $display = 'style="display:block"';
                                }
                                if ($bet_model != '') {
                                    $cls = 'text-color-red';
                                    $all_bet_model .= $bet_model;
                                }
                                //end for bet calculation
                                $html_two .= '<tr class="fancy-suspend-tr-1 desktop-ui-tr">
								<td colspan="3"></td>
								<td class="fancy-suspend-td-1" colspan="2">
									<div class="fancy-suspend-1 black-bg-5 text-color-white"><span>SUSPENDED</span></div>
								</td>
								</tr>
								<tr class="white-bg desktop-ui-tr">
									<td colspan="3"><b>' . $nat[$sid[$i]] . ' </b></td>
									<td class="pink-bg  back1btn text-center1111"><a> <br> <span> </span></a></td>
									<td class="cyan-bg lay1btn  text-center"><a> <br> <span> </span></a></td>
									<td class="zeroopa1" colspan="1"> <span>Min/Max</span> <br> ' . $match_detail['min_fancy_limit'] . ' / ' . $match_detail['max_fancy_limit'] . ' </td>
								</tr>';
                                $html_two .= '
								<tr class="white-bg light-bg-tr-fancy mobile-ui-tr collapse">
									<td colspan="3"><b>' . $nat[$sid[$i]] . ' </b></td>
									<td colspan="2" class="text-right" align="right" valign="middle">
										<a href="#feeds9_' . $i . '" class="" data-toggle="collapse">
										<svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15"><path fill="%233B5160" fill-rule="evenodd" d="M6.76 5.246V3.732h1.48v1.514H6.76zm.74 8.276a5.86 5.86 0 0 0 3.029-.83 5.839 5.839 0 0 0 2.163-2.163 5.86 5.86 0 0 0 .83-3.029 5.86 5.86 0 0 0-.83-3.029 5.839 5.839 0 0 0-2.163-2.163 5.86 5.86 0 0 0-3.029-.83 5.86 5.86 0 0 0-3.029.83A5.839 5.839 0 0 0 2.308 4.47a5.86 5.86 0 0 0-.83 3.029 5.86 5.86 0 0 0 .83 3.029 5.839 5.839 0 0 0 2.163 2.163 5.86 5.86 0 0 0 3.029.83zM7.5 0c1.37 0 2.638.343 3.804 1.028a7.108 7.108 0 0 1 2.668 2.668A7.376 7.376 0 0 1 15 7.5c0 1.37-.343 2.638-1.028 3.804a7.108 7.108 0 0 1-2.668 2.668A7.376 7.376 0 0 1 7.5 15a7.376 7.376 0 0 1-3.804-1.028 7.243 7.243 0 0 1-2.668-2.686A7.343 7.343 0 0 1 0 7.5c0-1.358.343-2.62 1.028-3.786a7.381 7.381 0 0 1 2.686-2.686A7.343 7.343 0 0 1 7.5 0zm-.74 11.268V6.761h1.48v4.507H6.76z"/></svg>
										</a>
											<div id="feeds9_' . $i . '" class="collapse fancy_minmax_info text-let">
											    <dl>
													<dt>Min / Max</dt>
													<dd id="minMax"> ' . $match_detail['min_fancy_limit'] . ' / ' . $match_detail['max_fancy_limit'] . '</dd>
												</dl>
											</div>
									</td>
									<td class="zeroopa1" colspan="1"> <span>Min/Max</span> <br> ' . $match_detail['min_fancy_limit'] . ' / ' . $match_detail['max_fancy_limit'] . ' </td>
								</tr>';

                                $html_two .= '<tr class="white-bg white-bg-tr-fancy mobile-ui-tr collapse">
									<td colspan="3"></td>
									<td class="pink-bg  back1btn text-center1111"><a> <br> <span> </span></a></td>
									<td class="cyan-bg lay1btn  text-center"><a> <br> <span> </span></a></td>
									<td class="zeroopa1" colspan="1"> <span>Min/Max</span> <br> ' . $match_detail['min_fancy_limit'] . ' / ' . $match_detail['max_fancy_limit'] . ' </td>
								</tr>
								<tr class="fancy-suspend-tr-1 white-bg-tr-fancy mobile-ui-tr collapse">
                                    <td colspan="3"></td>
                                    <td class="fancy-suspend-td-1" colspan="2">
                                        <div class="fancy-suspend-1 black-bg-5 text-color-white"><span>SUSPENDED</span></div>
                                    </td>
								</tr>
								';
                            }
                        } else {
                            //for bet calculation
                            $getUserCheck = Session::get('playerUser');
                            if (!empty($getUserCheck)) {
                                $sessionData = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
                            }
                            if (!empty($sessionData)) {
                                $getUserCheckuser = Session::get('playerUser');
                                if (!empty($getUserCheckuser)) {
                                    $getUser = User::where('id', $getUserCheckuser->id)->where('check_login', 1)->first();
                                }

                                $userId = $getUser->id;
                                $my_placed_bets = MyBets::where('user_id', $userId)->where('match_id', $eventId)->where('team_name', @$nat[$sid[$i]])->where('bet_type', 'SESSION')->where('isDeleted', 0)->where('result_declare', 0)->orderBy('created_at', 'asc')->get();
                                $abc = sizeof($my_placed_bets);
                                if (sizeof($my_placed_bets) > 0) {
                                    $run_arr = array();
                                    foreach ($my_placed_bets as $bet) {
                                        $down_position = $bet->bet_odds - 1;
                                        if (!in_array($down_position, $run_arr)) {
                                            $run_arr[] = $down_position;
                                        }
                                        $level_position = $bet->bet_odds;
                                        if (!in_array($level_position, $run_arr)) {
                                            $run_arr[] = $level_position;
                                        }
                                        $up_position = $bet->bet_odds + 1;
                                        if (!in_array($up_position, $run_arr)) {
                                            $run_arr[] = $up_position;
                                        }
                                    }
                                    array_unique($run_arr);
                                    sort($run_arr);

                                    $min_val = min($run_arr);
                                    $max_val = max($run_arr);

                                    $newArr = array();

                                    for ($z = $min_val; $z <= $max_val; ++$z) {
                                        $new = $z;
                                        $newArr[] = $new;
                                    }

                                    $run_arr = array();
                                    $run_arr = $newArr;

                                    $bet_chk = '';
                                    $bet_model = '';
                                    for ($kk = 0; $kk < sizeof($run_arr); $kk++) {
                                        $bet_deduct_amt = 0;
                                        $placed_bet_type = '';
                                        foreach ($my_placed_bets as $bet) {
                                            if ($bet->bet_side == 'back') {
                                                if ($bet->bet_odds == $run_arr[$kk]) {
                                                    $bet_deduct_amt = $bet_deduct_amt + $bet->bet_profit;
                                                } else if ($bet->bet_odds < $run_arr[$kk]) {
                                                    $bet_deduct_amt = $bet_deduct_amt + $bet->bet_profit;
                                                } else if ($bet->bet_odds > $run_arr[$kk]) {
                                                    $bet_deduct_amt = $bet_deduct_amt - $bet->exposureAmt;
                                                }
                                            } else if ($bet->bet_side == 'lay') {
                                                if ($bet->bet_odds == $run_arr[$kk]) {
                                                    $bet_deduct_amt = $bet_deduct_amt - $bet->exposureAmt;
                                                } else if ($bet->bet_odds < $run_arr[$kk]) {
                                                    $bet_deduct_amt = $bet_deduct_amt - $bet->exposureAmt;
                                                } else if ($bet->bet_odds > $run_arr[$kk]) {
                                                    $bet_deduct_amt = $bet_deduct_amt + $bet->bet_amount;
                                                }
                                            }
                                        }
                                        if ($final_exposer == "")
                                            $final_exposer = $bet_deduct_amt;
                                        else {
                                            if ($final_exposer > $bet_deduct_amt)
                                                $final_exposer = $bet_deduct_amt;
                                        }

                                        if ($bet_deduct_amt > 0) {
                                            $position .= '<tr>
													<td class="text-center cyan-bg">' . $run_arr[$kk] . '</td>
													<td class="text-right cyan-bg">' . $bet_deduct_amt . '</td>
													</tr>';
                                        } else {
                                            $position .= '<tr>
													<td class="text-center pink-bg">' . $run_arr[$kk] . '</td>
													<td class="text-right pink-bg">' . $bet_deduct_amt . '</td>
													</tr>';
                                        }
                                    }
                                    /*$abc=sizeof($my_placed_bets);
									if(sizeof($my_placed_bets)>0)
									{
										$run_arr=array();
										foreach($my_placed_bets as $bet)
										{
											$down_position=$bet->bet_odds-1;
											if(!in_array($down_position,$run_arr))
											{
												$run_arr[]=$down_position;
											}
											$level_position=$bet->bet_odds;
											if(!in_array($level_position,$run_arr))
											{
												$run_arr[]=$level_position;
											}
											$up_position=$bet->bet_odds+1;
											if(!in_array($up_position,$run_arr))
											{
												$run_arr[]=$up_position;
											}
										}
										array_unique($run_arr);
										sort($run_arr);
										$bet_chk='';
										for($kk=0;$kk<sizeof($run_arr);$kk++)
										{
											$bet_deduct_amt=0; $placed_bet_type='';
											foreach($my_placed_bets as $bet)
											{
												if($bet->bet_side=='back')
												{
													if($bet->bet_odds==$run_arr[$kk])
													{
														$bet_deduct_amt=$bet_deduct_amt+$bet->exposureAmt;
													}
													else if($bet->bet_odds<$run_arr[$kk])
													{
														$bet_deduct_amt=$bet_deduct_amt+$bet->exposureAmt;
													}
													else if($bet->bet_odds>$run_arr[$kk])
													{
														$bet_deduct_amt=$bet_deduct_amt-$bet->bet_amount;
													}
												}
												else if($bet->bet_side=='lay')
												{
													if($bet->bet_odds==$run_arr[$kk])
													{
														$bet_deduct_amt=$bet_deduct_amt-$bet->exposureAmt;
													}
													else if($bet->bet_odds<$run_arr[$kk])
													{
														$bet_deduct_amt=$bet_deduct_amt-$bet->exposureAmt;
													}
													else if($bet->bet_odds>$run_arr[$kk])
													{
														$bet_deduct_amt=$bet_deduct_amt+$bet->bet_amount;
													}
												}
											}
											if($final_exposer=="")
												$final_exposer=$bet_deduct_amt;
											else
											{
												if($final_exposer>$bet_deduct_amt)
													$final_exposer=$bet_deduct_amt;
											}

											if($bet_deduct_amt>0) {
												$position.='<tr>
												<td class="text-center cyan-bg">'.$run_arr[$kk].'999</td>
												<td class="text-right cyan-bg">'.$bet_deduct_amt.'1010</td>
												</tr>';
											}
											else
											{
												$position.='<tr>
												<td class="text-center pink-bg">'.$run_arr[$kk].'11 11</td>
												<td class="text-right pink-bg">'.$bet_deduct_amt.'12 12</td>
												</tr>';
											}
										}*/
                                    if ($position != '') {
                                        $bet_model = '<div class="modal credit-modal" id="runPosition' . $i . '">
												<div class="modal-dialog">
													<div class="modal-content light-grey-bg-1">
														<div class="modal-header">
															<h4 class="modal-title text-color-blue-1">Run Position</h4>
															<button type="button" class="close modelclose" data-dismiss="modal"><img src="' . asset('asset/front/img/close-icon.png') . '" alt=""></button>
														</div>
														<div class="modal-body white-bg p-3">
															<table class="table table-bordered w-100 fonts-1 mb-0">
																<thead>
																	<tr>
																		<th width="50%" class="text-center">Run' . $abc . '</th>
																		<th width="50%" class="text-right">Amount</th>
																	</tr>
																</thead>
																<tbody> ' . $position . '</tbody>
															</table>
														</div>
													</div>
												</div>
											</div>';
                                    }
                                }
                            }

                            $display = '';
                            $cls = '';
                            if ($bet_model == '') {
                                $display = 'style="display:block"';
                            }
                            if ($bet_model != '') {
                                $cls = 'text-color-red';
                                $all_bet_model .= $bet_model;
                            }
                            //end for bet calculation
                            $html_two .= '<tr class="fancy-suspend-tr-1 desktop-ui-tr">
								<td colspan="3"></td>
								<td class="fancy-suspend-td-1" colspan="2">
									<div class="fancy-suspend-1 black-bg-5 text-color-white"><span>' . strtoupper($gstatus[$sid[$i]]) . '</span></div>
								</td>
							</tr>
							<tr class="white-bg desktop-ui-tr">
								<td colspan="3"><b>' . $nat[$sid[$i]] . ' </b>
									<div><a class="openfancymodel_dynamic" data-toggle="modal" data-target="#runPosition' . $i . '">
											<span class="lose ' . $cls . '" ' . $display . ' id="Fancy_Total_Div"><span id="Fancy_Total_' . $i . '">' . abs($final_exposer) . '</span></span>
										</a>
										</div>
								</td>
								<td class="pink-bg  back1btn text-center"><a> <br> <span> </span></a></td>
								<td class="cyan-bg lay1btn  text-center"><a> <br> <span> </span></a></td>
								<td class="zeroopa1" colspan="1"> <span>Min/Max</span> <br> ' . $matchList['min_fancy_limit'] . ' / ' . $matchList['max_fancy_limit'] . ' </td>
							</tr>';

                            $html_two .= '<tr class="white-bg mobile-ui-tr light-bg-tr-fancy collapse">
								<td colspan="3"><b>' . $nat[$sid[$i]] . ' </b>
									<div><a class="openfancymodel_dynamic" data-toggle="modal" data-target="#runPosition' . $i . '">
											<span class="lose ' . $cls . '" ' . $display . ' id="Fancy_Total_Div"><span id="Fancy_Total_' . $i . '">' . abs($final_exposer) . '</span></span>
										</a>
										</div>
								</td>
								<td colspan="2" class="td_fancy_lay_colspan_' . $i . ' text-right" align="right" valign="middle">
                                    <a href="#feeds_tr4_' . $i . '" class="" data-toggle="collapse">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15"><path fill="%233B5160" fill-rule="evenodd" d="M6.76 5.246V3.732h1.48v1.514H6.76zm.74 8.276a5.86 5.86 0 0 0 3.029-.83 5.839 5.839 0 0 0 2.163-2.163 5.86 5.86 0 0 0 .83-3.029 5.86 5.86 0 0 0-.83-3.029 5.839 5.839 0 0 0-2.163-2.163 5.86 5.86 0 0 0-3.029-.83 5.86 5.86 0 0 0-3.029.83A5.839 5.839 0 0 0 2.308 4.47a5.86 5.86 0 0 0-.83 3.029 5.86 5.86 0 0 0 .83 3.029 5.839 5.839 0 0 0 2.163 2.163 5.86 5.86 0 0 0 3.029.83zM7.5 0c1.37 0 2.638.343 3.804 1.028a7.108 7.108 0 0 1 2.668 2.668A7.376 7.376 0 0 1 15 7.5c0 1.37-.343 2.638-1.028 3.804a7.108 7.108 0 0 1-2.668 2.668A7.376 7.376 0 0 1 7.5 15a7.376 7.376 0 0 1-3.804-1.028 7.243 7.243 0 0 1-2.668-2.686A7.343 7.343 0 0 1 0 7.5c0-1.358.343-2.62 1.028-3.786a7.381 7.381 0 0 1 2.686-2.686A7.343 7.343 0 0 1 7.5 0zm-.74 11.268V6.761h1.48v4.507H6.76z"/></svg>
                                    </a>
                                    <div id="feeds_tr4_' . $i . '" class="collapse fancy_minmax_info text-let">
                                        <dl>
                                            <dt>Min / Max</dt>
                                            <dd id="minMax"> ' . $match_detail['min_fancy_limit'] . ' / ' . $match_detail['max_fancy_limit'] . '</dd>
                                        </dl>
                                    </div>
								</td>
								<td class="zeroopa1" colspan="1"> <span>Min/Max</span> <br> ' . $matchList['min_fancy_limit'] . ' / ' . $matchList['max_fancy_limit'] . ' </td>
							</tr>';

                            $html_two .= '
							<tr class="white-bg mobile-ui-tr collapse white-bg-tr-fancy">
								<td colspan="3"></td>
								<td class="pink-bg  back1btn text-center"><a> <br> <span> </span></a></td>
								<td class="cyan-bg lay1btn  text-center"><a> <br> <span> </span></a></td>
								<td class="zeroopa1" colspan="1"> <span>Min/Max</span> <br> ' . $matchList['min_fancy_limit'] . ' / ' . $matchList['max_fancy_limit'] . ' </td>
							</tr>
							<tr class="fancy-suspend-tr-1 mobile-ui-tr collapse white-bg-tr-fancy">
								<td colspan="3"></td>
								<td class="fancy-suspend-td-1" colspan="2">
									<div class="fancy-suspend-1 black-bg-5 text-color-white"><span>' . strtoupper($gstatus[$sid[$i]]) . '</span></div>
								</td>
							</tr>
							';

                        }
                    } // end suspended if
                }
                if ($html_two != '')
                    $html_two = $html_two_team . $html_two;
            }
        }
        echo $html_two . '#######' . $all_bet_model;
    }

    public function matchCallFor_FANCY($matchId, Request $request)
    {
        $matchtype = $request->matchtype;
        $match_id = $request->match_id;
        $sport = Sport::where('sId', $matchtype)->first();
        $matchList = Match::where('id', $match_id)->where('status', 1)->first();

        $min_bet_odds_limit = $matchList->min_bookmaker_limit;
        $max_bet_odds_limit = $matchList->max_bookmaker_limit;

        $min_bet_fancy_limit = $matchList->min_fancy_limit;
        $max_bet_fancy_limit = $matchList->max_fancy_limit;

        $matchtype = $sport->id;
        $eventId = $request->event_id;
        $matchname = $request->matchname;
        $match_b = $matchList->suspend_b;
        $match_f = $matchList->suspend_f;
        $html = '';
        $html_bm_team = "";

        @$team_name = explode(" v ", strtolower($matchname));
        $team1_name = @$team_name[0];
        if (@$team_name[1])
            @$team2_name = $team_name[1];
        else
            $team2_name = '';

        $html_two_team = '';
        $html_two = '';
        $all_bet_model = '';
        $match_detail = Match::where('event_id', $request->event_id)->where('status', 1)->first();

        $match_data = app('App\Http\Controllers\RestApi')->Fancy_and_Bookmaker_DetailCall($eventId, $matchId, $matchtype);

        $team2_bet_total = 0;
        $team1_bet_total = 0;
        $team_draw_bet_total = 0;
        //for fancy
        $login_check = '';
        $getUserCheck = Session::get('playerUser');
        if (!empty($getUserCheck)) {
            $sessionData = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
        }

        if (!empty($sessionData)) {
            if ($min_bet_fancy_limit > 0 && $min_bet_fancy_limit != "" && $max_bet_fancy_limit > 0 && $max_bet_fancy_limit != "")
                $login_check = 'onclick="opnForm(this);"';
        } else {
            $useragent = $_SERVER['HTTP_USER_AGENT'];
            $iPod = stripos($useragent, "iPod");
            $iPad = stripos($useragent, "iPad");
            $iPhone = stripos($useragent, "iPhone");
            $Android = stripos($useragent, "Android");
            $iOS = stripos($useragent, "iOS");

            $DEVICE = ($iPod || $iPad || $iPhone || $Android || $iOS);
            $is_agent = '';
            if (!$DEVICE) {
                $is_agent = 'desktop';
                $login_check = 'data-toggle="modal" data-target="#myLoginModal"';
            } else {
                $is_agent = 'mobile';
                $login_check = 'data-toggle="modal" data-target="#myLoginModalFront"';
            }
        }
        /*$html_two_team.='<tr>
            	<td class="text-color-grey fancybet-block" colspan="7">
                	<div class="dark-blue-bg-1 text-color-white">
                    	<a> <img src="'.asset('asset/front/img/pin-bg.png').' "> <img src="'.asset('asset/front/img/pin-bg-1.png').'" class="hover-img"> </a>
                        Fancy Bet <span id="div_min_bet_fancy_limit" style="display:none">'.$min_bet_fancy_limit.'</span> <span id="div_max_bet_fancy_limit" style="display:none">'.$max_bet_fancy_limit.'</span>
                   	</div>
              	</td>
          	</tr>';*/
        $html_two_team .= '

			<tr class="bets-fancy white-bg">
            	<td colspan="3">
					<div class="minmax-txt minmaxmobile" style="padding-left:0px">
                    	<span>Min</span>
						<span id="div_min_bet_odds_limit" class="fancyMin">' . $min_bet_fancy_limit . '</span>
						<span>Max</span>
						<span id="div_max_bet_odds_limit" class="fancyMax">' . $max_bet_fancy_limit . '</span>
					</div>
				</td>
                <td>No</td>
                <td>Yes</td>
                <td colspan="1"></td>
           	</tr>
			';
        $nat = array();
        $gstatus = array();
        $b = array();
        $l = array();
        $bs = array();
        $ls = array();
        $min = array();
        $max = array();
        $sid = array();
        if (@$match_data['fancy']) {
            foreach ($match_data['fancy'] as $key => $value) {
                $sid_val = '';
                foreach ($value as $key1 => $value1) {
                    if ($key1 == 'sid') {
                        $sid_val = $value1;
                        $sid[] = $value1;
                    }
                    if ($key1 == 'nat')
                        $nat[$sid_val] = $value1;
                    if ($key1 == 'gstatus') {
                        $gstatus[$sid_val] = $value1;
                    }
                    if ($key1 == 'b1')
                        $b[$sid_val] = $value1;
                    if ($key1 == 'l1')
                        $l[$sid_val] = $value1;
                    if ($key1 == 'bs1')
                        $bs[$sid_val] = $value1;
                    if ($key1 == 'ls1')
                        $ls[$sid_val] = $value1;
                    if ($key1 == 'min')
                        $min[$sid_val] = $value1;
                    if ($key1 == 'max')
                        $max[$sid_val] = $value1;
                }
            }

            sort($sid);
            for ($i = 0; $i < sizeof($sid); $i++) {
                $max_val = 0;
                if ($max[$sid[$i]] > 999) {
                    $input = number_format($max[$sid[$i]]);
                    $input_count = substr_count($input, ',');
                    $arr = array(1 => 'K', 'M', 'B', 'T');
                    if (isset($arr[(int)$input_count]))
                        $max_val = substr($input, 0, (-1 * $input_count) * 4) . $arr[(int)$input_count];
                    else
                        $max_val = $input;
                }

                if ($match_f == '0') {
                    $getUserCheck = Session::get('playerUser');
                    if (!empty($getUserCheck)) {
                        $sessionData = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
                    }
                    if (!empty($sessionData)) {
                        $getUserCheckuser = Session::get('playerUser');
                        if (!empty($getUserCheckuser)) {
                            $getUser = User::where('id', $getUserCheckuser->id)->where('check_login', 1)->first();
                        }
                        $userId = $getUser->id;
                        $my_placed_bets = MyBets::where('user_id', $userId)->where('match_id', $eventId)->where('team_name', @$nat[$sid[$i]])->where('bet_type', 'SESSION')->where('isDeleted', 0)->where('result_declare', 0)->orderBy('created_at', 'asc')->get();
                        $abc = sizeof($my_placed_bets);
                        if (sizeof($my_placed_bets) > 0) {
                            $run_arr = array();
                            foreach ($my_placed_bets as $bet) {
                                $down_position = $bet->bet_odds - 1;
                                if (!in_array($down_position, $run_arr)) {
                                    $run_arr[] = $down_position;
                                }
                                $level_position = $bet->bet_odds;
                                if (!in_array($level_position, $run_arr)) {
                                    $run_arr[] = $level_position;
                                }
                                $up_position = $bet->bet_odds + 1;
                                if (!in_array($up_position, $run_arr)) {
                                    $run_arr[] = $up_position;
                                }
                            }
                            array_unique($run_arr);
                            sort($run_arr);

                            $min_val = min($run_arr);
                            $max_val = max($run_arr);

                            $newArr = array();

                            for ($z = $min_val; $z <= $max_val; ++$z) {
                                $new = $z;
                                $newArr[] = $new;
                            }

                            $run_arr = array();
                            $run_arr = $newArr;

                            $bet_chk = '';
                            for ($kk = 0; $kk < sizeof($run_arr); $kk++) {
                                $bet_deduct_amt = 0;
                                $placed_bet_type = '';
                                foreach ($my_placed_bets as $bet) {
                                    if ($bet->bet_side == 'back') {
                                        if ($bet->bet_odds == $run_arr[$kk]) {

                                            $bet_deduct_amt = $bet_deduct_amt + $bet->bet_profit;
                                        } else if ($bet->bet_odds < $run_arr[$kk]) {

                                            $bet_deduct_amt = $bet_deduct_amt + $bet->bet_profit;
                                        } else if ($bet->bet_odds > $run_arr[$kk]) {

                                            $bet_deduct_amt = $bet_deduct_amt + $bet->exposureAmt;
                                        }
                                    } else if ($bet->bet_side == 'lay') {
                                        if ($bet->bet_odds == $run_arr[$kk]) {

                                            $bet_deduct_amt = $bet_deduct_amt - $bet->exposureAmt;
                                        } else if ($bet->bet_odds < $run_arr[$kk]) {

                                            $bet_deduct_amt = $bet_deduct_amt - $bet->exposureAmt;
                                        } else if ($bet->bet_odds > $run_arr[$kk]) {

                                            $bet_deduct_amt = $bet_deduct_amt + $bet->bet_amount;
                                        }
                                    }
                                }
                                if ($final_exposer == "")
                                    $final_exposer = $bet_deduct_amt;
                                else {
                                    if ($final_exposer > $bet_deduct_amt)
                                        $final_exposer = $bet_deduct_amt;
                                }

                                if ($bet_deduct_amt > 0) {
                                    $position .= '<tr>
											<td class="text-center cyan-bg">' . $run_arr[$kk] . '</td>
											<td class="text-right cyan-bg">' . $bet_deduct_amt . '</td>
											</tr>';
                                } else {
                                    $position .= '<tr>
											<td class="text-center pink-bg">' . $run_arr[$kk] . '</td>
											<td class="text-right pink-bg">' . $bet_deduct_amt . '<br>' . $bet_chk . '</td>
											</tr>';
                                }
                            }
                            if ($position != '') {
                                $bet_model = '<div class="modal credit-modal" id="runPosition' . $i . '">
											<div class="modal-dialog">
												<div class="modal-content light-grey-bg-1">
													<div class="modal-header">
														<h4 class="modal-title text-color-blue-1">Run Position</h4>
														<button type="button" class="close modelclose" data-dismiss="modal"><img src="' . asset('asset/front/img/close-icon.png') . '" alt=""></button>
													</div>
													<div class="modal-body white-bg p-3">
														<table class="table table-bordered w-100 fonts-1 mb-0">
															<thead>
																<tr>
																	<th width="50%" class="text-center">Run</th>
																	<th width="50%" class="text-right">Amount</th>
																</tr>
															</thead>
															<tbody> ' . $position . '</tbody>
														</table>
													</div>
												</div>
											</div>
										</div>';
                            }
                        }
                    }

                    $display = '';
                    $cls = '';
                    if ($bet_model == '') {
                        $display = 'style="display:block"';
                    }
                    if ($bet_model != '') {
                        $cls = 'text-color-red';
                        $all_bet_model .= $bet_model;
                    }
                    //end for bet calculation

                    $html_two .= '
						<tr class="fancy-suspend-tr">
						<td colspan="3"></td>
						<td class="fancy-suspend-td" colspan="2">
							<div class="fancy-suspend black-bg-5 text-color-white"><span>SUSPENDED</span></div>
						</td>
						</tr>
						<tr class="white-bg tr_fancy_' . $i . '">
								<td colspan="3"><b>55 ' . $nat[$sid[$i]] . '</b>
									<div>
									<a data-position="' . $i . '" data-team="' . $nat[$sid[$i]] . '" class="openfancymodel_dynamic" data-toggle="modal" data-target="#runPosition' . $i . '">
										<span class="lose ' . $cls . '" ' . $display . ' id="Fancy_Total_Div"><span id="Fancy_Total_' . $i . '">' . abs($final_exposer) . '</span></span>
									</a>
									</div>
								</td>
								<td class="pink-bg back1btn text-center FancyLay td_fancy_lay_' . $i . '" id="td_fancy_lay_' . $i . '" onClick="colorclick(this.id)">
									<a><br> <span>--</span></a></td>
								<td class="lay1btn cyan-bg text-center FancyBack td_fancy_back_' . $i . '" id="td_fancy_back_' . $i . '" onClick="colorclickback(this.id)">
									<a>--<br> <span>--</span></a>
								</td>
								<td class="zeroopa1" colspan="1"> <span></span> <br></td>
							</tr>';
                } else {
                    $placed_bet = '';
                    $position = '';
                    $bet_model = '';
                    $abc = '';
                    $final_exposer = '';
                    if ($gstatus[$sid[$i]] != 'Ball Running' && $gstatus[$sid[$i]] != 'Suspended' && $l[$sid[$i]] != 0 && round($b[$sid[$i]]) != 0) {
                        if ($l[$sid[$i]] != 0 && round($b[$sid[$i]]) != 0 && $l[$sid[$i]] != '' && $b[$sid[$i]] != '') {
                            //bet calculation

                            $getUserCheck = Session::get('playerUser');
                            if (!empty($getUserCheck)) {
                                $sessionData = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
                            }
                            if (!empty($sessionData)) {
                                $getUserCheckuser = Session::get('playerUser');
                                if (!empty($getUserCheckuser)) {
                                    $getUser = User::where('id', $getUserCheckuser->id)->where('check_login', 1)->first();
                                }
                                $userId = $getUser->id;
                                $my_placed_bets = MyBets::where('user_id', $userId)->where('match_id', $eventId)->where('team_name', @$nat[$sid[$i]])->where('bet_type', 'SESSION')->where('isDeleted', 0)->where('result_declare', 0)->orderBy('created_at', 'asc')->get();
                                $abc = sizeof($my_placed_bets);
                                if (sizeof($my_placed_bets) > 0) {
                                    $run_arr = array();
                                    foreach ($my_placed_bets as $bet) {
                                        $down_position = $bet->bet_odds - 1;
                                        if (!in_array($down_position, $run_arr)) {
                                            $run_arr[] = $down_position;
                                        }
                                        $level_position = $bet->bet_odds;
                                        if (!in_array($level_position, $run_arr)) {
                                            $run_arr[] = $level_position;
                                        }
                                        $up_position = $bet->bet_odds + 1;
                                        if (!in_array($up_position, $run_arr)) {
                                            $run_arr[] = $up_position;
                                        }
                                    }
                                    array_unique($run_arr);
                                    sort($run_arr);

                                    $min_val = min($run_arr);
                                    $max_val = max($run_arr);

                                    $newArr = array();

                                    for ($z = $min_val; $z <= $max_val; ++$z) {
                                        $new = $z;
                                        $newArr[] = $new;
                                    }

                                    $run_arr = array();
                                    $run_arr = $newArr;

                                    $bet_chk = '';
                                    for ($kk = 0; $kk < sizeof($run_arr); $kk++) {
                                        $bet_deduct_amt = 0;
                                        $placed_bet_type = '';
                                        foreach ($my_placed_bets as $bet) {
                                            if ($bet->bet_side == 'back') {
                                                if ($bet->bet_odds == $run_arr[$kk]) {

                                                    $bet_deduct_amt = $bet_deduct_amt + $bet->bet_profit;

                                                } else if ($bet->bet_odds < $run_arr[$kk]) {

                                                    $bet_deduct_amt = $bet_deduct_amt + $bet->bet_profit;
                                                } else if ($bet->bet_odds > $run_arr[$kk]) {

                                                    $bet_deduct_amt = $bet_deduct_amt - $bet->exposureAmt;
                                                }
                                            } else if ($bet->bet_side == 'lay') {
                                                if ($bet->bet_odds == $run_arr[$kk]) {

                                                    $bet_deduct_amt = $bet_deduct_amt - $bet->exposureAmt;
                                                } else if ($bet->bet_odds < $run_arr[$kk]) {

                                                    $bet_deduct_amt = $bet_deduct_amt - $bet->exposureAmt;
                                                } else if ($bet->bet_odds > $run_arr[$kk]) {

                                                    $bet_deduct_amt = $bet_deduct_amt + $bet->bet_amount;
                                                }
                                            }
                                        }
                                        if ($final_exposer == "")
                                            $final_exposer = $bet_deduct_amt;
                                        else {
                                            if ($final_exposer > $bet_deduct_amt)
                                                $final_exposer = $bet_deduct_amt;
                                        }

                                        if ($bet_deduct_amt > 0) {
                                            $position .= '<tr>
												<td class="text-center cyan-bg">' . $run_arr[$kk] . '</td>
												<td class="text-right cyan-bg">' . $bet_deduct_amt . '<br>' . $bet_chk . '</td>
												</tr>';
                                        } else {
                                            $position .= '<tr>
												<td class="text-center pink-bg">' . $run_arr[$kk] . '</td>
												<td class="text-right pink-bg">' . $bet_deduct_amt . '<br>' . $bet_chk . '</td>
												</tr>';
                                        }
                                    }
                                    if ($position != '') {
                                        $bet_model = '<div class="modal credit-modal" id="runPosition' . $i . '">
												<div class="modal-dialog">
													<div class="modal-content light-grey-bg-1">
														<div class="modal-header">
															<h4 class="modal-title text-color-blue-1">Run Position</h4>
															<button type="button" class="close modelclose" data-dismiss="modal"><img src="' . asset('asset/front/img/close-icon.png') . '" alt=""></button>
														</div>
														<div class="modal-body white-bg p-3">
															<table class="table table-bordered w-100 fonts-1 mb-0">
																<thead>
																	<tr>
																		<th width="50%" class="text-center">Run</th>
																		<th width="50%" class="text-right">Amount</th>
																	</tr>
																</thead>
																<tbody> ' . $position . '</tbody>
															</table>
														</div>
													</div>
												</div>
											</div>';
                                    }
                                }
                            }

                            $display = '';
                            $cls = '';
                            if ($bet_model == '') {
                                $display = 'style="display:block"';
                            }
                            if ($bet_model != '') {
                                $cls = 'text-color-red';
                                $all_bet_model .= $bet_model;
                            }
                            //end for bet calculation

                            $html_two .= '<tr class="white-bg tr_fancy_' . $i . '">
									<td colspan="3"><b>66 ' . $nat[$sid[$i]] . '</b>
										<div>
											<a data-position="' . $i . '" data-team="' . $nat[$sid[$i]] . '" class="openfancymodel_dynamic" data-toggle="modal" data-target="#runPosition' . $i . '">
												<span class="lose ' . $cls . '" ' . $display . ' id="Fancy_Total_Div"><span id="Fancy_Total_' . $i . '">' . abs($final_exposer) . '</span></span>
											</a>
										</div>

									</td>
									<td class="pink-bg back1btn text-center FancyLay td_fancy_lay_' . $i . '" data-team="' . $nat[$sid[$i]] . '" id="td_fancy_lay_' . $i . 'onClick="colorclick(this.id)">
										<a data-bettype="SESSION" data-position="' . $i . '" data-team="' . $nat[$sid[$i]] . '" ' . $login_check . ' data-cls="pink-bg" data-volume="' . round($ls[$sid[$i]]) . '" data-val="' . round($l[$sid[$i]]) . '">' . round($l[$sid[$i]]) . '<br> <span>' . round($ls[$sid[$i]]) . '</span></a></td>
									<td class="lay1btn cyan-bg text-center FancyBack td_fancy_back_' . $i . '" data-team="' . $nat[$sid[$i]] . '" id="td_fancy_back_' . $i . '" onClick="colorclickback(this.id)">
										<a data-bettype="SESSION" data-position="' . $i . '" data-team="' . $nat[$sid[$i]] . '" ' . $login_check . ' data-cls="cyan-bg" data-volume="' . round($bs[$sid[$i]]) . '" data-val="' . round($b[$sid[$i]]) . '">' . round($b[$sid[$i]]) . '<br> <span>' . round($bs[$sid[$i]]) . '</span></a>
									</td>
									<td class="zeroopa1" colspan="1"> <span>Min/Max</span> <br> ' . $match_detail['min_fancy_limit'] . ' / ' . $match_detail['max_fancy_limit'] . '</td>
								</tr>
								<tr class="mobileBack tr_team' . $i . '_fancy mobile_bet_model_div" id="mobile_tr">
									<td colspan="6" class="tr_team' . $i . '_fancy_td_mobile mobile_tr_common_class"></td>
								</tr>
								';
                        } else {
                            //for bet calculation
                            $getUserCheck = Session::get('playerUser');
                            if (!empty($getUserCheck)) {
                                $sessionData = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
                            }
                            if (!empty($sessionData)) {
                                $getUserCheckuser = Session::get('playerUser');
                                if (!empty($getUserCheckuser)) {
                                    $getUser = User::where('id', $getUserCheckuser->id)->where('check_login', 1)->first();
                                }
                                $userId = $getUser->id;
                                $my_placed_bets = MyBets::where('user_id', $userId)->where('match_id', $eventId)->where('team_name', @$nat[$sid[$i]])->where('bet_type', 'SESSION')->where('isDeleted', 0)->where('result_declare', 0)->orderBy('created_at', 'asc')->get();
                                $abc = sizeof($my_placed_bets);
                                if (sizeof($my_placed_bets) > 0) {
                                    $run_arr = array();
                                    foreach ($my_placed_bets as $bet) {
                                        $down_position = $bet->bet_odds - 1;
                                        if (!in_array($down_position, $run_arr)) {
                                            $run_arr[] = $down_position;
                                        }
                                        $level_position = $bet->bet_odds;
                                        if (!in_array($level_position, $run_arr)) {
                                            $run_arr[] = $level_position;
                                        }
                                        $up_position = $bet->bet_odds + 1;
                                        if (!in_array($up_position, $run_arr)) {
                                            $run_arr[] = $up_position;
                                        }
                                    }
                                    array_unique($run_arr);
                                    sort($run_arr);

                                    $min_val = min($run_arr);
                                    $max_val = max($run_arr);

                                    $newArr = array();

                                    for ($z = $min_val; $z <= $max_val; ++$z) {
                                        $new = $z;
                                        $newArr[] = $new;
                                    }

                                    $run_arr = array();
                                    $run_arr = $newArr;

                                    $bet_chk = '';
                                    for ($kk = 0; $kk < sizeof($run_arr); $kk++) {
                                        $bet_deduct_amt = 0;
                                        $placed_bet_type = '';
                                        foreach ($my_placed_bets as $bet) {
                                            if ($bet->bet_side == 'back') {
                                                if ($bet->bet_odds == $run_arr[$kk]) {

                                                    $bet_deduct_amt = $bet_deduct_amt + $bet->bet_profit;

                                                } else if ($bet->bet_odds < $run_arr[$kk]) {

                                                    $bet_deduct_amt = $bet_deduct_amt + $bet->bet_profit;
                                                } else if ($bet->bet_odds > $run_arr[$kk]) {

                                                    $bet_deduct_amt = $bet_deduct_amt - $bet->exposureAmt;
                                                }
                                            } else if ($bet->bet_side == 'lay') {
                                                if ($bet->bet_odds == $run_arr[$kk]) {

                                                    $bet_deduct_amt = $bet_deduct_amt - $bet->exposureAmt;
                                                } else if ($bet->bet_odds < $run_arr[$kk]) {

                                                    $bet_deduct_amt = $bet_deduct_amt - $bet->exposureAmt;
                                                } else if ($bet->bet_odds > $run_arr[$kk]) {

                                                    $bet_deduct_amt = $bet_deduct_amt + $bet->bet_amount;
                                                }
                                            }
                                        }
                                        if ($final_exposer == "")
                                            $final_exposer = $bet_deduct_amt;
                                        else {
                                            if ($final_exposer > $bet_deduct_amt)
                                                $final_exposer = $bet_deduct_amt;
                                        }

                                        if ($bet_deduct_amt > 0) {
                                            $position .= '<tr>
												<td class="text-center cyan-bg">' . $run_arr[$kk] . '</td>
												<td class="text-right cyan-bg">' . $bet_deduct_amt . '</td>
												</tr>';
                                        } else {
                                            $position .= '<tr>
												<td class="text-center pink-bg">' . $run_arr[$kk] . '</td>
												<td class="text-right pink-bg">' . $bet_deduct_amt . '<br>' . $bet_chk . '</td>
												</tr>';
                                        }
                                    }
                                    if ($position != '') {
                                        $bet_model = '<div class="modal credit-modal" id="runPosition' . $i . '">
												<div class="modal-dialog">
													<div class="modal-content light-grey-bg-1">
														<div class="modal-header">
															<h4 class="modal-title text-color-blue-1">Run Position</h4>
															<button type="button" class="close modelclose" data-dismiss="modal"><img src="' . asset('asset/front/img/close-icon.png') . '" alt=""></button>
														</div>
														<div class="modal-body white-bg p-3">
															<table class="table table-bordered w-100 fonts-1 mb-0">
																<thead>
																	<tr>
																		<th width="50%" class="text-center">Run' . $abc . '</th>
																		<th width="50%" class="text-right">Amount</th>
																	</tr>
																</thead>
																<tbody> ' . $position . '</tbody>
															</table>
														</div>
													</div>
												</div>
											</div>';
                                    }
                                }
                            }

                            $display = '';
                            $cls = '';
                            if ($bet_model == '') {
                                $display = 'style="display:block"';
                            }
                            if ($bet_model != '') {
                                $cls = 'text-color-red';
                                $all_bet_model .= $bet_model;
                            }
                            //end for bet calculation
                            $html_two .= '<tr class="fancy-suspend-tr-1 ">
								<td colspan="3"></td>
								<td class="fancy-suspend-td-1" colspan="2">
									<div class="fancy-suspend-1 black-bg-5 text-color-white"><span>SUSPENDED</span></div>
								</td>
								</tr>
								<tr class="white-bg tr_fancy_' . $i . '">
									<td colspan="3"><b>88 ' . $nat[$sid[$i]] . ' </b></td>
									<td class="pink-bg  back1btn text-center1111 td_fancy_lay_' . $i . '"onClick="colorclick(this.id)"><a> <br> <span> </span></a></td>
									<td class="cyan-bg lay1btn  text-center td_fancy_back_' . $i . '"onClick="colorclickback(this.id)"><a> <br> <span> </span></a></td>
									<td class="zeroopa1" colspan="1"> <span>Min/Max</span> <br> ' . $match_detail['min_fancy_limit'] . ' / ' . $match_detail['max_fancy_limit'] . ' </td>
								</tr>
								<tr class="mobileBack tr_team' . $i . '_fancy mobile_bet_model_div" id="mobile_tr">
									<td colspan="7" class="tr_team' . $i . '_fancy_td_mobile mobile_tr_common_class"></td>
								</tr>
								';
                        }
                    } else {
                        //for bet calculation
                        $getUserCheck = Session::get('playerUser');
                        if (!empty($getUserCheck)) {
                            $sessionData = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
                        }
                        if (!empty($sessionData)) {
                            $getUserCheckuser = Session::get('playerUser');
                            if (!empty($getUserCheckuser)) {
                                $getUser = User::where('id', $getUserCheckuser->id)->where('check_login', 1)->first();
                            }
                            $userId = $getUser->id;
                            $my_placed_bets = MyBets::where('user_id', $userId)->where('match_id', $eventId)->where('team_name', @$nat[$sid[$i]])->where('bet_type', 'SESSION')->where('isDeleted', 0)->where('result_declare', 0)->orderBy('created_at', 'asc')->get();
                            $abc = sizeof($my_placed_bets);
                            if (sizeof($my_placed_bets) > 0) {
                                $run_arr = array();
                                foreach ($my_placed_bets as $bet) {
                                    $down_position = $bet->bet_odds - 1;
                                    if (!in_array($down_position, $run_arr)) {
                                        $run_arr[] = $down_position;
                                    }
                                    $level_position = $bet->bet_odds;
                                    if (!in_array($level_position, $run_arr)) {
                                        $run_arr[] = $level_position;
                                    }
                                    $up_position = $bet->bet_odds + 1;
                                    if (!in_array($up_position, $run_arr)) {
                                        $run_arr[] = $up_position;
                                    }
                                }
                                array_unique($run_arr);
                                sort($run_arr);

                                $min_val = min($run_arr);
                                $max_val = max($run_arr);

                                $newArr = array();

                                for ($z = $min_val; $z <= $max_val; ++$z) {
                                    $new = $z;
                                    $newArr[] = $new;
                                }

                                $run_arr = array();
                                $run_arr = $newArr;

                                $bet_chk = '';
                                for ($kk = 0; $kk < sizeof($run_arr); $kk++) {
                                    $bet_deduct_amt = 0;
                                    $placed_bet_type = '';
                                    foreach ($my_placed_bets as $bet) {
                                        if ($bet->bet_side == 'back') {
                                            if ($bet->bet_odds == $run_arr[$kk]) {

                                                $bet_deduct_amt = $bet_deduct_amt + $bet->bet_profit;
                                            } else if ($bet->bet_odds < $run_arr[$kk]) {

                                                $bet_deduct_amt = $bet_deduct_amt + $bet->bet_profit;
                                            } else if ($bet->bet_odds > $run_arr[$kk]) {

                                                $bet_deduct_amt = $bet_deduct_amt - $bet->exposureAmt;
                                            }
                                        } else if ($bet->bet_side == 'lay') {
                                            if ($bet->bet_odds == $run_arr[$kk]) {

                                                $bet_deduct_amt = $bet_deduct_amt - $bet->exposureAmt;
                                            } else if ($bet->bet_odds < $run_arr[$kk]) {

                                                $bet_deduct_amt = $bet_deduct_amt - $bet->exposureAmt;
                                            } else if ($bet->bet_odds > $run_arr[$kk]) {

                                                $bet_deduct_amt = $bet_deduct_amt + $bet->bet_amount;
                                            }
                                        }
                                    }
                                    if ($final_exposer == "")
                                        $final_exposer = $bet_deduct_amt;
                                    else {
                                        if ($final_exposer > $bet_deduct_amt)
                                            $final_exposer = $bet_deduct_amt;
                                    }

                                    if ($bet_deduct_amt > 0) {
                                        $position .= '<tr>
												<td class="text-center cyan-bg">' . $run_arr[$kk] . '</td>
												<td class="text-right cyan-bg">' . $bet_deduct_amt . '</td>
												</tr>';
                                    } else {
                                        $position .= '<tr>
												<td class="text-center pink-bg">' . $run_arr[$kk] . '</td>
												<td class="text-right pink-bg">' . $bet_deduct_amt . '<br>' . $bet_chk . '</td>
												</tr>';
                                    }
                                }
                                if ($position != '') {
                                    $bet_model = '<div class="modal credit-modal" id="runPosition' . $i . '">
												<div class="modal-dialog">
													<div class="modal-content light-grey-bg-1">
														<div class="modal-header">
															<h4 class="modal-title text-color-blue-1">Run Position</h4>
															<button type="button" class="close modelclose" data-dismiss="modal"><img src="' . asset('asset/front/img/close-icon.png') . '" alt=""></button>
														</div>
														<div class="modal-body white-bg p-3">
															<table class="table table-bordered w-100 fonts-1 mb-0">
																<thead>
																	<tr>
																		<th width="50%" class="text-center">Run' . $abc . '</th>
																		<th width="50%" class="text-right">Amount</th>
																	</tr>
																</thead>
																<tbody> ' . $position . '</tbody>
															</table>
														</div>
													</div>
												</div>
											</div>';
                                }
                            }
                        }

                        $display = '';
                        $cls = '';
                        if ($bet_model == '') {
                            $display = 'style="display:block"';
                        }
                        if ($bet_model != '') {
                            $cls = 'text-color-red';
                            $all_bet_model .= $bet_model;
                        }
                        //end for bet calculation
                        $html_two .= '<tr class="fancy-suspend-tr-1">
								<td colspan="3"></td>
								<td class="fancy-suspend-td-1" colspan="2">
									<div class="fancy-suspend-1 black-bg-5 text-color-white"><span>' . strtoupper($gstatus[$sid[$i]]) . '</span></div>
								</td>
							</tr>
							<tr class="white-bg tr_fancy_' . $i . '">
								<td colspan="3"><b>99 ' . $nat[$sid[$i]] . ' </b>
									<div>
										<a data-position="' . $i . '" data-team="' . $nat[$sid[$i]] . '" class="openfancymodel_dynamic" data-toggle="modal" data-target="#runPosition' . $i . '">
											<span class="lose ' . $cls . '" ' . $display . ' id="Fancy_Total_Div"><span id="Fancy_Total_' . $i . '">' . abs($final_exposer) . '</span></span>
										</a>
									</div>
								</td>
								<td class="pink-bg  back1btn text-center td_fancy_lay_' . $i . '" id="td_fancy_lay_' . $i . '" onClick="colorclick(this.id)"><a> <br> <span> </span></a></td>
								<td class="cyan-bg lay1btn  text-center td_fancy_back_' . $i . '" id="td_fancy_back_' . $i . '" onClick="colorclickback(this.id)"><a> <br> <span> </span></a></td>
								<td class="zeroopa1" colspan="1"> <span>Min/Max</span> <br> ' . $matchList['min_fancy_limit'] . ' / ' . $matchList['max_fancy_limit'] . ' </td>
							</tr>
							<tr class="mobileBack tr_team' . $i . '_fancy mobile_bet_model_div" id="mobile_tr">
								<td colspan="7" class="tr_team' . $i . '_fancy_td_mobile mobile_tr_common_class"></td>
							</tr>
							';
                    }
                } // end suspended if
            }
            if ($html_two != '')
                $html_two = $html_two_team . $html_two . '<input type="hidden" name="hid_fancy" id="hid_fancy" value="' . $i . '">';
        }
        return $html_two . '#######' . $all_bet_model;
    }

    public function matchCallFor_BM($matchId, Request $request)
    {
        $matchtype = $request->matchtype;
        $match_id = $request->match_id;
        $sport = Sport::where('sId', $matchtype)->first();
        $matchList = Match::where('id', $match_id)->where('status', 1)->first();

        $min_bet_odds_limit = $matchList->min_bookmaker_limit;
        $max_bet_odds_limit = $matchList->max_bookmaker_limit;

        $min_bet_fancy_limit = $matchList->min_fancy_limit;
        $max_bet_fancy_limit = $matchList->max_fancy_limit;

        // $matchtype=$sport->id;
        $eventId = $request->event_id;
        $matchname = $matchList->match_name;
        $match_b = $matchList->suspend_b;
        $match_f = $matchList->suspend_f;
        $html = '';
        $html_bm_team = "";

        @$team_name = explode(" v ", strtolower($matchname));
        //print_r($team_name);
        $team1_name = @$team_name[0];
        if (@$team_name[1])
            @$team2_name = $team_name[1];
        else
            $team2_name = '';

        $match_detail = Match::where('event_id', $request->event_id)->where('status', 1)->first();

        $match_data = app('App\Http\Controllers\RestApi')->getSingleMatchData($eventId, $matchId, $matchtype);

        $team2_bet_total = 0;
        $team1_bet_total = 0;
        $team_draw_bet_total = 0;

        //for bm
        $getUserCheck = Session::get('playerUser');
        if (!empty($getUserCheck)) {
            $sessionData = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
        }
        if (!empty($sessionData)) {
            $getUserCheckuser = Session::get('playerUser');
            if (!empty($getUserCheckuser)) {
                $getUser = User::where('id', $getUserCheckuser->id)->where('check_login', 1)->first();
            }
            $userId = $getUser->id;
            $my_placed_bets = MyBets::where('user_id', $userId)->where('match_id', $eventId)->where('bet_type', 'BOOKMAKER')->where('isDeleted', 0)->where('result_declare', 0)->orderby('id', 'DESC')->get();

            if (sizeof($my_placed_bets) > 0) {
                foreach ($my_placed_bets as $bet) {
                    $abc = json_decode($bet->extra, true);
                    if (count($abc) >= 2) {
                        if (array_key_exists("teamname1", $abc) && array_key_exists("teamname2", $abc)) {
                            //bet on draw
                            if ($bet->bet_side == 'back') {
                                $team1_bet_total = $team1_bet_total - $bet->exposureAmt;
                                if (count($abc) >= 2) {
                                    $team_draw_bet_total = $team_draw_bet_total + $bet->bet_profit;
                                }
                                $team2_bet_total = $team2_bet_total - $bet->exposureAmt;
                            }
                            if ($bet->bet_side == 'lay') {
                                $team1_bet_total = $team1_bet_total + $bet->bet_amount;
                                if (count($abc) >= 2) {
                                    $team_draw_bet_total = $team_draw_bet_total - $bet->bet_profit;
                                }
                                $team2_bet_total = $team2_bet_total + $bet->bet_amount;
                            }
                        } else if (array_key_exists("teamname3", $abc) && array_key_exists("teamname2", $abc)) {
                            //bet on team1
                            if ($bet->bet_side == 'back') {
                                $team1_bet_total = $team1_bet_total + $bet->bet_profit;
                                if (count($abc) >= 2) {
                                    $team_draw_bet_total = $team_draw_bet_total - $bet->exposureAmt;
                                }
                                $team2_bet_total = $team2_bet_total - $bet->exposureAmt;
                            }
                            if ($bet->bet_side == 'lay') {
                                $team1_bet_total = $team1_bet_total - $bet->bet_profit;
                                if (count($abc) >= 2) {
                                    $team_draw_bet_total = $team_draw_bet_total + $bet->bet_amount;
                                }
                                $team2_bet_total = $team2_bet_total + $bet->bet_amount;
                            }
                        } else if (array_key_exists("teamname3", $abc) && array_key_exists("teamname1", $abc)) {
                            //bet on team2
                            if ($bet->bet_side == 'back') {
                                $team2_bet_total = $team2_bet_total + $bet->bet_profit;
                                if (count($abc) >= 2) {
                                    $team_draw_bet_total = $team_draw_bet_total - $bet->exposureAmt;
                                }
                                $team1_bet_total = $team1_bet_total - $bet->exposureAmt;
                            }
                            if ($bet->bet_side == 'lay') {
                                $team2_bet_total = $team2_bet_total - $bet->bet_profit;
                                if (count($abc) >= 2) {
                                    $team_draw_bet_total = $team_draw_bet_total + $bet->bet_amount;
                                }
                                $team1_bet_total = $team1_bet_total + $bet->bet_amount;
                            }
                        }
                    } else if (count($abc) == 1) {
                        if (array_key_exists("teamname1", $abc)) {
                            //bet on team2
                            if ($bet->bet_side == 'back') {
                                $team2_bet_total = $team2_bet_total + $bet->bet_profit;
                                $team1_bet_total = $team1_bet_total - $bet->exposureAmt;
                            }
                            if ($bet->bet_side == 'lay') {
                                $team2_bet_total = $team2_bet_total - $bet->bet_profit;
                                $team1_bet_total = $team1_bet_total + $bet->bet_amount;
                            }
                        } else {
                            //bet on team1
                            if ($bet->bet_side == 'back') {
                                $team1_bet_total = $team1_bet_total + $bet->bet_profit;
                                $team2_bet_total = $team2_bet_total - $bet->exposureAmt;
                            }
                            if ($bet->bet_side == 'lay') {
                                $team1_bet_total = $team1_bet_total - $bet->bet_profit;
                                $team2_bet_total = $team2_bet_total + $bet->bet_amount;
                            }
                        }
                    }
                }
            }
        }

        $html_two = '';
        $html_two_team = "";
        $html = '';

        $login_check = '';
        $getUserCheck = Session::get('playerUser');
        if (!empty($getUserCheck)) {
            $sessionData = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
        }
        if (!empty($sessionData)) {
            if ($min_bet_odds_limit > 0 && $min_bet_odds_limit != "" && $max_bet_odds_limit > 0 && $max_bet_odds_limit != "")
                $login_check = 'onclick="opnForm(this);"';
        } else {
            $useragent = $_SERVER['HTTP_USER_AGENT'];
            $iPod = stripos($useragent, "iPod");
            $iPad = stripos($useragent, "iPad");
            $iPhone = stripos($useragent, "iPhone");
            $Android = stripos($useragent, "Android");
            $iOS = stripos($useragent, "iOS");

            $DEVICE = ($iPod || $iPad || $iPhone || $Android || $iOS);
            $is_agent = '';
            if (!$DEVICE) {
                $is_agent = 'desktop';
                $login_check = 'data-toggle="modal" data-target="#myLoginModal"';
            } else {
                $is_agent = 'mobile';
                $login_check = 'data-toggle="modal" data-target="#myLoginModalFront"';
            }
        }

        if (!empty($match_data) && $match_data != 0) {

            //for bookmaker
            $html_bm_team .= '
			<tr>
                <td class="text-color-grey fancybet-block" colspan="7">
                    <div class="dark-blue-bg-1 text-color-white">
                        <a> <img src="' . asset('asset/front/img/pin-bg.png') . '"> <img src="' . asset('asset/front/img/pin-bg-1.png') . '" class="hover-img"> </a>
                        Bookmaker Market <span class="zeroopa">| Zero Commission</span>
                    </div>
                    <div class="fancy_info text-color-white">
                        <span class="light-grey-bg-5 text-color-blue-1">Min</span> <span id="div_min_bet_bm_limit" class="bookmakerMin">' . $match_detail['min_bookmaker_limit'] . '</span>
                        <span class="light-grey-bg-5 text-color-blue-1">Max</span> <span id="div_max_bet_bm_limit" class="bookmakerMax">' . $match_detail['max_bookmaker_limit'] . '</span>
                    </div>
                </td>
            </tr>
			<tr class="bets-fancy white-bg d-none d-lg-table-row">
				<td colspan="3" style="width:170px">
					<div class="minmax-txt minmaxmobile">
                    	<span>Min</span>
						<span id="div_min_bet_odds_limit" class="bookmakerMin">' . $min_bet_odds_limit . '</span>
						<span>Max</span>
						<span id="div_max_bet_odds_limit" class="bookmakerMax">' . $max_bet_odds_limit . '</span>
					</div>
				</td>
				<td>
					<a class="backall">
						<img src="' . asset('asset/front/img/bluebg1.png') . '">
						<span>Back</span>
					</a>
				</td>
				<td>
					<a class="layall">
						<img src="' . asset('asset/front/img/pinkbg1.png') . '">
						<span>Lay</span>
					</a>
				</td>
				<td colspan="2"></td>
			</tr>

            <tr class="bets-fancy white-bg d-lg-none">
				<td style="width:170px">
					<div class="minmax-txt minmaxmobile">
                    	<span>Min</span>
						<span id="div_min_bet_odds_limit" class="bookmakerMin">' . $min_bet_odds_limit . '</span>
						<span>Max</span>
						<span id="div_max_bet_odds_limit" class="bookmakerMax">' . $max_bet_odds_limit . '</span>
					</div>
				</td>
				<td>
					<a class="backall">
						<img src="' . asset('asset/front/img/bluebg1.png') . '" style="width:100%;height: 25px;">
						<span>
						Back</span>
					</a>
				</td>
				<td>
					<a class="layall">
						<img src="' . asset('asset/front/img/pinkbg1.png') . '" style="width:100%;height: 25px;">
						<span>Lay</span>
					</a>
				</td>
				<td colspan="2"></td>
			</tr>';
            /*<tr class="bets-fancy white-bg">
				<td colspan="3" style="width:170px"></td>
				<td class="text-right">Back</td>
				<td class="text-left">Lay</td>
				<td colspan="2"></td>
			</tr>';*/

            $team_name_array = array();
            $team_name_array[] = @$match_data['t2'][0]['bm1']['0']['nat'];
            $team_name_array[] = @$match_data['t2'][0]['bm1']['1']['nat'];
            $team_name_array[] = @$match_data['t2'][0]['bm1']['2']['nat'];

            $team1_name = $arry_position = array_search(ucwords($team1_name), $team_name_array);
            $team2_name = $arry_position = array_search(ucwords($team2_name), $team_name_array);
            $team3_name = 0;

            if ($team1_name == 0 && $team2_name == 1)
                $team3_name = 2;
            else if ($team1_name == 1 && $team2_name == 0)
                $team3_name = 2;
            else if ($team1_name == 2 && $team2_name == 1)
                $team3_name = 0;
            else if ($team1_name == 1 && $team2_name == 2)
                $team3_name = 0;
            else if ($team1_name == 0 && $team2_name == 2)
                $team3_name = 1;
            else if ($team1_name == 2 && $team2_name == 0)
                $team3_name = 1;


            if ($team1_name != '' || $team2_name != '') {
                if ($match_b == '0') {

                    $html .= '<tr class="fancy-suspend-tr team1_bm_fancy">
						<td></td>
						<td class="fancy-suspend-td" colspan="6">
							<div class="fancy-suspend black-bg-5 text-color-white"><span>SUSPENDED</span></div>
						</td>
					</tr>
                    <tr class="white-bg tr_bm_team1">
								<td class="padding3">' . @$match_data['t2'][0]['bm1'][$team1_name]['nat'] . '<br>
								<div>
									<span class="lose" id="team1_betBM_count_old">(<span id="team1_BM_total">' . round($team1_bet_total, 2) . '</span>)</span>
									<span class="tolose text-color-red" style="display:none" id="team1_betBM_count_new">(6.7)</span>
								</div>
								</td>
								<td class="spark td_team1_bm_back_2">
									<div class="back-gradient text-color-black">
										<div id="back_3" class="BmBack light-blue-bg-2" data-team="team' . @$match_data['t2'][0]['bm1'][$team1_name]['sectionId'] . '">
											<a data-cls="cyan-bg" ' . $login_check . ' data-position="2" data-val="' . round(@$match_data['t2'][0]['bm1'][$team1_name]['b3']) . '">' . round(@$match_data['t2'][0]['bm1'][$team1_name]['b3'], 2) . '</a>
										</div>
									</div>
								</td>
                                <td class="spark td_team1_bm_back_1">
									<div class="back-gradient text-color-black">
										<div id="back_2" class="BmBack light-blue-bg-3" data-team="team' . @$match_data['t2'][0]['bm1'][$team1_name]['sectionId'] . '">
											<a data-cls="cyan-bg" ' . $login_check . ' data-position="1" data-val="' . round(@$match_data['t2'][0]['bm1'][$team1_name]['b2'], 2) . '">' . round(@$match_data['t2'][0]['bm1'][$team1_name]['b2'], 2) . '</a>
										</div>
									</div>
								</td>
                                <td class="spark td_team1_bm_back_0">
									<div class="back-gradient text-color-black">
										<div id="back_1" class="BmBack" data-team="team' . @$match_data['t2'][0]['bm1'][$team1_name]['sectionId'] . '">
											<a data-cls="cyan-bg" class="cyan-bg" ' . $login_check . ' data-position="0" data-val="' . round(@$match_data['t2'][0]['bm1'][$team1_name]['b1'], 2) . '">' . round(@$match_data['t2'][0]['bm1'][$team1_name]['b1'], 2) . '</a>
										</div>
									</div>
								</td>

								<td class="sparkLay td_team1_bm_lay_0">
									<div class="lay-gradient text-color-black">
										<div id="lay_1"  class="BmLay pink-bg" data-team="team' . @$match_data['t2'][0]['bm1'][$team1_name]['sectionId'] . '">
											<a data-cls="pink-bg" ' . $login_check . ' data-position="0" data-val="' . round(@$match_data['t2'][0]['bm1'][$team1_name]['l1'], 2) . '">' . @round($match_data['t2'][0]['bm1'][$team1_name]['l1'], 2) . '</a></div>
									</div>
								</td>
                                <td class="sparkLay td_team1_bm_lay_1">
									<div class="lay-gradient text-color-black">

										<div id="lay_2" class="BmLay light-pink-bg-2" data-team="team' . @$match_data['t2'][0]['bm1'][$team1_name]['sectionId'] . '">
											<a data-cls="pink-bg" ' . $login_check . ' data-position="1" data-val="' . round(@$match_data['t2'][0]['bm1'][$team1_name]['l2'], 2) . '">' . round(@$match_data['t2'][0]['bm1'][$team1_name]['l2'], 2) . '</a>
										</div>

									</div>
								</td>
                                <td class="sparkLay td_team1_bm_lay_2">
									<div class="lay-gradient text-color-black
										<div id="lay_3" class="BmLay light-pink-bg-3" data-team="team' . @$match_data['t2'][0]['bm1'][$team1_name]['sectionId'] . '">
											<a data-cls="pink-bg" ' . $login_check . ' data-position="2" data-val="' . round(@$match_data['t2'][0]['bm1'][$team1_name]['l3'], 2) . '">' . round(@$match_data['t2'][0]['bm1'][$team1_name]['l3'], 2) . '</a>
										</div>
									</div>
								</td>
							</tr>
							<tr class="fancy-suspend-tr team2_bm_fancy">
						<td></td>
						<td class="fancy-suspend-td" colspan="6">
							<div class="fancy-suspend black-bg-5 text-color-white"><span>SUSPENDED</span></div>
						</td>
					</tr>
							<tr class="white-bg tr_bm_team2">
								<td class="padding3">' . @$match_data['t2'][0]['bm1'][$team1_name]['nat'] . '<br>
								<div>
									<span class="lose" id="team1_betBM_count_old">(<span id="team1_BM_total">' . round($team1_bet_total, 2) . '</span>)</span>
									<span class="tolose text-color-red" style="display:none" id="team1_betBM_count_new">(6.7)</span>
								</div>
								</td>
								<td class="spark td_team2_bm_back_2">
									<div class="back-gradient text-color-black">
										<div id="back_3" class="BmBack light-blue-bg-2" data-team="team' . @$match_data['t2'][0]['bm1'][$team1_name]['sectionId'] . '">
											<a data-cls="cyan-bg" ' . $login_check . ' data-position="2" data-val="' . round(@$match_data['t2'][0]['bm1'][$team1_name]['b3'], 2) . '">' . round(@$match_data['t2'][0]['bm1'][$team1_name]['b3'], 2) . '</a>
										</div>
									</div>
								</td>
                                <td class="spark td_team2_bm_back_1">
									<div class="back-gradient text-color-black">

										<div id="back_2" class="BmBack light-blue-bg-3" data-team="team' . @$match_data['t2'][0]['bm1'][$team1_name]['sectionId'] . '">
											<a data-cls="cyan-bg" ' . $login_check . ' data-position="1"  data-val="' . round(@$match_data['t2'][0]['bm1'][$team1_name]['b2'], 2) . '">' . round(@$match_data['t2'][0]['bm1'][$team1_name]['b2'], 2) . '</a>
										</div>

									</div>
								</td>
                                <td class="spark td_team2_bm_back_0">
									<div class="back-gradient text-color-black">

										<div id="back_1" class="BmBack" data-team="team' . @$match_data['t2'][0]['bm1'][$team1_name]['sectionId'] . '">
											<a data-cls="cyan-bg" class="cyan-bg" ' . $login_check . ' data-position="0" data-val="' . round(@$match_data['t2'][0]['bm1'][$team1_name]['b1'], 2) . '">' . round(@$match_data['t2'][0]['bm1'][$team1_name]['b1'], 2) . '</a>
										</div>
									</div>
								</td>

								<td class="sparkLay td_team2_bm_lay_0">
									<div class="lay-gradient text-color-black">
										<div id="lay_1"  class="BmLay pink-bg" data-team="team' . @$match_data['t2'][0]['bm1'][$team1_name]['sectionId'] . '">
											<a data-cls="pink-bg" ' . $login_check . ' data-position="0" data-val="' . round(@$match_data['t2'][0]['bm1'][$team1_name]['l1'], 2) . '">' . @round($match_data['t2'][0]['bm1'][$team1_name]['l1'], 2) . '</a></div>

									</div>
								</td>
                                <td class="sparkLay td_team2_bm_lay_1">
									<div class="lay-gradient text-color-black">

										<div id="lay_2" class="BmLay light-pink-bg-2" data-team="team' . @$match_data['t2'][0]['bm1'][$team1_name]['sectionId'] . '">
											<a data-cls="pink-bg" ' . $login_check . ' data-position="1" data-val="' . round(@$match_data['t2'][0]['bm1'][$team1_name]['l2'], 2) . '">' . round(@$match_data['t2'][0]['bm1'][$team1_name]['l2'], 2) . '</a>
										</div>

									</div>
								</td>
                                <td class="sparkLay td_team2_bm_lay_2">
									<div class="lay-gradient text-color-black">

										<div id="lay_3" class="BmLay light-pink-bg-3" data-team="team' . @$match_data['t2'][0]['bm1'][$team1_name]['sectionId'] . '">
											<a data-cls="pink-bg" ' . $login_check . ' data-position="2" data-val="' . round(@$match_data['t2'][0]['bm1'][$team1_name]['l3'], 2) . '">' . round(@$match_data['t2'][0]['bm1'][$team1_name]['l3'], 2) . '</a>
										</div>
									</div>
								</td>
							</tr>';
                } else {
                    if (isset($match_data['t2'][0]['bm1'][$team1_name]['s']) && $match_data['t2'][0]['bm1'][$team1_name]['s'] != 'SUSPENDED') {
                        $display = '';
                        $cls = '';
                        if ($team1_bet_total == '')
                            $display = 'style="display:none"';
                        if ($team1_bet_total != '' && $team1_bet_total >= 0) {
                            $cls = 'text-color-green';
                        } else if ($team1_bet_total != '' && $team1_bet_total < 0) {
                            $cls = 'text-color-red';
                        }
                        $html .= '

					<tr class="white-bg tr_bm_team1">
								<td class="padding3">' . @$match_data['t2'][0]['bm1'][$team1_name]['nat'] . '<br>
								<div>
									<span class="lose ' . $cls . '" ' . $display . ' id="team1_betBM_count_old">(<span id="team1_BM_total">' . round($team1_bet_total, 2) . '</span>)</span>
									<span class="tolose text-color-red" style="display:none" id="team1_betBM_count_new">(6.7)</span>
								</div>
								</td>
								<td class="spark td_team1_bm_back_2">
									<div class="back-gradient text-color-black">
										<div id="back_3" class="BmBack light-blue-bg-2" data-team="team' . @$match_data['t2'][0]['bm1'][$team1_name]['sectionId'] . '">
											<a data-cls="cyan-bg" ' . $login_check . ' data-position="2" data-val="' . round(@$match_data['t2'][0]['bm1'][$team1_name]['b3'], 2) . '">' . round(@$match_data['t2'][0]['bm1'][$team1_name]['b3'], 2) . '</a>
										</div>

									</div>
								</td>
                                <td class="spark td_team1_bm_back_1">
									<div class="back-gradient text-color-black">

										<div id="back_2" class="BmBack light-blue-bg-3" data-team="team' . @$match_data['t2'][0]['bm1'][$team1_name]['sectionId'] . '">
											<a data-cls="cyan-bg" ' . $login_check . ' data-position="1" data-val="' . round(@$match_data['t2'][0]['bm1'][$team1_name]['b2'], 2) . '">' . round(@$match_data['t2'][0]['bm1'][$team1_name]['b2'], 2) . '</a>
										</div>
									</div>
								</td>
                                <td class="spark td_team1_bm_back_0">
									<div class="back-gradient text-color-black">

										<div id="back_1" class="BmBack" data-team="team' . @$match_data['t2'][0]['bm1'][$team1_name]['sectionId'] . '">
											<a data-cls="cyan-bg" class="cyan-bg" ' . $login_check . ' data-position="0" data-val="' . round(@$match_data['t2'][0]['bm1'][$team1_name]['b1'], 2) . '">' . round(@$match_data['t2'][0]['bm1'][$team1_name]['b1'], 2) . '</a>
										</div>
									</div>
								</td>


								<td class="sparkLay td_team1_bm_lay_0">
									<div class="lay-gradient text-color-black">
										<div id="lay_1"  class="BmLay pink-bg" data-team="team' . @$match_data['t2'][0]['bm1'][$team1_name]['sectionId'] . '">
											<a data-cls="pink-bg" ' . $login_check . ' data-position="0" data-val="' . round(@$match_data['t2'][0]['bm1'][$team1_name]['l1'], 2) . '">' . @round($match_data['t2'][0]['bm1'][$team1_name]['l1'], 2) . '</a></div>

									</div>
								</td>
                                <td class="sparkLay td_team1_bm_lay_1">
									<div class="lay-gradient text-color-black">

										<div id="lay_2" class="BmLay light-pink-bg-2" data-team="team' . @$match_data['t2'][0]['bm1'][$team1_name]['sectionId'] . '">
											<a data-cls="pink-bg" ' . $login_check . ' data-position="1" data-val="' . round(@$match_data['t2'][0]['bm1'][$team1_name]['l2'], 2) . '">' . round(@$match_data['t2'][0]['bm1'][$team1_name]['l2'], 2) . '</a>
										</div>
									</div>
								</td>
                                <td class="sparkLay td_team1_bm_lay_2">
									<div class="lay-gradient text-color-black">

										<div id="lay_3" class="BmLay light-pink-bg-3" data-team="team' . @$match_data['t2'][0]['bm1'][$team1_name]['sectionId'] . '">
											<a data-cls="pink-bg" ' . $login_check . ' data-position="2" data-val="' . round(@$match_data['t2'][0]['bm1'][$team1_name]['l3'], 2) . '">' . round(@$match_data['t2'][0]['bm1'][$team1_name]['l3'], 2) . '</a>
										</div>
									</div>
								</td>
							</tr>
							<tr class="mobileBack tr_team1_BM mobile_bet_model_div" id="mobile_tr">
								<td colspan="7" class="tr_team1_BM_td_mobile mobile_tr_common_class"></td>
							</tr>
							';
                    } else {
                        $display = '';
                        $cls = '';
                        if ($team1_bet_total == '')
                            $display = 'style="display:none"';
                        if ($team1_bet_total != '' && $team1_bet_total >= 0) {
                            $cls = 'text-color-green';
                        } else if ($team1_bet_total != '' && $team1_bet_total < 0) {
                            $cls = 'text-color-red';
                        }
                        $html .= '

					<tr class="fancy-suspend-tr team1_bm_fancy">
						<td></td>
						<td class="fancy-suspend-td" colspan="6">
							<div class="fancy-suspend black-bg-5 text-color-white"><span>SUSPENDED</span></div>
						</td>
					</tr>
					<tr class="white-bg tr_bm_team1">
						<td class="padding3">' . @$match_data['t2'][0]['bm1'][$team2_name]['nat'] . '<br>
							<div>
								<span class="lose ' . $cls . '" ' . $display . ' id="team1_betBM_count_old">(<span id="team1_BM_total">' . round($team1_bet_total, 2) . '</span>)</span>
								<span class="tolose text-color-red" style="display:none" id="team1_betBM_count_new">(6.7)</span>
							</div>
						</td>
						<td class="td_team1_bm_back_2">
							<div class="back-gradient text-color-black">
								<div id="back_3" class="light-blue-bg-2">
									<a>  </a>
								</div>
							</div>
						</td>
                        <td class="td_team1_bm_back_1">
							<div class="back-gradient text-color-black">

								<div id="back_2" class="light-blue-bg-3">
									<a>  </a>
								</div>
							</div>
						</td>
                        <td class="td_team1_bm_back_0">
							<div class="back-gradient text-color-black">

								<div id="back_1"><a class="cyan-bg">  </a></div>
							</div>
						</td>

						<td class="td_team1_bm_lay_0">
							<div class="lay-gradient text-color-black">
								<div id="lay_1"><a class="pink-bg">  </a></div>

							</div>
						</td>
                        <td class="td_team1_bm_lay_1">
							<div class="lay-gradient text-color-black">

								<div id="lay_2" class="light-pink-bg-2">
									<a>  </a>
								</div>
							</div>
						</td>
                        <td class="td_team1_bm_lay_2">
							<div class="lay-gradient text-color-black">

								<div id="lay_3" class="light-pink-bg-3">
									<a>  </a>
								</div>
							</div>
						</td>
					</tr>
					';

                    }
                    if (isset($match_data['t2'][0]['bm1'][$team2_name]['s']) && @$match_data['t2'][0]['bm1'][$team2_name]['s'] != 'SUSPENDED') {
                        $display = '';
                        $cls = '';
                        if ($team2_bet_total == '')
                            $display = 'style="display:none"';
                        if ($team2_bet_total != '' && $team2_bet_total >= 0) {
                            $cls = 'text-color-green';
                        } else if ($team2_bet_total != '' && $team2_bet_total < 0) {
                            $cls = 'text-color-red';
                        }
                        $html .= '<tr class="white-bg tr_bm_team2">
								<td class="padding3">' . @$match_data['bm'][1]['nation'] . '<br>
									<div>
										<span class="lose ' . $cls . '" ' . $display . ' id="team2_betBM_count_old">(<span id="team2_BM_total">' . round($team2_bet_total, 2) . '</span>)</span>
										<span class="tolose text-color-red" style="display:none" id="team2_betBM_count_new">(6.7)</span>
									</div>
								</td>
								<td class="spark td_team2_bm_back_2">
									<div class="back-gradient text-color-black">
										<div id="back_3" class="BmBack light-blue-bg-2" data-team="team' . @$match_data['t2'][0]['bm1'][$team2_name]['sectionId'] . '">
											<a data-cls="cyan-bg" ' . $login_check . ' data-position="2" data-val="' . round(@$match_data['t2'][0]['bm1'][$team2_name]['b3'], 2) . '">' . round(@$match_data['t2'][0]['bm1'][$team2_name]['b3'], 2) . '</a>
										</div>
									</div>
								</td>
                                <td class="spark td_team2_bm_back_1">
									<div class="back-gradient text-color-black">

										<div id="back_2" class="BmBack light-blue-bg-3" data-team="team' . @$match_data['t2'][0]['bm1'][$team2_name]['sectionId'] . '">
											<a data-cls="cyan-bg" ' . $login_check . ' data-position="1" data-val="' . round(@$match_data['t2'][0]['bm1'][$team2_name]['b2'], 2) . '">' . round(@$match_data['t2'][0]['bm1'][$team2_name]['b2'], 2) . '</a>
										</div>
									</div>
								</td>
                                <td class="spark td_team2_bm_back_0">
									<div class="back-gradient text-color-black">

										<div id="back_1" class="BmBack" data-team="team' . @$match_data['t2'][0]['bm1'][$team2_name]['sectionId'] . '">
											<a data-cls="cyan-bg" class="cyan-bg" ' . $login_check . ' data-position="0" data-val="' . round(@$match_data['t2'][0]['bm1'][$team2_name]['b1'], 2) . '">' . round(@$match_data['t2'][0]['bm1'][$team2_name]['b1'], 2) . '</a>
										</div>
									</div>
								</td>


								<td class="sparkLay td_team2_bm_lay_0">
									<div class="lay-gradient text-color-black">
										<div id="lay_1" class="BmLay pink-bg" data-team="team' . @$match_data['t2'][0]['bm1'][$team2_name]['sectionId'] . '">
											<a  data-cls="pink-bg" ' . $login_check . ' data-position="0" data-val="' . round(@$match_data['t2'][0]['bm1'][$team2_name]['l1'], 2) . '">' . round(@$match_data['t2'][0]['bm1'][$team2_name]['l1'], 2) . '</a></div>
									</div>
								</td>
                                <td class="sparkLay td_team2_bm_lay_1">
									<div class="lay-gradient text-color-black">

										<div id="lay_2" class="BmLay light-pink-bg-2" data-team="team' . @$match_data['t2'][0]['bm1'][$team2_name]['sectionId'] . '">
											<a  data-cls="pink-bg" ' . $login_check . ' data-position="1" data-val="' . round(@$match_data['t2'][0]['bm1'][$team2_name]['l2'], 2) . '">' . round(@$match_data['t2'][0]['bm1'][$team2_name]['l2'], 2) . '</a>
										</div>
									</div>
								</td>
                                <td class="sparkLay td_team2_bm_lay_2">
									<div class="lay-gradient text-color-black">

										<div id="lay_3" class="BmLay light-pink-bg-3" data-team="team' . @$match_data['t2'][0]['bm1'][$team2_name]['sectionId'] . '">
											<a  data-cls="pink-bg" ' . $login_check . ' data-position="2" data-val="' . round(@$match_data['t2'][0]['bm1'][$team2_name]['l3'], 2) . '">' . round(@$match_data['t2'][0]['bm1'][$team2_name]['l3'], 2) . '</a>
										</div>
									</div>
								</td>
							</tr>
							<tr class="mobileBack tr_team2_BM mobile_bet_model_div" id="mobile_tr">
								<td colspan="7" class="tr_team2_BM_td_mobile mobile_tr_common_class"></td>
							</tr>
							';
                    } else {
                        $display = '';
                        $cls = '';
                        if ($team2_bet_total == '')
                            $display = 'style="display:none"';
                        if ($team2_bet_total != '' && $team2_bet_total >= 0) {
                            $cls = 'text-color-green';
                        } else if ($team2_bet_total != '' && $team2_bet_total < 0) {
                            $cls = 'text-color-red';
                        }
                        $html .= '<tr class="fancy-suspend-tr team2_bm_fancy">
						<td></td>
						<td class="fancy-suspend-td" colspan="6">
							<div class="fancy-suspend black-bg-5 text-color-white"><span>SUSPENDED</span></div>
						</td>
					</tr>
					<tr class="white-bg tr_bm_team2">
						<td class="padding3">' . @$match_data['t2'][0]['bm1'][$team2_name]['nat'] . '<br>
							<div>
								<span class="lose ' . $cls . '" ' . $display . ' id="team2_betBM_count_old">(<span id="team2_BM_total">' . round($team2_bet_total, 2) . '</span>)</span>
								<span class="tolose text-color-red" style="display:none" id="team2_betBM_count_new">(6.7)</span>
							</div>
						</td>
						<td class="td_team2_bm_back_2">
							<div class="back-gradient text-color-black">
								<div id="back_3" class="light-blue-bg-2">
									<a> </a>
								</div>
							</div>
						</td>
                        <td class="td_team2_bm_back_1">
							<div class="back-gradient text-color-black">

								<div id="back_2" class="light-blue-bg-3">
									<a> </a>
								</div>
							</div>
						</td>
                        <td class="td_team2_bm_back_0">
							<div class="back-gradient text-color-black">

								<div id="back_1"><a class="cyan-bg"> </a></div>
							</div>
						</td>


						<td class="td_team2_bm_lay_0">
							<div class="lay-gradient text-color-black">
								<div id="lay_1"><a class="pink-bg"> </a></div>

				            </div>
						</td>
                        <td class="td_team2_bm_lay_1">
							<div class="lay-gradient text-color-black">
								<div id="lay_2" class="light-pink-bg-2">
										<a> </a>
								</div>
				            </div>
						</td>
                        <td class="td_team2_bm_lay_2">
							<div class="lay-gradient text-color-black">

								<div id="lay_3" class="light-pink-bg-3">
										<a> </a>
								</div>
				            </div>
						</td>
					</tr>
					';

                    }
                    if (isset($match_data['t2'][0]['bm1'][$team3_name]['s'])) {
                        if (@$match_data['t2'][0]['bm1'][$team3_name]['s'] != 'SUSPENDED') {
                            $display = '';
                            $cls = '';
                            if ($team_draw_bet_total == '') {
                                $display = 'style="display:none"';
                            }
                            if ($team_draw_bet_total != '' && $team_draw_bet_total >= 0) {
                                $cls = 'text-color-green';
                            } else if ($team_draw_bet_total != '' && $team_draw_bet_total < 0) {
                                $cls = 'text-color-red';
                            }

                            $html .= '<tr class="white-bg tr_bm_team3">
									<td class="padding3">' . @$match_data['t2'][0]['bm1'][$team3_name]['nat'] . '<br>
										<div>
											<span class="lose ' . $cls . '" ' . $display . ' id="draw_betBM_count_old">(<span id="draw_BM_total">' . round($team_draw_bet_total, 2) . '</span>)</span>
											<span class="tolose text-color-red" style="display:none" id="draw_betBM_count_new">(6.7)</span>
										</div>
									</td>
									<td class="spark td_team3_bm_back_2">
										<div class="back-gradient text-color-black">
											<div id="back_3" class="BmBack light-blue-bg-2" data-team="team' . @$match_data['t2'][0]['bm1'][$team3_name]['sectionId'] . '">
												<a data-cls="cyan-bg" ' . $login_check . ' data-position="2" data-val="' . round(@$match_data['t2'][0]['bm1'][$team3_name]['b3'], 2) . '">' . round(@$match_data['t2'][0]['bm1'][$team3_name]['b3'], 2) . '</a>
											</div>
										</div>
									</td>
                                    <td class="spark td_team3_bm_back_1">
										<div class="back-gradient text-color-black">

											<div id="back_2" class="BmBack light-blue-bg-3" data-team="team' . @$match_data['t2'][0]['bm1'][$team3_name]['sectionId'] . '">
												<a data-cls="cyan-bg" ' . $login_check . ' data-position="1" data-val="' . round(@$match_data['t2'][0]['bm1'][$team3_name]['b2'], 2) . '">' . round(@$match_data['t2'][0]['bm1'][$team3_name]['b2'], 2) . '</a>
											</div>

										</div>
									</td>
                                    <td class="spark td_team3_bm_back_0">
										<div class="back-gradient text-color-black">

											<div id="back_1" class="BmBack" data-team="team' . @$match_data['t2'][0]['bm1'][$team3_name]['sectionId'] . '">
												<a data-cls="cyan-bg" class="cyan-bg" ' . $login_check . ' data-position="0" data-val="' . round(@$match_data['t2'][0]['bm1'][$team3_name]['b1'], 2) . '">' . round(@$match_data['t2'][0]['bm1'][$team3_name]['b1'], 2) . '</a>
											</div>
										</div>
									</td>


									<td class="sparkLay td_team3_bm_lay_0">
										<div class="lay-gradient text-color-black">
											<div id="lay_1" class="BmLay pink-bg" data-team="team' . @$match_data['t2'][0]['bm1'][$team3_name]['sectionId'] . '">
												<a data-cls="pink-bg" ' . $login_check . ' data-position="0" data-val="' . round(@$match_data['t2'][0]['bm1'][$team3_name]['l1'], 2) . '">' . round(@$match_data['t2'][0]['bm1'][$team3_name]['l1'], 2) . '</a>
											</div>
										</div>
									</td>
                                    <td class="sparkLay td_team3_bm_lay_1">
										<div class="lay-gradient text-color-black">

											<div id="lay_2" class="BmLay light-pink-bg-2" data-team="team' . @$match_data['t2'][0]['bm1'][$team3_name]['sectionId'] . '">
												<a data-cls="pink-bg" ' . $login_check . ' data-position="1" data-val="' . round(@$match_data['t2'][0]['bm1'][$team3_name]['l2'], 2) . '">' . round(@$match_data['t2'][0]['bm1'][$team3_name]['l2'], 2) . '</a>
											</div>
										</div>
									</td>
                                    <td class="sparkLay td_team3_bm_lay_2">
										<div class="lay-gradient text-color-black">

											<div id="lay_3" class="BmLay light-pink-bg-3" data-team="team' . @$match_data['t2'][0]['bm1'][$team3_name]['sectionId'] . '">
												<a data-cls="pink-bg" ' . $login_check . ' data-position="2" data-val="' . round(@$match_data['t2'][0]['bm1'][$team3_name]['l3'], 2) . '">' . round(@$match_data['t2'][0]['bm1'][$team3_name]['l3'], 2) . '</a>
											</div>
										</div>
									</td>
								</tr>
								<tr class="mobileBack tr_team3_BM mobile_bet_model_div" id="mobile_tr">
									<td colspan="7" class="tr_team3_BM_td_mobile mobile_tr_common_class"></td>
								</tr>
								';
                        } else {
                            $display = '';
                            $cls = '';
                            if ($team_draw_bet_total == '') {
                                $display = 'style="display:none"';
                            }
                            if ($team_draw_bet_total != '' && $team_draw_bet_total >= 0) {
                                $cls = 'text-color-green';
                            } else if ($team_draw_bet_total != '' && $team_draw_bet_total < 0) {
                                $cls = 'text-color-red';
                            }
                            $html .= '<tr class="fancy-suspend-tr team3_bm_fancy">
							<td></td>
							<td class="fancy-suspend-td" colspan="6">
								<div class="fancy-suspend black-bg-5 text-color-white"><span>' . @$match_data['bm'][$team3_name]['status'] . '</span></div>
							</td>
						</tr>
						<tr class="white-bg tr_bm_team3">
							<td class="padding3">' . @$match_data['t2'][0]['bm1'][$team3_name]['nat'] . '<br>
							<div>
								<span class="lose ' . $cls . '" ' . $display . ' id="draw_betBM_count_old">(<span id="draw_BM_total">' . round($team_draw_bet_total) . '</span>)</span>
								<span class="tolose text-color-red" style="display:none" id="draw_betBM_count_new">(6.7)</span>
							</div>
							</td>
							<td class="td_team3_bm_back_2">
								<div class="back-gradient text-color-black">
									<div id="back_3" class="light-blue-bg-2">
										<a>  </a>
									</div>
								</div>
							</td>
                            <td class="td_team3_bm_back_1">
								<div class="back-gradient text-color-black">

									<div id="back_2" class="light-blue-bg-3">
										<a>  </a>
									</div>
								</div>
							</td>
                            <td class="td_team3_bm_back_0">
								<div class="back-gradient text-color-black">

									<div id="back_1"><a class="cyan-bg">  </a></div>
								</div>
							</td>


							<td class="td_team3_bm_lay_0">
								<div class="lay-gradient text-color-black">
									<div id="lay_1"><a class="pink-bg">  </a></div>
								</div>
							</td>
                            <td class="td_team3_bm_lay_1">
								<div class="lay-gradient text-color-black">
									<div id="lay_2" class="light-pink-bg-2">
										<a>  </a>
									</div>
								</div>
							</td>
                            <td class="td_team3_bm_lay_2">
								<div class="lay-gradient text-color-black">

									<div id="lay_3" class="light-pink-bg-3">
										<a>  </a>
									</div>
								</div>
							</td>
						</tr>
						';

                        }
                    }
                } // end suspended if
            }
            if ($html != '')
                $html = $html_bm_team . $html;
        }
        echo $html;
    }

    public function casino()
    {
        $casino = Casino::where('status',1)->get();
        return view('front.casino', compact('casino'));
    }

    public function inplay()
    {

//        $htmlResp = $this->getInPlayDataInHtml('inplay');

        $cricketMatches = Match::where('sports_id', 4)->where('status', 1)->where('winner', NULL)->pluck('id','event_id');
        $tennisMatches = Match::where('sports_id', 2)->where('status', 1)->where('winner', NULL)->pluck('id','event_id');
        $soccerMatches = Match::where('sports_id', 1)->where('status', 1)->where('winner', NULL)->pluck('id','event_id');
        if(!empty($getUserCheck)) {
            $favMatches = UsersFavMatch::where("user_id", $getUserCheck->id)->pluck('match_id');
        }else{
            $favMatches = UsersFavMatch::where("user_id", 0)->pluck('match_id');
        }

        $sports = Sport::all();
        $cricket_html = '';
        $tennis_html = '';
        $soccer_html = '';
        $settings = setting::first();
        return view('front.inplay', compact('sports', 'cricket_html', 'soccer_html', 'tennis_html', 'settings','cricketMatches','tennisMatches','soccerMatches','favMatches'));
    }


    public function getInPlayFormattedData($type)
    {

        $htmlResp = $this->getInPlayDataInHtml($type);

        return response()->json(array('html' => $htmlResp['html']));
    }

    function getInPlayDataInHtml($type)
    {
        $sports = Sport::all();
        $html = [];
        foreach ($sports as $sport) {
            $todayDate = date('d-m-Y');
            $tomorrowDate = date('d-m-Y', strtotime("+1 day"));

            $html[$sport->sId] = '';
            $apiData = [];

            if ($sport->sId == 4) {
                $apiData = app('App\Http\Controllers\RestApi')->getCricketData();
            } elseif ($sport->sId == 2) {
                $apiData = app('App\Http\Controllers\RestApi')->getTennisData();
            } elseif ($sport->sId == 1) {
                $apiData = app('App\Http\Controllers\RestApi')->getSoccerData();
            }

            $records = [];
            if (!empty($apiData)) {

                $matches = Match::select('id', 'match_name', 'match_id', 'sports_id', 'status', 'winner', 'match_date', 'event_id')->where('sports_id', $sport->sId)->where('status', 1)->where('winner', NULL)->orderBy('match_date', 'ASC')->get();

                $finalMatchesToDisplay = [];

                foreach ($matches as $match) {
                    $finalMatchesToDisplay[$match->event_id] = $match;
                }
                if (!empty($finalMatchesToDisplay) && !empty($apiData)) {
                    foreach ($apiData as $item) {
                        if (isset($finalMatchesToDisplay[$item['gameId']])) {
                            $match = $finalMatchesToDisplay[$item['gameId']];

                            $eventNameExp = explode('/', $item['eventName']);
                            $event = trim($eventNameExp[0]);
                            $startTime = trim(end($eventNameExp));
                            $date = Carbon::parse($startTime);

                            if (Carbon::parse($date)->isToday()) {
                                $match_date = date('h:i A', strtotime($date));
                            } else if (Carbon::parse($date)->isTomorrow()) {
                                $match_date = 'Tomorrow ' . date('h:i A', strtotime($date));
                            } else {
                                $match_date = date('d-m-Y h:i A', strtotime($date));
                            }

                            $match['formatted_match_date'] = $match_date;

                            $orgDate = $match->match_date;
                            $newDate = date("d-m-Y", strtotime($orgDate));
                            if ($match->match_id != '' && $newDate == $todayDate && $type == 'today' && $item['inPlay'] != 'True') {
                                $item['match_detail'] = $finalMatchesToDisplay[$item['gameId']];
                                $records[] = $item;
                            } elseif ($match->match_id != '' && $newDate == $tomorrowDate && $type == 'tomorrow' && $item['inPlay'] != 'True') {
                                $item['match_detail'] = $finalMatchesToDisplay[$item['gameId']];
                                $records[] = $item;
                            } elseif ($match->match_id != '' && $type == 'inplay' && $item['inPlay'] == 'True') {
                                $item['match_detail'] = $finalMatchesToDisplay[$item['gameId']];
                                $records[] = $item;
                            }
                        }
                    }
                }
            }

            $render = view('front.ajax.inplaydata.inplay', compact('records', 'type', 'sport'))->render();

            $html[$sport->sId] = $render;
        }

        return [
            'html' => $html,
            'sports' => $sports
        ];
    }

    public function getInPlayDataCount()
    {
        $sports = Sport::all();

        $count = [];

        foreach ($sports as $sport) {
            $todayDate = date('d-m-Y');
            $tomorrowDate = date('d-m-Y', strtotime("+1 day"));

            $html[$sport->sId] = '';
            $apiData = [];

            if ($sport->sId == 4) {
                $apiData = app('App\Http\Controllers\RestApi')->getCricketData();
            } elseif ($sport->sId == 2) {
                $apiData = app('App\Http\Controllers\RestApi')->getTennisData();
            } elseif ($sport->sId == 1) {
                $apiData = app('App\Http\Controllers\RestApi')->getSoccerData();
            }

            $recordsCount = 0;
            if (!empty($apiData)) {

                $matches = Match::select('id', 'match_name', 'match_id', 'sports_id', 'status', 'winner', 'match_date', 'event_id')->where('sports_id', $sport->sId)->where('status', 1)->where('winner', NULL)->orderBy('match_date', 'ASC')->get();

                $finalMatchesToDisplay = [];

                foreach ($matches as $match) {
                    $finalMatchesToDisplay[$match->event_id] = $match;
                }
                if (!empty($finalMatchesToDisplay) && !empty($apiData)) {
                    foreach ($apiData as $item) {
                        if (isset($finalMatchesToDisplay[$item['gameId']])) {
                            if ($match->match_id != '' && $item['inPlay'] == 'True') {
                                $recordsCount++;
                            }
                        }
                    }
                }
            }

            $count[$sport->sId] = $recordsCount;
        }

        $sessionExpired = false;
        $sessionData = Session::get('playerUser');
        Log::info("\n" . str_repeat("~-~", 30));
        Log::info("sessionData");
        Log::info($sessionData);
        if (!empty($sessionData)) {
            $checkstatus = User::where('id', $sessionData->id)->first();

            if (!empty($checkstatus)) {
                Log::info("checkstatus");
                Log::info($checkstatus);
                if ($checkstatus->token_val != $sessionData->token_val) {
                    Session::forget('playerUser');
                    $sessionExpired = true;
                    Log::info("Session Expired: " . true);
                }
                if ($checkstatus->status == 'suspend') {
                    Session::forget('playerUser');
                    $sessionExpired = true;
                    Log::info("Session suspend Expired: " . true);
                }
            }
        }

        return response()->json(array('session_expired' => $sessionExpired, 'count' => $count));
    }

    public function getmatchdetailsOfInplay()
    {
        $sports = Sport::all();
        $html = '';
        $i = 0;
        $final_html = '';
        $cricket_final_html = '';
        $tennis_final_html = '';
        $soccer_final_html = '';
        $match_array_data_cricket = array();
        $match_array_data_tenis = array();
        $match_array_data_soccer = array();

        foreach ($sports as $sport) {
            $match_link = Match::where('sports_id', $sport->sId)->where('status', 1)->where('winner', NULL)->orderBy('match_date', 'ASC')->get();
            foreach ($match_link as $match) {
                if (@$match->match_id != '') {
                    if ($match->sports_id == 4)
                        $match_array_data_cricket[$match->match_id] = $match->event_id;
                    else if ($match->sports_id == 2)
                        $match_array_data_tenis[$match->match_id] = $match->match_id;
                    else if ($match->sports_id == 1)
                        $match_array_data_soccer[$match->match_id] = $match->match_id;
                }
            }
        }

        $mdata = array();
        $inplay = 0;
        if (!empty($match_array_data_cricket)) {
            foreach ($match_array_data_cricket as $ck => $cdata) {
                $match_data = app('App\Http\Controllers\RestApi')->getSingleMatchData($cdata, $ck, 4);
                $inplay_game = '';
                if (isset($match_data['t1'][0][0]['iplay']) && $match_data['t1'][0][0]['iplay'] === 'True') {
                    $match_detail = Match::where('match_id', $ck)->where('status', 1)->first();

                    if (isset($match_data['t1'][0][0]['b1']) && isset($match_data['t1'][0][0]['l1'])) {
                        $html .= '<div class="secondblock-cricket active-block active-tag white-bg"><span class="fir-col1-green desk">
						<a href="matchDetail/' . $match_detail['id'] . '" class="text-color-blue-light">' . $match_detail['match_name'] . '
						<span style="color:green" class="deskinplay">In-Play</span></a>
						</span>';

                        $html .= '<span class="fir-col2">
						<a class="backbtn lightblue-bg2">' . $match_data['t1'][0][0]['b1'] . '</a>
						<a class="laybtn lightpink-bg1">' . $match_data['t1'][0][0]['l1'] . '</a>
						</span>';

                    } else {
                        $html .= '<div class="secondblock-cricket active-block active-tag white-bg"><span class="fir-col1-green desk">
						<a href="matchDetail/' . $match_detail['id'] . '" class="text-color-blue-light">' . $match_detail['match_name'] . '
						<span style="color:green" class="deskinplay">In-Play</span></a>
						</span>';

                        $html .= '<span class="fir-col2">
						<a class="backbtn lightblue-bg2">--</a>
						<a class="laybtn lightpink-bg1">--</a>
						</span>';

                    }
                    if (isset($match_data['t1'][0][2]['b1'])) {
                        if (@$match_data['t1'][0][2]['b1'] != '' && @$match_data['t1'][0][2]['l1'] != '') {
                            $html .= '<span class="fir-col2">
						<a class="backbtn lightblue-bg2">' . $match_data['t1'][0][2]['b1'] . '</a>
						<a class="laybtn lightpink-bg1">' . $match_data['t1'][0][2]['l1'] . '</a>
						</span>';
                        } else {
                            $html .= '<span class="fir-col2">
						<a class="backbtn lightblue-bg2">--</a>
						<a class="laybtn lightpink-bg1">--</a>
						</span>';
                        }

                    } else {
                        $html .= '<span class="fir-col2">
						<a class="backbtn lightblue-bg2">--</a>
						<a class="laybtn lightpink-bg1">--</a>
						</span>';
                    }
                    if (isset($match_data['t1'][0][1]['b1']) && isset($match_data['t1'][0][1]['l1'])) {
                        $html .= '<span class="fir-col2">
						<a class="backbtn lightblue-bg2">' . $match_data['t1'][0][1]['b1'] . '</a>
						<a class="laybtn lightpink-bg1">' . $match_data['t1'][0][1]['l1'] . '</a>
						</span>
						<span class="fir-col3">
							<a><img src="' . asset('asset/front/img/round-pin.png') . '" > <img class="hover-img" src="public/asset/front/img/round-pin1.png" ></a>
						</span>
						</div>';
                    } else {
                        $html .= '<span class="fir-col2">
						<a class="backbtn lightblue-bg2">--</a>
						<a class="laybtn lightpink-bg1">--</a>
						</span>
						<span class="fir-col3">
							<a><img src="' . asset('asset/front/img/round-pin.png') . '" > <img class="hover-img" src="public/asset/front/img/round-pin1.png" ></a>
						</span>
						</div>';
                    }
                }
            }
            $cricket_final_html .= $html;
        } else {
            $cricket_final_html = 'No match found.';
        }
        $final_html .= $cricket_final_html;

        //for tennis
        $html = '';
        $tennis_html = '';
        if (!empty($match_array_data_tenis)) {
            foreach ($match_array_data_tenis as $ck => $cdata) {
                $match_data = app('App\Http\Controllers\RestApi')->getSingleMatchData($cdata, $ck, 2);

                if (empty($match_data)) {
                    continue;
                }
                $match_detail = Match::where('match_id', $ck)->where('status', 1)->first();
                if ($match_data[0]['inplay'] == 1) {
                    if (isset($match_data[0]['runners'][0]['ex']['availableToBack'][0]['price']) && isset($match_data[0]['runners'][0]['ex']['availableToLay'][0]['price'])) {
                        $html .= '<div class="secondblock-cricket active-block active-tag white-bg"><span class="fir-col1-green desk">
						<a href="matchDetail/' . $match_detail['id'] . '" class="text-color-blue-light">' . $match_detail['match_name'] . '</a>
						<span style="color:green" class="deskinplay">In-Play</span>
						</span>';

                        $html .= '<span class="fir-col2">
						<a class="backbtn lightblue-bg2">' . @$match_data[0]['runners'][0]['ex']['availableToLay'][0]['price'] . '</a>
						<a class="laybtn lightpink-bg1">' . @$match_data[0]['runners'][0]['ex']['availableToLay'][0]['price'] . '</a>
						</span>';

                    } else {
                        $html .= '<div class="secondblock-cricket active-block active-tag white-bg"><span class="fir-col1-green desk">
						<a href="matchDetail/' . $match_detail['id'] . '" class="text-color-blue-light">' . $match_detail['match_name'] . '
						<span style="color:green" class="deskinplay">In-Play</span></a>
						</span>';

                        $html .= '<span class="fir-col2">
						<a class="backbtn lightblue-bg2">--</a>
						<a class="laybtn lightpink-bg1">--</a>
						</span>';

                    }
                    if (isset($match_data[0]['runners'][2]['ex']['availableToBack'][0]['price']) && isset($match_data[0]['runners'][2]['ex']['availableToLay'][0]['price'])) {
                        $html .= '<span class="fir-col2">
						<a class="backbtn lightblue-bg2">' . @$match_data[0]['runners'][2]['ex']['availableToBack'][0]['price'] . '</a>
						<a class="laybtn lightpink-bg1">' . @$match_data[0]['runners'][2]['ex']['availableToLay'][0]['price'] . '</a>
						</span>';
                    } else {
                        $html .= '<span class="fir-col2">
						<a class="backbtn lightblue-bg2">--</a>
						<a class="laybtn lightpink-bg1">--</a>
						</span>';
                    }
                    if (isset($match_data[0]['runners'][1]['ex']['availableToBack'][0]['price']) && isset($match_data[0]['runners'][1]['ex']['availableToLay'][0]['price'])) {
                        $html .= '<span class="fir-col2">
						<a class="backbtn lightblue-bg2">' . @$match_data[0]['runners'][1]['ex']['availableToBack'][0]['price'] . '</a>
						<a class="laybtn lightpink-bg1">' . @$match_data[0]['runners'][1]['ex']['availableToLay'][0]['price'] . '</a>
						</span>
						<span class="fir-col3">
							<a><img src="' . asset('asset/front/img/round-pin.png') . '" > <img class="hover-img" src="public/asset/front/img/round-pin1.png" ></a>
						</span>
						</div>';
                    } else {
                        $html .= '<span class="fir-col2">
						<a class="backbtn lightblue-bg2">--</a>
						<a class="laybtn lightpink-bg1">--</a>
						</span>
						<span class="fir-col3">
							<a><img src="' . asset('asset/front/img/round-pin.png') . '" > <img class="hover-img" src="public/asset/front/img/round-pin1.png" ></a>
						</span>
						</div>';
                    }
                }

            }
            $tennis_final_html .= $html;
        } else {
            $tennis_final_html = 'No match found.';
        }
        $final_html .= "~~" . $tennis_final_html;

        $html = '';
        $soccer_html = '';

        if (!empty($match_array_data_soccer)) {
            foreach ($match_array_data_soccer as $ck => $cdata) {
                $match_data = app('App\Http\Controllers\RestApi')->getSingleMatchData($cdata, $ck, 1);

                if (empty($match_data)) {
                    continue;
                }
                $match_detail = Match::where('match_id', $ck)->where('status', 1)->first();
                if ($match_data[0]['inplay'] == 1) {
                    if (isset($match_data[0]['runners'][0]['ex']['availableToBack'][0]['price']) && isset($match_data[0]['runners'][0]['ex']['availableToLay'][0]['price'])) {
                        $html .= '<div class="secondblock-cricket active-block active-tag white-bg"><span class="fir-col1-green desk">
						<a href="matchDetail/' . $match_detail['id'] . '" class="text-color-blue-light">' . $match_detail['match_name'] . '
						<span style="color:green" class="deskinplay">In-Play</span></a>
						</span>';

                        $html .= '<span class="fir-col2">
						<a class="backbtn lightblue-bg2">' . @$match_data[0]['runners'][0]['ex']['availableToLay'][0]['price'] . '</a>
						<a class="laybtn lightpink-bg1">' . @$match_data[0]['runners'][0]['ex']['availableToLay'][0]['price'] . '</a>
						</span>';

                    } else {
                        $html .= '<div class="secondblock-cricket active-block active-tag white-bg"><span class="fir-col1-green desk">
						<a href="matchDetail/' . $match_detail['id'] . '" class="text-color-blue-light">' . $match_detail['match_name'] . '
						<span style="color:green" class="deskinplay">In-Play</span></a>
						</span>';

                        $html .= '<span class="fir-col2">
						<a class="backbtn lightblue-bg2">--</a>
						<a class="laybtn lightpink-bg1">--</a>
						</span>';

                    }
                    if (isset($match_data[0]['runners'][2]['ex']['availableToBack'][0]['price']) && isset($match_data[0]['runners'][2]['ex']['availableToLay'][0]['price'])) {
                        $html .= '<span class="fir-col2">
						<a class="backbtn lightblue-bg2">' . @$match_data[0]['runners'][2]['ex']['availableToBack'][0]['price'] . '</a>
						<a class="laybtn lightpink-bg1">' . @$match_data[0]['runners'][2]['ex']['availableToLay'][0]['price'] . '</a>
						</span>';
                    } else {
                        $html .= '<span class="fir-col2">
						<a class="backbtn lightblue-bg2">--</a>
						<a class="laybtn lightpink-bg1">--</a>
						</span>';
                    }
                    if (isset($match_data[0]['runners'][1]['ex']['availableToBack'][0]['price']) && isset($match_data[0]['runners'][1]['ex']['availableToLay'][0]['price'])) {
                        $html .= '<span class="fir-col2">
						<a class="backbtn lightblue-bg2">' . @$match_data[0]['runners'][1]['ex']['availableToBack'][0]['price'] . '</a>
						<a class="laybtn lightpink-bg1">' . @$match_data[0]['runners'][1]['ex']['availableToLay'][0]['price'] . '</a>
						</span>
						<span class="fir-col3">
							<a><img src="' . asset('asset/front/img/round-pin.png') . '" > <img class="hover-img" src="public/asset/front/img/round-pin1.png" ></a>
						</span>
						</div>';
                    } else {
                        $html .= '<span class="fir-col2">
						<a class="backbtn lightblue-bg2">--</a>
						<a class="laybtn lightpink-bg1">--</a>
						</span>
						<span class="fir-col3">
							<a><img src="' . asset('asset/front/img/round-pin.png') . '" > <img class="hover-img" src="public/asset/front/img/round-pin1.png" ></a>
						</span>
						</div>';
                    }
                }

            }
            $soccer_final_html .= $html;
        } else {
            $soccer_final_html = 'No match found.';
        }
        $final_html .= "~~" . $soccer_final_html;


        return $final_html;
    }

    public function getInplaydata(Request $request)
    {
        $val = $request->val;
        /* if($val == 'today')
		{
			$tdate = date('d-m-Y');
			$sports = Sport::all();
			$html=''; $i=0; $final_html=''; $cricket_final_html=''; $tennis_final_html=''; $soccer_final_html='';
			$match_array_data_cricket=array();
			$match_array_data_tenis=array();
			$match_array_data_soccer=array();

		  	foreach($sports as $sport)
		 	{
				$match_link = Match::where('sports_id',$sport->sId)->where('status',1)
				->where('winner',NULL)->orderBy('match_date','ASC')->get();

				foreach ($match_link as $match) {
					$orgDate = $match->match_date;
    				$newDate = date("d-m-Y", strtotime($orgDate));
    				if(@$match->match_id!='' && $newDate == $tdate)
					{
						if($match->sports_id==4)
							$match_array_data_cricket[$match->match_id]=$match->event_id;
						else if($match->sports_id==2)
							$match_array_data_tenis[$match->match_id]=$match->match_id;
						else if($match->sports_id==1)
							$match_array_data_soccer[$match->match_id]=$match->match_id;
					}
				}
			}
			$imp_match_array_data_cricket=@implode(",",$match_array_data_cricket);
			$imp_match_array_data_tenis=@implode(",",$match_array_data_tenis);
			$imp_match_array_data_soccer=@implode(",",$match_array_data_soccer);

			$mdata=array(); $inplay=0;
			if(!empty($imp_match_array_data_cricket)){
			$url='http://69.30.238.2:3644/odds/multiple?ids='.$imp_match_array_data_cricket;
			$headers = array('Content-Type: application/json');
			$process = curl_init();
			curl_setopt($process, CURLOPT_URL, $url);
			curl_setopt($process, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($process, CURLOPT_CUSTOMREQUEST, "GET");
			curl_setopt($process, CURLOPT_TIMEOUT, 30);
			curl_setopt($process, CURLOPT_HTTPGET, 1);
			curl_setopt($process, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
			$return = curl_exec($process);
			curl_close($process);

			$match_data = json_decode($return, true);

			$html=''; $cricket_html='';
			if(!empty($match_data))
			{
				for($j=0;$j<sizeof($match_data);$j++)
				{
					$match_detail = Match::where('match_id',$match_data[$j]['marketId'])->where('status',1)->first();
					$html.='
					<div class="secondblock-cricket active-block active-tag white-bg">
						<span class="fir-col1">
							<a href="matchDetail/'.$match_detail['id'].'" class="text-color-blue-light">'.$match_detail['match_name'].'</a>
							<span class="wd22 mobileDate">'.date('d-m-y H:i',strtotime($match_detail['match_date'])).'</span>
						</span>
						<span class="fir-col3 pinimg">
			                <a><img src="'.asset('asset/front/img/round-pin.png').'"> <img class="hover-img" src="public/asset/front/img/round-pin1.png"></a>
			            </span>
					</div>';

				}
			}
			$cricket_final_html.=$html;
			}
			else{
				$cricket_final_html='No match found.';
			}
			$final_html.=$cricket_final_html;

			//for tennis
			$html=''; $tennis_html='';
			if(!empty($imp_match_array_data_tenis)){
			$url='http://69.30.238.2:3644/odds/multiple?ids='.$imp_match_array_data_tenis;
			$headers = array('Content-Type: application/json');
			$process = curl_init();
			curl_setopt($process, CURLOPT_URL, $url);
			curl_setopt($process, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($process, CURLOPT_CUSTOMREQUEST, "GET");
			curl_setopt($process, CURLOPT_TIMEOUT, 30);
			curl_setopt($process, CURLOPT_HTTPGET, 1);
			curl_setopt($process, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
			$return = curl_exec($process);
			curl_close($process);
			$match_data = json_decode($return, true);

			if(!empty($match_data))
			{
				for($j=0;$j<sizeof($match_data);$j++)
				{
					$match_detail = Match::where('match_id',$match_data[$j]['marketId'])->where('status',1)->first();

					$html.='
					<div class="secondblock-cricket active-block active-tag white-bg">
						<span class="fir-col1">
							<a href="matchDetail/'.$match_detail['id'].'" class="text-color-blue-light">'.$match_detail['match_name'].'</a>
							<span class="wd22 mobileDate">'.date('d-m-y H:i',strtotime($match_detail['match_date'])).'</span>
						</span>
						<span class="fir-col3 pinimg">
			                <a><img src="'.asset('asset/front/img/round-pin.png').'"> <img class="hover-img" src="public/asset/front/img/round-pin1.png"></a>
			            </span>
					</div>';
				}
			}
			$tennis_final_html.=$html;
			}
			else{
				$tennis_final_html='No match found.';
			}
			$final_html.="~~".$tennis_final_html;

			$html=''; $soccer_html='';

			if(!empty($imp_match_array_data_soccer)){

			$url='http://69.30.238.2:3644/odds/multiple?ids='.$imp_match_array_data_soccer;
			$headers = array('Content-Type: application/json');
			$process = curl_init();
			curl_setopt($process, CURLOPT_URL, $url);
			curl_setopt($process, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($process, CURLOPT_CUSTOMREQUEST, "GET");
			curl_setopt($process, CURLOPT_TIMEOUT, 30);
			curl_setopt($process, CURLOPT_HTTPGET, 1);
			curl_setopt($process, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
			$return = curl_exec($process);
			curl_close($process);

			$match_data = json_decode($return, true);

			if(!empty($match_data))
			{
				for($j=0;$j<sizeof($match_data);$j++)
				{
					$inplay_game='';
					$match_detail = Match::where('match_id',$match_data[$j]['marketId'])->where('status',1)->first();
					$html.='
					<div class="secondblock-cricket active-block active-tag white-bg">
						<span class="fir-col1">
							<a href="matchDetail/'.$match_detail['id'].'" class="text-color-blue-light">'.$match_detail['match_name'].'</a>
							<span class="wd22 mobileDate">'.date('d-m-y H:i',strtotime($match_detail['match_date'])).'</span>
						</span>
						<span class="fir-col3 pinimg">
			                <a><img src="'.asset('asset/front/img/round-pin.png').'"> <img class="hover-img" src="public/asset/front/img/round-pin1.png"></a>
			            </span>
					</div>';
				}
			}
			$soccer_final_html.=$html;
			}
			else{
				$soccer_final_html='No match found.';
			}
			$final_html.="~~".$soccer_final_html;

		}
		if($val == 'tomorrow')
		{
			$tdate = date('d-m-Y',strtotime("+1 day"));
			$sports = Sport::all();
			$html=''; $i=0; $final_html=''; $cricket_final_html=''; $tennis_final_html=''; $soccer_final_html='';
			$match_array_data_cricket=array();
			$match_array_data_tenis=array();
			$match_array_data_soccer=array();

		  	foreach($sports as $sport)
		 	{
				$match_link = Match::where('sports_id',$sport->sId)->where('status',1)
				->where('winner',NULL)->orderBy('match_date','ASC')->get();

				foreach ($match_link as $match) {
					$orgDate = $match->match_date;
    				$newDate = date("d-m-Y", strtotime($orgDate));
    				if(@$match->match_id!='' && $newDate == $tdate)
					{
						if($match->sports_id==4)
							$match_array_data_cricket[]=$match->match_id;
						else if($match->sports_id==2)
							$match_array_data_tenis[]=$match->match_id;
						else if($match->sports_id==1)
							$match_array_data_soccer[]=$match->match_id;
					}
				}
			}
			$imp_match_array_data_cricket=@implode(",",$match_array_data_cricket);
			$imp_match_array_data_tenis=@implode(",",$match_array_data_tenis);
			$imp_match_array_data_soccer=@implode(",",$match_array_data_soccer);

			$mdata=array(); $inplay=0;
			if(!empty($imp_match_array_data_cricket)){
			$url='http://69.30.238.2:3644/odds/multiple?ids='.$imp_match_array_data_cricket;
			$headers = array('Content-Type: application/json');
			$process = curl_init();
			curl_setopt($process, CURLOPT_URL, $url);
			curl_setopt($process, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($process, CURLOPT_CUSTOMREQUEST, "GET");
			curl_setopt($process, CURLOPT_TIMEOUT, 30);
			curl_setopt($process, CURLOPT_HTTPGET, 1);
			curl_setopt($process, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
			$return = curl_exec($process);
			curl_close($process);

			$match_data = json_decode($return, true);

			$html=''; $cricket_html='';
			if(!empty($match_data))
			{
				for($j=0;$j<sizeof($match_data);$j++)
				{
					$match_detail = Match::where('match_id',$match_data[$j]['marketId'])->where('status',1)->first();
					$html.='
					<div class="secondblock-cricket active-block active-tag white-bg">
						<span class="fir-col1">
							<a href="matchDetail/'.$match_detail['id'].'" class="text-color-blue-light">'.$match_detail['match_name'].'</a>
							<span class="wd22 mobileDate">'.date('d-m-y H:i',strtotime($match_detail['match_date'])).'</span>
						</span>
						<span class="fir-col3 pinimg">
			                <a><img src="'.asset('asset/front/img/round-pin.png').'"> <img class="hover-img" src="public/asset/front/img/round-pin1.png"></a>
			            </span>
					</div>';
				}
			}
			$cricket_final_html.=$html;
			}
			else{
				$cricket_final_html='No match found.';
			}
			$final_html.=$cricket_final_html;

			//for tennis
			$html=''; $tennis_html='';
			if(!empty($imp_match_array_data_tenis)){
			$url='http://69.30.238.2:3644/odds/multiple?ids='.$imp_match_array_data_tenis;
			$headers = array('Content-Type: application/json');
			$process = curl_init();
			curl_setopt($process, CURLOPT_URL, $url);
			curl_setopt($process, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($process, CURLOPT_CUSTOMREQUEST, "GET");
			curl_setopt($process, CURLOPT_TIMEOUT, 30);
			curl_setopt($process, CURLOPT_HTTPGET, 1);
			curl_setopt($process, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
			$return = curl_exec($process);
			curl_close($process);
			$match_data = json_decode($return, true);

			if(!empty($match_data))
			{
				for($j=0;$j<sizeof($match_data);$j++)
				{
					$match_detail = Match::where('match_id',$match_data[$j]['marketId'])->where('status',1)->first();

					$html.='
					<div class="secondblock-cricket active-block active-tag white-bg">
						<span class="fir-col1">
							<a href="matchDetail/'.$match_detail['id'].'" class="text-color-blue-light">'.$match_detail['match_name'].'</a>
							<span class="wd22 mobileDate">'.date('d-m-y H:i',strtotime($match_detail['match_date'])).'</span>
						</span>
						<span class="fir-col3 pinimg">
			                <a><img src="'.asset('asset/front/img/round-pin.png').'"> <img class="hover-img" src="public/asset/front/img/round-pin1.png"></a>
			            </span>
					</div>';
				}
			}
			$tennis_final_html.=$html;
			}
			else{
				$tennis_final_html='No match found.';
			}
			$final_html.="~~".$tennis_final_html;

			$html=''; $soccer_html='';

			if(!empty($imp_match_array_data_soccer)){

			$url='http://69.30.238.2:3644/odds/multiple?ids='.$imp_match_array_data_soccer;
			$headers = array('Content-Type: application/json');
			$process = curl_init();
			curl_setopt($process, CURLOPT_URL, $url);
			curl_setopt($process, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($process, CURLOPT_CUSTOMREQUEST, "GET");
			curl_setopt($process, CURLOPT_TIMEOUT, 30);
			curl_setopt($process, CURLOPT_HTTPGET, 1);
			curl_setopt($process, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
			$return = curl_exec($process);
			curl_close($process);

			$match_data = json_decode($return, true);

			if(!empty($match_data))
			{
				for($j=0;$j<sizeof($match_data);$j++)
				{
					$inplay_game='';
					$match_detail = Match::where('match_id',$match_data[$j]['marketId'])->where('status',1)->first();
					$html.='
					<div class="secondblock-cricket active-block active-tag white-bg">
						<span class="fir-col1">
							<a href="matchDetail/'.$match_detail['id'].'" class="text-color-blue-light">'.$match_detail['match_name'].'</a>
							<span class="wd22 mobileDate">'.date('d-m-y H:i',strtotime($match_detail['match_date'])).'</span>
						</span>
						<span class="fir-col3 pinimg">
			                <a><img src="'.asset('asset/front/img/round-pin.png').'"> <img class="hover-img" src="public/asset/front/img/round-pin1.png"></a>
			            </span>
					</div>';
				}
			}
			$soccer_final_html.=$html;
			}
			else{
				$soccer_final_html='No match found.';
			}
			$final_html.="~~".$soccer_final_html;
		}
		if($val == 'inplay')
		{
			$mdata=array(); $inplay=0;
			if(!empty($match_array_data_cricket)){
				foreach ($match_array_data_cricket as $ck => $cdata) {
					$match_data = app('App\Http\Controllers\RestApi')->getSingleMatchData($cdata, $ck, 4);
					$inplay_game='';
					if(isset($match_data['t1'][0][0]['inplay']) && $match_data['t1'][0][0]['inplay'] === 'True'){
						$match_detail = Match::where('match_id',$ck)->where('status',1)->first();

						if(isset($match_data['t1'][0][0]['b1']) && isset($match_data['t1'][0][0]['l1']))
						{
							$html.='<div class="secondblock-cricket active-block active-tag white-bg"><span class="fir-col1">
							<a href="matchDetail/'.$match_detail['id'].'" class="text-color-blue-light">'.$match_detail['match_name'].'</a><div class="mobileDate">'.date('d-m-y H:i',strtotime($match_detail['match_date'])).'</div>
							<div  class="text-color-green">In-Play</div>
							</span>';

							$html.='<span class="fir-col2">
							<a class="backbtn lightblue-bg2">'.$match_data['t1'][0][0]['b1'].'</a>
							<a class="laybtn lightpink-bg1">'.$match_data['t1'][0][0]['l1'].'</a>
							</span>';

						}
						else
						{
							$html.='<div class="secondblock-cricket active-block active-tag white-bg"><span class="fir-col1">
							<a href="matchDetail/'.$match_detail['id'].'" class="text-color-blue-light">'.$match_detail['match_name'].'</a><div class="mobileDate">'.date('d-m-y H:i',strtotime($match_detail['match_date'])).'</div>
							<div  class="text-color-green">In-Play</div>
							</span>';

							$html.='<span class="fir-col2">
							<a class="backbtn lightblue-bg2">--</a>
							<a class="laybtn lightpink-bg1">--</a>
							</span>';

						}
						if(isset($match_data['t1'][0][1]['b1']))
						{	if(@$match_data['t1'][0][1]['b1']!='' && @$match_data['t1'][0][1]['l1']!='')
							{
							$html.='<span class="fir-col2">
							<a class="backbtn lightblue-bg2">'.$match_data['t1'][0][1]['b1'].'</a>
							<a class="laybtn lightpink-bg1">'.$match_data['t1'][0][1]['l1'].'</a>
							</span>';
							}else{
							$html.='<span class="fir-col2">
							<a class="backbtn lightblue-bg2">--</a>
							<a class="laybtn lightpink-bg1">--</a>
							</span>';
							}

						}
						else
						{
							$html.='<span class="fir-col2">
							<a class="backbtn lightblue-bg2">--</a>
							<a class="laybtn lightpink-bg1">--</a>
							</span>';
						}
						if(isset($match_data['t1'][0][2]['b1']) && isset($match_data['t1'][0][2]['l1']))
						{
							$html.='<span class="fir-col2">
							<a class="backbtn lightblue-bg2">'.$match_data['t1'][0][2]['b1'].'</a>
							<a class="laybtn lightpink-bg1">'.$match_data['t1'][0][2]['l1'].'</a>
							</span>
							<span class="fir-col3">
								<a><img src="'.asset('asset/front/img/round-pin.png').'" > <img class="hover-img" src="public/asset/front/img/round-pin1.png" ></a>
							</span>
							</div>';
						}
						else
						{
							$html.='<span class="fir-col2">
							<a class="backbtn lightblue-bg2">--</a>
							<a class="laybtn lightpink-bg1">--</a>
							</span>
							<span class="fir-col3">
								<a><img src="'.asset('asset/front/img/round-pin.png').'" > <img class="hover-img" src="public/asset/front/img/round-pin1.png" ></a>
							</span>
							</div>';
						}
					}
				}
				$cricket_final_html.=$html;
			}
			else{
				$cricket_final_html='No match found.';
			}
			$final_html.=$cricket.$cricket_final_html;

			//for tennis
			$html=''; $tennis_html='';
			if(!empty($match_array_data_tenis)){
				foreach ($match_array_data_tenis as $ck => $cdata) {
					$match_data = app('App\Http\Controllers\RestApi')->getSingleMatchData($cdata, $ck, 2);

					if(empty($match_data)){
						continue;
					}
					$match_detail = Match::where('match_id',$ck)->where('status',1)->first();
					if($match_data[0]['inplay'] == 1){
						if(isset($match_data[0]['runners'][0]['ex']['availableToBack'][0]['price']) && isset($match_data[0]['runners'][0]['ex']['availableToLay'][0]['price']))
						{
							$html.='<div class="secondblock-cricket active-block active-tag white-bg"><span class="fir-col1">
							<a href="matchDetail/'.$match_detail['id'].'" class="text-color-blue-light">'.$match_detail['match_name'].'</a><div class="mobileDate">'.date('d-m-y H:i',strtotime($match_detail['match_date'])).'</div>
							<div  class="text-color-green">In-Play</div>
							</span>';

							$html.='<span class="fir-col2">
							<a class="backbtn lightblue-bg2">'.@$match_data[0]['runners'][0]['ex']['availableToLay'][0]['price'].'</a>
							<a class="laybtn lightpink-bg1">'.@$match_data[0]['runners'][0]['ex']['availableToLay'][0]['price'].'</a>
							</span>';

						}
						else
						{
							$html.='<div class="secondblock-cricket active-block active-tag white-bg"><span class="fir-col1">
							<a href="matchDetail/'.$match_detail['id'].'" class="text-color-blue-light">'.$match_detail['match_name'].'</a><div class="mobileDate">'.date('d-m-y H:i',strtotime($match_detail['match_date'])).'</div>
							<div  class="text-color-green">In-Play</div>
							</span>';

							$html.='<span class="fir-col2">
							<a class="backbtn lightblue-bg2">--</a>
							<a class="laybtn lightpink-bg1">--</a>
							</span>';

						}
						if(isset($match_data[0]['runners'][2]['ex']['availableToBack'][0]['price']) && isset($match_data[0]['runners'][2]['ex']['availableToLay'][0]['price']))
						{
							$html.='<span class="fir-col2">
							<a class="backbtn lightblue-bg2">'.@$match_data[0]['runners'][2]['ex']['availableToBack'][0]['price'].'</a>
							<a class="laybtn lightpink-bg1">'.@$match_data[0]['runners'][2]['ex']['availableToLay'][0]['price'].'</a>
							</span>';
						}
						else
						{
							$html.='<span class="fir-col2">
							<a class="backbtn lightblue-bg2">--</a>
							<a class="laybtn lightpink-bg1">--</a>
							</span>';
						}
						if(isset($match_data[0]['runners'][1]['ex']['availableToBack'][0]['price']) && isset($match_data[0]['runners'][1]['ex']['availableToLay'][0]['price']))
						{
							$html.='<span class="fir-col2">
							<a class="backbtn lightblue-bg2">'.@$match_data[0]['runners'][1]['ex']['availableToBack'][0]['price'].'</a>
							<a class="laybtn lightpink-bg1">'.@$match_data[0]['runners'][1]['ex']['availableToLay'][0]['price'].'</a>
							</span>
							<span class="fir-col3">
								<a><img src="'.asset('asset/front/img/round-pin.png').'" > <img class="hover-img" src="public/asset/front/img/round-pin1.png" ></a>
							</span>
							</div>';
						}
						else
						{
							$html.='<span class="fir-col2">
							<a class="backbtn lightblue-bg2">--</a>
							<a class="laybtn lightpink-bg1">--</a>
							</span>
							<span class="fir-col3">
								<a><img src="'.asset('asset/front/img/round-pin.png').'" > <img class="hover-img" src="public/asset/front/img/round-pin1.png" ></a>
							</span>
							</div>';
						}
					}

				}
				$tennis_final_html.=$html;
			}else{
				$tennis_final_html='No match found.';
			}
			$final_html.="~~".$tennis.$tennis_final_html;

			$html=''; $soccer_html='';

			if(!empty($match_array_data_soccer)){
				foreach ($match_array_data_soccer as $ck => $cdata) {
					$match_data = app('App\Http\Controllers\RestApi')->getSingleMatchData($cdata, $ck, 1);

					if(empty($match_data)){
						continue;
					}
					$match_detail = Match::where('match_id',$ck)->where('status',1)->first();
					if($match_data[0]['inplay'] == 1){
						if(isset($match_data[0]['runners'][0]['ex']['availableToBack'][0]['price']) && isset($match_data[0]['runners'][0]['ex']['availableToLay'][0]['price']))
						{
							$html.='<div class="secondblock-cricket active-block active-tag white-bg"><span class="fir-col1">
							<a href="matchDetail/'.$match_detail['id'].'" class="text-color-blue-light">'.$match_detail['match_name'].'</a><div class="mobileDate">'.date('d-m-y H:i',strtotime($match_detail['match_date'])).'</div>
							<div  class="text-color-green">In-Play</div>
							</span>';

							$html.='<span class="fir-col2">
							<a class="backbtn lightblue-bg2">'.@$match_data[0]['runners'][0]['ex']['availableToLay'][0]['price'].'</a>
							<a class="laybtn lightpink-bg1">'.@$match_data[0]['runners'][0]['ex']['availableToLay'][0]['price'].'</a>
							</span>';

						}
						else
						{
							$html.='<div class="secondblock-cricket active-block active-tag white-bg"><span class="fir-col1">
							<a href="matchDetail/'.$match_detail['id'].'" class="text-color-blue-light">'.$match_detail['match_name'].'</a><div class="mobileDate">'.date('d-m-y H:i',strtotime($match_detail['match_date'])).'</div>
							<div  class="text-color-green">In-Play</div>
							</span>';

							$html.='<span class="fir-col2">
							<a class="backbtn lightblue-bg2">--</a>
							<a class="laybtn lightpink-bg1">--</a>
							</span>';

						}
						if(isset($match_data[0]['runners'][2]['ex']['availableToBack'][0]['price']) && isset($match_data[0]['runners'][2]['ex']['availableToLay'][0]['price']))
						{
							$html.='<span class="fir-col2">
							<a class="backbtn lightblue-bg2">'.@$match_data[0]['runners'][2]['ex']['availableToBack'][0]['price'].'</a>
							<a class="laybtn lightpink-bg1">'.@$match_data[0]['runners'][2]['ex']['availableToLay'][0]['price'].'</a>
							</span>';
						}
						else
						{
							$html.='<span class="fir-col2">
							<a class="backbtn lightblue-bg2">--</a>
							<a class="laybtn lightpink-bg1">--</a>
							</span>';
						}
						if(isset($match_data[0]['runners'][1]['ex']['availableToBack'][0]['price']) && isset($match_data[0]['runners'][1]['ex']['availableToLay'][0]['price']))
						{
							$html.='<span class="fir-col2">
							<a class="backbtn lightblue-bg2">'.@$match_data[0]['runners'][1]['ex']['availableToBack'][0]['price'].'</a>
							<a class="laybtn lightpink-bg1">'.@$match_data[0]['runners'][1]['ex']['availableToLay'][0]['price'].'</a>
							</span>
							<span class="fir-col3">
								<a><img src="'.asset('asset/front/img/round-pin.png').'" > <img class="hover-img" src="public/asset/front/img/round-pin1.png" ></a>
							</span>
							</div>';
						}
						else
						{
							$html.='<span class="fir-col2">
							<a class="backbtn lightblue-bg2">--</a>
							<a class="laybtn lightpink-bg1">--</a>
							</span>
							<span class="fir-col3">
								<a><img src="'.asset('asset/front/img/round-pin.png').'" > <img class="hover-img" src="public/asset/front/img/round-pin1.png" ></a>
							</span>
							</div>';
						}
					}

				}
				$soccer_final_html.=$html;
			}else{
				$soccer_final_html='No match found.';
			}
			$final_html.="~~".$soccer.$soccer_final_html;
		} */
        $final_html = '';
        return $final_html . "~~" . $val;
    }

    public function getInplayToday(Request $request)
    {
        $tdate = date('d-m-Y');
        $sports = Sport::all();
        $html = '';
        $i = 0;
        $final_html = '';
        $cricket_final_html = '';
        $tennis_final_html = '';
        $soccer_final_html = '';
        $match_array_data_cricket = array();
        $match_array_data_tenis = array();
        $match_array_data_soccer = array();

        foreach ($sports as $sport) {
            $match_link = Match::where('sports_id', $sport->sId)->where('status', 1)
                ->where('winner', NULL)->orderBy('match_date', 'ASC')->get();

            foreach ($match_link as $match) {
                $orgDate = $match->match_date;
                $newDate = date("d-m-Y", strtotime($orgDate));
                if (@$match->match_id != '' && $newDate == $tdate) {
                    if ($match->sports_id == 4)
                        $match_array_data_cricket[$match->match_id] = $match->event_id;
                    else if ($match->sports_id == 2)
                        $match_array_data_tenis[$match->match_id] = $match->match_id;
                    else if ($match->sports_id == 1)
                        $match_array_data_soccer[$match->match_id] = $match->match_id;
                }
            }
        }

        $mdata = array();
        $inplay = 0;
        if (!empty($match_array_data_cricket)) {
            foreach ($match_array_data_cricket as $ck => $cdata) {
                $match_data = app('App\Http\Controllers\RestApi')->getSingleMatchData($cdata, $ck, 4);
                $inplay_game = '';
                // if(isset($match_data['t1'][0][0]['inplay']) && $match_data['t1'][0][0]['inplay'] == True){
                $match_detail = Match::where('match_id', $ck)->where('status', 1)->first();

                $green_dot = 'fir-col1';
                if (isset($match_data['t1'][0][0]['iplay']) && $match_data['t1'][0][0]['iplay'] === 'True') {
                    $green_dot = 'fir-col1-green desk';
                }
                if (isset($match_data['t1'][0][0]['b1']) && isset($match_data['t1'][0][0]['l1'])) {
                    $html .= '<div class="secondblock-cricket active-block active-tag white-bg"><span class="' . $green_dot . '">
						<a href="matchDetail/' . $match_detail['id'] . '" class="text-color-blue-light">' . $match_detail['match_name'] . '</a>
						<div  class="text-color-green"></div>
						</span>';

                    $html .= '<span class="fir-col2">
						<a class="backbtn lightblue-bg2">' . $match_data['t1'][0][0]['b1'] . '</a>
						<a class="laybtn lightpink-bg1">' . $match_data['t1'][0][0]['l1'] . '</a>
						</span>';

                } else {
                    $html .= '<div class="secondblock-cricket active-block active-tag white-bg"><span class="' . $green_dot . '">
						<a href="matchDetail/' . $match_detail['id'] . '" class="text-color-blue-light">' . $match_detail['match_name'] . '</a>
						<div  class="text-color-green"></div>
						</span>';

                    $html .= '<span class="fir-col2">
						<a class="backbtn lightblue-bg2">--</a>
						<a class="laybtn lightpink-bg1">--</a>
						</span>';

                }
                if (isset($match_data['t1'][0][2]['b1'])) {
                    if (@$match_data['t1'][0][2]['b1'] != '' && @$match_data['t1'][0][2]['l1'] != '') {
                        $html .= '<span class="fir-col2">
						<a class="backbtn lightblue-bg2">' . $match_data['t1'][0][2]['b1'] . '</a>
						<a class="laybtn lightpink-bg1">' . $match_data['t1'][0][2]['l1'] . '</a>
						</span>';
                    } else {
                        $html .= '<span class="fir-col2">
						<a class="backbtn lightblue-bg2">--</a>
						<a class="laybtn lightpink-bg1">--</a>
						</span>';
                    }

                } else {
                    $html .= '<span class="fir-col2">
						<a class="backbtn lightblue-bg2">--</a>
						<a class="laybtn lightpink-bg1">--</a>
						</span>';
                }
                if (isset($match_data['t1'][0][1]['b1']) && isset($match_data['t1'][0][1]['l1'])) {
                    $html .= '<span class="fir-col2">
						<a class="backbtn lightblue-bg2">' . $match_data['t1'][0][1]['b1'] . '</a>
						<a class="laybtn lightpink-bg1">' . $match_data['t1'][0][1]['l1'] . '</a>
						</span>
						<span class="fir-col3">
							<a><img src="' . asset('asset/front/img/round-pin.png') . '" > <img class="hover-img" src="public/asset/front/img/round-pin1.png" ></a>
						</span>
						</div>';
                } else {
                    $html .= '<span class="fir-col2">
						<a class="backbtn lightblue-bg2">--</a>
						<a class="laybtn lightpink-bg1">--</a>
						</span>
						<span class="fir-col3">
							<a><img src="' . asset('asset/front/img/round-pin.png') . '" > <img class="hover-img" src="public/asset/front/img/round-pin1.png" ></a>
						</span>
						</div>';
                }
                // }
            }
            $cricket_final_html .= $html;
        } else {
            $cricket_final_html = 'No match found.';
        }
        $final_html .= $cricket_final_html;


        //for tennis
        $html = '';
        $tennis_html = '';
        if (!empty($match_array_data_tenis)) {
            foreach ($match_array_data_tenis as $ck => $cdata) {
                $match_data = app('App\Http\Controllers\RestApi')->getSingleMatchData($cdata, $ck, 2);

                if (empty($match_data)) {
                    continue;
                }
                $match_detail = Match::where('match_id', $ck)->where('status', 1)->first();
                // if($match_data[0]['inplay'] == 1){
                if (isset($match_data[0]['runners'][0]['ex']['availableToBack'][0]['price']) && isset($match_data[0]['runners'][0]['ex']['availableToLay'][0]['price'])) {
                    $html .= '<div class="secondblock-cricket active-block active-tag white-bg"><span class="' . $green_dot . '">
						<a href="matchDetail/' . $match_detail['id'] . '" class="text-color-blue-light">' . $match_detail['match_name'] . '</a>
						<div  class="text-color-green"></div>
						</span>';

                    $html .= '<span class="fir-col2">
						<a class="backbtn lightblue-bg2">' . @$match_data[0]['runners'][0]['ex']['availableToLay'][0]['price'] . '</a>
						<a class="laybtn lightpink-bg1">' . @$match_data[0]['runners'][0]['ex']['availableToLay'][0]['price'] . '</a>
						</span>';

                } else {
                    $html .= '<div class="secondblock-cricket active-block active-tag white-bg"><span class="' . $green_dot . '">
						<a href="matchDetail/' . $match_detail['id'] . '" class="text-color-blue-light">' . $match_detail['match_name'] . '</a>
						<div  class="text-color-green"></div>
						</span>';

                    $html .= '<span class="fir-col2">
						<a class="backbtn lightblue-bg2">--</a>
						<a class="laybtn lightpink-bg1">--</a>
						</span>';

                }
                if (isset($match_data[0]['runners'][2]['ex']['availableToBack'][0]['price']) && isset($match_data[0]['runners'][2]['ex']['availableToLay'][0]['price'])) {
                    $html .= '<span class="fir-col2">
						<a class="backbtn lightblue-bg2">' . @$match_data[0]['runners'][2]['ex']['availableToBack'][0]['price'] . '</a>
						<a class="laybtn lightpink-bg1">' . @$match_data[0]['runners'][2]['ex']['availableToLay'][0]['price'] . '</a>
						</span>';
                } else {
                    $html .= '<span class="fir-col2">
						<a class="backbtn lightblue-bg2">--</a>
						<a class="laybtn lightpink-bg1">--</a>
						</span>';
                }
                if (isset($match_data[0]['runners'][1]['ex']['availableToBack'][0]['price']) && isset($match_data[0]['runners'][1]['ex']['availableToLay'][0]['price'])) {
                    $html .= '<span class="fir-col2">
						<a class="backbtn lightblue-bg2">' . @$match_data[0]['runners'][1]['ex']['availableToBack'][0]['price'] . '</a>
						<a class="laybtn lightpink-bg1">' . @$match_data[0]['runners'][1]['ex']['availableToLay'][0]['price'] . '</a>
						</span>
						<span class="fir-col3">
							<a><img src="' . asset('asset/front/img/round-pin.png') . '" > <img class="hover-img" src="public/asset/front/img/round-pin1.png" ></a>
						</span>
						</div>';
                } else {
                    $html .= '<span class="fir-col2">
						<a class="backbtn lightblue-bg2">--</a>
						<a class="laybtn lightpink-bg1">--</a>
						</span>
						<span class="fir-col3">
							<a><img src="' . asset('asset/front/img/round-pin.png') . '" > <img class="hover-img" src="public/asset/front/img/round-pin1.png" ></a>
						</span>
						</div>';
                }
                // }

            }
            $tennis_final_html .= $html;
        } else {
            $tennis_final_html = 'No match found.';
        }
        $final_html .= "~~" . $tennis_final_html;

        $html = '';
        $soccer_html = '';

        if (!empty($match_array_data_soccer)) {
            foreach ($match_array_data_soccer as $ck => $cdata) {
                $match_data = app('App\Http\Controllers\RestApi')->getSingleMatchData($cdata, $ck, 1);

                if (empty($match_data)) {
                    continue;
                }
                $match_detail = Match::where('match_id', $ck)->where('status', 1)->first();
                // if($match_data[0]['inplay'] == 1){
                if (isset($match_data[0]['runners'][0]['ex']['availableToBack'][0]['price']) && isset($match_data[0]['runners'][0]['ex']['availableToLay'][0]['price'])) {
                    $html .= '<div class="secondblock-cricket active-block active-tag white-bg"><span class="' . $green_dot . '">
						<a href="matchDetail/' . $match_detail['id'] . '" class="text-color-blue-light">' . $match_detail['match_name'] . '</a>
						<div  class="text-color-green"></div>
						</span>';

                    $html .= '<span class="fir-col2">
						<a class="backbtn lightblue-bg2">' . @$match_data[0]['runners'][0]['ex']['availableToLay'][0]['price'] . '</a>
						<a class="laybtn lightpink-bg1">' . @$match_data[0]['runners'][0]['ex']['availableToLay'][0]['price'] . '</a>
						</span>';

                } else {
                    $html .= '<div class="secondblock-cricket active-block active-tag white-bg"><span class="' . $green_dot . '">
						<a href="matchDetail/' . $match_detail['id'] . '" class="text-color-blue-light">' . $match_detail['match_name'] . '</a>
						<div  class="text-color-green"></div>
						</span>';

                    $html .= '<span class="fir-col2">
						<a class="backbtn lightblue-bg2">--</a>
						<a class="laybtn lightpink-bg1">--</a>
						</span>';

                }
                if (isset($match_data[0]['runners'][2]['ex']['availableToBack'][0]['price']) && isset($match_data[0]['runners'][2]['ex']['availableToLay'][0]['price'])) {
                    $html .= '<span class="fir-col2">
						<a class="backbtn lightblue-bg2">' . @$match_data[0]['runners'][2]['ex']['availableToBack'][0]['price'] . '</a>
						<a class="laybtn lightpink-bg1">' . @$match_data[0]['runners'][2]['ex']['availableToLay'][0]['price'] . '</a>
						</span>';
                } else {
                    $html .= '<span class="fir-col2">
						<a class="backbtn lightblue-bg2">--</a>
						<a class="laybtn lightpink-bg1">--</a>
						</span>';
                }
                if (isset($match_data[0]['runners'][1]['ex']['availableToBack'][0]['price']) && isset($match_data[0]['runners'][1]['ex']['availableToLay'][0]['price'])) {
                    $html .= '<span class="fir-col2">
						<a class="backbtn lightblue-bg2">' . @$match_data[0]['runners'][1]['ex']['availableToBack'][0]['price'] . '</a>
						<a class="laybtn lightpink-bg1">' . @$match_data[0]['runners'][1]['ex']['availableToLay'][0]['price'] . '</a>
						</span>
						<span class="fir-col3">
							<a><img src="' . asset('asset/front/img/round-pin.png') . '" > <img class="hover-img" src="public/asset/front/img/round-pin1.png" ></a>
						</span>
						</div>';
                } else {
                    $html .= '<span class="fir-col2">
						<a class="backbtn lightblue-bg2">--</a>
						<a class="laybtn lightpink-bg1">--</a>
						</span>
						<span class="fir-col3">
							<a><img src="' . asset('asset/front/img/round-pin.png') . '" > <img class="hover-img" src="public/asset/front/img/round-pin1.png" ></a>
						</span>
						</div>';
                }
                // }

            }
            $soccer_final_html .= $html;
        } else {
            $soccer_final_html = 'No match found.';
        }
        $final_html .= "~~" . $soccer_final_html;

        return $final_html;
    }

    public function getInplayTomrw(Request $request)
    {
        $tdate = date('d-m-Y', strtotime("+1 day"));
        $sports = Sport::all();
        $html = '';
        $i = 0;
        $final_html = '';
        $cricket_final_html = '';
        $tennis_final_html = '';
        $soccer_final_html = '';
        $match_array_data_cricket = array();
        $match_array_data_tenis = array();
        $match_array_data_soccer = array();

        foreach ($sports as $sport) {
            $match_link = Match::where('sports_id', $sport->sId)->where('status', 1)
                ->where('winner', NULL)->orderBy('match_date', 'ASC')->get();

            foreach ($match_link as $match) {
                $orgDate = $match->match_date;
                $newDate = date("d-m-Y", strtotime($orgDate));
                if (@$match->match_id != '' && $newDate == $tdate) {
                    if ($match->sports_id == 4)
                        $match_array_data_cricket[$match->match_id] = $match->event_id;
                    else if ($match->sports_id == 2)
                        $match_array_data_tenis[$match->match_id] = $match->match_id;
                    else if ($match->sports_id == 1)
                        $match_array_data_soccer[$match->match_id] = $match->match_id;
                }
            }
        }
        $mdata = array();
        $inplay = 0;
        if (!empty($match_array_data_cricket)) {
            foreach ($match_array_data_cricket as $ck => $cdata) {
                $match_data = app('App\Http\Controllers\RestApi')->getSingleMatchData($cdata, $ck, 4);
                $inplay_game = '';
                // if(isset($match_data['t1'][0][0]['inplay']) && $match_data['t1'][0][0]['inplay'] == True){
                $match_detail = Match::where('match_id', $ck)->where('status', 1)->first();

                if (isset($match_data['t1'][0][0]['b1']) && isset($match_data['t1'][0][0]['l1'])) {
                    $html .= '<div class="secondblock-cricket active-block active-tag white-bg"><span class="fir-col1">
						<a href="matchDetail/' . $match_detail['id'] . '" class="text-color-blue-light">' . $match_detail['match_name'] . '</a>
						<div  class="text-color-green"></div>
						</span>';

                    $html .= '<span class="fir-col2">
						<a class="backbtn lightblue-bg2">' . $match_data['t1'][0][0]['b1'] . '</a>
						<a class="laybtn lightpink-bg1">' . $match_data['t1'][0][0]['l1'] . '</a>
						</span>';

                } else {
                    $html .= '<div class="secondblock-cricket active-block active-tag white-bg"><span class="fir-col1">
						<a href="matchDetail/' . $match_detail['id'] . '" class="text-color-blue-light">' . $match_detail['match_name'] . '</a>
						<div  class="text-color-green"></div>
						</span>';

                    $html .= '<span class="fir-col2">
						<a class="backbtn lightblue-bg2">--</a>
						<a class="laybtn lightpink-bg1">--</a>
						</span>';

                }
                if (isset($match_data['t1'][0][2]['b1'])) {
                    if (@$match_data['t1'][0][2]['b1'] != '' && @$match_data['t1'][0][1]['l1'] != '') {
                        $html .= '<span class="fir-col2">
						<a class="backbtn lightblue-bg2">' . $match_data['t1'][0][1]['b1'] . '</a>
						<a class="laybtn lightpink-bg1">' . $match_data['t1'][0][1]['l1'] . '</a>
						</span>';
                    } else {
                        $html .= '<span class="fir-col2">
						<a class="backbtn lightblue-bg2">--</a>
						<a class="laybtn lightpink-bg1">--</a>
						</span>';
                    }

                } else {
                    $html .= '<span class="fir-col2">
						<a class="backbtn lightblue-bg2">--</a>
						<a class="laybtn lightpink-bg1">--</a>
						</span>';
                }
                if (isset($match_data['t1'][0][1]['b1']) && isset($match_data['t1'][0][1]['l1'])) {
                    $html .= '<span class="fir-col2">
						<a class="backbtn lightblue-bg2">' . $match_data['t1'][0][1]['b1'] . '</a>
						<a class="laybtn lightpink-bg1">' . $match_data['t1'][0][1]['l1'] . '</a>
						</span>
						<span class="fir-col3">
							<a><img src="' . asset('asset/front/img/round-pin.png') . '" > <img class="hover-img" src="public/asset/front/img/round-pin1.png" ></a>
						</span>
						</div>';
                } else {
                    $html .= '<span class="fir-col2">
						<a class="backbtn lightblue-bg2">--</a>
						<a class="laybtn lightpink-bg1">--</a>
						</span>
						<span class="fir-col3">
							<a><img src="' . asset('asset/front/img/round-pin.png') . '" > <img class="hover-img" src="public/asset/front/img/round-pin1.png" ></a>
						</span>
						</div>';
                }
                // }
            }
            $cricket_final_html .= $html;
        } else {
            $cricket_final_html = 'No match found.';
        }
        $final_html .= $cricket_final_html;

        //for tennis
        $html = '';
        $tennis_html = '';
        if (!empty($match_array_data_tenis)) {
            foreach ($match_array_data_tenis as $ck => $cdata) {
                $match_data = app('App\Http\Controllers\RestApi')->getSingleMatchData($cdata, $ck, 2);

                if (empty($match_data)) {
                    continue;
                }
                $match_detail = Match::where('match_id', $ck)->where('status', 1)->first();
                // if($match_data[0]['inplay'] == 1){
                if (isset($match_data[0]['runners'][0]['ex']['availableToBack'][0]['price']) && isset($match_data[0]['runners'][0]['ex']['availableToLay'][0]['price'])) {
                    $html .= '<div class="secondblock-cricket active-block active-tag white-bg"><span class="fir-col1">
						<a href="matchDetail/' . $match_detail['id'] . '" class="text-color-blue-light">' . $match_detail['match_name'] . '</a>
						<div  class="text-color-green"></div>
						</span>';

                    $html .= '<span class="fir-col2">
						<a class="backbtn lightblue-bg2">' . @$match_data[0]['runners'][0]['ex']['availableToLay'][0]['price'] . '</a>
						<a class="laybtn lightpink-bg1">' . @$match_data[0]['runners'][0]['ex']['availableToLay'][0]['price'] . '</a>
						</span>';

                } else {
                    $html .= '<div class="secondblock-cricket active-block active-tag white-bg"><span class="fir-col1">
						<a href="matchDetail/' . $match_detail['id'] . '" class="text-color-blue-light">' . $match_detail['match_name'] . '</a>
						<div  class="text-color-green"></div>
						</span>';

                    $html .= '<span class="fir-col2">
						<a class="backbtn lightblue-bg2">--</a>
						<a class="laybtn lightpink-bg1">--</a>
						</span>';

                }
                if (isset($match_data[0]['runners'][2]['ex']['availableToBack'][0]['price']) && isset($match_data[0]['runners'][2]['ex']['availableToLay'][0]['price'])) {
                    $html .= '<span class="fir-col2">
						<a class="backbtn lightblue-bg2">' . @$match_data[0]['runners'][2]['ex']['availableToBack'][0]['price'] . '</a>
						<a class="laybtn lightpink-bg1">' . @$match_data[0]['runners'][2]['ex']['availableToLay'][0]['price'] . '</a>
						</span>';
                } else {
                    $html .= '<span class="fir-col2">
						<a class="backbtn lightblue-bg2">--</a>
						<a class="laybtn lightpink-bg1">--</a>
						</span>';
                }
                if (isset($match_data[0]['runners'][1]['ex']['availableToBack'][0]['price']) && isset($match_data[0]['runners'][1]['ex']['availableToLay'][0]['price'])) {
                    $html .= '<span class="fir-col2">
						<a class="backbtn lightblue-bg2">' . @$match_data[0]['runners'][1]['ex']['availableToBack'][0]['price'] . '</a>
						<a class="laybtn lightpink-bg1">' . @$match_data[0]['runners'][1]['ex']['availableToLay'][0]['price'] . '</a>
						</span>
						<span class="fir-col3">
							<a><img src="' . asset('asset/front/img/round-pin.png') . '" > <img class="hover-img" src="public/asset/front/img/round-pin1.png" ></a>
						</span>
						</div>';
                } else {
                    $html .= '<span class="fir-col2">
						<a class="backbtn lightblue-bg2">--</a>
						<a class="laybtn lightpink-bg1">--</a>
						</span>
						<span class="fir-col3">
							<a><img src="' . asset('asset/front/img/round-pin.png') . '" > <img class="hover-img" src="public/asset/front/img/round-pin1.png" ></a>
						</span>
						</div>';
                }
                // }

            }
            $tennis_final_html .= $html;
        } else {
            $tennis_final_html = 'No match found.';
        }
        $final_html .= "~~" . $tennis_final_html;

        $html = '';
        $soccer_html = '';

        if (!empty($match_array_data_soccer)) {
            foreach ($match_array_data_soccer as $ck => $cdata) {
                $match_data = app('App\Http\Controllers\RestApi')->getSingleMatchData($cdata, $ck, 1);

                if (empty($match_data)) {
                    continue;
                }
                $match_detail = Match::where('match_id', $ck)->where('status', 1)->first();
                // if($match_data[0]['inplay'] == 1){
                if (isset($match_data[0]['runners'][0]['ex']['availableToBack'][0]['price']) && isset($match_data[0]['runners'][0]['ex']['availableToLay'][0]['price'])) {
                    $html .= '<div class="secondblock-cricket active-block active-tag white-bg"><span class="fir-col1">
						<a href="matchDetail/' . $match_detail['id'] . '" class="text-color-blue-light">' . $match_detail['match_name'] . '</a>
						<div  class="text-color-green"></div>
						</span>';

                    $html .= '<span class="fir-col2">
						<a class="backbtn lightblue-bg2">' . @$match_data[0]['runners'][0]['ex']['availableToLay'][0]['price'] . '</a>
						<a class="laybtn lightpink-bg1">' . @$match_data[0]['runners'][0]['ex']['availableToLay'][0]['price'] . '</a>
						</span>';

                } else {
                    $html .= '<div class="secondblock-cricket active-block active-tag white-bg"><span class="fir-col1">
						<a href="matchDetail/' . $match_detail['id'] . '" class="text-color-blue-light">' . $match_detail['match_name'] . '</a>
						<div  class="text-color-green"></div>
						</span>';

                    $html .= '<span class="fir-col2">
						<a class="backbtn lightblue-bg2">--</a>
						<a class="laybtn lightpink-bg1">--</a>
						</span>';

                }
                if (isset($match_data[0]['runners'][2]['ex']['availableToBack'][0]['price']) && isset($match_data[0]['runners'][2]['ex']['availableToLay'][0]['price'])) {
                    $html .= '<span class="fir-col2">
						<a class="backbtn lightblue-bg2">' . @$match_data[0]['runners'][2]['ex']['availableToBack'][0]['price'] . '</a>
						<a class="laybtn lightpink-bg1">' . @$match_data[0]['runners'][2]['ex']['availableToLay'][0]['price'] . '</a>
						</span>';
                } else {
                    $html .= '<span class="fir-col2">
						<a class="backbtn lightblue-bg2">--</a>
						<a class="laybtn lightpink-bg1">--</a>
						</span>';
                }
                if (isset($match_data[0]['runners'][1]['ex']['availableToBack'][0]['price']) && isset($match_data[0]['runners'][1]['ex']['availableToLay'][0]['price'])) {
                    $html .= '<span class="fir-col2">
						<a class="backbtn lightblue-bg2">' . @$match_data[0]['runners'][1]['ex']['availableToBack'][0]['price'] . '</a>
						<a class="laybtn lightpink-bg1">' . @$match_data[0]['runners'][1]['ex']['availableToLay'][0]['price'] . '</a>
						</span>
						<span class="fir-col3">
							<a><img src="' . asset('asset/front/img/round-pin.png') . '" > <img class="hover-img" src="public/asset/front/img/round-pin1.png" ></a>
						</span>
						</div>';
                } else {
                    $html .= '<span class="fir-col2">
						<a class="backbtn lightblue-bg2">--</a>
						<a class="laybtn lightpink-bg1">--</a>
						</span>
						<span class="fir-col3">
							<a><img src="' . asset('asset/front/img/round-pin.png') . '" > <img class="hover-img" src="public/asset/front/img/round-pin1.png" ></a>
						</span>
						</div>';
                }
                // }

            }
            $soccer_final_html .= $html;
        } else {
            $soccer_final_html = 'No match found.';
        }
        $final_html .= "~~" . $soccer_final_html;
        return $final_html;
    }

    public function Inplaydata(Request $request)
    {
        $sports = Sport::all();
        $html = '';
        $i = 0;
        $final_html = '';
        $cricket_final_html = '';
        $tennis_final_html = '';
        $soccer_final_html = '';
        $top = '';
        $match_array_data_cricket = array();
        $match_array_data_tenis = array();
        $match_array_data_soccer = array();

        foreach ($sports as $sport) {
            $match_link = Match::where('sports_id', $sport->sId)->where('status', 1)->where('winner', NULL)->orderBy('match_date', 'ASC')->get();
            foreach ($match_link as $match) {
                if (@$match->match_id != '') {
                    if ($match->sports_id == 4)
                        $match_array_data_cricket[] = $match->match_id;
                    else if ($match->sports_id == 2)
                        $match_array_data_tenis[] = $match->match_id;
                    else if ($match->sports_id == 1)
                        $match_array_data_soccer[] = $match->match_id;
                }
            }
        }

        $imp_match_array_data_cricket = @implode(",", $match_array_data_cricket);
        $imp_match_array_data_tenis = @implode(",", $match_array_data_tenis);
        $imp_match_array_data_soccer = @implode(",", $match_array_data_soccer);

        $mdata = array();
        $inplay = 0;

        // inplay tab
        if (!empty($imp_match_array_data_cricket)) {
            $url = 'http://69.30.238.2:3644/odds/multiple?ids=' . $imp_match_array_data_cricket;
            $headers = array('Content-Type: application/json');
            $process = curl_init();
            curl_setopt($process, CURLOPT_URL, $url);
            curl_setopt($process, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($process, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($process, CURLOPT_TIMEOUT, 30);
            curl_setopt($process, CURLOPT_HTTPGET, 1);
            curl_setopt($process, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
            $return = curl_exec($process);
            curl_close($process);

            $match_data = json_decode($return, true);
            $cricket = '
			<div role="tabpanel" class="tab-pane active" id="inplay">

	        	<div class="programe-setcricket today_content">
	                <a class="todaytitle darkblue-bg text-color-white" data-toggle="collapse" href="#cricket-collapse" role="button" aria-expanded="false" aria-controls="cricket-collapse">
	                    Cricket <i class="fas fa-minus float-right" style="margin-top: 7px;margin-right: 5px;"></i>
	                </a>
	                <div class="collapse show" id="cricket-collapse">
	                    <div class="programe-setcricket">
	                        <div class="firstblock-cricket lightblue-bg1">
	                            <span class="fir-col1"></span>
	                            <span class="fir-col2">1</span>
	                            <span class="fir-col2">X</span>
	                            <span class="fir-col2">2</span>
	                            <span class="fir-col3"></span>
	                        </div>';

            $html = '';
            $cricket_html = '';
            if (!empty($match_data)) {
                for ($j = 0; $j < sizeof($match_data); $j++) {
                    $inplay_game = '';
                    $match_detail = Match::where('match_id', $match_data[$j]['marketId'])->where('status', 1)->first();
                    if (isset($match_data[$j]['inplay'])) {
                        if ($match_data[$j]['inplay'] == 1) {
                            if (isset($match_data[$j]['runners'][0]['ex']['availableToBack'][0]['price']) && isset($match_data[$j]['runners'][0]['ex']['availableToLay'][0]['price'])) {
                                $html .= '<div class="secondblock-cricket active-block active-tag white-bg"><span class="fir-col1">
									<a href="matchDetail/' . $match_detail['id'] . '" class="text-color-blue-light">' . $match_detail['match_name'] . '</a>
									<div  class="text-color-green">In-Play</div>
									</span>';

                                $html .= '<span class="fir-col2">
									<a class="backbtn lightblue-bg2">' . $match_data[$j]['runners'][0]['ex']['availableToLay'][0]['price'] . '</a>
									<a class="laybtn lightpink-bg1">' . $match_data[$j]['runners'][0]['ex']['availableToLay'][0]['price'] . '</a>
									</span>';
                            } else {
                                $html .= '<div class="secondblock-cricket active-block active-tag white-bg"><span class="fir-col1">
									<a href="matchDetail/' . $match_detail['id'] . '" class="text-color-blue-light">' . $match_detail['match_name'] . '</a>
									<div  class="text-color-green">In-Play</div>
									</span>';

                                $html .= '<span class="fir-col2">
									<a class="backbtn lightblue-bg2">--</a>
									<a class="laybtn lightpink-bg1">--</a>
									</span>';

                            }
                            if (isset($match_data[$j]['runners'][2])) {
                                $html .= '<span class="fir-col2">
									<a class="backbtn lightblue-bg2">' . $match_data[$j]['runners'][2]['ex']['availableToBack'][0]['price'] . '</a>
									<a class="laybtn lightpink-bg1">' . $match_data[$j]['runners'][2]['ex']['availableToLay'][0]['price'] . '</a>
									</span>';
                            } else {
                                $html .= '<span class="fir-col2">
									<a class="backbtn lightblue-bg2">--</a>
									<a class="laybtn lightpink-bg1">--</a>
									</span>';
                            }
                            if (isset($match_data[$j]['runners'][1]['ex']['availableToBack'][0]['price']) && isset($match_data[$j]['runners'][1]['ex']['availableToLay'][0]['price'])) {
                                $html .= '<span class="fir-col2">
									<a class="backbtn lightblue-bg2">' . $match_data[$j]['runners'][1]['ex']['availableToBack'][0]['price'] . '</a>
									<a class="laybtn lightpink-bg1">' . $match_data[$j]['runners'][1]['ex']['availableToLay'][0]['price'] . '</a>
									</span></div>';
                            } else {
                                $html .= '<span class="fir-col2">
									<a class="backbtn lightblue-bg2">--</a>
									<a class="laybtn lightpink-bg1">--</a>
									</span></div>';
                            }
                        }
                    }
                }
            }

            $cricket_html = '
                    </div>
                </div>
            </div>';

            $cricket_final_html .= $html;
        } else {
            $cricket_final_html = 'No match found.';
        }
        $final_html .= $cricket . $cricket_final_html;

        //for tennis
        $html = '';
        $tennis_html = '';
        if (!empty($imp_match_array_data_tenis)) {
            $url = 'http://69.30.238.2:3644/odds/multiple?ids=' . $imp_match_array_data_tenis;
            $headers = array('Content-Type: application/json');
            $process = curl_init();
            curl_setopt($process, CURLOPT_URL, $url);
            curl_setopt($process, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($process, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($process, CURLOPT_TIMEOUT, 30);
            curl_setopt($process, CURLOPT_HTTPGET, 1);
            curl_setopt($process, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
            $return = curl_exec($process);
            curl_close($process);
            $match_data = json_decode($return, true);
            $tennis = '
	        <div class="programe-setcricket today_content">
	            <a class="todaytitle darkblue-bg text-color-white" data-toggle="collapse" href="#cricket-collapse" role="button" aria-expanded="false" aria-controls="cricket-collapse">
	                Tennis <i class="fas fa-minus float-right" style="margin-top: 7px;margin-right: 5px;"></i>
	            </a>
	            <div class="collapse show" id="cricket-collapse">
	                <div class="programe-setcricket">
	                    <div class="firstblock-cricket lightblue-bg1">
	                        <span class="fir-col1"></span>
	                        <span class="fir-col2">1</span>
	                        <span class="fir-col2">X</span>
	                        <span class="fir-col2">2</span>
	                        <span class="fir-col3"></span>
	                    </div>';
            if (!empty($match_data)) {
                for ($j = 0; $j < sizeof($match_data); $j++) {
                    $inplay_game = '';
                    $match_detail = Match::where('match_id', $match_data[$j]['marketId'])->where('status', 1)->first();
                    if (isset($match_data[$j]['inplay'])) {
                        if ($match_data[$j]['inplay'] == 1) {
                            if (isset($match_data[$j]['runners'][0]['ex']['availableToBack'][0]['price']) && isset($match_data[$j]['runners'][0]['ex']['availableToLay'][0]['price'])) {
                                $html .= '<div class="secondblock-cricket active-block active-tag white-bg"><span class="fir-col1">
								<a href="matchDetail/' . $match_detail['id'] . '" class="text-color-blue-light">' . $match_detail['match_name'] . '</a>
								<div  class="text-color-green">In-Play</div>
								</span>';

                                $html .= '<span class="fir-col2">
								<a class="backbtn lightblue-bg2">' . $match_data[$j]['runners'][0]['ex']['availableToLay'][0]['price'] . '</a>
								<a class="laybtn lightpink-bg1">' . $match_data[$j]['runners'][0]['ex']['availableToLay'][0]['price'] . '</a>
								</span>';

                            } else {
                                $html .= '<div class="secondblock-cricket active-block active-tag white-bg"><span class="fir-col1">
								<a href="matchDetail/' . $match_detail['id'] . '" class="text-color-blue-light">' . $match_detail['match_name'] . '</a>
								<div  class="text-color-green">In-Play</div>
								</span>';

                                $html .= '<span class="fir-col2">
								<a class="backbtn lightblue-bg2">--</a>
								<a class="laybtn lightpink-bg1">--</a>
								</span>';

                            }
                            if (isset($match_data[$j]['runners'][2])) {
                                $html .= '<span class="fir-col2">
								<a class="backbtn lightblue-bg2">' . $match_data[$j]['runners'][2]['ex']['availableToBack'][0]['price'] . '</a>
								<a class="laybtn lightpink-bg1">' . $match_data[$j]['runners'][2]['ex']['availableToLay'][0]['price'] . '</a>
								</span>';
                            } else {
                                $html .= '<span class="fir-col2">
								<a class="backbtn lightblue-bg2">--</a>
								<a class="laybtn lightpink-bg1">--</a>
								</span>';
                            }
                            if (isset($match_data[$j]['runners'][1]['ex']['availableToBack'][0]['price']) && isset($match_data[$j]['runners'][1]['ex']['availableToLay'][0]['price'])) {
                                $html .= '<span class="fir-col2">
								<a class="backbtn lightblue-bg2">' . $match_data[$j]['runners'][1]['ex']['availableToBack'][0]['price'] . '</a>
								<a class="laybtn lightpink-bg1">' . $match_data[$j]['runners'][1]['ex']['availableToLay'][0]['price'] . '</a>
								</span></div>';
                            } else {
                                $html .= '<span class="fir-col2">
								<a class="backbtn lightblue-bg2">--</a>
								<a class="laybtn lightpink-bg1">--</a>
								</span></div>';
                            }
                        }
                    }
                }
            }
            $tennis_html .= '</div>
		        </div>
		    </div>';
            $tennis_final_html .= $html;
        } else {
            $tennis_final_html = 'No match found.';
        }
        $final_html .= $tennis . $tennis_final_html;

        //Soccer

        $html = '';
        $soccer_html = '';

        if (!empty($imp_match_array_data_soccer)) {

            $url = 'http://69.30.238.2:3644/odds/multiple?ids=' . $imp_match_array_data_soccer;
            $headers = array('Content-Type: application/json');
            $process = curl_init();
            curl_setopt($process, CURLOPT_URL, $url);
            curl_setopt($process, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($process, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($process, CURLOPT_TIMEOUT, 30);
            curl_setopt($process, CURLOPT_HTTPGET, 1);
            curl_setopt($process, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
            $return = curl_exec($process);
            curl_close($process);

            $match_data = json_decode($return, true);
            $soccer = '<div class="programe-setcricket today_content">
            <a class="todaytitle darkblue-bg text-color-white" data-toggle="collapse" href="#cricket-collapse" role="button" aria-expanded="false" aria-controls="cricket-collapse">
                Soccer <i class="fas fa-minus float-right" style="margin-top: 7px;margin-right: 5px;"></i>
            </a>
            <div class="collapse show" id="cricket-collapse">
                <div class="programe-setcricket">
                    <div class="firstblock-cricket lightblue-bg1">
                        <span class="fir-col1"></span>
                        <span class="fir-col2">1</span>
                        <span class="fir-col2">X</span>
                        <span class="fir-col2">2</span>
                        <span class="fir-col3"></span>
                    </div>';
            if (!empty($match_data)) {
                for ($j = 0; $j < sizeof($match_data); $j++) {
                    $inplay_game = '';
                    $match_detail = Match::where('match_id', $match_data[$j]['marketId'])->where('status', 1)->first();
                    if (isset($match_data[$j]['inplay'])) {
                        if ($match_data[$j]['inplay'] == 1) {
                            if (isset($match_data[$j]['runners'][0]['ex']['availableToBack'][0]['price']) && isset($match_data[$j]['runners'][0]['ex']['availableToLay'][0]['price'])) {
                                $html .= '<div class="secondblock-cricket active-block active-tag white-bg"><span class="fir-col1">
								<a href="matchDetail/' . $match_detail['id'] . '" class="text-color-blue-light">' . $match_detail['match_name'] . '</a>
								<div  class="text-color-green">In-Play</div>
								</span>';

                                $html .= '<span class="fir-col2">
								<a class="backbtn lightblue-bg2">' . @$match_data[$j]['runners'][0]['ex']['availableToLay'][0]['price'] . '</a>
								<a class="laybtn lightpink-bg1">' . @$match_data[$j]['runners'][0]['ex']['availableToLay'][0]['price'] . '</a>
								</span>';
                            } else {
                                $html .= '<div class="secondblock-cricket active-block active-tag white-bg"><span class="fir-col1">
								<a href="matchDetail/' . $match_detail['id'] . '" class="text-color-blue-light">' . $match_detail['match_name'] . '</a>
								<div  class="text-color-green">In-Play</div>
								</span>';

                                $html .= '<span class="fir-col2">
								<a class="backbtn lightblue-bg2">--</a>
								<a class="laybtn lightpink-bg1">--</a>
								</span>';
                            }
                            if (isset($match_data[$j]['runners'][2]['ex']['availableToBack'][0]['price']) && isset($match_data[$j]['runners'][2]['ex']['availableToLay'][0]['price'])) {
                                $html .= '<span class="fir-col2">
								<a class="backbtn lightblue-bg2">' . @$match_data[$j]['runners'][2]['ex']['availableToBack'][0]['price'] . '</a>
								<a class="laybtn lightpink-bg1">' . @$match_data[$j]['runners'][2]['ex']['availableToLay'][0]['price'] . '</a>
								</span>';
                            } else {
                                $html .= '<span class="fir-col2">
								<a class="backbtn lightblue-bg2">--</a>
								<a class="laybtn lightpink-bg1">--</a>
								</span>';
                            }
                            if (isset($match_data[$j]['runners'][1]['ex']['availableToBack'][0]['price']) && isset($match_data[$j]['runners'][1]['ex']['availableToLay'][0]['price'])) {
                                $html .= '<span class="fir-col2">
								<a class="backbtn lightblue-bg2">' . @$match_data[$j]['runners'][1]['ex']['availableToBack'][0]['price'] . '</a>
								<a class="laybtn lightpink-bg1">' . @$match_data[$j]['runners'][1]['ex']['availableToLay'][0]['price'] . '</a>
								</span></div>';
                            } else {
                                $html .= '<span class="fir-col2">
								<a class="backbtn lightblue-bg2">--</a>
								<a class="laybtn lightpink-bg1">--</a>
								</span></div>';
                            }
                        }
                    }
                }
            } else {
                $soccer_final_html = 'No match found.';
            }
            $soccer_html .= '
	                    </div>
	                </div>
	            </div>
	        </div>';
            $soccer_final_html .= $html;
        } else {
            $soccer_final_html = 'No match found.';
        }
        $final_html .= $soccer . $soccer_final_html;

        // end inplay tab

        //today tab
        $tdate = date('d-m-Y');
        $sports = Sport::all();
        $html = '';
        $i = 0;
        $final_html = '';
        $cricket_final_html = '';
        $tennis_final_html = '';
        $soccer_final_html = '';
        $match_array_data_cricket = array();
        $match_array_data_tenis = array();
        $match_array_data_soccer = array();

        foreach ($sports as $sport) {
            $match_link = Match::where('sports_id', $sport->sId)->where('status', 1)
                ->where('winner', NULL)->orderBy('match_date', 'ASC')->get();

            foreach ($match_link as $match) {
                $orgDate = $match->match_date;
                $newDate = date("d-m-Y", strtotime($orgDate));
                if (@$match->match_id != '' && $newDate == $tdate) {
                    if ($match->sports_id == 4)
                        $match_array_data_cricket[] = $match->match_id;
                    else if ($match->sports_id == 2)
                        $match_array_data_tenis[] = $match->match_id;
                    else if ($match->sports_id == 1)
                        $match_array_data_soccer[] = $match->match_id;
                }
            }
        }
        $imp_match_array_data_cricket = @implode(",", $match_array_data_cricket);
        $imp_match_array_data_tenis = @implode(",", $match_array_data_tenis);
        $imp_match_array_data_soccer = @implode(",", $match_array_data_soccer);

        $mdata = array();
        $inplay = 0;
        if (!empty($imp_match_array_data_cricket)) {
            $url = 'http://69.30.238.2:3644/odds/multiple?ids=' . $imp_match_array_data_cricket;
            $headers = array('Content-Type: application/json');
            $process = curl_init();
            curl_setopt($process, CURLOPT_URL, $url);
            curl_setopt($process, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($process, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($process, CURLOPT_TIMEOUT, 30);
            curl_setopt($process, CURLOPT_HTTPGET, 1);
            curl_setopt($process, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
            $return = curl_exec($process);
            curl_close($process);

            $match_data = json_decode($return, true);

            $html = '';
            $cricket_html = '';
            $html .= '<div role="tabpanel" class="tab-pane" id="today">
                <div class="programe-setcricket today_content">
                    <a class="todaytitle darkblue-bg text-color-white" data-toggle="collapse" href="#cricket-collapse" role="button" aria-expanded="false" aria-controls="cricket-collapse">
                        Cricket <i class="fas fa-minus float-right" style="margin-top: 7px;margin-right: 5px;"></i>
                    </a>
                    <div class="collapse show" id="cricket-collapse">
                        <div class="programe-setcricket">';
            if (!empty($match_data)) {
                for ($j = 0; $j < sizeof($match_data); $j++) {
                    $match_detail = Match::where('match_id', $match_data[$j]['marketId'])->where('status', 1)->first();
                    $html .= '
                            <div class="secondblock-cricket active-block active-tag white-bg">
                                <span class="fir-col1">
                                    <a href="matchDetail/' . $match_detail['id'] . ' class="text-color-blue-light">' . $match_detail['match_name'] . '</a>
                                </span>
                            </div>';
                }
            }
            $html .= '</div>
                </div>
            </div>';
            $cricket_final_html .= $html;
        } else {
            $cricket_final_html = 'No match found.';
        }
        $final_html .= $cricket_final_html;

        //for tennis
        $html = '';
        $tennis_html = '';
        if (!empty($imp_match_array_data_tenis)) {
            $url = 'http://69.30.238.2:3644/odds/multiple?ids=' . $imp_match_array_data_tenis;
            $headers = array('Content-Type: application/json');
            $process = curl_init();
            curl_setopt($process, CURLOPT_URL, $url);
            curl_setopt($process, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($process, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($process, CURLOPT_TIMEOUT, 30);
            curl_setopt($process, CURLOPT_HTTPGET, 1);
            curl_setopt($process, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
            $return = curl_exec($process);
            curl_close($process);
            $match_data = json_decode($return, true);

            $html .= '<div class="programe-setcricket today_content">
                    <a class="todaytitle darkblue-bg text-color-white" data-toggle="collapse" href="#cricket-collapse" role="button" aria-expanded="false" aria-controls="cricket-collapse">
                        Tennis <i class="fas fa-minus float-right" style="margin-top: 7px;margin-right: 5px;"></i>
                    </a>
                    <div class="collapse show" id="cricket-collapse">
                        <div class="programe-setcricket">';

            if (!empty($match_data)) {
                for ($j = 0; $j < sizeof($match_data); $j++) {
                    $match_detail = Match::where('match_id', $match_data[$j]['marketId'])->where('status', 1)->first();

                    $html .= '
                            <div class="secondblock-cricket active-block active-tag white-bg">
                                <span class="fir-col1">
                                    <a href="matchDetail/' . $match_detail['id'] . ' class="text-color-blue-light">' . $match_detail['match_name'] . '</a>
                                </span>
                            </div>';
                }
            }
            $html .= ' </div>
                    </div>
                </div>';
            $tennis_final_html .= $html;
        } else {
            $tennis_final_html = 'No match found.';
        }
        $final_html .= "~~" . $tennis_final_html;

        $html = '';
        $soccer_html = '';

        if (!empty($imp_match_array_data_soccer)) {

            $url = 'http://69.30.238.2:3644/odds/multiple?ids=' . $imp_match_array_data_soccer;
            $headers = array('Content-Type: application/json');
            $process = curl_init();
            curl_setopt($process, CURLOPT_URL, $url);
            curl_setopt($process, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($process, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($process, CURLOPT_TIMEOUT, 30);
            curl_setopt($process, CURLOPT_HTTPGET, 1);
            curl_setopt($process, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($process, CURLOPT_RETURNTRANSFER, TRUE);
            $return = curl_exec($process);
            curl_close($process);

            $match_data = json_decode($return, true);

            $html .= '
			<div class="programe-setcricket today_content">
                <a class="todaytitle darkblue-bg text-color-white" data-toggle="collapse" href="#cricket-collapse" role="button" aria-expanded="false" aria-controls="cricket-collapse">
                    Soccer <i class="fas fa-minus float-right" style="margin-top: 7px;margin-right: 5px;"></i>
                </a>
                <div class="collapse show" id="cricket-collapse">
                    <div class="programe-setcricket">
			';
            if (!empty($match_data)) {
                for ($j = 0; $j < sizeof($match_data); $j++) {
                    $inplay_game = '';
                    $match_detail = Match::where('match_id', $match_data[$j]['marketId'])->where('status', 1)->first();
                    $html .= '
                        <div class="secondblock-cricket active-block active-tag white-bg">
                            <span class="fir-col1">
                                <a href="matchDetail/' . $match_detail['id'] . '" class="text-color-blue-light">' . $match_detail['match_name'] . '</a>
                            </span>
                        </div>';
                }
            }
            $html .= ' </div>
                    </div>
                </div>
            </div>';
            $soccer_final_html .= $html;
        } else {
            $soccer_final_html = 'No match found.';
        }
        $final_html .= "~~" . $soccer_final_html;
        //end today tab

        return $final_html;
    }

    function search($array, $key, $value)
    {
        $results = array();

        if (is_array($array)) {
            if (isset($array[$key]) && $array[$key] == $value) {
                $results[] = $array;
            }

            foreach ($array as $subarray) {
                $results = array_merge($results, $this->search($subarray, $key, $value));
            }
        }

        return $results;
    }

    public function cricket()
    {

        $match_type = 4;
        $matches = Match::where('sports_id', $match_type)->where('status', 1)->where('winner', NULL)->pluck('id','event_id');
        $getUserCheck = Session::get('playerUser');

        if(!empty($getUserCheck)) {
            $favMatches = UsersFavMatch::where("user_id", $getUserCheck->id)->pluck('match_id');
        }else{
            $favMatches = UsersFavMatch::where("user_id", 0)->pluck('match_id');
        }
        return view('front.match-list', compact('matches','match_type','favMatches'));
    }

    public function getmatchdetailsOfCricket()
    {
        return $this->getMatchListingByTypes(4, 1);
    }

    //soccer
    public function soccer()
    {
        $match_type = 1;
        $matches = Match::where('sports_id', $match_type)->where('status', 1)->where('winner', NULL)->pluck('id','event_id');
        $getUserCheck = Session::get('playerUser');

        if(!empty($getUserCheck)) {
            $favMatches = UsersFavMatch::where("user_id", $getUserCheck->id)->pluck('match_id');
        }else{
            $favMatches = UsersFavMatch::where("user_id", 0)->pluck('match_id');
        }
        return view('front.match-list', compact('matches','match_type','favMatches'));
    }

    public function getmatchdetailsOfSoccer()
    {
        return $this->getMatchListingByTypes(1, 1);
    }

    //tennis
    public function tennis()
    {
        $match_type=2;
        $matches = Match::where('sports_id', $match_type)->where('status', 1)->where('winner', NULL)->pluck('id','event_id');
        $getUserCheck = Session::get('playerUser');

        if(!empty($getUserCheck)) {
            $favMatches = UsersFavMatch::where("user_id", $getUserCheck->id)->pluck('match_id');
        }else{
            $favMatches = UsersFavMatch::where("user_id", 0)->pluck('match_id');
        }
        return view('front.match-list', compact('matches','match_type','favMatches'));
    }

    public function getmatchdetailsOfTennis()
    {
        return $this->getMatchListingByTypes(2, 1);
    }

    public function getMatchListingByTypes($type, $is_interval = 0)
    {
        $useragent = $_SERVER['HTTP_USER_AGENT'];
        $iPod = stripos($useragent, "iPod");
        $iPad = stripos($useragent, "iPad");
        $iPhone = stripos($useragent, "iPhone");
        $Android = stripos($useragent, "Android");
        $iOS = stripos($useragent, "iOS");

        $DEVICE = ($iPod || $iPad || $iPhone || $Android || $iOS);
        $is_agent = '';
        if (!$DEVICE) {
            $is_agent = 'desktop';
        } else {
            $is_agent = 'mobile';
        }

        $i = 0;
        $final_html = '';
        $cricket_final_html = '';
        $mdata = array();
        $inplay = 0;
        $cricket_html = '';

        $matches = Match::where('sports_id', $type)->where('status', 1)->where('winner', NULL)->orderBy('match_date', 'ASC')->get();
        $match_data = app('App\Http\Controllers\RestApi')->GetAllMatch($type);

        $arrayB = $this->search($match_data, 'inPlay', 'True');
        $match_data_merge = array_merge($arrayB, $match_data);
        $match_data_arrange = array_unique($match_data_merge, SORT_REGULAR);
        $match_data = array_values($match_data_arrange);
//        echo "<pre>";
//        print_r($match_data);
//        exit;

        if ($is_interval) {
            $cricket_html = '<div class="firstblock-cricket lightblue-bg1">
                                <span class="fir-col1"></span>
                                <span class="fir-col2">1</span>
                                <span class="fir-col2">X</span>
                                <span class="fir-col2">2</span>
                                <span class="fir-col3"></span>
                            </div>';
        }

        if (!empty($match_data)) {
            for ($j = 0; $j < sizeof($match_data); $j++) {
                foreach ($matches as $value) {
                    if ($value->match_id == $match_data[$j]['marketId']) {
                        $html = '';
                        $inplay_game = '';
                        $mobileInplay = '';
                        if (isset($match_data[$j]['inPlay'])) {
                            if ($match_data[$j]['inPlay'] === 'True') {
                                $dt = '';
                                $style = "fir-col1-green";
                                $inplay_game = " <span style='color:green' class='deskinplay' >In-Play</span>";
                                $mobileInplay = '<span style="color:green" class="mplay">In-Play</span>';
                            } else {
                                $match_date = '';
                                $dt = '';
                                $eventNameExp = explode('/', $match_data[$j]['eventName']);
                                $event = trim($eventNameExp[0]);
                                $startTime = trim(end($eventNameExp));
                                $date = Carbon::parse($startTime);

                                if (Carbon::parse($date)->isToday()) {
                                    $match_date = date('h:i A', strtotime($date));
                                } else if (Carbon::parse($date)->isTomorrow()) {
                                    $match_date = 'Tomorrow ' . date('h:i A', strtotime($date));
                                } else {
                                    $match_date = date('d-m-Y h:i A', strtotime($date));
                                }

                                $dt = $match_date;
                                $style = "fir-col1";
                                $inplay_game = '';
                            }
                        }
                        else {
                            $dt = date("d-m-Y h:i A", strtotime($value->match_date));
                            $style = "fir-col1";
                            $inplay_game = '';
                            $mobileInplay = '';
                        }

                        $fancy = '';
                        $mobileFancy = '';
                        if ($type == 4) { // for cricket only
                            if ($match_data[$j]['f'] === 'True' && $inplay_game != '')
                                $fancy = '<span style="color:green" class="game-fancy in-play blue-bg-3 text-color-white">F</span>';
                            elseif ($match_data[$j]['f'] === 'True' && $inplay_game == '')
                                $fancy = '<span style="color:green" class="game-fancy in-play blue-bg-3 text-color-white">F</span>';

                            if ($match_data[$j]['f'] === 'True' && $mobileInplay != '')
                                $mobileFancy = '<span style="color:green" class="game-fancy in-play blue-bg-3 text-color-white">F</span>';
                            elseif ($match_data[$j]['f'] === 'True' && $mobileInplay == '')
                                $mobileFancy = '<span style="color:green" class="game-fancy in-play blue-bg-3 text-color-white">F</span>';
                        }

                        if ($is_agent == 'mobile') {
                            $matchName = substr($value['match_name'], 0, 36) . '...';
                        } else {
                            $matchName = $value['match_name'];
                        }

                        $bookmaker = '';
                        $mobileBookmaker = '';
                        if ($type == 4) { // for cricket only
                            if ($match_data[$j]['m1'] === 'True') {
                                $bookmaker = '<span style="color:green;margin-right: 40px;" class="game-fancy in-play blue-bg-3 text-color-white">B</span>';
                                $mobileBookmaker = '<span style="color:green" class="game-fancy in-play blue-bg-3 text-color-white">B</span>';
                            }
                        }

                        $mb_html = '';
                        if (isset($match_data[$j]['back1']) && $match_data[$j]['back1'] > 0) {
                            $mb_html = '<div class="mblinplay">
											' . $mobileFancy . '
											' . $mobileBookmaker . '
											' . $mobileInplay . '
										</div>';
                        }
                        $back1 = (isset($match_data[$j]['back1']) && $match_data[$j]['back1'] > 0) ? $match_data[$j]['back1'] : '--';
                        $lay1 = (isset($match_data[$j]['lay1']) && $match_data[$j]['lay1'] > 0) ? $match_data[$j]['lay1'] : '--';

                        $html .= '
							<div class="secondblock-cricket white-bg">
								' . $mb_html . '
								<span class="' . $style . ' desk">
									<a href="matchDetail/' . $value['id'] . '" class="text-color-blue-light">' . $matchName . $inplay_game . '</a>
									<div class="mobileDate">' . $dt . '</div>' . $bookmaker . $fancy . '
								</span>
								<span class="fir-col2">
									<a class="backbtn lightblue-bg2">' . $back1 . '</a>
									<a class="laybtn lightpink-bg1">' . $lay1 . '</a>
								</span>';


                        $back12 = (isset($match_data[$j]['back12']) && $match_data[$j]['back12'] > 0) ? $match_data[$j]['back12'] : '--';
                        $lay12 = (isset($match_data[$j]['lay12']) && $match_data[$j]['lay12'] > 0) ? $match_data[$j]['lay12'] : '--';

                        $html .= '<span class="fir-col2">
											<a class="backbtn lightblue-bg2">' . @$back12 . '</a>
											<a class="laybtn lightpink-bg1">' . @$lay12 . '</a>
										</span>';

                        $back11 = (isset($match_data[$j]['back11']) && $match_data[$j]['back11'] > 0) ? $match_data[$j]['back11'] : '--';
                        $lay11 = (isset($match_data[$j]['lay11']) && $match_data[$j]['lay11'] > 0) ? $match_data[$j]['lay11'] : '--';


                        $imagePath = "asset/front/img/round-pin.png";
                        $getUserCheck = Session::get('playerUser');
                        if(!empty($getUserCheck)){
                            $isFav = UsersFavMatch::where("user_id",$getUserCheck->id)->where("match_id",$value->id)->first();
                            if(!empty($isFav)){
                                $imagePath = "asset/front/img/round-pin1.png";
                            }
                        }

                        $html .= '<span class="fir-col2">
											<a class="backbtn lightblue-bg2">' . $back11 . '</a>
											<a class="laybtn lightpink-bg1">' . $lay11 . '</a>
										</span>
										<span class="fir-col3 text-center">
											<a data-id="'.$value->id.'" class="cricket-pin make-fav-match pin_'.$value->id.'"><img class="unpin-img" src="' . asset($imagePath) . '"><img class="pin-img hover-img" src="'.asset('asset/front/img/round-pin1.png').'"></a>
										</span>
                        	</div>';

                        $cricket_html .= $html;
                    }
                }
            }
        }

        return $cricket_html;
    }

    public function userFavMatch(Request $request){
        $getUserCheck = Session::get('playerUser');
        if(!empty($getUserCheck)){
            $isFav = UsersFavMatch::where("user_id",$getUserCheck->id)->where("match_id",$request->id)->first();

            if(!empty($isFav)){
                UsersFavMatch::where("user_id",$getUserCheck->id)->where("match_id",$request->id)->delete();
                $fav = 'remove';
            }else{
                UsersFavMatch::create(["user_id"=>$getUserCheck->id,"match_id"=>$request->id]);
                $fav = 'added';
            }

            return response()->json(array('result' => $fav));
        }else{
            return response()->json(array('result' => 'login'));
        }
    }

    public function getleftpanelMenu()
    {
        $html = '';
        $sports = Sport::all();
        foreach ($sports as $sport) {
            $html .= '<li>
            <a href="#homeSubmenu_' . $sport->sId . '" class="text-color-black2" data-toggle="collapse" aria-expanded="false">' . $sport->sport_name . '</a>
            <a href="#homeSubmenu_' . $sport->sId . '" data-toggle="collapse" aria-expanded="false">
                <img src="' . asset("asset/front/img/leftmenu-arrow3.png") . '" class="hoverleft"><img class="hover-img" src="' . asset('asset/front/img/leftmenu-arrow4.png') . '">
            </a>
            <ul class="dropul white-bg list-unstyled collapse" id="homeSubmenu_' . $sport->sId . '">';

            $sId = $sport->sId;
            //$match_data=app('App\Http\Controllers\RestApi')->GetAllMatch();
            $match_data = Match::where('sports_id', $sport->sId)->where('status', 1)->where('suspend_m', 1)->where('status_m', 1)->where('isDeleted', 0)->where('winner', NULL)->orderBy('match_date', 'ASC')->groupBy('leage_name')->get();
            $leage = array();

            if (!empty($match_data)) {
                foreach ($match_data as $value) {
                    $html .= '<li>
                            <a href="#homeSubmenu1_' . str_replace(' ', '_', $value->leage_name) . '" data-toggle="collapse" aria-expanded="false" class="text-color-black2">' . $value->leage_name . '</a>
                            <a href="#homeSubmenu1_' . str_replace(' ', '_', $value->leage_name) . '" data-toggle="collapse" aria-expanded="false">
                                <img src="' . asset('asset/front/img/leftmenu-arrow3.png') . '" class="hoverleft"><img class="hover-img" src="' . asset('asset/front/img/leftmenu-arrow4.png') . '">
                            </a>
                            <ul class="dropul white-bg list-unstyled collapse" id="homeSubmenu1_' . str_replace(' ', '_', $value->leage_name) . '">';

                    $match_data_result = Match::where('sports_id', $sport->sId)->where('status', 1)->where('suspend_m', 1)->where('status_m', 1)->where('isDeleted', 0)->where('winner', NULL)->where('leage_name', $value->leage_name)->orderBy('match_date', 'ASC')->get();

                    if (!empty($match_data_result)) {
                        foreach ($match_data_result as $matches_leage) {
                            $html .= '<li>
											<a href="#homeSubmenu2_' . $matches_leage->event_id . '" data-toggle="collapse" aria-expanded="false" class="text-color-black2">' . $matches_leage->match_name . '</a>
											<a href="#homeSubmenu2_' . $matches_leage->event_id . '" data-toggle="collapse" aria-expanded="false">
												<img src="' . asset('asset/front/img/leftmenu-arrow3.png') . '" class="hoverleft"><img class="hover-img" src="' . asset('asset/front/img/leftmenu-arrow4.png') . '">
											</a>
											<ul class="dropul white-bg list-unstyled collapse" id="homeSubmenu2_' . $matches_leage->event_id . '">
												<li>
													<a class="text-color-black2 w-100" href="matchDetail/' . $matches_leage->id . '"> <img src="' . asset('asset/front/img/green-dots.png') . '"> Match Odds</a>
												</li>
											</ul>
										</li>';
                        }
                    }


                    $html .= '</ul>
                        </li>';
                }
            }
            $html .= '</ul>
				</li>';
        }
        echo $html;
    }

    public function myprofile()
    {
        $getUserCheck = Session::get('playerUser');
        if (!empty($getUserCheck)) {
            $loginuser = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
        }
        $user = User::where('id', $loginuser->id)->first();
        return view('front.myprofile', compact('user'));
    }

    public function balanceoverview()
    {
        $getUserCheck = Session::get('playerUser');
        if (!empty($getUserCheck)) {
            $loginuser = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
        }
        $user = User::where('id', $loginuser->id)->first();
        return view('front.balance-overview', compact('user'));
    }

    public function accountstatement()
    {
        $getUserCheck = Session::get('playerUser');
        if (!empty($getUserCheck)) {
            $loginuser = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
        }
        $credit = UserDeposit::where(['child_id' => $loginuser->id, 'parent_id' => $loginuser->parentid])->latest()->get();

        $player_balance = CreditReference::where('player_id', $loginuser->id)->first();
        $player_balance = $player_balance['remain_bal'];
        return view('front.account-statement', compact('loginuser', 'credit', 'player_balance'));
    }

    /*public function array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {

		//echo "fjkgbv";
        $sort_col = array();

       //echo"<pre>";print_r($arr);
        foreach ($arr as $key=> $row) {
            $sort_col[$key] = $row[$col];
        }

        //echo"<pre>";print_r($sort_col);

       array_multisort($sort_col, $dir, $arr);
    }*/
    public function accountstmtdata(Request $request)
    {
        $getUserCheck = Session::get('playerUser');
        if (!empty($getUserCheck)) {
            $loginuser = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
        }

        $player_balance = CreditReference::where('player_id', $loginuser->id)->first();
        $player_balance = $player_balance['remain_bal'];

        $settings = CreditReference::where('player_id', $loginuser->id)->first();
        $balance = $settings['available_balance_for_D_W'];

        /*if(empty($request->dateto)){
	    	$todate = date("Y-m-d", strtotime("+1 day"));
	    }
	    if(empty($request->datefrom)){
	    	$fromdate = date("Y-m-d");
	    }*/
        if ($request->datefrom) {
            $fromdate = date('Y-m-d', strtotime($request->datefrom));
        }
        if ($request->dateto) {
            $todate = date('Y-m-d', strtotime($request->dateto));
        }

        if ($request->datefrom == $request->dateto) {
            $fromdate = date('Y-m-d', strtotime($request->datefrom));
            $todate = date('Y-m-d', strtotime($request->datefrom . "+1 day"));
        }

        /*echo $fromdate;
	    echo "/";
	    echo $todate;
	    exit;*/

        $drpval = $request->drpval;
        $html = '';

        if ($drpval == '0') {

            $draw = $request->get('draw');
            $start = $request->get("start");

            $rowperpage = $request->get("length");

            $columnIndex_arr = $request->get('order');
            $columnName_arr = $request->get('columns');
            $order_arr = $request->get('order');
            $search_arr = $request->get('search');

            $searchValue = $search_arr['value']; // Search value

            $credit = UserDeposit::where(['child_id' => $loginuser->id, 'parent_id' => $loginuser->parentid])
                ->whereBetween('created_at', [$fromdate, $todate])
                ->orderBy('created_at')
//                ->skip($start)->take($rowperpage)
                ->get();


            $betdata = MyBets::where('user_id', $loginuser->id)
                ->where('result_declare', 1)
                ->where('isDeleted', 0)
                ->whereBetween('created_at', [$fromdate, $todate])
                ->groupBy('match_id')
                ->orderBy('created_at')
//                ->skip($start)->take($rowperpage)
                ->get();


            $merged = $betdata->merge($credit)->sortBy('created_at');

//            dd($merged->toArray());

            $result = $merged->all();

            $merged1 = $betdata->merge($credit)->sortBy('created_at');
            $getresultcount = $merged1->all();

            $getresultcounttot = count($getresultcount);
            $totalRecords = $getresultcounttot;

            $ttlAmt = 0;
            $ttlAmto = 0;
            $ttlAmtb = 0;
            $i = 2;
            $blnc = 0;
            $sumAmt = 0;
            $sumAmto = 0;
            $sumAmtb = 0;
            $commn = 0;
            $date = '<span class="sorting_1"></span>';
            $srno = '<span class="text-right">1</span>';
            $credit = '<span class="text-right text-success">' . $blnc . '</span>';
            $debit = '<span class="text-right text-danger"></span>';
            $balance = '<span class="text-right text-success">' . $blnc . '</span>';
            $remark = '<span>Opening Balance</span>';

            $data_arr[] = array(
                "date" => $date,
                "srno" => $srno,
                "credit" => $credit,
                "debit" => $debit,
                "balance" => $balance,
                "remark" => $remark
            );

            $next_row_balance = $blnc;

            foreach ($result as $key => $data1) {
                $mthnm = Match::where('event_id', $data1['match_id'])->first();

                if ($mthnm) {
                    $sprtnm = Sport::where('sId', $mthnm->sports_id)->first();

                    $betlist = MyBets::where('user_id', $loginuser->id)
                        ->where('result_declare', 1)
                        ->where('isDeleted', 0)
                        ->where('match_id', $data1['match_id'])
                        ->whereBetween('created_at', [$fromdate, $todate])
                        ->groupBy('bet_type')
                        ->orderBy('created_at')
                        ->get();

                    $expodds = UserExposureLog::where('match_id', $mthnm->id)->where('user_id', $loginuser->id)->whereBetween('created_at', [$fromdate, $todate])->where('bet_type', 'ODDS')->first();

//                    dd($betlist->toArray());

                    if ($expodds) {
                        if ($expodds->bet_type == 'ODDS') {
                            if ($expodds->win_type == 'Profit') {
                                $sumAmto = $expodds->profit;
                                $sumAmt1 = $expodds->profit;
                            } else if ($expodds->win_type == 'Loss') {
                                $sumAmto = $expodds->loss;
                            }
                        }
                        $ttlAmto = $sumAmto;
                    }

                    $exposer_bm = UserExposureLog::where('bet_type', 'BOOKMAKER')->where('match_id', $mthnm->id)->where('user_id', $loginuser->id)->whereBetween('created_at', [$fromdate, $todate])->first();
                    if (!empty($exposer_bm)) {
                        $bm_win_type = $exposer_bm['win_type'];
                        if ($bm_win_type == 'Profit')
                            $sumAmtb = $exposer_bm->profit;
                        else
                            $sumAmtb = $exposer_bm->loss;
                    }

                    $ttlAmtb = $sumAmtb;

                    foreach ($betlist as $key => $value) {
                        if ($value->bet_type == 'SESSION') {
                            $betlist1 = MyBets::where('user_id', $loginuser->id)
                                ->where('result_declare', 1)
                                ->where('isDeleted', 0)
                                ->where('bet_type', 'SESSION')
                                ->groupBy('team_name')
                                ->where('match_id', $data1['match_id'])
                                ->whereBetween('created_at', [$fromdate, $todate])
                                ->orderBy('created_at')
                                ->get();

                            foreach ($betlist1 as $key => $value1) {
                                $fnc_rslt = FancyResult::where('eventid', $data1['match_id'])->where('fancy_name', $value1->team_name)->first();

                                $f_result = 0;
                                if (!empty($fnc_rslt)) {
                                    $f_result = $fnc_rslt->result;
                                }

                                $exposer_fancy = UserExposureLog::where('match_id', $mthnm->id)->where('bet_type', 'SESSION')->where('fancy_name', $value1->team_name)->where('user_id', $loginuser->id)->first();

                                if (!empty($exposer_fancy)) {
                                    $fancy_win_type = $exposer_fancy['win_type'];
                                    if ($fancy_win_type == 'Profit')
                                        $sumAmt = $exposer_fancy->profit;
                                    else
                                        $sumAmt = $exposer_fancy->loss;
                                }
                                $ttlAmt = $sumAmt;

                                $date = '<span class="sorting_1"> ' . date('d-m-y H:i', strtotime($value->created_at)) . '</span>';

                                $srno = '<span class="text-right">' . $i . '</span>';

                                if (!empty($ttlAmt)) {
                                    if ($fancy_win_type == 'Profit') {
                                        $credit = '<span class="text-right text-success">' . number_format($ttlAmt, 2) . '</span>';
                                    } else {
                                        $credit = '<span class="text-right text-success"></span>';
                                    }
                                } else if ($ttlAmt == 0) {
                                    $credit = '<span class="text-right text-success">0</span>';
                                } else {
                                    $credit = '<span class="text-right text-success"></span>';
                                }

                                if (!empty($ttlAmt)) {
                                    if ($fancy_win_type == 'Loss') {
                                        $debit = '<span class="text-right text-danger">' . number_format($ttlAmt, 2) . '</span>';
                                    } else {
                                        $debit = '<span class="text-right text-danger"></span>';
                                    }
                                } else if ($ttlAmt == 0) {
                                    $debit = '<span class="text-right text-danger"></span>';
                                } else {
                                    $debit = '<span class="text-right text-danger"></span>';
                                }

                                if ($fancy_win_type == 'Profit') {
                                    $next_row_balance = $next_row_balance + $ttlAmt;
                                    if ($next_row_balance >= 0) {
                                        $balance = '<span class="text-right text-success">
			                    		' . number_format(abs($next_row_balance), 2) . '
			                    		</span>';
                                    }
                                    if ($next_row_balance < 0) {
                                        $balance = '<span class="text-right text-danger">
			                    		' . number_format(abs($next_row_balance), 2) . '
			                    		</span>';
                                    }
                                } else if ($fancy_win_type == 'Loss') {
                                    $next_row_balance = $next_row_balance - $ttlAmt;
                                    if ($next_row_balance < 0) {
                                        $balance = '<span class="text-right text-danger">
			                    		' . number_format(abs($next_row_balance), 2) . '
			                    		</span>';
                                    }
                                    if ($next_row_balance >= 0) {
                                        $balance = '<span class="text-right text-success">
			                    		' . number_format(abs($next_row_balance), 2) . '
			                    		</span>';
                                    }
                                }
                                $remark = '<span>

			                   <a data-id="' . $mthnm->event_id . '" data-name="' . $value1->team_name . '" data-type="' . $value1->bet_type . '" class="text-dark" onclick="openMatchReport(this);" >' . $sprtnm->sport_name . ' / ' . $value1->team_name . ' / ' . $value1->bet_type . ' / ' . $f_result . '</a>
			                   </span>';
                                $totalRecords = $i;
                                $data_arr[] = array(
                                    "date" => $date,
                                    "srno" => $srno,
                                    "credit" => $credit,
                                    "debit" => $debit,
                                    "balance" => $balance,
                                    "remark" => $remark
                                );
                                $i++;

                            }
                        }
                        else if ($value->bet_type == 'ODDS') {
                            if (!empty($expodds)) {
                                $date = '<span class="sorting_1">' . date('d-m-y H:i', strtotime($value->created_at)) . '</span>';
                                $srno = '<span class="text-right">' . $i . '</span>';
                                if (!empty($ttlAmto)) {
                                    if ($expodds->win_type == 'Profit') {
                                        $credit = '<span class="text-right text-success">' . number_format($ttlAmto, 2) . '</span>';
                                    } else {
                                        $credit = '<span class="text-right text-success"></span>';
                                    }
                                } else if ($ttlAmto == 0) {
                                    $credit = '<span class="text-right text-success">0</span>';
                                }

                                if (!empty($ttlAmto)) {
                                    if ($expodds->win_type == 'Loss') {
                                        $debit = '<span class="text-right text-danger">' . number_format($ttlAmto, 2) . '</span>';
                                    } else {
                                        $debit = '<span class="text-right text-danger"></span>';
                                    }
                                }

                                if ($expodds->win_type == 'Profit') {
                                    $next_row_balance = $next_row_balance + $ttlAmto;
                                    if ($next_row_balance >= 0) {
                                        $balance = '<span class="text-right text-success">
			                    		' . number_format(abs($next_row_balance), 2) . '
			                    		</span>';
                                    }
                                    if ($next_row_balance < 0) {
                                        $balance = '<span class="text-right text-danger">
			                    		' . number_format(abs($next_row_balance), 2) . '
			                    		</span>';
                                    }
                                }
                                else if ($expodds->win_type == 'Loss') {
                                    $next_row_balance = $next_row_balance - abs($ttlAmto);
                                    if ($next_row_balance < 0) {
                                        $balance = '<span class="text-right text-danger">
			                    		' . number_format(abs($next_row_balance), 2) . '
			                    		</span>';
                                    }
                                    if ($next_row_balance >= 0) {
                                        $balance = '<span class="text-right text-success">
			                    		' . number_format(abs($next_row_balance), 2) . '
			                    		</span>';
                                    }
                                }

                                $remark = '<span>
			                   <a data-id="' . $mthnm->event_id . '" data-name="' . $value->team_name . '" data-type="' . $value->bet_type . '" class="text-dark" onclick="openMatchReport(this);" >' . $sprtnm->sport_name . ' / ' . $mthnm->match_name . ' / ' . $value->bet_type . ' / ' . $mthnm->winner . '</a>
			                   </span>';
                                $totalRecords = $i;
                                $data_arr[] = array(
                                    "date" => $date,
                                    "srno" => $srno,
                                    "credit" => $credit,
                                    "debit" => $debit,
                                    "balance" => $balance,
                                    "remark" => $remark
                                );
                                $i++;

                                if ($expodds->win_type == 'Profit') {
                                    $date = '<span class="sorting_1">' . date('d-m-y H:i', strtotime($value->created_at)) . '</span>';
                                    $srno = '<span class="text-right">' . $i . '</span>';
                                    $credit = '<span class="text-right text-success"></span>';

                                    if (!empty($sumAmt1)) {
                                        if (empty($loginuser->commission)) {
                                            $commn = 0;
                                        } else {
                                            $commn = $loginuser->commission;
                                        }
                                        $ttlAmto = ($sumAmt1 * $commn) / 100;
                                        $debit = '<span class="text-right text-danger">' . number_format($ttlAmto, 2) . '</span>';
                                    }

                                    if ($ttlAmto == 0) {
                                        $balance = '<span class="text-right text-danger">
				                    		' . number_format(abs($next_row_balance), 2) . '
				                    		</span>';
                                    } else {
                                        $next_row_balance = $next_row_balance - $ttlAmto;
                                        if ($next_row_balance < 0) {
                                            $balance = '<span class="text-right text-danger">
				                    		' . number_format(abs($next_row_balance), 2) . '
				                    		</span>';
                                        }
                                        if ($next_row_balance >= 0) {
                                            $balance = '<span class="text-right text-success">
				                    		' . number_format(abs($next_row_balance), 2) . '
				                    		</span>';
                                        }
                                    }

                                    $remark = '<span>
					                   <a data-id="' . $mthnm->event_id . '" data-name="' . $value->team_name . '" data-type="' . $value->bet_type . '" class="text-dark" onclick="openMatchReport(this);" >' . $sprtnm->sport_name . ' / ' . $mthnm->match_name . ' / ' . $value->bet_type . ' / ' . $mthnm->winner . ' (Com)</a>
					                </span>';
                                    $totalRecords = $i;
                                    $data_arr[] = array(
                                        "date" => $date,
                                        "srno" => $srno,
                                        "credit" => $credit,
                                        "debit" => $debit,
                                        "balance" => $balance,
                                        "remark" => $remark
                                    );

                                    $i++;
                                }
                            }
                        }
                        else {
                            if (!empty($exposer_bm)) {
                                $date = '<span class="sorting_1"> ' . date('d-m-y H:i', strtotime($value->created_at)) . '</span>';

                                $srno = '<span class="text-right">' . $i . '</span>';
                                if (!empty($ttlAmtb)) {
                                    if ($bm_win_type == 'Profit') {
                                        $credit = '<span class="text-right text-success">' . number_format($ttlAmtb, 2) . '</span>';
                                    } else {
                                        $credit = '<span class="text-right text-success"></span>';
                                    }
                                } else if ($ttlAmtb) {
                                    $credit = '<span class="text-right text-success">0</span>';
                                }

                                if (!empty($ttlAmtb)) {
                                    if ($bm_win_type == 'Loss') {
                                        $debit = '<span class="text-right text-danger">' . number_format($ttlAmtb, 2) . '</span>';
                                    } else {
                                        $debit = '<span class="text-right text-danger"></span>';
                                    }
                                }

                                if ($bm_win_type == 'Profit') {
                                    $next_row_balance = $next_row_balance + $ttlAmtb;
                                    if ($next_row_balance >= 0) {
                                        $balance = '<span class="text-right text-success">
			                    		' . number_format(abs($next_row_balance), 2) . '
			                    		</span>';
                                    }
                                    if ($next_row_balance < 0) {
                                        $balance = '<span class="text-right text-danger">
			                    		' . number_format(abs($next_row_balance), 2) . '
			                    		</span>';
                                    }
                                } else if ($bm_win_type == 'Loss') {
                                    $next_row_balance = $next_row_balance - $ttlAmtb;
                                    if ($next_row_balance < 0) {
                                        $balance = '<span class="text-right text-danger">
			                    		' . number_format(abs($next_row_balance), 2) . '
			                    		</span>';
                                    }
                                    if ($next_row_balance >= 0) {
                                        $balance = '<span class="text-right text-success">
			                    		' . number_format(abs($next_row_balance), 2) . '
			                    		</span>';
                                    }

                                }
                                $remark = '<span>

			                   <a data-id="' . $mthnm->event_id . '" data-name="' . $value->team_name . '" data-type="' . $value->bet_type . '" class="text-dark" onclick="openMatchReport(this);" >' . $sprtnm->sport_name . ' / ' . $mthnm->match_name . ' / ' . $value->bet_type . ' / ' . $mthnm->winner . '</a>
			                   </span>';
                                $totalRecords = $i;
                                $data_arr[] = array(
                                    "date" => $date,
                                    "srno" => $srno,
                                    "credit" => $credit,
                                    "debit" => $debit,
                                    "balance" => $balance,
                                    "remark" => $remark
                                );
                                $i++;
                            }
                        }
                    }
                }
                else {
                    $date = '<span class="sorting_1">' . date('d-m-y H:i', strtotime($data1->created_at)) . '</span>';
                    $srno = '<span class="text-right">' . $i . '</span>';

                    if (!empty($data1->amount)) {
                        if ($data1->balanceType == 'DEPOSIT') {
                            $credit = '<span class="text-right text-success">' . $data1->amount . '</span>';
                        } else {
                            $credit = '<span class="text-right text-success"></span>';
                        }
                    }

                    if (!empty($data1->amount)) {
                        if ($data1->balanceType == 'WITHDRAW') {

                            $debit = '<span class="text-right text-danger">' . $data1->amount . '</span>';
                        } else {
                            $debit = '<span class="text-right text-danger"></span>';
                        }
                    }

                    if ($data1->amount) {
                        if ($data1->balanceType == 'DEPOSIT') {
                            $next_row_balance = $next_row_balance + $data1->amount;
                            if ($next_row_balance > 0) {
                                $balance = '<span class="text-right text-success">' . number_format(abs($next_row_balance), 2) . '</span>';
                            } else {
                                $balance = '<span class="text-right text-danger">' . number_format(abs($next_row_balance), 2) . '</span>';
                            }
                        }
                        if ($data1->balanceType == 'WITHDRAW') {
                            $next_row_balance = $next_row_balance - $data1->amount;
                            if ($next_row_balance < 0) {
                                $balance = '<span class="text-right text-danger">' . number_format(abs($next_row_balance), 2) . '</span>';
                            } else {
                                $balance = '<span class="text-right text-success">' . number_format(abs($next_row_balance), 2) . '</span>';
                            }
                        }
                    }
                    $remark = '<span>' . $data1['extra'] . '</span>';
                    $totalRecords = $i;
                    $data_arr[] = array(
                        "date" => $date,
                        "srno" => $srno,
                        "credit" => $credit,
                        "debit" => $debit,
                        "balance" => $balance,
                        "remark" => $remark
                    );
                    $i++;
                }
            }
        }

        if($drpval == '1')
	    {
	    	$draw = $request->get('draw');
     		$start = $request->get("start");
     		$rowperpage = $request->get("length");

     		$columnIndex_arr = $request->get('order');
     		$columnName_arr = $request->get('columns');
     		$order_arr = $request->get('order');
     		$search_arr = $request->get('search');

     		$searchValue = $search_arr['value']; // Search value

	    	$creditData = UserDeposit::where(['child_id' =>$loginuser->id, 'parent_id' => $loginuser->parentid])
			->whereBetween('created_at',[$fromdate,$todate])
//			->skip($start)->take($rowperpage)
	        ->get();

	        $getresultcount = UserDeposit::where(['child_id' =>$loginuser->id, 'parent_id' => $loginuser->parentid])
			->whereBetween('created_at',[$fromdate,$todate])
	        ->get();

	        $getresultcounttot = count($getresultcount);
	        $totalRecords = $getresultcounttot;

	        $i=2;$blnc=0; $data_arr = array();

	        $date='<span class="sorting_1"></span>';
	        $srno='<span class="text-right">1</span>';
	        $credit='<span class="text-right text-success">'.$blnc.'</span>';
	        $debit='<span class="text-right text-danger"></span>';
	        $balance='<span class="text-right text-success">'.$blnc.'</span>';
	        $remark='<span>Opening Balance</span>';

	        $data_arr[] = array(
	          	"date" => $date,
	          	"srno" => $srno,
	           	"credit" => $credit,
	          	"debit" => $debit,
	          	"balance" => $balance,
	          	"remark" => $remark
	        );

	        foreach($creditData as $data)
		    {
	            $date='<span class="sorting_1"> '.date('d-m-y H:i',strtotime($data->created_at)).'</span>';

	            $srno='<span class="text-right">'.$i.'</span>';

	            if($data->balanceType == 'DEPOSIT'){
	            	$credit='<span class="text-right text-success">'.$data->amount.'</span>';
	            }
	            else{
	            	$credit='<span class="text-right text-success"></span>';
	            }

	            if($data->balanceType == 'WITHDRAW'){
	            	$debit='<span class="text-right text-danger">'.$data->amount.'</span>';
	            }
	            else{
	            	$debit='<span class="text-right text-danger"></span>';
	            }

	            if ($i == 2)
	            {
	                $prev_bal=$balance;
	            	$balance='<span class="text-right text-success">'.$data->amount.'</span>';

                    if($data->balanceType == 'DEPOSIT'){
                        $next_row_balance=$data->amount;
                    }

                    if($data->balanceType == 'WITHDRAW'){
                        $next_row_balance =$data->amount;
                    }
	            }
	            else
	            {
	                if($data->balanceType == 'DEPOSIT')
	                {
	                    $next_row_balance = $next_row_balance + $data->amount;
	                    $balance='<span class="text-right text-success">'.number_format(abs($next_row_balance),2).'</span>';
                	}
                	if($data->balanceType == 'WITHDRAW')
                	{
                		$next_row_balance = $next_row_balance - $data->amount;
                		$balance='<span class="text-right text-success">'.number_format(abs($next_row_balance),2).'</span>';
                	}
                }
		        $remark='<span>'.$data->extra.'</span>';
		        $totalRecords=$i;
	            $data_arr[] = array(
		          	"date" => $date,
		          	"srno" => $srno,
		           	"credit" => $credit,
		          	"debit" => $debit,
		          	"balance" => $balance,
		          	"remark" => $remark
		        );

	            $i++;
	            $previousValue = $data;
		    }
	    }

	    if($drpval == '2')
	    {
	    	$draw = $request->get('draw');
     		$start = $request->get("start");
     		$rowperpage = $request->get("length");

     		$columnIndex_arr = $request->get('order');
     		$columnName_arr = $request->get('columns');
     		$order_arr = $request->get('order');
     		$search_arr = $request->get('search');

     		$searchValue = $search_arr['value']; // Search value

	    	$gmdata = MyBets::where('user_id', $loginuser->id)
	    	->where('result_declare',1)
	    	->where('isDeleted',0)
	    	->whereBetween('created_at',[$fromdate,$todate])
	    	->groupBy('match_id')
//	    	->skip($start)->take($rowperpage)
	    	->get();

	    	$getresultcount = MyBets::where('user_id', $loginuser->id)
	    	->where('result_declare',1)
	    	->where('isDeleted',0)
	    	->whereBetween('created_at',[$fromdate,$todate])
	    	->groupBy('match_id')
	    	->get();

	    	$getresultcounttot = count($getresultcount);
	        $totalRecords = $getresultcounttot;

	    	$ttlAmt=0;$ttlAmto=0;$ttlAmtb=0;$i=2; $blnc=0;$sumAmt=0;$sumAmto=0;$sumAmtb=0;$sumAmt1=0; $commn=0;$bttl=0;

	    	$date='<span class="sorting_1"></span>';
	        $srno='<span class="text-right">1</span>';
	        $credit='<span class="text-right text-success">'.$blnc.'</span>';
	        $debit='<span class="text-right text-danger"></span>';
	        $balance='<span class="text-right text-success">'.$blnc.'</span>';
	        $remark='<span>Opening Balance</span>';

	        $data_arr[] = array(
	          	"date" => $date,
	          	"srno" => $srno,
	           	"credit" => $credit,
	          	"debit" => $debit,
	          	"balance" => $balance,
	          	"remark" => $remark
	        );

            $next_row_balance=$blnc;
	    	foreach($gmdata as $data)
	    	{
	    		$mthnm = Match::where('event_id', $data['match_id'])->first();
	    		$sprtnm = Sport::where('sId',$mthnm->sports_id)->first();

	    		$betlist= MyBets::where('user_id', $loginuser->id)
		    	->where('result_declare',1)
		    	->where('isDeleted',0)
		    	->where('match_id', $data['match_id'])
		    	->whereBetween('created_at',[$fromdate,$todate])
		    	->groupBy('bet_type')
		    	->get();

		    	$expodds=UserExposureLog::where('match_id',$mthnm->id)->where('user_id', $loginuser->id)->whereBetween('created_at',[$fromdate,$todate])->where('bet_type','ODDS')->first();

		    	if($expodds)
		    	{
		    		if($expodds->bet_type == 'ODDS' )
					{
	                	if($expodds->win_type=='Profit')
	                	{
	                		$sumAmto=$expodds->profit;
	                		$sumAmt1=$expodds->profit;
	                	}
	                	else if($expodds->win_type=='Loss')
	                	{
	                		$sumAmto=$expodds->loss;
	                	}
		     		}

		     		$ttlAmto=$sumAmto;
		    	}

	     		$exposer_bm=UserExposureLog::where('bet_type','BOOKMAKER')->where('match_id',$mthnm->id)->where('user_id', $loginuser->id)->whereBetween('created_at',[$fromdate,$todate])->first();
				if(!empty($exposer_bm))
				{
					$bm_win_type=$exposer_bm['win_type'];
					if($bm_win_type=='Profit')
						$sumAmtb=$exposer_bm->profit;
					else
						$sumAmtb=$exposer_bm->loss;
				}

	            $ttlAmtb=$sumAmtb;

		    	foreach ($betlist as $key => $value)
		    	{
		    		if($value->bet_type == 'SESSION')
		    		{
		    			$betlist1= MyBets::where('user_id', $loginuser->id)
				    	->where('result_declare',1)
				    	->where('isDeleted',0)
				    	->where('bet_type','SESSION')
				    	->where('match_id', $data['match_id'])
				    	->whereBetween('created_at',[$fromdate,$todate])
				    	->groupBy('team_name')
				    	->orderBy('created_at')
				    	->get();

				    	foreach ($betlist1 as $key => $value1)
		    			{
		    				$fnc_rslt=FancyResult::where('fancy_name',$value1->team_name)->where('eventid',$data['match_id'])->first();

		    				$f_result=0;
		    				if(!empty($fnc_rslt)){
		    					$f_result=$fnc_rslt->result;
		    				}

		    				$exposer_fancy=UserExposureLog::where('match_id',$mthnm->id)->where('bet_type','SESSION')->where('fancy_name',$value1->team_name)->where('user_id', $loginuser->id)->first();

							if(!empty($exposer_fancy))
							{
								$fancy_win_type=$exposer_fancy['win_type'];
								if($fancy_win_type=='Profit')
									$sumAmt=$exposer_fancy->profit;
								else
									$sumAmt=$exposer_fancy->loss;
							}
							$ttlAmt=$sumAmt;

			                $date='<span class="sorting_1"> '.date('d-m-y H:i',strtotime($value->created_at)).'</span>';
			                $srno='<span class="text-right">'.$i.'</span>';

			                if(!empty($ttlAmt))
			                {
				                if($fancy_win_type=='Profit')
				                {
			                		$credit='<span class="text-right text-success">'.number_format(abs($ttlAmt),2).'</span>';
				                }
				                else{
				                	$credit='<span class="text-right text-success"></span>';
				                }
				            }
				            else if($ttlAmt ==0){
				                $credit='<span class="text-right text-success">0</span>';
				            }
				            else{
			                	$credit='<span class="text-right text-success"></span>';
			                }

				            if(!empty($ttlAmt))
				            {
				                if($fancy_win_type=='Loss')
				                {
				                    $debit='<span class="text-right text-danger">'.number_format(abs($ttlAmt),2).'</span>';
				                }
				                else{
				                	$debit='<span class="text-right text-danger"></span>';
				                }
				            }
				            else if($ttlAmt ==0){
				                $debit='<span class="text-right text-danger"></span>';
				            }
				            else{
			                	$debit='<span class="text-right text-danger"></span>';
			                }

		                    if($fancy_win_type=='Profit')
		                    {
		                    	$next_row_balance = $next_row_balance + $ttlAmt;
		                    	if($next_row_balance >= 0){
		                    		$balance='<span class="text-right text-success">
		                    		'.number_format(abs($next_row_balance),2).'
		                    		</span>';
		                    	}
		                    	if($next_row_balance < 0){
		                    		$balance='<span class="text-right text-danger">
		                    		'.number_format(abs($next_row_balance),2).'
		                    		</span>';
		                    	}
		                    }

		                    else if($fancy_win_type=='Loss')
		                    {
		                    	$next_row_balance = $next_row_balance - $ttlAmt;
		                    	if($next_row_balance < 0){
		                    		$balance='<span class="text-right text-danger">
		                    		'.number_format(abs($next_row_balance), 2).'
		                    		</span>';
		                    	}
		                    	if($next_row_balance >= 0){
		                    		$balance='<span class="text-right text-success">
		                    		'.number_format(abs($next_row_balance), 2).'
		                    		</span>';
		                    	}
		                    }

			                $remark='<span>
			                   	<a data-id="'.$mthnm->event_id.'" data-name="'.$value1->team_name.'" data-type="'.$value1->bet_type.'" class="text-dark" onclick="openMatchReport(this);" >'.$sprtnm->sport_name.' / '.$value1->team_name.' / '.$value1->bet_type.' / '.$f_result.'</a>
			                </span>';
			                $totalRecords=$i;
			                $data_arr[] = array(
					          	"date" => $date,
					          	"srno" => $srno,
					           	"credit" => $credit,
					          	"debit" => $debit,
					          	"balance" => $balance,
					          	"remark" => $remark
					        );
					        $bttl=$i;
	                	$i++;
		    			}
		    		}
		    		else if($value->bet_type == 'ODDS')
		    		{
	                    $date='<span class="sorting_1"> '.date('d-m-y H:i',strtotime($value->created_at)).'</span>';
	                    $srno='<span class="text-right">'.$i.'</span>';

	                    if(!empty($ttlAmto))
	                    {
		                    if(isset($expodds) && $expodds->win_type=='Profit')
		                    {
	                    		$credit='<span class="text-right text-success">'.number_format($ttlAmto,2).'</span>';
		                    }
		                    else{
		                    	$credit='<span class="text-right text-success"></span>';
		                    }
		                }
		                else if($ttlAmto ==0) {
		                	$credit='<span class="text-right text-success">0</span>';
		                }

	                    if(!empty($ttlAmto))
	                    {
		                    if(isset($expodds) && $expodds->win_type=='Loss')
		                    {
	                    		$debit='<span class="text-right text-danger">'.number_format($ttlAmto,2).'</span>';
		                    }
		                    else{
		                    	$debit='<span class="text-right text-danger"></span>';
		                    }
		                }
		                else if($ttlAmto ==0) {
		                	$credit='<span class="text-right text-success">0</span>';
		                }

	                   	if(isset($expodds) && $expodds->win_type=='Profit')
	                    {
	                    	$next_row_balance = $next_row_balance + $ttlAmto;
	                    	if($next_row_balance >= 0){
	                    		$balance='<span class="text-right text-success">
	                    		'.number_format(abs($next_row_balance), 2).'
	                    		</span>';
	                    	}
	                    	if($next_row_balance < 0){
	                    		$balance='<span class="text-right text-danger">
	                    		'.number_format(abs($next_row_balance), 2).'
	                    		</span>';
	                    	}
	                    }
	                    else if(isset($expodds) && $expodds->win_type=='Loss')
	                    {
	                    	$next_row_balance = $next_row_balance - abs($ttlAmto);
	                    	if($next_row_balance < 0){
	                    		$balance='<span class="text-right text-danger">
	                    		'.number_format(abs($next_row_balance), 2).'
	                    		</span>';
	                    	}
	                    	if($next_row_balance >= 0){
	                    		$balance='<span class="text-right text-success">
	                    		'.number_format(abs($next_row_balance), 2).'
	                    		</span>';
	                    	}
	                    }
	                	$remark='<span>
		                   <a data-id="'.$mthnm->event_id.'" data-name="'.$value->team_name.'" data-type="'.$value->bet_type.'" class="text-dark" onclick="openMatchReport(this);" >'.$sprtnm->sport_name.' / '.$mthnm->match_name.' / '.$value->bet_type.' / '.$mthnm->winner.'</a>
		                </span>';
		                $totalRecords=$i;
		                $data_arr[] = array(
				          	"date" => $date,
				          	"srno" => $srno,
				           	"credit" => $credit,
				          	"debit" => $debit,
				          	"balance" => $balance,
				          	"remark" => $remark
				        );
	                  	$bttl=$i;
	                	$i++;

	                	if(isset($expodds) && $expodds->win_type=='Profit')
	                	{
		                	$date='<span class="sorting_1">'.date('d-m-y H:i',strtotime($value->created_at)).'</span>';
		                	$srno='<span class="text-right">'.$i.'</span>';
		                	$credit='<span class="text-right text-success"></span>';
		                	if(!empty($sumAmt1))
		                	{
	                			if(empty($loginuser->commission)){
	                				$commn=1;
	                			}
	                			else{
	                				$commn=$loginuser->commission;
	                			}
		                		$ttlAmto = ($sumAmt1 * $commn) /100;
		                		$debit='<span class="text-right text-danger">'.number_format($ttlAmto,2).'</span>';
	                		}

	                		if($ttlAmto==0)
	                		{
	                			$balance='<span class="text-right text-danger">'.number_format(abs($next_row_balance), 2).'
			                    </span>';
	                		}
	                		else
		                    {
		                    	$next_row_balance = $next_row_balance - $ttlAmto;
		                    	if($next_row_balance < 0){
		                    		$balance='<span class="text-right text-danger">
		                    		'.number_format(abs($next_row_balance), 2).'
		                    		</span>';
		                    	}
		                    	if($next_row_balance >= 0){
		                    		$balance='<span class="text-right text-success">
		                    		'.number_format(abs($next_row_balance), 2).'
		                    		</span>';
		                    	}
		                    }
	                		$remark='<span>
			                   <a data-id="'.$mthnm->event_id.'" data-name="'.$value->team_name.'" data-type="'.$value->bet_type.'" class="text-dark" onclick="openMatchReport(this);" >'.$sprtnm->sport_name.' / '.$mthnm->match_name.' / '.$value->bet_type.' / '.$mthnm->winner.' (Com)</a>
			                </span>';
			                $totalRecords=$i;
			                $data_arr[] = array(
					          	"date" => $date,
					          	"srno" => $srno,
					           	"credit" => $credit,
					          	"debit" => $debit,
					          	"balance" => $balance,
					          	"remark" => $remark
					        );
					        $bttl=$i;
		                	$i++;
		                }
		    		}
		    		else
	    			{
	                    $date='<span class="sorting_1"> '.date('d-m-y H:i',strtotime($value->created_at)).'</span>';
	                    $srno='<span class="text-right">'.$i.'</span>';
	                    if(!empty($ttlAmtb))
	                    {
		                    if(isset($bm_win_type) && $bm_win_type=='Profit')
		                    {
	                    		$credit='<span class="text-right text-success">'.number_format($ttlAmtb,2).'</span>';
		                    }
		                    else{
		                    	$credit='<span class="text-right text-success"></span>';
		                    }
		                }
		                else if($ttlAmtb){
		                	$credit='<span class="text-right text-success">0</span>';
		                }

		                if(!empty($ttlAmtb))
		                {
		                    if(isset($bm_win_type) && $bm_win_type=='Loss')
		                    {
	                    		$debit='<span class="text-right text-danger">'.number_format($ttlAmtb,2).'</span>';
		                    }
		                    else{
		                    	$debit='<span class="text-right text-danger"></span>';
		                    }
		                }

		                if(isset($bm_win_type)) {
                            if ($bm_win_type == 'Profit') {
                                $next_row_balance = $next_row_balance + $ttlAmtb;
                                if ($next_row_balance >= 0) {
                                    $balance = '<span class="text-right text-success">
	                    		' . number_format(abs($next_row_balance), 2) . '
	                    		</span>';
                                }
                                if ($next_row_balance < 0) {
                                    $balance = '<span class="text-right text-danger">
	                    		' . number_format(abs($next_row_balance), 2) . '
	                    		</span>';
                                }
                            } else if ($bm_win_type == 'Loss') {
                                $next_row_balance = $next_row_balance - $ttlAmtb;
                                if ($next_row_balance < 0) {
                                    $balance = '<span class="text-right text-danger">
	                    		' . number_format(abs($next_row_balance), 2) . '
	                    		</span>';
                                }
                                if ($next_row_balance >= 0) {
                                    $balance = '<span class="text-right text-success">
	                    		' . number_format(abs($next_row_balance), 2) . '
	                    		</span>';
                                }
                            }
                        }
	                    $remark='<span>
		                   <a data-id="'.$mthnm->event_id.'" data-name="'.$value->team_name.'" data-type="'.$value->bet_type.'" class="text-dark" onclick="openMatchReport(this);" >'.$sprtnm->sport_name.' / '.$mthnm->match_name.' / '.$value->bet_type.' / '.$mthnm->winner.'</a>
		                </span>';
		                $totalRecords=$i;
		                $data_arr[] = array(
				          	"date" => $date,
				          	"srno" => $srno,
				           	"credit" => $credit,
				          	"debit" => $debit,
				          	"balance" => $balance,
				          	"remark" => $remark
				        );
				        $bttl=$i;
	                	$i++;
	    			}
		    	}
	    	}
	    }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecords,
            "aaData" => $data_arr
        );

        echo json_encode($response);
        exit;
        //return $html;
    }

    public function getAccountPopup(Request $request)
    {
        $getUserCheck = Session::get('playerUser');
        if (!empty($getUserCheck)) {
            $loginuser = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
        }

        if (empty($request->dateto)) {
            $todate = date("Y-m-d", strtotime("+1 day"));
        }
        if (empty($request->datefrom)) {
            $fromdate = date("Y-m-d");
        }
        if ($request->datefrom) {
            $fromdate = date('Y-m-d', strtotime($request->datefrom));
        }
        if ($request->dateto) {
            $todate = date('Y-m-d', strtotime($request->dateto));
        }
        if ($request->datefrom == $request->dateto) {
            $fromdate = date('Y-m-d', strtotime($request->datefrom));
            $todate = date('Y-m-d', strtotime($request->datefrom . "+1 day"));
        }

        $mid = $request->mid;
        $btyp = $request->btyp;
        $tnm = $request->tnm;

        if ($btyp == 'SESSION') {
            $gmdata = MyBets::where('user_id', $loginuser->id)
                ->where('result_declare', 1)
                ->where('match_id', $mid)
                ->where('bet_type', $btyp)
                ->where('team_name', $tnm)
                //->groupBy('team_name',$tnm)
                ->whereBetween('created_at', [$fromdate, $todate])
                ->get();
        } else {
            $gmdata = MyBets::where('user_id', $loginuser->id)
                ->where('result_declare', 1)
                ->where('match_id', $mid)
                ->where('bet_type', $btyp)
                ->whereBetween('created_at', [$fromdate, $todate])
                ->get();
        }


        $matchdata = Match::where('event_id', $mid)->first();


        //echo"<pre>";print_r($gmdata);echo"<pre>";exit;

        $html = '';
        $i = 1;
        $sumAmt = 0;
        foreach ($gmdata as $data) {
            $html .= '
	    	<tr role="row" class="back">
	            <td aria-colindex="1" role="cell" class="text-right">
	                <span>' . $i . '</span>
	            </td>
	            <td aria-colindex="2" role="cell" class="text-center">' . $data->team_name . '</td>
	            <td aria-colindex="3" role="cell" class="text-center">' . $data->bet_type . '</td>
	            ';
            if ($data->bet_type == 'SESSION') {
                if ($data->bet_side == 'back') {
                    $html .= '<td aria-colindex="4" role="cell" class="text-center text-success" style="text-transform: uppercase;">Yes</td>';
                } else {
                    $html .= '<td aria-colindex="4" role="cell" class="text-center text-danger" style="text-transform: uppercase;">No</td>';
                }
            } else {
                $html .= '<td aria-colindex="4" role="cell" class="text-center" style="text-transform: uppercase;">' . $data->bet_side . '</td>';
            }
            $html .= '
	            <td aria-colindex="5" role="cell" class="text-center">' . $data->bet_odds . '';
            if ($data->bet_type == 'SESSION') {
                $html .= '<br>(' . $data->bet_oddsk . ')';
            }
            $html .= '</td>
	            <td aria-colindex="6" role="cell" class="text-right">' . $data->bet_amount . '</td>
	            <td aria-colindex="7" role="cell" class="text-right">';
            if ($data->bet_type == 'ODDS') {
                if ($matchdata->winner == $data->team_name && $data->bet_side == 'back') {
                    $sumAmt += $data->bet_profit;

                    $html .= '<span class="text-success">
			                    ' . $data->bet_profit . '
			                </span> ';
                } else if ($matchdata->winner != $data->team_name && $data->bet_side == 'back') {
                    $sumAmt -= $data->exposureAmt;
                    $html .= '<span class="text-danger">
			                    ' . $data->exposureAmt . '
			                </span> ';
                } else if ($matchdata->winner != $data->team_name && $data->bet_side == 'lay') {
                    $sumAmt += $data->bet_profit;
                    $html .= '<span class="text-success">
			                    ' . $data->bet_profit . '
			                </span> ';
                } else if ($matchdata->winner == $data->team_name && $data->bet_side == 'lay') {
                    $sumAmt -= $data->exposureAmt;
                    $html .= '<span class="text-danger">
			                    ' . $data->exposureAmt . '
			                </span> ';
                }
            }
            if ($data->bet_type == 'BOOKMAKER') {
                if ($matchdata->winner == $data->team_name && $data->bet_side == 'back') {
                    $sumAmt += $data->bet_profit;
                    $html .= '<span class="text-success">
			                    ' . $data->bet_profit . '
			                </span> ';
                } else if ($matchdata->winner != $data->team_name && $data->bet_side == 'back') {
                    $sumAmt -= $data->exposureAmt;
                    $html .= '<span class="text-danger">
			                    ' . $data->exposureAmt . '
			                </span> ';
                } else if ($matchdata->winner != $data->team_name && $data->bet_side == 'lay') {
                    $sumAmt += $data->bet_profit;
                    $html .= '<span class="text-success">
			                    ' . $data->bet_profit . '
			                </span> ';
                } else if ($matchdata->winner == $data->team_name && $data->bet_side == 'lay') {
                    $sumAmt -= $data->exposureAmt;
                    $html .= '<span class="text-danger">
			                    ' . $data->exposureAmt . '
			                </span> ';
                }
            }
            if ($data->bet_type == 'SESSION') {
                //$exposer_fancy=UserExposureLog::where('match_id',$matchdata->id)->where('bet_type','SESSION')->where('fancy_name',$data->team_name)->where('user_id', $loginuser->id)->first();

                $fancydata = FancyResult::where(['eventid' => $mid, 'fancy_name' => $data->team_name])->first();

                /*if(!empty($exposer_fancy))
						{
							$fancy_win_type=$exposer_fancy['win_type'];
							if($fancy_win_type=='Profit')
								$html.='<span class="text-success">
									'.$sumAmt=$exposer_fancy->profit.'
								</span> ';
							else
								$html.='<span class="text-danger">
								'.$sumAmt=$exposer_fancy->loss.'
								</span> ';
						}*/


                if ($data->bet_type == 'SESSION') {

                    if ($data->bet_side == 'back') {
                        if ($data->bet_odds <= $fancydata->result) {
                            $html .= '<span class="text-success">
									' . $sumAmt = $data->bet_profit . '
									</span> ';
                        } else {
                            $html .= '<span class="text-danger">
									' . $sumAmt = $data->exposureAmt . '
									</span> ';
                        }
                    } else if ($data->bet_side == 'lay') {
                        if ($data->bet_odds > $fancydata->result) {
                            $html .= '<span class="text-success">
									' . $sumAmt = $data->bet_profit . '
									</span> ';
                        } else {
                            $html .= '<span class="text-danger">
									' . $sumAmt = $data->exposureAmt . '
									</span> ';
                        }
                    }
                }


            }

            $html .= '
	            </td>
	            <td aria-colindex="9" role="cell" class="text-center">' . $data->created_at . '</td>
	        </tr>';
            $i++;
        }


        return $html;
    }

    public function mybets()
    {
        $getUserCheck = Session::get('playerUser');
        if (!empty($getUserCheck)) {
            $loginUser = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
        }
        $getresult = MyBets::where('user_id', $loginUser->id)->where('result_declare', 0)->where('isDeleted', 0)->latest()->get();
        //echo "<pre>";print_r($getresult);echo "<pre>";exit;
        return view('front.my-bets', compact('loginUser', 'getresult'));
    }

    public function betHistory(Request $request)
    {
        $past_date = date('Y-m-d', strtotime('today - 30 days'));
        $today_date = date("Y-m-d");


        //$todate = $request->todate;
        $fromdate = date("Y-m-d", strtotime($request->fromdate));
        $todate1 = $request->todate;
        $todate = date("Y-m-d", strtotime($todate1 . "+1 day"));

        //echo $fromdate; echo "/"; echo $todate; exit;

        $getUserCheck = Session::get('playerUser');
        if (!empty($getUserCheck)) {
            $loginUser = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
        }

        $getresult = MyBets::where(['user_id' => $loginUser->id, 'result_declare' => 1])
            ->whereBetween('created_at', [$fromdate, $todate])
            ->whereBetween('created_at', [$past_date, $todate])
            ->latest()->get();

        $html = '';
        $html .= '';

        foreach ($getresult as $data) {
            $sports = Sport::where('sId', $data->sportID)->first();
            $matchdata = Match::where('event_id', $data->match_id)->first();

            $fancydata = FancyResult::where(['eventid' => $data->match_id, 'fancy_name' => $data->team_name])->first();

            $html .= '
            	<tr class="white-bg">
                    <td class="white-bg"><img src="">
                        <a class="text-color-blue-light">' . $data->id . '</a>
                    </td>
                    <td>' . $loginUser->user_name . '</td>
                    <td>' . $sports->sport_name . '<i class="fas fa-caret-right text-color-grey"></i> <b> ' . $matchdata->match_name . ' </b> <i class="fas fa-caret-right text-color-grey"></i> ' . $data->bet_type . '</td>
                    <td class="text-right">' . $data->team_name . ' </td>
                    ';
            if ($data->bet_side == 'lay') {
                if ($data->bet_type == 'SESSION')
                    $html .= '<td class="text-right" style="color: #e33a5e !important;text-transform: uppercase;">no</td>';
                else
                    $html .= '<td class="text-right" style="color: #e33a5e !important;text-transform: uppercase;">' . $data->bet_side . '</td>';
            } else {
                if ($data->bet_type == 'SESSION')
                    $html .= '<td class="text-right" style="color: #1f72ac !important;text-transform: uppercase;">yes</td>';
                else
                    $html .= '<td class="text-right" style="color: #1f72ac !important;text-transform: uppercase;">' . $data->bet_side . '</td>';
            }

            $html .= '
                    <td class="text-right"> <span class="smtxt"> ' . $data->created_at . '</span> </td>
                    <td class="text-right">' . $data->bet_amount . '</td>
                    <td class="text-right">' . $data->bet_odds . '</td>';
            if ($data->bet_type == 'ODDS') {
                if ($matchdata->winner == $data->team_name && $data->bet_side == 'back') {
                    $html .= '<td class="text-color-green text-right">(' . $data->bet_profit . ')</td>';
                } else if ($matchdata->winner != $data->team_name && $data->bet_side == 'back') {
                    $html .= '<td class="text-color-red text-right">(' . $data->exposureAmt . ')</td>';
                } else if ($matchdata->winner == $data->team_name && $data->bet_side == 'lay') {
                    $html .= '<td class="text-color-red text-right">(' . $data->exposureAmt . ')</td>';
                } else if ($matchdata->winner != $data->team_name && $data->bet_side == 'lay') {
                    $html .= '<td class="text-color-green text-right">(' . $data->bet_profit . ')</td>';
                }
            }
            if ($data->bet_type == 'SESSION') {

                if (!empty($fancydata)) {

                    if ($data->bet_side == 'back') {
                        if ($data->bet_odds <= $fancydata->result) {
                            $html .= '<td class="text-color-green text-right">(' . $data->bet_profit . ')</td>';
                        } else {
                            $html .= '<td class="text-color-red text-right">(' . $data->exposureAmt . ')</td>';
                        }
                    } else if ($data->bet_side == 'lay') {
                        if ($data->bet_odds > $fancydata->result) {
                            $html .= '<td class="text-color-green text-right">(' . $data->bet_profit . ')</td>';
                        } else {
                            $html .= '<td class="text-color-red text-right">(' . $data->exposureAmt . ')</td>';
                        }
                    }
                }
            }
            if ($data->bet_type == 'BOOKMAKER') {
                if ($matchdata->winner == $data->team_name && $data->bet_side == 'back') {
                    $html .= '<td class="text-color-green text-right">(' . $data->bet_profit . ')</td>';
                } else if ($matchdata->winner != $data->team_name && $data->bet_side == 'back') {
                    $html .= '<td class="text-color-red text-right">(' . $data->exposureAmt . ')</td>';
                } else if ($matchdata->winner == $data->team_name && $data->bet_side == 'lay') {
                    $html .= '<td class="text-color-red text-right">(' . $data->exposureAmt . ')</td>';
                } else if ($matchdata->winner != $data->team_name && $data->bet_side == 'lay') {
                    $html .= '<td class="text-color-green text-right">(' . $data->bet_profit . ')</td>';
                }
            }

            $html .= '</tr>
            ';
        }

        return $html;
    }

    public function betToday(Request $request)
    {
        $tdate = $request->tdate;

        $getUserCheck = Session::get('playerUser');
        if (!empty($getUserCheck)) {
            $loginUser = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
        }
        $getresult = MyBets::where(['user_id' => $loginUser->id, 'result_declare' => 1])
            ->whereDate('created_at', $tdate)
            ->latest()->get();

        $html = '';
        $html .= '';

        foreach ($getresult as $data) {
            $sports = Sport::where('sId', $data->sportID)->first();
            $matchdata = Match::where('event_id', $data->match_id)->first();

            $fancydata = FancyResult::where(['eventid' => $data->match_id, 'fancy_name' => $data->team_name])->first();
            $html .= '
            	<tr class="white-bg">
                    <td class="white-bg"><img src="">
                        <a class="text-color-blue-light">' . $data->id . '</a>
                    </td>
                    <td>' . $loginUser->user_name . '</td>
                    <td>' . $sports->sport_name . '<i class="fas fa-caret-right text-color-grey"></i> <b> ' . $matchdata->match_name . ' </b> <i class="fas fa-caret-right text-color-grey"></i> ' . $data->bet_type . '</td>
                    <td class="text-right">' . $data->team_name . ' </td>
                    ';
            if ($data->bet_side == 'lay') {
                if ($data->bet_type == 'SESSION')
                    $html .= '<td class="text-right" style="color: #e33a5e !important;text-transform: uppercase;">no</td>';
                else
                    $html .= '<td class="text-right" style="color: #e33a5e !important;text-transform: uppercase;">' . $data->bet_side . '</td>';
            } else {
                if ($data->bet_type == 'SESSION')
                    $html .= '<td class="text-right" style="color: #1f72ac !important;text-transform: uppercase;">yes</td>';
                else
                    $html .= '<td class="text-right" style="color: #1f72ac !important;text-transform: uppercase;">' . $data->bet_side . '</td>';
            }

            $html .= '
                    <td class="text-right"> <span class="smtxt"> ' . $data->created_at . '</span> </td>
                    <td class="text-right">' . $data->bet_amount . '</td>
                    <td class="text-right">' . $data->bet_odds . '</td>';

            if ($data->bet_type == 'ODDS') {

                if ($matchdata->winner == $data->team_name && $data->bet_side == 'back') {
                    $html .= '<td class="text-color-green text-right">(' . $data->bet_profit . ')</td>';
                } else if ($matchdata->winner != $data->team_name && $data->bet_side == 'back') {
                    $html .= '<td class="text-color-red text-right">(' . $data->exposureAmt . ')</td>';
                } else if ($matchdata->winner == $data->team_name && $data->bet_side == 'lay') {
                    $html .= '<td class="text-color-red text-right">(' . $data->exposureAmt . ')</td>';
                } else if ($matchdata->winner != $data->team_name && $data->bet_side == 'lay') {
                    $html .= '<td class="text-color-green text-right">(' . $data->bet_profit . ')</td>';
                }
            }
            if ($data->bet_type == 'SESSION') {

                if (!empty($fancydata)) {

                    if ($data->bet_side == 'back') {
                        if ($data->bet_odds <= $fancydata->result) {
                            $html .= '<td class="text-color-green text-right">(' . $data->bet_profit . ')</td>';
                        } else {
                            $html .= '<td class="text-color-red text-right">(' . $data->exposureAmt . ')</td>';
                        }
                    } else if ($data->bet_side == 'lay') {
                        if ($data->bet_odds > $fancydata->result) {
                            $html .= '<td class="text-color-green text-right">(' . $data->bet_profit . ')</td>';
                        } else {
                            $html .= '<td class="text-color-red text-right">(' . $data->exposureAmt . ')</td>';
                        }
                    }
                }

            }
            if ($data->bet_type == 'BOOKMAKER') {
                if ($matchdata->winner == $data->team_name && $data->bet_side == 'back') {
                    $html .= '<td class="text-color-green text-right">(' . $data->bet_profit . ')</td>';
                } else if ($matchdata->winner != $data->team_name && $data->bet_side == 'back') {
                    $html .= '<td class="text-color-red text-right">(' . $data->exposureAmt . ')</td>';
                } else if ($matchdata->winner == $data->team_name && $data->bet_side == 'lay') {
                    $html .= '<td class="text-color-red text-right">(' . $data->exposureAmt . ')</td>';
                } else if ($matchdata->winner != $data->team_name && $data->bet_side == 'lay') {
                    $html .= '<td class="text-color-green text-right">(' . $data->bet_profit . ')</td>';
                }
            }

            $html .= '</tr>
            ';
        }
        return $html;
    }

    public function betYest(Request $request)
    {
        $ydate = $request->ydate;
        $getUserCheck = Session::get('playerUser');
        if (!empty($getUserCheck)) {
            $loginUser = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
        }
        $getresult = MyBets::where(['user_id' => $loginUser->id, 'result_declare' => 1])
            ->whereDate('created_at', $ydate)
            ->latest()->get();

        $html = '';
        $html .= '';

        foreach ($getresult as $data) {
            $sports = Sport::where('sId', $data->sportID)->first();
            $matchdata = Match::where('event_id', $data->match_id)->first();

            $fancydata = FancyResult::where(['eventid' => $data->match_id, 'fancy_name' => $data->team_name])->first();

            $html .= '
            	<tr class="white-bg">
                    <td class="white-bg"><img src="">
                        <a class="text-color-blue-light">' . $data->id . '</a>
                    </td>
                    <td>' . $loginUser->user_name . '</td>
                    <td>' . $sports->sport_name . '<i class="fas fa-caret-right text-color-grey"></i> <b> ' . $matchdata->match_name . ' </b> <i class="fas fa-caret-right text-color-grey"></i> ' . $data->bet_type . '</td>
                    <td class="text-right">' . $data->team_name . ' </td>
                    ';
            if ($data->bet_side == 'lay') {
                if ($data->bet_type == 'SESSION')
                    $html .= '<td class="text-right" style="color: #e33a5e !important;text-transform: uppercase;">no</td>';
                else
                    $html .= '<td class="text-right" style="color: #e33a5e !important;text-transform: uppercase;">' . $data->bet_side . '</td>';
            } else {
                if ($data->bet_type == 'SESSION')
                    $html .= '<td class="text-right" style="color: #1f72ac !important;text-transform: uppercase;">yes</td>';
                else
                    $html .= '<td class="text-right" style="color: #1f72ac !important;text-transform: uppercase;">' . $data->bet_side . '</td>';
            }

            $html .= '
                    <td class="text-right"> <span class="smtxt"> ' . $data->created_at . '</span> </td>
                    <td class="text-right">' . $data->bet_amount . '</td>
                    <td class="text-right">' . $data->bet_odds . '</td>';
            if ($data->bet_type == 'ODDS') {

                if ($matchdata->winner == $data->team_name && $data->bet_side == 'back') {
                    $html .= '<td class="text-color-green text-right">(' . $data->bet_profit . ')</td>';
                } else if ($matchdata->winner != $data->team_name && $data->bet_side == 'back') {
                    $html .= '<td class="text-color-red text-right">(' . $data->exposureAmt . ')</td>';
                } else if ($matchdata->winner == $data->team_name && $data->bet_side == 'lay') {
                    $html .= '<td class="text-color-red text-right">(' . $data->exposureAmt . ')</td>';
                } else if ($matchdata->winner != $data->team_name && $data->bet_side == 'lay') {
                    $html .= '<td class="text-color-green text-right">(' . $data->bet_profit . ')</td>';
                }
            }
            if ($data->bet_type == 'SESSION') {

                if (!empty($fancydata)) {

                    if ($data->bet_side == 'back') {
                        if ($data->bet_odds <= $fancydata->result) {
                            $html .= '<td class="text-color-green text-right">(' . $data->bet_profit . ')</td>';
                        } else {
                            $html .= '<td class="text-color-red text-right">(' . $data->exposureAmt . ')</td>';
                        }
                    } else if ($data->bet_side == 'lay') {
                        if ($data->bet_odds > $fancydata->result) {
                            $html .= '<td class="text-color-green text-right">(' . $data->bet_profit . ')</td>';
                        } else {
                            $html .= '<td class="text-color-red text-right">(' . $data->exposureAmt . ')</td>';
                        }
                    }
                }
            }
            if ($data->bet_type == 'BOOKMAKER') {
                if ($matchdata->winner == $data->team_name && $data->bet_side == 'back') {
                    $html .= '<td class="text-color-green text-right">(' . $data->bet_profit . ')</td>';
                } else if ($matchdata->winner != $data->team_name && $data->bet_side == 'back') {
                    $html .= '<td class="text-color-red text-right">(' . $data->exposureAmt . ')</td>';
                } else if ($matchdata->winner == $data->team_name && $data->bet_side == 'lay') {
                    $html .= '<td class="text-color-red text-right">(' . $data->exposureAmt . ')</td>';
                } else if ($matchdata->winner != $data->team_name && $data->bet_side == 'lay') {
                    $html .= '<td class="text-color-green text-right">(' . $data->bet_profit . ')</td>';
                }

            }
            $html .= '</tr>
            ';
        }
        return $html;
    }

    public function getPLdata(Request $request)
    {
        $fromdate = $request->fromdate;

        $todate1 = $request->todate;
        $todate = date("Y-m-d", strtotime($todate1 . "+1 day"));
        //$todate = $request->todate;

        $past_to = date('Y-m-d');
        $past_from = date('Y-m-d', strtotime("-60 days"));

        $getUserCheck = Session::get('playerUser');
        if (!empty($getUserCheck)) {
            $loginUser = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
        }

        $getresult = MyBets::where(['user_id' => $loginUser->id, 'result_declare' => 1])
            ->whereBetween('created_at', [$fromdate, $todate])
            ->whereBetween('created_at', [$past_from, $todate])
            ->groupBy('match_id')
            ->latest()->get();

        $html = '';
        $html .= '';
        $i = 1;
        $amt = '';
        $amt .= '';
        $totalp = 0;

        foreach ($getresult as $data) {
            $sports = Sport::where('sId', $data->sportID)->first();
            $matchdata = Match::where('event_id', $data->match_id)->latest()->first();

            $subresult = MyBets::where('match_id', $data->match_id)
                ->where(['user_id' => $loginUser->id, 'result_declare' => 1])
                ->whereBetween('created_at', [$fromdate, $todate])
                ->latest()->get();

            $sumAmt = 0;
            $totalAmt = 0;
            $totalPr = 0;

            $exposer_odds = UserExposureLog::where('match_id', $matchdata->id)->where('bet_type', 'ODDS')->where('user_id', $loginUser->id)->first();
            if (!empty($exposer_odds)) {
                $odds_win_type = $exposer_odds['win_type'];
                if ($odds_win_type == 'Profit')
                    $sumAmt = $sumAmt + $exposer_odds->profit;
                else
                    $sumAmt = $sumAmt - $exposer_odds->loss;
                $totalPr = ($sumAmt * $loginUser->commission) / 100;
            }
            $exposer_bm = UserExposureLog::where('match_id', $matchdata->id)->where('bet_type', 'BOOKMAKER')->where('user_id', $loginUser->id)->first();
            if (!empty($exposer_bm)) {
                $bm_win_type = $exposer_odds['win_type'];
                if ($bm_win_type == 'Profit')
                    $sumAmt = $sumAmt + $exposer_bm->profit;
                else
                    $sumAmt = $sumAmt - $exposer_bm->loss;
            }

            foreach ($subresult as $subd1) {
                $sports = Sport::where('sId', $subd1->sportID)->first();
                $matchdata1 = Match::where('event_id', $subd1->match_id)->latest()->first();

                $fancydata = FancyResult::where(['eventid' => $subd1->match_id, 'fancy_name' => $subd1->team_name])->first();

                /*if($subd1->bet_type == 'ODDS'){

	            			if($matchdata1->winner == $subd1->team_name){
		                        $sumAmt+=$subd1->bet_profit;
		                    }else{
		                        $sumAmt-=$subd1->exposureAmt;
	                    	}
	            		}*/
                if ($subd1->bet_type == 'SESSION') {

                    $exposer_fancy = UserExposureLog::where('match_id', $matchdata->id)->where('bet_type', 'SESSION')->where('fancy_name', $subd1->team_name)->where('user_id', $loginUser->id)->first();
                    if (!empty($exposer_fancy)) {
                        $fancy_win_type = $exposer_fancy['win_type'];
                        if ($fancy_win_type == 'Profit')
                            $sumAmt = $sumAmt + $exposer_fancy->profit;
                        else
                            $sumAmt = $sumAmt - $exposer_fancy->loss;
                    }

                }

                /*if($subd1->bet_type == 'BOOKMAKER'){
	            			if($matchdata1->winner == $subd1->team_name){
		                        $sumAmt+=$subd1->bet_profit;
		                    }else{
		                        $sumAmt-=$subd1->exposureAmt;
	                    	}
	            		}*/
            }

            $totalPr = ($sumAmt * $loginUser->commission) / 100;

            $totalAmt = $sumAmt;

            $totalp += $sumAmt;

            $clrtxt = '';
            if ($totalAmt < 0) {
                $clrtxt = 'clrtxtred';
            } else {
                $clrtxt = 'clrtxtgrn';
            }

            $html .= '

            	<tr class="white-bg">
                    <td>' . $sports->sport_name . ' <i class="fas fa-caret-right text-color-grey"></i> <b> ' . $matchdata->match_name . ' </b> </td>

                    <td class="text-right">' . $matchdata->match_date . '</td>
                    <td class="text-right">' . $matchdata->created_at . '</td>
                   <td class="text-right"><a href="#collapse' . $i . '" data-toggle="collapse" aria-expanded="false" class="text-color-black ' . $clrtxt . '">' . $totalAmt . '<img src="' . asset('asset/img/plus-icon.png') . '"></a> </td>

                </tr>';


            $html .= '<tr class="expand-block light-grey-bg-3 list-unstyled collapse" id="collapse' . $i . '">
                    <td colspan="4">
                        <img src="' . asset('img/arrow-down1.png') . '" class="expandarrow">
                        <table class="table-commission">
                            <thead>
                                <tr>
                                    <th width="9%">Bet ID</th>
                                    <th width="">Selection</th>
                                    <th width="9%">Odds</th>
                                    <th width="13%">Stake</th>
                                    <th width="8%">Type</th>
                                    <th width="16%">Placed</th>
                                    <th width="23%">Profit/Loss</th>
                                </tr>
                            </thead>
                          	<tbody>';
            foreach ($subresult as $subd) {

                $sports = Sport::where('sId', $subd->sportID)->first();
                $matchdata2 = Match::where('event_id', $subd->match_id)->latest()->first();

                $fancydata = FancyResult::where(['eventid' => $subd->match_id, 'fancy_name' => $subd->team_name])->first();


                $html .= '
	                            <tr class="light-grey-bg-4">
	                                <td>' . $subd->id . '</td>
	                                <td>' . $subd->team_name . '</td>
	                                <td>' . $subd->bet_odds . '</td>
	                                <td>' . $subd->bet_amount . '</td>';
                if ($subd->bet_side == 'lay') {
                    if ($subd->bet_type == 'SESSION')
                        $html .= '<td class="text-color-red"style="text-transform: uppercase;"><span>no</span></td>';
                    else
                        $html .= '<td class="text-color-red"style="text-transform: uppercase;"><span>' . $subd->bet_side . '</span></td>';
                } else {
                    if ($subd->bet_type == 'SESSION')
                        $html .= '<td class="text-color-blue-light"style="text-transform: uppercase;"><span>yes</span></td>';
                    else
                        $html .= '<td class="text-color-blue-light"style="text-transform: uppercase;"><span>' . $subd->bet_side . '</span></td>';
                }

                $html .= '<td>' . $subd->created_at . ' </td>';


                if ($subd->bet_type == 'ODDS') {

                    if ($matchdata2->winner == $subd->team_name && $subd->bet_side == 'back') {
                        $html .= '<td class="text-color-green">(' . $subd->bet_profit . ')</td>';
                    } else if ($matchdata2->winner != $subd->team_name && $subd->bet_side == 'back') {
                        $html .= '<td class="text-color-red">(' . $subd->exposureAmt . ')</td>';
                    } else if ($matchdata2->winner == $subd->team_name && $subd->bet_side == 'lay') {
                        $html .= '<td class="text-color-red">(' . $subd->exposureAmt . ')</td>';
                    } else if ($matchdata2->winner != $subd->team_name && $subd->bet_side == 'lay') {
                        $html .= '<td class="text-color-green">(' . $subd->bet_profit . ')</td>';
                    }
                }

                if ($subd->bet_type == 'SESSION') {

                    if (!empty($fancydata)) {

                        if ($subd->bet_side == 'back') {
                            if ($subd->bet_odds >= $fancydata->result) {
                                $html .= '<td class="text-color-green">(' . $subd->bet_profit . ')</td>';

                            } else {
                                $html .= '<td class="text-color-red">(' . $subd->exposureAmt . ')</td>';

                            }
                        } else if ($subd->bet_side == 'lay') {
                            if ($subd->bet_odds <= $fancydata->result) {
                                $html .= '<td class="text-color-green">(' . $subd->bet_profit . ')</td>';
                            } else {
                                $html .= '<td class="text-color-red">(' . $subd->exposureAmt . ')</td>';

                            }
                        }
                    }

                }
                if ($subd->bet_type == 'BOOKMAKER') {
                    if ($matchdata2->winner == $subd->team_name && $subd->bet_side == 'back') {
                        $html .= '<td class="text-color-green">(' . $subd->bet_profit . ')</td>';
                    } else if ($matchdata2->winner != $subd->team_name && $subd->bet_side == 'back') {
                        $html .= '<td class="text-color-red">(' . $subd->exposureAmt . ')</td>';
                    } else if ($matchdata2->winner == $subd->team_name && $subd->bet_side == 'lay') {
                        $html .= '<td class="text-color-green">(' . $subd->exposureAmt . ')</td>';
                    } else if ($matchdata2->winner != $subd->team_name && $subd->bet_side == 'lay') {
                        $html .= '<td class="text-color-red">(' . $subd->bet_profit . ')</td>';
                    }
                }


                $html .= '</tr>';
            }
            $html .= '</tbody>
		            	</table>
		        	</td>
		        </tr>';
            $i++;
        }
        $amt .= '' . $totalp . '';

        return $html . '~~' . $amt;
    }

    public function plToday(Request $request)
    {
        $tdate = $request->tdate;
        $getUserCheck = Session::get('playerUser');
        if (!empty($getUserCheck)) {
            $loginUser = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
        }
        $getresult = MyBets::where(['user_id' => $loginUser->id, 'result_declare' => 1])
            ->whereDate('created_at', $tdate)
            ->groupBy('match_id')
            ->latest()->get();

        $html = '';
        $html .= '';
        $i = 1;
        $amt = '';
        $amt .= '';
        $totalp = 0;

        foreach ($getresult as $data) {
            $sports = Sport::where('sId', $data->sportID)->first();
            $matchdata = Match::where('event_id', $data->match_id)->latest()->first();
            $subresult = MyBets::where('match_id', $data->match_id)->where(['user_id' => $loginUser->id, 'result_declare' => 1])->whereDate('created_at', $tdate)->latest()->get();
            $sumAmt = 0;
            $totalAmt = 0;
            $totalPr = 0;

            /*$exposer_odds=UserExposureLog::where('match_id',$matchdata->id)->where('bet_type','ODDS')->where('user_id', $loginUser->id)->first();
			if(!empty($exposer_odds))
			{
				$odds_win_type=$exposer_odds['win_type'];
				if($odds_win_type=='Profit')
					$sumAmt=$sumAmt+$exposer_odds->profit;
				else
					$sumAmt=$sumAmt-$exposer_odds->loss;
				$totalPr = ($sumAmt * $loginUser->commission) /100;
			}*/
            /*$exposer_bm=UserExposureLog::where('match_id',$matchdata->id)->where('bet_type','BOOKMAKER')->where('user_id', $loginUser->id)->first();
			if(!empty($exposer_bm))
			{
				$bm_win_type=$exposer_odds['win_type'];
				if($bm_win_type=='Profit')
					$sumAmt=$sumAmt+$exposer_bm->profit;
				else
					$sumAmt=$sumAmt-$exposer_bm->loss;
			}*/

            foreach ($subresult as $subd1) {
                $sports = Sport::where('sId', $subd1->sportID)->first();
                $matchdata1 = Match::where('event_id', $subd1->match_id)->latest()->first();
                $fancydata = FancyResult::where(['eventid' => $subd1->match_id, 'fancy_name' => $subd1->team_name])->first();

                if ($subd1->bet_type == 'ODDS') {
                    if ($matchdata1->winner == $subd1->team_name && $subd1->bet_side == 'back') {
                        $sumAmt += $subd1->bet_profit;
                    } else if ($matchdata1->winner != $subd1->team_name && $subd1->bet_side == 'back') {
                        $sumAmt -= $subd1->exposureAmt;
                    } else if ($matchdata1->winner != $subd1->team_name && $subd1->bet_side == 'lay') {
                        $sumAmt += $subd1->bet_profit;
                    } else if ($matchdata1->winner == $subd1->team_name && $subd1->bet_side == 'lay') {

                        $sumAmt -= $subd1->exposureAmt;
                    }
                }
                if ($subd1->bet_type == 'SESSION') {

                    if ($subd1->bet_side == 'back') {
                        if ($subd1->bet_odds <= $fancydata->result) {
                            $sumAmt += $subd1->bet_profit;
                        } else {
                            $sumAmt -= $subd1->exposureAmt;
                        }
                    } else if ($subd1->bet_side == 'lay') {
                        if ($subd1->bet_odds >= $fancydata->result) {
                            $sumAmt += $subd1->bet_profit;
                        } else {
                            $sumAmt -= $subd1->exposureAmt;
                        }
                    }
                    /*$exposer_fancy=UserExposureLog::where('match_id',$matchdata->id)->where('bet_type','SESSION')->where('fancy_name',$subd1->team_name)->where('user_id', $loginUser->id)->first();
						if(!empty($exposer_fancy))
						{
							$fancy_win_type=$exposer_fancy['win_type'];
							if($fancy_win_type=='Profit')
								$sumAmt=$sumAmt+$exposer_fancy->profit;
							else
								$sumAmt=$sumAmt-$exposer_fancy->loss;
						}*/

                }
                if ($subd1->bet_type == 'BOOKMAKER') {
                    if ($matchdata1->winner == $subd1->team_name && $subd1->bet_side == 'back') {
                        $sumAmt += $subd1->bet_profit;
                    } else if ($matchdata1->winner != $subd1->team_name && $subd1->bet_side == 'back') {
                        $sumAmt -= $subd1->exposureAmt;
                    } else if ($matchdata1->winner != $subd1->team_name && $subd1->bet_side == 'lay') {
                        $sumAmt += $subd1->bet_profit;
                    } else if ($matchdata1->winner == $subd1->team_name && $subd1->bet_side == 'lay') {
                        $sumAmt -= $subd1->exposureAmt;
                    }
                }
            } //commented by nipa - on 07-09-2021 as its not calculating proper amount if we win or loos


            $totalAmt = $sumAmt;
            $totalp += $sumAmt;
            $clrtxt = '';
            if ($totalAmt < 0) {
                $clrtxt = 'clrtxtred';
            } else {
                $clrtxt = 'clrtxtgrn';
            }

            $html .= '
            	<tr class="white-bg">
                    <td>' . $sports->sport_name . ' <i class="fas fa-caret-right text-color-grey"></i> <b> ' . $matchdata->match_name . ' </b> </td>

                    <td class="text-right">' . $matchdata->match_date . '</td>
                    <td class="text-right">' . $matchdata->created_at . '</td>
                   <td class="text-right"><a href="#collapse' . $i . '" data-toggle="collapse" aria-expanded="false" class="text-color-black ' . $clrtxt . '">' . $totalAmt . '<img src="' . asset('asset/img/plus-icon.png') . '"></a> </td>

                </tr>

                <tr class="expand-block light-grey-bg-3 list-unstyled collapse" id="collapse' . $i . '">
                    <td colspan="4">
                        <img src="' . asset('img/arrow-down1.png') . '" class="expandarrow">
                        <table class="table-commission">
                            <thead>
                                <tr>
                                    <th width="9%">Bet ID</th>
                                    <th width="">Selection</th>
                                    <th width="9%">Odds</th>
                                    <th width="13%">Stake</th>
                                    <th width="8%">Type</th>
                                    <th width="16%">Placed</th>
                                    <th width="23%">Profit/Loss</th>
                                </tr>
                            </thead>
                          	<tbody>';

            foreach ($subresult as $subd) {
                $sports = Sport::where('sId', $subd->sportID)->first();
                $matchdata2 = Match::where('event_id', $subd->match_id)->latest()->first();

                $fancydata = FancyResult::where(['eventid' => $subd->match_id, 'fancy_name' => $subd->team_name])->first();

                $html .= '
	                            <tr class="light-grey-bg-4">
	                                <td>' . $subd->id . '</td>
	                                <td>' . $subd->team_name . '</td>
	                                <td>' . $subd->bet_odds . '</td>
	                                <td>' . $subd->bet_amount . '</td>';
                if ($subd->bet_side == 'lay') {
                    if ($subd->bet_type == 'SESSION')
                        $html .= '<td class="text-color-red" style="text-transform: uppercase;"><span>no</span></td>';
                    else
                        $html .= '<td class="text-color-red" style="text-transform: uppercase;"><span>' . $subd->bet_side . '</span></td>';
                } else {
                    if ($subd->bet_type == 'SESSION')
                        $html .= '<td class="text-color-blue-light" style="text-transform: uppercase;"><span>yes</span></td>';
                    else
                        $html .= '<td class="text-color-blue-light" style="text-transform: uppercase;"><span>' . $subd->bet_side . '</span></td>';
                }

                $html .= '<td>' . $subd->created_at . ' </td>';
                if ($subd->bet_type == 'ODDS') {
                    if ($matchdata2->winner == $subd->team_name && $subd->bet_side == 'back') {
                        $html .= '<td class="text-color-green">(' . $subd->bet_profit . ')</td>';
                    } else if ($matchdata2->winner != $subd->team_name && $subd->bet_side == 'back') {
                        $html .= '<td class="text-color-red">(' . $subd->exposureAmt . ')</td>';
                    } else if ($matchdata2->winner != $subd->team_name && $subd->bet_side == 'lay') {
                        $html .= '<td class="text-color-green">(' . $subd->bet_profit . ')</td>';
                    } else if ($matchdata2->winner == $subd->team_name && $subd->bet_side == 'lay') {
                        $html .= '<td class="text-color-red">(' . $subd->exposureAmt . ')</td>';
                    }
                }
                if ($subd->bet_type == 'SESSION') {

                    if (!empty($fancydata)) {

                        if ($subd->bet_side == 'back') {
                            if ($subd->bet_odds <= $fancydata->result) {
                                $html .= '<td class="text-color-green">(' . $subd->bet_profit . ')</td>';

                            } else {
                                $html .= '<td class="text-color-red">(' . $subd->exposureAmt . ')</td>';

                            }
                        } else if ($subd->bet_side == 'lay') {
                            if ($subd->bet_odds >= $fancydata->result) {
                                $html .= '<td class="text-color-green">(' . $subd->bet_profit . ')</td>';
                            } else {
                                $html .= '<td class="text-color-red">(' . $subd->exposureAmt . ')</td>';

                            }
                        }
                    }

                }
                if ($subd->bet_type == 'BOOKMAKER') {
                    if ($matchdata2->winner == $subd->team_name && $subd->bet_side == 'back') {
                        $html .= '<td class="text-color-green">(' . $subd->bet_profit . ')</td>';
                    } else if ($matchdata2->winner != $subd->team_name && $subd->bet_side == 'back') {
                        $html .= '<td class="text-color-red">(' . $subd->exposureAmt . ')</td>';
                    } else if ($matchdata2->winner != $subd->team_name && $subd->bet_side == 'lay') {
                        $html .= '<td class="text-color-green">(' . $subd->bet_profit . ')</td>';
                    } else if ($matchdata2->winner == $subd->team_name && $subd->bet_side == 'lay') {
                        $html .= '<td class="text-color-red">(' . $subd->exposureAmt . ')</td>';
                    }
                }

                $html .= '</tr>';
            }
            $html .= '</tbody>
		            	</table>
		        	</td>
		        </tr>';
            $i++;
        }
        $amt .= '' . $totalp . '';
        return $html . '~~' . $amt;
    }

    public function plYest(Request $request)
    {
        $tdate = $request->ydate;

        $getUserCheck = Session::get('playerUser');
        if (!empty($getUserCheck)) {
            $loginUser = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
        }
        $getresult = MyBets::where(['user_id' => $loginUser->id, 'result_declare' => 1])
            ->whereDate('created_at', $tdate)
            ->groupBy('match_id')
            ->latest()->get();

        $html = '';
        $html .= '';
        $i = 1;
        $amt = '';
        $amt .= '';
        $totalp = 0;

        foreach ($getresult as $data) {
            $sports = Sport::where('sId', $data->sportID)->first();
            $matchdata = Match::where('event_id', $data->match_id)->latest()->first();
            $subresult = MyBets::where('match_id', $data->match_id)->where(['user_id' => $loginUser->id, 'result_declare' => 1])->whereDate('created_at', $tdate)->latest()->get();
            $sumAmt = 0;
            $totalAmt = 0;
            $totalPr = 0;

            /*$exposer_odds=UserExposureLog::where('match_id',$matchdata->id)->where('bet_type','ODDS')->where('user_id', $loginUser->id)->first();
			if(!empty($exposer_odds))
			{
				$odds_win_type=$exposer_odds['win_type'];
				if($odds_win_type=='Profit')
					$sumAmt=$sumAmt+$exposer_odds->profit;
				else
					$sumAmt=$sumAmt-$exposer_odds->loss;
				$totalPr = ($sumAmt * $loginUser->commission) /100;
			}*/
            /*$exposer_bm=UserExposureLog::where('match_id',$matchdata->id)->where('bet_type','BOOKMAKER')->where('user_id', $loginUser->id)->first();
			if(!empty($exposer_bm))
			{
				$bm_win_type=$exposer_odds['win_type'];
				if($bm_win_type=='Profit')
					$sumAmt=$sumAmt+$exposer_bm->profit;
				else
					$sumAmt=$sumAmt-$exposer_bm->loss;
			}*/

            foreach ($subresult as $subd1) {
                $sports = Sport::where('sId', $subd1->sportID)->first();
                $matchdata1 = Match::where('event_id', $subd1->match_id)->latest()->first();
                $fancydata = FancyResult::where(['eventid' => $subd1->match_id, 'fancy_name' => $subd1->team_name])->first();

                if ($subd1->bet_type == 'ODDS') {
                    if ($matchdata1->winner == $subd1->team_name && $subd1->bet_side == 'back') {
                        $sumAmt += $subd1->bet_profit;
                    } else if ($matchdata1->winner != $subd1->team_name && $subd1->bet_side == 'back') {
                        $sumAmt -= $subd1->exposureAmt;
                    } else if ($matchdata1->winner != $subd1->team_name && $subd1->bet_side == 'lay') {
                        $sumAmt += $subd1->bet_profit;
                    } else if ($matchdata1->winner == $subd1->team_name && $subd1->bet_side == 'lay') {

                        $sumAmt -= $subd1->exposureAmt;
                    }
                }
                if ($subd1->bet_type == 'SESSION') {

                    if ($subd1->bet_side == 'back') {
                        if ($subd1->bet_odds <= $fancydata->result) {
                            $sumAmt += $subd1->bet_profit;
                        } else {
                            $sumAmt -= $subd1->exposureAmt;
                        }
                    } else if ($subd1->bet_side == 'lay') {
                        if ($subd1->bet_odds >= $fancydata->result) {
                            $sumAmt += $subd1->bet_profit;
                        } else {
                            $sumAmt -= $subd1->exposureAmt;
                        }
                    }
                    /*$exposer_fancy=UserExposureLog::where('match_id',$matchdata->id)->where('bet_type','SESSION')->where('fancy_name',$subd1->team_name)->where('user_id', $loginUser->id)->first();
						if(!empty($exposer_fancy))
						{
							$fancy_win_type=$exposer_fancy['win_type'];
							if($fancy_win_type=='Profit')
								$sumAmt=$sumAmt+$exposer_fancy->profit;
							else
								$sumAmt=$sumAmt-$exposer_fancy->loss;
						}*/

                }
                if ($subd1->bet_type == 'BOOKMAKER') {
                    if ($matchdata1->winner == $subd1->team_name && $subd1->bet_side == 'back') {
                        $sumAmt += $subd1->bet_profit;
                    } else if ($matchdata1->winner != $subd1->team_name && $subd1->bet_side == 'back') {
                        $sumAmt -= $subd1->exposureAmt;
                    } else if ($matchdata1->winner != $subd1->team_name && $subd1->bet_side == 'lay') {
                        $sumAmt += $subd1->bet_profit;
                    } else if ($matchdata1->winner == $subd1->team_name && $subd1->bet_side == 'lay') {
                        $sumAmt -= $subd1->exposureAmt;
                    }
                }
            } //commented by nipa - on 07-09-2021 as its not calculating proper amount if we win or loos


            $totalAmt = $sumAmt;
            $totalp += $sumAmt;
            $clrtxt = '';
            if ($totalAmt < 0) {
                $clrtxt = 'clrtxtred';
            } else {
                $clrtxt = 'clrtxtgrn';
            }

            $html .= '
            	<tr class="white-bg">
                    <td>' . $sports->sport_name . ' <i class="fas fa-caret-right text-color-grey"></i> <b> ' . $matchdata->match_name . ' </b> </td>

                    <td class="text-right">' . $matchdata->match_date . '</td>
                    <td class="text-right">' . $matchdata->created_at . '</td>
                   <td class="text-right"><a href="#collapse' . $i . '" data-toggle="collapse" aria-expanded="false" class="text-color-black ' . $clrtxt . '">' . $totalAmt . '<img src="' . asset('asset/img/plus-icon.png') . '"></a> </td>

                </tr>

                <tr class="expand-block light-grey-bg-3 list-unstyled collapse" id="collapse' . $i . '">
                    <td colspan="4">
                        <img src="' . asset('img/arrow-down1.png') . '" class="expandarrow">
                        <table class="table-commission">
                            <thead>
                                <tr>
                                    <th width="9%">Bet ID</th>
                                    <th width="">Selection</th>
                                    <th width="9%">Odds</th>
                                    <th width="13%">Stake</th>
                                    <th width="8%">Type</th>
                                    <th width="16%">Placed</th>
                                    <th width="23%">Profit/Loss</th>
                                </tr>
                            </thead>
                          	<tbody>';

            foreach ($subresult as $subd) {
                $sports = Sport::where('sId', $subd->sportID)->first();
                $matchdata2 = Match::where('event_id', $subd->match_id)->latest()->first();

                $fancydata = FancyResult::where(['eventid' => $subd->match_id, 'fancy_name' => $subd->team_name])->first();

                $html .= '
	                            <tr class="light-grey-bg-4">
	                                <td>' . $subd->id . '</td>
	                                <td>' . $subd->team_name . '</td>
	                                <td>' . $subd->bet_odds . '</td>
	                                <td>' . $subd->bet_amount . '</td>';
                if ($subd->bet_side == 'lay') {
                    if ($subd->bet_type == 'SESSION')
                        $html .= '<td class="text-color-red" style="text-transform: uppercase;"><span>no</span></td>';
                    else
                        $html .= '<td class="text-color-red" style="text-transform: uppercase;"><span>' . $subd->bet_side . '</span></td>';
                } else {
                    if ($subd->bet_type == 'SESSION')
                        $html .= '<td class="text-color-blue-light" style="text-transform: uppercase;"><span>yes</span></td>';
                    else
                        $html .= '<td class="text-color-blue-light" style="text-transform: uppercase;"><span>' . $subd->bet_side . '</span></td>';
                }

                $html .= '<td>' . $subd->created_at . ' </td>';
                if ($subd->bet_type == 'ODDS') {
                    if ($matchdata2->winner == $subd->team_name && $subd->bet_side == 'back') {
                        $html .= '<td class="text-color-green">(' . $subd->bet_profit . ')</td>';
                    } else if ($matchdata2->winner != $subd->team_name && $subd->bet_side == 'back') {
                        $html .= '<td class="text-color-red">(' . $subd->exposureAmt . ')</td>';
                    } else if ($matchdata2->winner != $subd->team_name && $subd->bet_side == 'lay') {
                        $html .= '<td class="text-color-green">(' . $subd->bet_profit . ')</td>';
                    } else if ($matchdata2->winner == $subd->team_name && $subd->bet_side == 'lay') {
                        $html .= '<td class="text-color-red">(' . $subd->exposureAmt . ')</td>';
                    }
                }
                if ($subd->bet_type == 'SESSION') {

                    if (!empty($fancydata)) {

                        if ($subd->bet_side == 'back') {
                            if ($subd->bet_odds <= $fancydata->result) {
                                $html .= '<td class="text-color-green">(' . $subd->bet_profit . ')</td>';

                            } else {
                                $html .= '<td class="text-color-red">(' . $subd->exposureAmt . ')</td>';

                            }
                        } else if ($subd->bet_side == 'lay') {
                            if ($subd->bet_odds >= $fancydata->result) {
                                $html .= '<td class="text-color-green">(' . $subd->bet_profit . ')</td>';
                            } else {
                                $html .= '<td class="text-color-red">(' . $subd->exposureAmt . ')</td>';

                            }
                        }
                    }

                }
                if ($subd->bet_type == 'BOOKMAKER') {
                    if ($matchdata2->winner == $subd->team_name && $subd->bet_side == 'back') {
                        $html .= '<td class="text-color-green">(' . $subd->bet_profit . ')</td>';
                    } else if ($matchdata2->winner != $subd->team_name && $subd->bet_side == 'back') {
                        $html .= '<td class="text-color-red">(' . $subd->exposureAmt . ')</td>';
                    } else if ($matchdata2->winner != $subd->team_name && $subd->bet_side == 'lay') {
                        $html .= '<td class="text-color-green">(' . $subd->bet_profit . ')</td>';
                    } else if ($matchdata2->winner == $subd->team_name && $subd->bet_side == 'lay') {
                        $html .= '<td class="text-color-red">(' . $subd->exposureAmt . ')</td>';
                    }
                }

                $html .= '</tr>';
            }
            $html .= '</tbody>
		            	</table>
		        	</td>
		        </tr>';
            $i++;
        }
        $amt .= '' . $totalp . '';
        return $html . '~~' . $amt;
    }

    public function plSport(Request $request)
    {
        $fromdate = $request->fromdate;
        $todate = $request->todate;
        $sport = $request->sport;

        $getUserCheck = Session::get('playerUser');
        if (!empty($getUserCheck)) {
            $loginUser = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
        }

        if ($sport == 0) {
            $getresult = MyBets::where(['user_id' => $loginUser->id, 'result_declare' => 1])
                ->whereBetween('created_at', [$fromdate, $todate])
                ->groupBy('match_id')
                ->latest()->get();
        } else {
            $getresult = MyBets::where(['user_id' => $loginUser->id, 'sportID' => $sport])
                ->whereBetween('created_at', [$fromdate, $todate])
                ->groupBy('match_id')
                ->latest()->get();
        }

        $html = '';
        $html .= '';
        $i = 1;
        $amt = '';
        $amt .= '';
        $totalp = 0;

        foreach ($getresult as $data) {
            $sports = Sport::where('sId', $data->sportID)->first();
            $matchdata = Match::where('event_id', $data->match_id)->latest()->first();
            $subresult = MyBets::where('match_id', $data->match_id)->where(['user_id' => $loginUser->id, 'result_declare' => 1])->whereBetween('created_at', [$fromdate, $todate])->latest()->get();
            $sumAmt = 0;
            $totalAmt = 0;
            $totalPr = 0;

            /*$exposer_odds=UserExposureLog::where('match_id',$matchdata->id)->where('bet_type','ODDS')->where('user_id', $loginUser->id)->first();
			if(!empty($exposer_odds))
			{
				$odds_win_type=$exposer_odds['win_type'];
				if($odds_win_type=='Profit')
					$sumAmt=$sumAmt+$exposer_odds->profit;
				else
					$sumAmt=$sumAmt-$exposer_odds->loss;
				$totalPr = ($sumAmt * $loginUser->commission) /100;
			}*/
            /*$exposer_bm=UserExposureLog::where('match_id',$matchdata->id)->where('bet_type','BOOKMAKER')->where('user_id', $loginUser->id)->first();
			if(!empty($exposer_bm))
			{
				$bm_win_type=$exposer_odds['win_type'];
				if($bm_win_type=='Profit')
					$sumAmt=$sumAmt+$exposer_bm->profit;
				else
					$sumAmt=$sumAmt-$exposer_bm->loss;
			}*/

            foreach ($subresult as $subd1) {
                $sports = Sport::where('sId', $subd1->sportID)->first();
                $matchdata1 = Match::where('event_id', $subd1->match_id)->latest()->first();
                $fancydata = FancyResult::where(['eventid' => $subd1->match_id, 'fancy_name' => $subd1->team_name])->first();

                if ($subd1->bet_type == 'ODDS') {
                    if ($matchdata1->winner == $subd1->team_name && $subd1->bet_side == 'back') {
                        $sumAmt += $subd1->bet_profit;
                    } else if ($matchdata1->winner != $subd1->team_name && $subd1->bet_side == 'back') {
                        $sumAmt -= $subd1->exposureAmt;
                    } else if ($matchdata1->winner != $subd1->team_name && $subd1->bet_side == 'lay') {
                        $sumAmt += $subd1->bet_profit;
                    } else if ($matchdata1->winner == $subd1->team_name && $subd1->bet_side == 'lay') {

                        $sumAmt -= $subd1->exposureAmt;
                    }
                }
                if ($subd1->bet_type == 'SESSION') {

                    if ($subd1->bet_side == 'back') {
                        if ($subd1->bet_odds <= $fancydata->result) {
                            $sumAmt += $subd1->bet_profit;
                        } else {
                            $sumAmt -= $subd1->exposureAmt;
                        }
                    } else if ($subd1->bet_side == 'lay') {
                        if ($subd1->bet_odds >= $fancydata->result) {
                            $sumAmt += $subd1->bet_profit;
                        } else {
                            $sumAmt -= $subd1->exposureAmt;
                        }
                    }
                    /*$exposer_fancy=UserExposureLog::where('match_id',$matchdata->id)->where('bet_type','SESSION')->where('fancy_name',$subd1->team_name)->where('user_id', $loginUser->id)->first();
						if(!empty($exposer_fancy))
						{
							$fancy_win_type=$exposer_fancy['win_type'];
							if($fancy_win_type=='Profit')
								$sumAmt=$sumAmt+$exposer_fancy->profit;
							else
								$sumAmt=$sumAmt-$exposer_fancy->loss;
						}*/

                }
                if ($subd1->bet_type == 'BOOKMAKER') {
                    if ($matchdata1->winner == $subd1->team_name && $subd1->bet_side == 'back') {
                        $sumAmt += $subd1->bet_profit;
                    } else if ($matchdata1->winner != $subd1->team_name && $subd1->bet_side == 'back') {
                        $sumAmt -= $subd1->exposureAmt;
                    } else if ($matchdata1->winner != $subd1->team_name && $subd1->bet_side == 'lay') {
                        $sumAmt += $subd1->bet_profit;
                    } else if ($matchdata1->winner == $subd1->team_name && $subd1->bet_side == 'lay') {
                        $sumAmt -= $subd1->exposureAmt;
                    }
                }
            } //commented by nipa - on 07-09-2021 as its not calculating proper amount if we win or loos


            $totalAmt = $sumAmt;
            $totalp += $sumAmt;


            $html .= '
            	<tr class="white-bg">
                    <td>' . $sports->sport_name . ' <i class="fas fa-caret-right text-color-grey"></i> <b> ' . $matchdata->match_name . ' </b> </td>

                    <td class="text-right">' . $matchdata->match_date . '</td>
                    <td class="text-right">' . $matchdata->created_at . '</td>
                   <td class="text-right"><a href="#collapse' . $i . '" data-toggle="collapse" aria-expanded="false" class="text-color-black">' . $totalAmt . '<img src="' . asset('asset/img/plus-icon.png') . '"></a> </td>

                </tr>

                <tr class="expand-block light-grey-bg-3 list-unstyled collapse" id="collapse' . $i . '">
                    <td colspan="4">
                        <img src="' . asset('img/arrow-down1.png') . '" class="expandarrow">
                        <table class="table-commission">
                            <thead>
                                <tr>
                                    <th width="9%">Bet ID</th>
                                    <th width="">Selection</th>
                                    <th width="9%">Odds</th>
                                    <th width="13%">Stake</th>
                                    <th width="8%">Type</th>
                                    <th width="16%">Placed</th>
                                    <th width="23%">Profit/Loss</th>
                                </tr>
                            </thead>
                          	<tbody>';

            foreach ($subresult as $subd) {
                $sports = Sport::where('sId', $subd->sportID)->first();
                $matchdata2 = Match::where('event_id', $subd->match_id)->latest()->first();

                $fancydata = FancyResult::where(['eventid' => $subd->match_id, 'fancy_name' => $subd->team_name])->first();

                $html .= '
	                            <tr class="light-grey-bg-4">
	                                <td>' . $subd->id . '</td>
	                                <td>' . $subd->team_name . '</td>
	                                <td>' . $subd->bet_odds . '</td>
	                                <td>' . $subd->bet_amount . '</td>';
                if ($subd->bet_side == 'lay') {
                    if ($subd->bet_type == 'SESSION')
                        $html .= '<td class="text-color-red" style="text-transform: uppercase;"><span>no</span></td>';
                    else
                        $html .= '<td class="text-color-red" style="text-transform: uppercase;"><span>' . $subd->bet_side . '</span></td>';
                } else {
                    if ($subd->bet_type == 'SESSION')
                        $html .= '<td class="text-color-blue-light" style="text-transform: uppercase;"><span>yes</span></td>';
                    else
                        $html .= '<td class="text-color-blue-light" style="text-transform: uppercase;"><span>' . $subd->bet_side . '</span></td>';
                }

                $html .= '<td>' . $subd->created_at . ' </td>';
                if ($subd->bet_type == 'ODDS') {
                    if ($matchdata2->winner == $subd->team_name && $subd->bet_side == 'back') {
                        $html .= '<td class="text-color-green">(' . $subd->bet_profit . ')</td>';
                    } else if ($matchdata2->winner != $subd->team_name && $subd->bet_side == 'back') {
                        $html .= '<td class="text-color-red">(' . $subd->exposureAmt . ')</td>';
                    } else if ($matchdata2->winner != $subd->team_name && $subd->bet_side == 'lay') {
                        $html .= '<td class="text-color-green">(' . $subd->bet_profit . ')</td>';
                    } else if ($matchdata2->winner == $subd->team_name && $subd->bet_side == 'lay') {
                        $html .= '<td class="text-color-red">(' . $subd->exposureAmt . ')</td>';
                    }
                }
                if ($subd->bet_type == 'SESSION') {

                    if (!empty($fancydata)) {

                        if ($subd->bet_side == 'back') {
                            if ($subd->bet_odds <= $fancydata->result) {
                                $html .= '<td class="text-color-green">(' . $subd->bet_profit . ')</td>';

                            } else {
                                $html .= '<td class="text-color-red">(' . $subd->exposureAmt . ')</td>';

                            }
                        } else if ($subd->bet_side == 'lay') {
                            if ($subd->bet_odds >= $fancydata->result) {
                                $html .= '<td class="text-color-green">(' . $subd->bet_profit . ')</td>';
                            } else {
                                $html .= '<td class="text-color-red">(' . $subd->exposureAmt . ')</td>';

                            }
                        }
                    }

                }
                if ($subd->bet_type == 'BOOKMAKER') {
                    if ($matchdata2->winner == $subd->team_name && $subd->bet_side == 'back') {
                        $html .= '<td class="text-color-green">(' . $subd->bet_profit . ')</td>';
                    } else if ($matchdata2->winner != $subd->team_name && $subd->bet_side == 'back') {
                        $html .= '<td class="text-color-red">(' . $subd->exposureAmt . ')</td>';
                    } else if ($matchdata2->winner != $subd->team_name && $subd->bet_side == 'lay') {
                        $html .= '<td class="text-color-green">(' . $subd->bet_profit . ')</td>';
                    } else if ($matchdata2->winner == $subd->team_name && $subd->bet_side == 'lay') {
                        $html .= '<td class="text-color-red">(' . $subd->exposureAmt . ')</td>';
                    }
                }

                $html .= '</tr>';
            }
            $html .= '</tbody>
		            	</table>
		        	</td>
		        </tr>';
            $i++;
        }
        $amt .= '' . $totalp . '';
        return $html . '~~' . $amt;
    }

    public function activitylog()
    {
        $getUserCheck = Session::get('playerUser');
        if (!empty($getUserCheck)) {
            $loginuser = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
        }
        $user = User::where('id', $loginuser->id)->first();
        return view('front.activity-log', compact('user'));
    }

    public function updateUserPassword(Request $request, $id)
    {
        $userData = User::find($id);
        $newpass = $request->newpwd;
        $yourpwd = $request->yourpwd;
        if (Hash::check($yourpwd, $userData->password)) {
            $userData->first_login = 1;
            $userData->password = Hash::make($newpass);
            $userData->update();
        } else {
            return Redirect::back()->withErrors(['Your password do not match with current password', 'Password is not match !']);
        }
        return redirect()->route('front')->with('message', 'Password Change Successfully');
    }

    public function multimarket()
    {
        $getUserCheck = Session::get('playerUser');
        if (empty($getUserCheck)) {
            return redirect('/');
        }
        $sports = Sport::all();
        $settings = setting::first();
        $restapi = new RestApi();
        $socialdata = SocialMedia::first();
        $banner = Banner::get();
        return view('front.multimarket', compact('sports', 'settings', 'socialdata', 'banner'));
    }
}

?>
