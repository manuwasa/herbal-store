<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>500 — Terjadi Kesalahan</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-50 text-center px-4">
    <div>
        <p class="text-6xl font-bold text-brand-600">500</p>
        <h1 class="text-xl font-semibold text-gray-900 mt-4">Terjadi Kesalahan</h1>
        <p class="text-gray-500 mt-2">Maaf, terjadi kesalahan di server kami. Silakan coba lagi beberapa saat.</p>
        <a href="{{ url('/') }}" class="inline-block mt-6 bg-brand-600 text-white font-semibold px-6 py-3 rounded-lg hover:bg-brand-700">
            Kembali ke Beranda
        </a>
    </div>
</body>
</html>
