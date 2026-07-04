<x-layouts.invoice title="Invoice {{ $order->invoice_number }}" :back-url="route('orders.show', $order)">
    @include('orders._invoice', ['order' => $order, 'setting' => $setting])
</x-layouts.invoice>
