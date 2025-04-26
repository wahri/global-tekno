<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $guarded = [
        'id',
    ];

    
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
