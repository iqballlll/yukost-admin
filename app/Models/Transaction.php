<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'outlet_id',
        'transaction_id',
        'order_date',
        'due_date',
        'payment_date',
        'tukar_faktur',
        'total_price',
        'is_paid',
        'discount_type',
        'discount_amount',
        'total_price_after_discount',
        'created_by',
        'updated_by'
    ];
    protected $dates = ['deleted_at'];

    public function outlet()
    {
        return $this->belongsTo(CustomerOutlet::class, 'outlet_id');
    }

    public function details()
    {
        return $this->hasMany(TransactionDetail::class, 'transaction_id');
    }
}
