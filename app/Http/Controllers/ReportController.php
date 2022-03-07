<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\User;
use Redirect;
use Auth;
use App\Match;
use App\MyBets;
Use DB;
use App\UserExposureLog;

class ReportController extends Controller
{
  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
    $this->middleware('auth');
  }

  /**
   * Show the application dashboard.
   *
   * @return \Illuminate\Contracts\Support\Renderable
   */
  public function index()
  {
    $user = User::where('agent_level','PL')->get();
    return view('backpanel.commision-report',compact('user'));	        
  }
  public function getCommissionReport(Request $request )
  {   
    $date_from = date('Y-m-d',strtotime($request->date_from));
    $date_to = date('Y-m-d',strtotime($request->todate));
    $userId = $request->userName;   
    $profitSum=0;
    $commissionpro=0;
     $draw = $request->get('draw');
     $start = $request->get("start");
     $rowperpage = $request->get("length"); // Rows display per page
    
      $columnIndex_arr = $request->get('order');
     $columnName_arr = $request->get('columns');
     $order_arr = $request->get('order');
     $search_arr = $request->get('search');

    $searchValue = $search_arr['value']; // Search value

    $html='';
    if(!empty($userId)){
    
     $getresult = User::select('users.id','users.commission')->join('user_exposure_log','user_exposure_log.user_id','=','users.id')->where('users.id',$userId)->whereBetween('user_exposure_log.created_at',[$date_from,$date_to])->where('users.agent_level','=','PL')->where('user_exposure_log.win_type','Profit')->where('user_exposure_log.bet_type','ODDS')->where('users.user_name', 'like', '%' .$searchValue . '%')->skip($start)->take($rowperpage)->groupBy('user_exposure_log.user_id')->orderBy('users.user_name')->where('users.agent_level','!=','COM')->get();


   $getresultcount = User::select('users.id','users.commission')->join('user_exposure_log','user_exposure_log.user_id','=','users.id')->where('users.id',$userId)->whereBetween('user_exposure_log.created_at',[$date_from,$date_to])->where('users.agent_level','=','PL')->where('user_exposure_log.win_type','Profit')->where('user_exposure_log.bet_type','ODDS')->where('users.user_name', 'like', '%' .$searchValue . '%')->groupBy('user_exposure_log.user_id')->orderBy('users.user_name')->where('users.agent_level','!=','COM')->get();

    $getresultcounttot = count($getresultcount);
    }else{
     // DB::enableQueryLog();
      $getresult = User::select('users.id','users.commission')->join('user_exposure_log','user_exposure_log.user_id','=','users.id')->whereBetween('user_exposure_log.created_at',[$date_from,$date_to])->where('users.agent_level','=','PL')->where('user_exposure_log.win_type','Profit')->where('user_exposure_log.bet_type','ODDS')->where('users.user_name', 'like', '%' .$searchValue . '%')->skip($start)->take($rowperpage)->groupBy('user_exposure_log.user_id')->orderBy('users.user_name')->where('users.agent_level','!=','COM')->get();
     
     $getresultcount = User::select('users.id','users.commission')->join('user_exposure_log','user_exposure_log.user_id','=','users.id')->whereBetween('user_exposure_log.created_at',[$date_from,$date_to])->where('users.agent_level','=','PL')->where('user_exposure_log.win_type','Profit')->where('user_exposure_log.bet_type','ODDS')->where('users.user_name', 'like', '%' .$searchValue . '%')->groupBy('user_exposure_log.user_id')->where('users.agent_level','!=','COM')->get();

      $getresultcounttot = count($getresultcount);      
    }    
  
    // Total records
     $totalRecords = $getresultcounttot;
     
    $totalRecordswithFilter = Match::select('count(*) as allcount')->where('match_name', 'like', '%' .$searchValue . '%')->count();

    $count=1;
    $totalcomm=0;
    $totalcommperc=0;
    
    $betparray = array();
     $data_arr = array();
     foreach($getresult as $value){    
      $userData = User::find($value->id);         
      $commission=0;  
      $ccv=0;  
      $cc=0;

      $cumulative_pl_profit_get = UserExposureLog::where('user_id',$userData->id)->where('win_type','Profit')->whereBetween('created_at',[$date_from,$date_to])->where('bet_type','ODDS')->sum('profit');
    
    $getallBet = UserExposureLog::where('user_id',$userData->id)->whereBetween('created_at',[$date_from,$date_to])->where('bet_type','ODDS')->where('win_type','Profit')->get();

      $cumu_n=0;
      $cumu_n += $cumulative_pl_profit_get*($value->commission)/100;     
   
      $cc+=$ccv;
      $totalcommperc+= $userData->commission;
      $totalcomm+= $commission;
      //if($cumu_n != 0){
       $username = '<span><a data-id="'.$userData->id.'" data-name="'.$userData->user_name.'" onclick="openReport(this);">'.$userData->user_name.'</a></span>';
       $commissionPer = '<span class="text-color-green">'.$userData->commission.'%</span>';
       $totalcommRe = '<span class="text-color-green"><b>'.number_format($cumu_n, 2).'</b></span>';
        $data_arr[] = array(
          "id" => $count,
          "username" => $username,
           "ComPresentage" => $commissionPer,
          "commission" => $totalcommRe
         
        );
        $count++;
    

     }

     $response = array(
        "draw" => intval($draw),
        "iTotalRecords" => $totalRecords,
        "iTotalDisplayRecords" => $totalRecords,
        "aaData" => $data_arr
     );

     echo json_encode($response);
     exit;
    
    //return response()->json(array('result'=> 'success','html'=>$html));      
  }
  public function getCommissionPopup(Request $request)
  {
    $userId = $request->userId;
    $date_from = date('d-m-Y',strtotime($request->date_from));
    $date_to = date('d-m-Y',strtotime($request->todate));

    //$date_to = date("d-m-Y", strtotime($date_to1 ."+1 day"));
    /*echo $date_from;
    echo "---";
    echo $date_to;
    exit;*/
    $html='';
    $count=1;
   
   //DB::enableQueryLog();
    $getresultpopup = MyBets::select('match.match_date','match.event_id','match.id as mid','my_bets.user_id','match.match_name','my_bets.team_name','match.winner','my_bets.bet_profit')->join('match','match.event_id','=','my_bets.match_id')->where('my_bets.bet_type','ODDS')->where('match.winner','!=',Null)->where('my_bets.user_id',$userId)->groupBy('my_bets.match_id')->whereBetween('match.match_date', [$date_from, $date_to])->get();
    //dd(DB::getQueryLog()); 
   /* echo "<pre>";
    print_r($getresultpopup);
    exit;*/
    $userData = User::find($userId);

    $profitSumarray=array();
    $totalProfit=0;
    $totalCommission=0;
    //echo "<pre>"; print_r($getresultpopup);echo"<pre>";exit;
    foreach ($getresultpopup as $valuepopup) 
    {
     
      //echo $valuepopup; 
	    $profitSum=0;
	    $getallBet = UserExposureLog::where('match_id',$valuepopup->mid)->where('user_id',$valuepopup->user_id)->where('bet_type','ODDS')->where('win_type','Profit')->first();
    
    
	     $commission=0;
       if(!empty($getallBet)){
    	if($getallBet->profit>0)
    		$commission = $getallBet->profit*$userData->commission/100;
    	  $totalProfit += $getallBet->profit;
        $totalCommission += $commission;
       	$date = $valuepopup->match_date;
        $html.='<tr>
            <td>'.$count.'.</td>
              <td> '.$valuepopup->match_name.' '.$date.'</td>
              <td class="text-color-green"> '.$getallBet->profit.'</td>
              <td class="text-color-green"> '.number_format($commission, 2).'</td>
          </tr> ';
          $count++;
        }
    }
    $html.=' <tr>
      <td colspan="2" class="text-right"> <b> Grand Total </b> </td>
        <td class="text-color-green"> <b>'.$totalProfit.'</b> </td>
        <td class="text-color-green"> <b>'.number_format($totalCommission, 2).'</b> </td>
    </tr>';
    return response()->json(array('result'=> 'success','html'=>$html));        
  }   
}
