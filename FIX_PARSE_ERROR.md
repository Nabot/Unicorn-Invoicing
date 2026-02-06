# Fixing PHP Parse Error in Vendor Directory

## Problem
The error indicates that vendor packages were installed with PHP 8.4+ code, but your server runs PHP 8.2.30.

## Solution: Clean Install for PHP 8.2

### Step 1: Remove Vendor Directory and Lock File

```bash
cd ~/invoices.unicorn.com.na

# Remove vendor directory (contains incompatible packages)
rm -rf vendor

# Remove composer.lock (if it exists)
rm -f composer.lock
```

### Step 2: Reinstall Dependencies for PHP 8.2

```bash
# Install dependencies fresh for PHP 8.2
php -d allow_url_fopen=On composer.phar install --optimize-autoloader --no-dev

# This will resolve packages compatible with PHP 8.2
```

### Step 3: Verify Installation

```bash
# Check if vendor directory was created
ls -la vendor/

# Check PHP version
php -v

# Should show: PHP 8.2.30
```

## Complete Fix Sequence

Run all these commands:

```bash
cd ~/invoices.unicorn.com.na

# Remove incompatible packages
rm -rf vendor composer.lock

# Reinstall for PHP 8.2
php -d allow_url_fopen=On composer.phar install --optimize-autoloader --no-dev

# Verify installation
ls -la vendor/psy/psysh/src/Exception/ParseErrorException.php

# Continue with setup
chmod -R 775 storage bootstrap/cache
php artisan storage:link
php artisan key:generate
php artisan migrate --force
```

## Alternative: Use Platform Override

If the above doesn't work, force PHP 8.2 platform:

```bash
cd ~/invoices.unicorn.com.na

# Remove vendor
rm -rf vendor composer.lock

# Install with platform override
php -d allow_url_fopen=On composer.phar install --optimize-autoloader --no-dev --platform=php:8.2.30
```

## Check Composer Platform Configuration

Verify composer is detecting correct PHP version:

```bash
php -d allow_url_fopen=On composer.phar show --platform

# Should show PHP version as 8.2.30
```

## If Still Getting Errors

### Option 1: Check PHP Version Used by Composer

```bash
# Check what PHP version composer sees
php -d allow_url_fopen=On composer.phar --version
php -v

# Both should show PHP 8.2.30
```

### Option 2: Update composer.json Platform Requirement

Edit `composer.json` to be more specific:

```bash
nano composer.json
```

Ensure the PHP requirement is:
```json
"require": {
    "php": "^8.2",
    ...
}
```

Then:
```bash
rm -rf vendor composer.lock
php -d allow_url_fopen=On composer.phar install --optimize-autoloader --no-dev
```

### Option 3: Check for Multiple PHP Versions

```bash
# Check which PHP is being used
which php
php -v

# Check if there are multiple PHP versions
ls -la /usr/bin/php*
ls -la /usr/local/bin/php*

# Use specific PHP version if needed
/usr/bin/php82 -d allow_url_fopen=On composer.phar install --optimize-autoloader --no-dev
```

## Verify the Fix

After reinstalling, test:

```bash
# Test if parse error is gone
php artisan tinker --version

# Or try to run any artisan command
php artisan --version

# Check the problematic file
head -50 vendor/psy/psysh/src/Exception/ParseErrorException.php
```

## Complete Working Solution

```bash
cd ~/invoices.unicorn.com.na

# Step 1: Clean slate
rm -rf vendor composer.lock

# Step 2: Verify PHP version
php -v
# Should be 8.2.30

# Step 3: Install dependencies
php -d allow_url_fopen=On composer.phar install --optimize-autoloader --no-dev

# Step 4: Verify no parse errors
php artisan --version

# Step 5: Continue setup
chmod -R 775 storage bootstrap/cache
php artisan storage:link
php artisan key:generate
php artisan migrate --force
php artisan session:table
php artisan migrate --force
php artisan db:seed --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Troubleshooting

### If vendor directory is very large and removal is slow:

```bash
# Use find to remove (faster for large directories)
find vendor -type f -delete
find vendor -type d -delete
rmdir vendor 2>/dev/null || rm -rf vendor
```

### If composer install still installs wrong versions:

```bash
# Check composer platform
php -d allow_url_fopen=On composer.phar show --platform

# Force platform version
php -d allow_url_fopen=On composer.phar install --optimize-autoloader --no-dev --ignore-platform-reqs=php
```

### Check specific package version:

```bash
# Check what version of psy/psysh was installed
php -d allow_url_fopen=On composer.phar show psy/psysh
```
