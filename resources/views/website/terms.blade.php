@extends('layouts.app')

@section('title', 'Terms of Service — Beat Music')

@section('content')
<section class="pt-32 pb-24 px-4">
    <div class="max-w-3xl mx-auto">
        <div class="mb-10">
            <h1 class="text-4xl font-bold text-white mb-2">Terms of Service</h1>
            <p class="text-gray-400 text-sm">Last updated: January 1, 2024</p>
        </div>
        <div class="glass rounded-2xl p-8 border border-white/5 space-y-8 text-gray-300 text-sm leading-relaxed">
            @foreach([
                ['1. Acceptance of Terms','By accessing and using Beat Music, you agree to be bound by these Terms of Service. If you do not agree to these terms, please do not use our service.'],
                ['2. Description of Service','Beat Music is a music distribution platform that allows artists to upload and distribute their music to digital streaming platforms worldwide. We provide tools for music promotion, analytics, and revenue management.'],
                ['3. User Accounts','You must create an account to use our services. You are responsible for maintaining the security of your account credentials and for all activities that occur under your account. You must be at least 18 years old to create an account.'],
                ['4. Content Ownership','You retain all ownership rights to your music and content uploaded to Beat Music. By uploading content, you grant Beat Music a non-exclusive license to distribute and promote your content on your behalf to the platforms you select.'],
                ['5. Acceptable Use','You agree not to upload content that infringes on copyrights, trademarks, or other intellectual property rights of others. You may not upload explicit content without proper labeling, malicious code, or content that violates any applicable laws.'],
                ['6. Revenue & Royalties','Beat Music distributes royalties collected from streaming platforms to artists. Royalty rates and payment schedules are defined in your subscription plan. Payments are processed within 30 days of collection.'],
                ['7. Subscriptions & Payments','Subscription fees are charged according to your selected plan. You can cancel your subscription at any time. Refunds are provided on a case-by-case basis at our discretion.'],
                ['8. Termination','Beat Music reserves the right to terminate or suspend accounts that violate these terms. Upon termination, your music will remain distributed for any remaining subscription period.'],
                ['9. Disclaimer of Warranties','Beat Music is provided "as is" without warranties of any kind. We do not guarantee specific streaming numbers, revenue amounts, or placement on any platform.'],
                ['10. Changes to Terms','We may update these terms at any time. Continued use of the service after changes constitutes acceptance of the new terms. We will notify users of significant changes via email.'],
                ['11. Contact','If you have questions about these terms, please contact us at legal@beatmusic.com'],
            ] as [$title, $content])
            <div>
                <h2 class="text-white font-semibold text-base mb-2">{{ $title }}</h2>
                <p>{{ $content }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
