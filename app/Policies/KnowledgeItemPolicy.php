<?php

namespace App\Policies;

use App\Models\KnowledgeItem;
use App\Models\User;

class KnowledgeItemPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, KnowledgeItem $knowledgeItem): bool
    {
        return $user->id === $knowledgeItem->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, KnowledgeItem $knowledgeItem): bool
    {
        return $user->id === $knowledgeItem->user_id;
    }

    public function delete(User $user, KnowledgeItem $knowledgeItem): bool
    {
        return $user->id === $knowledgeItem->user_id;
    }
}
