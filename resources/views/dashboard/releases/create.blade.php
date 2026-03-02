@extends('layouts.dashboard')

@section('title', 'Carica versione')
@section('page-title', 'Carica nuova versione')
@section('page-subtitle', 'Condividi la tua musica con il mondo')

@section('content')
<div data-clear-draft-after="{{ $clearDraftIfSubmittedAfter ?? '' }}" x-data="{
    step: 1,
    releaseType: '',
    hasSpotifyApple: 'NO',
    coverPreview: '',
    coverFilePath: '',
    coverUploading: false,
    coverUploadProgress: 0,
    coverUploadTimeLeft: null,
    albumTracks: [{ title: '', file: null, uploadedPath: '', uploadProgress: 0, uploadTimeLeft: null }, { title: '', file: null, uploadedPath: '', uploadProgress: 0, uploadTimeLeft: null }],
    invalidFields: { title: false, album_title: false, artists: false, release_date: false, primary_genre: false, spotify_link: false, apple_music_link: false, cover_art: false, audio_file: false, lyrics: false },
    trackUploading: false,
    trackUploadProgress: 0,
    trackUploadTimeLeft: null,
    audioFilePath: '',
    submitting: false,
    draftRestored: false,
    uploadError: '',
    uploadWaitMessage: '',
    get uploadsInProgress() { return this.coverUploading || this.trackUploading || (this.releaseType === 'album' && this.albumTracks.some(t => (t.uploadProgress || 0) > 0 && (t.uploadProgress || 0) < 100)); },
    get maxStep() { return this.hasSpotifyApple === 'YES' ? 7 : 6 },
    get stepLabels() {
        return this.hasSpotifyApple === 'YES'
            ? ['Release Type', 'Main Info', 'Artist & Release', 'Streaming', 'Platform Links', 'Social Media', 'Cover & Upload']
            : ['Release Type', 'Main Info', 'Artist & Release', 'Streaming', 'Social Media', 'Cover & Upload'];
    },
    clearStepErrors() { for (let k in this.invalidFields) this.invalidFields[k] = false; },
    formatTimeLeft(sec) {
        if (sec == null || sec < 0 || !isFinite(sec)) return '';
        if (sec < 60) return '~' + Math.round(sec) + ' sec left';
        if (sec < 3600) return '~' + Math.round(sec / 60) + ' min left';
        return '~' + (sec / 3600).toFixed(1) + ' hr left';
    },
    validateStep() {
        const inv = this.invalidFields;
        if (this.step === 2) {
            inv.title = !this.$refs.titleInput?.value?.trim();
            inv.album_title = this.releaseType === 'album' && !this.$refs.albumTitleInput?.value?.trim();
            if (inv.title || inv.album_title) return false;
        }
        if (this.step === 3) {
            inv.artists = !this.$refs.artistsInput?.value?.trim();
            inv.release_date = !this.$refs.releaseDateInput?.value?.trim();
            inv.primary_genre = !this.$refs.primaryGenreSelect?.value?.trim();
            if (inv.artists || inv.release_date || inv.primary_genre) return false;
        }
        if (this.step === 5 && this.hasSpotifyApple === 'YES') {
            inv.spotify_link = !this.$refs.spotifyLinkInput?.value?.trim();
            inv.apple_music_link = !this.$refs.appleMusicLinkInput?.value?.trim();
            if (inv.spotify_link || inv.apple_music_link) return false;
        }
        const lastStep = (this.step === 6 && this.hasSpotifyApple === 'NO') || (this.step === 7 && this.hasSpotifyApple === 'YES');
        if (lastStep) {
            inv.cover_art = !this.coverFilePath && !(document.querySelector('input[name=cover_art]')?.files?.length);
            inv.lyrics = !this.$refs.lyricsInput?.value?.trim();
            if (this.releaseType === 'single') inv.audio_file = !this.audioFilePath && !(this.$refs.audioFileInput?.files?.length);
            if (this.releaseType === 'album') {
                const withFile = document.querySelectorAll('input[name^=album_tracks]');
                let fileCount = 0;
                withFile.forEach(i => { if (i.files && i.files.length) fileCount++; });
                const pathCount = this.albumTracks.filter(t => t.uploadedPath).length;
                inv.audio_file = (fileCount + pathCount) < 2;
            }
            if (inv.cover_art || inv.lyrics || inv.audio_file) return false;
        }
        this.clearStepErrors();
        return true;
    },
    nextStep() { if (this.step >= this.maxStep) return; if (!this.validateStep()) return; this.step++; this.saveDraft(); },
    prevStep() { if (this.step > 1) { this.clearStepErrors(); this.step--; this.saveDraft(); } },
    saveDraft() {
        try {
            const form = document.getElementById('release-form');
            if (!form) return;
            const data = { step: this.step, releaseType: this.releaseType, hasSpotifyApple: this.hasSpotifyApple, albumTracks: JSON.parse(JSON.stringify(this.albumTracks)), audioFilePath: this.audioFilePath, coverFilePath: this.coverFilePath, savedAt: Date.now(), form: {} };
            form.querySelectorAll('input, select, textarea').forEach(el => {
                if (!el.name || el.type === 'file') return;
                if (el.type === 'radio') { if (el.checked) data.form[el.name] = el.value; }
                else if (el.type === 'checkbox') data.form[el.name] = el.checked ? '1' : '';
                else data.form[el.name] = el.value || '';
            });
            localStorage.setItem('releaseDraft', JSON.stringify(data));
        } catch (e) {}
    },
    restoreDraft() {
        try {
            const raw = localStorage.getItem('releaseDraft');
            if (!raw) return;
            const data = JSON.parse(raw);
            if (Date.now() - data.savedAt > 600000) { localStorage.removeItem('releaseDraft'); return; }
            const submittedAfter = this.$el.dataset.clearDraftAfter;
            if (submittedAfter && data.savedAt < parseInt(submittedAfter, 10)) { localStorage.removeItem('releaseDraft'); return; }
            this.step = data.step || 1;
            this.releaseType = data.releaseType || '';
            this.hasSpotifyApple = data.hasSpotifyApple || 'NO';
            if (data.albumTracks && data.albumTracks.length) {
                this.albumTracks = data.albumTracks.map(t => ({ ...t, uploadProgress: t.uploadProgress || 0 }));
            }
            if (data.audioFilePath) this.audioFilePath = data.audioFilePath;
            if (data.coverFilePath) this.coverFilePath = data.coverFilePath;
            const form = document.getElementById('release-form');
            if (form && data.form) {
                Object.keys(data.form).forEach(name => {
                    const first = form.querySelector('[name='+JSON.stringify(name)+']');
                    if (!first || first.type === 'file') return;
                    if (first.type === 'checkbox') first.checked = data.form[name] === '1';
                    else if (first.type === 'radio') form.querySelectorAll('[name='+JSON.stringify(name)+']').forEach(r => { r.checked = r.value === data.form[name]; });
                    else first.value = data.form[name] || '';
                });
            }
            this.draftRestored = true;
        } catch (e) {}
    },
    onSubmit(e) {
        e.preventDefault();
        if (this.uploadsInProgress) {
            this.uploadWaitMessage = 'Please wait until all uploads finish before submitting.';
            this.$nextTick(() => { const el = document.querySelector('.upload-wait-message'); if (el) el.scrollIntoView({ behavior: 'smooth', block: 'nearest' }); });
            return;
        }
        this.uploadWaitMessage = '';
        if (!this.validateStep()) return;
        this.submitting = true;
        const form = document.getElementById('release-form');
        const formData = new FormData(form);
        if (this.coverFilePath) formData.delete('cover_art');
        if (this.audioFilePath) formData.delete('audio_file');
        if (this.releaseType === 'album') this.albumTracks.forEach((t, i) => { if (t.uploadedPath) formData.delete('album_tracks['+i+']'); });
        const xhr = new XMLHttpRequest();
        const url = form.getAttribute('action');
        xhr.open('POST', url);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.setRequestHeader('Accept', 'application/json');
        xhr.setRequestHeader('X-CSRF-TOKEN', form.querySelector('input[name=_token]').value);
        xhr.onload = () => {
            this.submitting = false;
            if (xhr.status === 200 || xhr.status === 201) {
                localStorage.removeItem('releaseDraft');
                const ct = xhr.getResponseHeader('Content-Type') || '';
                if (ct.indexOf('application/json') !== -1) {
                    try {
                        const r = JSON.parse(xhr.responseText);
                        if (r.redirect) { window.location.href = r.redirect; return; }
                    } catch (e) {}
                }
                window.location.href = xhr.responseURL || url;
            } else if (xhr.status === 422) {
                const errDiv = form.querySelector('.form-errors');
                if (errDiv) {
                    try {
                        const r = JSON.parse(xhr.responseText);
                        errDiv.innerHTML = (r.errors ? Object.values(r.errors).flat() : [r.message || 'Validation failed']).map(m => '<p class=\'text-red-400 text-sm\'>• '+m+'</p>').join('');
                        errDiv.classList.remove('hidden');
                        errDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    } catch (e) { errDiv.innerHTML = '<p class=\'text-red-400 text-sm\'>Validation failed.</p>'; errDiv.classList.remove('hidden'); }
                }
            } else if (xhr.status === 413) {
                const errDiv = form.querySelector('.form-errors');
                if (errDiv) { errDiv.innerHTML = '<p class=\'text-red-400 text-sm\'>Server upload limit too low. Stop the server (Ctrl+C), run <strong>php artisan serve</strong> again, then submit. See UPLOAD-LIMIT.txt if it persists.</p>'; errDiv.classList.remove('hidden'); errDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' }); }
            }
        };
        xhr.onerror = () => { this.submitting = false; };
        xhr.send(formData);
    },
    uploadSingleTrack(fileInput) {
            const file = fileInput?.files?.[0];
            if (!file) return;
            this.uploadError = '';
            const form = document.getElementById('release-form');
            const url = form.getAttribute('data-upload-audio-url');
            if (!url) return;
            this.trackUploading = true;
            this.trackUploadProgress = 0;
            this.trackUploadTimeLeft = null;
            const startTime = Date.now();
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
                    const elapsed = (Date.now() - startTime) / 1000;
                    const speed = ev.loaded / elapsed;
                    this.trackUploadTimeLeft = speed > 0 ? (ev.total - ev.loaded) / speed : null;
                }
            };
            xhr.onload = () => {
                this.trackUploading = false;
                this.trackUploadTimeLeft = null;
                this.uploadWaitMessage = '';
                if (xhr.status === 200) { this.uploadError = ''; try { const r = JSON.parse(xhr.responseText); if (r.path) this.audioFilePath = r.path; } catch(e) {} }
                else if (xhr.status === 413) this.uploadError = 'Server upload limit too low. Restart with: php artisan serve (then try again). See UPLOAD-LIMIT.txt if it persists.';
            };
            xhr.onerror = () => { this.trackUploading = false; this.trackUploadTimeLeft = null; this.uploadWaitMessage = ''; };
            xhr.send(fd);
        },
        uploadAlbumTrack(fileInput, index) {
            const file = fileInput?.files?.[0];
            if (!file) return;
            this.uploadError = '';
            const form = document.getElementById('release-form');
            const url = form.getAttribute('data-upload-audio-url');
            if (!url) return;
            if (!this.albumTracks[index]) return;
            this.albumTracks[index].uploadProgress = 0;
            this.albumTracks[index].uploadTimeLeft = null;
            this.trackUploading = true;
            const startTime = Date.now();
            const fd = new FormData();
            fd.append('file', file);
            fd.append('type', 'album');
            fd.append('index', index);
            fd.append('_token', form.querySelector('input[name=_token]').value);
            const xhr = new XMLHttpRequest();
            xhr.open('POST', url);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.setRequestHeader('Accept', 'application/json');
            xhr.setRequestHeader('X-CSRF-TOKEN', form.querySelector('input[name=_token]').value);
            xhr.upload.onprogress = (ev) => {
                if (ev.lengthComputable && this.albumTracks[index]) {
                    this.albumTracks[index].uploadProgress = Math.round((ev.loaded/ev.total)*100);
                    const elapsed = (Date.now() - startTime) / 1000;
                    const speed = ev.loaded / elapsed;
                    this.albumTracks[index].uploadTimeLeft = speed > 0 ? (ev.total - ev.loaded) / speed : null;
                }
            };
            xhr.onload = () => {
                this.trackUploading = false;
                this.uploadWaitMessage = '';
                if (this.albumTracks[index]) { this.albumTracks[index].uploadProgress = 0; this.albumTracks[index].uploadTimeLeft = null; }
                if (xhr.status === 200) { this.uploadError = ''; try { const r = JSON.parse(xhr.responseText); if (r.path && this.albumTracks[index]) this.albumTracks[index].uploadedPath = r.path; } catch(e) {} }
                else if (xhr.status === 413) this.uploadError = 'Server upload limit too low. Restart with: php artisan serve (then try again). See UPLOAD-LIMIT.txt if it persists.';
            };
            xhr.onerror = () => { this.trackUploading = false; this.uploadWaitMessage = ''; if (this.albumTracks[index]) { this.albumTracks[index].uploadProgress = 0; this.albumTracks[index].uploadTimeLeft = null; } };
            xhr.send(fd);
        },
        uploadCover(fileInput) {
            const file = fileInput?.files?.[0];
            if (!file) return;
            this.uploadError = '';
            const form = document.getElementById('release-form');
            const url = form.getAttribute('data-upload-cover-url');
            if (!url) return;
            this.coverPreview = URL.createObjectURL(file);
            this.coverUploading = true;
            this.coverUploadProgress = 0;
            this.coverUploadTimeLeft = null;
            const startTime = Date.now();
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
                    const elapsed = (Date.now() - startTime) / 1000;
                    const speed = ev.loaded / elapsed;
                    this.coverUploadTimeLeft = speed > 0 ? (ev.total - ev.loaded) / speed : null;
                }
            };
            xhr.onload = () => {
                this.coverUploading = false;
                this.coverUploadTimeLeft = null;
                this.uploadWaitMessage = '';
                if (xhr.status === 200) { this.uploadError = ''; try { const r = JSON.parse(xhr.responseText); if (r.path) this.coverFilePath = r.path; } catch(e) {} }
                else if (xhr.status === 413) this.uploadError = 'Server upload limit too low. Restart with: php artisan serve (then try again). See UPLOAD-LIMIT.txt if it persists.';
            };
            xhr.onerror = () => { this.coverUploading = false; this.coverUploadTimeLeft = null; this.uploadWaitMessage = ''; };
            xhr.send(fd);
        },
        clearCover() {
            this.coverPreview = '';
            this.coverFilePath = '';
            const el = document.querySelector('input[name=cover_art]');
            if (el) el.value = '';
            this.invalidFields.cover_art = false;
        },
        clearSingleTrack() {
            this.audioFilePath = '';
            const el = this.$refs.audioFileInput;
            if (el) el.value = '';
            this.invalidFields.audio_file = false;
        },
        clearTrackUpload(i) {
            if (this.albumTracks[i]) { this.albumTracks[i].uploadedPath = ''; this.albumTracks[i].uploadProgress = 0; this.albumTracks[i].uploadTimeLeft = null; }
            const el = document.querySelector('input[name=\'album_tracks['+i+']\']');
            if (el) el.value = '';
            this.invalidFields.audio_file = false;
        },
        addTrack() { if (this.albumTracks.length < 20) this.albumTracks.push({ title: '', file: null, uploadedPath: '', uploadProgress: 0, uploadTimeLeft: null }); },
        removeTrack(i) { if (this.albumTracks.length > 2) this.albumTracks.splice(i, 1); },
    clearDraft() {
        localStorage.removeItem('releaseDraft');
        this.step = 1;
        this.releaseType = '';
        this.hasSpotifyApple = 'NO';
        this.albumTracks = [{ title: '', file: null, uploadedPath: '', uploadProgress: 0, uploadTimeLeft: null }, { title: '', file: null, uploadedPath: '', uploadProgress: 0, uploadTimeLeft: null }];
        this.coverFilePath = '';
        this.audioFilePath = '';
        this.coverPreview = '';
        this.draftRestored = false;
        this.clearStepErrors();
        const form = document.getElementById('release-form');
        if (form) { form.querySelectorAll('input:not([type=hidden]), select, textarea').forEach(el => { if (el.type === 'checkbox') el.checked = false; else if (el.type === 'radio') el.checked = false; else el.value = ''; }); }
    }
}" x-init="if (window.location.search.includes('new=1')) localStorage.removeItem('releaseDraft'); restoreDraft()" @beforeunload.window="saveDraft()">

    <!-- Draft restored notice -->
    <div x-show="draftRestored" x-cloak class="mb-4 p-3 rounded-xl bg-blue-900/30 border border-blue-500/30 text-blue-300 text-sm flex flex-wrap items-center justify-between gap-2">
        <span>Bozza ripristinata. Seleziona nuovamente la copertina e il file della traccia se ne hai scelti alcuni.</span>
        <div class="flex items-center gap-2">
            <button type="button" @click="clearDraft()" class="px-3 py-1.5 rounded-lg bg-red-500/30 hover:bg-red-500/50 text-red-200 text-sm font-medium border border-red-400/40">Elimina la bozza e ricomincia da capo</button>
            <button type="button" @click="draftRestored = false" class="text-blue-400 hover:text-white" title="Dismiss">×</button>
        </div>
    </div>

    <!-- Upload limit error (413 from pre-upload) -->
    <div x-show="uploadError" x-cloak class="mb-4 p-3 rounded-xl bg-amber-900/30 border border-amber-500/30 text-amber-200 text-sm flex items-center justify-between">
        <span x-text="uploadError"></span>
        <button type="button" @click="uploadError = ''" class="text-amber-400 hover:text-white">×</button>
    </div>

    <!-- Progress Steps: all in one line -->
    <div class="flex items-center justify-center overflow-x-auto pb-2 mb-8 flex-nowrap">
        <div class="flex items-center flex-shrink-0 gap-0">
            <template x-for="(label, index) in stepLabels" :key="index">
                <div class="flex items-center flex-shrink-0">
                    <div :class="step >= index + 1 ? 'bg-purple-600 text-white' : 'bg-white/10 text-gray-400'"
                         class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold transition"
                         x-text="index + 1"></div>
                    <span :class="step >= index + 1 ? 'text-white' : 'text-gray-500'" class="ml-1 text-xs whitespace-nowrap max-w-[4.5rem] sm:max-w-none truncate sm:truncate-none" x-text="label"></span>
                    <div class="w-1 sm:w-2 h-px bg-white/10 mx-0.5 sm:mx-1 flex-shrink-0" x-show="index < stepLabels.length - 1"></div>
                </div>
            </template>
        </div>
    </div>

    <form method="POST" action="{{ route('dashboard.releases.store') }}" enctype="multipart/form-data" id="release-form" data-upload-audio-url="{{ route('dashboard.releases.upload-audio') }}" data-upload-cover-url="{{ route('dashboard.releases.upload-cover') }}" @submit="onSubmit($event)">
        @csrf
        <input type="hidden" name="cover_art_path" :value="coverFilePath">
        <input type="hidden" name="audio_file_path" :value="audioFilePath">
        <template x-for="(track, i) in albumTracks" :key="'path-'+i">
            <input type="hidden" :name="'album_track_paths['+i+']'" :value="track.uploadedPath || ''">
        </template>
        <div class="form-errors hidden"></div>

        @if($errors->any())
        <div class="glass rounded-xl p-4 mb-6 border border-red-500/30 bg-red-900/10">
            @foreach($errors->all() as $error)
                <p class="text-red-400 text-sm">• {{ $error }}</p>
            @endforeach
        </div>
        @endif

        <!-- Step 1: Release Type -->
        <div x-show="step === 1" x-cloak>
            <div class="glass rounded-2xl p-8">
                <h2 class="text-xl font-bold text-white mb-6 text-center">Cosa stai caricando?</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 max-w-2xl mx-auto">
                    <label class="cursor-pointer">
                        <input type="radio" name="release_type" value="single" x-model="releaseType" class="sr-only">
                        <div :class="releaseType === 'single' ? 'border-purple-500 bg-purple-600/10' : 'border-white/10 hover:border-white/20'" class="glass rounded-2xl p-8 text-center border-2 transition cursor-pointer">
                            <div class="text-5xl mb-4">🎵</div>
                            <h3 class="text-white font-bold text-xl mb-2">Singolo</h3>
                            <p class="text-gray-400 text-sm">Uscita di una traccia con copertina e file audio</p>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="release_type" value="album" x-model="releaseType" class="sr-only">
                        <div :class="releaseType === 'album' ? 'border-purple-500 bg-purple-600/10' : 'border-white/10 hover:border-white/20'" class="glass rounded-2xl p-8 text-center border-2 transition cursor-pointer">
                            <div class="text-5xl mb-4">💿</div>
                            <h3 class="text-white font-bold text-xl mb-2">EP / Album</h3>
                            <p class="text-gray-400 text-sm">Tracce multiple con file audio individuali</p>
                        </div>
                    </label>
                </div>
                <div class="flex justify-end mt-8">
                    <button type="button" @click="nextStep()" :disabled="!releaseType" class="px-8 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 disabled:opacity-40 hover:from-purple-700 hover:to-indigo-700 text-white font-semibold rounded-xl transition">prossimo →</button>
                </div>
            </div>
        </div>

        <!-- Step 2: Main Info - Title only -->
        <div x-show="step === 2" x-cloak>
            <div class="glass rounded-2xl p-6">
                <h2 class="text-lg font-bold text-white mb-4">Informazioni principali</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div x-show="releaseType === 'album'">
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Titolo dell'album <span class="text-red-400">*</span></label>
                        <input type="text" name="album_title" x-ref="albumTitleInput" :class="invalidFields.album_title ? 'border-red-500' : 'border-white/10'" class="w-full bg-white/5 border text-white placeholder-gray-500 px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500" placeholder="Titolo dell'album" @input="invalidFields.album_title = false">
                    </div>
                    <div :class="releaseType === 'album' ? '' : 'md:col-span-2'">
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Titolo <span class="text-red-400">*</span></label>
                        <input type="text" name="title" x-ref="titleInput" required :class="invalidFields.title ? 'border-red-500' : 'border-white/10'" class="w-full bg-white/5 border text-white placeholder-gray-500 px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500" :placeholder="releaseType === 'album' ? 'Main track title' : 'Track title'" @input="invalidFields.title = false">
                    </div>
                </div>
                <div class="flex justify-between mt-8">
                    <button type="button" @click="prevStep()" class="px-6 py-2.5 bg-white/10 hover:bg-white/15 text-white font-medium rounded-xl transition text-sm">← Indietro</button>
                    <button type="button" @click="nextStep()" class="px-8 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-semibold rounded-xl transition">Prossimo →</button>
                </div>
            </div>
        </div>

        <!-- Step 3: Artist and Release Information -->
        <div x-show="step === 3" x-cloak>
            <div class="glass rounded-2xl p-6 space-y-6">
                <h2 class="text-lg font-bold text-white mb-4">Informazioni sull'artista e sulla pubblicazione</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Nome di battesimo</label>
                        <input type="text" name="first_name" class="w-full bg-white/5 border border-white/10 text-white placeholder-gray-500 px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500" placeholder="Nome di battesimo">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Cognome</label>
                        <input type="text" name="last_name" class="w-full bg-white/5 border border-white/10 text-white placeholder-gray-500 px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500" placeholder="Cognome">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Artist Name</label>
                        <input type="text" name="stage_name" class="w-full bg-white/5 border border-white/10 text-white placeholder-gray-500 px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500" placeholder="ArtistNome dell'artista name">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Artista principale <span class="text-red-400">*</span></label>
                        <input type="text" name="artists" x-ref="artistsInput" required :class="invalidFields.artists ? 'border-red-500' : 'border-white/10'" class="w-full bg-white/5 border text-white placeholder-gray-500 px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500" placeholder="Artista principale" @input="invalidFields.artists = false">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Artisti in evidenza (facoltativo)</label>
                        <input type="text" name="featuring_artists" class="w-full bg-white/5 border border-white/10 text-white placeholder-gray-500 px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500" placeholder="feat. Artist Name">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Data di rilascio <span class="text-red-400">*</span></label>
                        <input type="date" name="release_date" x-ref="releaseDateInput" required :class="invalidFields.release_date ? 'border-red-500' : 'border-white/10'" class="w-full bg-gray-800 border text-white px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500" @change="invalidFields.release_date = false">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Codice ISRC (facoltativo)</label>
                        <input type="text" name="isrc" class="w-full bg-white/5 border border-white/10 text-white placeholder-gray-500 px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500" placeholder="Lascia vuoto per generare automaticamente">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Autori – Nome e Cognome</label>
                        <input type="text" name="authors" class="w-full bg-white/5 border border-white/10 text-white placeholder-gray-500 px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500" placeholder="Autori di canzoni">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Compositori – Nome e Cognome</label>
                        <input type="text" name="composers" class="w-full bg-white/5 border border-white/10 text-white placeholder-gray-500 px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500" placeholder="Compositori di canzoni">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Prodotto da</label>
                        <input type="text" name="producer" class="w-full bg-white/5 border border-white/10 text-white placeholder-gray-500 px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500" placeholder="Nome del produttore">
                    </div>
                    <div class="md:col-span-2" x-data="{ youtubeBeat: false }">
                        <label class="flex items-center space-x-2 cursor-pointer mb-2">
                            <input type="checkbox" name="is_youtube_beat" value="1" x-model="youtubeBeat" class="w-4 h-4 rounded border-white/20 bg-white/5 text-purple-600">
                            <span class="text-sm text-gray-300">Batti da YouTube</span>
                        </label>
                        <div x-show="youtubeBeat" x-cloak class="mt-2">
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" name="has_license" value="1" class="w-4 h-4 rounded border-white/20 bg-white/5 text-purple-600">
                                <span class="text-sm text-gray-300">Ho la licenza</span>
                            </label>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Generi <span class="text-red-400">*</span></label>
                        <select name="primary_genre" x-ref="primaryGenreSelect" required :class="invalidFields.primary_genre ? 'border-red-500' : 'border-white/10'" class="w-full bg-gray-800 border text-white px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500" @change="invalidFields.primary_genre = false">
                            <option value="">Seleziona il genere principale</option>
                            @foreach(['Pop','Hip-Hop','R&B','Electronic','Rock','Alternative','Jazz','Classical','Country','Latin','Folk','Reggae'] as $g)
                                <option value="{{ $g }}">{{ $g }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Seleziona il genere principale</label>
                        <input type="text" name="secondary_genre" class="w-full bg-white/5 border border-white/10 text-white placeholder-gray-500 px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500" placeholder="Opzionale">
                    </div>
                    <div class="md:col-span-2">
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" name="is_explicit" value="1" class="w-4 h-4 rounded border-white/20 bg-white/5 text-purple-600">
                            <span class="text-sm text-gray-300">Contenuto esplicito</span>
                        </label>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Ora di inizio di TikTok (facoltativo)</label>
                        <input type="text" name="tik_tok_start_time" class="w-full bg-white/5 border border-white/10 text-white placeholder-gray-500 px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500" placeholder="e.g. 0:30">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Breve biografia (terza persona)</label>
                        <textarea name="short_bio" rows="3" class="w-full bg-white/5 border border-white/10 text-white placeholder-gray-500 px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500 resize-none" placeholder="Breve biografia in terza persona..."></textarea>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Descrizione della traccia</label>
                        <textarea name="track_description" rows="3" class="w-full bg-white/5 border border-white/10 text-white placeholder-gray-500 px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500 resize-none" placeholder="Descrizione della tua versione..."></textarea>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-300 mb-2">Società di gestione collettiva (facoltativo)</label>
                        <div class="space-y-2" x-data="{ society: 'NONE' }">
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="radio" name="cm_society" value="SIAE" x-model="society" class="w-4 h-4 border-white/20 bg-white/5 text-purple-600">
                                <span class="text-sm text-gray-300">SIAE</span>
                            </label>
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="radio" name="cm_society" value="SOUNDREEF" x-model="society" class="w-4 h-4 border-white/20 bg-white/5 text-purple-600">
                                <span class="text-sm text-gray-300">SOUNDREEF</span>
                            </label>
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="radio" name="cm_society" value="NONE" x-model="society" checked class="w-4 h-4 border-white/20 bg-white/5 text-purple-600">
                                <span class="text-sm text-gray-300">Non sono membro di nessuna società</span>
                            </label>
                            <div x-show="society === 'SIAE'" x-cloak class="mt-3">
                                <label class="block text-sm font-medium text-gray-300 mb-1.5">Numero di posizione SIAE (facoltativo)</label>
                                <input type="text" name="siae_position" class="w-full bg-white/5 border border-white/10 text-white placeholder-gray-500 px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500" placeholder="SIAE position number">
                            </div>
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Dettagli di distribuzione (facoltativo)</label>
                        <textarea name="distribution_details" rows="2" class="w-full bg-white/5 border border-white/10 text-white placeholder-gray-500 px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500 resize-none" placeholder="Note di distribuzione facoltative..."></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Durata del brano (facoltativa)</label>
                        <input type="text" name="song_duration" class="w-full bg-white/5 border border-white/10 text-white placeholder-gray-500 px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500" placeholder="00:00:00">
                    </div>
                </div>
                <div class="flex justify-between mt-8">
                    <button type="button" @click="prevStep()" class="px-6 py-2.5 bg-white/10 hover:bg-white/15 text-white font-medium rounded-xl transition text-sm">← Back</button>
                    <button type="button" @click="nextStep()" class="px-8 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-semibold rounded-xl transition">prossimo →</button>
                </div>
            </div>
        </div>

        <!-- Step 4: Streaming Platforms (YES/NO) -->
        <div x-show="step === 4" x-cloak>
            <div class="glass rounded-2xl p-6">
                <h2 class="text-lg font-bold text-white mb-4">Piattaforme di streaming</h2>
                <p class="text-sm text-gray-400 mb-6">Hai già profili Spotify e Apple Music?</p>
                <div class="space-y-4">
                    <label class="flex items-center space-x-3 cursor-pointer p-4 rounded-xl border-2 transition" :class="hasSpotifyApple === 'YES' ? 'border-purple-500 bg-purple-600/10' : 'border-white/10 hover:border-white/20'">
                        <input type="radio" name="has_spotify_apple" value="YES" x-model="hasSpotifyApple" class="w-4 h-4 border-white/20 bg-white/5 text-purple-600">
                        <span class="text-gray-300">SÌ – Ho entrambi i profili</span>
                    </label>
                    <label class="flex items-center space-x-3 cursor-pointer p-4 rounded-xl border-2 transition" :class="hasSpotifyApple === 'NO' ? 'border-purple-500 bg-purple-600/10' : 'border-white/10 hover:border-white/20'">
                        <input type="radio" name="has_spotify_apple" value="NO" x-model="hasSpotifyApple" class="w-4 h-4 border-white/20 bg-white/5 text-purple-600">
                        <span class="text-gray-300">NO – Devo crearli</span>
                    </label>
                </div>
                <p class="text-xs text-gray-500 mt-4" x-show="hasSpotifyApple === 'YES'">Il passaggio successivo ti chiederà i link dei tuoi profili Spotify e Apple Music.</p>
                <div class="flex justify-between mt-8">
                    <button type="button" @click="prevStep()" class="px-6 py-2.5 bg-white/10 hover:bg-white/15 text-white font-medium rounded-xl transition text-sm">← Back</button>
                    <button type="button" @click="nextStep()" class="px-8 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-700 text-white font-semibold rounded-xl transition">prossimo →</button>
                </div>
            </div>
        </div>

        <!-- Step 5 (if YES): Platform Links | (if NO): Social Media -->
        <div x-show="step === 5 && hasSpotifyApple === 'YES'" x-cloak>
            <div class="glass rounded-2xl p-6">
                <h2 class="text-lg font-bold text-white mb-4">Collegamenti alla piattaforma</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Collegamento al profilo Spotify <span class="text-red-400">*</span></label>
                        <input type="url" name="spotify_link" x-ref="spotifyLinkInput" :class="invalidFields.spotify_link ? 'border-red-500' : 'border-white/10'" class="w-full bg-white/5 border text-white placeholder-gray-500 px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500" placeholder="https://open.spotify.com/..." @input="invalidFields.spotify_link = false">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Collegamento al profilo Apple Music <span class="text-red-400">*</span></label>
                        <input type="url" name="apple_music_link" x-ref="appleMusicLinkInput" :class="invalidFields.apple_music_link ? 'border-red-500' : 'border-white/10'" class="w-full bg-white/5 border text-white placeholder-gray-500 px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500" placeholder="https://music.apple.com/..." @input="invalidFields.apple_music_link = false">
                    </div>
                </div>
                <div class="flex justify-between mt-8">
                    <button type="button" @click="prevStep()" class="px-6 py-2.5 bg-white/10 hover:bg-white/15 text-white font-medium rounded-xl transition text-sm">← Back</button>
                    <button type="button" @click="nextStep()" class="px-8 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-700 text-white font-semibold rounded-xl transition">Prossimo →</button>
                </div>
            </div>
        </div>

        <!-- Step 5 (if NO) or Step 6 (if YES): Social Media Links -->
        <div x-show="(step === 5 && hasSpotifyApple === 'NO') || (step === 6 && hasSpotifyApple === 'YES')" x-cloak>
            <div class="glass rounded-2xl p-6">
                <h2 class="text-lg font-bold text-white mb-4">Collegamenti ai social media</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Link al profilo TikTok (facoltativo)</label>
                        <input type="url" name="tik_tok_link" class="w-full bg-white/5 border border-white/10 text-white placeholder-gray-500 px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500" placeholder="https://tiktok.com/...">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">Link al profilo YouTube (facoltativo)</label>
                        <input type="url" name="youtube_link" class="w-full bg-white/5 border border-white/10 text-white placeholder-gray-500 px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500" placeholder="https://youtube.com/...">
                    </div>
                </div>
                <div class="flex justify-between mt-8">
                    <button type="button" @click="prevStep()" class="px-6 py-2.5 bg-white/10 hover:bg-white/15 text-white font-medium rounded-xl transition text-sm">← Back</button>
                    <button type="button" @click="nextStep()" class="px-8 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-700 text-white font-semibold rounded-xl transition">Prossimo →</button>
                </div>
            </div>
        </div>

        <!-- Step 6 (if NO) or Step 7 (if YES): Cover, Track, Lyrics -->
        <div x-show="(step === 6 && hasSpotifyApple === 'NO') || (step === 7 && hasSpotifyApple === 'YES')" x-cloak>
            <div class="glass rounded-2xl p-6 space-y-8">
                <h2 class="text-lg font-bold text-white">Copertina, traccia e testo</h2>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Copertina<span class="text-red-400">*</span></label>
                    <div x-data="{ dragging: false }" class="relative border-2 border-dashed rounded-xl p-6 transition cursor-pointer" :class="invalidFields.cover_art ? 'border-red-500 bg-red-900/10' : (dragging ? 'border-purple-500 bg-purple-600/5' : 'border-white/10 hover:border-white/20')">
                        <input type="file" name="cover_art" x-ref="coverArtInput" accept="image/*" :required="!coverFilePath" @change="uploadCover($event.target); invalidFields.cover_art = false" @dragover.prevent="dragging = true" @dragleave="dragging = false" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        <div class="text-center" x-show="!coverPreview && !coverFilePath && !coverUploading">
                            <svg class="w-10 h-10 text-gray-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <p class="text-gray-400 text-sm">Trascina l'immagine qui o clicca per sfogliare</p>
                            <p class="text-gray-600 text-xs mt-1">JPEG, PNG, max 5 MB. Quadrato consigliato (3000×3000px)</p>
                        </div>
                        <div x-show="coverPreview" x-cloak class="flex items-center space-x-4 relative">
                            <div class="relative">
                                <img :src="coverPreview" class="w-20 h-20 rounded-xl object-cover">
                                <button type="button" @click.prevent="clearCover()" class="absolute -top-1 -right-1 w-6 h-6 rounded-full bg-red-500 hover:bg-red-600 text-white flex items-center justify-center text-sm font-bold shadow" title="Remove cover">×</button>
                            </div>
                            <div>
                                <p class="text-white text-sm font-medium">Copertina selezionata</p>
                                <p class="text-gray-400 text-xs">Clicca per modificare o usa × per rimuovere</p>
                            </div>
                        </div>
                        <div x-show="coverFilePath && !coverPreview" x-cloak class="flex items-center gap-2">
                            <span class="text-green-400 text-sm">Copertina caricata</span>
                            <button type="button" @click="clearCover()" class="p-1 rounded text-red-400 hover:bg-red-500/20" title="Remove cover">×</button>
                        </div>
                        <div x-show="coverUploading" x-cloak class="mt-3 p-3 rounded-xl bg-white/5 border border-white/10">
                            <p class="text-gray-400 text-sm mb-2">Caricamento copertina...</p>
                            <div class="h-2 bg-white/10 rounded-full overflow-hidden">
                                <div class="h-full bg-purple-600 transition-all duration-300" :style="'width:'+coverUploadProgress+'%'"></div>
                            </div>
                            <p class="text-gray-500 text-xs mt-1"><span x-text="coverUploadProgress+'%'"></span><span x-show="coverUploadTimeLeft != null" x-cloak class="ml-2" x-text="formatTimeLeft(coverUploadTimeLeft)"></span></p>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-white font-medium mb-3">Traccia</h3>
                    <div x-show="releaseType === 'single'">
                        <label class="block text-sm font-medium text-gray-300 mb-1.5">File audio <span class="text-red-400">*</span></label>
                        <input type="file" name="audio_file" x-ref="audioFileInput" accept=".mp3,.wav,.flac,.aac,.ogg" :class="invalidFields.audio_file ? 'border-red-500' : 'border-white/10'" class="w-full bg-white/5 border text-white px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:bg-purple-600 file:text-white file:text-sm cursor-pointer" @change="invalidFields.audio_file = false; uploadSingleTrack($event.target)">
                        <p class="text-xs text-gray-500 mt-1">MP3, WAV, FLAC, AAC, OGG — max 2 GB. Seleziona un file da caricare immediatamente.</p>
                        <div x-show="audioFilePath" x-cloak class="mt-2 flex items-center gap-2">
                            <span class="text-sm text-green-400">Traccia caricata</span>
                            <button type="button" @click="clearSingleTrack()" class="p-1 rounded text-red-400 hover:bg-red-500/20 hover:text-red-300" title="Remove track">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                        <div x-show="trackUploading" x-cloak class="mt-3 p-3 rounded-xl bg-white/5 border border-white/10">
                            <p class="text-gray-400 text-sm mb-2">Caricamento traccia...</p>
                            <div class="h-2 bg-white/10 rounded-full overflow-hidden">
                                <div class="h-full bg-purple-600 transition-all duration-300" :style="'width:'+trackUploadProgress+'%'"></div>
                            </div>
                            <p class="text-gray-500 text-xs mt-1"><span x-text="trackUploadProgress+'%'"></span><span x-show="trackUploadTimeLeft != null" x-cloak class="ml-2" x-text="formatTimeLeft(trackUploadTimeLeft)"></span></p>
                        </div>
                    </div>
                    <div x-show="releaseType === 'album'" :class="invalidFields.audio_file ? 'rounded-xl ring-2 ring-red-500 p-2 -m-2' : ''">
                        <label class="block text-sm font-medium text-gray-300 mb-3">Tracce dell'album <span class="text-red-400">*</span> (minimo 2 tracce richieste)</label>
                        <div class="space-y-3">
                            <template x-for="(track, i) in albumTracks" :key="i">
                                <div class="glass rounded-xl p-4 flex flex-col gap-3">
                                    <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                                        <div class="flex-shrink-0 w-8 h-8 bg-purple-600/20 rounded-full flex items-center justify-center text-purple-400 text-sm font-bold" x-text="i + 1"></div>
                                        <input type="text" :name="'track_titles[' + i + ']'" placeholder="Track title" x-model="track.title" class="flex-1 bg-white/5 border border-white/10 text-white placeholder-gray-500 px-3 py-2 rounded-lg text-sm focus:outline-none focus:border-purple-500">
                                        <input type="file" :name="'album_tracks[' + i + ']'" accept=".mp3,.wav,.flac,.aac,.ogg" class="flex-1 bg-white/5 border border-white/10 text-white text-sm px-3 py-2 rounded-lg focus:outline-none file:bg-purple-600 file:text-white file:border-0 file:rounded file:px-2 file:py-0.5 cursor-pointer" @change="uploadAlbumTrack($event.target, i)">
                                        <span x-show="track.uploadedPath" x-cloak class="flex items-center gap-1 shrink-0">
                                            <span class="text-green-400 text-sm">Uploaded</span>
                                            <button type="button" @click="clearTrackUpload(i)" class="p-1 rounded text-red-400 hover:bg-red-500/20 hover:text-red-300" title="Remove this track file">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </span>
                                        <button type="button" @click="removeTrack(i)" x-show="albumTracks.length > 2" class="text-red-400 hover:text-red-300 p-1 shrink-0" title="Remove track row">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                    <div x-show="track.uploadProgress > 0" x-cloak class="mt-1 p-2 rounded-lg bg-white/5 border border-white/10">
                                        <p class="text-gray-400 text-xs mb-1.5">Caricamento traccia <span x-text="i + 1"></span>...</p>
                                        <div class="h-1.5 bg-white/10 rounded-full overflow-hidden">
                                            <div class="h-full bg-purple-600 transition-all duration-300" :style="'width:' + (track.uploadProgress || 0) + '%'"></div>
                                        </div>
                                        <p class="text-gray-500 text-xs mt-1"><span x-text="(track.uploadProgress || 0) + '%'"></span><span x-show="track.uploadTimeLeft != null" x-cloak class="ml-2" x-text="formatTimeLeft(track.uploadTimeLeft)"></span></p>
                                    </div>
                                </div>
                            </template>
                        </div>
                        <button type="button" @click="addTrack()" x-show="albumTracks.length < 20" class="mt-3 flex items-center space-x-2 text-purple-400 hover:text-purple-300 text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span>Caricamento traccia</span>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">Testi <span class="text-red-400">*</span> <span class="text-gray-500 font-normal">(richiesto per Apple Music)</span></label>
                    <textarea name="lyrics" x-ref="lyricsInput" rows="6" required :class="invalidFields.lyrics ? 'border-red-500' : 'border-white/10'" class="w-full bg-white/5 border text-white placeholder-gray-500 px-4 py-3 rounded-xl focus:outline-none focus:border-purple-500 resize-none" placeholder="Paste or type lyrics..." @input="invalidFields.lyrics = false"></textarea>
                </div>

                <div x-show="uploadWaitMessage" x-cloak class="upload-wait-message mb-4 p-3 rounded-xl bg-amber-900/30 border border-amber-500/30 text-amber-200 text-sm flex items-center justify-between">
                    <span x-text="uploadWaitMessage"></span>
                    <button type="button" @click="uploadWaitMessage = ''" class="text-amber-400 hover:text-white">×</button>
                </div>
                <div class="flex justify-between pt-4 border-t border-white/10">
                    <button type="button" @click="prevStep()" class="px-6 py-2.5 bg-white/10 hover:bg-white/15 text-white font-medium rounded-xl transition text-sm">← Indietro</button>
                    <button type="submit" :disabled="submitting || uploadsInProgress" class="px-8 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-semibold rounded-xl transition disabled:opacity-70 disabled:cursor-not-allowed flex items-center gap-2">
                        <span x-show="!submitting && !uploadsInProgress">Invia liberatoria</span>
                        <span x-show="uploadsInProgress && !submitting" x-cloak class="flex items-center gap-2">Attendi il completamento dei caricamenti...</span>
                        <span x-show="submitting" x-cloak class="flex items-center gap-2"><svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Invio...</span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
