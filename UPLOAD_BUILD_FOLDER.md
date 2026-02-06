# Upload Build Folder to Server

## Build Completed Successfully! ✅

The following files were created:
- `public/build/manifest.json`
- `public/build/assets/app-C_segwBS.css`
- `public/build/assets/app-DNg7CCpm.js`

## Upload to Server

### Method 1: Via cPanel File Manager (Easiest)

1. **Log into cPanel**
2. **Go to File Manager**
3. **Navigate to:** `invoices.unicorn.com.na/public/`
4. **Create `build` folder** if it doesn't exist
5. **Upload these files:**
   - `manifest.json` → `public/build/manifest.json`
   - `app-C_segwBS.css` → `public/build/assets/app-C_segwBS.css`
   - `app-DNg7CCpm.js` → `public/build/assets/app-DNg7CCpm.js`

**Or upload the entire `build` folder:**
- Upload `public/build/` folder → `public/build/`

### Method 2: Via SCP (If you have SSH key)

From your local machine:

```bash
# Upload build folder
scp -i ~/.ssh/cpanel_unicorn_key -r "/Users/byteable/Documents/APPS/Unicorn Invoicing System/public/build" unicorncom@your-server-ip:~/invoices.unicorn.com.na/public/
```

### Method 3: Via FTP Client

1. Connect via FTP (FileZilla, WinSCP, etc.)
2. Navigate to: `invoices.unicorn.com.na/public/`
3. Upload entire `build` folder

## Verify on Server

After uploading, verify on server:

```bash
cd ~/invoices.unicorn.com.na

# Check manifest exists
ls -la public/build/manifest.json

# Check assets exist
ls -la public/build/assets/

# Should see:
# app-C_segwBS.css
# app-DNg7CCpm.js
```

## After Upload

Refresh your browser - the Vite manifest error should be gone!

## Quick Upload Commands

If you have SSH access from local machine:

```bash
# From your local machine
cd "/Users/byteable/Documents/APPS/Unicorn Invoicing System"

# Upload build folder
scp -i ~/.ssh/cpanel_unicorn_key -r public/build unicorncom@your-server-ip:~/invoices.unicorn.com.na/public/
```

Replace `your-server-ip` with your actual server IP or hostname.
