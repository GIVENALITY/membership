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
            // Add allergy and preference fields
            $table->text('allergies')->nullable()->after('email');
            $table->text('dietary_preferences')->nullable()->after('allergies');
            $table->text('special_requests')->nullable()->after('dietary_preferences');
            $table->text('additional_notes')->nullable()->after('special_requests');
            $table->string('emergency_contact_name')->nullable()->after('additional_notes');
            $table->string('emergency_contact_phone')->nullable()->after('emergency_contact_name');
            $table->string('emergency_contact_relationship')->nullable()->after('emergency_contact_phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn([
                'allergies',
                'dietary_preferences', 
                'special_requests',
                'additional_notes',
                'emergency_contact_name',
                'emergency_contact_phone',
                'emergency_contact_relationship'
            ]);
        });
    }
}; 