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
            public_path('assets/fonts/Roboto.ttf'),
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
        $expX = 535; $expY = 781;

        if ($fontPath && function_exists('imagettftext')) {
            // Use PX as the primary unit; convert to PT for GD's imagettftext
            $pxToPt = function (float $px): float { return $px / 1.333; };

            // Base text size in PX (can be tuned). Auto-fit will scale down if needed
            $basePx = 27.0;

            // Measure rendered width (in px) for a given px size
            $measureWidthPx = function (string $text, string $fontPath, float $fontPx) use ($pxToPt) {
                $pt = $pxToPt($fontPx);
                $box = imagettfbbox($pt, 0, $fontPath, $text);
                return abs($box[2] - $box[0]);
            };

            // Fit a text to a maximum width by reducing pixel size
            $fitFontPx = function (string $text, string $fontPath, float $startPx, int $maxWidthPx) use ($measureWidthPx) {
                $current = $startPx;
                while ($current > 10 && $measureWidthPx($text, $fontPath, $current) > $maxWidthPx) {
                    $current -= 1; // reduce 1px and try again
                }
                return $current;
            };

            // Max widths for each bar (px)
            $nameMax = 1000;
            $idMax   = 900;
            $typeMax = 900;
            $expMax  = 800;

            $namePx = $fitFontPx($fullName, $fontPath, $basePx, $nameMax);
            $idPx   = $fitFontPx($membershipId, $fontPath, $basePx, $idMax);
            $typePx = $fitFontPx($membershipType, $fontPath, $basePx, $typeMax);
            $expPx  = $fitFontPx($expires, $fontPath, $basePx, $expMax);

            // Render using converted PT sizes
            imagettftext($image, $pxToPt($namePx), 0, $nameX, $nameY, $black, $fontPath, $fullName);
            imagettftext($image, $pxToPt($idPx),   0, $idX,   $idY,   $black, $fontPath, $membershipId);
            imagettftext($image, $pxToPt($typePx), 0, $typeX, $typeY, $black, $fontPath, $membershipType);
            imagettftext($image, $pxToPt($expPx),  0, $expX,  $expY,  $black, $fontPath, $expires);
        } else {
            // Fallback using bitmap fonts (limited sizing control)
            $font = 45; // built-in font size (~13px height)
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