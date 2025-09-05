<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TestQRCode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:qrcode';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test QR code generation functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing QR Code Generation...');

        try {
            // Test 1: Check if package is available
            $this->info('1. Checking QR Code package availability...');
            if (!class_exists('SimpleSoftwareIO\QrCode\Facades\QrCode')) {
                throw new \Exception('QR Code package not available');
            }
            $this->info('✓ QR Code package is available');

            // Test 2: Check GD extension
            $this->info('2. Checking GD extension...');
            if (!extension_loaded('gd')) {
                throw new \Exception('GD extension is not available. Please install php-gd extension.');
            }
            $this->info('✓ GD extension is available');

            // Test 3: Basic QR code generation
            $this->info('3. Testing basic QR code generation...');
            $testQr = QrCode::format('png')
                ->size(100)
                ->margin(5)
                ->generate('test');
            $this->info('✓ Basic QR code generation successful');

            // Test 4: Storage permissions
            $this->info('4. Testing storage permissions...');
            $testFile = 'qr_codes/test_' . time() . '.txt';
            Storage::disk('public')->put($testFile, 'test content');
            Storage::disk('public')->delete($testFile);
            $this->info('✓ Storage permissions OK');

            // Test 5: Create qr_codes directory
            $this->info('5. Testing qr_codes directory creation...');
            $directory = 'qr_codes';
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory);
                $this->info('✓ Created qr_codes directory');
            } else {
                $this->info('✓ qr_codes directory already exists');
            }

            // Test 6: Generate and save actual QR code
            $this->info('6. Testing actual QR code file creation...');
            $qrCode = QrCode::format('png')
                ->size(300)
                ->margin(10)
                ->errorCorrection('H')
                ->generate('test data');
            
            $fileName = 'qr_codes/test_' . time() . '.png';
            Storage::disk('public')->put($fileName, $qrCode);
            $this->info('✓ QR code file created successfully: ' . $fileName);

            // Clean up test file
            Storage::disk('public')->delete($fileName);
            $this->info('✓ Test file cleaned up');

            $this->info('All tests passed! QR code generation is working correctly.');

        } catch (\Exception $e) {
            $this->error('Test failed: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return 1;
        }

        return 0;
    }
}
