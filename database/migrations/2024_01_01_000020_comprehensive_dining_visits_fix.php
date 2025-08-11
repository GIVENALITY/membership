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
        // First, let's check if the table exists and what columns it has
        if (!Schema::hasTable('dining_visits')) {
            // If table doesn't exist, create it with the correct structure
            Schema::create('dining_visits', function (Blueprint $table) {
                $table->id();
                $table->foreignId('hotel_id')->constrained()->onDelete('cascade');
                $table->foreignId('member_id')->constrained()->onDelete('cascade');
                $table->integer('number_of_people')->default(1);
                $table->text('notes')->nullable();
                $table->boolean('is_checked_out')->default(false);
                $table->decimal('amount_spent', 10, 2)->nullable();
                $table->decimal('discount_amount', 10, 2)->nullable();
                $table->decimal('final_amount', 10, 2)->nullable();
                $table->string('receipt_path')->nullable();
                $table->text('checkout_notes')->nullable();
                $table->timestamp('checked_out_at')->nullable();
                $table->foreignId('recorded_by')->nullable()->constrained('users')->onDelete('set null');
                $table->foreignId('checked_out_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();
            });
        } else {
            // Table exists, let's fix the structure
            Schema::table('dining_visits', function (Blueprint $table) {
                // Drop any problematic old columns
                $oldColumns = [
                    'amount_spent_old',
                    'discount_rate_old',
                    'created_at_old',
                    'bill_amount',
                    'discount_rate',
                    'visited_at'
                ];
                
                foreach ($oldColumns as $column) {
                    if (Schema::hasColumn('dining_visits', $column)) {
                        $table->dropColumn($column);
                    }
                }
                
                // Add missing columns if they don't exist
                if (!Schema::hasColumn('dining_visits', 'hotel_id')) {
                    $table->foreignId('hotel_id')->after('id')->constrained()->onDelete('cascade');
                }
                
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
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We can't easily reverse this migration as it's a structural fix
        // The table will remain in its fixed state
    }
}; 