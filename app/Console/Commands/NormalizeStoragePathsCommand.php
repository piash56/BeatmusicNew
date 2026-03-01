<?php

namespace App\Console\Commands;

use App\Models\Track;
use Illuminate\Console\Command;

class NormalizeStoragePathsCommand extends Command
{
    protected $signature = 'migrate:normalize-storage-paths
                            {--dry-run : Show what would be updated without writing}
                            {--force : Skip confirmation prompt}';

    protected $description = 'Normalize cover_art and audio_file paths in tracks table from MongoDB format (/uploads/covers/X) to Laravel format (covers/X). Run once after migrating from MongoDB.';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        if ($dryRun) {
            $this->warn('DRY RUN – no changes will be written.');
        }

        $tracks = Track::query()->get(['id', 'title', 'cover_art', 'audio_file', 'album_tracks']);
        $toUpdate = [];

        foreach ($tracks as $track) {
            $updates = [];
            $normCover = Track::normalizeStoragePath($track->cover_art);
            if ($track->cover_art && $normCover !== $track->cover_art) {
                $updates['cover_art'] = $normCover;
            }
            $normAudio = Track::normalizeStoragePath($track->audio_file);
            if ($track->audio_file && $normAudio !== $track->audio_file) {
                $updates['audio_file'] = $normAudio;
            }
            $albumTracks = $track->album_tracks;
            if (is_array($albumTracks)) {
                $changed = false;
                foreach ($albumTracks as &$at) {
                    if (is_array($at) && isset($at['audio_file'])) {
                        $norm = Track::normalizeStoragePath($at['audio_file']);
                        if ($norm !== $at['audio_file']) {
                            $at['audio_file'] = $norm;
                            $changed = true;
                        }
                    }
                }
                if ($changed) {
                    $updates['album_tracks'] = $albumTracks;
                }
            }
            if (! empty($updates)) {
                $toUpdate[] = ['track' => $track, 'updates' => $updates];
            }
        }

        if (empty($toUpdate)) {
            $this->info('No tracks need path normalization.');
            return self::SUCCESS;
        }

        $this->info('Found ' . count($toUpdate) . ' track(s) with paths to normalize.');
        foreach (array_slice($toUpdate, 0, 5) as $item) {
            $this->line('  - ' . $item['track']->title . ' (id: ' . $item['track']->id . ')');
            foreach ($item['updates'] as $k => $v) {
                if ($k === 'album_tracks') {
                    $this->line('    album_tracks: (audio paths normalized)');
                } else {
                    $this->line("    {$k}: {$v}");
                }
            }
        }
        if (count($toUpdate) > 5) {
            $this->line('  ... and ' . (count($toUpdate) - 5) . ' more.');
        }

        if (! $dryRun && ! $force && ! $this->confirm('Apply these updates?', true)) {
            $this->info('Aborted.');
            return self::SUCCESS;
        }

        if ($dryRun) {
            $this->info('Dry run complete. Run without --dry-run to apply.');
            return self::SUCCESS;
        }

        $updated = 0;
        foreach ($toUpdate as $item) {
            $track = $item['track'];
            $track->update($item['updates']);
            $updated++;
        }

        $this->info("Normalized paths for {$updated} track(s).");
        return self::SUCCESS;
    }
}
