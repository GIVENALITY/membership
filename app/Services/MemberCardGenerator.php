<?php

namespace App\Services;

use App\Models\Member;

class MemberCardGenerator
{
    /**
     * Generate a membership card image for the given member.
     * Returns the relative storage path (public disk) of the generated image.
     */
    public function generate(Member $member): string
    {
        $templatePath = public_path('assets/img/card_temp.jpg');
        if (!file_exists($templatePath)) {
            throw new \RuntimeException('Card template not found at ' . $templatePath);
        }

        // Attempt to use GD
        $image = imagecreatefromjpeg($templatePath);
        if ($image === false) {
            throw new \RuntimeException('Failed to load card template image');
        }

        // Colors
        $black = imagecolorallocate($image, 0, 0, 0);

        // Try to use a TTF font if available, fallback to built-in bitmap font
        $fontPathCandidates = [
            public_path('assets/fonts/Inter-Regular.ttf'),
            public_path('assets/fonts/Inter.ttf'),
            base_path('vendor/google/fonts/apache/inter/Inter-Regular.ttf'),
        ];
        $fontPath = null;
        foreach ($fontPathCandidates as $candidate) {
            if (file_exists($candidate)) { $fontPath = $candidate; break; }
        }

        // Compose values
        $fullName = trim($member->first_name . ' ' . $member->last_name);
        $membershipId = $member->membership_id;
        $membershipType = optional($member->membershipType)->name ?? 'N/A';
        $expires = $member->expires_at ? $member->expires_at->format('M d, Y') : 'N/A';

        // Coordinates provided by user (x, y) in pixels
        // Name: 595,513 | Membership ID: 677,593 | Membership Type: 721,684 | Expires: 535,771
        $nameX = 595; $nameY = 525;
        $idX = 677; $idY = 613;
        $typeX = 721; $typeY = 700;
        $expX = 535; $expY = 790;

        if ($fontPath && function_exists('imagettftext')) {
            $fontSizePt = 153; // requested 13pt
            // Convert points to pixels approximation for GD: px ≈ pt * 1.333
            $fontPx = (int) round($fontSizePt * 1.333);
            imagettftext($image, $fontPx, 0, $nameX, $nameY, $black, $fontPath, $fullName);
            imagettftext($image, $fontPx, 0, $idX, $idY, $black, $fontPath, $membershipId);
            imagettftext($image, $fontPx, 0, $typeX, $typeY, $black, $fontPath, $membershipType);
            imagettftext($image, $fontPx, 0, $expX, $expY, $black, $fontPath, $expires);
        } else {
            // Fallback using bitmap fonts (approximate placement)
            $font = 5; // built-in font size (~13px height)
            imagestring($image, $font, $nameX, $nameY - 10, $fullName, $black);
            imagestring($image, $font, $idX, $idY - 10, $membershipId, $black);
            imagestring($image, $font, $typeX, $typeY - 10, $membershipType, $black);
            imagestring($image, $font, $expX, $expY - 10, $expires, $black);
        }

        // Save to public storage so it is web-accessible via storage symlink
        $fileName = $membershipId . '.jpg';
        $relativePath = 'cards/' . $fileName; // storage/app/public/cards/xxx.jpg
        $absolutePath = storage_path('app/public/' . $relativePath);
        if (!is_dir(dirname($absolutePath))) {
            @mkdir(dirname($absolutePath), 0775, true);
        }

        imagejpeg($image, $absolutePath, 90);
        imagedestroy($image);

        return $relativePath;
    }
} 