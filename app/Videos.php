<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Videos extends Model
{
    protected $guarded = ['id'];
    protected $table = 'videos';
}
