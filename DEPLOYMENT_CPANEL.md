# Laravel Deployment Guide for cPanel VPS

## Prerequisites

### Server Requirements
- PHP 8.2 or higher
- Composer installed
- MySQL/MariaDB database
- Apache/Nginx web server
- OpenSSL PHP Extension
- PDO PHP Extension
- Mbstring PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension
- Ctype PHP Extension
- JSON PHP Extension
- Fileinfo PHP Extension
- BCMath PHP Extension
- GD or Imagick Extension (for image processing)

### cPanel Access
- cPanel login credentials
- FTP/SFTP access
- SSH access (if available)
- Database management access (phpMyAdmin)

## Step 1: Prepare Your Application

### 1.1 Update .env for Production

Create a production `.env` file:

```env
APP_NAME="Unicorn Invoicing System"
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://yourdomain.com

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax

CACHE_DRIVER=file
QUEUE_CONNECTION=sync

APP_CURRENCY=R
```

### 1.2 Generate Application Key

```bash
php artisan key:generate
```

### 1.3 Optimize for Production

```bash
# Clear all caches
php artisan optimize:clear

# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer install --optimize-autoloader --no-dev
```

### 1.4 Remove Development Files

Remove or ignore:
- `.env.example` (keep for reference)
- `node_modules/` (if not needed)
- `.git/` (optional, but recommended to keep)
- Test files
- Development documentation

## Step 2: Upload Files to Server

### 2.1 File Structure on cPanel

cPanel typically uses this structure:
```
/home/username/
├── public_html/          (or your domain folder)
│   ├── index.php        (Laravel's public/index.php)
│   ├── .htaccess        (Laravel's public/.htaccess)
│   └── assets/           (if you have public assets)
└── laravel_app/         (or any folder name)
    ├── app/
    ├── bootstrap/
    ├── config/
    ├── database/
    ├── public/
    ├── resources/
    ├── routes/
    ├── storage/
    ├── vendor/
    ├── .env
    ├── artisan
    └── composer.json
```

### 2.2 Upload Methods

**Option A: Using FTP/SFTP Client (FileZilla, WinSCP)**
1. Connect to your server
2. Upload entire Laravel project to a folder outside `public_html` (e.g., `laravel_app`)
3. Upload contents of `public/` folder to `public_html/` or your domain folder

**Option B: Using cPanel File Manager**
1. Log into cPanel
2. Navigate to File Manager
3. Upload files (may need to zip first for large uploads)
4. Extract if uploaded as zip

**Option C: Using Git (Recommended if SSH access)**
```bash
# On your local machine
git add .
git commit -m "Production ready"
git push origin main

# On server (via SSH)
cd ~/laravel_app
git pull origin main
```

## Step 3: Configure cPanel

### 3.1 Set Up Database

1. **Create Database:**
   - Go to cPanel → MySQL Databases
   - Create a new database (e.g., `username_invoicing`)
   - Note the full database name

2. **Create Database User:**
   - Create a new MySQL user
   - Set a strong password
   - Note the full username (usually `username_dbuser`)

3. **Assign Privileges:**
   - Add user to database
   - Grant ALL PRIVILEGES

4. **Update .env:**
   ```env
   DB_DATABASE=username_invoicing
   DB_USERNAME=username_dbuser
   DB_PASSWORD=your_password
   ```

### 3.2 Configure Document Root

**Option A: Using cPanel Subdomain/Domain Settings**
1. Go to cPanel → Subdomains or Addon Domains
2. Set document root to `public_html` or your domain folder
3. Point it to Laravel's `public` folder

**Option B: Using .htaccess in public_html**
Create/update `.htaccess` in `public_html`:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ /laravel_app/public/$1 [L]
</IfModule>
```

**Option C: Symlink (if SSH access)**
```bash
cd ~/public_html
ln -s ~/laravel_app/public/* .
```

### 3.3 Update public/index.php

Update the paths in `public/index.php`:

```php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
```

If Laravel is in a subdirectory, adjust paths accordingly.

## Step 4: Set File Permissions

### 4.1 Set Directory Permissions

Via SSH (if available):
```bash
cd ~/laravel_app

# Set directory permissions
find . -type d -exec chmod 755 {} \;

# Set file permissions
find . -type f -exec chmod 644 {} \;

# Make storage and bootstrap/cache writable
chmod -R 775 storage bootstrap/cache
chown -R username:username storage bootstrap/cache
```

Via cPanel File Manager:
1. Navigate to `storage` folder
2. Right-click → Change Permissions
3. Set to `775` (or `755` if 775 doesn't work)
4. Repeat for `bootstrap/cache`

### 4.2 Create Storage Symlink

```bash
php artisan storage:link
```

Or manually create symlink:
```bash
cd ~/laravel_app/public
ln -s ../storage/app/public storage
```

## Step 5: Run Laravel Setup Commands

### 5.1 Install Dependencies

Via SSH:
```bash
cd ~/laravel_app
composer install --optimize-autoloader --no-dev
```

Via cPanel Terminal (if available):
```bash
cd ~/laravel_app
php composer.phar install --optimize-autoloader --no-dev
```

### 5.2 Run Migrations

```bash
php artisan migrate --force
```

**Note:** Use `--force` flag in production to skip confirmation.

### 5.3 Seed Database (if needed)

```bash
php artisan db:seed --force
```

### 5.4 Create Session Table

```bash
php artisan session:table
php artisan migrate
```

### 5.5 Optimize Application

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

## Step 6: Configure Web Server

### 6.1 Apache Configuration (.htaccess)

Ensure `public/.htaccess` exists:

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

### 6.2 PHP Configuration

Check PHP version in cPanel:
1. Go to cPanel → Select PHP Version
2. Select PHP 8.2 or higher
3. Enable required extensions:
   - OpenSSL
   - PDO
   - Mbstring
   - Tokenizer
   - XML
   - Ctype
   - JSON
   - Fileinfo
   - BCMath
   - GD or Imagick

### 6.3 Increase PHP Limits

Create or update `php.ini` or use cPanel MultiPHP INI Editor:

```ini
upload_max_filesize = 20M
post_max_size = 20M
memory_limit = 256M
max_execution_time = 300
max_input_time = 300
```

## Step 7: SSL Certificate

### 7.1 Install SSL Certificate

1. Go to cPanel → SSL/TLS Status
2. Install Let's Encrypt SSL (free) or your own certificate
3. Force HTTPS redirect in `.env`:
   ```env
   APP_URL=https://yourdomain.com
   ```

### 7.2 Force HTTPS in Laravel

Update `AppServiceProvider.php`:

```php
public function boot()
{
    if (config('app.env') === 'production') {
        \URL::forceScheme('https');
    }
}
```

Or add to `public/.htaccess`:

```apache
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

## Step 8: Security Checklist

### 8.1 Environment Variables

- [ ] `APP_DEBUG=false`
- [ ] `APP_ENV=production`
- [ ] Strong `APP_KEY` generated
- [ ] Database credentials secure
- [ ] Session secure cookies enabled

### 8.2 File Permissions

- [ ] Storage folder writable (775)
- [ ] Bootstrap/cache writable (775)
- [ ] `.env` file not publicly accessible
- [ ] Sensitive files outside `public_html`

### 8.3 Additional Security

1. **Hide .env file:**
   Add to `public_html/.htaccess`:
   ```apache
   <Files .env>
       Order allow,deny
       Deny from all
   </Files>
   ```

2. **Disable Directory Listing:**
   ```apache
   Options -Indexes
   ```

3. **Protect sensitive directories:**
   ```apache
   <DirectoryMatch "^/.*/\.git/">
       Order allow,deny
       Deny from all
   </DirectoryMatch>
   ```

## Step 9: Test Deployment

### 9.1 Test Checklist

- [ ] Homepage loads correctly
- [ ] Login works
- [ ] Database connection works
- [ ] File uploads work (if applicable)
- [ ] PDF generation works
- [ ] Email sending works (if configured)
- [ ] HTTPS redirects work
- [ ] All routes accessible
- [ ] Assets (CSS/JS) load correctly

### 9.2 Common Issues

**Issue: 500 Internal Server Error**
- Check file permissions
- Check `.env` configuration
- Check error logs: `storage/logs/laravel.log`
- Check cPanel error logs

**Issue: White Screen**
- Enable `APP_DEBUG=true` temporarily
- Check PHP error logs
- Verify `APP_KEY` is set
- Check file permissions

**Issue: Assets Not Loading**
- Run `php artisan storage:link`
- Check asset paths in views
- Clear view cache: `php artisan view:clear`

**Issue: Database Connection Error**
- Verify database credentials in `.env`
- Check database user privileges
- Verify database exists

## Step 10: Post-Deployment

### 10.1 Set Up Cron Jobs

In cPanel → Cron Jobs, add:

```bash
* * * * * cd /home/username/laravel_app && php artisan schedule:run >> /dev/null 2>&1
```

Or for Laravel's task scheduler:
```bash
* * * * * /usr/local/bin/php /home/username/laravel_app/artisan schedule:run >> /dev/null 2>&1
```

### 10.2 Set Up Log Rotation

Configure log rotation to prevent disk space issues.

### 10.3 Backup Strategy

1. **Database Backups:**
   - Use cPanel → Backup
   - Set up automated backups
   - Or use Laravel backup package

2. **File Backups:**
   - Backup `.env` file
   - Backup `storage/app` if it contains important files
   - Backup uploaded files

### 10.4 Monitoring

- Set up error monitoring (e.g., Laravel Telescope, Sentry)
- Monitor disk space
- Monitor database size
- Check application logs regularly

## Step 11: Domain Configuration

### 11.1 Point Domain to cPanel

1. Update DNS records:
   - A record: Point to server IP
   - CNAME: Point www to domain

2. In cPanel:
   - Add domain or subdomain
   - Set document root to Laravel's `public` folder

### 11.2 Subdomain Setup

If using subdomain (e.g., `app.yourdomain.com`):
1. Create subdomain in cPanel
2. Set document root to `laravel_app/public`
3. Update `.env` `APP_URL`

## Quick Reference Commands

```bash
# Navigate to Laravel directory
cd ~/laravel_app

# Clear all caches
php artisan optimize:clear

# Cache for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
php artisan migrate --force

# Create storage symlink
php artisan storage:link

# Check Laravel version
php artisan --version

# View logs
tail -f storage/logs/laravel.log
```

## Support Resources

- Laravel Documentation: https://laravel.com/docs
- cPanel Documentation: https://docs.cpanel.net
- Laravel Deployment: https://laravel.com/docs/deployment

## Notes

- Always test in a staging environment first
- Keep backups before making changes
- Document your deployment process
- Keep `.env` file secure and never commit it
- Regularly update dependencies for security
