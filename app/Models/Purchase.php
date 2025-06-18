<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'purchase_id',
        'supplier_id',
        'order_date',
        'due_date',
        'payment_date',
        'total_price',
        'is_paid',
        'created_by',
        'updated_by'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchaseDetails()
    {
        return $this->hasMany(PurchaseDetail::class);
    }

    protected $dates = ['deleted_at'];
}
