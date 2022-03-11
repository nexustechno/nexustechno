<?php

namespace App\Http\Controllers;

use App\UsersAccount;
use Illuminate\Http\Request;
use App\setting;
use App\User;
use App\Match;
use App\Sport;
use Auth;
use Hash;
use Illuminate\Support\Facades\Log;
use Redirect;
use Session;
use App\CreditReference;
use Carbon\Carbon;
use App\UserDeposit;
use App\MyBets;
use App\FancyResult;
use App\ManageTv;
use DB;
use App\UserExposureLog;
use App\SocialMedia;
use App\Website;
use App\Banner;
use App\UserHirarchy;

class SettingController extends Controller
{
    public function index()
    {
        $setting = setting::latest('id')->first();
        return view('backpanel/message', compact('setting'));
    }

    function dataAllParent($id)
    {
        do {
            $subdata = User::where('parentid', $id)->get();
            foreach ($subdata as $key => $value) {
            }
            $last = User::orderBy('id', 'DESC')->first();
            $id++;
        } while ($id <= $last->id);
        return $id;
    }

    public function userBet($id)
    {
        $user = User::find($id);
        $getresult = MyBets::where('user_id', $user->id)->where('result_declare', 0)->where('isDeleted', 0)->latest()->get();
        //echo "<pre>";print_r($getresult);echo "<pre>";exit;
        return view('backpanel/user-bets', compact('user', 'getresult', 'id'));
    }

    public function addBanner(Request $request)
    {
        if ($request->hasFile('banner_image')) {
            $imagebanner = $request->file('banner_image');
            $namebanner = $imagebanner->getClientOriginalName();
            $destinationPathfevicon = public_path('/asset/upload');
            $imagebanner->move($destinationPathfevicon, $namebanner);
            $data['banner_image'] = $namebanner;
        }

        $data['banner_name'] = $request->banner_name;
        Banner::create($data);

        return redirect()->route('banner')->with('message', 'Banner added successfully');
    }

    public function editBanner($id)
    {
        $banner = Banner::find($id);
        return view('backpanel/editBanner', compact('banner'));
    }

    public function updatebanner(Request $request, $id)
    {
        $banner = Banner::find($id);

        if ($request->hasFile('banner_image')) {
            $imagebanner = $request->file('banner_image');
            $namebanner = $imagebanner->getClientOriginalName();
            $destinationPathfevicon = public_path('/asset/upload');
            $imagebanner->move($destinationPathfevicon, $namebanner);
            $setimage = $namebanner;
        } else {
            $setimage = $request->old_bannerImage;
        }

        $banner->banner_name = $request->banner_name;
        $banner->banner_image = $setimage;
        $banner->update();

        return redirect()->route('banner')->with('message', 'Banner update successfully');
    }

    public function match_history()
    {
        $matchList = Match::where('winner', '!=', null)->orderBy('match_date', 'DESC')->get();
        return view('backpanel/match_history', compact('matchList'));
    }

    public function matchHistoryData(Request $request)
    {
        //echo"hi";exit;
        $mname = $request->val;

        $html = '';
        $html1 = '';
        if ($mname == 'cricket') {
            $matchList = Match::where('winner', '!=', null)->where('sports_id', 4)->orderBy('match_date', 'DESC')->get();
        } else if ($mname == 'tennis') {
            $matchList = Match::where('winner', '!=', null)->where('sports_id', 2)->orderBy('match_date', 'DESC')->get();
        } else if ($mname == 'soccer') {
            $matchList = Match::where('winner', '!=', null)->where('sports_id', 1)->orderBy('match_date', 'DESC')->get();
        }

        //echo"<pre>";print_r($matchList); exit;

        if (!empty($matchList)) {
            $count = 1;

            $html1 .= '' . $mname . '';

            foreach ($matchList as $key => $match) {
                if ($match->sports_id == 4) {
                    $sport = 'Cricket';
                } elseif ($match->sports_id == 2) {
                    $sport = 'Tennis';
                } elseif ($match->sports_id == 1) {
                    $sport = 'Soccer';
                }

                if ($match->winner == 'TIE') {
                    $m_winner = 'Cancel';
                } else {
                    $m_winner = $match->winner;
                }

                $html .= '<tr class="white-bg">
	                <td>' . $count . '</td>
	                <td>' . date("d/m/Y H:i:s", strtotime($match->match_date)) . '</td>
	                <td>' . $sport . '</td>
	                <td>' . $match->match_name . '</td>
	                <td>' . $match->match_id . '</td>
	                <td>' . $match->event_id . '</td>

	                <td>' . $m_winner . '</td>
	                <td>
	                    <a href="javascript:void(0)" data-id="' . $match->id . '" onclick="resultRollbackMatch(this);" class="text-color-blue">RESULT ROLLBACK</a>
	                </td>
	                <td> <a href="matchuser/' . $match->id . '" class="text-color-blue-light">BET</a></td>
	            </tr>';
                $count++;
            }

        } else {
            $html .= '<h3>No Match Found</h3>';
        }

        // print_r($html);
        // print_r($html1);
        // exit;

        return $html . "~~" . $html1;

    }

    public function matchHistoryCricket(Request $request)
    {
        $matchList = Match::where('winner', '!=', null)->where('sports_id', 4)->orderBy('match_date', 'DESC')->get();
        $html = '';
        $count = 1;
        foreach ($matchList as $key => $match) {
            if ($match->sports_id == 4) {
                $sport = 'Cricket';
            } elseif ($match->sports_id == 2) {
                $sport = 'Tennis';
            } elseif ($match->sports_id == 1) {
                $sport = 'Soccer';
            }

            if ($match->winner == 'TIE') {
                $m_winner = 'Cancel';
            } else {
                $m_winner = $match->winner;
            }

            $html .= '<tr class="white-bg">
                <td>' . $count . '</td>
                <td>' . date("d/m/Y H:i:s", strtotime($match->match_date)) . '</td>
                <td>' . $sport . '</td>
                <td>' . $match->match_name . '</td>
                <td>' . $match->match_id . '</td>
                <td>' . $match->event_id . '</td>

                <td>' . $m_winner . '</td>
                <td>
                    <a href="javascript:void(0)" data-id="' . $match->id . '" onclick="resultRollbackMatch(this);" class="text-color-blue">RESULT ROLLBACK</a>
                </td>
                <td> <a href="matchuser/' . $match->id . '" class="text-color-blue-light">BET</a></td>
            </tr>';
            $count++;
        }
        return $html;
    }

    public function matchuser($id)
    {
        $matchData = Match::where('id', $id)->first();
        $betdata = MyBets::where('match_id', $matchData->event_id)->where('bet_type', '!=', 'session')->get();
        return view('backpanel/matchuser', compact('betdata'));
    }

    public function fancy_history()
    {
        $sports = Sport::where('status', 'active')->where('sId', '4')->get();
//        $matchList = Match::orderBy('match_date', 'DESC')->get();
        return view('backpanel/fancy_history', compact('sports'));
    }

    public static function GetAllParentofPlayer($pid)
    {
        $parent = array();
        $subdata = User::where(['id' => $pid])->first();
        $id = $subdata->parentid;

        if ($id > 1) {
            $parent[] = $id;
            do {
                $subdata = User::where(['id' => $id])->first();
                $id = $subdata->parentid;
                $parent[] = $id;
            } while ($id > 1);
            return json_encode($parent);
        } else
            return json_encode($parent);


    }

    /*public function getFancyBetResult($fancyname,$matchid,$eventid,$id,$result)
	{
		$mytotal=0; $total_expo_amount=0;
		$my_placed_bets = MyBets::where('match_id',$eventid)->where('user_id',$id)->where('team_name',$fancyname)->where('isDeleted',0)->where('result_declare',0)->get();

		if(sizeof($my_placed_bets)>0)
		{
			foreach($my_placed_bets as $bet)
			{
				if($bet->bet_side=='back')
				{
					if($bet->bet_odds<=$result)
					{
						$mytotal=$mytotal+$bet->bet_profit;
						//$mytotal=$mytotal1+$bet->bet_amount;
					}
					else if($bet->bet_odds>$result)
					{
						$mytotal=$mytotal-$bet->exposureAmt;
					}
				}
				else if($bet->bet_side=='lay')
				{
					if($bet->bet_odds>$result)
					{
						$mytotal=$mytotal+$bet->bet_profit;
						//$mytotal=$mytotal2+$bet->bet_amount;
					}
					else if($bet->bet_odds<=$result)
					{
						$mytotal=$mytotal-$bet->exposureAmt;
					}
				}
				$total_expo_amount=$total_expo_amount+$bet->exposureAmt;
			}
		}

		if($mytotal>0)
		{

			$is_won=1;
			$betModel = new UserExposureLog();
			$betModel->match_id = $matchid;
			$betModel->user_id = $id;
			$betModel->bet_type = 'SESSION';
			$betModel->profit = $mytotal;
			if($is_won==1)
				$betModel->win_type = 'Profit';
			else
				$betModel->win_type = 'Loss';
			$betModel->fancy_name=$fancyname;
			$check=$betModel->save();
			if($check)
			{
				if($is_won==1)
				{
					$creditref=CreditReference::where(['player_id'=>$id])->first();
					$exposer=$creditref->exposure-abs($total_expo_amount);
					$balance=$creditref->available_balance_for_D_W+abs($total_expo_amount)+$mytotal;

					$remain_balance=$creditref->remain_bal+$mytotal;

					$upd=CreditReference::find($creditref['id']);
					$upd->exposure = $exposer;
					$upd->available_balance_for_D_W =$balance;
					$upd->remain_bal =$remain_balance;
					$update_=$upd->update();

					if($update_)
					{
						$parentid=self::GetAllParentofPlayer($id);
						$parentid=json_decode($parentid);
						if(!empty($parentid))
						{
							for($i=0;$i<sizeof($parentid);$i++)
							{
								$pid=$parentid[$i];
								if($pid!=1)
								{
									$creditref_bal=CreditReference::where(['player_id'=>$pid])->first();
									$bal=$creditref_bal->remain_bal;
									$remain_balance_=$bal+$mytotal;
									$upd_=CreditReference::find($creditref_bal->id);
									$upd_->remain_bal =$remain_balance_;
									$update_parent=$upd_->update();
								}
							}
						}
					}
				}
				else
				{
					$creditref=CreditReference::where(['player_id'=>$id])->first();
					$exposer=$creditref->exposure-abs($total_expo_amount);
					$balance=$creditref->available_balance_for_D_W;
					$remain_balance=$creditref->remain_bal-abs($total_expo_amount);

					$upd=CreditReference::find($creditref['id']);
					$upd->exposure = $exposer;
					$upd->available_balance_for_D_W =$balance;
					$upd->remain_bal =$remain_balance;
					$update_=$upd->update();
					if($update_)
					{
						$parentid=self::GetAllParentofPlayer($id);
						$parentid=json_decode($parentid);
						if(!empty($parentid))
						{
							for($i=0;$i<sizeof($parentid);$i++)
							{
								$pid=$parentid[$i];
								if($pid!=1)
								{
									$creditref_bal=CreditReference::where(['player_id'=>$pid])->get();
									$bal=$creditref_bal->remain_bal;
									$remain_balance_=$bal-abs($total_expo_amount);
									$upd_=CreditReference::find($creditref_bal->id);
									$upd_->remain_bal =$remain_balance_;
									$update_parent=$upd_->update();
								}
							}
						}
					}
				}

				//calculating admin balance
				$admin_tran=UserExposureLog::where('match_id',$matchid)->where('bet_type','SESSION')->where('fancy_name',$fancyname)->where('user_id',$id)->get();
				$admin_profit=0;
				$admin_loss=0;
				foreach($admin_tran as $trans)
				{
					if($trans->profit!='')
					{
						$admin_loss+=$trans->profit;
					}
					else if($trans->loss!='')
					{
						$admin_profit+=abs($trans->loss);
					}
				}
				$settings = setting::latest('id')->first();
				$adm_balance=$settings->balance;
				$new_balance=$adm_balance+$admin_profit-$admin_loss;

				$adminData = setting::find($settings->id);
				$adminData->balance=$new_balance;
				$adminData->update();
			}
		}
		else
		{

			$is_won=0;
			$betModel = new UserExposureLog();
			$betModel->match_id = $matchid;
			$betModel->user_id = $id;
			$betModel->bet_type = 'SESSION';
			$betModel->loss = abs($mytotal);
			$betModel->fancy_name=$fancyname;
			if($is_won==1)
				$betModel->win_type = 'Profit';
			else
				$betModel->win_type = 'Loss';
			$check=$betModel->save();
			if($check)
			{
				if($is_won==0)
				{
					$creditref=CreditReference::where(['player_id'=>$id])->first();
					$exposer=$creditref->exposure-abs($total_expo_amount);
					$balance=$creditref->available_balance_for_D_W;
					$remain_balance=$creditref->remain_bal-abs($total_expo_amount);

					$upd=CreditReference::find($creditref['id']);
					$upd->exposure = $exposer;
					$upd->available_balance_for_D_W =$balance;
					$upd->remain_bal =$remain_balance;
					$update_=$upd->update();
					if($update_)
					{
						$parentid=self::GetAllParentofPlayer($id);
						$parentid=json_decode($parentid);
						if(!empty($parentid))
						{
							for($i=0;$i<sizeof($parentid);$i++)
							{
								$pid=$parentid[$i];
								if($pid!=1)
								{
									$creditref_bal=CreditReference::where(['player_id'=>$pid])->first();
									$bal=$creditref_bal->remain_bal;
									$remain_balance_=$bal-abs($total_expo_amount);
									$upd_=CreditReference::find($creditref_bal->id);
									$upd_->remain_bal =$remain_balance_;
									$update_parent=$upd_->update();
								}
							}
						}
					}
				}
				else
				{
					$creditref=CreditReference::where(['player_id'=>$id])->first();
					$exposer=$creditref->exposure-abs($total_expo_amount);
					$balance=$creditref->available_balance_for_D_W+abs($total_expo_amount)+$mytotal;
					$remain_balance=$creditref->remain_bal+$mytotal;

					$upd=CreditReference::find($creditref['id']);
					$upd->exposure = $exposer;
					$upd->available_balance_for_D_W =$balance;
					$upd->remain_bal =$remain_balance;
					$update_=$upd->update();
					if($update_)
					{
						$parentid=self::GetAllParentofPlayer($id);
						$parentid=json_decode($parentid);
						if(!empty($parentid))
						{
							for($i=0;$i<sizeof($parentid);$i++)
							{
								$pid=$parentid[$i];
								if($pid!=1)
								{
									$creditref_bal=CreditReference::where(['player_id'=>$pid])->first();
									$bal=$creditref_bal->remain_bal;
									$remain_balance_=$bal+$mytotal;
									$upd_=CreditReference::find($creditref_bal->id);
									$upd_->remain_bal =$remain_balance_;
									$update_parent=$upd_->update();
								}
							}
						}
					}
				}

				//calculating admin balance
				$admin_tran=UserExposureLog::where('match_id',$matchid)->where('bet_type','SESSION')->where('fancy_name',$fancyname)->where('user_id',$id)->get();
				$admin_profit=0;
				$admin_loss=0;
				foreach($admin_tran as $trans)
				{
					if($trans->profit!='' && $trans->win_type=='Profit')
					{
						$admin_loss+=$trans->profit;
					}
					else if($trans->loss!='' && $trans->win_type=='Loss')
					{
						$admin_profit+=abs($trans->loss);
					}
				}
				$settings = setting::latest('id')->first();
				$adm_balance=$settings->balance;
				$new_balance=$adm_balance+$admin_profit-$admin_loss;
				$adminData = setting::find($settings->id);
				$adminData->balance=$new_balance;
				$adminData->update();
			}
		}
	}*/


    public function getFancyBetPositionForMatchDeclare($fancyName, $mid, $eventid, $uid, $fancy_result)
    {
        $my_placed_bets = MyBets::where('user_id', $uid)->where('match_id', $eventid)->where('team_name', @$fancyName)->where('bet_type', 'SESSION')->where('isDeleted', 0)->where('result_declare', 0)->orderBy('created_at', 'asc')->get();
        $abc = sizeof($my_placed_bets);
        $return_final_exposure = '';
        $profit_loss = "";
        $final_exposer_deduct = '';
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

            for ($i = 0; $i <= $max_val + 1000; ++$i) {
                $new = $i;
                $newArr[] = $new;
            }

            $run_arr = array();
            $run_arr = $newArr;

            $bet_chk = '';
            $bet_model = '';
            $final_exposer = '';
            $expo_array = array();
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
                //echo $final_exposer;
                $expo_array[] = $final_exposer;
                if ($bet_deduct_amt > 0) {
                    if ($fancy_result == $run_arr[$kk]) {
                        //echo $run_arr[$kk];
                        $return_final_exposure = $bet_deduct_amt;
                        $profit_loss = "Profit";
                        $final_exposer_deduct = $final_exposer;
                    }
                } else {
                    if ($fancy_result == $run_arr[$kk]) {
                        //echo $run_arr[$kk];
                        $return_final_exposure = abs($bet_deduct_amt);
                        $profit_loss = "Loss";
                        $final_exposer_deduct = $final_exposer;
                    }

                }

            }
        }
        //print_r($expo_array);

        $final_exposer_deduct = min($expo_array);
        //exit;
        return $return_final_exposure . "~~" . $profit_loss . "~~" . $final_exposer_deduct;

    }

    public function getFancyBetPositionForMatchDeclare2($fancyName, $mid, $eventid, $uid, $fancy_result)
    {
        $my_placed_bets = MyBets::where('user_id', $uid)->where('match_id', $eventid)->where('team_name', @$fancyName)->where('bet_type', 'SESSION')->where('isDeleted', 0)->where('result_declare', 1)->orderBy('created_at', 'asc')->get();
        $abc = sizeof($my_placed_bets);
        $return_final_exposure = '';
        $profit_loss = "";
        $final_exposer_deduct = '';
        $final_exposer = '';
        $expo_array = array();
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

            for ($i = 0; $i <= $max_val + 1000; ++$i) {
                $new = $i;
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
                //echo $final_exposer;
                $expo_array[] = $final_exposer;
                if ($bet_deduct_amt > 0) {
                    if ($fancy_result == $run_arr[$kk]) {
                        //echo $run_arr[$kk];
                        $return_final_exposure = $bet_deduct_amt;
                        $profit_loss = "Profit";
                        $final_exposer_deduct = $final_exposer;
                    }
                } else {
                    if ($fancy_result == $run_arr[$kk]) {
                        //echo $run_arr[$kk];
                        $return_final_exposure = abs($bet_deduct_amt);
                        $profit_loss = "Loss";
                        $final_exposer_deduct = $final_exposer;
                    }

                }

            }
        }
        //print_r($expo_array);

        $final_exposer_deduct = min($expo_array);
        //exit;
        return $return_final_exposure . "~~" . $profit_loss . "~~" . $final_exposer_deduct;

    }

    public function getFancyBetResult($fancyname, $matchid, $eventid, $id, $result)
    {

        $masterAdmin = User::where("agent_level",'COM')->first();
        $mytotal = 0;
        $total_expo_amount = 0;
        $my_placed_bets = MyBets::where('match_id', $eventid)->where('user_id', $id)
            ->where('team_name', $fancyname)->where('isDeleted', 0)
            ->where('result_declare', 0)->get();
        $mid = '';
        if (sizeof($my_placed_bets) > 0) {

            $total_calculated_position = SELF::getFancyBetPositionForMatchDeclare($fancyname, $mid, $eventid, $id, $result);
            //echo $total_calculated_position;
            //exit;
            $check_value = explode("~~", $total_calculated_position);
            $expamt = (int)$check_value[0];
            $proift_or_loss = $check_value[1];
            if ($proift_or_loss == 'Loss' && $expamt != 0 && $expamt != '')
                $mytotal = $expamt * (-1);
            else if ($proift_or_loss == 'Loss' && $expamt == 0 && $expamt == '')
                $mytotal = 0;
            else
                $mytotal = (int)$expamt;

            // added code for back and lay on same fancy exposer get minuse :: Jeet -   07-03-2022
            $total_expo_amount = (int)$check_value[2];
//            if($mytotal > 0){
//                $total_expo_amount = 0;
//                $mytotal = abs($mytotal-$total_expo_amount);
//            }


//            dd($total_expo_amount,$mytotal);

            //echo $total_calculated_position;
            //exit;
        }
        if ($result == 'cancel')
            $mytotal = 0;

        if ($mytotal > 0) /// profit
        {

            $is_won = 1;
            $betModel = new UserExposureLog();
            $betModel->match_id = $matchid;
            $betModel->user_id = $id;
            $betModel->bet_type = 'SESSION';
            $betModel->profit = $mytotal;
            if ($is_won == 1)
                $betModel->win_type = 'Profit';
            else
                $betModel->win_type = 'Loss';
            $betModel->fancy_name = $fancyname;
            $check = $betModel->save();

            if ($check) {
                if ($is_won == 1) {
                    $creditref = CreditReference::where(['player_id' => $id])->first();
                    $exposer = $creditref->exposure - abs($total_expo_amount);
                    $balance = $creditref->available_balance_for_D_W + abs($total_expo_amount) + $mytotal;

                    $available_balance = $creditref->available_balance_for_D_W;
                    UsersAccount::create([
                        'user_id' => $id,
                        'from_user_id' => $id,
                        'to_user_id' => $id,
                        'credit_amount' => $mytotal,
                        'balance' => $available_balance,
                        'closing_balance' => $available_balance + $mytotal,
                        'remark' => "",
                        "match_id" => $matchid,
                        'bet_user_id' => $id,
                        'user_exposure_log_id' => $betModel->id
                    ]);

                    $remain_balance = $creditref->remain_bal + $mytotal;

                    $upd = CreditReference::find($creditref['id']);
                    $upd->exposure = $exposer;
                    $upd->available_balance_for_D_W = $balance;
                    $upd->remain_bal = $remain_balance;
                    $update_ = $upd->update();
                    if ($update_) {
                        $parentid = self::GetAllParentofPlayer($id);

                        $parentid = json_decode($parentid);
                        if (!empty($parentid)) {
                            for ($i = 0; $i < sizeof($parentid); $i++) {
                                $pid = $parentid[$i];
                                if ($pid != 1) {
                                    $creditref_bal = CreditReference::where(['player_id' => $pid])->first();
                                    $bal = $creditref_bal->remain_bal;
                                    $remain_balance_ = $bal + $mytotal;
                                    $upd_ = CreditReference::find($creditref_bal->id);
                                    $upd_->remain_bal = $remain_balance_;
                                    $update_parent = $upd_->update();
                                }
                            }
                        }
                    }
                }
                else {
                    $creditref = CreditReference::where(['player_id' => $id])->first();
                    $exposer = $creditref->exposure - abs($total_expo_amount);
                    $balance = $creditref->available_balance_for_D_W;
                    $remain_balance = $creditref->remain_bal - abs($total_expo_amount);

                    $available_balance = $creditref->available_balance_for_D_W;
                    UsersAccount::create([
                        'user_id' => $id,
                        'from_user_id' => $id,
                        'to_user_id' => $id,
                        'debit_amount' => abs($total_expo_amount),
                        'balance' => $available_balance,
                        'closing_balance' => $available_balance - abs($total_expo_amount),
                        'remark' => "",
                        "match_id" => $matchid,
                        'bet_user_id' => $id,
                        'user_exposure_log_id' => $betModel->id
                    ]);

                    $upd = CreditReference::find($creditref['id']);
                    $upd->exposure = $exposer;
                    $upd->available_balance_for_D_W = $balance;
                    $upd->remain_bal = $remain_balance;
                    $update_ = $upd->update();
                    if ($update_) {
                        $parentid = self::GetAllParentofPlayer($id);

                        $parentid = json_decode($parentid);
                        if (!empty($parentid)) {
                            for ($i = 0; $i < sizeof($parentid); $i++) {
                                $pid = $parentid[$i];
                                if ($pid != 1) {
                                    $creditref_bal = CreditReference::where(['player_id' => $pid])->get();
                                    $bal = $creditref_bal->remain_bal;
                                    $remain_balance_ = $bal - abs($total_expo_amount);
                                    $upd_ = CreditReference::find($creditref_bal->id);
                                    $upd_->remain_bal = $remain_balance_;
                                    $update_parent = $upd_->update();
                                }
                            }
                        }
                    }
                }


                //calculating admin balance
                $admin_tran = UserExposureLog::where('match_id', $matchid)->where('bet_type', 'SESSION')->where('fancy_name', $fancyname)->where('user_id', $id)->get();
                $admin_profit = 0;
                $admin_loss = 0;

                $settings = setting::latest('id')->first();
                $adm_balance = $settings->balance;
                $available_balance = $settings->balance;
                foreach ($admin_tran as $trans) {
                    if ($trans->profit != '') {
                        $admin_loss += $trans->profit;

                        UsersAccount::create([
                            'user_id' => $masterAdmin->id,
                            'from_user_id' => $id,
                            'to_user_id' => $masterAdmin->id,
                            'debit_amount' => $trans->profit,
                            'balance' => $available_balance,
                            'closing_balance' => $available_balance - $trans->profit,
                            'remark' => "",
                            'match_id' => $matchid,
                            'bet_user_id' => $id,
                            'user_exposure_log_id' => $trans->id
                        ]);
                        $available_balance = $available_balance - $trans->profit;

                    } else if ($trans->loss != '') {
                        $admin_profit += abs($trans->loss);

                        UsersAccount::create([
                            'user_id' => $masterAdmin->id,
                            'from_user_id' => $id,
                            'to_user_id' => $masterAdmin->id,
                            'credit_amount' => $trans->profit,
                            'balance' => $available_balance,
                            'closing_balance' => $available_balance + $trans->profit,
                            'remark' => "",
                            'match_id' => $matchid,
                            'bet_user_id' => $id,
                            'user_exposure_log_id' => $trans->id
                        ]);
                        $available_balance = $available_balance + $trans->profit;
                    }
                }

                $new_balance = $adm_balance + $admin_profit - $admin_loss;

                $adminData = setting::find($settings->id);
                $adminData->balance = $new_balance;
                $adminData->update();

            }
        }
        else if ($mytotal < 0) // loss
        {

            $is_won = 0;
            $betModel = new UserExposureLog();
            $betModel->match_id = $matchid;
            $betModel->user_id = $id;
            $betModel->bet_type = 'SESSION';
            $betModel->loss = abs($mytotal);
            $betModel->fancy_name = $fancyname;
            if ($is_won == 1)
                $betModel->win_type = 'Profit';
            else
                $betModel->win_type = 'Loss';
            $check = $betModel->save();
            if ($check) {
                if ($is_won == 0) {
                    $creditref = CreditReference::where(['player_id' => $id])->first();
                    $exposer = $creditref->exposure - abs($total_expo_amount);
                    $balance = $creditref->available_balance_for_D_W + abs($total_expo_amount) - abs($expamt);
                    //$remain_balance=$creditref->remain_bal-abs($total_expo_amount);   ///nnn 20-10-2021
                    $remain_balance = $creditref->remain_bal - abs($expamt);

                    $available_balance = $creditref->available_balance_for_D_W;
                    UsersAccount::create([
                        'user_id' => $id,
                        'from_user_id' => $id,
                        'to_user_id' => $id,
                        'debit_amount' => abs($expamt),
                        'balance' => $available_balance,
                        'closing_balance' => $available_balance - abs($expamt),
                        'remark' => "",
                        'match_id' => $matchid,
                        'bet_user_id' => $id,
                        'user_exposure_log_id' => $betModel->id
                    ]);

                    $upd = CreditReference::find($creditref['id']);
                    $upd->exposure = $exposer;
                    $upd->available_balance_for_D_W = $balance;

                    $upd->remain_bal = $remain_balance;
                    $update_ = $upd->update();
                    $update_ = 1;
                    if ($update_) {
                        $parentid = self::GetAllParentofPlayer($id);

                        $parentid = json_decode($parentid);
                        if (!empty($parentid)) {
                            for ($i = 0; $i < sizeof($parentid); $i++) {
                                $pid = $parentid[$i];
                                if ($pid != 1) {
                                    $creditref_bal = CreditReference::where(['player_id' => $pid])->first();
                                    $bal = $creditref_bal->remain_bal;
                                    //$remain_balance_=$bal-abs($total_expo_amount); ///nnn 20-10-2021
                                    $remain_balance_ = $bal - abs($expamt);
                                    $upd_ = CreditReference::find($creditref_bal->id);
                                    $upd_->remain_bal = $remain_balance_;
                                    $update_parent = $upd_->update();
                                }
                            }
                        }
                    }
                }
                else {
                    $creditref = CreditReference::where(['player_id' => $id])->first();
                    $exposer = $creditref->exposure - abs($total_expo_amount);
                    $balance = $creditref->available_balance_for_D_W + abs($total_expo_amount) + $mytotal;
                    $remain_balance = $creditref->remain_bal + $mytotal;


                    $available_balance = $creditref->available_balance_for_D_W;
                    UsersAccount::create([
                        'user_id' => $id,
                        'from_user_id' => $id,
                        'to_user_id' => $id,
                        'credit_amount' => $mytotal,
                        'balance' => $available_balance,
                        'closing_balance' => $available_balance + $mytotal,
                        'remark' => "",
                        'match_id' => $matchid,
                        'bet_user_id' => $id,
                        'user_exposure_log_id' => $betModel->id
                    ]);

                    $upd = CreditReference::find($creditref['id']);
                    $upd->exposure = $exposer;
                    $upd->available_balance_for_D_W = $balance;
                    $upd->remain_bal = $remain_balance;
                    $update_ = $upd->update();

                    if ($update_) {
                        $parentid = self::GetAllParentofPlayer($id);

                        $parentid = json_decode($parentid);
                        if (!empty($parentid)) {
                            for ($i = 0; $i < sizeof($parentid); $i++) {
                                $pid = $parentid[$i];
                                if ($pid != 1) {
                                    $creditref_bal = CreditReference::where(['player_id' => $pid])->first();
                                    $bal = $creditref_bal->remain_bal;
                                    $remain_balance_ = $bal + $mytotal;
                                    $upd_ = CreditReference::find($creditref_bal->id);
                                    $upd_->remain_bal = $remain_balance_;
                                    $update_parent = $upd_->update();
                                }
                            }
                        }
                    }
                }


                    //calculating admin balance
                    $admin_tran = UserExposureLog::where('match_id', $matchid)->where('bet_type', 'SESSION')->where('fancy_name', $fancyname)->where('user_id', $id)->get();
                    $admin_profit = 0;
                    $admin_loss = 0;

                    $settings = setting::latest('id')->first();
                    $adm_balance = $settings->balance;
                    $available_balance = $settings->balance;
                    foreach ($admin_tran as $trans) {
                        if ($trans->profit != '') {
                            $admin_loss += $trans->profit;

                            UsersAccount::create([
                                'user_id' => $masterAdmin->id,
                                'from_user_id' => $id,
                                'to_user_id' => $masterAdmin->id,
                                'debit_amount' => $trans->profit,
                                'balance' => $available_balance,
                                'closing_balance' => $available_balance - $trans->profit,
                                'remark' => "",
                                'match_id' => $matchid,
                                'bet_user_id' => $id,
                                'user_exposure_log_id' => $trans->id
                            ]);
                            $available_balance = $available_balance - $trans->profit;

                        } else if ($trans->loss != '') {
                            $admin_profit += abs($trans->loss);

                            UsersAccount::create([
                                'user_id' => $masterAdmin->id,
                                'from_user_id' => $id,
                                'to_user_id' => $masterAdmin->id,
                                'credit_amount' => abs($trans->loss),
                                'balance' => $available_balance,
                                'closing_balance' => $available_balance + abs($trans->loss),
                                'remark' => "",
                                'match_id' => $matchid,
                                'bet_user_id' => $id,
                                'user_exposure_log_id' => $trans->id
                            ]);
                            $available_balance = $available_balance + abs($trans->loss);
                        }
                    }


                    $new_balance = $adm_balance + $admin_profit - $admin_loss;
                    $adminData = setting::find($settings->id);
                    $adminData->balance = $new_balance;
                    $adminData->update();

            }
        }
        else if ($mytotal == 0) // no profit no loss
        {

            $is_won = 1;
            $betModel = new UserExposureLog();
            $betModel->match_id = $matchid;
            $betModel->user_id = $id;
            $betModel->bet_type = 'SESSION';
            $betModel->profit = $mytotal;
            $betModel->loss = $mytotal;
            if ($is_won == 1)
                $betModel->win_type = 'Profit';
            else
                $betModel->win_type = 'Loss';
            $betModel->fancy_name = $fancyname;
            $check = $betModel->save();

            if ($check) {
                $creditref = CreditReference::where(['player_id' => $id])->first();
                $exposer = $creditref->exposure - abs($total_expo_amount);
                $balance = $creditref->available_balance_for_D_W + abs($total_expo_amount) + $mytotal;

                $remain_balance = $creditref->remain_bal + $mytotal;

                $available_balance = $creditref->available_balance_for_D_W;
                UsersAccount::create([
                    'user_id' => $id,
                    'from_user_id' => $id,
                    'to_user_id' => $id,
                    'credit_amount' => $mytotal,
                    'balance' => $available_balance,
                    'closing_balance' => $available_balance + $mytotal,
                    'remark' => "",
                    'match_id' => $matchid,
                    'bet_user_id' => $id,
                    'user_exposure_log_id' => $betModel->id
                ]);

                $upd = CreditReference::find($creditref['id']);
                $upd->exposure = $exposer;
                $upd->available_balance_for_D_W = $balance;
                $upd->remain_bal = $remain_balance;
                $update_ = $upd->update();
                if ($update_) {
                    $parentid = self::GetAllParentofPlayer($id);

                    $parentid = json_decode($parentid);
                    if (!empty($parentid)) {
                        for ($i = 0; $i < sizeof($parentid); $i++) {
                            $pid = $parentid[$i];
                            if ($pid != 1) {
                                $creditref_bal = CreditReference::where(['player_id' => $pid])->first();
                                $bal = $creditref_bal->remain_bal;
                                $remain_balance_ = $bal + $mytotal;
                                $upd_ = CreditReference::find($creditref_bal->id);
                                $upd_->remain_bal = $remain_balance_;
                                $update_parent = $upd_->update();
                            }
                        }
                    }
                }
            }

        }

    }

    public function resultDeclare(Request $request)
    {

        $fancyName = $request->fancyname;
        $data['fancy_name'] = $fancyName;
        $data['match_id'] = $request->match_id;
        $data['eventid'] = $request->eventid;
        $data['result'] = $request->fancy_result;
        $data['bet_id'] = $request->betId;
        FancyResult::create($data);

        $bet = MyBets::where('match_id', $request->eventid)->where('bet_type', 'SESSION')->where('team_name', $fancyName)->where('isDeleted', 0)->where('result_declare', 0)->groupby('user_id')->get();
        foreach ($bet as $b) {
            $exposer = SELF::getFancyBetResult($fancyName, $request->match_id, $request->eventid, $b->user_id, $request->fancy_result);

            /*$betData = MyBets::find($request->betId);
		    	$betData->result_declare = 1;
		    	$check=$betData->update();*/
        }

        //update in my_bet table for bet winner
        $updbet = MyBets::where('match_id', $request->eventid)->where('bet_type', 'SESSION')->where('team_name', $fancyName)->get();
        foreach ($updbet as $bet) {
            $upd_bet = MyBets::find($bet->id);
            $upd_bet->result_declare = 1;
            $upd_bet->update();
        }
        exit;
    }

    public function resultDeclarecancel(Request $request)
    {

        $data['result'] = 'cancel';
        $fancyName = $request->fancyname;
        $data['fancy_name'] = $fancyName;
        $data['match_id'] = $request->match_id;
        $data['eventid'] = $request->eventid;
        $data['bet_id'] = $request->betId;
        FancyResult::create($data);

        $bet = MyBets::where('match_id', $request->eventid)->where('bet_type', 'SESSION')->where('team_name', $fancyName)->where('isDeleted', 0)->where('result_declare', 0)->groupby('user_id')->get();

        foreach ($bet as $b) {
            $exposer = SELF::getFancyBetResult($fancyName, $request->match_id, $request->eventid, $b->user_id, 'cancel');
            $betData = MyBets::find($request->betId);
            $betData->result_declare = 1;
            $check = $betData->update();
            /*echo "11";
				exit;*/
        }

        //update in my_bet table for bet winner
        $updbet = MyBets::where('match_id', $request->eventid)->where('bet_type', 'SESSION')->where('team_name', $fancyName)->get();
        foreach ($updbet as $bet) {
            $upd_bet = MyBets::find($bet->id);
            $upd_bet->result_declare = 1;
            $upd_bet->update();
        }
    }

    public function storeMessage(Request $request)
    {
        $userPass = Auth::user()->password;
        $settingData = setting::latest('id')->first();
        $maintainmsg = '';
        if ($request->main_check != '') {
            $maintainmsg = $request->maintanence_msg;
        }
        if (Hash::check($request['master_password'], $userPass)) {
            $settingData->agent_msg = $request->agent_msg;
            $settingData->user_msg = $request->user_msg;
            $settingData->maintanence_msg = $maintainmsg;
            $settingData->update();
        } else {
            return Redirect::back()->with('error', 'Incorrect password.');
        }
        return Redirect::back()->with('message', 'Message added successfully.');
    }

    public function privilege()
    {
        $users = User::where('agent_level', 'SL')->get();
        return view('backpanel/privilege', compact('users'));
    }

    public function deleteprvlg(Request $request)
    {
        $id = $request->val;
        $data = User::where('id', $id)->first();
        $data->delete();
        return response()->json(array('result' => 'success'));
    }

    public function changePrivilegePass(Request $request)
    {
        $getuser = Auth::user();
        $pass = $request->passwordprivi;
        $userId = $request->userId;
        $userData = User::find($userId);
        $transaction_code = $request->transaction_code;
        if (Hash::check($transaction_code, $getuser->password)) {
            $userData->password = Hash::make($pass);
            $userData->update();
        } else {
            return response()->json(array('result' => 'error', 'message' => 'Your Transaction Password Is Incorrect !'));
        }
        return response()->json(array('result' => 'success'));
    }

    public function changestatusListClient(Request $request)
    {
        $userId = $request->uid;
        $gstatus = $request->gstatus;
        $nameatt = $request->nameatt;
        $user = User::find($userId);
        $user->$nameatt = $request->gstatus;
        $user->update();
        return response()->json(array('result' => 'success'));
    }

    public function storeBalance(Request $request)
    {
        $balance = setting::latest('id')->first();
        $balance = $balance->balance;
        $balance = $balance + $request->balance_amount;
        $settingData = setting::latest('id')->first();
        $settingData->balance = $balance;
        $settingData->update();
        return Redirect::back()->with('success', 'Balance added successfully.');
    }

    public function main_market()
    {
        $sports = Sport::get();
        return view('backpanel/main_market', compact('sports'));
    }

    public function match($id)
    {
        $sports = Sport::get();
        return view('backpanel/match', compact('id'));
    }

    public function addMatch(Request $request, $id)
    {
        $sports = Sport::where('id', $id)->first();
        $matchList = Match::where('event_id', $request->event_id)->where('match_id', $request->match_id)->get();
        if (count($matchList) > 0) {
            return redirect()->route('match', $id)->with('error', 'Match already added.');
        } else {
            $data = $request->all();
            $data['sports_id'] = $sports['sId'];
            Match::create($data);
            return redirect()->route('backpanel/main_market')->with('message', 'Match added successfully.');
        }
    }

    public function sports()
    {
        return view('backpanel/sports');
    }

    public function addSport(Request $request)
    {
        $data = $request->all();
        Sport::create($data);
        return redirect()->route('main_market')->with('message', 'Data created successfully.');
    }

    public function listmatch($id)
    {
        $sports = Sport::get();
        $matchList = Match::where('sports_id', $id)->get();
        return view('backpanel/listmatch', compact('matchList', 'sports'));
    }

    public function risk_management()
    {
        $sports = Sport::get();
        $matchList = Match::get();
        return view('backpanel/risk-management', compact('matchList', 'sports'));
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

    public function riskDetailsFormattedData()
    {
        $loginUser = Auth::user();
        $all_child = $this->GetChildofAgent($loginUser->id);

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

                $matches = Match::select('id', 'match_name', 'match_id', 'sports_id', 'status', 'winner', 'match_date', 'event_id')->where('sports_id', $sport->sId)->where('status', 1)->where('winner', NULL)->whereHas('bets', function ($q) {
                    $q->where('isDeleted', 0);
                })->orderBy('match_date', 'ASC')->get();

                $finalMatchesToDisplay = [];

                foreach ($matches as $match) {
                    $finalMatchesToDisplay[$match->event_id] = $match;
                }
                if (!empty($finalMatchesToDisplay) && !empty($apiData)) {
                    foreach ($apiData as $item) {
                        if (isset($finalMatchesToDisplay[$item['gameId']])) {

                            $match = $finalMatchesToDisplay[$item['gameId']];
                            $item['match_detail'] = $match->toArray();

                            $split = explode(" v ", $match->match_name);
                            if (@count($split) > 0) {
                                $teamone = $split[0];
                                if (isset($split[1]))
                                    $teamtwo = $split[1];
                                else
                                    $teamtwo = '';
                            } else {
                                $teamone = '';
                                $teamtwo = '';
                            }

                            $match_date = $match->match_date;

//                            if($item['inPlay']=='True'){
                            $date = Carbon::parse(strtotime($match->match_date));
                            $date->addMinutes(330);
                            if (Carbon::parse($date)->isToday()) {
                                $match_date = date('h:i A', strtotime($date));
                            } else if (Carbon::parse($date)->isTomorrow())
                                $match_date = 'Tomorrow ' . date('h:i A', strtotime($date));
                            else
                                $match_date = date('d-m-Y h:i A', strtotime($date));
//                            }else{
//                                $match_date = date("d-m-Y h:i A", strtotime($match_date));
//                            }

                            $item['match_detail']['formatted_match_date'] = $match_date;

                            $my_placed_bets = MyBets::where('match_id', $match['event_id'])->where('bet_type','!=', 'SESSION')->where('result_declare', 0)->where('isDeleted', 0)->whereIn('user_id', $all_child)->get();
                            $my_placed_bets_session = MyBets::where('match_id', $match['event_id'])->where('bet_type','=', 'SESSION')->where('result_declare', 0)->where('isDeleted', 0)->whereIn('user_id', $all_child)->count();
                            $team2_bet_total = 0;
                            $team1_bet_total = 0;
                            $team_draw_bet_total = 0;
                            if ($my_placed_bets->count() > 0 || $my_placed_bets_session > 0) {
                                if($my_placed_bets->count() > 0) {
                                    foreach ($my_placed_bets as $bet) {
                                        $abc = json_decode($bet->extra, true);
                                        if (!empty($abc)) {
                                            if (count($abc) >= 2) {
                                                if (array_key_exists("teamname1", $abc) && array_key_exists("teamname2", $abc)) {
                                                    //bet on draw
                                                    if ($bet->bet_side == 'back') {
                                                        $team1_bet_total = $team1_bet_total + $bet->exposureAmt;
                                                        if (count($abc) >= 2) {
                                                            $team_draw_bet_total = $team_draw_bet_total - $bet->bet_profit;
                                                        }
                                                        $team2_bet_total = $team2_bet_total + $bet->exposureAmt;
                                                    }
                                                    if ($bet->bet_side == 'lay') {
                                                        $team1_bet_total = $team1_bet_total - ($bet->bet_amount);
                                                        if (count($abc) >= 2) {
                                                            $team_draw_bet_total = $team_draw_bet_total + ($bet->exposureAmt);
                                                        }
                                                        $team2_bet_total = $team2_bet_total - ($bet->bet_amount);
                                                    }
                                                } else if (array_key_exists("teamname3", $abc) && array_key_exists("teamname2", $abc)) {
                                                    //bet on team1
                                                    if ($bet->bet_side == 'back') {
                                                        $team1_bet_total = $team1_bet_total - $bet->bet_profit;
                                                        if (count($abc) >= 2) {
                                                            $team_draw_bet_total = $team_draw_bet_total + $bet->exposureAmt;
                                                        }
                                                        $team2_bet_total = $team2_bet_total + $bet->exposureAmt;
                                                    }
                                                    if ($bet->bet_side == 'lay') {
                                                        $team1_bet_total = $team1_bet_total + ($bet->exposureAmt);
                                                        if (count($abc) >= 2) {
                                                            $team_draw_bet_total = $team_draw_bet_total - ($bet->bet_amount);
                                                        }
                                                        $team2_bet_total = $team2_bet_total - ($bet->bet_amount);
                                                    }
                                                } else if (array_key_exists("teamname3", $abc) && array_key_exists("teamname1", $abc)) {
                                                    //bet on team2
                                                    if ($bet->bet_side == 'back') {
                                                        $team2_bet_total = $team2_bet_total - ($bet->bet_profit);
                                                        if (count($abc) >= 2) {
                                                            $team_draw_bet_total = $team_draw_bet_total + $bet->exposureAmt;
                                                        }
                                                        $team1_bet_total = $team1_bet_total + $bet->exposureAmt;
                                                    }
                                                    if ($bet->bet_side == 'lay') {
                                                        $team2_bet_total = $team2_bet_total + $bet->exposureAmt;
                                                        if (count($abc) >= 2) {
                                                            $team_draw_bet_total = $team_draw_bet_total - $bet->bet_amount;
                                                        }
                                                        $team1_bet_total = $team1_bet_total - $bet->bet_amount;
                                                    }
                                                }
                                            } else if (count($abc) == 1) {
                                                if (array_key_exists("teamname1", $abc)) {
                                                    //bet on team2
                                                    if ($bet->bet_side == 'back') {
                                                        $team2_bet_total = $team2_bet_total - $bet->bet_profit;
                                                        $team1_bet_total = $team1_bet_total + $bet->exposureAmt;
                                                    }
                                                    if ($bet->bet_side == 'lay') {
                                                        $team2_bet_total = $team2_bet_total + $bet->exposureAmt;
                                                        $team1_bet_total = $team1_bet_total - $bet->bet_amount;
                                                    }
                                                } else {
                                                    //bet on team1
                                                    if ($bet->bet_side == 'back') {
                                                        $team1_bet_total = $team1_bet_total - $bet->bet_profit;
                                                        $team2_bet_total = $team2_bet_total + $bet->exposureAmt;
                                                    }
                                                    if ($bet->bet_side == 'lay') {
                                                        $team1_bet_total = $team1_bet_total + $bet->exposureAmt;
                                                        $team2_bet_total = $team2_bet_total - $bet->bet_amount;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                                $item['match_detail']['team_one'] = $teamone;
                                $item['match_detail']['team_two'] = $teamtwo;
                                $item['match_detail']['total_bets'] = $my_placed_bets->count() + $my_placed_bets_session;
                                $item['match_detail']['team1_bet_total'] = round($team1_bet_total,2);
                                $item['match_detail']['team2_bet_total'] = round($team2_bet_total,2);
                                $item['match_detail']['team_draw_bet_total'] = round($team_draw_bet_total,2);

                                $records[] = $item;
                            }


                        }
                    }
                }
            }

            $render = view('backpanel.ajax.risk-management-ajax', compact('records', 'sport'))->render();

            $html[$sport->sId] = $render;
        }

        return response()->json(array('html' => $html));
    }

    public function getriskdetails()
    {

        $sports = Sport::all();
        $getuser = Auth::user();
        $html = '';
        $i = 0;
        $final_html = '';
        $cricket_final_html = '';
        $tennis_final_html = '';
        $soccer_final_html = '';
        //get all child of agent
        $loginUser = Auth::user();
        $ag_id = $loginUser->id;
        $all_child = $this->GetChildofAgent($ag_id);

        $match_array_data_cricket = array();

        $match_link = Match::where('sports_id', 4)->where('status', 1)->where('suspend_m', 1)->where('status_m', 1)->where('isDeleted', 0)->where('winner', NULL)->orderBy('match_date', 'ASC')->get();
        foreach ($match_link as $match) {
            if (@$match->match_id != '') {

                if ($match->sports_id == 4)
                    $my_placed_bets = MyBets::where('match_id', $match['event_id'])->where('result_declare', 0)->where('isDeleted', 0)->whereIn('user_id', $all_child)->get();
                $total_bets = count($my_placed_bets);
                if ($total_bets > 0) {
                    $match_array_data_cricket[] = $match->match_id;
                }

            }
        }

        $imp_match_array_data_cricket = @implode(",", $match_array_data_cricket);
        $mdata = array();
        $inplay = 0;
        /*print_r($imp_match_array_data_cricket);
		exit;*/
        //for match original date and time
        $get_match_type = app('App\Http\Controllers\RestApi')->GetAllMatch(4);

//        dd($get_match_type);

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

        if ($imp_match_array_data_cricket != '') {
            $url = 'http://69.30.238.2:3644/odds/multiple?ids=' . $imp_match_array_data_cricket;

            dd($url);

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
            if ($match_data !== null && sizeof($match_data) > 0) {
                for ($j = 0; $j < sizeof($match_data); $j++) {
                    $inplay_game = '';
                    $match_detail = Match::where('match_id', $match_data[$j]['marketId'])->first();
                    $split = explode(" v ", $match_detail['match_name']);
                    if (@count($split) > 0) {
                        @$teamone = $split[0];
                        if (isset($split[1]))
                            @$teamtwo = $split[1];
                        else
                            $teamtwo = '';
                    } else {
                        $teamone = '';
                        $teamtwo = '';
                    }
                    $inplay_game = '';
                    if (isset($match_data[$j]['inplay'])) {
                        if ($match_data[$j]['inplay'] == 1) {
                            $dt = '';
                            $style = "fir-col1-green";
                            $inplay_game = " <span style='color:green'></span>";
                        } else {
                            $match_date = '';
                            $dt = '';
                            $key = array_search($match_detail['event_id'], array_column($st_criket, 'MarketId'));
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
                        }
                    } else {
                        $dt = date("d-m-Y h:i A", strtotime($match_detail['match_date']));
                        $style = "fir-col1";
                        $inplay_game = '';
                    }
                    //bet calculation
                    $total_bets = 0;


                    $my_placed_bets = MyBets::where('match_id', $match_detail['event_id'])->where('bet_type', 'ODDS')->where('result_declare', 0)->where('isDeleted', 0)->whereIn('user_id', $all_child)->get();
                    $total_bets = count($my_placed_bets);
                    $team2_bet_total = 0;
                    $team1_bet_total = 0;
                    $team_draw_bet_total = 0;
                    if (sizeof($my_placed_bets) > 0) {
                        foreach ($my_placed_bets as $bet) {
                            $abc = json_decode($bet->extra, true);
                            if (!empty($abc)) {
                                if (count($abc) >= 2) {
                                    if (array_key_exists("teamname1", $abc) && array_key_exists("teamname2", $abc)) {
                                        //bet on draw
                                        if ($bet->bet_side == 'back') {
                                            $team1_bet_total = $team1_bet_total + $bet->exposureAmt;
                                            if (count($abc) >= 2) {
                                                $team_draw_bet_total = $team_draw_bet_total - $bet->bet_profit;
                                            }
                                            $team2_bet_total = $team2_bet_total + $bet->exposureAmt;
                                        }
                                        if ($bet->bet_side == 'lay') {
                                            $team1_bet_total = $team1_bet_total - ($bet->bet_amount);
                                            if (count($abc) >= 2) {
                                                $team_draw_bet_total = $team_draw_bet_total + ($bet->exposureAmt);
                                            }
                                            $team2_bet_total = $team2_bet_total - ($bet->bet_amount);
                                        }
                                    } else if (array_key_exists("teamname3", $abc) && array_key_exists("teamname2", $abc)) {
                                        //bet on team1
                                        if ($bet->bet_side == 'back') {
                                            $team1_bet_total = $team1_bet_total - $bet->bet_profit;
                                            if (count($abc) >= 2) {
                                                $team_draw_bet_total = $team_draw_bet_total + $bet->exposureAmt;
                                            }
                                            $team2_bet_total = $team2_bet_total + $bet->exposureAmt;
                                        }
                                        if ($bet->bet_side == 'lay') {
                                            $team1_bet_total = $team1_bet_total + ($bet->exposureAmt);
                                            if (count($abc) >= 2) {
                                                $team_draw_bet_total = $team_draw_bet_total - ($bet->bet_amount);
                                            }
                                            $team2_bet_total = $team2_bet_total - ($bet->bet_amount);
                                        }
                                    } else if (array_key_exists("teamname3", $abc) && array_key_exists("teamname1", $abc)) {
                                        //bet on team2
                                        if ($bet->bet_side == 'back') {
                                            $team2_bet_total = $team2_bet_total - ($bet->bet_profit);
                                            if (count($abc) >= 2) {
                                                $team_draw_bet_total = $team_draw_bet_total + $bet->exposureAmt;
                                            }
                                            $team1_bet_total = $team1_bet_total + $bet->exposureAmt;
                                        }
                                        if ($bet->bet_side == 'lay') {
                                            $team2_bet_total = $team2_bet_total + $bet->exposureAmt;
                                            if (count($abc) >= 2) {
                                                $team_draw_bet_total = $team_draw_bet_total - $bet->bet_amount;
                                            }
                                            $team1_bet_total = $team1_bet_total - $bet->bet_amount;
                                        }
                                    }
                                } else if (count($abc) == 1) {
                                    if (array_key_exists("teamname1", $abc)) {
                                        //bet on team2
                                        if ($bet->bet_side == 'back') {
                                            $team2_bet_total = $team2_bet_total - $bet->bet_profit;
                                            $team1_bet_total = $team1_bet_total + $bet->exposureAmt;
                                        }
                                        if ($bet->bet_side == 'lay') {
                                            $team2_bet_total = $team2_bet_total + $bet->exposureAmt;
                                            $team1_bet_total = $team1_bet_total - $bet->bet_amount;
                                        }
                                    } else {
                                        //bet on team1
                                        if ($bet->bet_side == 'back') {
                                            $team1_bet_total = $team1_bet_total - $bet->bet_profit;
                                            $team2_bet_total = $team2_bet_total + $bet->exposureAmt;
                                        }
                                        if ($bet->bet_side == 'lay') {
                                            $team1_bet_total = $team1_bet_total + $bet->exposureAmt;
                                            $team2_bet_total = $team2_bet_total - $bet->bet_amount;
                                        }
                                    }
                                }

                            }
                        }
                    }

                    $bet_cls = '';
                    if ($team1_bet_total >= 0)
                        $bet_cls = 'text-color-green';
                    else
                        $bet_cls = 'text-color-red';

                    //end for bet calculation

                    $html .= '<div class="panel panel-default panel_content beige-bg-1">
				<h6>';
                    if ($match_data[$j]['inplay'] == 1) {
                        $html .= '<p class="blinkbtn"> <span class="blink_me"> IN PLAY </span> </p>';
                        $html .= '<a href="risk-management-details/' . $match_detail['id'] . '" class="text-color-black inplaytext">' . $match_detail['match_name'] . $inplay_game . '<b>[' . $dt . ']</b></a>';
                    } else {
                        $html .= '<a href="risk-management-details/' . $match_detail['id'] . '" class="text-color-black">' . $match_detail['match_name'] . $inplay_game . '<b>[' . $dt . ']</b></a>';
                    }
                    $html .= '</h6>
				<div class="row panel_row white-bg">';
                    if (isset($match_data[$j]['runners'][0]['ex']['availableToBack'][0]['price']) && isset($match_data[$j]['runners'][0]['ex']['availableToLay'][0]['price'])) {
                        $html .= '
						<div class="col-md-3 p-0">
							<div class="market_listitems">
								<div class="runner_details">
									<div class="r_title">' . $teamone . '</div>
									<div class="' . $bet_cls . '"><b>» ' . round($team1_bet_total) . '</b></div>
								</div>

								<div class="button_content">
									<button class="backbtn cyan-bg">' . $match_data[$j]['runners'][0]['ex']['availableToBack'][0]['price'] . '</button>
									<button class="laybtn pink-bg">' . $match_data[$j]['runners'][0]['ex']['availableToLay'][0]['price'] . '</button>
								</div>
							</div>
						</div>';
                    } else {
                        $html .= '
						<div class="col-md-3 p-0">
							<div class="market_listitems">
								<div class="runner_details">
									<div class="r_title">' . $teamone . '</div>
									<div class="' . $bet_cls . '"><b>» ' . round($team1_bet_total) . '</b></div>
								</div>

								<div class="button_content">
									<button class="backbtn cyan-bg">--</button>
									<button class="laybtn pink-bg">--</button>
								</div>
							</div>
						</div>';
                    }

                    if (isset($match_data[$j]['runners'][1]['ex']['availableToBack'][0]['price']) && isset($match_data[$j]['runners'][1]['ex']['availableToLay'][0]['price'])) {
                        $bet_cls = '';
                        if ($team2_bet_total >= 0)
                            $bet_cls = 'text-color-green';
                        else
                            $bet_cls = 'text-color-red';
                        $html .= '<div class="col-md-3 p-0">
							<div class="market_listitems">
								<div class="runner_details">
									<div class="r_title">' . $teamtwo . '</div>
									<div class="' . $bet_cls . '"><b>» ' . round($team2_bet_total) . '</b></div>
								</div>

								<div class="button_content">
									<button class="backbtn cyan-bg">' . $match_data[$j]['runners'][1]['ex']['availableToBack'][0]['price'] . '</button>
									<button class="laybtn pink-bg">' . $match_data[$j]['runners'][1]['ex']['availableToLay'][0]['price'] . '</button>
								</div>
							</div>
						</div>';
                    } else {
                        $bet_cls = '';
                        if ($team2_bet_total >= 0)
                            $bet_cls = 'text-color-green';
                        else
                            $bet_cls = 'text-color-red';
                        $html .= '<div class="col-md-3 p-0">
							<div class="market_listitems">
								<div class="runner_details">
									<div class="r_title">' . $teamtwo . '</div>
									<div class="' . $bet_cls . '"><b>» ' . round($team2_bet_total) . '</b></div>
								</div>
								<div class="button_content">
									<button class="backbtn cyan-bg">--</button>
									<button class="laybtn pink-bg">--</button>
								</div>
							</div>
						</div>';
                    }
                    if (isset($match_data[$j]['runners'][2]['ex']['availableToBack'][0]['price']) && isset($match_data[$j]['runners'][2]['ex']['availableToLay'][0]['price'])) {
                        $html .= '
						<div class="col-md-3 p-0">
							<div class="market_listitems">
								<div class="runner_details">
									<div class="r_title">The Draw</div>
									<div class="' . $bet_cls . '"><b>» ' . round($team1_bet_total) . '</b></div>
								</div>

								<div class="button_content">
									<button class="backbtn cyan-bg">' . $match_data[$j]['runners'][2]['ex']['availableToBack'][0]['price'] . '</button>
									<button class="laybtn pink-bg">' . $match_data[$j]['runners'][2]['ex']['availableToLay'][0]['price'] . '</button>
								</div>
							</div>
						</div>';
                    }
                    $html .= '<div class="col-md-3 p-0">
						<div class="market_listitems">
							<div class="runner_details">
								<div class="r_title">Total Bets</div>
								<div><b>» </b></div>
							</div>

							<div class="button_content">
								<b><span>' . $total_bets . '</span></b>
							</div>
						</div>
						</div>
					</div>
				</div>';
                }
            } else {
                $html .= "~~";
                $html .= '<div class="panel panel-default panel_content beige-bg-1">
				<h6>No match found.</h6></div>';
            }
        } else {
            $html .= "~~";
            $html .= '<div class="panel panel-default panel_content beige-bg-1">
		<h6>No match found.</h6></div>';
        }
        return $html;
    }

    public function getriskdetailTwo()
    {
        $sports = Sport::all();
        $getuser = Auth::user();
        $html = '';
        $i = 0;
        $final_html = '';
        $cricket_final_html = '';
        $tennis_final_html = '';
        $soccer_final_html = '';
        $match_array_data_cricket = array();
        $match_array_data_tenis = array();
        $match_array_data_soccer = array();
        //get all child of agent
        $loginUser = Auth::user();
        $ag_id = $loginUser->id;
        $all_child = $this->GetChildofAgent($ag_id);
        foreach ($sports as $sport) {
            $match_link = Match::where('sports_id', $sport->sId)->where('status', 1)->where('suspend_m', 1)->where('status_m', 1)->where('isDeleted', 0)->where('winner', NULL)->orderBy('match_date', 'ASC')->get();
            foreach ($match_link as $match) {
                if (@$match->match_id != '') {
                    $my_placed_bets = MyBets::where('match_id', $match['event_id'])->where('result_declare', 0)->where('isDeleted', 0)->whereIn('user_id', $all_child)->get();
                    $total_bets = count($my_placed_bets);
                    if ($total_bets > 0) {
                        if ($match->sports_id == 4)
                            $match_array_data_cricket[] = $match->match_id;
                        else if ($match->sports_id == 2)
                            $match_array_data_tenis[] = $match->match_id;
                        else if ($match->sports_id == 1)
                            $match_array_data_soccer[] = $match->match_id;
                    }
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

        if ($imp_match_array_data_cricket != '') {
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

            if ($match_data !== null && sizeof($match_data) > 0) {
                for ($j = 0; $j < sizeof($match_data); $j++) {
                    $inplay_game = '';
                    $match_detail = Match::where('match_id', $match_data[$j]['marketId'])->first();
                    $split = explode(" v ", $match_detail['match_name']);
                    if (@count($split) > 0) {
                        @$teamone = $split[0];
                        if (isset($split[1]))
                            @$teamtwo = $split[1];
                        else
                            $teamtwo = '';
                    } else {
                        $teamone = '';
                        $teamtwo = '';
                    }
                    $inplay_game = '';
                    if (isset($match_data[$j]['inplay'])) {
                        if ($match_data[$j]['inplay'] == 1) {
                            $dt = '';
                            $style = "fir-col1-green";
                            $inplay_game = " <span style='color:green'></span>";
                        } else {
                            $match_date = '';
                            $dt = '';
                            $key = array_search($match_detail['event_id'], array_column($st_criket, 'MarketId'));
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
                        }
                    } else {
                        $dt = date("d-m-Y h:i A", strtotime($match_detail['match_date']));
                        $style = "fir-col1";
                        $inplay_game = '';
                    }
                    //bet calculation
                    $total_bets = 0;


                    $my_placed_bets = MyBets::where('match_id', $match_detail['event_id'])->where('bet_type', 'ODDS')->where('result_declare', 0)->where('isDeleted', 0)->whereIn('user_id', $all_child)->get();
                    //$my_placed_bets = MyBets::where('match_id',$match_detail['event_id'])->where('bet_type','ODDS')->where('result_declare',0)->where('isDeleted',0)->get();
                    $total_bets = count($my_placed_bets);
                    $team2_bet_total = 0;
                    $team1_bet_total = 0;
                    $team_draw_bet_total = 0;
                    if (sizeof($my_placed_bets) > 0) {
                        foreach ($my_placed_bets as $bet) {
                            $abc = json_decode($bet->extra, true);
                            if (!empty($abc)) {
                                if (count($abc) >= 2) {
                                    if (array_key_exists("teamname1", $abc) && array_key_exists("teamname2", $abc)) {
                                        //bet on draw
                                        if ($bet->bet_side == 'back') {
                                            $team1_bet_total = $team1_bet_total + $bet->exposureAmt;
                                            if (count($abc) >= 2) {
                                                $team_draw_bet_total = $team_draw_bet_total - $bet->bet_profit;
                                            }
                                            $team2_bet_total = $team2_bet_total + $bet->exposureAmt;
                                        }
                                        if ($bet->bet_side == 'lay') {
                                            $team1_bet_total = $team1_bet_total - ($bet->bet_amount);
                                            if (count($abc) >= 2) {
                                                $team_draw_bet_total = $team_draw_bet_total + ($bet->exposureAmt);
                                            }
                                            $team2_bet_total = $team2_bet_total - ($bet->bet_amount);
                                        }
                                    } else if (array_key_exists("teamname3", $abc) && array_key_exists("teamname2", $abc)) {
                                        //bet on team1
                                        if ($bet->bet_side == 'back') {
                                            $team1_bet_total = $team1_bet_total - $bet->bet_profit;
                                            if (count($abc) >= 2) {
                                                $team_draw_bet_total = $team_draw_bet_total + $bet->exposureAmt;
                                            }
                                            $team2_bet_total = $team2_bet_total + $bet->exposureAmt;
                                        }
                                        if ($bet->bet_side == 'lay') {
                                            $team1_bet_total = $team1_bet_total + ($bet->exposureAmt);
                                            if (count($abc) >= 2) {
                                                $team_draw_bet_total = $team_draw_bet_total - ($bet->bet_amount);
                                            }
                                            $team2_bet_total = $team2_bet_total - ($bet->bet_amount);
                                        }
                                    } else if (array_key_exists("teamname3", $abc) && array_key_exists("teamname1", $abc)) {
                                        //bet on team2
                                        if ($bet->bet_side == 'back') {
                                            $team2_bet_total = $team2_bet_total - ($bet->bet_profit);
                                            if (count($abc) >= 2) {
                                                $team_draw_bet_total = $team_draw_bet_total + $bet->exposureAmt;
                                            }
                                            $team1_bet_total = $team1_bet_total + $bet->exposureAmt;
                                        }
                                        if ($bet->bet_side == 'lay') {
                                            $team2_bet_total = $team2_bet_total + $bet->exposureAmt;
                                            if (count($abc) >= 2) {
                                                $team_draw_bet_total = $team_draw_bet_total - $bet->bet_amount;
                                            }
                                            $team1_bet_total = $team1_bet_total - $bet->bet_amount;
                                        }
                                    }
                                } else if (count($abc) == 1) {
                                    if (array_key_exists("teamname1", $abc)) {
                                        //bet on team2
                                        if ($bet->bet_side == 'back') {
                                            $team2_bet_total = $team2_bet_total - $bet->bet_profit;
                                            $team1_bet_total = $team1_bet_total + $bet->exposureAmt;
                                        }
                                        if ($bet->bet_side == 'lay') {
                                            $team2_bet_total = $team2_bet_total + $bet->exposureAmt;
                                            $team1_bet_total = $team1_bet_total - $bet->bet_amount;
                                        }
                                    } else {
                                        //bet on team1
                                        if ($bet->bet_side == 'back') {
                                            $team1_bet_total = $team1_bet_total - $bet->bet_profit;
                                            $team2_bet_total = $team2_bet_total + $bet->exposureAmt;
                                        }
                                        if ($bet->bet_side == 'lay') {
                                            $team1_bet_total = $team1_bet_total + $bet->exposureAmt;
                                            $team2_bet_total = $team2_bet_total - $bet->bet_amount;
                                        }
                                    }
                                }
                            }
                        }
                    }

                    $bet_cls = '';
                    if ($team1_bet_total >= 0)
                        $bet_cls = 'text-color-green';
                    else
                        $bet_cls = 'text-color-red';

                    //end for bet calculation

                    $html .= '<div class="panel panel-default panel_content beige-bg-1">
				<h6>';
                    if ($match_data[$j]['inplay'] == 1) {
                        $html .= '<p class="blinkbtn"> <span class="blink_me"> IN PLAY </span> </p>';
                        $html .= '<a href="risk-management-details/' . $match_detail['id'] . '" class="text-color-black inplaytext">' . $match_detail['match_name'] . $inplay_game . '<b>[' . $dt . ']</b></a>';
                    } else {
                        $html .= '<a href="risk-management-details/' . $match_detail['id'] . '" class="text-color-black">' . $match_detail['match_name'] . $inplay_game . '<b>[' . $dt . ']</b></a>';
                    }
                    $html .= '</h6>
				<div class="row panel_row white-bg">';
                    if (isset($match_data[$j]['runners'][0]['ex']['availableToBack'][0]['price']) && isset($match_data[$j]['runners'][0]['ex']['availableToLay'][0]['price'])) {
                        $html .= '
						<div class="col-md-3 p-0">
							<div class="market_listitems">
								<div class="runner_details">
									<div class="r_title">' . $teamone . '</div>
									<div class="' . $bet_cls . '"><b>» ' . round($team1_bet_total) . '</b></div>
								</div>

								<div class="button_content">
									<button class="backbtn cyan-bg">' . $match_data[$j]['runners'][0]['ex']['availableToBack'][0]['price'] . '</button>
									<button class="laybtn pink-bg">' . $match_data[$j]['runners'][0]['ex']['availableToLay'][0]['price'] . '</button>
								</div>
							</div>
						</div>';
                    } else {
                        $html .= '
						<div class="col-md-3 p-0">
							<div class="market_listitems">
								<div class="runner_details">
									<div class="r_title">' . $teamone . '</div>
									<div class="' . $bet_cls . '"><b>» ' . round($team1_bet_total) . '</b></div>
								</div>

								<div class="button_content">
									<button class="backbtn cyan-bg">--</button>
									<button class="laybtn pink-bg">--</button>
								</div>
							</div>
						</div>';
                    }

                    if (isset($match_data[$j]['runners'][1]['ex']['availableToBack'][0]['price']) && isset($match_data[$j]['runners'][1]['ex']['availableToLay'][0]['price'])) {
                        $bet_cls = '';
                        if ($team2_bet_total >= 0)
                            $bet_cls = 'text-color-green';
                        else
                            $bet_cls = 'text-color-red';
                        $html .= '<div class="col-md-3 p-0">
							<div class="market_listitems">
								<div class="runner_details">
									<div class="r_title">' . $teamtwo . '</div>
									<div class="' . $bet_cls . '"><b>» ' . round($team2_bet_total) . '</b></div>
								</div>

								<div class="button_content">
									<button class="backbtn cyan-bg">' . $match_data[$j]['runners'][1]['ex']['availableToBack'][0]['price'] . '</button>
									<button class="laybtn pink-bg">' . $match_data[$j]['runners'][1]['ex']['availableToLay'][0]['price'] . '</button>
								</div>
							</div>
						</div>';
                    } else {
                        $bet_cls = '';
                        if ($team2_bet_total >= 0)
                            $bet_cls = 'text-color-green';
                        else
                            $bet_cls = 'text-color-red';
                        $html .= '<div class="col-md-3 p-0">
							<div class="market_listitems">
								<div class="runner_details">
									<div class="r_title">' . $teamtwo . '</div>
									<div class="' . $bet_cls . '"><b>» ' . round($team2_bet_total) . '</b></div>
								</div>
								<div class="button_content">
									<button class="backbtn cyan-bg">--</button>
									<button class="laybtn pink-bg">--</button>
								</div>
							</div>
						</div>';
                    }
                    if (isset($match_data[$j]['runners'][2]['ex']['availableToBack'][0]['price']) && isset($match_data[$j]['runners'][2]['ex']['availableToLay'][0]['price'])) {
                        $html .= '
						<div class="col-md-3 p-0">
							<div class="market_listitems">
								<div class="runner_details">
									<div class="r_title">The Draw</div>
									<div class="' . $bet_cls . '"><b>» ' . round($team1_bet_total) . '</b></div>
								</div>

								<div class="button_content">
									<button class="backbtn cyan-bg">' . $match_data[$j]['runners'][2]['ex']['availableToBack'][0]['price'] . '</button>
									<button class="laybtn pink-bg">' . $match_data[$j]['runners'][2]['ex']['availableToLay'][0]['price'] . '</button>
								</div>
							</div>
						</div>';
                    }
                    $html .= '<div class="col-md-3 p-0">
						<div class="market_listitems">
							<div class="runner_details">
								<div class="r_title">Total Bets</div>
								<div><b>» </b></div>
							</div>

							<div class="button_content">
								<b><span>' . $total_bets . '</span></b>
							</div>
						</div>
						</div>
					</div>
				</div>';
                }
            } else {
                $html .= "~~";
                $html .= '<div class="panel panel-default panel_content beige-bg-1">
				<h6>No match found.</h6></div>';
            }
        } else {
            //$html.="~~";
            $html .= '<div class="panel panel-default panel_content beige-bg-1">
		<h6>No match found.</h6></div>';
        }

        //for tennis

        if ($imp_match_array_data_tenis != '') {
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
            $html .= "~~";
            if ($match_data !== null && sizeof($match_data) > 0) {
                for ($j = 0; $j < sizeof($match_data); $j++) {
                    $inplay_game = '';
                    $match_detail = Match::where('match_id', $match_data[$j]['marketId'])->first();
                    $split = explode(" v ", $match_detail['match_name']);
                    if (@count($split) > 0) {
                        @$teamone = $split[0];
                        if (isset($split[1]))
                            @$teamtwo = $split[1];
                        else
                            $teamtwo = '';
                    } else {
                        $teamone = '';
                        $teamtwo = '';
                    }
                    $inplay_game = '';
                    if (isset($match_data[$j]['inplay'])) {
                        if ($match_data[$j]['inplay'] == 1) {
                            $dt = '';
                            $style = "fir-col1-green";
                            $inplay_game = " <span style='color:green'>In-Play</span>";
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
                        $dt = $match_detail['match_date'];
                        $style = "fir-col1";
                        $inplay_game = '';
                    }

                    //bet calculation

                    $total_bets = 0;
                    //DB::enableQueryLog();


                    $my_placed_bets = MyBets::where('match_id', $match_detail['event_id'])->where('bet_type', 'ODDS')->where('result_declare', 0)->where('isDeleted', 0)->whereIn('user_id', $all_child)->get();
                    //dd(DB::getQueryLog());
                    $total_bets = count($my_placed_bets);
                    $team2_bet_total = 0;
                    $team1_bet_total = 0;
                    $team_draw_bet_total = 0;

                    foreach ($my_placed_bets as $bet) {
                        /*$abc=json_decode($bet->extra,true);
					if(count($abc)>=2)
					{
						if (array_key_exists("teamname1",$abc) && array_key_exists("teamname2",$abc))
						{
							if($bet->bet_side=='back')
							{
								$team1_bet_total=$team1_bet_total+($bet->bet_odds/($bet->bet_amount-1));
								if(count($abc)>=2)
								{
									$team_draw_bet_total=$team_draw_bet_total-($bet->bet_odds/($bet->bet_amount-1));
								}
								$team2_bet_total=$team2_bet_total+($bet->bet_odds/($bet->bet_amount-1));
							}

							if($bet->bet_side=='lay')
							{
								$team1_bet_total=$team1_bet_total-($bet->bet_odds/($bet->bet_amount-1));
								if(count($abc)>=2)
								{
									$team_draw_bet_total=$team_draw_bet_total+$bet->bet_odds;
								}
								$team2_bet_total=$team2_bet_total-($bet->bet_odds/($bet->bet_amount-1));
							}
						}
						else if (array_key_exists("teamname3",$abc) && array_key_exists("teamname2",$abc))
						{
							if($bet->bet_side=='back')
							{
								$team1_bet_total=$team1_bet_total-$bet->bet_odds;
								if(count($abc)>=2)
								{
									$team_draw_bet_total=$team_draw_bet_total+($bet->bet_odds/($bet->bet_amount-1));
								}
								$team2_bet_total=$team2_bet_total+($bet->bet_odds/($bet->bet_amount-1));
							}

							if($bet->bet_side=='lay')
							{
								$team1_bet_total=$team1_bet_total+$bet->bet_odds;
								if(count($abc)>=2)
								{
									$team_draw_bet_total=$team_draw_bet_total-($bet->bet_odds/($bet->bet_amount-1));
								}
								$team2_bet_total=$team2_bet_total-($bet->bet_odds/($bet->bet_amount-1));
							}
						}

						else if (array_key_exists("teamname3",$abc) && array_key_exists("teamname1",$abc))
						{
							if($bet->bet_side=='back')
							{
								$team2_bet_total=$team2_bet_total-$bet->bet_odds;
								if(count($abc)>=2)
								{
									$team_draw_bet_total=$team_draw_bet_total+($bet->bet_odds/($bet->bet_amount-1));
								}
								$team1_bet_total=$team1_bet_total+($bet->bet_odds/($bet->bet_amount-1));
							}

							if($bet->bet_side=='lay')
							{
								$team2_bet_total=$team2_bet_total+$bet->bet_odds;
								if(count($abc)>=2)
								{
									$team_draw_bet_total=$team_draw_bet_total-($bet->bet_odds/($bet->bet_amount-1));
								}
								$team1_bet_total=$team1_bet_total-($bet->bet_odds/($bet->bet_amount-1));
							}
						}
					}
					else if(count($abc)==1)
					{
						if (array_key_exists("teamname1",$abc))
						{
							if($bet->bet_side=='back')
							{
								$team2_bet_total=$team2_bet_total-$bet->bet_odds;
								$team1_bet_total=$team1_bet_total+($bet->bet_odds/($bet->bet_amount-1));
							}

							if($bet->bet_side=='lay')
							{
								$team2_bet_total=$team2_bet_total+$bet->bet_odds;
								$team1_bet_total=$team1_bet_total-($bet->bet_odds/($bet->bet_amount-1));
							}
						}
						else
						{
							if($bet->bet_side=='back')
							{
								$team1_bet_total=$team1_bet_total-$bet->bet_odds;
								$team2_bet_total=$team2_bet_total+($bet->bet_odds/($bet->bet_amount-1));
							}
							if($bet->bet_side=='lay')
							{
								$team1_bet_total=$team1_bet_total+$bet->bet_odds;
								$team2_bet_total=$team2_bet_total-($bet->bet_odds/($bet->bet_amount-1));
							}
						}
					}*/

                        $abc = json_decode($bet->extra, true);
                        if (count($abc) >= 2) {
                            if (array_key_exists("teamname1", $abc) && array_key_exists("teamname2", $abc)) {
                                //bet on draw
                                if ($bet->bet_side == 'back') {
                                    $team1_bet_total = $team1_bet_total + $bet->exposureAmt;
                                    if (count($abc) >= 2) {
                                        $team_draw_bet_total = $team_draw_bet_total - $bet->bet_profit;
                                    }
                                    $team2_bet_total = $team2_bet_total + $bet->exposureAmt;
                                }
                                if ($bet->bet_side == 'lay') {
                                    $team1_bet_total = $team1_bet_total - ($bet->bet_amount);
                                    if (count($abc) >= 2) {
                                        $team_draw_bet_total = $team_draw_bet_total + ($bet->exposureAmt);
                                    }
                                    $team2_bet_total = $team2_bet_total - ($bet->bet_amount);
                                }
                            } else if (array_key_exists("teamname3", $abc) && array_key_exists("teamname2", $abc)) {
                                //bet on team1
                                if ($bet->bet_side == 'back') {
                                    $team1_bet_total = $team1_bet_total - $bet->bet_profit;
                                    if (count($abc) >= 2) {
                                        $team_draw_bet_total = $team_draw_bet_total + $bet->exposureAmt;
                                    }
                                    $team2_bet_total = $team2_bet_total + $bet->exposureAmt;
                                }
                                if ($bet->bet_side == 'lay') {
                                    $team1_bet_total = $team1_bet_total + ($bet->exposureAmt);
                                    if (count($abc) >= 2) {
                                        $team_draw_bet_total = $team_draw_bet_total - ($bet->bet_amount);
                                    }
                                    $team2_bet_total = $team2_bet_total - ($bet->bet_amount);
                                }
                            } else if (array_key_exists("teamname3", $abc) && array_key_exists("teamname1", $abc)) {
                                //bet on team2
                                if ($bet->bet_side == 'back') {
                                    $team2_bet_total = $team2_bet_total - ($bet->bet_profit);
                                    if (count($abc) >= 2) {
                                        $team_draw_bet_total = $team_draw_bet_total + $bet->exposureAmt;
                                    }
                                    $team1_bet_total = $team1_bet_total + $bet->exposureAmt;
                                }
                                if ($bet->bet_side == 'lay') {
                                    $team2_bet_total = $team2_bet_total + $bet->exposureAmt;
                                    if (count($abc) >= 2) {
                                        $team_draw_bet_total = $team_draw_bet_total - $bet->bet_amount;
                                    }
                                    $team1_bet_total = $team1_bet_total - $bet->bet_amount;
                                }
                            }
                        } else if (count($abc) == 1) {
                            if (array_key_exists("teamname1", $abc)) {
                                //bet on team2
                                if ($bet->bet_side == 'back') {
                                    $team2_bet_total = $team2_bet_total - $bet->bet_profit;
                                    $team1_bet_total = $team1_bet_total + $bet->exposureAmt;
                                }
                                if ($bet->bet_side == 'lay') {
                                    $team2_bet_total = $team2_bet_total + $bet->exposureAmt;
                                    $team1_bet_total = $team1_bet_total - $bet->bet_amount;
                                }
                            } else {
                                //bet on team1
                                if ($bet->bet_side == 'back') {
                                    $team1_bet_total = $team1_bet_total - $bet->bet_profit;
                                    $team2_bet_total = $team2_bet_total + $bet->exposureAmt;
                                }
                                if ($bet->bet_side == 'lay') {
                                    $team1_bet_total = $team1_bet_total + $bet->exposureAmt;
                                    $team2_bet_total = $team2_bet_total - $bet->bet_amount;
                                }
                            }
                        }
                    }

                    $bet_cls = '';
                    if ($team1_bet_total >= 0)
                        $bet_cls = 'text-color-green';
                    else
                        $bet_cls = 'text-color-red';
                    //end for bet calculation

                    $html .= '<div class="panel panel-default panel_content beige-bg-1">
				<h6>';
                    if ($match_data[$j]['inplay'] == 1) {
                        $html .= '<p class="blinkbtn"> <span class="blink_me"> IN PLAY </span> </p>';
                        $html .= '<a href="risk-management-details/' . $match_detail['id'] . '" class="text-color-black inplaytext">' . $match_detail['match_name'] . '<b>[' . $dt . ']</b></a>';
                    } else {
                        $html .= '<a href="risk-management-details/' . $match_detail['id'] . '" class="text-color-black">' . $match_detail['match_name'] . '<b>[' . $dt . ']</b></a>';
                    }

                    $html .= '</h6>
				<div class="row panel_row white-bg">';
                    if (isset($match_data[$j]['runners'][0]['ex']['availableToBack'][0]['price']) && isset($match_data[$j]['runners'][0]['ex']['availableToLay'][0]['price'])) {
                        $html .= '
						<div class="col-md-3 p-0">
							<div class="market_listitems">
								<div class="runner_details">
									<div class="r_title">' . $teamone . '</div>
									<div class="' . $bet_cls . '"><b>» ' . round($team1_bet_total) . '</b></div>
								</div>
								<div class="button_content">
									<button class="backbtn cyan-bg">' . $match_data[$j]['runners'][0]['ex']['availableToBack'][0]['price'] . '</button>
									<button class="laybtn pink-bg">' . $match_data[$j]['runners'][0]['ex']['availableToLay'][0]['price'] . '</button>
								</div>
							</div>
						</div>';
                    } else {
                        $html .= '
						<div class="col-md-3 p-0">
							<div class="market_listitems">
								<div class="runner_details">
									<div class="r_title">' . $teamone . '</div>
									<div class="' . $bet_cls . '"><b>» ' . round($team1_bet_total) . '</b></div>
								</div>
								<div class="button_content">
									<button class="backbtn cyan-bg">--</button>
									<button class="laybtn pink-bg">--</button>
								</div>
							</div>
						</div>';
                    }

                    $bet_cls = '';
                    if ($team2_bet_total >= 0)
                        $bet_cls = 'text-color-green';
                    else
                        $bet_cls = 'text-color-red';

                    if (isset($match_data[$j]['runners'][1]['ex']['availableToBack'][0]['price']) && isset($match_data[$j]['runners'][1]['ex']['availableToLay'][0]['price'])) {
                        $html .= '<div class="col-md-3 p-0">
							<div class="market_listitems">
								<div class="runner_details">
									<div class="r_title">' . $teamtwo . '</div>
									<div class="' . $bet_cls . '"><b>» ' . round($team2_bet_total) . '</b></div>
								</div>

								<div class="button_content">
									<button class="backbtn cyan-bg">' . $match_data[$j]['runners'][1]['ex']['availableToBack'][0]['price'] . '</button>
									<button class="laybtn pink-bg">' . $match_data[$j]['runners'][1]['ex']['availableToLay'][0]['price'] . '</button>
								</div>
							</div>
						</div>';
                    } else {
                        $html .= '<div class="col-md-3 p-0">
							<div class="market_listitems">
								<div class="runner_details">
									<div class="r_title">' . $teamtwo . '</div>
									<div class="' . $bet_cls . '"><b>» ' . round($team2_bet_total) . '</b></div>
								</div>

								<div class="button_content">
									<button class="backbtn cyan-bg">--</button>
									<button class="laybtn pink-bg">--</button>
								</div>
							</div>
						</div>';
                    }

                    /*if(isset($match_data[$j]['runners'][2]['ex']['availableToBack'][0]['price']) && isset($match_data[$j]['runners'][2]['ex']['availableToLay'][0]['price']))
					{
						$html.='
						<div class="col-md-3 p-0">
							<div class="market_listitems">
								<div class="runner_details">
									<div class="r_title">The Draw</div>
									<div class="'.$bet_cls.'"><b>» '.round($team1_bet_total).'</b></div>
								</div>

								<div class="button_content">
									<button class="backbtn cyan-bg">'.$match_data[$j]['runners'][2]['ex']['availableToBack'][2]['price'].'</button>
									<button class="laybtn pink-bg">'.$match_data[$j]['runners'][2]['ex']['availableToLay'][2]['price'].'</button>
								</div>
							</div>
						</div>';
					}*/
                    /*$bet_cls='';
					if($team_draw_bet_total>=0)
						$bet_cls='text-color-green';
					else
						$bet_cls='text-color-red';

					if(isset($match_data[$j]['runners'][2]['ex']['availableToBack'][2]['price']) && isset($match_data[$j]['runners'][2]['ex']['availableToLay'][2]['price']))
					{
						$html.='
						<div class="col-md-3 p-0">
							<div class="market_listitems">
								<div class="runner_details">
									<div class="r_title">The Draw</div>
									<div class="'.$bet_cls.'"><b>» '.round($team_draw_bet_total).'</b></div>
								</div>
								<div class="button_content">
									<button class="backbtn cyan-bg">'.$match_data[$j]['runners'][2]['ex']['availableToBack'][2]['price'].'</button>
									<button class="laybtn pink-bg">'.$match_data[$j]['runners'][2]['ex']['availableToLay'][2]['price'].'</button>
								</div>
							</div>
						</div>';
					}
					else
					{
						$html.='
						<div class="col-md-3 p-0">
							<div class="market_listitems">
								<div class="runner_details">
									<div class="r_title">The Draw</div>
									<div class="'.$bet_cls.'"><b>» '.round($team_draw_bet_total).'</b></div>
								</div>
							</div>
						</div>';
					}*/

                    $html .= '<div class="col-md-3 p-0">
							<div class="market_listitems">
								<div class="runner_details">
									<div class="r_title">Total Bets</div>
									<div><b>» </b></div>
								</div>
								<div class="button_content">
									<b><span>' . $total_bets . '<span></b>
								</div>
							</div>
							</div>
						</div>
					</div>';
                }
            } else {
                $html .= "~~";
                $html .= '<div class="panel panel-default panel_content beige-bg-1">
				<h6>No match found.</h6></div>';
            }
        } else {
            $html .= "~~";
            $html .= '<div class="panel panel-default panel_content beige-bg-1">
				<h6>No match found.</h6></div>';
        }

        //for soccer
        if ($imp_match_array_data_soccer != '') {
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
            $html .= "~~";
            if (sizeof($match_data) > 0) {
                for ($j = 0; $j < sizeof($match_data); $j++) {
                    $inplay_game = '';
                    $match_detail = Match::where('match_id', $match_data[$j]['marketId'])->first();
                    $split = explode(" v ", $match_detail['match_name']);
                    if (@count($split) > 0) {
                        @$teamone = $split[0];
                        if (isset($split[1]))
                            @$teamtwo = $split[1];
                        else
                            $teamtwo = '';
                    } else {
                        $teamone = '';
                        $teamtwo = '';
                    }
                    $inplay_game = '';
                    if (isset($match_data[$j]['inplay'])) {
                        if ($match_data[$j]['inplay'] == 1) {
                            $dt = '';
                            $style = "fir-col1-green";
                            $inplay_game = " <span style='color:green'>In-Play</span>";
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
                        $dt = $match_detail['match_date'];
                        $style = "fir-col1";
                        $inplay_game = '';
                    }
                    //bet calculation
                    $total_bets = 0;


                    $my_placed_bets = MyBets::where('match_id', $match_detail['event_id'])->where('bet_type', 'ODDS')->where('result_declare', 0)->where('isDeleted', 0)->whereIn('user_id', $all_child)->get();
                    $total_bets = sizeof($my_placed_bets);
                    $team2_bet_total = 0;
                    $team1_bet_total = 0;
                    $team_draw_bet_total = 0;

                    foreach ($my_placed_bets as $bet) {
                        /*$abc=json_decode($bet->extra,true);
					if(count($abc)>=2)
					{
						if (array_key_exists("teamname1",$abc) && array_key_exists("teamname2",$abc))
						{
							//bet on draw
							if($bet->bet_side=='back')
							{
								$team1_bet_total=$team1_bet_total+($bet->bet_odds/($bet->bet_amount-1));
								if(count($abc)>=2)
								{
									$team_draw_bet_total=$team_draw_bet_total-($bet->bet_odds/($bet->bet_amount-1));
								}
								$team2_bet_total=$team2_bet_total+($bet->bet_odds/($bet->bet_amount-1));
							}
							if($bet->bet_side=='lay')
							{
								$team1_bet_total=$team1_bet_total-($bet->bet_odds/($bet->bet_amount-1));
								if(count($abc)>=2)
								{
									$team_draw_bet_total=$team_draw_bet_total+$bet->bet_odds;
								}
								$team2_bet_total=$team2_bet_total-($bet->bet_odds/($bet->bet_amount-1));
							}
						}
						else if (array_key_exists("teamname3",$abc) && array_key_exists("teamname2",$abc))
						{
							//bet on team1
							if($bet->bet_side=='back')
							{
								$team1_bet_total=$team1_bet_total-$bet->bet_odds;
								if(count($abc)>=2)
								{
									$team_draw_bet_total=$team_draw_bet_total+($bet->bet_odds/($bet->bet_amount-1));
								}
								$team2_bet_total=$team2_bet_total+($bet->bet_odds/($bet->bet_amount-1));
							}
							if($bet->bet_side=='lay')
							{
								$team1_bet_total=$team1_bet_total+$bet->bet_odds;
								if(count($abc)>=2)
								{
									$team_draw_bet_total=$team_draw_bet_total-($bet->bet_odds/($bet->bet_amount-1));
								}
								$team2_bet_total=$team2_bet_total-($bet->bet_odds/($bet->bet_amount-1));
							}
						}
						else if (array_key_exists("teamname3",$abc) && array_key_exists("teamname1",$abc))
						{
							//bet on team2
							if($bet->bet_side=='back')
							{
								$team2_bet_total=$team2_bet_total-$bet->bet_odds;
								if(count($abc)>=2)
								{
									$team_draw_bet_total=$team_draw_bet_total+($bet->bet_odds/($bet->bet_amount-1));
								}
								$team1_bet_total=$team1_bet_total+($bet->bet_odds/($bet->bet_amount-1));
							}
							if($bet->bet_side=='lay')
							{
								$team2_bet_total=$team2_bet_total+$bet->bet_odds;
								if(count($abc)>=2)
								{
									$team_draw_bet_total=$team_draw_bet_total-($bet->bet_odds/($bet->bet_amount-1));
								}
								$team1_bet_total=$team1_bet_total-($bet->bet_odds/($bet->bet_amount-1));
							}
						}
					}
					else if(count($abc)==1)
					{
						if (array_key_exists("teamname1",$abc))
						{
							if($bet->bet_side=='back')
							{
								$team2_bet_total=$team2_bet_total-$bet->bet_odds;
								$team1_bet_total=$team1_bet_total+($bet->bet_odds/($bet->bet_amount-1));
							}
							if($bet->bet_side=='lay')
							{
								$team2_bet_total=$team2_bet_total+$bet->bet_odds;
								$team1_bet_total=$team1_bet_total-($bet->bet_odds/($bet->bet_amount-1));
							}
						}
						else
						{
							//bet on team1
							if($bet->bet_side=='back')
							{
								$team1_bet_total=$team1_bet_total-$bet->bet_odds;
								$team2_bet_total=$team2_bet_total+($bet->bet_odds/($bet->bet_amount-1));
							}
							if($bet->bet_side=='lay')
							{
								$team1_bet_total=$team1_bet_total+$bet->bet_odds;
								$team2_bet_total=$team2_bet_total-($bet->bet_odds/($bet->bet_amount-1));
							}
						}
					}*/

                        $abc = json_decode($bet->extra, true);
                        if (count($abc) >= 2) {
                            if (array_key_exists("teamname1", $abc) && array_key_exists("teamname2", $abc)) {
                                //bet on draw
                                if ($bet->bet_side == 'back') {
                                    $team1_bet_total = $team1_bet_total + $bet->exposureAmt;
                                    if (count($abc) >= 2) {
                                        $team_draw_bet_total = $team_draw_bet_total - $bet->bet_profit;
                                    }
                                    $team2_bet_total = $team2_bet_total + $bet->exposureAmt;
                                }
                                if ($bet->bet_side == 'lay') {
                                    $team1_bet_total = $team1_bet_total - ($bet->bet_amount);
                                    if (count($abc) >= 2) {
                                        $team_draw_bet_total = $team_draw_bet_total + ($bet->exposureAmt);
                                    }
                                    $team2_bet_total = $team2_bet_total - ($bet->bet_amount);
                                }
                            } else if (array_key_exists("teamname3", $abc) && array_key_exists("teamname2", $abc)) {
                                //bet on team1
                                if ($bet->bet_side == 'back') {
                                    $team1_bet_total = $team1_bet_total - $bet->bet_profit;
                                    if (count($abc) >= 2) {
                                        $team_draw_bet_total = $team_draw_bet_total + $bet->exposureAmt;
                                    }
                                    $team2_bet_total = $team2_bet_total + $bet->exposureAmt;
                                }
                                if ($bet->bet_side == 'lay') {
                                    $team1_bet_total = $team1_bet_total + ($bet->exposureAmt);
                                    if (count($abc) >= 2) {
                                        $team_draw_bet_total = $team_draw_bet_total - ($bet->bet_amount);
                                    }
                                    $team2_bet_total = $team2_bet_total - ($bet->bet_amount);
                                }
                            } else if (array_key_exists("teamname3", $abc) && array_key_exists("teamname1", $abc)) {
                                //bet on team2
                                if ($bet->bet_side == 'back') {
                                    $team2_bet_total = $team2_bet_total - ($bet->bet_profit);
                                    if (count($abc) >= 2) {
                                        $team_draw_bet_total = $team_draw_bet_total + $bet->exposureAmt;
                                    }
                                    $team1_bet_total = $team1_bet_total + $bet->exposureAmt;
                                }
                                if ($bet->bet_side == 'lay') {
                                    $team2_bet_total = $team2_bet_total + $bet->exposureAmt;
                                    if (count($abc) >= 2) {
                                        $team_draw_bet_total = $team_draw_bet_total - $bet->bet_amount;
                                    }
                                    $team1_bet_total = $team1_bet_total - $bet->bet_amount;
                                }
                            }
                        } else if (count($abc) == 1) {
                            if (array_key_exists("teamname1", $abc)) {
                                //bet on team2
                                if ($bet->bet_side == 'back') {
                                    $team2_bet_total = $team2_bet_total - $bet->bet_profit;
                                    $team1_bet_total = $team1_bet_total + $bet->exposureAmt;
                                }
                                if ($bet->bet_side == 'lay') {
                                    $team2_bet_total = $team2_bet_total + $bet->exposureAmt;
                                    $team1_bet_total = $team1_bet_total - $bet->bet_amount;
                                }
                            } else {
                                //bet on team1
                                if ($bet->bet_side == 'back') {
                                    $team1_bet_total = $team1_bet_total - $bet->bet_profit;
                                    $team2_bet_total = $team2_bet_total + $bet->exposureAmt;
                                }
                                if ($bet->bet_side == 'lay') {
                                    $team1_bet_total = $team1_bet_total + $bet->exposureAmt;
                                    $team2_bet_total = $team2_bet_total - $bet->bet_amount;
                                }
                            }
                        }
                    }

                    $bet_cls = '';
                    if ($team1_bet_total >= 0)
                        $bet_cls = 'text-color-green';
                    else
                        $bet_cls = 'text-color-red';

                    //end for bet calculation
                    $html .= '<div class="panel panel-default panel_content beige-bg-1">
				<h6>';
                    if ($match_data[$j]['inplay'] == 1) {
                        $html .= '<p class="blinkbtn"> <span class="blink_me"> IN PLAY </span> </p>';
                        $html .= '<a href="risk-management-details/' . $match_detail['id'] . '" class="text-color-black inplaytext">' . $match_detail['match_name'] . '<b>' . $dt . '</b></a>';
                    } else {
                        $html .= '<a href="risk-management-details/' . $match_detail['id'] . '" class="text-color-black">' . $match_detail['match_name'] . '<b>' . $dt . '</b></a>';
                    }
                    $html .= '</h6>
				<div class="row panel_row white-bg">';
                    if (isset($match_data[$j]['runners'][0]['ex']['availableToBack'][0]['price']) && isset($match_data[$j]['runners'][0]['ex']['availableToLay'][0]['price'])) {
                        $html .= '
						<div class="col-md-3 p-0">
							<div class="market_listitems">
								<div class="runner_details">
									<div class="r_title">' . $teamone . '</div>
									<div class="' . $bet_cls . '"><b>» ' . round($team1_bet_total) . '</b></div>
								</div>

								<div class="button_content">
									<button class="backbtn cyan-bg">' . $match_data[$j]['runners'][0]['ex']['availableToBack'][0]['price'] . '</button>
									<button class="laybtn pink-bg">' . $match_data[$j]['runners'][0]['ex']['availableToLay'][0]['price'] . '</button>
								</div>
							</div>
						</div>';
                    } else {
                        $html .= '
						<div class="col-md-3 p-0">
							<div class="market_listitems">
								<div class="runner_details">
									<div class="r_title">' . $teamone . '</div>
									<div class="' . $bet_cls . '"><b>» ' . round($team1_bet_total) . '</b></div>
								</div>

								<div class="button_content">
									<button class="backbtn cyan-bg">--</button>
									<button class="laybtn pink-bg">--</button>
								</div>
							</div>
						</div>';
                    }

                    $bet_cls = '';
                    if ($team2_bet_total >= 0)
                        $bet_cls = 'text-color-green';
                    else
                        $bet_cls = 'text-color-red';

                    if (isset($match_data[$j]['runners'][1]['ex']['availableToBack'][0]['price']) && isset($match_data[$j]['runners'][1]['ex']['availableToLay'][0]['price'])) {
                        $html .= '<div class="col-md-3 p-0">
						<div class="market_listitems">
							<div class="runner_details">
								<div class="r_title">' . $teamtwo . '</div>
								<div class="' . $bet_cls . '"><b>» ' . round($team2_bet_total) . '</b></div>
							</div>

							<div class="button_content">
								<button class="backbtn cyan-bg">' . $match_data[$j]['runners'][1]['ex']['availableToBack'][0]['price'] . '</button>
								<button class="laybtn pink-bg">' . $match_data[$j]['runners'][1]['ex']['availableToLay'][0]['price'] . '</button>
							</div>
						</div>
						</div>';
                    } else {
                        $html .= '<div class="col-md-3 p-0">
							<div class="market_listitems">
								<div class="runner_details">
									<div class="r_title">' . $teamtwo . '</div>
									<div class="' . $bet_cls . '"><b>» ' . round($team2_bet_total) . '</b></div>
								</div>

								<div class="button_content">
									<button class="backbtn cyan-bg">--</button>
									<button class="laybtn pink-bg">--</button>
								</div>
							</div>
						</div>';
                    }

                    $bet_cls = '';
                    if ($team_draw_bet_total >= 0)
                        $bet_cls = 'text-color-green';
                    else
                        $bet_cls = 'text-color-red';

                    if (isset($match_data[$j]['runners'][2]['ex']['availableToBack'][2]['price']) && isset($match_data[$j]['runners'][2]['ex']['availableToLay'][2]['price'])) {
                        $html .= '
						<div class="col-md-3 p-0">
							<div class="market_listitems">
								<div class="runner_details">
									<div class="r_title">The Draw</div>
									<div class="' . $bet_cls . '"><b>» ' . round($team_draw_bet_total) . '</b></div>
								</div>
								<div class="button_content">
									<button class="backbtn cyan-bg">' . $match_data[$j]['runners'][2]['ex']['availableToBack'][2]['price'] . '</button>
									<button class="laybtn pink-bg">' . $match_data[$j]['runners'][2]['ex']['availableToLay'][2]['price'] . '</button>
								</div>
							</div>
						</div>';
                    } else {
                        $html .= '
						<div class="col-md-3 p-0">
							<div class="market_listitems">
								<div class="runner_details">
									<div class="r_title">The Draw</div>
									<div class="' . $bet_cls . '"><b>» ' . round($team_draw_bet_total) . '</b></div>
								</div>

								<div class="button_content">
									<button class="backbtn cyan-bg">--</button>
									<button class="laybtn pink-bg">--</button>
								</div>
							</div>
						</div>';
                    }

                    $html .= '<div class="col-md-3 p-0">
						<div class="market_listitems">
							<div class="runner_details">
								<div class="r_title">Total Bets</div>
								<div><b>» </b></div>
							</div>

							<div class="button_content">
								<b><span>' . $total_bets . '</span></b>
							</div>
						</div>
						</div>
					</div>
				</div>';
                }
            } else {
                $html .= "~~";
                $html .= '<div class="panel panel-default panel_content beige-bg-1">
					<h6>No match found.</h6></div>';
            }
        } else {
            $html .= "~~";
            $html .= '<div class="panel panel-default panel_content beige-bg-1">
					<h6>No match found.</h6></div>';
        }
        return $html;
    }

    public function risk_management_details($id)
    {
        $managetv = ManageTv::latest()->first();
        //$loginUser = Auth::user();

        $loginUser = Auth::user();
        $ag_id = $loginUser->id;
        $all_child = $this->GetChildofAgent($ag_id);

        $website = UsersAccount::getWebsite();

        if ($loginUser->agent_level == 'COM') {
            $user = 'AD';
        } elseif ($loginUser->agent_level == 'AD') {
            $user = 'SP';
        } elseif ($loginUser->agent_level == 'SP') {
            $user = 'SMDL';
        } elseif ($loginUser->agent_level == 'SMDL') {
            $user = 'MDL';
        } elseif ($loginUser->agent_level == 'MDL') {
            $user = 'DL';
        } elseif ($loginUser->agent_level == 'DL') {
            $user = 'PL';
        }
        $matchList = Match::where('id', $id)->first();

//        echo __FILE__." at line ".__LINE__."<br>";echo "<pre>";print_r($matchList->toArray());die();

        $match_data = app('App\Http\Controllers\RestApi')->getSingleCricketMatchData($matchList->event_id, $matchList->match_id, $matchList->sports_id);

        $inplay = 'False';

        if (isset($match_data['t1']) && isset($match_data['t1'][0][0]['iplay']) && $match_data['t1'][0][0]['iplay'] === 'True') {
            $inplay = 'True';
        } else if (isset($match_data[0]['inplay']) != '') {
            $inplay = $match_data[0]['inplay'];
            if ($inplay == 1)
                $inplay = 'True';
            else
                $inplay = 'false';
        }

//        echo __FILE__." at line ".__LINE__."<br>";echo "<pre>";print_r($inplay);die();

        $list = User::where('parentid', $loginUser->id)->orderBy('user_name')->get();
        //odds bet
        $my_placed_bets = MyBets::where('match_id', $matchList->event_id)->where('bet_type', 'ODDS')->where('result_declare', 0)->whereIn('user_id', $all_child)->get();
        $html = '';
        foreach ($my_placed_bets as $bet) {
            $player = User::where('id', $bet->user_id)->where('agent_level', 'PL')->first();
            /*echo $player;
			exit;*/
            $getUserparent = User::where('id', $player->parentid)->first();
            /*echo $getUserparent;
			exit;*/
            $ad = '-';
            $sp = '-';
            $smdl = '-';
            $mdl = '-';
            $dl = '-';
            $com = '-';
            if ($getUserparent->agent_level == 'AD') {
                $ad = $getUserparent->user_name;
            } elseif ($getUserparent->agent_level == 'SP') {
                $sp = $getUserparent->user_name;
            } elseif ($getUserparent->agent_level == 'SMDL') {
                $smdl = $getUserparent->user_name;
            } elseif ($getUserparent->agent_level == 'MDL') {
                $mdl = $getUserparent->user_name;
            } elseif ($getUserparent->agent_level == 'DL') {
                $dl = $getUserparent->user_name;
            } elseif ($getUserparent->agent_level == 'COM') {
                $com = $getUserparent->user_name;
            }
            if (!empty($getUserparent->parentid)) {
                /*echo "aab->";
        	echo $getUserparent->parentid;
        	exit;*/
                $getUserparent2 = User::where('id', $getUserparent->parentid)->first();
                if ($getUserparent2->agent_level == 'AD') {
                    $ad = $getUserparent2->user_name;
                } elseif ($getUserparent2->agent_level == 'SP') {
                    $sp = $getUserparent2->user_name;
                } elseif ($getUserparent2->agent_level == 'SMDL') {
                    $smdl = $getUserparent2->user_name;
                } elseif ($getUserparent2->agent_level == 'MDL') {
                    $mdl = $getUserparent2->user_name;
                } elseif ($getUserparent2->agent_level == 'DL') {
                    $dl = $getUserparent2->user_name;
                } elseif ($getUserparent2->agent_level == 'COM') {
                    $com = $getUserparent2->user_name;
                }
            }

            if (!empty($getUserparent2->parentid)) {
                /*	echo "aam->";
      	echo $getUserparent2->parentid;
      	exit;*/
                $getUserparent3 = User::where('id', $getUserparent2->parentid)->first();
                if ($getUserparent3->agent_level == 'AD') {
                    $ad = $getUserparent3->user_name;
                } elseif ($getUserparent3->agent_level == 'SP') {
                    $sp = $getUserparent3->user_name;
                } elseif ($getUserparent3->agent_level == 'SMDL') {
                    $smdl = $getUserparent3->user_name;
                } elseif ($getUserparent3->agent_level == 'MDL') {
                    $mdl = $getUserparent3->user_name;
                } elseif ($getUserparent3->agent_level == 'DL') {
                    $dl = $getUserparent3->user_name;
                } elseif ($getUserparent3->agent_level == 'COM') {
                    $com = $getUserparent3->user_name;
                }
            }

            if (!empty($getUserparent3->parentid)) {
                /*echo "aam->";
      	echo $getUserparent3->parentid;
      	exit;*/
                $getUserparent4 = User::where('id', $getUserparent3->parentid)->first();

                if ($getUserparent4->agent_level == 'AD') {
                    $ad = $getUserparent4->user_name;
                } elseif ($getUserparent4->agent_level == 'SP') {
                    $sp = $getUserparent4->user_name;
                } elseif ($getUserparent4->agent_level == 'SMDL') {
                    $smdl = $getUserparent4->user_name;
                } elseif ($getUserparent4->agent_level == 'MDL') {
                    $mdl = $getUserparent4->user_name;
                } elseif ($getUserparent4->agent_level == 'DL') {
                    $dl = $getUserparent4->user_name;
                } elseif ($getUserparent4->agent_level == 'COM') {
                    $com = $getUserparent4->user_name;
                }
            }

            if (!empty($getUserparent4->parentid)) {
                /*echo "aam->";
      	echo $getUserparent4->parentid;
      	exit;*/
                $getUserparent5 = User::where('id', $getUserparent4->parentid)->first();

                if ($getUserparent5->agent_level == 'AD') {
                    $ad = $getUserparent5->user_name;
                } elseif ($getUserparent5->agent_level == 'SP') {
                    $sp = $getUserparent5->user_name;
                } elseif ($getUserparent5->agent_level == 'SMDL') {
                    $smdl = $getUserparent5->user_name;
                } elseif ($getUserparent5->agent_level == 'MDL') {
                    $mdl = $getUserparent5->user_name;
                } elseif ($getUserparent5->agent_level == 'DL') {
                    $dl = $getUserparent5->user_name;
                } elseif ($getUserparent5->agent_level == 'COM') {
                    $com = $getUserparent5->user_name;
                }
            }

            /*echo "-->".$ad;
      exit;*/

            if (!empty($getUserparent5->parentid)) {
                $getUserparent6 = User::where('id', $getUserparent5->parentid)->first();

                if ($getUserparent6->agent_level == 'AD') {
                    $ad = $getUserparent6->user_name;
                } elseif ($getUserparent6->agent_level == 'SP') {
                    $sp = $getUserparent6->user_name;
                } elseif ($getUserparent6->agent_level == 'SMDL') {
                    $smdl = $getUserparent6->user_name;
                } elseif ($getUserparent6->agent_level == 'MDL') {
                    $mdl = $getUserparent6->user_name;
                } elseif ($getUserparent6->agent_level == 'DL') {
                    $dl = $getUserparent6->user_name;
                } elseif ($getUserparent6->agent_level == 'COM') {
                    $com = $getUserparent6->user_name;
                }
            }

            $bet_type_cls = '';
            $bet_type = '';
            if ($bet->bet_side == 'lay') {
                $bet_type_cls = 'pink-bg';
                $bet_type = 'Lay';
            } else {
                $bet_type_cls = 'cyan-bg';
                $bet_type = 'Back';
            }

            $is_delete = '';
            if ($bet->isDeleted == 0) {
                $is_delete .= '<a id="delete_row_' . $bet->id . '" onclick="delete_bet(' . $bet->id . ')" class="btn times_btn red-bg text-color-white"><i class="fas fa-times"></i></a>';
                $is_delete .= '<a style="display:none" id="rollback_row_' . $bet->id . '" onclick="rollback_bet(' . $bet->id . ')" class="btn times_btn green-bg text-color-white"><i class="fas fa-check"></i></a>';
            } else {
                $is_delete .= '<a id="rollback_row_' . $bet->id . '" onclick="rollback_bet(' . $bet->id . ')" class="btn times_btn green-bg text-color-white"><i class="fas fa-check"></i></a>';
                $is_delete .= '<a style="display:none" id="delete_row_' . $bet->id . '" onclick="delete_bet(' . $bet->id . ')" class="btn times_btn red-bg text-color-white"><i class="fas fa-times"></i></a>';
            }

            $binfo = $bet->browser_details . ' &#013; ' . $bet->ip_address;
            $html .= '<tr class="' . $bet_type_cls . '">
			    <td><input type="checkbox" id="select_all" name="filter-checkbox" value=""></td>
                <td class="text-center"><b>' . ucfirst($player['user_name']) . '[' . $player['first_name'] . ']</b></td>
                <td class="text-center"><b>' . $bet->team_name . '</b></td>
				<td class="text-center">' . $bet_type . '</td>
                <td class="text-center"><b>' . $bet->bet_odds . '</b></td>
                <td class="text-center"><b>' . $bet->bet_amount . '</b></td>
                <td class="text-center"><b>' . $bet->bet_profit . '</b></td>
                <td>' . date('d-m-Y H:i:s A', strtotime($bet->created_at)) . '</td>
                <td class="text-center"><i class="fas fa-mobile text-color-red" data-toggle="tooltip" data-placement="top" title="' . $binfo . '"></i></td>';
            if ($loginUser->agent_level == 'COM') {
                $html .= '<td id="action_' . $bet->id . '">
	                	' . $is_delete . '
	               	</td>';
            }
            if ($loginUser->agent_level == 'COM') {
                $html .= '<td class="text-center">' . $com . '</td>';
            }
            if ($loginUser->agent_level == 'AD' || $loginUser->agent_level == 'COM') {
                $html .= '<td class="text-center">' . $ad . '</td>';
            }
            if ($loginUser->agent_level == 'SP' || $loginUser->agent_level == 'COM' || $loginUser->agent_level == 'AD') {
                $html .= '<td class="text-center">' . $sp . '</td>';
            }
            if ($loginUser->agent_level == 'SMDL' || $loginUser->agent_level == 'COM' || $loginUser->agent_level == 'AD' || $loginUser->agent_level == 'SP') {
                $html .= '<td class="text-center">' . $smdl . '</td>';
            }
            if ($loginUser->agent_level == 'MDL' || $loginUser->agent_level == 'COM' || $loginUser->agent_level == 'AD' || $loginUser->agent_level == 'SP' || $loginUser->agent_level == 'SMDL') {
                $html .= '<td class="text-center">' . $mdl . '</td>';
            }
            if ($loginUser->agent_level == 'DL' || $loginUser->agent_level == 'COM' || $loginUser->agent_level == 'AD' || $loginUser->agent_level == 'SP' || $loginUser->agent_level == 'SMDL' || $loginUser->agent_level == 'MDL') {
                $html .= '<td class="text-center">' . $dl . '</td>';
            }

            $html .= '</tr>';
        }

        //bookmaker bet
        $my_placed_bets_BM = MyBets::where('match_id', $matchList->event_id)->where('bet_type', 'BOOKMAKER')->where('result_declare', 0)->whereIn('user_id', $all_child)->get();
        $html_BM = '';
        foreach ($my_placed_bets_BM as $bet) {
            $player = User::where('id', $bet->user_id)->where('agent_level', 'PL')->first();
            $bet_type_cls = '';
            $bet_type = '';
            if ($bet->bet_side == 'lay') {
                $bet_type_cls = 'pink-bg';
                $bet_type = 'Lay';
            } else {
                $bet_type_cls = 'cyan-bg';
                $bet_type = 'Back';
            }

            /*echo $player;
			exit;*/
            $getUserparent = User::where('id', $player->parentid)->first();
            /*echo $getUserparent;
			exit;*/
            $ad = '-';
            $sp = '-';
            $smdl = '-';
            $mdl = '-';
            $dl = '-';
            $com = '-';
            if ($getUserparent->agent_level == 'AD') {
                $ad = $getUserparent->user_name;
            } elseif ($getUserparent->agent_level == 'SP') {
                $sp = $getUserparent->user_name;
            } elseif ($getUserparent->agent_level == 'SMDL') {
                $smdl = $getUserparent->user_name;
            } elseif ($getUserparent->agent_level == 'MDL') {
                $mdl = $getUserparent->user_name;
            } elseif ($getUserparent->agent_level == 'DL') {
                $dl = $getUserparent->user_name;
            } elseif ($getUserparent->agent_level == 'COM') {
                $com = $getUserparent->user_name;
            }
            if (!empty($getUserparent->parentid)) {
                /*echo "aab->";
        	echo $getUserparent->parentid;
        	exit;*/
                $getUserparent2 = User::where('id', $getUserparent->parentid)->first();
                if ($getUserparent2->agent_level == 'AD') {
                    $ad = $getUserparent2->user_name;
                } elseif ($getUserparent2->agent_level == 'SP') {
                    $sp = $getUserparent2->user_name;
                } elseif ($getUserparent2->agent_level == 'SMDL') {
                    $smdl = $getUserparent2->user_name;
                } elseif ($getUserparent2->agent_level == 'MDL') {
                    $mdl = $getUserparent2->user_name;
                } elseif ($getUserparent2->agent_level == 'DL') {
                    $dl = $getUserparent2->user_name;
                } elseif ($getUserparent2->agent_level == 'COM') {
                    $com = $getUserparent2->user_name;
                }
            }

            if (!empty($getUserparent2->parentid)) {
                /*	echo "aam->";
      	echo $getUserparent2->parentid;
      	exit;*/
                $getUserparent3 = User::where('id', $getUserparent2->parentid)->first();
                if ($getUserparent3->agent_level == 'AD') {
                    $ad = $getUserparent3->user_name;
                } elseif ($getUserparent3->agent_level == 'SP') {
                    $sp = $getUserparent3->user_name;
                } elseif ($getUserparent3->agent_level == 'SMDL') {
                    $smdl = $getUserparent3->user_name;
                } elseif ($getUserparent3->agent_level == 'MDL') {
                    $mdl = $getUserparent3->user_name;
                } elseif ($getUserparent3->agent_level == 'DL') {
                    $dl = $getUserparent3->user_name;
                } elseif ($getUserparent3->agent_level == 'COM') {
                    $com = $getUserparent3->user_name;
                }
            }

            if (!empty($getUserparent3->parentid)) {
                /*echo "aam->";
      	echo $getUserparent3->parentid;
      	exit;*/
                $getUserparent4 = User::where('id', $getUserparent3->parentid)->first();

                if ($getUserparent4->agent_level == 'AD') {
                    $ad = $getUserparent4->user_name;
                } elseif ($getUserparent4->agent_level == 'SP') {
                    $sp = $getUserparent4->user_name;
                } elseif ($getUserparent4->agent_level == 'SMDL') {
                    $smdl = $getUserparent4->user_name;
                } elseif ($getUserparent4->agent_level == 'MDL') {
                    $mdl = $getUserparent4->user_name;
                } elseif ($getUserparent4->agent_level == 'DL') {
                    $dl = $getUserparent4->user_name;
                } elseif ($getUserparent4->agent_level == 'COM') {
                    $com = $getUserparent4->user_name;
                }
            }

            if (!empty($getUserparent4->parentid)) {
                /*echo "aam->";
      	echo $getUserparent4->parentid;
      	exit;*/
                $getUserparent5 = User::where('id', $getUserparent4->parentid)->first();

                if ($getUserparent5->agent_level == 'AD') {
                    $ad = $getUserparent5->user_name;
                } elseif ($getUserparent5->agent_level == 'SP') {
                    $sp = $getUserparent5->user_name;
                } elseif ($getUserparent5->agent_level == 'SMDL') {
                    $smdl = $getUserparent5->user_name;
                } elseif ($getUserparent5->agent_level == 'MDL') {
                    $mdl = $getUserparent5->user_name;
                } elseif ($getUserparent5->agent_level == 'DL') {
                    $dl = $getUserparent5->user_name;
                } elseif ($getUserparent5->agent_level == 'COM') {
                    $com = $getUserparent5->user_name;
                }
            }

            /*echo "-->".$ad;
      exit;*/

            if (!empty($getUserparent5->parentid)) {
                $getUserparent6 = User::where('id', $getUserparent5->parentid)->first();

                if ($getUserparent6->agent_level == 'AD') {
                    $ad = $getUserparent6->user_name;
                } elseif ($getUserparent6->agent_level == 'SP') {
                    $sp = $getUserparent6->user_name;
                } elseif ($getUserparent6->agent_level == 'SMDL') {
                    $smdl = $getUserparent6->user_name;
                } elseif ($getUserparent6->agent_level == 'MDL') {
                    $mdl = $getUserparent6->user_name;
                } elseif ($getUserparent6->agent_level == 'DL') {
                    $dl = $getUserparent6->user_name;
                } elseif ($getUserparent6->agent_level == 'COM') {
                    $com = $getUserparent6->user_name;
                }
            }

            $is_delete = '';
            if ($bet->isDeleted == 0) {
                $is_delete .= '<a id="delete_row_' . $bet->id . '" onclick="delete_bet(' . $bet->id . ')" class="btn times_btn red-bg text-color-white"><i class="fas fa-times"></i></a>';
                $is_delete .= '<a style="display:none" id="rollback_row_' . $bet->id . '" onclick="rollback_bet(' . $bet->id . ')" class="btn times_btn green-bg text-color-white"><i class="fas fa-check"></i></a>';
            } else {
                $is_delete .= '<a id="rollback_row_' . $bet->id . '" onclick="rollback_bet(' . $bet->id . ')" class="btn times_btn green-bg text-color-white"><i class="fas fa-check"></i></a>';
                $is_delete .= '<a style="display:none" id="delete_row_' . $bet->id . '" onclick="delete_bet(' . $bet->id . ')" class="btn times_btn red-bg text-color-white"><i class="fas fa-times"></i></a>';
            }
            $binfo = $bet->browser_details . ' &#013; ' . $bet->ip_address;
            $html_BM .= '<tr class="' . $bet_type_cls . '">
			    <td><input type="checkbox" id="select_all" name="filter-checkbox" value=""></td>
                <td class="text-center"><b>' . ucfirst($player['user_name']) . '[' . $player['first_name'] . ']</b></td>
                <td class="text-center"><b>' . $bet->team_name . '</b></td>
				<td class="text-center">' . $bet_type . '</td>
                <td class="text-center"><b>' . $bet->bet_odds . '</b></td>
                <td class="text-center"><b>' . $bet->bet_amount . '</b></td>
                <td class="text-center"><b>' . $bet->bet_profit . '</b></td>
                <td><b>Matched</b></td>
                <td>' . date('d-m-Y H:i:s A', strtotime($bet->created_at)) . '</td>
                <td class="text-center">' . $matchList->event_id . '</td>
                <td class="text-center"><i class="fas fa-mobile text-color-red" data-toggle="tooltip" data-placement="top" title="' . $binfo . '"></i></td>';
            if ($loginUser->agent_level == 'COM') {
                $html_BM .= '<td id="action_' . $bet->id . '">
                	' . $is_delete . '
               	</td>';
            }
            if ($loginUser->agent_level == 'COM') {
                $html_BM .= '<td class="text-center">' . $com . '</td>';
            }
            if ($loginUser->agent_level == 'AD' || $loginUser->agent_level == 'COM') {
                $html_BM .= '<td class="text-center">' . $ad . '</td>';
            }
            if ($loginUser->agent_level == 'SP' || $loginUser->agent_level == 'COM' || $loginUser->agent_level == 'AD') {
                $html_BM .= '<td class="text-center">' . $sp . '</td>';
            }
            if ($loginUser->agent_level == 'SMDL' || $loginUser->agent_level == 'COM' || $loginUser->agent_level == 'AD' || $loginUser->agent_level == 'SP') {
                $html_BM .= '<td class="text-center">' . $smdl . '</td>';
            }
            if ($loginUser->agent_level == 'MDL' || $loginUser->agent_level == 'COM' || $loginUser->agent_level == 'AD' || $loginUser->agent_level == 'SP' || $loginUser->agent_level == 'SMDL') {
                $html_BM .= '<td class="text-center">' . $mdl . '</td>';
            }
            if ($loginUser->agent_level == 'DL' || $loginUser->agent_level == 'COM' || $loginUser->agent_level == 'AD' || $loginUser->agent_level == 'SP' || $loginUser->agent_level == 'SMDL' || $loginUser->agent_level == 'MDL') {
                $html_BM .= '<td class="text-center">' . $dl . '</td>';
            }

            $html_BM .= '</tr>';
        }
        //Fancy bet
        $my_placed_bets_fancy = MyBets::where('match_id', $matchList->event_id)->where('bet_type', 'SESSION')->where('result_declare', 0)->whereIn('user_id', $all_child)->get();

        $html_Fancy = '';
        foreach ($my_placed_bets_fancy as $bet) {
            $player = User::where('id', $bet->user_id)->where('agent_level', 'PL')->first();
            $bet_type_cls = '';
            $bet_type = '';
            if ($bet->bet_side == 'lay') {
                $bet_type_cls = 'pink-bg';
                $bet_type = 'N';
            } else {
                $bet_type_cls = 'cyan-bg';
                $bet_type = 'Y';
            }
            $getUserparent = User::where('id', $player->parentid)->first();
            /*echo $getUserparent;
			exit;*/
            $ad = '-';
            $sp = '-';
            $smdl = '-';
            $mdl = '-';
            $dl = '-';
            $com = '-';
            if ($getUserparent->agent_level == 'AD') {
                $ad = $getUserparent->user_name;
            } elseif ($getUserparent->agent_level == 'SP') {
                $sp = $getUserparent->user_name;
            } elseif ($getUserparent->agent_level == 'SMDL') {
                $smdl = $getUserparent->user_name;
            } elseif ($getUserparent->agent_level == 'MDL') {
                $mdl = $getUserparent->user_name;
            } elseif ($getUserparent->agent_level == 'DL') {
                $dl = $getUserparent->user_name;
            } elseif ($getUserparent->agent_level == 'COM') {
                $com = $getUserparent->user_name;
            }
            if (!empty($getUserparent->parentid)) {
                /*echo "aab->";
        	echo $getUserparent->parentid;
        	exit;*/
                $getUserparent2 = User::where('id', $getUserparent->parentid)->first();
                if ($getUserparent2->agent_level == 'AD') {
                    $ad = $getUserparent2->user_name;
                } elseif ($getUserparent2->agent_level == 'SP') {
                    $sp = $getUserparent2->user_name;
                } elseif ($getUserparent2->agent_level == 'SMDL') {
                    $smdl = $getUserparent2->user_name;
                } elseif ($getUserparent2->agent_level == 'MDL') {
                    $mdl = $getUserparent2->user_name;
                } elseif ($getUserparent2->agent_level == 'DL') {
                    $dl = $getUserparent2->user_name;
                } elseif ($getUserparent2->agent_level == 'COM') {
                    $com = $getUserparent2->user_name;
                }
            }

            if (!empty($getUserparent2->parentid)) {
                /*	echo "aam->";
      	echo $getUserparent2->parentid;
      	exit;*/
                $getUserparent3 = User::where('id', $getUserparent2->parentid)->first();
                if ($getUserparent3->agent_level == 'AD') {
                    $ad = $getUserparent3->user_name;
                } elseif ($getUserparent3->agent_level == 'SP') {
                    $sp = $getUserparent3->user_name;
                } elseif ($getUserparent3->agent_level == 'SMDL') {
                    $smdl = $getUserparent3->user_name;
                } elseif ($getUserparent3->agent_level == 'MDL') {
                    $mdl = $getUserparent3->user_name;
                } elseif ($getUserparent3->agent_level == 'DL') {
                    $dl = $getUserparent3->user_name;
                } elseif ($getUserparent3->agent_level == 'COM') {
                    $com = $getUserparent3->user_name;
                }
            }

            if (!empty($getUserparent3->parentid)) {
                /*echo "aam->";
      	echo $getUserparent3->parentid;
      	exit;*/
                $getUserparent4 = User::where('id', $getUserparent3->parentid)->first();

                if ($getUserparent4->agent_level == 'AD') {
                    $ad = $getUserparent4->user_name;
                } elseif ($getUserparent4->agent_level == 'SP') {
                    $sp = $getUserparent4->user_name;
                } elseif ($getUserparent4->agent_level == 'SMDL') {
                    $smdl = $getUserparent4->user_name;
                } elseif ($getUserparent4->agent_level == 'MDL') {
                    $mdl = $getUserparent4->user_name;
                } elseif ($getUserparent4->agent_level == 'DL') {
                    $dl = $getUserparent4->user_name;
                } elseif ($getUserparent4->agent_level == 'COM') {
                    $com = $getUserparent4->user_name;
                }
            }

            if (!empty($getUserparent4->parentid)) {
                /*echo "aam->";
      	echo $getUserparent4->parentid;
      	exit;*/
                $getUserparent5 = User::where('id', $getUserparent4->parentid)->first();

                if ($getUserparent5->agent_level == 'AD') {
                    $ad = $getUserparent5->user_name;
                } elseif ($getUserparent5->agent_level == 'SP') {
                    $sp = $getUserparent5->user_name;
                } elseif ($getUserparent5->agent_level == 'SMDL') {
                    $smdl = $getUserparent5->user_name;
                } elseif ($getUserparent5->agent_level == 'MDL') {
                    $mdl = $getUserparent5->user_name;
                } elseif ($getUserparent5->agent_level == 'DL') {
                    $dl = $getUserparent5->user_name;
                } elseif ($getUserparent5->agent_level == 'COM') {
                    $com = $getUserparent5->user_name;
                }
            }

            /*echo "-->".$ad;
      exit;*/

            if (!empty($getUserparent5->parentid)) {
                $getUserparent6 = User::where('id', $getUserparent5->parentid)->first();

                if ($getUserparent6->agent_level == 'AD') {
                    $ad = $getUserparent6->user_name;
                } elseif ($getUserparent6->agent_level == 'SP') {
                    $sp = $getUserparent6->user_name;
                } elseif ($getUserparent6->agent_level == 'SMDL') {
                    $smdl = $getUserparent6->user_name;
                } elseif ($getUserparent6->agent_level == 'MDL') {
                    $mdl = $getUserparent6->user_name;
                } elseif ($getUserparent6->agent_level == 'DL') {
                    $dl = $getUserparent6->user_name;
                } elseif ($getUserparent6->agent_level == 'COM') {
                    $com = $getUserparent6->user_name;
                }
            }
            $is_delete = '';
            if ($bet->isDeleted == 0) {
                $is_delete .= '<a id="delete_row_' . $bet->id . '" onclick="delete_bet(' . $bet->id . ')" class="btn times_btn red-bg text-color-white"><i class="fas fa-times"></i></a>';
                $is_delete .= '<a style="display:none" id="rollback_row_' . $bet->id . '" onclick="rollback_bet(' . $bet->id . ')" class="btn times_btn green-bg text-color-white"><i class="fas fa-check"></i></a>';
            } else {
                $is_delete .= '<a id="rollback_row_' . $bet->id . '" onclick="rollback_bet(' . $bet->id . ')" class="btn times_btn green-bg text-color-white"><i class="fas fa-check"></i></a>';
                $is_delete .= '<a style="display:none" id="delete_row_' . $bet->id . '" onclick="delete_bet(' . $bet->id . ')" class="btn times_btn red-bg text-color-white"><i class="fas fa-times"></i></a>';
            }

            $binfo = $bet->browser_details . ' &#013; ' . $bet->ip_address;
            $html_Fancy .= '<tr class="' . $bet_type_cls . '">
			    <td><input type="checkbox" id="select_all" name="filter-checkbox" value=""></td>
                <td class="text-center"><b>' . ucfirst($player['user_name']) . '[' . $player['first_name'] . ']</b></td>
                <td class="text-center"><b>' . $bet->team_name . '</b></td>
				<td class="text-center">' . $bet_type . '</td>
                <td class="text-center"><b>' . $bet->bet_odds . '</b></td>
                <td class="text-center"><b>' . $bet->bet_amount . '</b></td>
				<td><b>' . date('d-m-Y H:i:s A', strtotime($bet->created_at)) . '</b></td>
				<td class="text-center"><i class="fas fa-mobile text-color-red" data-toggle="tooltip" data-placement="top" title="' . $binfo . '"></i></td>';
            if ($loginUser->agent_level == 'COM') {
                $html_Fancy .= '<td id="action_' . $bet->id . '">
                	' . $is_delete . '
               	</td>';
            }
            if ($loginUser->agent_level == 'COM') {
                $html_Fancy .= '<td class="text-center">' . $com . '</td>';
            }
            if ($loginUser->agent_level == 'AD' || $loginUser->agent_level == 'COM') {
                $html_Fancy .= '<td class="text-center">' . $ad . '</td>';
            }
            if ($loginUser->agent_level == 'SP' || $loginUser->agent_level == 'COM' || $loginUser->agent_level == 'AD') {
                $html_Fancy .= '<td class="text-center">' . $sp . '</td>';
            }
            if ($loginUser->agent_level == 'SMDL' || $loginUser->agent_level == 'COM' || $loginUser->agent_level == 'AD' || $loginUser->agent_level == 'SP') {
                $html_Fancy .= '<td class="text-center">' . $smdl . '</td>';
            }
            if ($loginUser->agent_level == 'MDL' || $loginUser->agent_level == 'COM' || $loginUser->agent_level == 'AD' || $loginUser->agent_level == 'SP' || $loginUser->agent_level == 'SMDL') {
                $html_Fancy .= '<td class="text-center">' . $mdl . '</td>';
            }
            if ($loginUser->agent_level == 'DL' || $loginUser->agent_level == 'COM' || $loginUser->agent_level == 'AD' || $loginUser->agent_level == 'SP' || $loginUser->agent_level == 'SMDL' || $loginUser->agent_level == 'MDL') {
                $html_Fancy .= '<td class="text-center">' . $dl . '</td>';
            }
            $html_Fancy .= '</tr>';
        }

        // admin book cal start

        $resp = $this->getMatchDetailAdminBkUserHtml($matchList, $loginUser, $website);
        $adminBookUser = $resp['adminBookUser'];
        $adminBookUserBM = $resp['adminBookUserBM'];
        $adminBookUserTeamDrawEnable = $resp['adminBookUserTeamDrawEnable'];
        $adminBookBMUserTeamDrawEnable = $resp['adminBookBMUserTeamDrawEnable'];

        return view('backpanel/risk-management-details', compact('inplay', 'matchList', 'my_placed_bets', 'html', 'my_placed_bets_BM', 'html_BM', 'html_Fancy', 'managetv', 'list', 'my_placed_bets_fancy',  'adminBookUser', 'adminBookUserBM', 'adminBookUserTeamDrawEnable','adminBookBMUserTeamDrawEnable'));
    }

    public function risk_management_book_bm_book(Request $request){
        $loginUser = Auth::user();
        $matchList = Match::where('match_id', $request->matchid)->first();

        $website = UsersAccount::getWebsite();

        $resp = $this->getMatchDetailAdminBkUserHtml($matchList, $loginUser, $website);
        return response()->json($resp);
    }

    public function getMatchDetailAdminBkUserHtml($matchList, $loginUser, $website){

        $ag_id = $loginUser->id;
        $all_child = $this->GetChildofAgent($ag_id);

        $adminBookUser = '';
        // display odds total for admin book
        $team1_bet_total = 0;
        $team2_bet_total = 0;
        $team_draw_bet_total = 0;
        $my_placed_bets = MyBets::where('match_id', $matchList->event_id)->where('bet_type', 'ODDS')->where('result_declare', 0)->where('isDeleted', 0)->whereIn('user_id', $all_child)->orderby('id', 'DESC')->get();
        if (sizeof($my_placed_bets) > 0) {
            foreach ($my_placed_bets as $bet) {
                $abc = json_decode($bet->extra, true);
                if (count($abc) >= 2) {
                    if (array_key_exists("teamname1", $abc) && array_key_exists("teamname2", $abc)) {
                        //bet on draw
                        if ($bet->bet_side == 'back') {
                            $team1_bet_total = $team1_bet_total + $bet->exposureAmt;
                            if (count($abc) >= 2) {
                                $team_draw_bet_total = $team_draw_bet_total - $bet->bet_profit;
                            }
                            $team2_bet_total = $team2_bet_total + $bet->exposureAmt;
                        }
                        if ($bet->bet_side == 'lay') {
                            $team1_bet_total = $team1_bet_total - ($bet->bet_amount);
                            if (count($abc) >= 2) {
                                $team_draw_bet_total = $team_draw_bet_total + ($bet->exposureAmt);
                            }
                            $team2_bet_total = $team2_bet_total - ($bet->bet_amount);
                        }
                    } else if (array_key_exists("teamname3", $abc) && array_key_exists("teamname2", $abc)) {
                        //bet on team1
                        if ($bet->bet_side == 'back') {
                            $team1_bet_total = $team1_bet_total - $bet->bet_profit;
                            if (count($abc) >= 2) {
                                $team_draw_bet_total = $team_draw_bet_total + $bet->exposureAmt;
                            }
                            $team2_bet_total = $team2_bet_total + $bet->exposureAmt;
                        }
                        if ($bet->bet_side == 'lay') {
                            $team1_bet_total = $team1_bet_total + ($bet->exposureAmt);
                            if (count($abc) >= 2) {
                                $team_draw_bet_total = $team_draw_bet_total - ($bet->bet_amount);
                            }
                            $team2_bet_total = $team2_bet_total - ($bet->bet_amount);
                        }
                    } else if (array_key_exists("teamname3", $abc) && array_key_exists("teamname1", $abc)) {
                        //bet on team2
                        if ($bet->bet_side == 'back') {
                            $team2_bet_total = $team2_bet_total - ($bet->bet_profit);
                            if (count($abc) >= 2) {
                                $team_draw_bet_total = $team_draw_bet_total + $bet->exposureAmt;
                            }
                            $team1_bet_total = $team1_bet_total + $bet->exposureAmt;
                        }
                        if ($bet->bet_side == 'lay') {
                            $team2_bet_total = $team2_bet_total + $bet->exposureAmt;
                            if (count($abc) >= 2) {
                                $team_draw_bet_total = $team_draw_bet_total - $bet->bet_amount;
                            }
                            $team1_bet_total = $team1_bet_total - $bet->bet_amount;
                        }
                    }
                }
                else if (count($abc) == 1) {
                    if (array_key_exists("teamname1", $abc)) {
                        //bet on team2
                        if ($bet->bet_side == 'back') {
                            $team2_bet_total = $team2_bet_total - $bet->bet_profit;
                            $team1_bet_total = $team1_bet_total + $bet->exposureAmt;
                        }
                        if ($bet->bet_side == 'lay') {
                            $team2_bet_total = $team2_bet_total + $bet->exposureAmt;
                            $team1_bet_total = $team1_bet_total - $bet->bet_amount;
                        }
                    } else {
                        //bet on team1
                        if ($bet->bet_side == 'back') {
                            $team1_bet_total = $team1_bet_total - $bet->bet_profit;
                            $team2_bet_total = $team2_bet_total + $bet->exposureAmt;
                        }
                        if ($bet->bet_side == 'lay') {
                            $team1_bet_total = $team1_bet_total + $bet->exposureAmt;
                            $team2_bet_total = $team2_bet_total - $bet->bet_amount;
                        }
                    }
                }

            }
        }

        if ($team1_bet_total >= 0) {
            $clsa1 = 'text-color-green';
        } else {
            $clsa1 = 'text-color-red';
        }
        if ($team2_bet_total >= 0) {
            $clsa2 = 'text-color-green';
        } else {
            $clsa2 = 'text-color-red';
        }

        if ($team_draw_bet_total >= 0) {
            $clsa3 = 'text-color-green';
        } else {
            $clsa3 = 'text-color-red';
        }

        $adminBookUserTeamDrawEnable = false;
        $adminBookUser .= '
                <tr class="trMDl">
                  <td style="width: 215px;"><b class="ng-binding">Admin P&amp;L</b></td>
                  <td class="text-center ng-binding ' . $clsa1 . '" >' . round(abs($team1_bet_total),2) . '</td>
                  <td class="text-center ng-binding ' . $clsa2 . '" >' . round(abs($team2_bet_total),2) . '</td>';
        if ($team_draw_bet_total != '') {
            $adminBookUserTeamDrawEnable = true;
            $adminBookUser .= '<td class="text-center ng-binding ' . $clsa3 . '">' . round(abs($team_draw_bet_total),2) . '</td>';
        }
        $adminBookUser .= '</tr>';

        $adminBookUserBM = '';
        // display bookmaker total for admin book
        // display odds total for admin book
        $team1_bet_totalB = 0;
        $team2_bet_totalB = 0;
        $team_draw_bet_totalB = 0;
        $my_placed_betsB = MyBets::where('match_id', $matchList->event_id)->where('bet_type', 'BOOKMAKER')->where('result_declare', 0)->where('isDeleted', 0)->whereIn('user_id', $all_child)->orderby('id', 'DESC')->get();
        if (sizeof($my_placed_betsB) > 0) {
            foreach ($my_placed_betsB as $bet) {
                $abc = json_decode($bet->extra, true);
                if (count($abc) >= 2) {
                    if (array_key_exists("teamname1", $abc) && array_key_exists("teamname2", $abc)) {
                        //bet on draw
                        if ($bet->bet_side == 'back') {
                            $team1_bet_totalB = $team1_bet_totalB + $bet->exposureAmt;
                            if (count($abc) >= 2) {
                                $team_draw_bet_totalB = $team_draw_bet_totalB - $bet->bet_profit;
                            }
                            $team2_bet_totalB = $team2_bet_totalB + $bet->exposureAmt;
                        }
                        if ($bet->bet_side == 'lay') {
                            $team1_bet_totalB = $team1_bet_totalB - ($bet->bet_amount);
                            if (count($abc) >= 2) {
                                $team_draw_bet_totalB = $team_draw_bet_totalB + ($bet->exposureAmt);
                            }
                            $team2_bet_totalB = $team2_bet_totalB - ($bet->bet_amount);
                        }
                    } else if (array_key_exists("teamname3", $abc) && array_key_exists("teamname2", $abc)) {
                        //bet on team1
                        if ($bet->bet_side == 'back') {
                            $team1_bet_totalB = $team1_bet_totalB - $bet->bet_profit;
                            if (count($abc) >= 2) {
                                $team_draw_bet_totalB = $team_draw_bet_totalB + $bet->exposureAmt;
                            }
                            $team2_bet_totalB = $team2_bet_totalB + $bet->exposureAmt;
                        }
                        if ($bet->bet_side == 'lay') {
                            $team1_bet_totalB = $team1_bet_totalB + ($bet->exposureAmt);
                            if (count($abc) >= 2) {
                                $team_draw_bet_totalB = $team_draw_bet_totalB - ($bet->bet_amount);
                            }
                            $team2_bet_totalB = $team2_bet_totalB - ($bet->bet_amount);
                        }
                    } else if (array_key_exists("teamname3", $abc) && array_key_exists("teamname1", $abc)) {
                        //bet on team2
                        if ($bet->bet_side == 'back') {
                            $team2_bet_totalB = $team2_bet_totalB - ($bet->bet_profit);
                            if (count($abc) >= 2) {
                                $team_draw_bet_totalB = $team_draw_bet_totalB + $bet->exposureAmt;
                            }
                            $team1_bet_totalB = $team1_bet_totalB + $bet->exposureAmt;
                        }
                        if ($bet->bet_side == 'lay') {
                            $team2_bet_totalB = $team2_bet_totalB + $bet->exposureAmt;
                            if (count($abc) >= 2) {
                                $team_draw_bet_totalB = $team_draw_bet_totalB - $bet->bet_amount;
                            }
                            $team1_bet_totalB = $team1_bet_totalB - $bet->bet_amount;
                        }
                    }
                }
                else if (count($abc) == 1) {
                    if (array_key_exists("teamname1", $abc)) {
                        //bet on team2
                        if ($bet->bet_side == 'back') {
                            $team2_bet_totalB = $team2_bet_totalB - $bet->bet_profit;
                            $team1_bet_totalB = $team1_bet_totalB + $bet->exposureAmt;
                        }
                        if ($bet->bet_side == 'lay') {
                            $team2_bet_totalB = $team2_bet_totalB + $bet->exposureAmt;
                            $team1_bet_totalB = $team1_bet_totalB - $bet->bet_amount;
                        }
                    } else {
                        //bet on team1
                        if ($bet->bet_side == 'back') {
                            $team1_bet_totalB = $team1_bet_totalB - $bet->bet_profit;
                            $team2_bet_totalB = $team2_bet_totalB + $bet->exposureAmt;
                        }
                        if ($bet->bet_side == 'lay') {
                            $team1_bet_totalB = $team1_bet_totalB + $bet->exposureAmt;
                            $team2_bet_totalB = $team2_bet_totalB - $bet->bet_amount;
                        }
                    }
                }
            }
        }

        if ($team1_bet_totalB >= 0) {
            $cls1 = 'text-color-green';
        } else {
            $cls1 = 'text-color-red';
        }
        if ($team2_bet_totalB >= 0) {
            $cls2 = 'text-color-green';
        } else {
            $cls2 = 'text-color-red';
        }
        if ($team_draw_bet_totalB >= 0) {
            $cls3 = 'text-color-green';
        } else {
            $cls3 = 'text-color-red';
        }

        $adminBookBMUserTeamDrawEnable = false;
        $adminBookUserBM .= '
                <tr class="trMDl">
                  <td style="width: 215px;" class="text-left cursor-pointer"><b class="ng-binding">Admin P&amp;L</b>
                  </td>
                  <td class="text-center ng-binding ' . $cls1 . '">' . round(abs($team1_bet_totalB),2) . '</td>
                  <td class="text-center ng-binding ' . $cls2 . '">' . round(abs($team2_bet_totalB),2) . '</td>';
                if ($team_draw_bet_totalB != '') {
                    $adminBookBMUserTeamDrawEnable = true;
                    $adminBookUserBM .= '<td class="text-center ng-binding ' . $cls3 . '">' . round(abs($team_draw_bet_totalB),2) . '</td>';
                }
        $adminBookUserBM .= '</tr>';


        $users_all = User::where('parentid', $loginUser->id)->latest()->get();

        $i = 0;

        foreach ($users_all as $value) {
            $team2_bet_total_user = 0;
            $team1_bet_total_user = 0;
            $team_draw_bet_total_user = 0;

            $recordFound = false;
            if ($value->agent_level == 'PL') {
                $my_placed_bets_ods = MyBets::where('match_id', $matchList->event_id)->where('bet_type', 'ODDS')->where('result_declare', 0)->where('isDeleted', 0)->where('user_id', $value->id)->orderby('id', 'DESC')->get();
                $recordFound = true;
            } else {
                $all_childs = UserHirarchy::where('agent_user', $value->id)->first();
                $all_childsCount = UserHirarchy::where('agent_user', $value->id)->count();
                if ($all_childsCount != 0) {
                    $all_child = $this->GetChildofAgent($value->id);
                    $my_placed_bets_ods = MyBets::where('match_id', $matchList->event_id)->where('bet_type', 'ODDS')->where('result_declare', 0)->where('isDeleted', 0)->whereIn('user_id', $all_child)->orderby('id', 'DESC')->get();
                    $recordFound = true;
                }else{
                    $recordFound = false;
                }
            }
//            if($value->id == 5478) {
//                dd($value->id, $all_child, $my_placed_bets_ods);
//            }

            if (isset($my_placed_bets_ods) && $recordFound==true && $my_placed_bets_ods->count() > 0) {

                foreach ($my_placed_bets_ods as $bet) {

                    $abc = json_decode($bet->extra, true);
                    if (count($abc) >= 2) {
                        if (array_key_exists("teamname1", $abc) && array_key_exists("teamname2", $abc)) {

                            //bet on draw
                            if ($bet->bet_side == 'back') {
                                $team1_bet_total_user = $team1_bet_total_user + $bet->exposureAmt;
                                if (count($abc) >= 2) {
                                    $team_draw_bet_total_user = $team_draw_bet_total_user - $bet->bet_profit;
                                }
                                $team2_bet_total_user = $team2_bet_total_user + $bet->exposureAmt;
                            }
                            if ($bet->bet_side == 'lay') {
                                $team1_bet_total_user = $team1_bet_total_user - ($bet->bet_amount);
                                if (count($abc) >= 2) {
                                    $team_draw_bet_total_user = $team_draw_bet_total_user + ($bet->exposureAmt);
                                }
                                $team2_bet_total_user = $team2_bet_total_user - ($bet->bet_amount);
                            }
                        } else if (array_key_exists("teamname3", $abc) && array_key_exists("teamname2", $abc)) {
                            //bet on team1
                            if ($bet->bet_side == 'back') {
                                $team1_bet_total_user = $team1_bet_total_user - $bet->bet_profit;
                                if (count($abc) >= 2) {
                                    $team_draw_bet_total_user = $team_draw_bet_total_user + $bet->exposureAmt;
                                }
                                $team2_bet_total_user = $team2_bet_total_user + $bet->exposureAmt;
                            }
                            if ($bet->bet_side == 'lay') {
                                $team1_bet_total_user = $team1_bet_total_user + ($bet->exposureAmt);
                                if (count($abc) >= 2) {
                                    $team_draw_bet_total_user = $team_draw_bet_total_user - ($bet->bet_amount);
                                }
                                $team2_bet_total_user = $team2_bet_total_user - ($bet->bet_amount);
                            }
                        } else if (array_key_exists("teamname3", $abc) && array_key_exists("teamname1", $abc)) {
                            //bet on team2
                            if ($bet->bet_side == 'back') {
                                $team2_bet_total_user = $team2_bet_total_user - ($bet->bet_profit);
                                if (count($abc) >= 2) {
                                    $team_draw_bet_total_user = $team_draw_bet_total_user + $bet->exposureAmt;
                                }
                                $team1_bet_total_user = $team1_bet_total_user + $bet->exposureAmt;
                            }
                            if ($bet->bet_side == 'lay') {
                                $team2_bet_total_user = $team2_bet_total_user + $bet->exposureAmt;
                                if (count($abc) >= 2) {
                                    $team_draw_bet_total_user = $team_draw_bet_total_user - $bet->bet_amount;
                                }
                                $team1_bet_total_user = $team1_bet_total_user - $bet->bet_amount;
                            }
                        }
                    }
                    else if (count($abc) == 1) {
                        if (array_key_exists("teamname1", $abc)) {
                            //bet on team2
                            if ($bet->bet_side == 'back') {
                                $team2_bet_total_user = $team2_bet_total_user - $bet->bet_profit;
                                $team1_bet_total_user = $team1_bet_total_user + $bet->exposureAmt;
                            }
                            if ($bet->bet_side == 'lay') {
                                $team2_bet_total_user = $team2_bet_total_user + $bet->exposureAmt;
                                $team1_bet_total_user = $team1_bet_total_user - $bet->bet_amount;
                            }
                        } else {
                            //bet on team1
                            if ($bet->bet_side == 'back') {
                                $team1_bet_total_user = $team1_bet_total_user - $bet->bet_profit;
                                $team2_bet_total_user = $team2_bet_total_user + $bet->exposureAmt;
                            }
                            if ($bet->bet_side == 'lay') {
                                $team1_bet_total_user = $team1_bet_total_user + $bet->exposureAmt;
                                $team2_bet_total_user = $team2_bet_total_user - $bet->bet_amount;
                            }
                        }
                    }

                }

                if ($team1_bet_total_user >= 0) {
                    $clsa1 = 'text-color-green';
                } else {
                    $clsa1 = 'text-color-red';
                }
                if ($team2_bet_total_user >= 0) {
                    $clsa2 = 'text-color-green';
                } else {
                    $clsa2 = 'text-color-red';
                }
                if ($team_draw_bet_total_user >= 0) {
                    $clsa3 = 'text-color-green';
                } else {
                    $clsa3 = 'text-color-red';
                }

                $name = $value->user_name . '[' . $value->first_name . ']';
                if($value->id == $loginUser->id){
                    $name = 'Admin P&L';
                }

                $adminBookUser .= '
                <tr class="trMDl">
                  <td style="width: 215px;" class="text-left cursor-pointer" data-toggle="collapse" data-target="#SPname-' . $i . '"><b class="ng-binding">'.$name.'</b>
                  </td>
                  <td class="text-center ng-binding ' . $clsa1 . '" >' . round(abs($team1_bet_total_user),2) . '</td>
                  <td class="text-center ng-binding ' . $clsa2 . '" >' . round(abs($team2_bet_total_user),2) . '</td>';
                if ($team_draw_bet_total_user != '') {
                    $adminBookUserTeamDrawEnable = true;
                    $adminBookUser .= '<td class="text-center ng-binding ' . $clsa3 . '" >' . round(abs($team_draw_bet_total_user),2) . '</td>';
                }
                $adminBookUser .= '</tr>';
                $i++;
            }
        }

        // admin book bookmaker

        $i = 0;

        foreach ($users_all as $value) {
            /*echo "yess";
			exit;*/
            $team2_bet_total_user = 0;
            $team1_bet_total_user = 0;
            $team_draw_bet_total_user = 0;
            $recordFound = false;
            if ($value->agent_level == 'PL') {
                $my_placed_bets_ods = MyBets::where('match_id', $matchList->event_id)->where('bet_type', 'BOOKMAKER')->where('result_declare', 0)->where('isDeleted', 0)->where('user_id', $value->id)->orderby('id', 'DESC')->get();
                $recordFound = true;
            } else {
                $all_childs = UserHirarchy::where('agent_user', $value->id)->first();
                $all_childsCount = UserHirarchy::where('agent_user', $value->id)->count();
                if ($all_childsCount != 0) {

                    $all_child = $this->GetChildofAgent($value->id);
                    $my_placed_bets_ods = MyBets::where('match_id', $matchList->event_id)->where('bet_type', 'BOOKMAKER')->where('result_declare', 0)->where('isDeleted', 0)->whereIn('user_id', $all_child)->orderby('id', 'DESC')->get();
                    $recordFound = true;
                }else{
                    $recordFound = false;
                }
            }

            if (isset($my_placed_bets_ods) && $recordFound==true && $my_placed_bets_ods->count() > 0) {

                foreach ($my_placed_bets_ods as $bet) {

                    $abc = json_decode($bet->extra, true);
                    if (count($abc) >= 2) {
                        if (array_key_exists("teamname1", $abc) && array_key_exists("teamname2", $abc)) {

                            //bet on draw
                            if ($bet->bet_side == 'back') {
                                $team1_bet_total_user = $team1_bet_total_user + $bet->exposureAmt;
                                if (count($abc) >= 2) {
                                    $team_draw_bet_total_user = $team_draw_bet_total_user - $bet->bet_profit;
                                }
                                $team2_bet_total_user = $team2_bet_total_user + $bet->exposureAmt;
                            }
                            if ($bet->bet_side == 'lay') {
                                $team1_bet_total_user = $team1_bet_total_user - ($bet->bet_amount);
                                if (count($abc) >= 2) {
                                    $team_draw_bet_total_user = $team_draw_bet_total_user + ($bet->exposureAmt);
                                }
                                $team2_bet_total_user = $team2_bet_total_user - ($bet->bet_amount);
                            }
                        } else if (array_key_exists("teamname3", $abc) && array_key_exists("teamname2", $abc)) {
                            //bet on team1
                            if ($bet->bet_side == 'back') {
                                $team1_bet_total_user = $team1_bet_total_user - $bet->bet_profit;
                                if (count($abc) >= 2) {
                                    $team_draw_bet_total_user = $team_draw_bet_total_user + $bet->exposureAmt;
                                }
                                $team2_bet_total_user = $team2_bet_total_user + $bet->exposureAmt;
                            }
                            if ($bet->bet_side == 'lay') {
                                $team1_bet_total_user = $team1_bet_total_user + ($bet->exposureAmt);
                                if (count($abc) >= 2) {
                                    $team_draw_bet_total_user = $team_draw_bet_total_user - ($bet->bet_amount);
                                }
                                $team2_bet_total_user = $team2_bet_total_user - ($bet->bet_amount);
                            }
                        } else if (array_key_exists("teamname3", $abc) && array_key_exists("teamname1", $abc)) {
                            //bet on team2
                            if ($bet->bet_side == 'back') {
                                $team2_bet_total_user = $team2_bet_total_user - ($bet->bet_profit);
                                if (count($abc) >= 2) {
                                    $team_draw_bet_total_user = $team_draw_bet_total_user + $bet->exposureAmt;
                                }
                                $team1_bet_total_user = $team1_bet_total_user + $bet->exposureAmt;
                            }
                            if ($bet->bet_side == 'lay') {
                                $team2_bet_total_user = $team2_bet_total_user + $bet->exposureAmt;
                                if (count($abc) >= 2) {
                                    $team_draw_bet_total_user = $team_draw_bet_total_user - $bet->bet_amount;
                                }
                                $team1_bet_total_user = $team1_bet_total_user - $bet->bet_amount;
                            }
                        }
                    }
                    else if (count($abc) == 1) {
                        if (array_key_exists("teamname1", $abc)) {
                            //bet on team2
                            if ($bet->bet_side == 'back') {
                                $team2_bet_total_user = $team2_bet_total_user - $bet->bet_profit;
                                $team1_bet_total_user = $team1_bet_total_user + $bet->exposureAmt;
                            }
                            if ($bet->bet_side == 'lay') {
                                $team2_bet_total_user = $team2_bet_total_user + $bet->exposureAmt;
                                $team1_bet_total_user = $team1_bet_total_user - $bet->bet_amount;
                            }
                        } else {
                            //bet on team1
                            if ($bet->bet_side == 'back') {
                                $team1_bet_total_user = $team1_bet_total_user - $bet->bet_profit;
                                $team2_bet_total_user = $team2_bet_total_user + $bet->exposureAmt;
                            }
                            if ($bet->bet_side == 'lay') {
                                $team1_bet_total_user = $team1_bet_total_user + $bet->exposureAmt;
                                $team2_bet_total_user = $team2_bet_total_user - $bet->bet_amount;
                            }
                        }
                    }

                }
                $cls1 = 'text-color-red';
                $cls2 = 'text-color-red';
                $cls3 = 'text-color-red';
                if ($team1_bet_total_user >= 0) {
                    $cls1 = 'text-color-green';
                }
                if ($team2_bet_total_user >= 0) {
                    $cls2 = 'text-color-green';
                }
                if ($team_draw_bet_total_user >= 0) {
                    $cls3 = 'text-color-green';
                }

                $name = $value->user_name . '[' . $value->first_name . ']';
                if($value->id == $loginUser->id){
                    $name = 'Admin P&L';
                }

                $adminBookUserBM .= '
                <tr class="trMDl">
                  <td style="width: 215px;" class="text-left cursor-pointer" data-toggle="collapse" data-target="#SPnameB-' . $i . '"><b class="ng-binding">' . $name . '</b>
                  </td>
                  <td class="text-center ng-binding ' . $cls1 . '">' . round(abs($team1_bet_total_user),2) . '</td>
                  <td class="text-center ng-binding ' . $cls2 . '">' . round(abs($team2_bet_total_user),2) . '</td>';
                if ($team_draw_bet_total_user != '') {
                    $adminBookBMUserTeamDrawEnable = true;
                    $adminBookUserBM .= '<td class="text-center ng-binding ' . $cls3 . '" >' . round(abs($team_draw_bet_total_user),2) . '</td>';
                }
                $adminBookUserBM .= '</tr>';
                $i++;
            }
        }

        return array(
            'adminBookUserTeamDrawEnable' => $adminBookUserTeamDrawEnable,
            'adminBookBMUserTeamDrawEnable' => $adminBookBMUserTeamDrawEnable,
            'adminBookUser' => $adminBookUser,
            'adminBookUserBM' => $adminBookUserBM,
        );
    }


    public function blockMatch($id)
    {
        $status = 0;
        Match::where('id', $id)->update(['status_m' => $status]);
        return \Redirect::route('backpanel/risk-management-details', $id)->with('message', 'Status saved correctly!!!');
    }

    public function unblockMatch($id)
    {
        $status = 1;
        Match::where('id', $id)->update(['status_m' => $status]);
        return \Redirect::route('backpanel/risk-management-details', $id)->with('message', 'Status saved correctly!!!');
    }

    public function blockBook($id)
    {
        $status = 0;
        Match::where('id', $id)->update(['status_b' => $status]);
        return \Redirect::route('backpanel/risk-management-details', $id)->with('message', 'Status saved correctly!!!');
    }

    public function unblockBook($id)
    {
        $status = 1;
        Match::where('id', $id)->update(['status_b' => $status]);
        return \Redirect::route('backpanel/risk-management-details', $id)->with('message', 'Status saved correctly!!!');
    }

    public function blockFancy($id)
    {

        $status = 0;
        Match::where('id', $id)->update(['status_f' => $status]);
        return \Redirect::route('backpanel/risk-management-details', $id)->with('message', 'Status saved correctly!!!');
    }

    public function unblockFancy($id)
    {
        $status = 1;
        Match::where('id', $id)->update(['status_f' => $status]);
        return \Redirect::route('backpanel/risk-management-details', $id)->with('message', 'Status saved correctly!!!');
    }

    public function allBlock($id)
    {
        $status = 0;
        Match::where('id', $id)->update(['status_m' => $status, 'status_b' => $status, 'status_f' => $status]);
        return \Redirect::route('backpanel/risk-management-details', $id)->with('message', 'Status saved correctly!!!');
    }

    public function allunBlock($id)
    {
        $status = 1;
        Match::where('id', $id)->update(['status_m' => $status, 'status_b' => $status, 'status_f' => $status]);
        return \Redirect::route('backpanel/risk-management-details', $id)->with('message', 'Status saved correctly!!!');
    }

    public function risk_management_odds_bet(Request $request)
    {
        $loginUser = Auth::user();
        $matchList = Match::where('match_id', $request->matchid)->first();
        $valodd = $request->valodd;
        $valbm = $request->valbm;
        $valfnc = $request->valfnc;
        //get all child of agent
        $loginUser = Auth::user();
        $ag_id = $loginUser->id;
        $all_child = $this->GetChildofAgent($ag_id);
        if (!empty($valodd)) {
            $srhdata = User::where('user_name', 'LIKE', '%' . $valodd . '%')
                ->orWhere('first_name', 'LIKE', '%' . $valodd . '%')
                ->get();

            foreach ($srhdata as $value) {
                $child_array[] = $value->id;
            }

            $my_placed_bets = MyBets::where('match_id', $matchList['event_id'])->where('bet_type', 'ODDS')->where('result_declare', 0)->whereIn('user_id', $child_array)->orderby('id', 'DESC')->get();

        } else {
            $my_placed_bets = MyBets::where('match_id', $matchList['event_id'])->where('bet_type', 'ODDS')->where('result_declare', 0)->whereIn('user_id', $all_child)->orderby('id', 'DESC')->get();
        }

        //$my_placed_bets = MyBets::where('match_id',$matchList['event_id'])->where('bet_type','ODDS')->where('result_declare',0)->orderby('id','DESC')->get();
        $html = '';
        foreach ($my_placed_bets as $bet) {
            $player = User::where('id', $bet->user_id)->where('agent_level', 'PL')->first();
            $bet_type_cls = '';
            $bet_type = '';
            if ($bet->bet_side == 'lay') {
                $bet_type_cls = 'pink-bg';
                $bet_type = 'Lay';
            } else {
                $bet_type_cls = 'cyan-bg';
                $bet_type = 'Back';
            }
            $getUserparent = User::where('id', $player->parentid)->first();
            /*echo $getUserparent;
			exit;*/
            $ad = '-';
            $sp = '-';
            $smdl = '-';
            $mdl = '-';
            $dl = '-';
            $com = '-';
            if ($getUserparent->agent_level == 'AD') {
                $ad = $getUserparent->user_name;
            } elseif ($getUserparent->agent_level == 'SP') {
                $sp = $getUserparent->user_name;
            } elseif ($getUserparent->agent_level == 'SMDL') {
                $smdl = $getUserparent->user_name;
            } elseif ($getUserparent->agent_level == 'MDL') {
                $mdl = $getUserparent->user_name;
            } elseif ($getUserparent->agent_level == 'DL') {
                $dl = $getUserparent->user_name;
            } elseif ($getUserparent->agent_level == 'COM') {
                $com = $getUserparent->user_name;
            }
            if (!empty($getUserparent->parentid)) {
                /*echo "aab->";
        	echo $getUserparent->parentid;
        	exit;*/
                $getUserparent2 = User::where('id', $getUserparent->parentid)->first();
                if ($getUserparent2->agent_level == 'AD') {
                    $ad = $getUserparent2->user_name;
                } elseif ($getUserparent2->agent_level == 'SP') {
                    $sp = $getUserparent2->user_name;
                } elseif ($getUserparent2->agent_level == 'SMDL') {
                    $smdl = $getUserparent2->user_name;
                } elseif ($getUserparent2->agent_level == 'MDL') {
                    $mdl = $getUserparent2->user_name;
                } elseif ($getUserparent2->agent_level == 'DL') {
                    $dl = $getUserparent2->user_name;
                } elseif ($getUserparent2->agent_level == 'COM') {
                    $com = $getUserparent2->user_name;
                }
            }

            if (!empty($getUserparent2->parentid)) {
                /*	echo "aam->";
      	echo $getUserparent2->parentid;
      	exit;*/
                $getUserparent3 = User::where('id', $getUserparent2->parentid)->first();
                if ($getUserparent3->agent_level == 'AD') {
                    $ad = $getUserparent3->user_name;
                } elseif ($getUserparent3->agent_level == 'SP') {
                    $sp = $getUserparent3->user_name;
                } elseif ($getUserparent3->agent_level == 'SMDL') {
                    $smdl = $getUserparent3->user_name;
                } elseif ($getUserparent3->agent_level == 'MDL') {
                    $mdl = $getUserparent3->user_name;
                } elseif ($getUserparent3->agent_level == 'DL') {
                    $dl = $getUserparent3->user_name;
                } elseif ($getUserparent3->agent_level == 'COM') {
                    $com = $getUserparent3->user_name;
                }
            }

            if (!empty($getUserparent3->parentid)) {
                /*echo "aam->";
      	echo $getUserparent3->parentid;
      	exit;*/
                $getUserparent4 = User::where('id', $getUserparent3->parentid)->first();

                if ($getUserparent4->agent_level == 'AD') {
                    $ad = $getUserparent4->user_name;
                } elseif ($getUserparent4->agent_level == 'SP') {
                    $sp = $getUserparent4->user_name;
                } elseif ($getUserparent4->agent_level == 'SMDL') {
                    $smdl = $getUserparent4->user_name;
                } elseif ($getUserparent4->agent_level == 'MDL') {
                    $mdl = $getUserparent4->user_name;
                } elseif ($getUserparent4->agent_level == 'DL') {
                    $dl = $getUserparent4->user_name;
                } elseif ($getUserparent4->agent_level == 'COM') {
                    $com = $getUserparent4->user_name;
                }
            }

            if (!empty($getUserparent4->parentid)) {
                /*echo "aam->";
      	echo $getUserparent4->parentid;
      	exit;*/
                $getUserparent5 = User::where('id', $getUserparent4->parentid)->first();

                if ($getUserparent5->agent_level == 'AD') {
                    $ad = $getUserparent5->user_name;
                } elseif ($getUserparent5->agent_level == 'SP') {
                    $sp = $getUserparent5->user_name;
                } elseif ($getUserparent5->agent_level == 'SMDL') {
                    $smdl = $getUserparent5->user_name;
                } elseif ($getUserparent5->agent_level == 'MDL') {
                    $mdl = $getUserparent5->user_name;
                } elseif ($getUserparent5->agent_level == 'DL') {
                    $dl = $getUserparent5->user_name;
                } elseif ($getUserparent5->agent_level == 'COM') {
                    $com = $getUserparent5->user_name;
                }
            }

            /*echo "-->".$ad;
      exit;*/

            if (!empty($getUserparent5->parentid)) {
                $getUserparent6 = User::where('id', $getUserparent5->parentid)->first();

                if ($getUserparent6->agent_level == 'AD') {
                    $ad = $getUserparent6->user_name;
                } elseif ($getUserparent6->agent_level == 'SP') {
                    $sp = $getUserparent6->user_name;
                } elseif ($getUserparent6->agent_level == 'SMDL') {
                    $smdl = $getUserparent6->user_name;
                } elseif ($getUserparent6->agent_level == 'MDL') {
                    $mdl = $getUserparent6->user_name;
                } elseif ($getUserparent6->agent_level == 'DL') {
                    $dl = $getUserparent6->user_name;
                } elseif ($getUserparent6->agent_level == 'COM') {
                    $com = $getUserparent6->user_name;
                }
            }
            $is_delete = '';
            if ($bet->isDeleted == 0) {
                $is_delete .= '<a id="delete_row_' . $bet->id . '" onclick="delete_bet(' . $bet->id . ')" class="btn times_btn red-bg text-color-white"><i class="fas fa-times"></i></a>';
                $is_delete .= '<a style="display:none" id="rollback_row_' . $bet->id . '" onclick="rollback_bet(' . $bet->id . ')" class="btn times_btn green-bg text-color-white"><i class="fas fa-check"></i></a>';
            } else {
                $is_delete .= '<a id="rollback_row_' . $bet->id . '" onclick="rollback_bet(' . $bet->id . ')" class="btn times_btn green-bg text-color-white"><i class="fas fa-check"></i></a>';
                $is_delete .= '<a style="display:none" id="delete_row_' . $bet->id . '" onclick="delete_bet(' . $bet->id . ')" class="btn times_btn red-bg text-color-white"><i class="fas fa-times"></i></a>';
            }
            $binfo = $bet->browser_details . ' &#013; ' . $bet->ip_address;
            $html .= '<tr class="' . $bet_type_cls . '">
				<td><input type="checkbox" id="select_all" name="filter-checkbox" value=""></td>
                <td class="text-center"><b>' . ucfirst($player['user_name']) . '[' . $player['first_name'] . ']</b></td>
                <td class="text-center"><b>' . $bet->team_name . '</b></td>
				<td class="text-center">' . $bet_type . '</td>
                <td class="text-center"><b>' . $bet->bet_odds . '</b></td>
                <td class="text-center"><b>' . $bet->bet_amount . '</b></td>
                <td class="text-center"><b>' . $bet->bet_profit . '</b></td>
                <td>' . date('d-m-Y H:i:s A', strtotime($bet->created_at)) . '</td>
                <td class="text-center"><i class="fas fa-mobile text-color-red" data-toggle="tooltip" data-placement="top" title="' . $binfo . '"></i></td>';
            if ($loginUser->agent_level == 'COM') {
                $html .= '<td id="action_' . $bet->id . '">
	                	' . $is_delete . '
	               	</td>';
            }
            if ($loginUser->agent_level == 'COM') {
                $html .= '<td class="text-center">' . $com . '</td>';
            }
            if ($loginUser->agent_level == 'AD' || $loginUser->agent_level == 'COM') {
                $html .= '<td class="text-center">' . $ad . '</td>';
            }
            if ($loginUser->agent_level == 'SP' || $loginUser->agent_level == 'COM' || $loginUser->agent_level == 'AD') {
                $html .= '<td class="text-center">' . $sp . '</td>';
            }
            if ($loginUser->agent_level == 'SMDL' || $loginUser->agent_level == 'COM' || $loginUser->agent_level == 'AD' || $loginUser->agent_level == 'SP') {
                $html .= '<td class="text-center">' . $smdl . '</td>';
            }
            if ($loginUser->agent_level == 'MDL' || $loginUser->agent_level == 'COM' || $loginUser->agent_level == 'AD' || $loginUser->agent_level == 'SP' || $loginUser->agent_level == 'SMDL') {
                $html .= '<td class="text-center">' . $mdl . '</td>';
            }
            if ($loginUser->agent_level == 'DL' || $loginUser->agent_level == 'COM' || $loginUser->agent_level == 'AD' || $loginUser->agent_level == 'SP' || $loginUser->agent_level == 'SMDL' || $loginUser->agent_level == 'MDL') {
                $html .= '<td class="text-center">' . $dl . '</td>';
            }
            $html .= '</tr>';
        }
        //BM
        $html_two = '';
        if (!empty($valbm)) {
            $srhdata = User::where('user_name', 'LIKE', '%' . $valbm . '%')
                ->orWhere('first_name', 'LIKE', '%' . $valbm . '%')
                ->get();

            foreach ($srhdata as $value) {
                $child_array[] = $value->id;
            }

            $my_placed_bets = MyBets::where('match_id', $matchList['event_id'])->where('bet_type', 'BOOKMAKER')->where('result_declare', 0)->whereIn('user_id', $child_array)->orderby('id', 'DESC')->get();

        } else {
            $my_placed_bets = MyBets::where('match_id', $matchList['event_id'])->where('bet_type', 'BOOKMAKER')->where('result_declare', 0)->whereIn('user_id', $all_child)->orderby('id', 'DESC')->get();
        }
        foreach ($my_placed_bets as $bet) {
            $player = User::where('id', $bet->user_id)->where('agent_level', 'PL')->first();
            $bet_type_cls = '';
            $bet_type = '';
            if ($bet->bet_side == 'lay') {
                $bet_type_cls = 'pink-bg';
                $bet_type = 'Lay';
            } else {
                $bet_type_cls = 'cyan-bg';
                $bet_type = 'Back';
            }
            $getUserparent = User::where('id', $player->parentid)->first();
            /*echo $getUserparent;
			exit;*/
            $ad = '-';
            $sp = '-';
            $smdl = '-';
            $mdl = '-';
            $dl = '-';
            $com = '-';
            if ($getUserparent->agent_level == 'AD') {
                $ad = $getUserparent->user_name;
            } elseif ($getUserparent->agent_level == 'SP') {
                $sp = $getUserparent->user_name;
            } elseif ($getUserparent->agent_level == 'SMDL') {
                $smdl = $getUserparent->user_name;
            } elseif ($getUserparent->agent_level == 'MDL') {
                $mdl = $getUserparent->user_name;
            } elseif ($getUserparent->agent_level == 'DL') {
                $dl = $getUserparent->user_name;
            } elseif ($getUserparent->agent_level == 'COM') {
                $com = $getUserparent->user_name;
            }
            if (!empty($getUserparent->parentid)) {
                /*echo "aab->";
        	echo $getUserparent->parentid;
        	exit;*/
                $getUserparent2 = User::where('id', $getUserparent->parentid)->first();
                if ($getUserparent2->agent_level == 'AD') {
                    $ad = $getUserparent2->user_name;
                } elseif ($getUserparent2->agent_level == 'SP') {
                    $sp = $getUserparent2->user_name;
                } elseif ($getUserparent2->agent_level == 'SMDL') {
                    $smdl = $getUserparent2->user_name;
                } elseif ($getUserparent2->agent_level == 'MDL') {
                    $mdl = $getUserparent2->user_name;
                } elseif ($getUserparent2->agent_level == 'DL') {
                    $dl = $getUserparent2->user_name;
                } elseif ($getUserparent2->agent_level == 'COM') {
                    $com = $getUserparent2->user_name;
                }
            }

            if (!empty($getUserparent2->parentid)) {
                /*	echo "aam->";
      	echo $getUserparent2->parentid;
      	exit;*/
                $getUserparent3 = User::where('id', $getUserparent2->parentid)->first();
                if ($getUserparent3->agent_level == 'AD') {
                    $ad = $getUserparent3->user_name;
                } elseif ($getUserparent3->agent_level == 'SP') {
                    $sp = $getUserparent3->user_name;
                } elseif ($getUserparent3->agent_level == 'SMDL') {
                    $smdl = $getUserparent3->user_name;
                } elseif ($getUserparent3->agent_level == 'MDL') {
                    $mdl = $getUserparent3->user_name;
                } elseif ($getUserparent3->agent_level == 'DL') {
                    $dl = $getUserparent3->user_name;
                } elseif ($getUserparent3->agent_level == 'COM') {
                    $com = $getUserparent3->user_name;
                }
            }

            if (!empty($getUserparent3->parentid)) {
                /*echo "aam->";
      	echo $getUserparent3->parentid;
      	exit;*/
                $getUserparent4 = User::where('id', $getUserparent3->parentid)->first();

                if ($getUserparent4->agent_level == 'AD') {
                    $ad = $getUserparent4->user_name;
                } elseif ($getUserparent4->agent_level == 'SP') {
                    $sp = $getUserparent4->user_name;
                } elseif ($getUserparent4->agent_level == 'SMDL') {
                    $smdl = $getUserparent4->user_name;
                } elseif ($getUserparent4->agent_level == 'MDL') {
                    $mdl = $getUserparent4->user_name;
                } elseif ($getUserparent4->agent_level == 'DL') {
                    $dl = $getUserparent4->user_name;
                } elseif ($getUserparent4->agent_level == 'COM') {
                    $com = $getUserparent4->user_name;
                }
            }

            if (!empty($getUserparent4->parentid)) {
                /*echo "aam->";
      	echo $getUserparent4->parentid;
      	exit;*/
                $getUserparent5 = User::where('id', $getUserparent4->parentid)->first();

                if ($getUserparent5->agent_level == 'AD') {
                    $ad = $getUserparent5->user_name;
                } elseif ($getUserparent5->agent_level == 'SP') {
                    $sp = $getUserparent5->user_name;
                } elseif ($getUserparent5->agent_level == 'SMDL') {
                    $smdl = $getUserparent5->user_name;
                } elseif ($getUserparent5->agent_level == 'MDL') {
                    $mdl = $getUserparent5->user_name;
                } elseif ($getUserparent5->agent_level == 'DL') {
                    $dl = $getUserparent5->user_name;
                } elseif ($getUserparent5->agent_level == 'COM') {
                    $com = $getUserparent5->user_name;
                }
            }

            /*echo "-->".$ad;
      exit;*/

            if (!empty($getUserparent5->parentid)) {
                $getUserparent6 = User::where('id', $getUserparent5->parentid)->first();

                if ($getUserparent6->agent_level == 'AD') {
                    $ad = $getUserparent6->user_name;
                } elseif ($getUserparent6->agent_level == 'SP') {
                    $sp = $getUserparent6->user_name;
                } elseif ($getUserparent6->agent_level == 'SMDL') {
                    $smdl = $getUserparent6->user_name;
                } elseif ($getUserparent6->agent_level == 'MDL') {
                    $mdl = $getUserparent6->user_name;
                } elseif ($getUserparent6->agent_level == 'DL') {
                    $dl = $getUserparent6->user_name;
                } elseif ($getUserparent6->agent_level == 'COM') {
                    $com = $getUserparent6->user_name;
                }
            }
            $is_delete = '';
            if ($bet->isDeleted == 0) {
                $is_delete .= '<a id="delete_row_' . $bet->id . '" onclick="delete_bet(' . $bet->id . ')" class="btn times_btn red-bg text-color-white"><i class="fas fa-times"></i></a>';
                $is_delete .= '<a style="display:none" id="rollback_row_' . $bet->id . '" onclick="rollback_bet(' . $bet->id . ')" class="btn times_btn green-bg text-color-white"><i class="fas fa-check"></i></a>';
            } else {
                $is_delete .= '<a id="rollback_row_' . $bet->id . '" onclick="rollback_bet(' . $bet->id . ')" class="btn times_btn green-bg text-color-white"><i class="fas fa-check"></i></a>';
                $is_delete .= '<a style="display:none" id="delete_row_' . $bet->id . '" onclick="delete_bet(' . $bet->id . ')" class="btn times_btn red-bg text-color-white"><i class="fas fa-times"></i></a>';
            }
            $html_two .= '<tr class="' . $bet_type_cls . '">
           		<td><input type="checkbox" id="select_all" name="filter-checkbox" value=""></td>
                <td class="text-center"><b>' . ucfirst($player['user_name']) . '[' . $player['first_name'] . ']</b></td>
                <td class="text-center"><b>' . $bet->team_name . '</b></td>
				<td class="text-center">' . $bet_type . '</td>
                <td class="text-center"><b>' . $bet->bet_odds . '</b></td>
                <td class="text-center"><b>' . $bet->bet_amount . '</b></td>
                <td class="text-center"><b>' . $bet->bet_profit . '</b></td>
                <td><b>Matched</b></td>
                <td>' . date('d-m-Y H:i:s A', strtotime($bet->created_at)) . '</td>
                <td class="text-center">' . $matchList->event_id . '</td>
                <td class="text-center"><i class="fas fa-mobile text-color-red"></i></td>';
            if ($loginUser->agent_level == 'COM') {
                $html_two .= '<td id="action_' . $bet->id . '">
	                	' . $is_delete . '
	               	</td>';
            }
            if ($loginUser->agent_level == 'COM') {
                $html_two .= '<td class="text-center">' . $com . '</td>';
            }
            if ($loginUser->agent_level == 'AD' || $loginUser->agent_level == 'COM') {
                $html_two .= '<td class="text-center">' . $ad . '</td>';
            }
            if ($loginUser->agent_level == 'SP' || $loginUser->agent_level == 'COM' || $loginUser->agent_level == 'AD') {
                $html_two .= '<td class="text-center">' . $sp . '</td>';
            }
            if ($loginUser->agent_level == 'SMDL' || $loginUser->agent_level == 'COM' || $loginUser->agent_level == 'AD' || $loginUser->agent_level == 'SP') {
                $html_two .= '<td class="text-center">' . $smdl . '</td>';
            }
            if ($loginUser->agent_level == 'MDL' || $loginUser->agent_level == 'COM' || $loginUser->agent_level == 'AD' || $loginUser->agent_level == 'SP' || $loginUser->agent_level == 'SMDL') {
                $html_two .= '<td class="text-center">' . $mdl . '</td>';
            }
            if ($loginUser->agent_level == 'DL' || $loginUser->agent_level == 'COM' || $loginUser->agent_level == 'AD' || $loginUser->agent_level == 'SP' || $loginUser->agent_level == 'SMDL' || $loginUser->agent_level == 'MDL') {
                $html_two .= '<td class="text-center">' . $dl . '</td>';
            }
            $html_two .= '</tr>';
        }
        //Fancy
        $html_three = '';
        if (!empty($valfnc)) {
            $srhdata = User::where('user_name', 'LIKE', '%' . $valfnc . '%')
                ->orWhere('first_name', 'LIKE', '%' . $valfnc . '%')
                ->get();

            foreach ($srhdata as $value) {
                $child_array[] = $value->id;
            }

            $my_placed_bets = MyBets::where('match_id', $matchList['event_id'])->where('bet_type', 'SESSION')->where('result_declare', 0)->whereIn('user_id', $child_array)->orderby('id', 'DESC')->get();

        } else {
            $my_placed_bets = MyBets::where('match_id', $matchList['event_id'])->where('bet_type', 'SESSION')->where('result_declare', 0)->whereIn('user_id', $all_child)->orderby('id', 'DESC')->get();
        }
        foreach ($my_placed_bets as $bet) {
            $player = User::where('id', $bet->user_id)->where('agent_level', 'PL')->first();
            $bet_type_cls = '';
            $bet_type = '';
            if ($bet->bet_side == 'lay') {
                $bet_type_cls = 'pink-bg';
                $bet_type = 'No';
            } else {
                $bet_type_cls = 'cyan-bg';
                $bet_type = 'Yes';
            }
            $getUserparent = User::where('id', $player->parentid)->first();
            /*echo $getUserparent;
			exit;*/
            $ad = '-';
            $sp = '-';
            $smdl = '-';
            $mdl = '-';
            $dl = '-';
            $com = '-';
            if ($getUserparent->agent_level == 'AD') {
                $ad = $getUserparent->user_name;
            } elseif ($getUserparent->agent_level == 'SP') {
                $sp = $getUserparent->user_name;
            } elseif ($getUserparent->agent_level == 'SMDL') {
                $smdl = $getUserparent->user_name;
            } elseif ($getUserparent->agent_level == 'MDL') {
                $mdl = $getUserparent->user_name;
            } elseif ($getUserparent->agent_level == 'DL') {
                $dl = $getUserparent->user_name;
            } elseif ($getUserparent->agent_level == 'COM') {
                $com = $getUserparent->user_name;
            }
            if (!empty($getUserparent->parentid)) {
                /*echo "aab->";
        	echo $getUserparent->parentid;
        	exit;*/
                $getUserparent2 = User::where('id', $getUserparent->parentid)->first();
                if ($getUserparent2->agent_level == 'AD') {
                    $ad = $getUserparent2->user_name;
                } elseif ($getUserparent2->agent_level == 'SP') {
                    $sp = $getUserparent2->user_name;
                } elseif ($getUserparent2->agent_level == 'SMDL') {
                    $smdl = $getUserparent2->user_name;
                } elseif ($getUserparent2->agent_level == 'MDL') {
                    $mdl = $getUserparent2->user_name;
                } elseif ($getUserparent2->agent_level == 'DL') {
                    $dl = $getUserparent2->user_name;
                } elseif ($getUserparent2->agent_level == 'COM') {
                    $com = $getUserparent2->user_name;
                }
            }

            if (!empty($getUserparent2->parentid)) {
                /*	echo "aam->";
      	echo $getUserparent2->parentid;
      	exit;*/
                $getUserparent3 = User::where('id', $getUserparent2->parentid)->first();
                if ($getUserparent3->agent_level == 'AD') {
                    $ad = $getUserparent3->user_name;
                } elseif ($getUserparent3->agent_level == 'SP') {
                    $sp = $getUserparent3->user_name;
                } elseif ($getUserparent3->agent_level == 'SMDL') {
                    $smdl = $getUserparent3->user_name;
                } elseif ($getUserparent3->agent_level == 'MDL') {
                    $mdl = $getUserparent3->user_name;
                } elseif ($getUserparent3->agent_level == 'DL') {
                    $dl = $getUserparent3->user_name;
                } elseif ($getUserparent3->agent_level == 'COM') {
                    $com = $getUserparent3->user_name;
                }
            }

            if (!empty($getUserparent3->parentid)) {
                /*echo "aam->";
      	echo $getUserparent3->parentid;
      	exit;*/
                $getUserparent4 = User::where('id', $getUserparent3->parentid)->first();

                if ($getUserparent4->agent_level == 'AD') {
                    $ad = $getUserparent4->user_name;
                } elseif ($getUserparent4->agent_level == 'SP') {
                    $sp = $getUserparent4->user_name;
                } elseif ($getUserparent4->agent_level == 'SMDL') {
                    $smdl = $getUserparent4->user_name;
                } elseif ($getUserparent4->agent_level == 'MDL') {
                    $mdl = $getUserparent4->user_name;
                } elseif ($getUserparent4->agent_level == 'DL') {
                    $dl = $getUserparent4->user_name;
                } elseif ($getUserparent4->agent_level == 'COM') {
                    $com = $getUserparent4->user_name;
                }
            }

            if (!empty($getUserparent4->parentid)) {
                /*echo "aam->";
      	echo $getUserparent4->parentid;
      	exit;*/
                $getUserparent5 = User::where('id', $getUserparent4->parentid)->first();

                if ($getUserparent5->agent_level == 'AD') {
                    $ad = $getUserparent5->user_name;
                } elseif ($getUserparent5->agent_level == 'SP') {
                    $sp = $getUserparent5->user_name;
                } elseif ($getUserparent5->agent_level == 'SMDL') {
                    $smdl = $getUserparent5->user_name;
                } elseif ($getUserparent5->agent_level == 'MDL') {
                    $mdl = $getUserparent5->user_name;
                } elseif ($getUserparent5->agent_level == 'DL') {
                    $dl = $getUserparent5->user_name;
                } elseif ($getUserparent5->agent_level == 'COM') {
                    $com = $getUserparent5->user_name;
                }
            }

            /*echo "-->".$ad;
      exit;*/

            if (!empty($getUserparent5->parentid)) {
                $getUserparent6 = User::where('id', $getUserparent5->parentid)->first();

                if ($getUserparent6->agent_level == 'AD') {
                    $ad = $getUserparent6->user_name;
                } elseif ($getUserparent6->agent_level == 'SP') {
                    $sp = $getUserparent6->user_name;
                } elseif ($getUserparent6->agent_level == 'SMDL') {
                    $smdl = $getUserparent6->user_name;
                } elseif ($getUserparent6->agent_level == 'MDL') {
                    $mdl = $getUserparent6->user_name;
                } elseif ($getUserparent6->agent_level == 'DL') {
                    $dl = $getUserparent6->user_name;
                } elseif ($getUserparent6->agent_level == 'COM') {
                    $com = $getUserparent6->user_name;
                }
            }
            $is_delete = '';
            if ($bet->isDeleted == 0) {
                $is_delete .= '<a id="delete_row_' . $bet->id . '" onclick="delete_bet(' . $bet->id . ')" class="btn times_btn red-bg text-color-white"><i class="fas fa-times"></i></a>';
                $is_delete .= '<a style="display:none" id="rollback_row_' . $bet->id . '" onclick="rollback_bet(' . $bet->id . ')" class="btn times_btn green-bg text-color-white"><i class="fas fa-check"></i></a>';
            } else {
                $is_delete .= '<a id="rollback_row_' . $bet->id . '" onclick="rollback_bet(' . $bet->id . ')" class="btn times_btn green-bg text-color-white"><i class="fas fa-check"></i></a>';
                $is_delete .= '<a style="display:none" id="delete_row_' . $bet->id . '" onclick="delete_bet(' . $bet->id . ')" class="btn times_btn red-bg text-color-white"><i class="fas fa-times"></i></a>';
            }
            $binfo = $bet->browser_details . ' &#013; ' . $bet->ip_address;
            $html_three .= '<tr class="' . $bet_type_cls . '">
			    <td><input type="checkbox" id="select_all" name="filter-checkbox" value=""></td>
                <td class="text-center"><b>' . ucfirst($player['user_name']) . '[' . $player['first_name'] . ']</b></td>
                <td class="text-center"><b>' . $bet->team_name . '</b></td>
				<td class="text-center">' . $bet_type . '</td>
                <td class="text-center"><b>' . $bet->bet_odds . '</b></td>
				<td class="text-center"><b>' . $bet->bet_amount . '</b></td>
                <td>' . date('d-m-Y H:i:s A', strtotime($bet->created_at)) . '</td>';
            $html_three .= '<td class="text-center"><i class="fas fa-mobile text-color-red" data-toggle="tooltip" data-placement="top" title="' . $binfo . '"></i></td>';
            if ($loginUser->agent_level == 'COM') {
                $html_three .= '<td id="action_' . $bet->id . '">
	                	' . $is_delete . '
					</td>';
            }
            if ($loginUser->agent_level == 'COM') {
                $html_three .= '<td class="text-center">' . $com . '</td>';
            }
            if ($loginUser->agent_level == 'AD' || $loginUser->agent_level == 'COM') {
                $html_three .= '<td class="text-center">' . $ad . '</td>';
            }
            if ($loginUser->agent_level == 'SP' || $loginUser->agent_level == 'COM' || $loginUser->agent_level == 'AD') {
                $html_three .= '<td class="text-center">' . $sp . '</td>';
            }
            if ($loginUser->agent_level == 'SMDL' || $loginUser->agent_level == 'COM' || $loginUser->agent_level == 'AD' || $loginUser->agent_level == 'SP') {
                $html_three .= '<td class="text-center">' . $smdl . '</td>';
            }
            if ($loginUser->agent_level == 'MDL' || $loginUser->agent_level == 'COM' || $loginUser->agent_level == 'AD' || $loginUser->agent_level == 'SP' || $loginUser->agent_level == 'SMDL') {
                $html_three .= '<td class="text-center">' . $mdl . '</td>';
            }
            if ($loginUser->agent_level == 'DL' || $loginUser->agent_level == 'COM' || $loginUser->agent_level == 'AD' || $loginUser->agent_level == 'SP' || $loginUser->agent_level == 'SMDL' || $loginUser->agent_level == 'MDL') {
                $html_three .= '<td class="text-center">' . $dl . '</td>';
            }
            $html_three .= '</tr>';
        }
        echo $html . "~~" . $html_two . "~~" . $html_three;
    }

    public function risk_management_odds_search(Request $request)
    {
        $loginUser = Auth::user();
        $matchList = Match::where('match_id', $request->matchid)->first();

        $srhdata = User::where('user_name', 'LIKE', '%' . $request->search . '%')
            ->orWhere('first_name', 'LIKE', '%' . $request->search . '%')
            ->get();
        //echo"<pre>"; print_r($srhdata); exit;
        if (!empty($srhdata)) {
            foreach ($srhdata as $value) {
                $child_array[] = $value->id;
            }

            $my_placed_bets = MyBets::where('match_id', $matchList['event_id'])->where('bet_type', 'ODDS')->where('result_declare', 0)->whereIn('user_id', $child_array)->orderby('id', 'DESC')->get();

            $html = '';
            foreach ($my_placed_bets as $bet) {
                $player = User::where('id', $bet->user_id)->where('agent_level', 'PL')->first();
                $bet_type_cls = '';
                $bet_type = '';
                if ($bet->bet_side == 'lay') {
                    $bet_type_cls = 'pink-bg';
                    $bet_type = 'Lay';
                } else {
                    $bet_type_cls = 'cyan-bg';
                    $bet_type = 'Back';
                }
                $is_delete = '';
                if ($bet->isDeleted == 0) {
                    $is_delete .= '<a id="delete_row_' . $bet->id . '" onclick="delete_bet(' . $bet->id . ')" class="btn times_btn red-bg text-color-white"><i class="fas fa-times"></i></a>';
                    $is_delete .= '<a style="display:none" id="rollback_row_' . $bet->id . '" onclick="rollback_bet(' . $bet->id . ')" class="btn times_btn green-bg text-color-white"><i class="fas fa-check"></i></a>';
                } else {
                    $is_delete .= '<a id="rollback_row_' . $bet->id . '" onclick="rollback_bet(' . $bet->id . ')" class="btn times_btn green-bg text-color-white"><i class="fas fa-check"></i></a>';
                    $is_delete .= '<a style="display:none" id="delete_row_' . $bet->id . '" onclick="delete_bet(' . $bet->id . ')" class="btn times_btn red-bg text-color-white"><i class="fas fa-times"></i></a>';
                }
                $binfo = $bet->browser_details . ' &#013; ' . $bet->ip_address;
                $html .= '<tr class="' . $bet_type_cls . '">

	                <td class="text-center"><b>' . ucfirst($player['first_name']) . '[' . $player['user_name'] . ']</b></td>
	                <td class="text-center"><b>' . $bet->team_name . '</b></td>
					<td class="text-center">' . $bet_type . '</td>
	                <td class="text-center"><b>' . $bet->bet_odds . '</b></td>
	                <td class="text-center"><b>' . $bet->bet_amount . '</b></td>
	                <td class="text-center"><b>' . $bet->bet_profit . '</b></td>
	                <td>' . date('d-m-Y H:i:s A', strtotime($bet->created_at)) . '</td>
	                <td class="text-center"><i class="fas fa-mobile text-color-red" data-toggle="tooltip" data-placement="top" title="' . $binfo . '"></i></td>';
                if ($loginUser->agent_level == 'COM') {
                    $html .= '<td id="action_' . $bet->id . '">
		                	' . $is_delete . '
		               	</td>';
                }
                $html .= '</tr>';
            }
        } else {
            $html .= '<h3>No Record Found</h3>';
        }
        echo $html;
    }

    public function risk_management_bm_search(Request $request)
    {
        $loginUser = Auth::user();
        $matchList = Match::where('match_id', $request->matchid)->first();

        $srhdata = User::where('user_name', 'LIKE', '%' . $request->search . '%')
            ->orWhere('first_name', 'LIKE', '%' . $request->search . '%')
            ->get();
        //echo"<pre>"; print_r($srhdata); exit;
        if (!empty($srhdata)) {
            foreach ($srhdata as $value) {
                $child_array[] = $value->id;
            }

            $html = '';
            $my_placed_bets = MyBets::where('match_id', $matchList['event_id'])->where('bet_type', 'BOOKMAKER')->where('result_declare', 0)->whereIn('user_id', $child_array)->orderby('id', 'DESC')->get();
            foreach ($my_placed_bets as $bet) {
                $player = User::where('id', $bet->user_id)->where('agent_level', 'PL')->first();
                $bet_type_cls = '';
                $bet_type = '';
                if ($bet->bet_side == 'lay') {
                    $bet_type_cls = 'pink-bg';
                    $bet_type = 'Lay';
                } else {
                    $bet_type_cls = 'cyan-bg';
                    $bet_type = 'Back';
                }
                $is_delete = '';
                if ($bet->isDeleted == 0) {
                    $is_delete .= '<a id="delete_row_' . $bet->id . '" onclick="delete_bet(' . $bet->id . ')" class="btn times_btn red-bg text-color-white"><i class="fas fa-times"></i></a>';
                    $is_delete .= '<a style="display:none" id="rollback_row_' . $bet->id . '" onclick="rollback_bet(' . $bet->id . ')" class="btn times_btn green-bg text-color-white"><i class="fas fa-check"></i></a>';
                } else {
                    $is_delete .= '<a id="rollback_row_' . $bet->id . '" onclick="rollback_bet(' . $bet->id . ')" class="btn times_btn green-bg text-color-white"><i class="fas fa-check"></i></a>';
                    $is_delete .= '<a style="display:none" id="delete_row_' . $bet->id . '" onclick="delete_bet(' . $bet->id . ')" class="btn times_btn red-bg text-color-white"><i class="fas fa-times"></i></a>';
                }
                $html .= '<tr class="' . $bet_type_cls . '">

	                <td class="text-center"><b>' . ucfirst($player['first_name']) . '[' . $player['user_name'] . ']</b></td>
	                <td class="text-center"><b>' . $bet->team_name . '</b></td>
					<td class="text-center">' . $bet_type . '</td>
	                <td class="text-center"><b>' . $bet->bet_odds . '</b></td>
	                <td class="text-center"><b>' . $bet->bet_amount . '</b></td>
	                <td class="text-center"><b>' . $bet->bet_profit . '</b></td>
	                <td><b>Matched</b></td>
	                <td>' . date('d-m-Y H:i:s A', strtotime($bet->created_at)) . '</td>
	                <td class="text-center">' . $matchList->event_id . '</td>
	                <td class="text-center"><i class="fas fa-mobile text-color-red"></i></td>';
                if ($loginUser->agent_level == 'COM') {
                    $html .= '<td id="action_' . $bet->id . '">
		                	' . $is_delete . '
		               	</td>';
                }
                $html .= '</tr>';
            }
        } else {
            $html .= '<h3>No Record Found</h3>';
        }
        echo $html;
    }

    public function risk_management_fancy_search(Request $request)
    {
        $loginUser = Auth::user();
        $matchList = Match::where('match_id', $request->matchid)->first();

        $srhdata = User::where('user_name', 'LIKE', '%' . $request->search . '%')
            ->orWhere('first_name', 'LIKE', '%' . $request->search . '%')
            ->get();
        //echo"<pre>"; print_r($srhdata); exit;
        if (!empty($srhdata)) {
            foreach ($srhdata as $value) {
                $child_array[] = $value->id;
            }

            $html = '';
            $my_placed_bets = MyBets::where('match_id', $matchList['event_id'])->where('bet_type', 'SESSION')->where('result_declare', 0)->whereIn('user_id', $child_array)->orderby('id', 'DESC')->get();
            foreach ($my_placed_bets as $bet) {
                $player = User::where('id', $bet->user_id)->where('agent_level', 'PL')->first();
                $bet_type_cls = '';
                $bet_type = '';
                if ($bet->bet_side == 'lay') {
                    $bet_type_cls = 'pink-bg';
                    $bet_type = 'Lay';
                } else {
                    $bet_type_cls = 'cyan-bg';
                    $bet_type = 'Back';
                }
                $is_delete = '';
                if ($bet->isDeleted == 0) {
                    $is_delete .= '<a id="delete_row_' . $bet->id . '" onclick="delete_bet(' . $bet->id . ')" class="btn times_btn red-bg text-color-white"><i class="fas fa-times"></i></a>';
                    $is_delete .= '<a style="display:none" id="rollback_row_' . $bet->id . '" onclick="rollback_bet(' . $bet->id . ')" class="btn times_btn green-bg text-color-white"><i class="fas fa-check"></i></a>';
                } else {
                    $is_delete .= '<a id="rollback_row_' . $bet->id . '" onclick="rollback_bet(' . $bet->id . ')" class="btn times_btn green-bg text-color-white"><i class="fas fa-check"></i></a>';
                    $is_delete .= '<a style="display:none" id="delete_row_' . $bet->id . '" onclick="delete_bet(' . $bet->id . ')" class="btn times_btn red-bg text-color-white"><i class="fas fa-times"></i></a>';
                }
                $binfo = $bet->browser_details . ' &#013; ' . $bet->ip_address;
                $html .= '<tr class="' . $bet_type_cls . '">
	                <td class="text-center"><b>' . ucfirst($player['first_name']) . '[' . $player['user_name'] . ']</b></td>
	                <td class="text-center"><b>' . $bet->team_name . '</b></td>
					<td class="text-center">' . $bet_type . '</td>
	                <td class="text-center"><b>' . $bet->bet_odds . '</b></td>
					<td class="text-center"><b>' . $bet->bet_amount . '</b></td>
	                <td>' . date('d-m-Y H:i:s A', strtotime($bet->created_at)) . '</td>';
                if ($loginUser->agent_level == 'COM') {
                    $html .= '<td id="action_' . $bet->id . '">
		                	' . $is_delete . '
						</td>';
                }

                $html .= '<td class="text-center"><i class="fas fa-mobile text-color-red" data-toggle="tooltip" data-placement="top" title="' . $binfo . '"></i></td>
	            </tr>';
            }
        } else {
            $html .= '<h3>No Record Found</h3>';
        }
        echo $html;
    }

    public function delete_user_bet(Request $request)
    {
        $bid = $request->bid;
        $userData = MyBets::find($bid);
        $expamt = $userData->exposureAmt;
        $getc = CreditReference::where('player_id', $userData->user_id)->first();
        $creid = $getc['id'];
        $updc = CreditReference::find($creid);

        //calculate original exposure for this match
        $userId = $userData->user_id;
        $event_id = $userData->match_id;
//        $my_placed_bets = MyBets::where('user_id', $userId)->where('match_id', $event_id)->where('bet_type', 'ODDS')->where('isDeleted', 0)->where('result_declare', 0)->orderby('id', 'DESC')->get();
        $my_placed_bets = MyBets::where('user_id', $userId)->where('match_id', $event_id)->where('bet_type',"!=", 'SESSION')->where('isDeleted', 0)->where('result_declare', 0)->orderby('id', 'DESC')->get();
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

        $expo = min($team1_bet_total, $team2_bet_total, $team_draw_bet_total);
        $original_expo = 0;
        if ($expo < 0) {
            $original_expo = $expo;
        }

        $my_placed_bets = MyBets::select('user_id', 'match_id', 'bet_type', 'bet_side', 'bet_odds', 'bet_oddsk', 'bet_amount', 'bet_profit', 'team_name', 'exposureAmt')
            ->where('user_id', $userId)->where('bet_type', 'SESSION')->where('isDeleted', 0)->where('result_declare', 0)->groupby('team_name', 'match_id')->orderBy('created_at', 'asc')->get();

        $original_total_fancy_expo = 0;
        foreach ($my_placed_bets as $bet) {
            $or_sessionexposer = SELF::getAllSessionExposure($userId, $bet->team_name, $bet->match_id);
            $original_total_fancy_expo = $original_total_fancy_expo + $or_sessionexposer;
        }

        $userData = MyBets::find($bid);
        $userData->isDeleted = 1;
        $del = $userData->update();
        if ($del) {
//            $my_placed_bets = MyBets::where('user_id', $userId)->where('match_id', $event_id)->where('bet_type', 'ODDS')->where('isDeleted', 0)->where('result_declare', 0)->orderby('id', 'DESC')->get();
            $my_placed_bets = MyBets::where('user_id', $userId)->where('match_id', $event_id)->where('bet_type',"!=", 'SESSION')->where('isDeleted', 0)->where('result_declare', 0)->orderby('id', 'DESC')->get();
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
                        }
                        else if (count($abc) == 1) {
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

            $expo = min($team1_bet_total, $team2_bet_total, $team_draw_bet_total);
            $new_expo = 0;
            if ($expo < 0) {
                $new_expo = $expo;
            }

            $my_placed_bets = MyBets::select('user_id', 'match_id', 'bet_type', 'bet_side', 'bet_odds', 'bet_oddsk', 'bet_amount', 'bet_profit', 'team_name', 'exposureAmt')
                ->where('user_id', $userId)->where('bet_type', 'SESSION')->where('isDeleted', 0)->where('result_declare', 0)->groupby('team_name', 'match_id')->orderBy('created_at', 'asc')->get();

            $total_fancy_expo = 0;
            foreach ($my_placed_bets as $bet) {
                $sessionexposer = SELF::getAllSessionExposure($userId, $bet->team_name, $bet->match_id);
                $total_fancy_expo = $total_fancy_expo + $sessionexposer;
            }

            $updc->exposure = ($getc->exposure - abs($original_expo) - $original_total_fancy_expo) + abs($new_expo) + $total_fancy_expo;
            $updc->available_balance_for_D_W = ($getc->available_balance_for_D_W + abs($original_expo) + $original_total_fancy_expo) - abs($new_expo) - $total_fancy_expo ;
            $upd = $updc->update();

            if ($upd)
                return 'Success';
            else
                return 'Fail';
        } else {
            return 'Fail';
        }
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

    public function rollback_user_bet(Request $request)
    {
        $bid = $request->bid;
        $userData = MyBets::find($bid);
        $expamt = $userData->exposureAmt;
        $getc = CreditReference::where('player_id', $userData->user_id)->first();
        $creid = $getc['id'];
        $updc = CreditReference::find($creid);

        //calculate original exposure for this match
        $userId = $userData->user_id;
        $event_id = $userData->match_id;
        $my_placed_bets = MyBets::where('user_id', $userId)->where('match_id', $event_id)->where('bet_type',"!=", 'SESSION')->where('isDeleted', 0)->where('result_declare', 0)->orderby('id', 'DESC')->get();
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
        //echo $team1_bet_total.','.$team2_bet_total.','.$team_draw_bet_total; echo "<br>";
        $expo = min($team1_bet_total, $team2_bet_total, $team_draw_bet_total);
        $original_expo = 0;
        if ($expo < 0) {
            $original_expo = $expo;
        }

        $my_placed_bets = MyBets::select('user_id', 'match_id', 'bet_type', 'bet_side', 'bet_odds', 'bet_oddsk', 'bet_amount', 'bet_profit', 'team_name', 'exposureAmt')
            ->where('user_id', $userId)->where('bet_type', 'SESSION')->where('isDeleted', 0)->where('result_declare', 0)->groupby('team_name', 'match_id')->orderBy('created_at', 'asc')->get();

        $original_total_fancy_expo = 0;
        foreach ($my_placed_bets as $bet) {
            $or_sessionexposer = SELF::getAllSessionExposure($userId, $bet->team_name, $bet->match_id);
            $original_total_fancy_expo = $original_total_fancy_expo + $or_sessionexposer;
        }


        $userData = MyBets::find($bid);
        $userData->isDeleted = 0;
        $del = $userData->update();
        if ($del) {
            $my_placed_bets = MyBets::where('user_id', $userId)->where('match_id', $event_id)->where('bet_type',"!=", 'SESSION')->where('isDeleted', 0)->where('result_declare', 0)->orderby('id', 'DESC')->get();
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

            $expo = min($team1_bet_total, $team2_bet_total, $team_draw_bet_total);
            $new_expo = 0;
            if ($expo < 0) {
                $new_expo = $expo;
            }


            $my_placed_bets = MyBets::select('user_id', 'match_id', 'bet_type', 'bet_side', 'bet_odds', 'bet_oddsk', 'bet_amount', 'bet_profit', 'team_name', 'exposureAmt')
                ->where('user_id', $userId)->where('bet_type', 'SESSION')->where('isDeleted', 0)->where('result_declare', 0)->groupby('team_name', 'match_id')->orderBy('created_at', 'asc')->get();

            $total_fancy_expo = 0;
            foreach ($my_placed_bets as $bet) {
                $sessionexposer = SELF::getAllSessionExposure($userId, $bet->team_name, $bet->match_id);
                $total_fancy_expo = $total_fancy_expo + $sessionexposer;
            }

            $updc->exposure = ($getc->exposure - abs($original_expo) - $original_total_fancy_expo) + abs($new_expo) + $total_fancy_expo;
            $updc->available_balance_for_D_W = ($getc->available_balance_for_D_W + abs($original_expo) + $original_total_fancy_expo) - abs($new_expo) - $total_fancy_expo ;
            $upd = $updc->update();


//            $updc->exposure = $getc->exposure - abs($original_expo) + abs($new_expo);
//            $updc->available_balance_for_D_W = $getc->available_balance_for_D_W + abs($original_expo) - abs($new_expo);
//            $upd = $updc->update();
            if ($upd)
                return 'Success';
            else
                return 'Fail';
        } else {
            return 'Fail';
        }
    }

    /*public function rollback_user_bet(Request $request)
	{
		$bid=$request->bid;
		$userData = MyBets::find($bid);
		$expamt=$userData->exposureAmt;
		$getc=CreditReference::where('player_id',$userData->user_id)->first();
		$creid=$getc['id'];
		$updc=CreditReference::find($creid);
		$updc->exposure = $getc->exposure+$expamt;
		$chk=$getc->available_balance_for_D_W-$expamt;
		if($chk>0 && $getc->available_balance_for_D_W>0 && $getc->available_balance_for_D_W>$expamt)
		{
			$updc->available_balance_for_D_W = $getc->available_balance_for_D_W-$expamt;
			$upd=$updc->update();
			if($upd)
			{
				$userData = MyBets::find($bid);
				$userData->isDeleted = 0;
				$del=$userData->update();
				if($del)
					return 'Success';
				else
					return 'Fail';
			}
			else
			{
				return 'Fail';
			}
		}
		else
		{
			return 'Fail';
		}
	}*/
    public function risk_management_details_ajax($id, Request $request)
    {

        $matchtype = $request->matchtype;
        $sport = Sport::where('sId', $matchtype)->first();
        $matchtype = $sport->id;
        $matchId = $request->matchid;
        $matchname = $request->matchname;
        $event_id = $request->event_id;
        $match_m = $request->match_m;
        $team = explode(" v ", strtolower($matchname));
        $team2_bet_total = 0;
        $team1_bet_total = 0;
        $team_draw_bet_total = 0;

        $login_check = '';

        //get all child of agent
        $loginUser = Auth::user();
        $ag_id = $loginUser->id;
        $all_child = $this->GetChildofAgent($ag_id);
        $website = UsersAccount::getWebsite();
        $my_placed_bets = MyBets::where('match_id', $event_id)->where('bet_type', 'ODDS')->where('result_declare', 0)->where('isDeleted', 0)->whereIn('user_id', $all_child)->orderby('id', 'DESC')->get();
        if (sizeof($my_placed_bets) > 0) {
            foreach ($my_placed_bets as $bet) {

                $abc = json_decode($bet->extra, true);
                if (count($abc) >= 2) {
                    if (array_key_exists("teamname1", $abc) && array_key_exists("teamname2", $abc)) {
                        //bet on draw
                        if ($bet->bet_side == 'back') {
                            $team1_bet_total = $team1_bet_total + $bet->exposureAmt;
                            if (count($abc) >= 2) {
                                $team_draw_bet_total = $team_draw_bet_total - $bet->bet_profit;
                            }
                            $team2_bet_total = $team2_bet_total + $bet->exposureAmt;
                        }
                        if ($bet->bet_side == 'lay') {
                            $team1_bet_total = $team1_bet_total - ($bet->bet_amount);
                            if (count($abc) >= 2) {
                                $team_draw_bet_total = $team_draw_bet_total + ($bet->exposureAmt);
                            }
                            $team2_bet_total = $team2_bet_total - ($bet->bet_amount);
                        }
                    } else if (array_key_exists("teamname3", $abc) && array_key_exists("teamname2", $abc)) {
                        //bet on team1
                        if ($bet->bet_side == 'back') {
                            $team1_bet_total = $team1_bet_total - $bet->bet_profit;
                            if (count($abc) >= 2) {
                                $team_draw_bet_total = $team_draw_bet_total + $bet->exposureAmt;
                            }
                            $team2_bet_total = $team2_bet_total + $bet->exposureAmt;
                        }
                        if ($bet->bet_side == 'lay') {
                            $team1_bet_total = $team1_bet_total + ($bet->exposureAmt);
                            if (count($abc) >= 2) {
                                $team_draw_bet_total = $team_draw_bet_total - ($bet->bet_amount);
                            }
                            $team2_bet_total = $team2_bet_total - ($bet->bet_amount);
                        }
                    } else if (array_key_exists("teamname3", $abc) && array_key_exists("teamname1", $abc)) {
                        //bet on team2
                        if ($bet->bet_side == 'back') {
                            $team2_bet_total = $team2_bet_total - ($bet->bet_profit);
                            if (count($abc) >= 2) {
                                $team_draw_bet_total = $team_draw_bet_total + $bet->exposureAmt;
                            }
                            $team1_bet_total = $team1_bet_total + $bet->exposureAmt;
                        }
                        if ($bet->bet_side == 'lay') {
                            $team2_bet_total = $team2_bet_total + $bet->exposureAmt;
                            if (count($abc) >= 2) {
                                $team_draw_bet_total = $team_draw_bet_total - $bet->bet_amount;
                            }
                            $team1_bet_total = $team1_bet_total - $bet->bet_amount;
                        }
                    }
                }
                else if (count($abc) == 1) {
                    if (array_key_exists("teamname1", $abc)) {
                        //bet on team2
                        if ($bet->bet_side == 'back') {
                            $team2_bet_total = $team2_bet_total - $bet->bet_profit;
                            $team1_bet_total = $team1_bet_total + $bet->exposureAmt;
                        }
                        if ($bet->bet_side == 'lay') {
                            $team2_bet_total = $team2_bet_total + $bet->exposureAmt;
                            $team1_bet_total = $team1_bet_total - $bet->bet_amount;
                        }
                    } else {
                        //bet on team1
                        if ($bet->bet_side == 'back') {
                            $team1_bet_total = $team1_bet_total - $bet->bet_profit;
                            $team2_bet_total = $team2_bet_total + $bet->exposureAmt;
                        }
                        if ($bet->bet_side == 'lay') {
                            $team1_bet_total = $team1_bet_total + $bet->exposureAmt;
                            $team2_bet_total = $team2_bet_total - $bet->bet_amount;
                        }
                    }
                }

            }
        }

//        $match_data = app('App\Http\Controllers\RestApi')->DetailCall($matchId, $event_id, $matchtype);
        $match_data = app('App\Http\Controllers\RestApi')->getSingleCricketMatchData($event_id, $matchId, $sport->sId);
        $section = '';
        if ($sport->sId == '1') { //soccer
            $section = '3';
        } elseif ($sport->sId == '2') { //tennis
            $section = '2';
        } elseif ($sport->sId == '4') { //cricket
            $section = 4;
        }
        $html = '';
        if ($match_data != 0) {
            $html_chk = '';
            if ($match_m == '0') {
                $html_chk .= '
						<tr class="fancy-suspend-tr team1_fancy 11111">
							<td></td>
							<td class="fancy-suspend-td" colspan="6">
								<div class="fancy-suspend black-bg-5 text-color-white"><span>SUSPENDED</span></div>
							</td>
						</tr>
						<tr class="white-bg tr_team1">
							<td> <img src="' . asset('asset/front/img/bars.png') . '"> <b class="team1">' . ucfirst($team[0]) . ' </b>
								<div>
									<span class="lose " id="team1_bet_count_old"></span>
									<span class="towin text-color-green" style="display:none" id="team1_bet_count_new">0.00</span>
								</div>
							</td>
							<td class="light-blue-bg-2 spark opnForm ODDSBack td_team1_back_2" >
								<a class="back1btn text-color-black">--</span></a>
							</td>
							<td class="link(target, link)ght-blue-bg-3 spark ODDSBack td_team1_back_1" data-team="team1">
								<a  class="back1btn text-color-black"> --<br><span>--</span></a>
							</td>
							<td class="cyan-bg spark ODDSBack td_team1_back_0" >
								<a  class="back1btn text-color-black">-- <br><span>--</span></a>
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
						<tr class="fancy-suspend-tr team2_fancy">
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
                if ($section == 4) {
                    if (@$match_data['t1'][0][2]['b1'] != '') {
                        $html_chk .= '
								<tr class="fancy-suspend-tr team3_fancy">
									<td></td>
									<td class="fancy-suspend-td" colspan="6">
										<div class="fancy-suspend black-bg-5 text-color-white"><span>SUSPENDED</span></div>
									</td>
								</tr>
								<tr class="white-bg tr_team3 1111111111">
									<td> <img src="' . asset('asset/front/img/bars.png') . '"> <b class="team1">THE DRAW</b>
										<div>
											<span class="lose " id="team1_bet_count_old"></span>
											<span class="towin text-color-green" style="display:none" id="team1_bet_count_new">0.00</span>
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
                }
            }
            else {
                //check status
                if (@$match_data['t1'][0][0]['mstatus'] == 'OPEN' || @$match_data[0]['status'] == 'OPEN') {
                    //check section and set data for it
                    if ($section == 2 || $section == 3) { //tennis || soccer
                        if (isset($match_data[0]['runners'][0])) {
                            $display = '';
                            $cls = '';
                            if ($team_draw_bet_total == '' && $team1_bet_total && $team2_bet_total == "")
                                $display = 'style="display:none"';
                            else {
                                if ($team1_bet_total == "")
                                    $team1_bet_total = 0.00;
                            }
                            if ($team1_bet_total != '' && $team1_bet_total >= 0) {
                                $cls = 'text-color-green';
                            } else if ($team1_bet_total != '' && $team1_bet_total < 0) {
                                $cls = 'text-color-red';
                            }

                            if ($team1_bet_total != '' || $team2_bet_total != '' || $team_draw_bet_total != '') {
                                if ($team1_bet_total == '')
                                    $team1_bet_total = 0;
                                if ($team2_bet_total == '')
                                    $team2_bet_total = 0;
                                if ($team_draw_bet_total == '')
                                    $team_draw_bet_total = 0;
                            }

                            $html .= '<tr class="white-bg tr_team1" id="team1">
										<td> <img src="' . asset('asset/front/img/bars.png') . '"> <b class="team1">' . ucfirst($team[0]) . ' </b>
											<div>
												<span class="lose ' . $cls . '" ' . $display . ' id="team1_bet_count_old">(<span id="team1_total">' . round($team1_bet_total, 2) . '</span>)</span>
												<span class="towin text-color-green" style="display:none" id="team1_bet_count_new">0.00</span>
											</div>
										</td>
										<td class="light-blue-bg-4 spark opnForm ODDSBack text-center back1 spark td_team1_back_2" data-team="team1">
											<a data-bettype="ODDS" data-team="team1" ' . $login_check . '  data-val="' .
                                @$match_data[0]['runners'][0]['ex']['availableToBack'][2]['price'] . '" data-cls="cyan-bg" class="back1btn text-color-black">
												' .
                                @$match_data[0]['runners'][0]['ex']['availableToBack'][2]['price'] . ' <br><span>' . $this->number_format_short(@$match_data[0]['runners'][0]['ex']['availableToBack'][2]['size']) . '</span>
											</a>
										</td>
										<td class="link(target, link)ght-blue-bg-3 lightblue-bg5 text-center ODDSBack td_team1_back_1" data-team="team1">
											<a data-bettype="ODDS" data-team="team1" ' . $login_check . '  data-cls="cyan-bg" data-val="' .
                                @$match_data[0]['runners'][0]['ex']['availableToBack'][1]['price'] . '" class="back1btn text-color-black"> ' .
                                @$match_data[0]['runners'][0]['ex']['availableToBack'][1]['price'] . '<br><span>' . $this->number_format_short(@$match_data[0]['runners'][0]['ex']['availableToBack'][1]['size']) . '</span></a>
										</td>
										<td class="lightblue-bg3 ODDSBack text-center td_team1_back_0" data-team="team1">
											<a data-bettype="ODDS" data-team="team1" ' . $login_check . '  data-val="' .
                                @$match_data[0]['runners'][0]['ex']['availableToBack'][0]['price'] . '" data-cls="cyan-bg" class="back1btn text-color-black"> ' .
                                @$match_data[0]['runners'][0]['ex']['availableToBack'][0]['price'] . ' <br><span>' . $this->number_format_short(@$match_data[0]['runners'][0]['ex']['availableToBack'][0]['size']) . '</span></a>
										</td>
										<td class="lightpink-bg3 sparkLay lay1 layhover ODDSLay text-center td_team1_lay_0" data-team="team1">
											<a data-bettype="ODDS" data-team="team1" ' . $login_check . '  data-val="' .
                                @$match_data[0]['runners'][0]['ex']['availableToLay'][0]['price'] . '" data-cls="pink-bg" class="lay1btn text-color-black"> ' .
                                @$match_data[0]['runners'][0]['ex']['availableToLay'][0]['price'] . ' <br><span>' . $this->number_format_short(@$match_data[0]['runners'][0]['ex']['availableToLay'][0]['size']) . '</span></a>
										</td>
										<td class="lightpink-bg4 sparkLay text-center ODDSLay td_team1_lay_1" data-team="team1">
											<a data-bettype="ODDS" data-team="team1" ' . $login_check . ' href="javascript:void(0)" data-val="' .
                                @$match_data[0]['runners'][0]['ex']['availableToLay'][1]['price'] . '" data-cls="pink-bg" class="lay1btn text-color-black"> ' .
                                @$match_data[0]['runners'][0]['ex']['availableToLay'][1]['price'] . ' <br><span>' . $this->number_format_short(@$match_data[0]['runners'][0]['ex']['availableToLay'][1]['size']) . '</span></a>
										</td>
										<td class="lightpink-bg5     sparkLay text-center ODDSLay td_team1_lay_2" data-team="team1">
											<a data-bettype="ODDS" data-team="team1" ' . $login_check . '  data-val="' .
                                @$match_data[0]['runners'][0]['ex']['availableToLay'][2]['price'] . '" data-cls="pink-bg" class="lay1btn text-color-black"> ' .
                                @$match_data[0]['runners'][0]['ex']['availableToLay'][2]['price'] . ' <br><span>' . $this->number_format_short(@$match_data[0]['runners'][0]['ex']['availableToLay'][2]['size']) . '</span></a>
										</td>
									</tr>
									';
                        }
                    }
                    else {
                        if (isset($match_data['t1'][0][0]['b1'])) {

                            $display = '';
                            $cls = '';
                            if ($team_draw_bet_total == '' && $team1_bet_total && $team2_bet_total == "")
                                $display = 'style="display:none"';
                            else {
                                if ($team1_bet_total == "")
                                    $team1_bet_total = 0.00;
                            }
                            if ($team1_bet_total != '' && $team1_bet_total >= 0) {
                                $cls = 'text-color-green';
                            } else if ($team1_bet_total != '' && $team1_bet_total < 0) {
                                $cls = 'text-color-red';
                            }

                            if ($team1_bet_total != '' || $team2_bet_total != '' || $team_draw_bet_total != '') {
                                if ($team1_bet_total == '')
                                    $team1_bet_total = 0;
                                if ($team2_bet_total == '')
                                    $team2_bet_total = 0;
                                if ($team_draw_bet_total == '')
                                    $team_draw_bet_total = 0;
                            }

                            $html .= '<tr class="white-bg tr_team1" id="team1">
										<td> <img src="' . asset('asset/front/img/bars.png') . '"> <b class="team1">' . ucfirst($team[0]) . ' </b>
											<div>
												<span class="lose ' . $cls . '" ' . $display . ' id="team1_bet_count_old">(<span id="team1_total">' . round($team1_bet_total, 2) . '</span>)</span>
												<span class="towin text-color-green" style="display:none" id="team1_bet_count_new">0.00</span>
											</div>
										</td>
										<td class="lightblue-bg4 opnForm text-center ODDSBack td_team1_back_2" data-team="team1">
											<a data-bettype="ODDS" data-team="team1" ' . $login_check . '  data-val="' .
                                @$match_data['t1'][0][0]['b3'] . '" data-cls="cyan-bg" class="back1btn text-color-black">
												' .
                                @$match_data['t1'][0][0]['b3'] . ' <br><span>' . $this->number_format_short(@$match_data['t1'][0][0]['bs3']) . '</span>
											</a>
										</td>
										<td class="link(target, link) lightblue-bg5 text-center spark ODDSBack td_team1_back_1" data-team="team1">
											<a data-bettype="ODDS" data-team="team1" ' . $login_check . '  data-cls="cyan-bg" data-val="' .
                                @$match_data['t1'][0][0]['b2'] . '" class="back1btn text-color-black"> ' .
                                @$match_data['t1'][0][0]['b2'] . '<br><span>' . $this->number_format_short(@$match_data['t1'][0][0]['bs2']) . '</span></a>
										</td>
										<td class="lightblue-bg3 spark ODDSBack text-center td_team1_back_0" data-team="team1">
											<a data-bettype="ODDS" data-team="team1" ' . $login_check . '  data-val="' .
                                @$match_data['t1'][0][0]['b1'] . '" data-cls="cyan-bg" class="back1btn text-color-black"> ' .
                                @$match_data['t1'][0][0]['b1'] . ' <br><span>' . $this->number_format_short(@$match_data['t1'][0][0]['bs1']) . '</span></a>
										</td>
										<td class="lightpink-bg3 sparkLay ODDSLay text-center td_team1_lay_0" data-team="team1">
											<a data-bettype="ODDS" data-team="team1" ' . $login_check . '  data-val="' .
                                @$match_data['t1'][0][0]['l1'] . '" data-cls="pink-bg" class="lay1btn text-color-black"> ' .
                                @$match_data['t1'][0][0]['l1'] . ' <br><span>' . $this->number_format_short(@$match_data['t1'][0][0]['ls1']) . '</span></a>
										</td>
										<td class="lightpink-bg4 sparkLay text-center ODDSLay td_team1_lay_1" data-team="team1">
											<a data-bettype="ODDS" data-team="team1" ' . $login_check . ' href="javascript:void(0)" data-val="' .
                                @$match_data['t1'][0][0]['l2'] . '" data-cls="pink-bg" class="lay1btn text-color-black"> ' .
                                @$match_data['t1'][0][0]['l2'] . ' <br><span>' . $this->number_format_short(@$match_data['t1'][0][0]['ls2']) . '</span></a>
										</td>
										<td class="lightpink-bg5 sparkLay text-center ODDSLay td_team1_lay_2" data-team="team1">
											<a data-bettype="ODDS" data-team="team1" ' . $login_check . '  data-val="' .
                                @$match_data['t1'][0][0]['l3'] . '" data-cls="pink-bg" class="lay1btn text-color-black"> ' .
                                @$match_data['t1'][0][0]['l3'] . ' <br><span>' . $this->number_format_short(@$match_data['t1'][0][0]['ls3']) . '</span></a>
										</td>
									</tr>
									';
                        }
                        else {
                            $html .= '<tr class="tr_team1">
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
                    }
                } else {

                    $html_chk .= '<tr class="fancy-suspend-tr team1_fancy 222222">
									<td></td>
									<td class="fancy-suspend-td" colspan="6">
										<div class="fancy-suspend black-bg-5 text-color-white"><span>' . @$match_data[0]['status'] . '</span></div>
									</td>

								</tr>
								<tr class="white-bg tr_team1">
										<td> <img src="' . asset('asset/front/img/bars.png') . '"> <b class="team1">' . ucfirst($team[0]) . ' </b>
											<div>
												<span class="lose " id="team1_bet_count_old"></span>
												<span class="towin text-color-green" style="display:none" id="team1_bet_count_new">0.00</span>
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
                //team 2
                if (@$match_data['t1'][0][0]['mstatus'] == 'OPEN' || @$match_data[0]['status'] == 'OPEN') {
                    if ($section == 2 || $section == 3) { //tennis || soccer
                        if (isset($match_data[0]['runners'][1])) {
                            $display = '';
                            $cls = '';
                            if ($team_draw_bet_total == '' && $team1_bet_total && $team2_bet_total == "")
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
										<td class="lightblue-bg4 spark opnForm text-center ODDSBack td_team2_back_2" data-team="team2">
											<a data-bettype="ODDS" data-team="team2" ' . $login_check . ' href="javascript:void(0)" data-cls="cyan-bg" data-val="' .
                                @$match_data[0]['runners'][1]['ex']['availableToBack'][2]['price'] . '" class="back1btn text-color-black">' .
                                @$match_data[0]['runners'][1]['ex']['availableToBack'][2]['price'] . ' <br><span>' .
                                $this->number_format_short(@$match_data[0]['runners'][1]['ex']['availableToBack'][2]['size']) . '</span></a></td>
										<td class="link(target, link) lightblue-bg5 text-center spark ODDSBack td_team2_back_1" data-team="team2">
											<a data-bettype="ODDS" data-team="team2" ' . $login_check . ' href="javascript:void(0)" data-cls="cyan-bg" data-val="' .
                                @$match_data[0]['runners'][1]['ex']['availableToBack'][1]['price'] . '" class="back1btn text-color-black"> ' .
                                @$match_data[0]['runners'][1]['ex']['availableToBack'][1]['price'] . ' <br><span>' .
                                $this->number_format_short(@$match_data[0]['runners'][1]['ex']['availableToBack'][1]['size']) . '</span>
											</a>
										</td>
										<td class="lightblue-bg3 spark ODDSBack text-center td_team2_back_0" data-team="team2">
											<a data-bettype="ODDS" data-team="team2" ' . $login_check . ' href="javascript:void(0)" data-val="' .
                                @$match_data[0]['runners'][1]['ex']['availableToBack'][0]['price'] . '" data-cls="cyan-bg" class="back1btn text-color-black"> ' .
                                @$match_data[0]['runners'][1]['ex']['availableToBack'][0]['price'] . ' <br><span>' .
                                $this->number_format_short(@$match_data[0]['runners'][1]['ex']['availableToBack'][0]['size']) . '</span>
											</a>
										</td>
										<td class="lightpink-bg3 sparkLay ODDSLay text-center td_team2_lay_0" data-team="team2">
											<a data-bettype="ODDS" data-team="team2" ' . $login_check . ' href="javascript:void(0)" data-val="' .
                                @$match_data[0]['runners'][1]['ex']['availableToLay'][0]['price'] . '" data-cls="pink-bg" class="lay1btn text-color-black"> ' .
                                @$match_data[0]['runners'][1]['ex']['availableToLay'][0]['price'] . ' <br><span>' .
                                $this->number_format_short(@$match_data[0]['runners'][1]['ex']['availableToLay'][0]['size']) . '</span>
											</a>
										</td>
										<td class="lightpink-bg4 sparkLay text-center ODDSLay td_team2_lay_1" data-team="team2">
											<a data-bettype="ODDS" data-team="team2" ' . $login_check . ' href="javascript:void(0)" data-val="' .
                                @$match_data[0]['runners'][1]['ex']['availableToLay'][1]['price'] . '" data-cls="pink-bg" class="lay1btn text-color-black"> ' .
                                @$match_data[0]['runners'][1]['ex']['availableToLay'][1]['price'] . ' <br><span>' .
                                $this->number_format_short(@$match_data[0]['runners'][1]['ex']['availableToLay'][1]['size']) . '</span>
											</a>
										</td>
										<td class="lightpink-bg5 sparkLay text-center ODDSLay td_team2_lay_2" data-team="team2">
											<a data-bettype="ODDS" data-team="team2" ' . $login_check . ' href="javascript:void(0)" data-val="' .
                                @$match_data[0]['runners'][1]['ex']['availableToLay'][2]['price'] . '" data-cls="pink-bg" class="lay1btn text-color-black"> ' .
                                @$match_data[0]['runners'][1]['ex']['availableToLay'][2]['price'] . ' <br><span>' .
                                $this->number_format_short(@$match_data[0]['runners'][1]['ex']['availableToLay'][2]['size']) . '</span></a>
										</td>
									</tr>';
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
                        if (isset($match_data['t1'][0][0]['b1'])) {
                            $display = '';
                            $cls = '';
                            if ($team_draw_bet_total == '' && $team1_bet_total && $team2_bet_total == "")
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
										<td class="lightblue-bg4 spark opnForm text-center ODDSBack td_team2_back_2" data-team="team2">
											<a data-bettype="ODDS" data-team="team2" ' . $login_check . ' href="javascript:void(0)" data-cls="cyan-bg" data-val="' .
                                @$match_data['t1'][0][1]['b3'] . '" class="back1btn text-color-black">' .
                                @$match_data['t1'][0][1]['b3'] . ' <br><span>' .
                                $this->number_format_short(@$match_data['t1'][0][1]['bs3']) . '</span></a></td>
										<td class="link(target, link) lightblue-bg5 text-center spark ODDSBack td_team2_back_1" data-team="team2">
											<a data-bettype="ODDS" data-team="team2" ' . $login_check . ' href="javascript:void(0)" data-cls="cyan-bg" data-val="' .
                                @$match_data['t1'][0][1]['b2'] . '" class="back1btn text-color-black"> ' .
                                @$match_data['t1'][0][1]['b2'] . ' <br><span>' .
                                $this->number_format_short(@$match_data['t1'][0][1]['bs2']) . '</span>
											</a>
										</td>
										<td class="lightblue-bg3 spark ODDSBack text-center td_team2_back_0" data-team="team2">
											<a data-bettype="ODDS" data-team="team2" ' . $login_check . ' href="javascript:void(0)" data-val="' .
                                @$match_data['t1'][0][1]['b1'] . '" data-cls="cyan-bg" class="back1btn text-color-black"> ' .
                                @$match_data['t1'][0][1]['b1'] . ' <br><span>' .
                                $this->number_format_short(@$match_data['t1'][0][1]['bs1']) . '</span>
											</a>
										</td>
										<td class="lightpink-bg3 sparkLay ODDSLay text-center td_team2_lay_0" data-team="team2">
											<a data-bettype="ODDS" data-team="team2" ' . $login_check . ' href="javascript:void(0)" data-val="' .
                                @$match_data['t1'][0][1]['l1'] . '" data-cls="pink-bg" class="lay1btn text-color-black"> ' .
                                @$match_data['t1'][0][1]['l1'] . ' <br><span>' .
                                $this->number_format_short(@$match_data['t1'][0][1]['ls1']) . '</span>
											</a>
										</td>
										<td class="lightpink-bg4 sparkLay text-center ODDSLay td_team2_lay_1" data-team="team2">
											<a data-bettype="ODDS" data-team="team2" ' . $login_check . ' href="javascript:void(0)" data-val="' .
                                @$match_data['t1'][0][1]['l2'] . '" data-cls="pink-bg" class="lay1btn text-color-black"> ' .
                                @$match_data['t1'][0][1]['l2'] . ' <br><span>' .
                                $this->number_format_short(@$match_data['t1'][0][1]['ls2']) . '</span>
											</a>
										</td>
										<td class="lightpink-bg5 sparkLay text-center ODDSLay td_team2_lay_2" data-team="team2">
											<a data-bettype="ODDS" data-team="team2" ' . $login_check . ' href="javascript:void(0)" data-val="' .
                                @$match_data['t1'][0][1]['l3'] . '" data-cls="pink-bg" class="lay1btn text-color-black"> ' .
                                @$match_data['t1'][0][1]['l3'] . ' <br><span>' .
                                $this->number_format_short(@$match_data['t1'][0][1]['ls3']) . '</span></a>
										</td>
									</tr> ';
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
                    }
                }
                else {
                    $html_chk .= '
								<tr class="fancy-suspend-tr team1_fancy 33333">
									<td></td>
									<td class="fancy-suspend-td" colspan="6">
										<div class="fancy-suspend black-bg-5 text-color-white"><span>' . @$match_data[0]['status'] . '</span></div>
									</td>
								</tr>
								<tr class="white-bg tr_team2">
										<td> <img src="' . asset('asset/front/img/bars.png') . '"> <b class="team1">' . ucfirst($team[1]) . ' </b>
											<div>
												<span class="lose " id="team1_bet_count_old"></span>
												<span class="towin text-color-green" style="display:none" id="team1_bet_count_new">0.00</span>
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

                //team3
                if ($section == 3)
                { //soccer
                    if (isset($match_data[0]['runners'][2])) {
                        $display = '';
                        $cls = '';
                        if ($team_draw_bet_total == '' && $team1_bet_total && $team2_bet_total == "") {
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
								<td class="lightblue-bg4  spark ODDSBack text-center td_team3_back_2" data-team="team3">
									<a data-bettype="ODDS" data-team="team3" ' . $login_check . ' data-val="' .
                            @$match_data[0]['runners'][2]['ex']['availableToBack'][2]['price'] . '" data-cls="cyan-bg" class="back1btn text-color-black">
										' .
                            @$match_data[0]['runners'][2]['ex']['availableToBack'][2]['price'] . ' <br><span>' .
                            $this->number_format_short(@$match_data[0]['runners'][2]['ex']['availableToBack'][2]['size']) . '</span>
									</a>
								</td>
								<td class="link(target, link) lightblue-bg5 text-center spark ODDSBack td_team3_back_1" data-team="team3">
									<a data-bettype="ODDS" data-team="team3" ' . $login_check . '  data-cls="cyan-bg" data-val="' . @$match_data[0]['runners'][2]['ex']['availableToBack'][1]['price'] . '" class="text-color-black back1btn"> ' .
                            $match_data[0]['runners'][2]['ex']['availableToBack'][1]['price'] . ' <br><span>' .
                            $this->number_format_short(@$match_data[0]['runners'][2]['ex']['availableToBack'][1]['size']) . '</span>
									</a>
								</td>
								<td class="lightblue-bg3 spark ODDSBack text-center td_team3_back_0" data-team="team3">
									<a data-bettype="ODDS" data-team="team3" ' . $login_check . ' data-val="' .
                            @$match_data[0]['runners'][2]['ex']['availableToBack'][0]['price'] . '" data-cls="cyan-bg" class="back1btn text-color-black"> ' .
                            @$match_data[0]['runners'][2]['ex']['availableToBack'][0]['price'] . ' <br><span>' .
                            $this->number_format_short(@$match_data[0]['runners'][2]['ex']['availableToBack'][0]['size']) . '</span>
									</a>
								</td>
								<td class="lightpink-bg3 sparkLay ODDSLay text-center td_team3_lay_0" data-team="team3">
									<a data-bettype="ODDS" data-team="team3" ' . $login_check . '  data-val="' .
                            @$match_data[0]['runners'][2]['ex']['availableToLay'][0]['price'] . '" data-cls="pink-bg" class="lay1btn text-color-black"> ' .
                            @$match_data[0]['runners'][2]['ex']['availableToLay'][0]['price'] . ' <br><span>' .
                            $this->number_format_short(@$match_data[0]['runners'][2]['ex']['availableToLay'][0]['size']) . '</span>
									</a>
								</td>
								<td class="lightpink-bg4 sparkLay text-center ODDSLay td_team3_lay_1" data-team="team3">
									<a data-bettype="ODDS" data-team="team3" ' . $login_check . '  data-val="' .
                            @$match_data[0]['runners'][2]['ex']['availableToLay'][1]['price'] . '" data-cls="pink-bg" class="lay1btn text-color-black"> ' .
                            @$match_data[0]['runners'][2]['ex']['availableToLay'][1]['price'] . ' <br><span>' .
                            $this->number_format_short(@$match_data[0]['runners'][2]['ex']['availableToLay'][1]['size']) . '</span>
									</a>
								</td>
								<td class="lightpink-bg5 sparkLay text-center ODDSLay td_team3_lay_2" data-team="team3">
									<a data-bettype="ODDS" data-team="team3" ' . $login_check . '  data-val="' .
                            @$match_data[0]['runners'][2]['ex']['availableToLay'][2]['price'] . '" data-cls="pink-bg" class="lay1btn text-color-black"> ' .
                            @$match_data[0]['runners'][2]['ex']['availableToLay'][2]['price'] . ' <br><span>' .
                            $this->number_format_short(@$match_data[0]['runners'][2]['ex']['availableToLay'][2]['size']) . '</span>
									</a>
								</td>
							</tr>
							';
                    }
                    else {
                        $html .= '<tr class="white-bg tr_team3">
									<td> <img src="' . asset('asset/front/img/bars.png') . '"> <b class="team3">The Draw</b> </td>
									<td class="light-blue-bg-2 td_team3_back_2"><a class="back1btn">
										--</span></a></td>
									<td class="link(target, link)ght-blue-bg-3 td_team3_back_1"><a class="back1btn">--</span></a></td>
									<td class="cyan-bg td_team3_back_0"><a class="back1btn">--</a></td>
									<td class="pink-bg td_team3_lay_0"><a class="lay1btn">--</td>
									<td class="light-pink-bg-2 td_team3_lay_1"><a class="lay1btn">--</td>
									<td class="light-pink-bg-3 td_team3_lay_2"><a class="lay1btn">--</td>
								</tr>';
                    }
                }
                else {
                    if (!empty(@$match_data['t1'][0][2])) {
                        if (@$match_data['t1'][0][2]['mstatus'] == 'OPEN') {
                            if ($section == 4) { //cricket
                                if (isset($match_data['t1'][0][2]['b1'])) {
                                    $display = '';
                                    $cls = '';
                                    if ($team_draw_bet_total == '' && $team1_bet_total && $team2_bet_total == "") {
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

                                    $html_chk .= '<tr class="white-bg tr_team3 22222">
											<td> <img src="' . asset('asset/front/img/bars.png') . '"> <b class="team3"> The Draw </b>
												<div>
													<span class="lose ' . $cls . '" ' . $display . ' id="draw_bet_count_old">(<span id="draw_total">' . round($team_draw_bet_total, 2) . '</span>)</span>
													<span class="tolose text-color-red" style="display:none" id="draw_bet_count_new">0.00</span>
												</div>
											</td>
											<td class="lightblue-bg4  spark ODDSBack text-center td_team3_back_2" data-team="team3">
												<a data-bettype="ODDS" data-team="team3" ' . $login_check . ' data-val="' .
                                        @$match_data['t1'][0][2]['b3'] . '" data-cls="cyan-bg" class="back1btn text-color-black">
													' .
                                        @$match_data['t1'][0][2]['b3'] . ' <br><span>' .
                                        $this->number_format_short(@$match_data['t1'][0][2]['bs3']) . '</span>
												</a>
											</td>
											<td class="link(target, link) lightblue-bg5 text-center spark ODDSBack td_team3_back_1" data-team="team3">
												<a data-bettype="ODDS" data-team="team3" ' . $login_check . '  data-cls="cyan-bg" data-val="' . @$match_data['t1'][0][2]['b2'] . '" class="text-color-black back1btn"> ' .
                                        $match_data['t1'][0][2]['b2'] . ' <br><span>' .
                                        $this->number_format_short(@$match_data['t1'][0][2]['bs2']) . '</span>
												</a>
											</td>
											<td class="lightblue-bg3 spark ODDSBack text-center td_team3_back_0" data-team="team3">
												<a data-bettype="ODDS" data-team="team3" ' . $login_check . ' data-val="' .
                                        @$match_data['t1'][0][2]['b1'] . '" data-cls="cyan-bg" class="back1btn text-color-black"> ' .
                                        @$match_data['t1'][0][2]['b1'] . ' <br><span>' .
                                        $this->number_format_short(@$match_data['t1'][0][2]['bs1']) . '</span>
												</a>
											</td>
											<td class="lightpink-bg3 sparkLay ODDSLay text-center td_team3_lay_0" data-team="team3">
												<a data-bettype="ODDS" data-team="team3" ' . $login_check . '  data-val="' .
                                        @$match_data['t1'][0][2]['l1'] . '" data-cls="pink-bg" class="lay1btn text-color-black"> ' .
                                        @$match_data['t1'][0][2]['l1'] . ' <br><span>' .
                                        $this->number_format_short(@$match_data['t1'][0][2]['ls1']) . '</span>
												</a>
											</td>
											<td class="lightpink-bg4 sparkLay text-center ODDSLay td_team3_lay_1" data-team="team3">
												<a data-bettype="ODDS" data-team="team3" ' . $login_check . '  data-val="' .
                                        @$match_data['t1'][0][2]['l2'] . '" data-cls="pink-bg" class="lay1btn text-color-black"> ' .
                                        @$match_data['t1'][0][2]['l2'] . ' <br><span>' .
                                        $this->number_format_short(@$match_data['t1'][0][2]['ls2']) . '</span>
												</a>
											</td>
											<td class="lightpink-bg5 sparkLay text-center ODDSLay td_team3_lay_2" data-team="team3">
												<a data-bettype="ODDS" data-team="team3" ' . $login_check . '  data-val="' .
                                        @$match_data['t1'][0][2]['l3'] . '" data-cls="pink-bg" class="lay1btn text-color-black"> ' .
                                        @$match_data['t1'][0][2]['l3'] . ' <br><span>' .
                                        $this->number_format_short(@$match_data['t1'][0][2]['ls3']) . '</span>
												</a>
											</td>
										</tr>
										';
                                } else {
                                    $html .= '<tr class="white-bg tr_team3">
												<td> <img src="' . asset('asset/front/img/bars.png') . '"> <b class="team3">The Draw</b> </td>
												<td class="light-blue-bg-2 td_team3_back_2"><a class="back1btn">
													--</span></a></td>
												<td class="link(target, link)ght-blue-bg-3 td_team3_back_1"><a class="back1btn">--</span></a></td>
												<td class="cyan-bg td_team3_back_0"><a class="back1btn">--</a></td>
												<td class="pink-bg td_team3_lay_0"><a class="lay1btn">--</td>
												<td class="light-pink-bg-2 td_team3_lay_1"><a class="lay1btn">--</td>
												<td class="light-pink-bg-3 td_team3_lay_2"><a class="lay1btn">--</td>
											</tr>';
                                }
                            }
                        } else {
                            if ($section == 4) { //cricket
                                $html_chk .= '
										<tr class="fancy-suspend-tr team3_fancy 666666666666">
											<td></td>
											<td class="fancy-suspend-td" colspan="6">
												<div class="fancy-suspend black-bg-5 text-color-white"><span>' . @$match_data['t1'][0][2]['mstatus'] . '</span></div>
											</td>
										</tr>
										<tr class="white-bg tr_team3">
												<td> <img src="' . asset('asset/front/img/bars.png') . '"> <b class="team3">The Draw</b>
													<div>
														<span class="lose " id="team3_bet_count_old"></span>
														<span class="towin text-color-green" style="display:none" id="team3_bet_count_new">0.00</span>
													</div>
												</td>
												<td class="light-blue-bg-2 spark opnForm ODDSBack td_team3_back_2" ><a class="back1btn text-color-black">--</span></a></td>
												<td class="link(target, link)ght-blue-bg-3 spark ODDSBack td_team3_back_1" data-team="team1"><a  class="back1btn text-color-black"> --<br><span>--</span></a></td>
												<td class="cyan-bg spark ODDSBack td_team3_back_0" ><a  class="back1btn text-color-black"> -- <br><span>--</span></a></td>
												<td class="pink-bg sparkLay ODDSLay td_team3_lay_0" ><a  data-cls="pink-bg" class="lay1btn text-color-black"> --</span></a></td>
												<td class="light-pink-bg-2 sparkLay ODDSLay td_team3_lay_1" ><a data-cls="pink-bg" class="lay1btn text-color-black"> -- <br><span>--</span></a></td>
												<td class="light-pink-bg-3 sparkLay ODDSLay td_team3_back_2" ><a  data-cls="pink-bg" class="lay1btn text-color-black"> -- <br><span>--</span></a></td>
										</tr>';
                            }
                        }
                    }
                }
            } // end suspended if
            $html .= $html_chk;
            $html .= '</table>';

            if($section == 4) {
                $matchname = $request->matchname;
                $match_b = $request->match_b;
                $match_f = $request->match_f;
                $team_name = explode(" v ", strtolower($matchname));
                $team1_name = $team_name[0];
                if ($team_name[1])
                    $team2_name = $team_name[1];
                else
                    $team2_name = '';

                $resp = $this->risk_management_matchCallForFancyNBM($match_data, $matchId, $event_id, $loginUser, $website, $all_child, $match_f, $match_b, $team1_name, $team2_name, $sport);
            }else{
                $resp = ['boomaker'=>'','fancy'=>''];
            }

            return response()->json([
                'odds' => $html,
                'boomaker' => $resp['boomaker'],
                'fancy' => $resp['fancy']
            ]);
        }
        else {
            return 'No data found.';
        }
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

    public function matchDetailCall($eventId, Request $request)
    {
        $matchtype = $request->matchtype;
        $matchId = $request->matchid;
        $matchname = $request->matchname;
        $match_data = app('App\Http\Controllers\RestApi')->DetailCall($eventId, $matchId, $matchtype);
        $html = '';
        $html .= '';

        if ($matchtype == 1) {
            $b[] = array();
            $i = 0;
            $l[] = array();
            $j = 0;
            $nat[] = array();
            $k = 0;
            $bsr[] = array();
            $is = 0;
            $lsr[] = array();
            $js = 0;
            foreach ($match_data as $mngr) {
                if (is_array($mngr) || is_object($mngr)) {
                    foreach ($mngr as $key => $value) {
                        if (is_array($value) || is_object($value)) {
                            foreach ($value as $key1 => $value1) {
                                if ($key1 == 'b1' || $key1 == 'b2' || $key1 == 'b3') {
                                    $b[$i] = $value1;
                                    $i++;
                                }
                                if ($key1 == 'l1' || $key1 == 'l2' || $key1 == 'l3') {
                                    $l[$j] = $value1;
                                    $j++;
                                }
                                if ($key1 == 'nat') {
                                    $nat[$k] = $value1;
                                    $k++;
                                }

                                if ($key1 == 'bs1' || $key1 == 'bs2' || $key1 == 'bs3') {
                                    $bsr[$is] = $value1;
                                    $is++;
                                }

                                if ($key1 == 'ls1' || $key1 == 'ls2' || $key1 == 'ls3') {
                                    $lsr[$js] = $value1;
                                    $js++;
                                }
                            }
                        }
                    }
                }
            }

            if (!empty($b)) {
                $bl = array();
                foreach ($b as $bs) {
                    $bl[] = $bs;
                }
                $ll = array();
                foreach ($l as $bs) {
                    $ll[] = $bs;
                }
                $nat_val = array();
                foreach ($nat as $bs) {
                    $nat_val[] = $bs;
                }
                $bsl = array();
                foreach ($bsr as $bsc) {
                    $bsl[] = $bsc;
                }
                $lsl = array();
                foreach ($lsr as $lsc) {
                    $lsl[] = $lsc;
                }
                $html_chk = '';
                if (count($bl) > 5 && $bl[8] != '' && $bl[8] != '0.00') {
                    $html_chk .= '
					<tr class="rf_tr">
	                    <td class="text-left">
	                        <b>' . $nat_val[2] . ' <span class="text-color-green">1,804.11</span></b>
	                    </td>
	                    <td class="back1 text-center">
	                        <b>' . $bl[8] . '</b> <span>' . $bsl[8] . '</span>
	                    </td>
	                    <td class="back1 text-center">
	                        <b>' . $bl[7] . '</b> <span>' . $bsl[7] . '</span>
	                    </td>
	                    <td class="back1 cyan-bg text-center">
	                        <b>' . $bl[6] . '</b> <span>' . $bsl[6] . '</span>
	                    </td>
	                    <td class="lay1 pink-bg text-center">
	                        <b>' . $ll[6] . '</b> <span>' . $lsl[6] . '</span>
	                    </td>
	                    <td class="lay1 text-center">
	                        <b>' . $ll[7] . '</b> <span>' . $lsl[7] . '</span>
	                    </td>
	                    <td class="lay1 text-center">
	                        <b>' . $ll[8] . '</b> <span>' . $lsl[8] . '</span>
	                    </td>
					</tr>';
                }

                $html .= '<tr class="rf_tr">
                    <td class="text-left">
                        <b>' . $nat_val[0] . ' <span class="text-color-green">1,804.11</span></b>
                    </td>
                    <td class="back1 text-center">
                        <b>' . $bl[2] . '</b> <span>' . $bsl[2] . '</span>
                    </td>
                    <td class="back1 text-center">
                        <b>' . $bl[1] . '</b> <span>' . $bsl[1] . '</span>
                    </td>
                    <td class="back1 cyan-bg text-center">
                        <b>' . $bl[0] . '</b> <span>' . $bsl[0] . '</span>
                    </td>
                    <td class="lay1 pink-bg text-center">
                        <b>' . $ll[0] . '</b> <span>' . $lsl[0] . '</span>
                    </td>
                    <td class="lay1 text-center">
                        <b>' . $ll[1] . '</b> <span>' . $lsl[1] . '</span>
                    </td>
                    <td class="lay1 text-center">
                        <b>' . $ll[2] . '</b> <span>' . $lsl[2] . '</span>
                    </td>
					</tr>
					<tr>
					<td class="text-left">
                        <b>' . $nat_val[1] . ' <span class="text-color-green">1,804.11</span></b>
                    </td>
                    <td class="back1 text-center">
                        <b>' . $bl[5] . ' </b> <span>' . $bsl[5] . '</span>
                    </td>
                    <td class="back1 text-center">
                        <b>' . $bl[4] . ' </b> <span>' . $bsl[4] . '</span>
                    </td>
                    <td class="back1 cyan-bg text-center">
                        <b>' . $bl[3] . '</b> <span>' . $bsl[3] . '</span>
                    </td>
                    <td class="lay1 pink-bg text-center">
                        <b>' . $ll[3] . '</b> <span>' . $lsl[3] . '</span>
                    </td>
                    <td class="lay1 text-center">
                        <b>' . $ll[4] . '</b> <span>' . $lsl[4] . '</span>
                    </td>
                    <td class="lay1 text-center">
                        <b>' . $ll[5] . '</b> <span>' . $lsl[5] . '</span>
                    </td>
				</tr>';
                $html .= $html_chk;
            } else {
                $split = explode(" v ", $matchname);
                if (@count($split) > 0) {
                    $teamone = $split[0];
                    $teamtwo = $split[1];
                } else {
                    $teamone = '';
                    $teamtwo = '';
                }
                $html .= '<tr class="rf_tr">
                    <td class="text-left">
                        <b>' . $teamone . '<span class="text-color-green">--</span></b>
                    </td>
                    <td class="back1 text-center">
                        <b>--</b> <span>--</span>
                    </td>

                    <td class="back1 text-center">
                        <b>--</b> <span>--</span>
                    </td>

                    <td class="back1 cyan-bg text-center">
                        <b>--</b> <span>--</span>
                    </td>

                    <td class="lay1 pink-bg text-center">
                        <b>--</b> <span>--</span>
                    </td>

                    <td class="lay1 text-center">
                        <b>--</b> <span>--</span>
                    </td>

                    <td class="lay1 text-center">
                        <b>--</b> <span>--</span>
                    </td>
					</tr>

					<tr>
					<td class="text-left">
                        <b>' . $teamtwo . '<span class="text-color-green">--</span></b>
                    </td>
                    <td class="back1 text-center">
                        <b>-- </b> <span>--</span>
                    </td>
                    <td class="back1 text-center">
                        <b>-- </b> <span>--</span>
                    </td>
                    <td class="back1 cyan-bg text-center">
                        <b>--</b> <span>--</span>
                    </td>
                    <td class="lay1 pink-bg text-center">
                        <b>--</b> <span>--</span>
                    </td>
                    <td class="lay1 text-center">
                        <b>--</b> <span>--</span>
                    </td>
                    <td class="lay1 text-center">
                        <b>--</b> <span>--</span>
                    </td>
				</tr>';
            }
            $html .= '</table>';
            return $html;
        }
        else {
            $split = explode(" v ", $matchname);
            if (@count($split) > 0) {
                $teamone = $split[0];
                $teamtwo = $split[1];
            } else {
                $teamone = '';
                $teamtwo = '';
            }

            $html_chk = '';

            if (@$match_data[0]['runners'][2]['ex']['availableToBack'][0]['price'] != '') {
                $html_chk .= '<tr class="rf_tr">
                        <td class="text-left">
                            <b>The Draw <span class="text-color-red">-2,597.70</span></b>
                        </td>
                        <td class="back1 text-center">
                            <b>' .
                    $match_data[0]['runners'][2]['ex']['availableToBack'][2]['price'] . '</b> <span>' . $match_data[0]['runners'][2]['ex']['availableToBack'][2]['size'] . '</span>
                        </td>
                        <td class="back1 text-center">
                            <b>' .
                    $match_data[0]['runners'][2]['ex']['availableToBack'][1]['price'] . '</b> <span>' . $match_data[0]['runners'][2]['ex']['availableToBack'][1]['size'] . '</span>
                        </td>
                        <td class="back1 cyan-bg text-center">
                            <b>' . $match_data[0]['runners'][2]['ex']['availableToBack'][0]['price'] . '</b> <span>' . $match_data[0]['runners'][2]['ex']['availableToBack'][0]['size'] . '</span>
                        </td>
                        <td class="lay1 pink-bg text-center">
                            <b>' . $match_data[0]['runners'][2]['ex']['availableToLay'][0]['price'] . '</b> <span>' . $match_data[0]['runners'][2]['ex']['availableToLay'][0]['size'] . '</span>
                        </td>

                        <td class="lay1 text-center">
                            <b>' . $match_data[0]['runners'][2]['ex']['availableToLay'][1]['price'] . '</b>  <span>' . $match_data[0]['runners'][2]['ex']['availableToLay'][1]['size'] . '</span>
                        </td>
                        <td class="lay1 text-center">
                            <b>' . $match_data[0]['runners'][2]['ex']['availableToLay'][2]['price'] . '</b> <span>' . $match_data[0]['runners'][2]['ex']['availableToLay'][2]['size'] . '</span>
                        </td>
                    </tr>
				';
            }

            $html .= '<tr class="rf_tr">
	                    <td class="text-left">
	                        <b>' . $teamone . ' <span class="text-color-red">-2,597.70</span></b>
	                    </td>
	                    <td class="back1 text-center">
	                        <b>' .
                $match_data[0]['runners'][0]['ex']['availableToBack'][2]['price'] . '</b> <span>' . $match_data[0]['runners'][0]['ex']['availableToBack'][2]['size'] . '</span>
	                    </td>
	                    <td class="back1 text-center">
	                        <b>' .
                $match_data[0]['runners'][0]['ex']['availableToBack'][1]['price'] . '</b> <span>' . $match_data[0]['runners'][0]['ex']['availableToBack'][1]['size'] . '</span>
	                    </td>
	                    <td class="back1 cyan-bg text-center">
	                        <b>' . $match_data[0]['runners'][0]['ex']['availableToBack'][0]['price'] . '</b> <span>' . $match_data[0]['runners'][0]['ex']['availableToBack'][0]['size'] . '</span>
	                    </td>

	                    <td class="lay1 pink-bg text-center">
	                        <b>' . $match_data[0]['runners'][0]['ex']['availableToLay'][0]['price'] . '</b> <span>' . $match_data[0]['runners'][0]['ex']['availableToLay'][0]['size'] . '</span>
	                    </td>

	                    <td class="lay1 text-center">
	                        <b>' . $match_data[0]['runners'][0]['ex']['availableToLay'][1]['price'] . '</b>  <span>' . $match_data[0]['runners'][0]['ex']['availableToLay'][1]['size'] . '</span>
	                    </td>

	                    <td class="lay1 text-center">
	                        <b>' . $match_data[0]['runners'][0]['ex']['availableToLay'][2]['price'] . '</b> <span>' . $match_data[0]['runners'][0]['ex']['availableToLay'][2]['size'] . '</span>
	                    </td>
	                </tr>
					<tr class="rf_tr">
	                    <td class="text-left">
	                        <b>' . $teamtwo . '<span class="text-color-red">-2,597.70</span></b>
	                    </td>

	                    <td class="back1 text-center">
	                        <b>' .
                $match_data[0]['runners'][1]['ex']['availableToBack'][2]['price'] . '</b> <span>' . $match_data[0]['runners'][1]['ex']['availableToBack'][2]['size'] . '</span>

	                    </td>
	                    <td class="back1 text-center">
	                        <b>' .
                $match_data[0]['runners'][1]['ex']['availableToBack'][1]['price'] . '</b> <span>' . $match_data[0]['runners'][1]['ex']['availableToBack'][1]['size'] . '</span>
	                    </td>
	                    <td class="back1 cyan-bg text-center">
	                        <b>' . $match_data[0]['runners'][1]['ex']['availableToBack'][0]['price'] . '</b> <span>' . $match_data[0]['runners'][1]['ex']['availableToBack'][0]['size'] . '</span>
	                    </td>
	                    <td class="lay1 pink-bg text-center">
	                        <b>' . $match_data[0]['runners'][1]['ex']['availableToLay'][0]['price'] . '</b> <span>' . $match_data[0]['runners'][1]['ex']['availableToLay'][0]['size'] . '</span>
	                    </td>
	                    <td class="lay1 text-center">
	                        <b>' . $match_data[0]['runners'][1]['ex']['availableToLay'][1]['price'] . '</b>  <span>' . $match_data[0]['runners'][1]['ex']['availableToLay'][1]['size'] . '</span>
	                    </td>
	                    <td class="lay1 text-center">
	                        <b>' . $match_data[0]['runners'][1]['ex']['availableToLay'][2]['price'] . '</b> <span>' . $match_data[0]['runners'][1]['ex']['availableToLay'][2]['size'] . '</span>
	                    </td>
	                </tr>';

            $html .= $html_chk;
            $html .= '</table>';
            return $html;
        }
    }

    public function player_banking()
    {
        $getuser = Auth::user();
        $settings = "";
        $balance = 0;

        if ($getuser->agent_level == 'COM') {
            $settings = setting::latest('id')->first();
            $balance = $settings->balance;
        } else {
            $settings = CreditReference::where('player_id', $getuser->id)->first();
            $balance = $settings['available_balance_for_D_W'];
        }

        $agent = User::where('parentid', $getuser->id)->where('agent_level', '!=', 'PL')->get();
        $player = User::where('parentid', $getuser->id)->where('agent_level', 'PL')->orderBy('user_name')->get();
        return view('backpanel/player-banking', compact('player', 'settings', 'balance'));
    }

    public function agent_banking()
    {
        $getuser = Auth::user();
        $settings = "";
        $balance = 0;
        if ($getuser->agent_level == 'COM') {
            $settings = setting::latest('id')->first();
            $balance = $settings->balance;
        } else {
            $settings = CreditReference::where('player_id', $getuser->id)->first();
            $balance = $settings['available_balance_for_D_W'];
        }
        $agent = User::where('parentid', $getuser->id)->whereNotIn('agent_level', ['PL', 'SL'])->orderBy('user_name')->get();
//        $player = User::where('parentid', $getuser->id)->where('agent_level', 'PL')->get();
        return view('backpanel/agent-banking', compact('agent', 'settings', 'balance'));
    }

    public function addPlayerBanking(Request $request)
    {
        $apass = $request->adminpassword;
        $settings = "";
        $balance = 0;
        $remark = '';
        $getuser = Auth::user();
        if ($getuser->agent_level == 'COM') {
            $settings = setting::latest('id')->first();
            $balance = $settings->balance;
        } else {
            $settings = CreditReference::where('player_id', $getuser->id)->first();
            $balance = $settings['available_balance_for_D_W'];
        }


        $adm_password = $getuser->password;
        $admin_balance = $balance;

        $new_balance = 0;

        $agent = User::where('parentid', $getuser->id)->where('agent_level', '!=', 'PL')->get();
        $player = User::where('parentid', $getuser->id)->where('agent_level', 'PL')->orderBy('user_name')->get();
        if (Hash::check($apass, $adm_password)) {
            //check admin balance
            $i = 0;
            $total_deposit_amount = 0;
            $total_withdraw_amount = 0;
            foreach ($player as $play) {
                $btype = '';
                $amount = 0;
                $rem_balance = 0;
                $credit_amount = 0;
                $credit_balance = 0;
                $available_balance = 0;
                if ($request->txtamount[$i] != '' && $request->txtamount[$i] > 0) {
                    $credit_data = CreditReference::where('player_id', $play->id)->select('*')->first();
                    $credit = 0;
                    if ($credit_data['credit'] != '') {
                        $credit = $credit_data['credit'];
                    }
                    $balance = $credit_data['remain_bal'];
                    $available_balance = $credit_data['available_balance_for_D_W'];
                    $amount = $request->txtamount[$i];
                    if ($request->player_deposite[$i] != '' && $request->player_deposite[$i] == 'D') {
                        $btype = $request->player_deposite[$i];
                    } else if ($request->player_withdraw[$i] != '' && $request->player_withdraw[$i] == 'W') {
                        $btype = $request->player_withdraw[$i];
                    }
                    if ($btype == 'W') {
                        $total_withdraw_amount = $total_withdraw_amount + $amount;
                    } else {
                        $total_deposit_amount = $total_deposit_amount + $amount;
                    }
                }
                $i++;
            }
            //check balance
            $admin_balance_check = $admin_balance + $total_withdraw_amount;
            $admin_balance_check = $admin_balance - $total_deposit_amount;

            if ($admin_balance_check < 0) {
                return redirect()->route('backpanel/player-banking')->with('error', 'Player balance update failed!');
                exit;
            } else {
                $i = 0;
                foreach ($player as $play) {
                    $btype = '';
                    $amount = 0;
                    $rem_balance = 0;
                    $credit_amount = 0;
                    $credit_balance = 0;
                    $remark = '';
                    $available_balance = 0;
                    $av_balance = 0;

                    if ($request->txtamount[$i] != '' && $request->txtamount[$i] > 0) {
                        $remark = $request->remark[$i];
                        $credit_data = CreditReference::where('player_id', $play->id)->select('*')->first();
                        $credit = 0;
                        if ($credit_data['credit'] != '') {
                            $credit = $credit_data['credit'];
                        }
                        $balance = $credit_data['remain_bal'];
                        $available_balance = $credit_data['available_balance_for_D_W'];
                        $amount = $request->txtamount[$i];
                        if ($request->player_deposite[$i] != '' && $request->player_deposite[$i] == 'D') {
                            $btype = $request->player_deposite[$i];
                        } else if ($request->player_withdraw[$i] != '' && $request->player_withdraw[$i] == 'W') {
                            $btype = $request->player_withdraw[$i];
                        }
                        if ($btype == 'W') {
                            if ($available_balance < $amount) {
                                return redirect()->route('backpanel/agent-banking')->with('error', "Amount can not be more than Available D/W!");
                                exit;
                            }
                            $rem_balance = $balance - $amount;

                            // echo 'balance'.$balance ."-". $amount;;
                            // echo "<br>";
                            // echo "rem balance".$rem_balance;
                            // echo "<br>";
                            // echo "<br>";

                            $new_balance = $new_balance - $amount;

                            // echo 'amount'.$new_balance ."-". $amount;
                            // echo "<br>";
                            // echo "new balance".$new_balance;
                            // echo "<br>";
                            // echo "<br>";

                            $getuser = Auth::user();
                            $id = $getuser->id;
                            $player_new_balance = $available_balance - $new_balance;

                            $player_new_balance = $available_balance + $new_balance;
                            // echo 'available_balance'.$available_balance ."-". $new_balance;
                            // echo "<br>";
                            // echo "player balance".$player_new_balance;

                            // echo "available_balance". $available_balance;

                            $av_balance = $available_balance - $amount;

                            //exit;
                            UserDeposit::create([
                                'balanceType' => 'WITHDRAW',
                                'parent_id' => $id,
                                'child_id' => $play->id,
                                'amount' => $amount,
                                'extra' => $remark,
                                'balance' => $av_balance,
                            ]);

                            // Jitendra     :: 07/02/2022
//                            if ($website->enable_partnership == 1)
                            {
                                UsersAccount::create([
                                    'user_id' => $play->id,
                                    'from_user_id' => $getuser->id,
                                    'to_user_id' => $play->id,
                                    'debit_amount' => $amount,
                                    'balance' => $available_balance,
                                    'closing_balance' => $available_balance - $amount,
                                    'remark' => $remark,
                                ]);

                                UsersAccount::create([
                                    'user_id' => $getuser->id,
                                    'from_user_id' => $getuser->id,
                                    'to_user_id' => $play->id,
                                    'credit_amount' => $amount,
                                    'balance' => $admin_balance,
                                    'closing_balance' => $admin_balance + $amount,
                                    'remark' => $remark,
                                ]);
                            }

                        }
                        else {

                            $rem_balance = $balance + $amount;
                            // echo 'balance'.$balance ."+". $amount;;
                            // echo "<br>";
                            // echo "rem balance".$rem_balance;
                            // echo "<br>";
                            // echo "<br>";

                            // echo 'amount'.$new_balance ."+". $amount;
                            // echo "<br>";
                            $new_balance = $new_balance + $amount;

                            // echo "new balance".$new_balance;
                            // echo "<br>";
                            // echo "<br>";

                            $player_new_balance = $available_balance + $new_balance;
                            // echo 'available_balance'.$available_balance ."+". $new_balance;
                            // echo "<br>";
                            // echo "player balance".$player_new_balance;

                            // echo "available_balance". $available_balance;

                            $av_balance = $available_balance + $amount;

                            //exit;

                            $getuser = Auth::user();
                            $id = $getuser->id;
                            UserDeposit::create([
                                'balanceType' => 'DEPOSIT',
                                'parent_id' => $id,
                                'child_id' => $play->id,
                                'amount' => $amount,
                                'extra' => $remark,
                                'balance' => $av_balance,
                            ]);


                            // Jitendra     :: 07/02/2022
//                            if ($website->enable_partnership == 1)
                            {

                                UsersAccount::create([
                                    'user_id' => $play->id,
                                    'from_user_id' => $getuser->id,
                                    'to_user_id' => $play->id,
                                    'credit_amount' => $amount,
                                    'balance' => $available_balance,
                                    'closing_balance' => $available_balance + $amount,
                                    'remark' => $remark,
                                ]);

                                UsersAccount::create([
                                    'user_id' => $getuser->id,
                                    'from_user_id' => $getuser->id,
                                    'to_user_id' => $play->id,
                                    'debit_amount' => $amount,
                                    'balance' => $admin_balance,
                                    'closing_balance' => $admin_balance - $amount,
                                    'remark' => $remark,
                                ]);
                            }
                        }
                        $refid = $request->creditref[$i];
                        $play_client = CreditReference::find($refid);
                        $play_client->remain_bal = $rem_balance;
                        $play_client->available_balance_for_D_W = $av_balance;
                        $play_client->update();
                    }

                    if ($request->creditamount[$i] != '' && $request->creditamount[$i] > 0) {
                        $remark = $request->remark[$i];
                        $credit_data = CreditReference::where('player_id', $play->id)->select('*')->first();
                        $credit = 0;
                        if ($credit_data['credit'] != '') {
                            $credit = $credit_data['credit'];
                        }
                        $credit_amount = $request->creditamount[$i];
                        $credit_balance = $credit_amount;
                        $refid = $request->creditref[$i];
                        $play_clientx = CreditReference::find($refid);
                        $play_clientx->credit = $credit_balance;
                        $play_clientx->update();
                    }
                    $i++;
                }
                //$settingData->balance = $admin_balance-$new_balance;
                $settings = "";
                $balance = 0;
                $getuser = Auth::user();
                if ($getuser->agent_level == 'COM') {
                    $settingData = setting::latest('id')->first();
                    $balance = $settingData->balance;
                    $settingData->balance = $admin_balance - $new_balance;
                    $settingData->update();
                } else {
                    $settingData = CreditReference::where('player_id', $getuser->id)->first();
                    $balance = $settingData->available_balance_for_D_W;
                    $settingData->available_balance_for_D_W = $balance - $new_balance;
                    $settingData->update();
                }
                return redirect()->route('backpanel/player-banking')->with('message', 'Balance updated successfully.');
            }
        } elseif ($apass == '') {
            return redirect()->route('backpanel/player-banking')->with('error', 'Password can not be blank!');
        } else {
            return redirect()->route('backpanel/player-banking')->with('error', 'Wrong Password');
        }
        exit;
    }

    public function addAgentBanking(Request $request)
    {

        $apass = $request->adminpassword;
        $settings = "";
        $balance = 0;
        $getuser = Auth::user();
        if ($getuser->agent_level == 'COM') {
            $settings = setting::latest('id')->first();
            $balance = $settings->balance;
        } else {
            $settings = CreditReference::where('player_id', $getuser->id)->first();
            $balance = $settings['available_balance_for_D_W'];
        }
        $adm_password = $getuser->password;
        $admin_balance = $balance;

        $new_balance = 0;
        $agent = User::where('parentid', $getuser->id)->whereNotIn('agent_level', ['PL', 'SL'])->orderBy('user_name')->get();
        // ss agent balance check
        /*$player = User::where('parentid',$getuser->id)->where('agent_level','PL')->orderBy('user_name')->get();*/
        if (Hash::check($apass, $adm_password)) {
            //check admin balance
            $i = 0;
            $total_deposit_amount = 0;
            $total_withdraw_amount = 0;
            foreach ($agent as $play) {
                $btype = '';
                $amount = 0;
                $rem_balance = 0;
                $credit_amount = 0;
                $credit_balance = 0;
                if ($request->txtamount[$i] != '' && $request->txtamount[$i] > 0) {
                    $remark = $request->remark[$i];
                    $credit_data = CreditReference::where('player_id', $play->id)->select('*')->first();
                    $credit = 0;
                    if ($credit_data['credit'] != '') {
                        $credit = $credit_data['credit'];
                    }
                    $balance = $credit_data['remain_bal'];
                    $available_balance = $credit_data['available_balance_for_D_W'];
                    $amount = $request->txtamount[$i];
                    if ($request->player_deposite[$i] != '' && $request->player_deposite[$i] == 'D') {
                        $btype = $request->player_deposite[$i];
                    } else if ($request->player_withdraw[$i] != '' && $request->player_withdraw[$i] == 'W') {
                        $btype = $request->player_withdraw[$i];
                    }
                    if ($btype == 'W') {
                        $total_withdraw_amount = $total_withdraw_amount + $amount;
                    } else {
                        $total_deposit_amount = $total_deposit_amount + $amount;
                    }
                }
                $i++;
            }

            $admin_balance_check = $admin_balance + $total_withdraw_amount;
            $admin_balance_check = $admin_balance - $total_deposit_amount;
            if ($admin_balance_check < 0) {
                return redirect()->route('backpanel/agent-banking')->with('error', 'Agent balance update failed!');
            } else {
                $i = 0;
                foreach ($agent as $play) {
                    $btype = '';
                    $amount = 0;
                    $rem_balance = 0;
                    $credit_amount = 0;
                    $credit_balance = 0;
                    $player_new_balance = 0;
                    if ($request->txtamount[$i] != '' && $request->txtamount[$i] > 0) {
                        $remark = $request->remark[$i];
                        $credit_data = CreditReference::where('player_id', $play->id)->select('*')->first();
                        $credit = 0;
                        if ($credit_data['credit'] != '') {
                            $credit = $credit_data['credit'];
                        }
                        $balance = $credit_data['remain_bal'];
                        $available_balance = $credit_data['available_balance_for_D_W'];
                        $amount = $request->txtamount[$i];
                        if ($request->player_deposite[$i] != '' && $request->player_deposite[$i] == 'D') {
                            $btype = $request->player_deposite[$i];
                        } else if ($request->player_withdraw[$i] != '' && $request->player_withdraw[$i] == 'W') {
                            $btype = $request->player_withdraw[$i];
                        }
                        if ($btype == 'W') {
                            if ($available_balance < $amount) {
                                return redirect()->route('backpanel/agent-banking')->with('error', "Amount can not be more than Available D/W!");
                            }
                            $rem_balance = $balance - $amount;
                            $new_balance = $new_balance - $amount;
                            $player_new_balance = $available_balance - $amount;
                            $getuser = Auth::user();
                            $id = $getuser->id;
                            $totalbalance = $admin_balance - $amount;

                            UserDeposit::create([
                                'balanceType' => 'WITHDRAW',
                                'parent_id' => $id,
                                'child_id' => $play->id,
                                'amount' => $amount,
                                'balance' => $admin_balance,
                                'totalbalance' => $totalbalance,
                                'extra' => $remark,
                            ]);

                            // Jitendra     :: 07/02/2022

//                            if ($website->enable_partnership == 1)
                            {
                                UsersAccount::create([
                                    'user_id' => $play->id,
                                    'from_user_id' => $getuser->id,
                                    'to_user_id' => $play->id,
                                    'debit_amount' => $amount,
                                    'balance' => $available_balance,
                                    'closing_balance' => $available_balance - $amount,
                                    'remark' => $remark,
                                ]);

                                UsersAccount::create([
                                    'user_id' => $getuser->id,
                                    'from_user_id' => $getuser->id,
                                    'to_user_id' => $play->id,
                                    'credit_amount' => $amount,
                                    'balance' => $admin_balance,
                                    'closing_balance' => $admin_balance + $amount,
                                    'remark' => $remark,
                                ]);
                            }

                        }
                        else {
                            $rem_balance = $balance + $amount;
                            $new_balance = $new_balance + $amount;
                            $player_new_balance = $available_balance + $amount;
                            $getuser = Auth::user();
                            $id = $getuser->id;
                            $totalbalance = $admin_balance + $amount;

                            UserDeposit::create([
                                'balanceType' => 'DEPOSIT',
                                'parent_id' => $id,
                                'child_id' => $play->id,
                                'amount' => $amount,
                                'balance' => $admin_balance,
                                'totalbalance' => $totalbalance,
                                'extra' => $remark,
                            ]);

                            // Jitendra     :: 07/02/2022
//                            if ($website->enable_partnership == 1)
                            {
                                UsersAccount::create([
                                    'user_id' => $play->id,
                                    'from_user_id' => $getuser->id,
                                    'to_user_id' => $play->id,
                                    'credit_amount' => $amount,
                                    'balance' => $available_balance,
                                    'closing_balance' => $available_balance + $amount,
                                    'remark' => $remark,
                                ]);

                                UsersAccount::create([
                                    'user_id' => $getuser->id,
                                    'from_user_id' => $getuser->id,
                                    'to_user_id' => $play->id,
                                    'debit_amount' => $amount,
                                    'balance' => $admin_balance,
                                    'closing_balance' => $admin_balance - $amount,
                                    'remark' => $remark,
                                ]);
                            }
                        }
                        $refid = $request->creditref[$i];
                        $play_client = CreditReference::find($refid);
                        $play_client->remain_bal = $rem_balance;
                        //$play_client->available_balance_for_D_W = $rem_balance; -- comment by nnn on 6-9-2021
                        $play_client->available_balance_for_D_W = $player_new_balance;
                        $play_client->update();
                    }
                    if ($request->creditamount[$i] != '' && $request->creditamount[$i] > 0) {
                        $credit_data = CreditReference::where('player_id', $play->id)->select('*')->first();
                        $credit = 0;
                        if ($credit_data['credit'] != '') {
                            $credit = $credit_data['credit'];
                        }
                        $credit_amount = $request->creditamount[$i];
                        $credit_balance = $credit_amount;
                        $refid = $request->creditref[$i];
                        $play_client = CreditReference::find($refid);
                        $play_client->credit = $credit_balance;
                        $play_client->update();
                    }
                    $i++;
                }

                $settings = "";
                $balance = 0;
                $getuser = Auth::user();
                if ($getuser->agent_level == 'COM') {
                    $settingData = setting::latest('id')->first();
                    $balance = $settingData->balance;
                    $settingData->balance = $admin_balance - $new_balance;
                    $settingData->update();
                } else {
                    $settingData = CreditReference::where('player_id', $getuser->id)->first();
                    $balance = $settingData->available_balance_for_D_W;
                    $settingData->available_balance_for_D_W = $balance - $new_balance;
                    $settingData->update();
                }
                return redirect()->route('backpanel/agent-banking')->with('message', 'Agent balance updated successfully!');
            }
        } elseif ($apass == '') {
            return redirect()->route('backpanel/agent-banking')->with('error', 'Password can not be blank!');
        } else {
            return redirect()->route('backpanel/agent-banking')->with('error', 'Wrong Password!');
        }
    }

    public function manage_fancy()
    {
        $sports = Sport::where('status', 'active')->where('sId', '4')->get();
        $matchList = Match::get();

        return view('backpanel/manage_fancy', compact('sports', 'matchList'));
    }

    public function getFancyBetPosition($fancyName, $mid, $eventid, $uid, $fancy_result)
    {
        $my_placed_bets = MyBets::where('user_id', $uid)->where('match_id', $eventid)->where('team_name', @$fancyName)->where('bet_type', 'SESSION')->where('isDeleted', 0)->where('result_declare', 1)->orderBy('created_at', 'asc')->get();
        $abc = sizeof($my_placed_bets);
        $return_final_exposure = '';
        $profit_loss = "";
        $return_exposure = 0;
        $expo_array = array();
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
                $expo_array[] = $final_exposer;
                if ($bet_deduct_amt > 0) {
                    if ($fancy_result == $run_arr[$kk]) {
                        $return_final_exposure = $bet_deduct_amt;
                        $profit_loss = "Profit";
                        $return_exposure = $final_exposer;
                    }
                } else {
                    if ($fancy_result == $run_arr[$kk]) {
                        $return_final_exposure = abs($bet_deduct_amt);
                        $profit_loss = "Loss";
                        $return_exposure = $final_exposer;
                    }

                }
            }
        }

        if (count($expo_array) > 0) {
            $return_exposure = min($expo_array);
        }
        return $return_final_exposure . "~~" . $profit_loss . "~~" . $return_exposure;

    }

    public function resultRollback(Request $request){
       return $this->updateFancyResultRollback($request->id);
    }

    public function updateFancyResultRollback($id)
    {
        //fancy rollback

        $get = FancyResult::where('id', $id)->first();
        /*echo $get;
		exit;*/
        $eventid = $get['eventid'];
        $mid = $get['match_id'];
        $fancyname = $get['fancy_name'];
        $fancy_result = $get['result'];
        $match = Match::where('id', $mid)->first();
        //get bet

        //$allbet = MyBets::where('match_id',$eventid)->where('bet_type','SESSION')->where('team_name',$fancyname)->where('isDeleted',0)->where('result_declare',1)->get();
        $allbet = MyBets::where('match_id', $eventid)->where('bet_type', 'SESSION')->where('team_name', $fancyname)->where('isDeleted', 0)->where('result_declare', 1)->groupby('user_id')->get();

        foreach ($allbet as $bet) {
            $bid = $bet->id;
            $userData = MyBets::where('id', $bid)->first();
            //$expamt=$userData['exposureAmt'];
            $uid = $userData['user_id'];

            $total_calculated_position = SELF::getFancyBetPosition($fancyname, $mid, $eventid, $uid, $fancy_result);

            $check_value = explode("~~", $total_calculated_position);
            $profit_and_loss_amount = $check_value[0];
            $proift_or_loss = $check_value[1];
            $expamt = abs($check_value[2]);

            $exposer_tran_log = UserExposureLog::where('match_id', $mid)->where('bet_type', 'SESSION')->where('fancy_name', $fancyname)->where('user_id', $userData->user_id)->first();
            if(!empty($exposer_tran_log)) {
                $fancy_win_type = $exposer_tran_log['win_type'];
                $fancy_profit = $exposer_tran_log->profit;
                $fancy_loss = $exposer_tran_log->loss;
            }else {

                $fancy_profit = $profit_and_loss_amount;
                $fancy_loss = $proift_or_loss;
            }

            if($fancy_loss <= 0 && $fancy_profit > 0){
                $expamt = 0;
            }

            $getc = CreditReference::where('player_id', $userData->user_id)->first();
            $creid = $getc['id'];
            $updc = CreditReference::find($creid);
            $updc->exposure = $getc->exposure + $expamt;

            if ($fancy_profit != '')
                $chk = $getc->available_balance_for_D_W - $expamt - $fancy_profit;
            else {
                if (abs($fancy_loss) == abs($expamt))
                    $chk = $getc->available_balance_for_D_W + abs($fancy_loss);
                else
                    $chk = $getc->available_balance_for_D_W + abs($fancy_loss) - $expamt;
            }
            $remain_balance = $getc->remain_bal;

            //if($chk>0 && $getc->available_balance_for_D_W>0 && $getc->available_balance_for_D_W>$expamt) ///nnn 20-10-2021
            if ($chk >= 0) {
                if ($fancy_profit != '') {
                    $updc->available_balance_for_D_W = $getc->available_balance_for_D_W - $expamt - $fancy_profit;
                    $updc->remain_bal = $remain_balance - $fancy_profit;
                } else {
                    if (abs($fancy_loss) == abs($expamt)) {
                        $updc->available_balance_for_D_W = $getc->available_balance_for_D_W;
                        $updc->remain_bal = $remain_balance + abs($fancy_loss);
                    } else {
                        $updc->available_balance_for_D_W = $getc->available_balance_for_D_W + abs($fancy_loss) - $expamt;
                        $updc->remain_bal = $remain_balance + abs($fancy_loss);

                    }
                }

                $upd = $updc->update();

                if ($upd) {
                    $del = MyBets::where('match_id', $eventid)->where('bet_type', 'SESSION')->where('team_name', $fancyname)->where('isDeleted', 0)->where('user_id', $uid)->update(['result_declare' => 0]);


                    {
                        //calculating admin balance
                        $admin_tran = UserExposureLog::where('match_id', $match->id)->where('user_id', $uid)->where('bet_type', 'SESSION')->where('fancy_name', $fancyname)->get();
                        $admin_profit = 0;
                        $admin_loss = 0;
                        foreach ($admin_tran as $trans) {

                            if ($trans->profit != '' && $trans->win_type == 'Profit') {
                                $settings = setting::latest('id')->first();
                                $adm_balance = $settings->balance;
                                $new_balance = $adm_balance + $trans->profit;

                                $adminData = setting::find($settings->id);
                                $adminData->balance = $new_balance;
                                $adminData->update();



                                //calculating parent balance
                                $parentid = self::GetAllParentofPlayer($uid);
                                $parentid = json_decode($parentid);
                                if (!empty($parentid)) {
                                    for ($i = 0; $i < sizeof($parentid); $i++) {
                                        $pid = $parentid[$i];
                                        if ($pid != 1) {
                                            $creditref_bal = CreditReference::where(['player_id' => $pid])->first();
                                            $bal = $creditref_bal['remain_bal'];
                                            $remain_balance_ = $bal - $trans->profit;
                                            $upd_ = CreditReference::find($creditref_bal->id);
                                            $upd_->remain_bal = $remain_balance_;
                                            $update_parent = $upd_->update();
                                        }
                                    }
                                }
                                //end for calculating parent balance
                            }
                            else if ($trans->loss != '' && $trans->win_type == 'Loss') {

                                $settings = setting::latest('id')->first();
                                $adm_balance = $settings->balance;
                                $new_balance = $adm_balance - abs($trans->loss);

                                $adminData = setting::find($settings->id);
                                $adminData->balance = $new_balance;
                                $adminData->update();

                                //calculating parent balance
                                $parentid = self::GetAllParentofPlayer($uid);
                                $parentid = json_decode($parentid);
                                if (!empty($parentid)) {
                                    for ($i = 0; $i < sizeof($parentid); $i++) {
                                        $pid = $parentid[$i];
                                        if ($pid != 1) {
                                            $creditref_bal = CreditReference::where(['player_id' => $pid])->first();
                                            $bal = $creditref_bal['remain_bal'];
                                            $remain_balance_ = $bal + abs($trans->loss);
                                            $upd_ = CreditReference::find($creditref_bal['id']);
                                            $upd_->remain_bal = $remain_balance_;
                                            $update_parent = $upd_->update();
                                        }
                                    }
                                }
                                //end for calculating parent balance
                            }

                            // removing account statment entries from account table         :: JEET DUMS

                            UsersAccount::where("user_exposure_log_id",$trans->id)->delete();
                        }
                    }
                    //end for calculating admin balance
                    //$del_exp=UserExposureLog::where('match_id',$mid)->where('fancy_name',$fancyname)->where('user_id',$userData->user_id)->where('bet_type','SESSION')->delete();  //nnn 20-10-2021
                }
            }
        }

        UserExposureLog::where('match_id', $mid)->where('fancy_name', $fancyname)->where('bet_type', 'SESSION')->delete();
        FancyResult::find($id)->delete();

        return response()->json(array('success' => 'success'));
    }

    /*public function resultRollback(Request $request) ///nnn 20-10-2021
	{
		//fancy rollback

		$get=FancyResult::where('id',$request->id)->first();

		$eventid=$get['eventid'];
		$mid=$get['match_id'];
		$fancyname=$get['fancy_name'];
		$match = Match::where('id',$mid)->first();
		//get bet
		$allbet = MyBets::where('match_id',$eventid)->where('bet_type','SESSION')->where('team_name',$fancyname)->where('isDeleted',0)->where('result_declare',1)->get();

		foreach($allbet as $bet)
		{
			$bid=$bet->id;
			$userData = MyBets::where('id',$bid)->first();
			$expamt=$userData['exposureAmt'];
			$uid=$userData['user_id'];

			$exposer_tran_log=UserExposureLog::where('match_id',$mid)->where('bet_type','SESSION')->where('fancy_name',$fancyname)->where('user_id',$userData->user_id)->first();
			$fancy_win_type=$exposer_tran_log['win_type'];
			$fancy_profit=$exposer_tran_log->profit;
			$fancy_loss=$exposer_tran_log->loss;


			$getc=CreditReference::where('player_id',$userData->user_id)->first();
			$creid=$getc['id'];
			$updc=CreditReference::find($creid);
			$updc->exposure = $getc->exposure+$expamt;
			if($fancy_profit!='')
				$chk=$getc->available_balance_for_D_W-$expamt-$fancy_profit;
			else
				$chk=$getc->available_balance_for_D_W;
			$remain_balance=$getc->remain_bal;

			if($chk>0 && $getc->available_balance_for_D_W>0 && $getc->available_balance_for_D_W>$expamt)
			{
				if($fancy_profit!='')
				{
					$updc->available_balance_for_D_W = $getc->available_balance_for_D_W-$expamt-$fancy_profit;
					$updc->remain_bal =$remain_balance-$fancy_profit;
				}
				else{
					$updc->available_balance_for_D_W = $getc->available_balance_for_D_W;
					$updc->remain_bal =$remain_balance+$expamt;
				}

				$upd=$updc->update();

				if($upd)
				{
					$userData = MyBets::find($bid);
					$userData->result_declare = 0;
					$del=$userData->update();

					//calculating admin balance
					$admin_tran=UserExposureLog::where('match_id',$match->id)->where('user_id',$uid)->where('bet_type','SESSION')->where('fancy_name',$fancyname)->get();
					$admin_profit=0;
					$admin_loss=0;
					foreach($admin_tran as $trans)
					{
						if($trans->profit!='' && $trans->win_type=='Profit')
						{
							$settings = setting::latest('id')->first();
							$adm_balance=$settings->balance;
							$new_balance=$adm_balance+$trans->profit;

							$adminData = setting::find($settings->id);
							$adminData->balance=$new_balance;
							$adminData->update();

							//calculating parent balance
							$parentid=self::GetAllParentofPlayer($uid);
							$parentid=json_decode($parentid);
							if(!empty($parentid))
							{
								for($i=0;$i<sizeof($parentid);$i++)
								{
									$pid=$parentid[$i];
									if($pid!=1)
									{
										$creditref_bal=CreditReference::where(['player_id'=>$pid])->first();
										$bal=$creditref_bal['remain_bal'];
										$remain_balance_=$bal-$trans->profit;
										$upd_=CreditReference::find($creditref_bal->id);
										$upd_->remain_bal =$remain_balance_;
										$update_parent=$upd_->update();
									}
								}
							}
							//end for calculating parent balance
						}
						else if($trans->loss!='' && $trans->win_type=='Loss')
						{
							$settings = setting::latest('id')->first();
							$adm_balance=$settings->balance;
							$new_balance=$adm_balance-abs($trans->loss);

							$adminData = setting::find($settings->id);
							$adminData->balance=$new_balance;
							$adminData->update();

							//calculating parent balance
							$parentid=self::GetAllParentofPlayer($uid);
							$parentid=json_decode($parentid);
							if(!empty($parentid))
							{
								for($i=0;$i<sizeof($parentid);$i++)
								{
									$pid=$parentid[$i];
									if($pid!=1)
									{
										$creditref_bal=CreditReference::where(['player_id'=>$pid])->first();
										$bal=$creditref_bal['remain_bal'];
										$remain_balance_=$bal+abs($trans->loss);
										$upd_=CreditReference::find($creditref_bal['id']);
										$upd_->remain_bal =$remain_balance_;
										$update_parent=$upd_->update();
									}
								}
							}
							//end for calculating parent balance
						}
					}
					//end for calculating admin balance
					//$del_exp=UserExposureLog::where('match_id',$mid)->where('fancy_name',$fancyname)->where('user_id',$userData->user_id)->where('bet_type','SESSION')->delete();  //nnn 20-10-2021
				}
			}
		}
		$del_exp=UserExposureLog::where('match_id',$mid)->where('fancy_name',$fancyname)->where('bet_type','SESSION')->delete();

		FancyResult::find($request->id)->delete();
		return response()->json(array('success'=> 'success'));
	}*/
    public function manageFancyDetail($id)
    {
        $match = Match::where('id', $id)->first();

//        dd($match->toArray());

        return view('backpanel/managefancy-history-details', compact('match'));
    }

    public function fancyHistoryDetail($id)
    {
        $fancyResult = FancyResult::where('match_id', $id)->get();
        return view('backpanel/fancyHistoryDetail', compact('fancyResult'));
    }

    public function fancyuser($id)
    {
        $fancyResult = FancyResult::where('id', $id)->first();
        $betdata = MyBets::where('match_id', $fancyResult->eventid)->where('team_name', $fancyResult->fancy_name)->get();

        return view('backpanel/fancyuser', compact('betdata'));
    }

    public function getFancy($id)
    {
        $html = '';
        $match = Match::where('id', $id)->first();
        $fancyName = array();
        /*$match_bet = MyBets::whereNotIn('team_name', function($query) {
		$query->select('fancy_name')->from('fancy_results');
		})->where('match_id',$match->event_id)->where('bet_type','SESSION')->groupBy('my_bets.team_name')->get();

		foreach ($match_bet as $value) {
			$fancyName[] = $value->team_name;
		}*/
        $ev = $match->event_id;
        $match_bet = MyBets::whereNotIn('team_name', function ($query) use ($ev) {
            //DB::enableQueryLog();
            //$query->select('fancy_name')->from('fancy_results');
            $query->select('fancy_name')
                ->from(with(new FancyResult)->getTable())
                ->where('eventid', $ev);
            //->whereRaw('fancy_results.eventid = '.$match->event_id)

        })->where('match_id', $match->event_id)->where('bet_type', 'SESSION')->groupBy('my_bets.team_name')->get();
        //dd(DB::getQueryLog());
        foreach ($match_bet as $value) {
            $fancyName[] = $value->team_name;
        }
        $count = 1;
        $i = 0;
        foreach ($match_bet as $value1) {
            $html .= ' <tr class="white-bg">
            <td class="text-center">' . $count . '</td>
            <td class="text-left">' . $value1->team_name . '</td>
            <td class="text-center"><input type="text" class="fancy_result" name="fancy_result" id="fancy_result' . $i . '" onkeypress="return isNumberKey(event)" required></td>
            <td class="text-center"> <a href="javascript:void(0);" class="green-bg text-color-white sub_res" data-fancyre="' . $i . '" data-betId="' . $value1->id . '" data-eventid="' . $match->event_id . '"  data-match="' . $match->id . '" data-fancy=\'' . $value1->team_name . '\'onclick="resultDeclare(this);">SUBMIT</a> | <a href="javascript:void(0);" class="red-bg text-color-white " data-betId="' . $value1->id . '"  data-eventid="' . $match->event_id . '"  data-match="' . $match->id . '" data-fancy=\'' . $value1->team_name . '\' onclick="resultDeclarecancel(this);">CANCEL</a> </td>
	        </tr>';
            $count++;
            $i++;
        }
        return $html;
    }

    public function sports_list()
    {
        $sports = Sport::where('status', 'active')->with('matches')->get();
//        $matchList = Match::where('winner', null)->orderby('match_date', 'asc')->get();

//        dd($sports->toArray());

        return view('backpanel/sports-list', compact('sports'));
    }

    public function resultRollbackMatch(Request $request)
    {
        $match = Match::find($request->id);
        $match->winner = Null;
        $upd = $match->update();

        //$upd=1;
        if ($upd == 1) {

            $bet = MyBets::where('match_id', $match->event_id)->where('bet_type', '!=', 'SESSION')->where('result_declare', 1)->groupby('user_id')->get();
            foreach ($bet as $b) {
                $userid = $b->user_id;
                $match_data = Match::where(['event_id' => $b->match_id])->first();

                $expoLogIds = [];

                $user_expo = UserExposureLog::where('match_id', $match_data->id)->where('user_id', $userid)->where('bet_type', '!=', 'SESSION')->get();

                $odds_profit = 0;
                $odds_loss = 0;
                $bm_profit = 0;
                $bm_loss = 0;
                $odds_win_type = '';
                $bm_win_type = '';
                if (count($user_expo) > 0) {
                    foreach ($user_expo as $expo) {
                        $expoLogIds[] = $expo->id;
                        if ($expo->bet_type == 'ODDS') {
                            $odds_profit = $expo->profit;
                            $odds_loss = $expo->loss;
                            $odds_win_type = $expo->win_type;
                        } else if ($expo->bet_type == 'BOOKMAKER') {
                            $bm_profit = $expo->profit;
                            $bm_loss = $expo->loss;
                            $bm_win_type = $expo->win_type;
                        }
                    }
                }
                else {
                    $odds_win_type = 'Cancel';
                    $userData = MyBets::where("match_id", $match->event_id)->where('bet_type', '!=', 'SESSION')->update(["result_declare" => 0]);
                    //$betnew=MyBets::where('match_id',$match->event_id)->where('bet_type','!=','SESSION')->where('result_declare',0)->where("user_id", $userid)->groupby('user_id')->get();
                    //foreach($betnew as $b)
                    //{
                    $exposer = SELF::getPlayerExAmountForTie($match->event_id, $userid, 'Cancel');

                    $getc = CreditReference::where('player_id', $userid)->first();
                    $creid = $getc['id'];
                    $updc = CreditReference::find($creid);
                    $updc->exposure = $getc->exposure + $exposer;
                    $updc->available_balance_for_D_W = $getc->available_balance_for_D_W - $exposer;
                    $upd_can = $updc->update();
                    if ($upd)
                        echo "cnacell";
                    else
                        echo '1111';
                    exit;

                    //}

                }
                if ($odds_win_type == 'Profit') {

                    $calculated_commission = 0;
                    $user_detail = User::where(['id' => $userid])->first();
                    $my_parent = $user_detail->parentid;
                    $get_commission = $user_detail->commission;
                    $calculated_commission = round(($odds_profit * $get_commission) / 100, 2);

                    $getc = CreditReference::where('player_id', $userid)->first();
                    $creid = $getc['id'];
                    $updc = CreditReference::find($creid);
                    $updc->exposure = $getc->exposure + $odds_loss;
                    $avail = 0;
                    $avail = $getc->available_balance_for_D_W - $odds_loss - $odds_profit + $calculated_commission;
                    if ($avail < 0)
                        $avail = 0;
                    $updc->available_balance_for_D_W = $avail;
                    $updc->remain_bal = $getc->remain_bal - $odds_profit + $calculated_commission;
                    $upd = $updc->update();
                    if ($upd) {

                            //admin balance update
                            $settings = setting::latest('id')->first();
                            $adm_balance = $settings->balance;
                            $new_balance = $adm_balance + $odds_profit;

                            $adminData = setting::find($settings->id);
                            $adminData->balance = $new_balance;
                            $adminData->update();
                            //end for admin balance

                            //update commission on my player's parent account
                            if ($my_parent > 1) {
                                $creditref_bal = CreditReference::where(['player_id' => $my_parent])->first();
                                $bal = $creditref_bal->remain_bal;
                                $available_balance = $creditref_bal->available_balance_for_D_W;

                                $upd_ = CreditReference::find($creditref_bal->id);
                                //$upd_->available_balance_for_D_W =$available_balance-$calculated_commission;
                                $upd_->available_balance_for_D_W = $available_balance;
                                $update_parent = $upd_->update();
                            } else {
                                $setting = setting::latest('id')->first();
                                $balance = $setting->balance;
                                $new_balance = $balance - $calculated_commission;

                                $adminData = setting::find($setting->id);
                                $adminData->balance = $new_balance;
                                $adminData->update();
                            }

                        //end for updating commission on player's parent account
                        $userData = MyBets::where("match_id", $match->event_id)->where("user_id", $userid)->where('bet_type', '!=', 'SESSION')->update(["result_declare" => 0]);
                        //delete exposer log
                        $del_exp = UserExposureLog::where('match_id', $match_data->id)->where('user_id', $userid)->where('bet_type', 'ODDS')->delete();
                    }
                }
                else if ($odds_win_type == 'Loss') {

                    $getc = CreditReference::where('player_id', $userid)->first();
                    $creid = $getc['id'];
                    $updc = CreditReference::find($creid);
                    $updc->exposure = $getc->exposure + $odds_loss;
                    $updc->available_balance_for_D_W = $getc->available_balance_for_D_W;
                    $updc->remain_bal = $getc->remain_bal + $odds_loss;
                    $upd = $updc->update();
                    if ($upd) {

                            $settings = setting::latest('id')->first();
                            $adm_balance = $settings->balance;
                            $new_balance = $adm_balance - $odds_loss;

                            $adminData = setting::find($settings->id);
                            $adminData->balance = $new_balance;
                            $adminData->update();


                        $userData = MyBets::where("match_id", $match->event_id)->where("user_id", $userid)->where('bet_type', '!=', 'SESSION')->update(["result_declare" => 0]);
                        $del_exp = UserExposureLog::where('match_id', $match_data->id)->where('user_id', $userid)->where('bet_type', 'ODDS')->delete();
                    }
                }

                if ($bm_win_type == 'Profit') {

                    $getc = CreditReference::where('player_id', $userid)->first();
                    $creid = $getc['id'];
                    $updc = CreditReference::find($creid);
                    $updc->exposure = $getc->exposure + $bm_loss;
                    $avail = 0;
                    $avail = $getc->available_balance_for_D_W - $bm_loss - $bm_profit;
                    if ($avail < 0)
                        $avail = 0;
                    $updc->available_balance_for_D_W = $avail;
                    $upd = $updc->update();
                    if ($upd) {

                            $settings = setting::latest('id')->first();
                            $adm_balance = $settings->balance;
                            $new_balance = $adm_balance + $bm_profit;

                            $adminData = setting::find($settings->id);
                            $adminData->balance = $new_balance;
                            $adminData->update();


                        $userData = MyBets::where("match_id", $match->event_id)->where("user_id", $userid)->where('bet_type', '!=', 'SESSION')->update(["result_declare" => 0]);
                        $del_exp = UserExposureLog::where('match_id', $match_data->id)->where('user_id', $userid)->where('bet_type', 'BOOKMAKER')->delete();
                    }
                }
                else if ($bm_win_type == 'Loss') {

                    $getc = CreditReference::where('player_id', $userid)->first();
                    $creid = $getc['id'];
                    $updc = CreditReference::find($creid);
                    $updc->exposure = $getc->exposure + $bm_loss;
                    $updc->available_balance_for_D_W = $getc->available_balance_for_D_W;
                    $updc->remain_bal = $getc->remain_bal + $bm_loss;
                    $upd = $updc->update();
                    if ($upd) {

                            $settings = setting::latest('id')->first();
                            $adm_balance = $settings->balance;
                            $new_balance = $adm_balance - $bm_loss;

                            $adminData = setting::find($settings->id);
                            $adminData->balance = $new_balance;
                            $adminData->update();


                        $userData = MyBets::where("match_id", $match->event_id)->where("user_id", $userid)->where('bet_type', '!=', 'SESSION')->update(["result_declare" => 0]);
                        $del_exp = UserExposureLog::where('match_id', $match_data->id)->where('user_id', $userid)->where('bet_type', 'BOOKMAKER')->delete();
                    }
                }

                if(count($expoLogIds) > 0) {
                    UsersAccount::whereIn("user_exposure_log_id", $expoLogIds)->delete();
                }
            }


        }
        //return response()->json(array('success'=> 'success'));
    }

    public function saveMatchStatus(Request $request)
    {
        $fid = $request->fid;
        $chk = $request->chk;
        if ($chk != 1)
            $chk = 0;
        $settingData = Match::find($fid);
        $settingData->status = $chk;
        $upd = $settingData->update();
        if ($upd)
            echo 'Success';
        else
            echo 'Fail';
    }

    public function chkstatusbm(Request $request)
    {
        $matchId = $request->fid;
        $chk = $request->chk;

        $match = Match::find($matchId);
        $match_data = app('App\Http\Controllers\RestApi')->getSingleCricketMatchData($match->event_id, $match->match_id, $match->sports_id);
        if (isset($match_data['t2'][0]['bm1'][0]) != '') {
            if ($chk != 1) {
                $bm = 0;
            } else {
                $bm = 1;
            }
            $upd = Match::find($matchId);
            $upd->bookmaker = $bm;
            $upd->update();
            return response()->json(array('result' => 'success', 'message' => 'Status change successfully'));
        } else {
            return response()->json(array('result' => 'error', 'message' => 'Bookmaker is not available'));
        }
    }

    public function chkstatusfancy(Request $request)
    {
        $matchId = $request->fid;
        $chk = $request->chk;
        $match = Match::find($matchId);

        $match_data = app('App\Http\Controllers\RestApi')->getSingleCricketMatchData($match->event_id, $match->match_id, $match->sports_id);
        if (isset($match_data['t3'][0]) != '') {
            if ($chk != 1) {
                $fancy = 0;
            } else {
                $fancy = 1;
            }
            $upd = Match::find($matchId);
            $upd->fancy = $fancy;
            $upd->update();
            return response()->json(array('result' => 'success', 'message' => 'Status change successfully'));
        } else {
            return response()->json(array('result' => 'error', 'message' => 'fancy is not available'));
        }
    }

    public function saveMatchAction(Request $request)
    {
        $fid = $request->fid;
        $chk = $request->chk;
        if ($chk != 1)
            $chk = 0;
        $settingData = Match::find($fid);
        $settingData->action = $chk;
        $upd = $settingData->update();
        if ($upd)
            echo 'Success';
        else
            echo 'Fail';
    }

    public function saveMatchOddsLimit(Request $request)
    {
        $fid = $request->fid;
        $chk = $request->chk;
        if ($chk == '')
            $chk = 0;
        $settingData = Match::find($fid);
        $settingData->odds_limit = $chk;
        $upd = $settingData->update();
        if ($upd)
            echo 'Success';
        else
            echo 'Fail';
    }

    public function saveMatchBetsMinLimit(Request $request)
    {
        $fid = $request->fid;
        $chk = $request->chk;
        if ($chk == '')
            $chk = 0;
        $settingData = Match::find($fid);
        $settingData->min_bet_odds_limit = $chk;
        $upd = $settingData->update();
        if ($upd)
            echo 'Success';
        else
            echo 'Fail';
    }

    public function saveMatchBetsMaxLimit(Request $request)
    {
        $fid = $request->fid;
        $chk = $request->chk;
        if ($chk == '')
            $chk = 0;
        $settingData = Match::find($fid);
        $settingData->max_bet_odds_limit = $chk;
        $upd = $settingData->update();
        if ($upd)
            echo 'Success';
        else
            echo 'Fail';
    }

    public function saveMatchBmMinLimit(Request $request)
    {
        $fid = $request->fid;
        $chk = $request->chk;
        if ($chk == '')
            $chk = 0;
        $settingData = Match::find($fid);
        $settingData->min_bookmaker_limit = $chk;
        $upd = $settingData->update();
        if ($upd)
            echo 'Success';
        else
            echo 'Fail';
    }

    public function saveMatchBmMaxLimit(Request $request)
    {
        $fid = $request->fid;
        $chk = $request->chk;
        if ($chk == '')
            $chk = 0;
        $settingData = Match::find($fid);
        $settingData->max_bookmaker_limit = $chk;
        $upd = $settingData->update();
        if ($upd)
            echo 'Success';
        else
            echo 'Fail';
    }

    public function saveMatchFancyMinLimit(Request $request)
    {
        $fid = $request->fid;
        $chk = $request->chk;
        if ($chk == '')
            $chk = 0;
        $settingData = Match::find($fid);
        $settingData->min_fancy_limit = $chk;
        $upd = $settingData->update();
        if ($upd)
            echo 'Success';
        else
            echo 'Fail';
    }

    public function saveMatchFancyMaxLimit(Request $request)
    {
        $fid = $request->fid;
        $chk = $request->chk;
        if ($chk == '')
            $chk = 0;
        $settingData = Match::find($fid);
        $settingData->max_fancy_limit = $chk;
        $upd = $settingData->update();
        if ($upd)
            echo 'Success';
        else
            echo 'Fail';
    }

    public static function getExAmountCricketAndTennisBackend($sportID = '', $matchid = '', $userID = '')
    {
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
                    } else {
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
                    $profitAmt = $bet['bet_profit'];
                    if ($bet['bet_side'] == 'lay') {
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
                    } else {
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

            }
        }
        return $response;
    }

    public static function getPlayerExAmountForTie($sportID, $userid, $cancel = 'TIE')
    {
        $id = $userid;
        //DB::enableQueryLog();
        $sportsModel = '';
        if ($cancel == 'TIE') {
//            DB::enableQueryLog();
            $sportsModel = MyBets::select('my_bets.id', 'my_bets.sportID', 'my_bets.created_at', 'match.*')->join('match', 'match.event_id', '=', 'my_bets.match_id')
                ->where('my_bets.result_declare', 0)
                ->where('my_bets.user_id', $id)
                ->where('my_bets.isDeleted', 0)
                ->where('match.event_id', $sportID)
                ->where('match.winner', 'TIE')
                ->orderBy('my_bets.id', 'Desc')
                ->groupby('my_bets.match_id') /// nnn 19-8-2021 put becuase exposer calculating twice as over here this query fetching all same match bets multiple times
                ->get(); /// nnn 7-8-2021
//            dd(DB::getQueryLog());

        } else {
            //DB::enableQueryLog();
            $sportsModel = MyBets::select('my_bets.id', 'my_bets.sportID', 'my_bets.created_at', 'match.*')->join('match', 'match.event_id', '=', 'my_bets.match_id')
                ->where('my_bets.result_declare', 0)
                ->where('my_bets.user_id', $id)
                ->where('my_bets.isDeleted', 0)
                ->where('match.event_id', $sportID)
                ->whereNull('match.winner')
                ->orderBy('my_bets.id', 'Desc')
                ->groupby('my_bets.match_id') /// nnn 19-8-2021 put becuase exposer calculating twice as over here this query fetching all same match bets multiple times
                ->get(); /// nnn 7-8-2021
            //dd(DB::getQueryLog());

        }
        //dd(DB::getQueryLog());
        $exAmtTot = 0;

//        dd($sportsModel->toArray());

        foreach ($sportsModel as $keyMatch => $matchVal) {
            $gameModel = Sport::where(["sId" => $matchVal->sports_id])->first();
            if (strtoupper($gameModel->sport_name) == 'CRICKET' || strtoupper($gameModel->sport_name) == 'TENNIS' || strtoupper($gameModel->sport_name) == 'CASINO' || strtoupper($gameModel->sport_name) == 'SOCCER') {
                if (strtoupper($gameModel->name) == 'CASINO') {
                    $exAmtArr = self::getExAmountCricketAndTennisBackend($matchVal->id, '', $id);
                } else {
                    $matchid = $matchVal->event_id;
                    $exAmtArr = self::getExAmountCricketAndTennisBackend('', $matchid, $id);
                }

//                dd($exAmtArr);

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
            }
        }

        return (abs($exAmtTot));
    }

    public static function getExAmount($sportID = '', $id = '', $winner, $mid, $matchname, $bettype)
    {

        $masterAdmin = User::where("agent_level","COM")->first();
        $settings = setting::latest('id')->first();
        $adm_balance = $settings->balance;
        $team1_bet_total = '';
        $team1_bet_class = '';
        $team2_bet_total = '';
        $team2_bet_class = '';
        $team_draw_bet_total = '';
        $team_draw_bet_class = '';
        $my_placed_bets = MyBets::where('match_id', $sportID)->where('user_id', $id)->where('bet_type', $bettype)->where('isDeleted', 0)->where('result_declare', 0)->get();
        $team2_bet_total = 0;
        $team1_bet_total = 0;
        $team_draw_bet_total = 0;

        $team1_name = '';
        $team2_name = '';
        $team_draw_name = '';

        @$team_name = explode(" v ", strtolower($matchname));
        $team1_name = @$team_name[0];
        if (@$team_name[1])
            $team2_name = @$team_name[1];
        else
            $team2_name = '';

        if (sizeof($my_placed_bets) > 0) {
            foreach ($my_placed_bets as $bet) {
                $abc = json_decode($bet->extra, true);
                if (is_array($abc) && count($abc) >= 2) {
                    $team_draw_name = 'The Draw';
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
                }
                else if (is_array($abc) && count($abc) == 1) {
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

        if ($winner == ucfirst($team1_name)) {
            $profit = '';
            $loss = '';
            $is_won = 0;
            if ($team1_bet_total >= 0) {
                $is_won = 1;
                $profit = $team1_bet_total;
                if ($team2_bet_total < 0)
                    $loss = $team2_bet_total;
                else
                    $loss = 0;
            } else {
                $loss = $team1_bet_total;

                if($team2_bet_total > 0)
                    $profit = $team2_bet_total;
                else
                    $profit = 0;
            }

//            dd($team1_bet_total, $team2_bet_total, $team_draw_bet_total, $loss, $profit);

            $betModel = new UserExposureLog();
            $betModel->match_id = $mid;
            $betModel->user_id = $id;
            $betModel->bet_type = $bettype;
            $betModel->profit = $profit;
            $betModel->loss = abs($loss);
            if ($is_won == 1)
                $betModel->win_type = 'Profit';
            else
                $betModel->win_type = 'Loss';
            $check = $betModel->save();

            if ($check) {
                if ($is_won == 1 && $is_won != '') {
                    $calculated_commission = 0;
                    $user_detail = User::where(['id' => $id])->first();
                    $my_parent = $user_detail->parentid;
                    $get_commission = $user_detail->commission;
                    if ($betModel->bet_type == "ODDS")
                        $calculated_commission = round(($profit * $get_commission) / 100, 2);

                    $creditref = CreditReference::where(['player_id' => $id])->first();
                    $exposer = $creditref->exposure - abs($loss);
                    $balance = $creditref->available_balance_for_D_W + abs($loss) + $profit - $calculated_commission;
                    $remain_balance = $creditref->remain_bal + $profit - $calculated_commission;


                    $available_balance = $creditref->available_balance_for_D_W;
                    UsersAccount::create([
                        'user_id' => $id,
                        'from_user_id' => $id,
                        'to_user_id' => $id,
                        'credit_amount' => $profit,
                        'balance' => $available_balance,
                        'closing_balance' => $available_balance + $profit,
                        'remark' => "",
                        'match_id' => $mid,
                        'bet_user_id' => $id,
                        'user_exposure_log_id' => $betModel->id
                    ]);

                    $available_balance = $available_balance + $profit;

                    UsersAccount::create([
                        'user_id' => $masterAdmin->id,
                        'from_user_id' => $id,
                        'to_user_id' => $masterAdmin->id,
                        'debit_amount' => $profit,
                        'balance' => $adm_balance,
                        'closing_balance' => $adm_balance - $profit,
                        'remark' => "",
                        'match_id' => $mid,
                        'bet_user_id' => $id,
                        'user_exposure_log_id' => $betModel->id
                    ]);

                    $adm_balance = $adm_balance - $profit;

                    if($calculated_commission > 0) {
                        UsersAccount::create([
                            'user_id' => $id,
                            'from_user_id' => $id,
                            'to_user_id' => $id,
                            'debit_amount' => $calculated_commission,
                            'balance' => $available_balance,
                            'closing_balance' => $available_balance - $calculated_commission,
                            'remark' => "Commission",
                            'match_id' => $mid,
                            'bet_user_id' => $id,
                            'user_exposure_log_id' => $betModel->id
                        ]);

                        UsersAccount::create([
                            'user_id' => $masterAdmin->id,
                            'from_user_id' => $id,
                            'to_user_id' => $masterAdmin->id,
                            'credit_amount' => $calculated_commission,
                            'balance' => $adm_balance,
                            'closing_balance' => $adm_balance + $calculated_commission,
                            'remark' => "Commission",
                            'match_id' => $mid,
                            'bet_user_id' => $id,
                            'user_exposure_log_id' => $betModel->id
                        ]);

                        $adm_balance = $adm_balance + $calculated_commission;
                    }

                    $upd = CreditReference::find($creditref['id']);
                    $upd->exposure = $exposer;
                    $upd->available_balance_for_D_W = $balance;
                    $upd->remain_bal = $remain_balance;
                    $update_ = $upd->update();

                    if ($update_) {
                        //update commission on my player's parent account
                        if ($my_parent > 1) {
//                            $creditref_bal = CreditReference::where(['player_id' => $my_parent])->first();
//                            $bal = $creditref_bal->remain_bal;
//                            $available_balance = $creditref_bal->available_balance_for_D_W;
//
//                            $upd_ = CreditReference::find($creditref_bal->id);
//                            //$upd_->available_balance_for_D_W =$available_balance+$calculated_commission; //removed commission from other parent except main company
//                            $upd_->available_balance_for_D_W = $available_balance;
//                            $update_parent = $upd_->update();
                        } else {
                            if($calculated_commission > 0) {
                                $setting = setting::latest('id')->first();
                                $balance = $setting->balance;
                                $new_balance = $balance + $calculated_commission;

                                $adminData = setting::find($setting->id);
                                $adminData->balance = $new_balance;
                                $adminData->update();
                            }
                        }
                        //end for updating commission on player's parent account

                        $parentid = self::GetAllParentofPlayer($id);
                        $parentid = json_decode($parentid);
                        if (!empty($parentid)) {
                            for ($i = 0; $i < sizeof($parentid); $i++) {
                                $pid = $parentid[$i];
                                if ($pid != 1) {
                                    $creditref_bal = CreditReference::where(['player_id' => $pid])->first();
                                    $bal = $creditref_bal->remain_bal;
                                    $remain_balance_ = $bal + $profit;

                                    $upd_ = CreditReference::find($creditref_bal->id);
                                    $upd_->remain_bal = $remain_balance_;
                                    $update_parent = $upd_->update();
                                }
                            }
                        }
                    }
                }
                else {
                    $creditref = CreditReference::where(['player_id' => $id])->first();
                    $exposer = $creditref->exposure - abs($loss);
                    $balance = $creditref->available_balance_for_D_W;
                    $remain_balance = $creditref->remain_bal - abs($loss);


                    $available_balance = $creditref->available_balance_for_D_W;
                    UsersAccount::create([
                        'user_id' => $id,
                        'from_user_id' => $id,
                        'to_user_id' => $id,
                        'debit_amount' => abs($loss),
                        'balance' => $available_balance,
                        'closing_balance' => $available_balance - abs($loss),
                        'remark' => "",
                        'match_id' => $mid,
                        'bet_user_id' => $id,
                        'user_exposure_log_id' => $betModel->id
                    ]);

                    UsersAccount::create([
                        'user_id' => $masterAdmin->id,
                        'from_user_id' => $id,
                        'to_user_id' => $masterAdmin->id,
                        'credit_amount' => abs($loss),
                        'balance' => $adm_balance,
                        'closing_balance' => $adm_balance + abs($loss),
                        'remark' => "",
                        'match_id' => $mid,
                        'bet_user_id' => $id,
                        'user_exposure_log_id' => $betModel->id
                    ]);

                    $adm_balance = $adm_balance + abs($loss);


                    $upd = CreditReference::find($creditref['id']);
                    $upd->exposure = $exposer;
                    $upd->available_balance_for_D_W = $balance;
                    $upd->remain_bal = $remain_balance;
                    $update_ = $upd->update();

                    if ($update_) {
                        $parentid = self::GetAllParentofPlayer($id);
                        $parentid = json_decode($parentid);
                        if (!empty($parentid)) {
                            for ($i = 0; $i < sizeof($parentid); $i++) {
                                $pid = $parentid[$i];
                                if ($pid != 1) {
                                    $creditref_bal = CreditReference::where(['player_id' => $pid])->first();
                                    $bal = $creditref_bal->remain_bal;
                                    $remain_balance_ = $bal - abs($loss);
                                    $upd_ = CreditReference::find($creditref_bal->id);
                                    $upd_->remain_bal = $remain_balance_;
                                    $update_parent = $upd_->update();
                                }

                            }
                        }
                    }
                }
            }
        }
        else if ($winner == ucfirst($team2_name)) {
            $profit = '';
            $loss = '';
            $is_won = 0;
            if ($team2_bet_total >= 0) {
                $is_won = 1;
                $profit = $team2_bet_total;
                if ($team1_bet_total < 0)
                    $loss = $team1_bet_total;
                else
                    $loss = 0;
            } else {
                $loss = $team2_bet_total;

                if($team1_bet_total > 0)
                    $profit = $team1_bet_total;
                else
                    $profit = 0;
            }

//            dd($team1_bet_total, $team2_bet_total, $team_draw_bet_total, $loss, $profit);

            $betModel = new UserExposureLog();
            $betModel->match_id = $mid;
            $betModel->user_id = $id;
            $betModel->bet_type = $bettype;
            $betModel->profit = $profit;
            $betModel->loss = abs($loss);
            if ($is_won == 1)
                $betModel->win_type = 'Profit';
            else
                $betModel->win_type = 'Loss';
            $check = $betModel->save();
            if ($check) {
                if ($is_won == 1 && $is_won != '') {
                    $calculated_commission = 0;
                    $user_detail = User::where(['id' => $id])->first();
                    $my_parent = $user_detail->parentid;
                    $get_commission = $user_detail->commission;
                    if ($betModel->bet_type == "ODDS")
                        $calculated_commission = round(($profit * $get_commission) / 100, 2);

                    $creditref = CreditReference::where(['player_id' => $id])->first();
                    $exposer = $creditref->exposure - abs($loss);
                    $balance = $creditref->available_balance_for_D_W + abs($loss) + $profit - $calculated_commission;
                    $remain_balance = $creditref->remain_bal + $profit - $calculated_commission;

                    $available_balance = $creditref->available_balance_for_D_W;
                    UsersAccount::create([
                        'user_id' => $id,
                        'from_user_id' => $id,
                        'to_user_id' => $id,
                        'credit_amount' => $profit,
                        'balance' => $available_balance,
                        'closing_balance' => $available_balance + $profit,
                        'remark' => "",
                        'match_id' => $mid,
                        'bet_user_id' => $id,
                        'user_exposure_log_id' => $betModel->id
                    ]);

                    $available_balance = $available_balance + $profit;

                    UsersAccount::create([
                        'user_id' => $masterAdmin->id,
                        'from_user_id' => $id,
                        'to_user_id' => $masterAdmin->id,
                        'debit_amount' => $profit,
                        'balance' => $adm_balance,
                        'closing_balance' => $adm_balance - $profit,
                        'remark' => "",
                        'match_id' => $mid,
                        'bet_user_id' => $id,
                        'user_exposure_log_id' => $betModel->id
                    ]);

                    $adm_balance = $adm_balance - $profit;

                    if($calculated_commission > 0) {
                        UsersAccount::create([
                            'user_id' => $id,
                            'from_user_id' => $id,
                            'to_user_id' => $id,
                            'debit_amount' => $calculated_commission,
                            'balance' => $available_balance,
                            'closing_balance' => $available_balance - $calculated_commission,
                            'remark' => "Commission",
                            'match_id' => $mid,
                            'bet_user_id' => $id,
                            'user_exposure_log_id' => $betModel->id
                        ]);

                        UsersAccount::create([
                            'user_id' => $masterAdmin->id,
                            'from_user_id' => $id,
                            'to_user_id' => $masterAdmin->id,
                            'credit_amount' => $calculated_commission,
                            'balance' => $adm_balance,
                            'closing_balance' => $adm_balance + $calculated_commission,
                            'remark' => "Commission",
                            'match_id' => $mid,
                            'bet_user_id' => $id,
                            'user_exposure_log_id' => $betModel->id
                        ]);

                        $adm_balance = $adm_balance + $calculated_commission;
                    }


                    $upd = CreditReference::find($creditref['id']);
                    $upd->exposure = $exposer;
                    $upd->available_balance_for_D_W = $balance;
                    $upd->remain_bal = $remain_balance;
                    $update_ = $upd->update();

                    if ($update_) {
                        //update commission on my player's parent account
                        if ($my_parent > 1) {
//                            $creditref_bal = CreditReference::where(['player_id' => $my_parent])->first();
//                            $bal = $creditref_bal->remain_bal;
//                            $available_balance = $creditref_bal->available_balance_for_D_W;
//
//                            $upd_ = CreditReference::find($creditref_bal->id);
//                            //$upd_->available_balance_for_D_W =$available_balance+$calculated_commission; //removed commission from other parent except main company
//                            $upd_->available_balance_for_D_W = $available_balance;
//                            $update_parent = $upd_->update();
                        } else {
                            if($calculated_commission > 0) {
                                $setting = setting::latest('id')->first();
                                $balance = $setting->balance;
                                $new_balance = $balance + $calculated_commission;

                                $adminData = setting::find($setting->id);
                                $adminData->balance = $new_balance;
                                $adminData->update();
                            }
                        }
                        //end for updating commission on player's parent account

                        $parentid = self::GetAllParentofPlayer($id);
                        $parentid = json_decode($parentid);
                        if (!empty($parentid)) {
                            for ($i = 0; $i < sizeof($parentid); $i++) {
                                $pid = $parentid[$i];
                                if ($pid != 1) {
                                    $creditref_bal = CreditReference::where(['player_id' => $pid])->first();
                                    $bal = $creditref_bal->remain_bal;
                                    $remain_balance_ = $bal + $profit;
                                    $upd_ = CreditReference::find($creditref_bal->id);
                                    $upd_->remain_bal = $remain_balance_;
                                    $update_parent = $upd_->update();
                                }
                            }
                        }
                    }
                }
                else {
                    $creditref = CreditReference::where(['player_id' => $id])->first();
                    $exposer = $creditref->exposure - abs($loss);
                    $balance = $creditref->available_balance_for_D_W;
                    $remain_balance = $creditref->remain_bal - abs($loss);

                    $available_balance = $creditref->available_balance_for_D_W;
                    UsersAccount::create([
                        'user_id' => $id,
                        'from_user_id' => $id,
                        'to_user_id' => $id,
                        'debit_amount' => abs($loss),
                        'balance' => $available_balance,
                        'closing_balance' => $available_balance - abs($loss),
                        'remark' => "",
                        'match_id' => $mid,
                        'bet_user_id' => $id,
                        'user_exposure_log_id' => $betModel->id
                    ]);

                    UsersAccount::create([
                        'user_id' => $masterAdmin->id,
                        'from_user_id' => $id,
                        'to_user_id' => $masterAdmin->id,
                        'credit_amount' => abs($loss),
                        'balance' => $adm_balance,
                        'closing_balance' => $adm_balance + abs($loss),
                        'remark' => "",
                        'match_id' => $mid,
                        'bet_user_id' => $id,
                        'user_exposure_log_id' => $betModel->id
                    ]);

                    $upd = CreditReference::find($creditref['id']);
                    $upd->exposure = $exposer;
                    $upd->available_balance_for_D_W = $balance;
                    $upd->remain_bal = $remain_balance;
                    $update_ = $upd->update();

                    if ($update_) {
                        $parentid = self::GetAllParentofPlayer($id);
                        $parentid = json_decode($parentid);
                        if (!empty($parentid)) {
                            for ($i = 0; $i < sizeof($parentid); $i++) {
                                $pid = $parentid[$i];
                                if ($pid != 1) {
                                    $creditref_bal = CreditReference::where(['player_id' => $pid])->first();
                                    $bal = $creditref_bal->remain_bal;
                                    $remain_balance_ = $bal - abs($loss);
                                    $upd_ = CreditReference::find($creditref_bal->id);
                                    $upd_->remain_bal = $remain_balance_;
                                    $update_parent = $upd_->update();
                                }
                            }
                        }
                    }
                }
            }
        }
        else if ($winner == ucfirst($team_draw_name)) {
            $profit = '';
            $loss = '';
            $is_won = 0;
            if ($team_draw_bet_total >= 0) {
                $is_won = 1;
                $profit = $team_draw_bet_total;
                if ($team1_bet_total < 0)
                    $loss = $team1_bet_total;
                else
                    $loss = 0;
            } else {
                $loss = $team_draw_bet_total;

                if($team1_bet_total > 0)
                    $profit = $team1_bet_total;
                else
                    $profit = 0;
            }

//            dd($team1_bet_total, $team2_bet_total, $team_draw_bet_total, $loss, $profit);

            $betModel = new UserExposureLog();
            $betModel->match_id = $mid;
            $betModel->user_id = $id;
            $betModel->bet_type = $bettype;
            $betModel->profit = $profit;
            $betModel->loss = abs($loss);
            if ($is_won == 1)
                $betModel->win_type = 'Profit';
            else
                $betModel->win_type = 'Loss';
            $check = $betModel->save();
            if ($check) {
                if ($is_won == 1 && $is_won != '') {
                    $calculated_commission = 0;
                    $user_detail = User::where(['id' => $id])->first();
                    $my_parent = $user_detail->parentid;
                    $get_commission = $user_detail->commission;

                    if ($betModel->bet_type == "ODDS")
                        $calculated_commission = round(($profit * $get_commission) / 100, 2);

                    $creditref = CreditReference::where(['player_id' => $id])->first();
                    $exposer = $creditref->exposure - abs($loss);
                    $balance = $creditref->available_balance_for_D_W + abs($loss) + $profit - $calculated_commission;
                    $remain_balance = $creditref->remain_bal + $profit - $calculated_commission;

                    $available_balance = $creditref->available_balance_for_D_W;
                    UsersAccount::create([
                        'user_id' => $id,
                        'from_user_id' => $id,
                        'to_user_id' => $id,
                        'credit_amount' => $profit,
                        'balance' => $available_balance,
                        'closing_balance' => $available_balance + $profit,
                        'remark' => "",
                        'match_id' => $mid,
                        'bet_user_id' => $id,
                        'user_exposure_log_id' => $betModel->id
                    ]);

                    $available_balance = $available_balance + $profit;

                    UsersAccount::create([
                        'user_id' => $masterAdmin->id,
                        'from_user_id' => $id,
                        'to_user_id' => $masterAdmin->id,
                        'debit_amount' => $profit,
                        'balance' => $adm_balance,
                        'closing_balance' => $adm_balance - $profit,
                        'remark' => "",
                        'match_id' => $mid,
                        'bet_user_id' => $id,
                        'user_exposure_log_id' => $betModel->id
                    ]);

                    $adm_balance = $adm_balance - $profit;

                    if($calculated_commission > 0) {
                        UsersAccount::create([
                            'user_id' => $id,
                            'from_user_id' => $id,
                            'to_user_id' => $id,
                            'debit_amount' => $calculated_commission,
                            'balance' => $available_balance,
                            'closing_balance' => $available_balance - $calculated_commission,
                            'remark' => "Commission",
                            'match_id' => $mid,
                            'bet_user_id' => $id,
                            'user_exposure_log_id' => $betModel->id
                        ]);

                        UsersAccount::create([
                            'user_id' => $masterAdmin->id,
                            'from_user_id' => $id,
                            'to_user_id' => $masterAdmin->id,
                            'credit_amount' => $calculated_commission,
                            'balance' => $adm_balance,
                            'closing_balance' => $adm_balance + $calculated_commission,
                            'remark' => "Commission",
                            'match_id' => $mid,
                            'bet_user_id' => $id,
                            'user_exposure_log_id' => $betModel->id
                        ]);

                        $adm_balance = $adm_balance + $calculated_commission;
                    }

                    $upd = CreditReference::find($creditref['id']);
                    $upd->exposure = $exposer;
                    $upd->available_balance_for_D_W = $balance;
                    $upd->remain_bal = $remain_balance;
                    $update_ = $upd->update();

                    if ($update_) {
                        //update commission on my player's parent account
                        if ($my_parent > 1) {
//                            $creditref_bal = CreditReference::where(['player_id' => $my_parent])->first();
//                            $bal = $creditref_bal->remain_bal;
//                            $available_balance = $creditref_bal->available_balance_for_D_W;
//
//                            $upd_ = CreditReference::find($creditref_bal->id);
//                            //$upd_->available_balance_for_D_W =$available_balance+$calculated_commission; //removed commission from other parent except main company
//                            $upd_->available_balance_for_D_W = $available_balance;
//                            $update_parent = $upd_->update();
                        } else {
                            if($calculated_commission > 0) {
                                $setting = setting::latest('id')->first();
                                $balance = $setting->balance;
                                $new_balance = $balance + $calculated_commission;

                                $adminData = setting::find($setting->id);
                                $adminData->balance = $new_balance;
                                $adminData->update();
                            }
                        }
                        //end for updating commission on player's parent account

                        $parentid = self::GetAllParentofPlayer($id);
                        $parentid = json_decode($parentid);
                        if (!empty($parentid)) {
                            for ($i = 0; $i < sizeof($parentid); $i++) {
                                $pid = $parentid[$i];
                                if ($pid != 1) {
                                    $creditref_bal = CreditReference::where(['player_id' => $pid])->first();
                                    $bal = $creditref_bal->remain_bal;
                                    $remain_balance_ = $bal + $profit;
                                    $upd_ = CreditReference::find($creditref_bal->id);
                                    $upd_->remain_bal = $remain_balance_;
                                    $update_parent = $upd_->update();
                                }
                            }
                        }
                    }
                } else {
                    $creditref = CreditReference::where(['player_id' => $id])->first();
                    $exposer = $creditref->exposure - abs($loss);
                    $balance = $creditref->available_balance_for_D_W;
                    $remain_balance = $creditref->remain_bal - abs($loss);

                    $available_balance = $creditref->available_balance_for_D_W;
                    UsersAccount::create([
                        'user_id' => $id,
                        'from_user_id' => $id,
                        'to_user_id' => $id,
                        'debit_amount' => abs($loss),
                        'balance' => $available_balance,
                        'closing_balance' => $available_balance - abs($loss),
                        'remark' => "",
                        'match_id' => $mid,
                        'bet_user_id' => $id,
                        'user_exposure_log_id' => $betModel->id
                    ]);

                    UsersAccount::create([
                        'user_id' => $masterAdmin->id,
                        'from_user_id' => $id,
                        'to_user_id' => $masterAdmin->id,
                        'credit_amount' => abs($loss),
                        'balance' => $adm_balance,
                        'closing_balance' => $adm_balance + abs($loss),
                        'remark' => "",
                        'match_id' => $mid,
                        'bet_user_id' => $id,
                        'user_exposure_log_id' => $betModel->id
                    ]);

                    $adm_balance = $adm_balance + abs($loss);

                    $upd = CreditReference::find($creditref['id']);
                    $upd->exposure = $exposer;
                    $upd->available_balance_for_D_W = $balance;
                    $upd->remain_bal = $remain_balance;
                    $update_ = $upd->update();

                    if ($update_) {
                        $parentid = self::GetAllParentofPlayer($id);
                        $parentid = json_decode($parentid);
                        if (!empty($parentid)) {
                            for ($i = 0; $i < sizeof($parentid); $i++) {
                                $pid = $parentid[$i];
                                if ($pid != 1) {
                                    $creditref_bal = CreditReference::where(['player_id' => $pid])->first();
                                    $bal = $creditref_bal->remain_bal;
                                    $remain_balance_ = $bal - abs($loss);
                                    $upd_ = CreditReference::find($creditref_bal->id);
                                    $upd_->remain_bal = $remain_balance_;
                                    $update_parent = $upd_->update();
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function decideMatchWinner(Request $request)
    {
        $mid = $request->matchid;
        $win = $request->winner;
        if(empty($win) || $win==null){
            echo 'Problem';exit();
        }

        $result = $this->updateMatchWinnerResult($mid, $win);

        echo $result['message'];

//        if ($match->fancy == 1) {
//            $match_bet = MyBets::where('match_id', $match->event_id)->where('bet_type', 'SESSION')->where('result_declare', 0)->get();
//            if (count($match_bet) > 0) {
//                foreach ($match_bet as $value) {
//                    $fancyName[] = $value->team_name;
//                }
//                $match_data = app('App\Http\Controllers\RestApi')->Fancy_and_Bookmaker_DetailCall($match->event_id, $match->match_id, $match->id);
//                $count = 1;
//                $i = 0;
//                if (!empty($match_data['fancy'])) {
//                    foreach ($match_data['fancy'] as $value1) {
//                        if (in_array($value1['nat'], $fancyName)) {
//                            $is_fancy_result_declare = FancyResult::where('fancy_name', $value1['nat'])->where('match_id', $mid)->get();
//                            if (count($is_fancy_result_declare) > 0) {
//                                $result_count = 0;
//                                $total_fancy = 0;
//                                $total_fancy = count($is_fancy_result_declare);
//                                foreach ($is_fancy_result_declare as $fan) {
//                                    if ($fan->result != '')
//                                        $result_count++;
//                                }
//                                if ($total_fancy > 0 && $result_count > 0) {
//
//                                    if ($win == 'TIE') {
//
//                                        $settingData = Match::find($mid);
//                                        $settingData->winner = ucfirst($win);
//                                        $upd = $settingData->update();
//                                        if ($upd) {
//                                            $bet = MyBets::where('match_id', $match->event_id)->where('bet_type', '!=', 'SESSION')->where('result_declare', 0)->groupby('user_id')->get();
//                                            foreach ($bet as $b) {
//
//                                                $exposer = SELF::getPlayerExAmountForTie($match->event_id, $b->user_id);
//                                                $getc = CreditReference::where('player_id', $b->user_id)->first();
//                                                $creid = $getc['id'];
//                                                $updc = CreditReference::find($creid);
//                                                $updc->exposure = $getc->exposure - $exposer;
//                                                $updc->available_balance_for_D_W = $getc->available_balance_for_D_W + $exposer;
//                                                $upd = $updc->update();
//                                                if ($upd) {
//                                                    $userData = MyBets::where("match_id", $match->event_id)->update(["result_declare" => 1]);
//                                                }
//
//                                            }
//                                            echo 'Success';
//                                            exit;
//                                        } else
//                                            echo 'Problem';
//                                        exit;
//                                    } else {
//                                        $settingData = Match::find($mid);
//                                        $settingData->winner = ucfirst($win);
//                                        $upd = $settingData->update();
//                                        if ($upd) {
//                                            $bet = MyBets::where('match_id', $match->event_id)->where('bet_type', 'ODDS')->where('result_declare', 0)->groupby('user_id')->get();
//                                            foreach ($bet as $b) {
//                                                $exposer = SELF::getExAmount($match->event_id, $b->user_id, ucfirst($win), $match->id, $match->match_name, 'ODDS');
//                                            }
//                                            $bet = MyBets::where('match_id', $match->event_id)->where('bet_type', 'BOOKMAKER')->where('result_declare', 0)->groupby('user_id')->get();
//                                            foreach ($bet as $b) {
//                                                $exposer = SELF::getExAmount($match->event_id, $b->user_id, ucfirst($win), $match->id, $match->match_name, 'BOOKMAKER');
//                                            }
//                                            //calculating admin balance
//                                            $admin_tran = UserExposureLog::where('match_id', $match->id)->get();
//                                            $admin_profit = 0;
//                                            $admin_loss = 0;
//                                            foreach ($admin_tran as $trans) {
//                                                if ($trans->profit != '' && $trans->win_type == 'Profit') {
//                                                    $admin_loss += abs($trans->profit);
//                                                } else if ($trans->loss != '' && $trans->win_type == 'Loss') {
//                                                    $admin_profit += abs($trans->loss);
//                                                }
//                                            }
//                                            $settings = setting::latest('id')->first();
//                                            $adm_balance = $settings->balance;
//                                            $new_balance = $adm_balance + $admin_profit - $admin_loss;
//
//                                            $adminData = setting::find($settings->id);
//                                            $adminData->balance = $new_balance;
//                                            $adminData->update();
//                                            //end for calculating admin balance
//
//                                            //update in my_bet table for bet winner
//                                            $updbet = MyBets::where('match_id', $match->event_id)->where('bet_type', '!=', 'SESSION')->get();
//                                            foreach ($updbet as $bet) {
//                                                $upd_bet = MyBets::find($bet->id);
//                                                $upd_bet->result_declare = 1;
//                                                $upd_bet->update();
//                                            }
//                                            //end for update in my_bet table for bet winner
//                                            echo 'Success';
//                                            exit;
//                                        } else {
//                                            echo 'Problem';
//                                            exit;
//                                        }
//                                    }
//                                } else {
//                                    echo 'Problem';
//                                    exit;
//                                }
//                            } else {
//                                echo 'Fail';
//                                exit;
//                            }
//                        }
//                        else {
//                            if ($win == 'TIE') {
//                                $settingData = Match::find($mid);
//                                $settingData->winner = ucfirst($win);
//                                $upd = $settingData->update();
//                                if ($upd) {
//                                    $bet = MyBets::where('match_id', $match->event_id)->where('bet_type', '!=', 'SESSION')->where('result_declare', 0)->groupby('user_id')->get();
//                                    foreach ($bet as $b) {
//
//                                        $exposer = SELF::getPlayerExAmountForTie($match->event_id, $b->user_id);
//                                        $getc = CreditReference::where('player_id', $b->user_id)->first();
//                                        $creid = $getc['id'];
//                                        $updc = CreditReference::find($creid);
//                                        $updc->exposure = $getc->exposure - $exposer;
//                                        $updc->available_balance_for_D_W = $getc->available_balance_for_D_W + $exposer;
//                                        $upd = $updc->update();
//                                        if ($upd) {
//                                            $userData = MyBets::where("match_id", $match->event_id)->update(["result_declare" => 1]);
//                                        }
//
//                                    }
//                                    echo 'Success';
//                                    exit;
//                                } else
//                                    echo 'Problem';
//                                exit;
//                            } else {
//                                $settingData = Match::find($mid);
//                                $settingData->winner = ucfirst($win);
//                                $upd = $settingData->update();
//                                if ($upd) {
//                                    $bet = MyBets::where('match_id', $match->event_id)->where('bet_type', 'ODDS')->where('result_declare', 0)->groupby('user_id')->get();
//                                    foreach ($bet as $b) {
//                                        $exposer = SELF::getExAmount($match->event_id, $b->user_id, ucfirst($win), $match->id, $match->match_name, 'ODDS');
//                                    }
//                                    $bet = MyBets::where('match_id', $match->event_id)->where('bet_type', 'BOOKMAKER')->where('result_declare', 0)->groupby('user_id')->get();
//                                    foreach ($bet as $b) {
//                                        $exposer = SELF::getExAmount($match->event_id, $b->user_id, ucfirst($win), $match->id, $match->match_name, 'BOOKMAKER');
//                                    }
//                                    //calculating admin balance
//                                    $admin_tran = UserExposureLog::where('match_id', $match->id)->get();
//                                    $admin_profit = 0;
//                                    $admin_loss = 0;
//                                    foreach ($admin_tran as $trans) {
//                                        if ($trans->profit != '' && $trans->win_type == 'Profit') {
//                                            $admin_loss += abs($trans->profit);
//                                        } else if ($trans->loss != '' && $trans->win_type == 'Loss') {
//                                            $admin_profit += abs($trans->loss);
//                                        }
//                                    }
//                                    $settings = setting::latest('id')->first();
//                                    $adm_balance = $settings->balance;
//                                    $new_balance = $adm_balance + $admin_profit - $admin_loss;
//
//                                    $adminData = setting::find($settings->id);
//                                    $adminData->balance = $new_balance;
//                                    $adminData->update();
//                                    //end for calculating admin balance
//
//                                    //update in my_bet table for bet winner
//                                    $updbet = MyBets::where('match_id', $match->event_id)->where('bet_type', '!=', 'SESSION')->get();
//                                    foreach ($updbet as $bet) {
//                                        $upd_bet = MyBets::find($bet->id);
//                                        $upd_bet->result_declare = 1;
//                                        $upd_bet->update();
//                                    }
//                                    //end for update in my_bet table for bet winner
//                                    echo 'Success';
//                                    exit;
//                                } else
//                                    echo 'Problem';
//                                exit;
//
//                            }
//                        }
//                    }
//                } else {
//                    if ($win == 'TIE') {
//                        $settingData = Match::find($mid);
//                        $settingData->winner = ucfirst($win);
//                        $upd = $settingData->update();
//                        if ($upd) {
//                            $bet = MyBets::where('match_id', $match->event_id)->where('bet_type', '!=', 'SESSION')->where('result_declare', 0)->groupby('user_id')->get();
//                            foreach ($bet as $b) {
//
//                                $exposer = SELF::getPlayerExAmountForTie($match->event_id, $b->user_id);
//                                $getc = CreditReference::where('player_id', $b->user_id)->first();
//                                $creid = $getc['id'];
//                                $updc = CreditReference::find($creid);
//                                $updc->exposure = $getc->exposure - $exposer;
//                                $updc->available_balance_for_D_W = $getc->available_balance_for_D_W + $exposer;
//                                $upd = $updc->update();
//                                if ($upd) {
//                                    $userData = MyBets::where("match_id", $match->event_id)->update(["result_declare" => 1]);
//                                }
//
//                            }
//                            echo 'Success';
//                            exit;
//                        } else
//                            echo 'Problem';
//                        exit;
//                    } else {
//                        $settingData = Match::find($mid);
//                        $settingData->winner = ucfirst($win);
//                        $upd = $settingData->update();
//                        if ($upd) {
//                            $bet = MyBets::where('match_id', $match->event_id)->where('bet_type', 'ODDS')->where('result_declare', 0)->groupby('user_id')->get();
//                            foreach ($bet as $b) {
//                                $exposer = SELF::getExAmount($match->event_id, $b->user_id, ucfirst($win), $match->id, $match->match_name, 'ODDS');
//                            }
//                            $bet = MyBets::where('match_id', $match->event_id)->where('bet_type', 'BOOKMAKER')->where('result_declare', 0)->groupby('user_id')->get();
//                            foreach ($bet as $b) {
//                                $exposer = SELF::getExAmount($match->event_id, $b->user_id, ucfirst($win), $match->id, $match->match_name, 'BOOKMAKER');
//                            }
//                            //calculating admin balance
//                            $admin_tran = UserExposureLog::where('match_id', $match->id)->get();
//                            $admin_profit = 0;
//                            $admin_loss = 0;
//                            foreach ($admin_tran as $trans) {
//                                if ($trans->profit != '' && $trans->win_type == 'Profit') {
//                                    $admin_loss += abs($trans->profit);
//                                } else if ($trans->loss != '' && $trans->win_type == 'Loss') {
//                                    $admin_profit += abs($trans->loss);
//                                }
//                            }
//                            $settings = setting::latest('id')->first();
//                            $adm_balance = $settings->balance;
//                            $new_balance = $adm_balance + $admin_profit - $admin_loss;
//
//                            $adminData = setting::find($settings->id);
//                            $adminData->balance = $new_balance;
//                            $adminData->update();
//                            //end for calculating admin balance
//
//                            //update in my_bet table for bet winner
//                            $updbet = MyBets::where('match_id', $match->event_id)->where('bet_type', '!=', 'SESSION')->get();
//                            foreach ($updbet as $bet) {
//                                $upd_bet = MyBets::find($bet->id);
//                                $upd_bet->result_declare = 1;
//                                $upd_bet->update();
//                            }
//                            //end for update in my_bet table for bet winner
//                            echo 'Success';
//                            exit;
//                        } else
//                            echo 'Problem';
//                        exit;
//                    }
//                }
//            }
//            else {
//                if ($win == 'TIE') {
//                    //echo 'dsfdsfdsf111';
//                    //exit;
//                    $settingData = Match::find($mid);
//                    $settingData->winner = ucfirst($win);
//                    $upd = $settingData->update();
//                    if ($upd) {
//                        $bet = MyBets::where('match_id', $match->event_id)->where('bet_type', '!=', 'SESSION')->where('result_declare', 0)->groupby('user_id')->get();
//                        foreach ($bet as $b) {
//
//                            $exposer = SELF::getPlayerExAmountForTie($match->event_id, $b->user_id);
//                            $getc = CreditReference::where('player_id', $b->user_id)->first();
//                            $creid = $getc['id'];
//                            $updc = CreditReference::find($creid);
//
//                            $updc->exposure = $getc->exposure - $exposer;
//                            $updc->available_balance_for_D_W = $getc->available_balance_for_D_W + $exposer;
//                            $upd = $updc->update();
//                            if ($upd) {
//                                $userData = MyBets::where("match_id", $match->event_id)->where('bet_type', '!=', 'SESSION')->update(["result_declare" => 1]);
//                            }
//
//                        }
//                        echo 'Success';
//                        exit;
//                    } else
//                        echo 'Problem';
//                    exit;
//                }
//                else {
//                    if ($win == 'TIE') {
//
//                        $settingData = Match::find($mid);
//                        $settingData->winner = ucfirst($win);
//                        $upd = $settingData->update();
//                        if ($upd) {
//                            $bet = MyBets::where('match_id', $match->event_id)->where('bet_type', '!=', 'SESSION')->where('result_declare', 0)->groupby('user_id')->get();
//                            foreach ($bet as $b) {
//
//                                $exposer = SELF::getPlayerExAmountForTie($match->event_id, $b->user_id);
//                                $getc = CreditReference::where('player_id', $b->user_id)->first();
//                                $creid = $getc['id'];
//                                $updc = CreditReference::find($creid);
//                                $updc->exposure = $getc->exposure - $exposer;
//                                $updc->available_balance_for_D_W = $getc->available_balance_for_D_W + $exposer;
//                                $upd = $updc->update();
//                                if ($upd) {
//                                    $userData = MyBets::where("match_id", $match->event_id)->update(["result_declare" => 1]);
//                                }
//
//                            }
//                            echo 'Success';
//                            exit;
//                        } else
//                            echo 'Problem';
//                        exit;
//                    }
//                    else {
//                        $settingData = Match::find($mid);
//                        $settingData->winner = ucfirst($win);
//                        $upd = $settingData->update();
//                        if ($upd) {
//                            $bet = MyBets::where('match_id', $match->event_id)->where('bet_type', 'ODDS')->where('result_declare', 0)->groupby('user_id')->get();
//                            foreach ($bet as $b) {
//                                $exposer = SELF::getExAmount($match->event_id, $b->user_id, ucfirst($win), $match->id, $match->match_name, 'ODDS');
//                            }
//                            $bet = MyBets::where('match_id', $match->event_id)->where('bet_type', 'BOOKMAKER')->where('result_declare', 0)->groupby('user_id')->get();
//                            foreach ($bet as $b) {
//                                $exposer = SELF::getExAmount($match->event_id, $b->user_id, ucfirst($win), $match->id, $match->match_name, 'BOOKMAKER');
//                            }
//                            //calculating admin balance
//                            $admin_tran = UserExposureLog::where('match_id', $match->id)->get();
//                            $admin_profit = 0;
//                            $admin_loss = 0;
//                            foreach ($admin_tran as $trans) {
//                                if ($trans->profit != '' && $trans->win_type == 'Profit') {
//                                    $admin_loss += abs($trans->profit);
//                                } else if ($trans->loss != '' && $trans->win_type == 'Loss') {
//                                    $admin_profit += abs($trans->loss);
//                                }
//                            }
//                            $settings = setting::latest('id')->first();
//                            $adm_balance = $settings->balance;
//                            $new_balance = $adm_balance + $admin_profit - $admin_loss;
//
//                            $adminData = setting::find($settings->id);
//                            $adminData->balance = $new_balance;
//                            $adminData->update();
//                            //end for calculating admin balance
//
//                            //update in my_bet table for bet winner
//                            $updbet = MyBets::where('match_id', $match->event_id)->where('bet_type', '!=', 'SESSION')->get();
//                            foreach ($updbet as $bet) {
//                                $upd_bet = MyBets::find($bet->id);
//                                $upd_bet->result_declare = 1;
//                                $upd_bet->update();
//                            }
//                            //end for update in my_bet table for bet winner
//                            echo 'Success';
//                            exit;
//                        } else
//                            echo 'Problem';
//                        exit;
//                    }
//                }
//            }
//        }
//        else

        exit;
    }

    public function updateMatchWinnerResult($mid, $win)
    {
        $match = Match::where('id', $mid)->first();

        if ($win == 'TIE') {
            $settingData = Match::find($mid);
            $settingData->winner = ucfirst($win);
            $upd = $settingData->update();
            if ($upd) {
                $bet = MyBets::where('match_id', $match->event_id)->where('bet_type', '!=', 'SESSION')->where('result_declare', 0)->groupby('user_id')->get();
//                $bet = MyBets::where('match_id', $match->event_id)->where('bet_type', '!=', 'SESSION')->where('user_id',6423)->where('result_declare', 0)->groupby('user_id')->get();

                foreach ($bet as $b) {

                    $exposer = SELF::getPlayerExAmountForTie($match->event_id, $b->user_id);

                    $getc = CreditReference::where('player_id', $b->user_id)->first();

                    $creid = $getc['id'];
                    $updc = CreditReference::find($creid);
                    $updc->exposure = $getc->exposure - $exposer;
                    $updc->available_balance_for_D_W = $getc->available_balance_for_D_W + $exposer;

                    if ($updc->save()) {
                        $userData = MyBets::where("match_id", $match->event_id)->where('user_id', $b->user_id)->update(["result_declare" => 1]);
                    }
                }
                return ['message' => 'Success'];
            } else {
                return ['message' => 'Problem'];
            }
        }
        else {
            $settingData = Match::find($mid);
            $settingData->winner = ucfirst($win);
            $upd = $settingData->update();
            if ($upd) {
                $bet = MyBets::where('match_id', $match->event_id)->where('bet_type', 'ODDS')->where('result_declare', 0)->groupby('user_id')->get();
                foreach ($bet as $b) {
                    $exposer = SELF::getExAmount($match->event_id, $b->user_id, ucfirst($win), $match->id, $match->match_name, 'ODDS');
                }
                $bet = MyBets::where('match_id', $match->event_id)->where('bet_type', 'BOOKMAKER')->where('result_declare', 0)->groupby('user_id')->get();
                foreach ($bet as $b) {
                    $exposer = SELF::getExAmount($match->event_id, $b->user_id, ucfirst($win), $match->id, $match->match_name, 'BOOKMAKER');
                }

                    //calculating admin balance
                    $admin_tran = UserExposureLog::where('match_id', $match->id)->get();
                    $admin_profit = 0;
                    $admin_loss = 0;
                    foreach ($admin_tran as $trans) {
                        if ($trans->profit != '' && $trans->win_type == 'Profit') {
                            $admin_loss += abs($trans->profit);

                        } else if ($trans->loss != '' && $trans->win_type == 'Loss') {
                            $admin_profit += abs($trans->loss);
                        }
                    }
                    $settings = setting::latest('id')->first();
                    $adm_balance = $settings->balance;
                    $new_balance = $adm_balance + $admin_profit - $admin_loss;

                    $adminData = setting::find($settings->id);
                    $adminData->balance = $new_balance;
                    $adminData->update();
                    //end for calculating admin balance


                //update in my_bet table for bet winner
                $updbet = MyBets::where("match_id", $match->event_id)->where('bet_type', '!=', 'SESSION')->update(["result_declare" => 1]);
//                    $updbet = MyBets::where('match_id', $match->event_id)->where('bet_type', '!=', 'SESSION')->get();
//                    foreach ($updbet as $bet) {
//                        $upd_bet = MyBets::find($bet->id);
//                        $upd_bet->result_declare = 1;
//                        $upd_bet->update();
//                    }
                //end for update in my_bet table for bet winner
                return ['message' => 'Success'];
            } else {
                return ['message' => 'Problem'];
            }
        }
    }

    public function risk_management_matchCallForFancyNBM($match_data_res, $matchId, $event_id, $loginUser, $website, $all_child, $match_f, $match_b, $team1_name, $team2_name, $sport)
    {
        $html_two = '';
        $html_two = '';
        $html = '';
        $team2_bet_total = 0;
        $team1_bet_total = 0;
        $team_draw_bet_total = 0;

        $eventId = $event_id;
        $match_detail = Match::where('event_id', $eventId)->first();

        $my_placed_bets = MyBets::where('match_id', $eventId)->where('bet_type', 'BOOKMAKER')->where('result_declare', 0)->whereIn('user_id', $all_child)->where('isDeleted', 0)->get();

        if (sizeof($my_placed_bets) > 0) {
            foreach ($my_placed_bets as $bet) {
                $abc = json_decode($bet->extra, true);
                if (count($abc) >= 2) {
                    if (array_key_exists("teamname1", $abc) && array_key_exists("teamname2", $abc)) {
                        //bet on draw
                        if ($bet->bet_side == 'back') {
                            $team1_bet_total = $team1_bet_total + $bet->exposureAmt;
                            if (count($abc) >= 2) {
                                $team_draw_bet_total = $team_draw_bet_total - $bet->bet_profit;
                            }
                            $team2_bet_total = $team2_bet_total + $bet->exposureAmt;

                        }
                        if ($bet->bet_side == 'lay') {
                            $team1_bet_total = $team1_bet_total - ($bet->bet_amount);
                            if (count($abc) >= 2) {
                                $team_draw_bet_total = $team_draw_bet_total + ($bet->exposureAmt);
                            }
                            $team2_bet_total = $team2_bet_total - ($bet->bet_amount);
                        }
                    } else if (array_key_exists("teamname3", $abc) && array_key_exists("teamname2", $abc)) {
                        //bet on team1
                        if ($bet->bet_side == 'back') {
                            $team1_bet_total = $team1_bet_total - $bet->bet_profit;
                            if (count($abc) >= 2) {
                                $team_draw_bet_total = $team_draw_bet_total + $bet->exposureAmt;
                            }
                            $team2_bet_total = $team2_bet_total + $bet->exposureAmt;
                        }
                        if ($bet->bet_side == 'lay') {
                            $team1_bet_total = $team1_bet_total + ($bet->exposureAmt);
                            if (count($abc) >= 2) {
                                $team_draw_bet_total = $team_draw_bet_total - ($bet->bet_amount);
                            }
                            $team2_bet_total = $team2_bet_total - ($bet->bet_amount);
                        }
                    } else if (array_key_exists("teamname3", $abc) && array_key_exists("teamname1", $abc)) {
                        //bet on team2
                        if ($bet->bet_side == 'back') {
                            $team2_bet_total = $team2_bet_total - ($bet->bet_profit);
                            if (count($abc) >= 2) {
                                $team_draw_bet_total = $team_draw_bet_total + $bet->exposureAmt;
                            }
                            $team1_bet_total = $team1_bet_total + $bet->exposureAmt;
                        }
                        if ($bet->bet_side == 'lay') {
                            $team2_bet_total = $team2_bet_total + $bet->exposureAmt;
                            if (count($abc) >= 2) {
                                $team_draw_bet_total = $team_draw_bet_total - $bet->bet_amount;
                            }
                            $team1_bet_total = $team1_bet_total - $bet->bet_amount;
                        }
                    }
                }
                else if (count($abc) == 1) {
                    if (array_key_exists("teamname1", $abc)) {
                        //bet on team2
                        if ($bet->bet_side == 'back') {
                            $team2_bet_total = $team2_bet_total - $bet->bet_profit;
                            $team1_bet_total = $team1_bet_total + $bet->exposureAmt;
                        }
                        if ($bet->bet_side == 'lay') {
                            $team2_bet_total = $team2_bet_total + $bet->exposureAmt;
                            $team1_bet_total = $team1_bet_total - $bet->bet_amount;
                        }
                    } else {
                        //bet on team1
                        if ($bet->bet_side == 'back') {
                            $team1_bet_total = $team1_bet_total - $bet->bet_profit;
                            $team2_bet_total = $team2_bet_total + $bet->exposureAmt;
                        }
                        if ($bet->bet_side == 'lay') {
                            $team1_bet_total = $team1_bet_total + $bet->exposureAmt;
                            $team2_bet_total = $team2_bet_total - $bet->bet_amount;
                        }
                    }
                }
            }
        }

        if (isset($match_data_res['t2'][0])) {
            $match_data = $match_data_res['t2'][0];

            //for bookmaker
            $team_name_array = array();
            $team_name_array[] = @$match_data['bm1'][0]['nat'];
            $team_name_array[] = @$match_data['bm1'][1]['nat'];
            $team_name_array[] = @$match_data['bm1'][2]['nat'];

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
                    $html .= '<tr class="fancy-suspend-tr">
						<td></td>
						<td class="fancy-suspend-td" colspan="6">
							<div class="fancy-suspend-1 black-bg-5 text-color-white"><span>SUSPENDED</span></div>
						</td>
					</tr>
					<tr class="beige-bg">
						<td class="padding3">' . @$match_data['bm1'][$team1_name]['nat'] . '<br>
						<div>
							<span class="lose" id="team1_betBM_count_old"></span>
							<span class="tolose text-color-red" style="display:none" id="team1_betBM_count_new">(6.7)</span>
						</div>
						</td>
						<td id="back_3" class="lightblue-bg4 back1 text-center">
							<p>  </p>
						</td>
						<td id="back_2" class="lightblue-bg5 back1 text-center">
							<p>  </p>
						</td>
						<td id="back_1" class="back1 backhover lightblue-bg3 text-center"><a class="cyan-bg">  </a></td>
						<td class="lay1 layhover lightpink-bg3 text-center" id="lay_1"><a class="pink-bg"> </a></td>
						<td class="lightpink-bg4 lay1 text-center" id="lay_2"><p>  </p></td>
						<td class="lightpink-bg5 lay1 text-center" id="lay_3"><p>  </p></td>
					</tr>
					<tr class="fancy-suspend-tr">
						<td></td>
						<td class="fancy-suspend-td" colspan="6">
							<div class="fancy-suspend-1 black-bg-5 text-color-white"><span>SUSPENDED</span></div>
						</td>
					</tr>
					<tr class="beige-bg">
						<td class="padding3">' . @$match_data['bm'][$team2_name]['nation'] . '<br>
						<div>
							<span class="lose" id="team1_betBM_count_old"></span>
							<span class="tolose text-color-red" style="display:none" id="team1_betBM_count_new">(6.7)</span>
						</div>
						</td>
						<td id="back_3" class="lightblue-bg4 back1 text-center">
							<p>  </p>
						</td>
						<td id="back_2" class="lightblue-bg5 back1 text-center">
							<p>  </p>
						</td>

						<td id="back_1" class="back1 backhover lightblue-bg3 text-center"><a class="cyan-bg">  </a></td>

						<td class="lay1 layhover lightpink-bg3 text-center" id="lay_1"><a class="pink-bg"> </a></td>

						<td class="lightpink-bg4 lay1 text-center" id="lay_2"><p>  </p></td>

						<td class="lightpink-bg5 lay1 text-center" id="lay_3"><p>  </p></td>

					</tr>';

                } else {

                    if (isset($match_data['bm1'][$team1_name]['s']) && $match_data['bm1'][$team1_name]['s'] != 'SUSPENDED') {

                        $display = '';
                        $cls = '';
                        if ($team1_bet_total == '')
                            $display = 'style="display:none"';

                        if ($team1_bet_total != '' && $team1_bet_total >= 0) {
                            $cls = 'text-color-green';
                        } else if ($team1_bet_total != '' && $team1_bet_total < 0) {
                            $cls = 'text-color-red';
                        }

                        $html .= '<tr class="beige-bg">
								<td class="padding3"><b>' . @$match_data['bm1'][$team1_name]['nat'] . '</b><br>
								<div>
									<span class="lose ' . $cls . '" ' . $display . ' id="team1_betBM_count_old">(<span id="team1_BM_total">' . round($team1_bet_total, 2) . '</span>)</span>
									<span class="tolose text-color-red" style="display:none" id="team1_betBM_count_new">(6.7)</span>
								</div>
								</td>
								<td id="back_3" class="lightblue-bg4 back1 text-center BmBack spark" data-team="team' . $match_data['bm1'][$team1_name]['sid'] . '">
								<a data-cls="cyan-bg" data-val="' . ($match_data['bm1'][$team1_name]['b3']) . '">' . ($match_data['bm1'][$team1_name]['b3']) . '<br><span>100</span></a>						</td>
							   <td id="back_2" class="lightblue-bg5 back1 text-center BmBack" data-team="team' . @$match_data['bm1'][$team1_name]['sid'] . '">
									<a data-cls="cyan-bg" data-val="' . ($match_data['bm1'][$team1_name]['b2']) . '">' . ($match_data['bm1'][$team1_name]['b2']) . '</a>						</td>
							   <td id="back_1" class="back1 backhover lightblue-bg3 text-center BmBack" data-team="team' . $match_data['bm1'][$team1_name]['sid'] . '">
							   <a data-cls="cyan-bg" data-val="' . ($match_data['bm1'][$team1_name]['b1']) . '">' . ($match_data['bm1'][$team1_name]['b1']) . '<br><span>100</span></a>	</td>
							<td id="lay_1"  class="sparkLay lay1 layhover lightpink-bg3 text-center BmLay" data-team="team' . @$match_data['bm1'][$team1_name]['sid'] . '">
							<a data-cls="pink-bg" data-val="' . ($match_data['bm1'][$team1_name]['l1']) . '">' . ($match_data['bm1'][$team1_name]['l1']) . '<br><span>100</span></a></td>

							<td id="lay_2" class="lightpink-bg4 lay1 text-center BmLay" data-team="team' . @$match_data['bm1'][$team1_name]['sid'] . '">
								<a data-cls="pink-bg"  data-val="' . (@$match_data['bm1'][$team1_name]['l2']) . '">' . (@$match_data['bm1'][$team1_name]['l2']) . '<br><span>100</span></a>						</td>

							<td id="lay_3" class="lightpink-bg5 lay1 text-center BmLay" data-team="team' . @$match_data['bm1'][$team1_name]['sid'] . '">
								<a data-cls="pink-bg" data-val="' . (@$match_data['bm1'][$team1_name]['l3']) . '">' . (@$match_data['bm1'][$team1_name]['l3']) . '<br><span>100</span></a>						</td>
							</tr>';
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

                        $html .= '<tr class="fancy-suspend-tr">
						<td></td>
						<td class="fancy-suspend-td" colspan="6">
							<div class="fancy-suspend black-bg-5 text-color-white"><span>SUSPENDED</span></div>
						</td>
					</tr>

					<tr class="beige-bg">
						<td class="padding3"><b>' . @$match_data['bm1'][$team1_name]['nat'] . '</b><br>
						<div>
							<span class="lose ' . $cls . '" ' . $display . ' id="team1_betBM_count_old">(<span id="team1_BM_total">' . round($team1_bet_total, 2) . '</span>)</span>
							<span class="tolose text-color-red" style="display:none" id="team1_betBM_count_new">(6.7)</span>
						</div>
						</td>
						<td id="back_3" class="lightblue-bg4 back1 text-center">
							<p>  </p>
						</td>

						<td id="back_2" class="lightblue-bg5 back1 text-center">

							<p>  </p>

						</td>

						<td id="back_1" class="back1 backhover lightblue-bg3 text-center"><a class="cyan-bg">  </a></td>

						<td class="lay1 layhover lightpink-bg3 text-center" id="lay_1"><a class="pink-bg"> </a></td>

						<td class="lightpink-bg4 lay1 text-center" id="lay_2"><p>  </p></td>

						<td class="lightpink-bg5 lay1 text-center" id="lay_3"><p>  </p></td>

					</tr>
					';
                    }

                    if (isset($match_data['bm1'][$team2_name]['s']) && @$match_data['bm1'][$team2_name]['s'] != 'SUSPENDED') {
                        if ($team2_bet_total == '')
                            $display = 'style="display:none"';
                        if ($team2_bet_total != '' && $team2_bet_total >= 0) {
                            $cls = 'text-color-green';
                        } else if ($team2_bet_total != '' && $team2_bet_total < 0) {
                            $cls = 'text-color-red';
                        }
                        $html .= '<tr class="beige-bg">

						<td class="padding3"><b>' . @$match_data['bm1'][$team2_name]['nat'] . '</b><br>

						<div>

							<span class="lose ' . $cls . '" ' . $display . ' id="team1_betBM_count_old">(<span id="team1_BM_total">' . round($team2_bet_total, 2) . '</span>)</span>

							<span class="tolose text-color-red" style="display:none" id="team1_betBM_count_new">(6.7)</span>

							</div>

						</td>

						<td id="back_3" class="spark lightblue-bg4 back1 text-center BmBack" data-team="team' . @$match_data['bm1'][$team2_name]['sid'] . '">
						<a data-cls="cyan-bg" data-val="' . (@$match_data['bm1'][$team2_name]['b3']) . '">' . (@$match_data['bm1'][$team2_name]['b3']) . '<br><span>100</span></a>						 </td>

						<td id="back_2" class="lightblue-bg5 back1 text-center BmBack" data-team="team' . @$match_data['bm1'][$team2_name]['sid'] . '">

							<a data-cls="cyan-bg" data-val="' . (@$match_data['bm1'][$team2_name]['b2']) . '">' . (@$match_data['bm1'][$team2_name]['b2']) . '<br><span>100</span></a>						</td>

					   <td id="back_1" class="back1 backhover lightblue-bg3 text-center BmBack" data-team="team' . @$match_data['bm1'][$team2_name]['sid'] . '">
					   <a data-cls="cyan-bg"  data-val="' . (@$match_data['bm1'][$team2_name]['b1']) . '">' . (@$match_data['bm1'][$team2_name]['b1']) . '<br><span>100</span></a>							</td>

						<td id="lay_1" class=" sparkLaylay1 layhover lightpink-bg3 text-center BmLay" data-team="team' . @$match_data['bm1'][$team2_name]['sid'] . '">
						<a  data-cls="pink-bg"  data-val="' . (@$match_data['bm1'][$team2_name]['l1']) . '">' . (@$match_data['bm1'][$team2_name]['l1']) . '<br><span>100</span></a></div>
						<td id="lay_2" class="lightpink-bg4 lay1 text-center BmLay" data-team="team' . @$match_data['bm1'][$team2_name]['sid'] . '">

							<a  data-cls="pink-bg" data-val="' . (@$match_data['bm1'][$team2_name]['l2']) . '">' . (@$match_data['bm1'][$team2_name]['l2']) . '<br><span>100</span></a>					   </td>

					   <td  id="lay_3" class="lightpink-bg5 lay1 text-center BmLay" data-team="team' . @$match_data['bm1'][$team2_name]['sid'] . '">

							<a  data-cls="pink-bg" data-val="' . (@$match_data['bm1'][$team2_name]['l3']) . '">' . (@$match_data['bm1'][$team2_name]['l3']) . '<br><span>100</span></a>						</td>
					</tr>';
                    } else {
                        if ($team2_bet_total == '')

                            $display = 'style="display:none"';
                        if ($team2_bet_total != '' && $team2_bet_total >= 0) {
                            $cls = 'text-color-green';
                        } else if ($team2_bet_total != '' && $team2_bet_total < 0) {
                            $cls = 'text-color-red';
                        }
                        $html .= '<tr class="fancy-suspend-tr">
						<td></td>
						<td class="fancy-suspend-td" colspan="6">
							<div class="fancy-suspend black-bg-5 text-color-white"><span>SUSPENDED</span></div>
						</td>
					</tr>
					<tr class="beige-bg">
						<td class="padding3"><b>' . @$match_data['bm1'][$team2_name]['nat'] . '</b><br>
						<div>
							<span class="lose ' . $cls . '" ' . $display . ' id="team1_betBM_count_old">(<span id="team1_BM_total">' . round($team2_bet_total, 2) . '</span>)</span>
							<span class="tolose text-color-red" style="display:none" id="team1_betBM_count_new">(6.7)</span>
						</div>
						</td>
						<td id="back_3" class="lightblue-bg4 back1 text-center">
							<p> </p>
						</td>
						<td id="back_2" class="lightblue-bg5 back1 text-center">
							<p> </p>
						</td>

						<td id="back_1" class="back1 backhover lightblue-bg3 text-center"><a class="cyan-bg"> </a></td>

						<td class="lay1 layhover lightpink-bg3 text-center" id="lay_1"><a class="pink-bg"> </a></td>

						<td class="lightpink-bg4 lay1 text-center" id="lay_2"><p> </p></td>

						<td class="lightpink-bg5 lay1 text-center" id="lay_3"><p> </p></td>
					</tr>';
                    }
                    if (isset($match_data['bm1'][$team3_name]['s'])) {
                        if (@$match_data['bm1'][$team3_name]['s'] != 'SUSPENDED') {
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

                            $html .= '<tr class="beige-bg">

							<td class="padding3"><b>' . @$match_data['bm1'][$team3_name]['nat'] . '</b><br>

							<div>

							<span class="lose ' . $cls . '" ' . $display . ' id="team1_betBM_count_old">(<span id="team1_BM_total">' . round($team_draw_bet_total, 2) . '</span>)</span>

							<span class="tolose text-color-red" style="display:none" id="team1_betBM_count_new">(6.7)</span>

							</div>

							</td>

							<td id="back_3" class="spark lightblue-bg4 back1 text-center BmBack" data-team="team' . @$match_data['bm1'][$team3_name]['sid'] . '">							<a data-cls="cyan-bg" data-val="' . (@$match_data['bm1'][$team3_name]['b3']) . '">' . (@$match_data['bm1'][$team3_name]['b3']) . '<br><span>100</span></a>						 </td>

							<td id="back_2" class="lightblue-bg5 back1 text-center BmBack" data-team="team' . @$match_data['bm1'][$team3_name]['sid'] . '">									<a data-cls="cyan-bg" data-val="' . (@$match_data['bm1'][$team3_name]['b2']) . '">' . (@$match_data['bm1'][$team3_name]['b2']) . '<br><span>100</span></a>							</td>

						   <td id="back_1" class="back1 backhover lightblue-bg3 text-center BmBack" data-team="team' . @$match_data['bm1'][$team3_name]['sid'] . '">						<a data-cls="cyan-bg" data-val="' . (@$match_data['bm1'][$team3_name]['b1']) . '">' . (@$match_data['bm1'][$team3_name]['b1']) . '<br><span>100</span></a>							</td>

							<td class=" sparkLaylay1 layhover lightpink-bg3 text-center" id="lay_1" class="BmLay" data-team="team' . @$match_data['bm1'][$team3_name]['sid'] . '">			<a data-cls="pink-bg" data-val="' . (@$match_data['bm1'][$team3_name]['l1']) . '">' . (@$match_data['bm1'][$team3_name]['l1']) . '<br><span>100</span></a>						</td>

						   <td class="lightpink-bg4 lay1 text-center" id="lay_2" class="BmLay" data-team="team' . @$match_data['bm1'][$team3_name]['sid'] . '">						<a data-cls="pink-bg" data-val="' . (@$match_data['bm1'][$team3_name]['l2']) . '">' . (@$match_data['bm1'][$team3_name]['l2']) . '<br><span>100</span></a>							</td>

						   <td class="lightpink-bg5 lay1 text-center" id="lay_3" class="BmLay" data-team="team' . @$match_data['bm1'][$team3_name]['sid'] . '">						<a data-cls="pink-bg" data-val="' . (@$match_data['bm1'][$team3_name]['l3']) . '">' . (@$match_data['bm1'][$team3_name]['l3']) . '<br><span>100</span></a>							</td>
						</tr>';
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

                            $html .= '<tr class="fancy-suspend-tr">
							<td></td>
							<td class="fancy-suspend-td" colspan="6">
								<div class="fancy-suspend black-bg-5 text-color-white"><span><b>' . @$match_data['bm1'][$team3_name]['s'] . '</b></span></div>
								<div>
									<span class="lose ' . $cls . '" ' . $display . ' id="team1_betBM_count_old"></span>
									<span class="tolose text-color-red" style="display:none" id="team1_betBM_count_new">(6.7)</span>
								</div>
							</td>
						</tr>

						<tr class="beige-bg">
							<td class="padding3">' . @$match_data['bm1'][$team3_name]['nat'] . '<br>(<span id="team1_BM_total">' . round($team_draw_bet_total, 2) . '</span>)</td>
							<td id="back_3" class="lightblue-bg4 back1 text-center">
								<p>  </p>
							</td>
							<td id="back_2" class="lightblue-bg5 back1 text-center">
								<p>  </p>
							</td>

							<td id="back_1" class="back1 backhover lightblue-bg3 text-center"><a class="cyan-bg">  </a></td>
							<td class="lay1 layhover lightpink-bg3 text-center" id="lay_1"><a class="pink-bg">  </a>
							<td class="lightpink-bg4 lay1 text-center" id="lay_2">
								<p>  </p>
							</td>
							<td class="lightpink-bg5 lay1 text-center" id="lay_3"><p>  </p></td>
						</tr>
						';
                        }
                    }
                } // end suspended if
            }
        }

        //for fancy
        if ($match_detail->fancy != 1) {
            if (isset($match_data['t3'][0]['b1']) != '') {
                $upd = Match::find($match_detail->id);
                $upd->fancy = 1;
                $upd->update();
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

        $match_data = $match_data_res;
        if (isset($match_data['t3'])) {

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

//                dd($sid);

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
                    $placed_bet = '';
                    $position = '';
                    $bet_model = '';
                    $abc = '';
                    $final_exposer = '';
                    //bet calculation
                    $loginUser = Auth::user();
                    $ag_id = $loginUser->id;
                    $all_child = $this->GetChildofAgent($ag_id);
                    $my_placed_bets = MyBets::where('match_id', $eventId)->where('team_name', @$nat[$sid[$i]])->where('bet_type', 'SESSION')->where('result_declare', 0)->where('isDeleted', 0)->whereIn('user_id', $all_child)->orderBy('created_at', 'asc')->get();
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
                        $bet_chk = '';
                        for ($kk = 0; $kk < sizeof($run_arr); $kk++) {
                            $bet_deduct_amt = 0;
                            $placed_bet_type = '';
                            foreach ($my_placed_bets as $bet) {
                                if ($bet->bet_side == 'lay') {
                                    if ($bet->bet_odds == $run_arr[$kk]) {
                                        $bet_deduct_amt = $bet_deduct_amt + $bet->bet_amount;
                                    } else if ($bet->bet_odds < $run_arr[$kk]) {
                                        $bet_deduct_amt = $bet_deduct_amt + $bet->bet_amount;

                                    } else if ($bet->bet_odds > $run_arr[$kk]) {
                                        $bet_deduct_amt = $bet_deduct_amt - $bet->exposureAmt;
                                    }
                                } else if ($bet->bet_side == 'back') {
                                    if ($bet->bet_odds == $run_arr[$kk]) {
                                        $bet_deduct_amt = $bet_deduct_amt - $bet->bet_profit;
                                    } else if ($bet->bet_odds < $run_arr[$kk]) {

                                        $bet_deduct_amt = $bet_deduct_amt - $bet->bet_profit;
                                    } else if ($bet->bet_odds > $run_arr[$kk]) {
                                        $bet_deduct_amt = $bet_deduct_amt + $bet->exposureAmt;
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
												<button type="button" class="close" data-dismiss="modal"><img src="' . asset('asset/front/img/close-icon.png') . '" alt=""></button>
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

                    $display = '';
                    $cls = '';

                    if ($bet_model == '') {
                        $display = 'style="display:block"';
                    }
                    if ($bet_model != '') {
                        $cls = 'text-color-red';
                    }
                    //end for bet calculation

                    $html_two .= '
						<tr class="fancy-suspend-tr">
						<td></td>
						<td class="fancy-suspend-td" colspan="6">
							<div class="fancy-suspend-half black-bg-5 text-color-white"><span>SUSPENDED</span></div>
						</td>
						</tr>

						<tr class="rf_tr">
							<td class="text-left">
								<b>
                                 	<p class="mb-0">' . $nat[$sid[$i]] . '
										<div><a data-toggle="modal" data-target="#runPosition' . $i . '">
											<span class="lose ' . $cls . '" ' . $display . ' id="Fancy_Total_Div"><span id="Fancy_Total">' . $final_exposer . '</span></span>
										</a>
										' . $bet_model . '
                                   	</p>
                               	</b>
							</td>
							<td class="lay1 text-center"></td>
                          	<td class="lay1 text-center"></td>
                            <td class="lay1 layhover lightpink-bg3 text-center">
                            	<b>--</b> <span>--</span>
                           	</td>
                            <td class="back1 backhover lightblue-bg3 text-center">
                            	<b>--</b> <span>--</span>
                           	</td>
                            <td class="back1 text-center"><span></span></td>
                            <td class="back1 text-center"></td>
						</tr>';

                } else {
                    $placed_bet = '';
                    $position = '';
                    $bet_model = '';
                    $abc = '';
                    $final_exposer = '';
                    if ($gstatus[$sid[$i]] != 'Ball Running' && $gstatus[$sid[$i]] != 'SUSPENDED' && $l[$sid[$i]] != 0 && round($b[$sid[$i]]) != 0) {

                        if ($l[$sid[$i]] != 0 && round($b[$sid[$i]]) != 0) {
                            //bet calculation
                            $loginUser = Auth::user();
                            $ag_id = $loginUser->id;
                            $all_child = $this->GetChildofAgent($ag_id);
                            $my_placed_bets = MyBets::where('match_id', $eventId)->where('team_name', @$nat[$sid[$i]])->where('bet_type', 'SESSION')->whereIn('user_id', $all_child)->where('result_declare', 0)->where('isDeleted', 0)->orderBy('created_at', 'asc')->get();
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
                                $bet_chk = '';
                                for ($kk = 0; $kk < sizeof($run_arr); $kk++) {
                                    $bet_deduct_amt = 0;
                                    $placed_bet_type = '';
                                    foreach ($my_placed_bets as $bet) {
                                        if ($bet->bet_side == 'lay') {
                                            if ($bet->bet_odds == $run_arr[$kk]) {
                                                $bet_deduct_amt = $bet_deduct_amt + $bet->exposureAmt;
                                            } else if ($bet->bet_odds < $run_arr[$kk]) {
                                                $bet_deduct_amt = $bet_deduct_amt + $bet->exposureAmt;
                                            } else if ($bet->bet_odds > $run_arr[$kk]) {
                                                $bet_deduct_amt = $bet_deduct_amt - $bet->bet_amount;
                                            }
                                        }
                                        else if ($bet->bet_side == 'back') {
                                            if ($bet->bet_odds == $run_arr[$kk]) {
                                                $bet_deduct_amt = $bet_deduct_amt - $bet->bet_profit;
                                            } else if ($bet->bet_odds < $run_arr[$kk]) {

                                                $bet_deduct_amt = $bet_deduct_amt - $bet->bet_profit;
                                            } else if ($bet->bet_odds > $run_arr[$kk]) {
                                                $bet_deduct_amt = $bet_deduct_amt + $bet->exposureAmt;
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
													<button type="button" class="close" data-dismiss="modal"><img src="' . asset('asset/front/img/close-icon.png') . '" alt=""></button>
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
                            $display = '';
                            $cls = '';
                            if ($bet_model == '') {
                                $display = 'style="display:block"';
                            }
                            if ($bet_model != '') {
                                $cls = 'text-color-red';
                            }

                            //end for bet calculation

                            $html_two .= '<tr class="rf_tr">
								<td class="text-left">
									<b>
                                     	<p class="mb-0">' . $nat[$sid[$i]] . '
                                        	<div><a data-toggle="modal" data-target="#runPosition' . $i . '">
												<span class="lose ' . $cls . '" ' . $display . ' id="Fancy_Total_Div"><span id="Fancy_Total">' . $final_exposer . '</span></span>
											</a>
											' . $bet_model . '
											</div>
                                       	</p>
								</td>

								<td class="lay1 text-center"></td>
                              	<td class="lay1 text-center"></td>
                                <td class="lay1 layhover lightpink-bg3 text-center">
                                	<b>' . round($l[$sid[$i]]) . '</b> <span>' . round($ls[$sid[$i]]) . '</span>
                               	</td>
                                <td class="back1 backhover lightblue-bg3 text-center">
                                	<b>' . round($b[$sid[$i]]) . '</b> <span>' . round($bs[$sid[$i]]) . '</span>
                               	</td>
                                <td class="back1 text-center"></td>
                                <td class="back1 text-center"></td>
							</tr>';
                        }
                    }
                    else {
                        $placed_bet = '';
                        $position = '';
                        $bet_model = '';
                        $abc = '';
                        $final_exposer = '';
                        //bet calculation
                        $loginUser = Auth::user();
                        $ag_id = $loginUser->id;
                        $all_child = $this->GetChildofAgent($ag_id);
                        $my_placed_bets = MyBets::where('match_id', $eventId)->where('team_name', @$nat[$sid[$i]])->where('bet_type', 'SESSION')->whereIn('user_id', $all_child)->where('result_declare', 0)->where('isDeleted', 0)->orderBy('created_at', 'asc')->get();
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
                            $bet_chk = '';
                            for ($kk = 0; $kk < sizeof($run_arr); $kk++) {
                                $bet_deduct_amt = 0;
                                $placed_bet_type = '';
                                foreach ($my_placed_bets as $bet) {
                                    if ($bet->bet_side == 'lay') {
                                        if ($bet->bet_odds == $run_arr[$kk]) {
                                            $bet_deduct_amt = $bet_deduct_amt + $bet->exposureAmt;
                                        } else if ($bet->bet_odds < $run_arr[$kk]) {
                                            $bet_deduct_amt = $bet_deduct_amt + $bet->exposureAmt;
                                        } else if ($bet->bet_odds > $run_arr[$kk]) {
                                            $bet_deduct_amt = $bet_deduct_amt - $bet->bet_amount;
                                        }
                                    } else if ($bet->bet_side == 'back') {
                                        if ($bet->bet_odds == $run_arr[$kk]) {
                                            $bet_deduct_amt = $bet_deduct_amt - $bet->bet_profit;
                                        } else if ($bet->bet_odds < $run_arr[$kk]) {
                                            $bet_deduct_amt = $bet_deduct_amt - $bet->bet_profit;
                                        } else if ($bet->bet_odds > $run_arr[$kk]) {
                                            $bet_deduct_amt = $bet_deduct_amt + $bet->exposureAmt;
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
													<button type="button" class="close" data-dismiss="modal"><img src="' . asset('asset/front/img/close-icon.png') . '" alt=""></button>
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

                        $display = '';
                        $cls = '';

                        if ($bet_model == '') {
                            $display = 'style="display:block"';
                        }
                        if ($bet_model != '') {
                            $cls = 'text-color-red';
                        }

                        //end for bet calculation

                        $html_two .= '<tr class="fancy-suspend-tr-1">
							<td></td>
							<td></td>
							<td></td>
							<td class="fancy-suspend-td-1" colspan="2">
                                <div class="fancy-suspend-1 black-bg-5 text-color-white"><span>' . strtoupper($gstatus[$sid[$i]]) . '</span></div>
                            </td>
                        </tr>
						<tr class="rf_tr white-bg">
                            <td>
                                <b>
                                    <p class="mb-0">' . $nat[$sid[$i]] . '
												<div><a data-toggle="modal" data-target="#runPosition' . $i . '">
												<span class="lose ' . $cls . '" ' . $display . ' id="Fancy_Total_Div"><span id="Fancy_Total">' . $final_exposer . '</span></span>
												</a>' . $bet_model . '
                                    </p>
                                </b>
                            </td>
							<td class="lay1 text-center"></td>
									<td class="lay1 text-center"></td>
									<td class="lay1 layhover lightpink-bg3 text-center">
										<b></b> <span></span>
									</td>
									<td class="back1 backhover lightblue-bg3 text-center">
										<b></b> <span></span>
									</td>
									<td class="back1 text-center"></td>
									<td class="back1 text-center"></td>
                     	</tr>';
                        if ($gstatus[$sid[$i]] == "") {
                            $html_two .= '
								<tr class="rf_tr">
									<td class="text-left">
										<b>
											<p class="mb-0">' . $nat[$sid[$i]] . '
												<div><a data-toggle="modal" data-target="#runPosition' . $i . '">
												<span class="lose ' . $cls . '" ' . $display . ' id="Fancy_Total_Div"><span id="Fancy_Total">' . $final_exposer . '</span></span>
												</a>' . $bet_model . '
											</p>
										</b>
									</td>
									<td class="lay1 text-center"></td>
									<td class="lay1 text-center"></td>
									<td class="lay1 layhover lightpink-bg3 text-center">
										<b></b> <span></span>
									</td>
									<td class="back1 backhover lightblue-bg3 text-center">
										<b></b> <span></span>
									</td>
									<td class="back1 text-center"></td>
									<td class="back1 text-center"></td>
								</tr>';
                        }
                    }

                } // end suspended if
            }
        }

        return ['boomaker' => $html, 'fancy' => $html_two];
    }

    public function saveMatchSuspend(Request $request)
    {
        $suspend = $request->suspend;
        $fid = $request->fid;
        $status = $request->status;
        $settingData = Match::find($fid);
        $settingData->$status = $suspend;
        $upd = $settingData->update();
        return response()->json(array('success' => 'success'));
    }

    public function downline_list()
    {
        $getuser = Auth::user();
        if ($getuser->agent_level == 'SL' && $getuser->parentid == 1) {
            $agent = User::where('parentid', 1)->where('agent_level', '!=', 'PL')->get();
            $player = User::where('parentid', 1)->where('agent_level', 'PL')->get();
        } else {
            $agent = User::where('parentid', $getuser->id)->where('agent_level', '!=', 'PL')->get();
            $player = User::where('parentid', $getuser->id)->where('agent_level', 'PL')->get();
        }
        return view('backpanel/downline_list', compact('agent', 'player'));
    }

    public function getAgentChildAgent(Request $request)
    {
        $html = '';
        $agent = User::where('parentid', $request->mid)->where('agent_level', '!=', 'PL')->get();
        $player = User::where('parentid', $request->mid)->where('agent_level', 'PL')->get();

        foreach ($agent as $agentData) {
            if ($agentData->agent_level == 'SA') {
                $color = 'orange-bg';
            } else if ($agentData->agent_level == 'AD') {
                $color = 'pink-bg';
            } else if ($agentData->agent_level == 'SMDL') {
                $color = 'green-bg';
            } else if ($agentData->agent_level == 'MDL') {
                $color = 'yellow-bg';
            } else if ($agentData->agent_level == 'DL') {
                $color = 'blue-bg';
            } else {
                $color = 'red-bg';
            }
            $html .= '<tr>
	            <td class="align-L white-bg"><a onclick="get_mychild(' . $agentData->id . ')" class="ico_account text-color-blue-light"><span class="' . $color . ' text-color-white">' . $agentData->agent_level . '</span>' . $agentData->first_name . '(' . $agentData->user_name . ')</a></td>

	            <td class="credit-amount-member white-bg"><a class="favor-set text-color-blue-light" data-toggle="modal" data-target="#myModal">0</a></td>
	            <td class="white-bg">378.46</td>
	            <td class="text-color-red white-bg" style="display: table-cell;">(0.00)</td>
	            <td class="text-color-green white-bg">378.46</td>
	            <td class="text-color-red white-bg">(633.54)</td>
	            <td class="white-bg">
	                <span class="status-active light-green-bg text-color-green"><span class="round-circle green-bg"></span>Active</span>
	            </td>
	            <td class="white-bg">
	                <ul class="action-ul">
	                    <li><a class="grey-gradient-bg" data-toggle="modal" data-target="#myStatus"><img src="' . asset('asset/img/setting-icon.png') . '"></a></li>
	                    <li><a class="grey-gradient-bg" href="' . route('changePass', $agentData->id) . '"><img src="' . asset('asset/img/user-icon.png') . '"></a></li>
	                    <li><a class="grey-gradient-bg"><img src="' . asset('asset/img/updown-arrow-icon.png') . '"></a></li>
	                    <li><a class="grey-gradient-bg"><img src="' . asset('asset/img/history-icon.png') . '"></a></li>
	                </ul>
	            </td>
	        </tr>';
        }

        $html .= '~~';

        //player
        foreach ($player as $players) {
            $credit_data = CreditReference::where('player_id', $players->id)->select('credit')->first();
            $credit = 0;
            if ($credit_data['credit'] != '') {
                $credit = $credit_data['credit'];
            }
            $html .= '
		<tr>
            <td class="align-L white-bg"><a style="text-decoration:none !important" class="ico_account text-color-blue-light"><span class="orange-bg text-color-white">' . $players->agent_level . '</span>' . $players->first_name . " " . $players->last_name . ' [' . $players->user_name . '] </a></td>

            <td class="white-bg"><a id="' . $players->id . '" data-credit="' . $credit . '"  class="openCreditpopup favor-set">' . $credit . '</a></td>

            <td class="text-color-red white-bg" style="display: table-cell;">123</td>

            <td class="text-color-green white-bg">(0.00)</td>

             <td class="text-color-red white-bg" style="display: table-cell;">123</td>

              <td class="text-color-red white-bg" style="display: table-cell;">123</td>

               <td class="text-color-red white-bg" style="display: table-cell;">Active</td>

            	<td class="white-bg">

	                <ul class="action-ul">

	                    <li><a class="grey-gradient-bg" data-toggle="modal" data-target="#myStatus"><img src="' . asset('asset/img/setting-icon.png') . '"></a></li>

	                    <li><a class="grey-gradient-bg" href="' . route('changePass', $players->id) . '"><img src="' . asset('asset/img/user-icon.png') . '"></a></li>

	                    <li><a class="grey-gradient-bg"><img src="' . asset('asset/img/updown-arrow-icon.png') . '"></a></li>

	                    <li><a class="grey-gradient-bg"><img src="' . asset('asset/img/history-icon.png') . '"></a></li>

	                </ul>

	            </td>

	        </tr>';
        }
        return $html;
    }

    public function getAdminAgentBalance()
    {
        $loginuser = Auth::user();
        $ttuser = User::where('id', $loginuser->id)->first();

        $auth_id = $loginuser->id;
        $auth_type = $loginuser->agent_level;
        if ($auth_type == 'COM') {
            $settings = setting::latest('id')->first();
            $balance = $settings->balance;
        } else {
            $settings = CreditReference::where('player_id', $auth_id)->first();
            $balance = $settings['available_balance_for_D_W'];
        }
        echo $balance;
    }

    public function autoLogout()
    {
        $userData = Auth::user();
        $mntnc = setting::first();
        $sessionData = Session::get('adminUser');

        if ($userData->status == 'suspend') {
            Auth::logout();
            return response()->json(array('result' => 'suspendsuccess'));
        }
        if ($userData->agent_level != 'COM') {
            if (!empty($mntnc->maintanence_msg)) {
                Auth::logout();
                return response()->json(array('result' => 'msgsuccess'));
            }
        }
        $checkstatus = User::where('id', $sessionData->id)->first();
        if ($checkstatus->check_updpass != $sessionData->check_updpass) {
            Session::forget('adminUser');
            Auth::logout();
            return response()->json(array('result' => 'changePassLogout'));
        }
    }

    public function managetv()
    {
        $tv = ManageTv::latest()->first();
        return view('backpanel/managetv', compact('tv'));
    }

    public function addManageTv(Request $request)
    {
        $channel1 = '';
        $channel2 = '';
        $channel3 = '';
        $channel4 = '';
        $channel5 = '';
        $cs1 = '';
        $cs2 = '';
        $cs3 = '';
        $cs4 = '';
        $cs5 = '';

        if (empty($request->channel1)) {
            $channel1 = '';
        } else {
            $channel1 = $request->channel1;
        }
        if (empty($request->channel2)) {
            $channel2 = '';
        } else {
            $channel2 = $request->channel2;
        }
        if (empty($request->channel3)) {
            $channel3 = '';
        } else {
            $channel3 = $request->channel3;
        }
        if (empty($request->channel4)) {
            $channel4 = '';
        } else {
            $channel4 = $request->channel4;
        }
        if (empty($request->channel5)) {
            $channel5 = '';
        } else {
            $channel5 = $request->channel5;
        }
        if (empty($request->cs1)) {
            $cs1 = 'off';
        } else {
            $cs1 = $request->cs1;
        }
        if (empty($request->cs2)) {
            $cs2 = 'off';
        } else {
            $cs2 = $request->cs2;
        }
        if (empty($request->cs3)) {
            $cs3 = 'off';
        } else {
            $cs3 = $request->cs3;
        }
        if (empty($request->cs4)) {
            $cs4 = 'off';
        } else {
            $cs4 = $request->cs4;
        }
        if (empty($request->cs5)) {
            $cs5 = 'off';
        } else {
            $cs5 = $request->cs5;
        }

        $tvdata = ManageTv::latest()->first();
        if ($tvdata === null) {
            $data['channel1'] = $channel1;
            $data['channel2'] = $channel2;
            $data['channel3'] = $channel3;
            $data['channel4'] = $channel4;
            $data['channel5'] = $channel5;
            $data['cs1'] = $cs1;
            $data['cs2'] = $cs2;
            $data['cs3'] = $cs3;
            $data['cs4'] = $cs4;
            $data['cs5'] = $cs5;
            ManageTv::create($data);
        } else {
            ManageTv::where('id', 1)->update(
                [
                    'channel1' => $channel1,
                    'channel2' => $channel2,
                    'channel3' => $channel3,
                    'channel4' => $channel4,
                    'channel5' => $channel5,
                    'cs1' => $cs1,
                    'cs2' => $cs2,
                    'cs3' => $cs3,
                    'cs4' => $cs4,
                    'cs5' => $cs5
                ]
            );
        }
        return Redirect::back()->with('message', 'Channel added successfully.');
    }

    public function socialmedia()
    {
        $sm = SocialMedia::latest()->first();
        if (empty($sm)) {
            $sm = SocialMedia::create([
                'em1' => '', "em2" => '', "em3" => '', "wa1" => '', "wa2" => '', "wa3" => '', "tl1" => '', "tl2" => '', "tl3" => '', "ins1" => '', "ins2" => '', "ins3" => '', "sk1" => '', "sk2" => '', "sk3" => ''
            ]);
        }
        $banner = Banner::get();
        return view('backpanel/socialmedia', compact('sm', 'banner'));
    }

    public function banner()
    {
        $banner = Banner::get();
        return view('backpanel/banner', compact('banner'));
    }

    public function delBanner($id)
    {
        $banner = Banner::find($id);
        $banner->delete();
        return redirect()->route('banner')->with('message', 'Banner delete successfully');
    }

    public function addsocial(Request $request)
    {
        $em1 = '';
        $em2 = '';
        $em3 = '';
        $wa1 = '';
        $wa2 = '';
        $wa3 = '';
        $tl1 = '';
        $tl2 = '';
        $tl3 = '';
        $ins1 = '';
        $ins2 = '';
        $ins3 = '';
        $sk1 = '';
        $sk2 = '';
        $sk3 = '';

        if (empty($request->em1)) {
            $em1 = '';
        } else {
            $em1 = $request->em1;
        }
        if (empty($request->em2)) {
            $em2 = '';
        } else {
            $em2 = $request->em2;
        }
        if (empty($request->em3)) {
            $em3 = '';
        } else {
            $em3 = $request->em3;
        }

        if (empty($request->wa1)) {
            $wa1 = '';
        } else {
            $wa1 = $request->wa1;
        }
        if (empty($request->wa2)) {
            $wa2 = '';
        } else {
            $wa2 = $request->wa2;
        }
        if (empty($request->wa3)) {
            $wa3 = '';
        } else {
            $wa3 = $request->wa3;
        }

        if (empty($request->tl1)) {
            $tl1 = '';
        } else {
            $tl1 = $request->tl1;
        }
        if (empty($request->tl2)) {
            $tl2 = '';
        } else {
            $tl2 = $request->tl2;
        }
        if (empty($request->tl3)) {
            $tl3 = '';
        } else {
            $tl3 = $request->tl3;
        }

        if (empty($request->ins1)) {
            $ins1 = '';
        } else {
            $ins1 = $request->ins1;
        }
        if (empty($request->ins2)) {
            $ins2 = '';
        } else {
            $ins2 = $request->ins2;
        }
        if (empty($request->ins3)) {
            $ins3 = '';
        } else {
            $ins3 = $request->ins3;
        }

        if (empty($request->sk1)) {
            $sk1 = '';
        } else {
            $sk1 = $request->sk1;
        }
        if (empty($request->sk2)) {
            $sk2 = '';
        } else {
            $sk2 = $request->sk2;
        }
        if (empty($request->sk3)) {
            $sk3 = '';
        } else {
            $sk3 = $request->sk3;
        }
        $tvdata = SocialMedia::latest()->first();
        if ($tvdata === null) {

            $data['em1'] = $em1;
            $data['em2'] = $em2;
            $data['em3'] = $em3;

            $data['wa1'] = $wa1;
            $data['wa2'] = $wa2;
            $data['wa3'] = $wa3;

            $data['tl1'] = $tl1;
            $data['tl2'] = $tl2;
            $data['tl3'] = $tl3;

            $data['ins1'] = $ins1;
            $data['ins2'] = $ins2;
            $data['ins3'] = $ins3;

            $data['sk1'] = $sk1;
            $data['sk2'] = $sk2;
            $data['sk3'] = $sk3;
            SocialMedia::create($data);
        } else {
            SocialMedia::where('id', 1)->update(
                [
                    'em1' => $em1,
                    'em2' => $em2,
                    'em3' => $em3,

                    'wa1' => $wa1,
                    'wa2' => $wa2,
                    'wa3' => $wa3,

                    'tl1' => $tl1,
                    'tl2' => $tl2,
                    'tl3' => $tl3,

                    'ins1' => $ins1,
                    'ins2' => $ins2,
                    'ins3' => $ins3,

                    'sk1' => $sk1,
                    'sk2' => $sk2,
                    'sk3' => $sk3
                ]
            );
        }
        return Redirect::back()->with('message', 'Social Media added successfully.');
    }

    public function websetting()
    {
        $list = Website::get();
        return view('backpanel/websetting', compact('list'));
    }

    public function addWebsite(Request $request)
    {
        //echo"<pre>";print_r($request->all());echo"<pre>";exit;
        if ($request->hasFile('favicon')) {
            $imagefevicon = $request->file('favicon');
            $namefevicon = $imagefevicon->getClientOriginalName();
            $destinationPathfevicon = public_path('/asset/front/img');
            $imagefevicon->move($destinationPathfevicon, $namefevicon);
            $data['favicon'] = $namefevicon;
        }

        if ($request->hasFile('logo')) {
            $image = $request->file('logo');
            $name = $image->getClientOriginalName();
            $destinationPath = public_path('/asset/front/img');
            $image->move($destinationPath, $name);
            $data['logo'] = $name;
        }

        if ($request->hasFile('login_image')) {
            $imagelogin = $request->file('login_image');
            $namelogin = $imagelogin->getClientOriginalName();
            $destinationPathlogin = public_path('/asset/front/img');
            $imagelogin->move($destinationPathlogin, $namelogin);
            $data['login_image'] = $namelogin;
        }

        $data['title'] = $request->title;
        $data['domain'] = $request->domain;
        Website::create($data);
        return redirect()->route('websetting')->with('message', 'Web Site Added successfully.');
    }

    public function updateWebsetting(Request $request)
    {
        $mId = $request->fid;
        $chk = $request->chk;
        if ($chk != 1) {
            $ws = 0;
        } else {
            $ws = 1;
        }

        $upd = Website::find($mId);
        $upd->status = $ws;
        $upd->update();
        return response()->json(array('result' => 'success', 'message' => 'Status change successfully'));


    }

    public function updateWebsiteTheme(Request $request)
    {
        $id = $request->id;
        $theme_name = $request->theme_name;

        $upd = Website::find($id);
        $upd->themeClass = $theme_name;
        $upd->update();
        return response()->json(array('result' => 'success', 'message' => 'Theme change successfully'));


    }

    public function WebsettingData($id)
    {
        $list = Website::where('id', $id)->first();
        return view('backpanel/editwebsetting', compact('list'));
    }

    public function updateWebsettingData(Request $request)
    {
        //echo"<pre>";print_r($request->all());echo"<pre>";exit;
        $id = $request->id;
        $Website = Website::find($id);

        if ($request->hasFile('logo')) {
            $image = $request->file('logo');
            $name = $image->getClientOriginalName();
            $destinationPath = public_path('/asset/front/img');
            $image->move($destinationPath, $name);
            $data['logo'] = $name;
        }

        if ($request->hasFile('favicon')) {
            $imagefevicon = $request->file('favicon');
            $namefevicon = $imagefevicon->getClientOriginalName();
            $destinationPathfevicon = public_path('/asset/front/img');
            $imagefevicon->move($destinationPathfevicon, $namefevicon);
            $data['favicon'] = $namefevicon;
        }

        if ($request->hasFile('login_image')) {
            $imagelogin = $request->file('login_image');
            $namelogin = $imagelogin->getClientOriginalName();
            $destinationPathlogin = public_path('/asset/front/img');
            $imagelogin->move($destinationPathlogin, $namelogin);
            $data['login_image'] = $namelogin;
        }

        $data['title'] = $request->title;
        $data['domain'] = $request->domain;
        if ($request->has('enable_partnership')) {
            $data['enable_partnership'] = $request->enable_partnership;
        } else {
            $data['enable_partnership'] = 0;
        }

        $Website->update($data);

        //echo $Website; exit;

        return redirect()->route('websetting')
            ->with('success', 'Web Setting updated successfully');
    }
}
