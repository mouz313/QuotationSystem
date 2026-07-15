<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->string('payment_status')->default('unpaid')->after('status');
            $table->decimal('paid_amount', 10, 2)->nullable()->after('grand_total');
            $table->date('paid_at')->nullable()->after('paid_amount');
        });
    }

    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn(['payment_status', 'paid_amount', 'paid_at']);
        });
    }
};
