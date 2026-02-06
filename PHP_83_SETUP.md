# Setup with PHP 8.3.30

## Good News!
PHP 8.3.30 is perfect for Laravel 11! It's even better than 8.2.

## Fix the Parse Error

The parse error is likely because vendor was installed with wrong PHP version. Let's fix it:

```bash
cd ~/invoices.unicorn.com.na

# Step 1: Remove vendor and lock file
rm -rf vendor composer.lock

# Step 2: Verify PHP version
php -v
# Should show: PHP 8.3.30

# Step 3: Reinstall dependencies for PHP 8.3
php -d allow_url_fopen=On composer.phar install --optimize-autoloader --no-dev

# Step 4: Verify installation
php artisan --version
```

## Complete Setup Sequence for PHP 8.3

```bash
cd ~/invoices.unicorn.com.na

# Clean install
rm -rf vendor composer.lock
php -d allow_url_fopen=On composer.phar install --optimize-autoloader --no-dev

# Verify PHP version
php -v

# Set permissions
chmod -R 775 storage bootstrap/cache

# Setup Laravel
php artisan storage:link
php artisan key:generate
php artisan migrate --force
php artisan session:table
php artisan migrate --force
php artisan db:seed --force

# Cache configuration
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Test
php artisan --version
```

## Verify PHP 8.3 is Being Used

```bash
# Check PHP version
php -v

# Check what composer sees
php -d allow_url_fopen=On composer.phar show --platform

# Both should show PHP 8.3.30
```

## Update .env for PHP 8.3

Your `.env` file should have:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://invoices.unicorn.com.na
```

PHP 8.3.30 is fully compatible with Laravel 11, so you're all set!
