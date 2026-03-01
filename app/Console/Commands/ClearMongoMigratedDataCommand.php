<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ClearMongoMigratedDataCommand extends Command
{
    protected $signature = 'migrate:from-mongo-clear
                            {--force : Skip confirmation prompt}';

    protected $description = 'Delete users and tracks so you can re-run migrate:from-mongo without duplicates. Run only on dev/staging or after backup.';

    public function handle(): int
    {
        if (! $this->option('force')) {
            if (! $this->confirm('This will DELETE all records from tracks and users. Use only on a copy/development DB. Have you backed up? Continue?')) {
                $this->info('Aborted.');
                return self::SUCCESS;
            }
        }

        $driver = DB::getDriverName();

        try {
            if ($driver === 'mysql') {
                DB::statement('SET FOREIGN_KEY_CHECKS = 0');
            }

            $tables = [
                'ticket_replies',
                'tickets',
                'payouts',
                'playlist_submissions',
                'vevo_accounts',
                'concert_live_requests',
                'radio_promotions',
                'concert_lives',
                'radio_networks',
                'subscriptions',
                'payment_methods',
                'pre_saves',
                'vouchers',
                'knowledge_bases',
                'vevo_requests',
                'site_settings',
                'faqs',
                'testimonials',
                'pricing_plans',
                'tracks',
                'users',
            ];

            foreach ($tables as $table) {
                if (! Schema::hasTable($table)) {
                    continue;
                }
                $deleted = DB::table($table)->delete();
                $this->info("Deleted {$deleted} row(s) from {$table}.");
            }

            if ($driver === 'mysql') {
                DB::statement('SET FOREIGN_KEY_CHECKS = 1');
            }

            $this->newLine();
            $this->info('Done. You can now run: php artisan migrate:from-mongo');
        } catch (\Throwable $e) {
            if ($driver === 'mysql') {
                DB::statement('SET FOREIGN_KEY_CHECKS = 1');
            }
            $this->error('Clear failed: ' . $e->getMessage());
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
