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
        $sports_events = Match::whereNull('winner')->pluck('event_id');
        return view('backpanel/sportLeage',compact('sport','sports_events'));
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

                $draw = 0;
                if($matches['back12'] > 0 || $matches['lay12'] > 0){
                    $draw = 1;
                }

                if($sId == 1){
                    $draw = 1;
                }

                $bookmaker = 0;
                if($matches['m1'] == 'True'){
                    $bookmaker = 1;
                }

                $fancy = 0;
                if($matches['f'] == 'True'){
                    $fancy = 1;
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
                    <td class="text-left"><input '.$checked.'  '.$disabled.' type="checkbox" name="" data-fancy="'.$fancy.'" data-bookmaker="'.$bookmaker.'"  data-leage="" data-draw="'.$draw.'" data-marketid="'.$matches['marketId'].'" data-sid="'.$sId.'" data-matchdate="'.$match_date.'" data-event="'.$event.'" data-eventid="'.$matches['gameId'].'" id="" onclick="addMatch(this);"></td>
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
			$data = $request->all();
			$data['sports_id'] = $request->sports_id;
            $data['leage_name'] = $request->leage;
            $data['is_draw'] = $request->is_draw;
            $data['bookmaker'] = $request->bookmaker;
            $data['fancy'] = $request->fancy;
			$match=Match::create($data);
            return response()->json(array('result'=> 'success','message'=>'Match added successfully!'));
		}
	}
}
