<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Sport extends Model
{
    protected $fillable = [
        'sId','sport_name','status'
    ];

    public function matches(){
        return $this->hasMany(Match::class,'sports_id','sId')->where('event_id',">",0)->where('winner', null)->orderby('match_date', 'asc');
    }
}
