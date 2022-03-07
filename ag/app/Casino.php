<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class Casino extends Model
{
	protected $table = 'casino';
    protected $fillable = [
        'casino_title','casino_name','casino_image', 'casino_link','status','min_casino','max_casino'
    ];
}
