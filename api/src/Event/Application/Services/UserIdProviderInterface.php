<?php
namespace Src\Event\Application\Services;

interface UserIdProviderInterface {
    public function getUserId(): string;
}