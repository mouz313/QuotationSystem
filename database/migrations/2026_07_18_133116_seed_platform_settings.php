<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $defaults = [
            'pagination_per_page'   => '15',
            'pagination_client'     => '12',
            'pagination_activity'   => '25',
            'dashboard_limit'       => '10',
            'notification_limit'    => '15',
            'max_file_size_attachments' => '10240',
            'max_file_size_payment_proof' => '5120',
            'max_file_size_logo'    => '2048',
            'max_attachments_per_quotation' => '5',
        ];

        foreach ($defaults as $key => $value) {
            Setting::set($key, $value);
            Setting::where('key', $key)->update(['group' => 'platform']);
        }
    }

    public function down(): void
    {
        Setting::whereIn('key', [
            'pagination_per_page',
            'pagination_client',
            'pagination_activity',
            'dashboard_limit',
            'notification_limit',
            'max_file_size_attachments',
            'max_file_size_payment_proof',
            'max_file_size_logo',
            'max_attachments_per_quotation',
        ])->delete();
    }
};
