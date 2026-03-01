@extends('layouts.admin')

@section('title', 'Newsletter Subscribers')

@section('content')
    <div class="max-w-6xl mx-auto px-4 py-8">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-white">Newsletter Subscribers</h1>
                <p class="text-slate-400 text-sm mt-1">All emails subscribed through the website footer.</p>
            </div>
        </div>

        <div class="glass rounded-2xl border border-slate-800/60 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-800/60 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-slate-200 uppercase tracking-wider">Subscribers</h2>
                @if($subscribers->total())
                    <span class="text-xs text-slate-400">{{ $subscribers->total() }} total</span>
                @endif
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-800/60">
                    <thead class="bg-slate-900/40">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-400 uppercase tracking-wider">Subscribed At</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60 bg-slate-900/20">
                        @forelse($subscribers as $subscriber)
                            <tr>
                                <td class="px-6 py-3 text-sm text-slate-100">
                                    {{ $subscriber->email }}
                                </td>
                                <td class="px-6 py-3 text-sm text-slate-300">
                                    <div>{{ $subscriber->created_at->format('M d, Y') }}</div>
                                    <div class="text-xs text-slate-400">{{ $subscriber->created_at->format('H:i') }}</div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-6 py-8 text-center text-sm text-slate-400">
                                    No subscribers yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($subscribers->hasPages())
                <div class="px-6 py-4 border-t border-slate-800/60">
                    {{ $subscribers->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

