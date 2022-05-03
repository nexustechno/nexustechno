<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email','user_name', 'premium', 'email', 'password','agent_level','first_name','last_name','commission','partnership_perc','time_zone','list_client','main_market','manage_fancy','fancy_history','match_history','parentid','first_login','dealy_time','ip_address','sports_main_market','my_account','my_report','bet_list','bet_list_live','live_casino','risk_management','player_banking','agent_banking','sports_leage','add_balance','message','casino_manage','check_updpass','token_val','odds','bookmaker','fancy','soccer','tennis'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
