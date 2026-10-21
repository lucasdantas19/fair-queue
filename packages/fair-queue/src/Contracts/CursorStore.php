<?php

namespace FairQueue\Contracts;

interface CursorStore
{
    public function get(string $key): ?string;

    public function put(string $key, string $value): void;
}
