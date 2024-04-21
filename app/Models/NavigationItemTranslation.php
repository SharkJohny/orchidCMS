<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NavigationItemTranslation extends Model
{
    protected $fillable = ['navigation_item_id', 'locale', 'title'];

    public function item()
    {
        return $this->belongsTo(NavigationItem::class);
    }
}
