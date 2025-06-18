<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerOutlet extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'company_id',
        'outlet_id',
        'outlet_name',
        'address',
        'contact',
        'type',
        'custom_price',
        'is_active',
        'created_by',
        'updated_by'
    ];

    protected $dates = ['deleted_at'];

    public function company()
    {
        return $this->belongsTo(CustomerCompany::class, 'company_id');
    }

    public function sales()
    {
        return $this->hasMany(Transaction::class, 'outlet_id');
    }
}
