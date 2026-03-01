@extends('layouts.dashboard')

@section('title', 'Edit Release')
@section('page-title', 'Edit Release')
@section('page-subtitle', $track->title)

@section('content')
@php
    $releaseType = $track->release_type ?: 'single';
    $hasSpotifyApple = $track->has_spotify_apple ?: 'NO';
    $cmSociety = old('cm_society', $track->cm_society) ?: 'NONE';
    $isYoutubeBeat = old('is_youtube_beat', $track->is_youtube_beat) ? true : false;
@endphp
<style>
    #edit-release-form select.genre-select,
    #edit-release-form select.genre-select option { background-color: #1f2937; color: #e5e7eb; }
    #edit-release-form select.genre-select option:checked { background: linear-gradient(90deg, rgb(147 51 234), rgb(79 70 229)); color: #fff; }
</style>
<div x-data="{
    step: 1,
    releaseType: '{{ $releaseType }}',
    hasSpotifyApple: '{{ $hasSpotifyApple }}',
    cmSociety: '{{ $cmSociety }}',
    isYoutubeBeat: {{ $isYoutubeBeat ? 'true' : 'false' }},
    coverPreview: '',
    coverFilePath: '',
    coverUploading: false,
    coverUploadProgress: 0,
    coverUploadTimeLeft: null,
    trackUploading: false,
    trackUploadProgress: 0,
    trackUploadTimeLeft: null,
    audioFilePath: '',
    submitting: false,
    get maxStep() { return this.hasSpotifyApple === 'YES' ? 7 : 6 },
    get stepLabels() {
        return this.hasSpotifyApple === 'YES'
            ? ['Release Type', 'Main Info', 'Artist & Release', 'Streaming', 'Platform Links', 'Social Media', 'Cover & Upload']
            : ['Release Type', 'Main Info', 'Artist & Release', 'Streaming', 'Social Media', 'Cover & Upload'];
    },
    formatTimeLeft(sec) {
        if (sec == null || sec < 0 || !isFinite(sec)) return '';
        if (sec < 60) return '~' + Math.round(sec) + ' sec left';
        if (sec < 3600) return '~' + Math.round(sec / 60) + ' min left';
        return '~' + (sec / 3600).toFixed(1) + ' hr left';
    },
    init() {
        this.$watch('hasSpotifyApple', (val, old) => {
            if (!old) return;
            if (val === 'NO' && this.step === 7) this.step = 6;
            if (val === 'NO' && this.step === 6) this.step = 5;
        });
    },
    uploadCover(fileInput) {
        const file = fileInput?.files?.[0];
        if (!file) return;
        const form = document.getElementById('edit-release-form');
        const url = form.getAttribute('data-upload-cover-url');
        if (!url) return;
        this.coverPreview = URL.createObjectURL(file);
        this.coverUploading = true;
        this.coverUploadProgress = 0;
        this.coverUploadTimeLeft = null;
        const coverStartTime = Date.now();
        const fd = new FormData();
        fd.append('file', file);
        fd.append('_token', form.querySelector('input[name=_token]').value);
        const xhr = new XMLHttpRequest();
        xhr.open('POST', url);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.setRequestHeader('Accept', 'application/json');
        xhr.setRequestHeader('X-CSRF-TOKEN', form.querySelector('input[name=_token]').value);
        xhr.upload.onprogress = (ev) => {
            if (ev.lengthComputable) {
                this.coverUploadProgress = Math.round((ev.loaded/ev.total)*100);
                const elapsed = (Date.now() - coverStartTime) / 1000;
                const speed = ev.loaded / elapsed;
                this.coverUploadTimeLeft = speed > 0 ? (ev.total - ev.loaded) / speed : null;
            }
        };
        xhr.onload = () => {
            this.coverUploading = false;
            this.coverUploadTimeLeft = null;
            if (xhr.status === 200) { try { const r = JSON.parse(xhr.responseText); if (r.path) this.coverFilePath = r.path; } catch(e) {} }
            const el = form.querySelector('input[name=cover_art]');
            if (el) el.value = '';
        };
        xhr.onerror = () => { this.coverUploading = false; this.coverUploadTimeLeft = null; };
        xhr.send(fd);
    },
    uploadSingleTrack(fileInput) {
        const file = fileInput?.files?.[0];
        if (!file) return;
        const form = document.getElementById('edit-release-form');
        const url = form.getAttribute('data-upload-audio-url');
        if (!url) return;
        this.trackUploading = true;
        this.trackUploadProgress = 0;
        this.trackUploadTimeLeft = null;
        const trackStartTime = Date.now();
        const fd = new FormData();
        fd.append('file', file);
        fd.append('type', 'single');
        fd.append('_token', form.querySelector('input[name=_token]').value);
        const xhr = new XMLHttpRequest();
        xhr.open('POST', url);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.setRequestHeader('Accept', 'application/json');
        xhr.setRequestHeader('X-CSRF-TOKEN', form.querySelector('input[name=_token]').value);
        xhr.upload.onprogress = (ev) => {
            if (ev.lengthComputable) {
                this.trackUploadProgress = Math.round((ev.loaded/ev.total)*100);
                const elapsed = (Date.now() - trackStartTime) / 1000;
                const speed = ev.loaded / elapsed;
                this.trackUploadTimeLeft = speed > 0 ? (ev.total - ev.loaded) / speed : null;
            }
        };
        xhr.onload = () => {
            this.trackUploading = false;
            this.trackUploadTimeLeft = null;
            if (xhr.status === 200) { try { const r = JSON.parse(xhr.responseText); if (r.path) this.audioFilePath = r.path; } catch(e) {} }
            const el = form.querySelector('input[name=audio_file]');
            if (el) el.value = '';
        };
        xhr.onerror = () => { this.trackUploading = false; this.trackUploadTimeLeft = null; };
        xhr.send(fd);
    },
    clearCover() {
        this.coverPreview = '';
        this.coverFilePath = '';
        const form = document.getElementById('edit-release-form');
        if (form) { const el = form.querySelector('input[name=cover_art]'); if (el) el.value = ''; }
    },
    clearSingleTrack() {
        this.audioFilePath = '';
        const form = document.getElementById('edit-release-form');
        if (form) { const el = form.querySelector('input[name=audio_file]'); if (el) el.value = ''; }
    },
    onEditSubmit(e) {
        this.submitting = true;
        const form = document.getElementById('edit-release-form');
        if (this.coverFilePath) { form.querySelector('input[name=cover_art]')?.removeAttribute('name'); }
        if (this.audioFilePath) { form.querySelector('input[name=audio_file]')?.removeAttribute('name'); }
    }
}">

    <a href="{{ route('dashboard.releases.show', $track->id) }}" class="inline-flex items-center space-x-2 text-gray-400 hover:text-white transition text-sm mb-6">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        <span>Back to Release</span>
    </a>

    @if(in_array($track->status, ['Released','Rejected']))
    <div class="mb-6 p-4 rounded-xl bg-amber-900/30 border border-amber-500/30 text-amber-200 text-sm flex items-center space-x-2">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span>Editing this release will move it to "Modify Pending" for review.</span>
    </div>
    @endif

    @if($errors->any())
    <div class="mb-6 p-4 rounded-xl border border-red-500/30 bg-red-900/10 text-red-400 text-sm">
        <ul class="space-y-1 list-disc list-inside">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
    @endif

    <!-- Progress Steps -->
    <div class="flex items-center justify-center overflow-x-auto pb-2 mb-8 flex-nowrap">
        <div class="flex items-center gap-0 shrink-0">
            <template x-for="(label, index) in stepLabels" :key="index">
                <div class="flex items-center shrink-0">
                    <div :class="step >= index + 1 ? 'bg-purple-600 text-white' : 'bg-white/10 text-gray-400'"
                         class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold transition"
                         x-text="index + 1"></div>
                    <span :class="step >= index + 1 ? 'text-white' : 'text-gray-500'" class="ml-1 text-xs whitespace-nowrap max-w-[4.5rem] sm:max-w-none truncate" x-text="label"></span>
                    <div class="w-1 sm:w-2 h-px bg-white/10 mx-0.5 sm:mx-1 shrink-0" x-show="index < stepLabels.length - 1"></div>
                </div>
            </template>
        </div>
    </div>

    <form method="POST" action="{{ route('dashboard.releases.update', $track->id) }}" enctype="multipart/form-data" id="edit-release-form" data-upload-cover-url="{{ route('dashboard.releases.upload-cover') }}" data-upload-audio-url="{{ route('dashboard.releases.upload-audio') }}" @submit="onEditSubmit($event)">
        @csrf
        @method('PUT')
        <input type="hidden" name="cover_art_path" :value="coverFilePath">
        <input type="hidden" name="audio_file_path" :value="audioFilePath">

        <!-- Step 1: Release Type (read-only) -->
        <div x-show="step === 1" x-cloak class="glass rounded-2xl p-8">
            <h2 class="text-xl font-bold text-white mb-6 text-center">Release Type</h2>
            <div class="max-w-md mx-auto text-center">
                <p class="text-gray-300 text-lg capitalize">{{ $track->release_type }}</p>
                <p class="text-gray-500 text-sm mt-2">Release type cannot be changed when editing.</p>
            </div>
            <div class="flex justify-end mt-8">
                <button type="button" @click="step = 2" class="px-8 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-semibold rounded-xl transition">Next →</button>
            </div>
        </div>

        <!-- Step 2: Main Info -->
        <div x-show="step === 2" x-cloak class="glass rounded-2xl p-6">
            <h2 class="text-lg font-bold text-white mb-4">Main Info</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                @if($track->release_type === 'album')
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Album Title</label>
                    <input type="text" name="album_title" value="{{ old('album_title', $track->album_title) }}" class="w-full bg-white/5 border border-white/10 text-white px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500">
                </div>
                @endif
                <div class="{{ $track->release_type === 'album' ? '' : 'md:col-span-2' }}">
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Title <span class="text-red-400">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $track->title) }}" required class="w-full bg-white/5 border border-white/10 text-white px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500">
                </div>
            </div>
            <div class="flex justify-between mt-8">
                <button type="button" @click="step = 1" class="px-6 py-2.5 bg-white/10 hover:bg-white/15 text-white font-medium rounded-xl transition text-sm">← Back</button>
                <button type="button" @click="step = 3" class="px-8 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-semibold rounded-xl transition">Next →</button>
            </div>
        </div>

        <!-- Step 3: Artist & Release -->
        <div x-show="step === 3" x-cloak class="glass rounded-2xl p-6 space-y-6">
            <h2 class="text-lg font-bold text-white mb-4">Artist and Release Information</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div><label class="block text-sm font-medium text-gray-300 mb-1.5">First name</label><input type="text" name="first_name" value="{{ old('first_name', $track->first_name) }}" class="w-full bg-white/5 border border-white/10 text-white px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500"></div>
                <div><label class="block text-sm font-medium text-gray-300 mb-1.5">Last name</label><input type="text" name="last_name" value="{{ old('last_name', $track->last_name) }}" class="w-full bg-white/5 border border-white/10 text-white px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500"></div>
                <div><label class="block text-sm font-medium text-gray-300 mb-1.5">Stage name</label><input type="text" name="stage_name" value="{{ old('stage_name', $track->stage_name) }}" class="w-full bg-white/5 border border-white/10 text-white px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500"></div>
                <div><label class="block text-sm font-medium text-gray-300 mb-1.5">Main Artist <span class="text-red-400">*</span></label><input type="text" name="artists" value="{{ old('artists', $track->artists) }}" required class="w-full bg-white/5 border border-white/10 text-white px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500"></div>
                <div class="md:col-span-2"><label class="block text-sm font-medium text-gray-300 mb-1.5">Featuring</label><input type="text" name="featuring_artists" value="{{ old('featuring_artists', $track->featuring_artists) }}" class="w-full bg-white/5 border border-white/10 text-white px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500"></div>
                <div><label class="block text-sm font-medium text-gray-300 mb-1.5">Release date <span class="text-red-400">*</span></label><input type="date" name="release_date" value="{{ old('release_date', $track->release_date ? $track->release_date->format('Y-m-d') : '') }}" required class="w-full bg-white/5 border border-white/10 text-white px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500"></div>
                <div><label class="block text-sm font-medium text-gray-300 mb-1.5">ISRC</label><input type="text" name="isrc" value="{{ old('isrc', $track->isrc) }}" class="w-full bg-white/5 border border-white/10 text-white px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500"></div>
                <div><label class="block text-sm font-medium text-gray-300 mb-1.5">Authors</label><input type="text" name="authors" value="{{ old('authors', $track->authors) }}" class="w-full bg-white/5 border border-white/10 text-white px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500"></div>
                <div><label class="block text-sm font-medium text-gray-300 mb-1.5">Composers</label><input type="text" name="composers" value="{{ old('composers', $track->composers) }}" class="w-full bg-white/5 border border-white/10 text-white px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500"></div>
                <div><label class="block text-sm font-medium text-gray-300 mb-1.5">Producer</label><input type="text" name="producer" value="{{ old('producer', $track->producer) }}" class="w-full bg-white/5 border border-white/10 text-white px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500"></div>
                <div><label class="block text-sm font-medium text-gray-300 mb-1.5">Primary genre <span class="text-red-400">*</span></label>
                    <select name="primary_genre" required class="genre-select w-full bg-gray-800 border border-white/10 text-white px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500">
                        @foreach(['Pop','Hip-Hop','R&B','Electronic','Rock','Alternative','Jazz','Classical','Country','Latin','Folk','Reggae'] as $g)
                            <option value="{{ $g }}" {{ old('primary_genre', $track->primary_genre) == $g ? 'selected' : '' }}>{{ $g }}</option>
                        @endforeach
                    </select>
                </div>
                <div><label class="block text-sm font-medium text-gray-300 mb-1.5">Secondary genre</label><input type="text" name="secondary_genre" value="{{ old('secondary_genre', $track->secondary_genre) }}" class="w-full bg-white/5 border border-white/10 text-white px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500"></div>
                <div class="md:col-span-2"><label class="flex items-center space-x-2 cursor-pointer"><input type="checkbox" name="is_explicit" value="1" {{ old('is_explicit', $track->is_explicit) ? 'checked' : '' }} class="w-4 h-4 rounded border-white/20 bg-white/5 text-purple-600"><span class="text-sm text-gray-300">Explicit content</span></label></div>
                <div><label class="block text-sm font-medium text-gray-300 mb-1.5">TikTok start time</label><input type="text" name="tik_tok_start_time" value="{{ old('tik_tok_start_time', $track->tik_tok_start_time) }}" class="w-full bg-white/5 border border-white/10 text-white px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500"></div>
                <div><label class="block text-sm font-medium text-gray-300 mb-1.5">Song duration</label><input type="text" name="song_duration" value="{{ old('song_duration', $track->song_duration) }}" class="w-full bg-white/5 border border-white/10 text-white px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500" placeholder="00:00:00"></div>
                <div class="md:col-span-2"><label class="block text-sm font-medium text-gray-300 mb-1.5">Short bio</label><textarea name="short_bio" rows="3" class="w-full bg-white/5 border border-white/10 text-white px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500 resize-none">{{ old('short_bio', $track->short_bio) }}</textarea></div>
                <div class="md:col-span-2"><label class="block text-sm font-medium text-gray-300 mb-1.5">Track description</label><textarea name="track_description" rows="3" class="w-full bg-white/5 border border-white/10 text-white px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500 resize-none">{{ old('track_description', $track->track_description) }}</textarea></div>
                <div class="md:col-span-2"><label class="block text-sm font-medium text-gray-300 mb-1.5">Collecting societies (optional)</label>
                    <div class="space-y-2">
                        <label class="flex items-center space-x-2 cursor-pointer"><input type="radio" name="cm_society" value="SIAE" x-model="cmSociety" class="w-4 h-4 border-white/20 text-purple-600"><span class="text-sm text-gray-300">SIAE</span></label>
                        <label class="flex items-center space-x-2 cursor-pointer"><input type="radio" name="cm_society" value="SOUNDREEF" x-model="cmSociety" class="w-4 h-4 border-white/20 text-purple-600"><span class="text-sm text-gray-300">SOUNDREEF</span></label>
                        <label class="flex items-center space-x-2 cursor-pointer"><input type="radio" name="cm_society" value="NONE" x-model="cmSociety" class="w-4 h-4 border-white/20 text-purple-600"><span class="text-sm text-gray-300">Non sono membro di nessuna società</span></label>
                        <div x-show="cmSociety === 'SIAE'" x-cloak class="mt-3">
                            <label class="block text-sm font-medium text-gray-300 mb-1.5">SIAE position number (optional)</label>
                            <input type="text" name="siae_position" value="{{ old('siae_position', $track->siae_position) }}" class="w-full bg-white/5 border border-white/10 text-white px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500" placeholder="SIAE position number">
                        </div>
                    </div>
                </div>
                <div class="md:col-span-2"><label class="block text-sm font-medium text-gray-300 mb-1.5">Distribution details</label><textarea name="distribution_details" rows="2" class="w-full bg-white/5 border border-white/10 text-white px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500 resize-none">{{ old('distribution_details', $track->distribution_details) }}</textarea></div>
                <div class="md:col-span-2"><label class="flex items-center space-x-2 cursor-pointer"><input type="checkbox" name="is_youtube_beat" value="1" x-model="isYoutubeBeat" {{ old('is_youtube_beat', $track->is_youtube_beat) ? 'checked' : '' }} class="w-4 h-4 rounded border-white/20 text-purple-600"><span class="text-sm text-gray-300">Beat from YouTube</span></label></div>
                <div class="md:col-span-2" x-show="isYoutubeBeat" x-cloak><label class="flex items-center space-x-2 cursor-pointer"><input type="checkbox" name="has_license" value="1" {{ old('has_license', $track->has_license) ? 'checked' : '' }} class="w-4 h-4 rounded border-white/20 text-purple-600"><span class="text-sm text-gray-300">I have the license</span></label></div>
            </div>
            <div class="flex justify-between mt-8">
                <button type="button" @click="step = 2" class="px-6 py-2.5 bg-white/10 hover:bg-white/15 text-white font-medium rounded-xl transition text-sm">← Back</button>
                <button type="button" @click="step = 4" class="px-8 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-semibold rounded-xl transition">Next →</button>
            </div>
        </div>

        <!-- Step 4: Streaming -->
        <div x-show="step === 4" x-cloak class="glass rounded-2xl p-6">
            <h2 class="text-lg font-bold text-white mb-4">Streaming Platforms</h2>
            <div class="space-y-4">
                <label class="flex items-center space-x-3 cursor-pointer p-4 rounded-xl border-2 transition" :class="hasSpotifyApple === 'YES' ? 'border-purple-500 bg-purple-600/10' : 'border-white/10'">
                    <input type="radio" name="has_spotify_apple" value="YES" x-model="hasSpotifyApple" class="w-4 h-4 text-purple-600">
                    <span class="text-gray-300">YES – I have both profiles</span>
                </label>
                <label class="flex items-center space-x-3 cursor-pointer p-4 rounded-xl border-2 transition" :class="hasSpotifyApple === 'NO' ? 'border-purple-500 bg-purple-600/10' : 'border-white/10'">
                    <input type="radio" name="has_spotify_apple" value="NO" x-model="hasSpotifyApple" class="w-4 h-4 text-purple-600">
                    <span class="text-gray-300">NO – I need to create them</span>
                </label>
            </div>
            <div class="flex justify-between mt-8">
                <button type="button" @click="step = 3" class="px-6 py-2.5 bg-white/10 hover:bg-white/15 text-white font-medium rounded-xl transition text-sm">← Back</button>
                <button type="button" @click="step = 5" class="px-8 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold rounded-xl transition">Next →</button>
            </div>
        </div>

        <!-- Step 5 (if YES): Platform Links -->
        <div x-show="step === 5 && hasSpotifyApple === 'YES'" x-cloak class="glass rounded-2xl p-6">
            <h2 class="text-lg font-bold text-white mb-4">Platform Links</h2>
            <div class="space-y-4">
                <div><label class="block text-sm font-medium text-gray-300 mb-1.5">Spotify link</label><input type="url" name="spotify_link" value="{{ old('spotify_link', $track->spotify_link) }}" class="w-full bg-white/5 border border-white/10 text-white px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500"></div>
                <div><label class="block text-sm font-medium text-gray-300 mb-1.5">Apple Music link</label><input type="url" name="apple_music_link" value="{{ old('apple_music_link', $track->apple_music_link) }}" class="w-full bg-white/5 border border-white/10 text-white px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500"></div>
            </div>
            <div class="flex justify-between mt-8">
                <button type="button" @click="step = 4" class="px-6 py-2.5 bg-white/10 hover:bg-white/15 text-white font-medium rounded-xl transition text-sm">← Back</button>
                <button type="button" @click="step = 6" class="px-8 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold rounded-xl transition">Next →</button>
            </div>
        </div>

        <!-- Step 5 (if NO) or 6 (if YES): Social Media -->
        <div x-show="(step === 5 && hasSpotifyApple === 'NO') || (step === 6 && hasSpotifyApple === 'YES')" x-cloak class="glass rounded-2xl p-6">
            <h2 class="text-lg font-bold text-white mb-4">Social Media Links</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div><label class="block text-sm font-medium text-gray-300 mb-1.5">TikTok</label><input type="url" name="tik_tok_link" value="{{ old('tik_tok_link', $track->tik_tok_link) }}" class="w-full bg-white/5 border border-white/10 text-white px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500"></div>
                <div><label class="block text-sm font-medium text-gray-300 mb-1.5">YouTube</label><input type="url" name="youtube_link" value="{{ old('youtube_link', $track->youtube_link) }}" class="w-full bg-white/5 border border-white/10 text-white px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500"></div>
            </div>
            <div class="flex justify-between mt-8">
                <button type="button" @click="step = (hasSpotifyApple === 'YES' ? 5 : 4)" class="px-6 py-2.5 bg-white/10 hover:bg-white/15 text-white font-medium rounded-xl transition text-sm">← Back</button>
                <button type="button" @click="step = (hasSpotifyApple === 'YES' ? 7 : 6)" class="px-8 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold rounded-xl transition">Next →</button>
            </div>
        </div>

        <!-- Last step: Cover, Track & Lyrics (matches upload: no description field) -->
        <div x-show="(step === 6 && hasSpotifyApple === 'NO') || (step === 7 && hasSpotifyApple === 'YES')" x-cloak class="glass rounded-2xl p-6 space-y-8">
            <h2 class="text-lg font-bold text-white">Cover, Track & Lyrics</h2>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Cover art</label>
                @if($track->cover_art)
                <div class="flex items-center gap-4 mb-3" x-show="!coverPreview && !coverFilePath">
                    <img src="{{ $track->cover_art_url }}" class="w-20 h-20 rounded-xl object-cover" alt="Current cover">
                    <span class="text-gray-400 text-sm">Current cover. Upload a new file to replace.</span>
                </div>
                @endif
                <div x-show="coverPreview" x-cloak class="flex items-center space-x-4 mb-3">
                    <div class="relative">
                        <img :src="coverPreview" class="w-20 h-20 rounded-xl object-cover">
                        <button type="button" @click="clearCover()" class="absolute -top-1 -right-1 w-6 h-6 rounded-full bg-red-500 hover:bg-red-600 text-white flex items-center justify-center text-sm font-bold shadow">×</button>
                    </div>
                    <span class="text-gray-400 text-sm">New cover selected. Click × to remove.</span>
                </div>
                <div x-show="coverFilePath && !coverPreview" x-cloak class="mb-3 flex items-center gap-2">
                    <span class="text-green-400 text-sm">Cover uploaded</span>
                    <button type="button" @click="clearCover()" class="p-1 rounded text-red-400 hover:bg-red-500/20">×</button>
                </div>
                <div x-show="coverUploading" x-cloak class="mb-3 p-3 rounded-xl bg-white/5 border border-white/10">
                    <p class="text-gray-400 text-sm mb-2">Uploading cover...</p>
                    <div class="h-2 bg-white/10 rounded-full overflow-hidden">
                        <div class="h-full bg-purple-600 transition-all duration-300" :style="'width:'+coverUploadProgress+'%'"></div>
                    </div>
                    <p class="text-gray-500 text-xs mt-1"><span x-text="coverUploadProgress+'%'"></span><span x-show="coverUploadTimeLeft != null" x-cloak class="ml-2" x-text="formatTimeLeft(coverUploadTimeLeft)"></span></p>
                </div>
                <input type="file" name="cover_art" accept="image/*" class="w-full bg-white/5 border border-white/10 text-white px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:bg-purple-600 file:text-white file:text-sm" @change="uploadCover($event.target)">
            </div>

            @if($track->release_type === 'single')
            <div>
                <h3 class="text-white font-medium mb-3">Track</h3>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Audio file</label>
                @if($track->audio_file)
                <div class="mb-3 p-3 rounded-xl bg-white/5 border border-white/10" x-show="!audioFilePath">
                    <p class="text-gray-400 text-sm mb-2">Current track. Upload a new file to replace.</p>
                    <audio controls class="w-full max-w-md h-10" src="{{ route('files.audio', $track->id) }}">Your browser does not support the audio element.</audio>
                    <p class="text-gray-500 text-xs mt-1">{{ basename($track->audio_file) }}</p>
                </div>
                @endif
                <div x-show="audioFilePath" x-cloak class="mb-3 flex items-center gap-2">
                    <span class="text-green-400 text-sm">Track uploaded</span>
                    <button type="button" @click="clearSingleTrack()" class="p-1 rounded text-red-400 hover:bg-red-500/20">×</button>
                </div>
                <div x-show="trackUploading" x-cloak class="mb-3 p-3 rounded-xl bg-white/5 border border-white/10">
                    <p class="text-gray-400 text-sm mb-2">Uploading track...</p>
                    <div class="h-2 bg-white/10 rounded-full overflow-hidden">
                        <div class="h-full bg-purple-600 transition-all duration-300" :style="'width:'+trackUploadProgress+'%'"></div>
                    </div>
                    <p class="text-gray-500 text-xs mt-1"><span x-text="trackUploadProgress+'%'"></span><span x-show="trackUploadTimeLeft != null" x-cloak class="ml-2" x-text="formatTimeLeft(trackUploadTimeLeft)"></span></p>
                </div>
                <input type="file" name="audio_file" accept=".mp3,.wav,.flac,.aac,.ogg" class="w-full bg-white/5 border border-white/10 text-white px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:bg-purple-600 file:text-white file:text-sm" @change="uploadSingleTrack($event.target)">
                <p class="text-xs text-gray-500 mt-1">MP3, WAV, FLAC, AAC, OGG — max 2GB. Select a file to upload immediately.</p>
            </div>
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1.5">Lyrics <span class="text-red-400">*</span> <span class="text-gray-500 font-normal">(required for Apple Music)</span></label>
                <textarea name="lyrics" rows="6" required class="w-full bg-white/5 border border-white/10 text-white px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500 resize-none" placeholder="Paste or type lyrics...">{{ old('lyrics', $track->lyrics) }}</textarea>
            </div>

            <div class="flex justify-between pt-4 border-t border-white/10">
                <button type="button" @click="step = (hasSpotifyApple === 'YES' ? 6 : 5)" class="px-6 py-2.5 bg-white/10 hover:bg-white/15 text-white font-medium rounded-xl transition text-sm">← Back</button>
                <button type="submit" :disabled="submitting || coverUploading || trackUploading" class="px-8 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-semibold rounded-xl transition disabled:opacity-70 disabled:cursor-not-allowed flex items-center gap-2">
                    <span x-show="!submitting">Save Changes</span>
                    <span x-show="submitting" x-cloak class="flex items-center gap-2"><svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Saving...</span>
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
