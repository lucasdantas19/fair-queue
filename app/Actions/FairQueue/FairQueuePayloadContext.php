<?php

namespace App\Actions\FairQueue;

final class FairQueuePayloadContext
{
    private static ?int $scheduleJobId = null;

    private static ?string $dispatchToken = null;

    public static function set(int $scheduleJobId, string $dispatchToken): void
    {
        self::$scheduleJobId = $scheduleJobId;
        self::$dispatchToken = $dispatchToken;
    }

    public static function clear(): void
    {
        self::$scheduleJobId = null;
        self::$dispatchToken = null;
    }

    public static function scheduleJobId(): ?int
    {
        return self::$scheduleJobId;
    }

    public static function dispatchToken(): ?string
    {
        return self::$dispatchToken;
    }
}
