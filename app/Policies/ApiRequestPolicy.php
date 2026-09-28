<?php

namespace App\Policies;

use App\Models\ApiRequest;
use App\Models\User;

class ApiRequestPolicy
{
    public function view(User $user, ApiRequest $apiRequest): bool
    {
        return $user->id === $apiRequest->user_id;
    }

    public function delete(User $user, ApiRequest $apiRequest): bool
    {
        return $user->id === $apiRequest->user_id;
    }
}
