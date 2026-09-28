<?php

namespace App\Policies;

use App\Models\Problem;
use App\Models\User;

class ProblemPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Problem $problem): bool
    {
        return $user->id === $problem->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Problem $problem): bool
    {
        return $user->id === $problem->user_id;
    }

    public function delete(User $user, Problem $problem): bool
    {
        return $user->id === $problem->user_id;
    }
}
