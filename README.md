# Unicorn Invoicing System

A production-ready, multi-user invoicing system built with Laravel 11, featuring VAT calculation, audit logging, payment tracking, and role-based access control.

## Features

- **Multi-User System** with role-based permissions (Admin, Staff, Agent)
- **Client Management** - CRUD operations for clients
- **Invoice Management** - Create, edit, issue, and void invoices
- **Invoice Items** - Dynamic line items with per-item VAT calculation
- **Payment Tracking** - Record multiple payments per invoice
- **VAT Calculation** - 15% VAT with consistent rounding (per-line item)
- **Audit Logging** - Event-driven audit trail for all critical actions
- **Reports** - Sales summary, invoice aging, status reports
- **Multi-Tenancy Ready** - Company-scoped data isolation
- **UUID Support** - Public-facing UUIDs, sequential invoice numbers

## Tech Stack

- **Laravel 11** (PHP 8.2+)
- **MySQL** Database
- **Laravel Breeze** - Authentication
- **Spatie Laravel Permission** - Role & Permission management
- **Tailwind CSS** - Styling
- **Blade Templates** - Views

## Installation

### Prerequisites

- PHP 8.2 or higher
- Composer
- MySQL 5.7+ or MariaDB 10.3+
- Node.js & NPM (for frontend assets)

### Setup Steps

1. **Clone the repository** (if applicable) or navigate to the project directory

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install NPM dependencies**
   ```bash
   npm install
   ```

4. **Configure environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Update `.env` file** with your database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=unicorn_invoicing
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

6. **Run migrations**
   ```bash
   php artisan migrate
   ```

7. **Seed the database** (creates roles, permissions, demo users, clients, and invoices)
   ```bash
   php artisan db:seed
   ```

8. **Build frontend assets**
   ```bash
   npm run build
   # Or for development:
   npm run dev
   ```

9. **Start the development server**
   ```bash
   php artisan serve
   ```

## Default Users

After seeding, you can login with:

- **Admin**: `admin@example.com` / `password`
- **Staff**: `staff@example.com` / `password`
- **Agent**: `agent@example.com` / `password`

## Database Structure

### Core Tables

- `users` - System users with company_id and UUID
- `clients` - Client information
- `invoices` - Invoice headers
- `invoice_items` - Line items for invoices
- `payments` - Payment records
- `audit_logs` - Activity audit trail
- `invoice_numbers` - Sequential invoice number tracking

### Roles & Permissions

**Admin** - Full system access
- Manage users and system settings
- View all invoices and reports
- All invoice and client operations

**Staff** - Operational access
- Manage clients
- Create, edit, issue, and void invoices
- Record payments
- View assigned invoices

**Agent** - Read-only access
- View assigned invoices and clients only

## Usage

### Creating an Invoice

1. Navigate to **Invoices** → **New Invoice**
2. Select a client
3. Set due date
4. Add line items (description, quantity, unit price, VAT applicable)
5. Add notes/terms (optional)
6. Save as draft

### Issuing an Invoice

1. Open a draft invoice
2. Click **Issue Invoice**
3. Invoice status changes to "Issued" and issue_date is set

### Recording Payments

1. Open an invoice
2. Click **Record Payment**
3. Enter amount, payment date, method, and reference
4. Invoice status updates automatically (partially_paid → paid)

### Voiding an Invoice

1. Open an invoice (must not be paid)
2. Click **Void Invoice**
3. Invoice status changes to "void"

## VAT Calculation Strategy

The system uses **per-line item rounding**:

1. Each line item calculates: `line_subtotal = quantity × unit_price`
2. If VAT applicable: `line_vat = round(line_subtotal × 0.15, 2)`
3. `line_total = line_subtotal + line_vat`
4. Invoice totals = Sum of all line items

This ensures:
- Consistent 2-decimal rounding
- Totals always match sum of items exactly
- Transparent per-line VAT calculation

## Invoice Status Lifecycle

```
draft → issued → partially_paid → paid
                    ↓
                  void
```

- **draft**: Can be edited freely
- **issued**: Sent to client, limited edits
- **partially_paid**: Some payment received
- **paid**: Fully paid, no further payments
- **void**: Cancelled, cannot be modified

## API Architecture

The system is API-ready with:
- RESTful controllers
- Form Request validation
- Service layer for business logic
- Event-driven audit logging

## Testing

Run the test suite:

```bash
php artisan test
```

### Test Coverage

- Unit tests for VAT calculations
- Unit tests for invoice number generation
- Feature tests for CRUD operations
- Feature tests for authorization
- Feature tests for payment processing

## Multi-Tenancy

All queries are scoped by `company_id`:
- Users belong to a company
- Clients belong to a company
- Invoices belong to a company
- Invoice numbers are sequential per company/year

Global scopes ensure data isolation at the model level.

## Audit Logging

All critical actions are logged:
- Invoice created, updated, issued, voided
- Payment recorded, updated, deleted
- Client created/updated

Audit logs include:
- Actor (user who performed action)
- Action type
- Entity type and ID
- Metadata (JSON)
- Timestamp

## Reports

Available reports:
- **Sales Summary** - Totals by date range
- **Invoice Aging** - Outstanding balances by age (0-30, 31-60, 61-90, 90+ days)
- **Invoices by Status** - Count and totals by status

## File Structure

```
app/
├── Enums/          # InvoiceStatus, PaymentMethod
├── Events/         # InvoiceCreated, PaymentRecorded, etc.
├── Http/
│   ├── Controllers/ # ClientController, InvoiceController, etc.
│   └── Requests/   # Form validation requests
├── Listeners/      # Audit log listeners
├── Models/         # Eloquent models
├── Policies/       # Authorization policies
└── Services/       # Business logic services

database/
├── migrations/     # Database migrations
└── seeders/        # Database seeders

resources/
└── views/          # Blade templates
    ├── clients/
    ├── invoices/
    ├── payments/
    └── reports/
```

## Environment Variables

Key environment variables:

```env
APP_NAME="Unicorn Invoicing System"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=unicorn_invoicing
DB_USERNAME=root
DB_PASSWORD=
```

## Troubleshooting

### Migration Issues

If you encounter migration errors:
```bash
php artisan migrate:fresh --seed
```

### Permission Issues

Clear permission cache:
```bash
php artisan permission:cache-reset
```

### View Issues

Clear view cache:
```bash
php artisan view:clear
php artisan cache:clear
```

## Contributing

1. Follow Laravel coding standards
2. Write tests for new features
3. Update documentation
4. Ensure all tests pass

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Support

For issues and questions, please refer to the documentation or create an issue in the repository.
