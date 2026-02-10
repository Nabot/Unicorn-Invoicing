<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <div class="flex items-center gap-3">
                    <x-client-avatar :name="$client->name" size="lg" />
                    <h2 class="font-semibold text-xl text-brand-gold leading-tight">
                        {{ $client->name }}
                    </h2>
                </div>
            </div>
            <div class="flex gap-2">
                @can('create', App\Models\Invoice::class)
                    <a href="{{ route('invoices.create', ['client_id' => $client->id]) }}" class="bg-brand-gold hover:bg-brand-gold-light text-brand-black text-white font-bold py-2 px-4 rounded transition-all hover:shadow-lg flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Create Invoice
                    </a>
                @endcan
                @can('update', $client)
                    <a href="{{ route('clients.edit', $client) }}" class="bg-brand-gold hover:bg-brand-gold-light text-brand-black text-white font-bold py-2 px-4 rounded transition-all hover:shadow-lg flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                    </a>
                @endcan
                @if(auth()->user()->can('manage-clients') && auth()->user()->company_id === $client->company_id)
                    @if($client->invoices->count() > 0)
                        <button type="button" disabled class="bg-gray-400 cursor-not-allowed text-white font-bold py-2 px-4 rounded flex items-center gap-2" title="Cannot delete customer with existing invoices.">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Delete Customer
                        </button>
                    @else
                        <form method="POST" action="{{ route('clients.destroy', $client) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this customer? This action cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-brand-black hover:bg-gray-800 text-brand-gold border border-brand-gold text-white font-bold py-2 px-4 rounded transition-all hover:shadow-lg flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Delete Customer
                            </button>
                        </form>
                    @endif
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

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                    <div class="p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-brand-gold rounded-md p-2">
                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Total Invoices</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ number_format($stats['total_invoices']) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                    <div class="p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-brand-gold rounded-md p-2">
                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Total Revenue</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ config('app.currency', 'R') }} {{ number_format($stats['total_revenue'], 2) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                    <div class="p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-yellow-500 rounded-md p-2">
                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Outstanding</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ config('app.currency', 'R') }} {{ number_format($stats['outstanding_balance'], 2) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                    <div class="p-4">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-brand-gold rounded-md p-2">
                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Avg Invoice</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ config('app.currency', 'R') }} {{ number_format($stats['avg_invoice_value'] ?? 0, 2) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <div x-data="{ activeTab: 'overview' }" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <!-- Tab Headers -->
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <nav class="flex -mb-px">
                        <button @click="activeTab = 'overview'" :class="activeTab === 'overview' ? 'border-brand-gold text-brand-gold dark:text-brand-gold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'" class="py-4 px-6 border-b-2 font-medium text-sm transition-colors">
                            Overview
                        </button>
                        <button @click="activeTab = 'invoices'" :class="activeTab === 'invoices' ? 'border-brand-gold text-brand-gold dark:text-brand-gold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'" class="py-4 px-6 border-b-2 font-medium text-sm transition-colors">
                            Invoices ({{ $stats['total_invoices'] }})
                        </button>
                        <button @click="activeTab = 'statistics'" :class="activeTab === 'statistics' ? 'border-brand-gold text-brand-gold dark:text-brand-gold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'" class="py-4 px-6 border-b-2 font-medium text-sm transition-colors">
                            Statistics
                        </button>
                    </nav>
                </div>

                <!-- Tab Content -->
                <div class="p-6">
                    <!-- Overview Tab -->
                    <div x-show="activeTab === 'overview'" x-transition>
                        <h3 class="text-lg font-semibold mb-4">Customer Information</h3>
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Name</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $client->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $client->email ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Phone</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $client->phone ?? 'N/A' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">VAT Number</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $client->vat_number ?? 'N/A' }}</dd>
                            </div>
                            @if($client->address)
                            <div class="md:col-span-2">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Address</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $client->address }}</dd>
                            </div>
                            @endif
                        </dl>

                        <!-- Quick Stats -->
                        <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['paid_invoices'] }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Paid Invoices</p>
                            </div>
                            <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['pending_invoices'] }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Pending Invoices</p>
                            </div>
                            <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $invoicesByStatus->get('draft')?->count() ?? 0 }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Draft Invoices</p>
                            </div>
                            <div class="text-center p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $invoicesByStatus->get('void')?->count() ?? 0 }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Void Invoices</p>
                            </div>
                        </div>
                    </div>

                    <!-- Invoices Tab -->
                    <div x-show="activeTab === 'invoices'" x-transition>
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-semibold">All Invoices</h3>
                            @can('create', App\Models\Invoice::class)
                                <a href="{{ route('invoices.create', ['client_id' => $client->id]) }}" class="bg-brand-gold hover:bg-brand-gold-light text-brand-black text-white font-bold py-2 px-4 rounded text-sm transition-all hover:shadow-lg flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    New Invoice
                                </a>
                            @endcan
                        </div>
                        @if($client->invoices->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead>
                                        <tr>
                                            <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Invoice #</th>
                                            <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Date</th>
                                            <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Due Date</th>
                                            <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Total</th>
                                            <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                                            <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($client->invoices as $invoice)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <a href="{{ route('invoices.show', $invoice) }}" class="text-brand-gold hover:text-brand-gold-light dark:text-brand-gold font-medium">
                                                        {{ $invoice->invoice_number }}
                                                    </a>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                                    {{ $invoice->issue_date?->format('M d, Y') ?? 'Draft' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                                    {{ $invoice->due_date->format('M d, Y') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                    {{ config('app.currency', 'R') }} {{ number_format($invoice->total, 2) }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold 
                                                        @if($invoice->status->value === 'draft') bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200
                                                        @elseif($invoice->status->value === 'issued') bg-brand-gold/20 text-brand-gold border border-brand-gold
                                                        @elseif($invoice->status->value === 'partially_paid') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                                        @elseif($invoice->status->value === 'paid') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                                        @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                                        @endif">
                                                        {{ $invoice->status->label() }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                    <a href="{{ route('invoices.show', $invoice) }}" class="text-brand-gold hover:text-brand-gold-light dark:text-indigo-400">
                                                        View
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-12">
                                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="text-gray-500 dark:text-gray-400 text-lg font-medium mb-2">No invoices yet</p>
                                <p class="text-gray-400 dark:text-gray-500 text-sm mb-4">Create the first invoice for this customer</p>
                                @can('create', App\Models\Invoice::class)
                                    <a href="{{ route('invoices.create', ['client_id' => $client->id]) }}" class="inline-block bg-brand-gold hover:bg-brand-gold-light text-brand-black text-white font-bold py-2 px-4 rounded">
                                        Create First Invoice
                                    </a>
                                @endcan
                            </div>
                        @endif
                    </div>

                    <!-- Statistics Tab -->
                    <div x-show="activeTab === 'statistics'" x-transition>
                        <h3 class="text-lg font-semibold mb-4">Customer Statistics</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                                <h4 class="font-semibold mb-4">Invoice Breakdown</h4>
                                <dl class="space-y-3">
                                    <div class="flex justify-between">
                                        <dt class="text-sm text-gray-600 dark:text-gray-400">Total Invoices</dt>
                                        <dd class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $stats['total_invoices'] }}</dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-sm text-gray-600 dark:text-gray-400">Paid</dt>
                                        <dd class="text-sm font-semibold text-brand-gold dark:text-green-400">{{ $stats['paid_invoices'] }}</dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-sm text-gray-600 dark:text-gray-400">Pending</dt>
                                        <dd class="text-sm font-semibold text-yellow-600 dark:text-yellow-400">{{ $stats['pending_invoices'] }}</dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-sm text-gray-600 dark:text-gray-400">Draft</dt>
                                        <dd class="text-sm font-semibold text-gray-600 dark:text-gray-400">{{ $invoicesByStatus->get('draft')?->count() ?? 0 }}</dd>
                                    </div>
                                </dl>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                                <h4 class="font-semibold mb-4">Financial Summary</h4>
                                <dl class="space-y-3">
                                    <div class="flex justify-between">
                                        <dt class="text-sm text-gray-600 dark:text-gray-400">Total Revenue</dt>
                                        <dd class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ config('app.currency', 'R') }} {{ number_format($stats['total_revenue'], 2) }}</dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-sm text-gray-600 dark:text-gray-400">Outstanding Balance</dt>
                                        <dd class="text-sm font-semibold text-red-600 dark:text-red-400">{{ config('app.currency', 'R') }} {{ number_format($stats['outstanding_balance'], 2) }}</dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-sm text-gray-600 dark:text-gray-400">Average Invoice Value</dt>
                                        <dd class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ config('app.currency', 'R') }} {{ number_format($stats['avg_invoice_value'] ?? 0, 2) }}</dd>
                                    </div>
                                    @if($stats['total_invoices'] > 0)
                                    <div class="flex justify-between">
                                        <dt class="text-sm text-gray-600 dark:text-gray-400">Payment Rate</dt>
                                        <dd class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ number_format(($stats['paid_invoices'] / $stats['total_invoices']) * 100, 1) }}%</dd>
                                    </div>
                                    @endif
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
