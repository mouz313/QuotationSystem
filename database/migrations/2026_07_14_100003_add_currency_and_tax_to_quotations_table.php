<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->foreignId('currency_id')->nullable()->after('client_id')->constrained()->nullOnDelete();
            $table->foreignId('tax_id')->nullable()->after('currency_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropForeign(['currency_id']);
            $table->dropForeign(['tax_id']);
            $table->dropColumn(['currency_id', 'tax_id']);
        });
    }
};
