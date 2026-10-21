<?php

namespace FairQueue\Contracts;

use FairQueue\ValueObjects\GroupSnapshot;

interface FairnessPolicy
{
    /**
     * @param  iterable<GroupSnapshot>  $groups
     */
    public function select(iterable $groups): ?GroupSnapshot;
}
