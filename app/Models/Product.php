<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'image_url',
        'image_id',
        'is_active',
        'category_id',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_items')
            ->withPivot(['quantity', 'item_price', 'item_name']);
    }

    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_items')
            ->withPivot(['item_price', 'item_name']);
    }

    public function whatsappDrafts()
    {
        return $this->belongsToMany(WhatsappDraft::class, 'whatsapp_draft_items')
            ->withPivot(['quantity', 'item_price', 'item_name']);
    }
}
