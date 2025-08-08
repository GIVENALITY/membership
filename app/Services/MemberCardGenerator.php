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
        $nameX = 595; $nameY = 523;
        $idX = 677; $idY = 613;
        $typeX = 721; $typeY = 700;
        $expX = 535; $expY = 771;

        if ($fontPath && function_exists('imagettftext')) {
            // Base font size (pt); text will be auto-fit to bar width
            $basePt = 272; // tuned by user
            $toPx = function (int $pt): int { return (int) round($pt * 1.333); }; // px ≈ pt * 1.333
            $fontPx = $toPx($basePt);

            // Helper to shrink font so text fits within max width
            $fitFontPx = function (string $text, string $fontPath, int $fontPx, int $maxWidth) {
                $measure = function ($text, $fontPath, $fontPx) {
                    $box = imagettfbbox($fontPx, 0, $fontPath, $text);
                    $width = abs($box[2] - $box[0]);
                    return $width;
                };
                $current = $fontPx;
                while ($current > 12 && $measure($text, $fontPath, $current) > $maxWidth) {
                    $current -= 1; // step down until it fits
                }
                return $current;
            };

            // Reasonable widths for the golden bars area (adjust if needed after a visual check)
            $nameMax = 1200; // px
            $idMax   = 1000;
            $typeMax = 1000;
            $expMax  = 900;

            $namePx = $fitFontPx($fullName, $fontPath, $fontPx, $nameMax);
            $idPx   = $fitFontPx($membershipId, $fontPath, $fontPx, $idMax);
            $typePx = $fitFontPx($membershipType, $fontPath, $fontPx, $typeMax);
            $expPx  = $fitFontPx($expires, $fontPath, $fontPx, $expMax);

            imagettftext($image, $namePx, 0, $nameX, $nameY, $black, $fontPath, $fullName);
            imagettftext($image, $idPx,   0, $idX,   $idY,   $black, $fontPath, $membershipId);
            imagettftext($image, $typePx, 0, $typeX, $typeY, $black, $fontPath, $membershipType);
            imagettftext($image, $expPx,  0, $expX,  $expY,  $black, $fontPath, $expires);
        } else {
            // Fallback using bitmap fonts (limited sizing control)
            $font = 5; // built-in font size (~13px height)
            imagestring($image, $font, $nameX, $nameY - 10, $fullName, $black);
            imagestring($image, $font, $idX, $idY - 10, $membershipId, $black);
            imagestring($image, $font, $typeX, $typeY - 10, $membershipType, $black);
            imagestring($image, $font, $expX, $expY - 10, $expires, $black);
        }

        // Downscale the final image by 50%
        $origW = imagesx($image);
        $origH = imagesy($image);
        $newW = (int) floor($origW / 2);
        $newH = (int) floor($origH / 2);
        $scaled = imagecreatetruecolor($newW, $newH);
        imagecopyresampled($scaled, $image, 0, 0, 0, 0, $newW, $newH, $origW, $origH);

        // Save to public storage so it is web-accessible via storage symlink
        $fileName = $membershipId . '.jpg';
        $relativePath = 'cards/' . $fileName; // storage/app/public/cards/xxx.jpg
        $absolutePath = storage_path('app/public/' . $relativePath);
        if (!is_dir(dirname($absolutePath))) {
            @mkdir(dirname($absolutePath), 0775, true);
        }

        imagejpeg($scaled, $absolutePath, 90);
        imagedestroy($scaled);
        imagedestroy($image);

        return $relativePath;
    }
} 