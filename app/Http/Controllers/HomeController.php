<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\KnowledgeBase;
use App\Models\NewsletterSubscriber;
use App\Models\PricingPlan;
use App\Models\SiteSetting;
use App\Models\Testimonial;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $settings = SiteSetting::getSettings();
        // Prefer testimonials explicitly marked for home; fall back to any active ones
        $testimonials = Testimonial::where('status', 'active')
            ->whereJsonContains('display_on', 'home')
            ->latest()
            ->take(6)
            ->get();

        if ($testimonials->isEmpty()) {
            $testimonials = Testimonial::where('status', 'active')
                ->latest()
                ->take(6)
                ->get();
        }
        return view('website.home', compact('settings', 'testimonials'));
    }

    public function features()
    {
        $settings = SiteSetting::getSettings();
        return view('website.features', compact('settings'));
    }

    public function successStories()
    {
        $settings = SiteSetting::getSettings();
        $testimonials = Testimonial::where('status', 'active')->latest()->paginate(12);
        return view('website.success-stories', compact('settings', 'testimonials'));
    }

    public function aboutUs()
    {
        $settings = SiteSetting::getSettings();
        $testimonials = Testimonial::where('status', 'active')
            ->whereJsonContains('display_on', 'about')
            ->take(4)
            ->get();
        return view('website.about-us', compact('settings', 'testimonials'));
    }

    public function helpCenter()
    {
        $settings = SiteSetting::getSettings();
        $faqs = Faq::where('status', 'active')->orderBy('sort_order')->get();
        return view('website.help-center', compact('settings', 'faqs'));
    }

    public function knowledgeBase(Request $request)
    {
        $settings = SiteSetting::getSettings();
        $query = KnowledgeBase::where('status', 'active');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        $articles = $query->latest()->paginate(12);
        $categories = KnowledgeBase::where('status', 'active')
            ->distinct()->pluck('category')->filter()->values();

        return view('website.knowledge-base.index', compact('settings', 'articles', 'categories'));
    }

    public function knowledgeBaseArticle(int $id)
    {
        $settings = SiteSetting::getSettings();
        $article = KnowledgeBase::where('status', 'active')->findOrFail($id);
        $article->increment('views');
        $relatedArticles = KnowledgeBase::where('status', 'active')
            ->where('id', '!=', $id)
            ->where('category', $article->category)
            ->take(4)
            ->get();
        return view('website.knowledge-base.show', compact('settings', 'article', 'relatedArticles'));
    }

    public function pricing()
    {
        $settings = SiteSetting::getSettings();
        $plans = PricingPlan::where('is_active', true)->orderBy('sort_order')->get();
        return view('website.pricing', compact('settings', 'plans'));
    }

    public function checkout(Request $request)
    {
        $settings = SiteSetting::getSettings();
        $plans = PricingPlan::where('is_active', true)->orderBy('sort_order')->get();
        $plan = $request->filled('plan') ? PricingPlan::find($request->plan) : $plans->first();
        $billingCycle = $request->get('cycle', 'monthly');
        return view('website.checkout', compact('settings', 'plans', 'plan', 'billingCycle'));
    }

    public function terms()
    {
        $settings = SiteSetting::getSettings();
        return view('website.terms', compact('settings'));
    }

    public function privacy()
    {
        $settings = SiteSetting::getSettings();
        return view('website.privacy', compact('settings'));
    }

    public function cookiePolicy()
    {
        $settings = SiteSetting::getSettings();
        return view('website.cookie-policy', compact('settings'));
    }

    public function press()
    {
        $settings = SiteSetting::getSettings();
        return view('website.press', compact('settings'));
    }

    public function news()
    {
        $settings = SiteSetting::getSettings();
        return view('website.news', compact('settings'));
    }

    public function careers()
    {
        $settings = SiteSetting::getSettings();
        return view('website.careers', compact('settings'));
    }

    public function subscribeNewsletter(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        NewsletterSubscriber::firstOrCreate(['email' => $request->email]);
        return response()->json(['message' => 'Successfully subscribed to our newsletter!']);
    }

    public function checkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $exists = \App\Models\User::where('email', $request->email)->exists();
        return response()->json(['available' => !$exists]);
    }
}
