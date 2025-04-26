<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleCart extends Model
{
    protected $guarded = ['id'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
