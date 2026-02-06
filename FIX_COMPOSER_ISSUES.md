# Fixing Composer Issues on cPanel

## Issue 1: allow_url_fopen is disabled

## Solution 1: Use -d flag to enable it temporarily (Quickest)

Run composer setup with the flag:

```bash
cd ~/invoices.unicorn.com.na

# Download composer with allow_url_fopen enabled
php -d allow_url_fopen=On -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -d allow_url_fopen=On composer-setup.php
php -r "unlink('composer-setup.php');"

# Now use composer.phar with the flag
php -d allow_url_fopen=On composer.phar install --optimize-autoloader --no-dev
```

## Solution 2: Enable allow_url_fopen in php.ini

### Find your php.ini file:

```bash
# Check which ini files PHP is using
php --ini

# This will show something like:
# Loaded Configuration File: /opt/cpanel/ea-php82/root/etc/php.ini
# Scan for additional .ini files in: /opt/cpanel/ea-php82/root/etc/php.d
```

### Edit php.ini:

```bash
# Find the php.ini file (from php --ini output)
nano /opt/cpanel/ea-php82/root/etc/php.ini

# Or use the path shown by php --ini
# Search for allow_url_fopen (Ctrl+W to search)
# Change: allow_url_fopen = Off
# To: allow_url_fopen = On

# Save and exit (Ctrl+X, Y, Enter)
```

### Via cPanel (Easier):

1. Go to cPanel → **Select PHP Version** or **MultiPHP INI Editor**
2. Select your PHP version (8.2 or higher)
3. Find `allow_url_fopen` setting
4. Set it to **On**
5. Click **Save**

### Restart PHP (if needed):

```bash
# Usually not needed on cPanel, but if changes don't take effect:
# Contact your hosting provider or check cPanel PHP settings
```

## Solution 3: Download composer.phar directly (Bypass installer)

If the installer keeps failing, download composer.phar directly:

```bash
cd ~/invoices.unicorn.com.na

# Download composer.phar directly
wget https://getcomposer.org/download/latest-stable/composer.phar

# Or using curl with -d flag
php -d allow_url_fopen=On -r "file_put_contents('composer.phar', file_get_contents('https://getcomposer.org/download/latest-stable/composer.phar'));"

# Make it executable
chmod +x composer.phar

# Now use it
php -d allow_url_fopen=On composer.phar install --optimize-autoloader --no-dev
```

## Complete Working Solution

Here's the complete sequence that should work:

```bash
cd ~/invoices.unicorn.com.na

# Method 1: Download composer.phar directly (Recommended)
wget https://getcomposer.org/download/latest-stable/composer.phar
chmod +x composer.phar

# Method 2: Or use curl with allow_url_fopen enabled
# php -d allow_url_fopen=On -r "file_put_contents('composer.phar', file_get_contents('https://getcomposer.org/download/latest-stable/composer.phar'));"

# Verify composer.phar exists
ls -la composer.phar

# Install dependencies with allow_url_fopen enabled
php -d allow_url_fopen=On composer.phar install --optimize-autoloader --no-dev
```

## Alternative: Use cPanel's Composer Tool

1. Log into cPanel
2. Look for **"Composer"** or **"PHP Composer"** in the software section
3. Navigate to your Laravel directory
4. Use the cPanel interface to run: `install --optimize-autoloader --no-dev`

## After Composer Install Works

Once dependencies are installed, continue setup:

```bash
cd ~/invoices.unicorn.com.na

# Set permissions
chmod -R 775 storage bootstrap/cache

# Create storage link
php artisan storage:link

# Generate app key (if not done)
php artisan key:generate

# Run migrations
php artisan migrate --force

# Seed database (optional)
php artisan db:seed --force

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Troubleshooting

### If wget doesn't work:

```bash
# Try curl
curl -o composer.phar https://getcomposer.org/download/latest-stable/composer.phar

# Or download via browser and upload via FTP
# Visit: https://getcomposer.org/download/latest-stable/composer.phar
# Download and upload to your server
```

### If composer.phar still can't download packages:

You may need to enable allow_url_fopen permanently. Check cPanel PHP settings.

### Verify allow_url_fopen status:

```bash
php -r "echo ini_get('allow_url_fopen') ? 'Enabled' : 'Disabled';"
```

### Check PHP version:

```bash
php -v
# Should be 8.2 or higher
```

## Quick Command Reference

```bash
# Download composer.phar
wget https://getcomposer.org/download/latest-stable/composer.phar
chmod +x composer.phar

# Install dependencies
php -d allow_url_fopen=On composer.phar install --optimize-autoloader --no-dev

# Or if allow_url_fopen is enabled in php.ini:
php composer.phar install --optimize-autoloader --no-dev
```
