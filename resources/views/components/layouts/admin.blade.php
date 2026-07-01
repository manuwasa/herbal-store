@props(['setting', 'title' => 'Admin'])
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} &mdash; Admin {{ $setting->site_name }}</title>

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/datatables/dataTables.min.css') }}">
</head>
<body class="min-h-screen flex bg-gray-100 text-gray-900">
    <x-admin.sidebar />

    <div class="flex-1 flex flex-col min-w-0">
        <header class="bg-white border-b border-gray-200 px-6 py-3 flex items-center justify-between">
            <h1 class="text-lg font-semibold">{{ $title }}</h1>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="text-sm text-gray-500 hover:text-gray-800">Logout</button>
            </form>
        </header>

        <main class="flex-1 p-6">
            @if(session('status'))
                <div class="mb-4 rounded-lg bg-green-100 text-green-800 px-4 py-3 text-sm">
                    {{ session('status') }}
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>

    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/dataTables.min.js') }}"></script>
    <script src="{{ asset('js/admin.js') }}"></script>
</body>
</html>
