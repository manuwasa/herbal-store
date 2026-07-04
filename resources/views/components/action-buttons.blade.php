@props(['product', 'compact' => false])

@php
    $whatsappUrl = \App\Services\WhatsAppLinkBuilder::forProduct($product);
    $buttonClass = $compact
        ? 'inline-flex items-center gap-1.5 text-xs font-medium px-2.5 py-1.5 rounded-lg text-center'
        : 'inline-flex items-center gap-2 text-sm font-semibold px-5 py-3 rounded-full text-center';
    $iconClass = $compact ? 'w-3.5 h-3.5' : 'w-4 h-4';
    $shopEnabled = \App\Models\Setting::current()->payment_gateway_enabled;
@endphp

<div class="flex flex-wrap gap-2">
    {{-- The site's own checkout — primary CTA, shown only when the store is live and the product is purchasable. --}}
    @if($shopEnabled && $product->isPurchasable())
        <form method="POST" action="{{ route('cart.store') }}" class="contents" data-add-to-cart-form data-product-name="{{ $product->name }}">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <input type="hidden" name="quantity" value="1">
            <button type="submit" class="{{ $buttonClass }} bg-brand-700 text-white hover:bg-brand-800 transition-colors">
                <x-icon name="cart" class="{{ $iconClass }}" />
                {{ $compact ? 'Keranjang' : 'Tambah ke Keranjang' }}
            </button>
        </form>
    @endif

    @if($product->hasShopeeLink())
        <a href="{{ $product->shopee_url }}" target="_blank" rel="noopener"
           class="{{ $buttonClass }} bg-orange-100 text-orange-700 hover:bg-orange-200 transition-colors">
            <x-icon name="shopping-bag" class="{{ $iconClass }}" />
            Shopee
        </a>
    @endif

    @if($product->hasTiktokLink())
        <a href="{{ $product->tiktok_url }}" target="_blank" rel="noopener"
           class="{{ $buttonClass }} bg-stone-900 text-white hover:bg-stone-700 transition-colors">
            <x-icon name="play-circle" class="{{ $iconClass }}" />
            TikTok
        </a>
    @endif

    @if($product->hasOrderNowLink())
        <a href="{{ $product->order_now_url }}" target="_blank" rel="noopener"
           class="{{ $buttonClass }} bg-brand-100 text-brand-800 hover:bg-brand-200 transition-colors">
            <x-icon name="arrow-top-right" class="{{ $iconClass }}" />
            Order Now
        </a>
    @endif

    @if($whatsappUrl)
        <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener"
           class="{{ $buttonClass }} bg-whatsapp text-white hover:bg-whatsapp-dark transition-colors">
            <x-icon name="chat-bubble" class="{{ $iconClass }}" />
            {{ $compact ? 'Chat Admin' : 'Chat Admin (WhatsApp)' }}
        </a>
    @endif
</div>
