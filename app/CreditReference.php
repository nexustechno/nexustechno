<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class CreditReference extends Model
{
    protected $table = 'credit_reference';
    protected $fillable = [
        'player_id','credit','remain_bal','exposure','ref_pl','cumulative','status','available_balance_for_D_W'
    ];
}
