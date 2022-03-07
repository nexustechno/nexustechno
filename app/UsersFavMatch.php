<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class UsersFavMatch extends Model
{
    protected $table = 'users_fav_matches';

    protected $fillable = [
        'user_id', 'match_id'
    ];
}
