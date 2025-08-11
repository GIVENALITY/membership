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

            // Add new fields for two-step process
            $table->integer('number_of_people')->default(1)->after('member_id');
            $table->boolean('is_checked_out')->default(false)->after('notes');
            $table->decimal('amount_spent', 10, 2)->nullable()->after('is_checked_out');
            $table->decimal('discount_amount', 10, 2)->nullable()->after('amount_spent');
            $table->decimal('final_amount', 10, 2)->nullable()->after('discount_amount');
            $table->text('checkout_notes')->nullable()->after('final_amount');
            $table->timestamp('checked_out_at')->nullable()->after('checkout_notes');
            $table->foreignId('recorded_by')->nullable()->after('checked_out_at')->constrained('users')->onDelete('set null');
            $table->foreignId('checked_out_by')->nullable()->after('recorded_by')->constrained('users')->onDelete('set null');

            // Rename existing fields for clarity
            $table->renameColumn('bill_amount', 'amount_spent_old');
            $table->renameColumn('discount_rate', 'discount_rate_old');
            $table->renameColumn('visited_at', 'created_at_old');
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