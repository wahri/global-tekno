<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Get total orders this month
        $totalOrders = Sale::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Get total income this month
        $totalIncome = Sale::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_amount');

        // Get total expenses this month
        $totalExpenses = Purchase::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_amount');

        // Get total produck in stock
        $totalProducts = Product::where('stock', '>', 0)->count();

        $year = now()->year;

        // Get data and pluck into [ 'YYYY-MM' => total ]
        $totalOrdersPerMonth = Sale::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as total')
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get()->pluck('total', 'month');

        $totalIncomePerMonth = Sale::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(total_amount) as total')
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get()->pluck('total', 'month');

        $totalExpensesPerMonth = Purchase::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(total_amount) as total')
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get()->pluck('total', 'month');

        $totalProductsPerMonth = Product::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as total')
            ->where('stock', '>', 0)
            ->groupBy('month')
            ->orderBy('month')
            ->get()->pluck('total', 'month');

        $arrayPerMonth = [
            'totalOrdersPerMonth' => [],
            'totalIncomePerMonth' => [],
            'totalExpensesPerMonth' => [],
            'totalProductsPerMonth' => [],
        ];

        for ($i = 1; $i <= 12; $i++) {
            $monthKey = Carbon::createFromDate($year, $i, 1)->format('Y-m');
            $arrayPerMonth['totalOrdersPerMonth'][] = (int) ($totalOrdersPerMonth[$monthKey] ?? 0);
            $arrayPerMonth['totalIncomePerMonth'][] = (int) ($totalIncomePerMonth[$monthKey] ?? 0);
            $arrayPerMonth['totalExpensesPerMonth'][] = (int) ($totalExpensesPerMonth[$monthKey] ?? 0);
            $arrayPerMonth['totalProductsPerMonth'][] = (int) ($totalProductsPerMonth[$monthKey] ?? 0);
        }

        return view('dashboard', [
            'totalOrders' => $totalOrders,
            'totalIncome' => $totalIncome,
            'totalExpenses' => $totalExpenses,
            'totalProducts' => $totalProducts,
            'totalOrdersPerMonth' => $arrayPerMonth['totalOrdersPerMonth'],
            'totalIncomePerMonth' => $arrayPerMonth['totalIncomePerMonth'],
            'totalExpensesPerMonth' => $arrayPerMonth['totalExpensesPerMonth'],
            'totalProductsPerMonth' => $arrayPerMonth['totalProductsPerMonth'],
        ]);
    }
}
