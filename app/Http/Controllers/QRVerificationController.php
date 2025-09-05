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
