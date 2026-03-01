<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>Subscription Expiring</title></head>
<body style="font-family: Arial, sans-serif; background: #0a0a0a; color: #f1f1f1; margin: 0; padding: 20px;">
<div style="max-width: 480px; margin: 0 auto; background: #111827; border-radius: 16px; overflow: hidden;">
    <div style="background: linear-gradient(135deg, #b45309, #92400e); padding: 32px; text-align: center;">
        <h1 style="color: white; margin: 0; font-size: 24px;">⚠️ Subscription Expiring Soon</h1>
    </div>
    <div style="padding: 32px;">
        <p style="color: #d1d5db; margin: 0 0 16px;">Hi <strong style="color: white;">{{ $user->full_name }}</strong>,</p>
        <p style="color: #9ca3af; line-height: 1.6; margin: 0 0 24px;">Your <strong style="color: #a78bfa;">{{ $user->subscription }}</strong> subscription expires in <strong style="color: #fbbf24;">{{ $daysLeft }} days</strong>. Renew now to keep your music distributed and features active.</p>
        <div style="text-align: center; margin: 0 0 24px;">
            <a href="{{ url('/dashboard/billing') }}" style="display: inline-block; background: linear-gradient(135deg, #7c3aed, #4f46e5); color: white; text-decoration: none; padding: 14px 32px; border-radius: 10px; font-weight: bold;">Renew Subscription</a>
        </div>
    </div>
    <div style="background: #0f172a; padding: 20px; text-align: center;">
        <p style="color: #4b5563; font-size: 12px; margin: 0;">© {{ date('Y') }} Beat Music. All rights reserved.</p>
    </div>
</div>
</body>
</html>
