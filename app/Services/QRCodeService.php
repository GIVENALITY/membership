<?php

namespace App\Services;

use App\Models\Member;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use SimpleSoftwareIO\QrCode\Generator;

class QRCodeService
{
    /**
     * Generate QR code for a member
     */
    public function generateForMember(Member $member): string
    {
        try {
            // Generate QR code data
            $qrData = $this->generateQRData($member);
            
            // Ensure qr_codes directory exists
            $directory = 'qr_codes';
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory);
            }
            
            // Generate QR code image using the installed package (now that ImageMagick is enabled)
            $qrCode = QrCode::format('png')
                ->size(300)
                ->margin(10)
                ->errorCorrection('H')
                ->generate($qrData);
            
            // Store QR code image
            $fileName = $directory . '/' . $member->membership_id . '_' . time() . '.png';
            Storage::disk('public')->put($fileName, $qrCode);
            
            // Update member with QR code information
            $member->update([
                'qr_code_path' => $fileName,
                'qr_code_data' => $qrData,
            ]);
            
            \Log::info('QR code generated successfully using package', [
                'member_id' => $member->id,
                'membership_id' => $member->membership_id,
                'qr_path' => $fileName
            ]);
            
            return $fileName;
        } catch (\Exception $e) {
            \Log::error('Failed to generate QR code for member', [
                'member_id' => $member->id,
                'membership_id' => $member->membership_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Fallback to API method if package fails
            try {
                \Log::info('Attempting API fallback for QR code generation');
                return $this->generateQRCodeViaAPI($member);
            } catch (\Exception $fallbackError) {
                \Log::error('API fallback also failed', [
                    'member_id' => $member->id,
                    'fallback_error' => $fallbackError->getMessage()
                ]);
                throw $e; // Throw original error
            }
        }
    }

    /**
     * Generate QR code using API service (fallback method)
     */
    private function generateQRCodeViaAPI(Member $member): string
    {
        try {
            // Generate QR code data
            $qrData = $this->generateQRData($member);
            
            // Ensure qr_codes directory exists
            $directory = 'qr_codes';
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory);
            }
            
            // Use QR Server API to generate QR code
            $qrDataEncoded = urlencode($qrData);
            $apiUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data={$qrDataEncoded}&format=png&margin=10&ecc=H";
            
            // Download QR code from API
            $qrCodeContent = file_get_contents($apiUrl);
            
            if ($qrCodeContent === false) {
                throw new \Exception('Failed to generate QR code via API');
            }
            
            // Store QR code image
            $fileName = $directory . '/' . $member->membership_id . '_' . time() . '.png';
            Storage::disk('public')->put($fileName, $qrCodeContent);
            
            // Update member with QR code information
            $member->update([
                'qr_code_path' => $fileName,
                'qr_code_data' => $qrData,
            ]);
            
            \Log::info('QR code generated via API fallback', [
                'member_id' => $member->id,
                'membership_id' => $member->membership_id,
                'qr_path' => $fileName
            ]);
            
            return $fileName;
        } catch (\Exception $e) {
            \Log::error('Failed to generate QR code via API', [
                'member_id' => $member->id,
                'membership_id' => $member->membership_id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    /**
     * Generate QR code data for a member
     */
    private function generateQRData(Member $member): string
    {
        // Create a shorter, more concise QR data to fit database column
        $data = [
            't' => 'card', // type
            'id' => $member->membership_id, // membership_id
            'h' => $member->hotel_id, // hotel_id
            'n' => $member->full_name, // name
            's' => $member->status, // status
            'e' => $member->expires_at ? $member->expires_at->format('Y-m-d') : null, // expires_at (shorter format)
        ];
        
        return json_encode($data);
    }
    
    /**
     * Generate QR code for physical card
     */
    public function generateForPhysicalCard(Member $member): string
    {
        // Generate QR code data for physical card
        $qrData = $this->generatePhysicalCardQRData($member);
        
        // Generate QR code image
        $qrCode = QrCode::format('png')
            ->size(200)
            ->margin(5)
            ->errorCorrection('M')
            ->generate($qrData);
        
        // Store QR code image
        $fileName = 'qr_codes/physical_' . $member->membership_id . '_' . time() . '.png';
        Storage::disk('public')->put($fileName, $qrCode);
        
        return $fileName;
    }
    
    /**
     * Generate QR code data for physical card
     */
    private function generatePhysicalCardQRData(Member $member): string
    {
        $data = [
            'type' => 'physical_membership_card',
            'membership_id' => $member->membership_id,
            'hotel_id' => $member->hotel_id,
            'name' => $member->full_name,
            'status' => $member->status,
            'expires_at' => $member->expires_at ? $member->expires_at->toISOString() : null,
        ];
        
        return json_encode($data);
    }
    
    /**
     * Get QR code URL for a member
     */
    public function getQRCodeUrl(Member $member): ?string
    {
        if (!$member->qr_code_path) {
            return null;
        }
        
        return Storage::disk('public')->url($member->qr_code_path);
    }
    
    /**
     * Delete QR code for a member
     */
    public function deleteForMember(Member $member): bool
    {
        if ($member->qr_code_path) {
            Storage::disk('public')->delete($member->qr_code_path);
            
            $member->update([
                'qr_code_path' => null,
                'qr_code_data' => null,
            ]);
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Regenerate QR code for a member
     */
    public function regenerateForMember(Member $member): string
    {
        try {
            // Delete existing QR code
            $this->deleteForMember($member);
            
            // Generate new QR code using the package
            return $this->generateForMember($member);
        } catch (\Exception $e) {
            \Log::error('Failed to regenerate QR code for member', [
                'member_id' => $member->id,
                'membership_id' => $member->membership_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Verify QR code authenticity by checking if the scanned data matches a valid member
     */
    public function verifyQRCode(string $qrData): ?array
    {
        try {
            $data = json_decode($qrData, true);
            
            if (!$data || !isset($data['id']) || !isset($data['h'])) {
                return null; // Invalid QR data format
            }
            
            $membershipId = $data['id'];
            $hotelId = $data['h'];
            
            // Find member by membership ID and hotel ID
            $member = \App\Models\Member::where('membership_id', $membershipId)
                ->where('hotel_id', $hotelId)
                ->where('status', 'active')
                ->first();
            
            if (!$member) {
                return null; // Member not found or inactive
            }
            
            // Verify the QR data matches the current member data
            $expectedData = [
                't' => 'card',
                'id' => $member->membership_id,
                'h' => $member->hotel_id,
                'n' => $member->full_name,
                's' => $member->status,
                'e' => $member->expires_at ? $member->expires_at->format('Y-m-d') : null,
            ];
            
            // Check if the data matches (allowing for some flexibility in timestamp)
            $isValid = (
                $data['t'] === $expectedData['t'] &&
                $data['id'] === $expectedData['id'] &&
                $data['h'] === $expectedData['h'] &&
                $data['n'] === $expectedData['n'] &&
                $data['s'] === $expectedData['s']
            );
            
            if (!$isValid) {
                return null; // Data doesn't match
            }
            
            return [
                'valid' => true,
                'member' => $member,
                'member_data' => [
                    'id' => $member->id,
                    'membership_id' => $member->membership_id,
                    'name' => $member->full_name,
                    'email' => $member->email,
                    'phone' => $member->phone,
                    'status' => $member->status,
                    'membership_type' => $member->membershipType ? $member->membershipType->name : null,
                    'expires_at' => $member->expires_at,
                    'hotel_name' => $member->hotel ? $member->hotel->name : null,
                ]
            ];
            
        } catch (\Exception $e) {
            \Log::error('QR code verification failed', [
                'qr_data' => $qrData,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}
