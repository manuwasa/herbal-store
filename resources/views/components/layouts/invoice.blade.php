@props(['title' => 'Invoice', 'backUrl' => null])
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
</head>
<body class="min-h-screen bg-stone-200 py-8 print:bg-white print:py-0">
    <div class="max-w-2xl mx-auto px-4 print:px-0">
        {{-- Screen-only toolbar — never appears in the printed/saved-as-PDF output. --}}
        <div class="print:hidden flex items-center justify-between mb-4">
            @if($backUrl)
                <a href="{{ $backUrl }}" class="text-sm text-stone-600 hover:text-stone-900">&larr; Kembali</a>
            @else
                <span></span>
            @endif
            <button type="button" onclick="window.print()"
                    class="inline-flex items-center gap-2 bg-brand-700 text-white text-sm font-semibold px-4 py-2 rounded-full hover:bg-brand-800 transition-colors">
                Cetak Invoice
            </button>
        </div>

        {{ $slot }}
    </div>
</body>
</html>
