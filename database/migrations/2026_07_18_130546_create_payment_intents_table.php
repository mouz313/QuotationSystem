<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_intents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quotation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('quotation_item_id')->nullable()->constrained('quotation_items')->nullOnDelete();
            $table->foreignId('client_user_id')->constrained('client_users')->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('currency_code', 3)->default('USD');
            $table->string('gateway'); // stripe, paypal
            $table->string('gateway_intent_id')->nullable();
            $table->string('status')->default('created'); // created, processing, completed, failed, cancelled
            $table->json('gateway_response')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['gateway', 'gateway_intent_id']);
            $table->index(['quotation_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_intents');
    }
};
