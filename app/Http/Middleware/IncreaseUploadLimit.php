<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IncreaseUploadLimit
{
    /**
     * Try to increase PHP upload limits so cover/track uploads and release submit succeed.
     * Note: On some setups (e.g. FPM) these must be set in php.ini; see UPLOAD-LIMIT.txt.
     */
    public function handle(Request $request, Closure $next): Response
    {
        @ini_set('post_max_size', '200M');
        @ini_set('upload_max_filesize', '200M');

        return $next($request);
    }
}
