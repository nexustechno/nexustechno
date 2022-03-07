<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserHirarchy extends Model
{
	protected $table = 'user_hirarchy';
    protected $fillable = [
        'direct_user','sub_user','agent_user'
    ];
}
