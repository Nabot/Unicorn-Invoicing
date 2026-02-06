# Fixing 404 Not Found Error

## Common Causes
1. Document root not pointing to `public` folder
2. `.htaccess` file missing or not working
3. Apache mod_rewrite not enabled
4. Wrong file paths in `public/index.php`

## Solution 1: Configure Document Root (Recommended)

### Via cPanel:

1. **Go to cPanel → Subdomains** (or Addon Domains)
2. **Find your domain:** `invoices.unicorn.com.na`
3. **Click "Change"** or "Manage"
4. **Set Document Root to:** `/home/unicorncom/invoices.unicorn.com.na/public`
5. **Save changes**

### Verify Document Root:

```bash
# Check if public folder exists
ls -la ~/invoices.unicorn.com.na/public/

# Should see: index.php, .htaccess, etc.
```

## Solution 2: Copy Public Contents to public_html

If your domain points to `public_html`:

```bash
cd ~/invoices.unicorn.com.na

# Copy public folder contents to public_html
cp -r public/* ~/public_html/

# Update paths in public_html/index.php
nano ~/public_html/index.php
```

Change these lines:
```php
require __DIR__.'/../invoices.unicorn.com.na/vendor/autoload.php';
$app = require_once __DIR__.'/../invoices.unicorn.com.na/bootstrap/app.php';
```

## Solution 3: Check .htaccess File

### Verify .htaccess exists:

```bash
ls -la ~/invoices.unicorn.com.na/public/.htaccess
```

### If missing, create it:

```bash
cat > ~/invoices.unicorn.com.na/public/.htaccess << 'EOF'
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
EOF
```

## Solution 4: Check public/index.php Paths

Verify `public/index.php` has correct paths:

```bash
cat ~/invoices.unicorn.com.na/public/index.php
```

Should show:
```php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
```

If paths are wrong, fix them:
```bash
nano ~/invoices.unicorn.com.na/public/index.php
```

## Solution 5: Enable mod_rewrite

### Check if mod_rewrite is enabled:

```bash
# Check Apache modules
httpd -M | grep rewrite
# Or
apache2ctl -M | grep rewrite
```

### Enable via cPanel:

1. Go to cPanel → **Apache Modules** or **Select PHP Version**
2. Enable **mod_rewrite**
3. Or contact hosting support

## Solution 6: Test Direct Access

Test if Laravel is working via command line:

```bash
cd ~/invoices.unicorn.com.na

# Test routes
php artisan route:list

# Test if application loads
php artisan serve --host=127.0.0.1 --port=8000
# Then visit: http://127.0.0.1:8000 (if you can access server IP)
```

## Solution 7: Check File Permissions

```bash
cd ~/invoices.unicorn.com.na

# Set correct permissions
find . -type d -exec chmod 755 {} \;
find . -type f -exec chmod 644 {} \;
chmod -R 775 storage bootstrap/cache
chmod 644 public/.htaccess
```

## Solution 8: Check Error Logs

```bash
# Check Laravel logs
tail -f ~/invoices.unicorn.com.na/storage/logs/laravel.log

# Check Apache error logs (location varies)
tail -f /usr/local/apache/logs/error_log
# Or
tail -f ~/logs/error_log
# Or check in cPanel → Error Log
```

## Quick Diagnostic Commands

Run these to diagnose:

```bash
cd ~/invoices.unicorn.com.na

# 1. Check if public folder exists
ls -la public/

# 2. Check if index.php exists
ls -la public/index.php

# 3. Check if .htaccess exists
ls -la public/.htaccess

# 4. Check if vendor exists
ls -la vendor/

# 5. Test Laravel via CLI
php artisan --version

# 6. List routes
php artisan route:list | head -20
```

## Most Common Fix

**90% of 404 errors are fixed by:**

1. Setting document root to `/home/unicorncom/invoices.unicorn.com.na/public`
2. Ensuring `.htaccess` exists in `public/` folder
3. Verifying `public/index.php` has correct paths

## Step-by-Step Fix

```bash
# Step 1: Verify structure
cd ~/invoices.unicorn.com.na
ls -la public/index.php
ls -la public/.htaccess

# Step 2: Check document root in cPanel
# Go to: cPanel → Subdomains → invoices.unicorn.com.na
# Set to: /home/unicorncom/invoices.unicorn.com.na/public

# Step 3: Test
# Visit: https://invoices.unicorn.com.na
```

## Alternative: Create .htaccess in Root

If you can't change document root, create `.htaccess` in root:

```bash
cat > ~/invoices.unicorn.com.na/.htaccess << 'EOF'
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
EOF
```

## Verify Setup

After fixing, verify:

```bash
# Check routes are registered
php artisan route:list

# Check if homepage route exists
php artisan route:list | grep GET

# Should see routes like: GET|HEAD /, /login, etc.
```
