<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class MyBets extends Model
{
    protected $table = 'my_bets';
    protected $fillable = [
        'sportID','match_id','bet_type','bet_side','bet_odds','bet_amount','bet_profit','team_name','exposureAmt','ip_address','browser_details','result_declare',
    ];
}
