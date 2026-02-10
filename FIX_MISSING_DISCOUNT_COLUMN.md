# Fix Missing Discount Column Error

## Problem
You're getting this error:
```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'discount' in 'INSERT INTO'
```

This happens because the `discount` column migration hasn't been run on your production database.

## Quick Fix (Run on Production Server)

SSH into your server and run:

```bash
cd ~/invoices.unicorn.com.na

# Pull latest changes (if you haven't already)
git pull origin main

# Run the migration to add the discount column
php artisan migrate

# Or run just this specific migration
php artisan migrate --path=database/migrations/2026_02_06_083352_add_discount_to_invoice_items_table.php
```

## Verify Migration

After running the migration, verify the column exists:

```bash
php artisan tinker
```

Then run:
```php
\Illuminate\Support\Facades\Schema::hasColumn('invoice_items', 'discount');
```

This should return `true`. Type `exit` to leave tinker.

## Alternative: Manual SQL Fix

If migrations don't work, you can add the column manually via phpMyAdmin or MySQL:

```sql
ALTER TABLE `invoice_items` 
ADD COLUMN `discount` DECIMAL(15,2) DEFAULT 0.00 
AFTER `unit_price`;
```

## Check Migration Status

To see which migrations have been run:

```bash
php artisan migrate:status
```

Look for `2026_02_06_083352_add_discount_to_invoice_items_table` - it should show as "Ran" after running the migration.

## After Fix

Once the migration is complete, try creating an invoice again. The discount column should now be available.
