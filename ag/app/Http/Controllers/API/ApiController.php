<?php

namespace App\Http\Controllers\API;
use App\CreditReference;
use App\FancyResult;
use App\Http\Controllers\Controller;
use App\Http\Controllers\SettingController;
use App\Match;
use App\MyBets;
use App\setting;
use App\Website;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApiController extends Controller
{

    public $website;

    public function __construct(Request $request)
    {
        $main_url=explode(".",$request->getHost());
        if(count($main_url) == 3){
            unset($main_url[0]);
        }

        $this->website = Website::where('domain',implode(".",$main_url))->first();
    }

    public function connection(){
        return response()->json([
            'status' => 'success',
            'message' => "connection successfully created"
        ],200);
    }

    public function updateWebsite(Request $request)
    {

        $website = app('website');

        $message = 'Website updated successfully';

        if($website->currency!=$request->currency){

            try {
                DB::beginTransaction();

                if($request->has('old_currency') && $request->old_currency==$website->currency) {
                    $setting = setting::latest('id')->first();
                    $oldCurrencyToINRBalance = $setting->balance * $request->old_rate;
                    $setting->balance = $oldCurrencyToINRBalance / $request->rate;
                    $setting->save();

                    $users = CreditReference::all();
                    foreach ($users as $user) {

                        $oldCurrencyToINRCredit = $user->credit * $request->old_rate;
                        $oldCurrencyToINRRemainBal = $user->remain_bal * $request->old_rate;
                        $oldCurrencyToINRAvailableBalanceForDW = $user->available_balance_for_D_W * $request->old_rate;
                        $oldCurrencyToINRExposure = $user->exposure * $request->old_rate;

                        $user->credit = $oldCurrencyToINRCredit / $request->rate;
                        $user->remain_bal = $oldCurrencyToINRRemainBal / $request->rate;
                        $user->available_balance_for_D_W = $oldCurrencyToINRAvailableBalanceForDW / $request->rate;
                        $user->exposure = $oldCurrencyToINRExposure / $request->rate;

                        $user->save();
                    }
                }

                Website::where("id",$website->id)->update(['currency'=>$request->currency]);

                $update= [];
                $update['odds_limit'] = $request->odds_limit;
                $update['min_bet_odds_limit'] = $request->min_bet_odds_limit;
                $update['max_bet_odds_limit'] = $request->max_bet_odds_limit;
                $update['min_bookmaker_limit'] = $request->min_bookmaker_limit;
                $update['max_bookmaker_limit'] = $request->max_bookmaker_limit;
                $update['min_fancy_limit'] = $request->min_fancy_limit;
                $update['max_fancy_limit'] = $request->max_fancy_limit;

                Match::where("sports_id",4)->update($update);

                $update = [];
                $update['odds_limit'] = $request->tennis_odds_limit;
                $update['min_bet_odds_limit'] = $request->tennis_min_bet_odds_limit;
                $update['max_bet_odds_limit'] = $request->tennis_max_bet_odds_limit;
                Match::where("sports_id",2)->update($update);

                $update = [];
                $update['odds_limit'] = $request->soccer_odds_limit;
                $update['min_bet_odds_limit'] = $request->soccer_min_bet_odds_limit;
                $update['max_bet_odds_limit'] = $request->soccer_max_bet_odds_limit;
                Match::where("sports_id",1)->update($update);

                DB::commit();
            }catch (\Exception $e){
                DB::rollBack();
            }
        }
        else{
            $message = 'Website not updated';
        }

        if($request->status == 'On'){
            $message = 'Website updated successfully';
            $website->status = 1;
            $website->admin_status = 1;
            $website->save();
        }else if($request->status == 'Off'){
            $message = 'Website updated successfully';
            $website->status = 0;
            $website->admin_status = 0;
            $website->save();
        }

        return response()->json([
           'status' => 'success',
           'message' => $message
        ],200);
    }

    public function getFancyMatches(){
        $match=DB::table('my_bets as b')
            ->join('match as m', 'b.match_id', '=', 'm.event_id')
            ->selectRaw('*,CONCAT(m.match_name," [",m.match_date,"]") as match_name_string')
            ->where('b.team_name','!=','')
            ->where('b.result_declare','=',0)
            ->where('b.bet_type','=','SESSION')
            ->groupBy('b.match_id')
            ->orderBy('b.result_declare','ASC')
            ->distinct()
//            ->get();
            ->pluck('match_name_string','m.match_id');

        return response()->json([
            'status' => 'success',
            'message' => 'Data retrieved successfully',
            'data' => $match->toArray()
        ],200);
    }

    public function getFancy($id){
        $match = Match::where('match_id', $id)->first();
        $ev = $match->event_id;
        $match_bet = MyBets::whereNotIn('team_name', function ($query) use ($ev) {
            $query->select('fancy_name')
                ->from(with(new FancyResult())->getTable())
                ->where('eventid', $ev);
        })->where('match_id', $match->event_id)->where('bet_type', 'SESSION')->groupBy('my_bets.team_name')->pluck('match_id','team_name');

        return response()->json([
            'status' => 'success',
            'message' => 'Data retrieved successfully',
            'data' => $match_bet->toArray()
        ],200);
    }

    public function getFancyMatchHistory(){
        $match = Match::selectRaw('match.id,match.match_id,match.match_name,match.match_name,match.match_date,CONCAT(match.match_name," [",match.match_date,"]") as match_name_string')->join('fancy_results','fancy_results.match_id','=','match.id')->where('sports_id',4)->orderBy('match_date', 'DESC')->groupBy('match.id')->where('status',1)->pluck('match_name_string','match.match_id');

        return response()->json([
            'status' => 'success',
            'message' => 'Data retrieved successfully',
            'data' => $match->toArray()
        ],200);
    }

    public function getFancyHistory($id){
        $match = Match::where('match_id', $id)->first();
        $fancyResult = FancyResult::selectRaw('*, CONCAT(fancy_name,"[[]]",result) as fancy_result, '.$match->event_id.' as match_id_new')->where('match_id', $match->id)->pluck('match_id_new','fancy_result');

        return response()->json([
            'status' => 'success',
            'message' => 'Data retrieved successfully',
            'data' => $fancyResult->toArray()
        ],200);
    }
    public function declareFancyResult(Request $request){
        Log::info("Result Declare");
        $id = $request->id;
        $fancyName = $request->fancy_name;
        $run = $request->run;
        $action = $request->action;

        Log::info(['id'=>$id,'fancyName'=>$fancyName,'run'=>$run,'action'=>$action]);

        $match = Match::where('event_id', $id)->first();

        if(empty($match)){
            return response()->json([
                'status' => 'error',
                'message' => 'Match record not found',
            ],200);
        }

        $ev = $match->event_id;
        $match_bet = MyBets::whereNotIn('team_name', function ($query) use ($ev) {
            $query->select('fancy_name')
                ->from(with(new FancyResult)->getTable())
                ->where('eventid', $ev);
        })->where('match_id', $match->event_id)->where('bet_type', 'SESSION')->where('team_name',$fancyName)->groupBy('my_bets.team_name')->first();

        $settingController = new SettingController();

        $result = $run;
        if($action == 'cancel'){
            $result = 'cancel';
        }

        if(!empty($match_bet)){
            $data['fancy_name'] = $fancyName;
            $data['match_id'] = $match->id;
            $data['eventid'] = $match->event_id;
            $data['result'] = $result;
            $data['bet_id'] = $match_bet->id;

            if(FancyResult::where($data)->count() > 0){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Already fancy declare',
                ],200);
            }

            if(FancyResult::create($data)){
                $bet = MyBets::where('match_id', $match->event_id)->where('bet_type', 'SESSION')->where('team_name', $fancyName)->where('isDeleted', 0)->where('result_declare', 0)->groupby('user_id')->get();
                foreach ($bet as $b) {

                    Log::info("Result bet processing");
//                    Log::info($b->toArray());
                    Log::info(['fancyName'=>$fancyName, 'match_id'=>$match->id, 'event_id'=>$match->event_id, 'user_id'=>$b->user_id, 'result'=>$result]);

                    $settingController->getFancyBetResult($fancyName, $match->id, $match->event_id, $b->user_id, $result);
                }

                $message = "Result declare successfully";
                if($action == 'cancel'){
                    $message = "Result cancel successfully.";
                }
                MyBets::where('match_id', $match->event_id)->where('bet_type', 'SESSION')->where('team_name', $fancyName)->update(['result_declare'=>1]);

                return response()->json([
                    'status' => 'success',
                    'message' => $message,
                ],200);
            }
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Unable to found bet record',
        ],200);

    }

    public function rollbackFancyResult(Request $request){
        $id = $request->id;
        $fancyName = $request->fancy_name;
        $run = $request->run;
        $settingController = new SettingController();

        $fancyResult = FancyResult::where("eventid",$id)->where("fancy_name",$fancyName)->where("result",$run)->first();
        if(!empty($fancyResult)) {
            $settingController->updateFancyResultRollback($fancyResult->id);

            return response()->json([
                'status' => 'success',
                'message' => 'Result rollback successfully',
            ],200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Unable to found fancy result record',
        ],200);
    }

    public function getMatches($type){
        $matches = Match::query();
        if($type == 'cricket'){
            $matches->where('sports_id',4);
        }elseif($type == 'tennis'){
            $matches->where('sports_id',2);
        }elseif($type == 'soccer'){
            $matches->where('sports_id',1);
        }

        $data = $matches->selectRaw('*, CONCAT(match_name,"[",match_date,"]==",is_draw) as new_title')->whereNull('winner')->orderby('match_date', 'asc')->pluck('new_title','event_id');

        return response()->json([
            'status' => 'success',
            'message' => 'Matches List',
            'data' => $data
        ],200);
    }

    public function declareMatchResult(Request $request){

        $match = Match::where('event_id', $request->event_id)->first();

        if(empty($match)){
            return response()->json([
                'status' => 'error',
                'message' => 'Unable to found match record',
            ],200);
        }
        $settingController = new SettingController();
        if($request->action == 'rollback'){
            $res = $settingController->updateMatchRollbackResult($match->id);

            if ($res['success'] == 'success') {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Match result rollback successfully'
                ], 200);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Match result not rollback',
            ], 200);
        }else {

            $res = $settingController->updateMatchWinnerResult($match->id,$request->winner);

            if ($res['message'] == 'Success') {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Match result declare successfully'
                ], 200);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Match result not declare',
            ], 200);
        }
    }

    public function getMatchesHistory($type){

        $matches = Match::query();
        if($type == 'cricket'){
            $matches->where('sports_id',4);
        }elseif($type == 'tennis'){
            $matches->where('sports_id',2);
        }elseif($type == 'soccer'){
            $matches->where('sports_id',1);
        }

        $data = $matches->selectRaw('*, CONCAT(match_name,"[",match_date,"]==",winner) as new_title')->whereNotNull('winner')->orderby('match_date', 'asc')->pluck('new_title','event_id');

        return response()->json([
            'status' => 'success',
            'message' => 'Matches List',
            'data' => $data
        ],200);
    }

    public function addMatch(Request $request){
        $matchList = Match::where('event_id',$request->event_id)->where('match_id',$request->match_id)->get();
        if(count($matchList)>0)
        {
            return response()->json(array('status'=> 'error','message'=>'Match already added!'));
        }

        $data = [];
        $data['match_name'] = $request->match_name;
        $data['match_id'] = $request->match_id;
        $data['match_date'] = $request->match_date;
        $data['event_id'] = $request->event_id;
        $data['sports_id'] = $request->sports_id;
        $data['leage_name'] = '';
        $data['is_draw'] = $request->is_draw;
        $data['bookmaker'] = $request->bookmaker;
        $data['fancy'] = $request->fancy;
        $data['odds_limit'] = $request->odds_limit;
        $data['min_bet_odds_limit'] = $request->min_bet_odds_limit;
        $data['max_bet_odds_limit'] = $request->max_bet_odds_limit;
        $data['min_bookmaker_limit'] = $request->min_bookmaker_limit;
        $data['max_bookmaker_limit'] = $request->max_bookmaker_limit;
        $data['min_fancy_limit'] = $request->min_fancy_limit;
        $data['max_fancy_limit'] = $request->max_fancy_limit;
        $match=Match::create($data);
        return response()->json(array('status'=> 'success','message'=>'Match added successfully!'));
    }
}
