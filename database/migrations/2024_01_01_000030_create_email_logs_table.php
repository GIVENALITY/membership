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
        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained()->onDelete('cascade');
            $table->string('email_type'); // 'welcome', 'member_email', 'event_notification', etc.
            $table->string('subject');
            $table->text('content');
            $table->string('recipient_email');
            $table->string('recipient_name')->nullable();
            $table->foreignId('member_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('status'); // 'sent', 'delivered', 'failed', 'bounced', 'opened'
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('bounced_at')->nullable();
            $table->string('message_id')->nullable(); // Email provider message ID
            $table->json('metadata')->nullable(); // Additional tracking data
            $table->timestamps();
            
            $table->index(['hotel_id', 'status']);
            $table->index(['hotel_id', 'email_type']);
            $table->index(['hotel_id', 'sent_at']);
            $table->index(['member_id']);
            $table->index(['recipient_email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_logs');
    }
};
