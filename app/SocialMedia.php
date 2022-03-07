<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class SocialMedia extends Model
{
	protected $table = 'social_media';
	protected $fillable = [
        'em1 ','em2','em3','wa1','wa2','wa3','tl1','tl2','tl3','ins1','ins2','ins3','sk1','sk2','sk3'
    ];
}
