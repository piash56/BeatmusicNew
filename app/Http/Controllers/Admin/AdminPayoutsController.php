<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payout;
use App\Models\User;
use Illuminate\Http\Request;

class AdminPayoutsController extends Controller
{
    public function index(Request $request)
    {
        $query = Payout::with('user');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $payouts = $query->orderByDesc('created_at')->paginate(20);
        return view('admin.payouts.index', compact('payouts'));
    }

    public function updateStatus(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|in:pending,rejected,paid',
        ]);

        $payout = Payout::with('user')->findOrFail($id);
        $oldStatus = $payout->status;
        $payout->update([
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
            'paid_date' => $request->status === 'paid' ? now() : $payout->paid_date,
        ]);

        // Deduct balance when marked as paid (only once)
        if ($request->status === 'paid' && $oldStatus !== 'paid') {
            $payout->user->decrement('balance', $payout->amount);
        }

        return back()->with('success', 'Payout status updated to ' . $request->status);
    }
}
