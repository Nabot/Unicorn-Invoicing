# Pre-Deployment Checklist

## Before Deployment

### Code Preparation
- [ ] All features tested locally
- [ ] All migrations tested
- [ ] No debug code left in production files
- [ ] Error handling implemented
- [ ] Security vulnerabilities addressed
- [ ] Code reviewed

### Environment Configuration
- [ ] Production `.env` file prepared
- [ ] `APP_DEBUG=false` set
- [ ] `APP_ENV=production` set
- [ ] `APP_KEY` generated
- [ ] `APP_URL` set to production domain
- [ ] Database credentials configured
- [ ] Session configuration set
- [ ] Cache driver configured
- [ ] Queue driver configured (if using queues)
- [ ] Mail configuration set (if using email)

### Dependencies
- [ ] `composer.json` updated
- [ ] All dependencies compatible with PHP 8.2+
- [ ] Production dependencies only (no dev dependencies)
- [ ] Node modules built (if using Vite)
- [ ] Assets compiled for production

### Database
- [ ] All migrations ready
- [ ] Seeders tested (if using)
- [ ] Database backup strategy planned
- [ ] Database user has correct permissions

### Files & Assets
- [ ] Logo uploaded to `public/images/`
- [ ] Storage symlink will be created
- [ ] File permissions planned
- [ ] `.htaccess` files ready
- [ ] Public assets optimized

### Security
- [ ] `.env` file excluded from public access
- [ ] Sensitive files outside `public_html`
- [ ] File permissions set correctly
- [ ] SSL certificate ready
- [ ] HTTPS redirect configured
- [ ] CSRF protection enabled
- [ ] SQL injection prevention verified

### Testing
- [ ] Login/logout works
- [ ] Invoice creation works
- [ ] PDF generation works
- [ ] Customer management works
- [ ] Filters work correctly
- [ ] Export functions work
- [ ] All forms submit correctly
- [ ] Validation works
- [ ] Error pages display correctly

## Deployment Steps

### 1. Server Setup
- [ ] cPanel access confirmed
- [ ] PHP version verified (8.2+)
- [ ] Required PHP extensions enabled
- [ ] Database created
- [ ] Database user created and granted privileges
- [ ] FTP/SFTP access confirmed
- [ ] SSH access confirmed (if needed)

### 2. File Upload
- [ ] Laravel application uploaded
- [ ] Files in correct directory structure
- [ ] `.env` file uploaded with production settings
- [ ] Public folder contents uploaded to document root
- [ ] Storage folder created and writable
- [ ] Bootstrap/cache folder writable

### 3. Configuration
- [ ] `.env` file configured correctly
- [ ] Database connection tested
- [ ] Document root configured
- [ ] `.htaccess` files in place
- [ ] PHP settings configured

### 4. Laravel Setup
- [ ] Composer dependencies installed
- [ ] Application key set
- [ ] Storage symlink created
- [ ] Migrations run
- [ ] Database seeded (if needed)
- [ ] Session table created
- [ ] Caches cleared and rebuilt

### 5. Permissions
- [ ] Storage folder: 775
- [ ] Bootstrap/cache: 775
- [ ] Other directories: 755
- [ ] Files: 644
- [ ] `.env` file: 600 (if possible)

### 6. SSL & Security
- [ ] SSL certificate installed
- [ ] HTTPS redirect configured
- [ ] Secure cookies enabled
- [ ] `.env` file protected
- [ ] Sensitive directories protected

### 7. Testing
- [ ] Homepage loads
- [ ] Login works
- [ ] All pages accessible
- [ ] Forms work
- [ ] PDFs generate correctly
- [ ] File uploads work (if applicable)
- [ ] Email works (if configured)
- [ ] No console errors
- [ ] No 404/500 errors

### 8. Post-Deployment
- [ ] Cron jobs set up (if needed)
- [ ] Backup strategy implemented
- [ ] Monitoring set up
- [ ] Error logging configured
- [ ] Documentation updated

## Rollback Plan

If something goes wrong:
1. [ ] Keep previous version backed up
2. [ ] Database backup available
3. [ ] `.env` backup available
4. [ ] Know how to restore from backup
5. [ ] Test rollback procedure

## Post-Deployment Monitoring

### First 24 Hours
- [ ] Monitor error logs
- [ ] Check application performance
- [ ] Verify all features work
- [ ] Check database performance
- [ ] Monitor disk space
- [ ] Check email delivery (if applicable)

### First Week
- [ ] Review error logs daily
- [ ] Monitor user feedback
- [ ] Check server resources
- [ ] Verify backups are working
- [ ] Performance optimization if needed

## Quick Fixes Reference

### Clear All Caches
```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Fix Permissions
```bash
chmod -R 775 storage bootstrap/cache
```

### Recreate Storage Link
```bash
php artisan storage:link
```

### View Logs
```bash
tail -f storage/logs/laravel.log
```

### Check Configuration
```bash
php artisan config:show
```

### Test Database Connection
```bash
php artisan tinker
>>> DB::connection()->getPdo();
```
