<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Usage extends Model
{
    protected $guarded = ['id'];
    protected $table = 'usage';
}
