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
        Schema::create('event_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('member_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('registration_code')->unique(); // For tracking registrations
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->integer('number_of_guests')->default(1);
            $table->decimal('total_amount', 10, 2)->default(0.00);
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'attended'])->default('pending');
            $table->text('special_requests')->nullable();
            $table->json('guest_details')->nullable(); // For storing additional guest information
            $table->timestamp('registered_at');
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
            
            $table->index(['event_id', 'status']);
            $table->index(['member_id', 'status']);
            $table->index('registration_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_registrations');
    }
};
