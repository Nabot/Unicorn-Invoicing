<?php

namespace App\Http\Controllers;

use App\Enums\InvoiceStatus;
use App\Models\Client;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportController extends Controller
{
    /**
     * Export invoices to CSV.
     */
    public function exportCsv(Request $request): Response
    {
        $this->authorize('viewAny', Invoice::class);

        $companyId = $request->user()->company_id;

        $query = Invoice::forCompany($companyId)
            ->with(['client'])
            ->latest();

        // Apply filters
        if ($request->has('status')) {
            $query->byStatus(InvoiceStatus::from($request->status));
        }
        if ($request->has('client_id')) {
            $query->where('client_id', $request->client_id);
        }
        if ($request->has('from_date')) {
            $query->where('issue_date', '>=', $request->from_date);
        }
        if ($request->has('to_date')) {
            $query->where('issue_date', '<=', $request->to_date);
        }

        $invoices = $query->get();

        $filename = 'invoices_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($invoices) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'Invoice Number',
                'Client',
                'Issue Date',
                'Due Date',
                'Status',
                'Subtotal',
                'VAT',
                'Total',
                'Balance Due',
            ]);

            // CSV rows
            foreach ($invoices as $invoice) {
                fputcsv($file, [
                    $invoice->invoice_number,
                    $invoice->client->name,
                    $invoice->issue_date?->format('Y-m-d') ?? 'N/A',
                    $invoice->due_date->format('Y-m-d'),
                    $invoice->status->label(),
                    number_format($invoice->subtotal, 2),
                    number_format($invoice->vat_total, 2),
                    number_format($invoice->total, 2),
                    number_format($invoice->balance_due, 2),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export a single invoice to PDF.
     */
    public function exportPdf(Invoice $invoice)
    {
        $this->authorize('view', $invoice);

        $invoice->load(['items', 'client', 'creator', 'payments']);

        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));
        
        $filename = 'invoice_' . $invoice->invoice_number . '_' . date('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Export multiple invoices to PDF (batch export).
     * Note: This creates individual PDFs. For a combined PDF, you would need to merge them.
     */
    public function exportPdfBatch(Request $request)
    {
        $this->authorize('viewAny', Invoice::class);

        // For now, redirect to CSV export for batch operations
        // Batch PDF export would require merging multiple PDFs which is more complex
        return redirect()->route('invoices.export.csv', $request->query())
            ->with('info', 'For batch operations, please use CSV export. PDF export is available for individual invoices.');
    }

    /**
     * Export clients to CSV.
     */
    public function exportClientsCsv(Request $request): Response
    {
        $this->authorize('viewAny', Client::class);

        $companyId = $request->user()->company_id;

        $query = Client::forCompany($companyId)
            ->withCount(['invoices as total_invoices'])
            ->withSum(['invoices as total_revenue' => function($q) {
                $q->where('status', InvoiceStatus::PAID);
            }], 'total')
            ->withSum(['invoices as outstanding_balance' => function($q) {
                $q->whereIn('status', [InvoiceStatus::ISSUED, InvoiceStatus::PARTIALLY_PAID]);
            }], 'balance_due');

        // Apply filters
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->has('status_filter')) {
            $statusFilter = $request->status_filter;
            if ($statusFilter === 'active') {
                $query->has('invoices');
            } elseif ($statusFilter === 'inactive') {
                $query->doesntHave('invoices');
            } elseif ($statusFilter === 'outstanding') {
                $query->whereHas('invoices', function($q) {
                    $q->whereIn('status', [InvoiceStatus::ISSUED, InvoiceStatus::PARTIALLY_PAID]);
                });
            }
        }

        $clients = $query->get();

        $filename = 'clients_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($clients) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'Name',
                'Email',
                'Phone',
                'Address',
                'VAT Number',
                'Total Invoices',
                'Total Revenue',
                'Outstanding Balance',
                'Status',
            ]);

            // CSV rows
            foreach ($clients as $client) {
                $isActive = ($client->total_invoices ?? 0) > 0;
                fputcsv($file, [
                    $client->name,
                    $client->email ?? '',
                    $client->phone ?? '',
                    $client->address ?? '',
                    $client->vat_number ?? '',
                    $client->total_invoices ?? 0,
                    number_format($client->total_revenue ?? 0, 2),
                    number_format($client->outstanding_balance ?? 0, 2),
                    $isActive ? 'Active' : 'Inactive',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Email invoice.
     */
    public function emailInvoice(Request $request, Invoice $invoice)
    {
        $this->authorize('view', $invoice);

        // Note: This requires email configuration
        // To implement: Create a Mailable class and send email

        return redirect()->back()
            ->with('error', 'Email functionality is not yet implemented.');
    }
}
