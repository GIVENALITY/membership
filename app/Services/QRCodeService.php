<?php

namespace App\Services;

use App\Models\Member;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QRCodeService
{
    /**
     * Generate QR code for a member
     */
    public function generateForMember(Member $member): string
    {
        try {
            // Check if QR code package is available
            if (!class_exists('SimpleSoftwareIO\QrCode\Facades\QrCode')) {
                throw new \Exception('QR Code package not available. Please ensure simplesoftwareio/simple-qrcode is properly installed.');
            }

            // Generate QR code data
            $qrData = $this->generateQRData($member);
            
            // Ensure qr_codes directory exists
            $directory = 'qr_codes';
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory);
            }
            
            // Generate QR code image
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
            
            return $fileName;
        } catch (\Exception $e) {
            \Log::error('Failed to generate QR code for member', [
                'member_id' => $member->id,
                'membership_id' => $member->membership_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
    
    /**
     * Generate QR code data for a member
     */
    private function generateQRData(Member $member): string
    {
        $data = [
            'type' => 'membership_card',
            'member_id' => $member->id,
            'membership_id' => $member->membership_id,
            'hotel_id' => $member->hotel_id,
            'name' => $member->full_name,
            'email' => $member->email,
            'phone' => $member->phone,
            'status' => $member->status,
            'membership_type' => optional($member->membershipType)->name ?? 'N/A',
            'discount_rate' => $member->current_discount_rate ?? 0,
            'points_balance' => $member->current_points_balance ?? 0,
            'expires_at' => $member->expires_at ? $member->expires_at->toISOString() : null,
            'timestamp' => now()->toISOString(),
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
            
            // Generate new QR code
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
}
