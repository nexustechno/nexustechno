<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class UserDeposit extends Model
{
	protected $table = 'user_deposites';
	protected $fillable = [
        'balanceType','parent_id','child_id','amount','balance','totalbalance','note','match_id','type','callType','bet_id','extra','fancy_id',
    ];
}
