<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class CasinoBet extends Model
{
	protected $table = 'casino_bet';
    protected $fillable = [
        'user_id','casino_name','team_name','team_sid','odds_value','stake_value','casino_profit','result_declare','roundid','bet_side','exposureAmt','winner','cards','extra'
    ];

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }
}
