<?php

namespace App\Http\Controllers;

use App\Casino;
use App\CreditReference;
use App\setting;
use Illuminate\Http\Request;
use App\User;
use App\CasinoBet;
use Redirect;
use Session;

class CasinoCalculationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
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
        $headerUserBalance = $depTot['available_balance_for_D_W'];
        if ($headerUserBalance <= 0) {
            return response()->json(['status'=>false,'message'=>'Insufficient Balance!']);
        }

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

        if ($headerUserBalance < ($exposer)) {
            return response()->json(['status'=>false,'message'=>'Insufficient Balance!!!']);
        }

        $data = $request->all();

        $data['odds_value'] = $odds_value;
        $data['casino_profit'] = $profit;
        $data['casino_name'] = $casino->casino_name;
        $data['user_id'] = $getUser->id;
        $data['roundid'] = $roundid;
        $data['bet_side'] = $request->bet_side;
        $data['exposureAmt'] = $exposer;
        if(CasinoBet::create($data)){
            $upd = CreditReference::find($depTot->id);
            $upd->exposure = $upd->exposure + $exposer;
            $upd->available_balance_for_D_W = ($upd->available_balance_for_D_W - $exposer);
            $upd->update();
        }

        $bets = CasinoBet::where("user_id",$getUser->id)->where('casino_name',$casino->casino_name)->whereNull('winner')->get();
        $betHtml = $this->renderBetsHtml($bets);

        $totalProfitPlayers =  CasinoBet::where("user_id",$getUser->id)->where('casino_name',$casino->casino_name)->whereNull('winner')->groupBy('team_name')->get();
        $playerProfit = [];
        foreach ($totalProfitPlayers as $team){
            $playerProfit[$team->team_sid] = CasinoBet::where("user_id",$getUser->id)->where('casino_name',$casino->casino_name)->where('team_name',$team->team_name)->whereNull('winner')->sum('casino_profit');
        }

        return response()->json(array('status' => true,'message'=>'Bet has been placed and matched successfully', 'playerProfit' => $playerProfit,'betHtml'=>$betHtml));

    }

    public function renderBetsHtml($bets){
        return view('front.ajax.casino_bet',compact('bets'))->render();
    }

    public function declareCasinoBetWinner(Request $request){

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
            $bets = CasinoBet::whereIn('roundid',array_keys($resultArray))->where('casino_name',$casino->casino_name)->whereNull('winner')->get();
        }else{
            $bets = CasinoBet::where("user_id",$getUser->id)->whereIn('roundid',array_keys($resultArray))->where('casino_name',$casino->casino_name)->whereNull('winner')->get();
        }


        foreach ($bets as $bet){
            if($resultArray[$bet->roundid] == $bet->team_sid){
                $winner = $bet->team_name;
                $profit = $bet->casino_profit+$bet->stake_value;
                $exposer = $bet->stake_value;
            }else{
                if($bet->casino_name == 'teen20') {
                    if ($resultArray[$bet->roundid] == 1){
                        $winner = 'Player A';
                    }else{
                        $winner = 'Player B';
                    }
                }else if($bet->casino_name == '20dt' || $bet->casino_name == 'dt202') {
                    if ($resultArray[$bet->roundid] == 1){
                        $winner = 'Dragon';
                    }elseif ($resultArray[$bet->roundid] == 2){
                        $winner = 'Tiger';
                    }elseif ($resultArray[$bet->roundid] == 3){
                        $winner = 'Tie';
                    }
                }else if($bet->casino_name == 'l7a' || $bet->casino_name == 'l7b') {
                    if ($resultArray[$bet->roundid] == 1){
                        $winner = 'LOW Card';
                    }else{
                        $winner = 'HIGH Card';
                    }
                }
                $profit = 0;
                $exposer = $bet->stake_value;
            }

            $upd = CreditReference::where('player_id', $bet->user_id)->first();
            $upd->exposure = $upd->exposure - $exposer;
            $upd->available_balance_for_D_W = (($upd->available_balance_for_D_W + $profit));
            $update_ = $upd->update();

            if($exposer > 0){
                $admin_profit+=$exposer;
            }else{
                $admin_loss+=$profit;
            }

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

            $bet->winner = $winner;
            $bet->save();
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
            }else if($bet->casino_name == '20dt' || $bet->casino_name == 'dt202'){
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

            return response()->json(array('status' => true,'message'=>'Result cards detail','html'=>$html));
        }

        return response()->json(array('status' => false,'message'=>'No result cards found'));

    }

}
