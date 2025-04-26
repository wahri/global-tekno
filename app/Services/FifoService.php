<?php

namespace App\Services;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\SaleItem;
use App\Models\SaleItemDetail;
use Illuminate\Support\Facades\DB;

class FifoService
{
    public function calculateHpp($saleItem)
    {
        $productId = $saleItem->product_id;
        $qtyToSell = $saleItem->quantity;
        $hppTotal = 0;

        DB::beginTransaction();

        try {
            $purchases = PurchaseItem::where('product_id', $productId)
                ->where('remaining_quantity', '>', 0)
                ->orderBy('created_at') // FIFO
                ->lockForUpdate()
                ->get();

            foreach ($purchases as $purchase) {
                if ($qtyToSell <= 0) break;

                $available = $purchase->remaining_quantity;
                $takeQty = min($available, $qtyToSell);
                $hppTotal += $takeQty * $purchase->price;

                $purchase->remaining_quantity -= $takeQty;
                $purchase->save();

                SaleItemDetail::create([
                    'sale_item_id' => $saleItem->id,
                    'purchase_item_id' => $purchase->id,
                    'quantity' => $takeQty,
                ]);

                $qtyToSell -= $takeQty;
            }

            if ($qtyToSell > 0) {
                throw new \Exception("Stok tidak cukup untuk penjualan ini.");
            }

            $saleItem->hpp = $hppTotal;
            $saleItem->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function rollback(SaleItem $saleItem)
    {
        DB::beginTransaction();

        try {
            // Ambil semua detail penjualan
            $details = $saleItem->details;

            foreach ($details as $detail) {
                $purchase = $detail->purchaseItem;

                // Tambahkan kembali quantity ke sisa stok pembelian
                $purchase->remaining_quantity += $detail->quantity;
                $purchase->save();

                // Hapus detail pemakaian stok
                $detail->delete();
            }

            // Hapus penjualan utama
            $saleItem->delete();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
