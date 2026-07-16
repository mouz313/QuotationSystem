<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->enum('type', ['simple', 'milestone'])->default('simple')->after('quote_number');
        });

        Schema::table('quotation_items', function (Blueprint $table) {
            $table->date('start_date')->nullable()->after('subtotal');
            $table->date('end_date')->nullable()->after('start_date');
            $table->integer('sort_order')->default(0)->after('end_date');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('quotation_item_id')->nullable()->after('quotation_id')->constrained('quotation_items')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['quotation_item_id']);
            $table->dropColumn('quotation_item_id');
        });

        Schema::table('quotation_items', function (Blueprint $table) {
            $table->dropColumn(['start_date', 'end_date', 'sort_order']);
        });

        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
