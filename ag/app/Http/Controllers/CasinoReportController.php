<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\User;
use Auth;
use Illuminate\Support\Facades\Hash;
use Redirect;
use App\Casino;

class CasinoReportController extends Controller
{
	public function casinoreport()
    {
    	$getdata = Casino::where('status',1)->get();
    	return view('front/casinoreport',compact('getdata'));
    }
    public function dataCasinoReport(Request $request)
    {
    	//echo"<pre>";print_r($request->all());echo"<pre>";exit;
    	$casinoType = $request->type;
    	$casinoDate = $request->fdate;

    	$cdate=''; $html=''; $ndate='';

    	if($casinoDate ==''){
			$cdate=date("d/m/Y");
		}
		else{
			$cdate=date("d/m/Y", strtotime($casinoDate));
		}

    	if($casinoType == 'teen20')
    	{
    		$url='http://192.46.208.70/json/20teenpati/last_result.json';
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

			if(!empty($match_data))
			{
				foreach ($match_data as $data) 
				{
					$dt=explode(" ",$data['match_time']);

					if($dt[0] == $cdate){
						$html.='
							<tr>
				                <td>'.$data['round_id'].'</td>
				                <td onclick="openLastPopup('.$data['round_id'].')">'.$data['description']['winner'].'</td>
				            </tr>
						';
					}
				}
			}
    	}
    	if($casinoType == 'baccarat') 
    	{
    		$url='http://192.46.208.70/json/baccarat/last_result.json';
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

			//echo"<pre>";print_r($match_data);echo"<pre>";

			if(!empty($match_data))
			{
				foreach ($match_data as $data) 
				{
					$dt=explode(" ",$data['match_time']);

					if($dt[0] == $cdate){
						$html.='
							<tr>
				                <td>'.$data['round_id'].'</td>';
				                if($data['description']['Winner'] == ''){
				                	$html.='<td>-</td>';
				                }
				                else{
				                	$html.='<td>'.$data['description']['Winner'].'</td>';
				                }
				                
				            $html.='</tr>
						';
					}
				}
			}
    	}
    	if($casinoType == 'dt202')
    	{
    		//remain
    		$url='http://172.105.253.130/json/dragontiger2/last_result.json';
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

			if(!empty($match_data))
			{
				foreach ($match_data as $data) 
				{
					$dt=explode(" ",$data['match_time']);

					if($dt[0] == $cdate){
						$html.='
							<tr>
				                <td>'.$data['round_id'].'</td>
				                <td>'.$data['description']['winner'].'</td>
				            </tr>
						';
					}
				}
			}
    	}
    	if($casinoType == 'ab20') 
    	{
    		$url='http://192.46.208.70/json/andar_bahar/last_result.json';
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

			if(!empty($match_data))
			{
				foreach ($match_data as $data) 
				{
					$dt=explode(" ",$data['match_time']);

					if($dt[0] == $cdate){
						$html.='
							<tr>
				                <td>'.$data['round_id'].'</td>
				                <td>Win</td>
				            </tr>
						';
					}
				}
			}
    	}
    	if($casinoType == '32card')
    	{
    		$url='http://172.105.253.130/json/32cardsb/last_result.json';
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

			if(!empty($match_data))
			{
				foreach ($match_data as $data) 
				{
					$dt=explode(" ",$data['match_time']);

					if($dt[0] == $cdate){
						$html.='
							<tr>
				                <td>'.$data['round_id'].'</td>
				                <td>'.$data['description']['winner'].'</td>
				            </tr>
						';
					}
				}
			}
    	}
    	
		return $html;
    }
    public function teen20LastResultpopup(Request $request)
    {
    	$round=$request->round;
        $html='';
        $i=1;
        $last_result=app('App\Http\Controllers\RestApi')->getteen20LastResult("teen20");
        echo"<pre>";print_r($last_result);echo"<pre>"; exit;
        foreach ($last_result as $key => $value) {
            if($last_result[$i]['round_id'] == $round){
                $html.='<div class="modal-body" id="appnedLastResult">
                    <div class="casino_result_round">
                        <div>Round-Id: '.$round.'</div>
                        <div>Match Time: '.$last_result[$i]['match_time'].'</div>
                    </div>

                    <div class="row row1">
                        <div class="col-12 col-lg-8">
                            <div class="casino-result-content">
                                <div class="casino-result-content-item text-center">
                                    <div class="casino-result-cards">
                                        <div class="d-inline-block">
                                            <h4>Player A</h4>
                                            ';
                                            $winnerclass='';
                                        foreach ($last_result[$i]['cards']['a'] as $key => $value) {
                                                if($value=='https://sitethemedata.com/v3/static/front/img/winner.png'){
                                                    $winnerclass="winner_icon";
                                                     $html.='<div class="casino-result-cards-item "><img class="'.$winnerclass.'" src="'.$value.'"></div>';
                                                }else{
                                                     $html.='<div class="casino-result-cards-item "><img src="'.$value.'"></div>';
                                                }
                                           
                                        }
                                            $html.='                                     
                                        </div>
                                    </div>
                                </div>
                                <div class="casino-result-content-diveder darkblue-bg"></div>
                                <div class="casino-result-content-item text-center">
                                    <div class="casino-result-cards">
                                        <div class="casino-result-cards-item"><img src="" class="winner_icon"></div>
                                        <div class="d-inline-block">
                                            <h4>Player B</h4>';
                                            $winnerclass='';
                                        foreach ($last_result[$i]['cards']['b'] as $key => $value) {
                                            if($value=='https://sitethemedata.com/v3/static/front/img/winner.png'){
                                                    $winnerclass="winner_icon";
                                                    $html.='<div class="casino-result-cards-item"><img class="'.$winnerclass.'" src="'.$value.'"></div>';
                                                }else{
                                                    $html.='<div class="casino-result-cards-item"><img src="'.$value.'"></div>';
                                                }
                                        
                                        }
                                        $html.='</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-4">
                            <div class="casino-result-desc blue-dark-bg-1">
                                <div class="casino-result-desc-item">
                                    <div>Winner</div>
                                    <div>'.$last_result[$i]['description']['winner'].'</div>
                                </div>
                                <div class="casino-result-desc-item">
                                    <div>Mini Baccarat</div>
                                    <div>'.$last_result[$i]['description']['khal'].'</div>
                                </div>
                                <div class="casino-result-desc-item">
                                    <div>Total</div>
                                    <div>'.$last_result[$i]['description']['total'].'</div>
                                </div>
                                <div class="casino-result-desc-item">
                                    <div>Color Plus</div>
                                    <div>'.$last_result[$i]['description']['pair_plus'].'</div>
                                </div>
                                <div class="casino-result-desc-item">
                                    <div>Red Black</div>
                                    <div>'.$last_result[$i]['description']['red_black'].'</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>';
            }
            $i++;
        }
        return $html;
    }
}