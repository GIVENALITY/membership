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
        Schema::create('points_configurations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', [
                'dining_visit',
                'special_event',
                'referral',
                'social_media',
                'birthday_bonus',
                'holiday_bonus',
                'custom'
            ]);
            $table->json('rules'); // Store earning rules as JSON
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('points_multipliers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained()->onDelete('cascade');
            $table->foreignId('membership_type_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('multiplier_type', [
                'membership_type',
                'visit_frequency',
                'spending_tier',
                'time_based',
                'custom'
            ]);
            $table->decimal('multiplier_value', 5, 2)->default(1.00); // e.g., 1.5x, 2.0x
            $table->json('conditions'); // Store multiplier conditions as JSON
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('points_tiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hotel_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('min_points');
            $table->integer('max_points')->nullable();
            $table->decimal('multiplier', 5, 2)->default(1.00);
            $table->json('benefits')->nullable(); // Store tier benefits as JSON
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('points_tiers');
        Schema::dropIfExists('points_multipliers');
        Schema::dropIfExists('points_configurations');
    }
};
