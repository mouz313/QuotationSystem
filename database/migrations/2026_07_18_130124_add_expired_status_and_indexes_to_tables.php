<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE quotations MODIFY COLUMN status ENUM('draft','sent','opened','accepted','declined','change_requested','expired') NOT NULL DEFAULT 'draft'");

        Schema::table('quotations', function (Blueprint $table) {
            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'created_at']);
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->index('group');
        });
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE quotations MODIFY COLUMN status ENUM('draft','sent','opened','accepted','declined','change_requested') NOT NULL DEFAULT 'draft'");

        Schema::table('quotations', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'status']);
            $table->dropIndex(['user_id', 'created_at']);
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->dropIndex(['group']);
        });
    }
};
