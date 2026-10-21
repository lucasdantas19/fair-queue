<?php

namespace FairQueue\ValueObjects;

use DateTimeImmutable;

final readonly class GroupSnapshot
{
    public function __construct(
        public string $groupKey,
        public string $groupValue,
        public int $activeJobs,
        public DateTimeImmutable $oldestPendingAt,
        public int $oldestPendingId,
    ) {}

    public function identifier(): string
    {
        return "{$this->groupKey}:{$this->groupValue}";
    }
}
