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
            // Only add columns that don't exist yet
            if (!Schema::hasColumn('hotels', 'reply_to_email')) {
                $table->string('reply_to_email')->nullable()->after('email');
            }
            if (!Schema::hasColumn('hotels', 'email_logo_url')) {
                $table->string('email_logo_url')->nullable()->after('reply_to_email');
            }
            if (!Schema::hasColumn('hotels', 'email_primary_color')) {
                $table->string('email_primary_color')->default('#1976d2')->after('email_logo_url');
            }
            if (!Schema::hasColumn('hotels', 'email_secondary_color')) {
                $table->string('email_secondary_color')->default('#f8f9fa')->after('email_primary_color');
            }
            if (!Schema::hasColumn('hotels', 'email_accent_color')) {
                $table->string('email_accent_color')->default('#2196f3')->after('email_secondary_color');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            $columnsToDrop = [];
            
            if (Schema::hasColumn('hotels', 'reply_to_email')) {
                $columnsToDrop[] = 'reply_to_email';
            }
            if (Schema::hasColumn('hotels', 'email_logo_url')) {
                $columnsToDrop[] = 'email_logo_url';
            }
            if (Schema::hasColumn('hotels', 'email_primary_color')) {
                $columnsToDrop[] = 'email_primary_color';
            }
            if (Schema::hasColumn('hotels', 'email_secondary_color')) {
                $columnsToDrop[] = 'email_secondary_color';
            }
            if (Schema::hasColumn('hotels', 'email_accent_color')) {
                $columnsToDrop[] = 'email_accent_color';
            }
            
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
