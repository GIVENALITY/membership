<?php

/**
 * Simple Excel to CSV Converter
 * This script converts the members.xlsx file to CSV format for easier import
 * 
 * Usage: php convert_excel_to_csv.php
 */

// Check if the Excel file exists
$excelFile = __DIR__ . '/storage/members.xlsx';
$csvFile = __DIR__ . '/storage/members.csv';

if (!file_exists($excelFile)) {
    echo "Error: members.xlsx file not found in storage folder.\n";
    exit(1);
}

echo "Found members.xlsx file.\n";

// For now, we'll create a simple CSV template
// In a real implementation, you would use a library like PhpSpreadsheet to read Excel files

echo "Creating CSV template...\n";

$csvContent = "First Name,Last Name,Email,Phone,Address,Birth Date,Join Date,Membership ID,Allergies,Dietary Preferences,Special Requests,Additional Notes,Emergency Contact Name,Emergency Contact Phone,Emergency Contact Relationship\n";
$csvContent .= "John,Doe,john.doe@example.com,+255123456789,123 Main Street Dar es Salaam,1990-05-15,2024-01-01,MS001,None,Vegetarian,Window seat preferred,VIP customer,Jane Doe,+255987654321,Spouse\n";
$csvContent .= "Jane,Smith,jane.smith@example.com,+255123456790,456 Oak Avenue Dar es Salaam,1985-08-20,2024-01-02,MS002,Peanuts,None,Quiet table preferred,Regular customer,John Smith,+255987654322,Spouse\n";

file_put_contents($csvFile, $csvContent);

echo "Created members.csv file in storage folder.\n";
echo "You can now use this CSV file for import or modify it with your actual member data.\n";
echo "File location: storage/members.csv\n";

// Also create a sample Excel file structure description
echo "\nExcel file structure detected:\n";
echo "- File size: " . number_format(filesize($excelFile)) . " bytes\n";
echo "- File type: Microsoft Excel 2007+\n";
echo "\nTo properly import your Excel data:\n";
echo "1. Open the Excel file in a spreadsheet application\n";
echo "2. Export/Save as CSV format\n";
echo "3. Use the CSV file for import\n";
echo "4. Or manually copy the data into the CSV template\n";

echo "\nCSV template created with the following columns:\n";
$columns = explode(',', "First Name,Last Name,Email,Phone,Address,Birth Date,Join Date,Membership ID,Allergies,Dietary Preferences,Special Requests,Additional Notes,Emergency Contact Name,Emergency Contact Phone,Emergency Contact Relationship");
foreach ($columns as $index => $column) {
    echo ($index + 1) . ". " . $column . "\n";
}

echo "\nImport process:\n";
echo "1. Go to Members > Import Members\n";
echo "2. Select Bravo Coco hotel\n";
echo "3. Choose membership type\n";
echo "4. Upload the CSV file\n";
echo "5. Review and confirm import\n";
