<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RespCounter extends Model
{
    protected $table = "resp_counters";

    protected $fillable = [
        'respid',
        'projectid',
        'about',
        'Languageid',
        'status',
        'IP',
        'enddate'
    ];
}
