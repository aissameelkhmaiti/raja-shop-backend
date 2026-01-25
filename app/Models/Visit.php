<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
     protected $fillable = [
        'visitor_id',
        'page',
        'device',
        'ip',
    ];
}
