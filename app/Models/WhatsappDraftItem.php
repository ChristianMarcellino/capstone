<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappDraftItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'whatsapp_draft_id',
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
        static::saved(fn (WhatsappDraftItem $item) => $item->whatsappDraft?->recalculateTotal());
        static::deleted(fn (WhatsappDraftItem $item) => $item->whatsappDraft?->recalculateTotal());
    }

    public function whatsappDraft()
    {
        return $this->belongsTo(WhatsappDraft::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
