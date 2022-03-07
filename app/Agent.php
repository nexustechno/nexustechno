<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    protected $fillable = [
        'agent_level', 'user_name', 'password','first_name','last_name','commission','time_zone'
    ];
}
