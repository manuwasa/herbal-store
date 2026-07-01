<x-layouts.app :setting="$setting">
    <section class="relative overflow-hidden bg-linear-to-b from-brand-100 via-brand-50 to-stone-50 border-b border-brand-100 hero-ambient-bg">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-16 md:py-24 flex flex-col md:flex-row items-center gap-10">
            <div class="flex-1 max-w-xl">
                @if($setting->banner_badge_text)
                    <p class="hero-reveal inline-flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wider text-brand-700 bg-white/70 px-3 py-1.5 rounded-full" style="--reveal-delay: 0ms">
                        <x-icon name="leaf" class="w-3.5 h-3.5" />
                        {{ $setting->banner_badge_text }}
                    </p>
                @endif
                <h1 class="hero-reveal font-display font-semibold text-4xl md:text-5xl text-brand-900 leading-[1.1] mt-5 text-balance" style="--reveal-delay: 80ms">
                    {{ $setting->banner_heading }}
                </h1>
                @if($setting->banner_subheading)
                    <p class="hero-reveal mt-5 text-lg text-stone-600 leading-relaxed" style="--reveal-delay: 160ms">{{ $setting->banner_subheading }}</p>
                @endif
                <a href="{{ route('catalog.index') }}"
                   class="hero-reveal group inline-flex items-center gap-2 mt-8 bg-brand-700 text-white font-semibold px-7 py-3.5 rounded-full hover:bg-brand-800 transition-all motion-safe:hover:scale-[1.03] motion-safe:active:scale-[0.98] shadow-sm shadow-brand-900/10" style="--reveal-delay: 240ms">
                    Lihat Katalog
                    <x-icon name="arrow-top-right" class="w-4 h-4 transition-transform duration-200 group-hover:translate-x-0.5 group-hover:-translate-y-0.5" />
                </a>
            </div>
            @if($setting->banner_image_path)
                <div class="hero-reveal flex-1 w-full" style="--reveal-delay: 120ms">
                    <img src="{{ asset('storage/' . $setting->banner_image_path) }}" alt="{{ $setting->site_name }}"
                         class="w-full rounded-3xl shadow-xl shadow-brand-900/10 aspect-4/3 object-cover">
                </div>
            @endif
        </div>
    </section>

    @if($topPicks->isNotEmpty())
        <section class="py-16 border-b border-stone-200">
            <div class="reveal max-w-6xl mx-auto px-4 sm:px-6 flex items-end justify-between mb-8">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-brand-600 mb-1.5 flex items-center gap-1.5">
                        <x-icon name="star-solid" class="w-3.5 h-3.5 text-amber-400 pulse-soft" />
                        Top Pick
                    </p>
                    <h2 class="font-display font-semibold text-2xl md:text-3xl text-stone-900">Pilihan Terbaik</h2>
                </div>
                <div class="hidden sm:flex items-center gap-2">
                    <button type="button" id="top-pick-prev" aria-label="Sebelumnya"
                            class="inline-flex items-center justify-center w-10 h-10 rounded-full border border-stone-200 bg-white text-stone-500 hover:bg-stone-50 hover:text-stone-800 transition-all motion-safe:hover:scale-105 motion-safe:active:scale-95 disabled:opacity-30 disabled:pointer-events-none">
                        <x-icon name="chevron-left" class="w-4 h-4" />
                    </button>
                    <button type="button" id="top-pick-next" aria-label="Berikutnya"
                            class="inline-flex items-center justify-center w-10 h-10 rounded-full border border-stone-200 bg-white text-stone-500 hover:bg-stone-50 hover:text-stone-800 transition-all motion-safe:hover:scale-105 motion-safe:active:scale-95 disabled:opacity-30 disabled:pointer-events-none">
                        <x-icon name="chevron-right" class="w-4 h-4" />
                    </button>
                </div>
            </div>

            <div id="top-pick-slider" class="no-scrollbar flex gap-4 sm:gap-5 overflow-x-auto scroll-smooth snap-x snap-mandatory px-4 sm:px-6 max-w-6xl mx-auto">
                @foreach($topPicks as $product)
                    <div class="snap-start shrink-0 w-56 sm:w-64 grid reveal" style="--reveal-delay: {{ min($loop->index, 7) * 60 }}ms">
                        <x-product-card :product="$product" />
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <section class="max-w-6xl mx-auto px-4 sm:px-6 py-16">
        <div class="reveal flex items-end justify-between mb-8">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-brand-600 mb-1.5">Pilihan Kami</p>
                <h2 class="font-display font-semibold text-2xl md:text-3xl text-stone-900">Produk Terbaru</h2>
            </div>
            <a href="{{ route('catalog.index') }}" class="group hidden sm:inline-flex items-center gap-1 text-sm font-medium text-brand-700 hover:text-brand-800">
                Lihat semua
                <x-icon name="arrow-top-right" class="w-3.5 h-3.5 transition-transform duration-200 group-hover:translate-x-0.5 group-hover:-translate-y-0.5" />
            </a>
        </div>

        @if($featuredProducts->isEmpty())
            <div class="text-center py-16 text-stone-400">
                <x-icon name="box" class="w-10 h-10 mx-auto mb-3" />
                <p>Belum ada produk.</p>
            </div>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-5">
                @foreach($featuredProducts as $product)
                    <x-product-card :product="$product" class="reveal" style="--reveal-delay: {{ min($loop->index, 7) * 60 }}ms" />
                @endforeach
            </div>
        @endif
    </section>

    @if($categories->isNotEmpty())
        <section class="reveal max-w-6xl mx-auto px-4 sm:px-6 py-16 border-t border-stone-200">
            <p class="text-xs font-semibold uppercase tracking-wider text-brand-600 mb-1.5">Jelajahi</p>
            <h2 class="font-display font-semibold text-2xl md:text-3xl text-stone-900 mb-6">Kategori</h2>
            <div class="flex flex-wrap gap-3">
                @foreach($categories as $category)
                    <a href="{{ route('catalog.index', ['category' => $category->slug]) }}"
                       class="px-5 py-2.5 rounded-full bg-white border border-stone-200 text-sm font-medium text-stone-700 hover:border-brand-400 hover:bg-brand-50 hover:text-brand-800 transition-all motion-safe:hover:scale-[1.03] motion-safe:active:scale-[0.98]">
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>
        </section>
    @endif
</x-layouts.app>
