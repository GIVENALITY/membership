# Database Migrations for Membership MS

This document describes the database structure for the restaurant membership management system.

## Migration Files

### 1. `2024_01_01_000001_create_members_table.php`
**Purpose**: Stores member information and membership details

**Fields**:
- `id` - Primary key
- `membership_id` - Unique membership ID (MS001, MS002, etc.)
- `first_name`, `last_name` - Member's full name
- `email` - Unique email address
- `phone` - Contact phone number
- `address` - Member's address (optional)
- `birth_date` - Date of birth for birthday alerts
- `join_date` - When member joined
- `membership_type` - basic, premium, vip
- `status` - active, inactive, suspended
- `total_visits` - Number of restaurant visits
- `total_spent` - Total amount spent at restaurant
- `current_discount_rate` - Current discount percentage
- `last_visit_at` - Timestamp of last visit
- `created_at`, `updated_at` - Timestamps

### 2. `2024_01_01_000002_create_dining_visits_table.php`
**Purpose**: Tracks individual dining visits and transactions

**Fields**:
- `id` - Primary key
- `member_id` - Foreign key to members table
- `bill_amount` - Original bill amount
- `discount_amount` - Amount of discount applied
- `final_amount` - Final amount after discount
- `discount_rate` - Percentage discount applied
- `receipt_path` - Path to uploaded receipt file
- `notes` - Additional notes about the visit
- `visited_at` - When the visit occurred
- `created_at`, `updated_at` - Timestamps

### 3. `2024_01_01_000003_create_member_presence_table.php`
**Purpose**: Tracks when members are present in the restaurant

**Fields**:
- `id` - Primary key
- `member_id` - Foreign key to members table
- `date` - Date of presence
- `check_in_time` - When member checked in
- `check_out_time` - When member left (optional)
- `status` - present, left
- `notes` - Additional notes
- `created_at`, `updated_at` - Timestamps
- **Unique constraint**: One presence record per member per day

### 4. `2024_01_01_000004_create_email_notifications_table.php`
**Purpose**: Tracks email notifications sent to members

**Fields**:
- `id` - Primary key
- `member_id` - Foreign key to members table (optional)
- `email` - Recipient email address
- `subject` - Email subject line
- `message` - Email content
- `type` - welcome, birthday, custom
- `status` - pending, sent, failed
- `sent_at` - When email was sent
- `error_message` - Error details if failed
- `created_at`, `updated_at` - Timestamps

### 5. `2024_01_01_000005_create_system_settings_table.php`
**Purpose**: Stores system configuration and email templates

**Fields**:
- `id` - Primary key
- `key` - Setting key (unique)
- `value` - Setting value
- `type` - string, boolean, json
- `description` - Description of the setting
- `created_at`, `updated_at` - Timestamps

## Seeders

### 1. `SystemSettingsSeeder.php`
Populates system settings with:
- Welcome email template
- Birthday email template
- Discount rules configuration
- Restaurant information
- Currency settings

### 2. `SampleDataSeeder.php`
Creates sample data for testing:
- Sample members (John Doe, Jane Smith)
- Sample dining visits
- Sample presence records

## Running Migrations

```bash
# Run all migrations
php artisan migrate

# Run with seeders
php artisan migrate --seed

# Run specific seeder
php artisan db:seed --class=SystemSettingsSeeder
php artisan db:seed --class=SampleDataSeeder
```

## Database Relationships

```
members (1) -----> (many) dining_visits
members (1) -----> (many) member_presence
members (1) -----> (many) email_notifications
```

## Key Features Supported

1. **Member Management**: Complete member profiles with membership tracking
2. **Visit Tracking**: Detailed dining history with receipt uploads
3. **Presence Management**: Track when members are in the restaurant
4. **Email Automation**: Welcome and birthday email templates
5. **Discount Calculation**: Automatic discount based on visit count
6. **System Configuration**: Flexible settings for customization

## Sample Data

The `SampleDataSeeder` creates:
- 2 sample members with different membership levels
- 3 sample dining visits with discounts
- 2 sample presence records for today

This provides a complete testing environment for all system features. 