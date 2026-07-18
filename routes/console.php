<?php

use App\Console\Commands\ExpirePackageSubscriptions;
use App\Console\Commands\ExpireQuotations;
use Illuminate\Support\Facades\Schedule;

Schedule::command('quotations:expire')->daily();
Schedule::command('packages:expire')->daily();
