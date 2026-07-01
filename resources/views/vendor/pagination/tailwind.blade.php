@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <p class="text-sm text-stone-500">
            {!! __('Showing') !!}
            @if ($paginator->firstItem())
                <span class="font-medium text-stone-700">{{ $paginator->firstItem() }}</span>
                {!! __('to') !!}
                <span class="font-medium text-stone-700">{{ $paginator->lastItem() }}</span>
            @else
                {{ $paginator->count() }}
            @endif
            {!! __('of') !!}
            <span class="font-medium text-stone-700">{{ $paginator->total() }}</span>
            {!! __('results') !!}
        </p>

        <div class="flex items-center gap-1.5">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}"
                      class="inline-flex items-center justify-center w-9 h-9 rounded-full text-stone-300 cursor-not-allowed">
                    <x-icon name="chevron-left" class="w-4 h-4" />
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="{{ __('pagination.previous') }}"
                   class="inline-flex items-center justify-center w-9 h-9 rounded-full text-stone-500 border border-stone-200 bg-white hover:bg-stone-50 hover:text-stone-800 transition-colors">
                    <x-icon name="chevron-left" class="w-4 h-4" />
                </a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span aria-disabled="true" class="inline-flex items-center justify-center w-9 h-9 text-sm text-stone-400">{{ $element }}</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page"
                                  class="inline-flex items-center justify-center w-9 h-9 rounded-full text-sm font-semibold bg-brand-700 text-white">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}" aria-label="{{ __('Go to page :page', ['page' => $page]) }}"
                               class="inline-flex items-center justify-center w-9 h-9 rounded-full text-sm font-medium text-stone-600 border border-stone-200 bg-white hover:bg-stone-50 hover:text-stone-900 transition-colors">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="{{ __('pagination.next') }}"
                   class="inline-flex items-center justify-center w-9 h-9 rounded-full text-stone-500 border border-stone-200 bg-white hover:bg-stone-50 hover:text-stone-800 transition-colors">
                    <x-icon name="chevron-right" class="w-4 h-4" />
                </a>
            @else
                <span aria-disabled="true" aria-label="{{ __('pagination.next') }}"
                      class="inline-flex items-center justify-center w-9 h-9 rounded-full text-stone-300 cursor-not-allowed">
                    <x-icon name="chevron-right" class="w-4 h-4" />
                </span>
            @endif
        </div>
    </nav>
@endif
