# Deploy Changes to Live Server

## Quick Deployment Steps

### Option 1: Using SSH (Recommended)

1. **Connect to your server via SSH:**
   ```bash
   ssh -i /path/to/your/private/key unicorncom@your-server-ip
   ```
   Or if you have the SSH key file:
   ```bash
   ssh -i UNICORN_SSH_KEY.txt unicorncom@your-server-ip
   ```

2. **Navigate to your application directory:**
   ```bash
   cd ~/invoices.unicorn.com.na
   ```

3. **Pull the latest changes from GitHub:**
   ```bash
   git pull origin main
   ```

4. **Clear Laravel caches:**
   ```bash
   php artisan view:clear
   php artisan config:clear
   php artisan cache:clear
   php artisan route:clear
   ```

5. **If you made changes to frontend assets (Vite), rebuild them:**
   ```bash
   npm run build
   ```
   Then upload the `public/build` folder to the server if needed.

### Option 2: Using cPanel File Manager

1. **Log into cPanel**

2. **Navigate to File Manager**
   - Go to: `/home/unicorncom/invoices.unicorn.com.na`

3. **Open Terminal in cPanel** (if available)
   - Or use SSH Terminal option in cPanel

4. **Run the same commands as Option 1:**
   ```bash
   cd ~/invoices.unicorn.com.na
   git pull origin main
   php artisan view:clear
   php artisan config:clear
   php artisan cache:clear
   ```

## Important Notes

- **Always clear caches after pulling changes** - This ensures Laravel uses the latest views and configurations
- **View cache is especially important** - Since we modified `pdf.blade.php`, clearing view cache is critical
- **No need to run migrations** - Unless you added new database changes
- **No need to run composer install** - Unless you added new PHP packages

## Verify Changes

After deployment, test by:
1. Logging into your application
2. Opening an existing invoice
3. Clicking "Download PDF"
4. Verifying the PDF has smaller fonts and minimalist design

## Troubleshooting

If changes don't appear:
1. **Clear browser cache** - Hard refresh (Ctrl+F5 or Cmd+Shift+R)
2. **Check Laravel logs:**
   ```bash
   tail -f storage/logs/laravel.log
   ```
3. **Verify file was updated:**
   ```bash
   ls -la resources/views/invoices/pdf.blade.php
   git log -1 resources/views/invoices/pdf.blade.php
   ```

## Quick One-Liner (SSH)

```bash
cd ~/invoices.unicorn.com.na && git pull origin main && php artisan view:clear && php artisan config:clear && php artisan cache:clear
```
