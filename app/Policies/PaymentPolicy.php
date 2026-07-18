<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function approve(User $user, Payment $payment): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $payment->quotation->user_id === $user->id;
    }

    public function reject(User $user, Payment $payment): bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }

        return $payment->quotation->user_id === $user->id;
    }

    public function bulkApprove(User $user): bool
    {
        return true;
    }

    public function bulkReject(User $user): bool
    {
        return true;
    }
}
