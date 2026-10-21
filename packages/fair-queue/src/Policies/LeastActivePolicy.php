<?php

namespace FairQueue\Policies;

use FairQueue\Contracts\FairnessPolicy;
use FairQueue\ValueObjects\GroupSnapshot;

final class LeastActivePolicy implements FairnessPolicy
{
    public function __construct(private readonly int $agingSeconds = 0) {}

    public function select(iterable $groups): ?GroupSnapshot
    {
        $groups = array_values(is_array($groups) ? $groups : iterator_to_array($groups));

        if ($groups === []) {
            return null;
        }

        usort($groups, function (GroupSnapshot $left, GroupSnapshot $right): int {
            $scoreComparison = $this->score($left) <=> $this->score($right);

            if ($scoreComparison !== 0) {
                return $scoreComparison;
            }

            return [$left->activeJobs, $left->oldestPendingAt, $left->oldestPendingId]
                <=> [$right->activeJobs, $right->oldestPendingAt, $right->oldestPendingId];
        });

        return $groups[0];
    }

    private function score(GroupSnapshot $group): int
    {
        if ($this->agingSeconds <= 0) {
            return $group->activeJobs;
        }

        $age = max(0, time() - $group->oldestPendingAt->getTimestamp());

        return $group->activeJobs - intdiv($age, $this->agingSeconds);
    }
}
