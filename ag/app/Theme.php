<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
	protected $table = 'theme';
    protected $fillable = [
        'theme_name','header_theme', 'main_theme','status'
    ];
}
