<?php

use Illuminate\Support\Str;

if (!function_exists("browser_user_id")) {
    function browser_user_id(): string
    {
        $agent = request()->header("User-Agent", "unknown");

        // Create a deterministic hash
        $hash = sha1($agent);

        // Format first 32 chars into UUID-like string
        return sprintf(
            "%08s-%04s-%04s-%04s-%12s",
            substr($hash, 0, 8),
            substr($hash, 8, 4),
            substr($hash, 12, 4),
            substr($hash, 16, 4),
            substr($hash, 20, 12),
        );
    }
}
