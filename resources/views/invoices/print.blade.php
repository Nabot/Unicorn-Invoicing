<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .company-info {
            flex: 1;
        }
        .invoice-info {
            text-align: right;
        }
        .bill-to {
            margin: 20px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .totals {
            margin-top: 20px;
            margin-left: auto;
            width: 300px;
        }
        .totals table {
            margin: 0;
        }
        .totals td {
            border: none;
            padding: 5px;
        }
        .totals .total-row {
            border-top: 2px solid #333;
            font-weight: bold;
            font-size: 1.1em;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: right; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer; border-radius: 4px;">
            Print Invoice
        </button>
    </div>

    <div class="header">
        <div class="company-info">
            <img src="{{ asset('images/logo.jpg') }}" alt="Unicorn Supplies CC" style="max-height: 80px; margin-bottom: 10px;" onerror="this.style.display='none';">
            <h1 style="margin: 0; margin-top: 10px;">Unicorn Supplies CC</h1>
            <div style="margin-top: 10px; font-size: 14px; line-height: 1.8;">
                <p style="margin: 2px 0;"><strong>e.</strong> supply@unicorn.com.na</p>
                <p style="margin: 2px 0;"><strong>t.</strong> +264811600014</p>
                <p style="margin: 2px 0;"><strong>Registration:</strong> CC/2020/02411</p>
                <p style="margin: 2px 0;"><strong>VAT:</strong> 11070239</p>
            </div>
        </div>
        <div class="invoice-info">
            <h2 style="margin: 0;">Tax Invoice #{{ $invoice->invoice_number }}</h2>
            <p style="margin: 5px 0;"><strong>Status:</strong> {{ $invoice->status->label() }}</p>
            <p style="margin: 5px 0;"><strong>Issue Date:</strong> {{ $invoice->issue_date?->format('Y-m-d') ?? 'Draft' }}</p>
            <p style="margin: 5px 0;"><strong>Due Date:</strong> {{ $invoice->due_date->format('Y-m-d') }}</p>
        </div>
    </div>

    <div class="bill-to">
        <h3>Bill To:</h3>
        <p style="margin: 5px 0; font-weight: bold;">{{ $invoice->client->name }}</p>
        @if($invoice->client->address)
            <p style="margin: 5px 0;">{{ $invoice->client->address }}</p>
        @endif
        @if($invoice->client->email)
            <p style="margin: 5px 0;">{{ $invoice->client->email }}</p>
        @endif
        @if($invoice->client->phone)
            <p style="margin: 5px 0;">{{ $invoice->client->phone }}</p>
        @endif
        @if($invoice->client->vat_number)
            <p style="margin: 5px 0;"><strong>VAT Number:</strong> {{ $invoice->client->vat_number }}</p>
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
            @foreach($invoice->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td class="text-right">{{ number_format($item->quantity, 2) }}</td>
                    <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right">{{ number_format($item->discount ?? 0, 2) }}</td>
                    <td>{{ $item->vat_applicable ? '15%' : 'No' }}</td>
                    <td class="text-right">{{ number_format($item->line_total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <table>
            <tr>
                <td class="text-right"><strong>Subtotal:</strong></td>
                <td class="text-right">{{ number_format($invoice->subtotal, 2) }}</td>
            </tr>
            <tr>
                <td class="text-right"><strong>VAT (15%):</strong></td>
                <td class="text-right">{{ number_format($invoice->vat_total, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td class="text-right"><strong>Total:</strong></td>
                <td class="text-right">{{ number_format($invoice->total, 2) }}</td>
            </tr>
            <tr>
                <td class="text-right"><strong>Amount Paid:</strong></td>
                <td class="text-right">{{ number_format($invoice->amount_paid, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td class="text-right"><strong>Balance Due:</strong></td>
                <td class="text-right">{{ number_format($invoice->balance_due, 2) }}</td>
            </tr>
        </table>
    </div>

    @if($invoice->notes || $invoice->terms)
        <div class="footer">
            @if($invoice->notes)
                <div style="margin-bottom: 15px;">
                    <h4 style="margin-bottom: 5px;">Notes:</h4>
                    <p style="margin: 0;">{{ $invoice->notes }}</p>
                </div>
            @endif
            @if($invoice->terms)
                <div>
                    <h4 style="margin-bottom: 5px;">Terms:</h4>
                    <p style="margin: 0;">{{ $invoice->terms }}</p>
                </div>
            @endif
        </div>
    @endif

    @if($invoice->payments->count() > 0)
        <div class="footer">
            <h4>Payment History:</h4>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Reference</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->payments as $payment)
                        <tr>
                            <td>{{ $payment->payment_date->format('Y-m-d') }}</td>
                            <td class="text-right">{{ number_format($payment->amount, 2) }}</td>
                            <td>{{ $payment->method->label() }}</td>
                            <td>{{ $payment->reference ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="footer">
        <div style="margin-bottom: 15px; padding: 15px; background-color: #f9f9f9; border-left: 4px solid #007bff;">
            <h4 style="margin-bottom: 8px; font-weight: bold;">Banking Details</h4>
            <p style="margin: 3px 0;"><strong>Unicorn Supplies CC</strong></p>
            <p style="margin: 3px 0;"><strong>Account:</strong> 8019079296</p>
            <p style="margin: 3px 0;"><strong>Branch:</strong> 483872, Maerua Mall</p>
            <p style="margin: 3px 0;"><strong>Bank Name:</strong> Bank Windhoek</p>
        </div>
    </div>
</body>
</html>
