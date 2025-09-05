<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            // Change qr_code_data column to TEXT to accommodate larger JSON data
            $table->text('qr_code_data')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            // Revert back to smaller size (assuming it was VARCHAR before)
            $table->string('qr_code_data', 500)->nullable()->change();
        });
    }
};
