<?php

namespace App;
use Illuminate\Database\Eloquent\Model;

class ManageTv extends Model
{
    protected $fillable = [
        'channel1', 'channel2', 'channel3','channel4','channel5','cs1','cs2','cs3','cs4','cs5'
    ];
}