<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::query()->pluck('id');

        // Explicit edge cases so the conditional action-button rendering is exercised.
        Product::factory()->create([
            'category_id' => $categories->random(),
            'name' => 'Jamu Kunyit Asam Lengkap',
            'slug' => 'jamu-kunyit-asam-lengkap',
            'shopee_url' => 'https://shopee.co.id/product/123456/1000001',
            'tiktok_url' => 'https://www.tiktok.com/@tokoherbal/product/1000001',
            'order_now_url' => 'https://tokopedia.com/tokoherbal/jamu-kunyit-asam',
        ]);

        Product::factory()->create([
            'category_id' => $categories->random(),
            'name' => 'Teh Herbal Chat Admin Saja',
            'slug' => 'teh-herbal-chat-admin-saja',
            'shopee_url' => null,
            'tiktok_url' => null,
            'order_now_url' => null,
        ]);

        // Bulk sample products with randomized link combinations.
        Product::factory()
            ->count(30)
            ->create([
                'category_id' => fn () => $categories->random(),
            ]);
    }
}
