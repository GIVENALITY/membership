# Setup Guide for members.co.tz

## Quick Setup Steps

### 1. Update Your .env File
Copy the configuration from `env_members_domain.txt` to your `.env` file.

### 2. Choose Email Configuration
You have 4 options for email:

#### Option A: Google Workspace (Recommended)
```bash
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=noreply@members.co.tz
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@members.co.tz
MAIL_FROM_NAME="Membership MS"
```

#### Option B: Log Driver (For Testing)
```bash
MAIL_MAILER=log
```

#### Option C: cPanel Email
```bash
MAIL_MAILER=smtp
MAIL_HOST=mail.members.co.tz
MAIL_PORT=587
MAIL_USERNAME=noreply@members.co.tz
MAIL_PASSWORD=your-cpanel-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@members.co.tz
MAIL_FROM_NAME="Membership MS"
```

#### Option D: SendGrid (Free Tier)
```bash
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your-sendgrid-api-key
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@members.co.tz
MAIL_FROM_NAME="Membership MS"
```

### 3. Clear Application Cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### 4. Update Database (if needed)
```bash
mysql -u your_username -p your_database < database_migration_script.sql
```

### 5. Test the Application
- Visit: https://members.co.tz
- Test login functionality
- Test email sending (if configured)

### 6. Test Email Configuration
```bash
php test_email_config.php
```

## File Structure
Make sure your Laravel files are organized like this:
```
members.co.tz/
├── .htaccess (updated)
├── public/
│   ├── index.php
│   └── .htaccess
├── app/
├── config/
├── resources/
├── routes/
└── ... (other Laravel folders)
```

## Troubleshooting

### If you get directory listing:
- Check that your document root points to the `public` folder
- Verify the `.htaccess` file is in the root directory

### If emails don't work:
- Check your email configuration in `.env`
- Use the log driver for testing: `MAIL_MAILER=log`
- Check error logs: `tail -f storage/logs/laravel.log`

### If database connection fails:
- Verify database credentials in `.env`
- Check if the database exists
- Ensure MySQL is running

## Security Notes
- Keep your `.env` file secure
- Use strong passwords for database and email
- Enable SSL certificate for production
- Set `APP_DEBUG=false` for production
