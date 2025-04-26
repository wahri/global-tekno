<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Administrator',
            'email' => 'admin@gmail.com',
            'username' => 'admin',
        ]);

        Category::insert([
            [
                'name' => 'Laptop Baru',
            ],
            [
                'name' => 'Laptop Second',
            ],
            [
                'name' => 'Accesories',
            ],
            [
                'name' => 'Kabel',
            ],
            [
                'name' => 'Lainnya',
            ],
        ]);

        Supplier::insert([
            [
                'name' => 'Supplier 1',
                'address' => 'Jl. Raya No. 1',
                'phone' => '081234567890',
                'email' => 'aUzjI@example.com',
            ],
            [
                'name' => 'Supplier 2',
                'address' => 'Jl. Raya No. 2',
                'phone' => '081234567891',
                'email' => 'OyTb3@example.com',
            ],
        ]);

        Product::insert([
            [
                'name' => 'Asus 1',
                'sku' => 'BR0001',
                'merk' => 'Asus',
                'category_id' => 1,
                'price' => 1500000,
                'stock' => 10,
                'unit' => 'Unit',
            ],
            [
                'name' => 'Acer 2',
                'sku' => 'BR0002',
                'merk' => 'Acer',
                'category_id' => 1,
                'price' => 2500000,
                'stock' => 5,
                'unit' => 'Unit',
            ],
            [
                'name' => 'Lenovo 3',
                'sku' => 'BR0003',
                'merk' => 'Lenovo',
                'category_id' => 1,
                'price' => 3000000,
                'stock' => 4,
                'unit' => 'Unit',
            ],
        ]);

        Purchase::insert([
            [
                'supplier_id' => 1,
                'user_id' => 1,
                'order_number' => 'OR0001',
                'total_amount' => 10000000,
                'created_at' => now()->subDays(2),
            ],
            [
                'supplier_id' => 2,
                'user_id' => 1,
                'order_number' => 'OR0002',
                'total_amount' => 20000000,
                'created_at' => now()->subDays(1),
            ],
        ]);

        PurchaseItem::insert([
            [
                'purchase_id' => 1,
                'product_id' => 1,
                'quantity' => 10,
                'price' => 1000000,
                'subtotal' => 10000000,
                'remaining_quantity' => 10,
                'created_at' => now()->subDays(2),
            ],
            [
                'purchase_id' => 2,
                'product_id' => 2,
                'quantity' => 5,
                'price' => 2000000,
                'subtotal' => 10000000,
                'remaining_quantity' => 5,
                'created_at' => now()->subDays(1),
            ],
            [
                'purchase_id' => 2,
                'product_id' => 3,
                'quantity' => 4,
                'price' => 2500000,
                'subtotal' => 10000000,
                'remaining_quantity' => 4,
                'created_at' => now()->subDays(1),
            ],
        ]);
    }
}
