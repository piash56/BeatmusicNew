<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RadioNetwork;
use App\Models\RadioPromotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminRadioController extends Controller
{
    public function networks(Request $request)
    {
        $networks = RadioNetwork::withCount('promotions')->latest()->paginate(20);
        return view('admin.radio.networks', compact('networks'));
    }

    public function storeNetwork(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'cover_image' => 'nullable|image|mimes:jpeg,jpg,png|max:5120',
        ]);

        $data = $request->only(['name']);
        $data['is_active'] = $request->boolean('is_active', true);
        $data['created_by'] = auth()->id();

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('radio-networks', 'public');
        }

        RadioNetwork::create($data);
        return back()->with('success', 'Radio network created!');
    }

    public function updateNetwork(Request $request, int $id)
    {
        $network = RadioNetwork::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'cover_image' => 'nullable|image|mimes:jpeg,jpg,png|max:5120',
        ]);

        $data = $request->only(['name']);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('cover_image')) {
            if ($network->cover_image) Storage::disk('public')->delete($network->cover_image);
            $data['cover_image'] = $request->file('cover_image')->store('radio-networks', 'public');
        }

        $network->update($data);
        return back()->with('success', 'Radio network updated!');
    }

    public function destroyNetwork(int $id)
    {
        $network = RadioNetwork::findOrFail($id);
        if ($network->cover_image) Storage::disk('public')->delete($network->cover_image);
        $network->delete();
        return back()->with('success', 'Radio network deleted!');
    }

    public function requests(Request $request)
    {
        $query = RadioPromotion::with(['user', 'track', 'radioNetwork']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('user', fn ($u) => $u->where('full_name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%"));
        }

        $promotions = $query->orderByDesc('created_at')->paginate(20);
        return view('admin.radio.requests', compact('promotions'));
    }

    public function updateRequestStatus(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|in:pending,published,rejected,finished',
        ]);

        $promotion = RadioPromotion::findOrFail($id);
        $data = [
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
            'updated_by' => auth()->id(),
        ];

        if ($request->status === 'published' && $promotion->status !== 'published') {
            $data['published_date'] = now();
            $data['finish_date'] = now()->addDays(28);
        }

        $promotion->update($data);
        return back()->with('success', 'Radio promotion status updated!');
    }

    public function updateExpired()
    {
        $count = RadioPromotion::where('status', 'published')
            ->where('finish_date', '<=', now())
            ->update(['status' => 'finished']);

        return back()->with('success', "{$count} promotion(s) marked as finished.");
    }
}
