<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Only update if the role column exists
        if (Schema::hasColumn('users', 'role')) {
            DB::table('users')->update([
                'role' => 'manager'
            ]);
        }
        
        // Only update if the is_active column exists
        if (Schema::hasColumn('users', 'is_active')) {
            DB::table('users')->update([
                'is_active' => true
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration cannot be safely reversed
    }
}; 