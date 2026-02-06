# cPanel Setup Steps - After Git Clone

## Step 1: Create Database and User

### In cPanel:

1. **Go to MySQL Databases:**
   - Login to cPanel
   - Find "MySQL Databases" or "MySQL Database Wizard"
   - Click on it

2. **Create Database:**
   - Enter database name: `unicorn_invoicing` (or your preferred name)
   - Click "Create Database"
   - **Note the full database name** (usually `cpanelusername_unicorn_invoicing`)

3. **Create Database User:**
   - Scroll to "MySQL Users" section
   - Enter username: `invoicing_user` (or your preferred name)
   - Enter a strong password (use password generator)
   - Click "Create User"
   - **Note the full username** (usually `cpanelusername_invoicing_user`)

4. **Assign User to Database:**
   - Scroll to "Add User To Database"
   - Select the user you created
   - Select the database you created
   - Click "Add"
   - Check "ALL PRIVILEGES"
   - Click "Make Changes"

**Important:** Write down these details:
- Full Database Name: `_________________`
- Full Database Username: `_________________`
- Database Password: `_________________`

## Step 2: Configure .env File

### Via cPanel File Manager or SSH:

1. **Navigate to your Laravel application folder**
   - Usually: `/home/username/laravel_app` or `/home/username/Unicorn-Invoicing`

2. **Copy .env.example to .env:**
   ```bash
   cp .env.example .env
   ```

3. **Edit .env file** and update these values:

```env
APP_NAME="Unicorn Invoicing System"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://yourdomain.com

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_full_database_name_here
DB_USERNAME=your_full_database_username_here
DB_PASSWORD=your_database_password_here

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax

CACHE_DRIVER=file
QUEUE_CONNECTION=sync

APP_CURRENCY=R
```

**Replace:**
- `yourdomain.com` with your actual domain
- `your_full_database_name_here` with the full database name from Step 1
- `your_full_database_username_here` with the full database username from Step 1
- `your_database_password_here` with the database password from Step 1

## Step 3: Generate Application Key

### Via cPanel Terminal or SSH:

```bash
cd ~/Unicorn-Invoicing  # or your Laravel folder name
php artisan key:generate
```

This will automatically fill in the `APP_KEY` in your `.env` file.

## Step 4: Install Dependencies

### Via cPanel Terminal or SSH:

```bash
cd ~/Unicorn-Invoicing  # or your Laravel folder name

# Install Composer dependencies
composer install --optimize-autoloader --no-dev
```

**Note:** If `composer` command doesn't work, try:
```bash
php composer.phar install --optimize-autoloader --no-dev
```

Or download composer:
```bash
curl -sS https://getcomposer.org/installer | php
php composer.phar install --optimize-autoloader --no-dev
```

## Step 5: Set File Permissions

### Via cPanel File Manager:

1. Navigate to `storage` folder
2. Right-click → Change Permissions
3. Set to `775` (or `755` if 775 doesn't work)
4. Check "Recurse into subdirectories"
5. Click "Change Permissions"

6. Repeat for `bootstrap/cache` folder

### Via SSH (if available):

```bash
cd ~/Unicorn-Invoicing
chmod -R 775 storage bootstrap/cache
```

## Step 6: Create Storage Symlink

### Via cPanel Terminal or SSH:

```bash
cd ~/Unicorn-Invoicing
php artisan storage:link
```

This creates a symbolic link from `public/storage` to `storage/app/public`.

## Step 7: Run Database Migrations

### Via cPanel Terminal or SSH:

```bash
cd ~/Unicorn-Invoicing

# Run migrations
php artisan migrate --force

# Create session table (if not exists)
php artisan session:table
php artisan migrate --force
```

**Note:** The `--force` flag is needed in production to skip confirmation prompts.

## Step 8: Seed Database (Optional - for initial data)

### Via cPanel Terminal or SSH:

```bash
cd ~/Unicorn-Invoicing
php artisan db:seed --force
```

This will create:
- Default admin user
- Sample clients
- Sample invoices
- Roles and permissions

**Default Admin Credentials** (if seeded):
- **Admin User:**
  - Email: `admin@example.com`
  - Password: `password`
- **Staff User:**
  - Email: `staff@example.com`
  - Password: `password`
- **Agent User:**
  - Email: `agent@example.com`
  - Password: `password`

**⚠️ IMPORTANT:** Change these default passwords immediately after first login!

## Step 9: Cache Configuration

### Via cPanel Terminal or SSH:

```bash
cd ~/Unicorn-Invoicing

# Clear all caches first
php artisan optimize:clear

# Cache configuration for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Step 10: Configure Document Root

### Option A: Using cPanel Domain/Subdomain Settings

1. Go to cPanel → **Subdomains** or **Addon Domains**
2. Find your domain/subdomain
3. Set **Document Root** to point to your Laravel's `public` folder:
   - Example: `/home/username/Unicorn-Invoicing/public`
   - Or: `/home/username/public_html` (if you copied public contents there)

### Option B: If Laravel is in Subdirectory

If your Laravel app is in a subdirectory (e.g., `/home/username/Unicorn-Invoicing`), you need to:

1. **Copy public folder contents to public_html:**
   ```bash
   cp -r ~/Unicorn-Invoicing/public/* ~/public_html/
   ```

2. **Update public/index.php paths:**
   Edit `~/public_html/index.php`:
   ```php
   require __DIR__.'/../Unicorn-Invoicing/vendor/autoload.php';
   $app = require_once __DIR__.'/../Unicorn-Invoicing/bootstrap/app.php';
   ```

### Option C: Using .htaccess Redirect

Create/edit `.htaccess` in `public_html`:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ /Unicorn-Invoicing/public/$1 [L]
</IfModule>
```

## Step 11: Test Database Connection

### Via cPanel Terminal or SSH:

```bash
cd ~/Unicorn-Invoicing
php artisan tinker
```

Then in tinker:
```php
DB::connection()->getPdo();
```

If it works, you'll see connection info. Type `exit` to leave tinker.

## Step 12: Test the Application

1. **Visit your domain** in a browser
2. **Check for errors:**
   - If you see a white screen, check `storage/logs/laravel.log`
   - Temporarily set `APP_DEBUG=true` in `.env` to see errors

3. **Test login:**
   - Try to access the login page
   - If seeded, use the default admin credentials

## Step 13: Install SSL Certificate

1. Go to cPanel → **SSL/TLS Status**
2. Install **Let's Encrypt SSL** (free) for your domain
3. Force HTTPS by ensuring `APP_URL=https://yourdomain.com` in `.env`
4. The application will automatically force HTTPS (already configured)

## Step 14: Final Optimizations

### Clear and Rebuild Caches:

```bash
cd ~/Unicorn-Invoicing
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Troubleshooting

### Issue: "500 Internal Server Error"

**Check:**
1. File permissions: `storage` and `bootstrap/cache` must be writable (775)
2. `.env` file exists and is configured correctly
3. `APP_KEY` is set (run `php artisan key:generate`)
4. Check error logs: `storage/logs/laravel.log`

**Enable debug temporarily:**
```env
APP_DEBUG=true
```
Then check the error message.

### Issue: "Database Connection Error"

**Check:**
1. Database credentials in `.env` are correct
2. Database user has privileges
3. Database exists
4. Test connection: `php artisan tinker` → `DB::connection()->getPdo();`

### Issue: "Storage link not working"

**Fix:**
```bash
php artisan storage:link
# Or manually:
cd ~/Unicorn-Invoicing/public
ln -s ../storage/app/public storage
```

### Issue: "Composer not found"

**Solutions:**
1. Use full path: `/usr/local/bin/composer` or `/usr/bin/composer`
2. Download composer:
   ```bash
   curl -sS https://getcomposer.org/installer | php
   php composer.phar install
   ```
3. Use cPanel's Composer tool if available

### Issue: "Permission denied"

**Fix:**
```bash
chmod -R 775 storage bootstrap/cache
chmod -R 755 .
chmod -R 644 .env
```

## Quick Command Reference

```bash
# Navigate to Laravel directory
cd ~/Unicorn-Invoicing

# Generate app key
php artisan key:generate

# Install dependencies
composer install --optimize-autoloader --no-dev

# Set permissions
chmod -R 775 storage bootstrap/cache

# Create storage link
php artisan storage:link

# Run migrations
php artisan migrate --force

# Seed database
php artisan db:seed --force

# Cache everything
php artisan config:cache
php artisan route:cache
php artisan view:cache

# View logs
tail -f storage/logs/laravel.log

# Clear caches
php artisan optimize:clear
```

## Verification Checklist

- [ ] Database created and user assigned
- [ ] `.env` file configured with correct database credentials
- [ ] `APP_KEY` generated
- [ ] Composer dependencies installed
- [ ] File permissions set (storage: 775, bootstrap/cache: 775)
- [ ] Storage symlink created
- [ ] Migrations run successfully
- [ ] Database seeded (if needed)
- [ ] Configuration cached
- [ ] Document root configured correctly
- [ ] SSL certificate installed
- [ ] Application accessible via browser
- [ ] Login works
- [ ] No errors in logs

## Next Steps After Setup

1. **Set up cron job** (if using Laravel scheduler):
   ```
   * * * * * cd /home/username/Unicorn-Invoicing && php artisan schedule:run >> /dev/null 2>&1
   ```

2. **Set up backups:**
   - Use cPanel → Backup
   - Or configure automated database backups

3. **Monitor logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

4. **Keep updated:**
   ```bash
   git pull origin main
   composer install --optimize-autoloader --no-dev
   php artisan migrate --force
   php artisan optimize:clear
   php artisan config:cache
   ```
