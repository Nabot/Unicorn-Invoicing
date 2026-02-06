# Fixing Vite Manifest Error

## Problem
Laravel is looking for compiled frontend assets that don't exist yet.

## Solution 1: Build Assets on Server (If Node.js is Available)

### Check if Node.js/npm is installed:

```bash
node -v
npm -v
```

### If Node.js is available:

```bash
cd ~/invoices.unicorn.com.na

# Install npm dependencies
npm install

# Build production assets
npm run build

# Verify build was created
ls -la public/build/manifest.json
```

## Solution 2: Build Assets Locally and Upload (Recommended)

### On Your Local Machine:

```bash
cd "/Users/byteable/Documents/APPS/Unicorn Invoicing System"

# Install dependencies (if not done)
npm install

# Build production assets
npm run build

# Verify build folder exists
ls -la public/build/
```

### Upload build folder to server:

**Via FTP/cPanel File Manager:**
1. Navigate to `public/build/` folder locally
2. Upload entire `build` folder to: `~/invoices.unicorn.com.na/public/build/`

**Via SCP (if you have SSH access from local machine):**
```bash
scp -i ~/.ssh/cpanel_unicorn_key -r public/build unicorncom@your-server-ip:~/invoices.unicorn.com.na/public/
```

## Solution 3: Disable Vite Temporarily (Quick Fix)

If you can't build assets right now, you can temporarily disable Vite:

### Edit your layout file:

```bash
nano ~/invoices.unicorn.com.na/resources/views/layouts/app.blade.php
```

Find and comment out or remove Vite directives:
```blade
{{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
```

Instead, add direct CSS/JS links if you have them compiled, or use CDN versions.

## Solution 4: Use Pre-compiled Assets

If you have the assets from your local build, upload them:

```bash
# On server, create build directory
mkdir -p ~/invoices.unicorn.com.na/public/build

# Upload manifest.json and assets from local public/build/ folder
```

## Quick Fix: Create Empty Manifest (Temporary)

This is a temporary workaround - create a minimal manifest:

```bash
cd ~/invoices.unicorn.com.na/public/build

# Create minimal manifest.json
cat > manifest.json << 'EOF'
{
  "resources/css/app.css": {
    "file": "assets/app.css",
    "src": "resources/css/app.css",
    "isEntry": true
  },
  "resources/js/app.js": {
    "file": "assets/app.js",
    "src": "resources/js/app.js",
    "isEntry": true
  }
}
EOF

# Create assets directory
mkdir -p assets

# Create empty CSS file (or copy from resources if exists)
touch assets/app.css

# Create empty JS file (or copy from resources if exists)
touch assets/app.js
```

## Recommended Solution: Build Locally and Upload

**Step 1: On your local machine:**

```bash
cd "/Users/byteable/Documents/APPS/Unicorn Invoicing System"

# Build assets
npm run build

# This creates: public/build/manifest.json and assets
```

**Step 2: Upload to server:**

Upload the entire `public/build/` folder to your server at:
`~/invoices.unicorn.com.na/public/build/`

**Step 3: Verify on server:**

```bash
cd ~/invoices.unicorn.com.na
ls -la public/build/manifest.json
ls -la public/build/assets/
```

## Check if Vite is Required

If your application doesn't heavily rely on JavaScript, you might be able to use simpler asset loading. Check your Blade templates for `@vite` directives.

## Complete Fix Sequence

```bash
# On your LOCAL machine:
cd "/Users/byteable/Documents/APPS/Unicorn Invoicing System"
npm install
npm run build

# Then upload public/build/ folder to server

# On SERVER:
cd ~/invoices.unicorn.com.na
ls -la public/build/manifest.json  # Verify it exists
```

## Alternative: Use Laravel Mix or CDN Assets

If building assets is problematic, you can modify the layout to use CDN versions of CSS/JS libraries instead of Vite.
