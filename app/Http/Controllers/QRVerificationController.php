<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\QRCodeService;

class QRVerificationController extends Controller
{
    protected $qrService;

    public function __construct(QRCodeService $qrService)
    {
        $this->qrService = $qrService;
    }

    /**
     * Show QR verification form
     */
    public function index()
    {
        return view('qr-verification.index');
    }

    /**
     * Verify QR code directly from URL (for scanned QR codes)
     */
    public function verifyDirect($membershipId, $hotelId)
    {
        try {
            // Find member by membership ID and hotel ID
            $member = \App\Models\Member::where('membership_id', $membershipId)
                ->where('hotel_id', $hotelId)
                ->where('status', 'active')
                ->first();

            if ($member) {
                return view('qr-verification.result', [
                    'valid' => true,
                    'member' => [
                        'id' => $member->id,
                        'membership_id' => $member->membership_id,
                        'name' => $member->full_name,
                        'email' => $member->email,
                        'phone' => $member->phone,
                        'status' => $member->status,
                        'membership_type' => $member->membershipType ? $member->membershipType->name : null,
                        'expires_at' => $member->expires_at,
                        'hotel_name' => $member->hotel ? $member->hotel->name : null,
                    ],
                    'qrData' => null
                ]);
            } else {
                return view('qr-verification.result', [
                    'valid' => false,
                    'member' => null,
                    'qrData' => "Membership ID: {$membershipId}, Hotel ID: {$hotelId}"
                ]);
            }
        } catch (\Exception $e) {
            return view('qr-verification.result', [
                'valid' => false,
                'member' => null,
                'qrData' => "Error: " . $e->getMessage()
            ]);
        }
    }

    /**
     * Verify QR code and show result
     */
    public function verify(Request $request)
    {
        $request->validate([
            'qr_data' => 'required|string'
        ]);

        $qrData = $request->qr_data;
        $result = $this->qrService->verifyQRCode($qrData);

        if ($result) {
            return view('qr-verification.result', [
                'valid' => true,
                'member' => $result['member_data'],
                'qrData' => $qrData
            ]);
        } else {
            return view('qr-verification.result', [
                'valid' => false,
                'member' => null,
                'qrData' => $qrData
            ]);
        }
    }

    /**
     * API endpoint for QR verification (returns JSON)
     */
    public function verifyApi(Request $request)
    {
        $request->validate([
            'qr_data' => 'required|string'
        ]);

        $qrData = $request->qr_data;
        $result = $this->qrService->verifyQRCode($qrData);

        if ($result) {
            return response()->json([
                'success' => true,
                'valid' => true,
                'message' => 'QR code is valid and authentic',
                'member' => $result['member_data']
            ]);
        } else {
            return response()->json([
                'success' => true,
                'valid' => false,
                'message' => 'QR code is invalid or expired'
            ]);
        }
    }
}
