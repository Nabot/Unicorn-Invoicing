<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-brand-gold leading-tight">
            {{ __('Sales Summary Report') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-4">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="GET" action="{{ route('reports.sales-summary') }}" class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">From Date</label>
                            <input type="date" name="from_date" value="{{ $fromDate }}" required
                                   class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">To Date</label>
                            <input type="date" name="to_date" value="{{ $toDate }}" required
                                   class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md">
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="bg-brand-gold hover:bg-brand-gold-light text-brand-black text-white font-bold py-2 px-4 rounded w-full">
                                Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Summary Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-4">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">Summary</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Total Invoices</p>
                            <p class="text-2xl font-bold">{{ $summary['total_invoices'] }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Subtotal</p>
                            <p class="text-2xl font-bold">{{ number_format($summary['subtotal'], 2) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">VAT Total</p>
                            <p class="text-2xl font-bold">{{ number_format($summary['vat_total'], 2) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Total</p>
                            <p class="text-2xl font-bold text-blue-600">{{ number_format($summary['total'], 2) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Amount Paid</p>
                            <p class="text-2xl font-bold text-green-600">{{ number_format($summary['amount_paid'], 2) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Balance Due</p>
                            <p class="text-2xl font-bold text-yellow-600">{{ number_format($summary['balance_due'], 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- By Status -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-4">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">Invoices by Status</h3>
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                                <th class="px-4 py-2 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Count</th>
                                <th class="px-4 py-2 bg-gray-50 dark:bg-gray-700 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($byStatus as $status => $data)
                                <tr>
                                    <td class="px-4 py-2">{{ \App\Enums\InvoiceStatus::from($status)->label() }}</td>
                                    <td class="px-4 py-2">{{ $data['count'] }}</td>
                                    <td class="px-4 py-2 text-right">{{ number_format($data['total'], 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Invoice Aging -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">Invoice Aging (Outstanding Balances)</h3>
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Age Range</th>
                                <th class="px-4 py-2 bg-gray-50 dark:bg-gray-700 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Balance Due</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="px-4 py-2">0-30 days</td>
                                <td class="px-4 py-2 text-right">{{ number_format($aging['0-30'], 2) }}</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2">31-60 days</td>
                                <td class="px-4 py-2 text-right">{{ number_format($aging['31-60'], 2) }}</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2">61-90 days</td>
                                <td class="px-4 py-2 text-right">{{ number_format($aging['61-90'], 2) }}</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2">90+ days</td>
                                <td class="px-4 py-2 text-right font-semibold text-red-600">{{ number_format($aging['90+'], 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
