@extends('layouts.app')

@section('title', 'Cookie Policy — Beat Music')

@section('content')
<section class="pt-32 pb-24 px-4">
    <div class="max-w-3xl mx-auto">
        <div class="mb-10">
            <h1 class="text-4xl font-bold text-white mb-2">Cookie Policy</h1>
            <p class="text-gray-400 text-sm">Last updated: January 1, 2024</p>
        </div>
        <div class="glass rounded-2xl p-8 border border-white/5 space-y-8 text-gray-300 text-sm leading-relaxed">
            <div>
                <h2 class="text-white font-semibold text-base mb-2">What Are Cookies?</h2>
                <p>Cookies are small text files stored on your device when you visit a website. They help websites remember information about your visit, making your next visit easier and the site more useful to you.</p>
            </div>
            <div>
                <h2 class="text-white font-semibold text-base mb-2">How We Use Cookies</h2>
                <p class="mb-3">Beat Music uses cookies for the following purposes:</p>
                <div class="space-y-3">
                    @foreach([
                        ['Essential Cookies','Required for the website to function properly. These cannot be disabled. They include session cookies for login and security cookies.'],
                        ['Analytics Cookies','Help us understand how visitors interact with our website. We use this data to improve our service. These cookies are anonymous.'],
                        ['Preference Cookies','Remember your settings and preferences, such as your preferred language or display settings.'],
                        ['Marketing Cookies','Used to deliver relevant advertisements and track the effectiveness of our marketing campaigns. You can opt out at any time.'],
                    ] as [$type, $desc])
                    <div class="bg-white/3 rounded-xl p-4 border border-white/5">
                        <p class="text-white text-sm font-medium mb-1">{{ $type }}</p>
                        <p class="text-gray-400 text-sm">{{ $desc }}</p>
                    </div>
                    @endforeach
                </div>
            </div>
            <div>
                <h2 class="text-white font-semibold text-base mb-2">Managing Cookies</h2>
                <p>You can control and manage cookies through your browser settings. Please note that disabling certain cookies may affect the functionality of our website. Most browsers allow you to view, delete, and block cookies from specific sites.</p>
            </div>
            <div>
                <h2 class="text-white font-semibold text-base mb-2">Third-Party Cookies</h2>
                <p>We may use third-party services such as Google Analytics, Stripe, and social media platforms that set their own cookies. We do not control these cookies and they are governed by the privacy policies of those services.</p>
            </div>
            <div>
                <h2 class="text-white font-semibold text-base mb-2">Contact</h2>
                <p>If you have questions about our cookie policy, contact us at privacy@beatmusic.com</p>
            </div>
        </div>
    </div>
</section>
@endsection
