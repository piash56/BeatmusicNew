<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class AdminSupportController extends Controller
{
    public function index(Request $request)
    {
        $query = Ticket::with('user')->withCount('replies');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        $tickets = $query->orderByDesc('created_at')->paginate(20);
        return view('admin.support.index', compact('tickets'));
    }

    public function show(int $id)
    {
        $ticket = Ticket::with(['user', 'replies.user'])->findOrFail($id);
        return view('admin.support.show', compact('ticket'));
    }

    public function reply(Request $request, int $id)
    {
        $ticket = Ticket::findOrFail($id);

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
            'user_id' => Auth::id(),
            'message' => $request->message,
            'attachments' => $attachments ?: null,
            'is_admin_reply' => true,
        ]);

        // When admin replies, move ticket into in-progress (unless already final)
        // NOTE: requires tickets.status enum includes 'in_progress' (see migration).
        if (!in_array($ticket->status, ['resolved', 'closed'], true)) {
            $ticket->update(['status' => 'in_progress']);
        }

        return back()->with('success', 'Reply sent!');
    }

    public function updateStatus(Request $request, int $id)
    {
        $request->validate(['status' => 'required|in:pending,open,in_progress,resolved,closed']);
        Ticket::findOrFail($id)->update(['status' => $request->status]);
        return back()->with('success', 'Ticket status updated!');
    }

    public function destroy(int $id)
    {
        $ticket = Ticket::with('replies')->findOrFail($id);

        $paths = [];
        foreach (($ticket->attachments ?? []) as $file) {
            if (!empty($file['path'])) $paths[] = $file['path'];
        }
        foreach ($ticket->replies as $reply) {
            foreach (($reply->attachments ?? []) as $file) {
                if (!empty($file['path'])) $paths[] = $file['path'];
            }
        }

        if ($paths) {
            Storage::disk('public')->delete(array_values(array_unique($paths)));
        }

        $ticket->delete();

        return redirect()->route('admin.support')->with('success', 'Ticket deleted successfully!');
    }
}
