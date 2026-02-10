<?php

namespace App\Http\Controllers;

use App\Enums\QuoteStatus;
use App\Http\Requests\Quote\ConvertQuoteRequest;
use App\Http\Requests\Quote\StoreQuoteRequest;
use App\Http\Requests\Quote\UpdateQuoteRequest;
use App\Models\Client;
use App\Models\Quote;
use App\Services\CacheService;
use App\Services\QuoteService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class QuoteController extends Controller
{
    public function __construct(
        private QuoteService $quoteService
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Quote::class);

        $companyId = $request->user()->company_id;
        $query = Quote::forCompany($companyId)
            ->with(['client', 'creator', 'items']);

        // Filter by status
        if ($request->has('status')) {
            $query->byStatus(QuoteStatus::from($request->status));
        }

        // Filter by client
        if ($request->has('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('quote_number', 'like', "%{$search}%")
                    ->orWhere('total', 'like', "%{$search}%")
                    ->orWhereHas('client', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'quote_date');
        $sortDir = $request->get('sort_dir', 'desc');
        
        switch ($sortBy) {
            case 'quote_number':
                $query->orderBy('quote_number', $sortDir);
                break;
            case 'client':
                $query->join('clients', 'quotes.client_id', '=', 'clients.id')
                    ->orderBy('clients.name', $sortDir)
                    ->select('quotes.*');
                break;
            case 'quote_date':
                $query->orderBy('quote_date', $sortDir);
                break;
            case 'expiry_date':
                $query->orderBy('expiry_date', $sortDir);
                break;
            case 'total':
                $query->orderBy('total', $sortDir);
                break;
            case 'status':
                $query->orderBy('status', $sortDir);
                break;
            default:
                $query->orderBy('quote_date', 'desc');
        }

        $perPage = $request->get('per_page', 15);
        $perPage = in_array($perPage, [10, 15, 25, 50, 100]) ? $perPage : 15;

        $quotes = $query->paginate($perPage)->withQueryString();

        // Cache client list
        $clients = CacheService::getClients(
            $companyId,
            fn() => Client::forCompany($companyId)->orderBy('name')->get()
        );

        return view('quotes.index', compact('quotes', 'clients', 'sortBy', 'sortDir'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        $this->authorize('create', Quote::class);

        $clients = CacheService::getClients(
            $request->user()->company_id,
            fn() => Client::forCompany($request->user()->company_id)
                ->orderBy('name')
                ->get()
        );

        return view('quotes.create', compact('clients'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreQuoteRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();
            $items = $validated['items'] ?? $request->input('items', []);
            
            if (!is_array($items) || empty($items)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Please add at least one item to the quote.');
            }
            
            $quote = $this->quoteService->createDraft(
                $request->user()->company_id,
                $request->user()->id,
                $validated,
                $items
            );

            return redirect()->route('quotes.show', $quote)
                ->with('success', 'Quote created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Quote creation failed', [
                'user_id' => $request->user()->id,
                'company_id' => $request->user()->company_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $errorMessage = config('app.debug') 
                ? 'Failed to create quote: ' . $e->getMessage()
                : 'Failed to create quote. Please check your input and try again.';

            return redirect()->back()
                ->withInput()
                ->with('error', $errorMessage);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Quote $quote): View
    {
        $this->authorize('view', $quote);

        $quote->load(['items', 'client', 'creator', 'invoice']);

        return view('quotes.show', compact('quote'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Quote $quote): View
    {
        $this->authorize('update', $quote);

        $quote->load('items');
        $clients = Client::forCompany($quote->company_id)
            ->orderBy('name')
            ->get();

        return view('quotes.edit', compact('quote', 'clients'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateQuoteRequest $request, Quote $quote): RedirectResponse
    {
        try {
            $quote = $this->quoteService->update(
                $quote,
                $request->validated(),
                $request->items ?? []
            );

            return redirect()->route('quotes.show', $quote)
                ->with('success', 'Quote updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Send a quote.
     */
    public function send(Request $request, Quote $quote): RedirectResponse
    {
        try {
            $quote = $this->quoteService->send($quote);

            return redirect()->route('quotes.show', $quote)
                ->with('success', 'Quote sent successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Accept a quote.
     */
    public function accept(Request $request, Quote $quote): RedirectResponse
    {
        try {
            $quote = $this->quoteService->accept($quote);

            return redirect()->route('quotes.show', $quote)
                ->with('success', 'Quote accepted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Reject a quote.
     */
    public function reject(Request $request, Quote $quote): RedirectResponse
    {
        try {
            $quote = $this->quoteService->reject($quote);

            return redirect()->route('quotes.show', $quote)
                ->with('success', 'Quote rejected.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Convert a quote to an invoice.
     */
    public function convertToInvoice(ConvertQuoteRequest $request, Quote $quote): RedirectResponse
    {
        try {
            $invoice = $this->quoteService->convertToInvoice($quote, $request->validated());

            return redirect()->route('invoices.show', $invoice)
                ->with('success', 'Quote converted to invoice successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }
}
