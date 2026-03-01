<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Payout;
use Illuminate\Http\Request;

class RevenueController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $payouts = Payout::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(10);

        $paidQuery = Payout::where('user_id', $user->id)
            ->where('status', 'paid');

        $totalEarned = (clone $paidQuery)->sum('amount');

        $lastPaidOut = (clone $paidQuery)
            ->orderByDesc('created_at')
            ->value('amount') ?? 0;

        $lockedPending = Payout::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'processing'])
            ->sum('amount');

        $rawBalance = (float) ($user->balance ?? 0);
        $available = max(0, $rawBalance - $lockedPending);

        $balance = [
            'available' => $available,
            'total_earned' => (float) $totalEarned,
            'last_paid_out' => (float) $lastPaidOut,
            'has_pending' => $lockedPending > 0,
        ];

        return view('dashboard.revenue.index', compact('user', 'payouts', 'balance'));
    }

    public function requestPayout(Request $request)
    {
        $user = auth()->user();

        if (!$user->paypal_email) {
            return back()->with('error', 'Please set your PayPal email in Account Settings before requesting a payout.');
        }

        $request->validate([
            'amount' => 'required|numeric|min:10|max:' . $user->balance,
        ]);

        if ($user->balance < $request->amount) {
            return back()->with('error', 'Insufficient balance for this payout.');
        }

        // Check for pending payout
        $pendingPayout = Payout::where('user_id', $user->id)
            ->where('status', 'pending')->first();

        if ($pendingPayout) {
            return back()->with('error', 'You already have a pending payout request.');
        }

        Payout::create([
            'user_id' => $user->id,
            'paypal_email' => $user->paypal_email,
            'amount' => $request->amount,
            'status' => 'pending',
            'request_date' => now(),
            'user_full_name' => $user->full_name,
            'user_email' => $user->email,
        ]);

        return back()->with('success', 'Payout request submitted! We will process it within 5 business days.');
    }
}
