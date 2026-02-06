<?php

namespace App\Http\Controllers;

use App\Http\Requests\Client\StoreClientRequest;
use App\Http\Requests\Client\UpdateClientRequest;
use App\Models\Client;
use App\Services\CacheService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Client::class);

        $companyId = $request->user()->company_id;

        // Get statistics
        $cacheService = app(CacheService::class);
        $stats = $cacheService->remember("clients_stats_{$companyId}", 1800, function () use ($companyId) {
            $totalClients = Client::forCompany($companyId)->count();
            $activeClients = Client::forCompany($companyId)->has('invoices')->count();
            
            $totalRevenue = \App\Models\Invoice::forCompany($companyId)
                ->where('status', \App\Enums\InvoiceStatus::PAID)
                ->sum('total');
            
            $avgInvoiceValue = \App\Models\Invoice::forCompany($companyId)
                ->where('status', \App\Enums\InvoiceStatus::PAID)
                ->avg('total');
            
            $withOutstanding = Client::forCompany($companyId)
                ->whereHas('invoices', function($q) {
                    $q->whereIn('status', [\App\Enums\InvoiceStatus::ISSUED, \App\Enums\InvoiceStatus::PARTIALLY_PAID]);
                })->count();

            return compact('totalClients', 'activeClients', 'totalRevenue', 'avgInvoiceValue', 'withOutstanding');
        });

        $query = Client::forCompany($companyId)
            ->withCount(['invoices as total_invoices'])
            ->withSum(['invoices as total_revenue' => function($q) {
                $q->where('status', \App\Enums\InvoiceStatus::PAID);
            }], 'total')
            ->withSum(['invoices as outstanding_balance' => function($q) {
                $q->whereIn('status', [\App\Enums\InvoiceStatus::ISSUED, \App\Enums\InvoiceStatus::PARTIALLY_PAID]);
            }], 'balance_due')
            ->with(['invoices' => function($q) {
                $q->latest()->limit(1);
            }]);

        // Filter by search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status_filter')) {
            $statusFilter = $request->status_filter;
            if ($statusFilter === 'active') {
                $query->has('invoices');
            } elseif ($statusFilter === 'inactive') {
                $query->doesntHave('invoices');
            } elseif ($statusFilter === 'outstanding') {
                $query->whereHas('invoices', function($q) {
                    $q->whereIn('status', [\App\Enums\InvoiceStatus::ISSUED, \App\Enums\InvoiceStatus::PARTIALLY_PAID]);
                });
            }
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'name_asc');
        switch ($sortBy) {
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'revenue_desc':
                $query->orderBy('total_revenue', 'desc');
                break;
            case 'invoices_desc':
                $query->orderBy('total_invoices', 'desc');
                break;
            case 'recent':
                $query->orderBy('updated_at', 'desc');
                break;
            case 'name_asc':
            default:
                $query->orderBy('name', 'asc');
                break;
        }

        $perPage = $request->get('per_page', 15);
        $clients = $query->paginate($perPage)->withQueryString();

        return view('clients.index', compact('clients', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $this->authorize('create', Client::class);

        return view('clients.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreClientRequest $request): RedirectResponse
    {
        $client = Client::create([
            'company_id' => $request->user()->company_id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'vat_number' => $request->vat_number,
            'user_id' => $request->user_id,
        ]);

        // Clear cache
        CacheService::clearClients($request->user()->company_id);

        return redirect()->route('clients.show', $client)
            ->with('success', 'Customer created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client): View
    {
        $this->authorize('view', $client);

        $client->load([
            'invoices' => function ($query) {
                $query->latest();
            }, 
            'invoices.payments',
            'invoices.items'
        ]);

        // Calculate statistics
        $stats = [
            'total_invoices' => $client->invoices->count(),
            'total_revenue' => $client->invoices->where('status', \App\Enums\InvoiceStatus::PAID)->sum('total'),
            'outstanding_balance' => $client->invoices->whereIn('status', [\App\Enums\InvoiceStatus::ISSUED, \App\Enums\InvoiceStatus::PARTIALLY_PAID])->sum('balance_due'),
            'avg_invoice_value' => $client->invoices->where('status', \App\Enums\InvoiceStatus::PAID)->avg('total'),
            'paid_invoices' => $client->invoices->where('status', \App\Enums\InvoiceStatus::PAID)->count(),
            'pending_invoices' => $client->invoices->whereIn('status', [\App\Enums\InvoiceStatus::ISSUED, \App\Enums\InvoiceStatus::PARTIALLY_PAID])->count(),
        ];

        // Group invoices by status
        $invoicesByStatus = $client->invoices->groupBy('status');

        return view('clients.show', compact('client', 'stats', 'invoicesByStatus'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client): View
    {
        $this->authorize('update', $client);

        return view('clients.edit', compact('client'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateClientRequest $request, Client $client): RedirectResponse
    {
        $client->update($request->validated());

        return redirect()->route('clients.show', $client)
            ->with('success', 'Customer updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client): RedirectResponse
    {
        $this->authorize('delete', $client);

        // Prevent deletion if client has invoices
        if ($client->invoices()->exists()) {
            return redirect()->route('clients.show', $client)
                ->with('error', 'Cannot delete customer with existing invoices.');
        }

        $companyId = $client->company_id;
        $client->delete();

        // Clear cache
        CacheService::clearClients($companyId);

        return redirect()->route('clients.index')
            ->with('success', 'Customer deleted successfully.');
    }
}
