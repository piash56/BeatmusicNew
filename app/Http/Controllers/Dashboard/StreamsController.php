<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Track;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StreamsController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $releasedStatuses = ['Released', 'Modify Released'];

        $baseQuery = Track::where('user_id', $user->id)
            ->whereIn('status', $releasedStatuses);

        if ($request->filled('search')) {
            $search = $request->search;
            $baseQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('album_title', 'like', "%{$search}%")
                    ->orWhere('main_track_title', 'like', "%{$search}%")
                    ->orWhere('artists', 'like', "%{$search}%");
            });
        }

        $summaryQuery = (clone $baseQuery);

        $summary = [
            'total_streams' => (int) (clone $summaryQuery)->sum('total_streams'),
            'released_count' => (int) (clone $summaryQuery)->count(),
            'total_presaves' => (int) (clone $summaryQuery)->sum('pre_save_count'),
        ];

        $tracks = $baseQuery
            ->orderByDesc('total_streams')
            ->paginate(20)
            ->withQueryString();

        return view('dashboard.streams.index', compact('user', 'tracks', 'summary'));
    }
}
