@extends('layouts.admin')

@section('title', 'Modifica utente')
@section('page-title', 'Modifica utente')

@section('content')
<div class="max-w-2xl space-y-6">
    <a href="{{ route('admin.users.show', $user->id) }}" class="flex items-center space-x-2 text-gray-400 hover:text-white transition text-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        <span>Indietro</span>
    </a>

    @if($errors->any())
    <div class="bg-red-900/30 border border-red-500/30 text-red-400 p-4 rounded-xl text-sm">
        @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
    </div>
    @endif

    <!-- Read-only user information -->
    <div class="bg-gray-900 rounded-2xl border border-white/5 p-6">
        <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-4">Informazioni sull'utente</h3>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
            <div><dt class="text-gray-500">ID</dt><dd class="text-white font-medium">{{ $user->id }}</dd></div>
            <div><dt class="text-gray-500">Partecipato</dt><dd class="text-white">{{ $user->created_at?->format('M d, Y H:i') ?? '—' }}</dd></div>
            <div><dt class="text-gray-500">Verificato</dt><dd class="text-white">{{ $user->is_verified ? 'Yes' : 'No' }}</dd></div>
            <div><dt class="text-gray-500">Bilancia</dt><dd class="text-white">${{ number_format($user->balance ?? 0, 2) }}</dd></div>
            <div><dt class="text-gray-500">Ultimo accesso</dt><dd class="text-white">{{ $user->last_login_time?->format('M d, Y H:i') ?? '—' }}</dd></div>
            <div><dt class="text-gray-500">Conteggio tracce</dt><dd class="text-white">{{ $user->tracks()->count() }}</dd></div>
        </dl>
    </div>

    <form method="POST" action="{{ route('admin.users.update', $user->id) }}" class="bg-gray-900 rounded-2xl border border-white/5 p-6 space-y-5">
        @csrf @method('PUT')
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <label class="block text-sm text-gray-400 mb-1.5">Nome e cognome</label>
                <input type="text" name="full_name" value="{{ old('full_name', $user->full_name) }}"
                    class="w-full bg-gray-800 border border-white/10 text-white px-4 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 text-sm">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm text-gray-400 mb-1.5">E-mail</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                    class="w-full bg-gray-800 border border-white/10 text-white px-4 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 text-sm">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1.5">Telefono</label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                    class="w-full bg-gray-800 border border-white/10 text-white px-4 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 text-sm">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1.5">Paese</label>
                <input type="text" name="country" value="{{ old('country', $user->country) }}"
                    class="w-full bg-gray-800 border border-white/10 text-white px-4 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 text-sm">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1.5">Città</label>
                <input type="text" name="city" value="{{ old('city', $user->city) }}"
                    class="w-full bg-gray-800 border border-white/10 text-white px-4 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 text-sm">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1.5">Stato</label>
                <input type="text" name="state" value="{{ old('state', $user->state) }}"
                    class="w-full bg-gray-800 border border-white/10 text-white px-4 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 text-sm">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1.5">Cap</label>
                <input type="text" name="zip" value="{{ old('zip', $user->zip) }}"
                    class="w-full bg-gray-800 border border-white/10 text-white px-4 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 text-sm">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm text-gray-400 mb-1.5">Indirizzo</label>
                <input type="text" name="address" value="{{ old('address', $user->address) }}"
                    class="w-full bg-gray-800 border border-white/10 text-white px-4 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 text-sm">
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1.5">Stato</label>
                <select name="status" class="w-full bg-gray-800 border border-white/10 text-gray-300 px-4 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 text-sm">
                    <option value="active" {{ old('status', $user->status) === 'active' ? 'selected' : '' }}>Attivo</option>
                    <option value="suspended" {{ old('status', $user->status) === 'suspended' ? 'selected' : '' }}>Sospeso</option>
                </select>
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm text-gray-400 mb-1.5">Tipo di utente</label>
                <div class="flex gap-6 mt-2">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="radio" name="is_company" value="0" {{ old('is_company', $user->is_company) ? '' : 'checked' }} class="w-4 h-4 border-white/20 bg-gray-800 text-purple-600 focus:ring-purple-500">
                        <span class="text-sm text-gray-300">Individuale</span>
                    </label>
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="radio" name="is_company" value="1" {{ old('is_company', $user->is_company) ? 'checked' : '' }} class="w-4 h-4 border-white/20 bg-gray-800 text-purple-600 focus:ring-purple-500">
                        <span class="text-sm text-gray-300">Azienda</span>
                    </label>
                </div>
            </div>
            <div class="sm:col-span-2">
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="can_upload_tracks" value="1" {{ old('can_upload_tracks', $user->can_upload_tracks) ? 'checked' : '' }} class="w-4 h-4 rounded border-white/20 bg-gray-800 text-purple-600">
                    <span class="text-sm text-gray-300">Può caricare tracce</span>
                </label>
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm text-gray-400 mb-1.5">E-mail PayPal</label>
                <input type="email" name="paypal_email" value="{{ old('paypal_email', $user->paypal_email) }}"
                    class="w-full bg-gray-800 border border-white/10 text-white px-4 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 text-sm">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm text-gray-400 mb-1.5">Bio</label>
                <textarea name="bio" rows="3"
                    class="w-full bg-gray-800 border border-white/10 text-white px-4 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 text-sm resize-none">{{ old('bio', $user->bio) }}</textarea>
            </div>
            <div class="sm:col-span-2 border-t border-white/5 pt-4">
                <span class="block text-xs text-gray-500 mb-2">Collegamenti sociali</span>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Facebook</label>
                        <input type="url" name="social_facebook" value="{{ old('social_facebook', $user->social_facebook) }}"
                            class="w-full bg-gray-800 border border-white/10 text-white px-3 py-2 rounded-xl focus:outline-none focus:border-purple-500 text-xs">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Twitter / X</label>
                        <input type="url" name="social_twitter" value="{{ old('social_twitter', $user->social_twitter) }}"
                            class="w-full bg-gray-800 border border-white/10 text-white px-3 py-2 rounded-xl focus:outline-none focus:border-purple-500 text-xs">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Instagram</label>
                        <input type="url" name="social_instagram" value="{{ old('social_instagram', $user->social_instagram) }}"
                            class="w-full bg-gray-800 border border-white/10 text-white px-3 py-2 rounded-xl focus:outline-none focus:border-purple-500 text-xs">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Website</label>
                        <input type="url" name="social_website" value="{{ old('social_website', $user->social_website) }}"
                            class="w-full bg-gray-800 border border-white/10 text-white px-3 py-2 rounded-xl focus:outline-none focus:border-purple-500 text-xs">
                    </div>
                </div>
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm text-gray-400 mb-1.5">Nuova password <span class="text-gray-600 text-xs">(lasciare vuoto per mantenere aggiornato)</span></label>
                <input type="password" name="password"
                    class="w-full bg-gray-800 border border-white/10 text-white px-4 py-2.5 rounded-xl focus:outline-none focus:border-purple-500 text-sm">
            </div>
        </div>
        <div class="flex justify-end space-x-3">
            <a href="{{ route('admin.users.show', $user->id) }}" class="px-5 py-2 bg-white/5 hover:bg-white/10 text-gray-300 rounded-xl border border-white/10 transition text-sm">Cancellare</a>
            <button type="submit" class="px-5 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-xl transition">Salva modifiche</button>
        </div>
    </form>
</div>
@endsection
