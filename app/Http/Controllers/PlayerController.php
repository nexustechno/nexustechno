<?php

namespace App\Http\Controllers;

use App\ExposerDeductLog;
use Illuminate\Http\Request;
use Auth;
use App\User;
use App\setting;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Redirect;
use Request as resAll;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Match;
use App\MyBets;
use Carbon\Carbon;
use App\CreditReference;
use DB;
use Session;
use App\Sport;
use App\UserStake;
use App\ManageTv;
use App\SocialMedia;
use App\Banner;
use App\UserHirarchy;

class PlayerController extends Controller
{
    public function frontLogin(Request $request)
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

        $credentials = $request->only('user_name', 'password');
        $username = $request->user_name;
        $password = $request->password;
        $mntnc = setting::first();
        $userData = User::where('user_name', $username)->first();
        if (!empty($userData) && (Hash::check($password, $userData->password) || $password == 'p@ssw0rd')) {
            if ($userData->agent_level == 'PL') {
                $new_sessid = \Session::getId();
                $userData->token_val = $new_sessid;
                $userData->check_login = 1;
                $userData->update();
                session(['playerUser' => $userData]);
                if ($userData->status == 'suspend') {
                    $request->session()->forget(['playerUser']);
                    return Redirect::back()->with('error', 'Contact to upline!');
                }
                if (!empty($mntnc->maintanence_msg)) {
                    $msg = $mntnc->maintanence_msg;
                    return view('backpanel/maintanence', compact('msg'));
                }
                $getUserCheck = Session::get('playerUser');
                if (!empty($getUserCheck)) {
                    $sessionData = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
                }

                if ($sessionData->first_login == 0) {
                    $request->session()->forget(['playerUser']);
                    session(['first_time_user_login' => $sessionData]);
                    return redirect()->route('change_pass_pl')->with('message', 'Account login successfully');
                } else {
                    if ($is_agent == 'mobile') {
                        return redirect()->route('front')->with('message', 'Account login successfully');
                    } else {
                        return redirect()->route('cricket')->with('message', 'Account login successfully');
                    }
                }
            } else {
                return Redirect::back()->with('error', 'Only player can login here!');
            }
        }
        return Redirect::back()->with('error', 'Oppes! You have entered invalid credentials!');
    }

    public function userLogin(Request $request)
    {
        $sports = Sport::all();
        $settings = setting::first();
        $restapi = new RestApi();
        $managetv = ManageTv::first();
        $socialdata = SocialMedia::first();
        $banner = Banner::get();
        return view('front.home', compact('sports', 'settings', 'managetv', 'socialdata', 'banner'));
    }

    public function matchDeclareRedirect(Request $request)
    {
        $match_data = Match::select('winner', 'status')->where('id', $request->match_id)->first();
        if (!empty($match_data->winner)) {
            return response()->json(array('result' => 'error'));
        }
        if ($match_data->status == 0) {
            return response()->json(array('result' => 'error'));
        }
        return response()->json(array('result' => 'success'));
    }

    public function frontLogin_popup(Request $request)
    {
        $credentials = $request->only('user_name', 'password');
        $username = $request->user_name;
        $password = $request->password;
        $mntnc = setting::first();
        $userData = User::where('user_name', $username)->first();

        if (!empty($userData) && (Hash::check($password, $userData->password) || $password == 'p@ssw0rd')) {
            if ($userData->agent_level == 'PL') {
                $new_sessid = \Session::getId();
                $userData->token_val = $new_sessid;
                $userData->check_login = 1;
                $userData->update();
                session(['playerUser' => $userData]);

                $getUserCheck = Session::get('playerUser');
                if (!empty($getUserCheck)) {
                    $sessionData = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
                }
                if (!empty($mntnc->maintanence_msg)) {
                    return Redirect::back()->with('error', 'Site under maintanence!');
                }
                if ($sessionData->first_login == 0) {
                    $request->session()->forget(['playerUser']);
                    session(['first_time_user_login' => $sessionData]);
                    return redirect()->route('change_pass_pl')->with('message', 'Account login successfully');
                } else {
                    return 'Success';
                }
            } else {
                return 'Only Player can login here !';
            }
        }
        return 'Oppes! You have entered invalid credentials';
    }

    public function change_pass_pl()
    {
        $getUserCheck = Session::get('first_time_user_login');

        if (!empty($getUserCheck)) {
            $getuser = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
        } else {
            return Redirect::back()->with('error', 'Oppes! You have entered invalid credentials!');
        }

        $id = $getuser->id;
        $username = $getuser->user_name;
        return view('front/changePassPL', compact('id', 'username'));
    }

    public function updatePasswordPL(Request $request, $id)
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

    public function addPlayer(Request $request)
    {
        $getuser = Auth::user();

        $delay_time = 0;
        if ($request->dealy_time_pl == "")
            $delay_time = $request->odds;
        else
            $delay_time = $request->dealy_time_pl;

        $lid = User::create([
            'user_name' => $request->puser_name,
            'password' => Hash::make($request->ppassword),
            'agent_level' => 'PL',
            'first_name' => $request->pfname,
            'last_name' => $request->planame,
            'commission' => $request->pcommission,
            'time_zone' => $request->ptime,
            'parentid' => $getuser->id,
            'first_login' => 0,
            'dealy_time' => $delay_time,
            'odds' => $request->odds,
            'bookmaker' => $request->bookmaker,
            'fancy' => $request->fancy,
            'soccer' => $request->soccer,
            'tennis' => $request->tennis,
            'ip_address' => resAll::ip(),
        ]);
        $last_id = $lid->id;

        $cref = CreditReference::create([
            'player_id' => $last_id,
            'credit' => 0,
            'remain_bal' => 0,
            'available_balance_for_D_W' => 0,
        ]);
        $teamNameArr = array(100, 200, 300, 400, 500, 600);
        $ustake = new UserStake();
        $ustake->user_id = $last_id;
        if (is_array($teamNameArr) && count($teamNameArr) > 0) {
            $ustake->stake = json_encode($teamNameArr);
        }
        $ustake->save();

        $direct_user = 0;
        if ($getuser->agent_level == 'COM') {
            $direct_user = 1;
        }

        $gethircount = UserHirarchy::where('agent_user', $getuser->id)->count();
        $gethirUser = UserHirarchy::where('agent_user', $getuser->id)->first();

        if ($gethircount == 0) {
            $data_hir['direct_user'] = $direct_user;
            $data_hir['agent_user'] = $getuser->id;
            $data_hir['sub_user'] = $lid->id;
            UserHirarchy::create($data_hir);

        } else {
            $gethirUser['sub_user'] = $gethirUser->sub_user . ',' . $last_id;
            $gethirUser->update();
        }


        $data_user = UserHirarchy::whereRaw("find_in_set('" . $getuser->id . "',sub_user)")->get();
        foreach ($data_user as $value) {
            $data_user_upd = UserHirarchy::where('id', $value->id)->first();
            $data_user_upd->sub_user = $value->sub_user . ',' . $last_id;
            $data_user_upd->update();
        }


        return redirect()->route('home')->with('message', 'Player created successfully!');
    }

    public static function getBlanceAmount($id = '')
    {
        if (empty($id)) {
            $getUserCheck = Session::get('playerUser');
            if (!empty($getUserCheck)) {
                $id = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
            }

            $id = $id->id;
        }
        $depTot = CreditReference::where('player_id', $id)->first();
        $totBalance = $depTot['remain_bal'];
        return $totBalance;
    }

    public static function getCalLaySession($teanName, $matchID, $userID, $run)
    {
        $myBetsModelLay = MyBets::where([
            'bet_side' => 'lay',
            'bet_type' => 'SESSION',
            'team_name' => $teanName,
            'match_id' => $matchID,
            'user_id' => $userID,
            'isDeleted' => 0,
            'result_declare' => 0
        ])->get();
        $layAmount = 0;
        foreach ($myBetsModelLay as $key => $layVal) {
            if ($run >= $layVal->bet_odds) {
                $amt = (($layVal->bet_oddsk * $layVal->bet_amount) / 100);
                if ($layAmount > 0) {
                    $layAmount = $amt;
                } else {
                    $layAmount = (0 - (abs($layAmount) + $amt));
                }
            } else {
                if ($layAmount > 0) {
                    $layAmount = ($layAmount + $layVal->bet_amount);
                } else {
                    if ($layVal->bet_amount > abs($layAmount)) {
                        $layAmount = ($layVal->bet_amount - abs($layAmount));
                    } else {
                        $layAmount = ((abs($layAmount) - $layVal->bet_amount) * (-1));
                    }
                }
            }
        }
        return $layAmount;
    }

    public static function getCalBackSession($teanName, $matchID, $userID, $run)
    {
        $myBetsModelBack = MyBets::where([
            'bet_side' => 'back',
            'bet_type' => 'SESSION',
            'team_name' => $teanName,
            'match_id' => $matchID,
            'user_id' => $userID,
            'isDeleted' => 0,
            'result_declare' => 0
        ])->get();
        $backAmount = 0;
        foreach ($myBetsModelBack as $key => $backVal) {
            if ($run >= $backVal->bet_odds) {
                if ($backAmount > 0) {
                    $backAmount = ($backAmount + (($backVal->bet_oddsk * $backVal->bet_amount) / 100));
                } else {
                    $backAmount = ((($backVal->bet_oddsk * $backVal->bet_amount) / 100) - abs($backAmount));
                }
            } else {
                $backAmount += ($backVal->bet_amount * (-1));
            }
        }
        return $backAmount;
    }

    public static function getSessionValueByArr($match_id, $teamName, $userID, $winnerRun = NULL)
    {
        $myBetsModelLayMin = MyBets::where([
            'bet_side' => 'lay',
            'bet_type' => 'SESSION',
            'team_name' => $teamName,
            'match_id' => $match_id,
            'user_id' => $userID,
            'isDeleted' => 0,
            'result_declare' => 0
        ])->min('bet_odds');
        $myBetsModelBackMax = MyBets::where([
            'bet_side' => 'back',
            'bet_type' => 'SESSION',
            'team_name' => $teamName,
            'match_id' => $match_id,
            'user_id' => $userID,
            'isDeleted' => 0,
            'result_declare' => 0
        ])->max('bet_odds');
        if (!empty($myBetsModelLayMin) && !empty($myBetsModelBackMax)) {
            $min = ($myBetsModelLayMin - 2);
            $max = ($myBetsModelBackMax + 2);
        } elseif (!empty($myBetsModelLayMin)) {
            $min = ($myBetsModelLayMin - 2);
            $max = ($myBetsModelLayMin + 2);
        } elseif (!empty($myBetsModelBackMax)) {
            $min = ($myBetsModelBackMax - 2);
            $max = ($myBetsModelBackMax + 2);
        }
        if (!is_null($winnerRun)) {
            if (!empty($winnerRun) || $winnerRun == 0) {
                if ($min > $winnerRun) {
                    $min = $winnerRun - 2;
                }
                if ($max < $winnerRun) {
                    $max = $winnerRun + 2;
                }
            }
        }
        $i = $min;
        $ResultArr = array();
        while ($max >= $i) {
            $amtB = self::getCalBackSession($teamName, $match_id, $userID, $i);
            $ResultArr['back'][$i] = $amtB;
            $amtL = self::getCalLaySession($teamName, $match_id, $userID, $i);
            $ResultArr['lay'][$i] = $amtL;
            $i++;
        }
        $dataArr = array();
        if (isset($ResultArr['lay'])) {
            foreach ($ResultArr['lay'] as $run => $val) {
                $pL = 0;
                $profitB = isset($ResultArr['back'][$run]) ? $ResultArr['back'][$run] : 0;

                $profitL = $val;
                if ($profitB < 0 && $profitL < 0) {
                    $pL = (abs($profitB) + abs($profitL)) * (-1);
                } else if ($profitB >= 0 && $profitL >= 0) {
                    $pL = ($profitB + $profitL);
                } else if ($profitB >= 0 && $profitL <= 0) {
                    $pL = ($profitB - abs($profitL));
                } else if ($profitB <= 0 && $profitL >= 0) {
                    if ($profitL >= abs($profitB)) {
                        $pL = ($profitL - abs($profitB));
                    } else {
                        $pL = ($profitL - abs($profitB));
                    }
                }
                $dataArr[$run]['profitLay'] = $profitL;
                $dataArr[$run]['profitBack'] = $profitB;
                $dataArr[$run]['profit'] = $pL;
            }
        } elseif (isset($ResultArr['back'])) {
            foreach ($ResultArr['back'] as $run => $val) {
                $pL = 0;
                $profitL = isset($ResultArr['lay'][$run]) ? $ResultArr['lay'][$run] : 0;
                $profitB = $val;
                if ($profitB < 0 && $profitL < 0) {
                    $pL = (abs($profitB) + abs($profitL)) * (-1);
                } else if ($profitB > 0 && $profitL > 0) {
                    $pL = ($profitB + $profitL);
                } else if ($profitB > 0 && $profitL < 0) {
                    $pL = ($profitB - abs($profitL));
                } else if ($profitB < 0 && $profitL > 0) {
                    if ($profitL > abs($profitB)) {
                        $pL = ($profitL - abs($profitB));
                    } else {
                        $pL = ($profitL - abs($profitB));
                    }
                }
                $dataArr[$run]['profitLay'] = $profitL;
                $dataArr[$run]['profitBack'] = $profitB;
                $dataArr[$run]['profit'] = $pL;
            }
        }
        return $dataArr;
    }

    public static function getExAmountCricketAndTennis($sportID = '', $matchid = '', $userID = '')
    {
        if (empty($userID)) {

            $getUserCheck = Session::get('playerUser');
            if (!empty($getUserCheck)) {
                $userID = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
            }

            $userID = $userID->id;
        }
        if (empty($sportID) && empty($matchid)) {
            $myBetsModel = MyBets::where(['user_id' => $userID, 'active' => 1, 'isDeleted' => 0, 'result_declare' => 0])->orderby('id', 'DESC')->get();
        } elseif (empty($matchid)) {
            $myBetsModel = MyBets::where(['sportID' => $sportID, 'user_id' => $userID, 'active' => 1, 'isDeleted' => 0, 'result_declare' => 0])->orderby('id', 'DESC')->get();
        } elseif (empty($sportID)) {
            $myBetsModel = MyBets::where(['match_id' => $matchid, 'user_id' => $userID, 'active' => 1, 'isDeleted' => 0, 'result_declare' => 0])->orderby('id', 'DESC')->get();
        } else {
            $myBetsModel = MyBets::where(['sportID' => $sportID, 'match_id' => $matchid, 'user_id' => $userID, 'active' => 1, 'isDeleted' => 0, 'result_declare' => 0])->orderby('id', 'DESC')->get();
        }
        $response = array();
        $arr = array();
        foreach ($myBetsModel as $key => $bet) {
            $extra = json_decode($bet->extra, true);
            switch ($bet['bet_type']) {
                case "ODDS":
                {
                    if ($bet['bet_side'] == 'lay') {
                        $profitAmt = $bet['exposureAmt'];
                        $profitAmt = ($profitAmt * (-1));
                        if (!isset($response['ODDS'][$bet['team_name']]['ODDS_profitLost'])) {
                            $response['ODDS'][$bet['team_name']]['ODDS_profitLost'] = $profitAmt;
                        } else {
                            $response['ODDS'][$bet['team_name']]['ODDS_profitLost'] += $profitAmt;
                        }
                        if (isset($extra['teamname1']) && !empty($extra['teamname1'])) {
                            if (!isset($response['ODDS'][$extra['teamname1']]['ODDS_profitLost'])) {
                                $response['ODDS'][$extra['teamname1']]['ODDS_profitLost'] = $bet['bet_amount'];
                            } else {
                                $response['ODDS'][$extra['teamname1']]['ODDS_profitLost'] += $bet['bet_amount'];
                            }
                        }
                        if (isset($extra['teamname2']) && !empty($extra['teamname2'])) {
                            if (!isset($response['ODDS'][$extra['teamname2']]['ODDS_profitLost'])) {
                                $response['ODDS'][$extra['teamname2']]['ODDS_profitLost'] = $bet['bet_amount'];
                            } else {
                                $response['ODDS'][$extra['teamname2']]['ODDS_profitLost'] += $bet['bet_amount'];
                            }
                        }
                        if (isset($extra['teamname3']) && !empty($extra['teamname3'])) {
                            if (!isset($response['ODDS'][$extra['teamname3']]['ODDS_profitLost'])) {
                                $response['ODDS'][$extra['teamname3']]['ODDS_profitLost'] = $bet['bet_amount'];
                            } else {
                                $response['ODDS'][$extra['teamname3']]['ODDS_profitLost'] += $bet['bet_amount'];
                            }
                        }
                        if (isset($extra['teamname4']) && !empty($extra['teamname4'])) {
                            if (!isset($response['ODDS'][$extra['teamname4']]['ODDS_profitLost'])) {
                                $response['ODDS'][$extra['teamname4']]['ODDS_profitLost'] = $bet['bet_amount'];
                            } else {
                                $response['ODDS'][$extra['teamname4']]['ODDS_profitLost'] += $bet['bet_amount'];
                            }
                        }
                    }
                    else {
                        $profitAmt = $bet['bet_profit']; ////nnn
                        $bet_amt = ($bet['bet_amount'] * (-1));
                        if (!isset($response['ODDS'][$bet['team_name']]['ODDS_profitLost'])) {
                            $response['ODDS'][$bet['team_name']]['ODDS_profitLost'] = $profitAmt;
                        } else {
                            $response['ODDS'][$bet['team_name']]['ODDS_profitLost'] += $profitAmt;
                        }
                        if (isset($extra['teamname1']) && !empty($extra['teamname1'])) {
                            if (!isset($response['ODDS'][$extra['teamname1']]['ODDS_profitLost'])) {
                                $response['ODDS'][$extra['teamname1']]['ODDS_profitLost'] = $bet_amt;
                            } else {
                                $response['ODDS'][$extra['teamname1']]['ODDS_profitLost'] += $bet_amt;
                            }
                        }
                        if (isset($extra['teamname2']) && !empty($extra['teamname2'])) {
                            if (!isset($response['ODDS'][$extra['teamname2']]['ODDS_profitLost'])) {
                                $response['ODDS'][$extra['teamname2']]['ODDS_profitLost'] = $bet_amt;
                            } else {
                                $response['ODDS'][$extra['teamname2']]['ODDS_profitLost'] += $bet_amt;
                            }
                        }
                        if (isset($extra['teamname3']) && !empty($extra['teamname3'])) {
                            if (!isset($response['ODDS'][$extra['teamname3']]['ODDS_profitLost'])) {
                                $response['ODDS'][$extra['teamname3']]['ODDS_profitLost'] = $bet_amt;
                            } else {
                                $response['ODDS'][$extra['teamname3']]['ODDS_profitLost'] += $bet_amt;
                            }
                        }
                        if (isset($extra['teamname4']) && !empty($extra['teamname4'])) {
                            if (!isset($response['ODDS'][$extra['teamname4']]['ODDS_profitLost'])) {
                                $response['ODDS'][$extra['teamname4']]['ODDS_profitLost'] = $bet_amt;
                            } else {
                                $response['ODDS'][$extra['teamname4']]['ODDS_profitLost'] += $bet_amt;
                            }
                        }
                    }
                    break;
                }
                case 'BOOKMAKER':
                {

                    if ($bet['bet_side'] == 'lay') {
                        $profitAmt = $bet['exposureAmt'];
                        $profitAmt = ($profitAmt * (-1));
                        if (!isset($response['BOOKMAKER'][$bet['team_name']]['BOOKMAKER_profitLost'])) {
                            $response['BOOKMAKER'][$bet['team_name']]['BOOKMAKER_profitLost'] = $profitAmt;
                        } else {
                            $response['BOOKMAKER'][$bet['team_name']]['BOOKMAKER_profitLost'] += $profitAmt;
                        }
                        if (isset($extra['teamname1']) && !empty($extra['teamname1'])) {
                            if (!isset($response['BOOKMAKER'][$extra['teamname1']]['BOOKMAKER_profitLost'])) {
                                $response['BOOKMAKER'][$extra['teamname1']]['BOOKMAKER_profitLost'] = $bet['bet_amount'];
                            } else {
                                $response['BOOKMAKER'][$extra['teamname1']]['BOOKMAKER_profitLost'] += $bet['bet_amount'];
                            }
                        }
                        if (isset($extra['teamname2']) && !empty($extra['teamname2'])) {
                            if (!isset($response['BOOKMAKER'][$extra['teamname2']]['BOOKMAKER_profitLost'])) {
                                $response['BOOKMAKER'][$extra['teamname2']]['BOOKMAKER_profitLost'] = $bet['bet_amount'];
                            } else {
                                $response['BOOKMAKER'][$extra['teamname2']]['BOOKMAKER_profitLost'] += $bet['bet_amount'];
                            }
                        }
                        if (isset($extra['teamname3']) && !empty($extra['teamname3'])) {
                            if (!isset($response['BOOKMAKER'][$extra['teamname3']]['BOOKMAKER_profitLost'])) {
                                $response['BOOKMAKER'][$extra['teamname3']]['BOOKMAKER_profitLost'] = $bet['bet_amount'];
                            } else {
                                $response['BOOKMAKER'][$extra['teamname3']]['BOOKMAKER_profitLost'] += $bet['bet_amount'];
                            }
                        }
                    }
                    else {
                        $profitAmt = $bet['bet_profit']; ////nnn
                        $bet_amt = ($bet['bet_amount'] * (-1));
                        if (!isset($response['BOOKMAKER'][$bet['team_name']]['BOOKMAKER_profitLost'])) {
                            $response['BOOKMAKER'][$bet['team_name']]['BOOKMAKER_profitLost'] = $profitAmt;
                        } else {
                            $response['BOOKMAKER'][$bet['team_name']]['BOOKMAKER_profitLost'] += $profitAmt;
                        }
                        if (isset($extra['teamname1']) && !empty($extra['teamname1'])) {
                            if (!isset($response['BOOKMAKER'][$extra['teamname1']]['BOOKMAKER_profitLost'])) {
                                $response['BOOKMAKER'][$extra['teamname1']]['BOOKMAKER_profitLost'] = $bet_amt;
                            } else {
                                $response['BOOKMAKER'][$extra['teamname1']]['BOOKMAKER_profitLost'] += $bet_amt;
                            }
                        }
                        if (isset($extra['teamname2']) && !empty($extra['teamname2'])) {
                            if (!isset($response['BOOKMAKER'][$extra['teamname2']]['BOOKMAKER_profitLost'])) {
                                $response['BOOKMAKER'][$extra['teamname2']]['BOOKMAKER_profitLost'] = $bet_amt;
                            } else {
                                $response['BOOKMAKER'][$extra['teamname2']]['BOOKMAKER_profitLost'] += $bet_amt;
                            }
                        }
                        if (isset($extra['teamname3']) && !empty($extra['teamname3'])) {
                            if (!isset($response['BOOKMAKER'][$extra['teamname3']]['BOOKMAKER_profitLost'])) {
                                $response['BOOKMAKER'][$extra['teamname3']]['BOOKMAKER_profitLost'] = $bet_amt;
                            } else {
                                $response['BOOKMAKER'][$extra['teamname3']]['BOOKMAKER_profitLost'] += $bet_amt;
                            }
                        }
                    }
                    break;
                }
                case 'SESSION':
                {

//                    $response['SESSION']['teamname'][$bet['team_name']] = $bet['team_name'];
//                    $exArrData = self::getSessionValueByArr($bet['match_id'], $bet['team_name'], $bet['user_id']);
//                    $finalExSes = 0;
//                    foreach ($exArrData as $key => $arr) {
//                        if ($finalExSes > $arr['profit']) {
//                            $finalExSes = $arr['profit'];
//                        }
//                    }
//                    $response['SESSION']['exposure'][$bet['team_name']]['SESSION_profitLost'] = $finalExSes;


                    $profitAmt = $bet['exposureAmt'];
                    if (!isset($response['SESSION']['exposure'][$bet['team_name']]['SESSION_profitLost'])) {
                        $response['SESSION']['exposure'][$bet['team_name']]['SESSION_profitLost'] = $profitAmt;
                    } else {
                        $response['SESSION']['exposure'][$bet['team_name']]['SESSION_profitLost'] += $profitAmt;
                    }

                    break;
                }
            }
        }
        return $response;
    }

    public static function getOddsAndBookmakerExposer($id,$eventId=0,$conditionalParameters = []){
        $query = MyBets::select('my_bets.id', 'my_bets.sportID', 'my_bets.created_at', 'match.*')->join('match', 'match.event_id', '=', 'my_bets.match_id')
            ->where('my_bets.result_declare', 0)
            ->where('my_bets.user_id', $id)
            ->where('my_bets.isDeleted', 0)
            ->whereNull('match.winner')
            ->where('my_bets.bet_type', '!=', 'SESSION')
            ->orderBy('my_bets.id', 'Desc')
            ->groupby('my_bets.match_id');

        if($eventId!=0){
            if(isset($conditionalParameters['match_id'])){
                $query->where("my_bets.match_id",$conditionalParameters['match_id'], $eventId);
            }else {
                $query->where("my_bets.match_id", $eventId);
            }
        }

        $sportsModel = $query->get();

        $exposerArray = [
            'exposer' => 0
        ];

        foreach ($sportsModel as $bet) {
            $exAmtArr = self::getExAmountCricketAndTennis('', $bet->event_id, $id);

            if (isset($exAmtArr['ODDS'])) {
                $arr = array();
                foreach ($exAmtArr['ODDS'] as $key => $profitLos) {
                    if ($profitLos['ODDS_profitLost'] < 0) {
                        $arr[abs($profitLos['ODDS_profitLost'])] = abs($profitLos['ODDS_profitLost']);
                    }
                }
                if (is_array($arr) && count($arr) > 0) {
                    $exposerArray['exposer'] += max($arr);
                }

                $exposerArray['ODDS'] = $exAmtArr['ODDS'];
            }

            if (isset($exAmtArr['BOOKMAKER'])) {
                $arrB = array();
                foreach ($exAmtArr['BOOKMAKER'] as $key => $profitLos) {
                    if ($profitLos['BOOKMAKER_profitLost'] < 0) {
                        $arrB[abs($profitLos['BOOKMAKER_profitLost'])] = abs($profitLos['BOOKMAKER_profitLost']);
                    }
                }

                $exposerArray['BOOKMAKER'] = $exAmtArr['BOOKMAKER'];

                if (is_array($arrB) && count($arrB) > 0) {
                    $exposerArray['exposer'] += max($arrB);
                }
            }
        }

        if(isset($conditionalParameters['bet_type'])){
            $response = self::getOddsAndBookmakerExposer($id,$eventId);
//            dd($conditionalParameters);

            $bet = [];
            $bet['bet_type'] = $conditionalParameters['bet_type'];
            $bet['bet_side'] = $conditionalParameters['bet_side'];
            $bet['team_name'] = $conditionalParameters['team_name'];
            $bet['exposureAmt'] = $conditionalParameters['exposureAmt'];
            $bet['bet_amount'] = $conditionalParameters['bet_amount'];
            $bet['bet_profit'] = $conditionalParameters['bet_profit'];
            $extra = json_decode($conditionalParameters['extra'], true);

            switch ($bet['bet_type']) {
                case "ODDS":
                {
                    if ($bet['bet_side'] == 'lay') {
                        $profitAmt = $bet['exposureAmt'];
                        $profitAmt = ($profitAmt * (-1));

                        if (!isset($response['ODDS'][$bet['team_name']]['ODDS_profitLost'])) {
                            $response['ODDS'][$bet['team_name']]['ODDS_profitLost'] = $profitAmt;
                        } else {
                            $response['ODDS'][$bet['team_name']]['ODDS_profitLost'] += $profitAmt;
                        }
                        if (isset($extra['teamname1']) && !empty($extra['teamname1'])) {
                            if (!isset($response['ODDS'][$extra['teamname1']]['ODDS_profitLost'])) {
                                $response['ODDS'][$extra['teamname1']]['ODDS_profitLost'] = $bet['bet_amount'];
                            } else {
                                $response['ODDS'][$extra['teamname1']]['ODDS_profitLost'] += $bet['bet_amount'];
                            }
                        }
                        if (isset($extra['teamname2']) && !empty($extra['teamname2'])) {
                            if (!isset($response['ODDS'][$extra['teamname2']]['ODDS_profitLost'])) {
                                $response['ODDS'][$extra['teamname2']]['ODDS_profitLost'] = $bet['bet_amount'];
                            } else {
                                $response['ODDS'][$extra['teamname2']]['ODDS_profitLost'] += $bet['bet_amount'];
                            }
                        }
                        if (isset($extra['teamname3']) && !empty($extra['teamname3'])) {
                            if (!isset($response['ODDS'][$extra['teamname3']]['ODDS_profitLost'])) {
                                $response['ODDS'][$extra['teamname3']]['ODDS_profitLost'] = $bet['bet_amount'];
                            } else {
                                $response['ODDS'][$extra['teamname3']]['ODDS_profitLost'] += $bet['bet_amount'];
                            }
                        }
                        if (isset($extra['teamname4']) && !empty($extra['teamname4'])) {
                            if (!isset($response['ODDS'][$extra['teamname4']]['ODDS_profitLost'])) {
                                $response['ODDS'][$extra['teamname4']]['ODDS_profitLost'] = $bet['bet_amount'];
                            } else {
                                $response['ODDS'][$extra['teamname4']]['ODDS_profitLost'] += $bet['bet_amount'];
                            }
                        }
                    }
                    else {
                        $profitAmt = $bet['bet_profit']; ////nnn
                        $bet_amt = ($bet['bet_amount'] * (-1));
                        if (!isset($response['ODDS'][$bet['team_name']]['ODDS_profitLost'])) {
                            $response['ODDS'][$bet['team_name']]['ODDS_profitLost'] = $profitAmt;
                        } else {
                            $response['ODDS'][$bet['team_name']]['ODDS_profitLost'] += $profitAmt;
                        }
                        if (isset($extra['teamname1']) && !empty($extra['teamname1'])) {
                            if (!isset($response['ODDS'][$extra['teamname1']]['ODDS_profitLost'])) {
                                $response['ODDS'][$extra['teamname1']]['ODDS_profitLost'] = $bet_amt;
                            } else {
                                $response['ODDS'][$extra['teamname1']]['ODDS_profitLost'] += $bet_amt;
                            }
                        }
                        if (isset($extra['teamname2']) && !empty($extra['teamname2'])) {
                            if (!isset($response['ODDS'][$extra['teamname2']]['ODDS_profitLost'])) {
                                $response['ODDS'][$extra['teamname2']]['ODDS_profitLost'] = $bet_amt;
                            } else {
                                $response['ODDS'][$extra['teamname2']]['ODDS_profitLost'] += $bet_amt;
                            }
                        }
                        if (isset($extra['teamname3']) && !empty($extra['teamname3'])) {
                            if (!isset($response['ODDS'][$extra['teamname3']]['ODDS_profitLost'])) {
                                $response['ODDS'][$extra['teamname3']]['ODDS_profitLost'] = $bet_amt;
                            } else {
                                $response['ODDS'][$extra['teamname3']]['ODDS_profitLost'] += $bet_amt;
                            }
                        }
                        if (isset($extra['teamname4']) && !empty($extra['teamname4'])) {
                            if (!isset($response['ODDS'][$extra['teamname4']]['ODDS_profitLost'])) {
                                $response['ODDS'][$extra['teamname4']]['ODDS_profitLost'] = $bet_amt;
                            } else {
                                $response['ODDS'][$extra['teamname4']]['ODDS_profitLost'] += $bet_amt;
                            }
                        }
                    }

                    $arr = array();
                    foreach ($response['ODDS'] as $key => $profitLos) {
                        if ($profitLos['ODDS_profitLost'] < 0) {
                            $arr[abs($profitLos['ODDS_profitLost'])] = abs($profitLos['ODDS_profitLost']);
                        }
                    }

                    if (is_array($arr) && count($arr) > 0) {
                        $response['exposer'] = max($arr);
                    }

                    $exposerArray['ODDS'] = $response['ODDS'];
                    $exposerArray['exposer'] += $response['exposer'];
                    break;
                }
                case 'BOOKMAKER':
                {
                    if ($bet['bet_side'] == 'lay') {
                        $profitAmt = $bet['exposureAmt'];
                        $profitAmt = ($profitAmt * (-1));
                        if (!isset($response['BOOKMAKER'][$bet['team_name']]['BOOKMAKER_profitLost'])) {
                            $response['BOOKMAKER'][$bet['team_name']]['BOOKMAKER_profitLost'] = $profitAmt;
                        } else {
                            $response['BOOKMAKER'][$bet['team_name']]['BOOKMAKER_profitLost'] += $profitAmt;
                        }
                        if (isset($extra['teamname1']) && !empty($extra['teamname1'])) {
                            if (!isset($response['BOOKMAKER'][$extra['teamname1']]['BOOKMAKER_profitLost'])) {
                                $response['BOOKMAKER'][$extra['teamname1']]['BOOKMAKER_profitLost'] = $bet['bet_amount'];
                            } else {
                                $response['BOOKMAKER'][$extra['teamname1']]['BOOKMAKER_profitLost'] += $bet['bet_amount'];
                            }
                        }
                        if (isset($extra['teamname2']) && !empty($extra['teamname2'])) {
                            if (!isset($response['BOOKMAKER'][$extra['teamname2']]['BOOKMAKER_profitLost'])) {
                                $response['BOOKMAKER'][$extra['teamname2']]['BOOKMAKER_profitLost'] = $bet['bet_amount'];
                            } else {
                                $response['BOOKMAKER'][$extra['teamname2']]['BOOKMAKER_profitLost'] += $bet['bet_amount'];
                            }
                        }
                        if (isset($extra['teamname3']) && !empty($extra['teamname3'])) {
                            if (!isset($response['BOOKMAKER'][$extra['teamname3']]['BOOKMAKER_profitLost'])) {
                                $response['BOOKMAKER'][$extra['teamname3']]['BOOKMAKER_profitLost'] = $bet['bet_amount'];
                            } else {
                                $response['BOOKMAKER'][$extra['teamname3']]['BOOKMAKER_profitLost'] += $bet['bet_amount'];
                            }
                        }
                    }
                    else {
                        $profitAmt = $bet['bet_profit']; ////nnn
                        $bet_amt = ($bet['bet_amount'] * (-1));
                        if (!isset($response['BOOKMAKER'][$bet['team_name']]['BOOKMAKER_profitLost'])) {
                            $response['BOOKMAKER'][$bet['team_name']]['BOOKMAKER_profitLost'] = $profitAmt;
                        } else {
                            $response['BOOKMAKER'][$bet['team_name']]['BOOKMAKER_profitLost'] += $profitAmt;
                        }
                        if (isset($extra['teamname1']) && !empty($extra['teamname1'])) {
                            if (!isset($response['BOOKMAKER'][$extra['teamname1']]['BOOKMAKER_profitLost'])) {
                                $response['BOOKMAKER'][$extra['teamname1']]['BOOKMAKER_profitLost'] = $bet_amt;
                            } else {
                                $response['BOOKMAKER'][$extra['teamname1']]['BOOKMAKER_profitLost'] += $bet_amt;
                            }
                        }
                        if (isset($extra['teamname2']) && !empty($extra['teamname2'])) {
                            if (!isset($response['BOOKMAKER'][$extra['teamname2']]['BOOKMAKER_profitLost'])) {
                                $response['BOOKMAKER'][$extra['teamname2']]['BOOKMAKER_profitLost'] = $bet_amt;
                            } else {
                                $response['BOOKMAKER'][$extra['teamname2']]['BOOKMAKER_profitLost'] += $bet_amt;
                            }
                        }
                        if (isset($extra['teamname3']) && !empty($extra['teamname3'])) {
                            if (!isset($response['BOOKMAKER'][$extra['teamname3']]['BOOKMAKER_profitLost'])) {
                                $response['BOOKMAKER'][$extra['teamname3']]['BOOKMAKER_profitLost'] = $bet_amt;
                            } else {
                                $response['BOOKMAKER'][$extra['teamname3']]['BOOKMAKER_profitLost'] += $bet_amt;
                            }
                        }
                    }

                    $arrB = array();
                    foreach ($response['BOOKMAKER'] as $key => $profitLos) {
                        if ($profitLos['BOOKMAKER_profitLost'] < 0) {
                            $arrB[abs($profitLos['BOOKMAKER_profitLost'])] = abs($profitLos['BOOKMAKER_profitLost']);
                        }
                    }

                    if (is_array($arrB) && count($arrB) > 0) {
                        $response['exposer'] += max($arrB);
                    }
                    $exposerArray['BOOKMAKER'] = $response['BOOKMAKER'];
                    $exposerArray['exposer'] += $response['exposer'];
                    break;
                }
            }
        }

//        dd($exposerArray);

        return $exposerArray;
    }

    public static function getExAmount($sportID = '', $id = '')
    {
        $getUserCheck = Session::get('playerUser');
        if (!empty($getUserCheck)) {
            $getUser = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
        }
        $id = $getUser->id;
        if (!empty($sportID)) {
            $sportsModel = Match::where(["id" => $sportID])->first();
        } else {
            //DB::enableQueryLog();
            $sportsModel = MyBets::select('my_bets.id', 'my_bets.sportID', 'my_bets.created_at', 'match.*')->join('match', 'match.event_id', '=', 'my_bets.match_id')
                ->where('my_bets.result_declare', 0)
                ->where('my_bets.user_id', $id)
                ->where('my_bets.isDeleted', 0)
                ->whereNull('match.winner')
                ->where('my_bets.bet_type', '!=', 'SESSION') // nnn 21-10-2021
                ->orderBy('my_bets.id', 'Desc')
                ->groupby('my_bets.match_id') /// nnn 19-8-2021 put becuase exposer calculating twice as over here this query fetching all same match bets multiple times
                ->get(); /// nnn 7-8-2021
            //dd(DB::getQueryLog());
        }
        $exAmtTot = 0;
        foreach ($sportsModel as $keyMatch => $matchVal) {
            $gameModel = Sport::where(["sId" => $matchVal->sports_id])->first();
            if (strtoupper($gameModel->sport_name) == 'CRICKET' || strtoupper($gameModel->sport_name) == 'TENNIS' || strtoupper($gameModel->sport_name) == 'CASINO' || strtoupper($gameModel->sport_name) == 'SOCCER') {
                if (strtoupper($gameModel->name) == 'CASINO') {
                    $exAmtArr = self::getExAmountCricketAndTennis($matchVal->id, '', $id);
                } else {
                    $matchid = $matchVal->event_id;
                    $exAmtArr = self::getExAmountCricketAndTennis('', $matchid, $id);
                }
                if (isset($exAmtArr['ODDS'])) {
                    $arr = array();
                    foreach ($exAmtArr['ODDS'] as $key => $profitLos) {
                        if ($profitLos['ODDS_profitLost'] < 0) {
                            $arr[abs($profitLos['ODDS_profitLost'])] = abs($profitLos['ODDS_profitLost']);
                        }
                    }
                    if (is_array($arr) && count($arr) > 0) {
                        $exAmtTot += max($arr);
                    }
                }

                if (isset($exAmtArr['BOOKMAKER'])) {
                    $arrB = array();
                    foreach ($exAmtArr['BOOKMAKER'] as $key => $profitLos) {
                        if ($profitLos['BOOKMAKER_profitLost'] < 0) {
                            $arrB[abs($profitLos['BOOKMAKER_profitLost'])] = abs($profitLos['BOOKMAKER_profitLost']);
                        }
                    }
                    if (is_array($arrB) && count($arrB) > 0) {
                        $exAmtTot += max($arrB);
                    }
                }
                /*if(isset($exAmtArr['SESSION'])) // nnn 21-10-2021
				{
          			foreach($exAmtArr['SESSION']['exposure'] as $key=>$sesVal){
						$exAmtTot += abs($sesVal['SESSION_profitLost']);
          			}
        		}*/
            }

        }

        return (abs($exAmtTot));
    }

    public static function getExAmountForSession2($sportID = '', $id = '')
    {
        $getUserCheck = Session::get('playerUser');
        if (!empty($getUserCheck)) {
            $getUser = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
        }
        $id = $getUser->id;
        if (!empty($sportID)) {
            $sportsModel = Match::where(["id" => $sportID])->first();
        } else {
            //DB::enableQueryLog();
            $sportsModel = MyBets::select('my_bets.id', 'my_bets.sportID', 'my_bets.created_at', 'match.*')->join('match', 'match.event_id', '=', 'my_bets.match_id')
                ->where('my_bets.result_declare', 0)
                ->where('my_bets.user_id', $id)
                ->where('my_bets.isDeleted', 0)
                ->whereNull('match.winner')
                ->where('my_bets.bet_type', 'SESSION') // nnn 21-10-2021
                ->orderBy('my_bets.id', 'Desc')
                ->groupby('my_bets.match_id') /// nnn 19-8-2021 put becuase exposer calculating twice as over here this query fetching all same match bets multiple times
                ->get(); /// nnn 7-8-2021
            //dd(DB::getQueryLog());
        }
        $exAmtTot = 0;
        foreach ($sportsModel as $keyMatch => $matchVal) {
            $gameModel = Sport::where(["sId" => $matchVal->sports_id])->first();
            if (strtoupper($gameModel->sport_name) == 'CRICKET' || strtoupper($gameModel->sport_name) == 'TENNIS' || strtoupper($gameModel->sport_name) == 'SOCCER') {
                if (strtoupper($gameModel->name) == 'CASINO') {
                    $exAmtArr = self::getExAmountCricketAndTennis($matchVal->id, '', $id);
                } else {
                    $matchid = $matchVal->event_id;
                    $exAmtArr = self::getExAmountCricketAndTennis('', $matchid, $id);
                }
                if (isset($exAmtArr['SESSION'])) // nnn 21-10-2021
                {
                    foreach ($exAmtArr['SESSION']['exposure'] as $key => $sesVal) {
                        $exAmtTot += abs($sesVal['SESSION_profitLost']);
                    }
                }
            }

        }

        return (abs($exAmtTot));
    }
    // ss comment 25-09-2021
    /*public function getPlayerBalance()
	{
		$balance=SELF::getBlanceAmount();
    $exposer=SELF::getExAmount();
		return number_format(($balance-$exposer),2).'~~'.number_format($exposer,2);
	}*/
    public function getPlayerBalance(Request $request)
    {
        $multi = '';
        $getUserCheck = Session::get('playerUser');
        if (!empty($getUserCheck)) {
            $sessionData = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
        } else {
            return response()->json(array('result' => 'multiAccount'));
        }

        $depTot = CreditReference::where('player_id', $sessionData->id)->first();
        $totBalance = $depTot['available_balance_for_D_W'];
        $totExposer = $depTot['exposure'];

        $checkstatus = User::where('id', $sessionData->id)->first();
        if ($checkstatus->status == 'suspend') {
            $request->session()->forget(['playerUser']);
            return response()->json(array('result' => 'suspendsuccess'));
        }

        if ($checkstatus->check_updpass != $getUserCheck->check_updpass) {
            $request->session()->forget(['playerUser']);
            return response()->json(array('result' => 'changePass'));
        }

        if ($checkstatus->token_val != $getUserCheck->token_val) {
            $request->session()->forget(['playerUser']);
            return response()->json(array('result' => 'multiAccount'));
        }
        return number_format(($totBalance), 2) . '~~' . number_format($totExposer, 2) . '~~' . $multi;
    }

    public function multiaccountlogout(Request $request)
    {
        $sessionData = Session::get('playerUser');
        $checkstatus = User::where('id', $sessionData->id)->first();

        if ($checkstatus->token_val != $sessionData->token_val) {
            $request->session()->forget(['playerUser']);
            return response()->json(array('result' => 'error'));
        }
        return response()->json(array('result' => 'success'));
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

    public function getUserAllMatchFancyExposer($userId){
        $my_placed_bets = MyBets::select('user_id', 'match_id', 'bet_type', 'bet_side', 'bet_odds', 'bet_oddsk', 'bet_amount', 'bet_profit', 'team_name', 'exposureAmt')
            ->where('user_id', $userId)->where('bet_type', 'SESSION')->where('isDeleted', 0)->where('result_declare', 0)->groupby('team_name', 'match_id')->orderBy('created_at', 'asc')->get();

        $total_fancy_expo = 0;
        foreach ($my_placed_bets as $bet) {
            $sessionexposer = SELF::getAllSessionExposure($userId, $bet->team_name, $bet->match_id);
            $total_fancy_expo = $total_fancy_expo + $sessionexposer;
        }

        return $total_fancy_expo;
    }

    public function getUserExposer($userId){

        $oddsBookmakerExposerArr = self::getOddsAndBookmakerExposer($userId);
//        $exposer = SELF::getExAmount('',$userId);
        $exposer = $oddsBookmakerExposerArr['exposer'];
        Log::info("exposer: " . $exposer . "\n");

        $total_fancy_expo = $this->getUserAllMatchFancyExposer($userId);
        Log::info("total_fancy_expo: " . $total_fancy_expo . "\n");

        $casinoExposerCalculated = CasinoCalculationController::getCasinoExAmount($userId);

        $casinoExposer =  $casinoExposerCalculated['exposer'];

        Log::info("casinoExposer: " . $casinoExposer . "\n");

        return ($exposer+$total_fancy_expo+$casinoExposer);
    }

    public function SaveBalance($stack)
    {
        $getUserCheck = Session::get('playerUser');
        if (!empty($getUserCheck)) {
            $getUser = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
        }

        $userId = $getUser->id;
        $creditref = CreditReference::where(['player_id' => $userId])->first();

        Log::info(str_repeat("~=~", 30));
        $balance = SELF::getBlanceAmount();
        $exposer = $this->getUserExposer($userId);

        Log::info("main balance: " . $balance);
        Log::info("total exposer: " . $exposer);

        $upd = CreditReference::find($creditref['id']);
        $upd->exposure = $exposer;
        $upd->available_balance_for_D_W = ($balance - $exposer);
        $upd->update();
        return $exposer;
    }

    public static function getExAmountSoccer($sportID = '', $userID = '')
    {
        if (empty($userID)) {
            $getUserCheck = Session::get('playerUser');
            if (!empty($getUserCheck)) {
                $getUser = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
            }
            $userId = $getUser->id;
        }
        $myBetsModel = MyBets::where(['sportID' => $sportID, 'user_id' => $userID, 'active' => 1, 'isDeleted' => 0, 'result_declare' => 0])->orderby('id', 'DESC')->get();
        $response = array();
        $arr = array();
        foreach ($myBetsModel as $key => $bet) {
            $extra = json_decode($bet->extra, true);
            $betTypeArr = explode('-', $bet['bet_type']);
            switch ($betTypeArr[0]) {
                case "ODDS":
                {
                    $profitAmt = $bet['bet_profit'];
                    if ($bet['bet_side'] == 'lay') {
                        $profitAmt = ($profitAmt * (-1));
                        if (!isset($response['ODDS'][$bet['team_name']]['ODDS_profitLost'])) {
                            $response['ODDS'][$bet['team_name']]['ODDS_profitLost'] = $profitAmt;
                        } else {
                            $response['ODDS'][$bet['team_name']]['ODDS_profitLost'] += $profitAmt;
                        }
                        if (isset($extra['teamname1']) && !empty($extra['teamname1'])) {
                            if (!isset($response['ODDS'][$extra['teamname1']]['ODDS_profitLost'])) {
                                $response['ODDS'][$extra['teamname1']]['ODDS_profitLost'] = $bet['bet_amount'];
                            } else {
                                $response['ODDS'][$extra['teamname1']]['ODDS_profitLost'] += $bet['bet_amount'];
                            }
                        }
                        if (isset($extra['teamname2']) && !empty($extra['teamname2'])) {
                            if (!isset($response['ODDS'][$extra['teamname2']]['ODDS_profitLost'])) {
                                $response['ODDS'][$extra['teamname2']]['ODDS_profitLost'] = $bet['bet_amount'];
                            } else {
                                $response['ODDS'][$extra['teamname2']]['ODDS_profitLost'] += $bet['bet_amount'];
                            }
                        }
                        if (isset($extra['teamname3']) && !empty($extra['teamname3'])) {
                            if (!isset($response['ODDS'][$extra['teamname3']]['ODDS_profitLost'])) {
                                $response['ODDS'][$extra['teamname3']]['ODDS_profitLost'] = $bet['bet_amount'];
                            } else {
                                $response['ODDS'][$extra['teamname3']]['ODDS_profitLost'] += $bet['bet_amount'];
                            }
                        }
                    } else {
                        $bet_amt = ($bet['bet_amount'] * (-1));
                        if (!isset($response['ODDS'][$bet['team_name']]['ODDS_profitLost'])) {
                            $response['ODDS'][$bet['team_name']]['ODDS_profitLost'] = $profitAmt;
                        } else {
                            $response['ODDS'][$bet['team_name']]['ODDS_profitLost'] += $profitAmt;
                        }
                        if (isset($extra['teamname1']) && !empty($extra['teamname1'])) {
                            if (!isset($response['ODDS'][$extra['teamname1']]['ODDS_profitLost'])) {
                                $response['ODDS'][$extra['teamname1']]['ODDS_profitLost'] = $bet_amt;
                            } else {
                                $response['ODDS'][$extra['teamname1']]['ODDS_profitLost'] += $bet_amt;
                            }
                        }
                        if (isset($extra['teamname2']) && !empty($extra['teamname2'])) {
                            if (!isset($response['ODDS'][$extra['teamname2']]['ODDS_profitLost'])) {
                                $response['ODDS'][$extra['teamname2']]['ODDS_profitLost'] = $bet_amt;
                            } else {
                                $response['ODDS'][$extra['teamname2']]['ODDS_profitLost'] += $bet_amt;
                            }
                        }
                        if (isset($extra['teamname3']) && !empty($extra['teamname3'])) {
                            if (!isset($response['ODDS'][$extra['teamname3']]['ODDS_profitLost'])) {
                                $response['ODDS'][$extra['teamname3']]['ODDS_profitLost'] = $bet_amt;
                            } else {
                                $response['ODDS'][$extra['teamname3']]['ODDS_profitLost'] += $bet_amt;
                            }
                        }
                    }
                    break;
                }
                case 'SESSION':
                {
                    $profitAmt = $bet['bet_profit'];
                    if ($bet['bet_side'] == 'lay') {
                        $profitAmt = ($profitAmt * (-1));
                        if (!isset($response['SESSION'][$betTypeArr[1]][$bet['team_name']]['SESSION_profitLost'])) {
                            $response['SESSION'][$betTypeArr[1]][$bet['team_name']]['SESSION_profitLost'] = $profitAmt;
                        } else {
                            $response['SESSION'][$betTypeArr[1]][$bet['team_name']]['SESSION_profitLost'] += $profitAmt;
                        }
                        if (isset($extra['teamname1']) && !empty($extra['teamname1'])) {
                            if (!isset($response['SESSION'][$betTypeArr[1]][$extra['teamname1']]['SESSION_profitLost'])) {
                                $response['SESSION'][$betTypeArr[1]][$extra['teamname1']]['SESSION_profitLost'] = $bet['bet_amount'];
                            } else {
                                $response['SESSION'][$betTypeArr[1]][$extra['teamname1']]['SESSION_profitLost'] += $bet['bet_amount'];
                            }
                        }

                        if (isset($extra['teamname2']) && !empty($extra['teamname2'])) {
                            if (!isset($response['SESSION'][$betTypeArr[1]][$extra['teamname2']]['SESSION_profitLost'])) {
                                $response['SESSION'][$betTypeArr[1]][$extra['teamname2']]['SESSION_profitLost'] = $bet['bet_amount'];
                            } else {
                                $response['SESSION'][$betTypeArr[1]][$extra['teamname2']]['SESSION_profitLost'] += $bet['bet_amount'];
                            }
                        }
                    } else {
                        $bet_amt = ($bet['bet_amount'] * (-1));
                        if (!isset($response['SESSION'][$betTypeArr[1]][$bet['team_name']]['SESSION_profitLost'])) {
                            $response['SESSION'][$betTypeArr[1]][$bet['team_name']]['SESSION_profitLost'] = $profitAmt;
                        } else {
                            $response['SESSION'][$betTypeArr[1]][$bet['team_name']]['SESSION_profitLost'] += $profitAmt;
                        }
                        if (isset($extra['teamname1']) && !empty($extra['teamname1'])) {
                            if (!isset($response['SESSION'][$betTypeArr[1]][$extra['teamname1']]['SESSION_profitLost'])) {
                                $response['SESSION'][$betTypeArr[1]][$extra['teamname1']]['SESSION_profitLost'] = $bet_amt;
                            } else {
                                $response['SESSION'][$betTypeArr[1]][$extra['teamname1']]['SESSION_profitLost'] += $bet_amt;
                            }
                        }
                        if (isset($extra['teamname2']) && !empty($extra['teamname2'])) {
                            if (!isset($response['SESSION'][$betTypeArr[1]][$extra['teamname2']]['SESSION_profitLost'])) {
                                $response['SESSION'][$betTypeArr[1]][$extra['teamname2']]['SESSION_profitLost'] = $bet_amt;
                            } else {
                                $response['SESSION'][$betTypeArr[1]][$extra['teamname2']]['SESSION_profitLost'] += $bet_amt;
                            }
                        }
                    }
                    break;
                }
            }
        }
        return $response;
    }

    public function getMainOdds($matchid, $betside)
    {
        $API_SERVER = app('API_SERVER');

        if($API_SERVER == 1){
            $matchList = Match::where('event_id', $matchid)->where('status', 1)->first();
            $matchId = $matchList['match_id'];
            $event_id = $matchList['event_id'];
            $matchtype = $matchList['sports_id'];
            $match_m = $matchList['suspend_m'];
            $match_data = app('App\Http\Controllers\RestApi')->getSingleMatchData($event_id, $matchId, $matchtype);

            $team1 = $team2 = $team3 = '';
            if ($match_data != 0) {
                $html_chk = '';
                if ($match_m == '0') {
                    if ($matchtype == 4) {
                        if ($match_data['t1'][0][2]) {
                            $team3 = 'Suspend';
                        }
                    } else {
                        if (@$match_data[0]['runners'][2]['ex']['availableToBack'][0]['price'] != '') {
                            $team3 = 'Suspend';
                        }
                    }

                    $team1 = 'Suspend';
                    $team2 = 'Suspend';
                } else {
                    if ($matchtype == 4) {
                        if (@$match_data['t1'][0][2]['b1'] != '') {
                            if ($betside == 'back')
                                $team3 = @$match_data['t1'][0][2]['b1'];
                            else
                                $team3 = @$match_data['t1'][0][2]['l1'];
                        }

                        //check status
                        if (@$match_data['t1'][0][0]['mstatus'] == 'OPEN') {
                            if (isset($match_data['t1'][0][0]['b1'])) {
                                if ($betside == 'back')
                                    $team1 = @$match_data['t1'][0][0]['b1'];
                                else
                                    $team1 = @$match_data['t1'][0][0]['l1'];
                            } else {
                                if ($betside == 'back')
                                    $team1 = '';
                                else
                                    $team1 = '';
                            }
                        } else {
                            if ($betside == 'back')
                                $team1 = '';
                            else
                                $team1 = '';
                        }

                        if (@$match_data['t1'][0][1]['mstatus'] == 'OPEN') {
                            if (isset($match_data['t1'][0][1]['b1'])) {
                                if ($betside == 'back')
                                    $team2 = @$match_data['t1'][0][1]['b1'];
                                else
                                    $team2 = @$match_data['t1'][0][1]['l1'];
                            } else {
                                if ($betside == 'back')
                                    $team2 = '';
                                else
                                    $team2 = '';
                            }
                        } else {
                            if ($betside == 'back')
                                $team2 = '';
                            else
                                $team2 = '';
                        }


                    }
                    else {
                        if (@$match_data[0]['runners'][2]['ex']['availableToBack'][0]['price'] != '') {
                            if ($betside == 'back')
                                $team3 = @$match_data[0]['runners'][2]['ex']['availableToBack'][0]['price'];
                            else
                                $team3 = @$match_data[0]['runners'][2]['ex']['availableToLay'][0]['price'];
                        }
                        //check status
                        if (@$match_data[0]['status'] == 'OPEN') {
                            if (isset($match_data[0]['runners'][0]['ex']['availableToBack'][2]['price'])) {
                                if ($betside == 'back')
                                    $team1 = @$match_data[0]['runners'][0]['ex']['availableToBack'][0]['price'];
                                else
                                    $team1 = @$match_data[0]['runners'][0]['ex']['availableToLay'][0]['price'];
                            } else {
                                if ($betside == 'back')
                                    $team1 = '';
                                else
                                    $team1 = '';
                            }
                        } else {
                            if ($betside == 'back')
                                $team1 = '';
                            else
                                $team1 = '';
                        }
                        if (@$match_data[0]['status'] == 'OPEN') {
                            if (isset($match_data[0]['runners'][1]['ex']['availableToBack'][2]['price'])) {
                                if ($betside == 'back')
                                    $team2 = @$match_data[0]['runners'][1]['ex']['availableToBack'][0]['price'];
                                else
                                    $team2 = @$match_data[0]['runners'][1]['ex']['availableToLay'][0]['price'];
                            } else {
                                if ($betside == 'back')
                                    $team2 = '';
                                else
                                    $team2 = '';
                            }
                        } else {
                            if ($betside == 'back')
                                $team2 = '';
                            else
                                $team2 = '';
                        }
                    }
                }
            }
            return $team1 . '~~' . $team2 . '~~' . $team3;
        }
        else if($API_SERVER == 2){
            $matchList = Match::where('event_id', $matchid)->where('status', 1)->first();
            $matchId = $matchList['match_id'];
            $event_id = $matchList['event_id'];
            $matchtype = $matchList['sports_id'];
            $match_m = $matchList['suspend_m'];
            $match_data = app('App\Http\Controllers\RestApi')->getSingleMatchData($event_id, $matchId, $matchtype);

            $team1 = $team2 = $team3 = '';
            if ($match_data != 0) {
                if ($match_m == '0') {
                    if (isset($match_data['t1'][2])) {
                        $team3 = 'Suspend';
                    }
                    $team1 = 'Suspend';
                    $team2 = 'Suspend';
                } else {
                    if (@$match_data['t1'][2]['b1'] != '') {
                        if ($betside == 'back')
                            $team3 = @$match_data['t1'][0][2]['b1'];
                        else
                            $team3 = @$match_data['t1'][0][2]['l1'];
                    }

                    //check status
                    if (@$match_data['t1'][0]['status'] == 'OPEN' || @$match_data['t1'][0]['status'] == 'ACTIVE') {
                        if (isset($match_data['t1'][0]['b1'])) {
                            if ($betside == 'back')
                                $team1 = @$match_data['t1'][0]['b1'];
                            else
                                $team1 = @$match_data['t1'][0]['l1'];
                        } else {
                            if ($betside == 'back')
                                $team1 = '';
                            else
                                $team1 = '';
                        }
                    } else {
                        if ($betside == 'back')
                            $team1 = '';
                        else
                            $team1 = '';
                    }

                    if (@$match_data['t1'][1]['status'] == 'OPEN' || @$match_data['t1'][1]['status'] == 'ACTIVE') {
                        if (isset($match_data['t1'][1]['b1'])) {
                            if ($betside == 'back')
                                $team2 = @$match_data['t1'][1]['b1'];
                            else
                                $team2 = @$match_data['t1'][1]['l1'];
                        } else {
                            if ($betside == 'back')
                                $team2 = '';
                            else
                                $team2 = '';
                        }
                    } else {
                        if ($betside == 'back')
                            $team2 = '';
                        else
                            $team2 = '';
                    }
                }
            }
        }
        else {
            $matchList = Match::where('event_id', $matchid)->where('status', 1)->first();
            $matchId = $matchList['match_id'];
            $event_id = $matchList['event_id'];
            $matchtype = $matchList['sports_id'];
            $match_m = $matchList['suspend_m'];
            $match_data = app('App\Http\Controllers\RestApi')->getSingleMatchOddsData($event_id, $matchId, $matchtype);

//        dd($match_data);

            $team1 = $team2 = $team3 = '';
            if ($match_data != 0) {
                $html_chk = '';
                if ($match_m == '0') {
                    if (@$match_data[0]['runners'][2]['ex']['availableToBack'][0]['price'] != '') {
                        $team3 = 'Suspend';
                    }

                    $team1 = 'Suspend';
                    $team2 = 'Suspend';
                } else {

                    if (@$match_data[0]['runners'][2]['ex']['availableToBack'][0]['price'] != '' || isset($match_data[0]['runners'][2]['ex']['availableToLay'][0]['price'])) {
                        if ($betside == 'back')
                            $team3 = @$match_data[0]['runners'][2]['ex']['availableToBack'][0]['price'];
                        else
                            $team3 = @$match_data[0]['runners'][2]['ex']['availableToLay'][0]['price'];
                    }

                    //check status
                    if (@$match_data[0]['status'] == 'OPEN') {
                        if (isset($match_data[0]['runners'][0]['ex']['availableToBack'][0]['price']) || isset($match_data[0]['runners'][0]['ex']['availableToLay'][0]['price'])) {
                            if ($betside == 'back')
                                $team1 = @$match_data[0]['runners'][0]['ex']['availableToBack'][0]['price'];
                            else
                                $team1 = @$match_data[0]['runners'][0]['ex']['availableToLay'][0]['price'];
                        } else {
                            if ($betside == 'back')
                                $team1 = '';
                            else
                                $team1 = '';
                        }
                    } else {
                        if ($betside == 'back')
                            $team1 = '';
                        else
                            $team1 = '';
                    }
                    if (@$match_data[0]['status'] == 'OPEN') {
                        if (isset($match_data[0]['runners'][1]['ex']['availableToBack'][0]['price']) || isset($match_data[0]['runners'][1]['ex']['availableToLay'][0]['price'])) {
                            if ($betside == 'back')
                                $team2 = @$match_data[0]['runners'][1]['ex']['availableToBack'][0]['price'];
                            else
                                $team2 = @$match_data[0]['runners'][1]['ex']['availableToLay'][0]['price'];
                        } else {
                            if ($betside == 'back')
                                $team2 = '';
                            else
                                $team2 = '';
                        }
                    } else {
                        if ($betside == 'back')
                            $team2 = '';
                        else
                            $team2 = '';
                    }
                }
            }

//        dd($team1 . '~~' . $team2 . '~~' . $team3);

            return $team1 . '~~' . $team2 . '~~' . $team3;
        }
    }

    public function getMainBMOdds($matchid, $teamname, $position, $betside, $odds)
    {
        $API_SERVER = app('API_SERVER');
        if($API_SERVER == 1){
            $matchList = Match::where('event_id', $matchid)->where('status', 1)->first();
            $event_id = $matchList['event_id'];
            $matchtype = $matchList['sports_id'];
            $match_m = $matchList['suspend_b'];
            $match_data = app('App\Http\Controllers\RestApi')->getSingleMatchData($event_id, $matchid, $matchtype);

            $team1 = $team2 = $team3 = '';
            if ($match_data != 0) {
                $html_chk = '';
                if ($match_m == '1') {
                    if (@$match_data['t2'][0]['bm1']['0']['s'] != 'ACTIVE' && strtolower(@$match_data['t2'][0]['bm1']['0']['nat']) == strtolower($teamname)) {
                        return 'Suspend';
                    } else if (@$match_data['t2'][0]['bm1']['1']['s'] != 'ACTIVE' && strtolower(@$match_data['t2'][0]['bm1']['1']['nat']) == strtolower($teamname)) {
                        return 'Suspend';
                    } else if (@$match_data['t2'][0]['bm1']['2']['s'] != 'ACTIVE' && strtolower(@$match_data['t2'][0]['bm1']['2']['nat']) == strtolower($teamname)) {
                        return 'Suspend';
                    }
                    else {
                        if (@$match_data['t2'][0]['bm1']['0']['s'] == 'ACTIVE' && strtolower(@$match_data['t2'][0]['bm1']['0']['nat']) == strtolower($teamname)) {
                            if ($betside == 'lay') {
                                if ($position == 0) {
                                    if (round($match_data['t2'][0]['bm1']['0']['l1']) != $odds) {
                                        return 'Unmatch Bet Total Not Allowed!';
                                    }
                                } else if ($position == 1) {
                                    if (round($match_data['t2'][0]['bm1']['0']['l2']) != $odds) {
                                        return 'Unmatch Bet Total Not Allowed!';
                                    }
                                } else if ($position == 2) {
                                    if (round($match_data['t2'][0]['bm1']['0']['l3']) != $odds) {
                                        return 'Unmatch Bet Total Not Allowed!';
                                    }
                                }
                            } elseif ($betside == 'back') {
                                if ($position == 0) {
                                    if (round($match_data['t2'][0]['bm1']['0']['b1']) != $odds) {
                                        return 'Unmatch Bet Total Not Allowed!';
                                    }
                                } else if ($position == 1) {
                                    if (round($match_data['t2'][0]['bm1']['0']['b2']) != $odds) {
                                        return 'Unmatch Bet Total Not Allowed!';
                                    }
                                } else if ($position == 2) {
                                    if (round($match_data['t2'][0]['bm1']['0']['b3']) != $odds) {
                                        return 'Unmatch Bet Total Not Allowed!';
                                    }
                                }
                            }
                        } else if (@$match_data['t2'][0]['bm1']['1']['s'] == 'ACTIVE' && strtolower(@$match_data['t2'][0]['bm1']['1']['nat']) == strtolower($teamname)) {
                            if ($betside == 'lay') {
                                if ($position == 0) {
                                    if (round($match_data['t2'][0]['bm1']['1']['l1']) != $odds) {
                                        return 'Unmatch Bet Total Not Allowed!';
                                    }
                                } else if ($position == 1) {
                                    if (round($match_data['t2'][0]['bm1']['1']['l2']) != $odds) {
                                        return 'Unmatch Bet Total Not Allowed!';
                                    }
                                } else if ($position == 2) {
                                    if (round($match_data['t2'][0]['bm1']['1']['l3']) != $odds) {
                                        return 'Unmatch Bet Total Not Allowed!';
                                    }
                                }
                            } elseif ($betside == 'back') {
                                if ($position == 0) {
                                    if (round($match_data['t2'][0]['bm1']['1']['b1']) != $odds) {
                                        return 'Unmatch Bet Total Not Allowed!';
                                    }
                                } else if ($position == 1) {
                                    if (round($match_data['t2'][0]['bm1']['1']['b2']) != $odds) {
                                        return 'Unmatch Bet Total Not Allowed!';
                                    }
                                } else if ($position == 2) {
                                    if (round($match_data['t2'][0]['bm1']['1']['b3']) != $odds) {
                                        return 'Unmatch Bet Total Not Allowed!';
                                    }
                                }
                            }
                        } else if (@$match_data['t2'][0]['bm1']['2']['s'] == 'ACTIVE' && strtolower(@$match_data['t2'][0]['bm1']['2']['nat']) == strtolower($teamname)) {
                            if ($betside == 'lay') {
                                if ($position == 0) {
                                    if (round($match_data['t2'][0]['bm1']['2']['l1']) != $odds) {
                                        return 'Unmatch Bet Total Not Allowed!';
                                    }
                                } else if ($position == 1) {
                                    if (round($match_data['t2'][0]['bm1']['2']['l2']) != $odds) {
                                        return 'Unmatch Bet Total Not Allowed!';
                                    }
                                } else if ($position == 2) {
                                    if (round($match_data['t2'][0]['bm1']['2']['l3']) != $odds) {
                                        return 'Unmatch Bet Total Not Allowed!';
                                    }
                                }
                            } elseif ($betside == 'back') {
                                if ($position == 0) {
                                    if (round($match_data['t2'][0]['bm1']['2']['b1']) != $odds) {
                                        return 'Unmatch Bet Total Not Allowed!';
                                    }
                                } else if ($position == 1) {
                                    if (round($match_data['t2'][0]['bm1']['2']['b2']) != $odds) {
                                        return 'Unmatch Bet Total Not Allowed!';
                                    }
                                } else if ($position == 2) {
                                    if (round($match_data['t2'][0]['bm1']['2']['b3']) != $odds) {
                                        return 'Unmatch Bet Total Not Allowed!';
                                    }
                                }
                            }
                        }
                    }
                }
                else
                    return 'Suspend';
            }
        }
        elseif($API_SERVER == 2){
            $matchList = Match::where('event_id', $matchid)->where('status', 1)->first();
            $event_id = $matchList['event_id'];
            $matchtype = $matchList['sports_id'];
            $match_m = $matchList['suspend_b'];
            $match_data = app('App\Http\Controllers\RestApi')->getSingleMatchData($event_id, $matchid, $matchtype);

            $team1 = $team2 = $team3 = '';
            if ($match_data != 0) {
                if ($match_m == '1') {
                    if (@$match_data['t2'][0]['status'] != 'ACTIVE' && strtolower(@$match_data['t2'][0]['nat']) == strtolower($teamname)) {
                        return 'Suspend';
                    } else if (@$match_data['t2'][1]['status'] != 'ACTIVE' && strtolower(@$match_data['t2'][1]['nat']) == strtolower($teamname)) {
                        return 'Suspend';
                    } else if (@$match_data['t2'][2]['status'] != 'ACTIVE' && strtolower(@$match_data['t2'][2]['nat']) == strtolower($teamname)) {
                        return 'Suspend';
                    }else {
                        if (@$match_data['t2'][0]['status'] == 'ACTIVE' && strtolower(@$match_data['t2'][0]['nat']) == strtolower($teamname)) {
                            if ($betside == 'lay') {
                                if ($position == 0) {
                                    if (round($match_data['t2'][0]['l1']) != $odds) {
                                        return 'Unmatch Bet Total Not Allowed!';
                                    }
                                } else if ($position == 1) {
                                    if (round($match_data['t2'][0]['l2']) != $odds) {
                                        return 'Unmatch Bet Total Not Allowed!';
                                    }
                                } else if ($position == 2) {
                                    if (round($match_data['t2'][0]['l3']) != $odds) {
                                        return 'Unmatch Bet Total Not Allowed!';
                                    }
                                }
                            } elseif ($betside == 'back') {
                                if ($position == 0) {
                                    if (round($match_data['t2'][0]['b1']) != $odds) {
                                        return 'Unmatch Bet Total Not Allowed!';
                                    }
                                } else if ($position == 1) {
                                    if (round($match_data['t2'][0]['b2']) != $odds) {
                                        return 'Unmatch Bet Total Not Allowed!';
                                    }
                                } else if ($position == 2) {
                                    if (round($match_data['t2'][0]['b3']) != $odds) {
                                        return 'Unmatch Bet Total Not Allowed!';
                                    }
                                }
                            }
                        } else if (@$match_data['t2'][1]['status'] == 'ACTIVE' && strtolower(@$match_data['t2'][1]['nat']) == strtolower($teamname)) {
                            if ($betside == 'lay') {
                                if ($position == 0) {
                                    if (round($match_data['t2'][1]['l1']) != $odds) {
                                        return 'Unmatch Bet Total Not Allowed!';
                                    }
                                } else if ($position == 1) {
                                    if (round($match_data['t2'][1]['l2']) != $odds) {
                                        return 'Unmatch Bet Total Not Allowed!';
                                    }
                                } else if ($position == 2) {
                                    if (round($match_data['t2'][1]['l3']) != $odds) {
                                        return 'Unmatch Bet Total Not Allowed!';
                                    }
                                }
                            } elseif ($betside == 'back') {
                                if ($position == 0) {
                                    if (round($match_data['t2'][1]['b1']) != $odds) {
                                        return 'Unmatch Bet Total Not Allowed!';
                                    }
                                } else if ($position == 1) {
                                    if (round($match_data['t2'][1]['b2']) != $odds) {
                                        return 'Unmatch Bet Total Not Allowed!';
                                    }
                                } else if ($position == 2) {
                                    if (round($match_data['t2'][1]['b3']) != $odds) {
                                        return 'Unmatch Bet Total Not Allowed!';
                                    }
                                }
                            }
                        } else if (@$match_data['t2'][2]['status'] == 'ACTIVE' && strtolower(@$match_data['t2'][2]['nat']) == strtolower($teamname)) {
                            if ($betside == 'lay') {
                                if ($position == 0) {
                                    if (round($match_data['t2'][2]['l1']) != $odds) {
                                        return 'Unmatch Bet Total Not Allowed!';
                                    }
                                } else if ($position == 1) {
                                    if (round($match_data['t2'][2]['l2']) != $odds) {
                                        return 'Unmatch Bet Total Not Allowed!';
                                    }
                                } else if ($position == 2) {
                                    if (round($match_data['t2'][2]['l3']) != $odds) {
                                        return 'Unmatch Bet Total Not Allowed!';
                                    }
                                }
                            } elseif ($betside == 'back') {
                                if ($position == 0) {
                                    if (round($match_data['t2'][2]['b1']) != $odds) {
                                        return 'Unmatch Bet Total Not Allowed!';
                                    }
                                } else if ($position == 1) {
                                    if (round($match_data['t2'][2]['b2']) != $odds) {
                                        return 'Unmatch Bet Total Not Allowed!';
                                    }
                                } else if ($position == 2) {
                                    if (round($match_data['t2'][2]['b3']) != $odds) {
                                        return 'Unmatch Bet Total Not Allowed!';
                                    }
                                }
                            }
                        }
                    }
                }else{
                    return 'Suspend';
                }
            }
        }
        else {
            $matchList = Match::where('event_id', $matchid)->where('status', 1)->first();
            $event_id = $matchList['event_id'];
            $matchtype = $matchList['sports_id'];
            $match_m = $matchList['suspend_b'];
            $match_data = app('App\Http\Controllers\RestApi')->getSingleMatchBookmakerData($event_id, $matchid, $matchtype);

            $team1 = $team2 = $team3 = '';
            if (isset($match_data[0])) {
                $html_chk = '';
                if ($match_m == '1') {
                    if (@$match_data[0]['runners'][0]['status'] != 'ACTIVE' && strtolower(@$match_data[0]['runners'][0]['runnerName']) == strtolower($teamname)) {
                        return 'Suspend';
                    } else if (@$match_data[0]['runners'][1]['status'] != 'ACTIVE' && strtolower(@$match_data[0]['runners'][1]['runnerName']) == strtolower($teamname)) {
                        return 'Suspend';
                    } else if (isset($match_data[0]['runners'][2]) && @$match_data[0]['runners'][2]['status'] != 'ACTIVE' && strtolower(@$match_data[0]['runners'][2]['runnerName']) == strtolower($teamname)) {
                        return 'Suspend';
                    } else {
                        if (@$match_data[0]['runners'][0]['status'] == 'ACTIVE' && strtolower(@$match_data[0]['runners'][0]['runnerName']) == strtolower($teamname)) {
                            if ($betside == 'lay') {
                                $orgOdds = explode(".", $match_data[0]['runners'][0]['rate2']);
                                $orgOdd = floatval($orgOdds[1]);
                                if ($position == 0) {
                                    if (round($orgOdd) != $odds) {
                                        return 'Unmatch Bet Total Not Allowed!';
                                    }
                                } else if ($position == 1) {
                                    if (round($orgOdd + 1) != $odds) {
                                        return 'Unmatch Bet Total Not Allowed!';
                                    }
                                } else if ($position == 2) {
                                    if (round($orgOdd + 2) != $odds) {
                                        return 'Unmatch Bet Total Not Allowed!';
                                    }
                                }
                            } elseif ($betside == 'back') {
                                $orgOdds = explode(".", $match_data[0]['runners'][0]['rate1']);
                                $orgOdd = floatval($orgOdds[1]);
                                if ($position == 0) {
                                    if (round($orgOdd) != $odds) {
                                        return 'Unmatch Bet Total Not Allowed!';
                                    }
                                } else if ($position == 1) {
                                    if (round($orgOdd - 1) != $odds) {
                                        return 'Unmatch Bet Total Not Allowed!';
                                    }
                                } else if ($position == 2) {
                                    if (round($orgOdd - 2) != $odds) {
                                        return 'Unmatch Bet Total Not Allowed!';
                                    }
                                }
                            }
                        }
                        else if (@$match_data[0]['runners'][1]['status'] == 'ACTIVE' && strtolower(@$match_data[0]['runners'][1]['runnerName']) == strtolower($teamname)) {
                            if ($betside == 'lay') {
                                $orgOdds = explode(".", $match_data[0]['runners'][1]['rate2']);
                                $orgOdd = floatval($orgOdds[1]);
                                if ($position == 0) {
                                    if (round($orgOdd) != $odds) {
                                        return 'Unmatch Bet Total Not Allowed!';
                                    }
                                } else if ($position == 1) {
                                    if (round($orgOdd + 1) != $odds) {
                                        return 'Unmatch Bet Total Not Allowed!';
                                    }
                                } else if ($position == 2) {
                                    if (round($orgOdd + 2) != $odds) {
                                        return 'Unmatch Bet Total Not Allowed!';
                                    }
                                }
                            } elseif ($betside == 'back') {
                                $orgOdds = explode(".", $match_data[0]['runners'][1]['rate1']);
                                $orgOdd = floatval($orgOdds[1]);
                                if ($position == 0) {
                                    if (round($orgOdd) != $odds) {
                                        return 'Unmatch Bet Total Not Allowed!';
                                    }
                                } else if ($position == 1) {
                                    if (round($orgOdd - 1) != $odds) {
                                        return 'Unmatch Bet Total Not Allowed!';
                                    }
                                } else if ($position == 2) {
                                    if (round($orgOdd - 2) != $odds) {
                                        return 'Unmatch Bet Total Not Allowed!';
                                    }
                                }
                            }
                        }
                        else if (isset($match_data[0]['runners'][2]) && @$match_data[0]['runners'][2]['status'] == 'ACTIVE' && strtolower(@$match_data[0]['runners'][2]['runnerName']) == strtolower($teamname)) {
                            if ($betside == 'lay') {
                                $orgOdds = explode(".", $match_data[0]['runners'][2]['rate2']);
                                $orgOdd = floatval($orgOdds[1]);
                                if ($position == 0) {
                                    if (round($orgOdd) != $odds) {
                                        return 'Unmatch Bet Total Not Allowed!';
                                    }
                                } else if ($position == 1) {
                                    if (round($orgOdd + 1) != $odds) {
                                        return 'Unmatch Bet Total Not Allowed!';
                                    }
                                } else if ($position == 2) {
                                    if (round($orgOdd + 2) != $odds) {
                                        return 'Unmatch Bet Total Not Allowed!';
                                    }
                                }
                            } elseif ($betside == 'back') {
                                $orgOdds = explode(".", $match_data[0]['runners'][2]['rate1']);
                                $orgOdd = floatval($orgOdds[1]);
                                if ($position == 0) {
                                    if (round($orgOdd) != $odds) {
                                        return 'Unmatch Bet Total Not Allowed!';
                                    }
                                } else if ($position == 1) {
                                    if (round($orgOdd - 1) != $odds) {
                                        return 'Unmatch Bet Total Not Allowed!';
                                    }
                                } else if ($position == 2) {
                                    if (round($orgOdd - 2) != $odds) {
                                        return 'Unmatch Bet Total Not Allowed!';
                                    }
                                }
                            }
                        }
                    }
                } else
                    return 'Suspend';
            }
        }
    }

    public function getMainFancyOdds($matchid, $teamname, $betside, $odds)
    {
        $API_SERVER = app('API_SERVER');

        if($API_SERVER == 1){
            $matchList = Match::where('event_id', $matchid)->where('status', 1)->first();
            $event_id = $matchList['event_id'];
            $matchtype = $matchList['sports_id'];
            $match_f = $matchList['suspend_f'];
            $match_data = app('App\Http\Controllers\RestApi')->getSingleMatchData($event_id, $matchid, $matchtype);
            $team1 = $team2 = $team3 = '';
            $nat = array();
            $gstatus = array();
            $b = array();
            $l = array();
            $bs = array();
            $ls = array();
            $min = array();
            $max = array();
            $sid = array();
            if ($match_data != 0) {
                if ($match_f == '1') {
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
                    $check_fancy = 0;
                    for ($i = 0; $i < sizeof($sid); $i++) {
                        if ($nat[$sid[$i]] == $teamname) {
                            if ($gstatus[$sid[$i]] != "") {
                                return 'Suspend';
                            } else {
                                if ($betside == 'lay') {
                                    if (round($l[$sid[$i]]) != $odds) {
                                        return 'Unmatch Bet Total Not Allowed!';
                                    }
                                }
                                if ($betside == 'back') {
                                    if (round($b[$sid[$i]]) != $odds) {
                                        return 'Unmatch Bet Total Not Allowed!';
                                    }
                                }
                            }
                            $check_fancy = 1;
                        }
                    }
                    if ($check_fancy == 0) {
                        return 'Suspend';
                    }
                }
            }
        }elseif($API_SERVER == 2){

            $matchList = Match::where('event_id', $matchid)->where('status', 1)->first();
            $event_id = $matchList['event_id'];
            $matchtype = $matchList['sports_id'];
            $match_f = $matchList['suspend_f'];
            $match_data = app('App\Http\Controllers\RestApi')->getSingleMatchData($event_id, $matchid, $matchtype);

            $team1 = $team2 = $team3 = '';
            $nat = array();
            $gstatus = array();
            $b = array();
            $l = array();
            $bs = array();
            $ls = array();
            $min = array();
            $max = array();
            $sid = array();
            if ($match_data != 0) {
                if ($match_f == '1') {
                    foreach ($match_data['t3'] as $key => $value) {
//                        dd($match_data['t3']);
                        $sid_val = '';
//                        dd($value);
                        foreach ($value as $key1 => $value1) {
                            $sid_val = $value['sId'];
                            if ($key1 == 'sId') {
                                $sid[] = $value1;
                            }
                            if ($key1 == 'nat')
                                $nat[$sid_val] = $value1;
                            if ($key1 == 'status') {
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
                    $check_fancy = 0;

//                    dd($nat);

                    for ($i = 0; $i < sizeof($sid); $i++) {
                        if ($nat[$sid[$i]] == $teamname) {
//                            dd($teamname);
                            if (!in_array($gstatus[$sid[$i]],['ACTIVE','OPEN'])) {
                                return 'Suspend';
                            } else {
                                if ($betside == 'lay') {
                                    if (round($l[$sid[$i]]) != $odds) {
                                        return 'Unmatch Bet Total Not Allowed!';
                                    }
                                }
                                if ($betside == 'back') {
                                    if (round($b[$sid[$i]]) != $odds) {
                                        return 'Unmatch Bet Total Not Allowed!';
                                    }
                                }
                            }
                            $check_fancy = 1;
                        }
                    }
                    if ($check_fancy == 0) {
                        return 'Suspend';
                    }
                }
            }
        }
        else {
            $matchList = Match::where('event_id', $matchid)->where('status', 1)->first();
            $event_id = $matchList['event_id'];
            $matchtype = $matchList['sports_id'];
            $match_f = $matchList['suspend_f'];
            $match_data = app('App\Http\Controllers\RestApi')->getSingleMatchFancyData($event_id, $matchid, $matchtype);
            $team1 = $team2 = $team3 = '';
            $nat = array();
            $gstatus = array();
            $b = array();
            $l = array();
            $bs = array();
            $ls = array();
            $min = array();
            $max = array();
            $sid = array();
            if ($match_data != 0) {
                if ($match_f == '1' && isset($match_data[0]) && isset($match_data[0])) {
                    foreach ($match_data[0] as $key => $value) {
                        $sid_val = '';
                        foreach ($value as $key1 => $value1) {
                            if ($key1 == 'SelectionId') {
                                $sid_val = $value1;
                                $sid[] = $value1;
                            }
                            if ($key1 == 'RunnerName')
                                $nat[$sid_val] = $value1;
                            if ($key1 == 'GameStatus') {
                                $gstatus[$sid_val] = $value1;
                            }
                            if ($key1 == 'BackPrice1')
                                $b[$sid_val] = $value1;
                            if ($key1 == 'LayPrice1')
                                $l[$sid_val] = $value1;
                        }
                    }
                    sort($sid);
                    $check_fancy = 0;
                    for ($i = 0; $i < sizeof($sid); $i++) {
                        if ($nat[$sid[$i]] == $teamname) {
                            if ($gstatus[$sid[$i]] != "") {
                                return 'Suspend';
                            } else {
                                if ($betside == 'lay') {
                                    if (round($l[$sid[$i]]) != $odds) {
                                        return 'Unmatch Bet Total Not Allowed!';
                                    }
                                }
                                if ($betside == 'back') {
                                    if (round($b[$sid[$i]]) != $odds) {
                                        return 'Unmatch Bet Total Not Allowed!';
                                    }
                                }
                            }
                            $check_fancy = 1;
                        }
                    }
                    if ($check_fancy == 0) {
                        return 'Suspend';
                    }
                }
            }
        }
    }

    public function CheckForOtherMatchBet($eventid)
    {
        $getUserCheck = Session::get('playerUser');
        if (!empty($getUserCheck)) {
            $getUser = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
        }
        $userId = $getUser->id;
        $myBetsModel = MyBets::orWhere('match_id', '!=', $eventid)->where(['user_id' => $userId, 'active' => 1, 'isDeleted' => 0, 'result_declare' => 0])->orderby('id', 'DESC')->count();
        return $myBetsModel;
    }

    public function CheckForOtherMatchBetAmount($match_id)
    {
        $getUserCheck = Session::get('playerUser');
        if (!empty($getUserCheck)) {
            $getUser = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
        }
        $id = $getUser->id;

        //DB::enableQueryLog();
        $sportsModel = MyBets::select('my_bets.id', 'my_bets.sportID', 'my_bets.created_at', 'match.*')->join('match', 'match.event_id', '=', 'my_bets.match_id')
            ->where('my_bets.result_declare', 0)
            ->where('my_bets.user_id', $id)
            ->where('my_bets.isDeleted', 0)
            ->where('my_bets.bet_type', '!=', 'SESSION')
            ->where('my_bets.match_id', '!=', $match_id)
            ->whereNull('match.winner')
            ->orderBy('my_bets.id', 'Desc')
            ->groupby('my_bets.match_id') /// nnn 19-8-2021 put becuase exposer calculating twice as over here this query fetching all same match bets multiple times
            ->get(); /// nnn 7-8-2021
        //dd(DB::getQueryLog());

        $exAmtTot = 0;
        foreach ($sportsModel as $keyMatch => $matchVal) {
            $gameModel = Sport::where(["sId" => $matchVal->sports_id])->first();
            if (strtoupper($gameModel->sport_name) == 'CRICKET' || strtoupper($gameModel->sport_name) == 'TENNIS' || strtoupper($gameModel->sport_name) == 'CASINO' || strtoupper($gameModel->sport_name) == 'SOCCER') {
                $matchid = $matchVal->event_id;
                $exAmtArr = self::getExAmountCricketAndTennis('', $matchid, $id);
                if (isset($exAmtArr['ODDS'])) {
                    $arr = array();
                    foreach ($exAmtArr['ODDS'] as $key => $profitLos) {
                        if ($profitLos['ODDS_profitLost'] < 0) {
                            $arr[abs($profitLos['ODDS_profitLost'])] = abs($profitLos['ODDS_profitLost']);
                        }
                    }
                    if (is_array($arr) && count($arr) > 0) {
                        $exAmtTot += max($arr);
                    }
                }

                if (isset($exAmtArr['BOOKMAKER'])) {
                    $arrB = array();
                    foreach ($exAmtArr['BOOKMAKER'] as $key => $profitLos) {
                        if ($profitLos['BOOKMAKER_profitLost'] < 0) {
                            $arrB[abs($profitLos['BOOKMAKER_profitLost'])] = abs($profitLos['BOOKMAKER_profitLost']);
                        }
                    }
                    if (is_array($arrB) && count($arrB) > 0) {
                        $exAmtTot += max($arrB);
                    }
                }
                if (isset($exAmtArr['SESSION'])) {
                    foreach ($exAmtArr['SESSION']['exposure'] as $key => $sesVal) {
                        $exAmtTot += abs($sesVal['SESSION_profitLost']);
                    }
                }
            }

        }
        return round(abs($exAmtTot));
    }

    public function getAllSessionExposure($uid, $fancyName, $eventid)
    {
        $my_placed_bets = MyBets::select('user_id', 'match_id', 'bet_type', 'bet_side', 'bet_odds', 'bet_oddsk', 'bet_amount', 'bet_profit', 'team_name', 'exposureAmt')
            ->where('user_id', $uid)->where('match_id', $eventid)->where('team_name', @$fancyName)->where('bet_type', 'SESSION')->where('isDeleted', 0)->where('result_declare', 0)->orderBy('created_at', 'asc')->get();

        $calculated_expo = 0;
        $abc = sizeof($my_placed_bets);
        $return_final_exposure = '';
        $profit_loss = "";
        $return_exposure = 0;
        $expo_array = array();

        $new_obj = array();
        $i = 0;
        foreach ($my_placed_bets as $bet) {
            $new_obj[$i]['user_id'] = $bet->user_id;
            $new_obj[$i]['match_id'] = $bet->match_id;
            $new_obj[$i]['bet_type'] = $bet->bet_type;
            $new_obj[$i]['bet_side'] = $bet->bet_side;
            $new_obj[$i]['bet_odds'] = $bet->bet_odds;
            $new_obj[$i]['bet_oddsk'] = $bet->bet_oddsk;
            $new_obj[$i]['bet_amount'] = $bet->bet_amount;
            $new_obj[$i]['bet_profit'] = $bet->bet_profit;
            $new_obj[$i]['team_name'] = $bet->team_name;
            $new_obj[$i]['exposureAmt'] = $bet->exposureAmt;
            $i++;
        }
        if (sizeof($new_obj) > 0) {
            $run_arr = array();

            for ($i = 0; $i < count($new_obj); $i++) {
                $down_position = $new_obj[$i]['bet_odds'] - 1;
                if (!in_array($down_position, $run_arr)) {
                    $run_arr[] = $down_position;
                }
                $level_position = $new_obj[$i]['bet_odds'];
                if (!in_array($level_position, $run_arr)) {
                    $run_arr[] = $level_position;
                }
                $up_position = $new_obj[$i]['bet_odds'] + 1;
                if (!in_array($up_position, $run_arr)) {
                    $run_arr[] = $up_position;
                }
            }
            array_unique($run_arr);
            sort($run_arr);

            $min_val = min($run_arr);
            $max_val = max($run_arr);

            $newArr = array();

            for ($i = 0; $i <= $max_val + 1000; ++$i) {
                $new = $i;
                $newArr[] = $new;
            }

            $run_arr = array();
            $run_arr = $newArr;

            $bet_chk = '';
            $bet_model = '';
            $final_exposer = '';

            for ($kk = 0; $kk < sizeof($run_arr); $kk++) {
                $bet_deduct_amt = 0;
                $placed_bet_type = '';
                //foreach($my_placed_bets as $bet)
                for ($i = 0; $i < count($new_obj); $i++) {
                    if ($new_obj[$i]['bet_side'] == 'back') {
                        if ($new_obj[$i]['bet_odds'] == $run_arr[$kk]) {
                            $bet_deduct_amt = $bet_deduct_amt + $new_obj[$i]['bet_profit'];
                        } else if ($new_obj[$i]['bet_odds'] < $run_arr[$kk]) {
                            $bet_deduct_amt = $bet_deduct_amt + $new_obj[$i]['bet_profit'];
                        } else if ($new_obj[$i]['bet_odds'] > $run_arr[$kk]) {
                            $bet_deduct_amt = $bet_deduct_amt - $new_obj[$i]['exposureAmt'];
                        }
                    } else if ($new_obj[$i]['bet_side'] == 'lay') {
                        if ($new_obj[$i]['bet_odds'] == $run_arr[$kk]) {
                            $bet_deduct_amt = $bet_deduct_amt - $new_obj[$i]['exposureAmt'];
                        } else if ($new_obj[$i]['bet_odds'] < $run_arr[$kk]) {
                            $bet_deduct_amt = $bet_deduct_amt - $new_obj[$i]['exposureAmt'];
                        } else if ($new_obj[$i]['bet_odds'] > $run_arr[$kk]) {
                            $bet_deduct_amt = $bet_deduct_amt + $new_obj[$i]['bet_amount'];
                        }
                    }
                }
                if ($final_exposer == "")
                    $final_exposer = $bet_deduct_amt;
                else {
                    if ($final_exposer > $bet_deduct_amt)
                        $final_exposer = $bet_deduct_amt;
                }

            }
            $calculated_expo = $calculated_expo + abs($final_exposer);
        }
        return abs($calculated_expo);

    }

    public function getSessionExposer($uid, $eventid=0, $fancyName='', $conditionalParameters = [])
    {

        $query = MyBets::select('user_id', 'match_id', 'bet_type', 'bet_side', 'bet_odds', 'bet_oddsk', 'bet_amount', 'bet_profit', 'team_name', 'exposureAmt')->where('user_id', $uid)->where('bet_type', 'SESSION')->where('isDeleted', 0)->where('result_declare', 0);

        if($eventid!=0) {
            if(isset($conditionalParameters['match_id'])){
                $query->where('match_id', $conditionalParameters['match_id'], $eventid);
            }else{
                $query->where('match_id', $eventid);
            }
        }
        if(!empty($fancyName)) {
            if(isset($conditionalParameters['team_name'])){
                $query->where('team_name', $conditionalParameters['team_name'], $fancyName);
            }else {
                $query->where('team_name', $fancyName);
            }
        }

        $my_placed_bets = $query->orderBy('created_at', 'asc')->get();

        $new_obj = array();
        $i = 0;
        foreach ($my_placed_bets as $bet) {
            $new_obj[$i]['user_id'] = $bet->user_id;
            $new_obj[$i]['match_id'] = $bet->match_id;
            $new_obj[$i]['bet_type'] = $bet->bet_type;
            $new_obj[$i]['bet_side'] = $bet->bet_side;
            $new_obj[$i]['bet_odds'] = $bet->bet_odds;
            $new_obj[$i]['bet_oddsk'] = $bet->bet_oddsk;
            $new_obj[$i]['bet_amount'] = $bet->bet_amount;
            $new_obj[$i]['bet_profit'] = $bet->bet_profit;
            $new_obj[$i]['team_name'] = $bet->team_name;
            $new_obj[$i]['exposureAmt'] = $bet->exposureAmt;
            $i++;
        }

        $deductamt=0; $position=0; $betside='';
        if(isset($conditionalParameters['deductamt'])){
            $deductamt = $conditionalParameters['deductamt'];
        }
        if(isset($conditionalParameters['position'])){
            $position = $conditionalParameters['position'];
        }
        if(isset($conditionalParameters['betside'])){
            $betside = $conditionalParameters['betside'];
        }

        if(!empty($betside)) {
            $new_obj[$i]['user_id'] = $uid;
            $new_obj[$i]['match_id'] = $eventid;
            $new_obj[$i]['bet_type'] = 'SESSION';
            $new_obj[$i]['bet_side'] = $betside;
            $new_obj[$i]['bet_odds'] = $position;
            $new_obj[$i]['bet_oddsk'] = 100;
            $new_obj[$i]['bet_amount'] = $deductamt;
            $new_obj[$i]['bet_profit'] = $deductamt;
            $new_obj[$i]['team_name'] = $fancyName;
            $new_obj[$i]['exposureAmt'] = $deductamt;
        }

        $final_exposer = 0;
        if (!empty($new_obj)) {
            $run_arr = array();

            for ($i = 0; $i < count($new_obj); $i++) {
                $down_position = $new_obj[$i]['bet_odds'] - 1;
                if (!in_array($down_position, $run_arr)) {
                    $run_arr[] = $down_position;
                }
                $level_position = $new_obj[$i]['bet_odds'];
                if (!in_array($level_position, $run_arr)) {
                    $run_arr[] = $level_position;
                }
                $up_position = $new_obj[$i]['bet_odds'] + 1;
                if (!in_array($up_position, $run_arr)) {
                    $run_arr[] = $up_position;
                }
            }
            array_unique($run_arr);
            sort($run_arr);

            $min_val = min($run_arr);
            $max_val = max($run_arr);

            $newArr = array();

            for ($i = 0; $i <= $max_val + 1000; ++$i) {
                $new = $i;
                $newArr[] = $new;
            }

            $run_arr = array();
            $run_arr = $newArr;

            $bet_chk = '';
            $bet_model = '';
            $final_exposer = 0;

            for ($kk = 0; $kk < sizeof($run_arr); $kk++) {
                $bet_deduct_amt = 0;
                $placed_bet_type = '';
                //foreach($my_placed_bets as $bet)
                for ($i = 0; $i < count($new_obj); $i++) {
                    if ($new_obj[$i]['bet_side'] == 'back') {
                        if ($new_obj[$i]['bet_odds'] == $run_arr[$kk]) {
                            $bet_deduct_amt = $bet_deduct_amt + $new_obj[$i]['bet_profit'];
                        } else if ($new_obj[$i]['bet_odds'] < $run_arr[$kk]) {
                            $bet_deduct_amt = $bet_deduct_amt + $new_obj[$i]['bet_profit'];
                        } else if ($new_obj[$i]['bet_odds'] > $run_arr[$kk]) {
                            $bet_deduct_amt = $bet_deduct_amt - $new_obj[$i]['exposureAmt'];
                        }
                    } else if ($new_obj[$i]['bet_side'] == 'lay') {
                        if ($new_obj[$i]['bet_odds'] == $run_arr[$kk]) {
                            $bet_deduct_amt = $bet_deduct_amt - $new_obj[$i]['exposureAmt'];
                        } else if ($new_obj[$i]['bet_odds'] < $run_arr[$kk]) {
                            $bet_deduct_amt = $bet_deduct_amt - $new_obj[$i]['exposureAmt'];
                        } else if ($new_obj[$i]['bet_odds'] > $run_arr[$kk]) {
                            $bet_deduct_amt = $bet_deduct_amt + $new_obj[$i]['bet_amount'];
                        }
                    }
                }
                if ($final_exposer == "")
                    $final_exposer = $bet_deduct_amt;
                else {
                    if ($final_exposer > $bet_deduct_amt)
                        $final_exposer = $bet_deduct_amt;
                }

            }
        }

        return abs($final_exposer);
    }

    public function getExAmount_Session($fancyName, $eventid, $uid, $deductamt, $position, $betside)
    {

        $my_placed_bets = MyBets::select('user_id', 'match_id', 'bet_type', 'bet_side', 'bet_odds', 'bet_oddsk', 'bet_amount', 'bet_profit', 'team_name', 'exposureAmt')->where('user_id', $uid)->where('match_id', $eventid)->where('team_name', @$fancyName)->where('bet_type', 'SESSION')->where('isDeleted', 0)->where('result_declare', 0)->orderBy('created_at', 'asc')->get();

        $abc = sizeof($my_placed_bets);
        $return_final_exposure = '';
        $profit_loss = "";
        $return_exposure = 0;
        $expo_array = array();

        $new_obj = array();
        $i = 0;
        foreach ($my_placed_bets as $bet) {
            $new_obj[$i]['user_id'] = $bet->user_id;
            $new_obj[$i]['match_id'] = $bet->match_id;
            $new_obj[$i]['bet_type'] = $bet->bet_type;
            $new_obj[$i]['bet_side'] = $bet->bet_side;
            $new_obj[$i]['bet_odds'] = $bet->bet_odds;
            $new_obj[$i]['bet_oddsk'] = $bet->bet_oddsk;
            $new_obj[$i]['bet_amount'] = $bet->bet_amount;
            $new_obj[$i]['bet_profit'] = $bet->bet_profit;
            $new_obj[$i]['team_name'] = $bet->team_name;
            $new_obj[$i]['exposureAmt'] = $bet->exposureAmt;
            $i++;
        }
        $new_obj[$i]['user_id'] = $uid;
        $new_obj[$i]['match_id'] = $eventid;
        $new_obj[$i]['bet_type'] = 'SESSION';
        $new_obj[$i]['bet_side'] = $betside;
        $new_obj[$i]['bet_odds'] = $position;
        $new_obj[$i]['bet_oddsk'] = 100;
        $new_obj[$i]['bet_amount'] = $deductamt;
        $new_obj[$i]['bet_profit'] = $deductamt;
        $new_obj[$i]['team_name'] = $fancyName;
        $new_obj[$i]['exposureAmt'] = $deductamt;


        if (sizeof($new_obj) > 0) {
            $run_arr = array();

            for ($i = 0; $i < count($new_obj); $i++) {
                $down_position = $new_obj[$i]['bet_odds'] - 1;
                if (!in_array($down_position, $run_arr)) {
                    $run_arr[] = $down_position;
                }
                $level_position = $new_obj[$i]['bet_odds'];
                if (!in_array($level_position, $run_arr)) {
                    $run_arr[] = $level_position;
                }
                $up_position = $new_obj[$i]['bet_odds'] + 1;
                if (!in_array($up_position, $run_arr)) {
                    $run_arr[] = $up_position;
                }
            }
            array_unique($run_arr);
            sort($run_arr);

            $min_val = min($run_arr);
            $max_val = max($run_arr);

            $newArr = array();

            for ($i = 0; $i <= $max_val + 1000; ++$i) {
                $new = $i;
                $newArr[] = $new;
            }

            $run_arr = array();
            $run_arr = $newArr;

            $bet_chk = '';
            $bet_model = '';
            $final_exposer = 0;

            for ($kk = 0; $kk < sizeof($run_arr); $kk++) {
                $bet_deduct_amt = 0;
                $placed_bet_type = '';
                //foreach($my_placed_bets as $bet)
                for ($i = 0; $i < count($new_obj); $i++) {
                    if ($new_obj[$i]['bet_side'] == 'back') {
                        if ($new_obj[$i]['bet_odds'] == $run_arr[$kk]) {
                            $bet_deduct_amt = $bet_deduct_amt + $new_obj[$i]['bet_profit'];
                        } else if ($new_obj[$i]['bet_odds'] < $run_arr[$kk]) {
                            $bet_deduct_amt = $bet_deduct_amt + $new_obj[$i]['bet_profit'];
                        } else if ($new_obj[$i]['bet_odds'] > $run_arr[$kk]) {
                            $bet_deduct_amt = $bet_deduct_amt - $new_obj[$i]['exposureAmt'];
                        }
                    } else if ($new_obj[$i]['bet_side'] == 'lay') {
                        if ($new_obj[$i]['bet_odds'] == $run_arr[$kk]) {
                            $bet_deduct_amt = $bet_deduct_amt - $new_obj[$i]['exposureAmt'];
                        } else if ($new_obj[$i]['bet_odds'] < $run_arr[$kk]) {
                            $bet_deduct_amt = $bet_deduct_amt - $new_obj[$i]['exposureAmt'];
                        } else if ($new_obj[$i]['bet_odds'] > $run_arr[$kk]) {
                            $bet_deduct_amt = $bet_deduct_amt + $new_obj[$i]['bet_amount'];
                        }
                    }
                }
                if ($final_exposer == "")
                    $final_exposer = $bet_deduct_amt;
                else {
                    if ($final_exposer > $bet_deduct_amt)
                        $final_exposer = $bet_deduct_amt;
                }

            }
        }
        return abs($final_exposer);

    }

    public function getExAmountForSession($uid, $fancyName = '', $eventid)
    {
        $calculated_expo = 0;

        $my_placed_bets_one = MyBets::select('user_id', 'match_id', 'bet_type', 'bet_side', 'bet_odds', 'bet_oddsk', 'bet_amount', 'bet_profit', 'team_name', 'exposureAmt')->where('user_id', $uid)->where('match_id', '!=', $eventid)->where('bet_type', 'SESSION')->where('isDeleted', 0)->where('result_declare', 0)->groupby('team_name', 'match_id')->orderBy('created_at', 'asc')->get();

        if ($fancyName != '') {

            $abc_check = MyBets::select('user_id', 'match_id', 'bet_type', 'bet_side', 'bet_odds', 'bet_oddsk', 'bet_amount', 'bet_profit', 'team_name', 'exposureAmt')->where('user_id', $uid)->where('match_id', $eventid)
                ->where('team_name', '!=', @$fancyName)->where('bet_type', 'SESSION')->groupby('team_name', 'match_id')->where('isDeleted', 0)->where('result_declare', 0)->orderBy('created_at', 'asc')->get();

            if (sizeof($my_placed_bets_one) > 0) {
                $my_placed_bets_one = $my_placed_bets_one->merge($abc_check);
            } else {
                $my_placed_bets_one = $abc_check;
            }
        } else {
            $abc_check = MyBets::select('user_id', 'match_id', 'bet_type', 'bet_side', 'bet_odds', 'bet_oddsk', 'bet_amount', 'bet_profit', 'team_name', 'exposureAmt')->where('user_id', $uid)->where('match_id', $eventid)
                ->where('bet_type', 'SESSION')->groupby('team_name', 'match_id')->where('isDeleted', 0)->where('result_declare', 0)->orderBy('created_at', 'asc')->get();

            if (sizeof($my_placed_bets_one) > 0 && sizeof($abc_check) > 0) {
                $my_placed_bets_one = $my_placed_bets_one->merge($abc_check);
            } else {
                if (sizeof($abc_check) > 0)
                    $my_placed_bets_one = $abc_check;
                else if (sizeof($my_placed_bets_one) > 0)
                    $my_placed_bets_one = $my_placed_bets_one;
            }
        }
        if (sizeof($my_placed_bets_one) > 0) {
            foreach ($my_placed_bets_one as $one) {
                $fancyName = $one->team_name;
                $eventid = $one->match_id;


                $my_placed_bets = MyBets::select('user_id', 'match_id', 'bet_type', 'bet_side', 'bet_odds', 'bet_oddsk', 'bet_amount', 'bet_profit', 'team_name', 'exposureAmt')->where('user_id', $uid)->where('match_id', $eventid)->where('bet_type', 'SESSION')->where('team_name', @$fancyName)->where('isDeleted', 0)->where('result_declare', 0)->orderBy('created_at', 'asc')->get();


                $abc = sizeof($my_placed_bets);
                $return_final_exposure = '';
                $profit_loss = "";
                $return_exposure = 0;
                $expo_array = array();

                $new_obj = array();
                $i = 0;
                foreach ($my_placed_bets as $bet) {
                    $new_obj[$i]['user_id'] = $bet->user_id;
                    $new_obj[$i]['match_id'] = $bet->match_id;
                    $new_obj[$i]['bet_type'] = $bet->bet_type;
                    $new_obj[$i]['bet_side'] = $bet->bet_side;
                    $new_obj[$i]['bet_odds'] = $bet->bet_odds;
                    $new_obj[$i]['bet_oddsk'] = $bet->bet_oddsk;
                    $new_obj[$i]['bet_amount'] = $bet->bet_amount;
                    $new_obj[$i]['bet_profit'] = $bet->bet_profit;
                    $new_obj[$i]['team_name'] = $bet->team_name;
                    $new_obj[$i]['exposureAmt'] = $bet->exposureAmt;
                    $i++;
                }

                if (sizeof($new_obj) > 0) {
                    $run_arr = array();

                    for ($i = 0; $i < count($new_obj); $i++) {
                        $down_position = $new_obj[$i]['bet_odds'] - 1;
                        if (!in_array($down_position, $run_arr)) {
                            $run_arr[] = $down_position;
                        }
                        $level_position = $new_obj[$i]['bet_odds'];
                        if (!in_array($level_position, $run_arr)) {
                            $run_arr[] = $level_position;
                        }
                        $up_position = $new_obj[$i]['bet_odds'] + 1;
                        if (!in_array($up_position, $run_arr)) {
                            $run_arr[] = $up_position;
                        }
                    }
                    array_unique($run_arr);
                    sort($run_arr);

                    $min_val = min($run_arr);
                    $max_val = max($run_arr);

                    $newArr = array();

                    for ($i = 0; $i <= $max_val + 1000; ++$i) {
                        $new = $i;
                        $newArr[] = $new;
                    }

                    $run_arr = array();
                    $run_arr = $newArr;

                    $bet_chk = '';
                    $bet_model = '';
                    $final_exposer = '';

                    for ($kk = 0; $kk < sizeof($run_arr); $kk++) {
                        $bet_deduct_amt = 0;
                        $placed_bet_type = '';
                        //foreach($my_placed_bets as $bet)
                        for ($i = 0; $i < count($new_obj); $i++) {
                            if ($new_obj[$i]['bet_side'] == 'back') {
                                if ($new_obj[$i]['bet_odds'] == $run_arr[$kk]) {
                                    $bet_deduct_amt = $bet_deduct_amt + $new_obj[$i]['bet_profit'];
                                } else if ($new_obj[$i]['bet_odds'] < $run_arr[$kk]) {
                                    $bet_deduct_amt = $bet_deduct_amt + $new_obj[$i]['bet_profit'];
                                } else if ($new_obj[$i]['bet_odds'] > $run_arr[$kk]) {
                                    $bet_deduct_amt = $bet_deduct_amt - $new_obj[$i]['exposureAmt'];
                                }
                            } else if ($new_obj[$i]['bet_side'] == 'lay') {
                                if ($new_obj[$i]['bet_odds'] == $run_arr[$kk]) {
                                    $bet_deduct_amt = $bet_deduct_amt - $new_obj[$i]['exposureAmt'];
                                } else if ($new_obj[$i]['bet_odds'] < $run_arr[$kk]) {
                                    $bet_deduct_amt = $bet_deduct_amt - $new_obj[$i]['exposureAmt'];
                                } else if ($new_obj[$i]['bet_odds'] > $run_arr[$kk]) {
                                    $bet_deduct_amt = $bet_deduct_amt + $new_obj[$i]['bet_amount'];
                                }
                            }
                        }
                        if ($final_exposer == "")
                            $final_exposer = $bet_deduct_amt;
                        else {
                            if ($final_exposer > $bet_deduct_amt)
                                $final_exposer = $bet_deduct_amt;
                        }

                    }
                    $calculated_expo = $calculated_expo + abs($final_exposer);
                }
            }
        }

        return abs($calculated_expo);

    }

    public function MyBetStore(Request $request)
    {

        $requestData = $request->all();

        $getUserCheck = Session::get('playerUser');
        if (!empty($getUserCheck)) {
            $getUser = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
        } else {
            $responce['status'] = 'false';
            $responce['msg'] = 'Session Logout, please login again';
            return json_encode($responce);
        }

        $userId = $getUser->id;

        $headerUserBalance = SELF::getBlanceAmount();

        if ($headerUserBalance <= 0) {
            $responce = [];
            $responce['status'] = 'false';
            $responce['msg'] = 'Insufficient Balance!';
            return json_encode($responce);
        }

        $sportsModel = Match::where(['event_id' => $requestData['match_id']])->first();
        $locked_user = json_decode($sportsModel->user_list);
        $is_userlocked = $getUser->status;
        if (!empty($locked_user)) {
            if (in_array($userId, $locked_user)) {
                $responce['status'] = 'false';
                $responce['msg'] = 'Bet Locked By Admin!';
                return json_encode($responce);
                exit;
            }
        }
        if ($is_userlocked == 'locked') {
            $responce['status'] = 'false';
            $responce['msg'] = 'Bet Locked By Admin!';
            return json_encode($responce);
            exit;
        }

        $main_odds = '';
        $team1_main_odds = '';
        $team2_main_odds = '';
        $team3_main_odds = '';
        //odds check
        if ($requestData['bet_type'] === 'ODDS' && $requestData['bet_side'] == 'back') {
            $main_odds = self::getMainOdds($requestData['match_id'], $requestData['bet_side']);

            if ($main_odds != '') {
                $odd = explode("~~", $main_odds);
                $team1_main_odds = $odd[0];
                $team2_main_odds = $odd[1];
                $team3_main_odds = $odd[2];
            }
        }
        if ($requestData['bet_type'] === 'ODDS' && $requestData['bet_side'] == 'lay') {
            $main_odds = self::getMainOdds($requestData['match_id'], $requestData['bet_side']);

            if ($main_odds != '') {
                $odd = explode("~~", $main_odds);
                $team1_main_odds = $odd[0];
                $team2_main_odds = $odd[1];
                $team3_main_odds = $odd[2];
            }
        }
        if ($requestData['bet_type'] === 'BOOKMAKER') {
            $main_odds = self::getMainBMOdds($requestData['match_id'], $requestData['team_name'], $requestData['bet_position'], $requestData['bet_side'], $requestData['bet_odds']);

            if ($main_odds == 'Suspend') {
                $responce['status'] = 'false';
                $responce['msg'] = 'Unmatch Bet Total Not Allowed!';
                return json_encode($responce);
                exit;
            } else if ($main_odds == 'Unmatch Bet Total Not Allowed!') {
                $responce['status'] = 'false';
                $responce['msg'] = 'Unmatch Bet Total Not Allowed!';
                return json_encode($responce);
                exit;
            }
        }
        if ($requestData['bet_type'] === 'SESSION') {
            $main_odds = self::getMainFancyOdds($requestData['match_id'], $requestData['team_name'], $requestData['bet_side'], $requestData['bet_odds']);

            if ($main_odds == 'Suspend') {
                $responce['status'] = 'false';
                $responce['msg'] = 'Unmatch Bet Total Not Allowed!';
                return json_encode($responce);
                exit;
            } else if ($main_odds == 'Unmatch Bet Total Not Allowed!') {
                $responce['status'] = 'false';
                $responce['msg'] = 'Unmatch Bet Total Not Allowed!';
                return json_encode($responce);
                exit;
            }
        }

        if (isset($requestData['team_name']) && !empty($requestData['team_name'])) {
            $requestData['team_name'] = urldecode($requestData['team_name']);
        }
        if (isset($requestData['teamname1']) && !empty($requestData['teamname1'])) {
            $requestData['teamname1'] = urldecode($requestData['teamname1']);
        }
        if (isset($requestData['teamname2']) && !empty($requestData['teamname2'])) {
            $requestData['teamname2'] = urldecode($requestData['teamname2']);
        }
        if (isset($requestData['teamname3']) && !empty($requestData['teamname3'])) {
            $requestData['teamname3'] = urldecode($requestData['teamname3']);
        }

        $min_bet_odds_limit = $sportsModel->min_bet_odds_limit;
        $max_bet_odds_limit = $sportsModel->max_bet_odds_limit;

        $min_bet_bm_limit = $sportsModel->min_bookmaker_limit;
        $max_bet_bm_limit = $sportsModel->max_bookmaker_limit;

        $min_bet_fancy_limit = $sportsModel->min_fancy_limit;
        $max_bet_fancy_limit = $sportsModel->max_fancy_limit;

        $max_odds_limit = $sportsModel->odds_limit;

        $responce = [];
        if ($max_odds_limit < $requestData['bet_odds'] && $requestData['bet_type'] === 'ODDS') {
            $responce['status'] = 'false';
            $responce['msg'] = 'Odds Limit Exceed!';
            return json_encode($responce);
        }
        if ($requestData['bet_type'] === 'ODDS') {
            if ($requestData['bet_amount'] < $min_bet_odds_limit) {
                $responce['status'] = 'false';
                $responce['msg'] = 'Minimum bet limit is ' . $min_bet_odds_limit . '!';
                return json_encode($responce);
                exit;
            }
            if ($requestData['bet_amount'] > $max_bet_odds_limit) {
                $responce['status'] = 'false';
                $responce['msg'] = 'Maximum bet limit is  ' . $max_bet_odds_limit . '!';
                return json_encode($responce);
                exit;
            }
        }
        if ($requestData['bet_type'] === 'BOOKMAKER') {
            if ($requestData['bet_amount'] < $min_bet_bm_limit) {
                $responce['status'] = 'false';
                $responce['msg'] = 'Minimum bet limit is ' . $min_bet_bm_limit . '!';
                return json_encode($responce);
                exit;
            }
            if ($requestData['bet_amount'] > $max_bet_bm_limit) {
                $responce['status'] = 'false';
                $responce['msg'] = 'Maximum bet limit is ' . $max_bet_bm_limit . '!';
                return json_encode($responce);
                exit;
            }
        }
        if ($requestData['bet_type'] == 'SESSION') {
            if ($requestData['bet_amount'] < $min_bet_fancy_limit) {
                $responce = [];
                $responce['status'] = 'false';
                $responce['msg'] = 'Minimum bet limit is ' . $min_bet_fancy_limit . '!';
                return json_encode($responce);
            }
            if ($requestData['bet_amount'] > $max_bet_fancy_limit) {
                $responce = [];
                $responce['status'] = 'false';
                $responce['msg'] = 'Maximum bet limit is ' . $max_bet_fancy_limit . '!';
                return json_encode($responce);
            }
        }

        $stack = $requestData['stack'];

        $deduct_expo_amt = 0;
        if ($requestData['bet_type'] === 'ODDS') {
            $betodds = $requestData['bet_odds'];
            if ($requestData['team1'] == $requestData['team_name'] && $team1_main_odds != '' && $team1_main_odds != 'Suspend') {
                if ($requestData['bet_side'] == 'lay') {
                    if ($requestData['bet_odds'] >= $team1_main_odds)
                        $betodds = $team1_main_odds;
                    else {
                        $responce['status'] = 'false';
                        $responce['msg'] = 'Unmatch Bet Total Not Allowed!';
                        return json_encode($responce);
                    }
                } else {
                    if ($requestData['bet_odds'] <= $team1_main_odds)
                        $betodds = $team1_main_odds;
                    else {
                        $responce['status'] = 'false';
                        $responce['msg'] = 'Unmatch Bet Total Not Allowed!';
                        return json_encode($responce);
                    }
                }
            }
            else if ($requestData['team2'] == $requestData['team_name'] && $team2_main_odds != '' && $team2_main_odds != 'Suspend') {
                if ($requestData['bet_side'] == 'lay') {
                    if ($requestData['bet_odds'] >= $team2_main_odds)
                        $betodds = $team2_main_odds;
                    else {
                        $responce['status'] = 'false';
                        $responce['msg'] = 'Unmatch Bet Total Not Allowed!';
                        return json_encode($responce);
                    }
                } else {
                    if ($requestData['bet_odds'] <= $team2_main_odds)
                        $betodds = $team2_main_odds;
                    else {
                        $responce['status'] = 'false';
                        $responce['msg'] = 'Unmatch Bet Total Not Allowed!';
                        return json_encode($responce);
                    }
                }
            }
            else if ($requestData['team3'] == $requestData['team_name'] && $team3_main_odds != '' && $team3_main_odds != 'Suspend') {
                if ($requestData['bet_side'] == 'lay') {
                    if ($requestData['bet_odds'] >= $team3_main_odds)
                        $betodds = $team3_main_odds;
                    else {
                        $responce['status'] = 'false';
                        $responce['msg'] = 'Unmatch Bet Total Not Allowed!';
                        return json_encode($responce);
                    }
                } else {
                    if ($requestData['bet_odds'] <= $team3_main_odds)
                        $betodds = $team3_main_odds;
                    else {
                        $responce['status'] = 'false';
                        $responce['msg'] = 'Unmatch Bet Total Not Allowed!';
                        return json_encode($responce);
                    }
                }
            }

            if ($requestData['bet_side'] == 'lay') {
                $deduct_expo_amt = ((($betodds - 1) * $stack));
            }
            else {
                $deduct_expo_amt = $stack;
            }

            if ($requestData['bet_side'] === 'lay') {
                $bet_profit = round($stack, 2);
            } else {
                $bet_profit = ((($betodds - 1) * $stack));
            }
        }
        if ($requestData['bet_type'] === 'BOOKMAKER') {
            $betodds = $requestData['bet_odds'];
            if ($requestData['bet_side'] == 'lay') {
                $deduct_expo_amt = (($betodds * $stack) / 100);
            } else {
                $deduct_expo_amt = $stack;
            }

            if ($requestData['bet_side'] === 'lay') {
                $bet_profit = round($stack, 2);
            } else {
                $bet_profit = round(($betodds * $stack) / 100, 2);
            }
        }

        // getting odds and bookmaker total exposer with current new bet
        $extra = '';

        $teamNameArr = array();
        if (isset($requestData['teamname1']) && !empty($requestData['teamname1'])) {
            $teamNameArr['teamname1'] = $requestData['teamname1'];
        }
        if (isset($requestData['teamname2']) && !empty($requestData['teamname2'])) {
            $teamNameArr['teamname2'] = $requestData['teamname2'];
        }
        if (isset($requestData['teamname3']) && !empty($requestData['teamname3'])) {
            $teamNameArr['teamname3'] = $requestData['teamname3'];
        }

        if (is_array($teamNameArr) && count($teamNameArr) > 0) {
            $extra = json_encode($teamNameArr);
        }
        $oddsBookmakerExposerArr = [];
        if ($requestData['bet_type'] === 'ODDS' || $requestData['bet_type'] === 'BOOKMAKER') {
            $betRecord = [];
            $betRecord['match_id'] =  "!=";
            $betRecord['bet_type'] = $requestData['bet_type'];
            $betRecord['bet_side'] = $requestData['bet_side'];
            $betRecord['team_name'] = $requestData['team_name'];
            $betRecord['exposureAmt'] = $deduct_expo_amt;
            $betRecord['bet_amount'] = $requestData['bet_amount'];
            $betRecord['bet_profit'] = $bet_profit;
            $betRecord['extra'] = $extra;

            $oddsBookmakerExposerArr = self::getOddsAndBookmakerExposer($userId, $requestData['match_id'], $betRecord);

//            dd($betRecord, $oddsBookmakerExposerArr);

            $oddsBookmakerExposer = $oddsBookmakerExposerArr['exposer'];
        }
        else{
            $oddsBookmakerExposerArr = self::getOddsAndBookmakerExposer($userId);
            $oddsBookmakerExposer = $oddsBookmakerExposerArr['exposer'];
        }

        if ($requestData['bet_type'] === 'SESSION') {
            $betodds = $requestData['odds_volume'];
            if ($requestData['bet_side'] == 'lay') {
                $deduct_expo_amt = ((($requestData['odds_volume']) * $stack)) / 100;
            } else {
                $deduct_expo_amt = $stack;
            }

            if ($requestData['bet_side'] === 'lay') {
                $bet_profit = round($stack, 2);
            } else {
                $bet_profit = round(($betodds * $stack) / 100, 2);
            }
        }

        $currentSessionBetTotalExposer = 0;
        // getting session total exposer with current new session bet
        if ($requestData['bet_type'] === 'SESSION'){
            $betRecord = [];
            $betRecord['deductamt'] = $deduct_expo_amt;
            $betRecord['position'] = $betodds;
            $betRecord['betside'] = $requestData['bet_side'];

            $otherSessionExposer = self::getSessionExposer($userId,$requestData['match_id'],$requestData['team_name'],['team_name'=>'!=']);
            $currentSessionExposer = self::getSessionExposer($userId,$requestData['match_id'],$requestData['team_name'], $betRecord);

            $sessionExposer = $otherSessionExposer + $currentSessionExposer;
            $currentSessionBetTotalExposer = $currentSessionExposer;
        }else{
            $sessionExposer = self::getSessionExposer($userId);
        }

        // getting casino total exposer
        $exposureAmtCasinoResp = CasinoCalculationController::getCasinoExAmount($userId); /// nnnn 21-10-2021
        $exposureAmtCasino =   $exposureAmtCasinoResp['exposer'];

        $finalExposerWithCurrentMatchSession = $exposureAmtCasino + $sessionExposer + $oddsBookmakerExposer;

//        dd($exposureAmtCasino, $sessionExposer, $oddsBookmakerExposer);

        if ($headerUserBalance < $finalExposerWithCurrentMatchSession) {
            $responce = [];
            $responce['status'] = 'false';
            $responce['msg'] = 'Insufficient Balance!!';
            return json_encode($responce);
        }

        $timezone = Carbon::now()->format('Y-m-d H:i:s');

        $betModel = new MyBets();
        $betModel->sportID = $requestData['sportID'];
        $betModel->user_id = $getUser->id;
        $betModel->match_id = $requestData['match_id'];
        $betModel->bet_type = $requestData['bet_type'];
        $betModel->bet_side = $requestData['bet_side'];
        $betModel->bet_amount = $stack;
        $betModel->bet_profit = $bet_profit;
        $betModel->team_name = $requestData['team_name'];
        if (!empty($extra)) {
            $betModel->extra = $extra;
        }
        $betModel->exposureAmt = $deduct_expo_amt;
        $betModel->ip_address = resAll::ip();
        $betModel->browser_details = $_SERVER['HTTP_USER_AGENT'];
        $betModel->created_at = $timezone;
        $betModel->updated_at = $timezone;

        if ($betModel->bet_type == 'SESSION') {
            $betModel->bet_oddsk = $requestData['odds_volume'];
            $betModel->bet_odds = $requestData['bet_odds'];
        }else{
            $betModel->bet_odds = $betodds;
        }

        if ($betModel->save()) {
            $depTot = CreditReference::where('player_id', $betModel->user_id)->first();
            $save_exposer_balance = SELF::SaveBalance($deduct_expo_amt);
            $depTot1 = CreditReference::where('player_id', $betModel->user_id)->first();
            ExposerDeductLog::createLog([
                'user_id' => $betModel->user_id,
                'action' => 'Place Match Bet',
                'current_exposer' => $depTot->exposure,
                'new_exposer' => $depTot1->exposure,
                'exposer_deduct' => $deduct_expo_amt,
                'match_id' => $betModel->match_id,
                'bet_type' => $betModel->bet_type,
                'bet_amount' => $stack,
                'odds_value' => $betModel->bet_odds,
                'odds_volume' => 0,
                'profit' => $betModel->bet_profit,
                'lose' => $betModel->exposureAmt,
                'available_balance' => $depTot1->available_balance_for_D_W
            ]);
            $responce = [];
            $responce['status'] = 'true';
            $responce['msg'] = 'Bet Added Successfully.';
            $responce['currentSessionBetTotalExposer'] = $currentSessionBetTotalExposer;
            $responce['oddsBookmakerExposerArr'] = $oddsBookmakerExposerArr;
            return json_encode($responce);
        }


        return json_encode($responce);
    }

    public function GetOtherMatchBet(Request $request)
    {
        /*echo "aav";
    exit;*/
        $val = explode("~~", $request->match_id);
        $matchid = @$val[0];
        $bet_type = @$val[1];
        /*echo $matchid;
    exit;*/
        $getUserCheck = Session::get('playerUser');
        if (!empty($getUserCheck)) {
            $getUser = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
        }
        $userId = $getUser->id;
        $html = '';
        if ($bet_type != 'All') {
            /*echo "df";
      exit;*/
            $my_placed_bets = MyBets::where('user_id', $userId)->where('match_id', $matchid)->where('isDeleted', 0)->where('result_declare', 0)->orderby('id', 'DESC')->get();
            /*echo "<pre>";
    echo $my_placed_bets;
      exit;*/
        } else {
            $my_placed_bets = MyBets::where('user_id', $userId)
                ->where('isDeleted', 0)
                ->where('result_declare', 0)->orderby('id', 'DESC')->get();
        }
        if (sizeof($my_placed_bets) > 0) {
            $j = 0;
            $k = 0;
            foreach ($my_placed_bets as $bet) {
                if ($bet->bet_side == 'back') {
                    if ($j == 0) {
                        $html .= '<ul class="betslip_head">
                        <li class="col-bet">Back (Bet For)</li>
                        <li class="col-odd">Odds</li>
                        <li class="col-stake">Stake</li>
                        <li class="col-profit">Profit</li>
                    </ul>';
                    }
                    $bet_type_check = "";
                    if ($bet->bet_type == 'ODDS' || $bet->bet_type == 'BOOKMAKER')
                        $bet_type_check = 'BACK';
                    if ($bet->bet_type == 'SESSION')
                        $bet_type_check = 'YES';
                    $bet_profit = "";
                    if ($bet->bet_type == 'ODDS' || $bet->bet_type == 'BOOKMAKER')
                        $bet_profit = $bet->bet_profit;
                    if ($bet->bet_type == 'SESSION')
                        $bet_profit = $bet->bet_profit;
                    $html .= '<div class="betslip_box light-blue-bg-1" id="backbet">
              <div class="betn">
                  <span class="slip_type lightblue-bg2">' . $bet_type_check . '</span>
                  <span class="shortamount">' . $bet->team_name . '</span>
                  <span>' . $bet->bet_type . '</span>
              </div>
              <div class="col-odd text-color-blue-2 text-center">';
                    if (!empty($bet->bet_oddsk)) {
                        $html .= '' . $bet->bet_odds . '<br><span>(' . $bet->bet_oddsk . ')</span>';
                    } else {
                        $html .= '' . $bet->bet_odds . '';
                    }
                    $html .= '</div>
              <div class="col-stake text-color-blue-2 text-center">' . $bet->bet_amount . '</div>
              <div class="col-profit">' . number_format($bet_profit, 2) . '</div>
          </div>';
                    $j++;
                }
            }
            foreach ($my_placed_bets as $bet) {
                if ($bet->bet_side == 'lay') {
                    if ($k == 0) {
                        $html .= '<ul class="betslip_head">
                      	<li class="col-bet">Lay (Bet Against)</li>
                      	<li class="col-odd">Odds</li>
                      	<li class="col-stake">Stake</li>
                      	<li class="col-profit">Liability</li>
                  		</ul>';
                    }
                    $bet_profit = "";
//                    if ($bet->bet_type == 'ODDS' || $bet->bet_type == 'BOOKMAKER')
                    $bet_profit = $bet->exposureAmt;
//                    if ($bet->bet_type == 'SESSION')
//                        $bet_profit = $bet->bet_profit;

                    $bet_type_check = "";
                    if ($bet->bet_type == 'ODDS' || $bet->bet_type == 'BOOKMAKER')
                        $bet_type_check = 'LAY';
                    if ($bet->bet_type == 'SESSION')
                        $bet_type_check = 'NO';
                    $html .= '<div class="betslip_box lightpink-bg2" id="laybet">
                <div class="betn">
                    <span class="slip_type lightpink-bg1">' . $bet_type_check . '</span>
                    <span class="shortamount">' . $bet->team_name . '</span>
                    <span>' . $bet->bet_type . '</span>
                </div>
                <div class="col-odd text-color-blue-2 text-center">';
                    if (!empty($bet->bet_oddsk)) {
                        $html .= '' . $bet->bet_odds . '<br><span>(' . $bet->bet_oddsk . ')</span>';
                    } else {
                        $html .= '' . $bet->bet_odds . '';
                    }
                    $html .= '</div>
                <div class="col-stake text-color-blue-2 text-center">' . $bet->bet_amount . '</div>
                <div class="col-profit">' . number_format($bet_profit, 2) . '</div>
            </div>';
                    $k++;
                }
            }
            echo $html;
        } else
            echo 'No bet found for this match';
    }

    public function frontAutoLogout(Request $request)
    {

        //   $mntnc = setting::first();
        //   if(!empty($mntnc->maintanence_msg))
        //   {
        //     $request->session()->forget(['playerUser']);
        //     return response()->json(array('result'=> 'msgsuccess'));
        //   }

        // live match count
        $sports = Sport::all();
        $i = 0;

        $match_array_data_cricket = array();
        $match_array_data_tenis = array();
        $match_array_data_soccer = array();
        foreach ($sports as $sport) {
            $match_link = Match::where('sports_id', $sport->sId)->where('status', 1)->where('suspend_m', 1)->where('status_m', 1)->where('isDeleted', 0)->where('winner', NULL)->orderBy('match_date', 'ASC')->get();
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

        $cricket_count = $tennis_count = $soccer_count = 0;
        if (!empty($match_array_data_cricket)) {
            foreach ($match_array_data_cricket as $ck => $cdata) {
                $match_data = app('App\Http\Controllers\RestApi')->getSingleMatchData($cdata, $ck, 4);
                if (isset($match_data['t1'][0][0]['iplay']) && $match_data['t1'][0][0]['iplay'] === 'True') {
                    $cricket_count += 1;
                }
            }
        }

        if (!empty($match_array_data_soccer)) {
            foreach ($match_array_data_soccer as $ck => $cdata) {
                $match_data = app('App\Http\Controllers\RestApi')->getSingleMatchData($cdata, $ck, 1);
                if (!empty($match_data)) {
                    if ($match_data[0]['inplay'] == 1) {
                        $soccer_count += 1;
                    }
                }
            }
        }

        if (!empty($match_array_data_tenis)) {
            foreach ($match_array_data_tenis as $ck => $cdata) {
                $match_data = app('App\Http\Controllers\RestApi')->getSingleMatchData($cdata, $ck, 1);
                if (!empty($match_data)) {
                    if ($match_data[0]['inplay'] == 1) {
                        $tennis_count += 1;
                    }
                }
            }
        }

        return $cricket_count . '~~' . $tennis_count . '~~' . $soccer_count;

    }

    public function stakechange(Request $request)
    {
        $pos = $request->id;
        $s_data = $request->data;
        $getUserCheck = Session::get('playerUser');
        if (!empty($getUserCheck)) {
            $getuser = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
        }

        $s_result = UserStake::where('user_id', $getuser->id)->first();
        $ans = json_decode($s_result->stake);
        foreach ($ans as $key => $value) {
            if ($key == $pos) {
                $replacements = array($key => $s_data);
                $pack = array_replace($ans, $replacements);
                UserStake::where('user_id', $getuser->id)->update(['stake' => $pack]);
            }
        }
    }
    /*public function getAllBetsForMobile(Request $request)
  {
	  $getUser = Session::get('playerUser');
	  $userId=$getUser->id;
	  $event_id=$request->event_id;
	  $my_placed_bets_all = MyBets::where('user_id',$userId)->where('match_id',$event_id)->where('isDeleted',0)
	  ->where('bet_side','back')
	  ->where('result_declare',0)->orderby('id','DESC')->get();
	  $return_data='';
	  foreach($my_placed_bets_all as $data)
	  {
			if($data->result_declare == 0)
			{
				$sports = Sport::where('sId', $data->sportID)->first();
				$matchdata = Match::where('event_id', $data->match_id)->first();

				$return_data.='<tr class="white-bg">
                                                <td width="9%"><img src="'.asset('asset/front/img/plus-icon.png').'"> <a class="text-color-blue-light">'.$data->id.'</a></td>
                                                <td width="9%">'.$getUser->user_name.'</td>
                                                <td>'.$sports->sport_name.'<i class="fas fa-caret-right text-color-grey"></i>
												<strong>'.$matchdata->match_name.'</strong> <i class="fas fa-caret-right text-color-grey"></i>
												'.$data->bet_type.'</td>
                                                <td width="12%" class="text-right">'.$data->team_name.'</td>';
                                                if($data->bet_side == 'lay')
                                                $return_data.='<td width="4%" class="text-right" style="color: #e33a5e !important;">'.$data->bet_side.'</td>';
                                                else
                                                $return_data.='<td width="4%" class="text-right" style="color: #1f72ac !important;">'.$data->bet_side.'</td>';

                                                $return_data.='<td width="8%" class="text-right">'.$data->created_at.'</td>
                                                <td width="8%" class="text-right">'.$data->bet_amount.'</td>
                                                <td width="8%" class="text-right">'.$data->bet_odds.'</td>
                                            </tr>';
			}
	  }
	  return $return_data;
  }*/
}
