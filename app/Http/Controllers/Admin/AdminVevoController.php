<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VevoAccount;
use Illuminate\Http\Request;

class AdminVevoController extends Controller
{
    public function index(Request $request)
    {
        $query = VevoAccount::with('user');
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('artist_name', 'like', "%{$s}%")
                    ->orWhere('contact_email', 'like', "%{$s}%")
                    ->orWhereHas('user', fn ($u) => $u->where('full_name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%"));
            });
        }
        $accounts = $query->orderByDesc('created_at')->paginate(20);
        return view('admin.vevo-accounts.index', compact('accounts'));
    }

    public function show(int $id)
    {
        $account = VevoAccount::with('user')->findOrFail($id);
        return view('admin.vevo-accounts.show', compact('account'));
    }

    public function edit(int $id)
    {
        $account = VevoAccount::findOrFail($id);
        return view('admin.vevo-accounts.edit', compact('account'));
    }

    public function update(Request $request, int $id)
    {
        $account = VevoAccount::findOrFail($id);
        $request->validate([
            'artist_name' => 'required|string|max:255',
            'contact_email' => 'required|email',
            'telephone' => 'nullable|string|max:50',
            'release_name' => 'nullable|string|max:255',
            'biography' => 'required|string|min:50',
            'status' => 'required|in:Pending,Approved,Rejected',
            'admin_notes' => 'nullable|string',
            'vevo_channel_url' => 'nullable|url',
        ]);

        $data = $request->only([
            'artist_name', 'contact_email', 'telephone', 'release_name', 'biography',
            'admin_notes', 'vevo_channel_url', 'status',
        ]);

        if ($request->status === 'Approved') {
            $data['approved_at'] = now();
            $data['approved_by'] = auth()->id();
            $data['rejected_at'] = null;
            $data['rejected_by'] = null;
        } elseif ($request->status === 'Rejected') {
            $data['rejected_at'] = now();
            $data['rejected_by'] = auth()->id();
            $data['approved_at'] = null;
            $data['approved_by'] = null;
        } else {
            $data['approved_at'] = null;
            $data['approved_by'] = null;
            $data['rejected_at'] = null;
            $data['rejected_by'] = null;
        }

        $account->update($data);

        return back()->with('success', 'Vevo account updated.');
    }

    public function updateStatus(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|in:Pending,Approved,Rejected',
            'admin_notes' => 'nullable|string',
            'vevo_channel_url' => 'nullable|url',
        ]);

        $account = VevoAccount::findOrFail($id);
        $data = [
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
            'vevo_channel_url' => $request->vevo_channel_url,
        ];

        if ($request->status === 'Approved') {
            $data['approved_at'] = now();
            $data['approved_by'] = auth()->id();
            $data['rejected_at'] = null;
            $data['rejected_by'] = null;
        } elseif ($request->status === 'Rejected') {
            $data['rejected_at'] = now();
            $data['rejected_by'] = auth()->id();
            $data['approved_at'] = null;
            $data['approved_by'] = null;
        } else {
            $data['approved_at'] = null;
            $data['approved_by'] = null;
            $data['rejected_at'] = null;
            $data['rejected_by'] = null;
        }

        $account->update($data);

        return back()->with('success', 'Vevo account status updated to ' . $request->status);
    }

    public function destroy(int $id)
    {
        VevoAccount::findOrFail($id)->delete();
        return back()->with('success', 'Vevo account deleted!');
    }

}
