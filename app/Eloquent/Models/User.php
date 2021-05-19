<?php

namespace App\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $guarded = ['id'];

    public $timestamps = false;
}
