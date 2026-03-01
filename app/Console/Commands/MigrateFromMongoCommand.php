<?php

namespace App\Console\Commands;

use App\Services\MongoDBJsonParser;
use App\Models\ConcertLive;
use App\Models\ConcertLiveRequest;
use App\Models\Faq;
use App\Models\KnowledgeBase;
use App\Models\PaymentMethod;
use App\Models\PlaylistSubmission;
use App\Models\Payout;
use App\Models\PreSave;
use App\Models\PricingPlan;
use App\Models\RadioNetwork;
use App\Models\RadioPromotion;
use App\Models\SiteSetting;
use App\Models\Subscription;
use App\Models\Testimonial;
use App\Models\Track;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use App\Models\VevoAccount;
use App\Models\VevoRequest;
use App\Models\Voucher;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MigrateFromMongoCommand extends Command
{
    protected $signature = 'migrate:from-mongo
                            {--path= : Path to folder containing MongoDB JSON exports (default: MongoDB-Database in project root)}
                            {--dry-run : Only show what would be imported, do not insert}
                            {--skip-users : Skip user import}
                            {--skip-tracks : Skip track import}';

    protected $description = 'Import data from MongoDB JSON exports (MongoDB-Database/ or --path). Uses soundwave.users.json, soundwave.tracks.json, etc.';

    /** @var array<string, int> old MongoDB _id or email -> new Laravel user id */
    private array $userIdMap = [];

    /** @var array<string, int> old MongoDB _id -> new Laravel track id */
    private array $trackIdMap = [];

    /** @var array<string, int> old MongoDB _id -> new Laravel radio_network id */
    private array $radioNetworkIdMap = [];

    /** @var array<string, int> old MongoDB _id -> new Laravel concert_live id */
    private array $concertLiveIdMap = [];

    /** @var array<string, int> old MongoDB _id -> new Laravel ticket id */
    private array $ticketIdMap = [];

    /** @var array<string, int> old MongoDB _id -> new Laravel pricing_plan id */
    private array $pricingPlanIdMap = [];

    /** @var bool use soundwave.*.json filenames */
    private bool $useSoundwavePrefix = true;

    public function handle(): int
    {
        $basePath = $this->option('path') ? base_path($this->option('path')) : base_path('MongoDB-Database');
        if (! is_dir($basePath)) {
            $this->error("Migration data folder not found: {$basePath}");
            $this->info('Place your MongoDB export in project root as MongoDB-Database/ with soundwave.users.json, soundwave.tracks.json, etc.');
            return self::FAILURE;
        }
        $this->info("Using path: {$basePath}");

        $dryRun = $this->option('dry-run');
        if ($dryRun) {
            $this->warn('DRY RUN – no data will be written.');
        }

        try {
            if (! $this->option('skip-users')) {
                $this->importUsers($basePath, $dryRun);
            } else {
                $this->buildUserIdMapFromExisting();
            }
            if (! $this->option('skip-tracks')) {
                $this->importTracks($basePath, $dryRun);
            }
            $this->importRadioNetworks($basePath, $dryRun);
            $this->importConcertLives($basePath, $dryRun);
            $this->importPlaylistSubmissions($basePath, $dryRun);
            $this->importVevoAccounts($basePath, $dryRun);
            $this->importPayouts($basePath, $dryRun);
            $this->importTickets($basePath, $dryRun);
            $this->importConcertLiveRequests($basePath, $dryRun);
            $this->importRadioPromotions($basePath, $dryRun);
            $this->importPricingPlans($basePath, $dryRun);
            $this->importSiteSettings($basePath, $dryRun);
            $this->importFaqs($basePath, $dryRun);
            $this->importTestimonials($basePath, $dryRun);
            $this->importKnowledgeBases($basePath, $dryRun);
            $this->importVevoRequests($basePath, $dryRun);
            $this->importSubscriptions($basePath, $dryRun);
            $this->importPaymentMethods($basePath, $dryRun);
            $this->importPreSaves($basePath, $dryRun);
            $this->importVouchers($basePath, $dryRun);
        } catch (\Throwable $e) {
            $this->error('Migration failed: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
            return self::FAILURE;
        }

        $this->newLine();
        $this->info('Migration finished. Check counts in DB vs your MongoDB export.');
        return self::SUCCESS;
    }

    private function buildUserIdMapFromExisting(): void
    {
        User::query()->get(['id', 'email'])->each(function (User $u) {
            $this->userIdMap[$u->email] = $u->id;
        });
        $this->info('Built user map from existing users: ' . count($this->userIdMap) . ' entries.');
    }

    private function importUsers(string $basePath, bool $dryRun): void
    {
        $path = $basePath . '/soundwave.users.json';
        if (! file_exists($path)) {
            $path = $basePath . '/users.json';
        }
        if (! file_exists($path)) {
            $this->warn("users.json / soundwave.users.json not found at {$basePath}; skipping users.");
            $this->buildUserIdMapFromExisting();
            return;
        }

        $data = $this->readJson($path);
        if (! is_array($data)) {
            $this->error('users.json must be a JSON array.');
            return;
        }

        $this->info('Importing ' . count($data) . ' users...');
        $bar = $this->output->createProgressBar(count($data));
        $bar->start();

        foreach ($data as $row) {
            $bar->advance();
            $mapped = $this->mapUserRow($row);
            if (! $mapped) {
                continue;
            }
            $email = $mapped['email'] ?? null;
            if (! $email) {
                continue;
            }
            if (User::where('email', $email)->exists()) {
                $existing = User::where('email', $email)->first();
                $this->userIdMap[$email] = $existing->id;
                $oldId = $row['_id']['$oid'] ?? $row['_id'] ?? $email;
                $this->userIdMap[(string) $oldId] = $existing->id;
                continue;
            }
            if ($dryRun) {
                $this->userIdMap[$email] = 0;
                $oldId = $row['_id']['$oid'] ?? $row['_id'] ?? $email;
                $this->userIdMap[(string) $oldId] = 0;
                continue;
            }
            $existingPasswordHash = null;
            if (isset($mapped['password']) && str_starts_with((string) $mapped['password'], '$')) {
                $existingPasswordHash = (string) $mapped['password'];
                if (str_starts_with($existingPasswordHash, '$2a$') || str_starts_with($existingPasswordHash, '$2b$')) {
                    $existingPasswordHash = '$2y$' . substr($existingPasswordHash, 4);
                }
                $mapped['password'] = Hash::make(\Illuminate\Support\Str::random(32));
            }
            $user = User::create($mapped);
            if ($existingPasswordHash !== null) {
                DB::table('users')->where('id', $user->id)->update(['password' => $existingPasswordHash]);
            }
            $this->userIdMap[$email] = $user->id;
            $oldId = $row['_id']['$oid'] ?? $row['_id'] ?? $email;
            $this->userIdMap[(string) $oldId] = $user->id;
            if (isset($mapped['created_at']) || isset($mapped['updated_at'])) {
                DB::table('users')->where('id', $user->id)->update([
                    'created_at' => $mapped['created_at'] ?? $user->created_at,
                    'updated_at' => $mapped['updated_at'] ?? $user->updated_at,
                ]);
            }
        }
        $bar->finish();
        $this->newLine();
        $this->info('Users imported. Map size: ' . count($this->userIdMap));
    }

    private function mapUserRow(array $row): ?array
    {
        $email = $row['email'] ?? $row['Email'] ?? null;
        if (! $email) {
            return null;
        }
        $name = $row['full_name'] ?? $row['fullName'] ?? $row['name'] ?? null;
        if ($name === null) {
            $name = trim(($row['firstName'] ?? '') . ' ' . ($row['lastName'] ?? ''));
        }
        if (is_array($name)) {
            $name = trim(($name['firstName'] ?? '') . ' ' . ($name['lastName'] ?? ''));
        }
        $password = $row['password'] ?? null;
        if (! $password || ! \Illuminate\Support\Str::startsWith($password, '$')) {
            $password = Hash::make($password ?? \Illuminate\Support\Str::random(32));
        }
        $createdAt = $this->parseDate($row['createdAt'] ?? $row['created_at'] ?? null);
        $updatedAt = $this->parseDate($row['updatedAt'] ?? $row['updated_at'] ?? null);

        $socialLinks = $row['socialLinks'] ?? $row['social_links'] ?? [];
        if (! is_array($socialLinks)) {
            $socialLinks = [];
        }

        return array_filter([
            'full_name' => $name ?: 'Imported User',
            'email' => $email,
            'password' => $password,
            'is_admin' => (bool) ($row['is_admin'] ?? $row['isAdmin'] ?? false),
            'is_company' => (bool) ($row['is_company'] ?? $row['isCompany'] ?? false),
            'status' => $this->mapEnum($row['status'] ?? 'active', ['active', 'suspended']),
            'paypal_email' => $row['paypal_email'] ?? $row['paypalEmail'] ?? null,
            'balance' => isset($row['balance']) ? (float) $row['balance'] : 0,
            'country' => $row['country'] ?? null,
            'phone' => $row['phone'] ?? null,
            'address' => $row['address'] ?? null,
            'city' => $row['city'] ?? null,
            'state' => $row['state'] ?? null,
            'zip' => $row['zip'] ?? null,
            'is_verified' => (bool) ($row['is_verified'] ?? $row['isVerified'] ?? false),
            'profile_picture' => $row['profile_picture'] ?? $row['profilePicture'] ?? null,
            'bio' => $row['bio'] ?? null,
            'social_facebook' => $row['social_facebook'] ?? $row['socialFacebook'] ?? $socialLinks['facebook'] ?? null,
            'social_twitter' => $row['social_twitter'] ?? $row['socialTwitter'] ?? $socialLinks['twitter'] ?? null,
            'social_instagram' => $row['social_instagram'] ?? $row['socialInstagram'] ?? $socialLinks['instagram'] ?? null,
            'social_website' => $row['social_website'] ?? $row['socialWebsite'] ?? $socialLinks['website'] ?? null,
            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
        ], fn ($v) => $v !== null && $v !== '');
    }

    private function importTracks(string $basePath, bool $dryRun): void
    {
        $path = $basePath . '/soundwave.tracks.json';
        if (! file_exists($path)) {
            $path = $basePath . '/tracks.json';
        }
        if (! file_exists($path)) {
            $this->warn("tracks.json / soundwave.tracks.json not found at {$basePath}; skipping tracks.");
            return;
        }

        $data = $this->readJson($path);
        if (! is_array($data)) {
            $this->error('tracks.json must be a JSON array.');
            return;
        }

        $this->info('Importing ' . count($data) . ' tracks...');
        $bar = $this->output->createProgressBar(count($data));
        $bar->start();
        $skipped = 0;

        foreach ($data as $row) {
            $bar->advance();
            $userId = $this->resolveUserId($row);
            if (! $userId) {
                $skipped++;
                continue;
            }
            $mapped = $this->mapTrackRow($row, $userId);
            if (! $mapped) {
                $skipped++;
                continue;
            }
            if ($dryRun) {
                $oldId = $row['_id']['$oid'] ?? $row['_id'] ?? null;
                if ($oldId !== null) {
                    $this->trackIdMap[(string) $oldId] = 0;
                }
                continue;
            }
            $track = Track::create($mapped);
            $oldId = $row['_id']['$oid'] ?? $row['_id'] ?? null;
            if ($oldId !== null) {
                $this->trackIdMap[(string) $oldId] = $track->id;
            }
            if (isset($mapped['created_at']) || isset($mapped['updated_at'])) {
                DB::table('tracks')->where('id', $track->id)->update([
                    'created_at' => $mapped['created_at'] ?? $track->created_at,
                    'updated_at' => $mapped['updated_at'] ?? $track->updated_at,
                ]);
            }
        }
        $bar->finish();
        $this->newLine();
        if ($skipped > 0) {
            $this->warn("Skipped {$skipped} track(s) (missing user or invalid data).");
        }
        $this->info('Tracks imported. Map size: ' . count($this->trackIdMap));
    }

    private function resolveUserId(array $row): ?int
    {
        $oldId = $row['user_id'] ?? $row['userId'] ?? $row['user'] ?? null;
        if ($oldId !== null) {
            $key = is_array($oldId) ? ($oldId['$oid'] ?? $oldId) : $oldId;
            if (isset($this->userIdMap[(string) $key])) {
                return $this->userIdMap[(string) $key];
            }
        }
        $email = $row['user_email'] ?? null;
        if ($email && isset($this->userIdMap[$email])) {
            return $this->userIdMap[$email];
        }
        return null;
    }

    private function mapTrackRow(array $row, int $userId): ?array
    {
        $releaseType = $this->mapEnum($row['release_type'] ?? $row['releaseType'] ?? 'single', ['single', 'album']);
        $status = $this->mapEnum(
            $row['status'] ?? 'Draft',
            ['Draft', 'On Request', 'On Process', 'Released', 'Rejected', 'Modify Pending', 'Modify Process', 'Modify Released', 'Modify Rejected']
        );
        $createdAt = $this->parseDate($row['createdAt'] ?? $row['created_at'] ?? null);
        $updatedAt = $this->parseDate($row['updatedAt'] ?? $row['updated_at'] ?? null);
        $releaseDate = $this->parseDate($row['release_date'] ?? $row['releaseDate'] ?? null);

        $data = [
            'user_id' => $userId,
            'title' => $row['title'] ?? $row['main_track_title'] ?? $row['mainTrackTitle'] ?? 'Imported',
            'artists' => $row['artists'] ?? $row['stage_name'] ?? $row['stageName'] ?? '',
            'release_type' => $releaseType,
            'primary_genre' => $this->mapEnum($row['primary_genre'] ?? $row['primaryGenre'] ?? null, [
                'Pop', 'Hip-Hop', 'R&B', 'Electronic', 'Rock', 'Alternative', 'Jazz', 'Classical', 'Country', 'Latin', 'Folk', 'Reggae',
            ]),
            'secondary_genre' => $row['secondary_genre'] ?? $row['secondaryGenre'] ?? null,
            'audio_file' => Track::normalizeStoragePath($row['audio_file'] ?? $row['audioFile'] ?? null),
            'cover_art' => Track::normalizeStoragePath($row['cover_art'] ?? $row['coverArt'] ?? null),
            'release_date' => $releaseDate,
            'description' => $row['description'] ?? null,
            'is_explicit' => (bool) ($row['is_explicit'] ?? $row['isExplicit'] ?? false),
            'isrc' => $row['isrc'] ?? null,
            'upc' => $row['upc'] ?? null,
            'status' => $status,
            'new_streams' => (int) ($row['new_streams'] ?? $row['newStreams'] ?? 0),
            'total_streams' => (int) ($row['total_streams'] ?? $row['totalStreams'] ?? 0),
            'first_name' => $row['first_name'] ?? $row['firstName'] ?? null,
            'last_name' => $row['last_name'] ?? $row['lastName'] ?? null,
            'stage_name' => $row['stage_name'] ?? $row['stageName'] ?? null,
            'featuring_artists' => $row['featuring_artists'] ?? $row['featuringArtists'] ?? null,
            'authors' => $row['authors'] ?? null,
            'composers' => $row['composers'] ?? null,
            'producer' => $row['producer'] ?? null,
            'is_youtube_beat' => (bool) ($row['is_youtube_beat'] ?? $row['isYoutubeBeat'] ?? false),
            'has_license' => (bool) ($row['has_license'] ?? $row['hasLicense'] ?? false),
            'tik_tok_start_time' => $row['tik_tok_start_time'] ?? $row['tikTokStartTime'] ?? null,
            'short_bio' => $row['short_bio'] ?? $row['shortBio'] ?? null,
            'track_description' => $row['track_description'] ?? $row['trackDescription'] ?? null,
            'song_duration' => $row['song_duration'] ?? $row['songDuration'] ?? null,
            'cm_society' => $this->mapEnum($row['cm_society'] ?? $row['cmSociety'] ?? 'NONE', ['SIAE', 'SOUNDREEF', 'NONE']),
            'siae_position' => $row['siae_position'] ?? $row['siaePosition'] ?? null,
            'distribution_details' => $row['distribution_details'] ?? $row['distributionDetails'] ?? null,
            'has_spotify_apple' => $this->mapEnum($row['has_spotify_apple'] ?? $row['hasSpotifyApple'] ?? 'NO', ['YES', 'NO']),
            'spotify_link' => $row['spotify_link'] ?? $row['spotifyLink'] ?? null,
            'apple_music_link' => $row['apple_music_link'] ?? $row['appleMusicLink'] ?? null,
            'tik_tok_link' => $row['tik_tok_link'] ?? $row['tikTokLink'] ?? null,
            'youtube_link' => $row['youtube_link'] ?? $row['youtubeLink'] ?? null,
            'lyrics' => $row['lyrics'] ?? null,
            'album_title' => $row['album_title'] ?? $row['albumTitle'] ?? null,
            'main_track_title' => $row['main_track_title'] ?? $row['mainTrackTitle'] ?? null,
            'track_titles' => $this->ensureArray($row['track_titles'] ?? $row['trackTitles'] ?? null),
            'album_tracks' => $this->normalizeAlbumTracksPaths($this->ensureArray($row['album_tracks'] ?? $row['albumTracks'] ?? null)),
            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
        ];
        return $data;
    }

    private function importRadioNetworks(string $basePath, bool $dryRun): void
    {
        $path = $basePath . '/soundwave.radionetworks.json';
        if (! file_exists($path)) {
            return;
        }
        $data = $this->readJson($path);
        if (! is_array($data)) {
            return;
        }
        $this->info('Importing ' . count($data) . ' radio networks...');
        foreach ($data as $row) {
            $createdBy = $this->resolveUserIdFromOid($row['createdBy'] ?? null);
            if ($dryRun) {
                $oid = $row['_id']['$oid'] ?? $row['_id'] ?? null;
                if ($oid !== null) {
                    $this->radioNetworkIdMap[(string) $oid] = 0;
                }
                continue;
            }
            $coverImage = $row['coverImage'] ?? $row['cover_image'] ?? null;
            if (is_string($coverImage)) {
                $coverImage = str_replace('\\', '/', $coverImage);
            }
            $created = RadioNetwork::create([
                'name' => $row['name'] ?? 'Imported',
                'cover_image' => $coverImage,
                'is_active' => (bool) ($row['isActive'] ?? $row['is_active'] ?? true),
                'created_by' => $createdBy,
                'created_at' => $this->parseDate($row['createdAt'] ?? null),
                'updated_at' => $this->parseDate($row['updatedAt'] ?? null),
            ]);
            $oid = $row['_id']['$oid'] ?? $row['_id'] ?? null;
            if ($oid !== null) {
                $this->radioNetworkIdMap[(string) $oid] = $created->id;
            }
        }
    }

    private function importConcertLives(string $basePath, bool $dryRun): void
    {
        $path = $basePath . '/soundwave.concertlives.json';
        if (! file_exists($path)) {
            return;
        }
        $data = $this->readJson($path);
        if (! is_array($data)) {
            return;
        }
        $this->info('Importing ' . count($data) . ' concert lives...');
        foreach ($data as $row) {
            $createdBy = $this->resolveUserIdFromOid($row['createdBy'] ?? null);
            if ($dryRun) {
                $oid = $row['_id']['$oid'] ?? $row['_id'] ?? null;
                if ($oid !== null) {
                    $this->concertLiveIdMap[(string) $oid] = 0;
                }
                continue;
            }
            $concertDate = $this->parseDate($row['concertDate'] ?? $row['concert_date'] ?? null);
            $created = ConcertLive::create([
                'name' => $row['name'] ?? 'Imported',
                'city' => $row['city'] ?? '',
                'concert_date' => $concertDate ? substr($concertDate, 0, 10) : now()->format('Y-m-d'),
                'slots_available' => (int) ($row['slotsAvailable'] ?? $row['slots_available'] ?? 0),
                'slots_booked' => (int) ($row['slotsBooked'] ?? $row['slots_booked'] ?? 0),
                'is_active' => (bool) ($row['isActive'] ?? $row['is_active'] ?? true),
                'created_by' => $createdBy,
                'created_at' => $this->parseDate($row['createdAt'] ?? null),
                'updated_at' => $this->parseDate($row['updatedAt'] ?? null),
            ]);
            $oid = $row['_id']['$oid'] ?? $row['_id'] ?? null;
            if ($oid !== null) {
                $this->concertLiveIdMap[(string) $oid] = $created->id;
            }
        }
    }

    private function importPlaylistSubmissions(string $basePath, bool $dryRun): void
    {
        $path = $basePath . '/soundwave.playlistsubmissions.json';
        if (! file_exists($path)) {
            return;
        }
        $data = $this->readJson($path);
        if (! is_array($data)) {
            return;
        }
        $this->info('Importing ' . count($data) . ' playlist submissions...');
        foreach ($data as $row) {
            $userId = $this->resolveUserIdFromOid($row['userId'] ?? null);
            $trackId = $this->resolveTrackIdFromOid($row['trackId'] ?? null);
            if (! $userId || ! $trackId) {
                continue;
            }
            if ($dryRun) {
                continue;
            }
            $status = $this->mapEnum($row['status'] ?? 'Waiting', ['Waiting', 'Processing', 'Published', 'Rejected']);
            PlaylistSubmission::create([
                'user_id' => $userId,
                'track_id' => $trackId,
                'platform' => $row['platform'] ?? 'Spotify',
                'playlist_name' => $row['playlistName'] ?? $row['playlist_name'] ?? '',
                'playlist_url' => $row['playlistUrl'] ?? $row['playlist_url'] ?? null,
                'status' => $status,
                'submission_date' => $this->parseDate($row['submissionDate'] ?? $row['submission_date'] ?? null),
                'listeners' => (int) ($row['listeners'] ?? 0),
                'streams' => (int) ($row['streams'] ?? 0),
                'created_at' => $this->parseDate($row['createdAt'] ?? null),
                'updated_at' => $this->parseDate($row['updatedAt'] ?? null),
            ]);
        }
    }

    private function importVevoAccounts(string $basePath, bool $dryRun): void
    {
        $path = $basePath . '/soundwave.vevos.json';
        if (! file_exists($path)) {
            return;
        }
        $data = $this->readJson($path);
        if (! is_array($data)) {
            return;
        }
        $this->info('Importing ' . count($data) . ' Vevo accounts...');
        foreach ($data as $row) {
            $userId = $this->resolveUserIdFromOid($row['userId'] ?? null);
            if (! $userId) {
                continue;
            }
            if ($dryRun) {
                continue;
            }
            $status = $this->mapEnum($row['status'] ?? 'Pending', ['Pending', 'Approved', 'Rejected']);
            $approvedBy = $this->resolveUserIdFromOid($row['approvedBy'] ?? null);
            $rejectedBy = $this->resolveUserIdFromOid($row['rejectedBy'] ?? null);
            $createdAt = $this->parseDate($row['createdAt'] ?? null);
            $updatedAt = $this->parseDate($row['updatedAt'] ?? null);
            $account = VevoAccount::create([
                'user_id' => $userId,
                'artist_name' => $row['artistName'] ?? $row['artist_name'] ?? '',
                'contact_email' => $row['contactEmail'] ?? $row['contact_email'] ?? '',
                'telephone' => $row['telephone'] ?? '',
                'release_name' => $row['releaseName'] ?? $row['release_name'] ?? '',
                'biography' => $row['biography'] ?? '',
                'status' => $status,
                'admin_notes' => $row['adminNotes'] ?? $row['admin_notes'] ?? null,
                'vevo_channel_url' => $row['vevoChannelUrl'] ?? $row['vevo_channel_url'] ?? null,
                'approved_at' => $this->parseDate($row['approvedAt'] ?? null),
                'approved_by' => $approvedBy,
                'rejected_at' => $this->parseDate($row['rejectedAt'] ?? null),
                'rejected_by' => $rejectedBy,
                'created_at' => $createdAt,
                'updated_at' => $updatedAt,
            ]);
            if ($createdAt !== null || $updatedAt !== null) {
                DB::table('vevo_accounts')->where('id', $account->id)->update([
                    'created_at' => $createdAt ?? $account->created_at,
                    'updated_at' => $updatedAt ?? $account->updated_at,
                ]);
            }
        }
    }

    private function importPayouts(string $basePath, bool $dryRun): void
    {
        $path = $basePath . '/soundwave.payouts.json';
        if (! file_exists($path)) {
            return;
        }
        $data = $this->readJson($path);
        if (! is_array($data)) {
            return;
        }
        $this->info('Importing ' . count($data) . ' payouts...');
        foreach ($data as $row) {
            $userId = $this->resolveUserIdFromOid($row['userId'] ?? null);
            if (! $userId) {
                continue;
            }
            if ($dryRun) {
                continue;
            }
            $status = $this->mapEnum($row['status'] ?? 'pending', ['pending', 'approved', 'rejected', 'paid']);
            $payoutStats = $row['payoutStats'] ?? $row['payout_stats'] ?? null;
            if (! is_array($payoutStats)) {
                $payoutStats = $payoutStats ? (json_decode($payoutStats, true) ?? null) : null;
            }
            Payout::create([
                'user_id' => $userId,
                'paypal_email' => $row['paypalEmail'] ?? $row['paypal_email'] ?? '',
                'amount' => (float) ($row['amount'] ?? 0),
                'status' => $status,
                'request_date' => $this->parseDate($row['requestDate'] ?? $row['request_date'] ?? null),
                'paid_date' => $this->parseDate($row['paidDate'] ?? $row['paid_date'] ?? null),
                'user_full_name' => $row['userFullName'] ?? $row['user_full_name'] ?? null,
                'user_email' => $row['userEmail'] ?? $row['user_email'] ?? null,
                'payout_stats' => $payoutStats,
                'created_at' => $this->parseDate($row['requestDate'] ?? $row['created_at'] ?? null),
                'updated_at' => $this->parseDate($row['updatedAt'] ?? null),
            ]);
        }
    }

    private function importTickets(string $basePath, bool $dryRun): void
    {
        $path = $basePath . '/soundwave.tickets.json';
        if (! file_exists($path)) {
            return;
        }
        $data = $this->readJson($path);
        if (! is_array($data)) {
            return;
        }
        $this->info('Importing ' . count($data) . ' tickets...');
        foreach ($data as $row) {
            $userId = $this->resolveUserIdFromOid($row['user'] ?? $row['userId'] ?? null);
            if (! $userId) {
                continue;
            }
            $category = $this->mapEnum($row['category'] ?? 'other', ['technical', 'billing', 'feature_request', 'account', 'other']);
            $status = $this->mapEnum($row['status'] ?? 'pending', ['pending', 'open', 'in_progress', 'resolved', 'closed']);
            if ($dryRun) {
                $oid = $row['_id']['$oid'] ?? $row['_id'] ?? null;
                if ($oid !== null) {
                    $this->ticketIdMap[(string) $oid] = 0;
                }
                continue;
            }
            $attachments = $row['attachments'] ?? [];
            if (! is_array($attachments)) {
                $attachments = [];
            }
            $ticket = Ticket::create([
                'user_id' => $userId,
                'subject' => $row['subject'] ?? 'Imported',
                'category' => $category,
                'priority' => $this->mapEnum($row['priority'] ?? 'medium', ['low', 'medium', 'high']),
                'status' => $status,
                'message' => $row['message'] ?? '',
                'attachments' => $attachments,
                'created_at' => $this->parseDate($row['createdAt'] ?? null),
                'updated_at' => $this->parseDate($row['updatedAt'] ?? null),
            ]);
            $oid = $row['_id']['$oid'] ?? $row['_id'] ?? null;
            if ($oid !== null) {
                $this->ticketIdMap[(string) $oid] = $ticket->id;
            }
            $replies = $row['replies'] ?? [];
            if (! is_array($replies)) {
                continue;
            }
            foreach ($replies as $reply) {
                $replyUserId = $this->resolveUserIdFromOid($reply['user'] ?? $reply['userId'] ?? null);
                TicketReply::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $replyUserId,
                    'message' => $reply['message'] ?? '',
                    'attachments' => is_array($reply['attachments'] ?? null) ? $reply['attachments'] : [],
                    'is_admin_reply' => (bool) ($reply['isAdminReply'] ?? $reply['is_admin_reply'] ?? false),
                    'created_at' => $this->parseDate($reply['createdAt'] ?? $reply['created_at'] ?? null),
                    'updated_at' => $this->parseDate($reply['createdAt'] ?? $reply['updated_at'] ?? null),
                ]);
            }
        }
    }

    private function importConcertLiveRequests(string $basePath, bool $dryRun): void
    {
        $path = $basePath . '/soundwave.concertliverequests.json';
        if (! file_exists($path)) {
            return;
        }
        $data = $this->readJson($path);
        if (! is_array($data)) {
            return;
        }
        $this->info('Importing ' . count($data) . ' concert live requests...');
        foreach ($data as $row) {
            $userId = $this->resolveUserIdFromOid($row['userId'] ?? null);
            $concertLiveId = $this->resolveConcertLiveIdFromOid($row['concertLiveId'] ?? null);
            if (! $userId || ! $concertLiveId) {
                continue;
            }
            if ($dryRun) {
                continue;
            }
            $status = $this->mapEnum($row['status'] ?? 'pending', ['pending', 'confirmed', 'cancelled', 'finished']);
            $updatedBy = $this->resolveUserIdFromOid($row['updatedBy'] ?? null);
            ConcertLiveRequest::create([
                'user_id' => $userId,
                'concert_live_id' => $concertLiveId,
                'artist_name' => $row['artistName'] ?? $row['artist_name'] ?? '',
                'status' => $status,
                'request_date' => $this->parseDate($row['requestDate'] ?? $row['request_date'] ?? null),
                'admin_notes' => $row['adminNotes'] ?? $row['admin_notes'] ?? null,
                'updated_by' => $updatedBy,
                'is_active' => (bool) ($row['isActive'] ?? $row['is_active'] ?? true),
                'created_at' => $this->parseDate($row['createdAt'] ?? null),
                'updated_at' => $this->parseDate($row['updatedAt'] ?? null),
            ]);
        }
    }

    private function importRadioPromotions(string $basePath, bool $dryRun): void
    {
        $path = $basePath . '/soundwave.radiopromotions.json';
        if (! file_exists($path)) {
            return;
        }
        $data = $this->readJson($path);
        if (! is_array($data)) {
            return;
        }
        $this->info('Importing ' . count($data) . ' radio promotions...');
        foreach ($data as $row) {
            $userId = $this->resolveUserIdFromOid($row['userId'] ?? null);
            $trackId = $this->resolveTrackIdFromOid($row['trackId'] ?? null);
            if (! $userId || ! $trackId) {
                continue;
            }
            if ($dryRun) {
                continue;
            }
            $radioNetworkId = $this->resolveRadioNetworkIdFromOid($row['radioNetworkId'] ?? null);
            $status = $this->mapEnum($row['status'] ?? 'pending', ['pending', 'published', 'rejected', 'finished']);
            $updatedBy = $this->resolveUserIdFromOid($row['updatedBy'] ?? null);
            $likedBy = $row['likedBy'] ?? $row['liked_by'] ?? null;
            if (is_array($likedBy)) {
                $likedBy = array_map(function ($v) {
                    return is_array($v) && isset($v['$oid']) ? $v['$oid'] : $v;
                }, $likedBy);
            }
            RadioPromotion::create([
                'user_id' => $userId,
                'track_id' => $trackId,
                'track_index' => isset($row['trackIndex']) ? (int) $row['trackIndex'] : ($row['track_index'] ?? null),
                'radio_network_id' => $radioNetworkId,
                'status' => $status,
                'request_date' => $this->parseDate($row['requestDate'] ?? $row['request_date'] ?? null),
                'published_date' => $this->parseDate($row['publishedDate'] ?? $row['published_date'] ?? null),
                'finish_date' => $this->parseDate($row['finishDate'] ?? $row['finish_date'] ?? null),
                'updated_by' => $updatedBy,
                'admin_notes' => $row['adminNotes'] ?? $row['admin_notes'] ?? null,
                'is_active' => (bool) ($row['isActive'] ?? $row['is_active'] ?? true),
                'likes' => (int) ($row['likes'] ?? 0),
                'liked_by' => $likedBy,
                'liked_by_guests' => $row['likedByGuests'] ?? $row['liked_by_guests'] ?? null,
                'created_at' => $this->parseDate($row['createdAt'] ?? null),
                'updated_at' => $this->parseDate($row['updatedAt'] ?? null),
            ]);
        }
    }

    private function importPricingPlans(string $basePath, bool $dryRun): void
    {
        $path = $basePath . '/soundwave.pricingplans.json';
        if (! file_exists($path)) {
            return;
        }
        $data = $this->readJson($path);
        if (! is_array($data)) {
            return;
        }
        $this->info('Importing ' . count($data) . ' pricing plans...');
        foreach ($data as $row) {
            if ($dryRun) {
                $oid = $row['_id']['$oid'] ?? $row['_id'] ?? null;
                if ($oid !== null) {
                    $this->pricingPlanIdMap[(string) $oid] = 0;
                }
                continue;
            }
            $features = $row['features'] ?? null;
            if (! is_array($features)) {
                $features = $features ? (json_decode($features, true) ?? []) : [];
            }
            $created = PricingPlan::create([
                'name' => $row['name'] ?? 'Imported',
                'description' => $row['description'] ?? null,
                'features' => $features,
                'price_monthly' => (float) ($row['priceMonthly'] ?? $row['price_monthly'] ?? 0),
                'price_yearly' => (float) ($row['priceYearly'] ?? $row['price_yearly'] ?? 0),
                'is_active' => (bool) ($row['isActive'] ?? $row['is_active'] ?? true),
                'sort_order' => (int) ($row['sortOrder'] ?? $row['sort_order'] ?? 0),
                'created_at' => $this->parseDate($row['createdAt'] ?? null),
                'updated_at' => $this->parseDate($row['updatedAt'] ?? null),
            ]);
            $oid = $row['_id']['$oid'] ?? $row['_id'] ?? null;
            if ($oid !== null) {
                $this->pricingPlanIdMap[(string) $oid] = $created->id;
            }
        }
    }

    private function importSiteSettings(string $basePath, bool $dryRun): void
    {
        $path = $basePath . '/soundwave.sitesettings.json';
        if (! file_exists($path)) {
            return;
        }
        $data = $this->readJson($path);
        if (! is_array($data)) {
            return;
        }
        $row = isset($data[0]) ? $data[0] : $data;
        if (! is_array($row)) {
            return;
        }
        $this->info('Importing site settings...');
        if ($dryRun) {
            return;
        }
        SiteSetting::query()->delete();
        $logo = $row['logo'] ?? [];
        $favicon = $row['favicon'] ?? [];
        $logoUrl = is_array($logo) ? ($logo['url'] ?? null) : $logo;
        $logoAlt = is_array($logo) ? ($logo['alt'] ?? null) : null;
        $faviconUrl = is_array($favicon) ? ($favicon['url'] ?? null) : $favicon;
        $socialLinks = $row['socialLinks'] ?? $row['social_links'] ?? null;
        $footerLinks1 = $row['footerLinks1'] ?? $row['footer_links_1'] ?? null;
        $footerLinks2 = $row['footerLinks2'] ?? $row['footer_links_2'] ?? null;
        $copyrightLinks = $row['copyrightLinks'] ?? $row['copyright_links'] ?? null;
        $lastUpdatedBy = $this->resolveUserIdFromOid($row['lastUpdatedBy'] ?? null);
        SiteSetting::create([
            'site_title' => $row['title'] ?? $row['site_title'] ?? 'Beat Music',
            'logo_url' => $logoUrl,
            'logo_alt' => $logoAlt,
            'favicon' => $faviconUrl,
            'footer_text' => $row['footerText'] ?? $row['footer_text'] ?? null,
            'copyright_text' => $row['copyrightText'] ?? $row['copyright_text'] ?? null,
            'social_links' => $socialLinks,
            'footer_links_1' => $footerLinks1,
            'footer_links_2' => $footerLinks2,
            'copyright_links' => $copyrightLinks,
            'last_updated_by' => $lastUpdatedBy,
            'created_at' => $this->parseDate($row['createdAt'] ?? null),
            'updated_at' => $this->parseDate($row['updatedAt'] ?? null),
        ]);
    }

    private function importFaqs(string $basePath, bool $dryRun): void
    {
        $path = $basePath . '/soundwave.faqs.json';
        if (! file_exists($path)) {
            return;
        }
        $data = $this->readJson($path);
        if (! is_array($data)) {
            return;
        }
        $this->info('Importing ' . count($data) . ' FAQs...');
        foreach ($data as $row) {
            if ($dryRun) {
                continue;
            }
            $status = $this->mapEnum($row['status'] ?? 'active', ['active', 'inactive']);
            Faq::create([
                'question' => $row['question'] ?? '',
                'answer' => $row['answer'] ?? '',
                'category' => $row['category'] ?? null,
                'status' => $status,
                'sort_order' => (int) ($row['sortOrder'] ?? $row['sort_order'] ?? 0),
                'created_at' => $this->parseDate($row['createdAt'] ?? null),
                'updated_at' => $this->parseDate($row['updatedAt'] ?? null),
            ]);
        }
    }

    private function importTestimonials(string $basePath, bool $dryRun): void
    {
        $path = $basePath . '/soundwave.testimonials.json';
        if (! file_exists($path)) {
            return;
        }
        $data = $this->readJson($path);
        if (! is_array($data)) {
            return;
        }
        $this->info('Importing ' . count($data) . ' testimonials...');
        foreach ($data as $row) {
            if ($dryRun) {
                continue;
            }
            $status = $this->mapEnum($row['status'] ?? 'active', ['active', 'inactive']);
            $displayOn = $row['displayOn'] ?? $row['display_on'] ?? null;
            if (! is_array($displayOn)) {
                $displayOn = $displayOn ? (json_decode($displayOn, true) ?? null) : null;
            }
            Testimonial::create([
                'customer_name' => $row['customerName'] ?? $row['customer_name'] ?? '',
                'title' => $row['title'] ?? null,
                'feedback' => $row['feedback'] ?? '',
                'rating' => (int) ($row['rating'] ?? 5),
                'status' => $status,
                'display_on' => $displayOn,
                'profile_picture' => $row['profilePicture'] ?? $row['profile_picture'] ?? null,
                'created_at' => $this->parseDate($row['createdAt'] ?? null),
                'updated_at' => $this->parseDate($row['updatedAt'] ?? null),
            ]);
        }
    }

    private function importKnowledgeBases(string $basePath, bool $dryRun): void
    {
        $path = $basePath . '/soundwave.knowledgebases.json';
        if (! file_exists($path)) {
            return;
        }
        $data = $this->readJson($path);
        if (! is_array($data)) {
            return;
        }
        $this->info('Importing ' . count($data) . ' knowledge base articles...');
        foreach ($data as $row) {
            $createdBy = $this->resolveUserIdFromOid($row['createdBy'] ?? null);
            if ($dryRun) {
                continue;
            }
            $status = $this->mapEnum($row['status'] ?? 'active', ['active', 'inactive']);
            $tags = $row['tags'] ?? null;
            if (! is_array($tags)) {
                $tags = $tags ? (json_decode($tags, true) ?? null) : null;
            }
            KnowledgeBase::create([
                'title' => $row['title'] ?? '',
                'content' => $row['content'] ?? '',
                'excerpt' => $row['excerpt'] ?? null,
                'category' => $row['category'] ?? null,
                'status' => $status,
                'tags' => $tags,
                'views' => (int) ($row['views'] ?? 0),
                'likes' => (int) ($row['likes'] ?? 0),
                'dislikes' => (int) ($row['dislikes'] ?? 0),
                'featured' => (bool) ($row['featured'] ?? false),
                'last_updated' => $this->parseDate($row['lastUpdated'] ?? $row['updatedAt'] ?? null),
                'created_by' => $createdBy,
                'created_at' => $this->parseDate($row['createdAt'] ?? null),
                'updated_at' => $this->parseDate($row['updatedAt'] ?? null),
            ]);
        }
    }

    private function importVevoRequests(string $basePath, bool $dryRun): void
    {
        $path = $basePath . '/soundwave.vevorequests.json';
        if (! file_exists($path)) {
            return;
        }
        $data = $this->readJson($path);
        if (! is_array($data) || count($data) === 0) {
            return;
        }
        $this->info('Importing ' . count($data) . ' Vevo requests...');
        foreach ($data as $row) {
            $userId = $this->resolveUserIdFromOid($row['userId'] ?? null);
            if (! $userId) {
                continue;
            }
            if ($dryRun) {
                continue;
            }
            $status = $this->mapEnum($row['status'] ?? 'Pending', ['Pending', 'Approved', 'Rejected']);
            $processedBy = $this->resolveUserIdFromOid($row['processedBy'] ?? null);
            VevoRequest::create([
                'user_id' => $userId,
                'artist_name' => $row['artistName'] ?? $row['artist_name'] ?? '',
                'contact_email' => $row['contactEmail'] ?? $row['contact_email'] ?? '',
                'telephone' => $row['telephone'] ?? '',
                'release_name' => $row['releaseName'] ?? $row['release_name'] ?? '',
                'biography' => $row['biography'] ?? '',
                'status' => $status,
                'admin_notes' => $row['adminNotes'] ?? $row['admin_notes'] ?? null,
                'processed_by' => $processedBy,
                'processed_at' => $this->parseDate($row['processedAt'] ?? null),
                'created_at' => $this->parseDate($row['createdAt'] ?? null),
                'updated_at' => $this->parseDate($row['updatedAt'] ?? null),
            ]);
        }
    }

    private function importSubscriptions(string $basePath, bool $dryRun): void
    {
        $path = $basePath . '/soundwave.subscriptions.json';
        if (! file_exists($path)) {
            return;
        }
        $data = $this->readJson($path);
        if (! is_array($data) || count($data) === 0) {
            return;
        }
        $this->info('Importing ' . count($data) . ' subscriptions...');
        foreach ($data as $row) {
            $userId = $this->resolveUserIdFromOid($row['userId'] ?? null);
            if (! $userId) {
                continue;
            }
            $planId = $this->resolvePricingPlanIdFromOid($row['planId'] ?? $row['plan_id'] ?? null);
            if ($dryRun) {
                continue;
            }
            Subscription::create([
                'user_id' => $userId,
                'plan_id' => $planId,
                'plan_name' => $row['planName'] ?? $row['plan_name'] ?? '',
                'start_date' => $this->parseDate($row['startDate'] ?? $row['start_date'] ?? null),
                'end_date' => $this->parseDate($row['endDate'] ?? $row['end_date'] ?? null),
                'status' => $row['status'] ?? 'active',
                'payment_method' => $row['paymentMethod'] ?? $row['payment_method'] ?? null,
                'amount' => (float) ($row['amount'] ?? 0),
                'created_at' => $this->parseDate($row['createdAt'] ?? null),
                'updated_at' => $this->parseDate($row['updatedAt'] ?? null),
            ]);
        }
    }

    private function importPaymentMethods(string $basePath, bool $dryRun): void
    {
        $path = $basePath . '/soundwave.paymentmethods.json';
        if (! file_exists($path)) {
            return;
        }
        $data = $this->readJson($path);
        if (! is_array($data) || count($data) === 0) {
            return;
        }
        $this->info('Importing ' . count($data) . ' payment methods...');
        foreach ($data as $row) {
            $userId = $this->resolveUserIdFromOid($row['userId'] ?? null);
            if (! $userId) {
                continue;
            }
            if ($dryRun) {
                continue;
            }
            PaymentMethod::create([
                'user_id' => $userId,
                'type' => $row['type'] ?? 'stripe',
                'payment_method_id' => $row['paymentMethodId'] ?? $row['payment_method_id'] ?? null,
                'is_default' => (bool) ($row['isDefault'] ?? $row['is_default'] ?? false),
                'paypal_email' => $row['paypalEmail'] ?? $row['paypal_email'] ?? null,
                'created_at' => $this->parseDate($row['createdAt'] ?? null),
                'updated_at' => $this->parseDate($row['updatedAt'] ?? null),
            ]);
        }
    }

    private function importPreSaves(string $basePath, bool $dryRun): void
    {
        $path = $basePath . '/soundwave.presaves.json';
        if (! file_exists($path)) {
            return;
        }
        $data = $this->readJson($path);
        if (! is_array($data)) {
            return;
        }
        $this->info('Importing ' . count($data) . ' pre-saves...');
        foreach ($data as $row) {
            $userId = $this->resolveUserIdFromOid($row['userId'] ?? null);
            $trackId = $this->resolveTrackIdFromOid($row['trackId'] ?? null);
            if (! $trackId) {
                continue;
            }
            if ($dryRun) {
                continue;
            }
            $releaseDate = $this->parseDate($row['releaseDate'] ?? $row['release_date'] ?? null);
            PreSave::create([
                'user_id' => $userId,
                'track_id' => $trackId,
                'platform' => $row['platform'] ?? 'spotify',
                'spotify_user_id' => $row['spotifyUserId'] ?? $row['spotify_user_id'] ?? null,
                'access_token' => $row['accessToken'] ?? $row['access_token'] ?? null,
                'refresh_token' => $row['refreshToken'] ?? $row['refresh_token'] ?? null,
                'token_expires_at' => $this->parseDate($row['tokenExpiresAt'] ?? $row['token_expires_at'] ?? null),
                'status' => $row['status'] ?? 'pending',
                'user_display_name' => $row['userDisplayName'] ?? $row['user_display_name'] ?? null,
                'user_email' => $row['userEmail'] ?? $row['user_email'] ?? null,
                'track_title' => $row['trackTitle'] ?? $row['track_title'] ?? null,
                'artist_name' => $row['artistName'] ?? $row['artist_name'] ?? null,
                'release_date' => $releaseDate ? substr($releaseDate, 0, 10) : null,
                'is_public_pre_save' => (bool) ($row['isPublicPreSave'] ?? $row['is_public_pre_save'] ?? false),
                'processed_at' => $this->parseDate($row['processedAt'] ?? null),
                'error_message' => $row['errorMessage'] ?? $row['error_message'] ?? null,
                'created_at' => $this->parseDate($row['createdAt'] ?? null),
                'updated_at' => $this->parseDate($row['updatedAt'] ?? null),
            ]);
        }
    }

    private function importVouchers(string $basePath, bool $dryRun): void
    {
        $path = $basePath . '/soundwave.vouchers.json';
        if (! file_exists($path)) {
            return;
        }
        $data = $this->readJson($path);
        if (! is_array($data) || count($data) === 0) {
            return;
        }
        $this->info('Importing ' . count($data) . ' vouchers...');
        foreach ($data as $row) {
            $createdBy = $this->resolveUserIdFromOid($row['createdBy'] ?? null);
            $specificUser = $this->resolveUserIdFromOid($row['specificUser'] ?? null);
            if ($dryRun) {
                continue;
            }
            $discountType = $this->mapEnum($row['discountType'] ?? $row['discount_type'] ?? 'percentage', ['percentage', 'fixed']);
            Voucher::create([
                'code' => $row['code'] ?? '',
                'discount_type' => $discountType,
                'discount_amount' => (float) ($row['discountAmount'] ?? $row['discount_amount'] ?? 0),
                'max_uses' => isset($row['maxUses']) ? (int) $row['maxUses'] : ($row['max_uses'] ?? null),
                'used_count' => (int) ($row['usedCount'] ?? $row['used_count'] ?? 0),
                'expiration_date' => $this->parseDate($row['expirationDate'] ?? $row['expiration_date'] ?? null),
                'is_active' => (bool) ($row['isActive'] ?? $row['is_active'] ?? true),
                'specific_user' => $specificUser,
                'subscription_plan' => $row['subscriptionPlan'] ?? $row['subscription_plan'] ?? null,
                'created_by' => $createdBy,
                'created_at' => $this->parseDate($row['createdAt'] ?? null),
                'updated_at' => $this->parseDate($row['updatedAt'] ?? null),
            ]);
        }
    }

    private function resolveUserIdFromOid(mixed $oid): ?int
    {
        if ($oid === null) {
            return null;
        }
        $key = is_array($oid) && isset($oid['$oid']) ? $oid['$oid'] : $oid;
        return $this->userIdMap[(string) $key] ?? null;
    }

    private function resolvePricingPlanIdFromOid(mixed $oid): ?int
    {
        if ($oid === null) {
            return null;
        }
        $key = is_array($oid) && isset($oid['$oid']) ? $oid['$oid'] : $oid;
        return $this->pricingPlanIdMap[(string) $key] ?? null;
    }

    private function resolveTrackIdFromOid(mixed $oid): ?int
    {
        if ($oid === null) {
            return null;
        }
        $key = is_array($oid) && isset($oid['$oid']) ? $oid['$oid'] : $oid;
        return $this->trackIdMap[(string) $key] ?? null;
    }

    private function resolveRadioNetworkIdFromOid(mixed $oid): ?int
    {
        if ($oid === null) {
            return null;
        }
        $key = is_array($oid) && isset($oid['$oid']) ? $oid['$oid'] : $oid;
        return $this->radioNetworkIdMap[(string) $key] ?? null;
    }

    private function resolveConcertLiveIdFromOid(mixed $oid): ?int
    {
        if ($oid === null) {
            return null;
        }
        $key = is_array($oid) && isset($oid['$oid']) ? $oid['$oid'] : $oid;
        return $this->concertLiveIdMap[(string) $key] ?? null;
    }

    private function readJson(string $path): mixed
    {
        $parsed = MongoDBJsonParser::parseFile($path);
        if (! is_array($parsed)) {
            return null;
        }
        return $parsed;
    }

    private function parseDate(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        if ($value instanceof \Carbon\Carbon) {
            return $value->format('Y-m-d H:i:s');
        }
        if (is_array($value) && isset($value['$date'])) {
            $value = $value['$date'];
        }
        if (is_numeric($value)) {
            return date('Y-m-d H:i:s', (int) $value);
        }
        try {
            $dt = $value instanceof \DateTimeInterface ? $value : new \DateTime($value);
            return $dt->format('Y-m-d H:i:s');
        } catch (\Throwable) {
            return null;
        }
    }

    private function mapEnum(mixed $value, array $allowed): string
    {
        if ($value === null || $value === '') {
            return $allowed[0];
        }
        $v = (string) $value;
        foreach ($allowed as $a) {
            if (strcasecmp($v, $a) === 0) {
                return $a;
            }
        }
        return $allowed[0];
    }

    private function ensureArray(mixed $value): ?array
    {
        if ($value === null) {
            return null;
        }
        if (is_array($value)) {
            return $value;
        }
        if (is_string($value)) {
            $dec = json_decode($value, true);
            return is_array($dec) ? $dec : null;
        }
        return null;
    }

    /** Normalize audio paths and map audioFile->audio_file in album_tracks from MongoDB format. */
    private function normalizeAlbumTracksPaths(?array $tracks): ?array
    {
        if (! $tracks) {
            return null;
        }
        $out = [];
        foreach ($tracks as $i => $t) {
            if (! is_array($t)) {
                continue;
            }
            $audioPath = $t['audio_file'] ?? $t['audioFile'] ?? null;
            $normalized = Track::normalizeStoragePath($audioPath);
            $out[] = [
                'title' => $t['title'] ?? 'Track ' . ($i + 1),
                'audio_file' => $normalized,
                'duration' => $t['duration'] ?? null,
                'order' => $t['order'] ?? $i,
            ];
        }
        return $out;
    }
}
