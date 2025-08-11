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
        Schema::create('member_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->onDelete('cascade');
            $table->foreignId('hotel_id')->constrained()->onDelete('cascade');
            $table->foreignId('dining_visit_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('points_earned')->default(0);
            $table->integer('points_used')->default(0);
            $table->integer('points_balance')->default(0);
            $table->decimal('spending_amount', 10, 2)->default(0);
            $table->integer('number_of_people')->default(1);
            $table->decimal('per_person_spending', 10, 2)->default(0);
            $table->boolean('qualifies_for_discount')->default(false);
            $table->boolean('is_birthday_visit')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['member_id', 'hotel_id']);
            $table->index(['hotel_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_points');
    }
}; 