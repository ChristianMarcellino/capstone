<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'customer_name',
        'customer_phone',
        'notes',
        'pickup_date',
        'total',
        'status',
    ];

    protected static function booted()
    {
        static::saving(function (Order $order) {
            $order->total = $order->orderItems->sum(
                fn ($item) => $item->quantity * $item->item_price
            );
        });
    }

    public function recalculateTotal()
    {
        $this->total = $this->orderItems()
            ->selectRaw('COALESCE(SUM(quantity * item_price), 0) as aggregate')
            ->value('aggregate');

        $this->saveQuietly();
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
