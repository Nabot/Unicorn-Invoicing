<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            color: #333;
            padding: 20px;
            line-height: 1.6;
        }
        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            border-bottom: 4px solid #2563eb;
            padding-bottom: 25px;
        }
        .company-info {
            flex: 1;
        }
        .company-info h1 {
            font-size: 28px;
            margin-bottom: 8px;
            color: #1a1a1a;
            font-weight: bold;
        }
        .invoice-info {
            text-align: right;
        }
        .invoice-info h2 {
            font-size: 24px;
            margin-bottom: 12px;
            color: #1a1a1a;
            font-weight: bold;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 8px;
        }
        .status-draft {
            background-color: #e5e7eb;
            color: #374151;
        }
        .status-issued {
            background-color: #dbeafe;
            color: #1e40af;
        }
        .status-paid {
            background-color: #d1fae5;
            color: #065f46;
        }
        .status-partially-paid {
            background-color: #fef3c7;
            color: #92400e;
        }
        .status-overdue {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .status-void {
            background-color: #f3f4f6;
            color: #6b7280;
        }
        .bill-to {
            margin: 25px 0;
            padding: 18px;
            background-color: #f9fafb;
            border-left: 5px solid #2563eb;
            border-radius: 4px;
        }
        .bill-to h3 {
            font-size: 16px;
            margin-bottom: 12px;
            color: #1a1a1a;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 25px 0;
            page-break-inside: auto;
        }
        thead {
            background-color: #1f2937;
            color: white;
        }
        thead tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
        th, td {
            border: 1px solid #e5e7eb;
            padding: 12px;
            text-align: left;
        }
        th {
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        tbody tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
        tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }
        tbody tr:hover {
            background-color: #f3f4f6;
        }
        .text-right {
            text-align: right;
        }
        .totals {
            margin-top: 30px;
            margin-left: auto;
            width: 380px;
        }
        .totals table {
            margin: 0;
            border: 2px solid #e5e7eb;
            border-radius: 4px;
        }
        .totals td {
            border: none;
            padding: 10px 15px;
            border-bottom: 1px solid #e5e7eb;
        }
        .totals .label {
            text-align: right;
            font-weight: normal;
            font-size: 12px;
            color: #6b7280;
        }
        .totals .value {
            text-align: right;
            font-weight: 600;
            font-size: 12px;
            color: #1f2937;
        }
        .totals .subtotal-row {
            background-color: #f9fafb;
        }
        .totals .vat-row {
            background-color: #f9fafb;
        }
        .totals .total-row {
            border-top: 3px solid #1f2937;
            border-bottom: 3px solid #1f2937;
            font-size: 16px;
            padding: 15px;
            background-color: #f3f4f6;
        }
        .totals .total-row .label {
            font-size: 16px;
            font-weight: bold;
            color: #1f2937;
        }
        .totals .total-row .value {
            font-size: 18px;
            font-weight: bold;
            color: #1f2937;
        }
        .totals .balance-row {
            background-color: #fef3c7;
            font-weight: bold;
            font-size: 14px;
            border: 2px solid #fbbf24;
        }
        .totals .balance-row .label {
            font-size: 14px;
            font-weight: bold;
            color: #92400e;
        }
        .totals .balance-row .value {
            font-size: 16px;
            font-weight: bold;
            color: #92400e;
        }
        .totals .paid-row {
            background-color: #d1fae5;
        }
        .totals .paid-row .label {
            color: #065f46;
        }
        .totals .paid-row .value {
            color: #065f46;
        }
        .discount-row {
            color: #dc2626;
        }
        .footer {
            margin-top: 50px;
            padding-top: 25px;
            border-top: 2px solid #e5e7eb;
            font-size: 10px;
            color: #6b7280;
        }
        .notes-terms {
            margin-top: 35px;
            display: flex;
            gap: 30px;
        }
        .notes-terms > div {
            flex: 1;
        }
        .notes-terms h4 {
            font-size: 14px;
            margin-bottom: 10px;
            color: #1f2937;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 8px;
            font-weight: bold;
        }
        .notes-terms p {
            font-size: 11px;
            color: #4b5563;
            line-height: 1.6;
        }
        .page-number {
            position: fixed;
            bottom: 20px;
            right: 20px;
            font-size: 10px;
            color: #9ca3af;
        }
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 72px;
            font-weight: bold;
            color: rgba(0, 0, 0, 0.05);
            z-index: -1;
            pointer-events: none;
        }
        .watermark.draft {
            color: rgba(107, 114, 128, 0.1);
        }
        .watermark.paid {
            color: rgba(5, 150, 105, 0.1);
        }
        .watermark.overdue {
            color: rgba(220, 38, 38, 0.1);
        }
        @page {
            margin: 1.5cm;
        }
        .page-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: right;
            padding: 10px 20px;
            font-size: 10px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>
<body>
    @php
        $watermarkClass = '';
        $isOverdue = $invoice->status->value !== 'paid' && $invoice->status->value !== 'void' && $invoice->due_date < now();
        if ($invoice->status->value === 'draft') {
            $watermarkClass = 'draft';
        } elseif ($invoice->status->value === 'paid') {
            $watermarkClass = 'paid';
        } elseif ($isOverdue) {
            $watermarkClass = 'overdue';
        }
    @endphp
    @if($watermarkClass)
        <div class="watermark {{ $watermarkClass }}">
            @if($invoice->status->value === 'draft')
                DRAFT
            @elseif($invoice->status->value === 'paid')
                PAID
            @elseif($isOverdue)
                OVERDUE
            @endif
        </div>
    @endif
    <div class="header">
        <div class="company-info">
            @php
                $logoPath = public_path('images/logo.jpg');
                if (file_exists($logoPath)) {
                    $logoData = base64_encode(file_get_contents($logoPath));
                    $logoMime = mime_content_type($logoPath);
                    $logoBase64 = 'data:' . $logoMime . ';base64,' . $logoData;
                } else {
                    $logoBase64 = null;
                }
            @endphp
            @if($logoBase64)
                <img src="{{ $logoBase64 }}" alt="Unicorn Supplies CC" style="max-height: 80px; margin-bottom: 10px;">
            @endif
            <h1 style="margin-top: {{ $logoBase64 ? '10px' : '0' }};">Unicorn Supplies CC</h1>
            <div style="color: #666; font-size: 12px; margin-top: 10px; line-height: 1.8;">
                <p style="margin: 2px 0;"><strong>e.</strong> supply@unicorn.com.na</p>
                <p style="margin: 2px 0;"><strong>t.</strong> +264811600014</p>
                <p style="margin: 2px 0;"><strong>Registration:</strong> CC/2020/02411</p>
                <p style="margin: 2px 0;"><strong>VAT:</strong> 11070239</p>
            </div>
        </div>
        <div class="invoice-info">
            <h2>Tax Invoice #{{ $invoice->invoice_number }}</h2>
            @php
                $statusClass = 'status-' . strtolower(str_replace(' ', '-', $invoice->status->value));
                $isOverdue = $invoice->status->value !== 'paid' && $invoice->status->value !== 'void' && $invoice->due_date < now();
                if ($isOverdue) {
                    $statusClass = 'status-overdue';
                }
            @endphp
            <div class="status-badge {{ $statusClass }}">
                {{ $invoice->status->label() }}
                @if($isOverdue)
                    - OVERDUE
                @endif
            </div>
            <p style="margin: 8px 0; font-size: 12px;"><strong>Issue Date:</strong> {{ $invoice->issue_date?->format('F j, Y') ?? 'Draft' }}</p>
            <p style="margin: 8px 0; font-size: 12px; {{ $isOverdue ? 'color: #dc2626; font-weight: bold;' : '' }}"><strong>Due Date:</strong> {{ $invoice->due_date->format('F j, Y') }}</p>
            @if($invoice->status->value === 'paid')
                <p style="margin: 8px 0; font-size: 12px; color: #065f46; font-weight: bold;">✓ Payment Received</p>
            @elseif($invoice->status->value === 'partially_paid')
                <p style="margin: 8px 0; font-size: 12px; color: #92400e; font-weight: bold;">⚠ Partially Paid</p>
            @endif
        </div>
    </div>

    <div class="bill-to">
        <h3>Bill To:</h3>
        <p style="font-weight: bold; font-size: 14px; margin-bottom: 5px;">{{ $invoice->client->name }}</p>
        @if($invoice->client->address)
            <p style="margin: 3px 0;">{{ $invoice->client->address }}</p>
        @endif
        @if($invoice->client->email)
            <p style="margin: 3px 0;">Email: {{ $invoice->client->email }}</p>
        @endif
        @if($invoice->client->phone)
            <p style="margin: 3px 0;">Phone: {{ $invoice->client->phone }}</p>
        @endif
        @if($invoice->client->vat_number)
            <p style="margin: 3px 0;"><strong>VAT Number:</strong> {{ $invoice->client->vat_number }}</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th class="text-right">Quantity</th>
                <th class="text-right">Unit Price</th>
                <th class="text-right">Discount</th>
                <th>VAT</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}. {{ $item->description }}</td>
                    <td class="text-right">{{ number_format($item->quantity, 2) }}</td>
                    <td class="text-right">R {{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right {{ ($item->discount ?? 0) > 0 ? 'discount-row' : '' }}">R {{ number_format($item->discount ?? 0, 2) }}</td>
                    <td>{{ $item->vat_applicable ? '15%' : 'No' }}</td>
                    <td class="text-right"><strong>R {{ number_format($item->line_total, 2) }}</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @php
        $totalDiscount = $invoice->items->sum('discount') ?? 0;
        $subtotalBeforeDiscount = $invoice->items->sum(function($item) {
            return $item->quantity * $item->unit_price;
        });
    @endphp
    <div class="totals">
        <table>
            @if($totalDiscount > 0)
                <tr>
                    <td class="label">Subtotal (before discount):</td>
                    <td class="value">R {{ number_format($subtotalBeforeDiscount, 2) }}</td>
                </tr>
                <tr class="discount-row">
                    <td class="label">Total Discount:</td>
                    <td class="value">- R {{ number_format($totalDiscount, 2) }}</td>
                </tr>
            @endif
            <tr class="subtotal-row">
                <td class="label">Subtotal:</td>
                <td class="value">R {{ number_format($invoice->subtotal, 2) }}</td>
            </tr>
            <tr class="vat-row">
                <td class="label">VAT (15%):</td>
                <td class="value">R {{ number_format($invoice->vat_total, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td class="label">Total:</td>
                <td class="value">R {{ number_format($invoice->total, 2) }}</td>
            </tr>
            @if($invoice->amount_paid > 0)
                <tr class="{{ $invoice->status->value === 'paid' ? 'paid-row' : '' }}">
                    <td class="label">Amount Paid:</td>
                    <td class="value">R {{ number_format($invoice->amount_paid, 2) }}</td>
                </tr>
            @endif
            <tr class="balance-row {{ $invoice->balance_due == 0 ? 'paid-row' : '' }}">
                <td class="label">Balance Due:</td>
                <td class="value">R {{ number_format($invoice->balance_due, 2) }}</td>
            </tr>
        </table>
    </div>

    @if($invoice->notes || $invoice->terms)
        <div class="notes-terms">
            @if($invoice->notes)
                <div>
                    <h4>Notes:</h4>
                    <p>{{ $invoice->notes }}</p>
                </div>
            @endif
            @if($invoice->terms)
                <div>
                    <h4>Terms & Conditions:</h4>
                    <p>{{ $invoice->terms }}</p>
                </div>
            @endif
        </div>
    @endif

    <div class="footer">
        <div style="margin-bottom: 15px; padding: 15px; background-color: #f9fafb; border-left: 5px solid #2563eb; border-radius: 4px;">
            <h4 style="font-size: 13px; margin-bottom: 10px; color: #1f2937; font-weight: bold;">Banking Details</h4>
            <p style="margin: 4px 0; font-size: 11px; color: #4b5563;"><strong>Unicorn Supplies CC</strong></p>
            <p style="margin: 4px 0; font-size: 11px; color: #4b5563;"><strong>Account:</strong> 8019079296</p>
            <p style="margin: 4px 0; font-size: 11px; color: #4b5563;"><strong>Branch:</strong> 483872, Maerua Mall</p>
            <p style="margin: 4px 0; font-size: 11px; color: #4b5563;"><strong>Bank Name:</strong> Bank Windhoek</p>
        </div>
        <p style="margin: 10px 0; font-size: 10px; color: #6b7280;">This is a computer-generated invoice. No signature required.</p>
        <p style="margin: 5px 0; font-size: 10px; color: #6b7280;">Generated on: {{ now()->format('F j, Y \a\t g:i A') }}</p>
    </div>
</body>
</html>
