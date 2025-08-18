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
        Schema::table('membership_types', function (Blueprint $table) {
            $table->string('card_template_image')->nullable()->after('sort_order');
            $table->json('card_field_mappings')->nullable()->after('card_template_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('membership_types', function (Blueprint $table) {
            $table->dropColumn(['card_template_image', 'card_field_mappings']);
        });
    }
};
