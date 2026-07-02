<?php

namespace App\Models;

use App\Observers\ProductObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'weight',
        'image_path',
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
            'weight' => 'integer',
            'is_active' => 'boolean',
            'is_top_pick' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function branchStocks(): HasMany
    {
        return $this->hasMany(BranchStock::class);
    }

    /**
     * Total stock across every branch — the only honest signal pre-checkout,
     * since which branch fulfills an order isn't known until a destination is
     * entered. Used by the catalog and isPurchasable().
     */
    public function totalStock(): int
    {
        return (int) $this->branchStocks()->sum('stock');
    }

    public function stockAt(Branch $branch): int
    {
        return (int) ($this->branchStocks()->where('branch_id', $branch->id)->value('stock') ?? 0);
    }

    public function decrementStockAt(Branch $branch, int $quantity): void
    {
        BranchStock::query()
            ->where('branch_id', $branch->id)
            ->where('product_id', $this->id)
            ->decrement('stock', $quantity);
    }

    public function restoreStockAt(Branch $branch, int $quantity): void
    {
        BranchStock::query()->firstOrCreate(
            ['branch_id' => $branch->id, 'product_id' => $this->id],
        )->increment('stock', $quantity);
    }

    public function isPurchasable(): bool
    {
        return $this->is_active && $this->totalStock() > 0;
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
