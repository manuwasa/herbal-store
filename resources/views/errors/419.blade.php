<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sesi Berakhir</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-50 text-center px-4">
    <div>
        <p class="text-6xl font-bold text-brand-600">419</p>
        <h1 class="text-xl font-semibold text-gray-900 mt-4">Sesi Berakhir</h1>
        <p class="text-gray-500 mt-2">Halaman ini sudah terlalu lama terbuka. Silakan muat ulang dan coba lagi.</p>
        <a href="{{ url()->previous() }}" class="inline-block mt-6 bg-brand-600 text-white font-semibold px-6 py-3 rounded-lg hover:bg-brand-700">
            Muat Ulang
        </a>
    </div>
</body>
</html>
