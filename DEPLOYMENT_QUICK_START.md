# Quick Start Deployment Guide

## TL;DR - Essential Steps

### 1. Prepare Files Locally

```bash
# Generate production key
php artisan key:generate

# Install production dependencies
composer install --optimize-autoloader --no-dev

# Build assets (if using Vite)
npm run build

# Clear all caches
php artisan optimize:clear
```

### 2. Create Production .env

Copy `.env` and update:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
SESSION_SECURE_COOKIE=true
```

### 3. Upload to Server

**Structure:**
```
/home/username/
├── public_html/          (or domain folder)
│   └── [contents of public/ folder]
└── invoicing_app/        (Laravel root)
    ├── app/
    ├── config/
    ├── database/
    ├── resources/
    ├── storage/
    ├── vendor/
    ├── .env
    └── artisan
```

### 4. On Server (via SSH or cPanel Terminal)

```bash
cd ~/invoicing_app

# Install dependencies
composer install --optimize-autoloader --no-dev

# Set permissions
chmod -R 775 storage bootstrap/cache

# Create storage link
php artisan storage:link

# Run migrations
php artisan migrate --force

# Create session table
php artisan session:table
php artisan migrate --force

# Cache everything
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 5. Configure cPanel

1. **Database:** Create database and user in cPanel → MySQL Databases
2. **Document Root:** Point to `public_html` (where public/ contents are)
3. **PHP Version:** Set to 8.2+ in cPanel → Select PHP Version
4. **SSL:** Install SSL certificate in cPanel → SSL/TLS Status

### 6. Test

Visit your domain and test:
- Login works
- Invoices can be created
- PDFs download correctly
- All pages load

## Common cPanel Paths

- **Home Directory:** `/home/username/`
- **Public HTML:** `/home/username/public_html/`
- **Laravel App:** `/home/username/invoicing_app/` (or your folder name)
- **PHP Binary:** `/usr/local/bin/php` or `/usr/bin/php`
- **Composer:** May need to install or use `php composer.phar`

## File Permissions

```bash
# Directories
find . -type d -exec chmod 755 {} \;

# Files
find . -type f -exec chmod 644 {} \;

# Storage and cache (writable)
chmod -R 775 storage bootstrap/cache
```

## Troubleshooting

**500 Error:**
- Check `storage/logs/laravel.log`
- Verify `.env` is correct
- Check file permissions
- Verify `APP_KEY` is set

**Database Error:**
- Verify database credentials
- Check database user has privileges
- Test connection: `php artisan tinker` → `DB::connection()->getPdo();`

**Assets Not Loading:**
- Run `php artisan storage:link`
- Check `public/storage` exists
- Clear view cache: `php artisan view:clear`

**White Screen:**
- Temporarily set `APP_DEBUG=true` to see errors
- Check PHP error logs in cPanel
- Verify all files uploaded correctly

## Important Files to Upload

✅ **Must Upload:**
- All files except `node_modules`, `.git`, `.env.example`
- `.env` (with production settings)
- `vendor/` folder (or run `composer install` on server)
- `storage/` folder (create if doesn't exist)
- `public/` contents to `public_html/`

❌ **Don't Upload:**
- `.env.example` (keep local)
- `node_modules/` (not needed)
- `.git/` (optional)
- Development files

## Post-Deployment

1. **Set up cron job** (if using Laravel scheduler):
   ```
   * * * * * cd /home/username/invoicing_app && php artisan schedule:run >> /dev/null 2>&1
   ```

2. **Enable backups** in cPanel

3. **Monitor logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```

4. **Keep updated:**
   ```bash
   composer update
   php artisan migrate
   php artisan optimize:clear
   php artisan config:cache
   ```
