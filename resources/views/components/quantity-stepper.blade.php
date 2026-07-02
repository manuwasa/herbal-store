@props(['name' => 'quantity', 'value' => 1, 'max' => 99])

{{-- -/input/+ stepper. Buttons are real, but JS (app.js) disables them at
     boundaries. data-max-stock feeds the boundary logic. --}}
<div {{ $attributes->merge(['class' => 'inline-flex items-center border border-stone-300 rounded-lg overflow-hidden']) }}
     data-quantity-stepper data-max-stock="{{ $max }}">
    <button type="button" data-step="-1"
            class="px-3 py-2 text-stone-600 hover:bg-stone-100 disabled:opacity-40 disabled:cursor-not-allowed">
        <x-icon name="minus" class="w-4 h-4" />
    </button>
    <input type="number" name="{{ $name }}" value="{{ $value }}" min="1" max="{{ $max }}" readonly
           class="w-12 text-center border-0 focus:ring-0 text-sm font-medium bg-transparent" data-quantity-input>
    <button type="button" data-step="1"
            class="px-3 py-2 text-stone-600 hover:bg-stone-100 disabled:opacity-40 disabled:cursor-not-allowed">
        <x-icon name="plus" class="w-4 h-4" />
    </button>
</div>
