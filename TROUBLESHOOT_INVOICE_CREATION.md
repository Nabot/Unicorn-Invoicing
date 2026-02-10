# Troubleshooting Invoice Creation Error

If you're getting "Failed to create invoice. Please try again." in production, follow these steps:

## Step 1: Check Laravel Logs

The error details are logged in Laravel's log file. Check the logs:

```bash
# SSH into your server
ssh -i UNICORN_SSH_KEY.txt unicorncom@your-server-ip

# Navigate to your application
cd ~/invoices.unicorn.com.na

# View the latest errors
tail -n 100 storage/logs/laravel.log | grep -A 20 "Invoice creation failed"
```

Or view the full log:
```bash
tail -n 200 storage/logs/laravel.log
```

## Step 2: Common Issues and Fixes

### Issue 1: Database Connection
**Symptoms**: Error mentions "SQLSTATE" or "Connection refused"

**Fix**:
```bash
# Check your .env file
cat .env | grep DB_

# Test database connection
php artisan tinker
>>> DB::connection()->getPdo();
```

### Issue 2: Missing Migrations
**Symptoms**: Error mentions "Table 'invoices' doesn't exist" or "Column 'discount' doesn't exist"

**Fix**:
```bash
# Run migrations
php artisan migrate

# Check migration status
php artisan migrate:status
```

### Issue 3: Missing Required Data
**Symptoms**: Error mentions "company_id" or "user_id" is null

**Fix**:
- Ensure the user is logged in
- Ensure the user has a `company_id` set
- Check the users table: `SELECT id, name, company_id FROM users WHERE id = YOUR_USER_ID;`

### Issue 4: Invoice Number Generation Failure
**Symptoms**: Error mentions "Failed to generate unique invoice number"

**Fix**:
```bash
# Check invoice_numbers table exists
php artisan tinker
>>> \App\Models\InvoiceNumber::count();

# Check company_settings table
>>> \App\Models\CompanySetting::count();
```

### Issue 5: Missing Company Settings
**Symptoms**: Error when generating invoice number

**Fix**:
```bash
php artisan tinker
>>> $companyId = YOUR_COMPANY_ID;
>>> \App\Models\CompanySetting::firstOrCreate(
    ['company_id' => $companyId],
    \App\Models\CompanySetting::defaults()
);
```

## Step 3: Enable Debug Mode (Temporarily)

To see more detailed error messages, temporarily enable debug mode:

```bash
# Edit .env file
nano .env

# Change this line:
APP_DEBUG=true

# Clear config cache
php artisan config:clear
php artisan cache:clear
```

**⚠️ IMPORTANT**: Set `APP_DEBUG=false` after debugging for security!

## Step 4: Test Invoice Creation Manually

Test if you can create an invoice via Tinker:

```bash
php artisan tinker
```

```php
$user = \App\Models\User::first();
$client = \App\Models\Client::where('company_id', $user->company_id)->first();

$invoiceService = app(\App\Services\InvoiceService::class);

try {
    $invoice = $invoiceService->createDraft(
        $user->company_id,
        $user->id,
        [
            'client_id' => $client->id,
            'due_date' => now()->addDays(30)->format('Y-m-d'),
            'notes' => 'Test invoice',
            'terms' => null,
        ],
        [
            [
                'description' => 'Test Item',
                'quantity' => 1,
                'unit_price' => 100,
                'discount' => 0,
                'vat_applicable' => true,
            ]
        ]
    );
    echo "Invoice created: " . $invoice->invoice_number . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
```

## Step 5: Check File Permissions

Ensure Laravel can write to storage and cache:

```bash
# Fix permissions
chmod -R 775 storage bootstrap/cache
chown -R unicorncom:unicorncom storage bootstrap/cache
```

## Step 6: Verify Environment Variables

Check that all required environment variables are set:

```bash
php artisan tinker
>>> config('database.default');
>>> config('database.connections.mysql.database');
>>> config('app.debug');
```

## Step 7: Clear All Caches

Sometimes cached config or routes can cause issues:

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

## Still Having Issues?

If none of the above fixes the issue, please provide:

1. The full error message from `storage/logs/laravel.log`
2. Output of `php artisan migrate:status`
3. Output of `php artisan tinker` test (from Step 4)
4. Your `.env` file (with sensitive data redacted)
