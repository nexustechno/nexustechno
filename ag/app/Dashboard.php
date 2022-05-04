<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Dashboard extends Model
{
	protected $table = 'dashboard';
    protected $fillable = [
        'title','file_name','status','link','width_type'
    ];
}
