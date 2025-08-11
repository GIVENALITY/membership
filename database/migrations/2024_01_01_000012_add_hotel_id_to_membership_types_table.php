<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, add the column as nullable
        Schema::table('membership_types', function (Blueprint $table) {
            $table->foreignId('hotel_id')->nullable()->after('id');
        });

        // Get the default hotel (created in previous migration)
        $defaultHotelId = DB::table('hotels')->where('email', 'default@hotel.com')->value('id');
        
        if (!$defaultHotelId) {
            // Create a default hotel if it doesn't exist
            $defaultHotelId = DB::table('hotels')->insertGetId([
                'name' => 'Default Hotel',
                'email' => 'default@hotel.com',
                'phone' => '+255000000000',
                'address' => 'Default Address',
                'city' => 'Dar es Salaam',
                'country' => 'Tanzania',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Update existing membership types to use the default hotel
        DB::table('membership_types')->whereNull('hotel_id')->update([
            'hotel_id' => $defaultHotelId
        ]);

        // Now make the column required and add foreign key constraint
        Schema::table('membership_types', function (Blueprint $table) {
            $table->foreignId('hotel_id')->nullable(false)->change();
            $table->foreign('hotel_id')->references('id')->on('hotels')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('membership_types', function (Blueprint $table) {
            $table->dropForeign(['hotel_id']);
            $table->dropColumn('hotel_id');
        });
    }
}; 