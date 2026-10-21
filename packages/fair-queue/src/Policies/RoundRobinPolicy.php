<?php

namespace FairQueue\Policies;

use FairQueue\Contracts\CursorStore;
use FairQueue\Contracts\FairnessPolicy;
use FairQueue\ValueObjects\GroupSnapshot;

final class RoundRobinPolicy implements FairnessPolicy
{
    public function __construct(
        private readonly CursorStore $cursorStore,
        private readonly string $cursorKey,
    ) {}

    public function select(iterable $groups): ?GroupSnapshot
    {
        $groups = array_values(is_array($groups) ? $groups : iterator_to_array($groups));

        if ($groups === []) {
            return null;
        }

        usort($groups, fn (GroupSnapshot $left, GroupSnapshot $right): int => [
            $left->oldestPendingAt,
            $left->oldestPendingId,
        ] <=> [
            $right->oldestPendingAt,
            $right->oldestPendingId,
        ]);

        $cursor = $this->cursorStore->get($this->cursorKey);
        $selectedIndex = 0;

        foreach ($groups as $index => $group) {
            if ($group->identifier() === $cursor) {
                $selectedIndex = ($index + 1) % count($groups);
                break;
            }
        }

        $selected = $groups[$selectedIndex];
        $this->cursorStore->put($this->cursorKey, $selected->identifier());

        return $selected;
    }
}
