# Check User Permissions

## Step 1: Find Your User

Run this in tinker to see all users:

```php
\App\Models\User::select('id', 'name', 'email', 'company_id')->get();
```

## Step 2: Check Your User's Roles and Permissions

Replace `YOUR_ACTUAL_EMAIL` with your actual email from Step 1:

```php
$user = \App\Models\User::where('email', 'YOUR_ACTUAL_EMAIL')->first();
$user->roles;
$user->getAllPermissions();
```

## Step 3: Check if Quote Permissions Exist

```php
\Spatie\Permission\Models\Permission::where('name', 'like', '%quote%')->get();
```

## Step 4: Manually Assign Permissions (if needed)

If permissions exist but aren't assigned:

```php
$user = \App\Models\User::where('email', 'YOUR_ACTUAL_EMAIL')->first();
$role = $user->roles->first(); // Get first role
$role->givePermissionTo(['view-quotes', 'create-quotes', 'edit-quotes', 'delete-quotes']);
```

Or assign directly to user:

```php
$user = \App\Models\User::where('email', 'YOUR_ACTUAL_EMAIL')->first();
$user->givePermissionTo(['view-quotes', 'create-quotes', 'edit-quotes', 'delete-quotes']);
```

## Step 5: Clear Cache

After assigning permissions:

```php
app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
```

Then exit tinker and refresh your browser.
