<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class FancyResult extends Model
{
    protected $fillable = [
        'bet_id', 'fancy_name', 'result','match_id','eventid',
    ];
}
