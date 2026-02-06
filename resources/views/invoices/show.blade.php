<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Invoice #{{ $invoice->invoice_number }}
                </h2>
            </div>
            <div class="flex flex-wrap gap-2">
                @can('update', $invoice)
                    @if($invoice->canBeEdited())
                        <a href="{{ route('invoices.edit', $invoice) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition-all hover:shadow-lg flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit
                        </a>
                    @endif
                @endcan
                @can('issue', $invoice)
                    @if($invoice->status->value === 'draft')
                        <form method="POST" action="{{ route('invoices.issue', $invoice) }}" class="inline">
                            @csrf
                            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition-all hover:shadow-lg flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Issue Invoice
                            </button>
                        </form>
                    @endif
                @endcan
                @can('void', $invoice)
                    @if($invoice->status->value !== 'void' && $invoice->status->value !== 'paid')
                        <form method="POST" action="{{ route('invoices.void', $invoice) }}" class="inline" onsubmit="return confirm('Are you sure you want to void this invoice?');">
                            @csrf
                            <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition-all hover:shadow-lg flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Void Invoice
                            </button>
                        </form>
                    @endif
                @endcan
                <a href="{{ route('invoices.download.pdf', $invoice) }}" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition-all hover:shadow-lg flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Download PDF
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <x-alert type="success" dismissible>{{ session('success') }}</x-alert>
            @endif
            @if(session('error'))
                <x-alert type="error" dismissible>{{ session('error') }}</x-alert>
            @endif

            <!-- Invoice Details -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-4">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="grid grid-cols-2 gap-6 mb-6">
                        <div>
                            <h3 class="text-lg font-semibold mb-2">Bill To:</h3>
                            <p class="font-medium">{{ $invoice->client->name }}</p>
                            @if($invoice->client->address)
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $invoice->client->address }}</p>
                            @endif
                            @if($invoice->client->email)
                                <p class="text-sm">{{ $invoice->client->email }}</p>
                            @endif
                        </div>
                        <div class="text-right">
                            <p class="mb-2"><strong>Invoice Number:</strong> {{ $invoice->invoice_number }}</p>
                            <p class="mb-2"><strong>Status:</strong> 
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold 
                                    @if($invoice->status->value === 'draft') bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200
                                    @elseif($invoice->status->value === 'issued') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                    @elseif($invoice->status->value === 'partially_paid') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                    @elseif($invoice->status->value === 'paid') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                    @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                    @endif">
                                    {{ $invoice->status->label() }}
                                </span>
                            </p>
                            <p class="mb-2"><strong>Issue Date:</strong> {{ $invoice->issue_date?->format('Y-m-d') ?? 'Draft' }}</p>
                            <p><strong>Due Date:</strong> {{ $invoice->due_date->format('Y-m-d') }}</p>
                        </div>
                    </div>

                    <!-- Invoice Items -->
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 mb-6">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Description</th>
                                <th class="px-4 py-2 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Quantity</th>
                                <th class="px-4 py-2 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Unit Price</th>
                                <th class="px-4 py-2 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Discount</th>
                                <th class="px-4 py-2 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">VAT</th>
                                <th class="px-4 py-2 bg-gray-50 dark:bg-gray-700 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($invoice->items as $item)
                                <tr>
                                    <td class="px-4 py-2">{{ $item->description }}</td>
                                    <td class="px-4 py-2">{{ number_format($item->quantity, 2) }}</td>
                                    <td class="px-4 py-2">{{ format_currency($item->unit_price) }}</td>
                                    <td class="px-4 py-2">{{ format_currency($item->discount ?? 0) }}</td>
                                    <td class="px-4 py-2">{{ $item->vat_applicable ? '15%' : 'No' }}</td>
                                    <td class="px-4 py-2 text-right font-medium">{{ format_currency($item->line_total) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="border-t-2 border-gray-300 dark:border-gray-600">
                                <td colspan="5" class="px-4 py-2 text-right font-semibold">Subtotal:</td>
                                <td class="px-4 py-2 text-right font-semibold">{{ config('app.currency', 'R') }} {{ number_format($invoice->subtotal, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="5" class="px-4 py-2 text-right font-semibold">VAT (15%):</td>
                                <td class="px-4 py-2 text-right font-semibold">{{ format_currency($invoice->vat_total) }}</td>
                            </tr>
                            <tr class="border-t-2 border-gray-300 dark:border-gray-600">
                                <td colspan="5" class="px-4 py-2 text-right font-semibold text-lg">Total:</td>
                                <td class="px-4 py-2 text-right font-semibold text-lg">{{ format_currency($invoice->total) }}</td>
                            </tr>
                            <tr>
                                <td colspan="5" class="px-4 py-2 text-right font-semibold">Amount Paid:</td>
                                <td class="px-4 py-2 text-right font-semibold">{{ format_currency($invoice->amount_paid) }}</td>
                            </tr>
                            <tr class="bg-yellow-50 dark:bg-yellow-900">
                                <td colspan="5" class="px-4 py-2 text-right font-semibold text-lg">Balance Due:</td>
                                <td class="px-4 py-2 text-right font-semibold text-lg">{{ format_currency($invoice->balance_due) }}</td>
                            </tr>
                        </tfoot>
                    </table>

                    @if($invoice->notes || $invoice->terms)
                        <div class="grid grid-cols-2 gap-4 mt-6">
                            @if($invoice->notes)
                                <div>
                                    <h4 class="font-semibold mb-2">Notes:</h4>
                                    <p class="text-sm">{{ $invoice->notes }}</p>
                                </div>
                            @endif
                            @if($invoice->terms)
                                <div>
                                    <h4 class="font-semibold mb-2">Terms:</h4>
                                    <p class="text-sm">{{ $invoice->terms }}</p>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Payments -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-4">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Payments</h3>
                        @can('create', App\Models\Payment::class)
                            @if($invoice->status->value !== 'void' && $invoice->status->value !== 'paid')
                                <a href="{{ route('payments.create', $invoice) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition-all hover:shadow-lg flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Record Payment
                                </a>
                            @endif
                        @endcan
                    </div>

                    @if($invoice->payments->count() > 0)
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Date</th>
                                    <th class="px-4 py-2 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Amount</th>
                                    <th class="px-4 py-2 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Method</th>
                                    <th class="px-4 py-2 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Reference</th>
                                    <th class="px-4 py-2 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Recorded By</th>
                                    <th class="px-4 py-2 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoice->payments as $payment)
                                    <tr>
                                        <td class="px-4 py-2">{{ $payment->payment_date->format('Y-m-d') }}</td>
                                        <td class="px-4 py-2 font-semibold">{{ config('app.currency', 'R') }} {{ number_format($payment->amount, 2) }}</td>
                                        <td class="px-4 py-2">{{ $payment->method->label() }}</td>
                                        <td class="px-4 py-2">{{ $payment->reference ?? 'N/A' }}</td>
                                        <td class="px-4 py-2">{{ $payment->recorder->name }}</td>
                                        <td class="px-4 py-2">
                                            @can('update', $payment)
                                                <a href="{{ route('payments.edit', [$invoice, $payment]) }}" class="text-blue-600 hover:text-blue-800">Edit</a>
                                            @endcan
                                            @can('delete', $payment)
                                                <form method="POST" action="{{ route('payments.destroy', [$invoice, $payment]) }}" class="inline" onsubmit="return confirm('Are you sure?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                                                </form>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="text-center py-8">
                            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <p class="text-gray-500 dark:text-gray-400 text-lg font-medium mb-2">No payments recorded</p>
                            <p class="text-gray-400 dark:text-gray-500 text-sm mb-4">Record a payment to track invoice payments</p>
                            @can('create', App\Models\Payment::class)
                                @if($invoice->status->value !== 'void' && $invoice->status->value !== 'paid')
                                    <a href="{{ route('payments.create', $invoice) }}" class="inline-block bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                        Record First Payment
                                    </a>
                                @endif
                            @endcan
                        </div>
                    @endif
                </div>
            </div>

            <!-- Audit Log -->
            @if($auditLogs->count() > 0)
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-semibold mb-4">Activity Log</h3>
                        <div class="space-y-2">
                            @foreach($auditLogs as $log)
                                <div class="border-l-4 border-blue-500 pl-4 py-2">
                                    <p class="text-sm">
                                        <strong>{{ $log->action }}</strong>
                                        @if($log->actor)
                                            by {{ $log->actor->name }}
                                        @endif
                                        <span class="text-gray-500">{{ $log->created_at->diffForHumans() }}</span>
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
