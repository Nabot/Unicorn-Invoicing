<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-brand-gold leading-tight">
            {{ __('Reports') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Sales Summary Card -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-semibold mb-4">Sales Summary</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            View sales totals, VAT, and payment summaries by date range.
                        </p>
                        <a href="{{ route('reports.sales-summary') }}" class="bg-brand-gold hover:bg-brand-gold-light text-brand-black text-white font-bold py-2 px-4 rounded inline-block">
                            View Sales Summary
                        </a>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-semibold mb-4">Quick Stats</h3>
                        <dl class="space-y-2">
                            <dt class="font-medium">Total Invoices:</dt>
                            <dd class="text-2xl font-bold">{{ \App\Models\Invoice::forCompany(auth()->user()->company_id)->count() }}</dd>
                            <dt class="font-medium">Draft Invoices:</dt>
                            <dd class="text-xl">{{ \App\Models\Invoice::forCompany(auth()->user()->company_id)->where('status', 'draft')->count() }}</dd>
                            <dt class="font-medium">Outstanding Balance:</dt>
                            <dd class="text-xl font-semibold text-yellow-600">
                                {{ number_format(\App\Models\Invoice::forCompany(auth()->user()->company_id)->sum('balance_due'), 2) }}
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
