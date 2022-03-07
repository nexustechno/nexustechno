<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class UsersAccount extends Model
{

    protected $table = 'users_account';

    protected $fillable = [
        'user_id', 'user_exposure_log_id', 'from_user_id','to_user_id', 'credit_amount','debit_amount','balance','closing_balance','match_id','bet_user_id','remark','created_at','updated_at'
    ];

    protected $casts = [
          'credit_amount' => 'decimal:2',
          'debit_amount' => 'decimal:2',
          'balance' => 'decimal:2',
          'closing_balance' => 'decimal:2',
    ];

    public static function getUserRemainingBalance($id){
        $totalCreditAmount =  UsersAccount::where("user_id",$id)->sum('credit_amount');
        $totalDebitAmount =  UsersAccount::where("user_id",$id)->sum('debit_amount');

        return number_format($totalCreditAmount - $totalDebitAmount,2,'.','');
    }

    public static function getWebsite(){
        return app('website');
    }

    //Jitendra  :: 08-02-2022
//    public static function boot()
//    {
//        parent::boot();
//
//        self::creating(function($model){
//            // ... code here
//        });
//
//        // updating user available balance
//        self::created(function($model){
////            Log::info("created model");
////            Log::info($model);
//
//            if($model->user_id!=1) {
//
//                $user = User::find($model->user_id);
//                if($user->agent_level!='PL') {
//                    $credit = CreditReference::where("player_id", $model->user_id)->first();
//                    $credit->available_balance_for_D_W = $model->closing_balance;
//                    $credit->remain_bal = $model->closing_balance + $credit->exposure;
//                    $credit->save();
//                }
//            }else{
//                $settingData = setting::latest('id')->first();
//                $settingData->balance = $model->closing_balance;
//                $settingData->update();
//            }
//        });
//
//        self::updating(function($model){
//            // ... code here
//        });
//
//        self::updated(function($model){
//            // ... code here
//        });
//
//        self::deleting(function($model){
//            // ... code here
//        });
//
//        self::deleted(function($model){
//            // ... code here
//        });
//    }
}
