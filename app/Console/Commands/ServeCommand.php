<?php

namespace App\Console\Commands;

use Illuminate\Foundation\Console\ServeCommand as BaseServeCommand;

/**
 * Override Laravel's serve command so the PHP built-in server runs with
 * increased upload limits (200M). This fixes 413 "Content Too Large"
 * when uploading cover art and tracks during local development.
 *
 * Unlike Node.js, PHP applies post_max_size and upload_max_filesize from
 * php.ini before your app runs; Laravel cannot change them at runtime.
 * So we run the server process with -d flags instead.
 */
class ServeCommand extends BaseServeCommand
{
    /**
     * Get the full server command, with upload limits set for release uploads.
     *
     * @return array<int, string>
     */
    protected function serverCommand(): array
    {
        $parent = parent::serverCommand();

        return array_merge(
            [
                $parent[0],
                '-d', 'post_max_size=200M',
                '-d', 'upload_max_filesize=200M',
            ],
            array_slice($parent, 1)
        );
    }
}
