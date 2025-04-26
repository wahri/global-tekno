<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\Supplier;
use App\Models\SaleCart;
use App\Models\SaleItem;
use App\Services\FifoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CashierController extends Controller
{
    public function index()
    {
        $products = Product::all();
        $suppliers = Supplier::all();
        $maxId = Sale::max('id');
        $invoice_number = 'OR' . str_pad($maxId + 1, 5, '0', STR_PAD_LEFT);
        $carts = SaleCart::with('product')->where('user_id', Auth::id())->get();
        return view('pages.cashier.index', compact('products', 'carts', 'suppliers', 'invoice_number'));
    }

    public function addToCart(Request $request)
    {
        $product = Product::findOrFail($request->product_id);
        $cart = SaleCart::where('user_id', Auth::id())->where('product_id', $request->product_id)->first();

        if ($cart) {
            // Check if the product stock is sufficient
            if ($cart->quantity + $request->quantity > $product->stock) {
                return response()->json([
                    'message' => 'Insufficient stock for this product',
                ], 400);
            }
            // Update the cart
            $cart->update([
                'quantity' => $cart->quantity + $request->quantity,
                'subtotal' => (($cart->quantity + $request->quantity) * $cart->price) - $cart->discount,
            ]);
        } else {
            // Check if the product stock is sufficient
            if ($request->quantity > $product->stock) {
                return response()->json([
                    'message' => 'Insufficient stock for this product',
                ], 400);
            }
            // Create a new cart item
            SaleCart::create([
                'user_id'   => Auth::id(),
                'product_id' => $request->product_id,
                'price'     => $product->price ?? 0,
                'quantity'  => $request->quantity,
                'subtotal'  => $request->quantity * ($product->price ?? 0),
            ]);
        }

        $carts = SaleCart::with('product')->where('user_id', Auth::id())->get();

        return response()->json([
            'message' => 'Product added to cart successfully',
            'carts' => $carts,
        ]);
    }
    public function scanCode(Request $request)
    {
        $product = Product::where('sku', $request->sku)->first();
        $cart = SaleCart::where('user_id', Auth::id())->where('product_id', $product->id)->first();

        if ($cart) {
            // Check if the product stock is sufficient
            if ($cart->quantity + 1 > $product->stock) {
                return response()->json([
                    'message' => 'Insufficient stock for this product',
                ], 400);
            }
            // Update the cart
            $cart->update([
                'quantity' => $cart->quantity + 1,
                'subtotal' => (($cart->quantity + 1) * $cart->price) - $cart->discount,
            ]);
        } else {
            // Check if the product stock is sufficient
            if (1 > $product->stock) {
                return response()->json([
                    'message' => 'Insufficient stock for this product',
                ], 400);
            }
            SaleCart::create([
                'user_id'   => Auth::id(),
                'product_id' => $product->id,
                'price'     => $product->price ?? 0,
                'quantity'  => 1,
                'subtotal'  => 1 * ($product->price ?? 0),
            ]);
        }

        $carts = SaleCart::with('product')->where('user_id', Auth::id())->get();

        return response()->json([
            'message' => 'Product added to cart successfully',
            'carts' => $carts,
        ]);
    }

    public function updateCart(Request $request, $id)
    {
        $cart = SaleCart::findOrFail($id);
        $product = Product::findOrFail($cart->product_id);
        // Check if the product stock is sufficient
        if ($request->quantity > $product->stock) {
            return response()->json([
                'message' => 'Insufficient stock for this product',
            ], 400);
        }
        // Update the cart
        $cart->update([
            'quantity' => $request->quantity,
            'discount' => $request->discount,
            'subtotal' => ($request->quantity * $cart->price) - $request->discount,
        ]);

        $carts = SaleCart::with('product')->where('user_id', Auth::id())->get();

        return response()->json([
            'carts' => $carts
        ]);
    }

    public function removeFromCart($id)
    {
        $cart = SaleCart::findOrFail($id);
        $cart->delete();

        $carts = SaleCart::with('product')->where('user_id', Auth::id())->get();

        return response()->json([
            'message' => 'Product removed from cart successfully',
            'carts' => $carts,
        ]);
    }
    public function clearCart()
    {
        SaleCart::where('user_id', Auth::id())->delete();
        $carts = SaleCart::with('product')->where('user_id', Auth::id())->get();

        return response()->json([
            'message' => 'Cart cleared successfully',
            'carts' => $carts,
        ]);
    }
    public function submitOrder(Request $request, FifoService $fifoService)
    {
        $request->validate([
            'paid_amount' => 'nullable|numeric',
            'change_amount' => 'nullable|numeric',
            'invoice_number' => 'required|string|max:255|unique:sales,invoice_number',
        ]);

        $carts = SaleCart::where('user_id', Auth::id())->get();
        $totalAmount = $carts->sum('subtotal');

        // Check if the cart is empty   
        if ($carts->count() == 0) {
            return response()->json([
                'message' => 'Cart is empty',
            ], 400);
        }

        // Check if paid amount is greater than or equal to total amount
        if ($request->paid_amount < $totalAmount) {
            return response()->json([
                'message' => 'Paid amount must be greater than or equal to total amount',
            ], 400);
        }

        // Check stok
        foreach ($carts as $cart) {
            if ($cart->quantity > $cart->product->stock) {
                return response()->json([
                    'message' => 'Insufficient stock for this product',
                ], 400);
            }
        }

        DB::beginTransaction(); // Start Transaction
        
        try{
            $sale = Sale::create([
                'user_id' => Auth::id(),
                'total_amount' => $totalAmount,
                'invoice_number' => $request->invoice_number,
                'paid_amount' => $request->paid_amount,
                'change_amount' => $request->change_amount,
            ]);
    
            foreach ($carts as $cart) {
                // Update product stock
                $cart->product->update([
                    'stock' => $cart->product->stock - $cart->quantity,
                ]);
    
                $saleItem = SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $cart->product_id,
                    'quantity' => $cart->quantity,
                    'price' => $cart->price,
                    'discount' => $cart->discount,
                    'subtotal' => $cart->subtotal,
                ]);

    
                // Calculate HPP using FIFO
                $fifoService->calculateHpp($saleItem);
                
            }
            // Clear the cart after successful checkout
            $carts = SaleCart::where('user_id', Auth::id())->delete();

            DB::commit();
    
            return response()->json([
                'message' => 'Order submitted successfully',
                'carts' => $carts,
            ]);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback Transaction
            return response()->json([
                'message' => 'Failed to submit order: ' . $e->getMessage(),
            ], 500);
        }
    }
}
