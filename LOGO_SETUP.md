# Logo Setup Instructions

The application has been configured to use the Unicorn Supplies logo. If the logo file is not present, please download it manually.

## Logo URL
https://pub-679ab607c9c64903b284c10abb72494c.r2.dev/UNICORN-SUPPLIES-LOGO-1-1.jpg

## Manual Download Instructions

1. **Download the logo:**
   ```bash
   cd public/images
   curl -k -o logo.jpg "https://pub-679ab607c9c64903b284c10abb72494c.r2.dev/UNICORN-SUPPLIES-LOGO-1-1.jpg"
   ```

   Or download it manually from your browser and save it as `public/images/logo.jpg`

2. **Verify the file exists:**
   ```bash
   ls -lh public/images/logo.jpg
   ```

## Where the Logo Appears

The logo is now integrated into:
- ✅ Navigation bar (top left)
- ✅ Mobile navigation menu
- ✅ PDF invoices (top left)
- ✅ Print view invoices (top left)

## Fallback

If the logo file is missing, the application will:
- Show the default SVG logo in navigation
- Hide the logo gracefully in PDF/print views
