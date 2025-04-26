<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch all products from the database
        $products = Product::all();

        // Return the view with the products data
        return view('pages.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        $units = Product::select('unit')->distinct()->get();
        $maxSku = Product::max('id');
        $sku = 'BR' . str_pad($maxSku + 1, 5, '0', STR_PAD_LEFT);
        // Generate a new SKU based on the maximum ID in the products table
        // Return the view for creating a new product
        return view('pages.products.create', compact('categories', 'sku', 'units'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request data
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'merk' => 'nullable|string|max:100',
            'unit' => 'nullable|string|max:100',
            'sku' => 'nullable|string|max:50|unique:products,sku',
            'price' => 'required|string',
            'category_id' => 'nullable|exists:categories,id',
            'stock' => 'nullable|integer|min:0',
            'image' => 'nullable|file|mimes:jpg,jpeg,png|max:2048', // max 2MB
        ]);

        if($validated['sku'] == null) {
            $validated['sku'] = Str::random(10);
        }
        // Generate a unique SKU if not provided

        // Proses upload image (jika ada)
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        // Create a new product
        $product = Product::create([
            'name' => $validated['name'],
            'merk' => $validated['merk'],
            'unit' => $validated['unit'],
            'sku' => $validated['sku'],
            'price' => (int)str_replace('.', '', $request->price),
            'category_id' => $validated['category_id'] ?? null,
            'stock' => $validated['stock'],
            'image' => $imagePath,
        ]);

        // Redirect to the product list with a success message
        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Find the product by ID
        $product = Product::findOrFail($id);
        $categories = Category::all();
        $units = Product::select('unit')->distinct()->get();

        // Return the view for editing the product
        return view('pages.products.edit', compact('product', 'categories', 'units'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validate the request data
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'merk' => 'nullable|string|max:100',
            'unit' => 'nullable|string|max:100',
            'price' => 'required|string',
            'category_id' => 'nullable|exists:categories,id',
            'stock' => 'required|integer|min:0',
        ]);

        // Find the product by ID
        $product = Product::findOrFail($id);

        // Proses upload image (jika ada)
        if ($request->hasFile('image')) {
            // Delete the old image if it exists
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            // Validate the new image
            $request->validate([
                'image' => 'file|mimes:jpg,jpeg,png|max:2048', // max 2MB
            ]);

            // Store the new image and update the product's image path
            $imagePath = $request->file('image')->store('products', 'public');
            $product->update(['image' => $imagePath]);

        }

        // Update the product with the validated data    
        $product->update($validated);


        // Redirect to the product list with a success message    
        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Find the product and delete it
        $product = Product::findOrFail($id);

        // Delete the image if it exists
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }
        // Delete the product
        // Check if the product has any purchase items
        if ($product->purchaseItems()->exists()) {
            return redirect()->route('products.index')->with('error', 'Product cannot be deleted because it has purchase items.');
        }
        $product->delete();

        // Redirect to the product list with a success message
        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }
}
