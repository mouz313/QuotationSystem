<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\User;

class CompanyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function view(User $user, Company $company): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->company_id === $company->id;
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function update(User $user, Company $company): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->company_id === $company->id && $user->isCompanyAdmin();
    }

    public function delete(User $user, Company $company): bool
    {
        return $user->isSuperAdmin();
    }

    public function manageSettings(User $user, Company $company): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $user->company_id === $company->id && $user->isCompanyAdmin();
    }
}
