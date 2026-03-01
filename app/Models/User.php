<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'full_name',
        'email',
        'password',
        'is_admin',
        'is_company',
        'status',
        'subscription',
        'subscription_id',
        'subscription_status',
        'subscription_start_date',
        'subscription_end_date',
        'payment_method',
        'payout_method',
        'paypal_email',
        'balance',
        'can_upload_tracks',
        'country',
        'phone',
        'address',
        'city',
        'state',
        'zip',
        'is_verified',
        'otp',
        'otp_expires',
        'otp_resend_count',
        'last_otp_resend_time',
        'otp_resend_reset_time',
        'reset_password_token',
        'reset_password_expiry',
        'profile_picture',
        'preferences_theme',
        'preferences_favorite_genres',
        'preferences_language',
        'bio',
        'social_facebook',
        'social_twitter',
        'social_instagram',
        'social_website',
        'billing_full_name',
        'billing_email',
        'billing_address',
        'billing_city',
        'billing_state',
        'billing_zip_code',
        'billing_country',
        'billing_updated_at',
        'stats_total_streams',
        'stats_last_24h',
        'stats_last_week',
        'stats_last_month',
        'stats_last_year',
        'stats_stream_updates',
        'stats_on_request_tracks',
        'stats_on_process_tracks',
        'stats_released_tracks',
        'stats_rejected_tracks',
        'stats_playlist_count',
        'stats_track_count',
        'last_active',
        'last_login_time',
        'last_login_ip',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'otp',
        'reset_password_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'is_company' => 'boolean',
            'can_upload_tracks' => 'boolean',
            'is_verified' => 'boolean',
            'preferences_favorite_genres' => 'array',
            'stats_stream_updates' => 'array',
            'otp_expires' => 'datetime',
            'last_otp_resend_time' => 'datetime',
            'otp_resend_reset_time' => 'datetime',
            'subscription_start_date' => 'datetime',
            'subscription_end_date' => 'datetime',
            'billing_updated_at' => 'datetime',
            'last_active' => 'datetime',
            'last_login_time' => 'datetime',
            'reset_password_expiry' => 'datetime',
            'balance' => 'decimal:2',
        ];
    }

    public function tracks()
    {
        return $this->hasMany(Track::class);
    }

    public function subscriptionModel()
    {
        return $this->belongsTo(Subscription::class, 'subscription_id');
    }

    public function paymentMethods()
    {
        return $this->hasMany(PaymentMethod::class);
    }

    public function payouts()
    {
        return $this->hasMany(Payout::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function preSaves()
    {
        return $this->hasMany(PreSave::class);
    }

    public function playlistSubmissions()
    {
        return $this->hasMany(PlaylistSubmission::class);
    }

    public function radioPromotions()
    {
        return $this->hasMany(RadioPromotion::class);
    }

    public function concertLiveRequests()
    {
        return $this->hasMany(ConcertLiveRequest::class);
    }

    public function vevoRequests()
    {
        return $this->hasMany(VevoRequest::class);
    }

    public function vevoAccounts()
    {
        return $this->hasMany(VevoAccount::class);
    }

    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }

    public function isVerified(): bool
    {
        return (bool) $this->is_verified;
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function hasActivePlan(string $plan): bool
    {
        return $this->subscription === $plan;
    }

    public function getProfilePictureUrlAttribute(): string
    {
        if ($this->profile_picture) {
            return asset('storage/' . $this->profile_picture);
        }
        return asset('images/default-avatar.png');
    }

    /**
     * Accessor so `auth()->user()->name` works across Blade views.
     */
    public function getNameAttribute(): string
    {
        return $this->full_name ?? '';
    }
}
