# Super Admin Impersonation Feature

## Overview

The impersonation feature allows super admins to temporarily login as hotel managers to provide support, troubleshoot issues, or perform actions on behalf of hotel staff.

## Features

### 1. Impersonation Capabilities
- **Super Admin Only**: Only users with the `superadmin` role can impersonate other users
- **Manager Impersonation**: Super admins can impersonate hotel managers (users with `manager` role)
- **Security**: Cannot impersonate other super admins
- **Session Management**: Maintains original super admin session data for easy return

### 2. Visual Indicators
- **Impersonation Banner**: A prominent banner appears at the top of the page when impersonating
- **Clear Identification**: Shows who is being impersonated and at which hotel
- **Easy Exit**: One-click button to stop impersonation and return to super admin account

### 3. Logging & Audit
- **Comprehensive Logging**: All impersonation actions are logged with timestamps
- **Audit Trail**: Tracks who impersonated whom and when
- **Security Monitoring**: Helps identify potential security issues

## How to Use

### For Super Admins

1. **Access Hotel Management**:
   - Go to Super Admin Dashboard
   - Click "Manage Hotels" or "Impersonate Managers"
   - View the list of all hotels

2. **Start Impersonation**:
   - Find the hotel with the manager you want to impersonate
   - Click the impersonation button (user settings icon) next to the manager's name
   - Confirm the action when prompted

3. **During Impersonation**:
   - You'll see a red banner at the top indicating you're impersonating
   - You have full access to the manager's dashboard and features
   - All actions are performed as the manager user

4. **Stop Impersonation**:
   - Click "Stop Impersonating" in the banner
   - You'll be returned to your super admin account
   - Redirected to the super admin dashboard

### For Developers

#### Routes
```php
// Start impersonation
GET /impersonate/start/{userId}

// Stop impersonation
GET /impersonate/stop

// Check impersonation status
GET /impersonate/status
```

#### Controller
```php
App\Http\Controllers\ImpersonationController
```

#### Middleware
```php
App\Http\Middleware\ImpersonationMiddleware
```

## Security Considerations

### Access Control
- Only super admins can initiate impersonation
- Cannot impersonate other super admins
- Target user must be active and have a valid role

### Session Management
- Original super admin session is preserved
- Impersonation session data is stored separately
- Automatic cleanup when stopping impersonation

### Logging
- All impersonation events are logged
- Includes user IDs, timestamps, and actions
- Helps with security auditing

## Technical Implementation

### Database
No additional database tables required. Uses existing session storage.

### Session Data
```php
session([
    'impersonator_id' => $superadmin->id,
    'impersonator_name' => $superadmin->name,
    'impersonator_email' => $superadmin->email,
]);
```

### Middleware
The `ImpersonationMiddleware` automatically adds the impersonation banner to all web responses when impersonating.

### Translations
Supports both English and Swahili languages for all impersonation-related text.

## Testing

Run the impersonation tests:
```bash
php artisan test tests/Feature/ImpersonationTest.php
```

## Troubleshooting

### Common Issues

1. **Cannot impersonate user**
   - Ensure you're logged in as a super admin
   - Verify the target user is active and has manager role
   - Check that the target user is not another super admin

2. **Banner not showing**
   - Clear browser cache
   - Check that ImpersonationMiddleware is registered
   - Verify session data is present

3. **Cannot stop impersonation**
   - Try accessing `/impersonate/stop` directly
   - Clear session data manually if needed
   - Check logs for any errors

### Log Locations
- Application logs: `storage/logs/laravel.log`
- Look for entries with "Superadmin started impersonation" or "Superadmin stopped impersonation"

## Future Enhancements

Potential improvements for future versions:
- Time-limited impersonation sessions
- Impersonation approval workflow
- More granular permissions for impersonation
- Impersonation history tracking
- Email notifications for impersonation events
