<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\User;

class ClientPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Client $client): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $client->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->company && $user->company->isActive();
    }

    public function update(User $user, Client $client): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $client->user_id === $user->id;
    }

    public function delete(User $user, Client $client): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $client->user_id === $user->id;
    }
}
