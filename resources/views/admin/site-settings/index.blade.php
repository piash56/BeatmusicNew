@extends('layouts.admin')

@section('title', 'Site Settings')
@section('page-title', 'Site Settings')

@section('content')
<div class="max-w-4xl space-y-6">

    @if(session('success'))
        <div class="bg-green-900/30 border border-green-500/30 text-green-300 rounded-xl p-3 text-sm">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.site-settings.update') }}" enctype="multipart/form-data">
        @csrf @method('PUT')

        {{-- General --}}
        <div class="bg-gray-900 rounded-2xl border border-white/5 p-6 mb-6">
            <h2 class="text-white font-semibold mb-5">General</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="text-sm text-gray-400 mb-1.5 block">Site Title</label>
                    <input type="text" name="site_title" value="{{ old('site_title', $settings->site_title ?? '') }}"
                        class="w-full bg-gray-800 border border-white/10 text-white px-4 py-2.5 rounded-xl text-sm focus:outline-none focus:border-purple-500">
                </div>
                <div>
                    <label class="text-sm text-gray-400 mb-1.5 block">Logo Alt Text</label>
                    <input type="text" name="logo_alt" value="{{ old('logo_alt', $settings->logo_alt ?? '') }}"
                        class="w-full bg-gray-800 border border-white/10 text-white px-4 py-2.5 rounded-xl text-sm focus:outline-none focus:border-purple-500">
                </div>
                <div>
                    <label class="text-sm text-gray-400 mb-1.5 block">Copyright Text</label>
                    <input type="text" name="copyright_text" value="{{ old('copyright_text', $settings->copyright_text ?? '') }}"
                        class="w-full bg-gray-800 border border-white/10 text-white px-4 py-2.5 rounded-xl text-sm focus:outline-none focus:border-purple-500">
                </div>
                <div class="sm:col-span-2">
                    <label class="text-sm text-gray-400 mb-1.5 block">Footer Text</label>
                    <textarea name="footer_text" rows="3"
                        class="w-full bg-gray-800 border border-white/10 text-white px-4 py-2.5 rounded-xl text-sm focus:outline-none focus:border-purple-500 resize-none">{{ old('footer_text', $settings->footer_text ?? '') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Branding --}}
        <div class="bg-gray-900 rounded-2xl border border-white/5 p-6 mb-6">
            <h2 class="text-white font-semibold mb-5">Branding</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="text-sm text-gray-400 mb-1.5 block">Logo</label>
                    @if(!empty($settings->logo_url))
                        <div class="flex items-center gap-3 mb-2">
                            <img src="{{ Storage::url($settings->logo_url) }}" class="h-10" alt="Current logo">
                            <label class="flex items-center gap-2 text-sm text-red-400 hover:text-red-300 cursor-pointer">
                                <input type="checkbox" name="remove_logo" value="1" class="rounded border-white/20 text-red-500">
                                Remove (use default)
                            </label>
                        </div>
                    @else
                        <p class="text-gray-500 text-sm mb-2">No custom logo. Default is used.</p>
                    @endif
                    <input type="file" name="logo" accept="image/*" class="text-sm text-gray-300 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:bg-purple-600 file:text-white hover:file:bg-purple-700">
                </div>
                <div>
                    <label class="text-sm text-gray-400 mb-1.5 block">Favicon</label>
                    @if(!empty($settings->favicon))
                        <div class="flex items-center gap-3 mb-2">
                            <img src="{{ Storage::url($settings->favicon) }}" class="h-8" alt="Current favicon">
                            <label class="flex items-center gap-2 text-sm text-red-400 hover:text-red-300 cursor-pointer">
                                <input type="checkbox" name="remove_favicon" value="1" class="rounded border-white/20 text-red-500">
                                Remove (use default)
                            </label>
                        </div>
                    @else
                        <p class="text-gray-500 text-sm mb-2">No custom favicon. Default is used.</p>
                    @endif
                    <input type="file" name="favicon" accept="image/*" class="text-sm text-gray-300 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:bg-purple-600 file:text-white hover:file:bg-purple-700">
                </div>
            </div>
        </div>

        {{-- Social Links --}}
        <div class="bg-gray-900 rounded-2xl border border-white/5 p-6 mb-6">
            <h2 class="text-white font-semibold mb-5">Social Links</h2>
            @php $social = $settings->social_links ?? []; @endphp
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach(['facebook'=>'Facebook','twitter'=>'Twitter / X','instagram'=>'Instagram','youtube'=>'YouTube','tiktok'=>'TikTok'] as $key => $label)
                <div>
                    <label class="text-sm text-gray-400 mb-1.5 block">{{ $label }}</label>
                    <input type="url" name="social_{{ $key }}" value="{{ old('social_'.$key, $social[$key] ?? '') }}"
                        class="w-full bg-gray-800 border border-white/10 text-white px-4 py-2.5 rounded-xl text-sm focus:outline-none focus:border-purple-500" placeholder="https://">
                </div>
                @endforeach
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="px-6 py-2.5 bg-purple-600 hover:bg-purple-700 text-white text-sm rounded-xl transition font-medium">Save Settings</button>
        </div>
    </form>
</div>
@endsection
