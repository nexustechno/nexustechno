<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Website extends Model
{
    protected $fillable = [
        'title','domain','status','favicon','logo','login_image','themeClass','enable_partnership','currency','admin_status'
    ];

    protected $casts = [
        'enable_partnership' => 'integer'
    ];
}
