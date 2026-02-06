# Debugging Browser Errors

## Step 1: Check Laravel Error Logs

```bash
cd ~/invoices.unicorn.com.na

# View recent errors
tail -50 storage/logs/laravel.log

# Or watch logs in real-time
tail -f storage/logs/laravel.log
```

## Step 2: Enable Debug Mode Temporarily

Edit `.env` file to see detailed errors:

```bash
nano .env
```

Change:
```env
APP_DEBUG=true
```

Then refresh browser to see detailed error.

**⚠️ Remember to set back to `false` after fixing!**

## Step 3: Common Errors and Fixes

### Error: "No application encryption key has been specified"

**Fix:**
```bash
php artisan key:generate
php artisan config:clear
```

### Error: "SQLSTATE[HY000] [2002] Connection refused" or Database Error

**Fix:**
```bash
# Check .env database settings
cat .env | grep DB_

# Test database connection
php artisan tinker
DB::connection()->getPdo();
exit
```

### Error: "The stream or file could not be opened" (Storage)

**Fix:**
```bash
chmod -R 775 storage bootstrap/cache
php artisan storage:link
```

### Error: "Class not found" or "Target class does not exist"

**Fix:**
```bash
php artisan optimize:clear
php composer.phar dump-autoload
php artisan config:cache
```

### Error: "Route [login] not defined"

**Fix:**
```bash
php artisan route:clear
php artisan route:cache
php artisan route:list
```

### Error: "View not found"

**Fix:**
```bash
php artisan view:clear
php artisan view:cache
```

### Error: "500 Internal Server Error"

**Check:**
1. File permissions
2. .env configuration
3. Laravel logs
4. PHP error logs in cPanel

## Step 4: Check PHP Error Logs

In cPanel:
1. Go to **Error Log**
2. Check for PHP errors
3. Look for specific error messages

## Step 5: Verify Basic Setup

```bash
cd ~/invoices.unicorn.com.na

# Check Laravel version
php artisan --version

# Check routes
php artisan route:list | head -10

# Check if storage is writable
ls -ld storage bootstrap/cache

# Check .env exists
ls -la .env

# Check APP_KEY is set
grep APP_KEY .env
```

## Step 6: Common Quick Fixes

Run all these:

```bash
cd ~/invoices.unicorn.com.na

# Clear all caches
php artisan optimize:clear

# Regenerate autoload
php composer.phar dump-autoload

# Fix permissions
chmod -R 775 storage bootstrap/cache

# Recreate storage link
rm -f public/storage
php artisan storage:link

# Regenerate config cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Test
php artisan --version
```

## Step 7: Check File Structure

```bash
cd ~/invoices.unicorn.com.na

# Verify key files exist
ls -la .env
ls -la vendor/autoload.php
ls -la bootstrap/app.php
ls -la public/index.php
ls -la storage/logs/laravel.log
```

## What to Share for Help

Please share:
1. **The exact error message** from the browser
2. **Output of:** `tail -50 storage/logs/laravel.log`
3. **Output of:** `php artisan --version`
4. **Output of:** `grep APP_KEY .env`
5. **What URL you're visiting**

This will help identify the exact issue!
