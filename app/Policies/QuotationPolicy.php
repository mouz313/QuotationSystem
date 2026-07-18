<?php

namespace App\Policies;

use App\Models\Quotation;
use App\Models\User;

class QuotationPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Quotation $quotation): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $quotation->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->company && $user->company->isActive();
    }

    public function update(User $user, Quotation $quotation): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $quotation->user_id === $user->id;
    }

    public function delete(User $user, Quotation $quotation): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $quotation->user_id === $user->id;
    }
}
