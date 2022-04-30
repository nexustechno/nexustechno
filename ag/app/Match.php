<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Match extends Model
{
    protected $table = 'match';
    protected $fillable = [
        'match_name','match_date','match_id','score-url','tv','bookmaker','fancy','inplay','sports_id','event_id','is_draw','leage_name','match_finish',
        'odds_limit','min_bet_odds_limit','max_bet_odds_limit','min_bookmaker_limit','max_bookmaker_limit','min_fancy_limit','max_fancy_limit',
        'min_premium_limit','max_premium_limit'
    ];

    public function bets(){
        return $this->hasMany(MyBets::class,'match_id','event_id');
    }
}
