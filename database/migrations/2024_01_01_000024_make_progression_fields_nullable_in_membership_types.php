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
        Schema::table('membership_types', function (Blueprint $table) {
            // Make progression fields nullable
            $table->decimal('birthday_discount_rate', 5, 2)->nullable()->change();
            $table->integer('consecutive_visits_for_bonus')->nullable()->change();
            $table->decimal('consecutive_visit_bonus_rate', 5, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('membership_types', function (Blueprint $table) {
            // Revert to not nullable with defaults
            $table->decimal('birthday_discount_rate', 5, 2)->default(25.00)->change();
            $table->integer('consecutive_visits_for_bonus')->default(5)->change();
            $table->decimal('consecutive_visit_bonus_rate', 5, 2)->default(20.00)->change();
        });
    }
};
