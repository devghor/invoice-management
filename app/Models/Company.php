<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'name_en',
        'name_bn',
        'address',
        'mobel_no',
        'email',
    ];
}
