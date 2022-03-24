<?php

namespace App\Http\Controllers;

use App\Casino;
use App\CasinoBet;
use App\UsersAccount;
use Illuminate\Http\Request;
use Auth;
use App\User;
use Illuminate\Support\Facades\Hash;
use Redirect;
use Request as resAll;
use Carbon\Carbon;
use App\CreditReference;
use DB;
use App\UserDeposit;
use App\setting;
use App\Sport;
use App\MyBets;
use App\Match;
use App\FancyResult;
use App\UserExposureLog;
use App\UserHirarchy;

class MyaccountController extends Controller
{
    public function index()
    {
        $loginuser = Auth::user();
        $user = User::where('id', $loginuser->id)->first();
        $id = $loginuser->id;
        return view('backpanel.myaccount-summary', compact('user', 'id'));
    }

    public function accountprofile()
    {
        $loginuser = Auth::user();
        $user = User::where('id', $loginuser->id)->first();
        return view('backpanel.myaccount-profile', compact('user'));
    }

    public function myaccountstatement(Request $request)
    {
        $loginuser = Auth::user();
        $user = User::where('id', $loginuser->id)->first();
        $list = User::where('parentid', $loginuser->id)->latest()->get();
        $id = $loginuser->id;
        return view('backpanel.myaccount-statement', compact('user', 'list', 'id'));
    }

    public function accountstatement(Request $request)
    {
        $loginuser = Auth::user();
        $user = User::where('id', $loginuser->id)->first();
        $list = User::where('parentid', $loginuser->id)->latest()->get();
        $id = $loginuser->id;
        return view('backpanel.acount-statement', compact('user', 'list', 'id'));
    }

    public function accountStatementData(Request $request)
    {
        $loginuser = Auth::user();

        if (!empty($request->user) && $request->user != null) {
            $id = $request->user;
        } else {
            $id = $loginuser->id;
        }

        if ($request->startdate) {
            $fromdate = date('Y-m-d', strtotime($request->startdate));
        }
        if ($request->todate) {
            $todate = date('Y-m-d', strtotime($request->todate));
        }

        if ($request->startdate == $request->todate) {
            $fromdate = date('Y-m-d', strtotime($request->startdate));
            $todate = date('Y-m-d', strtotime($request->todate . "+1 day"));
        }
        $drpval = intval($request->drpval);
        if ($drpval > 0) {
            if ($drpval == 1) {
                if($request->report_for!='all'){
                    if($request->report_for=='upper') {
                        $records = UsersAccount::where("user_id", $id)->where("to_user_id", $id)->whereBetween('created_at', [$fromdate, $todate])->where('match_id', 0)->where('casino_id', 0)->orderBy('created_at', 'ASC')->get();
                    }else{
                        $records = UsersAccount::where("user_id", $id)->where("from_user_id", $id)->whereBetween('created_at', [$fromdate, $todate])->where('match_id', 0)->where('casino_id', 0)->orderBy('created_at', 'ASC')->get();
                    }
                }else{
                    $records = UsersAccount::where("user_id", $id)->whereBetween('created_at', [$fromdate, $todate])->where('match_id', 0)->where('casino_id', 0)->orderBy('created_at', 'ASC')->get();
                }
            } else {
                $records = UsersAccount::where("user_id", $id)->whereBetween('created_at', [$fromdate, $todate])->where(function ($q){
                    $q->where('match_id', '!=', 0);
                    $q->orWhere('casino_id', '!=', 0);
                })->orderBy('created_at', 'ASC')->get();
            }
        } else {
            $records = UsersAccount::where("user_id", $id)->whereBetween('created_at', [$fromdate, $todate])->orderBy('created_at', 'ASC')->get();
        }

        $record = UsersAccount::where("user_id", $id)->where('created_at', "<", $fromdate)->orderBy('created_at', 'ASC')->first();
        $openingBalanceDate = '';
        $openingBalance = 0;
        if (!empty($record)) {
            $openingBalance = $record->closing_balance;
            $openingBalanceDate = date('d-m-y H:i', strtotime($record->created_at));
        }

        $closing_balance = $openingBalance;
        $i = 1;
        $html = '';
        $html .= '<tr>
                        <td style="width: 110px"> ' . $i . ' </td>
                        <td style="width: 110px"> ' . $openingBalanceDate . ' </td>
                        <td style="width: 110px;text-align: right;" class="text-color-green">0</td>
                        <td style="width: 110px;text-align: right;" class="text-color-red">0</td>
                        <td style="text-align: right;">' . $openingBalance . '</td>
                        <td>Opening Balance</td>
			        </tr>';

        foreach ($records as $data) {
            $username = '';
            if ($data->from_user_id > 0) {
                $fromUser = User::select('id', 'user_name')->where('id', $data->from_user_id)->first();
                $toUser = User::select('id', 'user_name')->where('id', $data->to_user_id)->first();
                if (empty($toUser) || empty($fromUser)) {
                    continue;
                }
                $username = $fromUser->user_name . ' <i class="fas fa-caret-right text-color-grey"></i> ' . $toUser->user_name;
            }

            $remark = $data->remark;

            if ($data->match_id > 0) {

                $match = Match::where("id", $data->match_id)->first();

                $sprtnm = Sport::where('sId', $match->sports_id)->first();

                $log = UserExposureLog::where("id", $data->user_exposure_log_id)->first();

                if ($log->bet_type != 'SESSION') {
                    $bet = MyBets::where('user_id', $data->bet_user_id)
                        ->where('result_declare', 1)
                        ->where('isDeleted', 0)
                        ->where('match_id', $match->event_id)
                        ->whereBetween('created_at', [$fromdate, $todate])
                        ->groupBy('bet_type')
                        ->where('bet_type', $log->bet_type)
                        ->orderBy('created_at')
                        ->first();
                    if(empty($bet)){
                        continue;
                    }

                    $remark = '<span><a data-betuserid="' . $data->bet_user_id . '" data-id="' . $match->event_id . '" data-name="' . $bet->team_name . '" data-type="' . $bet->bet_type . '" class="text-dark" onclick="openMatchReport(this);" >' . $sprtnm->sport_name . ' / ' . $match->match_name . ' / ' . $bet->bet_type . ' / ' . $match->winner . '</a></span>';
                } else {
                    $bet = MyBets::where('user_id', $data->bet_user_id)
                        ->where('result_declare', 1)
                        ->where('isDeleted', 0)
                        ->where('bet_type', 'SESSION')
                        ->where('team_name', $log->fancy_name)
                        ->groupBy('team_name')
                        ->where('match_id', $match->event_id)
                        ->whereBetween('created_at', [$fromdate, $todate])
                        ->orderBy('created_at')
                        ->first();
                    if(empty($bet)){
                        continue;
                    }

                    $fnc_rslt = FancyResult::where('eventid', $match->event_id)->where('fancy_name', $bet->team_name)->first();

                    $f_result = 0;
                    if (!empty($fnc_rslt)) {
                        $f_result = $fnc_rslt->result;
                    }

                    $remark = '<span><a data-betuserid="' . $data->bet_user_id . '" data-id="' . $match->event_id . '" data-name="' . $bet->team_name . '" data-type="' . $bet->bet_type . '" class="text-dark" onclick="openMatchReport(this);" >' . $sprtnm->sport_name . ' / ' . $bet->team_name . ' / ' . $bet->bet_type . ' / ' . $f_result . '</a></span>';
                }
            } elseif ($data->casino_id > 0) {
                $casino = Casino::find($data->casino_id);
                $casinoBet = CasinoBet::where("id", $data->user_exposure_log_id)->first();
                if (!empty($casinoBet)) {
                    $roundId = explode(".", $casinoBet->roundid);
                    $remark = '<span><a data-betuserid="' . $data->bet_user_id . '" data-id="' . $casinoBet->roundid . '" data-name="' . $casinoBet->team_name . '" data-type="casino" class="text-dark" onclick="openMatchReport(this);" >CASINO / ' . $casino->casino_title . ' / ' . $casinoBet->team_name . ' / ' . $roundId[1] . ' / ' . strtoupper($casinoBet->bet_side) . ' / ' . $casinoBet->winner . '</a></span>';
                }
            }

            if ($data->remark == 'Commission') {
                $remark .= "(" . $data->remark . ")";
            }

            if ($data->credit_amount > 0) {
                $closing_balance += $data->credit_amount;
            } else {
                $closing_balance -= $data->debit_amount;
            }

            $html .= '<tr>
                        <td style="width: 110px"> ' . ($i) . ' </td>
                        <td style="width: 110px"> ' . date('d-m-y H:i', strtotime($data->created_at)) . ' </td>
                        <td style="width: 110px" class="text-color-green">' . $data->credit_amount . '</td>
                        <td style="width: 110px" class="text-color-red">' . $data->debit_amount . '</td>
                        <td>' . $closing_balance . '</td>
                        <td>' . $remark . ' </td>
                        <td style="width: 120px">' . $username . '</td>
			        </tr>';

            $i++;
        }
        return $html;
    }

    public function getAccountStatmentPopupData(Request $request)
    {
        $loginuser = Auth::user();

        if (!empty($request->user) && $request->user != null) {
            $userid = $request->user;
        } else {
            $userid = $loginuser->id;
        }

        if ($request->startdate) {
            $fromdate = date('Y-m-d', strtotime($request->startdate));
        }
        if ($request->startdate) {
            $todate = date('Y-m-d', strtotime($request->todate));
        }

        if ($request->startdate == $request->todate) {
            $fromdate = date('Y-m-d', strtotime($request->startdate));
            $todate = date('Y-m-d', strtotime($request->todate . "+1 day"));
        }

        $mid = $request->mid;
        $btyp = $request->btyp;
        $tnm = $request->tnm;
        $betuserid = $request->betuserid;

        if ($btyp == 'SESSION') {
            $gmdata = MyBets::where('user_id', $betuserid)
                ->where('result_declare', 1)
                ->where('match_id', $mid)
                ->where('bet_type', $btyp)
                ->where('team_name', $tnm)
                //->groupBy('team_name',$tnm)
                ->whereBetween('created_at', [$fromdate, $todate])
                ->get();
        } elseif ($btyp == 'casino') {
            $gmdata = CasinoBet::where('user_id', $betuserid)->where('roundid', $mid)->get();
        } else {
            $gmdata = MyBets::where('user_id', $betuserid)
                ->where('result_declare', 1)
                ->where('match_id', $mid)
                ->where('bet_type', $btyp)
                ->whereBetween('created_at', [$fromdate, $todate])
                ->get();
        }
        if ($btyp != 'casino') {
            $matchdata = Match::where('event_id', $mid)->first();
        }
        $html = '';
        $i = 1;
        $sumAmt = 0;
        foreach ($gmdata as $data) {
            if ($btyp == 'casino') {
                $winner = strtolower($data->winner);
                $html .= '<tr role="row" class="' . $data->bet_side . '">
                    <td aria-colindex="1" role="cell" class="text-right">
                        <span>' . $i . '</span>
                    </td>
                    <td aria-colindex="2" role="cell" class="text-center">' . $data->team_name . '</td>
                    <td aria-colindex="3" role="cell" class="text-center">ODDS</td>';
                $html .= '<td aria-colindex="4" role="cell" class="text-center" style="text-transform: uppercase;">' . $data->bet_side . '</td>';
                $html .= '<td aria-colindex="5" role="cell" class="text-center">' . $data->odds_value . '</td>';
                $html .= '<td aria-colindex="6" role="cell" class="text-right">' . $data->stake_value . '</td>';
                $html .= '<td aria-colindex="7" role="cell" class="text-right">';
                if ($winner == strtolower($data->team_name) && $data->bet_side == 'back') {
                    $sumAmt += $data->casino_profit;
                    $html .= '<span class="text-success">' . $data->casino_profit . '</span> ';
                } else if ($winner != strtolower($data->team_name) && $data->bet_side == 'back') {
                    $sumAmt -= $data->exposureAmt;
                    $html .= '<span class="text-danger">' . $data->exposureAmt . '</span> ';
                } else if ($winner != strtolower($data->team_name) && $data->bet_side == 'lay') {
                    $sumAmt += $data->casino_profit;
                    $html .= '<span class="text-success">' . $data->casino_profit . '</span> ';
                } else if ($winner == strtolower($data->team_name) && $data->bet_side == 'lay') {
                    $sumAmt -= $data->exposureAmt;
                    $html .= '<span class="text-danger">' . $data->exposureAmt . '</span> ';
                }

                $html .= '</td><td aria-colindex="9" role="cell" class="text-center">' . $data->created_at . '</td></tr>';
            } else {
                $winner = strtolower($matchdata->winner);

                $html .= '
	    	<tr role="row" class="' . $data->bet_side . '">
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
                    if ($winner == strtolower($data->team_name) && $data->bet_side == 'back') {
                        $sumAmt += $data->bet_profit;

                        $html .= '<span class="text-success">
			                    ' . $data->bet_profit . '
			                </span> ';
                    } else if ($winner != strtolower($data->team_name) && $data->bet_side == 'back') {
                        $sumAmt -= $data->exposureAmt;
                        $html .= '<span class="text-danger">
			                    ' . $data->exposureAmt . '
			                </span> ';
                    } else if ($winner != strtolower($data->team_name) && $data->bet_side == 'lay') {
                        $sumAmt += $data->bet_profit;
                        $html .= '<span class="text-success">
			                    ' . $data->bet_profit . '
			                </span> ';
                    } else if ($winner == strtolower($data->team_name) && $data->bet_side == 'lay') {
                        $sumAmt -= $data->exposureAmt;
                        $html .= '<span class="text-danger">
			                    ' . $data->exposureAmt . '
			                </span> ';
                    }
                }
                if ($data->bet_type == 'BOOKMAKER') {
                    if ($winner == strtolower($data->team_name) && $data->bet_side == 'back') {
                        $sumAmt += $data->bet_profit;
                        $html .= '<span class="text-success">
			                    ' . $data->bet_profit . '
			                </span> ';
                    } else if ($winner != strtolower($data->team_name) && $data->bet_side == 'back') {
                        $sumAmt -= $data->exposureAmt;
                        $html .= '<span class="text-danger">
			                    ' . $data->exposureAmt . '
			                </span> ';
                    } else if ($winner != strtolower($data->team_name) && $data->bet_side == 'lay') {
                        $sumAmt += $data->bet_profit;
                        $html .= '<span class="text-success">
			                    ' . $data->bet_profit . '
			                </span> ';
                    } else if ($winner == strtolower($data->team_name) && $data->bet_side == 'lay') {
                        $sumAmt -= $data->exposureAmt;
                        $html .= '<span class="text-danger">
			                    ' . $data->exposureAmt . '
			                </span> ';
                    }
                }
                if ($data->bet_type == 'SESSION') {

                    $fancydata = FancyResult::where(['eventid' => $mid, 'fancy_name' => $data->team_name])->first();
                    if ($data->bet_type == 'SESSION') {

                        if($fancydata->result =='cancel'){
                            $html .= '<span class="text-danger">0</span> ';
                        }else {
                            if ($data->bet_side == 'back') {
                                if ($data->bet_odds <= $fancydata->result) {
                                    $sumAmt += $data->bet_profit;
                                    $html .= '<span class="text-success">
									' . $sumAmt = $data->bet_profit . '
									</span> ';
                                } else {
                                    $sumAmt -= $data->exposureAmt;
                                    $html .= '<span class="text-danger">
									' . $sumAmt = $data->exposureAmt . '
									</span> ';
                                }
                            } else if ($data->bet_side == 'lay') {
                                if ($data->bet_odds > $fancydata->result) {
                                    $sumAmt += $data->bet_profit;
                                    $html .= '<span class="text-success">
									' . $sumAmt = $data->bet_profit . '
									</span> ';
                                } else {
                                    $sumAmt -= $data->exposureAmt;
                                    $html .= '<span class="text-danger">
									' . $sumAmt = $data->exposureAmt . '
									</span> ';
                                }
                            }
                        }
                    }
                }

                $html .= '</td>
	            <td aria-colindex="9" role="cell" class="text-center">' . $data->created_at . '</td>
	        </tr>';
            }
            $i++;
        }

        $html .= '<tr role="row"><td aria-colindex="1" role="cell" class="text-right" colspan="6">Total</td>';
        if ($sumAmt > 0) {
            $html .= '<td aria-colindex="2" role="cell" class="text-right text-success">' . abs($sumAmt) . '</td>';
        } else {
            $html .= '<td aria-colindex="2" role="cell" class="text-right text-danger">' . abs($sumAmt) . '</td>';
        }
        $html .= '<td aria-colindex="3" role="cell" class="text-right"></td></tr>';

        return $html;
    }

    public function datamyaccountstatement(Request $request)
    {

        $loginuser = Auth::user();

        if (!empty($request->user) && $request->user != null) {
            $id = $request->user;
        } else {
            $id = $loginuser->id;
        }

        if ($request->startdate) {
            $fromdate = date('Y-m-d', strtotime($request->startdate));
        }
        if ($request->todate) {
            $todate = date('Y-m-d', strtotime($request->todate));
        }

        if ($request->startdate == $request->todate) {
            $fromdate = date('Y-m-d', strtotime($request->startdate));
            $todate = date('Y-m-d', strtotime($request->todate . "+1 day"));
        }

        $records = UsersAccount::where("user_id", $id)->whereBetween('created_at', [$fromdate, $todate])->where('match_id', 0)->where('casino_id', 0)->orderBy('created_at', 'ASC')->get();

        $record = UsersAccount::where("user_id", $id)->where('created_at', "<", $fromdate)->orderBy('created_at', 'ASC')->first();
        $openingBalanceDate = '';
        $openingBalance = 0;
        if (!empty($record)) {
            $openingBalance = $record->closing_balance;
            $openingBalanceDate = date('d-m-y H:i', strtotime($record->created_at));
        }

        $closing_balance = $openingBalance;

        $html = '';
        $html .= '<tr>
                        <td style="width: 110px"> ' . $openingBalanceDate . ' </td>
                        <td style="width: 110px;text-align: right;" class="text-color-green">0</td>
                        <td style="width: 110px;text-align: right;" class="text-color-red">0</td>
                        <td style="text-align: right;">' . $openingBalance . '</td>
                        <td>Opening Balance</td>
                         <td style="width: 120px">-</td>
			        </tr>';

        foreach ($records as $data){
            if ($data->credit_amount > 0) {
                $closing_balance += $data->credit_amount;
            } else {
                $closing_balance -= $data->debit_amount;
            }

            $username = '';
            if ($data->from_user_id > 0) {
                $fromUser = User::select('id', 'user_name')->where('id', $data->from_user_id)->first();
                $toUser = User::select('id', 'user_name')->where('id', $data->to_user_id)->first();
                if (empty($toUser) || empty($fromUser)) {
                    continue;
                }
                $username = $fromUser->user_name . ' <i class="fas fa-caret-right text-color-grey"></i> ' . $toUser->user_name;
            }

            $html .= '<tr>
                        <td style="width: 110px"> ' . date('d-m-y H:i', strtotime($data->created_at)) . ' </td>
                        <td style="width: 110px" class="text-color-green">' . $data->credit_amount . '</td>
                        <td style="width: 110px" class="text-color-red">' . $data->debit_amount . '</td>
                        <td>' . $closing_balance . '</td>
                        <td>' . $data->remark . ' </td>
                        <td style="width: 120px">' . $username . '</td>
			        </tr>';
        }

        return $html;
    }

    public function myaccounttrasferredlog()
    {
        $loginuser = Auth::user();
        $user = User::where('id', $loginuser->id)->first();
        $id = $loginuser->id;
        return view('backpanel.myaccount-trasferred-log', compact('user', 'id'));
    }

    public function myaccountactivelog()
    {
        $loginuser = Auth::user();
        $user = User::where('id', $loginuser->id)->first();
        return view('backpanel.myaccount-active-log', compact('user'));
    }

    public function updateAccountPassword(Request $request, $id)
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
        return redirect()->route('home')->with('message', 'Password Change Successfully');
    }

    public function commisionreport()
    {
        $user = User::where('agent_level', 'PL')->get();
        return view('backpanel.commision-report', compact('user'));
    }

    public function profitlossmarket()
    {
        $loginuser = Auth::user();
        $sports = Sport::where('status', 'active')->get();
        $users = User::where('parentid', $loginuser->id)->latest()->get();
        return view('backpanel.profitloss-market', compact('sports', 'users'));
    }

    public function marketPLdata(Request $request)
    {
        $html = '';
        $sport_data = $request->sport;
        $childlist = $request->childlist;
        $val = $request->val;
        $totcms = 0;
        $totAmto = 0;
        $totcmsG = 0;
        $totcmsR = 0;
        $ttlnpG = 0;
        $ttlnpR = 0;
        $totttlAmt = 0;
        $ttlAmts = 0;
        $sumAmtG = 0;
        $sumAmtR = 0;
        $totsumAmt = 0;
        $ttlnps = 0;
        $ttlnpsG = 0;
        $ttlnpsR = 0;
        $ttlnps1 = 0;
        $ttlnps2 = 0;
        $totsumAmtbG = 0;
        $totsumAmtbR = 0;
        $totttlAmtb = 0;

        $totttlnp = 0;
        if ($val == 'today') {
            $fromdate = date('Y-m-d') . ' 09:00';
            $todate = date("Y-m-d", strtotime("+1 day")) . ' 08:59';
        } else if ($val == 'yesterday') {
            $fromdate = date("Y-m-d", strtotime("-1 day")) . ' 09:00';
            $todate = date('Y-m-d') . ' 08:59';
        } else {
            $fromdate = date('Y-m-d', strtotime($request->fromdate)) . ' 09:00';
            //$todate1 = date('Y-m-d',strtotime($request->todate));
            $todate = date("Y-m-d", strtotime($request->todate)) . ' 08:59';
        }
        /* echo $fromdate;
        echo "/";
        echo $todate;
        exit;*/

        if ($childlist != 0) {
            $chk = User::where('id', $childlist)->first();
            $clist = [$childlist];
            if ($chk->agent_level == 'PL') {
                if ($sport_data != 0) {
                    $getresult = MyBets::select('my_bets.match_id', 'my_bets.sportID')
                        ->join('user_exposure_log', 'user_exposure_log.user_id', '=', 'my_bets.user_id')
                        ->where(['my_bets.sportID' => $sport_data, 'my_bets.user_id' => $childlist, 'my_bets.result_declare' => 1])
                        ->where('my_bets.isDeleted', 0)
                        ->whereBetween('user_exposure_log.created_at', [$fromdate, $todate])
                        ->groupBy('my_bets.match_id')
                        ->orderBy('user_exposure_log.created_at')
                        ->get();
                } else {
                    $getresult = MyBets::select('my_bets.match_id', 'my_bets.sportID')
                        ->join('user_exposure_log', 'user_exposure_log.user_id', '=', 'my_bets.user_id')
                        ->where(['my_bets.user_id' => $childlist, 'my_bets.result_declare' => 1])
                        ->whereBetween('user_exposure_log.created_at', [$fromdate, $todate])
                        ->groupBy('my_bets.match_id')
                        ->where('my_bets.isDeleted', 0)
                        ->orderBy('user_exposure_log.created_at')
                        ->get();
                }

                if (!empty($getresult)) {
                    foreach ($getresult as $data) {
                        $sports = Sport::where('sId', $data->sportID)->first();
                        $matchdata = Match::where('event_id', $data->match_id)->first();

                        $subresult = MyBets::where('match_id', $data->match_id)
                            ->where('isDeleted', 0)
                            //->whereBetween('created_at',[$fromdate,$todate])
                            ->groupBy('bet_type')
                            ->latest()
                            ->get();

                        $ttlAmto = 0;
                        $sumAmto = 0;
                        $sumAmt = 0;
                        $ttlAmt = 0;
                        $ttlAmtb = 0;
                        $sumAmtb = 0;
                        $sumAmt1 = 0;
                        $sumAmt2 = 0;
                        $ttlodd = 0;
                        foreach ($subresult as $key => $value) {
                            if ($value->bet_type == 'SESSION') {
                                $betlist1 = MyBets::where('user_id', $childlist)
                                    ->where('result_declare', 1)
                                    ->where('bet_type', 'SESSION')
                                    ->where('isDeleted', 0)
                                    ->groupBy('team_name')
                                    ->where('match_id', $data->match_id)
                                    //->whereBetween('created_at',[$fromdate,$todate])
                                    ->orderBy('created_at')
                                    ->get();

                                $betlist2 = MyBets::where('user_id', $childlist)
                                    ->where('result_declare', 1)
                                    ->where('bet_type', 'SESSION')
                                    ->where('isDeleted', 0)
                                    ->where('match_id', $data->match_id)
                                    //->whereBetween('created_at',[$fromdate,$todate])
                                    ->orderBy('created_at')
                                    ->get();

                                foreach ($betlist2 as $key => $value2) {
                                    $ttlAmt += $value2->bet_amount;
                                }
                                $ttlAmts += $ttlAmt;

                                foreach ($betlist1 as $key => $value1) {

                                    $fnc_rslt = FancyResult::where('eventid', $data->match_id)->where('fancy_name', $value1->team_name)->first();

                                    $f_result = 0;
                                    if (!empty($fnc_rslt)) {
                                        $f_result = $fnc_rslt->result;
                                    }

                                    $exposer_fancy_a = UserExposureLog::where('match_id', $matchdata->id)->where('bet_type', 'SESSION')->whereBetween('created_at', [$fromdate, $todate])->where('fancy_name', $value1->team_name)->where('user_id', $childlist)->get();

                                    foreach ($exposer_fancy_a as $key => $exposer_fancy) {
                                        if (!empty($exposer_fancy)) {
                                            $fancy_win_type = $exposer_fancy['win_type'];
                                            if ($fancy_win_type == 'Profit')
                                                $sumAmt += $exposer_fancy->profit;
                                            else
                                                $sumAmt -= $exposer_fancy->loss;
                                        }
                                    }


                                }
                            } else if ($value->bet_type == 'ODDS') {

                                $betlist1 = MyBets::where('user_id', $childlist)
                                    ->where('result_declare', 1)
                                    ->where('bet_type', 'ODDS')
                                    ->where('match_id', $data->match_id)
                                    //->whereBetween('created_at',[$fromdate,$todate])
                                    ->orderBy('created_at')
                                    ->get();


                                foreach ($betlist1 as $key => $value1) {
                                    $ttlAmto += $value1->bet_amount;
                                }
                                $totAmto += $ttlAmto;
                                $expodds = UserExposureLog::where('match_id', $matchdata->id)->where('user_id', $childlist)->whereBetween('created_at', [$fromdate, $todate])->whereBetween('created_at', [$fromdate, $todate])->where('bet_type', 'ODDS')->first();


                                if ($expodds) {
                                    if ($expodds->bet_type == 'ODDS') {
                                        if ($expodds->win_type == 'Profit') {
                                            $sumAmt1 = $expodds->profit;

                                            $ttlcm = ($sumAmt1 * $chk->commission) / 100;
                                            $cms = $sumAmt1 - $ttlcm;

                                            $ttlodd += $cms;
                                        } else if ($expodds->win_type == 'Loss') {
                                            //$sumAmto-=$expodds->loss;
                                            $sumAmt2 = $expodds->loss;
                                            $ttlodd -= $sumAmt2;
                                        }
                                    }
                                }
                            } else if ($value->bet_type == 'BOOKMAKER') {
                                $betlist1 = MyBets::where('user_id', $childlist)
                                    ->where('result_declare', 1)
                                    ->where('bet_type', 'BOOKMAKER')
                                    ->where('isDeleted', 0)
                                    ->where('match_id', $data->match_id)
                                    //->whereBetween('created_at',[$fromdate,$todate])
                                    ->orderBy('created_at')
                                    ->get();

                                foreach ($betlist1 as $key => $value1) {
                                    $ttlAmtb += $value1->bet_amount;
                                }
                                $totttlAmtb += $ttlAmtb;

                                $exposer_bm = UserExposureLog::where('bet_type', 'BOOKMAKER')->where('match_id', $matchdata->id)->whereBetween('created_at', [$fromdate, $todate])->where('user_id', $childlist)->whereBetween('created_at', [$fromdate, $todate])->first();
                                if (!empty($exposer_bm)) {
                                    $bm_win_type = $exposer_bm['win_type'];
                                    if ($bm_win_type == 'Profit')
                                        $sumAmtb += $exposer_bm->profit;
                                    else
                                        $sumAmtb -= $exposer_bm->loss;
                                }
                            }
                        }
                        $html .= '
                        <tr>
                            <td class="white-bg"><img src="' . asset('asset/img/plus-icon.png') . '">
                                <a class="ico_account text-color-blue-light">
                                    ' . $sports->sport_name . ' <i class="fas fa-caret-right text-color-grey"></i> <strong> ' . $matchdata->match_name . ' </strong>
                                </a>
                            </td>';
                        if (!empty($ttlodd)) {
                            $cms = $ttlodd;

                            if ($cms >= 0) {
                                $totcmsG += $cms;
                                $html .= '<td class="white-bg text-color-green">' . round($cms, 2) . ' </td>';
                            } else {
                                $totcmsR += $cms;
                                $html .= '<td class="white-bg text-color-red">' . round(abs($cms), 2) . ' </td>';
                            }
                        } else if ($ttlodd == 0) {
                            $html .= '<td class="white-bg text-color-green">0</td>';
                        } else {
                            $html .= '<td class="white-bg"> -- </td>';
                        }
                        if (!empty($ttlAmto)) {
                            $html .= '<td class="white-bg">' . $ttlAmto . ' </td>';
                        } else if ($ttlAmto == 0) {
                            $html .= '<td class="white-bg">0</td>';
                        } else {
                            $html .= '<td class="white-bg"> -- </td>';
                        }

                        if (!empty($sumAmtb)) {
                            if ($sumAmtb >= 0) {
                                $totsumAmtbG += $sumAmtb;
                                $html .= '<td class="white-bg text-color-green">' . round($sumAmtb, 2) . '</td>';
                            } else {
                                $totsumAmtbR += $sumAmtb;
                                $html .= '<td class="white-bg text-color-red">' . round(abs($sumAmtb), 2) . ' </td>';
                            }
                        } else if ($sumAmtb == 0) {
                            $html .= '<td class="white-bg text-color-green">0</td>';
                        } else {
                            $html .= '<td class="white-bg"> -- </td>';
                        }

                        if (!empty($ttlAmtb)) {
                            $html .= '<td class="white-bg">' . $ttlAmtb . ' </td>';
                        } else if ($ttlAmtb == 0) {
                            $html .= '<td class="white-bg">0</td>';
                        } else {
                            $html .= '<td class="white-bg"> -- </td>';
                        }

                        if (!empty($sumAmt)) {
                            if ($sumAmt >= 0) {
                                $sumAmtG += $sumAmt;
                                $html .= '<td class="white-bg text-color-green">' . round($sumAmt, 2) . ' </td>';
                            } else {
                                $sumAmtR += $sumAmt;
                                $html .= '<td class="white-bg text-color-red">' . round(abs($sumAmt), 2) . ' </td>';
                            }
                        } else if ($sumAmt == 0) {
                            $html .= '<td class="white-bg text-color-green">0</td>';
                        } else {
                            $html .= '<td class="white-bg"> -- </td>';
                        }

                        if (!empty($ttlAmt)) {
                            $html .= '<td class="white-bg">' . $ttlAmt . ' </td>';
                        } else if ($ttlAmt == 0) {
                            $html .= '<td class="white-bg">0</td>';
                        } else {
                            $html .= '<td class="white-bg"> -- </td>';
                        }

                        /*if(!empty($sumAmto))
                            {*/
                        $ttlnp = $ttlodd + $sumAmtb + $sumAmt;
                        if ($ttlnp > 0) {
                            $ttlnpsR += $ttlnp;
                            $html .= '<td class="white-bg text-color-red">' . round($ttlnp, 2) . '</td>';
                        } else {
                            $ttlnpsG += $ttlnp;
                            $html .= '<td class="white-bg text-color-green">' . round(abs($ttlnp), 2) . '</td>';
                        }
                        $ttlnps2 = abs($ttlnpsG) - abs($ttlnpsR);
                        // }
                        /*else if(empty($sumAmto)){
                                $ttlnp=$sumAmtb+$sumAmt;
                                if($ttlnp > 0)
                                {
                                    $html.='<td class="white-bg text-color-red">'.round($ttlnp,2).'</td>';
                                }
                                else{
                                    $html.='<td class="white-bg text-color-green">'.round(abs($ttlnp),2).'</td>';
                                }
                            }
                            else{

                                $html.='<td class="white-bg"> -- </td>';
                            }*/
                        $html .= '</tr>';
                    }
                } else {
                    $html .= 'No Record Found';
                }
            }
            else {
                $all_child = UserHirarchy::where('agent_user', $childlist)->first();
                $clist = (explode(",", $all_child->sub_user));
                $cmp = 0;

                if ($sport_data != 0) {
                    /*$getresult = MyBets::where(['sportID' => $sport_data, 'result_declare'=>1])
                    ->whereIn('user_id', $clist)
                    ->whereBetween('created_at',[$fromdate,$todate])
                    ->groupBy('match_id')
                    ->latest()
                    ->get();*/

                    $getresult = MyBets::select('my_bets.match_id', 'my_bets.sportID')
                        ->join('user_exposure_log', 'user_exposure_log.user_id', '=', 'my_bets.user_id')
                        ->where(['my_bets.sportID' => $sport_data, 'my_bets.result_declare' => 1])
                        ->whereIn('my_bets.user_id', $clist)
                        ->where('my_bets.isDeleted', 0)
                        ->whereBetween('user_exposure_log.created_at', [$fromdate, $todate])
                        ->groupBy('my_bets.match_id')
                        ->orderBy('user_exposure_log.created_at', 'Desc')
                        ->get();
                } else {
                    /*$getresult = MyBets::where('result_declare',1)
                    ->whereIn('user_id',$clist)
                    ->whereBetween('created_at',[$fromdate,$todate])
                    ->groupBy('match_id')
                    ->latest()
                    ->get();*/

                    $getresult = MyBets::select('my_bets.match_id', 'my_bets.sportID')
                        ->join('user_exposure_log', 'user_exposure_log.user_id', '=', 'my_bets.user_id')
                        ->where('my_bets.result_declare', 1)
                        ->whereIn('my_bets.user_id', $clist)
                        ->where('my_bets.isDeleted', 0)
                        ->whereBetween('user_exposure_log.created_at', [$fromdate, $todate])
                        ->groupBy('my_bets.match_id')
                        ->orderBy('user_exposure_log.created_at', 'Desc')
                        ->get();
                }

                if (!empty($getresult)) {
                    foreach ($getresult as $data) {
                        $usercm = User::where('id', $data->user_id)->first();
                        if (!empty($usercm->commission)) {
                            $cmp = $usercm->commission;
                        }

                        $sports = Sport::where('sId', $data->sportID)->first();
                        $matchdata = Match::where('event_id', $data->match_id)->first();

                        $subresult = MyBets::where('match_id', $data->match_id)
                            ->where('isDeleted', 0)
                            //->whereBetween('created_at',[$fromdate,$todate])
                            ->groupBy('bet_type')
                            ->latest()
                            ->get();

                        $ttlAmto = 0;
                        $sumAmto = 0;
                        $sumAmt = 0;
                        $ttlAmt = 0;
                        $ttlAmtb = 0;
                        $sumAmtb = 0;
                        $sumAmt1 = 0;
                        $sumAmt2 = 0;
                        $ttlodd = 0;
                        foreach ($subresult as $key => $value) {
                            if ($value->bet_type == 'SESSION') {
                                $betlist1 = MyBets::whereIn('user_id', $clist)
                                    ->where('result_declare', 1)
                                    ->where('isDeleted', 0)
                                    ->where('bet_type', 'SESSION')
                                    ->groupBy('team_name')
                                    ->where('match_id', $data->match_id)
                                    //->whereBetween('created_at',[$fromdate,$todate])
                                    ->orderBy('created_at')
                                    ->get();

                                $betlist2 = MyBets::whereIn('user_id', $clist)
                                    ->where('result_declare', 1)
                                    ->where('bet_type', 'SESSION')
                                    ->where('isDeleted', 0)
                                    ->where('match_id', $data->match_id)
                                    //->whereBetween('created_at',[$fromdate,$todate])
                                    ->orderBy('created_at')
                                    ->get();

                                $uid = array();
                                foreach ($betlist2 as $key => $value2) {
                                    $ttlAmt += $value2->bet_amount;
                                    $uid[] = $value2->user_id;
                                }
                                $ttlAmts += $ttlAmt;
                                $result_list = array_unique($uid);

                                foreach ($betlist1 as $key => $value1) {
                                    $fnc_rslt = FancyResult::where('eventid', $data->match_id)->where('fancy_name', $value1->team_name)->first();

                                    $f_result = 0;
                                    if (!empty($fnc_rslt)) {
                                        $f_result = $fnc_rslt->result;
                                    }

                                    $exposer_fancy_a = UserExposureLog::where('match_id', $matchdata->id)->whereBetween('created_at', [$fromdate, $todate])->where('bet_type', 'SESSION')->where('fancy_name', $value1->team_name)->whereIn('user_id', $result_list)->get();

                                    if (!empty($exposer_fancy_a)) {
                                        foreach ($exposer_fancy_a as $key => $exposer_fancy) {
                                            $fancy_win_type = $exposer_fancy['win_type'];
                                            if ($fancy_win_type == 'Profit') {
                                                $sumAmt = $sumAmt + (int)$exposer_fancy->profit;
                                            } else {
                                                $sumAmt = $sumAmt - (int)$exposer_fancy->loss;
                                            }
                                        }
                                    }
                                }
                            } else if ($value->bet_type == 'ODDS') {
                                $betlist1 = MyBets::whereIn('user_id', $clist)
                                    ->where('result_declare', 1)
                                    ->where('bet_type', 'ODDS')
                                    ->where('isDeleted', 0)
                                    ->where('match_id', $data->match_id)
                                    //->whereBetween('created_at',[$fromdate,$todate])
                                    ->orderBy('created_at')
                                    ->get();

                                $uid = array();
                                foreach ($betlist1 as $key => $value1) {
                                    $ttlAmto += $value1->bet_amount;
                                    $uid[] = $value1->user_id;
                                }
                                $totAmto += $ttlAmto;
                                $result_list = array_unique($uid);
                                $cmp = 0;
                                foreach ($result_list as $key => $cmdata) {
                                    $usercm = User::where('id', $cmdata)->first();
                                    if (!empty($usercm->commission)) {
                                        $cmp = $usercm->commission;
                                    }
                                    //echo $cmdata;
                                    //echo "**";

                                }
                                //echo "<br>";


                                $expodds_a = UserExposureLog::where('match_id', $matchdata->id)->whereIn('user_id', $result_list)->whereBetween('created_at', [$fromdate, $todate])->whereBetween('created_at', [$fromdate, $todate])->where('bet_type', 'ODDS')->get();

                                if (!empty($expodds_a)) {
                                    foreach ($expodds_a as $key => $expodds) {
                                        if ($expodds->bet_type == 'ODDS') {
                                            if ($expodds->win_type == 'Profit') {
                                                $sumAmt1 = $expodds->profit;

                                                $ttlcm = ($sumAmt1 * $cmp) / 100;
                                                $cms = $sumAmt1 - $ttlcm;

                                                $ttlodd += $cms;
                                            } else if ($expodds->win_type == 'Loss') {
                                                //$sumAmto-=$expodds->loss;
                                                $sumAmt2 = $expodds->loss;
                                                $ttlodd -= $sumAmt2;
                                            }
                                        }
                                    }
                                }
                            } else if ($value->bet_type == 'BOOKMAKER') {
                                $betlist1 = MyBets::whereIn('user_id', $clist)
                                    ->where('result_declare', 1)
                                    ->where('isDeleted', 0)
                                    ->where('bet_type', 'BOOKMAKER')
                                    ->where('match_id', $data->match_id)
                                    //->whereBetween('created_at',[$fromdate,$todate])
                                    ->orderBy('created_at')
                                    ->get();

                                $uid = array();
                                foreach ($betlist1 as $key => $value1) {
                                    $ttlAmtb += $value1->bet_amount;
                                    $uid[] = $value1->user_id;
                                }
                                $totttlAmtb += $ttlAmtb;
                                $result_list = array_unique($uid);
                                $cmp = 0;
                                foreach ($result_list as $key => $cmdata) {
                                    $usercm = User::where('id', $cmdata)->first();
                                    if (!empty($usercm->commission)) {
                                        $cmp += $usercm->commission;
                                    }
                                    //echo $cmdata;
                                    //echo "**";

                                }

                                $exposer_bm_a = UserExposureLog::where('bet_type', 'BOOKMAKER')->where('match_id', $matchdata->id)->whereBetween('created_at', [$fromdate, $todate])->whereIn('user_id', $result_list)->whereBetween('created_at', [$fromdate, $todate])->get();
                                if (!empty($exposer_bm_a)) {
                                    foreach ($exposer_bm_a as $key => $exposer_bm) {
                                        $bm_win_type = $exposer_bm['win_type'];
                                        if ($bm_win_type == 'Profit')
                                            $sumAmtb += $exposer_bm->profit;
                                        else
                                            $sumAmtb -= $exposer_bm->loss;
                                    }
                                }
                            }
                        }
                        $html .= '
                        <tr>
                            <td class="white-bg"><img src="' . asset('asset/img/plus-icon.png') . '">
                                <a class="ico_account text-color-blue-light">
                                    ' . $sports->sport_name . ' <i class="fas fa-caret-right text-color-grey"></i> <strong> ' . $matchdata->match_name . ' </strong>
                                </a>
                            </td>';
                        if (!empty($ttlodd)) {
                            $cms = $ttlodd;

                            if ($cms >= 0) {
                                $totcmsG += $cms;
                                $html .= '<td class="white-bg text-color-green">' . round($cms, 2) . ' </td>';
                            } else {
                                $totcmsR += $cms;
                                $html .= '<td class="white-bg text-color-red">' . round(abs($cms), 2) . ' </td>';
                            }
                        } else if ($ttlodd == 0) {
                            $html .= '<td class="white-bg text-color-green">0</td>';
                        } else {
                            $html .= '<td class="white-bg"> -- </td>';
                        }
                        if (!empty($ttlAmto)) {
                            $html .= '<td class="white-bg">' . $ttlAmto . ' </td>';
                        } else if ($ttlAmto == 0) {
                            $html .= '<td class="white-bg">0</td>';
                        } else {
                            $html .= '<td class="white-bg"> -- </td>';
                        }

                        if (!empty($sumAmtb)) {
                            if ($sumAmtb >= 0) {
                                $totsumAmtbG += $sumAmtb;
                                $html .= '<td class="white-bg text-color-green">' . round($sumAmtb, 2) . '</td>';
                            } else {
                                $totsumAmtbR += $sumAmtb;
                                $html .= '<td class="white-bg text-color-red">' . round(abs($sumAmtb), 2) . ' </td>';
                            }
                        } else if ($sumAmtb == 0) {
                            $html .= '<td class="white-bg text-color-green">0</td>';
                        } else {
                            $html .= '<td class="white-bg"> -- </td>';
                        }

                        if (!empty($ttlAmtb)) {
                            $html .= '<td class="white-bg">' . $ttlAmtb . ' </td>';
                        } else if ($ttlAmtb == 0) {
                            $html .= '<td class="white-bg">0</td>';
                        } else {
                            $html .= '<td class="white-bg"> -- </td>';
                        }

                        if (!empty($sumAmt)) {
                            if ($sumAmt >= 0) {
                                $sumAmtG += $sumAmt;
                                $html .= '<td class="white-bg text-color-green">' . round($sumAmt, 2) . ' </td>';
                            } else {
                                $sumAmtR += $sumAmt;
                                $html .= '<td class="white-bg text-color-red">' . round(abs($sumAmt), 2) . ' </td>';
                            }
                        } else if ($sumAmt == 0) {
                            $html .= '<td class="white-bg text-color-green">0</td>';
                        } else {
                            $html .= '<td class="white-bg"> -- </td>';
                        }

                        if (!empty($ttlAmt)) {
                            $html .= '<td class="white-bg">' . $ttlAmt . ' </td>';
                        } else if ($ttlAmt == 0) {
                            $html .= '<td class="white-bg">0</td>';
                        } else {
                            $html .= '<td class="white-bg"> -- </td>';
                        }

                        // if(!empty($ttlodd))
                        // {
                        $ttlnp = $ttlodd + $sumAmtb + $sumAmt;
                        if ($ttlnp > 0) {
                            $ttlnpR += $ttlnp;
                            $html .= '<td class="white-bg text-color-red">' . round($ttlnp, 2) . '</td>';
                        } else {
                            $ttlnpG += $ttlnp;
                            $html .= '<td class="white-bg text-color-green">' . round(abs($ttlnp), 2) . '</td>';
                        }
                        $ttlnps1 = abs($ttlnpG) - abs($ttlnpR);
                        // }
                        /*else if(empty($sumAmto)){
                                $ttlnp=$sumAmtb+$sumAmt;
                                if($ttlnp > 0)
                                {
                                    $html.='<td class="white-bg text-color-red">'.round($ttlnp,2).'</td>';
                                }
                                else{
                                    $html.='<td class="white-bg text-color-green">'.round(abs($ttlnp),2).'</td>';
                                }
                            }
                            else{

                                $html.='<td class="white-bg"> -- </td>';
                            }*/
                        $html .= '
                        </tr>';
                    }
                } else {
                    $html .= 'No Record Found';
                }
            }
        }
        else {
            $loginuser = Auth::user();
            $all_child = UserHirarchy::where('agent_user', $loginuser->id)->first();
            $clist = (explode(",", $all_child->sub_user));

            if ($sport_data != 0) {
                $getresult = MyBets::select('my_bets.match_id', 'my_bets.sportID')
                    ->join('user_exposure_log', 'user_exposure_log.user_id', '=', 'my_bets.user_id')
                    ->where(['my_bets.sportID' => $sport_data, 'my_bets.result_declare' => 1])
                    ->whereIn('my_bets.user_id', $clist)
                    ->where('my_bets.isDeleted', 0)
                    ->whereBetween('user_exposure_log.created_at', [$fromdate, $todate])
                    ->groupBy('my_bets.match_id')
                    ->orderBy('user_exposure_log.created_at', 'Desc')
                    ->get();
            } else {
                $getresult = MyBets::select('my_bets.match_id', 'my_bets.sportID')
                    ->join('user_exposure_log', 'user_exposure_log.user_id', '=', 'my_bets.user_id')
                    ->where('my_bets.result_declare', 1)
                    ->whereIn('my_bets.user_id', $clist)
                    ->where('my_bets.isDeleted', 0)
                    ->whereBetween('user_exposure_log.created_at', [$fromdate, $todate])
                    ->groupBy('my_bets.match_id')
                    ->orderBy('user_exposure_log.created_at', 'Desc')
                    ->get();
                /* echo "<pre>";
                print_r($getresult);
                exit;*/
            }

            if (!empty($getresult)) {
                foreach ($getresult as $data) {
                    $sports = Sport::where('sId', $data->sportID)->first();
                    $matchdata = Match::where('event_id', $data->match_id)->first();

                    $subresult = MyBets::where('match_id', $data->match_id)
                        ->where('isDeleted', 0)
                        //->whereBetween('created_at',[$fromdate,$todate])
                        ->groupBy('bet_type')
                        ->latest()->get();
                    //echo "<pre>";print_r($subresult);

                    $ttlAmto = 0;
                    $sumAmto = 0;
                    $sumAmt = 0;
                    $ttlAmt = 0;
                    $ttlAmtb = 0;
                    $sumAmtb = 0;
                    $sumAmt1 = 0;
                    $sumAmt2 = 0;
                    $ttlodd = 0;

                    foreach ($subresult as $key => $value) {
                        if ($value->bet_type == 'SESSION') {
                            $betlist1 = MyBets::whereIn('user_id', $clist)
                                ->where('result_declare', 1)
                                ->where('isDeleted', 0)
                                ->where('bet_type', 'SESSION')
                                ->groupBy('team_name')
                                ->where('match_id', $data->match_id)
                                //->whereBetween('created_at',[$fromdate,$todate])
                                ->orderBy('created_at')
                                ->get();

                            $betlist2 = MyBets::whereIn('user_id', $clist)
                                ->where('result_declare', 1)
                                ->where('isDeleted', 0)
                                ->where('bet_type', 'SESSION')
                                ->where('match_id', $data->match_id)
                                //->whereBetween('created_at',[$fromdate,$todate])
                                ->orderBy('created_at')
                                ->get();

                            $uid = array();
                            foreach ($betlist2 as $key => $value2) {
                                $ttlAmt += $value2->bet_amount;
                                $uid[] = $value2->user_id;
                            }
                            $ttlAmts += $ttlAmt;
                            $result_list = array_unique($uid);

                            foreach ($betlist1 as $key => $value1) {
                                $fnc_rslt = FancyResult::where('eventid', $data->match_id)->where('fancy_name', $value1->team_name)->first();

                                $f_result = 0;
                                if (!empty($fnc_rslt)) {
                                    $f_result = $fnc_rslt->result;
                                }

                                $exposer_fancy_a = UserExposureLog::where('match_id', $matchdata->id)->whereBetween('created_at', [$fromdate, $todate])->where('bet_type', 'SESSION')->where('fancy_name', $value1->team_name)->whereIn('user_id', $result_list)->get();

                                if (!empty($exposer_fancy_a)) {
                                    foreach ($exposer_fancy_a as $key => $exposer_fancy) {
                                        $fancy_win_type = $exposer_fancy['win_type'];
                                        if ($fancy_win_type == 'Profit') {
                                            $sumAmt = $sumAmt + (int)$exposer_fancy->profit;
                                        } else {
                                            $sumAmt = $sumAmt - (int)$exposer_fancy->loss;
                                        }
                                    }
                                }
                            }
                        } else if ($value->bet_type == 'ODDS') {
                            $betlist1 = MyBets::whereIn('user_id', $clist)
                                ->where('result_declare', 1)
                                ->where('isDeleted', 0)
                                ->where('bet_type', 'ODDS')
                                ->where('match_id', $data->match_id)
                                //->whereBetween('created_at',[$fromdate,$todate])
                                ->orderBy('created_at')
                                ->get();

                            $uid = array();
                            foreach ($betlist1 as $key => $value1) {
                                $ttlAmto += $value1->bet_amount;
                                $uid[] = $value1->user_id;
                            }
                            $totAmto += $ttlAmto;

                            $result_list = array_unique($uid);
                            $cmp = 0;
                            foreach ($result_list as $key => $cmdata) {
                                $usercm = User::where('id', $cmdata)->first();
                                if (!empty($usercm->commission)) {
                                    $cmp = $usercm->commission;
                                }
                                //echo $cmdata;
                                //echo "**";

                            }
                            //echo "<br>";


                            $expodds_a = UserExposureLog::where('match_id', $matchdata->id)->whereIn('user_id', $result_list)->whereBetween('created_at', [$fromdate, $todate])->whereBetween('created_at', [$fromdate, $todate])->where('bet_type', 'ODDS')->get();

                            if (!empty($expodds_a)) {
                                foreach ($expodds_a as $key => $expodds) {
                                    if ($expodds->bet_type == 'ODDS') {
                                        if ($expodds->win_type == 'Profit') {
                                            $sumAmt1 = $expodds->profit;

                                            $ttlcm = ($sumAmt1 * $cmp) / 100;
                                            $cms = $sumAmt1 - $ttlcm;

                                            $ttlodd += $cms;
                                        } else if ($expodds->win_type == 'Loss') {
                                            //$sumAmto-=$expodds->loss;
                                            $sumAmt2 = $expodds->loss;
                                            $ttlodd -= $sumAmt2;
                                        }
                                    }
                                }

                            }
                        } else if ($value->bet_type == 'BOOKMAKER') {
                            $betlist1 = MyBets::whereIn('user_id', $clist)
                                ->where('result_declare', 1)
                                ->where('isDeleted', 0)
                                ->where('bet_type', 'BOOKMAKER')
                                ->where('match_id', $data->match_id)
                                //->whereBetween('created_at',[$fromdate,$todate])
                                ->orderBy('created_at')
                                ->get();

                            $uid = array();
                            foreach ($betlist1 as $key => $value1) {
                                $ttlAmtb += $value1->bet_amount;
                                $uid[] = $value1->user_id;
                            }
                            $totttlAmtb += $ttlAmtb;

                            $result_list = array_unique($uid);
                            $cmp = 0;
                            foreach ($result_list as $key => $cmdata) {
                                $usercm = User::where('id', $cmdata)->first();
                                if (!empty($usercm->commission)) {
                                    $cmp += $usercm->commission;
                                }
                            }

                            $exposer_bm_a = UserExposureLog::where('bet_type', 'BOOKMAKER')->where('match_id', $matchdata->id)->whereBetween('created_at', [$fromdate, $todate])->whereIn('user_id', $result_list)->whereBetween('created_at', [$fromdate, $todate])->get();
                            if (!empty($exposer_bm_a)) {
                                foreach ($exposer_bm_a as $key => $exposer_bm) {
                                    $bm_win_type = $exposer_bm['win_type'];
                                    if ($bm_win_type == 'Profit')
                                        $sumAmtb += $exposer_bm->profit;
                                    else
                                        $sumAmtb -= $exposer_bm->loss;
                                }
                            }
                        }
                    }

                    $html .= '
                    <tr>
                        <td class="white-bg"><img src="' . asset('asset/img/plus-icon.png') . '">
                            <a class="ico_account text-color-blue-light">
                                ' . $sports->sport_name . ' <i class="fas fa-caret-right text-color-grey"></i> <strong> ' . $matchdata->match_name . ' </strong>
                            </a>
                        </td>';
                    if (!empty($ttlodd)) {
                        $cms = $ttlodd;

                        if ($cms >= 0) {
                            $totcmsG += $cms;
                            $html .= '<td class="white-bg text-color-green">' . round($cms, 2) . ' </td>';
                        } else {
                            $totcmsR += $cms;
                            $html .= '<td class="white-bg text-color-red">' . round(abs($cms), 2) . ' </td>';
                        }
                    } else if ($ttlodd == 0) {
                        $html .= '<td class="white-bg text-color-green">0</td>';
                    } else {
                        $html .= '<td class="white-bg"> -- </td>';
                    }
                    if (!empty($ttlAmto)) {
                        $html .= '<td class="white-bg">' . $ttlAmto . ' </td>';
                    } else if ($ttlAmto == 0) {
                        $html .= '<td class="white-bg">0</td>';
                    } else {
                        $html .= '<td class="white-bg"> -- </td>';
                    }

                    if (!empty($sumAmtb)) {
                        if ($sumAmtb >= 0) {
                            $totsumAmtbG += $sumAmtb;
                            $html .= '<td class="white-bg text-color-green">' . round($sumAmtb, 2) . '</td>';
                        } else {
                            $totsumAmtbR += $sumAmtb;
                            $html .= '<td class="white-bg text-color-red">' . round(abs($sumAmtb), 2) . ' </td>';
                        }
                    } else if ($sumAmtb == 0) {
                        $html .= '<td class="white-bg text-color-green">0</td>';
                    } else {
                        $html .= '<td class="white-bg"> -- </td>';
                    }

                    if (!empty($ttlAmtb)) {
                        $html .= '<td class="white-bg">' . $ttlAmtb . ' </td>';
                    } else if ($ttlAmtb == 0) {
                        $html .= '<td class="white-bg">0</td>';
                    } else {
                        $html .= '<td class="white-bg"> -- </td>';
                    }

                    if (!empty($sumAmt)) {
                        if ($sumAmt >= 0) {
                            $sumAmtG += $sumAmt;
                            $html .= '<td class="white-bg text-color-green">' . round($sumAmt, 2) . ' </td>';
                        } else {
                            $sumAmtR += $sumAmt;
                            $html .= '<td class="white-bg text-color-red">' . round(abs($sumAmt), 2) . ' </td>';
                        }
                    } else if ($sumAmt == 0) {
                        $html .= '<td class="white-bg text-color-green">0</td>';
                    } else {
                        $html .= '<td class="white-bg"> -- </td>';
                    }

                    if (!empty($ttlAmt)) {
                        $html .= '<td class="white-bg">' . $ttlAmt . ' </td>';
                    } else if ($ttlAmt == 0) {
                        $html .= '<td class="white-bg">0</td>';
                    } else {
                        $html .= '<td class="white-bg"> -- </td>';
                    }

                    $ttlnp = $ttlodd + $sumAmtb + $sumAmt;

                    if ($ttlnp > 0) {
                        $ttlnpR += $ttlnp;
                        $html .= '<td class="white-bg text-color-red">' . round($ttlnp, 2) . '</td>';
                    } else {
                        $ttlnpG += $ttlnp;
                        $html .= '<td class="white-bg text-color-green">' . round(abs($ttlnp), 2) . '</td>';
                    }
                    $ttlnps1 = abs($ttlnpG) - abs($ttlnpR);

                    $html .= '
                    </tr>';
                }
            } else {
//                $html .= 'No Record Found';
            }
        }

        $casino_entries = UsersAccount::whereIn('user_id', $clist)->where('casino_id', ">", 0)->get();

        if(!empty($casino_entries)){
            foreach ($casino_entries as $statment){
                $casino = Casino::find($statment->casino_id);
                $casinoBet = CasinoBet::find($statment->user_exposure_log_id);
                if(!empty($casinoBet)) {
                    $html .= '<tr>
                    <td class="white-bg"><img src="' . asset('asset/img/plus-icon.png') . '">
                        <a class="ico_account text-color-blue-light">
                            CASINO <i class="fas fa-caret-right text-color-grey"></i> <strong> ' . $casino->casino_title . ' </strong>
                        </a>
                    </td>';

                    if ($statment->credit_amount > 0) {
                        $totcmsG += $statment->credit_amount;
                        $html .= '<td class="white-bg text-color-green">' . round($statment->credit_amount, 2) . ' </td>';
                    } else {
                        $totcmsR -= $statment->debit_amount;
                        $html .= '<td class="white-bg text-color-red">' . round(abs($statment->debit_amount), 2) . ' </td>';
                    }

                    $totAmto += $casinoBet->stake_value;

                    $html .= '<td class="white-bg text-color-red">' . round(abs($casinoBet->stake_value), 2) . ' </td>';
                    $html .= '<td class="white-bg text-color-red">0</td>';
                    $html .= '<td class="white-bg text-color-red">0</td>';
                    $html .= '<td class="white-bg text-color-red">0</td>';
                    $html .= '<td class="white-bg text-color-red">0</td>';

                    if ($statment->credit_amount > 0) {
                        $ttlnpR += $statment->credit_amount;
                        $html .= '<td class="white-bg text-color-green">' . round($statment->credit_amount, 2) . '</td>';
                    } else {
                        $ttlnpG += $statment->debit_amount;
                        $html .= '<td class="white-bg text-color-red">' . round($statment->debit_amount, 2) .'</td>';
                    }
                    $ttlnps1 = abs($ttlnpG) - abs($ttlnpR);

                    $html .= "</tr>";
                }
            }
        }


        $totcms = $totcmsG - abs($totcmsR);
        $totsumAmtb = $totsumAmtbG - abs($totsumAmtbR);
//        $totttlAmt += $ttlAmt;
        $totsumAmt = $sumAmtG - abs($sumAmtR);

        //$totttlnp = $ttlnpG-abs($ttlnpR);
//        $totttlnp += $sumAmtb + $sumAmt;
        if ($totcms < 0) {
            $totcmsClass = 'text-color-red';
        } else {
            $totcmsClass = 'text-color-green';
        }

        if ($totsumAmtb < 0) {
            $totsumAmtbClass = 'text-color-red';
        } else {
            $totsumAmtbClass = 'text-color-green';
        }
        if ($totsumAmt < 0) {
            $totsumAmtClass = 'text-color-red';
        } else {
            $totsumAmtClass = 'text-color-green';
        }


        $ttlnps = $ttlnps1 + ($ttlnps2);


        if ($ttlnps < 0) {
            $totttlnpsClass = 'text-color-red';
        } else {
            $totttlnpsClass = 'text-color-green';
        }


        //$totAmto += $ttlAmto;
        $html1 = '<tr><td>Total</td><td class=' . $totcmsClass . '>' . number_format(abs($totcms), 2) . '</td><td>' . number_format($totAmto, 2) . '</td><td class=' . $totsumAmtbClass . '>' . number_format($totsumAmtb, 2) . '</td><td>' . number_format($totttlAmtb, 2) . '</td><td class=' . $totsumAmtClass . '>' . number_format(abs($totsumAmt), 2) . '</td><td>' . number_format($ttlAmts, 2) . '</td><td class=' . $totttlnpsClass . '>' . number_format(abs($ttlnps), 2) . '</td></tr>';
        return $html . '~~' . $html1;
    }

    public function profitlossdownline(Request $request)
    {
        $loginuser = Auth::user();
        $users = User::where('parentid', $loginuser->id)->latest()->get();
        //echo "<pre>";print_r($users);echo "<pre>"; exit;
        return view('backpanel.profitloss-downline', compact('users'));
    }

    /*public static function GetAllChildofUser($pid)
	{
		$parent=array();
		$subdata = User::where('parentid',$pid)->get();
		//$id=$subdata['parentid'];
		foreach($subdata as $sub)
		{

			if($sub->agent_level!='PL')
			{
				do {
					//$subdata = SELF::GetAllChildofUser($sub->id);
					$subdata = User::where('parentid',$sub->id)->first();
					$id=$subdata['parentid'];
					$parent[]=$id;
				} while ($sub->agent_level!='PL');
			}
			else
			{
				$parent[]=$sub->id;
			}
		}
		return json_encode($parent);
	}*/
    function childdata($id)
    {
        $cat = User::where('parentid', $id)->get();
        $children = array();
        $i = 0;
        foreach ($cat as $key => $cat_value) {
            $children[] = array();
            $children[] = $cat_value->id;
            $new = $this->childdata($cat_value->id);
            $children = array_merge($children, $new);
            $i++;
        }
        $new = array();
        foreach ($children as $child) {
            if (!empty($child))
                $new[] = $child;
        }
        return $new;
    }

    function backdataagent($id)
    {
        $adata = array();
        do {
            $test = User::where('id', $id)->first();
            $adata[] = $test->id;
            $first = User::orderBy('id', 'ASC')->first();
        } while ($id = $test->parentid);
        return $adata;
    }

    function GetChildofAgent($id)
    {
        $cat = User::where('parentid', $id)->get();
        $children = array();
        $i = 0;
        foreach ($cat as $key => $cat_value) {
            $children[] = array();
            $children[] = $cat_value->id;
            $new = $this->GetChildofAgent($cat_value->id);
            $children = array_merge($children, $new);
            $i++;
        }
        $new = array();
        foreach ($children as $child) {
            if (!empty($child))
                $new[] = $child;
        }
        return $new;
    }

    public function getHistoryPL(Request $request)
    {
        $val = $request->val;
        $html = '';
        $html1 = '';
        $totalAmt = 0;

        if ($val == 'today') {
            $date_from = date('Y-m-d') . ' 09:00';
            $date_to = date("Y-m-d", strtotime("+1 day")) . ' 08:59';
        } else if ($val == 'yesterday') {
            $date_from = date("Y-m-d", strtotime("-1 day")) . ' 09:00';
            $date_to = date("Y-m-d") . ' 08:59';
        } else {
            $date_from = date('Y-m-d', strtotime($request->date_from)) . ' 09:00';
            $date_to = date('Y-m-d', strtotime($request->date_to)) . ' 08:59';

        }

        /* echo $date_from;
        echo "/";
        echo $date_to;
        exit;*/
        $loginuser = Auth::user();
        $ag_id = $loginuser->id;
        $all_child = $this->GetChildofAgent($ag_id);

        $users_all_count = User::where('parentid', $loginuser->id)->latest()->count();
        $users_all = User::where('parentid', $loginuser->id)->latest()->get();
        if ($users_all_count != 0) {
            foreach ($users_all as $key => $value) {


                if ($value->agent_level == 'SA') {
                    $color = 'orange-bg';
                } else if ($value->agent_level == 'AD') {
                    $color = 'black-bg';
                } else if ($value->agent_level == 'SMDL') {
                    $color = 'green-bg';
                } else if ($value->agent_level == 'MDL') {
                    $color = 'yellow-bg';
                } else if ($value->agent_level == 'DL') {
                    $color = 'blue-bg';
                } else {
                    $color = 'red-bg';
                }

                if ($date_from != '' && $date_to != '') {
                    $totpamount = 0;
                    $totalLoss = 0;
                    $totalProfit = 0;
                    if ($value->agent_level == 'PL') {
                        $datac = $value->id;
                        $cumulative_pl_profit_get = UserExposureLog::where('user_id', $datac)->where('win_type', 'Profit')->whereBetween('created_at', [$date_from, $date_to])->where('bet_type', 'ODDS')->sum('profit');
                        $cumulative_pl_profit = UserExposureLog::where('user_id', $datac)->where('win_type', 'Profit')->whereBetween('created_at', [$date_from, $date_to])->where('bet_type', '!=', 'ODDS')->sum('profit');
                        $cumulative_pl_loss = UserExposureLog::where('user_id', $datac)->where('win_type', 'Loss')->whereBetween('created_at', [$date_from, $date_to])->sum('loss');
                        $casino_pl_profit = UsersAccount::where('user_id', $datac)->where('casino_id',">",0)->sum('credit_amount');
                        $casino_pl_lose = UsersAccount::where('user_id', $datac)->where('casino_id',">",0)->sum('debit_amount');
                        $cumu_n = 0;
                        $cumu_n = $cumulative_pl_profit_get * ($value->commission) / 100;
                        $cumuPL_n = $cumulative_pl_profit_get + $cumulative_pl_profit + $casino_pl_profit - $cumu_n;
                        $totalProfit += $cumuPL_n - $cumulative_pl_loss - $casino_pl_lose;

                    } else {
                        $x = $value->id;
                        $ans = $this->childdata($x);

                        foreach ($ans as $datac) {

                            $cumulative_pl_profit_get = UserExposureLog::where('user_id', $datac)->where('win_type', 'Profit')->whereBetween('created_at', [$date_from, $date_to])->where('bet_type', 'ODDS')->sum('profit');
                            $cumulative_pl_profit = UserExposureLog::where('user_id', $datac)->where('win_type', 'Profit')->whereBetween('created_at', [$date_from, $date_to])->where('bet_type', '!=', 'ODDS')->sum('profit');
                            $cumulative_pl_loss = UserExposureLog::where('user_id', $datac)->where('win_type', 'Loss')->whereBetween('created_at', [$date_from, $date_to])->sum('loss');
                            $cumu_n = 0;

                            $casino_pl_profit = UsersAccount::where('user_id', $datac)->where('casino_id',">",0)->sum('credit_amount');
                            $casino_pl_lose = UsersAccount::where('user_id', $datac)->where('casino_id',">",0)->sum('debit_amount');

                            $cumu_n = $cumulative_pl_profit_get * ($value->commission) / 100;
                            $cumuPL_n = $cumulative_pl_profit_get + $cumulative_pl_profit + $casino_pl_profit - $cumu_n;
                            $totalProfit += $cumuPL_n - $cumulative_pl_loss - $casino_pl_lose;


                        }
                    }
                    $totpamount = $totalProfit;
                }

                if ($totpamount != 0) {
                    $html .= '
                <tr>
                    <td class="white-bg"><img src="' . asset('asset/img/plus-icon.png') . '">';

                    $html .= '<a class="ico_account text-color-blue-light" id="' . $value->id . '"  onclick="subpagedata(this.id);">
                        <span class="' . $color . ' text-color-white">' . $value->agent_level . '</span>' . $value->user_name . '
                        </a>';

                    $html .= '</td>';
                    if ($totpamount >= 0) {
                        $class = "text-color-green";
                    } else {
                        $class = "text-color-red";
                    }
                    $html .= '<td class=" ' . $class . ' white-bg">' . number_format(abs($totpamount), 2) . '</td>';

                    if ($totpamount >= 0) {
                        $classdown = "text-color-red";
                    } else {
                        $classdown = "text-color-green";
                    }
                    $html .= '<td class="' . $class . ' white-bg">' . number_format(abs($totpamount), 2) . '</td>';
                    $html .= '<td class="white-bg">0.00</td>
                    <td class="white-bg">0.00</td>
                    <td class="white-bg">0.00</td>
                    <td class="white-bg">0.00</td>';
                    $html .= '<td class="' . $classdown . ' white-bg">(' . number_format(abs($totpamount), 2) . ')</td>';
                    $html .= '</tr>';
                    $totalAmt += $totpamount;
                }
            }


            $html1 .= '
            <tr class="table-total">
                <td class="white-bg">Total</td>';
            if ($totalAmt >= 0) {
                $class = "text-color-green";
            } else {
                $class = "text-color-red";
            }
            $html1 .= '<td class="white-bg ' . $class . '">' . number_format(abs($totalAmt), 2) . '</td>';
            if ($totalAmt >= 0) {
                $classdown = "text-color-red";
            } else {
                $classdown = "text-color-green";
            }
            $html1 .= '<td class="white-bg ' . $class . '">' . number_format(abs($totalAmt), 2) . '</td>';
            $html1 .= '<td class="white-bg">0.00</td>
                <td class="white-bg">0.00</td>
                <td class="white-bg">0.00</td>
                <td class="white-bg">0.00</td>';
            $html1 .= '<td class="white-bg ' . $classdown . '">(' . number_format(abs($totalAmt), 2) . ')</td>';
            $html1 .= '</tr>';
        } else {
            $html .= '<tr> <td class="white-bg" collapse=8>No data available</td> </tr>';
            $html1 .= '';
        }


        return $html . '~~' . $html1;

    }
    // wrong calculation method ss comment
    /* public function getHistoryPL(Request $request)
    {
	    $val = $request->val;
        $html=''; $html1='';

        if($val=='today')
        {
            $date_from = date('Y-m-d');
            $date_to = date("Y-m-d", strtotime("+1 day"));
        }
        else if($val=='yesterday')
        {

            $date_from = date("Y-m-d", strtotime("-1 day"));
            $date_to = date("Y-m-d");
        }
        else
        {
            $date_from = $request->date_from;

            $date_to1 = date('d-m-Y',strtotime($request->date_to));
            $date_to = date("Y-m-d", strtotime($date_to1 ."+1 day"));
        }

        $loginuser = Auth::user();
        $users = User::where('parentid', $loginuser->id)->latest()->get();

        $totalAmt=0;

        foreach ($users as $key => $value) {

            if($value->agent_level == 'SA'){
                $color = 'orange-bg';
            }else if($value->agent_level == 'AD'){
                $color = 'black-bg';
            }else if($value->agent_level == 'SMDL'){
                $color = 'green-bg';
            }else if($value->agent_level == 'MDL'){
                $color = 'yellow-bg';
            }else if($value->agent_level == 'DL'){
                $color = 'blue-bg';
            }else{
                $color = 'red-bg';
            }


            if($date_from != '' && $date_to != '')
            {
                $getresult = MyBets::where(['user_id' => $value->id, 'result_declare'=>1])
                ->whereBetween('created_at',[$date_from,$date_to])
                ->get();

                $sumAmt=0;



                if($value->agent_level == 'PL'){

                    foreach ($getresult as $data) {
                        $matchdata = Match::where('event_id', $data->match_id)->latest()->first();
                        $fancydata = FancyResult::where(['eventid' => $data->match_id, 'fancy_name' => $data->team_name])->first();

                        if($data->bet_type == 'ODDS'){

                            if($matchdata->winner == $data->team_name){

                                $sumAmt+=$data->bet_profit;
                            }else{

                                $sumAmt-=$data->exposureAmt;
                            }
                        }

                        if($data->bet_type == 'SESSION'){
                            if(!empty($fancydata)){
                                if($data->bet_side=='back')
                                {
                                    if($data->bet_odds<=$fancydata->result)
                                    {
                                        $sumAmt+=$data->bet_profit;
                                    }
                                    else if($data->bet_odds>$fancydata->result)
                                    {
                                        $sumAmt-=$data->bet_amount;
                                    }
                                }else if($data->bet_side=='lay')
                                {
                                    if($data->bet_odds>$fancydata->result)
                                    {
                                        $sumAmt+=$data->bet_amount;
                                    }
                                    else if($data->bet_odds<=$fancydata->result)
                                    {
                                        $sumAmt-=$data->exposureAmt;
                                    }
                                }
                            }
                        }
                        if($data->bet_type == 'BOOKMAKER'){
                            if($matchdata->winner == $data->team_name){
                                $sumAmt+=$data->bet_profit;
                            }else{
                                $sumAmt-=$data->exposureAmt;
                            }
                        }
                    }

                }
                else
				{
                   $x = $value->id;
                    $ans = $this->childdata($x);
                    $totpamount = 0;
                        $totalLoss = 0;
                        $totalProfit = 0;
                    foreach($ans as $datac)
                    {

                        $getdata = MyBets::where(['user_id' => $datac, 'result_declare'=>1])
                        ->whereBetween('created_at',[$date_from,$date_to])
                        ->get();



                        foreach ($getdata as $data) {
                            $matchdata = Match::where('event_id', $data->match_id)->latest()->first();
                            $fancydata = FancyResult::where(['eventid' => $data->match_id, 'fancy_name' => $data->team_name])->first();

                        $getdata_exposer = UserExposureLog::where(['user_id' => $datac])
                        ->get();

                        foreach ($getdata_exposer as $value) {
                            if($value->win_type=='Loss'){
                                $totalLoss += $value->loss;
                            }
                            if($value->win_type=='Profit'){
                                $totalProfit += $value->profit;
                            }
                        }
                        $totpamount = $totalLoss-$totalProfit;


                            if($data->bet_type == 'ODDS'){
                                if($matchdata->winner == $data->team_name){
                                    $sumAmt+=$data->bet_profit;
                                }else{
                                    $sumAmt-=$data->exposureAmt;
                                }
                            }

                            if($data->bet_type == 'SESSION'){
                                if(!empty($fancydata)){
                                    if($data->bet_side=='back')
                                    {
                                        if($data->bet_odds<=$fancydata->result)
                                        {
                                            $sumAmt+=$data->bet_profit;
                                        }
                                        else if($data->bet_odds>$fancydata->result)
                                        {
                                            $sumAmt-=$data->bet_amount;
                                        }
                                    }else if($data->bet_side=='lay')
                                    {
                                        if($data->bet_odds>$fancydata->result)
                                        {
                                            $sumAmt+=$data->bet_amount;
                                        }
                                        else if($data->bet_odds<=$fancydata->result)
                                        {
                                            $sumAmt-=$data->exposureAmt;
                                        }
                                    }
                                }
                            }

                            if($data->bet_type == 'BOOKMAKER'){

                                if($matchdata->winner == $data->team_name){
                                    $sumAmt+=$data->bet_profit;
                                }else{
                                    $sumAmt-=$data->exposureAmt;
                                }
                            }
                        }

                    }
                }
            }
            else
			{

                $getresult = MyBets::where(['user_id' => $value->id, 'result_declare'=>1])
                ->get();

                $sumAmt=0;

                if($value->agent_level == 'PL'){



                    foreach ($getresult as $data) {
                        $matchdata = Match::where('event_id', $data->match_id)->latest()->first();
                        $fancydata = FancyResult::where(['eventid' => $data->match_id, 'fancy_name' => $data->team_name])->first();

                        if($data->bet_type == 'ODDS'){

                            if($matchdata->winner == $data->team_name){

                                $sumAmt+=$data->bet_profit;
                            }else{

                                $sumAmt-=$data->exposureAmt;
                            }
                        }

                        if($data->bet_type == 'SESSION'){
                            if(!empty($fancydata)){
                                if($data->bet_side=='back')
                                {
                                    if($data->bet_odds<=$fancydata->result)
                                    {
                                        $sumAmt+=$data->bet_profit;
                                    }
                                    else if($data->bet_odds>$fancydata->result)
                                    {
                                        $sumAmt-=$data->bet_amount;
                                    }
                                }else if($data->bet_side=='lay')
                                {
                                    if($data->bet_odds>$fancydata->result)
                                    {
                                        $sumAmt+=$data->bet_amount;
                                    }
                                    else if($data->bet_odds<=$fancydata->result)
                                    {
                                        $sumAmt-=$data->exposureAmt;
                                    }
                                }

                            }
                        }

                        if($data->bet_type == 'BOOKMAKER'){

                            if($matchdata->winner == $data->team_name){
                                $sumAmt+=$data->bet_profit;
                            }else{
                                $sumAmt-=$data->exposureAmt;
                            }
                        }
                    }

                }
                else
                {
                    $x = $value->id;
                    $ans = $this->childdata($x);
                     $totpamount = 0;
                        $totalLoss = 0;
                        $totalProfit = 0;

                    foreach($ans as $datac)
                    {

                        $getdata = MyBets::where(['user_id' => $datac, 'result_declare'=>1])
                        ->get();



                        foreach ($getdata as $data) {
                            $matchdata = Match::where('event_id', $data->match_id)->latest()->first();
                            $fancydata = FancyResult::where(['eventid' => $data->match_id, 'fancy_name' => $data->team_name])->first();
                              $getdata_exposer = UserExposureLog::where(['user_id' => $datac])->get();
                            foreach ($getdata_exposer as $value) {
                            if($value->win_type=='Loss'){
                                $totalLoss += $value->loss;
                            }
                            if($value->win_type=='Profit'){
                                $totalProfit += $value->profit;
                            }
                        }
                        $totpamount = $totalLoss-$totalProfit;

                            if($data->bet_type == 'ODDS'){

                                if($matchdata->winner == $data->team_name){

                                    $sumAmt+=$data->bet_profit;
                                }else{

                                    $sumAmt-=$data->exposureAmt;
                                }
                            }


                            if($data->bet_type == 'SESSION'){

                                if(!empty($fancydata)){

                                    if($data->bet_side=='back')
                                    {
                                        if($data->bet_odds<=$fancydata->result)
                                        {
                                            $sumAmt+=$data->bet_profit;
                                        }
                                        else if($data->bet_odds>$fancydata->result)
                                        {
                                            $sumAmt-=$data->bet_amount;
                                        }
                                    }else if($data->bet_side=='lay')
                                    {
                                        if($data->bet_odds>$fancydata->result)
                                        {
                                            $sumAmt+=$data->bet_amount;
                                        }
                                        else if($data->bet_odds<=$fancydata->result)
                                        {
                                            $sumAmt-=$data->exposureAmt;
                                        }
                                    }
                                }
                            }

                            if($data->bet_type == 'BOOKMAKER'){

                                if($matchdata->winner == $data->team_name){
                                    $sumAmt+=$data->bet_profit;
                                }else{
                                    $sumAmt-=$data->exposureAmt;
                                }
                            }
                        }

                    }
                }
            }

            $totalAmt+=$sumAmt;
            $html.='
                <tr>
                    <td class="white-bg"><img src="'.asset('asset/img/plus-icon.png').'">';
                        if($value->agent_level == 'PL'){
                            $html.='<a class="ico_account text-color-blue-light" id="'.$value->id.'">
                            <span class="'.$color.' text-color-white">'.$value->agent_level.'</span>'.$value->user_name.'
                            </a>';
                        }else{
                            $html.='<a class="ico_account text-color-blue-light" id="'.$value->id.'"  onclick="subpagedata(this.id);">
                            <span class="'.$color.' text-color-white">'.$value->agent_level.'</span>'.$value->user_name.'
                            </a>';
                        }

                    $html.='</td>';

                    if($sumAmt >=0){
                        $html.='<td class="text-color-green white-bg">'.$sumAmt.'</td>';
                    }else{
                        $html.='<td class="text-color-red white-bg">'.$sumAmt.'</td>';
                    }

                    if($sumAmt >=0){
                        $html.='<td class="text-color-green white-bg">'.$sumAmt.'</td>';
                    }else{
                        $html.='<td class="text-color-red white-bg">'.$sumAmt.'</td>';
                    }

                    $html.='<td class="white-bg">0.00</td>
                    <td class="white-bg">0.00</td>
                    <td class="white-bg">0.00</td>
                    <td class="white-bg">0.00</td>';

                    if($sumAmt >=0){
                        $html.='<td class="text-color-green white-bg">('.$sumAmt.')</td>';
                    }else{
                        $html.='<td class="text-color-red white-bg">('.$sumAmt.')</td>';
                    }

                $html.='</tr>
            ';
        }

        $html1.='
            <tr class="table-total">
                <td class="white-bg">Total</td>';
                if($totalAmt >= 0){
                    $html1.='<td class="text-color-green white-bg">'.$totalAmt.'</td>';
                }else{
                    $html1.='<td class="text-color-red white-bg">'.$totalAmt.'</td>';
                }

                if($totalAmt >= 0){
                    $html1.='<td class="text-color-green white-bg">'.$totalAmt.'</td>';
                }else{
                    $html1.='<td class="text-color-red white-bg">'.$totalAmt.'</td>';
                }

                $html1.='<td class="white-bg">0.00</td>
                <td class="white-bg">0.00</td>
                <td class="white-bg">0.00</td>
                <td class="white-bg">0.00</td>';

                if($totalAmt >= 0){
                    $html1.='<td class="text-color-green white-bg">('.$totalAmt.')</td>';
                }else{
                    $html1.='<td class="text-color-red white-bg">('.$totalAmt.')</td>';
                }

            $html1.='</tr>
        ';
        return $html.'~~'.$html1;

    }*/
    public function SubBackDetail(Request $request)
    {
        $user_id = $request->user_id;
        $date_from = date('Y-m-d', strtotime($request->date_from));
        $date_to = date('Y-m-d', strtotime($request->date_to));

        $crumb = User::where('id', $user_id)->first();
        $adata = $this->backdata($crumb->parentid);
        sort($adata);

        $html = '';
        $html1 = '';
        $html2 = '';
        $html .= '';
        $html1 .= '';
        $totalAmt = 0;
        foreach ($adata as $bread) {
            $finaldata = User::where('id', $bread)->first();
            $html1 .= '
            <li class="firstli" id=' . $finaldata['id'] . '>';
            if ($finaldata['agent_level'] == 'COM') {
                $html1 .= '
                    <a href="profitloss-downline">
                    <span class="blue-bg text-color-white">' . $finaldata->agent_level . '</span>
                    <strong id=' . $finaldata->id . '>' . $finaldata->first_name . '</strong>
                    </a>
                    <img src="' . asset('asset/img/arrow-right2.png') . '">
                </li>';
            } else {
                $html1 .= '
                <a>
                    <span class="blue-bg text-color-white">' . $finaldata->agent_level . '</span>
                    <strong id=' . $finaldata->id . '  onclick="backpagedata(this.id);">' . $finaldata->first_name . '</strong>
                </a>
                <img src="' . asset('asset/img/arrow-right2.png') . '">
            </li>';
            }
        }

        $user = User::where('parentid', $user_id)->get();
        $admin = Auth::user()->id;

        foreach ($user as $key => $row) {
            if ($row->agent_level == 'SA') {
                $color = 'orange-bg';
            } else if ($row->agent_level == 'AD') {
                $color = 'black-bg';
            } else if ($row->agent_level == 'SMDL') {
                $color = 'green-bg';
            } else if ($row->agent_level == 'MDL') {
                $color = 'yellow-bg';
            } else if ($row->agent_level == 'DL') {
                $color = 'blue-bg';
            } else {
                $color = 'red-bg';
            }

            if ($date_from != '' && $date_to != '') {
                $sumAmt = 0;
                $x = $row->id;

                $totpamount = 0;
                $totalLoss = 0;
                $totalProfit = 0;
                if ($row->agent_level == 'PL') {
                    $datac = $row->id;
                    $cumulative_pl_profit_get = UserExposureLog::where('user_id', $datac)->where('win_type', 'Profit')->whereBetween('created_at', [$date_from, $date_to])->where('bet_type', 'ODDS')->sum('profit');
                    $cumulative_pl_profit = UserExposureLog::where('user_id', $datac)->where('win_type', 'Profit')->whereBetween('created_at', [$date_from, $date_to])->where('bet_type', '!=', 'ODDS')->sum('profit');
                    $cumulative_pl_loss = UserExposureLog::where('user_id', $datac)->where('win_type', 'Loss')->whereBetween('created_at', [$date_from, $date_to])->sum('loss');
                    $cumu_n = 0;
                    $cumu_n = $cumulative_pl_profit_get * ($row->commission) / 100;
                    $cumuPL_n = $cumulative_pl_profit_get + $cumulative_pl_profit - $cumu_n;
                    $totalProfit += $cumuPL_n - $cumulative_pl_loss;


                } else {
                    $ans = $this->childdata($x);
                    foreach ($ans as $datac) {
                        $cumulative_pl_profit_get = UserExposureLog::where('user_id', $datac)->where('win_type', 'Profit')->whereBetween('created_at', [$date_from, $date_to])->where('bet_type', 'ODDS')->sum('profit');
                        $cumulative_pl_profit = UserExposureLog::where('user_id', $datac)->where('win_type', 'Profit')->whereBetween('created_at', [$date_from, $date_to])->where('bet_type', '!=', 'ODDS')->sum('profit');
                        $cumulative_pl_loss = UserExposureLog::where('user_id', $datac)->where('win_type', 'Loss')->whereBetween('created_at', [$date_from, $date_to])->sum('loss');
                        $cumu_n = 0;
                        $cumu_n = $cumulative_pl_profit_get * ($row->commission) / 100;
                        $cumuPL_n = $cumulative_pl_profit_get + $cumulative_pl_profit - $cumu_n;
                        $totalProfit += $cumuPL_n - $cumulative_pl_loss;
                    }
                }
                $totpamount = $totalProfit;
            }
            /* else{
                $sumAmt=0;
                    $x = $row->id;
                    $ans = $this->childdata($x);

                    $totpamount = 0;
                    $totalLoss = 0;
                    $totalProfit = 0;
                if(!empty($ans)){
                    foreach($ans as $datac)
                    {
                        $getdata_exposer = UserExposureLog::where(['user_id' => $datac])->get();
                            foreach ($getdata_exposer as $value) {
                            if($value->win_type=='Loss'){
                                $totalLoss += $value->loss;
                            }
                            if($value->win_type=='Profit'){
                                $totalProfit += $value->profit;
                            }
                        }
                    }
                }else{

                        $getdata_exposer = UserExposureLog::where(['user_id' => $row->id])->get();
                            foreach ($getdata_exposer as $value) {
                            if($value->win_type=='Loss'){
                                $totalLoss += $value->loss;
                            }
                            if($value->win_type=='Profit'){
                                $totalProfit += $value->profit;
                            }
                        }

                }

                $totpamount = $totalProfit-$totalLoss;

            }*/
            if ($totpamount != 0) {
                $html .= '
                <tr>
                    <td class="white-bg"><img src="' . asset('asset/img/plus-icon.png') . '">';
                $html .= '<a class="ico_account text-color-blue-light" id="' . $row->id . '"  onclick="subpagedata(this.id);">
                            <span class="' . $color . ' text-color-white">' . $row->agent_level . '</span>' . $row->user_name . '
                            </a>';


                $html .= '</td>';
                if ($totpamount >= 0) {
                    $class = "text-color-green";
                } else {
                    $class = "text-color-red";
                }
                $html .= '<td class="' . $class . ' white-bg">' . number_format(abs($totpamount), 2) . '</td>';

                if ($totpamount >= 0) {
                    $classdown = "text-color-red";
                } else {
                    $classdown = "text-color-green";
                }
                $html .= '<td class="' . $classdown . ' white-bg">' . number_format(abs($totpamount), 2) . '</td>';
                $html .= '<td class="white-bg">0.00</td>
                    <td class="white-bg">0.00</td>
                    <td class="white-bg">0.00</td>
                    <td class="white-bg">0.00</td>';

                $html .= '<td class="' . $classdown . ' white-bg">(' . number_format(abs($totpamount), 2) . ')</td>';


                $html .= '</tr>
            ';
                $totalAmt += $totpamount;
            }
        }

        $html2 .= '
            <tr class="table-total">
                <td class="white-bg">Total</td>';
        if ($totpamount >= 0) {
            $classdown = "text-color-green";
        } else {
            $classdown = "text-color-red";
        }
        $html2 .= '<td class="white-bg ' . $classdown . '">' . number_format(abs($totalAmt), 2) . '</td>';
        if ($totpamount >= 0) {
            $classdown = "text-color-red";
        } else {
            $classdown = "text-color-green";
        }
        $html2 .= '<td class="white-bg ' . $classdown . '">' . number_format(abs($totalAmt), 2) . '</td>';
        $html2 .= '<td class="white-bg">0.00</td>
                <td class="white-bg">0.00</td>
                <td class="white-bg">0.00</td>
                <td class="white-bg">0.00</td>';
        $html2 .= '<td class="white-bg ' . $classdown . '">(' . number_format(abs($totalAmt), 2) . ')</td>';
        $html2 .= '</tr>';
        return $html . '~~' . $html1 . '~~' . $html2;
    }

    function getAllChild($id)
    {
        global $children;
        $subdata = User::where('parentid', $id)->get();
        $count = count($subdata);
        if ($count > 0) {
            foreach ($subdata as $key => $value) {
                $children[$value->id] = databackend($value->id);
            }
        }
        return $children;
    }

    public function SubDetail(Request $request)
    {
        $user_id = $request->user_id;
        $date_from = date('Y-m-d', strtotime($request->date_from));
        $date_to = date('Y-m-d', strtotime($request->date_to));

        $crumb = User::where('id', $user_id)->first();
        $user = User::where('parentid', $user_id)->get();
        $admin = Auth::user()->id;

        $html = '';
        $html1 = '';
        $html2 = '';
        $html .= '';
        $html1 .= '';

        $totalAmt = 0;
        foreach ($user as $key => $row) {
            if ($row->agent_level == 'SA') {
                $color = 'orange-bg';
            } else if ($row->agent_level == 'AD') {
                $color = 'black-bg';
            } else if ($row->agent_level == 'SMDL') {
                $color = 'green-bg';
            } else if ($row->agent_level == 'MDL') {
                $color = 'yellow-bg';
            } else if ($row->agent_level == 'DL') {
                $color = 'blue-bg';
            } else {
                $color = 'red-bg';
            }

            if ($date_from != '' && $date_to != '') {
                $sumAmt = 0;
                $x = $row->id;

                $totpamount = 0;
                $totalLoss = 0;
                $totalProfit = 0;
                if ($row->agent_level == 'PL') {
                    $datac = $row->id;
                    $cumulative_pl_profit_get = UserExposureLog::where('user_id', $datac)->where('win_type', 'Profit')->whereBetween('created_at', [$date_from, $date_to])->where('bet_type', 'ODDS')->sum('profit');
                    $cumulative_pl_profit = UserExposureLog::where('user_id', $datac)->where('win_type', 'Profit')->whereBetween('created_at', [$date_from, $date_to])->where('bet_type', '!=', 'ODDS')->sum('profit');
                    $cumulative_pl_loss = UserExposureLog::where('user_id', $datac)->where('win_type', 'Loss')->whereBetween('created_at', [$date_from, $date_to])->sum('loss');
                    $cumu_n = 0;
                    $cumu_n = $cumulative_pl_profit_get * ($row->commission) / 100;
                    $cumuPL_n = $cumulative_pl_profit_get + $cumulative_pl_profit - $cumu_n;
                    $totalProfit += $cumuPL_n - $cumulative_pl_loss;

                } else {


                    $ans = $this->childdata($x);
                    foreach ($ans as $datac) {
                        $cumulative_pl_profit_get = UserExposureLog::where('user_id', $datac)->where('win_type', 'Profit')->whereBetween('created_at', [$date_from, $date_to])->where('bet_type', 'ODDS')->sum('profit');
                        $cumulative_pl_profit = UserExposureLog::where('user_id', $datac)->where('win_type', 'Profit')->whereBetween('created_at', [$date_from, $date_to])->where('bet_type', '!=', 'ODDS')->sum('profit');
                        $cumulative_pl_loss = UserExposureLog::where('user_id', $datac)->where('win_type', 'Loss')->whereBetween('created_at', [$date_from, $date_to])->sum('loss');
                        $cumu_n = 0;
                        $cumu_n = $cumulative_pl_profit_get * ($row->commission) / 100;
                        $cumuPL_n = $cumulative_pl_profit_get + $cumulative_pl_profit - $cumu_n;
                        $totalProfit += $cumuPL_n - $cumulative_pl_loss;

                    }
                }

                $totpamount = $totalProfit;
                // $totpamountCom = abs($totpamount)+($totalProfit*$row->commission/100);
            }
            /* else{

                    $sumAmt=0;
                    $x = $row->id;
                    $ans = $this->childdata($x);

                    $totpamount = 0;
                    $totpamountCom=0;
                    $totalLoss = 0;
                    $totalProfit = 0;
                if(!empty($ans)){
                    foreach($ans as $datac)
                    {
                        $getdata_exposer = UserExposureLog::where(['user_id' => $datac])->get();
                            foreach ($getdata_exposer as $value) {
                            if($value->win_type=='Loss'){
                                $totalLoss += $value->loss;
                            }
                            if($value->win_type=='Profit'){
                                $totalProfit += $value->profit;
                            }
                        }
                    }
                }else{

                        $getdata_exposer = UserExposureLog::where(['user_id' => $row->id])->get();
                            foreach ($getdata_exposer as $value) {
                            if($value->win_type=='Loss'){
                                $totalLoss += $value->loss;
                            }
                            if($value->win_type=='Profit'){
                                $totalProfit += $value->profit;
                            }
                        }

                }

                $totpamount = $totalProfit-$totalLoss;
                $totpamountCom = abs($totpamount)+($totalProfit*$row->commission/100);
            }*/
            if ($totpamount != 0) {
                $html .= '
                <tr>
                <td class="white-bg"><img src="' . asset('asset/img/plus-icon.png') . '">';
                $html .= '<a class="ico_account text-color-blue-light" id="' . $row->id . '"  onclick="subpagedata(this.id);">
                    <span class="' . $color . ' text-color-white">' . $row->agent_level . '</span>' . $row->user_name . '
                    </a>';
                $html .= '</td>';

                if ($totpamount >= 0) {
                    $class = "text-color-green";
                } else {
                    $class = "text-color-red";
                }
                $html .= '<td class=" ' . $class . ' white-bg">' . number_format(abs($totpamount), 2) . '</td>';

                if ($totpamount >= 0) {
                    $classdown = "text-color-red";
                } else {
                    $classdown = "text-color-green";
                }
                $html .= '<td class="' . $classdown . ' white-bg">' . number_format(abs($totpamount), 2) . '</td>';

                $html .= '<td class="white-bg">0.00</td>
                    <td class="white-bg">0.00</td>
                    <td class="white-bg">0.00</td>
                    <td class="white-bg">0.00</td>';
                $html .= '<td class="' . $classdown . ' white-bg">(' . number_format(abs($totpamount), 2) . ')</td>';

                $html .= '</tr>
            ';
                $totalAmt += $totpamount;
            }
        }
        $html1 .= '
            <li class="firstli" id=' . $crumb->id . '><a ><span class="blue-bg text-color-white">' . $crumb->agent_level . '</span><strong id=' . $crumb->id . ' onclick="backpagedata(this.id);">' . $crumb->user_name . '</strong></a> <img src="' . asset('asset/img/arrow-right2.png') . '"> </li>';

        $html2 .= '
            <tr class="table-total">
                <td class="white-bg">Total</td>';
        if ($totalAmt >= 0) {
            $classdown = "text-color-green";
        } else {
            $classdown = "text-color-red";
        }
        $html2 .= '<td class="white-bg ' . $classdown . '">' . number_format(abs($totalAmt), 2) . '</td>';
        if ($totalAmt >= 0) {
            $classdown = "text-color-red";
        } else {
            $classdown = "text-color-green";
        }
        $html2 .= '<td class=" white-bg ' . $classdown . '">' . number_format(abs($totalAmt), 2) . '</td>';
        $html2 .= '<td class="white-bg">0.00</td>
                <td class="white-bg">0.00</td>
                <td class="white-bg">0.00</td>
                <td class="white-bg">0.00</td>';
        $html2 .= '<td class="white-bg ' . $classdown . '">(' . number_format(abs($totalAmt), 2) . ')</td>';

        $html2 .= '</tr>
        ';

        return $html . '~~' . $html1 . '~~' . $html2;
    }

    function backdata($id)
    {
        $adata = array();
        do {
            $test = User::where('id', $id)->first();
            $adata[] = $test->id;
            $first = User::orderBy('id', 'ASC')->first();
        } while ($id = $test->parentid);
        return $adata;
    }

    public function betHistoryBack($id)
    {

        $getresult = MyBets::where('user_id', $id)->latest()->get();

        $user = User::where('id', $id)->first();
        return view('backpanel.downline-myaccount-history', compact('getresult', 'id', 'user'));
    }

    public function activityLog($id)
    {
        $user = User::find($id);
        return view('backpanel.downline-activityLog', compact('user', 'id'));
    }

    public function transactionHistory($id)
    {
        $getUserCheck = User::find($id);

        $user = User::find($id);
        if (!empty($getUserCheck)) {
            $loginuser = User::where('id', $getUserCheck->id)->first();
        }
        //echo $loginuser; exit;
        if ($id == 1) {
            $credit = UserDeposit::where(['parent_id' => $id])
                ->latest()
                ->get();

            $player_balance = CreditReference::get();
            foreach ($player_balance as $key => $value) {
                $player_balance = $value['remain_bal'];
            }
        } else {

            $credit = UserDeposit::where(['child_id' => $loginuser->id, 'parent_id' => $loginuser->parentid])
                ->latest()
                ->get();

            $player_balance = CreditReference::where('player_id', $loginuser->id)->first();
            $player_balance = $player_balance['remain_bal'];
        }

        return view('backpanel.transactionHistory', compact('user', 'id', 'loginuser', 'credit', 'player_balance'));
    }

    public function getBetHistoryPL(Request $request)
    {
        $val = $request->val;
        $pid = $request->pid;

        $loginUser = User::where('id', $pid)->first();
        if ($val == 'today') {
            $date_from = date('Y-m-d');
            $date_to = date("Y-m-d", strtotime("+1 day"));
        } else if ($val == 'yesterday') {
            $date_from = date("Y-m-d", strtotime("-1 day"));
            $date_to = date("Y-m-d");
        } else {
            $date_from = date("Y-m-d", strtotime($request->date_from));
            $date_to = date("Y-m-d", strtotime($request->date_to));
        }

        //echo $date_from; echo"/"; echo $date_to; exit;

        if ($date_from != '' && $date_to != '') {
        } else {
            $date_from = date("Y-m-d", strtotime("-30 day"));
            $date_to = date("Y-m-d");
        }

        $getresult = MyBets::where(['user_id' => $pid, 'result_declare' => 1])->whereBetween('created_at', [$date_from, $date_to])->latest()->get();
        $casinoBets = CasinoBet::where(['user_id' => $pid])->whereNotNull('winner')->whereBetween('created_at', [$date_from, $date_to])->latest()->get();


        $html = '';
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
                    <td class="text-right">' . $data->team_name . ' </td>';
            if ($data->bet_type == 'SESSION') {
                if ($data->bet_side == 'lay') {
                    $html .= '<td class="text-right" style="color: #e33a5e !important;text-transform: uppercase;">No</td>';
                } else {
                    $html .= '<td class="text-right" style="color: #1f72ac !important;text-transform: uppercase;">Yes</td>';
                }
            } else {
                if ($data->bet_side == 'lay') {
                    $html .= '<td class="text-right" style="color: #e33a5e !important;text-transform: uppercase;">' . $data->bet_side . '</td>';
                } else {
                    $html .= '<td class="text-right" style="color: #1f72ac !important;text-transform: uppercase;">' . $data->bet_side . '</td>';
                }
            }

            $html .= '
                    <td class="text-right"> <span class="smtxt"> ' . $data->created_at . '</span> </td>
                    <td class="text-right">' . $data->bet_amount . '</td>
                    <td class="text-right">' . $data->bet_odds . '</td>';
            if ($data->bet_type == 'ODDS') {

                if (strtolower($matchdata->winner) == strtolower($data->team_name) && $data->bet_side == 'back') {
                    $html .= '<td class="text-color-green text-right">(' . $data->bet_profit . ')</td>';
                } else if (strtolower($matchdata->winner) != strtolower($data->team_name) && $data->bet_side == 'back') {
                    $html .= '<td class="text-color-red text-right">(' . $data->exposureAmt . ')</td>';
                } else if (strtolower($matchdata->winner) == strtolower($data->team_name) && $data->bet_side == 'lay') {
                    $html .= '<td class="text-color-red text-right">(' . $data->exposureAmt . ')</td>';
                } else if (strtolower($matchdata->winner) != strtolower($data->team_name) && $data->bet_side == 'lay') {
                    $html .= '<td class="text-color-green text-right">(' . $data->bet_profit . ')</td>';
                }
            }
            if ($data->bet_type == 'SESSION') {

                if (!empty($fancydata)) {

                    if($fancydata->result == 'cancel'){
                        $html .= '<td class="text-color-red text-right">0</td>';
                    }else {

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
            }
            if ($data->bet_type == 'BOOKMAKER') {
                if (strtolower($matchdata->winner) == strtolower($data->team_name) && $data->bet_side == 'back') {
                    $html .= '<td class="text-color-green text-right">(' . $data->bet_profit . ')</td>';
                } else if (strtolower($matchdata->winner) != strtolower($data->team_name) && $data->bet_side == 'back') {
                    $html .= '<td class="text-color-red text-right">(' . $data->exposureAmt . ')</td>';
                } else if (strtolower($matchdata->winner) == strtolower($data->team_name) && $data->bet_side == 'lay') {
                    $html .= '<td class="text-color-red text-right">(' . $data->exposureAmt . ')</td>';
                } else if (strtolower($matchdata->winner) != strtolower($data->team_name) && $data->bet_side == 'lay') {
                    $html .= '<td class="text-color-green text-right">(' . $data->bet_profit . ')</td>';
                }
            }

            $html .= '</tr>';
        }

        foreach ($casinoBets as $bet) {

            $casino = Casino::where('casino_name', $bet->casino_name)->first();
            if (!empty($casino)) {
                $html .= '
                <tr class="white-bg">
                    <td class="white-bg"><img src="">
                        <a class="text-color-blue-light">' . $bet->id . '</a>
                    </td>
                    <td>' . $loginUser->user_name . '</td>
                    <td>CASINO<i class="fas fa-caret-right text-color-grey"></i> <b> ' . $casino->casino_title . ' </b> <i class="fas fa-caret-right text-color-grey"></i> ODDS </td>
                    <td class="text-right">' . $bet->team_name . ' </td>';

                if ($bet->bet_side == 'lay') {
                    $html .= '<td class="text-right" style="color: #e33a5e !important;text-transform: uppercase;">' . $bet->bet_side . '</td>';
                } else {
                    $html .= '<td class="text-right" style="color: #1f72ac !important;text-transform: uppercase;">' . $bet->bet_side . '</td>';
                }
                $html .= '
                    <td class="text-right"> <span class="smtxt"> ' . $bet->created_at . '</span> </td>
                    <td class="text-right">' . $bet->stake_value . '</td>
                    <td class="text-right">' . $bet->odds_value . '</td>';


                if ($bet->winner == $bet->team_name && $bet->bet_side == 'back') {
                    $html .= '<td class="text-color-green text-right">(' . $bet->casino_profit . ')</td>';
                } else if ($bet->winner != $bet->team_name && $bet->bet_side == 'back') {
                    $html .= '<td class="text-color-red text-right">(' . $bet->exposureAmt . ')</td>';
                } else if ($bet->winner == $bet->team_name && $bet->bet_side == 'lay') {
                    $html .= '<td class="text-color-red text-right">(' . $bet->exposureAmt . ')</td>';
                } else if ($bet->winner != $bet->team_name && $bet->bet_side == 'lay') {
                    $html .= '<td class="text-color-green text-right">(' . $bet->casino_profit . ')</td>';
                }


                $html .= "</tr>";
            }
        }

        return $html;
    }

    public function betHistoryPLBack($id)
    {
        $getresult = MyBets::where('user_id', $id)->latest()->get();
        $user = User::where('id', $id)->first();
        return view('backpanel.downline-myaccount-profitloss', compact('getresult', 'id', 'user'));
    }

    public function getBetHistoryPLBack(Request $request)
    {
        $val = $request->val;
        $pid = $request->pid;

        $sport = $request->sport;
        $loginUser = User::where('id', $pid)->first();
        if ($val == 'today') {
            $date_from = date('Y-m-d');
            $date_to = date("Y-m-d", strtotime("+1 day"));
        } else if ($val == 'yesterday') {
            $date_from = date("Y-m-d", strtotime("-1 day"));
            $date_to = date("Y-m-d");
        } else {
            $date_from = $request->date_from;
            $date_to = date("Y-m-d", strtotime("+1 day"));
        }

        if ($date_from != '' && $date_to != '') {
            $getresult = MyBets::where(['user_id' => $pid, 'result_declare' => 1])
                ->whereBetween('created_at', [$date_from, $date_to])
                ->groupBy('match_id')
                ->latest()->get();

            if ($sport != 0) {
                $getresult = MyBets::where(['user_id' => $pid, 'result_declare' => 1, 'sportID' => $sport])
                    ->whereBetween('created_at', [$date_from, $date_to])
                    ->groupBy('match_id')
                    ->latest()->get();
            }
        } else {
            if ($sport == 0) {
                $fromdate = date("Y-m-d", strtotime("-60 day"));
                $todate = date("Y-m-d");

                $getresult = MyBets::where(['user_id' => $pid, 'result_declare' => 1])
                    ->whereBetween('created_at', [$date_from, $date_to])
                    ->groupBy('match_id')
                    ->latest()->get();
            } else {
                $fromdate = date("Y-m-d", strtotime("-60 day"));
                $todate = date("Y-m-d");

                $getresult = MyBets::where(['user_id' => $pid, 'result_declare' => 1, 'sportID' => $sport])
                    ->whereBetween('created_at', [$date_from, $date_to])
                    ->groupBy('match_id')
                    ->latest()->get();
            }
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

            $subresult = MyBets::where('match_id', $data->match_id)
                ->whereBetween('created_at', [$date_from, $date_to])
                ->latest()->get();

            $sumAmt = 0;
            $totalAmt = 0;
            $totalPr = 0;

            $loginUser = User::where('id', $pid)->first();

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

                    if (!empty($fancydata)) {

                        /*if($subd1->bet_side=='back')
                        {
                            if($subd1->bet_odds<=$fancydata->result)
                            {
                                $sumAmt+=$subd1->bet_profit;
                            }
                            else if($subd1->bet_odds>$fancydata->result)
                            {
                                $sumAmt-=$subd1->bet_amount;
                            }
                        }else if($subd1->bet_side=='lay')
                        {
                            if($subd1->bet_odds>$fancydata->result)
                            {
                                $sumAmt+=$subd1->bet_amount;
                            }
                            else if($subd1->bet_odds<=$fancydata->result)
                            {
                                $sumAmt-=$subd1->exposureAmt;
                            }
                        }*/

                        $exposer_fancy = UserExposureLog::where('match_id', $matchdata->id)->where('bet_type', 'SESSION')->where('fancy_name', $subd1->team_name)->where('user_id', $loginUser->id)->first();
                        if (!empty($exposer_fancy)) {
                            $fancy_win_type = $exposer_fancy['win_type'];
                            if ($fancy_win_type == 'Profit')
                                $sumAmt = $sumAmt + $exposer_fancy->profit;
                            else
                                $sumAmt = $sumAmt - $exposer_fancy->loss;
                        }
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

            $html .= '

            <tr class="white-bg">
                <td>' . $sports->sport_name . ' <i class="fas fa-caret-right text-color-grey"></i> <b> ' . $matchdata->match_name . ' </b> </td>

                <td class="text-right">' . $matchdata->match_date . '</td>
                <td class="text-right">' . $matchdata->created_at . '</td>
               <td class="text-right"><a href="#collapse' . $i . '" data-toggle="collapse" aria-expanded="false" class="text-color-black">' . $totalAmt . '<img src="' . asset('asset/img/plus-icon.png') . '"></a> </td>
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
                    $html .= '<td class="text-color-red" style="text-transform: uppercase;"><span>' . $subd->bet_side . '</span></td>';
                } else {
                    $html .= '<td class="text-color-blue-light" style="text-transform: uppercase;"><span>' . $subd->bet_side . '</span></td>';
                }

                $html .= '<td>' . $subd->created_at . ' </td>';


                if ($subd->bet_type == 'ODDS') {

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

    /*public function back_accountstmtdata(Request $request)
    {
        //$getUserCheck = Session::get('playerUser');
        $loginuser = User::where('id',$request->pid)->where('check_login',1)->first();

        $player_balance=CreditReference::where('player_id',$loginuser->id)->first();
        $player_balance=$player_balance['remain_bal'];

        $settings = CreditReference::where('player_id',$loginuser->id)->first();
        $balance=$settings['available_balance_for_D_W'];

        if(empty($request->dateto)){
            $todate = date("Y-m-d", strtotime("+1 day"));
        }
        if(empty($request->datefrom)){
            $fromdate = date("Y-m-d");
        }
        if($request->datefrom){
            $fromdate = date('Y-m-d',strtotime($request->datefrom));
        }
        if($request->dateto){
            $todate = date('Y-m-d',strtotime($request->dateto));
        }

        if($request->datefrom == $request->dateto)
        {
            $fromdate = date('Y-m-d',strtotime($request->datefrom));
            $todate = date('Y-m-d',strtotime($request->datefrom ."+1 day"));
        }


        $drpval = $request->drpval;
        $html='';

        if($drpval == '0')
        {
            $credit = UserDeposit::where(['child_id' =>$loginuser->id, 'parent_id' => $loginuser->parentid])
            ->whereBetween('created_at',[$fromdate,$todate])
            //->latest()
            ->orderBy('created_at')
            ->get();

            $betdata = MyBets::where('user_id', $loginuser->id)
            ->where('result_declare',1)
            ->whereBetween('created_at',[$fromdate,$todate])
            ->groupBy('match_id')
            ->orderBy('created_at')
            ->get();


            $merged = $betdata->merge($credit)->sortBy('created_at');
            $result = $merged->all();

            //echo "<pre>"; print_r($result); exit;

            $ttlAmt=0;$ttlAmto=0;$ttlAmtb=0;$i=2; $blnc=0;$sumAmt=0;$sumAmto=0;$sumAmtb=0;$commn=0;
            $html.='<tr role="row" class="odd">
                    <td class="sorting_1">
                    </td>
                    <td class="text-right">1</td>
                    <td class="text-right text-success">'.$blnc.'</td>
                    <td class="text-right text-danger"></td>
                    <td class="text-right text-success">'.$blnc.'</td>
                    <td>
                        <a>Opening Balance</a>
                    </td>
                </tr>';

            $next_row_balance=$blnc;

            foreach ($result as $key => $data1)
            {

                $mthnm = Match::where('event_id', $data1['match_id'])->first();

                if($mthnm){
                    $sprtnm = Sport::where('sId',$mthnm->sports_id)->first();

                    $betlist= MyBets::where('user_id', $loginuser->id)
                    ->where('result_declare',1)
                    ->where('match_id', $data1['match_id'])
                    ->whereBetween('created_at',[$fromdate,$todate])
                    ->groupBy('bet_type')
                    ->orderBy('created_at')
                    ->get();

                    $expodds=UserExposureLog::where('match_id',$mthnm->id)->where('user_id', $loginuser->id)->whereBetween('created_at',[$fromdate,$todate])->where('bet_type','ODDS')->first();

                    if($expodds){

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
                            ->where('bet_type','SESSION')
                            ->groupBy('team_name')
                            ->where('match_id', $data1['match_id'])
                            ->whereBetween('created_at',[$fromdate,$todate])
                            ->orderBy('created_at')
                            ->get();



                            foreach ($betlist1 as $key => $value1)
                            {
                                //echo $value1->id;
                                $fnc_rslt=FancyResult::where('eventid',$data1['match_id'])->where('fancy_name',$value1->team_name)->first();
                                //echo $data1['match_id'];
                                //echo "<br>";
                                //cho $loginuser->id;
                                //echo $data1['match_id'];


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

                                $html.='<tr role="row" class="odd">
                                <td class="sorting_1">
                                 '.date('d-m-y H:i',strtotime($value->created_at)).'
                                </td>
                                <td class="text-right">'.$i.'</td>
                                <td class="text-right text-success">';
                                if(!empty($ttlAmt) ){
                                    if($fancy_win_type=='Profit'){
                                        $html.=''.$ttlAmt.'';
                                    }
                                }
                                else if($ttlAmt==0){
                                    $html.='0';
                                }
                                $html.='</td>
                                <td class="text-right text-danger">';
                                if(!empty($ttlAmt)){
                                    if($fancy_win_type=='Loss'){
                                        $html.=''.$ttlAmt.'';
                                    }
                                }
                                $html.='</td>';
                                if($fancy_win_type=='Profit')
                                {
                                    //$html.=''.$ttlAmt.'';
                                    $next_row_balance = $next_row_balance + $ttlAmt;
                                    if($next_row_balance >= 0){
                                        $html.='<td class="text-right text-success">
                                        '.abs(round($next_row_balance, 2)).'
                                        </td>';
                                    }
                                    if($next_row_balance < 0){
                                        $html.='<td class="text-right text-danger">
                                        '.abs(round($next_row_balance, 2)).'
                                        </td>';
                                    }
                                }

                                else if($fancy_win_type=='Loss')
                                {
                                    //$html.=''.$ttlAmt.'';
                                    $next_row_balance = $next_row_balance - $ttlAmt;
                                    if($next_row_balance < 0){
                                        $html.='<td class="text-right text-danger">
                                        '.abs(round($next_row_balance, 2)).'
                                        </td>';
                                    }
                                    if($next_row_balance >= 0){
                                        $html.='<td class="text-right text-success">
                                        '.abs(round($next_row_balance, 2)).'
                                        </td>';
                                    }

                                }
                               $html.='<td>

                               <a data-id="'.$mthnm->event_id.'" data-name="'.$value1->team_name.'" data-type="'.$value1->bet_type.'" class="text-dark" onclick="openMatchReport(this);" >'.$sprtnm->sport_name.' / '.$value1->team_name.' / '.$value1->bet_type.' / '.$f_result.'</a>
                               </td>

                            </tr>';
                            $i++;

                            }
                        }
                        else if($value->bet_type == 'ODDS')
                        {
                            if(!empty($expodds))
                            {
                                $html.='<tr role="row" class="odd">
                                <td class="sorting_1">
                                 '.date('d-m-y H:i',strtotime($value->created_at)).'
                                </td>
                                <td class="text-right">'.$i.'</td>
                                <td class="text-right text-success">';
                                if(!empty($ttlAmto)){
                                    if($expodds->win_type=='Profit'){
                                        $html.=''.$ttlAmto.'';
                                    }

                                }
                                else if($ttlAmto ==0){
                                    $html.='0';
                                }
                                $html.='</td>
                                <td class="text-right text-danger">';
                                if(!empty($ttlAmto)){
                                    if($expodds->win_type=='Loss'){
                                        $html.=''.$ttlAmto.'';
                                    }
                                }
                                $html.='</td>';
                                if($expodds->win_type=='Profit')
                                {
                                    //$html.=''.$ttlAmto.'';
                                    $next_row_balance = $next_row_balance + $ttlAmto;
                                    if($next_row_balance >= 0){
                                        $html.='<td class="text-right text-success">
                                        '.abs(round($next_row_balance, 2)).'
                                        </td>';
                                    }
                                    if($next_row_balance < 0){
                                        $html.='<td class="text-right text-danger">
                                        '.abs(round($next_row_balance, 2)).'
                                        </td>';
                                    }
                                }

                                else if($expodds->win_type=='Loss')
                                {
                                    //$html.=''.$ttlAmto.'';
                                    $next_row_balance = $next_row_balance - abs($ttlAmto);
                                    if($next_row_balance < 0){
                                        $html.='<td class="text-right text-danger">
                                        '.abs(round($next_row_balance, 2)).'
                                        </td>';
                                    }
                                    if($next_row_balance >= 0){
                                        $html.='<td class="text-right text-success">
                                        '.abs(round($next_row_balance, 2)).'
                                        </td>';
                                    }
                                }
                               $html.='<td>

                               <a data-id="'.$mthnm->event_id.'" data-name="'.$value->team_name.'" data-type="'.$value->bet_type.'" class="text-dark" onclick="openMatchReport(this);" >'.$sprtnm->sport_name.' / '.$mthnm->match_name.' / '.$value->bet_type.' / '.$mthnm->winner.'</a>
                               </td>

                                </tr>';
                                $i++;

                                if($expodds->win_type=='Profit')
                                {

                                    $html.='
                                        <tr role="row" class="odd">
                                        <td class="sorting_1">'.date('d-m-y H:i',strtotime($value->created_at)).'</td>
                                        <td class="text-right">'.$i.'</td>
                                        <td class="text-right text-success"></td>
                                        <td class="text-right text-danger">';
                                        if(!empty($sumAmt1)){
                                            if(empty($loginuser->commission)){
                                                $commn=0;
                                            }
                                            else{
                                                $commn=$loginuser->commission;
                                            }
                                            $ttlAmto = ($sumAmt1 * $commn) /100;
                                            //$ttlAmto+=$sumAmt1-$sumAmt_ttl;
                                            $html.=''.$ttlAmto.'';
                                        }
                                        $html.='
                                        </td>';
                                        if($ttlAmto == 0){
                                            $html.='<td class="text-right text-danger">
                                                '.abs(round($next_row_balance, 2)).'
                                                </td>';
                                        }
                                        else
                                        {
                                            //$html.=''.$ttlAmto.'';
                                            $next_row_balance = $next_row_balance - $ttlAmto;
                                            if($next_row_balance < 0){
                                                $html.='<td class="text-right text-danger">
                                                '.abs(round($next_row_balance, 2)).'
                                                </td>';
                                            }
                                            if($next_row_balance >= 0){
                                                $html.='<td class="text-right text-success">
                                                '.abs(round($next_row_balance, 2)).'
                                                </td>';
                                            }
                                        }

                                        $html.='<td>
                                           <a data-id="'.$mthnm->event_id.'" data-name="'.$value->team_name.'" data-type="'.$value->bet_type.'" class="text-dark" onclick="openMatchReport(this);" >'.$sprtnm->sport_name.' / '.$mthnm->match_name.' / '.$value->bet_type.' / '.$mthnm->winner.' (Com)</a>
                                        </td>
                                        </tr>
                                    ';
                                    $i++;
                                }
                            }
                        }
                        else
                        {
                            if(!empty($exposer_bm))
                            {
                                $html.='<tr role="row" class="odd">
                                <td class="sorting_1">
                                 '.date('d-m-y H:i',strtotime($value->created_at)).'
                                </td>
                                <td class="text-right">'.$i.'</td>
                                <td class="text-right text-success">';
                                if(!empty($ttlAmtb)){
                                    if($bm_win_type=='Profit'){
                                        $html.=''.$ttlAmtb.'';
                                    }
                                }
                                else if($ttlAmtb ==0){
                                    $html.='0';
                                }
                                $html.='</td>
                                <td class="text-right text-danger">';
                                if(!empty($ttlAmtb)){
                                    if($bm_win_type=='Loss'){
                                        $html.=''.$ttlAmtb.'';
                                    }
                                }
                                $html.='</td>';
                                if($bm_win_type=='Profit'){

                                    //$html.=''.$ttlAmtb.'';

                                        $next_row_balance = $next_row_balance + $ttlAmtb;
                                        if($next_row_balance >= 0){
                                            $html.='<td class="text-right text-success">
                                            '.abs(round($next_row_balance, 2)).'
                                            </td>';
                                        }
                                        if($next_row_balance < 0){
                                            $html.='<td class="text-right text-danger">
                                            '.abs(round($next_row_balance, 2)).'
                                            </td>';
                                        }

                                }

                                else if($bm_win_type=='Loss'){

                                    //$html.=''.$ttlAmtb.'';

                                        $next_row_balance = $next_row_balance - $ttlAmtb;
                                        if($next_row_balance < 0){
                                            $html.='<td class="text-right text-danger">
                                            '.abs(round($next_row_balance, 2)).'
                                            </td>';
                                        }
                                        if($next_row_balance >= 0){
                                            $html.='<td class="text-right text-success">
                                            '.abs(round($next_row_balance, 2)) .'
                                            </td>';
                                        }

                                }
                               $html.='<td>

                               <a data-id="'.$mthnm->event_id.'" data-name="'.$value->team_name.'" data-type="'.$value->bet_type.'" class="text-dark" onclick="openMatchReport(this);" >'.$sprtnm->sport_name.' / '.$mthnm->match_name.' / '.$value->bet_type.' / '.$mthnm->winner.'</a>
                               </td>

                                </tr>';
                                $i++;
                            }
                        }
                    }
                }
                else{
                    $html.='
                    <tr role="row" class="odd">
                        <td class="sorting_1">'.date('d-m-y H:i',strtotime($data1->created_at)).'</td>
                        <td class="text-right">'.$i.'</td>
                        <td class="text-right text-success">';
                            if(!empty($data1->amount))
                            {
                                if($data1->balanceType == 'DEPOSIT'){
                                    $html.=''.$data1->amount.'';
                                }
                            }
                        $html.='</td>
                        <td class="text-right text-danger">';
                            if(!empty($data1->amount))
                            {
                                if($data1->balanceType == 'WITHDRAW'){
                                    $html.=''.$data1->amount.'';
                                }
                            }
                        $html.='</td>
                        <td class="text-right text-success">';
                            if($data1->amount)
                            {
                                if($data1->balanceType == 'DEPOSIT')
                                {
                                    $next_row_balance = $next_row_balance + $data1->amount;
                                    $html.=''.round($next_row_balance, 2).'';
                                }
                                if($data1->balanceType == 'WITHDRAW')
                                {
                                    $next_row_balance = $next_row_balance - $data1->amount;
                                    $html.=''.round($next_row_balance, 2).'';
                                }
                            }
                        $html.='</td>
                        <td>'.$data1['extra'].'</td>
                     </tr>';
                     $i++;
                }

               //$i++;
            }
            //exit;

            return $html;

        //Match::where('event_id', $data['match_id'])->first();
        }

        if($drpval == '1')
        {
            $credit = UserDeposit::where(['child_id' =>$loginuser->id, 'parent_id' => $loginuser->parentid])
            ->whereBetween('created_at',[$fromdate,$todate])
            //->latest()
            ->get();

            $i=2;$blnc=0;

            $html.='<tr role="row" class="odd">
                    <td class="sorting_1">
                    </td>
                    <td class="text-right">1</td>
                    <td class="text-right text-success">'.$blnc.'</td>
                    <td class="text-right text-danger"></td>
                    <td class="text-right text-success">'.$blnc.'</td>
                    <td>Opening Balance</td>
                </tr>';
            foreach($credit as $data)
            {
                $html.='<tr role="row" class="odd">
                    <td class="sorting_1">
                    '.date('d-m-y H:i',strtotime($data->created_at)).'
                    </td>
                    <td class="text-right">'.$i.'</td>
                    <td class="text-right text-success">';
                        if($data->balanceType == 'DEPOSIT'){
                            $html.=''.$data->amount.'';
                        }
                    $html.='</td>
                    <td class="text-right text-danger">';
                        if($data->balanceType == 'WITHDRAW'){
                            $html.=''.$data->amount.'';
                        }
                    $html.='</td>
                    <td class="text-right text-success">';
                        if ($i == 2){
                            $prev_bal=$balance;

                            $html.=''.$data->amount.'';

                            if($data->balanceType == 'DEPOSIT'){
                                $next_row_balance=$data->amount;
                            }

                            if($data->balanceType == 'WITHDRAW'){
                                $next_row_balance =$data->amount;
                            }
                        }
                        else{

                            if($data->balanceType == 'DEPOSIT')
                            {
                                $next_row_balance = $next_row_balance + $data->amount;
                                $html.=''.round($next_row_balance, 2).'';

                                //$html.=''.$next_row_balance.'+ '.$data->amount.'';
                                //$next_row_balance=$next_row_balance+$data->amount;

                            }
                            if($data->balanceType == 'WITHDRAW')
                            {
                                $next_row_balance = $next_row_balance - $data->amount;
                                $html.=''.round($next_row_balance, 2).'';
                                //$html.=''.$next_row_balance.'- '.$data->amount.'';
                                //$next_row_balance=$next_row_balance-$data->amount;
                            }
                        }
                   $html.=' </td>
                    <td>';
                        $html.=''.$data->extra.'';
                    $html.='</td>
                </tr>';
                $i++;
                $previousValue = $data;
            }
        }

        if($drpval == '2')
        {

            $gmdata = MyBets::where('user_id', $loginuser->id)
            ->where('result_declare',1)
            ->whereBetween('created_at',[$fromdate,$todate])
            ->groupBy('match_id')->get();

            //echo"<pre>";print_r($gmdata); exit;

            $ttlAmt=0;$ttlAmto=0;$ttlAmtb=0;$i=2; $blnc=0;$sumAmt=0;$sumAmto=0;$sumAmtb=0;$sumAmt1=0;
            $commn=0;
            $html.='<tr role="row" class="odd">
                    <td class="sorting_1">

                    </td>
                    <td class="text-right">1</td>
                    <td class="text-right text-success">'.$blnc.'</td>
                    <td class="text-right text-danger"></td>
                    <td class="text-right text-success">'.$blnc.'</td>
                    <td>
                        <a>Opening Balance</a>
                    </td>
                </tr>';

            $next_row_balance=$blnc;
            foreach($gmdata as $data)
            {
                $mthnm = Match::where('event_id', $data['match_id'])->first();

                $sprtnm = Sport::where('sId',$mthnm->sports_id)->first();

                $betlist= MyBets::where('user_id', $loginuser->id)
                ->where('result_declare',1)
                ->where('match_id', $data['match_id'])
                ->whereBetween('created_at',[$fromdate,$todate])
                ->groupBy('bet_type')
                ->get();

                //echo"<pre>";print_r($betlist);

                $expodds=UserExposureLog::where('match_id',$mthnm->id)->where('user_id', $loginuser->id)->whereBetween('created_at',[$fromdate,$todate])->where('bet_type','ODDS')->first();

                if(!empty($expodds)){
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

                            $html.='<tr role="row" class="odd">
                                <td class="sorting_1">
                                '.date('d-m-y H:i',strtotime($value->created_at)).'

                                </td>
                                <td class="text-right">'.$i.'</td>
                                <td class="text-right text-success">';
                                if(!empty($ttlAmt)){
                                    if($fancy_win_type=='Profit'){
                                        $html.=''.$ttlAmt.'';
                                    }
                                }
                                else if($ttlAmt ==0){
                                    $html.='0';
                                }
                                $html.='</td>
                                <td class="text-right text-danger">';
                                if(!empty($ttlAmt)){
                                    if($fancy_win_type=='Loss'){
                                        $html.=''.$ttlAmt.'';
                                    }
                                }
                                $html.='</td>';
                                if($fancy_win_type=='Profit')
                                {
                                    //$html.=''.$ttlAmt.'';
                                    $next_row_balance = $next_row_balance + $ttlAmt;
                                    if($next_row_balance >= 0){
                                        $html.='<td class="text-right text-success">
                                        '.abs(round($next_row_balance, 2)).'
                                        </td>';
                                    }
                                    if($next_row_balance < 0){
                                        $html.='<td class="text-right text-danger">
                                        '.abs(round($next_row_balance, 2)).'
                                        </td>';
                                    }
                                }

                                else if($fancy_win_type=='Loss')
                                {
                                    //$html.=''.$ttlAmt.'';
                                    $next_row_balance = $next_row_balance - $ttlAmt;
                                    if($next_row_balance < 0){
                                        $html.='<td class="text-right text-danger">
                                        '.abs(round($next_row_balance, 2)).'
                                        </td>';
                                    }
                                    if($next_row_balance >= 0){
                                        $html.='<td class="text-right text-success">
                                        '.abs(round($next_row_balance, 2)).'
                                        </td>';
                                    }

                                }
                               $html.='<td>

                               <a data-id="'.$mthnm->event_id.'" data-name="'.$value1->team_name.'" data-type="'.$value1->bet_type.'" class="text-dark" onclick="openMatchReport(this);" >'.$sprtnm->sport_name.' / '.$value1->team_name.' / '.$value1->bet_type.' / '.$f_result.'</a>
                               </td>

                            </tr>';
                        $i++;

                        }

                    }
                    else if($value->bet_type == 'ODDS')
                    {
                        if(!empty($expodds))
                        {
                            $html.='<tr role="row" class="odd">
                            <td class="sorting_1">
                             '.date('d-m-y H:i',strtotime($value->created_at)).'
                            </td>
                            <td class="text-right">'.$i.'</td>
                            <td class="text-right text-success">';
                            if(!empty($ttlAmto)){
                                if($expodds->win_type=='Profit'){
                                    $html.=''.$ttlAmto.'';
                                }

                            }
                            else if($ttlAmto ==0){
                                $html.='0';
                            }
                            $html.='</td>
                            <td class="text-right text-danger">';
                            if(!empty($ttlAmto)){
                                if($expodds->win_type=='Loss'){
                                    $html.=''.$ttlAmto.'';
                                }
                            }
                            $html.='</td>';
                            if($expodds->win_type=='Profit')
                            {
                                //$html.=''.$ttlAmto.'';
                                $next_row_balance = $next_row_balance + $ttlAmto;
                                if($next_row_balance >= 0){
                                    $html.='<td class="text-right text-success">
                                    '.abs(round($next_row_balance, 2)).'
                                    </td>';
                                }
                                if($next_row_balance < 0){
                                    $html.='<td class="text-right text-danger">
                                    '.abs(round($next_row_balance, 2)).'
                                    </td>';
                                }
                            }

                            else if($expodds->win_type=='Loss')
                            {
                                //$html.=''.$ttlAmto.'';
                                $next_row_balance = $next_row_balance - abs($ttlAmto);
                                if($next_row_balance < 0){
                                    $html.='<td class="text-right text-danger">
                                    '.abs(round($next_row_balance, 2)).'
                                    </td>';
                                }
                                if($next_row_balance >= 0){
                                    $html.='<td class="text-right text-success">
                                    '.abs(round($next_row_balance, 2)).'
                                    </td>';
                                }
                            }
                           $html.='<td>

                           <a data-id="'.$mthnm->event_id.'" data-name="'.$value->team_name.'" data-type="'.$value->bet_type.'" class="text-dark" onclick="openMatchReport(this);" >'.$sprtnm->sport_name.' / '.$mthnm->match_name.' / '.$value->bet_type.' / '.$mthnm->winner.'</a>
                           </td>

                            </tr>';
                            $i++;

                            if($expodds->win_type=='Profit')
                            {

                                $html.='
                                    <tr role="row" class="odd">
                                    <td class="sorting_1">'.date('d-m-y H:i',strtotime($value->created_at)).'</td>
                                    <td class="text-right">'.$i.'</td>
                                    <td class="text-right text-success"></td>
                                    <td class="text-right text-danger">';
                                    if(!empty($sumAmt1)){
                                        if(empty($loginuser->commission)){
                                            $commn=0;
                                        }
                                        else{
                                            $commn=$loginuser->commission;
                                        }
                                        $ttlAmto = ($sumAmt1 * $commn) /100;
                                        //$ttlAmto+=$sumAmt1-$sumAmt_ttl;
                                        $html.=''.$ttlAmto.'';
                                    }
                                    $html.='
                                    </td>';
                                    if($ttlAmto == 0){
                                        $html.='<td class="text-right text-danger">
                                            '.abs(round($next_row_balance, 2)).'
                                            </td>';
                                    }
                                    else
                                    {
                                        //$html.=''.$ttlAmto.'';
                                        $next_row_balance = $next_row_balance - $ttlAmto;
                                        if($next_row_balance < 0){
                                            $html.='<td class="text-right text-danger">
                                            '.abs(round($next_row_balance, 2)).'
                                            </td>';
                                        }
                                        if($next_row_balance >= 0){
                                            $html.='<td class="text-right text-success">
                                            '.abs(round($next_row_balance, 2)).'
                                            </td>';
                                        }
                                    }

                                    $html.='<td>
                                       <a data-id="'.$mthnm->event_id.'" data-name="'.$value->team_name.'" data-type="'.$value->bet_type.'" class="text-dark" onclick="openMatchReport(this);" >'.$sprtnm->sport_name.' / '.$mthnm->match_name.' / '.$value->bet_type.' / '.$mthnm->winner.' (Com)</a>
                                    </td>
                                    </tr>
                                ';
                                $i++;
                            }
                        }
                    }
                    else
                    {
                        if(!empty($exposer_bm))
                        {
                            $html.='<tr role="row" class="odd">
                            <td class="sorting_1">
                             '.date('d-m-y H:i',strtotime($value->created_at)).'
                            </td>
                            <td class="text-right">'.$i.'</td>
                            <td class="text-right text-success">';
                            if(!empty($ttlAmtb)){
                                if($bm_win_type=='Profit'){
                                    $html.=''.$ttlAmtb.'';
                                }
                            }
                            else if($ttlAmtb ==0){
                                $html.='0';
                            }
                            $html.='</td>
                            <td class="text-right text-danger">';
                            if(!empty($ttlAmtb)){
                                if($bm_win_type=='Loss'){
                                    $html.=''.$ttlAmtb.'';
                                }
                            }
                            $html.='</td>';
                            if($bm_win_type=='Profit'){

                                //$html.=''.$ttlAmtb.'';

                                    $next_row_balance = $next_row_balance + $ttlAmtb;
                                    if($next_row_balance >= 0){
                                        $html.='<td class="text-right text-success">
                                        '.abs(round($next_row_balance, 2)).'
                                        </td>';
                                    }
                                    if($next_row_balance < 0){
                                        $html.='<td class="text-right text-danger">
                                        '.abs(round($next_row_balance, 2)).'
                                        </td>';
                                    }

                            }

                            else if($bm_win_type=='Loss'){

                                //$html.=''.$ttlAmtb.'';

                                    $next_row_balance = $next_row_balance - $ttlAmtb;
                                    if($next_row_balance < 0){
                                        $html.='<td class="text-right text-danger">
                                        '.abs(round($next_row_balance, 2)).'
                                        </td>';
                                    }
                                    if($next_row_balance >= 0){
                                        $html.='<td class="text-right text-success">
                                        '.abs(round($next_row_balance, 2)) .'
                                        </td>';
                                    }

                            }
                           $html.='<td>

                           <a data-id="'.$mthnm->event_id.'" data-name="'.$value->team_name.'" data-type="'.$value->bet_type.'" class="text-dark" onclick="openMatchReport(this);" >'.$sprtnm->sport_name.' / '.$mthnm->match_name.' / '.$value->bet_type.' / '.$mthnm->winner.'</a>
                           </td>

                            </tr>';
                            $i++;
                        }
                    }

                //$i++;

                }

            }
        }

        return $html;

    }*/
    public function back_accountstmtdata(Request $request)
    {
        $loginuser = User::where('id', $request->pid)->first();

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

        $drpval = $request->drpval;
        $html = '';
        $response = array();

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
                ->get();

            $betdata = MyBets::where('user_id', $loginuser->id)
                ->where('result_declare', 1)
                ->whereBetween('created_at', [$fromdate, $todate])
                ->groupBy('match_id')
                ->orderBy('created_at')
                ->get();


            $merged = $betdata->merge($credit)->sortBy('created_at')->skip($start)->take($rowperpage);
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
            $bttl = 0;
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
                        ->where('match_id', $data1['match_id'])
                        ->whereBetween('created_at', [$fromdate, $todate])
                        ->groupBy('bet_type')
                        ->orderBy('created_at')
                        ->get();

                    $expodds = UserExposureLog::where('match_id', $mthnm->id)->where('user_id', $loginuser->id)->whereBetween('created_at', [$fromdate, $todate])->where('bet_type', 'ODDS')->first();

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

                                $data_arr[] = array(
                                    "date" => $date,
                                    "srno" => $srno,
                                    "credit" => $credit,
                                    "debit" => $debit,
                                    "balance" => $balance,
                                    "remark" => $remark
                                );
                                $bttl = $i;
                                $i++;

                            }
                        } else if ($value->bet_type == 'ODDS') {
                            if (!empty($expodds)) {
                                $date = '<span class="sorting_1">' . date('d-m-y H:i', strtotime($value->created_at)) . '</span>';
                                $srno = '<span class="text-right">' . $i . '</span>';
                                if (!empty($ttlAmto)) {
                                    if ($expodds->win_type == 'Profit') {
                                        $credit = '<span class="text-right text-success">' . number_format($ttlAmto, 2) . '</span>';
                                    } else {
                                        $credit = '<span class="text-right text-success"></span>';
                                    }
                                } else if ($ttlAmt == 0) {
                                    $credit = '<span class="text-right text-success">0</span>';
                                } else {
                                    $credit = '<span class="text-right text-success"></span>';
                                }

                                if (!empty($ttlAmto)) {
                                    if ($expodds->win_type == 'Loss') {
                                        $debit = '<span class="text-right text-danger">' . number_format($ttlAmto, 2) . '</span>';
                                    } else {
                                        $debit = '<span class="text-right text-danger"></span>';
                                    }
                                } else if ($ttlAmt == 0) {
                                    $debit = '<span class="text-right text-danger"></span>';
                                } else {
                                    $debit = '<span class="text-right text-danger"></span>';
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
                                } else if ($expodds->win_type == 'Loss') {
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

                                $data_arr[] = array(
                                    "date" => $date,
                                    "srno" => $srno,
                                    "credit" => $credit,
                                    "debit" => $debit,
                                    "balance" => $balance,
                                    "remark" => $remark
                                );
                                $bttl = $i;
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
                                    $data_arr[] = array(
                                        "date" => $date,
                                        "srno" => $srno,
                                        "credit" => $credit,
                                        "debit" => $debit,
                                        "balance" => $balance,
                                        "remark" => $remark
                                    );
                                    $bttl = $i;
                                    $i++;
                                }
                            }
                        } else {
                            if (!empty($exposer_bm)) {
                                $date = '<span class="sorting_1"> ' . date('d-m-y H:i', strtotime($value->created_at)) . '</span>';

                                $srno = '<span class="text-right">' . $i . '</span>';
                                if (!empty($ttlAmtb)) {
                                    if ($bm_win_type == 'Profit') {
                                        $credit = '<span class="text-right text-success">' . number_format($ttlAmtb, 2) . '</span>';
                                    } else {
                                        $credit = '<span class="text-right text-success"></span>';
                                    }
                                } else if ($ttlAmtb == 0) {
                                    $credit = '<span class="text-right text-success">0</span>';
                                } else {
                                    $credit = '<span class="text-right text-success"></span>';
                                }

                                if (!empty($ttlAmtb)) {
                                    if ($bm_win_type == 'Loss') {
                                        $debit = '<span class="text-right text-danger">' . number_format($ttlAmtb, 2) . '</span>';
                                    } else {
                                        $debit = '<span class="text-right text-danger"></span>';
                                    }
                                } else if ($ttlAmtb == 0) {
                                    $debit = '<span class="text-right text-danger"></span>';
                                } else {
                                    $debit = '<span class="text-right text-danger"></span>';
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
                                $data_arr[] = array(
                                    "date" => $date,
                                    "srno" => $srno,
                                    "credit" => $credit,
                                    "debit" => $debit,
                                    "balance" => $balance,
                                    "remark" => $remark
                                );
                                $bttl = $i;
                                $i++;
                            }
                        }
                    }
                } else {
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

                    $data_arr[] = array(
                        "date" => $date,
                        "srno" => $srno,
                        "credit" => $credit,
                        "debit" => $debit,
                        "balance" => $balance,
                        "remark" => $remark
                    );
                    $bttl = $i;
                    $i++;
                }
            }
        }

        if ($drpval == '1') {
            $draw = $request->get('draw');
            $start = $request->get("start");
            $rowperpage = $request->get("length");

            $columnIndex_arr = $request->get('order');
            $columnName_arr = $request->get('columns');
            $order_arr = $request->get('order');
            $search_arr = $request->get('search');

            $searchValue = $search_arr['value']; // Search value

            $creditData = UserDeposit::where(['child_id' => $loginuser->id, 'parent_id' => $loginuser->parentid])
                ->whereBetween('created_at', [$fromdate, $todate])
                ->skip($start)->take($rowperpage)
                ->get();

            $getresultcount = UserDeposit::where(['child_id' => $loginuser->id, 'parent_id' => $loginuser->parentid])
                ->whereBetween('created_at', [$fromdate, $todate])
                ->get();

            $getresultcounttot = count($getresultcount);
            $totalRecords = $getresultcounttot;

            $i = 2;
            $blnc = 0;
            $data_arr = array();
            $bttl = 0;

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

            foreach ($creditData as $data) {
                $date = '<span class="sorting_1"> ' . date('d-m-y H:i', strtotime($data->created_at)) . '</span>';

                $srno = '<span class="text-right">' . $i . '</span>';

                if ($data->balanceType == 'DEPOSIT') {
                    $credit = '<span class="text-right text-success">' . $data->amount . '</span>';
                } else {
                    $credit = '<span class="text-right text-success"></span>';
                }

                if ($data->balanceType == 'WITHDRAW') {
                    $debit = '<span class="text-right text-danger">' . $data->amount . '</span>';
                } else {
                    $debit = '<span class="text-right text-danger"></span>';
                }

                if ($i == 2) {
                    $prev_bal = $balance;
                    $balance = '<span class="text-right text-success">' . $data->amount . '</span>';

                    if ($data->balanceType == 'DEPOSIT') {
                        $next_row_balance = $data->amount;
                    }

                    if ($data->balanceType == 'WITHDRAW') {
                        $next_row_balance = $data->amount;
                    }
                } else {
                    if ($data->balanceType == 'DEPOSIT') {
                        $next_row_balance = $next_row_balance + $data->amount;
                        $balance = '<span class="text-right text-success">' . number_format($next_row_balance, 2) . '</span>';
                    }
                    if ($data->balanceType == 'WITHDRAW') {
                        $next_row_balance = $next_row_balance - $data->amount;
                        $balance = '<span class="text-right text-success">' . number_format($next_row_balance, 2) . '</span>';
                    }
                }
                $remark = '<span>' . $data->extra . '</span>';

                $data_arr[] = array(
                    "date" => $date,
                    "srno" => $srno,
                    "credit" => $credit,
                    "debit" => $debit,
                    "balance" => $balance,
                    "remark" => $remark
                );
                $bttl = $i;
                $i++;
                $previousValue = $data;
            }
        }

        if ($drpval == '2') {
            $draw = $request->get('draw');
            $start = $request->get("start");
            $rowperpage = $request->get("length");

            $columnIndex_arr = $request->get('order');
            $columnName_arr = $request->get('columns');
            $order_arr = $request->get('order');
            $search_arr = $request->get('search');

            $searchValue = $search_arr['value']; // Search value

            $gmdata = MyBets::where('user_id', $loginuser->id)
                ->where('result_declare', 1)
                ->whereBetween('created_at', [$fromdate, $todate])
                ->groupBy('match_id')
                //->skip($start)->take($rowperpage)
                ->get();

            $getresultcount = MyBets::where('user_id', $loginuser->id)
                ->where('result_declare', 1)
                ->whereBetween('created_at', [$fromdate, $todate])
                ->groupBy('match_id')
                ->skip($start)->take($rowperpage)
                ->get();

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
            $sumAmt1 = 0;
            $commn = 0;
            $bttl = 0;

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
            foreach ($gmdata as $data) {
                $mthnm = Match::where('event_id', $data['match_id'])->first();
                $sprtnm = Sport::where('sId', $mthnm->sports_id)->first();

                $betlist = MyBets::where('user_id', $loginuser->id)
                    ->where('result_declare', 1)
                    ->where('match_id', $data['match_id'])
                    ->whereBetween('created_at', [$fromdate, $todate])
                    ->groupBy('bet_type')
                    ->skip($start)->take($rowperpage)
                    ->get();

                $expodds = UserExposureLog::where('match_id', $mthnm->id)->where('user_id', $loginuser->id)->whereBetween('created_at', [$fromdate, $todate])->where('bet_type', 'ODDS')->first();

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
                            ->where('bet_type', 'SESSION')
                            ->where('match_id', $data['match_id'])
                            ->whereBetween('created_at', [$fromdate, $todate])
                            ->groupBy('team_name')
                            ->orderBy('created_at')
                            ->skip($start)->take($rowperpage)
                            ->get();

                        foreach ($betlist1 as $key => $value1) {
                            $fnc_rslt = FancyResult::where('fancy_name', $value1->team_name)->where('eventid', $data['match_id'])->first();

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

                            $data_arr[] = array(
                                "date" => $date,
                                "srno" => $srno,
                                "credit" => $credit,
                                "debit" => $debit,
                                "balance" => $balance,
                                "remark" => $remark
                            );
                            $bttl = $i;
                            $i++;
                        }
                    } else if ($value->bet_type == 'ODDS') {
                        $date = '<span class="sorting_1"> ' . date('d-m-y H:i', strtotime($value->created_at)) . '</span>';
                        $srno = '<span class="text-right">' . $i . '</span>';

                        if (!empty($ttlAmto)) {
                            if ($expodds->win_type == 'Profit') {
                                $credit = '<span class="text-right text-success">' . number_format($ttlAmto, 2) . '</span>';
                            } else {
                                $credit = '<span class="text-right text-success"></span>';
                            }
                        } else if ($ttlAmto == 0) {
                            $credit = '<span class="text-right text-success">0</span>';
                        } else {
                            $credit = '<span class="text-right text-success"></span>';
                        }

                        if (!empty($ttlAmto)) {
                            if ($expodds->win_type == 'Loss') {
                                $debit = '<span class="text-right text-danger">' . number_format($ttlAmto, 2) . '</span>';
                            } else {
                                $debit = '<span class="text-right text-danger"></span>';
                            }
                        } else if ($ttlAmto == 0) {
                            $debit = '<span class="text-right text-danger"></span>';
                        } else {
                            $debit = '<span class="text-right text-danger"></span>';
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
                        } else if ($expodds->win_type == 'Loss') {
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

                        $data_arr[] = array(
                            "date" => $date,
                            "srno" => $srno,
                            "credit" => $credit,
                            "debit" => $debit,
                            "balance" => $balance,
                            "remark" => $remark
                        );
                        $bttl = $i;
                        $i++;

                        if ($expodds->win_type == 'Profit') {
                            $date = '<span class="sorting_1">' . date('d-m-y H:i', strtotime($value->created_at)) . '</span>';
                            $srno = '<span class="text-right">' . $i . '</span>';
                            $credit = '<span class="text-right text-success"></span>';
                            if (!empty($sumAmt1)) {
                                if (empty($loginuser->commission)) {
                                    $commn = 1;
                                } else {
                                    $commn = $loginuser->commission;
                                }
                                $ttlAmto = ($sumAmt1 * $commn) / 100;
                                $debit = '<span class="text-right text-danger">' . number_format($ttlAmto, 2) . '</span>';
                            }

                            if ($ttlAmto == 0) {
                                $balance = '<span class="text-right text-danger">' . number_format(abs($next_row_balance), 2) . '
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

                            $data_arr[] = array(
                                "date" => $date,
                                "srno" => $srno,
                                "credit" => $credit,
                                "debit" => $debit,
                                "balance" => $balance,
                                "remark" => $remark
                            );
                            $bttl = $i;
                            $i++;
                        }
                    } else {
                        $date = '<span class="sorting_1"> ' . date('d-m-y H:i', strtotime($value->created_at)) . '</span>';
                        $srno = '<span class="text-right">' . $i . '</span>';
                        if (!empty($ttlAmtb)) {
                            if ($bm_win_type == 'Profit') {
                                $credit = '<span class="text-right text-success">' . number_format($ttlAmtb, 2) . '</span>';
                            } else {
                                $credit = '<span class="text-right text-success"></span>';
                            }
                        } else if ($ttlAmtb == 0) {
                            $credit = '<span class="text-right text-success">0</span>';
                        } else {
                            $credit = '<span class="text-right text-success"></span>';
                        }

                        if (!empty($ttlAmtb)) {
                            if ($bm_win_type == 'Loss') {
                                $debit = '<span class="text-right text-danger">' . number_format($ttlAmtb, 2) . '</span>';
                            } else {
                                $debit = '<span class="text-right text-danger"></span>';
                            }
                        } else if ($ttlAmtb == 0) {
                            $debit = '<span class="text-right text-danger"></span>';
                        } else {
                            $debit = '<span class="text-right text-danger"></span>';
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

                        $data_arr[] = array(
                            "date" => $date,
                            "srno" => $srno,
                            "credit" => $credit,
                            "debit" => $debit,
                            "balance" => $balance,
                            "remark" => $remark
                        );
                        $bttl = $i;
                        $i++;
                    }
                }
            }
        }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $bttl,
            "iTotalDisplayRecords" => $bttl,
            "aaData" => $data_arr
        );

        echo json_encode($response);
        exit;
        //return $html;
    }

    public function back_getAccountPopup(Request $request)
    {
        $loginuser = User::where('id', $request->pid)->first();

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
//            ->whereBetween('created_at',[$fromdate,$todate])
                ->get();
        } else {
            $gmdata = MyBets::where('user_id', $loginuser->id)
                ->where('result_declare', 1)
                ->where('match_id', $mid)
                ->where('bet_type', $btyp)
//            ->whereBetween('created_at',[$fromdate,$todate])
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
}
