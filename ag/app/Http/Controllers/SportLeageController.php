<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Sport;
use App\Match;

class SportLeageController extends Controller
{
    public function index()
    {
    	$sport = Sport::get();
        return view('backpanel/sportLeage',compact('sport'));
    }
    public function getallMatch(Request $request)
    {
    	$sId = $request->sId;
        $match_data=app('App\Http\Controllers\RestApi')->GetAllMatch($sId);
        $leage=array();
        $html='';

//        dd($match_data);

        if($match_data!=0)
        {
            foreach ($match_data as $matches)
            {
                if(substr($matches['gameId'], 0, 1) != 3){
                    continue;
                }

                $matchAdded = Match::where('match_id',$matches['marketId'])->count();
                $checked='';
                $disabled='';

                if($matchAdded > 0){
                    $checked='checked';
                    $disabled='disabled';
                }

                $eventNameExp = explode('/', $matches['eventName']);
                $event = trim($eventNameExp[0]);
                $startTime = trim(end($eventNameExp));
                $date = Carbon::parse($startTime);

                $match_date = date('d-m-Y h:i A', strtotime($date));

                $html .= '<tr class="white-bg">
                    <td>'.$matches['marketId'].'</td>
                    <td>'.$matches['gameId'].'</td>
                    <td class="text-left">'.$event.'</td>
                    <td class="text-left">'.$match_date.'</td>

                    <input type="hidden" name="event_id " id="event_id" value="'.$matches['gameId'].'" >
                    <td class="text-left"><input '.$checked.'  '.$disabled.' type="checkbox" name="" data-leage="" data-marketid="'.$matches['marketId'].'" data-sid="'.$sId.'" data-matchdate="'.$match_date.'" data-event="'.$event.'" data-eventid="'.$matches['gameId'].'" id="" onclick="addMatch(this);"></td>
                </tr>';
            }
        }
        return $html;
    }
    public function getLeageData(Request $request)
    {
        $sId = $request->sId;
        $leage = $request->leage;
        $match_data=app('App\Http\Controllers\RestApi')->GetAllMatch();
        $html='';

        if($match_data!=0)
        {
            foreach ($match_data as $matches)
            {
                if($matches['SportsId'] == $sId && $matches['Market']=='Match Odds'){
                    if($leage == $matches['Competition']){
                        $matchAdded = Match::all();
                        $checked='';
                        $disabled='';
                        foreach ($matchAdded as $value) {
                            if($value->match_id == $matches['MarketId']){
                               $checked='checked';
                               $disabled='disabled';
                            }
                        }
                        $html .= '<tr class="white-bg">
                            <td>'.$matches['MarketId'].'</td>
                            <td>'.$matches['EventId'].'</td>
                            <td class="text-left">'.$matches['Event'].'</td>
                            <td class="text-left">'.date("d-m-Y h:i:s",strtotime($matches['StartTime'])).'</td>

                            <input type="hidden" name="event_id " id="event_id" value="'.$matches['EventId'].'" >
                            <td class="text-left"><input '.$checked.'  '.$disabled.' type="checkbox" name="" data-leage="'.$leage.'" data-marketid="'.$matches['MarketId'].'" data-sid="'.$sId.'" data-matchdate="'.date("d-m-Y h:i:s",strtotime($matches['StartTime'])).'" data-event="'.$matches['Event'].'" data-eventid="'.$matches['EventId'].'" id="" onclick="addMatch(this);"></td>
                        </tr>';
                    }
                }
            }
        }
        return $html;
    }
	public function addMatchFromAPI(Request $request)
	{
		$matchList = Match::where('event_id',$request->event_id)->where('match_id',$request->match_id)->get();
		if(count($matchList)>0)
		{
            return response()->json(array('result'=> 'error','message'=>'Match already added!'));
		}
		else
		{
			// $match_data=app('App\Http\Controllers\RestApi')->Fancy_and_Bookmaker_DetailCall($request->event_id,$request->match_id,$request->sports_id);
            // $match_data_getTeam=app('App\Http\Controllers\RestApi')->DetailCall($request->match_id,$request->event_id,$request->sports_id);

            $match_data = app('App\Http\Controllers\RestApi')->getSingleCricketMatchData($request->event_id,$request->match_id,$request->sports_id);
            // echo "<pre>"; print_r($match_data); exit;
            // dd($match_data);

            $draw=0;
            if(isset($match_data['t1'][0][2]['nat']) =='The Draw'){
                $draw=1;
            }

            // if(@$match_data_getTeam[0]['runners'][2]['ex']['availableToBack'][0]['price']!='')
            // {
            //     $draw=1;
            // }
            $nation = array();
			$bm='';
			if(isset($match_data['t2'][0]['bm1'][0])!='')
            	$bm=1;
			$fancy='';
			if(isset($match_data['t3'][0])!='')
				$fancy=1;
			$data = $request->all();
			$data['sports_id'] = $request->sports_id;
            $data['leage_name'] = $request->leage;
            $data['is_draw'] = $draw;
            $data['bookmaker'] = $bm;
            $data['fancy'] = $fancy;
            $data['is_draw'] = $draw;
			$match=Match::create($data);
            return response()->json(array('result'=> 'success','message'=>'Match added successfully!'));
		}
	}
}
