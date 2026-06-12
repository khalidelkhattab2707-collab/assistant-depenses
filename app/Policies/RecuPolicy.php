<?php

namespace App\Policies;

use App\Models\Recu;
use App\Models\User;

class RecuPolicy
{
    public function view(User $user, Recu $recu): bool
    {
        return $user->id === $recu->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Recu $recu): bool
    {
        return $user->id === $recu->user_id;
    }

    public function delete(User $user, Recu $recu): bool
    {
        return $user->id === $recu->user_id;
    }
}
