<?php

namespace App\Models;

use App\Observers\ProductObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[ObservedBy(ProductObserver::class)]
class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'image_path',
        'stock',
        'shopee_url',
        'tiktok_url',
        'order_now_url',
        'is_active',
        'is_top_pick',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock' => 'integer',
            'is_active' => 'boolean',
            'is_top_pick' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeTopPick(Builder $query): Builder
    {
        return $query->where('is_top_pick', true);
    }

    public function hasShopeeLink(): bool
    {
        return filled($this->shopee_url);
    }

    public function hasTiktokLink(): bool
    {
        return filled($this->tiktok_url);
    }

    public function hasOrderNowLink(): bool
    {
        return filled($this->order_now_url);
    }
}
