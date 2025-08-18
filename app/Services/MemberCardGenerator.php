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
        // --- Template (PNG recommended for this artwork) ---
        $templatePath = public_path('assets/img/platinumcard.png');
        if (!file_exists($templatePath)) {
            throw new \RuntimeException('Card template not found at ' . $templatePath);
        }

        // Load template
        $ext = strtolower(pathinfo($templatePath, PATHINFO_EXTENSION));
        if ($ext === 'png') {
            $image = imagecreatefrompng($templatePath);
            if ($image === false) throw new \RuntimeException('Failed to load PNG template');
            // keep alpha if ever needed
            imagesavealpha($image, true);
        } elseif (in_array($ext, ['jpg', 'jpeg'])) {
            $image = imagecreatefromjpeg($templatePath);
            if ($image === false) throw new \RuntimeException('Failed to load JPG template');
        } else {
            throw new \RuntimeException('Unsupported template format: ' . $ext);
        }

        // --- Colors (template uses white text) ---
        $white = imagecolorallocate($image, 255, 255, 255);

        // --- Font discovery ---
        $fontPathCandidates = [
            public_path('assets/fonts/Inter-Regular.ttf'),
            public_path('assets/fonts/Inter.ttf'),
            public_path('assets/fonts/Roboto.ttf'),
            base_path('vendor/google/fonts/apache/inter/Inter-Regular.ttf'),
        ];
        $fontPath = null;
        foreach ($fontPathCandidates as $candidate) {
            if (file_exists($candidate)) { $fontPath = $candidate; break; }
        }

        // --- Values ---
        $fullName     = trim($member->first_name . ' ' . $member->last_name);
        $membershipId = (string) $member->membership_id;

        // --- Coordinates for 591×945 template (GD uses text *baseline* for Y) ---
        // Left-aligned lines as in the mock
        $nameX = 58; $nameY = 360; // "NAME & SURNAME" line
        $idX   = 58; $idY   = 405; // membership number line

        // --- Draw (TTF preferred with auto-fit; fallback to bitmap) ---
        if ($fontPath && function_exists('imagettftext')) {
            // Convert PX → PT for GD's imagettftext
            $pxToPt = static fn (float $px): float => $px / 1.333;

            // Measure rendered width (px) at a given px font size
            $measureWidthPx = static function (string $text, string $font, float $fontPx) use ($pxToPt): float {
                $pt  = $pxToPt($fontPx);
                $box = imagettfbbox($pt, 0, $font, $text);
                return abs($box[2] - $box[0]);
            };

            // Fit text into a max width by reducing size
            $fitFontPx = static function (string $text, string $font, float $startPx, int $maxWidthPx) use ($measureWidthPx): float {
                $current = $startPx;
                while ($current > 10 && $measureWidthPx($text, $font, $current) > $maxWidthPx) {
                    $current -= 1;
                }
                return $current;
            };

            // Safe widths for this layout
            $nameMax = 540 - 58; // ≈ 482px (from left margin ~58 to safe right ~540)
            $idMax   = 360;      // number block

            $basePx  = 37.0;
            $namePx  = $fitFontPx($fullName,     $fontPath, $basePx, $nameMax);
            $idPx    = $fitFontPx($membershipId, $fontPath, $basePx, $idMax);

            imagettftext($image, $pxToPt($namePx), 0, $nameX, $nameY, $white, $fontPath, $fullName);
            imagettftext($image, $pxToPt($idPx),   0, $idX,   $idY,   $white, $fontPath, $membershipId);
        } else {
            // Fallback bitmap rendering (sizes limited)
            $font = 5; // GD built-in font
            imagestring($image, $font, $nameX, $nameY - 12, $fullName, $white);
            imagestring($image, $font, $idX,   $idY   - 12, $membershipId, $white);
        }

        // --- Save to public storage (web-accessible via `storage:link`) ---
        $fileName     = $membershipId . '.jpg';
        $relativePath = 'cards/' . $fileName;               // storage/app/public/cards/xxx.jpg
        $absolutePath = storage_path('app/public/' . $relativePath);
        if (!is_dir(dirname($absolutePath))) {
            @mkdir(dirname($absolutePath), 0775, true);
        }

        imagejpeg($image, $absolutePath, 90);
        imagedestroy($image);

        return $relativePath;
    }
}
