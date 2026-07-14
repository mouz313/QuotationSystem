<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('admin', function ($user) {
    return $user->isSuperAdmin();
});

Broadcast::channel('company.{companyId}', function ($user, $companyId) {
    return $user->company_id == $companyId;
});
