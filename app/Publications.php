<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Publications extends Model
{
    protected $guarded = ['id'];
    protected $table = 'publications';
}