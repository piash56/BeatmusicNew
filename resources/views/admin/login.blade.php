<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Beat Music</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-gray-950 text-gray-100 min-h-screen flex items-center justify-center px-4">
<div class="w-full max-w-md">
    <div class="text-center mb-8">
        <div class="w-14 h-14 bg-red-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
        </div>
        <h1 class="text-2xl font-bold text-white">Admin Login</h1>
        <p class="text-gray-400 mt-1">Access restricted to administrators only</p>
    </div>
    <div class="bg-gray-900 rounded-2xl p-8 border border-white/5">
        @if($errors->any())
            <div class="bg-red-900/30 border border-red-500/30 text-red-300 rounded-lg p-3 mb-6 text-sm">{{ $errors->first() }}</div>
        @endif
        <form method="POST"
              action="{{ route('admin.login.post') }}"
              class="space-y-5"
              x-data="{ loading: false }"
              @submit="loading = true">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Email</label>
                <input type="email" name="email" required class="w-full bg-white/5 border border-white/10 text-white placeholder-gray-500 px-4 py-3 rounded-xl focus:outline-none focus:border-red-500 transition" placeholder="admin@beatmusic.com">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Password</label>
                <input type="password" name="password" required class="w-full bg-white/5 border border-white/10 text-white placeholder-gray-500 px-4 py-3 rounded-xl focus:outline-none focus:border-red-500 transition" placeholder="Admin password">
            </div>
            <button type="submit"
                    :disabled="loading"
                    class="w-full py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl transition disabled:opacity-60 flex items-center justify-center gap-2">
                <span x-show="loading" x-cloak class="inline-block w-4 h-4 border-2 border-white/40 border-t-white rounded-full animate-spin"></span>
                <span x-text="loading ? 'Signing In...' : 'Sign In as Admin'"></span>
            </button>
        </form>
        <div class="mt-4 text-center">
            <a href="{{ route('admin.forgot-password') }}" class="text-gray-500 hover:text-gray-400 text-sm">Forgot password?</a>
        </div>
    </div>
</div>
</body>
</html>
