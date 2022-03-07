<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class UserExposureLog extends Model
{
	protected $table = 'user_exposure_log';
	protected $fillable = [
        'match_id','user_id','bet_type','profit','loss',
    ];
}
