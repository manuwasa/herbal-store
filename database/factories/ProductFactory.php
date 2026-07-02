<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'category_id' => Category::factory(),
            'name' => Str::title($name),
            'slug' => Str::slug($name),
            'description' => fake()->paragraphs(2, true),
            'price' => fake()->numberBetween(15000, 250000),
            'weight' => fake()->numberBetween(50, 2000), // grams
            'image_path' => null,
            'shopee_url' => fake()->boolean(50) ? 'https://shopee.co.id/product/' . fake()->numberBetween(100000, 999999) : null,
            'tiktok_url' => fake()->boolean(40) ? 'https://www.tiktok.com/@tokoherbal/product/' . fake()->numberBetween(100000, 999999) : null,
            'order_now_url' => fake()->boolean(20) ? 'https://tokopedia.com/tokoherbal/' . fake()->slug() : null,
            'is_active' => true,
        ];
    }
}
