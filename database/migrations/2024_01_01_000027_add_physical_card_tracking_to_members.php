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
            $table->enum('physical_card_status', ['not_issued', 'issued', 'delivered', 'lost', 'replaced'])->default('not_issued')->after('card_image_path');
            $table->date('physical_card_issued_date')->nullable()->after('physical_card_status');
            $table->string('physical_card_issued_by')->nullable()->after('physical_card_issued_date');
            $table->text('physical_card_notes')->nullable()->after('physical_card_issued_by');
            $table->date('physical_card_delivered_date')->nullable()->after('physical_card_notes');
            $table->string('physical_card_delivered_by')->nullable()->after('physical_card_delivered_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn([
                'physical_card_status',
                'physical_card_issued_date',
                'physical_card_issued_by',
                'physical_card_notes',
                'physical_card_delivered_date',
                'physical_card_delivered_by'
            ]);
        });
    }
};
