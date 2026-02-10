# Fix Missing Company Error

## Problem
You're getting this error:
```
SQLSTATE[23000]: Integrity constraint violation: 1452 Cannot add or update a child row: 
a foreign key constraint fails (`unicorncom_invoicing`.`company_settings`, 
CONSTRAINT `company_settings_company_id_foreign` FOREIGN KEY (`company_id`) 
REFERENCES `companies` (`id`) ON DELETE CASCADE)
```

This happens because your users/clients have `company_id = 1`, but there's no company record with `id = 1` in the database.

## Quick Fix (Run on Production Server)

### Option 1: Create Company via Tinker (Recommended)

SSH into your server and run:

```bash
cd ~/invoices.unicorn.com.na
php artisan tinker
```

Then run:
```php
\App\Models\Company::firstOrCreate(
    ['id' => 1],
    [
        'name' => 'Unicorn Supplies CC',
        'email' => 'supply@unicorn.com.na',
        'phone' => '+264811600014',
        'address' => null,
        'tax_id' => '11070239',
    ]
);
```

Type `exit` to leave tinker.

### Option 2: Create Company via SQL

Connect to your database via phpMyAdmin or MySQL command line:

```sql
INSERT INTO `companies` (`id`, `name`, `email`, `phone`, `address`, `tax_id`, `logo`, `created_at`, `updated_at`)
VALUES (1, 'Unicorn Supplies CC', 'supply@unicorn.com.na', '+264811600014', NULL, '11070239', NULL, NOW(), NOW());
```

### Option 3: Run the Seeder

```bash
cd ~/invoices.unicorn.com.na
php artisan db:seed --class=CompanySeeder
```

## Verify Fix

After creating the company, verify it exists:

```bash
php artisan tinker
```

```php
\App\Models\Company::find(1);
```

You should see the company details. Then try creating an invoice again.

## Prevention

The code has been updated to:
1. Check if company exists before creating settings
2. Include CompanySeeder in DatabaseSeeder
3. Show a clearer error message if company is missing

After pulling the latest code, the seeder will automatically create the company when you run `php artisan db:seed`.
