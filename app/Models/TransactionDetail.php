<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionDetail extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'product_id',
        'transaction_id',
        'selling_price',
        'quantity',
        'total_price',
        'created_by',
        'updated_by'

    ];
    protected $dates = ['deleted_at'];

    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }
}
