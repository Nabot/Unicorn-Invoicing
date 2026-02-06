# Unicorn Invoicing System - Architecture Documentation

## Entity Relationship Diagram (ERD)

```
┌─────────────────┐
│     users       │
├─────────────────┤
│ id (bigint)     │
│ uuid (uuid)     │◄─────┐
│ name            │      │
│ email           │      │
│ password        │      │
│ company_id      │      │
│ created_at      │      │
│ updated_at      │      │
└─────────────────┘      │
         │                │
         │                │
         │ 1:N            │
         ▼                │
┌─────────────────┐       │
│  model_has_     │       │
│  roles          │       │
├─────────────────┤       │
│ role_id         │       │
│ model_id        │       │
│ model_type      │       │
└─────────────────┘       │
                          │
┌─────────────────┐       │
│  model_has_     │       │
│  permissions    │       │
├─────────────────┤       │
│ permission_id   │       │
│ model_id        │       │
│ model_type      │       │
└─────────────────┘       │
                          │
┌─────────────────┐       │
│    clients      │       │
├─────────────────┤       │
│ id (bigint)     │       │
│ uuid (uuid)     │       │
│ company_id      │       │
│ name            │       │
│ email           │       │
│ phone           │       │
│ address         │       │
│ vat_number      │       │
│ user_id (FK)    │───────┘
│ created_at      │
│ updated_at      │
└─────────────────┘
         │
         │ 1:N
         ▼
┌─────────────────┐
│    invoices     │
├─────────────────┤
│ id (bigint)     │
│ uuid (uuid)     │
│ invoice_number  │
│ company_id      │
│ client_id (FK)  │
│ status (enum)   │
│ issue_date      │
│ due_date        │
│ subtotal        │
│ vat_total       │
│ total           │
│ amount_paid     │
│ balance_due     │
│ notes           │
│ terms           │
│ created_by (FK) │
│ created_at      │
│ updated_at      │
└─────────────────┘
         │
         │ 1:N
         ▼
┌─────────────────┐
│ invoice_items   │
├─────────────────┤
│ id (bigint)     │
│ invoice_id (FK) │
│ description     │
│ quantity        │
│ unit_price      │
│ vat_applicable  │
│ line_subtotal   │
│ line_vat        │
│ line_total      │
│ created_at      │
│ updated_at      │
└─────────────────┘

┌─────────────────┐
│    payments     │
├─────────────────┤
│ id (bigint)     │
│ uuid (uuid)     │
│ invoice_id (FK) │
│ amount          │
│ payment_date    │
│ method (enum)   │
│ reference       │
│ created_by (FK) │
│ created_at      │
│ updated_at      │
└─────────────────┘

┌─────────────────┐
│  audit_logs     │
├─────────────────┤
│ id (bigint)     │
│ actor_id (FK)   │
│ action          │
│ entity_type     │
│ entity_id       │
│ metadata (JSON) │
│ created_at      │
└─────────────────┘

┌─────────────────┐
│ invoice_numbers │
├─────────────────┤
│ id (bigint)     │
│ company_id      │
│ year            │
│ last_number     │
│ created_at      │
│ updated_at      │
└─────────────────┘
```

## Folder Structure

```
app/
├── Enums/
│   ├── InvoiceStatus.php
│   └── PaymentMethod.php
├── Events/
│   ├── InvoiceCreated.php
│   ├── InvoiceIssued.php
│   ├── InvoiceUpdated.php
│   ├── InvoiceVoided.php
│   ├── PaymentRecorded.php
│   └── PaymentDeleted.php
├── Http/
│   ├── Controllers/
│   │   ├── ClientController.php
│   │   ├── InvoiceController.php
│   │   ├── PaymentController.php
│   │   └── ReportController.php
│   └── Requests/
│       ├── Client/
│       │   ├── StoreClientRequest.php
│       │   └── UpdateClientRequest.php
│       ├── Invoice/
│       │   ├── StoreInvoiceRequest.php
│       │   ├── UpdateInvoiceRequest.php
│       │   ├── IssueInvoiceRequest.php
│       │   └── VoidInvoiceRequest.php
│       └── Payment/
│           ├── StorePaymentRequest.php
│           └── UpdatePaymentRequest.php
├── Listeners/
│   ├── LogInvoiceActivity.php
│   └── LogPaymentActivity.php
├── Models/
│   ├── AuditLog.php
│   ├── Client.php
│   ├── Invoice.php
│   ├── InvoiceItem.php
│   ├── InvoiceNumber.php
│   ├── Payment.php
│   └── User.php (updated)
├── Policies/
│   ├── ClientPolicy.php
│   ├── InvoicePolicy.php
│   └── PaymentPolicy.php
└── Services/
    ├── AuditLogService.php
    ├── InvoiceCalculatorService.php
    ├── InvoiceNumberService.php
    ├── InvoiceService.php
    └── PaymentService.php

database/
├── migrations/
│   ├── 2026_02_06_000001_add_company_id_to_users.php
│   ├── 2026_02_06_000002_create_clients_table.php
│   ├── 2026_02_06_000003_create_invoices_table.php
│   ├── 2026_02_06_000004_create_invoice_items_table.php
│   ├── 2026_02_06_000005_create_payments_table.php
│   ├── 2026_02_06_000006_create_audit_logs_table.php
│   └── 2026_02_06_000007_create_invoice_numbers_table.php
└── seeders/
    ├── RolePermissionSeeder.php
    ├── UserSeeder.php
    ├── ClientSeeder.php
    └── InvoiceSeeder.php

resources/
└── views/
    ├── clients/
    │   ├── index.blade.php
    │   ├── create.blade.php
    │   ├── edit.blade.php
    │   └── show.blade.php
    ├── invoices/
    │   ├── index.blade.php
    │   ├── create.blade.php
    │   ├── edit.blade.php
    │   ├── show.blade.php
    │   └── print.blade.php
    ├── payments/
    │   ├── create.blade.php
    │   └── edit.blade.php
    └── reports/
        ├── index.blade.php
        └── sales-summary.blade.php

tests/
├── Feature/
│   ├── ClientTest.php
│   ├── InvoiceTest.php
│   ├── PaymentTest.php
│   └── ReportTest.php
└── Unit/
    ├── InvoiceCalculatorServiceTest.php
    └── InvoiceNumberServiceTest.php
```

## VAT Calculation Strategy

**Strategy: Round per line item, then sum**

- Each line item calculates: `line_subtotal = quantity * unit_price`
- If VAT applicable: `line_vat = round(line_subtotal * 0.15, 2)`
- `line_total = line_subtotal + line_vat`
- Invoice totals: Sum of all line items
- `invoice.subtotal = sum(line_subtotal)`
- `invoice.vat_total = sum(line_vat)`
- `invoice.total = sum(line_total)`

This ensures:
- Consistent rounding (2 decimal places)
- Totals always match sum of items exactly
- Transparent per-line VAT calculation

## Invoice Status Lifecycle

```
draft → issued → partially_paid → paid
                    ↓
                  void
```

- **draft**: Initial state, can be edited freely
- **issued**: Invoice sent to client, limited edits
- **partially_paid**: Some payment received
- **paid**: Fully paid, no further payments
- **void**: Cancelled invoice, cannot be modified

## Role-Based Permissions

### Admin
- `manage-users`
- `manage-system-settings`
- `view-all-invoices`
- `view-all-reports`
- `manage-clients`
- `create-invoices`
- `edit-invoices`
- `issue-invoices`
- `void-invoices`
- `record-payments`

### Staff
- `manage-clients`
- `create-invoices`
- `edit-invoices`
- `issue-invoices`
- `void-invoices`
- `record-payments`
- `view-assigned-invoices` (limited)

### Agent
- `view-assigned-invoices` (read-only)
- `view-assigned-clients` (read-only)

## Multi-Tenancy Strategy

All queries are scoped by `company_id`:
- Users belong to a company
- Clients belong to a company
- Invoices belong to a company
- Invoice numbers are sequential per company/year

Global scopes ensure data isolation at the model level.
