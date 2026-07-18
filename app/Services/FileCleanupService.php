<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Company;
use App\Models\Payment;
use App\Models\Quotation;
use App\Models\QuotationAttachment;
use Illuminate\Support\Facades\Storage;

class FileCleanupService
{
    public static function deleteQuotationFiles(Quotation $quotation): void
    {
        $quotation->load('attachments');

        foreach ($quotation->attachments as $attachment) {
            $path = 'quotation-attachments/' . $attachment->filename;
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }

        $quotation->load('payments');
        foreach ($quotation->payments as $payment) {
            static::deletePaymentProof($payment);
        }
    }

    public static function deleteQuotationAttachment(QuotationAttachment $attachment): void
    {
        $path = 'quotation-attachments/' . $attachment->filename;
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    public static function deleteAttachmentsByIds(array $attachmentIds): void
    {
        $attachments = QuotationAttachment::whereIn('id', $attachmentIds)->get();
        foreach ($attachments as $attachment) {
            static::deleteQuotationAttachment($attachment);
        }
    }

    public static function deletePaymentProof(Payment $payment): void
    {
        if ($payment->proof) {
            $path = 'payment-proofs/' . basename($payment->proof);
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }
    }

    public static function deleteCompanyFiles(Company $company): void
    {
        if ($company->logo) {
            $path = $company->logo;
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }

        $company->load('users.quotations');
        foreach ($company->users as $user) {
            foreach ($user->quotations as $quotation) {
                static::deleteQuotationFiles($quotation);
            }
        }
    }

    public static function deleteOrphanedFiles(): int
    {
        $deleted = 0;
        $directories = ['quotation-attachments', 'payment-proofs', 'logos', 'settings'];

        foreach ($directories as $directory) {
            if (!Storage::disk('public')->exists($directory)) {
                continue;
            }

            $files = Storage::disk('public')->files($directory);
            foreach ($files as $file) {
                $filename = basename($file);

                $isUsed = static::isFileReferenced($directory, $filename);
                if (!$isUsed) {
                    Storage::disk('public')->delete($file);
                    $deleted++;
                }
            }
        }

        return $deleted;
    }

    private static function isFileReferenced(string $directory, string $filename): bool
    {
        return match ($directory) {
            'quotation-attachments' => QuotationAttachment::where('filename', $filename)->exists(),
            'payment-proofs' => Payment::where('proof', 'like', "%{$filename}%")->exists(),
            default => true,
        };
    }
}
