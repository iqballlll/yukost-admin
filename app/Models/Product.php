<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'product_id',
        'product_name',
        'stock',
        'base_price',
        'selling_price',
        'min_stock',
        'created_by',
        'updated_by',
    ];

    public function transactionDetails()
    {
        return $this->hasMany(TransactionDetail::class);
    }
    public function purchaseDetails()
    {
        return $this->hasMany(PurchaseDetail::class);
    }
}
