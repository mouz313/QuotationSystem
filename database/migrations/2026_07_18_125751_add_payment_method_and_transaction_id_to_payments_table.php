<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('payment_method')->nullable()->after('amount');
            $table->string('transaction_id')->nullable()->after('payment_method');
            $table->json('gateway_response')->nullable()->after('transaction_id');
            $table->string('paid_via')->default('manual')->after('gateway_response');
            $table->index(['quotation_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['quotation_id', 'status']);
            $table->dropColumn(['payment_method', 'transaction_id', 'gateway_response', 'paid_via']);
        });
    }
};
