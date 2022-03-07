<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class setting extends Model
{
    protected $fillable = [
        'agent_msg','user_msg', 'maintanence_msg','balance'
    ];
}
