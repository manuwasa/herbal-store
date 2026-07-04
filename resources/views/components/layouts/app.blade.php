@props(['setting', 'title' => null, 'description' => null])
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ empty($title) ? $setting->site_name : "{$title} — {$setting->site_name}" }}</title>
    <meta name="description" content="{{ $description ?? $setting->site_description }}">

    @if($setting->favicon_path)
        <link rel="icon" href="{{ asset('storage/' . $setting->favicon_path) }}">
    @endif

    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
</head>
<body class="min-h-screen flex flex-col bg-stone-50 text-stone-900">
    <x-navbar :setting="$setting" />

    {{-- No-JS / fetch-failure fallback: a normal full-page flash message. --}}
    @if(session('status'))
        <div class="max-w-6xl mx-auto px-4 sm:px-6 mt-4">
            <div class="flex items-center gap-2 rounded-lg bg-green-50 border border-green-200 text-green-800 px-4 py-3 text-sm">
                <x-icon name="check-circle" class="w-5 h-5 shrink-0" />
                {{ session('status') }}
            </div>
        </div>
    @endif

    <main class="flex-1">
        {{ $slot }}
    </main>

    <x-footer :setting="$setting" />

    {{-- Populated by cart.js when an add/update/remove succeeds via fetch. --}}
    <div id="toast-container" class="fixed bottom-4 right-4 z-50 flex flex-col gap-2 items-end" aria-live="polite"></div>

    <script src="{{ asset('js/app.js') }}?v={{ filemtime(public_path('js/app.js')) }}"></script>
    <script src="{{ asset('js/cart.js') }}?v={{ filemtime(public_path('js/cart.js')) }}"></script>
    @stack('scripts')
</body>
</html>
