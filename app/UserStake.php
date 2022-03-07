<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class UserStake extends Model
{
	protected $table = 'user_stake';
	protected $fillable = [
        'user_id','stake',
    ];
}
