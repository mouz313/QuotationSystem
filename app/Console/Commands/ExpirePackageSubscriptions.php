<?php

namespace App\Console\Commands;

use App\Mail\PackageExpiryReminderMail;
use App\Models\CompanyPackage;
use App\Models\Notification;
use Illuminate\Console\Command;

class ExpirePackageSubscriptions extends Command
{
    protected $signature = 'packages:expire';
    protected $description = 'Expire package subscriptions past their end date and send reminder emails';

    public function handle(): int
    {
        $expiredCount = CompanyPackage::where('status', 'active')
            ->where('end_date', '<', now()->toDateString())
            ->update(['status' => 'expired']);

        $this->info("Marked {$expiredCount} package(s) as expired.");

        $expiringIn7Days = CompanyPackage::where('status', 'active')
            ->whereBetween('end_date', [now()->toDateString(), now()->addDays(7)->toDateString()])
            ->with(['company', 'package'])
            ->get();

        foreach ($expiringIn7Days as $cp) {
            $admin = $cp->company->users()->where('role', 'company_admin')->first();
            if (!$admin) continue;

            $daysLeft = now()->diffInDays($cp->end_date, false);

            Notification::create([
                'user_id' => $admin->id,
                'type'    => 'package_expiry_warning',
                'message' => "Your package '{$cp->package->name}' expires in {$daysLeft} day(s). Please renew to avoid service interruption.",
                'url'     => '/company/settings',
            ]);

            try {
                Mail::to($admin->email)->send(
                    new PackageExpiryReminderMail($cp->company, $cp->package, (int) $daysLeft)
                );
            } catch (\Exception $e) {
                \Log::warning('Package expiry reminder email failed: ' . $e->getMessage());
            }
        }

        $this->info("Sent " . $expiringIn7Days->count() . " expiry reminder(s).");
        return Command::SUCCESS;
    }
}
