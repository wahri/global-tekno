<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseCart extends Model
{
    protected $guarded = ['id'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
