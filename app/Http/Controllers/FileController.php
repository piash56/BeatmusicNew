<?php

namespace App\Http\Controllers;

use App\Models\Track;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileController extends Controller
{
    public function cover(int $trackId)
    {
        $track = Track::findOrFail($trackId);
        $path = $track->cover_art_storage_path;

        if (!$path || !Storage::disk('public')->exists($path)) {
            abort(404, 'Cover art not found.');
        }

        return response()->file(Storage::disk('public')->path($path));
    }

    /** Download cover art (track owner or admin). */
    public function downloadCover(int $trackId)
    {
        $track = Track::findOrFail($trackId);
        if (!auth()->check() || (!auth()->user()->isAdmin() && $track->user_id !== auth()->id())) {
            abort(403);
        }
        $path = $track->cover_art_storage_path;
        if (!$path || !Storage::disk('public')->exists($path)) {
            abort(404, 'Cover art not found.');
        }
        $fullPath = Storage::disk('public')->path($path);
        $name = \Illuminate\Support\Str::slug($track->title) . '-cover.' . pathinfo($path, PATHINFO_EXTENSION);
        return response()->download($fullPath, $name);
    }

    public function audio(Request $request, int $trackId)
    {
        $track = Track::findOrFail($trackId);

        // Authorization: admin or the track owner
        if (!auth()->check()) {
            abort(403);
        }

        if (!auth()->user()->isAdmin() && $track->user_id !== auth()->id()) {
            abort(403);
        }

        $audioPath = $track->audio_file_storage_path;
        if (!$audioPath || !Storage::disk('public')->exists($audioPath)) {
            abort(404, 'Audio file not found.');
        }

        $path = Storage::disk('public')->path($audioPath);
        $size = filesize($path);
        $mimeType = mime_content_type($path);

        $start = 0;
        $end = $size - 1;
        $statusCode = 200;
        $headers = [
            'Content-Type' => $mimeType,
            'Accept-Ranges' => 'bytes',
            'Content-Length' => $size,
        ];

        if ($request->hasHeader('Range')) {
            preg_match('/bytes=(\d+)-(\d*)/', $request->header('Range'), $matches);
            $start = intval($matches[1]);
            $end = !empty($matches[2]) ? intval($matches[2]) : $size - 1;
            $length = $end - $start + 1;
            $statusCode = 206;
            $headers['Content-Range'] = "bytes {$start}-{$end}/{$size}";
            $headers['Content-Length'] = $length;
        }

        // Optional download (track owner or admin)
        if ($request->boolean('download')) {
            $name = \Illuminate\Support\Str::slug($track->title) . '.' . pathinfo($audioPath, PATHINFO_EXTENSION);
            return response()->download(Storage::disk('public')->path($audioPath), $name);
        }

        return response()->stream(function () use ($path, $start, $end) {
            $file = fopen($path, 'rb');
            fseek($file, $start);
            $remaining = $end - $start + 1;
            while (!feof($file) && $remaining > 0) {
                $chunkSize = min(8192, $remaining);
                echo fread($file, $chunkSize);
                $remaining -= $chunkSize;
                flush();
            }
            fclose($file);
        }, $statusCode, $headers);
    }

    public function albumTrack(Request $request, int $trackId, int $trackIndex)
    {
        $track = Track::findOrFail($trackId);

        if (!auth()->check()) abort(403);
        if (!auth()->user()->isAdmin() && $track->user_id !== auth()->id()) abort(403);

        $albumTracks = $track->album_tracks ?? [];
        $albumTrack = $albumTracks[$trackIndex] ?? null;

        $audioPath = $albumTrack['audio_file'] ?? $albumTrack['audioFile'] ?? null;
        if (!$albumTrack || !$audioPath) {
            abort(404, 'Album track not found.');
        }

        $filePath = Track::normalizeStoragePath($audioPath);
        if (!$filePath || !Storage::disk('public')->exists($filePath)) {
            abort(404, 'Audio file not found.');
        }

        $path = Storage::disk('public')->path($filePath);
        if ($request->boolean('download')) {
            $title = $albumTrack['title'] ?? 'Track-' . ($trackIndex + 1);
            $name = \Illuminate\Support\Str::slug($title) . '.' . pathinfo($filePath, PATHINFO_EXTENSION);
            return response()->download($path, $name);
        }
        return response()->file($path);
    }
}
