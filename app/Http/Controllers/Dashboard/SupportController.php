<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class SupportController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $tickets = Ticket::where('user_id', $user->id)
            ->withCount('replies')
            ->orderByDesc('created_at')
            ->paginate(10);
        return view('dashboard.support.index', compact('user', 'tickets'));
    }

    public function create()
    {
        return view('dashboard.support.create');
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'subject' => 'required|string|max:255',
            'category' => 'required|in:distribution,account,payment,technical,royalties,radio,vevo,other',
            'priority' => 'required|in:low,medium,high',
            'message' => 'required|string|min:10',
            'attachment' => 'nullable|file|max:5120',
            'attachments.*' => 'nullable|file|max:10240',
        ]);

        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('tickets', 'public');
                $attachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'type' => $file->getClientMimeType(),
                ];
            }
        } elseif ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store('tickets', 'public');
            $attachments[] = [
                'name' => $file->getClientOriginalName(),
                'path' => $path,
                'type' => $file->getClientMimeType(),
            ];
        }

        $category = match ($request->category) {
            'distribution', 'technical', 'radio', 'vevo' => 'technical',
            'payment', 'royalties' => 'billing',
            'account' => 'account',
            default => 'other',
        };

        $ticket = Ticket::create([
            'user_id' => $user->id,
            'subject' => $request->subject,
            'category' => $category,
            'priority' => $request->priority,
            'status' => 'open',
            'message' => $request->message,
            'attachments' => $attachments ?: null,
        ]);

        return redirect()->route('dashboard.support.show', $ticket->id)
            ->with('success', 'Support ticket created successfully!');
    }

    public function show(int $id)
    {
        $user = Auth::user();
        $ticket = Ticket::where('user_id', $user->id)->with('replies.user')->findOrFail($id);
        return view('dashboard.support.show', compact('user', 'ticket'));
    }

    public function reply(Request $request, int $id)
    {
        $user = Auth::user();
        $ticket = Ticket::where('user_id', $user->id)->findOrFail($id);

        if (in_array($ticket->status, ['closed', 'resolved'], true)) {
            return back()->with('error', 'This ticket is closed. Please open a new ticket.');
        }

        $request->validate([
            'message' => 'required|string|min:2',
            'attachment' => 'nullable|file|max:5120',
            'attachments.*' => 'nullable|file|max:10240',
        ]);

        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('tickets', 'public');
                $attachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'type' => $file->getClientMimeType(),
                ];
            }
        } elseif ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store('tickets', 'public');
            $attachments[] = [
                'name' => $file->getClientOriginalName(),
                'path' => $path,
                'type' => $file->getClientMimeType(),
            ];
        }

        TicketReply::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'message' => $request->message,
            'attachments' => $attachments ?: null,
            'is_admin_reply' => false,
        ]);

        if ($ticket->status === 'pending') {
            $ticket->update(['status' => 'open']);
        }

        return back()->with('success', 'Reply sent!');
    }
}
