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
        Schema::table('dining_visits', function (Blueprint $table) {
            // Drop old temporary columns if they exist
            if (Schema::hasColumn('dining_visits', 'amount_spent_old')) {
                $table->dropColumn('amount_spent_old');
            }
            
            if (Schema::hasColumn('dining_visits', 'discount_rate_old')) {
                $table->dropColumn('discount_rate_old');
            }
            
            if (Schema::hasColumn('dining_visits', 'created_at_old')) {
                $table->dropColumn('created_at_old');
            }
            
            // Also drop the original old columns if they still exist
            if (Schema::hasColumn('dining_visits', 'bill_amount')) {
                $table->dropColumn('bill_amount');
            }
            
            if (Schema::hasColumn('dining_visits', 'discount_rate')) {
                $table->dropColumn('discount_rate');
            }
            
            if (Schema::hasColumn('dining_visits', 'visited_at')) {
                $table->dropColumn('visited_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We don't need to recreate these columns in the down method
        // as they were temporary columns used during the migration process
    }
}; 