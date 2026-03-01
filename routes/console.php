<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Mail;

Schedule::command('radio:expire-promotions')->daily();

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('mail:test {to=hostison247@gmail.com}', function (string $to) {
    $this->info('Sending test email to: ' . $to);
    try {
        Mail::raw('This is a test email from Beat Music. If you received this, SMTP is working.', function ($message) use ($to) {
            $message->to($to)->subject('Beat Music – Test Email');
        });
        $this->info('Email sent successfully. Check inbox (and spam) for: ' . $to);
    } catch (\Throwable $e) {
        $this->error('Failed: ' . $e->getMessage());
        $this->line('Host: ' . config('mail.mailers.smtp.host') . ' Port: ' . config('mail.mailers.smtp.port') . ' Encryption: ' . (config('mail.mailers.smtp.encryption') ?? 'none'));
    }
})->purpose('Send a test email to verify SMTP configuration');
