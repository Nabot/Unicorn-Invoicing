# Next Steps After Composer Install

## Step 1: Set File Permissions

```bash
cd ~/invoices.unicorn.com.na

# Set storage and cache directories to be writable
chmod -R 775 storage bootstrap/cache

# Verify permissions
ls -ld storage bootstrap/cache
```

## Step 2: Create Storage Symlink

```bash
# Create symbolic link from public/storage to storage/app/public
php artisan storage:link

# Verify it was created
ls -la public/storage
```

## Step 3: Generate Application Key

```bash
# Generate APP_KEY in .env file
php artisan key:generate

# Verify .env has APP_KEY set
grep APP_KEY .env
```

## Step 4: Configure .env File

Make sure your `.env` file has correct settings:

```bash
# Edit .env file
nano .env
```

Verify these settings:
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://invoices.unicorn.com.na` (or your domain)
- Database credentials are correct
- `SESSION_SECURE_COOKIE=true`

## Step 5: Run Database Migrations

```bash
# Run migrations to create database tables
php artisan migrate --force

# Create session table (if not exists)
php artisan session:table
php artisan migrate --force
```

## Step 6: Seed Database (Optional - Creates Default Users)

```bash
# Seed database with default users and sample data
php artisan db:seed --force
```

**Default Login Credentials** (after seeding):
- **Admin:** `admin@example.com` / `password`
- **Staff:** `staff@example.com` / `password`
- **Agent:** `agent@example.com` / `password`

⚠️ **IMPORTANT:** Change these passwords immediately after first login!

## Step 7: Cache Configuration for Production

```bash
# Clear all caches first
php artisan optimize:clear

# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache
```

## Step 8: Configure Document Root

### Option A: Point Domain to Laravel's Public Folder

1. Go to cPanel → **Subdomains** or **Addon Domains**
2. Find `invoices.unicorn.com.na`
3. Set **Document Root** to: `/home/unicorncom/invoices.unicorn.com.na/public`
4. Save

### Option B: Copy Public Contents to public_html

If your domain points to `public_html`:

```bash
# Copy public folder contents to public_html
cp -r ~/invoices.unicorn.com.na/public/* ~/public_html/

# Update paths in public_html/index.php
nano ~/public_html/index.php
```

Change these lines:
```php
require __DIR__.'/../invoices.unicorn.com.na/vendor/autoload.php';
$app = require_once __DIR__.'/../invoices.unicorn.com.na/bootstrap/app.php';
```

## Step 9: Test Database Connection

```bash
# Test database connection
php artisan tinker
```

In tinker, type:
```php
DB::connection()->getPdo();
```

If successful, you'll see connection info. Type `exit` to leave.

## Step 10: Test the Application

1. **Visit your domain:** `https://invoices.unicorn.com.na`
2. **Check for errors:**
   - If white screen, check: `tail -f storage/logs/laravel.log`
   - Temporarily enable debug: Set `APP_DEBUG=true` in `.env` to see errors

3. **Test login:**
   - Go to login page
   - Use default credentials if you seeded: `admin@example.com` / `password`

## Step 11: Install SSL Certificate (If Not Done)

1. Go to cPanel → **SSL/TLS Status**
2. Install **Let's Encrypt SSL** for your domain
3. Force HTTPS (already configured in AppServiceProvider)

## Complete Command Sequence (Copy-Paste)

Run all these commands in order:

```bash
cd ~/invoices.unicorn.com.na

# Set permissions
chmod -R 775 storage bootstrap/cache

# Create storage link
php artisan storage:link

# Generate app key
php artisan key:generate

# Run migrations
php artisan migrate --force

# Create session table
php artisan session:table
php artisan migrate --force

# Seed database (optional)
php artisan db:seed --force

# Cache everything
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Test database connection
php artisan tinker
# Type: DB::connection()->getPdo();
# Type: exit
```

## Verification Checklist

After completing all steps, verify:

- [ ] `storage` and `bootstrap/cache` are writable (775)
- [ ] `public/storage` symlink exists
- [ ] `.env` file has `APP_KEY` set
- [ ] Database migrations ran successfully
- [ ] Database seeded (if you ran seeder)
- [ ] Configuration cached
- [ ] Document root configured correctly
- [ ] Website accessible via browser
- [ ] Login page works
- [ ] No errors in `storage/logs/laravel.log`

## Troubleshooting

### Issue: "Storage link already exists"
```bash
# Remove existing link and recreate
rm public/storage
php artisan storage:link
```

### Issue: "Permission denied" on storage
```bash
# Fix permissions
chmod -R 775 storage bootstrap/cache
chown -R unicorncom:unicorncom storage bootstrap/cache
```

### Issue: "APP_KEY not set"
```bash
php artisan key:generate
```

### Issue: "Database connection error"
```bash
# Check .env database settings
cat .env | grep DB_

# Test connection
php artisan tinker
DB::connection()->getPdo();
```

### Issue: "500 Internal Server Error"
```bash
# Check logs
tail -f storage/logs/laravel.log

# Enable debug temporarily
# Edit .env: APP_DEBUG=true
# Then check browser for error details
```

### Issue: "Route not found" or "404"
- Verify document root points to `public` folder
- Clear route cache: `php artisan route:clear`
- Rebuild cache: `php artisan route:cache`

## Next Steps After Setup

1. **Change default passwords** - Log in and change admin/staff/agent passwords
2. **Set up cron job** (if using Laravel scheduler):
   ```
   * * * * * cd /home/unicorncom/invoices.unicorn.com.na && php artisan schedule:run >> /dev/null 2>&1
   ```
3. **Set up backups** - Configure database backups in cPanel
4. **Monitor logs** - Check `storage/logs/laravel.log` regularly

## Quick Reference Commands

```bash
# View logs
tail -f storage/logs/laravel.log

# Clear all caches
php artisan optimize:clear

# Rebuild caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Check Laravel version
php artisan --version

# List all routes
php artisan route:list
```
