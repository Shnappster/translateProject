<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Catalog extends Model
{
    protected $guarded = ['id'];
    protected $table = 'catalog';
}
