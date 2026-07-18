<?php

namespace App\Console\Commands;

use App\Mail\QuotationStatusMail;
use App\Models\Notification;
use App\Models\Quotation;
use App\Models\QuotationStatusLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class ExpireQuotations extends Command
{
    protected $signature = 'quotations:expire';
    protected $description = 'Expire quotations past their expiry date';

    public function handle(): int
    {
        $expired = Quotation::where('expiry_date', '<', now()->toDateString())
            ->whereIn('status', ['sent', 'opened'])
            ->get();

        $count = 0;

        foreach ($expired as $quotation) {
            $oldStatus = $quotation->status;
            $quotation->update(['status' => 'expired']);

            QuotationStatusLog::create([
                'quotation_id'    => $quotation->id,
                'from_status'     => $oldStatus,
                'to_status'       => 'expired',
                'changed_by_type' => null,
                'changed_by_id'   => null,
                'notes'           => 'Auto-expired: past expiry date',
            ]);

            $clientEmail = $quotation->client->clientUser?->email ?? $quotation->client->email;
            if ($clientEmail) {
                try {
                    Mail::to($clientEmail)->send(
                        new QuotationStatusMail(
                            $quotation,
                            $oldStatus,
                            'expired',
                            'This quotation has expired as of ' . $quotation->expiry_date->format('d M Y') . '.',
                            'System',
                            'client',
                        )
                    );
                } catch (\Exception $e) {
                    \Log::warning('Email failed (auto-expire): ' . $e->getMessage());
                }
            }

            Notification::create([
                'user_id' => $quotation->user_id,
                'type'    => 'quotation_expired',
                'message' => "Quotation {$quotation->quote_number} has expired.",
                'url'     => "/quotations/{$quotation->id}",
            ]);

            $count++;
        }

        $this->info("Expired {$count} quotation(s).");
        return Command::SUCCESS;
    }
}
