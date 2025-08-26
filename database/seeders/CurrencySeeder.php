<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Hotel;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update existing hotels with default currency settings
        Hotel::whereNull('currency')->update([
            'currency' => 'TZS',
            'currency_symbol' => 'TSh'
        ]);

        $this->command->info('Updated existing hotels with default currency settings (TZS).');
    }
}
