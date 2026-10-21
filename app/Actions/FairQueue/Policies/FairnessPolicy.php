<?php

namespace App\Actions\FairQueue\Policies;

use FairQueue\Contracts\QueueFairnessPolicy;

interface FairnessPolicy extends QueueFairnessPolicy
{
    /**
     * @param  list<array{group_key: string, group_value: string}>  $excludedGroups
     * @return array{group_key: string, group_value: string}|null
     */
    public function selectGroup(string $queue, array $excludedGroups = []): ?array;

    /**
     * @return list<string>
     */
    public function activeStatuses(): array;
}
