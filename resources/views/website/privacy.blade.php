@extends('layouts.app')

@section('title', 'Privacy Policy — Beat Music')

@section('content')
<section class="pt-32 pb-24 px-4">
    <div class="max-w-3xl mx-auto">
        <div class="mb-10">
            <h1 class="text-4xl font-bold text-white mb-2">Privacy Policy</h1>
            <p class="text-gray-400 text-sm">Last updated: January 1, 2024</p>
        </div>
        <div class="glass rounded-2xl p-8 border border-white/5 space-y-8 text-gray-300 text-sm leading-relaxed">
            @foreach([
                ['1. Information We Collect','We collect information you provide directly, such as your name, email address, payment information, and uploaded content. We also collect usage data, device information, and cookies to improve our service.'],
                ['2. How We Use Your Information','We use your information to provide and improve our services, process payments, send important notifications, personalize your experience, and comply with legal obligations. We do not sell your personal data to third parties.'],
                ['3. Data Sharing','We share your information with trusted third-party services necessary to operate our platform, including payment processors (Stripe, PayPal), cloud storage providers, and digital streaming platforms for distribution purposes.'],
                ['4. Data Security','We implement industry-standard security measures including SSL encryption, secure data storage, and regular security audits to protect your personal information.'],
                ['5. Cookies','We use cookies and similar tracking technologies to enhance your experience. You can control cookie settings through your browser preferences. Disabling cookies may affect some features.'],
                ['6. Your Rights','You have the right to access, correct, or delete your personal data. You can request a copy of your data, opt out of marketing communications, and request data portability. Contact us to exercise these rights.'],
                ['7. Data Retention','We retain your data for as long as your account is active and as required by law. You can request deletion of your account and associated data at any time.'],
                ['8. Children\'s Privacy','Our service is not directed to children under 13. We do not knowingly collect personal information from children under 13.'],
                ['9. International Transfers','Your data may be transferred to and processed in countries other than your own. We ensure appropriate safeguards are in place for such transfers.'],
                ['10. Changes to This Policy','We may update this privacy policy periodically. We will notify you of significant changes via email or a prominent notice on our website.'],
                ['11. Contact Us','For privacy-related questions or requests, contact our Data Protection Officer at privacy@beatmusic.com'],
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
