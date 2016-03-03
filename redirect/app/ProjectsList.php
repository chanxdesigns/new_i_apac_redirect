<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectsList extends Model
{
    protected $table = "projects_list";
    protected $fillable = [
        'Completes',
        'Terminates',
        'Quotafull'
    ];
}
