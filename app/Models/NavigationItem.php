<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NavigationItem extends Model
{
    protected $fillable = ['navigation_id', 'url', 'parent_id', 'order'];

    public function navigation()
    {
        return $this->belongsTo(Navigation::class);
    }

    public function translations()
    {
        return $this->hasMany(NavigationItemTranslation::class, 'navigation_item_id');
    }
}
