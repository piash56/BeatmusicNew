<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Track;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminTracksController extends Controller
{
    public function trackSubmissions(Request $request)
    {
        $query = Track::select([
                'id',
                'user_id',
                'title',
                'artists',
                'release_type',
                'primary_genre',
                'status',
                'cover_art',
                'created_at',
                'upc',
            ])
            ->where('release_type', 'single')
            ->with(['user:id,full_name,email']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('artists', 'like', '%' . $request->search . '%');
            });
        }

        $tracks = $query->orderByDesc('created_at')->paginate(20);
        return view('admin.track-submissions.index', compact('tracks'));
    }

    public function viewTrack(int $id)
    {
        $track = Track::with(['user', 'preSaves'])->findOrFail($id);
        $preSaves = $track->preSaves()->paginate(10);
        return view('admin.track-submissions.show', compact('track', 'preSaves'));
    }

    public function albumSubmissions(Request $request)
    {
        $query = Track::select([
                'id',
                'user_id',
                'title',
                'album_title',
                'artists',
                'release_type',
                'primary_genre',
                'status',
                'cover_art',
                'created_at',
                'album_tracks',
                'upc',
            ])
            ->where('release_type', 'album')
            ->with(['user:id,full_name,email']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('artists', 'like', '%' . $request->search . '%');
            });
        }

        $tracks = $query->orderByDesc('created_at')->paginate(20);
        return view('admin.album-submissions.index', compact('tracks'));
    }

    public function viewAlbum(int $id)
    {
        $track = Track::where('release_type', 'album')->with(['user', 'preSaves'])->findOrFail($id);
        $preSaves = $track->preSaves()->paginate(10);
        return view('admin.album-submissions.show', compact('track', 'preSaves'));
    }

    public function updateStatus(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|in:Draft,On Request,On Process,Released,Rejected,Modify Pending,Modify Process,Modify Released,Modify Rejected',
        ]);

        $track = Track::findOrFail($id);
        $oldStatus = $track->status;
        $track->update(['status' => $request->status]);

        // Update user stats
        $user = $track->user;
        if ($request->status === 'Released' && $oldStatus !== 'Released') {
            $user->increment('stats_released_tracks');
            $user->decrement('stats_on_process_tracks');
        } elseif ($request->status === 'On Process') {
            $user->increment('stats_on_process_tracks');
            $user->decrement('stats_on_request_tracks');
        } elseif ($request->status === 'Rejected') {
            $user->increment('stats_rejected_tracks');
        }

        return back()->with('success', 'Track status updated to ' . $request->status . '.');
    }

    public function updateUpc(Request $request, int $id)
    {
        $request->validate(['upc' => 'nullable|string|max:255']);
        $track = Track::findOrFail($id);
        $track->update(['upc' => $request->input('upc')]);
        if ($request->expectsJson()) {
            return response()->json(['upc' => $track->upc, 'message' => 'UPC updated.']);
        }
        return back()->with('success', 'UPC updated.');
    }

    public function updateTrack(Request $request, int $id)
    {
        $track = Track::findOrFail($id);
        $data = $request->only([
            'title',
            'artists',
            'album_title',
            'main_track_title',
            'primary_genre',
            'secondary_genre',
            'release_date',
            'description',
            'is_explicit',
            'isrc',
            'upc',
            'first_name',
            'last_name',
            'stage_name',
            'featuring_artists',
            'authors',
            'composers',
            'producer',
            'is_youtube_beat',
            'has_license',
            'tik_tok_start_time',
            'short_bio',
            'track_description',
            'song_duration',
            'cm_society',
            'siae_position',
            'distribution_details',
            'has_spotify_apple',
            'spotify_link',
            'apple_music_link',
            'tik_tok_link',
            'youtube_link',
            'lyrics',
        ]);
        $data['is_explicit'] = $request->boolean('is_explicit');
        $data['is_youtube_beat'] = $request->boolean('is_youtube_beat');
        $data['has_license'] = $request->boolean('has_license');
        if ($request->has('release_date') && $request->release_date !== '') {
            $data['release_date'] = $request->release_date;
        }
        $track->update($data);
        return back()->with('success', 'Release updated.');
    }

    public function updateStreams(Request $request, int $trackId)
    {
        $request->validate([
            'streams' => 'required|integer|min:0',
        ]);

        $track = Track::findOrFail($trackId);
        $increment = (int) $request->streams;
        $oldStreams = (int) ($track->total_streams ?? 0);
        $newTotal = $oldStreams + $increment;

        $track->update([
            'total_streams' => $newTotal,
            'new_streams' => $increment,
        ]);

        // Update user total streams
        $user = $track->user;
        if ($increment !== 0 && $user) {
            $user->increment('stats_total_streams', $increment);
        }

        return back()->with('success', 'Streams updated!');
    }

    public function streamsManagement(Request $request)
    {
        $query = Track::select([
                'id',
                'user_id',
                'title',
                'album_title',
                'artists',
                'release_type',
                'upc',
                'total_streams',
                'new_streams',
                'cover_art',
                'status',
            ])
            ->with(['user:id,full_name,email'])
            ->where('status', 'Released');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                    ->orWhere('album_title', 'like', "%{$s}%")
                    ->orWhere('artists', 'like', "%{$s}%")
                    ->orWhere('upc', 'like', "%{$s}%")
                    ->orWhereHas('user', function ($u) use ($s) {
                        $u->where('full_name', 'like', "%{$s}%")
                            ->orWhere('email', 'like', "%{$s}%");
                    });
            });
        }

        $tracks = $query->orderByDesc('total_streams')
            ->paginate(20)
            ->withQueryString();
        return view('admin.streams', compact('tracks'));
    }

    public function importStreams(Request $request)
    {
        $request->validate([
            'streams_file' => 'required|file|mimes:xlsx|max:10240',
        ]);

        $file = $request->file('streams_file');
        $path = $file->getRealPath();

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);

        $results = [];
        $rowNum = 0;

        foreach ($rows as $row) {
            $rowNum++;
            if ($rowNum === 1) {
                // header
                continue;
            }

            $upc = trim($row['A'] ?? '');
            $rawIncrement = $row['B'] ?? null;
            $increment = (int) ($rawIncrement ?? 0);

            // Completely empty row: skip silently
            if ($upc === '' && ($rawIncrement === null || $rawIncrement === '')) {
                continue;
            }

            // Row with missing UPC or zero increment: record as error
            if ($upc === '' || $increment === 0) {
                $results[] = [
                    'upc' => $upc,
                    'increment' => $increment,
                    'title' => null,
                    'artist' => null,
                    'success' => 'No',
                    'message' => 'Missing UPC or zero increment',
                ];
                continue;
            }

            $track = Track::where('upc', $upc)->first();
            if (!$track) {
                $results[] = [
                    'upc' => $upc,
                    'increment' => $increment,
                    'title' => null,
                    'artist' => null,
                    'success' => 'No',
                    'message' => 'Track/Album with this UPC not found',
                ];
                continue;
            }

            $oldTotal = (int) ($track->total_streams ?? 0);
            $newTotal = $oldTotal + $increment;

            $track->update([
                'total_streams' => $newTotal,
                'new_streams' => $increment,
            ]);

            if ($increment !== 0 && $track->user) {
                $track->user->increment('stats_total_streams', $increment);
            }

            $displayTitle = $track->release_type === 'album'
                ? ($track->album_title ?: $track->title)
                : $track->title;

            $results[] = [
                'upc' => $upc,
                'increment' => $increment,
                'title' => $displayTitle,
                'artist' => $track->artists,
                'success' => 'Yes',
                'message' => 'Updated successfully',
            ];
        }

        // Build results xlsx
        $export = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $exportSheet = $export->getActiveSheet();
        $exportSheet->fromArray(
            ['UPC', 'Update Streams', 'Track/Album', 'Artist', 'Success', 'Message'],
            null,
            'A1'
        );

        $rowOut = 2;
        foreach ($results as $res) {
            $exportSheet->fromArray(
                [
                    $res['upc'],
                    $res['increment'],
                    $res['title'],
                    $res['artist'],
                    $res['success'],
                    $res['message'],
                ],
                null,
                'A' . $rowOut
            );
            $rowOut++;
        }

        // Build results filename: import_results_{index}_{YYYYMMDD}.xlsx in public/csv
        $directory = 'csv';
        Storage::disk('public')->makeDirectory($directory);
        $existing = Storage::disk('public')->files($directory);
        $maxIndex = 0;
        foreach ($existing as $filePath) {
            $basename = basename($filePath);
            if (preg_match('/^import_results_(\d+)_\d{8}\.xlsx$/', $basename, $m)) {
                $maxIndex = max($maxIndex, (int) $m[1]);
            }
        }
        $index = $maxIndex + 1;
        $date = now()->format('Ymd');
        $filename = 'import_results_' . $index . '_' . $date . '.xlsx';
        $outputPath = Storage::disk('public')->path($directory . '/' . $filename);

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($export, 'Xlsx');
        $writer->save($outputPath);

        $downloadUrl = Storage::disk('public')->url($directory . '/' . $filename);

        return redirect()
            ->route('admin.streams')
            ->with('success', 'Bulk streams import completed.')
            ->with('streams_import_download', $downloadUrl);
    }
}
