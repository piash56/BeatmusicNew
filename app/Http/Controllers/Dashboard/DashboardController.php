<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\PlaylistSubmission;
use App\Models\Track;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $recentReleases = Track::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        $releasedStatuses = ['Released', 'Modify Released'];
        $pendingStatuses = ['On Request', 'On Process', 'Modify Pending', 'Modify Process'];

        $stats = [
            'total_streams' => (int) ($user->stats_total_streams ?? 0),
            'total_tracks' => $user->tracks()->count(),
            'released_tracks' => Track::where('user_id', $user->id)->whereIn('status', $releasedStatuses)->count(),
            'pending_review' => Track::where('user_id', $user->id)->whereIn('status', $pendingStatuses)->count(),
            'playlist_published' => PlaylistSubmission::where('user_id', $user->id)->where('status', 'Published')->count(),
        ];

        $subscriptionDaysLeft = null;
        if ($user->subscription_end_date) {
            $subscriptionDaysLeft = max(0, now()->diffInDays($user->subscription_end_date, false));
        }

        return view('dashboard.index', compact('user', 'recentReleases', 'stats', 'subscriptionDaysLeft'));
    }
}
