@props(['setting', 'title' => null, 'description' => null])
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ empty($title) ? $setting->site_name : "{$title} — {$setting->site_name}" }}</title>
    <meta name="description" content="{{ $description ?? $setting->site_description }}">

    @if($setting->favicon_path)
        <link rel="icon" href="{{ asset('storage/' . $setting->favicon_path) }}">
    @endif

    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
</head>
<body class="min-h-screen flex flex-col bg-stone-50 text-stone-900">
    <x-navbar :setting="$setting" />

    <main class="flex-1">
        {{ $slot }}
    </main>

    <x-footer :setting="$setting" />

    <script src="{{ asset('js/app.js') }}?v={{ filemtime(public_path('js/app.js')) }}"></script>
</body>
</html>
