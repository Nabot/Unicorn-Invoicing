# Downloading Composer.phar - Step by Step

## Step 1: Navigate to Your Laravel Directory

```bash
cd ~/invoices.unicorn.com.na
pwd  # Verify you're in the right directory
ls -la  # See what files are there
```

## Step 2: Download composer.phar

### Method 1: Using wget (Try this first)

```bash
wget https://getcomposer.org/download/latest-stable/composer.phar
```

### Method 2: Using curl (If wget doesn't work)

```bash
curl -o composer.phar https://getcomposer.org/download/latest-stable/composer.phar
```

### Method 3: Using PHP with allow_url_fopen enabled

```bash
php -d allow_url_fopen=On -r "file_put_contents('composer.phar', file_get_contents('https://getcomposer.org/download/latest-stable/composer.phar'));"
```

### Method 4: Download specific version (if latest doesn't work)

```bash
# Download version 2.7.6 (stable)
wget https://getcomposer.org/download/2.7.6/composer.phar
```

## Step 3: Verify composer.phar was downloaded

```bash
# Check if file exists
ls -la composer.phar

# Should show something like:
# -rw-r--r-- 1 user user 2457600 composer.phar

# Check file size (should be around 2-3 MB)
du -h composer.phar
```

## Step 4: Make it executable

```bash
chmod +x composer.phar
```

## Step 5: Test composer.phar

```bash
# Test if it works
php composer.phar --version

# Should show: Composer version X.X.X
```

## Step 6: Install dependencies

```bash
# Now install dependencies
php -d allow_url_fopen=On composer.phar install --optimize-autoloader --no-dev
```

## Complete Sequence (Copy and paste all at once)

```bash
cd ~/invoices.unicorn.com.na
wget https://getcomposer.org/download/latest-stable/composer.phar
chmod +x composer.phar
ls -la composer.phar
php composer.phar --version
php -d allow_url_fopen=On composer.phar install --optimize-autoloader --no-dev
```

## Troubleshooting

### If wget says "command not found":

```bash
# Try curl instead
curl -o composer.phar https://getcomposer.org/download/latest-stable/composer.phar
chmod +x composer.phar
php composer.phar --version
```

### If curl also doesn't work:

```bash
# Use PHP directly
php -d allow_url_fopen=On -r "file_put_contents('composer.phar', file_get_contents('https://getcomposer.org/download/latest-stable/composer.phar'));"
chmod +x composer.phar
php composer.phar --version
```

### If download fails due to SSL:

```bash
# Download without SSL verification (less secure, but works)
wget --no-check-certificate https://getcomposer.org/download/latest-stable/composer.phar
# Or
curl -k -o composer.phar https://getcomposer.org/download/latest-stable/composer.phar
```

### If file downloads but is empty or corrupted:

```bash
# Check file size
ls -lh composer.phar

# If it's very small (< 100KB), it's corrupted
# Delete and try again
rm composer.phar
wget https://getcomposer.org/download/latest-stable/composer.phar
```

### Verify you're in the right directory:

```bash
# Check current directory
pwd

# Should be something like:
# /home/unicorncom/invoices.unicorn.com.na

# List files to see if you're in Laravel root
ls -la

# Should see: composer.json, app/, config/, etc.
```

## Alternative: Download via Browser and Upload

If all download methods fail:

1. **Download on your local computer:**
   - Visit: https://getcomposer.org/download/latest-stable/composer.phar
   - Save the file

2. **Upload to server:**
   - Use cPanel File Manager
   - Navigate to `~/invoices.unicorn.com.na/`
   - Upload `composer.phar`

3. **Set permissions via SSH:**
   ```bash
   cd ~/invoices.unicorn.com.na
   chmod +x composer.phar
   php composer.phar --version
   ```

## After composer.phar is working

Once you have composer.phar and it shows version info, continue:

```bash
cd ~/invoices.unicorn.com.na

# Install dependencies
php -d allow_url_fopen=On composer.phar install --optimize-autoloader --no-dev

# Set permissions
chmod -R 775 storage bootstrap/cache

# Create storage link
php artisan storage:link

# Generate app key
php artisan key:generate

# Run migrations
php artisan migrate --force

# Cache config
php artisan config:cache
php artisan route:cache
php artisan view:cache
```
