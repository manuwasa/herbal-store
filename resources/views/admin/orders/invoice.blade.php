<x-layouts.invoice title="Invoice {{ $order->invoice_number }}" :back-url="route('admin.pesanan.show', $order)">
    @include('orders._invoice', ['order' => $order, 'setting' => \App\Models\Setting::current()])
</x-layouts.invoice>
