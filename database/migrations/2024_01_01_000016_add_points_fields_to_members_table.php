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
            $table->integer('total_points_earned')->default(0)->after('current_discount_rate');
            $table->integer('total_points_used')->default(0)->after('total_points_earned');
            $table->integer('current_points_balance')->default(0)->after('total_points_used');
            $table->boolean('qualifies_for_discount')->default(false)->after('current_points_balance');
            $table->integer('consecutive_visits')->default(0)->after('qualifies_for_discount');
            $table->date('last_visit_date')->nullable()->after('consecutive_visits');
            $table->decimal('average_spending_per_visit', 10, 2)->default(0)->after('last_visit_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn([
                'total_points_earned',
                'total_points_used', 
                'current_points_balance',
                'qualifies_for_discount',
                'consecutive_visits',
                'last_visit_date',
                'average_spending_per_visit'
            ]);
        });
    }
}; 