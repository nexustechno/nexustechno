<?php

namespace App\Http\Controllers;

use App\Agent;
use App\setting;
use App\User;
use App\UserHirarchy;
use App\UserExposureLog;
use App\UsersAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Auth;
use Carbon\Carbon;
use App\CreditReference;
use Request as resAll;

class AgentController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */

    public static function userBalance($userId)
    {

        $hirUser = UserHirarchy::where('agent_user', $userId)->first();
        $hirUser_bal = 0;
        $totalClientBal = 0;
        $totalExposure = 0;
        $posTotal = 0;
        $negTotal = 0;
        $cumulative_pl = 0;

        if (!empty($hirUser)) {
            $getuserArray = explode(',', $hirUser->sub_user);

            $hirUser_bal = CreditReference::whereIn('player_id', $getuserArray)->whereHas('user', function ($q) {
                $q->where('agent_level','!=','PL');
            })->sum('available_balance_for_D_W');

            foreach ($getuserArray as $value_data) {
                $exposer = 0;
                $userData = User::where('id', $value_data)->first();
                if (!empty($userData->agent_level)) {
                    if ($userData->agent_level != 'PL') {
//                        $hirUser_bal += CreditReference::where('player_id', $value_data)->sum('available_balance_for_D_W');
                    } else {
                        $credit_dataclient = CreditReference::where('player_id', $value_data)->select('remain_bal', 'exposure')->first();
                        if (!empty($credit_dataclient)) {
                            $totalClientBal += $credit_dataclient->remain_bal;
                            if ($credit_dataclient->exposure < 0) {
                                $posTotal = abs($credit_dataclient->exposure);
                            } else {
                                $negTotal = $credit_dataclient->exposure;
                            }
                            $exposer = $posTotal + $negTotal;
                        }

                        // calculate cumulative PL
                        $cumulative_pl_profit_get = UserExposureLog::where('user_id', $value_data)->where('win_type', 'Profit')->where('bet_type', 'ODDS')->sum('profit');
                        $cumulative_pl_profit = UserExposureLog::where('user_id', $value_data)->where('win_type', 'Profit')->where('bet_type', '!=', 'ODDS')->sum('profit');
                        $cumulative_pl_loss = UserExposureLog::where('user_id', $value_data)->where('win_type', 'Loss')->sum('loss');
                        $cumu_n = 0;
                        $cumu_n = $cumulative_pl_profit_get * ($userData->commission) / 100;
                        $cumuPL_n = $cumulative_pl_profit_get + $cumulative_pl_profit - $cumu_n;
                        $cumulative_pl2 = $cumuPL_n - $cumulative_pl_loss;

                        $totalExposure += $exposer;
                        $cumulative_pl += $cumulative_pl2;
                    }
                }
            }
        }

        return $hirUser_bal . '~' . $totalClientBal . '~' . $totalExposure . '~' . $cumulative_pl;
    }

    public function dashboardStatistics()
    {
        $loginuser = Auth::user();
        if ($loginuser->agent_level == 'COM') {
            $settings = setting::latest('id')->first();
            $balance = $settings->balance;
            $remain_bal = $settings->balance;;
        } else {
            $settings = CreditReference::where('player_id', $loginuser->id)->first();
            $balance = $settings['available_balance_for_D_W'];
            $remain_bal = $settings['remain_bal'];
        }

        $website = UsersAccount::getWebsite();

        $hirUser = UserHirarchy::where('agent_user', $loginuser->id)->first();
        $hirUser_bal = 0;
        $totalClientBal = 0;
        $totalExposure = 0;
        $posTotal = 0;
        $negTotal = 0;

//        $settingController = app(SettingController::class);

        $arr = [];
        if (!empty($hirUser)) {
            $getuserArray = explode(',', $hirUser->sub_user);

            foreach ($getuserArray as $value_data) {
                $userData = User::where('id', $value_data)->first();
                $exposer = 0;
                if (!empty($userData) && !empty($userData->agent_level)) {
                    if ($userData->agent_level != 'PL') {
                        $hirUser_bal += CreditReference::where('player_id', $value_data)->sum('available_balance_for_D_W');
                    } else {
                        $credit_dataclient = CreditReference::where('player_id', $value_data)->select('remain_bal', 'exposure')->first();
                        if (!empty($credit_dataclient)) {
                            $totalClientBal += $credit_dataclient->remain_bal;
                            if ($credit_dataclient->exposure < 0) {
                                $posTotal = abs($credit_dataclient->exposure);
                            } else {
                                $negTotal = $credit_dataclient->exposure;
                            }

                            $exposer = $posTotal + $negTotal;

                            $arr[$value_data] = $exposer;
                            $arr[$value_data.'ref'] = $credit_dataclient->toArray();

                            $totalExposure+= $exposer;
                        }
                    }

                    // calculate cumulative PL
//                    $cumulative_pl_profit_get = UserExposureLog::where('user_id', $value_data)->where('win_type', 'Profit')->where('bet_type', 'ODDS')->sum('profit');
//                    $cumulative_pl_profit = UserExposureLog::where('user_id', $value_data)->where('win_type', 'Profit')->where('bet_type', '!=', 'ODDS')->sum('profit');
//                    $cumulative_pl_loss = UserExposureLog::where('user_id', $value_data)->where('win_type', 'Loss')->sum('loss');
//                    $cumu_n = 0;
//                    $cumu_n = $cumulative_pl_profit_get * ($userData->commission) / 100;
//                    $cumuPL_n = $cumulative_pl_profit_get + $cumulative_pl_profit - $cumu_n;
//                    $cumulative_pl += $cumuPL_n - $cumulative_pl_loss;
                }
            }

        }

//        dd($arr);

        return response()->json(array(
            'balance' => number_format($balance,2,'.',''),
            'remain_bal' => number_format($remain_bal,2,'.',''),
            'hirUser_bal' => number_format($hirUser_bal,2,'.',''),
            'totalClientBal' => number_format($totalClientBal,2,'.',''),
            'totalExposure' => number_format($totalExposure,2,'.',''),
        ), 200);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $getuser = Auth::user();
        $data['password'] = Hash::make($request['password']);
        $data['parentid'] = $getuser->id;
        $data['first_login'] = 0;
        $data['ip_address'] = resAll::ip();

        if($request->has('partnership_perc') && !empty($request->partnership_perc)){

        }else{
            $data['partnership_perc'] = 0;
        }

        $data['dealy_time'] = $request['odds'];
        if ($getuser->agent_level != 'COM') {
            $data['dealy_time'] = $getuser->dealy_time;
            $data['bookmaker'] = $getuser->bookmaker;
            $data['fancy'] = $getuser->fancy;
            $data['soccer'] = $getuser->soccer;
            $data['tennis'] = $getuser->tennis;
        }

        $lid = User::create($data);

        $last_id = $lid->id;

        $cref = CreditReference::create([
            'player_id' => $last_id,
            'credit' => 0,
            'remain_bal' => 0,
            'available_balance_for_D_W' => 0,
        ]);

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
            $gethirUser['sub_user'] = $gethirUser->sub_user . ',' . $lid->id;
            $gethirUser->update();
        }

        $data_user = UserHirarchy::whereRaw("find_in_set('" . $getuser->id . "',sub_user)")->get();
        foreach ($data_user as $value) {
            $data_user_upd = UserHirarchy::where('id', $value->id)->first();
            $data_user_upd->sub_user = $value->sub_user . ',' . $lid->id;
            $data_user_upd->update();
        }
        return redirect()->route('home')->with('message', 'Agent created successfully!');
    }

    public function getusername(Request $request)
    {
        $uvalue = $request->uvalue;
        $user = User::where('user_name', $uvalue)->get();
        return response()->json(array('result' => $user), 200);
    }

    public function storeuser(Request $request)
    {
        $getuser = Auth::user();
        $data = $request->all();
        $getuser = Auth::user();
        $data['password'] = Hash::make($request['password']);
        $data['parentid'] = $getuser->id;
        $data['first_login'] = 0;
        $last_id = User::create($data)->id;
        $cref = CreditReference::create([
            'player_id' => $last_id,
            'credit' => 0,
            'remain_bal' => 0,
            'available_balance_for_D_W' => 0,
        ]);
        return redirect()->route('privileges')
            ->with('message', 'Agent created successfully.');
    }
}
