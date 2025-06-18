<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerGroup extends Model
{
    use SoftDeletes;


    protected $fillable = [
        'group_id',
        'group_name',
        'address',
        'contact',
        'created_by',
        'updated_by'
    ];

    protected $dates = ['deleted_at'];

    public function companies()
    {
        return $this->hasMany(CustomerCompany::class, 'group_id');
    }
}
