<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $fillable = [
        'code', 'discount_type', 'discount_amount', 'max_uses',
        'used_count', 'expiration_date', 'is_active',
        'specific_user', 'subscription_plan', 'created_by',
    ];

    protected $casts = [
        'discount_amount' => 'decimal:2',
        'is_active' => 'boolean',
        'expiration_date' => 'datetime',
    ];

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function specificUser()
    {
        return $this->belongsTo(User::class, 'specific_user');
    }

    public function isValid(): bool
    {
        if (!$this->is_active) return false;
        if ($this->expiration_date && $this->expiration_date->isPast()) return false;
        if ($this->max_uses && $this->used_count >= $this->max_uses) return false;
        return true;
    }
}
