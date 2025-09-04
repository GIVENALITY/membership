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
            // Member approval workflow
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending')->after('status');
            $table->text('approval_notes')->nullable()->after('approval_status');
            $table->foreignId('approved_by')->nullable()->after('approval_notes')->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            
            // Payment verification
            $table->enum('payment_status', ['pending', 'verified', 'failed'])->default('pending')->after('approval_status');
            $table->string('payment_proof_path')->nullable()->after('payment_status');
            $table->text('payment_notes')->nullable()->after('payment_proof_path');
            $table->foreignId('payment_verified_by')->nullable()->after('payment_notes')->constrained('users')->onDelete('set null');
            $table->timestamp('payment_verified_at')->nullable()->after('payment_verified_by');
            
            // Card issuance workflow
            $table->enum('card_issuance_status', ['pending', 'approved', 'issued', 'delivered'])->default('pending')->after('payment_status');
            $table->text('card_issuance_notes')->nullable()->after('card_issuance_status');
            $table->foreignId('card_approved_by')->nullable()->after('card_issuance_notes')->constrained('users')->onDelete('set null');
            $table->timestamp('card_approved_at')->nullable()->after('card_approved_by');
            
            // QR Code for virtual cards
            $table->string('qr_code_path')->nullable()->after('card_image_path');
            $table->string('qr_code_data')->nullable()->after('qr_code_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropForeign(['approved_by', 'payment_verified_by', 'card_approved_by']);
            $table->dropColumn([
                'approval_status',
                'approval_notes',
                'approved_by',
                'approved_at',
                'payment_status',
                'payment_proof_path',
                'payment_notes',
                'payment_verified_by',
                'payment_verified_at',
                'card_issuance_status',
                'card_issuance_notes',
                'card_approved_by',
                'card_approved_at',
                'qr_code_path',
                'qr_code_data'
            ]);
        });
    }
};
