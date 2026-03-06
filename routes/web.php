<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminConcertController;
use App\Http\Controllers\Admin\AdminContentController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminPayoutsController;
use App\Http\Controllers\Admin\AdminNewsletterController;
use App\Http\Controllers\Admin\AdminPlaylistsController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\AdminRadioController;
use App\Http\Controllers\Admin\AdminSupportController;
use App\Http\Controllers\Admin\AdminTracksController;
use App\Http\Controllers\Admin\AdminUsersController;
use App\Http\Controllers\Admin\AdminVevoController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Dashboard\ConcertLiveController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\PlaylistsController;
use App\Http\Controllers\Dashboard\ProfileController;
use App\Http\Controllers\Dashboard\RadioPromotionController;
use App\Http\Controllers\Dashboard\ReleasesController;
use App\Http\Controllers\Dashboard\RevenueController;
use App\Http\Controllers\Dashboard\StreamsController;
use App\Http\Controllers\Dashboard\SupportController;
use App\Http\Controllers\Dashboard\VevoController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PodcastController;
use App\Http\Controllers\PreSaveController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Website Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/features', [HomeController::class, 'features'])->name('features');
Route::get('/success-stories', [HomeController::class, 'successStories'])->name('success-stories');
Route::get('/about-us', [HomeController::class, 'aboutUs'])->name('about-us');
Route::get('/help-center', [HomeController::class, 'helpCenter'])->name('help-center');
Route::get('/knowledge-base', [HomeController::class, 'knowledgeBase'])->name('knowledge-base');
Route::get('/knowledge-base/articles/{id}', [HomeController::class, 'knowledgeBaseArticle'])->name('knowledge-base.article');
Route::get('/pricing', [HomeController::class, 'pricing'])->name('pricing');
Route::get('/checkout', [HomeController::class, 'checkout'])->name('checkout');
Route::get('/terms-of-service', [HomeController::class, 'terms'])->name('terms');
Route::get('/privacy-policy', [HomeController::class, 'privacy'])->name('privacy');
Route::get('/cookie-policy', [HomeController::class, 'cookiePolicy'])->name('cookie-policy');
Route::get('/press', [HomeController::class, 'press'])->name('press');
Route::get('/news', [HomeController::class, 'news'])->name('news');
Route::get('/careers', [HomeController::class, 'careers'])->name('careers');
Route::post('/api/newsletter/subscribe', [HomeController::class, 'subscribeNewsletter'])->name('newsletter.subscribe');
Route::post('/api/check-email', [HomeController::class, 'checkEmail'])->name('check-email');

/*
|--------------------------------------------------------------------------
| Auth Routes (User)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('forgot-password');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('forgot-password.post');
    Route::get('/reset-password', [AuthController::class, 'showResetPassword'])->name('reset-password');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('reset-password.post');
    Route::get('/set-password', [AuthController::class, 'showSetPassword'])->name('set-password');
    Route::post('/set-password', [AuthController::class, 'setPassword'])->name('set-password.post');
});

Route::get('/verify-otp/{userId}', [AuthController::class, 'showVerifyOtp'])->name('verify-otp');
Route::post('/verify-otp', [AuthController::class, 'verifyOtp'])->name('verify-otp.post');
Route::post('/resend-otp', [AuthController::class, 'resendOtp'])->name('resend-otp');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Pre-save Routes (Public)
|--------------------------------------------------------------------------
*/
Route::get('/presave/spotify/callback', [PreSaveController::class, 'spotifyCallback'])->name('presave.callback');
Route::get('/presave/success', [PreSaveController::class, 'success'])->name('presave.success');
Route::get('/presave/{trackId}', [PreSaveController::class, 'show'])->whereNumber('trackId')->name('presave.show');
Route::get('/presave/{trackId}/spotify', [PreSaveController::class, 'spotifyAuthRedirect'])->whereNumber('trackId')->name('presave.spotify');

/*
|--------------------------------------------------------------------------
| Podcast/Radio Public Pages
|--------------------------------------------------------------------------
*/
Route::get('/podcast/{id}', [PodcastController::class, 'show'])->name('podcast.show');
Route::post('/podcast/{id}/like', [PodcastController::class, 'like'])->name('podcast.like');

/*
|--------------------------------------------------------------------------
| File Serving
|--------------------------------------------------------------------------
*/
Route::get('/files/cover/{trackId}', [FileController::class, 'cover'])->name('files.cover');
Route::get('/files/audio/{trackId}', [FileController::class, 'audio'])->name('files.audio');
Route::get('/files/album/{trackId}/track/{trackIndex}', [FileController::class, 'albumTrack'])->name('files.album-track');
Route::middleware(['auth'])->group(function () {
    Route::get('/files/cover/{trackId}/download', [FileController::class, 'downloadCover'])->name('files.cover.download');
});

/*
|--------------------------------------------------------------------------
| Dashboard Routes (Authenticated Artist)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified_user'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('home');
    Route::get('/not-eligible', fn() => view('dashboard.not-eligible'))->name('not-eligible');

    // Releases
    Route::get('/releases', [ReleasesController::class, 'index'])->name('releases.index');
    Route::get('/releases/create', [ReleasesController::class, 'create'])->name('releases.create');
    Route::post('/releases/upload-audio', [ReleasesController::class, 'uploadAudio'])->name('releases.upload-audio');
    Route::post('/releases/upload-cover', [ReleasesController::class, 'uploadCover'])->name('releases.upload-cover');
    Route::post('/releases', [ReleasesController::class, 'store'])->name('releases.store');
    Route::get('/releases/{id}', [ReleasesController::class, 'show'])->name('releases.show');
    Route::get('/releases/{id}/edit', [ReleasesController::class, 'edit'])->name('releases.edit');
    Route::put('/releases/{id}', [ReleasesController::class, 'update'])->name('releases.update');

    // Playlists
    Route::get('/playlists', [PlaylistsController::class, 'index'])->name('playlists');
    Route::post('/playlists/submit', [PlaylistsController::class, 'submit'])->name('playlists.submit');

    // Radio Promotion
    Route::get('/radio-promotion', [RadioPromotionController::class, 'index'])->name('radio-promotion');
    Route::get('/radio-promotion/album/{id}/tracks', [RadioPromotionController::class, 'albumTracks'])->name('radio-promotion.album-tracks');
    Route::post('/radio-promotion/submit', [RadioPromotionController::class, 'submit'])->name('radio-promotion.submit');

    // Concert Live
    Route::get('/concert-live', [ConcertLiveController::class, 'index'])->name('concert-live');
    Route::post('/concert-live/request', [ConcertLiveController::class, 'request'])->name('concert-live.request');

    // Vevo
    Route::get('/vevo', [VevoController::class, 'index'])->name('vevo');
    Route::post('/vevo/submit', [VevoController::class, 'submit'])->name('vevo.submit');

    // Analytics & Revenue
    Route::get('/streams', [StreamsController::class, 'index'])->name('streams');
    Route::get('/revenue', [RevenueController::class, 'index'])->name('revenue');
    Route::post('/revenue/payout', [RevenueController::class, 'requestPayout'])->name('revenue.payout');

    // Profile & Settings
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/picture', [ProfileController::class, 'updatePicture'])->name('profile.picture');
    Route::get('/settings', [ProfileController::class, 'settings'])->name('settings');
    Route::put('/settings/password', [ProfileController::class, 'updatePassword'])->name('settings.password');
    Route::put('/settings/payout', [ProfileController::class, 'updatePayout'])->name('settings.payout');
    Route::get('/billing', [ProfileController::class, 'billing'])->name('billing');

    // Support
    Route::get('/support', [SupportController::class, 'index'])->name('support');
    Route::get('/support/create', [SupportController::class, 'create'])->name('support.create');
    Route::post('/support', [SupportController::class, 'store'])->name('support.store');
    Route::get('/support/{id}', [SupportController::class, 'show'])->name('support.show');
    Route::post('/support/{id}/reply', [SupportController::class, 'reply'])->name('support.reply');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    // Admin Auth (guests only)
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login'])->name('login.post');
        Route::get('/forgot-password', [AdminAuthController::class, 'showForgotPassword'])->name('forgot-password');
        Route::post('/forgot-password', [AdminAuthController::class, 'sendResetLink'])->name('forgot-password.post');
        Route::get('/reset-password', [AdminAuthController::class, 'showResetPassword'])->name('reset-password');
        Route::post('/reset-password', [AdminAuthController::class, 'resetPassword'])->name('reset-password.post');
    });

    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    // Admin Panel (admin auth required)
    Route::middleware(['auth', 'is_admin'])->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Track Submissions
        Route::get('/track-submissions', [AdminTracksController::class, 'trackSubmissions'])->name('track-submissions');
        Route::get('/track-submissions/{id}/view', [AdminTracksController::class, 'viewTrack'])->name('track-submissions.view');
        Route::put('/track-submissions/{id}/status', [AdminTracksController::class, 'updateStatus'])->name('track-submissions.status');
        Route::put('/track-submissions/{id}/upc', [AdminTracksController::class, 'updateUpc'])->name('track-submissions.upc');
        Route::put('/track-submissions/{id}', [AdminTracksController::class, 'updateTrack'])->name('track-submissions.update');

        // Album Submissions
        Route::get('/album-submissions', [AdminTracksController::class, 'albumSubmissions'])->name('album-submissions');
        Route::get('/album-submissions/{id}/view', [AdminTracksController::class, 'viewAlbum'])->name('album-submissions.view');

        // Editorial Playlists
        Route::get('/editorial-playlists', [AdminPlaylistsController::class, 'index'])->name('editorial-playlists');
        Route::patch('/editorial-playlists/{id}/status', [AdminPlaylistsController::class, 'updateStatus'])->name('editorial-playlists.status');
        Route::patch('/editorial-playlists/{id}/streams', [AdminPlaylistsController::class, 'updateStreams'])->name('editorial-playlists.streams');
        Route::get('/editorial-playlists/catalog', [AdminPlaylistsController::class, 'catalog'])->name('editorial-playlists.catalog');
        Route::post('/editorial-playlists/catalog', [AdminPlaylistsController::class, 'storePlaylist'])->name('editorial-playlists.catalog.store');
        Route::get('/editorial-playlists/catalog/{id}/edit', [AdminPlaylistsController::class, 'editPlaylist'])->name('editorial-playlists.catalog.edit');
        Route::put('/editorial-playlists/catalog/{id}', [AdminPlaylistsController::class, 'updatePlaylist'])->name('editorial-playlists.catalog.update');
        Route::delete('/editorial-playlists/catalog/{id}', [AdminPlaylistsController::class, 'destroyPlaylist'])->name('editorial-playlists.catalog.destroy');

        // Newsletter Subscribers
        Route::get('/newsletter-subscribers', [AdminNewsletterController::class, 'index'])->name('newsletter-subscribers');

        // Vevo Accounts
        Route::get('/vevo-accounts', [AdminVevoController::class, 'index'])->name('vevo-accounts');
        Route::get('/vevo-accounts/{id}', [AdminVevoController::class, 'show'])->name('vevo-accounts.show');
        Route::get('/vevo-accounts/{id}/edit', [AdminVevoController::class, 'edit'])->name('vevo-accounts.edit');
        Route::put('/vevo-accounts/{id}', [AdminVevoController::class, 'update'])->name('vevo-accounts.update');
        Route::patch('/vevo-accounts/{id}/status', [AdminVevoController::class, 'updateStatus'])->name('vevo-accounts.status');
        Route::delete('/vevo-accounts/{id}', [AdminVevoController::class, 'destroy'])->name('vevo-accounts.destroy');

        // Radio
        Route::get('/radio-networks', [AdminRadioController::class, 'networks'])->name('radio-networks');
        Route::post('/radio-networks', [AdminRadioController::class, 'storeNetwork'])->name('radio-networks.store');
        Route::put('/radio-networks/{id}', [AdminRadioController::class, 'updateNetwork'])->name('radio-networks.update');
        Route::delete('/radio-networks/{id}', [AdminRadioController::class, 'destroyNetwork'])->name('radio-networks.destroy');
        Route::get('/radio-requests', [AdminRadioController::class, 'requests'])->name('radio-requests');
        Route::patch('/radio-requests/{id}/status', [AdminRadioController::class, 'updateRequestStatus'])->name('radio-requests.status');
        Route::post('/radio-requests/expire', [AdminRadioController::class, 'updateExpired'])->name('radio-requests.expire');

        // Concert Lives
        Route::get('/concert-lives', [AdminConcertController::class, 'index'])->name('concert-lives');
        Route::post('/concert-lives', [AdminConcertController::class, 'store'])->name('concert-lives.store');
        Route::put('/concert-lives/{id}', [AdminConcertController::class, 'update'])->name('concert-lives.update');
        Route::delete('/concert-lives/{id}', [AdminConcertController::class, 'destroy'])->name('concert-lives.destroy');
        Route::get('/live-requests', [AdminConcertController::class, 'liveRequests'])->name('live-requests');
        Route::patch('/live-requests/{id}', [AdminConcertController::class, 'updateRequest'])->name('live-requests.update');

        // Streams Management
        Route::get('/streams/update-streams', [AdminTracksController::class, 'streamsManagement'])->name('streams');
        Route::put('/streams/{trackId}', [AdminTracksController::class, 'updateStreams'])->name('streams.update');
        Route::post('/streams/import', [AdminTracksController::class, 'importStreams'])->name('streams.import');

        // Users
        Route::get('/users', [AdminUsersController::class, 'index'])->name('users');
        Route::get('/users/new', [AdminUsersController::class, 'create'])->name('users.create');
        Route::post('/users', [AdminUsersController::class, 'store'])->name('users.store');
        Route::get('/users/{id}', [AdminUsersController::class, 'show'])->name('users.show');
        Route::get('/users/{id}/edit', [AdminUsersController::class, 'edit'])->name('users.edit');
        Route::put('/users/{id}', [AdminUsersController::class, 'update'])->name('users.update');
        Route::post('/users/{id}/toggle-suspension', [AdminUsersController::class, 'toggleSuspension'])->name('users.toggle-suspension');
        Route::post('/users/{id}/resend-set-password', [AdminUsersController::class, 'resendSetPassword'])->name('users.resend-set-password');

        // Payouts
        Route::get('/payout-requests', [AdminPayoutsController::class, 'index'])->name('payout-requests');
        Route::patch('/payout-requests/{id}/status', [AdminPayoutsController::class, 'updateStatus'])->name('payout-requests.status');

        // Royalties
        Route::get('/update-royalties', [AdminUsersController::class, 'updateRoyalties'])->name('update-royalties');
        Route::post('/update-royalties/{userId}', [AdminUsersController::class, 'addRoyalty'])->name('update-royalties.add');

        // FAQs
        Route::get('/faqs', [AdminContentController::class, 'faqs'])->name('faqs');
        Route::post('/faqs', [AdminContentController::class, 'storeFaq'])->name('faqs.store');
        Route::put('/faqs/{id}', [AdminContentController::class, 'updateFaq'])->name('faqs.update');
        Route::delete('/faqs/{id}', [AdminContentController::class, 'destroyFaq'])->name('faqs.destroy');

        // Knowledge Base
        Route::get('/knowledge-base', [AdminContentController::class, 'knowledgeBase'])->name('knowledge-base');
        Route::get('/knowledge-base/create', [AdminContentController::class, 'createArticle'])->name('knowledge-base.create');
        Route::post('/knowledge-base', [AdminContentController::class, 'storeArticle'])->name('knowledge-base.store');
        Route::get('/knowledge-base/edit/{id}', [AdminContentController::class, 'editArticle'])->name('knowledge-base.edit');
        Route::put('/knowledge-base/{id}', [AdminContentController::class, 'updateArticle'])->name('knowledge-base.update');
        Route::delete('/knowledge-base/{id}', [AdminContentController::class, 'destroyArticle'])->name('knowledge-base.destroy');

        // Testimonials
        Route::get('/testimonials', [AdminContentController::class, 'testimonials'])->name('testimonials');
        Route::post('/testimonials', [AdminContentController::class, 'storeTestimonial'])->name('testimonials.store');
        Route::put('/testimonials/{id}', [AdminContentController::class, 'updateTestimonial'])->name('testimonials.update');
        Route::delete('/testimonials/{id}', [AdminContentController::class, 'destroyTestimonial'])->name('testimonials.destroy');

        // Support
        Route::get('/support', [AdminSupportController::class, 'index'])->name('support');
        Route::get('/support/{id}', [AdminSupportController::class, 'show'])->name('support.show');
        Route::post('/support/{id}/reply', [AdminSupportController::class, 'reply'])->name('support.reply');
        Route::patch('/support/{id}/status', [AdminSupportController::class, 'updateStatus'])->name('support.status');
        Route::delete('/support/{id}', [AdminSupportController::class, 'destroy'])->name('support.destroy');

        // Profile
        Route::get('/profile', [AdminProfileController::class, 'index'])->name('profile');
        Route::put('/profile', [AdminProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [AdminProfileController::class, 'updatePassword'])->name('profile.password');

        // Site Settings
        Route::get('/site-settings', [AdminContentController::class, 'siteSettings'])->name('site-settings');
        Route::put('/site-settings', [AdminContentController::class, 'updateSiteSettings'])->name('site-settings.update');
    });
});
