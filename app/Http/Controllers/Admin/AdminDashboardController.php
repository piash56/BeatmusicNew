<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConcertLiveRequest;
use App\Models\Payout;
use App\Models\PlaylistSubmission;
use App\Models\RadioPromotion;
use App\Models\Ticket;
use App\Models\Track;
use App\Models\User;
use App\Models\VevoAccount;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $releasedStatuses = ['Released', 'Modify Released'];
        $stats = [
            // Row 1: Pending items (singles & albums: On Request, Modify Pending = require review; On Process, Modify Process = processing)
            'tracks_require_review' => Track::whereIn('status', ['On Request', 'Modify Pending'])->count(),
            'tracks_processing' => Track::whereIn('status', ['On Process', 'Modify Process'])->count(),
            'vevo_approved' => VevoAccount::where('status', 'Approved')->count(),
            'vevo_pending' => VevoAccount::where('status', 'Pending')->count(),
            'pending_concerts' => ConcertLiveRequest::where('status', 'pending')->count(),
            'pending_playlists' => PlaylistSubmission::whereIn('status', ['Waiting', 'Processing'])->count(),
            'radio_published' => RadioPromotion::where('status', 'published')->count(),
            'pending_radio' => RadioPromotion::where('status', 'pending')->count(),
            // Row 2: Totals & approved
            'released_singles' => Track::where('release_type', 'single')->whereIn('status', $releasedStatuses)->count(),
            'released_albums' => Track::where('release_type', 'album')->whereIn('status', $releasedStatuses)->count(),
            'approved_playlists' => PlaylistSubmission::where('status', 'Published')->count(),
            'approved_concerts' => ConcertLiveRequest::where('status', 'confirmed')->count(),
            // Row 3: Users & support
            'total_users' => User::where('is_admin', false)->count(),
            'admin_users' => User::where('is_admin', true)->count(),
            'artist_accounts' => User::where('is_admin', false)->where('is_company', false)->count(),
            'company_accounts' => User::where('is_admin', false)->where('is_company', true)->count(),
            'tickets_in_progress' => Ticket::where('status', 'in_progress')->count(),
            'tickets_active' => Ticket::whereIn('status', ['pending', 'open', 'in_progress'])->count(),
            'pending_payouts' => Payout::where('status', 'pending')->count(),
            'open_tickets' => Ticket::whereIn('status', ['pending', 'open'])->count(),
        ];

        $recentUsers = User::where('is_admin', false)->latest()->take(5)->get();
        $recentTracks = Track::with('user')->latest()->take(5)->get();
        $pendingPayouts = Payout::where('status', 'pending')->with('user')->latest()->take(5)->get();
        $openTickets = Ticket::whereIn('status', ['pending', 'open'])->with('user')->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentTracks', 'pendingPayouts', 'openTickets'));
    }
}
