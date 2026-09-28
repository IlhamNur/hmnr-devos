<?php

namespace App\Policies;

use App\Models\MvelExecution;
use App\Models\User;

class MvelExecutionPolicy
{
    public function view(User $user, MvelExecution $mvelExecution): bool
    {
        return $user->id === $mvelExecution->user_id;
    }

    public function delete(User $user, MvelExecution $mvelExecution): bool
    {
        return $user->id === $mvelExecution->user_id;
    }
}
