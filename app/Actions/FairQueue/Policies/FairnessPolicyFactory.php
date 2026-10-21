<?php

namespace App\Actions\FairQueue\Policies;

use InvalidArgumentException;

class FairnessPolicyFactory
{
    public function make(string $queue): FairnessPolicy
    {
        $name = config("fair_queue.queues.{$queue}.policy", config('fair_queue.default_policy', 'least_active'));
        $policyClass = config("fair_queue.policies.{$name}");

        if (! is_string($policyClass) || ! is_a($policyClass, FairnessPolicy::class, true)) {
            throw new InvalidArgumentException("Fair Queue policy [{$name}] is not configured.");
        }

        return app($policyClass);
    }
}
