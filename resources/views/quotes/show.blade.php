<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Quote #{{ $quote->quote_number }}
                </h2>
            </div>
            <div class="flex flex-wrap gap-2">
                @can('update', $quote)
                    @if($quote->canBeEdited())
                        <a href="{{ route('quotes.edit', $quote) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition-all hover:shadow-lg flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit
                        </a>
                    @endif
                @endcan
                @if($quote->status->value === 'draft')
                    <form method="POST" action="{{ route('quotes.send', $quote) }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition-all hover:shadow-lg flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                            Send Quote
                        </button>
                    </form>
                @endif
                @if($quote->status->value === 'sent')
                    <form method="POST" action="{{ route('quotes.accept', $quote) }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition-all hover:shadow-lg flex items-center gap-2">
                            Accept
                        </button>
                    </form>
                    <form method="POST" action="{{ route('quotes.reject', $quote) }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition-all hover:shadow-lg flex items-center gap-2">
                            Reject
                        </button>
                    </form>
                @endif
                @if($quote->canConvertToInvoice())
                    <form method="POST" action="{{ route('quotes.convert-to-invoice', $quote) }}" class="inline" id="convert-form">
                        @csrf
                        <div class="flex items-center gap-2">
                            <input type="date" name="due_date" value="{{ now()->addDays(30)->format('Y-m-d') }}" required
                                   class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md py-2 px-3"
                                   min="{{ now()->format('Y-m-d') }}">
                            <button type="submit" class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded transition-all hover:shadow-lg flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Convert to Invoice
                            </button>
                        </div>
                    </form>
                @endif
                @if($quote->invoice)
                    <a href="{{ route('invoices.show', $quote->invoice) }}" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded transition-all hover:shadow-lg flex items-center gap-2">
                        View Invoice
                    </a>
                @endif
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

            <!-- Quote Details -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-4">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="grid grid-cols-2 gap-6 mb-6">
                        <div>
                            <h3 class="text-lg font-semibold mb-2">Quote To:</h3>
                            <p class="font-medium">{{ $quote->client->name }}</p>
                            @if($quote->client->address)
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $quote->client->address }}</p>
                            @endif
                            @if($quote->client->email)
                                <p class="text-sm">{{ $quote->client->email }}</p>
                            @endif
                        </div>
                        <div class="text-right">
                            <p class="mb-2"><strong>Quote Number:</strong> {{ $quote->quote_number }}</p>
                            <p class="mb-2"><strong>Status:</strong> 
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold 
                                    @if($quote->status->value === 'draft') bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200
                                    @elseif($quote->status->value === 'sent') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                    @elseif($quote->status->value === 'accepted') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                    @elseif($quote->status->value === 'rejected') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                    @else bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200
                                    @endif">
                                    {{ $quote->status->label() }}
                                </span>
                            </p>
                            <p class="mb-2"><strong>Quote Date:</strong> {{ $quote->quote_date->format('Y-m-d') }}</p>
                            @if($quote->expiry_date)
                                <p class="mb-2"><strong>Expiry Date:</strong> 
                                    <span class="{{ $quote->isExpired() ? 'text-red-600 dark:text-red-400' : '' }}">
                                        {{ $quote->expiry_date->format('Y-m-d') }}
                                    </span>
                                </p>
                            @endif
                            @if($quote->invoice)
                                <p class="mb-2"><strong>Converted to Invoice:</strong> 
                                    <a href="{{ route('invoices.show', $quote->invoice) }}" class="text-blue-600 hover:underline">
                                        {{ $quote->invoice->invoice_number }}
                                    </a>
                                </p>
                            @endif
                        </div>
                    </div>

                    <!-- Quote Items -->
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
                            @foreach($quote->items as $item)
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
                                <td class="px-4 py-2 text-right font-semibold">{{ format_currency($quote->subtotal) }}</td>
                            </tr>
                            <tr>
                                <td colspan="5" class="px-4 py-2 text-right font-semibold">VAT (15%):</td>
                                <td class="px-4 py-2 text-right font-semibold">{{ format_currency($quote->vat_total) }}</td>
                            </tr>
                            <tr class="border-t-2 border-gray-300 dark:border-gray-600">
                                <td colspan="5" class="px-4 py-2 text-right font-semibold text-lg">Total:</td>
                                <td class="px-4 py-2 text-right font-semibold text-lg">{{ format_currency($quote->total) }}</td>
                            </tr>
                        </tfoot>
                    </table>

                    @if($quote->notes || $quote->terms)
                        <div class="grid grid-cols-2 gap-4 mt-6">
                            @if($quote->notes)
                                <div>
                                    <h4 class="font-semibold mb-2">Notes:</h4>
                                    <p class="text-sm">{{ $quote->notes }}</p>
                                </div>
                            @endif
                            @if($quote->terms)
                                <div>
                                    <h4 class="font-semibold mb-2">Terms:</h4>
                                    <p class="text-sm">{{ $quote->terms }}</p>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
