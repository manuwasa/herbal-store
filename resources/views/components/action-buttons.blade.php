@props(['product', 'compact' => false])

@php
    $whatsappUrl = \App\Services\WhatsAppLinkBuilder::forProduct($product);
    $buttonClass = $compact
        ? 'text-xs font-medium px-2.5 py-1.5 rounded-md text-center'
        : 'text-sm font-semibold px-4 py-2.5 rounded-lg text-center';
@endphp

<div class="flex flex-wrap gap-2">
    @if($product->hasShopeeLink())
        <a href="{{ $product->shopee_url }}" target="_blank" rel="noopener"
           class="{{ $buttonClass }} bg-orange-100 text-orange-700 hover:bg-orange-200">
            Order via Shopee
        </a>
    @endif

    @if($product->hasTiktokLink())
        <a href="{{ $product->tiktok_url }}" target="_blank" rel="noopener"
           class="{{ $buttonClass }} bg-gray-900 text-white hover:bg-gray-700">
            Order via TikTok
        </a>
    @endif

    @if($product->hasOrderNowLink())
        <a href="{{ $product->order_now_url }}" target="_blank" rel="noopener"
           class="{{ $buttonClass }} bg-brand-100 text-brand-700 hover:bg-brand-200">
            Order Now
        </a>
    @endif

    @if($whatsappUrl)
        <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener"
           class="{{ $buttonClass }} bg-green-600 text-white hover:bg-green-700">
            Chat Admin (WhatsApp)
        </a>
    @endif
</div>
