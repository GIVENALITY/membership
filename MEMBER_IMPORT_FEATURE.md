# Member Import Feature

## Overview

The member import feature allows you to bulk import member data from Excel or CSV files into the system. This is particularly useful for importing existing member databases or migrating data from other systems.

## Features

### 1. File Upload Import
- **Supported Formats**: XLSX, XLS, CSV
- **File Size Limit**: 10MB maximum
- **Validation**: Automatic validation of data format and required fields
- **Error Handling**: Detailed error reporting for failed imports

### 2. Storage Import
- **Direct Import**: Import from the `members.xlsx` file in the storage folder
- **Bravo Coco Specific**: Pre-configured for Bravo Coco hotel
- **Batch Processing**: Process large datasets efficiently

### 3. Data Validation
- **Required Fields**: First Name, Last Name, Email, Phone
- **Optional Fields**: Address, Birth Date, Join Date, Membership ID, etc.
- **Duplicate Detection**: Prevents importing duplicate members
- **Format Validation**: Validates email addresses, phone numbers, and dates

## How to Use

### For Bravo Coco Hotel Import

1. **Access Import Page**:
   - Go to Members > Import Members
   - Or navigate to `/members/import`

2. **Select Import Method**:
   - **File Upload**: Upload your Excel/CSV file
   - **Storage Import**: Use the existing `members.xlsx` file

3. **Configure Import Settings**:
   - Select "Bravo Coco" as the hotel (pre-selected)
   - Choose the appropriate membership type
   - Review the file format requirements

4. **Upload and Process**:
   - Select your file or use the storage option
   - Click "Import Members"
   - Review the import results

### File Format Requirements

#### Required Columns:
- **First Name** - Member's first name
- **Last Name** - Member's last name  
- **Email** - Member's email address
- **Phone** - Member's phone number

#### Optional Columns:
- **Address** - Member's address
- **Birth Date** - Format: YYYY-MM-DD
- **Join Date** - Format: YYYY-MM-DD
- **Membership ID** - Auto-generated if not provided
- **Membership Type Name** - Name of membership type (e.g., "VIP", "Standard", "Premium")
- **Membership Type ID** - ID of membership type (numeric)
- **Allergies** - Member's allergies
- **Dietary Preferences** - Member's dietary preferences
- **Special Requests** - Special requests or notes
- **Additional Notes** - Additional member information
- **Emergency Contact Name** - Emergency contact person
- **Emergency Contact Phone** - Emergency contact phone
- **Emergency Contact Relationship** - Relationship to member

### Sample Data Format

```csv
First Name,Last Name,Email,Phone,Address,Birth Date,Join Date,Membership ID,Membership Type Name,Membership Type ID,Allergies,Dietary Preferences,Special Requests,Additional Notes,Emergency Contact Name,Emergency Contact Phone,Emergency Contact Relationship
John,Doe,john.doe@example.com,+255123456789,123 Main Street Dar es Salaam,1990-05-15,2024-01-01,MS001,VIP,,None,Vegetarian,Window seat preferred,VIP customer,Jane Doe,+255987654321,Spouse
Jane,Smith,jane.smith@example.com,+255123456790,456 Oak Avenue Dar es Salaam,1985-08-20,2024-01-02,MS002,Standard,,Peanuts,None,Quiet table preferred,Regular customer,John Smith,+255987654322,Spouse
```

## Technical Implementation

### Controller
```php
App\Http\Controllers\MemberImportController
```

### Routes
```php
GET  /members/import                    # Show import form
POST /members/import                    # Process file upload
POST /members/import/storage            # Import from storage
GET  /members/import/membership-types   # Get membership types
GET  /members/import/template           # Download template
```

### Database Integration
- Uses existing `Member` model
- Automatic membership ID generation
- Hotel and membership type association
- Transaction-based import for data integrity

### Membership Type Assignment Logic

The importer uses intelligent membership type assignment with the following priority order:

1. **Explicit Membership Type Name** - If provided in the import file
2. **Explicit Membership Type ID** - If provided in the import file
3. **VIP Indicators** - Automatic detection based on keywords in member data
4. **Standard Indicators** - Automatic detection for standard memberships
5. **Default Assignment** - Falls back to the first available membership type

#### VIP Detection Keywords:
- "VIP", "Premium", "Gold", "Platinum", "Diamond", "Executive"
- Searches in: First Name, Last Name, Additional Notes, Special Requests

#### Standard Detection Keywords:
- "Standard", "Basic", "Regular", "Silver", "Bronze"
- Searches in membership type names

#### Example Assignment Logic:
```php
// Member with "VIP" in notes gets VIP membership type
"John Doe" + "VIP customer" → VIP Membership Type

// Member with "Standard" in membership type name
"Jane Smith" + Standard Membership Type → Standard Membership Type

// Member with no indicators gets default membership type
"Bob Wilson" → First Available Membership Type
```

## Error Handling

### Common Import Errors
1. **Missing Required Fields**: First name, last name, email, or phone missing
2. **Invalid Email Format**: Email address format validation
3. **Duplicate Members**: Member already exists with same email or membership ID
4. **Invalid Date Format**: Dates must be in YYYY-MM-DD format
5. **File Format Issues**: Unsupported file format or corrupted file

### Error Reporting
- Detailed error messages for each failed row
- Row number identification for easy troubleshooting
- Logging of all import activities
- Success/failure counts

## Security Features

### Access Control
- Requires authentication
- Hotel-specific data isolation
- Role-based permissions

### Data Validation
- Input sanitization
- SQL injection prevention
- File type validation
- Size limit enforcement

## Logging and Audit

### Import Logs
- All import activities are logged
- Includes user, timestamp, and results
- Error details for failed imports
- Success counts and member details

### Audit Trail
- Tracks who imported what data
- When imports were performed
- Which hotel the data was imported to

## Best Practices

### Before Import
1. **Backup Data**: Always backup existing data before large imports
2. **Test Import**: Use a small sample file first
3. **Validate Data**: Check your source data for accuracy
4. **Review Format**: Ensure your file matches the required format

### During Import
1. **Monitor Progress**: Watch for error messages
2. **Check Results**: Review the import summary
3. **Verify Data**: Check that imported data is correct

### After Import
1. **Review Members**: Check the imported member list
2. **Test Functionality**: Verify member features work correctly
3. **Clean Up**: Remove temporary files if needed

## Troubleshooting

### Common Issues

1. **File Not Uploading**
   - Check file size (max 10MB)
   - Verify file format (XLSX, XLS, CSV)
   - Ensure file is not corrupted

2. **Import Fails**
   - Check error messages for specific issues
   - Verify required fields are present
   - Ensure data format is correct

3. **Duplicate Members**
   - Check for existing members with same email
   - Verify membership IDs are unique
   - Use different membership IDs if needed

4. **Date Format Issues**
   - Ensure dates are in YYYY-MM-DD format
   - Check for invalid date values
   - Use consistent date formatting

### Getting Help
- Check the import error messages
- Review the application logs
- Contact system administrator for technical issues

## Future Enhancements

Potential improvements for future versions:
- **Excel Library Integration**: Direct Excel file reading
- **Bulk Operations**: Update existing members
- **Data Mapping**: Custom field mapping
- **Scheduled Imports**: Automated import scheduling
- **Import Templates**: Hotel-specific templates
- **Data Validation Rules**: Custom validation rules
- **Import History**: Track all import activities
- **Rollback Feature**: Undo import operations
