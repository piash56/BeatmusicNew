<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConcertLive;
use App\Models\ConcertLiveRequest;
use Illuminate\Http\Request;

class AdminConcertController extends Controller
{
    public function index()
    {
        $concerts = ConcertLive::withCount('requests')->orderBy('concert_date', 'desc')->paginate(20);
        return view('admin.concert-lives.index', compact('concerts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'city' => 'required|string|max:100',
            'concert_date' => 'required|date|after:today',
            'slots_available' => 'required|integer|min:1|max:1000',
        ]);

        ConcertLive::create([
            'name' => $request->name,
            'city' => $request->city,
            'concert_date' => $request->concert_date,
            'slots_available' => $request->slots_available,
            'is_active' => $request->boolean('is_active', true),
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', 'Concert created!');
    }

    public function update(Request $request, int $id)
    {
        $concert = ConcertLive::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:200',
            'city' => 'required|string|max:100',
            'concert_date' => 'required|date',
            'slots_available' => 'required|integer|min:1|max:1000',
        ]);
        if ($request->slots_available < $concert->slots_booked) {
            return back()->with('error', 'Slots available cannot be less than slots already booked.');
        }

        $concert->update([
            'name' => $request->name,
            'city' => $request->city,
            'concert_date' => $request->concert_date,
            'slots_available' => $request->slots_available,
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Concert updated!');
    }

    public function destroy(int $id)
    {
        $concert = ConcertLive::findOrFail($id);
        if ($concert->slots_booked > 0) {
            return back()->with('error', 'Cannot delete a concert that has booked slots.');
        }
        $concert->delete();
        return back()->with('success', 'Concert deleted!');
    }

    public function liveRequests(Request $request)
    {
        ConcertLiveRequest::where('status', 'confirmed')
            ->whereHas('concertLive', fn ($q) => $q->where('concert_date', '<', now()->startOfDay()))
            ->update(['status' => 'finished']);

        $query = ConcertLiveRequest::with(['user', 'concertLive']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('artist_name', 'like', "%{$s}%")
                    ->orWhereHas('concertLive', fn ($c) => $c->where('name', 'like', "%{$s}%")->orWhere('city', 'like', "%{$s}%"))
                    ->orWhereHas('user', fn ($u) => $u->where('full_name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%"));
            });
        }

        $requests = $query->orderByDesc('created_at')->paginate(20);
        return view('admin.concert-lives.live-requests', compact('requests'));
    }

    public function updateRequest(Request $request, int $id)
    {
        $request->validate(['status' => 'required|in:pending,confirmed,cancelled,finished']);

        $req = ConcertLiveRequest::with('concertLive')->findOrFail($id);
        $oldStatus = $req->status;
        $req->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
            'updated_by' => auth()->id(),
        ]);

        $concert = $req->concertLive;
        if ($request->status === 'confirmed' && $oldStatus !== 'confirmed') {
            if ($concert->slots_booked >= $concert->slots_available) {
                return back()->with('error', 'No slots left for this concert.');
            }
            $concert->increment('slots_booked');
            if ($concert->fresh()->slots_booked >= $concert->slots_available) {
                $concert->update(['is_active' => false]);
            }
        } elseif ($oldStatus === 'confirmed' && in_array($request->status, ['cancelled', 'finished', 'pending'])) {
            $concert->decrement('slots_booked');
            if ($concert->fresh()->slots_booked < $concert->slots_available) {
                $concert->update(['is_active' => true]);
            }
        }

        return back()->with('success', 'Request status updated!');
    }
}
