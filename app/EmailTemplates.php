<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailTemplates extends Model
{
    protected $guarded = ['id'];
    protected $table = 'email_templates';
}
