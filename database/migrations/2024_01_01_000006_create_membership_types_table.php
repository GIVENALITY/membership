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
        Schema::create('membership_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Basic, Premium, VIP, etc.
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2); // Price in TZS
            $table->enum('billing_cycle', ['monthly', 'yearly'])->default('yearly');
            $table->json('perks'); // Array of perks/benefits
            $table->integer('max_visits_per_month')->nullable(); // Unlimited if null
            $table->decimal('discount_rate', 5, 2)->default(5.00); // Base discount rate
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0); // For ordering in lists
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membership_types');
    }
}; 