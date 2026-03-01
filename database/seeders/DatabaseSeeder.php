<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\PricingPlan;
use App\Models\SiteSetting;
use App\Models\User;
use Database\Seeders\EditorialPlaylistSeeder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Primary Admin (user-specified credentials)
        User::updateOrCreate(
            ['email' => 'mdpiash4447@gmail.com'],
            [
                'full_name'   => 'MD Piash',
                'password'    => Hash::make('Piash@234'),
                'is_admin'    => true,
                'is_verified' => true,
                'status'      => 'active',
                'subscription' => 'Pro',
            ]
        );

        // Default Admin
        User::updateOrCreate(
            ['email' => 'admin@beatmusic.com'],
            [
                'full_name'   => 'Beat Music Admin',
                'password'    => Hash::make('Admin@12345'),
                'is_admin'    => true,
                'is_verified' => true,
                'status'      => 'active',
                'subscription' => 'Pro',
            ]
        );

        // Demo artist
        User::updateOrCreate(
            ['email' => 'artist@beatmusic.com'],
            [
                'full_name'   => 'Demo Artist',
                'password'    => Hash::make('Demo@12345'),
                'is_admin'    => false,
                'is_verified' => true,
                'status'      => 'active',
                'subscription' => 'Premium',
                'country'     => 'United States',
                'bio'         => 'Independent artist making waves in the music industry.',
            ]
        );

        // Pricing Plans
        if (PricingPlan::count() === 0) {
            $plans = [
                [
                    'name' => 'Free',
                    'description' => 'Perfect for getting started with music distribution.',
                    'features' => [
                        'Distribute up to 2 tracks/month',
                        'Access to 50+ platforms',
                        'Basic analytics',
                        'Keep 80% royalties',
                        'Email support',
                    ],
                    'price_monthly' => 0,
                    'price_yearly' => 0,
                    'is_active' => true,
                    'sort_order' => 1,
                ],
                [
                    'name' => 'Premium',
                    'description' => 'Great for growing artists ready to level up.',
                    'features' => [
                        'Distribute up to 10 tracks/month',
                        'All platforms (100+)',
                        'Advanced analytics',
                        'Keep 100% royalties',
                        'Editorial playlist submissions',
                        'Radio promotion access',
                        'Priority support',
                        'Pre-save campaigns',
                    ],
                    'price_monthly' => 9.99,
                    'price_yearly' => 99.99,
                    'is_active' => true,
                    'sort_order' => 2,
                ],
                [
                    'name' => 'Pro',
                    'description' => 'For professional artists and labels.',
                    'features' => [
                        'Unlimited tracks',
                        'All platforms (100+)',
                        'Full analytics suite',
                        'Keep 100% royalties',
                        'Unlimited playlist submissions',
                        'Radio promotion included',
                        'Vevo distribution',
                        'Concert live slots',
                        'Dedicated account manager',
                        '24/7 priority support',
                    ],
                    'price_monthly' => 19.99,
                    'price_yearly' => 199.99,
                    'is_active' => true,
                    'sort_order' => 3,
                ],
            ];

            foreach ($plans as $plan) {
                PricingPlan::create($plan);
            }
        }

        // FAQs
        if (Faq::count() === 0) {
            $faqs = [
                ['question' => 'How long does distribution take?', 'answer' => 'Your music will be live on most platforms within 1-3 business days after approval. Some platforms like Spotify may take up to 5 days.', 'category' => 'distribution'],
                ['question' => 'Do I own my music after uploading?', 'answer' => 'Yes! You retain 100% ownership of your music. We never take ownership of your content.', 'category' => 'rights'],
                ['question' => 'How do I get paid?', 'answer' => 'Royalties are collected from all platforms and deposited to your Beat Music balance monthly. You can request a payout to your PayPal account anytime (minimum $10).', 'category' => 'payments'],
                ['question' => 'What audio formats are accepted?', 'answer' => 'We accept MP3, WAV, FLAC, AAC, and OGG. We recommend uploading in WAV or FLAC for the best quality.', 'category' => 'uploads'],
                ['question' => 'Can I change my release after submitting?', 'answer' => 'Yes, you can edit your release details. Changes will go through a review process before being applied to streaming platforms.', 'category' => 'releases'],
            ];

            foreach ($faqs as $faq) {
                Faq::create(array_merge($faq, ['status' => 'active', 'sort_order' => 0]));
            }
        }

        $this->call(EditorialPlaylistSeeder::class);

        // Site Settings
        if (SiteSetting::count() === 0) {
            SiteSetting::create([
                'site_title' => 'Beat Music',
                'logo_alt' => 'Beat Music',
                'footer_text' => 'Empowering independent artists to distribute, promote, and monetize their music worldwide.',
                'copyright_text' => '© ' . date('Y') . ' Beat Music. All rights reserved.',
                'social_links' => [
                    'facebook' => 'https://facebook.com/beatmusic',
                    'twitter' => 'https://twitter.com/beatmusic',
                    'instagram' => 'https://instagram.com/beatmusic',
                    'youtube' => null,
                    'tiktok' => null,
                ],
            ]);
        }
    }
}
