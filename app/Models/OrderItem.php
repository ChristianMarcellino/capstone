<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'item_price',
        'item_name',
    ];

    protected $appends = ['subtotal'];

    public function getSubtotalAttribute()
    {
        return $this->quantity * $this->item_price;
    }

    protected static function booted()
    {
        static::saved(fn (OrderItem $item) => $item->order?->recalculateTotal());
        static::deleted(fn (OrderItem $item) => $item->order?->recalculateTotal());
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
