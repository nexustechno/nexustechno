<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Sport;
use App\setting;
use App\Match;
use App\Casino;
use GuzzleHttp\Client;
use Carbon\Carbon;
use App\MyBets;
use Auth;
use DB;
use Session;
use App\User;
use Illuminate\Support\Facades\Hash;

class ScoreBoardController extends Controller
{
	public function getScoreBoard(Request $request)
	{
		$mid =  $request->match_id;
		$matchtype=$request->match_type;
		$sport = Sport::where('sId',$matchtype)->first();
		$matchList = Match::where('id',$mid)->where('status',1)->first();
		$eventId=$matchList->event_id;

		$html=''; $clrcls = '';$html1='';

		$getUserCheck = Session::get('playerUser');
	    if(!empty($getUserCheck)){
	      $sessionData = User::where('id',$getUserCheck->id)->where('check_login',1)->first();
	    }

		if(!empty($sessionData))
		{

			if($matchtype == 4)
			{
				$url='http://18.135.67.118:8081/scoreApi/'.$eventId;
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
					$html.='
						<div class="scorecard">
			            	<div class="scorecard-row">
			            		<div class="row row5">
		                        	<div class="col-6 text-lg-left pl-0">
		                            	<div><b>'.$match_data['spnnation1'].'</b> '.$match_data['score1'].'</div> 
		                            	<div><span>'; if(!empty($match_data['spnrunrate1'])){$html.='CRR '.$match_data['spnrunrate1'].' ';}$html.='</span>
		                            		<span>'; if(!empty($match_data['spnreqrate1'])){$html.='RR '.$match_data['spnreqrate1'].' ';}$html.='</span>
		                            	</div>
		                            </div>
		                            <div class="col-6 text-lg-right pr-0">
		                            	<div><b>'.$match_data['spnnation2'].' </b> '.$match_data['score2'].'</div>
		                            	<div><span>'; if(!empty($match_data['spnrunrate2'])){$html.='CRR '.$match_data['spnrunrate2'].' ';}$html.='</span>
		                            	<span>'; if(!empty($match_data['spnreqrate2'])){$html.='RR '.$match_data['spnreqrate2'].' ';}$html.='</span></div>
		                            </div>
		                        </div>
			                </div>
			                <div class="scorecard-row">
			                	<div class="row row5">
			                    	<div class="col-lg-6 col-sm-12 col-6 text-left pl-0"> 
			                        	<span class="score-detail">Day '.$match_data['dayno'].' | '.$match_data['spnmessage'].'</span> 
			                        </div>
			                        <div class="col-lg-6 col-sm-12 col-6 text-lg-right text-right pr-0">';
			                        for($i=0; $i<=5; $i++){
			                        	if($match_data['balls'][$i] == 4){
			                        		$clrcls = 'four';
			                        	}
			                        	elseif($match_data['balls'][$i] == 6){
			                        		$clrcls = 'six';
			                        	}
			                        	elseif($match_data['balls'][$i] == 'ww'){
			                        		$clrcls = 'wicket';
			                        	}
			                        	elseif($match_data['balls'][$i] == 'w'){
			                        		$clrcls = 'yellow';
			                        	}
			                        	else{
			                        		$clrcls = '';
			                        	}
			                        	$html.='<span class="ball-runs mr-1 '.$clrcls.'">'.$match_data['balls'][$i].'
			                        	</span>';
			                        }
			                        $html.='</div>
			                    </div>
			                </div>
			            </div>
					';
				}
			}
			if($matchtype == 2)
			{
				$url='http://23.106.234.25:8081/betFairApiScore/'.$eventId;
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
					$html.='
						<div class="tennis-header-container">
	                    	<div class="row">
	                        	<div class="col-lg-6 col-sm-6 tennis_left_side_team">
	                            	<div class="tennis-star-wraper"> 
	                                	<span class="tennis-star"><i class="fas fa-star"></i></span> 
	                                </div>
	                            	<div class="tennis-runners">
	                                    <span class="tennis-runner-name">'.$match_data['data'][0]['score']['home']['name'].' ';
	                                    if($match_data['data'][0]['score']['home']['isServing'] == 'true'){
		                                	$html.='<span class="circle away-highlight"></span>';
		                                }
		                                $html.='</span>
	                                    <span class="tennis-runner-name">'.$match_data['data'][0]['score']['away']['name'].'';
	                                    if($match_data['data'][0]['score']['away']['isServing'] == 'true'){
		                                	$html.='<span class="circle away-highlight"></span>';
		                                }
		                                $html.='</span>
	                                </div>
	                                <div class="abstract-sports-footer">
	                                	<span class="match-finished">Set '.$match_data['data'][0]['currentSet'].'</span>
	                                </div>
	                            </div>
	                            <div class="col-lg-3 col-sm-6 tennis_right_side_score">
	                            	<div class="scores tennis-points">
	                                	<div class="home">
	                                		<div class="cell"> <span class="highlight">'.$match_data['data'][0]['score']['home']['sets'].'</span> </div>
	                                    </div>
	                                    <div class="away">
	                                		<div class="cell"> <span class="highlight">'.$match_data['data'][0]['score']['away']['sets'].'</span> </div>
	                                    </div>
	                                    <div class="description">
	                                		<div class="cell"> <span class="highlight">Points</span> </div> 
	                                    </div>
	                                </div>
	                            	<div class="scores tennis-sets">
	                                	<div class="home">
	                                    	';
	                                    	if($match_data['data'][0]['currentSet'] == 1)
			                            	{
				                            	if(!empty($match_data['data'][0]['score']['home']['gameSequence'][0])){
				                                	$html.='<div class="cell"> '.$match_data['data'][0]['score']['home']['gameSequence'][0].' </div>';
				                                }
				                                else{
				                                	$html.='<div class="cell"> '.$match_data['data'][0]['score']['home']['games'].' </div>';
				                                }
				                            }
				                            else{
				                            	$html.='<div class="cell"> </div>';
				                            }

				                            if($match_data['data'][0]['currentSet'] == 2)
			                            	{
				                            	if(!empty($match_data['data'][0]['score']['home']['gameSequence'][1])){
				                                	$html.='<div class="cell"> '.$match_data['data'][0]['score']['home']['gameSequence'][1].' </div>';
				                                }
				                                else{
				                                	$html.='<div class="cell"> '.$match_data['data'][0]['score']['home']['games'].' </div>';
				                                }
				                            }
				                            else{
				                            	$html.='<div class="cell"> </div>';
				                            }
				                            if($match_data['data'][0]['currentSet'] == 3)
				                            {
				                                if(!empty($match_data['data'][0]['score']['home']['gameSequence'][1])){
				                                	$html.='<div class="cell"> '.$match_data['data'][0]['score']['home']['gameSequence'][2].' </div>';
				                                }
				                                else{
				                                	$html.='<div class="cell"> '.$match_data['data'][0]['score']['home']['games'].' </div>';
				                                }
				                            }
				                            else{
				                            	$html.='<div class="cell"> </div>';
				                            }
	                                    	$html.='
	                                    </div>
	                                    <div class="away">
	                                    	';
	                                    	if($match_data['data'][0]['currentSet'] == 1)
			                                {
				                                if(!empty($match_data['data'][0]['score']['away']['gameSequence'][0])){
				                                	$html.='<div class="cell"> '.$match_data['data'][0]['score']['away']['gameSequence'][0].' </div>';
				                                }
				                                else{
				                                	$html.='<div class="cell"> '.$match_data['data'][0]['score']['away']['games'].' </div>';
				                                }
				                            }
				                            else{
				                            	$html.='<div class="cell"> </div>';
				                            }
				                            if($match_data['data'][0]['currentSet'] == 2)
				                            {
				                                if(!empty($match_data['data'][0]['score']['away']['gameSequence'][1])){
				                                	$html.='<div class="cell"> '.$match_data['data'][0]['score']['away']['gameSequence'][1].' </div>';
				                                }
				                                else{
				                                	$html.='<div class="cell"> '.$match_data['data'][0]['score']['away']['games'].' </div>';
				                                }
				                            }
				                            else{
				                            	$html.='<div class="cell"> </div>';
				                            }
				                            if($match_data['data'][0]['currentSet'] == 3)
				                            {
				                                if(!empty($match_data['data'][0]['score']['away']['gameSequence'][2])){
				                                	$html.='<div class="cell"> '.$match_data['data'][0]['score']['away']['gameSequence'][2].' </div>';
				                                }
				                                else{
				                                	$html.='<div class="cell"> '.$match_data['data'][0]['score']['away']['games'].' </div>';
				                                }
	                            			}
	                            			else{
				                            	$html.='<div class="cell"> </div>';
				                            }
	                                    	$html.='
	                                     
	                                    </div>
	                                    <div class="description">
	                                    	<div class="cell"> 1 </div>
	                                        <div class="cell"> 2 </div>
	                                        <div class="cell"> 3 </div>
	                                    </div>
	                                </div>
	                            </div>
	                            <div class="col-lg-3">
	                            	<div class="button-container">
	                                	<a class="icon-livestream"> Live Stream </a>
	                                    <a class="icon-head2head"> Head to Head </a>
	                                    <a class="icon-multiples"> Multiples </a>
	                                </div>
	                            </div>
	                        </div>
	                    </div>
					';
				}
			}
			if($matchtype == 1)
			{
				$url='http://23.106.234.25:8081/betFairApiScore/'.$eventId;
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
					$html.='
						<div class="soccer-header-container">
	                    	<div class="row">
	                       		<div class="col-lg-6">
	                            	<div class="soccer-star-wraper"> 
	                                	<span class="soccer-star"><i class="fas fa-star"></i></span> 
	                                </div>
	                            	<div class="soccer-runners">
	                                    <span class="soccer-runner-name">'.$match_data['data'][0]['score']['home']['name'].' </span>
	                                    <span class="score finished"> '.$match_data['data'][0]['score']['home']['score'].'-'.$match_data['data'][0]['score']['away']['score'].' </span>
	                                    <span class="soccer-runner-name">'.$match_data['data'][0]['score']['away']['name'].'</span>
	                                    
	                                </div>
	                                <div class="time-elapsed">
	                                	<p class="match-finished">
	                                	<span>'.$match_data['data'][0]['timeElapsed'].'</span>
	                                	'.$match_data['data'][0]['matchStatus'].'
	                                    	<span class="halftime-fulltime"> (HT '.$match_data['data'][0]['score']['home']['score'].'-'.$match_data['data'][0]['score']['away']['score'].') </span>
	                                    </p>
	                                </div>
	                            </div>
	                            <div class="col-lg-6">
	                               	<div class="button-container">
										<a class="icon-livestream"> Live Stream </a>
										<a class="icon-head2head"> Head to Head </a>
	                                    <a class="icon-multiples"> Multiples </a>
									</div>
								</div>
							</div>
						</div>
	                    <div class="timeline-penalties-container">
	                    	<div class="timeline-container">
	                        	<div class="timeline">
	                            	<div class="half firstHalf">
	                                	<div class="empty-bar">
	                                    	<div class="bf-tooltip-parent Goal away tooltip">
	                                        	<p>
	                                            	<span class="match-time">60</span>
	                                            	<span class="update-type Goal"></span>
	                                            	<span class="team-name">Rudes U19</span>
	                                            </p>
	                                        </div>
	                                        <div class="bar" style="width: 100%;"></div>
	                                    </div>
	                                </div>
	                                <div class="half firstHalf">
	                                	<div class="empty-bar">
	                                    	<div class="bf-tooltip-parent Goal away tooltip" style="left: 10%;">
	                                        	<p>
	                                            	<span class="match-time">60</span>
	                                            	<span class="update-type Goal"></span>
	                                            	<span class="team-name">Rudes U19</span>
	                                            </p>
	                                        </div>
	                                        <div class="bf-tooltip-parent Goal away tooltip" style="left: 33.33%;">
	                                        	<p>
	                                            	<span class="match-time">60</span>
	                                            	<span class="update-type Goal"></span>
	                                            	<span class="team-name">Rudes U19</span>
	                                            </p>
	                                        </div>
	                                        <div class="bf-tooltip-parent Goal away tooltip" style="left: 93.33%;">
	                                        	<p>
	                                            	<span class="match-time">60</span>
	                                            	<span class="update-type Goal"></span>
	                                            	<span class="team-name">Rudes U19</span>
	                                            </p>
	                                        </div>
	                                        
	                                        <div class="bar" style="width: 50%;"></div>
	                                    </div>
	                                </div>
	                            </div>
	                        </div>
	                    </div>
					';
				}
			}
		}

		$html1.=''.$matchtype.'';
		return $html.'~~'.$html1;
	}
}
?>