<?php

namespace App\Http\Controllers;

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
use App\setting;
use App\Match;
use App\UserExposureLog;
use App\UserHirarchy;
use App\MyBets;
use App\Sport;
use App\FancyResult;


class RestrictionController extends Controller
{
    public function suspend_pa(Request $request)
    {
        $user_id = $request->user_id;
        $pw = $request->password;
        $status = $request->status;
        $adminpass = Auth::user()->password;
        $data = User::where('id', $user_id)->first();

        if (empty($pw)) {
            return response()->json(array('result' => 'error'));
        }

        if (Hash::check($pw, $adminpass)) {
            if ($data->agent_level == 'PL') {
                User::where('id', $user_id)->update(['status' => $status]);
                //return response()->json(array('result'=> 'success'));
            } else {
                $x = $data->id;
                User::where('id', $x)->update(['status' => $status]);

                //$ans = $this->data($x,$status);
                $ans = $this->childdata1($x);
                foreach ($ans as $key => $value) {
                    //echo $status;
                    User::where('id', $value)->update(['status' => $status]);
                }
            }
            return response()->json(array('result' => 'success'));
        } else {
            return response()->json(array('result' => 'error'));
        }
    }

    function childdata1($id)
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

    function data($id, $status)
    {
        do {
            $subdata = User::where('parentid', $id)->get();
            foreach ($subdata as $key => $value) {
                User::where('id', $value->id)->update(['status' => $status]);
            }

            $last = User::orderBy('id', 'DESC')->first();
            $id++;
        } while ($id <= $last->id);
        return 'done';
    }

    function datacli($id)
    {
        $adata = array();
        do {
            $subdata = User::where('parentid', $id)->where('agent_level', '!=', 'PL')->get();
            $subdatacounta = User::where('parentid', $id)->where('agent_level', '!=', 'PL')->count();
            foreach ($subdata as $key => $value) {
                $adata[] = $value->id;
            }

            $last = User::orderBy('id', 'DESC')->first();
            $id++;
        } while ($subdatacounta != 0);
        return $adata;
    }

    function dataclient($id)
    {
        $adata = array();
        do {
            $subdata = User::where('parentid', $id)->get();
            $subdatacount = User::where('parentid', $id)->count();
            foreach ($subdata as $key => $value) {
                $adata[] = $value->id;
            }

            $last = User::orderBy('id', 'DESC')->first();
            $id++;
        } while ($subdatacount != 0);
        return $adata;
    }

    function backdata($id)
    {
        $getuser = Auth::user();

//        dd($getuser->id);
        $adata = array();
        do {
            $test = User::where('id', $id)->first();
            $adata[] = $test->id;
//            $first = User::orderBy('id', 'ASC')->first();
//            if($getuser->id!=$test->parentid)
//            echo $id;

        } while ($id = $test->parentid);

        return $adata;
    }

    /*function subuser($id)
    {
        echo $id;
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

    public function agentSubDetail(Request $request)
    {
        $user_id = $request->user_id;
        $search = $request->search;

        $getuser = Auth::user();

        if ($user_id == 0) {
            if ($getuser->agent_level == 'SL' && $getuser->list_client == '1') {
                $crumb = User::where('id', '1')->orderBy('user_name')->first();
            } else {
                $crumb = User::where('id', $getuser->id)->orderBy('user_name')->first();
            }
        } else {
            $crumb = User::where('id', $user_id)->orderBy('user_name')->first();
        }

        if(!empty($search) && $search!=null){
            $users = User::where('parentid', $crumb->id)->where(function ($q) use ($search){
                $q->where("user_name",'like', '%' . $search . '%')->orWhere("first_name",'like', '%' . $search . '%')->orWhere("last_name",'like', '%' . $search . '%');
            })->where("agent_level", "!=",  "PL")->orderBy('user_name')->paginate(10);
        }else {
            $users = User::where('parentid', $crumb->id)->where("agent_level", "!=", "PL")->orderBy('user_name')->paginate(10);
        }

        $html = '';
        $html1 = '';
        $html2 = '';
//        $passexp = '';

        $cumulative_pl_cli = 0;

        if($users->count() > 0) {
            foreach ($users as $key => $row) {
                // calculation
//            $totalClientBal = 0;
//            $totalAgentBal = 0;
//            $totalExposure = 0;
                $cumulative_pl = 0;
//            $total_Player_exposer = 0;
//            $total_ref_pl = 0;
//            $exposure_cli = 0;
//            $cumulative_pl_cli = 0;

                $x = $row->id;
                $sum_credit = 0;
                $credit_datamn = CreditReference::where('player_id', $row->id)->first();
                $sum_credit = $credit_datamn->credit;

//            $credit_datacli = CreditReference::where('player_id', $row->id)->select('remain_bal', 'exposure')->first();
//            $remain_bal_cli = '';

//            if (!empty($credit_datacli)) {
//                $remain_bal_cli = $credit_datacli->remain_bal;
//                $exposure_cli = $credit_datacli->exposure;
//            }
//            $cumulative_pl_profit_get = UserExposureLog::where('user_id', $row->id)->where('win_type', 'Profit')->where('bet_type', 'ODDS')->sum('profit');
//            $cumulative_pl_profit = UserExposureLog::where('user_id', $row->id)->where('win_type', 'Profit')->where('bet_type', '!=', 'ODDS')->sum('profit');
//            $cumulative_pl_loss = UserExposureLog::where('user_id', $row->id)->where('win_type', 'Loss')->sum('loss');
//            $cumu_n = 0;
//            $cumu_n = $cumulative_pl_profit_get * ($row->commission) / 100; /// added nnnn 18-10-2021
//            $cumuPL_n = $cumulative_pl_profit_get + $cumulative_pl_profit - $cumu_n;   ///added nnnn 18-10-2021

//            $cumulative_pl_cli = $cumuPL_n - $cumulative_pl_loss;
                //$cumulative_pl_cli=$cumulative_pl_profit-$cumulative_pl_loss; /// nnnn 18-10-2021

                $depParent = AgentController::userBalance($row->id);
                $calData = explode("~", $depParent);

//                $calData = [];
//                $calData[0] = 0;
//                $calData[1] = 0;
//                $calData[2] = 0;
//                $calData[3] = 0;

                $cumulative_pl_cli+=$calData[3];

//                echo __FILE__." at line ".__LINE__."<br>";echo "<pre>";print_r($calData);die();

                /*  foreach ($dataResult as $value) {
                      $subdata = User::where('id',$value)->first();
                      if($subdata->agent_level=='PL'){
                          $credit_data = CreditReference::where('player_id',$subdata->id)->select('remain_bal','exposure')->first();
                          if(!empty($credit_data)){
                              $totalExposure += $credit_data->exposure;
                          }
                      }else{

                          $credit_data = CreditReference::where('player_id',$subdata->id)->select('available_balance_for_D_W')->first();
                          if(!empty($credit_data)){
                              $totalAgentBal += $credit_data->available_balance_for_D_W;
                          }
                      }
                  }*/

                $credit_data = CreditReference::where('player_id', $row->id)->select('available_balance_for_D_W')->first();
                $availableBalance = '';
//            $total_calculated_available_balance = 0;

                if (!empty($credit_data)) {
                    $availableBalance = $credit_data->available_balance_for_D_W;
                }

//            $credit_data = CreditReference::where('player_id', $row->id)->select('remain_bal')->first();
//            $remain_bal = '';
//
//            if (!empty($credit_data)) {
//                $remain_bal = $credit_data->remain_bal;
//            }

                // end calculation

                if ($row->agent_level == 'SA') {
                    $color = 'orange-bg';
                } else if ($row->agent_level == 'AD') {
                    $color = 'pink-bg';
                } else if ($row->agent_level == 'SMDL') {
                    $color = 'green-bg';
                } else if ($row->agent_level == 'MDL') {
                    $color = 'yellow-bg';
                } else if ($row->agent_level == 'DL') {
                    $color = 'blue-bg';
                } else {
                    $color = 'red-bg';
                }

                {
                    $html .= '
                <tr>
                    <td class="align-L white-bg">';

                    $html .= '<a class="ico_account text-color-blue-light" id="' . $row->id . '"   onclick="subpagedata(this.id,1);">
                            <span class="' . $color . ' text-color-white">' . $row->agent_level . '</span>' . $row->user_name . ' [' . $row->first_name . ' ' . $row->last_name . ']
                        </a>';

                    $credit_data = CreditReference::where('player_id', $row->id)->first();
                    $credit = 0;
                    if (!empty($credit_data['credit'])) {
                        $credit = $credit_data['credit'];
                    }
                    $total_calculated_available_balance = $availableBalance + $calData[0] + $calData[1];
                    $html .= '</td>

                    <td class="white-bg"><a id="'.$row->id.'" data-credit="'.$credit.'"  class="openCreditpopup favor-set">'.$sum_credit.'</a></td>
                    <td class="white-bg">' . number_format($availableBalance, 2, '.', '') . '</td>
                    <td class="white-bg">' . number_format($calData[0], 2, '.', '') . '</td>
                    <td class="white-bg">' . number_format($calData[1], 2, '.', '') . '</td>
                    <td class="white-bg">' . number_format($total_calculated_available_balance, 2, '.', '') . '</td>';
                    $html .= '<td class="white-bg text-color-red" style="display:table-cell;">(' . number_format(abs($calData[2]), 2, '.', '') . ')</td>';
                    $refPL = $credit - $total_calculated_available_balance;
                    if ($refPL < 0) {
                        $class = "text-color-green";
                    } else {
                        $class = "text-color-red";
                    }
//                $total_ref_pl += $cumulative_pl;

                    if ($calData[3] >= 0) {
                        $cuClass = 'text-color-green';
                    } else {
                        $cuClass = 'text-color-red';
                    }

                    $html .= '<td class="white-bg ' . $class . '">(' . number_format(abs($refPL), 2, '.', '') . ')</td>
                    <td class=" ' . $cuClass . ' white-bg">(' . number_format(abs($calData[3]), 2, '.', '') . ')</td>

                    <td class="white-bg" style="display: table-cell;">';

                    if ($row->status == 'active') {
                        $html .= '<span class="status-active light-green-bg text-color-green"><span class="round-circle green-bg"></span>' . ucfirst(trans($row->status)) . '</span>';
                    }

                    if ($row->status == 'suspend') {
                        $html .= '<span class="status-suspended light-red-bg text-color-red"><span class="round-circle red-bg"></span>' . ucfirst(trans($row->status)) . '</span>';
                    }

                    if ($row->status == 'locked') {
                        $html .= '<span class="status-locked light-blue-bg-2 text-color-darkblue"><span class="round-circle darkblue-bg1"></span>' . ucfirst(trans($row->status)) . '</span>';
                    }

                    $html .= '</td>';

//                    if ($admin == $row->id) {
                        $html .= '
                        <td class="white-bg">
                            <ul class="action-ul">

                                <li><a class="grey-gradient-bg setting" data-toggle="modal" data-target="#myStatus" data-id="' . $row->id . '" data-username="' . $row->user_name . '" data-agent="' . $row->agent_level . '" data-status="' . $row->status . '"><img src="' . asset('asset/img/setting-icon.png') . '"></a></li>

                                <li><a class="grey-gradient-bg" href="changePass/' . $row->id . '"><img src="' . asset('asset/img/user-icon.png') . '"></a></li>
                            </ul>
                        </td>';
//                    } else {
//                        $html .= '
//                        <td class="white-bg">
//                            <ul class="action-ul">
//                                <li><a class="grey-gradient-bg" href="changePass/' . $row->id . '"><img src="' . asset('asset/img/user-icon.png') . '"></a></li>
//                            </ul>
//                        </td>';
//                    }
                    $html .= '</tr>';
                }
            }
        }else{
            $html.= "<tr><td colspan='12' class=\"align-L white-bg text-center\">No records found</td></tr>";
        }

        $adata = $this->backdata($crumb->id);
        $child = $this->childdata($getuser->id);
        sort($adata);

        $child[] = $getuser->id;

        foreach ($adata as $bread) {
            if(in_array($bread,$child)) {
                $finaldata = User::where('id', $bread)->first();
                $html1 .= '<li class="firstli" id=' . $finaldata['id'] . '>';
                if ($finaldata['agent_level'] == 'COM') {
                    $html1 .= '
                        <a href="home">
                        <span class="blue-bg text-color-white">' . $finaldata->agent_level . '</span>
                        <strong id=' . $finaldata->id . '>' . $finaldata->user_name . '</strong>
                        </a>
                        <img src="' . asset('asset/img/arrow-right2.png') . '">
                    </li>';
                } else {
                    $html1 .= '
                        <a>
                            <span class="blue-bg text-color-white">' . $finaldata->agent_level . '</span>
                            <strong id=' . $finaldata->id . '  onclick="subpagedata(this.id,1);">' . $finaldata->user_name . '</strong>
                        </a>
                        <img src="' . asset('asset/img/arrow-right2.png') . '">
                    </li>';
                }
            }
        }

        $pagination=$users->links()->render();

        if ($cumulative_pl_cli <= 0) {
            $myPl = 'text-color-green';
        } else {
            $myPl = 'text-color-red';
        }

        return response()->json(array(
            'html' => $html,
            'breadcurm' => $html1,
            'pagination' => $pagination,
            'total_ref_pl' => ($cumulative_pl_cli),
            'myPl' => $myPl
        ));

//        return $html . '~~' . $html1 . '~~' . $html2;
    }

    public function agentSubBackDetail(Request $request)
    {
        $user_id = $request->user_id;
        $search = $request->search;

        $getuser = Auth::user();
        if ($user_id == 0) {
            $crumb = User::where('id', $getuser->id)->first();
        } else {
            $crumb = User::where('id', $user_id)->first();
        }

        $website = UsersAccount::getWebsite();

        $html2 = '';

        if(!empty($search) && $search!=null){
            $users = User::where('parentid', $crumb->id)->where(function ($q) use ($search){
                $q->where("user_name",'like', '%' . $search . '%')->orWhere("first_name",'like', '%' . $search . '%')->orWhere("last_name",'like', '%' . $search . '%');
            })->where("agent_level", "PL")->orderBy('user_name')->paginate(10);
        }else {
            $users = User::where('parentid', $crumb->id)->where("agent_level", "PL")->orderBy('user_name')->paginate(10);
        }
        $total_ref_pl = 0;

        if($users->count() > 0) {
            foreach ($users as $key => $row) {

                $totalClientBal = 0;
                $totalAgentBal = 0;
//            $totalExposure = 0;
                $cumulative_pl = 0;
                $total_Player_exposer = 0;
//            $cumulative_pl_cli = 0;
                // calculation

//            $x = $row->id;
//            $sum_credit = 0;
                $credit_datamn = CreditReference::where('player_id', $row->id)->first();
                $sum_credit = $credit_datamn->credit;

//            $depParent = AgentController::userBalance($row->id);
//            $calData = explode("~", $depParent);
                $credit_data = CreditReference::where('player_id', $row->id)->select('available_balance_for_D_W','exposure')->first();
                $availableBalance = '';
                $total_calculated_available_balance = 0;
                if (!empty($credit_data)) {
                    $availableBalance = $credit_data->available_balance_for_D_W;
                    $total_Player_exposer = $credit_data->exposure;
                }

                $credit_data = CreditReference::where('player_id', $row->id)->select('remain_bal')->first();
//            $remain_bal = '';
//            if (!empty($credit_data)) {
//                $remain_bal = $credit_data->remain_bal;
//            }
                $cumulative_pl_profit_get = UserExposureLog::where('user_id', $row->id)->where('win_type', 'Profit')->where('bet_type', 'ODDS')->sum('profit');
                $cumulative_pl_profit = UserExposureLog::where('user_id', $row->id)->where('win_type', 'Profit')->where('bet_type', '!=', 'ODDS')->sum('profit');
                $cumulative_pl_loss = UserExposureLog::where('user_id', $row->id)->where('win_type', 'Loss')->sum('loss');
//            $cumu_n = 0;
                $cumu_n = $cumulative_pl_profit_get * ($row->commission) / 100;
                $cumuPL_n = $cumulative_pl_profit_get + $cumulative_pl_profit - $cumu_n;   ///added nnnn 18-10-2021

                $cumulative_pl2 = $cumuPL_n - $cumulative_pl_loss;

                $cumulative_pl_cli = $cumulative_pl2;

                $color = 'red-bg';

                $total_ref_pl += $cumulative_pl;

                if ($row->agent_level == 'PL') {
                    $html2 .= '<tr>
                    <td class="align-L white-bg">';

                    $credit_data = CreditReference::where('player_id', $row->id)->first();
                    $credit = 0;
                    if (!empty($credit_data['credit'])) {
                        $credit = $credit_data['credit'];
                    }

                    $html2 .= '<a class="ico_account text-color-blue-light" id="' . $row->id . '">
                            <span class="' . $color . ' text-color-white">' . $row->agent_level . '</span>' . $row->user_name . ' [' . $row->first_name . ' ' . $row->last_name . ']
                        </a>
                    </td>
                    <td class="white-bg"><a id="'.$row->id.'" data-credit="'.$credit.'"  class="openCreditpopup favor-set">'.$sum_credit.'</a></td>
                    <td class="white-bg">' . number_format($availableBalance, 2, '.', '') . '</td>';
                    $html2 .= '<td class="white-bg text-color-red" style="display:table-cell;">(' . number_format($total_Player_exposer, 2, '.', '') . ')</td>';


                    $total_calculated_available_balance = $availableBalance + $totalAgentBal + $totalClientBal;

                    $refPL = $credit - $total_calculated_available_balance;
                    if ($refPL < 0) {
                        $class = "text-color-green";
                    } else {
                        $class = "text-color-red";
                    }
                    $total_ref_pl += $cumulative_pl_cli;
                    if ($cumulative_pl_cli < 0) {
                        $cuClass = 'text-color-red';
                    } else {
                        $cuClass = 'text-color-green';
                    }
                    $html2 .= '<td class="white-bg ' . $refPL .' '.$class. '">(' . number_format(abs($refPL), 2, '.', '') . ')</td>
                    <td class="' . $cuClass . ' white-bg">(' . number_format(abs($cumulative_pl_cli), 2, '.', '') . ')</td>
                    <td class="white-bg" style="display: table-cell;">
                    ';

                    if ($row->status == 'active') {
                        $html2 .= '<span class="status-active light-green-bg text-color-green"><span class="round-circle green-bg"></span>' . ucfirst(trans($row->status)) . '</span>';
                    }

                    if ($row->status == 'suspend') {
                        $html2 .= '<span class="status-suspended light-red-bg text-color-red"><span class="round-circle red-bg"></span>' . ucfirst(trans($row->status)) . '</span>';
                    }

                    if ($row->status == 'locked') {
                        $html2 .= '<span class="status-locked light-blue-bg-2 text-color-darkblue"><span class="round-circle darkblue-bg1"></span>' . ucfirst(trans($row->status)) . '</span>';
                    }

                    $html2 .= '</td>';

//                if ($admin == $row->id) {
                    $html2 .= '
                        <td class="white-bg">
                            <ul class="action-ul">

                                <li><a class="grey-gradient-bg setting" data-toggle="modal" data-target="#myStatus" data-id="' . $row->id . '" data-username="' . $row->user_name . '" data-agent="' . $row->agent_level . '" data-status="' . $row->status . '"><img src="' . asset('asset/img/setting-icon.png') . '"></a></li>

                                <li><a class="grey-gradient-bg" href="' . route('changePass', $row->id) . '"><img src="' . asset('asset/img/user-icon.png') . '"></a></li>
                                <li><a class="grey-gradient-bg" href="' . route('betHistoryBack', $row->id) . '"><img src="' . asset('asset/img/updown-arrow-icon.png') . '"></a></li>
                                <li><a class="grey-gradient-bg" href="' . route('betHistoryPLBack', $row->id) . '"><img src="' . asset('asset/img/history-icon.png') . '"></a></li>
                            </ul>
                        </td>';
//                } else {
//                    $html2 .= '
//                        <td class="white-bg">
//                            <ul class="action-ul">
//                                <li><a class="grey-gradient-bg" href="changePass/' . $row->id . '"><img src="' . asset('asset/img/user-icon.png') . '"></a></li>
//                            </ul>
//                        </td>';
//                }

                    $html2 .= '</tr>';
                }
            }
        }else{
            $html2.= "<tr><td colspan='8' class=\"align-L white-bg text-center\">No records found</td></tr>";
        }

        if ($total_ref_pl <= 0) {
            $myPl = 'text-color-green';
        } else {
            $myPl = 'text-color-red';
        }

//        dd($users->render());

        $pagination=$users->links()->render();

        return response()->json(array("html"=>$html2,'total_ref_pl'=>($total_ref_pl),'myPl'=>$myPl,'pagination'=>$pagination));

    }

    public function maintenance()
    {
        $mntnc = setting::first();
        $msg = $mntnc->maintanence_msg;
        return view('backpanel/maintanence', compact('msg'));
    }

    public function userWiseBlock(Request $request)
    {
        $matchid = $request->matchid;
        $event_id = $request->event_id;
        $checks = $request->checks;
        $mid = $request->mid;
        Match::where(['match_id' => $matchid, 'event_id' => $event_id])
            ->update(['user_list' => $checks]);
        return response()->json(array('success' => 'success'));
    }
}
