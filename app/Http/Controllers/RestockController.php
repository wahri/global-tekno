<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseCart;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RestockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::all();
        $suppliers = Supplier::all();
        $maxId = Purchase::max('id');
        $order_number = 'OR' . str_pad($maxId + 1, 5, '0', STR_PAD_LEFT);
        $carts = PurchaseCart::with('product')->where('user_id', Auth::id())->get();
        return view('pages.restock.index', compact('products', 'carts', 'suppliers', 'order_number'));
    }

    public function addToCart(Request $request)
    {
        $product = Product::findOrFail($request->product_id);
        $cart = PurchaseCart::where('user_id', Auth::id())->where('product_id', $request->product_id)->first();

        if ($cart) {
            // Update the cart
            $cart->update([
                'quantity' => $cart->quantity + $request->quantity,
                'subtotal' => ($cart->quantity + $request->quantity) * $cart->price,
            ]);
        } else {
            PurchaseCart::create([
                'user_id'   => Auth::id(),
                'product_id' => $request->product_id,
                'price'     => $product->lastPurchaseItem->price ?? 0,
                'selling_price' => $product->price,
                'quantity'  => $request->quantity,
                'subtotal'  => $request->quantity * ($product->lastPurchaseItem->price ?? 0),
            ]);
        }

        $carts = PurchaseCart::with('product')->where('user_id', Auth::id())->get();

        return response()->json([
            'message' => 'Product added to cart successfully',
            'carts' => $carts,
        ]);
    }
    public function scanCode(Request $request)
    {
        $product = Product::where('sku', $request->sku)->first();
        $cart = PurchaseCart::where('user_id', Auth::id())->where('product_id', $product->id)->first();

        if ($cart) {
            $cart->update([
                'quantity' => $cart->quantity + 1,
                'subtotal' => ($cart->quantity + 1) * $cart->price,
            ]);
        } else {
            PurchaseCart::create([
                'user_id'   => Auth::id(),
                'product_id' => $product->id,
                'price'     => $product->lastPurchaseItem->price ?? 0,
                'selling_price' => $product->price,
                'quantity'  => 1,
                'subtotal'  => 1 * ($product->lastPurchaseItem->price ?? 0),
            ]);
        }

        $carts = PurchaseCart::with('product')->where('user_id', Auth::id())->get();

        return response()->json([
            'message' => 'Product added to cart successfully',
            'carts' => $carts,
        ]);
    }

    public function updateCart(Request $request, $id)
    {
        $cart = PurchaseCart::findOrFail($id);
        $cart->update([
            'price' => $request->price,
            'selling_price' => $request->selling_price,
            'quantity' => $request->quantity,
            'subtotal' => $request->quantity * $request->price,
        ]);


        $carts = PurchaseCart::with('product')->where('user_id', Auth::id())->get();

        return response()->json([
            'carts' => $carts
        ]);
    }

    public function removeFromCart($id)
    {
        $cart = PurchaseCart::findOrFail($id);
        $cart->delete();

        $carts = PurchaseCart::with('product')->where('user_id', Auth::id())->get();

        return response()->json([
            'message' => 'Product removed from cart successfully',
            'carts' => $carts,
        ]);
    }
    public function clearCart()
    {
        PurchaseCart::where('user_id', Auth::id())->delete();
        $carts = PurchaseCart::with('product')->where('user_id', Auth::id())->get();

        return response()->json([
            'message' => 'Cart cleared successfully',
            'carts' => $carts,
        ]);
    }
    public function submitOrder(Request $request)
    {
        $request->validate([
            'supplier_id' => 'nullable|exists:suppliers,id',
            'order_number' => 'required|string|max:255',
        ]);

        // Check if the cart is empty   
        if (PurchaseCart::where('user_id', Auth::id())->count() == 0) {
            return response()->json([
                'message' => 'Cart is empty',
            ], 400);
        }
        // Check if the order number is unique
        if (Purchase::where('order_number', $request->order_number)->exists()) {
            return response()->json([
                'message' => 'Order number already exists',
            ], 400);
        }

        // Check if the supplier exists
        if (!Supplier::where('id', $request->supplier_id)->exists()) {
            return response()->json([
                'message' => 'Supplier not found',
            ], 400);
        }

        $carts = PurchaseCart::with('product')->where('user_id', Auth::id())->get();

        $totalAmount = $carts->sum('subtotal');
        $purchase = Purchase::create([
            'user_id' => Auth::id(),
            'supplier_id' => $request->supplier_id,
            'total_amount' => $totalAmount,
            'order_number' => $request->order_number,
        ]);

        foreach ($carts as $cart) {
            // Update product stock
            $cart->product->update([
                'stock' => $cart->product->stock + $cart->quantity,
            ]);

            if ($cart->selling_price != $cart->product->price) {
                $cart->product->update([
                    'price' => $cart->selling_price,
                ]);
            }

            // Save purchase item
            $purchase->purchaseItems()->create([
                'product_id' => $cart->product_id,
                'quantity' => $cart->quantity,
                'price' => $cart->price,
                'subtotal' => $cart->subtotal,
                'remaining_quantity' => $cart->quantity,
            ]);
        }
        // Clear the cart after successful checkout
        $carts = PurchaseCart::where('user_id', Auth::id())->delete();

        return response()->json([
            'message' => 'Order submitted successfully',
            'carts' => $carts,
        ]);
    }
}
