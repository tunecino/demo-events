<?php

namespace Src\Event\Infrastructure\Services;

use Src\Event\Application\Services\UserIdProviderInterface;

final class BrowserUserIdProvider implements UserIdProviderInterface
{
    public function getUserId(): string
    {
        $agent = request()->header('User-Agent', 'unknown');
        $hash = sha1($agent);
        return sprintf(
            '%08s-%04s-%04s-%04s-%12s',
            substr($hash, 0, 8),
            substr($hash, 8, 4),
            substr($hash, 12, 4),
            substr($hash, 16, 4),
            substr($hash, 20, 12)
        );
    }
}