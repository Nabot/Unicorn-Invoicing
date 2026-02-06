# Fixing PHP Version Compatibility Issue

## Problem
Your `composer.lock` file was created with PHP 8.4+ dependencies, but your server has PHP 8.2.30.

## Solution: Regenerate composer.lock for PHP 8.2

### Option 1: Delete composer.lock and Update (Recommended)

```bash
cd ~/invoices.unicorn.com.na

# Delete the incompatible lock file
rm composer.lock

# Update composer to regenerate lock file for PHP 8.2
php -d allow_url_fopen=On composer.phar update --no-dev

# This will regenerate composer.lock with packages compatible with PHP 8.2
```

### Option 2: Use --ignore-platform-reqs (Quick Fix, Not Recommended)

```bash
cd ~/invoices.unicorn.com.na

# Install ignoring platform requirements
php -d allow_url_fopen=On composer.phar install --optimize-autoloader --no-dev --ignore-platform-reqs

# WARNING: This may cause runtime issues if packages actually require PHP 8.4+
```

### Option 3: Update PHP Version (If Available)

If your cPanel supports PHP 8.4:

1. Go to cPanel → **Select PHP Version**
2. Select **PHP 8.4** (if available)
3. Then run: `php -d allow_url_fopen=On composer.phar install --optimize-autoloader --no-dev`

## Recommended Solution (Step by Step)

```bash
cd ~/invoices.unicorn.com.na

# Step 1: Remove incompatible lock file
rm composer.lock

# Step 2: Update composer to regenerate lock for PHP 8.2
php -d allow_url_fopen=On composer.phar update --no-dev

# Step 3: Verify installation
ls -la vendor/

# Step 4: Continue with setup
chmod -R 775 storage bootstrap/cache
php artisan storage:link
php artisan key:generate
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Why This Happened

The `composer.lock` file was likely created on a machine with PHP 8.4+, which locked dependencies to versions requiring PHP 8.4+. By deleting it and running `composer update`, Composer will resolve dependencies compatible with PHP 8.2.

## Verify PHP Version

```bash
# Check current PHP version
php -v

# Should show: PHP 8.2.30
```

## After Fixing

Once dependencies install successfully, you should see:

```bash
ls -la vendor/
# Should show many packages installed

php artisan --version
# Should show Laravel version
```

## Complete Working Sequence

```bash
cd ~/invoices.unicorn.com.na

# Remove incompatible lock file
rm composer.lock

# Regenerate for PHP 8.2
php -d allow_url_fopen=On composer.phar update --no-dev

# Set permissions
chmod -R 775 storage bootstrap/cache

# Setup Laravel
php artisan storage:link
php artisan key:generate
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```
