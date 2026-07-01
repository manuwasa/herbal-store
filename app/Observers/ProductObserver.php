<?php

namespace App\Observers;

use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class ProductObserver
{
    /**
     * Delete the old image file when it's replaced by a new one.
     */
    public function updating(Product $product): void
    {
        if ($product->isDirty('image_path') && $product->getOriginal('image_path')) {
            Storage::disk('public')->delete($product->getOriginal('image_path'));
        }
    }

    /**
     * Delete the image file when the product is permanently removed.
     */
    public function forceDeleted(Product $product): void
    {
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }
    }
}
