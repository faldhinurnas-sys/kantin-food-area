<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop the old status column and create a new one to support 'pending'
        // Using DB statement for simplicity in SQLite/MySQL
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('status_new')->default('success')->after('type');
        });

        DB::statement('UPDATE transactions SET status_new = status');

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->renameColumn('status_new', 'status');
        });
    }

    public function down(): void
    {
        // Reverting back not strictly necessary for this context
    }
};
