<?php

namespace App\Http\Controllers;

use App\Casino;
use App\CreditReference;
use App\setting;
use App\UsersAccount;
use Illuminate\Http\Request;
use App\User;
use App\CasinoBet;
use Redirect;
use Session;

class CasinoCalculationController extends Controller
{

    public static function getExAmountCasinoForEachTeam($casino_name,$id,$roundid=''){
        if(empty($roundid)) {
            if(!empty($id)) {
                $casinoBets = CasinoBet::where("casino_name", $casino_name)->where('user_id', $id)->whereNull('winner')->get();
            }else{
                $casinoBets = CasinoBet::where("casino_name", $casino_name)->whereNull('winner')->get();
            }
        }else{
            if(!empty($id)) {
                $casinoBets = CasinoBet::where("casino_name", $casino_name)->where('user_id', $id)->where('roundid', $roundid)->whereNull('winner')->get();
            }else{
                $casinoBets = CasinoBet::where("casino_name", $casino_name)->where('roundid', $roundid)->whereNull('winner')->get();
            }
        }
        $response = array();
        $arr = array();
        foreach ($casinoBets as $bet) {
            $extra = json_decode($bet->extra, true);
            if ($bet['bet_side'] == 'lay') {
                $profitAmt = $bet['exposureAmt'];
                $profitAmt = ($profitAmt * (-1));
                if (!isset($response['ODDS'][$bet['team_sid']])) {
                    $response['ODDS'][$bet['team_sid']] = $profitAmt;
                } else {
                    $response['ODDS'][$bet['team_sid']] += $profitAmt;
                }

                foreach ($extra as $team){
                    if (!isset($response['ODDS'][$team])) {
                        $response['ODDS'][$team] = $bet['stake_value'];
                    } else {
                        $response['ODDS'][$team] += $bet['stake_value'];
                    }
                }
            }
            else {
                $profitAmt = $bet['casino_profit']; ////nnn
                $bet_amt = ($bet['stake_value'] * (-1));
                if (!isset($response['ODDS'][$bet['team_sid']])) {
                    $response['ODDS'][$bet['team_sid']] = $profitAmt;
                } else {
                    $response['ODDS'][$bet['team_sid']] += $profitAmt;
                }

                foreach ($extra as $team){
                    if (!isset($response['ODDS'][$team])) {
                        $response['ODDS'][$team] = $bet_amt;
                    } else {
                        $response['ODDS'][$team] += $bet_amt;
                    }
                }
            }
        }

        return $response;
    }

    public static function getCasinoExAmount($casino_name = '', $id = '',$roundid='')
    {
//        if(empty($id)) {
//            $getUserCheck = Session::get('playerUser');
//            if (empty($getUserCheck)) {
//                return ['status'=>false,'message'=>'Required login'];
//            }
//            $id = $getUserCheck->id;
//        }
        if (!empty($casino_name)) {
            if(empty($roundid)) {
                if(empty($id)) {
                    $casinoBets = CasinoBet::where("casino_name", $casino_name)->groupBy('casino_name')->whereNull('winner')->get();
                }else {
                    $casinoBets = CasinoBet::where("casino_name", $casino_name)->where('user_id', $id)->groupBy('casino_name')->whereNull('winner')->get();
                }
            }else{
                if(empty($id)) {
                    $casinoBets = CasinoBet::where("casino_name", $casino_name)->groupBy('roundid')->whereNull('winner')->get();
                }else {
                    $casinoBets = CasinoBet::where("casino_name", $casino_name)->where('user_id', $id)->groupBy('roundid')->whereNull('winner')->get();
                }
            }
        } else {
            if(empty($roundid)) {
                if(!empty($id)) {
                    $casinoBets = CasinoBet::where('user_id', $id)->where('user_id', $id)->groupBy('casino_name')->whereNull('winner')->get();
                }else{
                    $casinoBets = CasinoBet::groupBy('casino_name')->whereNull('winner')->get();
                }
            }else{
                if(!empty($id)) {
                    $casinoBets = CasinoBet::where('user_id', $id)->groupBy('roundid')->whereNull('winner')->get();
                }else{
                    $casinoBets = CasinoBet::groupBy('roundid')->whereNull('winner')->get();
                }
            }
        }
        $exAmtTot = [
            'exposer' => 0,
        ];
        foreach ($casinoBets as $bet) {
            $exAmtArr = self::getExAmountCasinoForEachTeam($bet->casino_name,$id,$roundid);

            if (isset($exAmtArr['ODDS'])) {
                $arr = array();
                foreach ($exAmtArr['ODDS'] as $key => $profitLos) {
                    if ($profitLos < 0) {
                        $arr[abs($profitLos)] = abs($profitLos);
                    }
                }
                if (is_array($arr) && count($arr) > 0) {
                    $exAmtTot['exposer'] += max($arr);
                }

                $exAmtTot['ODDS'] = $exAmtArr['ODDS'];
            }
        }

        $exAmtTot['exposer'] = abs($exAmtTot['exposer']);

        return $exAmtTot;
    }

    public function casino_bet(Request $request)
    {

        $getUserCheck = Session::get('playerUser');
        if (!empty($getUserCheck)) {
            $getUser = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
        }else{
            return response()->json(['status'=>false,'message'=>'Session Logout, please login again']);
        }

        $casino = Casino::where("casino_name",$request->casino_name)->first();
        if(empty($casino)){
            return response()->json(['status'=>false,'message'=>'Casino not found']);
        }

        if ($casino->max_casino < $request->stake_value) {
            return response()->json(['status'=>false,'message'=>'Bet Not Confirm Reason Min and Max Bet Range Not Valid.']);
        }

        $depTot = CreditReference::where('player_id', $getUser->id)->first();
//        $headerUserBalance = $depTot['available_balance_for_D_W'];
        $headerUserBalance = $depTot['remain_bal'];
        if ($headerUserBalance <= 0) {
            return response()->json(['status'=>false,'message'=>'Insufficient Balance!']);
        }

        $casinoExposer = self::getCasinoExAmount($casino->casino_name,$getUser->id,'');

        $roundid = $request->roundid;
        $stake_value = $request->stake_value;
        $odds_value = $request->odds_value;
        $team_name = $request->team_name;
        $exposer = 0;
        if($request->bet_side ==  'back'){
            $exposer = $stake_value;
            $profit = ($odds_value - 1) * ($stake_value);
        }else{
            $exposer = ($odds_value - 1) * ($stake_value);
            $profit = $stake_value;
        }

        $data = $request->all();
        $other_team_name = array_filter(explode(",",$request->other_team_name));
        unset($data['other_team_name']);
        $betTeamIndex = array_search($request->team_sid, $other_team_name);
        unset($other_team_name[$betTeamIndex]);

        $currentRoundCasinoExposer = $casinoExposer['exposer'];
        $oldExposer = $casinoExposer;

//        dd($other_team_name);

//        dd($casinoExposer);
//        if($currentRoundCasinoExposer > 0)
//        {
        if ($request->bet_side == 'lay') {
            $profitAmt = $exposer;
            $profitAmt = -($profitAmt);

//                dd($casinoExposer['ODDS'][$request->team_sid] , $profitAmt);

            if (!isset($casinoExposer['ODDS'][$request->team_sid])) {
                $casinoExposer['ODDS'][$request->team_sid] = $profitAmt;
            } else {
                $casinoExposer['ODDS'][$request->team_sid] += $profitAmt;
            }

            foreach ($other_team_name as $team) {
                if (!isset($casinoExposer['ODDS'][$team])) {
                    $casinoExposer['ODDS'][$team] = $stake_value;
                } else {
                    $casinoExposer['ODDS'][$team] += $stake_value;
                }
            }
        } else {
            $profitAmt = $profit;
            $bet_amt = ($stake_value * (-1));
            if (!isset($casinoExposer['ODDS'][$request->team_sid])) {
                $casinoExposer['ODDS'][$request->team_sid] = $profitAmt;
            } else {
                $casinoExposer['ODDS'][$request->team_sid] += $profitAmt;
            }

            foreach ($other_team_name as $team) {
                if (!isset($casinoExposer['ODDS'][$team])) {
                    $casinoExposer['ODDS'][$team] = $bet_amt;
                } else {
                    $casinoExposer['ODDS'][$team] += $bet_amt;
                }
            }
        }
        $arr = [];
        $casinoExposer['exposer'] = 0;
        foreach ($casinoExposer['ODDS'] as $key => $profitLos) {
            if ($profitLos < 0) {
                $arr[abs($profitLos)] = abs($profitLos);
            }
        }
        if (is_array($arr) && count($arr) > 0) {
            $casinoExposer['exposer'] += max($arr);
        }

        $currentRoundCasinoExposer = $casinoExposer['exposer'];
//        }else{
//            $currentRoundCasinoExposer = $exposer;
//        }

//        dd($headerUserBalance,  $currentRoundCasinoExposer, $exposer, $oldExposer, $casinoExposer);

        if ($headerUserBalance < $currentRoundCasinoExposer) {
            return response()->json(['status'=>false,'message'=>'Insufficient Balance!!']);
        }


        $data['odds_value'] = $odds_value;
        $data['casino_profit'] = $profit;
        $data['casino_name'] = $casino->casino_name;
        $data['user_id'] = $getUser->id;
        $data['roundid'] = $roundid;
        $data['bet_side'] = $request->bet_side;
        $data['exposureAmt'] = $exposer;
        $data['extra'] = json_encode($other_team_name);
        if(CasinoBet::create($data)){

            $playerController = new PlayerController();
            $playerController->SaveBalance($exposer);

//            $upd = CreditReference::find($depTot->id);
//            $upd->exposure = $upd->exposure + $exposer;
//            $upd->available_balance_for_D_W = ($upd->available_balance_for_D_W - $exposer);
//            $upd->update();
        }

        $bets = CasinoBet::where("user_id",$getUser->id)->where('casino_name',$casino->casino_name)->whereNull('winner')->get();
        $betHtml = $this->renderBetsHtml($bets);

//        $totalProfitPlayers =  CasinoBet::where("user_id",$getUser->id)->where('casino_name',$casino->casino_name)->whereNull('winner')->groupBy('team_name')->get();
//        $playerProfit = [];
//        foreach ($totalProfitPlayers as $team){
//            $playerProfit[$team->team_sid] = CasinoBet::where("user_id",$getUser->id)->where('casino_name',$casino->casino_name)->where('team_name',$team->team_name)->whereNull('winner')->sum('casino_profit');
//        }

        $casinoExposerWithNewBet = self::getCasinoExAmount($casino->casino_name,$getUser->id);

        $playerProfit = $casinoExposerWithNewBet['ODDS'];

        $balance = CreditReference::where('player_id', $getUser->id)->first();
        $available_balance_for_D_W = number_format($balance->available_balance_for_D_W, 2);
        $exposure = number_format($balance->exposure, 2);

        return response()->json(array('status' => true,'message'=>'Bet has been placed and matched successfully','exposure'=>$exposure,'available_balance_for_D_W'=>$available_balance_for_D_W, 'playerProfit' => $playerProfit,'betHtml'=>$betHtml));

    }

    public function renderBetsHtml($bets){
        return view('front.ajax.casino_bet',compact('bets'))->render();
    }

    public function declareCasinoBetWinner(Request $request){
//        die();
        $last10Results = json_decode($request->result,true);

        $casino = Casino::where("casino_name",$request->casino_name)->first();
        if(empty($casino)){
            return response()->json(['status'=>false,'message'=>'Casino not found']);
        }
        if(empty($request->roundid) || $request->roundid<=0){
            return ['status'=>false,'message'=>'Invalid round id'];
        }

        CasinoBet::where("roundid",$request->roundid)->update([
            'cards' => json_encode($request->cards)
        ]);

        $getUserCheck = Session::get('playerUser');
        if (!empty($getUserCheck)) {
            $getUser = User::where('id', $getUserCheck->id)->where('check_login', 1)->first();
        } else {
            return ['status' => false, 'message' => 'Session Logout, please login again'];
        }

        $resp = $this->updateCasinoWinner($last10Results, $casino, $getUser);

//        dd($resp);

        $bets = CasinoBet::where("user_id",$getUser->id)->where('casino_name',$casino->casino_name)->whereNull('winner')->get();

        $betHtml = $this->renderBetsHtml($bets);

        return response()->json(array('status' => true,'message'=>'Result declare successfully', 'betHtml'=>$betHtml));
    }

    public function updateCasinoWinner($last10Results, $casino, $getUser = []){

        $resultArray = [];
        foreach ($last10Results as $result){
            $resultArray[$result['mid']] = $result['result'];
        }

        $admin_profit = 0; $admin_loss = 0;
        if(empty($getUser)) {
            $bets = CasinoBet::whereIn('roundid',array_keys($resultArray))->where('casino_name',$casino->casino_name)->whereNull('winner')->groupBy('roundid')->get();
        }else{
            $bets = CasinoBet::where("user_id",$getUser->id)->whereIn('roundid',array_keys($resultArray))->where('casino_name',$casino->casino_name)->groupBy('roundid')->whereNull('winner')->get();
        }
        $masterAdmin = User::where("agent_level","COM")->first();
        $settings = setting::latest('id')->first();
        $adm_balance = $settings->balance;

//        dd($bets);

        foreach ($bets as $bet){

            $casinoExposer = self::getCasinoExAmount($casino->casino_name,$getUser->id,$bet->roundid);
//            dd($casinoExposer);

            $odds = $casinoExposer['ODDS'];
            $totalExposer = $casinoExposer['exposer'];

            $winnerSid = $resultArray[$bet->roundid];

//            dd($casinoExposer, $resultArray, $bet->roundid);

            if($winnerSid == "0" || $winnerSid == 0){
                $lose = abs($casinoExposer['exposer'])*50/100;
                $exposerToBeReturn = $casinoExposer['exposer'] - $lose ;
                $profit = 0;
            }else {
                $winnerProfitLose = $odds[$winnerSid];
                if ($winnerProfitLose < 0) {
                    $lose = abs($winnerProfitLose);
                    $exposerToBeReturn = abs($totalExposer - abs($winnerProfitLose));
                    $profit = 0;
                } else {
                    $profit = abs($winnerProfitLose);
                    $exposerToBeReturn = $totalExposer;
                    $lose = 0;
                }
            }
            $winner = null;
            if($bet->casino_name == 'teen20') {
                if ($resultArray[$bet->roundid] == 1){
                    $winner = 'Player A';
                }else{
                    $winner = 'Player B';
                }
            }
            else if($bet->casino_name == '20dt' || $bet->casino_name == 'dt202') {
                if ($resultArray[$bet->roundid] == 1){
                    $winner = 'Dragon';
                }elseif ($resultArray[$bet->roundid] == 2){
                    $winner = 'Tiger';
                }elseif ($resultArray[$bet->roundid] == 3){
                    $winner = 'Tie';
                }
            }
            else if($bet->casino_name == 'l7a' || $bet->casino_name == 'l7b') {
                if ($resultArray[$bet->roundid] == 1){
                    $winner = 'LOW Card';
                }else if($resultArray[$bet->roundid] == 2){
                    $winner = 'HIGH Card';
                }else if($resultArray[$bet->roundid] == 0){
                    $winner = 'Tie';
                }
            }
            else if($bet->casino_name == '20poker') {
                if ($resultArray[$bet->roundid] == 11){
                    $winner = 'Player A';
                }else if($resultArray[$bet->roundid] == 21){
                    $winner = 'Player B';
                }
            }
            else if($bet->casino_name == 'ab1' || $bet->casino_name == 'ab2') {
                if ($resultArray[$bet->roundid] == 1){
                    $winner = 'Player A';
                }else{
                    $winner = 'Player B';
                }
            }
            else if($bet->casino_name == 'aaa') {
                if ($resultArray[$bet->roundid] == 1){
                    $winner = 'Amar';
                }else if($resultArray[$bet->roundid] == 2){
                    $winner = 'Akbar';
                }else if($resultArray[$bet->roundid] == 3){
                    $winner = 'Anthony';
                }
            }
            else if($bet->casino_name == 'bollywood') {
                if ($resultArray[$bet->roundid] == 1){
                    $winner = 'DON';
                }else if($resultArray[$bet->roundid] == 2){
                    $winner = 'Amar Akbar Anthony';
                }else if($resultArray[$bet->roundid] == 3){
                    $winner = 'Sahib Bibi Aur Ghulam';
                }else if($resultArray[$bet->roundid] == 4){
                    $winner = 'Dharam Veer';
                }else if($resultArray[$bet->roundid] == 5){
                    $winner = 'Kis KisKo Pyaar Karoon';
                }else if($resultArray[$bet->roundid] == 6){
                    $winner = 'Ghulam';
                }
            }
            else if($bet->casino_name == '32a' || $bet->casino_name == '32b') {
                if ($resultArray[$bet->roundid] == 1){
                    $winner = 'Player 8';
                }else if($resultArray[$bet->roundid] == 2){
                    $winner = 'Player 9';
                }else if($resultArray[$bet->roundid] == 3){
                    $winner = 'Player 10';
                }else if($resultArray[$bet->roundid] == 4){
                    $winner = 'Player 11';
                }
            }
            else if($bet->casino_name == '1daydt') {
                if ($resultArray[$bet->roundid] == 1){
                    $winner = 'Dragon';
                }else if($resultArray[$bet->roundid] == 2){
                    $winner = 'Tiger';
                }
            }

            $upd = CreditReference::where('player_id', $bet->user_id)->first();

            $available_balance = $upd->available_balance_for_D_W;

            $upd->exposure = $upd->exposure - $totalExposer;

            $upd->available_balance_for_D_W = (($upd->available_balance_for_D_W + $profit + $exposerToBeReturn));

            if($profit > 0){

                $upd->remain_bal  =  $upd->remain_bal + $profit;

                UsersAccount::create([
                    'user_id' => $bet->user_id,
                    'from_user_id' => $bet->user_id,
                    'to_user_id' => $bet->user_id,
                    'credit_amount' => $profit,
                    'balance' => $available_balance,
                    'closing_balance' => $available_balance + $profit,
                    'remark' => "",
                    'bet_user_id' => $bet->user_id,
                    'casino_id' => $casino->id,
                    'user_exposure_log_id' => $bet->id
                ]);
                UsersAccount::create([
                    'user_id' => $masterAdmin->id,
                    'from_user_id' => $bet->user_id,
                    'to_user_id' => $masterAdmin->id,
                    'debit_amount' => $profit,
                    'balance' => $adm_balance,
                    'closing_balance' => $adm_balance - $profit,
                    'remark' => "",
                    'bet_user_id' => $bet->user_id,
                    'casino_id' => $casino->id,
                    'user_exposure_log_id' => $bet->id
                ]);

                $admin_loss+=$profit;
            }
            else{
                $upd->remain_bal  =  $upd->remain_bal - $lose;

                UsersAccount::create([
                    'user_id' => $bet->user_id,
                    'from_user_id' => $bet->user_id,
                    'to_user_id' => $bet->user_id,
                    'debit_amount' => $lose,
                    'balance' => $available_balance,
                    'closing_balance' => $available_balance - $lose,
                    'remark' => "",
                    'bet_user_id' => $bet->user_id,
                    'casino_id' => $casino->id,
                    'user_exposure_log_id' => $bet->id
                ]);
                UsersAccount::create([
                    'user_id' => $masterAdmin->id,
                    'from_user_id' => $bet->user_id,
                    'to_user_id' => $masterAdmin->id,
                    'credit_amount' => $lose,
                    'balance' => $adm_balance,
                    'closing_balance' => $adm_balance + $lose,
                    'remark' => "",
                    'bet_user_id' => $bet->user_id,
                    'casino_id' => $casino->id,
                    'user_exposure_log_id' => $bet->id
                ]);

                $admin_profit+=$lose;
            }

            $update_ = $upd->update();

            if ($update_) {
                $parentid = self::GetAllParentofPlayer($bet->user_id);

                $parentid = json_decode($parentid);
                if (!empty($parentid)) {
                    for ($i = 0; $i < sizeof($parentid); $i++) {
                        $pid = $parentid[$i];
                        if ($pid != 1) {
                            if($profit > 0) {
                                $creditref_bal = CreditReference::where(['player_id' => $pid])->first();
                                $bal = $creditref_bal->remain_bal;
                                $remain_balance_ = $bal + $profit;
                                $upd_ = CreditReference::find($creditref_bal->id);
                                $upd_->remain_bal = $remain_balance_;
                                $update_parent = $upd_->update();
                            }else{
                                $creditref_bal = CreditReference::where(['player_id' => $pid])->get();
                                $bal = $creditref_bal->remain_bal;
                                $remain_balance_ = $bal - abs($exposer);
                                $upd_ = CreditReference::find($creditref_bal->id);
                                $upd_->remain_bal = $remain_balance_;
                                $update_parent = $upd_->update();
                            }
                        }
                    }
                }
            }

            CasinoBet::where('casino_name',$bet->casino_name)->where("roundid",$bet->roundid)->update(['winner'=>$winner]);
        }

        if($admin_loss > 0 || $admin_profit > 0){
            $settings = setting::latest('id')->first();
            $adm_balance = $settings->balance;
            $new_balance = $adm_balance + $admin_profit - $admin_loss;
            $adminData = setting::find($settings->id);
            $adminData->balance = $new_balance;
            $adminData->update();
        }

        return ['status'=>true,'message'=>'Result declare successfully'];
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
        } else {
            return json_encode($parent);
        }
    }

    public function getWinnerCards($roundId,$casinoName){
        $bet = CasinoBet::where("roundid",$roundId)->where('casino_name',$casinoName)->first();
        if(!empty($bet) && $bet->cards!='' && $bet->cards!=null){
            $cards = json_decode($bet->cards,true);
            $roundId = explode(".",$bet->roundid);
            $html = '<h6 class="text-right round-id"><b>Round Id:</b> <span>'.$roundId[1].'</span></h6>';
            if($bet->casino_name == 'teen20'){
                $html.= '<div class="row">
                            <div class="col br1 text-center playera"><h4>Player A</h4>
                                <div class="result-image">
                                    <img class="mr-2" src="'.asset('asset/front/img/cards')."/".$cards[0][0].'.png">
                                    <img class="mr-2" src="'.asset('asset/front/img/cards')."/".$cards[0][1].'.png">
                                    <img class="mr-2" src="'.asset('asset/front/img/cards')."/".$cards[0][2].'.png">
                                </div>';
                if($bet->winner == 'Player A') {
                    $html .= '<div class="winner-icon mt-3"><i class="fas fa-trophy mr-2"></i></div>';
                }
                $html.='</div>
                            <div class="col text-center playerb"><h4>Player B</h4>
                                <div class="result-image">
                                    <img class="mr-2" src="'.asset('asset/front/img/cards')."/".$cards[1][0].'.png">
                                    <img class="mr-2" src="'.asset('asset/front/img/cards')."/".$cards[1][1].'.png">
                                    <img class="mr-2" src="'.asset('asset/front/img/cards')."/".$cards[1][2].'.png">
                                </div>';
                if($bet->winner == 'Player B') {
                    $html .= '<div class="winner-icon mt-3"><i class="fas fa-trophy mr-2"></i></div>';
                }

                $html.='</div></div>';
            }else if($bet->casino_name == '20dt' || $bet->casino_name == 'dt202' || $bet->casino_name == '1daydt'){
                $html.= '<div class="row">
                            <div class="col br1 text-center playera"><h4>Dragon</h4>
                                <div class="result-image">
                                    <img class="mr-2" src="'.asset('asset/front/img/cards')."/".$cards[0][0].'.png">
                                </div>';
                if($bet->winner == 'Dragon') {
                    $html .= '<div class="winner-icon mt-3"><i class="fas fa-trophy mr-2"></i></div>';
                }
                $html.='</div>
                            <div class="col text-center playerb"><h4>Tiger</h4>
                                <div class="result-image">
                                    <img class="mr-2" src="'.asset('asset/front/img/cards')."/".$cards[1][0].'.png">
                                </div>';
                if($bet->winner == 'Tiger') {
                    $html .= '<div class="winner-icon mt-3"><i class="fas fa-trophy mr-2"></i></div>';
                }

                $html.='</div></div>';

                if($bet->winner == 'Tie') {
                    $html.='<div class="row"><div class="col text-center m-t">WINNER: <b>Tie</b></div></div>';
                }
            }
            else if($bet->casino_name == 'l7a' || $bet->casino_name == 'l7b' || $bet->casino_name == 'aaa' || $bet->casino_name == 'bollywood'){
                $html.= '<div class="row">
                            <div class="col br1 text-center playera"><h4>'.$bet->winner.'</h4>
                                <div class="result-image">
                                    <img class="mr-2" src="'.asset('asset/front/img/cards')."/".$cards[0][0].'.png">
                                </div>
                                <div class="winner-icon mt-3"><i class="fas fa-trophy mr-2"></i></div>
                            </div>
                        </div>';

            }
            else if($bet->casino_name == '20poker'){
                $html.= '<div class="row">
                            <div class="col br1 text-center playera"><h4>Player A</h4>
                                <div class="result-image">
                                    <img class="mr-2" src="'.asset('asset/front/img/cards')."/".$cards[0][0].'.png">
                                    <img class="mr-2" src="'.asset('asset/front/img/cards')."/".$cards[0][1].'.png">
                                </div>';
                if($bet->winner == 'Player A') {
                    $html .= '<div class="winner-icon mt-3"><i class="fas fa-trophy mr-2"></i></div>';
                }
                $html.='</div>
                            <div class="col text-center playerb"><h4>Player B</h4>
                                <div class="result-image">
                                    <img class="mr-2" src="'.asset('asset/front/img/cards')."/".$cards[1][0].'.png">
                                    <img class="mr-2" src="'.asset('asset/front/img/cards')."/".$cards[1][1].'.png">
                                </div>';
                if($bet->winner == 'Player B') {
                    $html .= '<div class="winner-icon mt-3"><i class="fas fa-trophy mr-2"></i></div>';
                }

                $html.='</div>
                            <div class="col-12 text-center playerb"><h4>Cards</h4>
                                <div class="result-image">
                                    <img class="mr-2" src="'.asset('asset/front/img/cards')."/".$cards[2][0].'.png">
                                    <img class="mr-2" src="'.asset('asset/front/img/cards')."/".$cards[2][1].'.png">
                                    <img class="mr-2" src="'.asset('asset/front/img/cards')."/".$cards[2][2].'.png">
                                    <img class="mr-2" src="'.asset('asset/front/img/cards')."/".$cards[2][3].'.png">
                                    <img class="mr-2" src="'.asset('asset/front/img/cards')."/".$cards[2][4].'.png">
                                </div>';

                $html.='</div></div>';

                if($bet->winner == 'Tie') {
                    $html.='<div class="row"><div class="col text-center m-t">WINNER: <b>Tie</b></div></div>';
                }

            }else if($bet->casino_name == 'ab1' || $bet->casino_name == 'ab2'){

                $html1 = '';
                foreach($cards[0] as $card){
                    $html1.= '<img class="mr-2" src="'.asset('asset/front/img/cards')."/".$card.'.png">';
                }
                $html2 = '';
                foreach($cards[1] as $card){
                    $html2.= '<img class="mr-2" src="'.asset('asset/front/img/cards')."/".$card.'.png">';
                }

                $html.= '<div class="row">
                            <div class="col-12 br1 text-center playera"><h4>Player A</h4>
                                <div class="result-image">'.$html1.'</div>';
                if($bet->winner == 'Player A') {
                    $html .= '<div class="winner-icon mt-3"><i class="fas fa-trophy mr-2"></i></div>';
                }
                $html.='</div>
                            <div class="col-12 text-center playerb"><h4>Player B</h4>
                                <div class="result-image">'.$html2.'</div>';
                if($bet->winner == 'Player B') {
                    $html .= '<div class="winner-icon mt-3"><i class="fas fa-trophy mr-2"></i></div>';
                }

                $html.='</div></div>';
            }
            else if($bet->casino_name == '32a' || $bet->casino_name == '32b'){

                $html1 = '';
                foreach($cards[0] as $card){
                    $html1.= '<img class="mr-2" src="'.asset('asset/front/img/cards')."/".$card.'.png">';
                }
                $html2 = '';
                foreach($cards[1] as $card){
                    $html2.= '<img class="mr-2" src="'.asset('asset/front/img/cards')."/".$card.'.png">';
                }
                $html3 = '';
                foreach($cards[2] as $card){
                    $html3.= '<img class="mr-2" src="'.asset('asset/front/img/cards')."/".$card.'.png">';
                }
                $html4 = '';
                foreach($cards[3] as $card){
                    $html4.= '<img class="mr-2" src="'.asset('asset/front/img/cards')."/".$card.'.png">';
                }

                $html.= '<div class="row">
                            <div class="col-12 br1 text-center playera"><h4>Player 8</h4>
                                <div class="result-image">'.$html1.'</div>';
                if($bet->winner == 'Player 8') {
                    $html .= '<div class="winner-icon mt-3"><i class="fas fa-trophy mr-2"></i></div>';
                }
                $html.='</div>
                            <div class="col-12 text-center playerb"><h4>Player 9</h4>
                                <div class="result-image">'.$html2.'</div>';
                if($bet->winner == 'Player 9') {
                    $html .= '<div class="winner-icon mt-3"><i class="fas fa-trophy mr-2"></i></div>';
                }
                $html.='</div>
                            <div class="col-12 text-center playerb"><h4>Player 10</h4>
                                <div class="result-image">'.$html2.'</div>';
                if($bet->winner == 'Player 10') {
                    $html .= '<div class="winner-icon mt-3"><i class="fas fa-trophy mr-2"></i></div>';
                }

                $html.='</div>
                            <div class="col-12 text-center playerb"><h4>Player 11</h4>
                                <div class="result-image">'.$html2.'</div>';
                if($bet->winner == 'Player 11') {
                    $html .= '<div class="winner-icon mt-3"><i class="fas fa-trophy mr-2"></i></div>';
                }
                $html.='</div></div>';
            }

            return response()->json(array('status' => true,'message'=>'Result cards detail','html'=>$html));
        }

        return response()->json(array('status' => false,'message'=>'No result cards found'));

    }

}
