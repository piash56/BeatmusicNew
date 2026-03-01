<?php

namespace App\Http\Controllers;

use App\Models\RadioPromotion;
use Illuminate\Http\Request;

class PodcastController extends Controller
{
    public function show(int $id)
    {
        $promotion = RadioPromotion::with(['track', 'user', 'radioNetwork'])
            ->where('status', 'published')
            ->findOrFail($id);

        $isLiked = false;
        if (auth()->check()) {
            $isLiked = $promotion->isLikedByUser(auth()->id());
        }

        return view('podcast.show', compact('promotion', 'isLiked'));
    }

    public function like(Request $request, int $id)
    {
        $promotion = RadioPromotion::findOrFail($id);
        $userId = auth()->id();
        $guestUuid = $request->input('guest_uuid');

        $likedBy = $promotion->liked_by ?? [];
        $likedByGuests = $promotion->liked_by_guests ?? [];

        if ($userId) {
            if (in_array($userId, $likedBy)) {
                $likedBy = array_values(array_diff($likedBy, [$userId]));
                $promotion->update(['liked_by' => $likedBy, 'likes' => max(0, $promotion->likes - 1)]);
                return response()->json(['liked' => false, 'likes' => $promotion->fresh()->likes]);
            }
            $likedBy[] = $userId;
            $promotion->update(['liked_by' => $likedBy, 'likes' => count($likedBy) + count($likedByGuests)]);
            return response()->json(['liked' => true, 'likes' => $promotion->fresh()->likes]);
        }

        if (!$guestUuid || !is_string($guestUuid) || strlen($guestUuid) < 10) {
            return response()->json(['error' => 'guest_uuid required for guests'], 422);
        }
        if (in_array($guestUuid, $likedByGuests)) {
            $likedByGuests = array_values(array_diff($likedByGuests, [$guestUuid]));
            $promotion->update(['liked_by_guests' => $likedByGuests, 'likes' => max(0, count($likedBy) + count($likedByGuests))]);
            return response()->json(['liked' => false, 'likes' => $promotion->fresh()->likes]);
        }
        $likedByGuests[] = $guestUuid;
        $promotion->update(['liked_by_guests' => $likedByGuests, 'likes' => count($likedBy) + count($likedByGuests)]);
        return response()->json(['liked' => true, 'likes' => $promotion->fresh()->likes]);
    }
}
