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
            // Add hotel_id for multi-hotel support
            $table->foreignId('hotel_id')->after('id')->constrained()->onDelete('cascade');
            
            // Add waiter checkout fields
            $table->foreignId('waiter_id')->nullable()->after('member_id')->constrained('users')->onDelete('set null');
            $table->text('waiter_notes')->nullable()->after('waiter_id');
            $table->timestamp('waiter_checkout_at')->nullable()->after('waiter_notes');
            
            // Add receipt attachment fields
            $table->string('receipt_path')->nullable()->after('notes');
            $table->text('receipt_notes')->nullable()->after('receipt_path');
            $table->foreignId('receipt_uploaded_by')->nullable()->after('receipt_notes')->constrained('users')->onDelete('set null');
            $table->timestamp('receipt_uploaded_at')->nullable()->after('receipt_uploaded_at');
            
            // Add checkout workflow fields
            $table->enum('checkout_status', ['checked_in', 'checked_out', 'cancelled'])->default('checked_in')->after('receipt_uploaded_at');
            $table->text('checkout_notes')->nullable()->after('checkout_status');
            $table->foreignId('checked_out_by')->nullable()->after('checkout_notes')->constrained('users')->onDelete('set null');
            $table->timestamp('checked_out_at')->nullable()->after('checked_out_by');
            
            // Add payment tracking
            $table->enum('payment_method', ['cash', 'card', 'mobile_money', 'bank_transfer'])->nullable()->after('checkout_status');
            $table->string('transaction_reference')->nullable()->after('payment_method');
            $table->text('payment_notes')->nullable()->after('transaction_reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dining_visits', function (Blueprint $table) {
            $table->dropForeign(['hotel_id', 'waiter_id', 'receipt_uploaded_by', 'checked_out_by']);
            $table->dropColumn([
                'hotel_id',
                'waiter_id',
                'waiter_notes',
                'waiter_checkout_at',
                'receipt_path',
                'receipt_notes',
                'receipt_uploaded_by',
                'receipt_uploaded_at',
                'checkout_status',
                'checkout_notes',
                'checked_out_by',
                'checked_out_at',
                'payment_method',
                'transaction_reference',
                'payment_notes'
            ]);
        });
    }
};
