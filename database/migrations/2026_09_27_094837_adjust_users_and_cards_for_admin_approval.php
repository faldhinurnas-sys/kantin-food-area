<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Adjust users table
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('pedagang', 'mahasiswa', 'wali', 'admin') DEFAULT 'mahasiswa'");
        }
        
        Schema::table('users', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'rejected', 'inactive'])->default('pending')->after('role');
        });

        // Adjust cards table
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE cards MODIFY COLUMN status ENUM('pending', 'active', 'inactive') DEFAULT 'pending'");
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('pedagang', 'mahasiswa', 'wali') DEFAULT 'mahasiswa'");
            DB::statement("ALTER TABLE cards MODIFY COLUMN status ENUM('active', 'inactive') DEFAULT 'active'");
        }
    }
};
