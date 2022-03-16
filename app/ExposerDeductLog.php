<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExposerDeductLog extends Model
{
    protected $fillable = [
        'user_id', 'action', 'current_exposer','new_exposer','exposer_deduct','match_id','bet_type','bet_amount','odds_value','odds_volume','profit','lose'
    ];
}
