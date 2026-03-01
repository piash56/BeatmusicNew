<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\EmailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\Mailer\Exception\TransportException;

class AdminUsersController extends Controller
{
    public function __construct(
        protected EmailService $emailService
    ) {}

    public function index(Request $request)
    {
        $query = User::where('is_admin', false);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('artist_type')) {
            if ($request->artist_type === 'company') {
                $query->where('is_company', true);
            } else {
                $query->where('is_company', false);
            }
        }

        $users = $query->orderByDesc('created_at')->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'artist_type' => 'required|in:individual,company',
        ]);

        $token = Str::random(64);

        $user = User::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'password' => Hash::make(Str::random(32)),
            'is_verified' => false,
            'is_company' => $request->artist_type === 'company',
            'status' => 'active',
            'reset_password_token' => hash('sha256', $token),
            'reset_password_expiry' => now()->addHours(24),
        ]);

        try {
            $this->emailService->sendSetPassword($user, $token);
            $message = 'User created. A set-password email has been sent to the artist (link valid 24 hours).';
        } catch (TransportException $e) {
            Log::warning('Set-password email failed for user ' . $user->id . ': ' . $e->getMessage());
            $message = 'User created. The set-password email could not be sent (check mail configuration). You can resend it from this user\'s profile.';
        } catch (\Throwable $e) {
            Log::warning('Set-password email failed for user ' . $user->id . ': ' . $e->getMessage());
            $message = 'User created. The set-password email could not be sent. You can resend it from this user\'s profile.';
        }

        return redirect()->route('admin.users.show', $user->id)
            ->with('success', $message);
    }

    public function show(int $id)
    {
        $user = User::with(['tracks', 'tickets', 'payouts'])->findOrFail($id);
        return view('admin.users.show', compact('user'));
    }

    public function edit(int $id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, int $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'subscription' => 'nullable|in:Free,Premium,Pro',
            'status' => 'nullable|in:active,suspended',
            'country' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:1000',
            'social_facebook' => 'nullable|url',
            'social_twitter' => 'nullable|url',
            'social_instagram' => 'nullable|url',
            'social_website' => 'nullable|url',
            'paypal_email' => 'nullable|email|max:255',
            'billing_full_name' => 'nullable|string|max:255',
            'billing_email' => 'nullable|email|max:255',
            'billing_address' => 'nullable|string|max:255',
            'billing_city' => 'nullable|string|max:100',
            'billing_state' => 'nullable|string|max:100',
            'billing_zip_code' => 'nullable|string|max:20',
            'billing_country' => 'nullable|string|max:100',
        ]);

        $data = $request->only([
            'full_name',
            'email',
            'country',
            'phone',
            'address',
            'city',
            'state',
            'zip',
            'subscription',
            'status',
            'bio',
            'social_facebook',
            'social_twitter',
            'social_instagram',
            'social_website',
            'paypal_email',
            'billing_full_name',
            'billing_email',
            'billing_address',
            'billing_city',
            'billing_state',
            'billing_zip_code',
            'billing_country',
        ]);
        $data['is_company'] = $request->boolean('is_company');
        $data['can_upload_tracks'] = $request->boolean('can_upload_tracks');

        if ($request->has('paypal_email')) {
            $data['payout_method'] = $request->filled('paypal_email') ? 'paypal' : null;
        }

        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:8']);
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.show', $user->id)
            ->with('success', 'User updated successfully!');
    }

    public function toggleSuspension(int $id)
    {
        $user = User::findOrFail($id);
        $newStatus = $user->status === 'active' ? 'suspended' : 'active';
        $user->update(['status' => $newStatus]);

        return back()->with('success', 'User ' . $newStatus . ' successfully.');
    }

    public function resendSetPassword(int $id)
    {
        $user = User::findOrFail($id);

        if ($user->is_verified) {
            return back()->with('info', 'User has already set their password.');
        }

        $token = Str::random(64);
        $user->update([
            'reset_password_token' => hash('sha256', $token),
            'reset_password_expiry' => now()->addHours(24),
        ]);

        try {
            $this->emailService->sendSetPassword($user, $token);
            return back()->with('success', 'Set-password email sent (link valid 24 hours).');
        } catch (TransportException $e) {
            Log::warning('Resend set-password email failed for user ' . $user->id . ': ' . $e->getMessage());
            return back()->with('error', 'User updated with new link, but the email could not be sent. Check MAIL_* settings in .env.');
        } catch (\Throwable $e) {
            Log::warning('Resend set-password email failed for user ' . $user->id . ': ' . $e->getMessage());
            return back()->with('error', 'The email could not be sent. Check your mail configuration.');
        }
    }

    public function updateRoyalties(Request $request)
    {
        $query = User::where('is_admin', false);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('full_name')->paginate(20)->withQueryString();
        return view('admin.royalties.index', compact('users'));
    }

    public function addRoyalty(Request $request, int $userId)
    {
        $request->validate([
            'amount' => 'required|numeric',
        ]);

        $user = User::findOrFail($userId);
        $user->increment('balance', $request->amount);

        return back()->with('success', 'Royalty of $' . $request->amount . ' added to ' . $user->full_name);
    }
}
