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
        $white = imagecolorallocate($image, 255, 255, 255);
        $gold = imagecolorallocate($image, 212, 175, 55);

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

        // Coordinates (tuned for the provided template). Adjust if needed.
        // These Y positions target the four gold bars on the template.
        $startX = 235; // left margin inside the bars
        $yFullName = 360;
        $yMemberId = 430;
        $yType = 500;
        $yExpires = 570;

        if ($fontPath && function_exists('imagettftext')) {
            $fontSize = 30; // px
            imagettftext($image, $fontSize, 0, $startX, $yFullName, $white, $fontPath, $fullName);
            imagettftext($image, $fontSize, 0, $startX, $yMemberId, $white, $fontPath, $membershipId);
            imagettftext($image, $fontSize, 0, $startX, $yType, $white, $fontPath, $membershipType);
            imagettftext($image, $fontSize, 0, $startX, $yExpires, $white, $fontPath, $expires);
        } else {
            // Fallback using bitmap fonts
            $font = 5; // built-in font size
            imagestring($image, $font, $startX, $yFullName - 16, $fullName, $white);
            imagestring($image, $font, $startX, $yMemberId - 16, $membershipId, $white);
            imagestring($image, $font, $startX, $yType - 16, $membershipType, $white);
            imagestring($image, $font, $startX, $yExpires - 16, $expires, $white);
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