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
            // Add hotel_id for multi-hotel support (only if it doesn't exist)
            if (!Schema::hasColumn('dining_visits', 'hotel_id')) {
                $table->foreignId('hotel_id')->after('id')->constrained()->onDelete('cascade');
            }
            
            // Add waiter checkout fields
            if (!Schema::hasColumn('dining_visits', 'waiter_id')) {
                $table->foreignId('waiter_id')->nullable()->after('member_id')->constrained('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('dining_visits', 'waiter_notes')) {
                $table->text('waiter_notes')->nullable()->after('waiter_id');
            }
            if (!Schema::hasColumn('dining_visits', 'waiter_checkout_at')) {
                $table->timestamp('waiter_checkout_at')->nullable()->after('waiter_notes');
            }
            
            // Add receipt attachment fields
            if (!Schema::hasColumn('dining_visits', 'receipt_path')) {
                $table->string('receipt_path')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('dining_visits', 'receipt_notes')) {
                $table->text('receipt_notes')->nullable()->after('receipt_path');
            }
            if (!Schema::hasColumn('dining_visits', 'receipt_uploaded_by')) {
                $table->foreignId('receipt_uploaded_by')->nullable()->after('receipt_notes')->constrained('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('dining_visits', 'receipt_uploaded_at')) {
                $table->timestamp('receipt_uploaded_at')->nullable()->after('receipt_uploaded_by');
            }
            
            // Add checkout workflow fields
            if (!Schema::hasColumn('dining_visits', 'checkout_status')) {
                $table->enum('checkout_status', ['checked_in', 'checked_out', 'cancelled'])->default('checked_in')->after('receipt_uploaded_at');
            }
            if (!Schema::hasColumn('dining_visits', 'checkout_notes')) {
                $table->text('checkout_notes')->nullable()->after('checkout_status');
            }
            if (!Schema::hasColumn('dining_visits', 'checked_out_by')) {
                $table->foreignId('checked_out_by')->nullable()->after('checkout_notes')->constrained('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('dining_visits', 'checked_out_at')) {
                $table->timestamp('checked_out_at')->nullable()->after('checked_out_by');
            }
            
            // Add payment tracking
            if (!Schema::hasColumn('dining_visits', 'payment_method')) {
                $table->enum('payment_method', ['cash', 'card', 'mobile_money', 'bank_transfer'])->nullable()->after('checkout_status');
            }
            if (!Schema::hasColumn('dining_visits', 'transaction_reference')) {
                $table->string('transaction_reference')->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('dining_visits', 'payment_notes')) {
                $table->text('payment_notes')->nullable()->after('transaction_reference');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dining_visits', function (Blueprint $table) {
            // Drop foreign keys only if they exist
            $foreignKeys = [];
            if (Schema::hasColumn('dining_visits', 'hotel_id')) $foreignKeys[] = 'hotel_id';
            if (Schema::hasColumn('dining_visits', 'waiter_id')) $foreignKeys[] = 'waiter_id';
            if (Schema::hasColumn('dining_visits', 'receipt_uploaded_by')) $foreignKeys[] = 'receipt_uploaded_by';
            if (Schema::hasColumn('dining_visits', 'checked_out_by')) $foreignKeys[] = 'checked_out_by';
            
            if (!empty($foreignKeys)) {
                $table->dropForeign($foreignKeys);
            }
            
            // Drop columns only if they exist
            $columnsToDrop = [];
            if (Schema::hasColumn('dining_visits', 'hotel_id')) $columnsToDrop[] = 'hotel_id';
            if (Schema::hasColumn('dining_visits', 'waiter_id')) $columnsToDrop[] = 'waiter_id';
            if (Schema::hasColumn('dining_visits', 'waiter_notes')) $columnsToDrop[] = 'waiter_notes';
            if (Schema::hasColumn('dining_visits', 'waiter_checkout_at')) $columnsToDrop[] = 'waiter_checkout_at';
            if (Schema::hasColumn('dining_visits', 'receipt_path')) $columnsToDrop[] = 'receipt_path';
            if (Schema::hasColumn('dining_visits', 'receipt_notes')) $columnsToDrop[] = 'receipt_notes';
            if (Schema::hasColumn('dining_visits', 'receipt_uploaded_by')) $columnsToDrop[] = 'receipt_uploaded_by';
            if (Schema::hasColumn('dining_visits', 'receipt_uploaded_at')) $columnsToDrop[] = 'receipt_uploaded_at';
            if (Schema::hasColumn('dining_visits', 'checkout_status')) $columnsToDrop[] = 'checkout_status';
            if (Schema::hasColumn('dining_visits', 'checkout_notes')) $columnsToDrop[] = 'checkout_notes';
            if (Schema::hasColumn('dining_visits', 'checked_out_by')) $columnsToDrop[] = 'checked_out_by';
            if (Schema::hasColumn('dining_visits', 'checked_out_at')) $columnsToDrop[] = 'checked_out_at';
            if (Schema::hasColumn('dining_visits', 'payment_method')) $columnsToDrop[] = 'payment_method';
            if (Schema::hasColumn('dining_visits', 'transaction_reference')) $columnsToDrop[] = 'transaction_reference';
            if (Schema::hasColumn('dining_visits', 'payment_notes')) $columnsToDrop[] = 'payment_notes';
            
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
