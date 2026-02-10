<?php

namespace App\Http\Controllers;

use App\Enums\InvoiceStatus;
use App\Http\Requests\Invoice\IssueInvoiceRequest;
use App\Http\Requests\Invoice\StoreInvoiceRequest;
use App\Http\Requests\Invoice\UpdateInvoiceRequest;
use App\Http\Requests\Invoice\VoidInvoiceRequest;
use App\Models\Client;
use App\Models\Invoice;
use App\Services\CacheService;
use App\Services\InvoiceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function __construct(
        private InvoiceService $invoiceService
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Invoice::class);

        $companyId = $request->user()->company_id;
        $cacheService = app(\App\Services\CacheService::class);

        // Calculate statistics (cached)
        $stats = $cacheService->remember("invoices_stats_{$companyId}", 1800, function () use ($companyId) {
            $baseQuery = Invoice::forCompany($companyId);
            
            $totalInvoices = (clone $baseQuery)->count();
            $totalRevenue = (clone $baseQuery)->sum('total');
            $outstandingBalance = (clone $baseQuery)
                ->whereIn('status', [InvoiceStatus::ISSUED, InvoiceStatus::PARTIALLY_PAID])
                ->sum('balance_due');
            
            $overdueCount = (clone $baseQuery)
                ->whereIn('status', [InvoiceStatus::ISSUED, InvoiceStatus::PARTIALLY_PAID])
                ->where('due_date', '<', now())
                ->count();
            
            $thisMonthRevenue = (clone $baseQuery)
                ->whereMonth('issue_date', now()->month)
                ->whereYear('issue_date', now()->year)
                ->where('status', '!=', InvoiceStatus::DRAFT)
                ->sum('total');
            
            $lastMonthRevenue = (clone $baseQuery)
                ->whereMonth('issue_date', now()->subMonth()->month)
                ->whereYear('issue_date', now()->subMonth()->year)
                ->where('status', '!=', InvoiceStatus::DRAFT)
                ->sum('total');
            
            $avgInvoiceValue = $totalInvoices > 0 ? $totalRevenue / $totalInvoices : 0;
            $revenueChange = $lastMonthRevenue > 0 
                ? (($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100 
                : 0;

            return compact(
                'totalInvoices',
                'totalRevenue',
                'outstandingBalance',
                'overdueCount',
                'thisMonthRevenue',
                'lastMonthRevenue',
                'avgInvoiceValue',
                'revenueChange'
            );
        });

        $query = Invoice::forCompany($companyId)
            ->with(['client', 'creator', 'items']);

        // Quick filters
        if ($request->has('filter')) {
            $filter = $request->filter;
            switch ($filter) {
                case 'overdue':
                    $query->whereIn('status', [InvoiceStatus::ISSUED, InvoiceStatus::PARTIALLY_PAID])
                        ->where('due_date', '<', now());
                    break;
                case 'due_soon':
                    $query->whereIn('status', [InvoiceStatus::ISSUED, InvoiceStatus::PARTIALLY_PAID])
                        ->whereBetween('due_date', [now(), now()->addDays(7)]);
                    break;
                case 'this_month':
                    $query->whereMonth('issue_date', now()->month)
                        ->whereYear('issue_date', now()->year);
                    break;
                case 'unpaid':
                    $query->whereIn('status', [InvoiceStatus::ISSUED, InvoiceStatus::PARTIALLY_PAID]);
                    break;
                case 'draft':
                    $query->byStatus(InvoiceStatus::DRAFT);
                    break;
                case 'issued':
                    $query->byStatus(InvoiceStatus::ISSUED);
                    break;
            }
        }

        // Filter by status
        if ($request->has('status') && !$request->has('filter')) {
            $query->byStatus(InvoiceStatus::from($request->status));
        }

        // Filter by client
        if ($request->has('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        // Date range presets (only apply if not already filtered by quick filter)
        if ($request->has('date_preset') && !$request->has('filter')) {
            $preset = $request->date_preset;
            switch ($preset) {
                case 'today':
                    $query->whereDate('issue_date', today());
                    break;
                case 'this_week':
                    $query->whereBetween('issue_date', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'this_month':
                    $query->whereMonth('issue_date', now()->month)
                        ->whereYear('issue_date', now()->year);
                    break;
                case 'last_month':
                    $query->whereMonth('issue_date', now()->subMonth()->month)
                        ->whereYear('issue_date', now()->subMonth()->year);
                    break;
                case 'this_quarter':
                    $query->whereBetween('issue_date', [now()->startOfQuarter(), now()->endOfQuarter()]);
                    break;
                case 'this_year':
                    $query->whereYear('issue_date', now()->year);
                    break;
            }
        }

        // Filter by date range (only if no preset and no quick filter)
        if ($request->has('from_date') && !$request->has('date_preset') && !$request->has('filter')) {
            $query->where('issue_date', '>=', $request->from_date);
        }
        if ($request->has('to_date') && !$request->has('date_preset') && !$request->has('filter')) {
            $query->where('issue_date', '<=', $request->to_date);
        }

        // Filter by search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhere('total', 'like', "%{$search}%")
                    ->orWhereHas('client', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'issue_date');
        $sortDir = $request->get('sort_dir', 'desc');
        
        switch ($sortBy) {
            case 'invoice_number':
                $query->orderBy('invoice_number', $sortDir);
                break;
            case 'client':
                $query->join('clients', 'invoices.client_id', '=', 'clients.id')
                    ->orderBy('clients.name', $sortDir)
                    ->select('invoices.*');
                break;
            case 'issue_date':
                $query->orderBy('issue_date', $sortDir);
                break;
            case 'due_date':
                $query->orderBy('due_date', $sortDir);
                break;
            case 'total':
                $query->orderBy('total', $sortDir);
                break;
            case 'balance_due':
                $query->orderBy('balance_due', $sortDir);
                break;
            case 'status':
                $query->orderBy('status', $sortDir);
                break;
            default:
                $query->orderBy('issue_date', 'desc');
        }

        // Items per page
        $perPage = $request->get('per_page', 15);
        $perPage = in_array($perPage, [10, 15, 25, 50, 100]) ? $perPage : 15;

        $invoices = $query->paginate($perPage)->withQueryString();
        
        // Calculate summary totals for displayed invoices
        $summaryTotals = [
            'count' => $invoices->count(),
            'total_amount' => $invoices->getCollection()->sum('total'),
            'total_outstanding' => $invoices->getCollection()->sum('balance_due'),
            'avg_value' => $invoices->count() > 0 ? $invoices->getCollection()->sum('total') / $invoices->count() : 0,
        ];

        // Cache client list
        $clients = CacheService::getClients(
            $companyId,
            fn() => Client::forCompany($companyId)->orderBy('name')->get()
        );

        return view('invoices.index', compact('invoices', 'clients', 'stats', 'summaryTotals', 'sortBy', 'sortDir'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $this->authorize('create', Invoice::class);

        // Cache client list
        $clients = CacheService::getClients(
            $request->user()->company_id,
            fn() => Client::forCompany($request->user()->company_id)
                ->orderBy('name')
                ->get()
        );

        return view('invoices.create', compact('clients'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInvoiceRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();
            $items = $validated['items'] ?? $request->input('items', []);
            
            // Ensure items is an array
            if (!is_array($items) || empty($items)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Please add at least one item to the invoice.');
            }
            
            $invoice = $this->invoiceService->createDraft(
                $request->user()->company_id,
                $request->user()->id,
                $validated,
                $items
            );

            // Clear cache
            CacheService::clearInvoiceStats($request->user()->company_id);

            return redirect()->route('invoices.show', $invoice)
                ->with('success', 'Invoice created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Re-throw validation exceptions so Laravel handles them properly
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Invoice creation failed', [
                'user_id' => $request->user()->id,
                'company_id' => $request->user()->company_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            // Show more specific error in development, generic in production
            $errorMessage = config('app.debug') 
                ? 'Failed to create invoice: ' . $e->getMessage()
                : 'Failed to create invoice. Please check your input and try again.';

            return redirect()->back()
                ->withInput()
                ->with('error', $errorMessage);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Invoice $invoice): View
    {
        $this->authorize('view', $invoice);

        $invoice->load(['items', 'client', 'creator', 'payments.recorder']);

        // Load audit logs
        $auditLogs = \App\Models\AuditLog::where('entity_type', Invoice::class)
            ->where('entity_id', $invoice->id)
            ->with('actor')
            ->latest()
            ->limit(20)
            ->get();

        return view('invoices.show', compact('invoice', 'auditLogs'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Invoice $invoice): View
    {
        $this->authorize('update', $invoice);

        $invoice->load('items');
        $clients = Client::forCompany($invoice->company_id)
            ->orderBy('name')
            ->get();

        return view('invoices.edit', compact('invoice', 'clients'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInvoiceRequest $request, Invoice $invoice): RedirectResponse
    {
        try {
            $invoice = $this->invoiceService->update(
                $invoice,
                $request->validated(),
                $request->items ?? []
            );

            return redirect()->route('invoices.show', $invoice)
                ->with('success', 'Invoice updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Issue an invoice.
     */
    public function issue(IssueInvoiceRequest $request, Invoice $invoice): RedirectResponse
    {
        try {
            $invoice = $this->invoiceService->issue($invoice);

            return redirect()->route('invoices.show', $invoice)
                ->with('success', 'Invoice issued successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Void an invoice.
     */
    public function void(VoidInvoiceRequest $request, Invoice $invoice): RedirectResponse
    {
        try {
            $invoice = $this->invoiceService->void($invoice);

            return redirect()->route('invoices.show', $invoice)
                ->with('success', 'Invoice voided successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Print invoice.
     */
    public function print(Invoice $invoice): View
    {
        $this->authorize('view', $invoice);

        $invoice->load(['items', 'client', 'creator', 'payments']);

        return view('invoices.print', compact('invoice'));
    }
}
