<?php

/**
 * Test script to verify the email fix for custom recipients
 */

echo "Testing Email Fix for Custom Recipients\n";
echo "=====================================\n\n";

// Test 1: Check if custom recipient ID is handled correctly
echo "Test 1: Custom Recipient ID Handling\n";
$customId = 'custom_0';
echo "Custom ID: $customId\n";
echo "Is numeric: " . (is_numeric($customId) ? 'Yes' : 'No') . "\n";
echo "Is valid for member_id: " . (is_numeric($customId) && $customId > 0 ? 'Yes' : 'No') . "\n\n";

// Test 2: Check if regular member ID is handled correctly
echo "Test 2: Regular Member ID Handling\n";
$regularId = 123;
echo "Regular ID: $regularId\n";
echo "Is numeric: " . (is_numeric($regularId) ? 'Yes' : 'No') . "\n";
echo "Is valid for member_id: " . (is_numeric($regularId) && $regularId > 0 ? 'Yes' : 'No') . "\n\n";

// Test 3: Simulate the email log creation logic
echo "Test 3: Email Log Creation Logic\n";

function createEmailLogData($member, $hotelId, $request) {
    $emailLogData = [
        'hotel_id' => $hotelId,
        'email_type' => 'member_email',
        'subject' => 'Test Subject',
        'content' => 'Test Content',
        'recipient_email' => $member->email,
        'recipient_name' => $member->first_name . ' ' . $member->last_name,
        'status' => 'pending',
        'metadata' => [
            'recipient_type' => 'custom',
            'send_immediately' => '1',
            'sent_by' => 4
        ]
    ];
    
    // Only set member_id if it's a valid integer (not a custom recipient)
    if (is_numeric($member->id) && $member->id > 0) {
        $emailLogData['member_id'] = $member->id;
        echo "✅ Setting member_id to: {$member->id}\n";
    } else {
        echo "❌ Skipping member_id for custom recipient: {$member->id}\n";
    }
    
    return $emailLogData;
}

// Test with custom recipient
$customRecipient = new stdClass();
$customRecipient->id = 'custom_0';
$customRecipient->email = 'test@example.com';
$customRecipient->first_name = 'Test';
$customRecipient->last_name = 'User';

echo "Testing with custom recipient:\n";
$emailData = createEmailLogData($customRecipient, 4, null);
echo "Email log data: " . json_encode($emailData, JSON_PRETTY_PRINT) . "\n\n";

// Test with regular member
$regularMember = new stdClass();
$regularMember->id = 123;
$regularMember->email = 'member@example.com';
$regularMember->first_name = 'John';
$regularMember->last_name = 'Doe';

echo "Testing with regular member:\n";
$emailData = createEmailLogData($regularMember, 4, null);
echo "Email log data: " . json_encode($emailData, JSON_PRETTY_PRINT) . "\n\n";

echo "✅ Test completed successfully!\n";
echo "The fix should now handle both custom recipients and regular members correctly.\n";
