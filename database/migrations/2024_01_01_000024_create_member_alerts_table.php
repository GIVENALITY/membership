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
        Schema::create('member_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', [
                'spending_threshold',
                'visit_frequency',
                'points_threshold',
                'birthday_approaching',
                'membership_expiry',
                'custom'
            ]);
            $table->json('conditions'); // Store alert conditions as JSON
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->boolean('is_active')->default(true);
            $table->boolean('send_email')->default(false);
            $table->boolean('show_dashboard')->default(true);
            $table->boolean('show_quickview')->default(true);
            $table->text('email_template')->nullable();
            $table->string('color')->default('#ffc107'); // Alert color
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('member_alert_triggers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_alert_id')->constrained()->onDelete('cascade');
            $table->foreignId('member_id')->constrained()->onDelete('cascade');
            $table->foreignId('hotel_id')->constrained()->onDelete('cascade');
            $table->json('trigger_data'); // Store the data that triggered the alert
            $table->enum('status', ['active', 'acknowledged', 'resolved'])->default('active');
            $table->timestamp('triggered_at');
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->foreignId('acknowledged_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_alert_triggers');
        Schema::dropIfExists('member_alerts');
    }
};
