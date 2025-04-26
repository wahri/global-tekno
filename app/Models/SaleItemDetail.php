<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleItemDetail extends Model
{
    protected $guarded = ['id'];

    public function saleItem()
    {
        return $this->belongsTo(SaleItem::class);
    }

    public function purchaseItem()
    {
        return $this->belongsTo(PurchaseItem::class);
    }
}
