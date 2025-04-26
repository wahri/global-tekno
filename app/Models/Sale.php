<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $guarded = ['id'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
    public function cashier()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }
}
