# Fixing "composer not found" on cPanel

## Solution 1: Use Full Path to Composer (Most Common)

cPanel servers often have composer installed but not in PATH. Try these paths:

```bash
# Try these one by one:
/usr/local/bin/composer install --optimize-autoloader --no-dev
/usr/bin/composer install --optimize-autoloader --no-dev
/opt/cpanel/composer/bin/composer install --optimize-autoloader --no-dev
```

## Solution 2: Find Composer Location

First, find where composer is installed:

```bash
# Search for composer
which composer
whereis composer
find /usr -name composer 2>/dev/null
find /opt -name composer 2>/dev/null
find /home -name composer 2>/dev/null

# Check common locations
ls -la /usr/local/bin/composer
ls -la /usr/bin/composer
ls -la /opt/cpanel/composer/bin/composer
ls -la ~/composer.phar
```

Once found, use the full path.

## Solution 3: Download Composer Locally

If composer is not installed, download it:

```bash
cd ~/Unicorn-Invoicing

# Download composer installer
curl -sS https://getcomposer.org/installer | php

# This creates composer.phar in current directory
# Now use it:
php composer.phar install --optimize-autoloader --no-dev
```

## Solution 4: Use cPanel's Composer Tool (If Available)

1. Log into cPanel
2. Look for "Composer" or "PHP Composer" in the software section
3. Use the cPanel interface to install dependencies
4. Or check if it provides a command path

## Solution 5: Install Composer Globally

If you have SSH access and permissions:

```bash
# Download and install composer globally
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer

# Verify installation
composer --version
```

## Solution 6: Create an Alias

If you find composer but want to use it easily:

```bash
# Add to ~/.bashrc or ~/.bash_profile
echo 'alias composer="php /path/to/composer.phar"' >> ~/.bashrc
source ~/.bashrc

# Or create a symlink
ln -s /path/to/composer.phar ~/bin/composer
```

## Solution 7: Use PHP Directly with Composer.phar

If you have composer.phar downloaded:

```bash
cd ~/Unicorn-Invoicing

# Download composer.phar if you don't have it
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"

# Now use it
php composer.phar install --optimize-autoloader --no-dev
```

## Quick Test Script

Create a test script to find composer:

```bash
cat > ~/find_composer.sh << 'EOF'
#!/bin/bash
echo "Searching for composer..."

PATHS=(
    "/usr/local/bin/composer"
    "/usr/bin/composer"
    "/opt/cpanel/composer/bin/composer"
    "$HOME/composer.phar"
    "$HOME/.composer/vendor/bin/composer"
)

for path in "${PATHS[@]}"; do
    if [ -f "$path" ] || [ -x "$path" ]; then
        echo "Found: $path"
        if [ -x "$path" ]; then
            echo "Testing: $path --version"
            $path --version
        fi
    fi
done

echo ""
echo "Checking PATH..."
echo $PATH | tr ':' '\n' | while read dir; do
    if [ -f "$dir/composer" ]; then
        echo "Found in PATH: $dir/composer"
    fi
done
EOF

chmod +x ~/find_composer.sh
~/find_composer.sh
```

## Recommended Approach for cPanel

**Most reliable method:**

```bash
cd ~/Unicorn-Invoicing

# Download composer.phar to your project directory
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php composer-setup.php
php -r "unlink('composer-setup.php');"

# Use it
php composer.phar install --optimize-autoloader --no-dev

# Optional: Move to home directory for reuse
mv composer.phar ~/composer.phar
# Then use: php ~/composer.phar install
```

## After Installing Dependencies

Once composer works, continue with setup:

```bash
cd ~/Unicorn-Invoicing

# Set permissions
chmod -R 775 storage bootstrap/cache

# Create storage link
php artisan storage:link

# Run migrations
php artisan migrate --force

# Seed database (optional)
php artisan db:seed --force

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Troubleshooting

### Issue: "curl not found"
Use wget instead:
```bash
wget https://getcomposer.org/installer -O composer-setup.php
php composer-setup.php
```

### Issue: "Permission denied"
```bash
# Try without sudo first
php composer.phar install --optimize-autoloader --no-dev

# If that fails, check directory permissions
chmod 755 ~/Unicorn-Invoicing
```

### Issue: "Memory limit exhausted"
```bash
# Increase PHP memory limit temporarily
php -d memory_limit=512M composer.phar install --optimize-autoloader --no-dev
```

### Issue: "SSL certificate problem"
```bash
# Disable SSL verification (not recommended for production)
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" --insecure
# Or download via HTTP (less secure)
php -r "copy('http://getcomposer.org/installer', 'composer-setup.php');"
```

## Alternative: Manual Dependency Installation

If composer absolutely won't work, you can manually install dependencies, but this is **NOT recommended** as it's very complex. It's better to get composer working.

## Verify Installation

After running composer install, verify:

```bash
# Check if vendor directory exists
ls -la vendor/

# Check if autoload files exist
ls -la vendor/autoload.php
ls -la bootstrap/cache/packages.php
```

If these exist, composer worked successfully!
