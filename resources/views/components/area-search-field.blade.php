@props(['name', 'value' => '', 'labelValue' => ''])

{{-- Debounced autocomplete against the server-side Biteship proxy. Emits an
     'area-selected' event; the page script reacts (e.g. fetches rates). --}}
<div data-area-search class="relative">
    <input type="text" data-area-input
           value="{{ old($name . '_label', $labelValue) }}"
           placeholder="Ketik kota / kecamatan tujuan…"
           autocomplete="off"
           class="w-full border border-stone-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">

    {{-- Populated on selection; these are what the checkout form submits. --}}
    <input type="hidden" name="{{ $name }}_id" data-area-id value="{{ old($name . '_id') }}">
    <input type="hidden" name="{{ $name }}_label" data-area-label value="{{ old($name . '_label', $labelValue) }}">
    <input type="hidden" name="{{ $name }}_province" data-area-province value="{{ old($name . '_province') }}">
    <input type="hidden" name="{{ $name }}_city" data-area-city value="{{ old($name . '_city') }}">
    <input type="hidden" name="{{ $name }}_district" data-area-district value="{{ old($name . '_district') }}">

    <div data-area-dropdown hidden
         class="absolute z-30 left-0 right-0 mt-1 bg-white border border-stone-200 rounded-lg shadow-lg max-h-60 overflow-y-auto text-sm"></div>
</div>
