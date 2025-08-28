# Domain Migration Guide: From Subdomain to Main Domain

## Current Setup
- **Current Domain**: `membership.kinara.co.tz` (subdomain)
- **Target Domain**: `members.co.tz` (main domain)

## Step 1: Update Environment Configuration

### Update your `.env` file:
```bash
# Change this line in your .env file
APP_URL=https://members.co.tz

# If you have any other domain-specific configurations, update them:
MAIL_FROM_ADDRESS="noreply@members.co.tz"
MAIL_FROM_NAME="Members.co.tz"
```

## Step 2: Update Application Configuration

### Update `config/app.php`:
```php
'url' => env('APP_URL', 'https://members.co.tz'),
```

## Step 3: Update Web Server Configuration

### Option A: Apache Configuration

#### Root `.htaccess` (update the commented redirect):
```apache
<IfModule mod_alias.c>
RedirectMatch 301 ^/$ https://members.co.tz/
</IfModule>
```

#### Virtual Host Configuration:
Create or update your Apache virtual host configuration:

```apache
<VirtualHost *:80>
    ServerName members.co.tz
    ServerAlias www.members.co.tz
    DocumentRoot /path/to/your/laravel/public
    
    <Directory /path/to/your/laravel/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/members.co.tz_error.log
    CustomLog ${APACHE_LOG_DIR}/members.co.tz_access.log combined
</VirtualHost>

<VirtualHost *:443>
    ServerName members.co.tz
    ServerAlias www.members.co.tz
    DocumentRoot /path/to/your/laravel/public
    
    SSLEngine on
    SSLCertificateFile /path/to/your/ssl/certificate.crt
    SSLCertificateKeyFile /path/to/your/ssl/private.key
    SSLCertificateChainFile /path/to/your/ssl/chain.crt
    
    <Directory /path/to/your/laravel/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/members.co.tz_error.log
    CustomLog ${APACHE_LOG_DIR}/members.co.tz_access.log combined
</VirtualHost>
```

### Option B: Nginx Configuration

```nginx
server {
    listen 80;
    server_name members.co.tz www.members.co.tz;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name members.co.tz www.members.co.tz;
    
    ssl_certificate /path/to/your/ssl/certificate.crt;
    ssl_certificate_key /path/to/your/ssl/private.key;
    
    root /path/to/your/laravel/public;
    index index.php index.html index.htm;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

## Step 4: DNS Configuration

### Update your DNS records:
1. **A Record**: Point `members.co.tz` to your server's IP address
2. **CNAME Record**: Point `www.members.co.tz` to `members.co.tz`
3. **Remove or redirect**: The old subdomain `membership.kinara.co.tz`

### Example DNS records:
```
Type    Name                    Value
A       members.co.tz           YOUR_SERVER_IP
CNAME   www.members.co.tz       members.co.tz
```

## Step 5: SSL Certificate

### Obtain SSL Certificate:
1. **Let's Encrypt** (recommended for free certificates):
   ```bash
   sudo certbot --apache -d members.co.tz -d www.members.co.tz
   ```

2. **Commercial SSL**: Purchase and install your SSL certificate

## Step 6: Update Application Code

### Check for hardcoded URLs in your codebase:
```bash
# Search for old domain references
grep -r "membership.kinara.co.tz" .
grep -r "kinara.co.tz" .
```

### Update any hardcoded URLs in:
- Views (`.blade.php` files)
- Controllers
- Configuration files
- Email templates
- JavaScript files

## Step 7: Database Updates

### Update any stored URLs in the database:
```sql
-- Update hotel domains if stored
UPDATE hotels SET domain = REPLACE(domain, 'membership.kinara.co.tz', 'members.co.tz');

-- Update any other domain references
UPDATE system_settings SET value = REPLACE(value, 'membership.kinara.co.tz', 'members.co.tz') 
WHERE value LIKE '%membership.kinara.co.tz%';
```

## Step 8: Clear Application Cache

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

## Step 9: Test the Migration

### Test URLs:
1. `https://members.co.tz` - Main site
2. `https://www.members.co.tz` - WWW subdomain
3. `https://members.co.tz/login` - Login page
4. `https://members.co.tz/dashboard` - Dashboard (after login)

### Test functionality:
- User registration and login
- Email sending
- File uploads
- API endpoints
- Public event pages

## Step 10: Set Up Redirects (Optional)

### Redirect old subdomain to new domain:
```apache
# In your old subdomain's virtual host
<VirtualHost *:80>
    ServerName membership.kinara.co.tz
    Redirect 301 / https://members.co.tz/
</VirtualHost>

<VirtualHost *:443>
    ServerName membership.kinara.co.tz
    Redirect 301 / https://members.co.tz/
</VirtualHost>
```

## Step 11: Update External Services

### Update any external service configurations:
- Email service providers
- Payment gateways
- Analytics services
- Social media integrations
- Third-party APIs

## Step 12: Monitor and Verify

### Check for issues:
1. **Error logs**: Monitor web server and Laravel logs
2. **SSL certificate**: Verify SSL is working correctly
3. **Email delivery**: Test email functionality
4. **Performance**: Monitor site performance
5. **SEO**: Update sitemap and robots.txt

## Troubleshooting

### Common Issues:
1. **SSL Certificate Errors**: Ensure certificate is properly installed
2. **404 Errors**: Check file permissions and .htaccess configuration
3. **Email Issues**: Update mail configuration for new domain
4. **Database Connection**: Verify database configuration
5. **Cache Issues**: Clear all application caches

### Useful Commands:
```bash
# Check SSL certificate
openssl s_client -connect members.co.tz:443 -servername members.co.tz

# Test DNS resolution
nslookup members.co.tz
dig members.co.tz

# Check web server status
sudo systemctl status apache2
sudo systemctl status nginx

# Check Laravel logs
tail -f storage/logs/laravel.log
```

## Post-Migration Checklist

- [ ] Domain resolves correctly
- [ ] SSL certificate is valid
- [ ] All pages load without errors
- [ ] User authentication works
- [ ] Email sending works
- [ ] File uploads work
- [ ] API endpoints respond correctly
- [ ] Old subdomain redirects properly
- [ ] Search engines can crawl the site
- [ ] Analytics tracking works
- [ ] Performance is acceptable
- [ ] Error logs are clean

## Rollback Plan

If issues arise, you can quickly rollback by:
1. Reverting DNS changes
2. Restoring old virtual host configuration
3. Reverting .env changes
4. Clearing application cache

Keep the old subdomain configuration active during the transition period for safety.
