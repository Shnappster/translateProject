<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductApplicability extends Model
{
    protected $guarded = ['id'];
    protected $table = 'product_applicability';
}
