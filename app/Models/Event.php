<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'name',
        'description',
        'is_active',
        'start_date',
        'end_date',
    ];
    
    public function eventItems()
    {
        return $this->hasMany(EventItem::class);
    }
}
