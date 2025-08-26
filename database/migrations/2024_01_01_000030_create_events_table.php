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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->string('image')->nullable();
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->string('location')->nullable();
            $table->integer('max_capacity')->nullable();
            $table->decimal('price', 10, 2)->default(0.00);
            $table->boolean('is_public')->default(true);
            $table->boolean('is_active')->default(true);
            $table->enum('status', ['draft', 'published', 'cancelled', 'completed'])->default('draft');
            $table->json('settings')->nullable(); // For additional event settings
            $table->timestamps();
            
            $table->index(['hotel_id', 'status']);
            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
