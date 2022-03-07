<?php

namespace App\Http\Controllers;
use App\Agent;
use App\User;
use App\UserHirarchy;
use App\UserExposureLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Auth;
use Carbon\Carbon;
use App\CreditReference;
use Request as resAll;

class AgentController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public static function userBalance($userId)
    {
      $hirUser=UserHirarchy::where('agent_user',$userId)->first();
      $hirUser_bal=0;
      $totalClientBal=0;
      $totalExposure=0;      
      $posTotal=0;
      $negTotal=0;
      $cumulative_pl=0;
      if(!empty($hirUser)){
        $getuserArray = explode(',', $hirUser->sub_user);        
          foreach ($getuserArray as $value_data) {
          $userData = User::where('id',$value_data)->first();
            if(!empty($userData->agent_level)){
           if($userData->agent_level!='PL'){
                $hirUser_bal += CreditReference::where('player_id',$value_data)->sum('available_balance_for_D_W');
            }else{
                $credit_dataclient = CreditReference::where('player_id',$value_data)->select('remain_bal','exposure')->first();                
                if(!empty($credit_dataclient)){
                    $totalClientBal+= $credit_dataclient->remain_bal;
                    if($credit_dataclient->exposure < 0){
                      $posTotal += abs($credit_dataclient->exposure);
                    }else{
                       $negTotal += $credit_dataclient->exposure;
                    }
                    $totalExposure=$posTotal+$negTotal;
                }

              // calculate cumulative PL
                $cumulative_pl_profit_get = UserExposureLog::where('user_id',$value_data)->where('win_type','Profit')->where('bet_type','ODDS')->sum('profit');
                $cumulative_pl_profit = UserExposureLog::where('user_id',$value_data)->where('win_type','Profit')->where('bet_type','!=','ODDS')->sum('profit');
                $cumulative_pl_loss = UserExposureLog::where('user_id',$value_data)->where('win_type','Loss')->sum('loss'); 
                $cumu_n=0;
                $cumu_n = $cumulative_pl_profit_get*($userData->commission)/100; 
                $cumuPL_n = $cumulative_pl_profit_get+$cumulative_pl_profit-$cumu_n;
                $cumulative_pl+=$cumuPL_n-$cumulative_pl_loss;

                   /* $cumuPL = $cumulative_pl_profit-$cumu; 
                 $cumulative_pl+=$cumuPL-$cumulative_pl_loss;*/
                /*echo $cumulative_pl_profit;
                echo "/";
                echo $cumulative_pl_loss;
                echo "/";
                echo $cumulative_pl;
                exit;*/
               
               

            }
        }
        }
        }
         
      return $hirUser_bal.'~'.$totalClientBal.'~'.$totalExposure.'~'.$cumulative_pl;
    }       
    public function store(Request $request)
    {
        $getuser = Auth::user();
        $data = $request->all();
        $getuser = Auth::user();           
        $data['password'] = Hash::make($request['password']);
        $data['parentid'] = $getuser->id;
        $data['first_login'] = 0;
        $data['ip_address'] = resAll::ip();
		  
		    $data['dealy_time'] = $request['odds'];
        if($getuser->agent_level!='COM'){
          $data['dealy_time'] = $getuser->dealy_time;
          $data['bookmaker'] = $getuser->bookmaker;
          $data['fancy'] = $getuser->fancy;
          $data['soccer'] = $getuser->soccer;
          $data['tennis'] = $getuser->tennis;
        }
        /*echo "<pre>";
        print_r($data);
        exit;*/
        $lid=User::create($data);

        $last_id=$lid->id;
    
        $cref=CreditReference::create([
            'player_id' => $last_id,
            'credit' => 0,
            'remain_bal' => 0,
            'available_balance_for_D_W' => 0,
        ]);
        
        $direct_user=0;
        if($getuser->agent_level=='COM'){
            $direct_user=1;
        }

        $gethircount=UserHirarchy::where('agent_user',$getuser->id)->count();
        $gethirUser=UserHirarchy::where('agent_user',$getuser->id)->first();
        

        if($gethircount==0){

            $data_hir['direct_user'] = $direct_user;
            $data_hir['agent_user'] = $getuser->id;
            $data_hir['sub_user'] = $lid->id;
         
           
            UserHirarchy::create($data_hir);
        }else{
            $gethirUser['sub_user'] = $gethirUser->sub_user.','.$lid->id;   
            $gethirUser->update();     
        }
        
         $data_user = UserHirarchy::whereRaw("find_in_set('".$getuser->id."',sub_user)")->get();
            foreach ($data_user as $value) {
              $data_user_upd = UserHirarchy::where('id',$value->id)->first();
              $data_user_upd->sub_user=$value->sub_user.','.$lid->id;
              $data_user_upd->update();  
            }
       return redirect()->route('home')->with('message','Agent created successfully!'); 
    }
    public function getusername(Request $request)
    {
        $uvalue = $request->uvalue; 
        $user = User::where('user_name',$uvalue)->get();
        return response()->json(array('result'=> $user), 200);
    }
	public function storeuser(Request $request)
    {
		$getuser = Auth::user(); 
		$data = $request->all();
        $getuser = Auth::user();           
        $data['password'] = Hash::make($request['password']);
        $data['parentid'] = $getuser->id;
        $data['first_login'] = 0;
        $last_id = User::create($data)->id;
        $cref=CreditReference::create([
            'player_id' => $last_id,
            'credit' => 0,
            'remain_bal' => 0,
            'available_balance_for_D_W' => 0,
        ]);
        return redirect()->route('privileges')
        ->with('message','Agent created successfully.'); 
    }
}
