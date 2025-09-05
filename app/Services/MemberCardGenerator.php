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
        $membershipType = $member->membershipType;
        
        // Check if membership type has custom card template
        if ($membershipType && $membershipType->hasCardTemplate()) {
            return $this->generateWithCustomTemplate($member, $membershipType);
        }
        
        // Fallback to default template
        return $this->generateWithDefaultTemplate($member);
    }
    
    /**
     * Debug card template configuration for a member
     */
    public function debugCardTemplate(Member $member): array
    {
        $membershipType = $member->membershipType;
        
        return [
            'member_id' => $member->id,
            'member_name' => $member->first_name . ' ' . $member->last_name,
            'membership_type_id' => $membershipType ? $membershipType->id : null,
            'membership_type_name' => $membershipType ? $membershipType->name : null,
            'has_card_template' => $membershipType ? $membershipType->hasCardTemplate() : false,
            'card_template_image' => $membershipType ? $membershipType->card_template_image : null,
            'card_template_url' => $membershipType ? $membershipType->card_template_url : null,
            'card_field_mappings' => $membershipType ? $membershipType->card_field_mappings : null,
            'template_path' => $membershipType && $membershipType->card_template_image 
                ? storage_path('app/public/' . $membershipType->card_template_image) 
                : null,
            'template_exists' => $membershipType && $membershipType->card_template_image 
                ? file_exists(storage_path('app/public/' . $membershipType->card_template_image))
                : false,
        ];
    }

    /**
     * Generate card using custom template and field mappings
     */
    private function generateWithCustomTemplate(Member $member, $membershipType): string
    {
        // Get template path from membership type (fresh from database)
        $templatePath = $this->getTemplatePath($member);
        
        if (!$templatePath || !file_exists($templatePath)) {
            // Provide more detailed error information
            $errorMsg = "Card template not found at: {$templatePath}\n";
            $errorMsg .= "Membership Type: {$membershipType->name}\n";
            $errorMsg .= "Template Image Field: {$membershipType->card_template_image}\n";
            $errorMsg .= "Storage Path: " . storage_path('app/public/') . "\n";
            $errorMsg .= "Please check if the template file exists and the path is correct.";
            
            throw new \RuntimeException($errorMsg);
        }

        $image = $this->loadImage($templatePath);
        $white = imagecolorallocate($image, 255, 255, 255);
        $fontPath = $this->findFontPath();

        // Get member data
        $memberData = $this->getMemberData($member);

        // Apply field mappings
        foreach ($membershipType->card_field_mappings as $mapping) {
            $field = $mapping['field'];
            $x = $mapping['x'];
            $y = $mapping['y'];
            $fontSize = $mapping['font_size'] ?? 16;

            if (isset($memberData[$field])) {
                $text = $memberData[$field];
                $this->drawText($image, $text, $x, $y, $fontSize, $white, $fontPath);
            }
        }

        // Add QR code to the card
        $this->addQRCodeToCard($image, $member);

        return $this->saveImage($image, $member->membership_id);
    }

    /**
     * Generate card using default template
     */
    private function generateWithDefaultTemplate(Member $member): string
    {
        // Get template path from membership type (fresh from database)
        $templatePath = $this->getTemplatePath($member);
        
        if (!$templatePath || !file_exists($templatePath)) {
            throw new \RuntimeException('No card template found. Please upload a template in Restaurant Settings > Membership Types.');
        }

        $image = $this->loadImage($templatePath);
        $white = imagecolorallocate($image, 255, 255, 255);
        $fontPath = $this->findFontPath();

        // --- Values ---
        $fullName     = trim($member->first_name . ' ' . $member->last_name);
        $membershipId = (string) $member->membership_id;

        // --- Coordinates for 591×945 template (GD uses text *baseline* for Y) ---
        // Left-aligned lines as in the mock
        $nameX = 58; $nameY = 360; // "NAME & SURNAME" line
        $idX   = 58; $idY   = 405; // membership number line

        $this->drawText($image, $fullName, $nameX, $nameY, 37, $white, $fontPath);
        $this->drawText($image, $membershipId, $idX, $idY, 37, $white, $fontPath);

        // Add QR code to the card
        $this->addQRCodeToCard($image, $member);

        return $this->saveImage($image, $member->membership_id);
    }

    /**
     * Add QR code to the membership card
     */
    private function addQRCodeToCard($image, Member $member): void
    {
        // Generate QR code if it doesn't exist
        if (!$member->hasQRCode()) {
            try {
                $qrService = app(\App\Services\QRCodeService::class);
                $qrPath = $qrService->generateForMember($member);
                $member->refresh(); // Refresh to get updated QR code path
                \Log::info('QR code generated during card creation', [
                    'member_id' => $member->id,
                    'qr_path' => $qrPath
                ]);
            } catch (\Exception $e) {
                \Log::error('Failed to generate QR code during card creation', [
                    'member_id' => $member->id,
                    'error' => $e->getMessage()
                ]);
                return; // Skip QR code if generation fails
            }
        }

        $qrPath = storage_path('app/public/' . $member->qr_code_path);
        if (!file_exists($qrPath)) {
            \Log::warning('QR code file not found', [
                'member_id' => $member->id,
                'qr_path' => $qrPath
            ]);
            return; // QR code file doesn't exist
        }

        try {
            // Load QR code image
            $qrImage = $this->loadImage($qrPath);
            
            // Get dimensions
            $cardWidth = imagesx($image);
            $cardHeight = imagesy($image);
            $qrWidth = imagesx($qrImage);
            $qrHeight = imagesy($qrImage);
            
            // Calculate QR code position (bottom right corner)
            $qrSize = 120; // Size of QR code on card
            $margin = 30; // Margin from edges
            $qrX = $cardWidth - $qrSize - $margin; // Right side
            $qrY = $cardHeight - $qrSize - $margin; // Bottom side
            
            // Resize and copy QR code to card
            $this->copyResizedImage($image, $qrImage, $qrX, $qrY, $qrSize, $qrSize);
            
            \Log::info('QR code added to card successfully', [
                'member_id' => $member->id,
                'position' => "x:{$qrX}, y:{$qrY}, size:{$qrSize}"
            ]);
            
            // Clean up
            imagedestroy($qrImage);
        } catch (\Exception $e) {
            \Log::error('Failed to add QR code to card', [
                'member_id' => $member->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Copy and resize image
     */
    private function copyResizedImage($destImage, $srcImage, $destX, $destY, $destWidth, $destHeight): void
    {
        $srcWidth = imagesx($srcImage);
        $srcHeight = imagesy($srcImage);
        
        imagecopyresampled(
            $destImage, $srcImage,
            $destX, $destY, 0, 0,
            $destWidth, $destHeight,
            $srcWidth, $srcHeight
        );
    }

    /**
     * Load image from file
     */
    private function loadImage(string $path)
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        
        if ($ext === 'png') {
            $image = imagecreatefrompng($path);
            if ($image === false) throw new \RuntimeException('Failed to load PNG template');
            imagesavealpha($image, true);
        } elseif (in_array($ext, ['jpg', 'jpeg'])) {
            $image = imagecreatefromjpeg($path);
            if ($image === false) throw new \RuntimeException('Failed to load JPG template');
        } else {
            throw new \RuntimeException('Unsupported template format: ' . $ext);
        }

        return $image;
    }

    /**
     * Find available font path
     */
    private function findFontPath(): ?string
    {
        $fontPathCandidates = [
            public_path('assets/fonts/Inter-Regular.ttf'),
            public_path('assets/fonts/Inter.ttf'),
            public_path('assets/fonts/Roboto.ttf'),
            base_path('vendor/google/fonts/apache/inter/Inter-Regular.ttf'),
        ];
        
        foreach ($fontPathCandidates as $candidate) {
            if (file_exists($candidate)) {
                return $candidate;
            }
        }
        
        return null;
    }

    /**
     * Get template path from member's membership type (fresh from database)
     */
    private function getTemplatePath(Member $member): ?string
    {
        // Refresh the member to get latest membership type data
        $member->refresh();
        $membershipType = $member->membershipType;
        
        if (!$membershipType || !$membershipType->card_template_image) {
            return null;
        }
        
        // Build full path to template
        $templatePath = storage_path('app/public/' . $membershipType->card_template_image);
        
        \Log::info('Getting template path from database', [
            'member_id' => $member->id,
            'membership_type_id' => $membershipType->id,
            'template_image' => $membershipType->card_template_image,
            'full_path' => $templatePath,
            'exists' => file_exists($templatePath)
        ]);
        
        return $templatePath;
    }

    /**
     * Get member data for card generation
     */
    private function getMemberData(Member $member): array
    {
        return [
            'first_name' => $member->first_name,
            'last_name' => $member->last_name,
            'full_name' => trim($member->first_name . ' ' . $member->last_name),
            'membership_id' => (string) $member->membership_id,
            'email' => $member->email,
            'phone' => $member->phone,
            'address' => $member->address,
            'birth_date' => $member->birth_date ? date('M d, Y', strtotime($member->birth_date)) : '',
            'join_date' => $member->join_date ? date('M d, Y', strtotime($member->join_date)) : '',
            'membership_type_name' => $member->membershipType ? $member->membershipType->name : '',
            'hotel_name' => $member->hotel ? $member->hotel->name : '',
        ];
    }

    /**
     * Draw text on image with auto-fitting
     */
    private function drawText($image, string $text, int $x, int $y, int $fontSize, int $color, ?string $fontPath): void
    {
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

            // Safe width (adjust based on your template)
            $maxWidth = 400;
            $adjustedFontSize = $fitFontPx($text, $fontPath, $fontSize, $maxWidth);

            imagettftext($image, $pxToPt($adjustedFontSize), 0, $x, $y, $color, $fontPath, $text);
        } else {
            // Fallback bitmap rendering
            $font = 5; // GD built-in font
            imagestring($image, $font, $x, $y - 12, $text, $color);
        }
    }

    /**
     * Save image to storage
     */
    private function saveImage($image, string $membershipId): string
    {
        $fileName = $membershipId . '.jpg';
        $relativePath = 'cards/' . $fileName;
        $absolutePath = storage_path('app/public/' . $relativePath);
        
        if (!is_dir(dirname($absolutePath))) {
            @mkdir(dirname($absolutePath), 0775, true);
        }

        imagejpeg($image, $absolutePath, 90);
        imagedestroy($image);

        return $relativePath;
    }
}
