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
        // First, let's check what columns exist and clean up any problematic ones
        Schema::table('dining_visits', function (Blueprint $table) {
            // Drop any old columns that might be causing issues
            $columnsToDrop = [
                'amount_spent_old',
                'discount_rate_old', 
                'created_at_old',
                'bill_amount',
                'discount_rate',
                'visited_at'
            ];
            
            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('dining_visits', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
        
        // Now ensure we have all the required columns with proper structure
        Schema::table('dining_visits', function (Blueprint $table) {
            // Ensure hotel_id exists
            if (!Schema::hasColumn('dining_visits', 'hotel_id')) {
                $table->foreignId('hotel_id')->after('id')->constrained()->onDelete('cascade');
            }
            
            // Ensure number_of_people exists
            if (!Schema::hasColumn('dining_visits', 'number_of_people')) {
                $table->integer('number_of_people')->default(1)->after('member_id');
            }
            
            // Ensure is_checked_out exists
            if (!Schema::hasColumn('dining_visits', 'is_checked_out')) {
                $table->boolean('is_checked_out')->default(false)->after('notes');
            }
            
            // Ensure amount_spent exists
            if (!Schema::hasColumn('dining_visits', 'amount_spent')) {
                $table->decimal('amount_spent', 10, 2)->nullable()->after('is_checked_out');
            }
            
            // Ensure discount_amount exists
            if (!Schema::hasColumn('dining_visits', 'discount_amount')) {
                $table->decimal('discount_amount', 10, 2)->nullable()->after('amount_spent');
            }
            
            // Ensure final_amount exists
            if (!Schema::hasColumn('dining_visits', 'final_amount')) {
                $table->decimal('final_amount', 10, 2)->nullable()->after('discount_amount');
            }
            
            // Ensure checkout_notes exists
            if (!Schema::hasColumn('dining_visits', 'checkout_notes')) {
                $table->text('checkout_notes')->nullable()->after('final_amount');
            }
            
            // Ensure checked_out_at exists
            if (!Schema::hasColumn('dining_visits', 'checked_out_at')) {
                $table->timestamp('checked_out_at')->nullable()->after('checkout_notes');
            }
            
            // Ensure recorded_by exists
            if (!Schema::hasColumn('dining_visits', 'recorded_by')) {
                $table->foreignId('recorded_by')->nullable()->after('checked_out_at')->constrained('users')->onDelete('set null');
            }
            
            // Ensure checked_out_by exists
            if (!Schema::hasColumn('dining_visits', 'checked_out_by')) {
                $table->foreignId('checked_out_by')->nullable()->after('recorded_by')->constrained('users')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is for cleanup, so we don't need to reverse it
    }
}; 