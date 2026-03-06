<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $settings->site_title ?? 'Beat Music') - Beat Music</title>
    @if(isset($settings) && $settings->favicon)
        <link rel="icon" href="{{ asset('storage/' . $settings->favicon) }}">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(135deg, #020617 0%, #04112b 45%, #020617 100%);
            background-attachment: fixed;
            color: #f1f5f9;
            font-family: 'Inter', system-ui, sans-serif;
        }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen text-slate-100 antialiased">
    <div class="fixed top-6 right-4 z-50 space-y-3 w-80 max-w-[calc(100vw-2rem)]">
        @if(session('success'))
            <div class="rounded-xl border border-emerald-500/30 bg-emerald-900/30 px-4 py-3 text-sm text-emerald-200 shadow-xl">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="rounded-xl border border-rose-500/30 bg-rose-900/30 px-4 py-3 text-sm text-rose-200 shadow-xl">
                {{ session('error') }}
            </div>
        @endif
    </div>

    <main>
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
