<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class NormalizeMigratedPasswordsCommand extends Command
{
    protected $signature = 'migrate:from-mongo-normalize-passwords';

    protected $description = 'Normalize bcrypt passwords from $2a$/$2b$ (Node/MongoDB) to $2y$ so Laravel login works. Run once after migrating users.';

    public function handle(): int
    {
        $updated = 0;
        $users = DB::table('users')->get(['id', 'password']);

        foreach ($users as $user) {
            $pw = $user->password;
            if (! is_string($pw) || strlen($pw) < 4) {
                continue;
            }
            $newHash = null;
            if (str_starts_with($pw, '$2a$')) {
                $newHash = '$2y$' . substr($pw, 4);
            } elseif (str_starts_with($pw, '$2b$')) {
                $newHash = '$2y$' . substr($pw, 4);
            }
            if ($newHash !== null) {
                DB::table('users')->where('id', $user->id)->update(['password' => $newHash]);
                $updated++;
            }
        }

        $this->info("Normalized {$updated} user password(s). Login with old MongoDB passwords should now work.");
        return self::SUCCESS;
    }
}
