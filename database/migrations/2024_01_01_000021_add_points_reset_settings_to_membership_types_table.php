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
            $table->boolean('points_reset_after_redemption')->default(false)->after('consecutive_visit_bonus_rate');
            $table->integer('points_reset_threshold')->nullable()->after('points_reset_after_redemption');
            $table->text('points_reset_notes')->nullable()->after('points_reset_threshold');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('membership_types', function (Blueprint $table) {
            $table->dropColumn(['points_reset_after_redemption', 'points_reset_threshold', 'points_reset_notes']);
        });
    }
}; 