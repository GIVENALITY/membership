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
            $table->string('slug')->nullable()->after('name');
        });

        // Update existing hotels with slugs
        $hotels = DB::table('hotels')->get();
        foreach ($hotels as $hotel) {
            $slug = \Str::slug($hotel->name);
            $originalSlug = $slug;
            $counter = 1;
            
            // Ensure unique slug
            while (DB::table('hotels')->where('slug', $slug)->where('id', '!=', $hotel->id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
            
            DB::table('hotels')->where('id', $hotel->id)->update(['slug' => $slug]);
        }

        // Now make the column unique and not nullable
        Schema::table('hotels', function (Blueprint $table) {
            $table->string('slug')->unique()->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hotels', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
