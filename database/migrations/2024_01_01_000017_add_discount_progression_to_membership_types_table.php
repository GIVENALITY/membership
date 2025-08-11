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
            $table->json('discount_progression')->nullable()->after('discount_rate');
            $table->integer('points_required_for_discount')->default(5)->after('discount_progression');
            $table->boolean('has_special_birthday_discount')->default(true)->after('points_required_for_discount');
            $table->decimal('birthday_discount_rate', 5, 2)->default(25.00)->after('has_special_birthday_discount');
            $table->boolean('has_consecutive_visit_bonus')->default(true)->after('birthday_discount_rate');
            $table->integer('consecutive_visits_for_bonus')->default(5)->after('has_consecutive_visit_bonus');
            $table->decimal('consecutive_visit_bonus_rate', 5, 2)->default(20.00)->after('consecutive_visits_for_bonus');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('membership_types', function (Blueprint $table) {
            $table->dropColumn([
                'discount_progression',
                'points_required_for_discount',
                'has_special_birthday_discount',
                'birthday_discount_rate',
                'has_consecutive_visit_bonus',
                'consecutive_visits_for_bonus',
                'consecutive_visit_bonus_rate'
            ]);
        });
    }
}; 