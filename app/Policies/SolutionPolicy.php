<?php

namespace App\Policies;

use App\Models\Solution;
use App\Models\User;

class SolutionPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Solution $solution): bool
    {
        return $user->id === $solution->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Solution $solution): bool
    {
        return $user->id === $solution->user_id;
    }

    public function delete(User $user, Solution $solution): bool
    {
        return $user->id === $solution->user_id;
    }
}
