@php $setting = \App\Models\Setting::current(); @endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Admin</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="w-full max-w-sm bg-white rounded-xl border border-gray-200 p-8">
        <div class="flex flex-col items-center text-center mb-6">
            <span class="flex items-center justify-center w-12 h-12 rounded-xl bg-brand-600 text-white mb-3 overflow-hidden">
                @if($setting->logo_path)
                    <img src="{{ asset('storage/' . $setting->logo_path) }}" alt="{{ $setting->site_name }}" class="w-full h-full object-cover">
                @else
                    <x-icon name="leaf" class="w-6 h-6" />
                @endif
            </span>
            <h1 class="text-xl font-bold text-gray-900">Login Admin</h1>
        </div>

        @if ($errors->any())
            <div class="mb-4 flex items-center gap-2 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">
                <x-icon name="alert" class="w-5 h-5 shrink-0" />
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.store') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
            </div>

            <label class="flex items-center gap-2 text-sm text-gray-600">
                <input type="checkbox" name="remember">
                Ingat saya
            </label>

            <button type="submit" class="w-full bg-brand-600 text-white font-semibold py-2.5 rounded-lg hover:bg-brand-700">
                Login
            </button>
        </form>
    </div>
</body>
</html>
