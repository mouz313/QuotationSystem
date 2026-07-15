<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->timestamp('viewed_at')->nullable()->after('terms_conditions');
        });

        DB::statement("ALTER TABLE quotations MODIFY COLUMN status ENUM('draft','sent','opened','change_requested','accepted','declined') NOT NULL DEFAULT 'draft'");
    }

    public function down(): void
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn('viewed_at');
        });

        DB::statement("ALTER TABLE quotations MODIFY COLUMN status ENUM('draft','sent','accepted','declined') NOT NULL DEFAULT 'draft'");
    }
};
