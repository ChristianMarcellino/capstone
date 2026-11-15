<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'event_id',
        'product_id',
        'item_price',
        'item_name',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
