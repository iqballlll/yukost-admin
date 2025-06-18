<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $fillable = [
        'supplier_name',
        'address',
        'contact',
        'is_active'
    ];
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}
