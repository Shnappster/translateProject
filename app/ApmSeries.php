<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ApmSeries extends Model
{
    protected $guarded = ['id'];
    protected $table = 'apm_series';
}
