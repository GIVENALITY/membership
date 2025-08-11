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
            // Add hotel_id if not exists
            if (!Schema::hasColumn('dining_visits', 'hotel_id')) {
                $table->foreignId('hotel_id')->after('id')->constrained()->onDelete('cascade');
            }

            // Add new fields for two-step process (check if they exist first)
            if (!Schema::hasColumn('dining_visits', 'number_of_people')) {
                $table->integer('number_of_people')->default(1)->after('member_id');
            }
            
            if (!Schema::hasColumn('dining_visits', 'is_checked_out')) {
                $table->boolean('is_checked_out')->default(false)->after('notes');
            }
            
            if (!Schema::hasColumn('dining_visits', 'amount_spent')) {
                $table->decimal('amount_spent', 10, 2)->nullable()->after('is_checked_out');
            }
            
            if (!Schema::hasColumn('dining_visits', 'discount_amount')) {
                $table->decimal('discount_amount', 10, 2)->nullable()->after('amount_spent');
            }
            
            if (!Schema::hasColumn('dining_visits', 'final_amount')) {
                $table->decimal('final_amount', 10, 2)->nullable()->after('discount_amount');
            }
            
            if (!Schema::hasColumn('dining_visits', 'checkout_notes')) {
                $table->text('checkout_notes')->nullable()->after('final_amount');
            }
            
            if (!Schema::hasColumn('dining_visits', 'checked_out_at')) {
                $table->timestamp('checked_out_at')->nullable()->after('checkout_notes');
            }
            
            if (!Schema::hasColumn('dining_visits', 'recorded_by')) {
                $table->foreignId('recorded_by')->nullable()->after('checked_out_at')->constrained('users')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('dining_visits', 'checked_out_by')) {
                $table->foreignId('checked_out_by')->nullable()->after('recorded_by')->constrained('users')->onDelete('set null');
            }

            // Rename existing fields for clarity (only if they exist and haven't been renamed)
            if (Schema::hasColumn('dining_visits', 'bill_amount') && !Schema::hasColumn('dining_visits', 'amount_spent_old')) {
                $table->renameColumn('bill_amount', 'amount_spent_old');
            }
            
            if (Schema::hasColumn('dining_visits', 'discount_rate') && !Schema::hasColumn('dining_visits', 'discount_rate_old')) {
                $table->renameColumn('discount_rate', 'discount_rate_old');
            }
            
            if (Schema::hasColumn('dining_visits', 'visited_at') && !Schema::hasColumn('dining_visits', 'created_at_old')) {
                $table->renameColumn('visited_at', 'created_at_old');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dining_visits', function (Blueprint $table) {
            // Drop new fields
            $table->dropForeign(['hotel_id', 'recorded_by', 'checked_out_by']);
            $table->dropColumn([
                'hotel_id', 'number_of_people', 'is_checked_out', 'amount_spent',
                'discount_amount', 'final_amount', 'checkout_notes', 'checked_out_at',
                'recorded_by', 'checked_out_by'
            ]);

            // Rename back
            $table->renameColumn('amount_spent_old', 'bill_amount');
            $table->renameColumn('discount_rate_old', 'discount_rate');
            $table->renameColumn('created_at_old', 'visited_at');
        });
    }
}; 