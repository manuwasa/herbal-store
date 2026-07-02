<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\BranchStock;
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
            'is_top_pick' => true,
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

        // Mark a handful more as Top Pick so the homepage slider has enough to scroll.
        Product::query()->inRandomOrder()->take(5)->update(['is_top_pick' => true]);

        // Give every product some stock at the default branch, so the catalog shows
        // in-stock items and checkout can resolve a fulfilling branch out of the box.
        // A few are left at 0 so the "Stok Habis" state is still exercised.
        $branch = Branch::default();

        if ($branch) {
            Product::query()->each(function (Product $product) use ($branch) {
                BranchStock::query()->updateOrCreate(
                    ['branch_id' => $branch->id, 'product_id' => $product->id],
                    ['stock' => fake()->boolean(85) ? fake()->numberBetween(5, 100) : 0],
                );
            });
        }
    }
}
