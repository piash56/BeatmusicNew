<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\KnowledgeBase;
use App\Models\SiteSetting;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminContentController extends Controller
{
    // =====================
    // FAQs
    // =====================
    public function faqs()
    {
        $faqs = Faq::orderBy('sort_order')->paginate(20);
        return view('admin.faqs.index', compact('faqs'));
    }

    public function storeFaq(Request $request)
    {
        $request->validate([
            'question' => 'required|string',
            'answer' => 'required|string',
            'category' => 'nullable|string|max:100',
            'status' => 'required|in:active,inactive',
        ]);

        Faq::create($request->only(['question', 'answer', 'category', 'status', 'sort_order']));
        return back()->with('success', 'FAQ created!');
    }

    public function updateFaq(Request $request, int $id)
    {
        $request->validate([
            'question' => 'required|string',
            'answer' => 'required|string',
            'status' => 'required|in:active,inactive',
        ]);

        Faq::findOrFail($id)->update($request->only(['question', 'answer', 'category', 'status', 'sort_order']));
        return back()->with('success', 'FAQ updated!');
    }

    public function destroyFaq(int $id)
    {
        Faq::findOrFail($id)->delete();
        return back()->with('success', 'FAQ deleted!');
    }

    // =====================
    // Testimonials
    // =====================
    public function testimonials()
    {
        $testimonials = Testimonial::latest()->paginate(20);
        return view('admin.testimonials.index', compact('testimonials'));
    }

    public function storeTestimonial(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'title' => 'nullable|string|max:255',
            'feedback' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'status' => 'required|in:active,inactive',
            'display_on' => 'nullable|array',
            'profile_picture' => 'nullable|image|mimes:jpeg,jpg,png|max:5120',
        ]);

        $data = $request->only(['customer_name', 'title', 'feedback', 'rating', 'status']);
        $data['display_on'] = $request->input('display_on', []);

        if ($request->hasFile('profile_picture')) {
            $data['profile_picture'] = $request->file('profile_picture')->store('testimonials', 'public');
        }

        Testimonial::create($data);
        return back()->with('success', 'Testimonial created!');
    }

    public function updateTestimonial(Request $request, int $id)
    {
        $testimonial = Testimonial::findOrFail($id);
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'feedback' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'status' => 'required|in:active,inactive',
        ]);

        $data = $request->only(['customer_name', 'title', 'feedback', 'rating', 'status']);
        $data['display_on'] = $request->input('display_on', []);

        if ($request->hasFile('profile_picture')) {
            if ($testimonial->profile_picture) Storage::disk('public')->delete($testimonial->profile_picture);
            $data['profile_picture'] = $request->file('profile_picture')->store('testimonials', 'public');
        }

        $testimonial->update($data);
        return back()->with('success', 'Testimonial updated!');
    }

    public function destroyTestimonial(int $id)
    {
        $testimonial = Testimonial::findOrFail($id);
        if ($testimonial->profile_picture) Storage::disk('public')->delete($testimonial->profile_picture);
        $testimonial->delete();
        return back()->with('success', 'Testimonial deleted!');
    }

    // =====================
    // Knowledge Base
    // =====================
    public function knowledgeBase(Request $request)
    {
        $query = KnowledgeBase::latest();
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        $articles = $query->paginate(20);
        return view('admin.knowledge-base.index', compact('articles'));
    }

    public function createArticle()
    {
        return view('admin.knowledge-base.create');
    }

    public function storeArticle(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'status' => 'required|in:active,inactive',
            'tags' => 'nullable|string',
            'featured' => 'nullable|boolean',
        ]);

        $tags = $request->filled('tags')
            ? array_map('trim', explode(',', $request->tags))
            : [];

        KnowledgeBase::create([
            'title' => $request->title,
            'content' => $request->content,
            'excerpt' => $request->excerpt,
            'category' => $request->category,
            'status' => $request->status,
            'tags' => $tags,
            'featured' => $request->boolean('featured'),
            'last_updated' => now(),
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.knowledge-base')->with('success', 'Article created!');
    }

    public function editArticle(int $id)
    {
        $article = KnowledgeBase::findOrFail($id);
        return view('admin.knowledge-base.edit', compact('article'));
    }

    public function updateArticle(Request $request, int $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'status' => 'required|in:active,inactive',
        ]);

        $tags = $request->filled('tags')
            ? array_map('trim', explode(',', $request->tags))
            : [];

        KnowledgeBase::findOrFail($id)->update([
            'title' => $request->title,
            'content' => $request->content,
            'excerpt' => $request->excerpt,
            'category' => $request->category,
            'status' => $request->status,
            'tags' => $tags,
            'featured' => $request->boolean('featured'),
            'last_updated' => now(),
        ]);

        return redirect()->route('admin.knowledge-base')->with('success', 'Article updated!');
    }

    public function destroyArticle(int $id)
    {
        KnowledgeBase::findOrFail($id)->delete();
        return back()->with('success', 'Article deleted!');
    }

    // =====================
    // Site Settings
    // =====================
    public function siteSettings()
    {
        $settings = SiteSetting::first() ?? new SiteSetting();
        return view('admin.site-settings.index', compact('settings'));
    }

    public function updateSiteSettings(Request $request)
    {
        $request->validate([
            'site_title' => 'required|string|max:255',
            'logo_alt' => 'nullable|string|max:255',
            'footer_text' => 'nullable|string',
            'copyright_text' => 'nullable|string',
        ]);

        $settings = SiteSetting::first();
        $data = $request->only([
            'site_title', 'logo_alt', 'footer_text', 'copyright_text',
        ]);

        // Social links
        $data['social_links'] = [
            'facebook' => $request->input('social_facebook'),
            'twitter' => $request->input('social_twitter'),
            'instagram' => $request->input('social_instagram'),
            'youtube' => $request->input('social_youtube'),
            'tiktok' => $request->input('social_tiktok'),
        ];

        if ($request->boolean('remove_logo') && $settings && $settings->logo_url) {
            Storage::disk('public')->delete($settings->logo_url);
            $data['logo_url'] = null;
        } elseif ($request->hasFile('logo')) {
            if ($settings && $settings->logo_url) Storage::disk('public')->delete($settings->logo_url);
            $data['logo_url'] = $request->file('logo')->store('brand', 'public');
        }

        if ($request->boolean('remove_favicon') && $settings && $settings->favicon) {
            Storage::disk('public')->delete($settings->favicon);
            $data['favicon'] = null;
        } elseif ($request->hasFile('favicon')) {
            if ($settings && $settings->favicon) Storage::disk('public')->delete($settings->favicon);
            $data['favicon'] = $request->file('favicon')->store('brand', 'public');
        }

        $data['last_updated_by'] = auth()->id();

        if ($settings) {
            $settings->update($data);
        } else {
            SiteSetting::create($data);
        }

        SiteSetting::clearCache();

        return back()->with('success', 'Site settings updated!');
    }
}
