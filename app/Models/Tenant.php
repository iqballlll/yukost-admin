<?php

namespace App\Models;

use App\Helpers\AppHelpers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Tenant extends Authenticatable
{
    protected $keyType = 'string';

    protected $increment = false;
    protected $fillable = ['id', 'name', 'email', 'password', 'status', 'note'];

    protected $hidden = ['password'];

}
