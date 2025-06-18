<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerCompany extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'group_id',
        'company_id',
        'company_name',
        'address',
        'contact',
        'invoice_exchange',
        'is_active',
        'created_by',
        'updated_by'
    ];

    protected $dates = ['deleted_at'];

    public function group()
    {
        return $this->belongsTo(CustomerGroup::class, 'group_id');
    }

    public function outlets()
    {
        return $this->hasMany(CustomerOutlet::class, 'company_id', 'id');
    }
}
