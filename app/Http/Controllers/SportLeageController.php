<?php

namespace App\Http\Controllers;
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
        $match_data=app('App\Http\Controllers\RestApi')->GetAllMatch();
        /*echo "<pre>";
        print_r($match_data);
        exit;*/
        $leage=array();
        $html='';
		$html.='<option value="">Select Leage</option>';
        if($match_data!=0)
        {
            foreach ($match_data as $matches)
            {
                if($matches['SportsId'] == $sId && $matches['Market']=='Match Odds'){
                    if(!in_array($matches['Competition'], $leage)){
                        $leage[] = $matches['Competition'];
                    }
                }
            }
           foreach ($leage as $value) {
            $html .= '<option value="'.$value.'">'.$value.'</option>';
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
			$match_data=app('App\Http\Controllers\RestApi')->Fancy_and_Bookmaker_DetailCall($request->event_id,$request->match_id,$request->sports_id);   
            $match_data_getTeam=app('App\Http\Controllers\RestApi')->DetailCall($request->match_id,$request->event_id,$request->sports_id);
            $draw=0;
            if(@$match_data_getTeam[0]['runners'][2]['ex']['availableToBack'][0]['price']!='')
            {
                $draw=1;
            }
            $nation = array();   
			$bm='';
			if(isset($match_data['bm'][0]['b1'])!='')
            	$bm=1;
			$fancy='';
			if(isset($match_data['fancy'][0]['b1'])!='')
				$fancy=1;	
			$data = $request->all();
			$data['sports_id'] = $request->sports_id;            
            $data['leage_name'] = $request->leage;
            $data['is_draw'] = $draw;
			$match=Match::create($data);
			$insertedId = $match->id;
			//upd
			$upd=Match::find($insertedId);
			$upd->bookmaker = $bm;
			$upd->fancy = $fancy;
			$upd->update();     
            return response()->json(array('result'=> 'success','message'=>'Match added successfully!'));
		}
	}
}