<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;

class EmailService
{
    public function sendOtp(User $user, string $otp): void
    {
        Mail::send('emails.otp', ['user' => $user, 'otp' => $otp], function (Message $message) use ($user) {
            $message->to($user->email, $user->full_name)
                ->subject('Your Beat Music Verification Code');
        });
    }

    public function sendPasswordReset(User $user, string $token, bool $isAdmin = false): void
    {
        $resetUrl = $isAdmin
            ? route('admin.reset-password', ['token' => $token, 'email' => $user->email])
            : route('reset-password', ['token' => $token, 'email' => $user->email]);

        Mail::send('emails.password-reset', ['user' => $user, 'resetUrl' => $resetUrl], function (Message $message) use ($user) {
            $message->to($user->email, $user->full_name)
                ->subject('Reset Your Beat Music Password');
        });
    }

    public function sendSubscriptionConfirmation(User $user): void
    {
        Mail::send('emails.subscription-confirmed', ['user' => $user], function (Message $message) use ($user) {
            $message->to($user->email, $user->full_name)
                ->subject('Subscription Confirmed - Beat Music');
        });
    }

    public function sendSubscriptionExpiring(User $user, int $daysLeft): void
    {
        Mail::send('emails.subscription-expiring', ['user' => $user, 'daysLeft' => $daysLeft], function (Message $message) use ($user) {
            $message->to($user->email, $user->full_name)
                ->subject('Your Beat Music Subscription is Expiring Soon');
        });
    }

    public function sendSetPassword(User $user, string $token): void
    {
        $setPasswordUrl = route('set-password', ['token' => $token, 'email' => $user->email]);

        Mail::send('emails.set-password', ['user' => $user, 'setPasswordUrl' => $setPasswordUrl], function (Message $message) use ($user) {
            $message->to($user->email, $user->full_name)
                ->subject('Set Your Beat Music Password');
        });
    }
}
