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
        Schema::table('hotels', function (Blueprint $table) {
            $table->string('email')->nullable()->after('currency_symbol');
            $table->string('reply_to_email')->nullable()->after('email');
            $table->string('email_logo_url')->nullable()->after('reply_to_email');
            $table->string('email_primary_color')->default('#1976d2')->after('email_logo_url');
            $table->string('email_secondary_color')->default('#f8f9fa')->after('email_primary_color');
            $table->string('email_accent_color')->default('#2196f3')->after('email_secondary_color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->dropColumn([
                'email',
                'reply_to_email', 
                'email_logo_url',
                'email_primary_color',
                'email_secondary_color',
                'email_accent_color'
            ]);
        });
    }
};
