<?php

namespace App\Policies;

use App\Models\PackageOrder;
use App\Models\User;

class PackageOrderPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function approve(User $user, PackageOrder $order): bool
    {
        return $user->isSuperAdmin();
    }

    public function reject(User $user, PackageOrder $order): bool
    {
        return $user->isSuperAdmin();
    }
}
