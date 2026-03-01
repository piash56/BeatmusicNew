<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>Verify Your Email</title></head>
<body style="font-family: Arial, sans-serif; background: #0a0a0a; color: #f1f1f1; margin: 0; padding: 20px;">
<div style="max-width: 480px; margin: 0 auto; background: #111827; border-radius: 16px; overflow: hidden;">
    <div style="background: linear-gradient(135deg, #7c3aed, #4f46e5); padding: 32px; text-align: center;">
        <h1 style="color: white; margin: 0; font-size: 24px;">Beat Music</h1>
        <p style="color: rgba(255,255,255,0.8); margin-top: 8px;">Email Verification</p>
    </div>
    <div style="padding: 32px;">
        <p style="color: #d1d5db; margin: 0 0 16px;">Hi <strong style="color: white;">{{ $user->full_name }}</strong>,</p>
        <p style="color: #9ca3af; line-height: 1.6; margin: 0 0 24px;">Enter this verification code to activate your Beat Music account:</p>
        <div style="background: #1f2937; border: 1px solid #374151; border-radius: 12px; padding: 24px; text-align: center; margin: 0 0 24px;">
            <div style="font-size: 40px; font-weight: bold; letter-spacing: 12px; color: #a78bfa;">{{ $otp }}</div>
        </div>
        <p style="color: #6b7280; font-size: 13px; margin: 0 0 16px;">⏱ This code expires in <strong style="color: #9ca3af;">5 minutes</strong>.</p>
        <p style="color: #6b7280; font-size: 13px; margin: 0;">If you didn't request this, please ignore this email.</p>
    </div>
    <div style="background: #0f172a; padding: 20px; text-align: center;">
        <p style="color: #4b5563; font-size: 12px; margin: 0;">© {{ date('Y') }} Beat Music. All rights reserved.</p>
    </div>
</div>
</body>
</html>
