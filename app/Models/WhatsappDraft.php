<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappDraft extends Model
{
    protected $fillable = [
        'customer_name',
        'customer_phone',
        'notes',
        'date',
        'total',
        'status',
    ];

    protected static function booted()
    {
        static::saving(function (WhatsappDraft $draft) {
            $draft->total = $draft->whatsappDraftItems->sum(
                fn ($item) => $item->quantity * $item->item_price
            );
        });
    }

    public function recalculateTotal()
    {
        $this->total = $this->whatsappDraftItems()
            ->selectRaw('COALESCE(SUM(quantity * item_price), 0) as aggregate')
            ->value('aggregate');

        $this->saveQuietly();
    }

    public function whatsappDraftItems()
    {
        return $this->hasMany(WhatsappDraftItem::class);
    }
}
