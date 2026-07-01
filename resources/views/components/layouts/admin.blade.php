@props(['setting', 'title' => 'Admin'])
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} &mdash; Admin {{ $setting->site_name }}</title>

    <link rel="stylesheet" href="{{ asset('vendor/datatables/dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}">
</head>
<body class="min-h-screen flex bg-gray-100 text-gray-900 overflow-x-hidden">
    <x-admin.sidebar :setting="$setting" />

    <div class="flex-1 flex flex-col min-w-0">
        <header class="bg-white border-b border-gray-200 px-4 md:px-6 py-4 flex items-center gap-3">
            <button type="button" id="sidebar-open-toggle" class="md:hidden -ml-1 p-1.5 rounded-lg text-gray-500 hover:bg-gray-100">
                <x-icon name="menu" class="w-6 h-6" />
            </button>
            <h1 class="text-lg md:text-xl font-bold text-gray-900">{{ $title }}</h1>
        </header>

        <main class="flex-1 p-4 md:p-6">
            @if(session('status'))
                <div class="mb-4 flex items-center gap-2 rounded-lg bg-green-50 border border-green-200 text-green-800 px-4 py-3 text-sm">
                    <x-icon name="check-circle" class="w-5 h-5 shrink-0" />
                    {{ session('status') }}
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>

    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/dataTables.min.js') }}"></script>
    <script src="{{ asset('js/admin.js') }}?v={{ filemtime(public_path('js/admin.js')) }}"></script>
</body>
</html>
