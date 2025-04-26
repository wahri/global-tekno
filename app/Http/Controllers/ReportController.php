<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Services\FifoService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function sales(Request $request)
    {
        $salesQuery = Sale::with(['saleItems', 'saleItems.product', 'cashier']);

        // Filter by start and end date
        if ($request->has('start_date') && $request->has('end_date')) {
            $salesQuery->whereBetween('created_at', [
                $request->input('start_date'),
                $request->input('end_date')
            ]);
        } else if ($request->has('month')) {
            $salesQuery->whereMonth('created_at', date('m', strtotime($request->input('month'))))
                ->whereYear('created_at', date('Y', strtotime($request->input('month'))));
        }

        $sales = $salesQuery->get();


        return view('pages.report.sales', compact('sales'));
    }

    public function deleteSales(string $id, FifoService $fifoService)
    {
        $sale = Sale::findOrFail($id);

        // Delete the sale items associated with the sale
        foreach ($sale->saleItems as $item) {
            $item->product->increment('stock', $item->quantity);

            $fifoService->rollback($item);

            $item->delete();
        }

        $sale->delete();

        return redirect()->back()->with('success', 'Invoice deleted successfully');
    }

    public function saleItems(Request $request)
    {
        $salesQuery = SaleItem::query();

        // Filter by start and end date
        if ($request->has('start_date') && $request->has('end_date')) {
            $salesQuery->whereHas('sale', function ($query) use ($request) {
                $query->whereBetween('created_at', [
                    $request->input('start_date'),
                    $request->input('end_date')
                ]);
            });
        } else if ($request->has('month')) {
            $salesQuery->whereHas('sale', function ($query) use ($request) {
                $query->whereMonth('created_at', date('m', strtotime($request->input('month'))))
                    ->whereYear('created_at', date('Y', strtotime($request->input('month'))));
            });
        }

        // filter by category product
        if ($request->has('category_id')) {
            $salesQuery->whereHas('product', function ($query) use ($request) {
                $query->where('category_id', $request->input('category_id'));
            });
        }

        // filter by month and year


        $saleItems = $salesQuery->get();

        $categories = Category::all();


        return view('pages.report.sale-items', compact('saleItems', 'categories'));
    }


    public function purchases(Request $request)
    {
        $purchaseQuery = Purchase::query();

        // Filter by start and end date
        if ($request->has('start_date') && $request->has('end_date')) {
            $purchaseQuery->whereBetween('created_at', [
                $request->input('start_date'),
                $request->input('end_date')
            ]);
        } else if ($request->has('month')) {
            $purchaseQuery->whereMonth('created_at', date('m', strtotime($request->input('month'))))
                ->whereYear('created_at', date('Y', strtotime($request->input('month'))));
        }

        $purchases = $purchaseQuery->get();


        return view('pages.report.purchases', compact('purchases'));
    }

    public function purchaseItems(Request $request)
    {
        $purchaseQuery = PurchaseItem::query();

        // Filter by start and end date
        if ($request->has('start_date') && $request->has('end_date')) {
            $purchaseQuery->whereHas('purchase', function ($query) use ($request) {
                $query->whereBetween('created_at', [
                    $request->input('start_date'),
                    $request->input('end_date')
                ]);
            });
        }else if ($request->has('month')) {
            $purchaseQuery->whereHas('purchase', function ($query) use ($request) {
                $query->whereMonth('created_at', date('m', strtotime($request->input('month'))))
                    ->whereYear('created_at', date('Y', strtotime($request->input('month'))));
            });
        }

        $purchaseItems = $purchaseQuery->get();
        $categories = Category::all();

        return view('pages.report.purchase-items', compact('purchaseItems', 'categories'));
    }
}
