<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Member;
use App\Models\Hotel;
use App\Models\MembershipType;
use Carbon\Carbon;

class MemberImportController extends Controller
{
    /**
     * Show the import form
     */
    public function index()
    {
        $hotels = Hotel::active()->get();
        return view('members.import', compact('hotels'));
    }

    /**
     * Process the member import
     */
    public function import(Request $request)
    {
        $request->validate([
            'hotel_id' => 'required|exists:hotels,id',
            'import_file' => 'required|file|mimes:xlsx,xls,csv|max:10240', // 10MB max
        ]);

        try {
            DB::beginTransaction();

            $hotel = Hotel::findOrFail($request->hotel_id);
            
            // Get all active membership types for this hotel
            $membershipTypes = MembershipType::where('hotel_id', $hotel->id)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();
                
            if ($membershipTypes->isEmpty()) {
                throw new \Exception('No active membership types found for this hotel. Please create membership types first.');
            }
            
            $file = $request->file('import_file');
            $filePath = $file->getRealPath();
            
            // Read the file content
            $fileContent = file_get_contents($filePath);
            $lines = explode("\n", $fileContent);
            
            $importedCount = 0;
            $errors = [];
            $rowNumber = 1; // Start from 1 for user-friendly error messages
            
            foreach ($lines as $line) {
                $rowNumber++;
                
                // Skip empty lines
                if (empty(trim($line))) {
                    continue;
                }
                
                // Parse CSV line (handle quoted fields)
                $data = $this->parseCsvLine($line);
                
                // Skip header row
                if ($rowNumber === 2 && $this->isHeaderRow($data)) {
                    continue;
                }
                
                try {
                    $member = $this->createMemberFromRow($data, $hotel, $membershipTypes);
                    $importedCount++;
                    
                    Log::info('Member imported successfully', [
                        'membership_id' => $member->membership_id,
                        'name' => $member->full_name,
                        'hotel' => $hotel->name,
                        'membership_type' => $member->membershipType->name,
                        'row' => $rowNumber
                    ]);
                    
                } catch (\Exception $e) {
                    $errors[] = "Row {$rowNumber}: " . $e->getMessage();
                    Log::error('Member import error', [
                        'row' => $rowNumber,
                        'data' => $data,
                        'error' => $e->getMessage()
                    ]);
                }
            }
            
            DB::commit();
            
            $message = "Successfully imported {$importedCount} members.";
            if (!empty($errors)) {
                $message .= " " . count($errors) . " errors occurred.";
            }
            
            return back()->with([
                'success' => $message,
                'import_errors' => $errors,
                'imported_count' => $importedCount
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Member import failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Import from the existing members.xlsx file in storage
     */
    public function importFromStorage(Request $request)
    {
        $request->validate([
            'hotel_id' => 'required|exists:hotels,id',
        ]);

        try {
            DB::beginTransaction();

            $hotel = Hotel::findOrFail($request->hotel_id);
            
            // Get all active membership types for this hotel
            $membershipTypes = MembershipType::where('hotel_id', $hotel->id)
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get();
                
            if ($membershipTypes->isEmpty()) {
                throw new \Exception('No active membership types found for this hotel. Please create membership types first.');
            }
            
            $filePath = storage_path('members.xlsx');
            
            if (!file_exists($filePath)) {
                return back()->with('error', 'members.xlsx file not found in storage folder.');
            }
            
            // For now, we'll create a simple import that works with CSV
            // You can convert the Excel file to CSV manually or use a tool
            return back()->with('error', 'Please convert the Excel file to CSV format first, or use the file upload option.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Member import from storage failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Parse a CSV line, handling quoted fields
     */
    private function parseCsvLine($line)
    {
        $data = [];
        $current = '';
        $inQuotes = false;
        $length = strlen($line);
        
        for ($i = 0; $i < $length; $i++) {
            $char = $line[$i];
            
            if ($char === '"') {
                $inQuotes = !$inQuotes;
            } elseif ($char === ',' && !$inQuotes) {
                $data[] = trim($current);
                $current = '';
            } else {
                $current .= $char;
            }
        }
        
        $data[] = trim($current);
        return $data;
    }

    /**
     * Check if this is a header row
     */
    private function isHeaderRow($data)
    {
        $headers = ['name', 'email', 'phone', 'address', 'birth_date', 'join_date'];
        $firstRow = array_map('strtolower', array_map('trim', $data));
        
        foreach ($headers as $header) {
            if (in_array($header, $firstRow)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Create a member from a data row
     */
    private function createMemberFromRow($data, $hotel, $membershipTypes)
    {
        // Map the data columns (adjust based on your Excel structure)
        $mappedData = $this->mapDataColumns($data);
        
        // Validate required fields
        if (empty($mappedData['first_name']) && empty($mappedData['last_name'])) {
            throw new \Exception('Name is required');
        }
        
        // Generate membership ID if not provided
        if (empty($mappedData['membership_id'])) {
            $mappedData['membership_id'] = Member::generateMembershipId();
        }
        
        // Check if member already exists
        $existingMember = Member::where('membership_id', $mappedData['membership_id'])
            ->orWhere('email', $mappedData['email'])
            ->first();
            
        if ($existingMember) {
            throw new \Exception('Member already exists with this membership ID or email');
        }
        
        // Set hotel
        $mappedData['hotel_id'] = $hotel->id;
        
        // Determine membership type based on data
        $membershipType = $this->determineMembershipType($mappedData, $membershipTypes);
        $mappedData['membership_type_id'] = $membershipType->id;
        
        // Set default values
        $mappedData['status'] = $mappedData['status'] ?? 'active';
        $mappedData['join_date'] = $mappedData['join_date'] ?? now()->toDateString();
        
        // Create the member
        $member = Member::create($mappedData);
        
        return $member;
    }

    /**
     * Map data columns from Excel to database fields
     */
    private function mapDataColumns($data)
    {
        // This mapping should be adjusted based on your Excel file structure
        // For now, using a simple mapping - you'll need to adjust this
        
        $mapped = [
            'first_name' => '',
            'last_name' => '',
            'email' => '',
            'phone' => '',
            'address' => '',
            'birth_date' => null,
            'join_date' => null,
            'membership_id' => '',
            'membership_type_name' => '',
            'membership_type_id' => '',
            'allergies' => '',
            'dietary_preferences' => '',
            'special_requests' => '',
            'additional_notes' => '',
            'emergency_contact_name' => '',
            'emergency_contact_phone' => '',
            'emergency_contact_relationship' => '',
        ];
        
        // Adjust these indices based on your Excel column order
        if (isset($data[0])) $mapped['first_name'] = trim($data[0]);
        if (isset($data[1])) $mapped['last_name'] = trim($data[1]);
        if (isset($data[2])) $mapped['email'] = trim($data[2]);
        if (isset($data[3])) $mapped['phone'] = trim($data[3]);
        if (isset($data[4])) $mapped['address'] = trim($data[4]);
        if (isset($data[5])) $mapped['birth_date'] = $this->parseDate($data[5]);
        if (isset($data[6])) $mapped['join_date'] = $this->parseDate($data[6]);
        if (isset($data[7])) $mapped['membership_id'] = trim($data[7]);
        if (isset($data[8])) $mapped['membership_type_name'] = trim($data[8]);
        if (isset($data[9])) $mapped['membership_type_id'] = trim($data[9]);
        if (isset($data[10])) $mapped['allergies'] = trim($data[10]);
        if (isset($data[11])) $mapped['dietary_preferences'] = trim($data[11]);
        if (isset($data[12])) $mapped['special_requests'] = trim($data[12]);
        if (isset($data[13])) $mapped['additional_notes'] = trim($data[13]);
        if (isset($data[14])) $mapped['emergency_contact_name'] = trim($data[14]);
        if (isset($data[15])) $mapped['emergency_contact_phone'] = trim($data[15]);
        if (isset($data[16])) $mapped['emergency_contact_relationship'] = trim($data[16]);
        
        return $mapped;
    }

    /**
     * Determine membership type based on member data
     */
    private function determineMembershipType($memberData, $membershipTypes)
    {
        // Priority order for membership type determination:
        // 1. Explicit membership type name in data
        // 2. Membership type ID in data
        // 3. Default to first available membership type
        
        // Check if membership type name is provided in the data
        if (!empty($memberData['membership_type_name'])) {
            $membershipType = $membershipTypes->first(function($type) use ($memberData) {
                return strtolower(trim($type->name)) === strtolower(trim($memberData['membership_type_name']));
            });
            
            if ($membershipType) {
                return $membershipType;
            }
        }
        
        // Check if membership type ID is provided in the data
        if (!empty($memberData['membership_type_id'])) {
            $membershipType = $membershipTypes->first(function($type) use ($memberData) {
                return $type->id == $memberData['membership_type_id'];
            });
            
            if ($membershipType) {
                return $membershipType;
            }
        }
        
        // Check for VIP indicators in member data
        $vipIndicators = ['vip', 'premium', 'gold', 'platinum', 'diamond', 'executive'];
        $memberText = strtolower(implode(' ', array_filter([
            $memberData['first_name'] ?? '',
            $memberData['last_name'] ?? '',
            $memberData['additional_notes'] ?? '',
            $memberData['special_requests'] ?? ''
        ])));
        
        foreach ($membershipTypes as $type) {
            $typeName = strtolower($type->name);
            foreach ($vipIndicators as $indicator) {
                if (strpos($typeName, $indicator) !== false && strpos($memberText, $indicator) !== false) {
                    return $type;
                }
            }
        }
        
        // Check for standard indicators
        $standardIndicators = ['standard', 'basic', 'regular', 'silver', 'bronze'];
        foreach ($membershipTypes as $type) {
            $typeName = strtolower($type->name);
            foreach ($standardIndicators as $indicator) {
                if (strpos($typeName, $indicator) !== false) {
                    return $type;
                }
            }
        }
        
        // Default to the first available membership type
        return $membershipTypes->first();
    }

    /**
     * Parse date from various formats
     */
    private function parseDate($dateString)
    {
        if (empty($dateString)) {
            return null;
        }
        
        try {
            return Carbon::parse($dateString)->toDateString();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get available hotels for import
     */
    public function getHotels()
    {
        $hotels = Hotel::active()->get();
        return response()->json($hotels);
    }

    /**
     * Get membership types for a hotel
     */
    public function getMembershipTypes(Request $request)
    {
        $membershipTypes = MembershipType::where('hotel_id', $request->hotel_id)
            ->where('is_active', true)
            ->get();
        return response()->json($membershipTypes);
    }

    /**
     * Download sample template
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="members_import_template.csv"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // Add headers
            fputcsv($file, [
                'First Name',
                'Last Name', 
                'Email',
                'Phone',
                'Address',
                'Birth Date',
                'Join Date',
                'Membership ID',
                'Membership Type Name',
                'Membership Type ID',
                'Allergies',
                'Dietary Preferences',
                'Special Requests',
                'Additional Notes',
                'Emergency Contact Name',
                'Emergency Contact Phone',
                'Emergency Contact Relationship'
            ]);
            
            // Add sample data
            fputcsv($file, [
                'John',
                'Doe',
                'john.doe@example.com',
                '+255123456789',
                '123 Main Street, Dar es Salaam',
                '1990-05-15',
                '2024-01-01',
                'MS001',
                'VIP',
                '',
                'None',
                'Vegetarian',
                'Window seat preferred',
                'VIP customer',
                'Jane Doe',
                '+255987654321',
                'Spouse'
            ]);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
