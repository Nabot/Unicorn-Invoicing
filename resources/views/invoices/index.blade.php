<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Invoices') }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
                <!-- Total Invoices -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow cursor-pointer" onclick="window.location='{{ route('invoices.index') }}'">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Total Invoices</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ number_format($stats['totalInvoices']) }}</p>
                            </div>
                            <div class="bg-blue-100 dark:bg-blue-900 rounded-full p-3">
                                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Revenue -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Total Revenue</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ format_currency($stats['totalRevenue']) }}</p>
                            </div>
                            <div class="bg-green-100 dark:bg-green-900 rounded-full p-3">
                                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Outstanding Balance -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow cursor-pointer" onclick="window.location='{{ route('invoices.index', ['filter' => 'unpaid']) }}'">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Outstanding</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ format_currency($stats['outstandingBalance']) }}</p>
                            </div>
                            <div class="bg-yellow-100 dark:bg-yellow-900 rounded-full p-3">
                                <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Overdue Invoices -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow cursor-pointer {{ $stats['overdueCount'] > 0 ? 'ring-2 ring-red-500' : '' }}" onclick="window.location='{{ route('invoices.index', ['filter' => 'overdue']) }}'">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Overdue</p>
                                <p class="text-2xl font-semibold {{ $stats['overdueCount'] > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-gray-100' }}">{{ number_format($stats['overdueCount']) }}</p>
                            </div>
                            <div class="bg-red-100 dark:bg-red-900 rounded-full p-3">
                                <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- This Month Revenue -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow cursor-pointer" onclick="window.location='{{ route('invoices.index', ['date_preset' => 'this_month']) }}'">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">This Month</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ format_currency($stats['thisMonthRevenue']) }}</p>
                                @if($stats['revenueChange'] != 0)
                                    <p class="text-xs mt-1 {{ $stats['revenueChange'] > 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $stats['revenueChange'] > 0 ? '↑' : '↓' }} {{ number_format(abs($stats['revenueChange']), 1) }}%
                                    </p>
                                @endif
                            </div>
                            <div class="bg-purple-100 dark:bg-purple-900 rounded-full p-3">
                                <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Average Invoice Value -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Avg Value</p>
                                <p class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ format_currency($stats['avgInvoiceValue']) }}</p>
                            </div>
                            <div class="bg-indigo-100 dark:bg-indigo-900 rounded-full p-3">
                                <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters and Search -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <!-- Quick Filter Buttons -->
                    <div class="mb-4">
                        <div class="flex flex-wrap gap-2 mb-4">
                            <a href="{{ route('invoices.index') }}" class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ !request()->has('filter') && !request()->has('status') ? 'bg-blue-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600' }}">
                                All
                            </a>
                            <a href="{{ route('invoices.index', ['filter' => 'draft']) }}" class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ request('filter') === 'draft' ? 'bg-blue-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600' }}">
                                Draft
                            </a>
                            <a href="{{ route('invoices.index', ['filter' => 'issued']) }}" class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ request('filter') === 'issued' ? 'bg-blue-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600' }}">
                                Issued
                            </a>
                            <a href="{{ route('invoices.index', ['filter' => 'overdue']) }}" class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ request('filter') === 'overdue' ? 'bg-red-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600' }}">
                                Overdue
                            </a>
                            <a href="{{ route('invoices.index', ['filter' => 'due_soon']) }}" class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ request('filter') === 'due_soon' ? 'bg-yellow-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600' }}">
                                Due Soon
                            </a>
                            <a href="{{ route('invoices.index', ['date_preset' => 'this_month']) }}" class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ request('date_preset') === 'this_month' ? 'bg-blue-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600' }}">
                                This Month
                            </a>
                            <a href="{{ route('invoices.index', ['filter' => 'unpaid']) }}" class="px-4 py-2 rounded-md text-sm font-medium transition-colors {{ request('filter') === 'unpaid' ? 'bg-blue-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600' }}">
                                Unpaid
                            </a>
                        </div>
                    </div>

                    <!-- Search and Advanced Filters -->
                    <form method="GET" action="{{ route('invoices.index') }}" id="filter-form">
                        <!-- Preserve filter parameter from quick filters -->
                        @if(request('filter'))
                            <input type="hidden" name="filter" value="{{ request('filter') }}">
                        @endif
                        @if(request('sort_by'))
                            <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">
                        @endif
                        @if(request('sort_dir'))
                            <input type="hidden" name="sort_dir" value="{{ request('sort_dir') }}">
                        @endif
                        <div class="flex flex-wrap items-end gap-3">
                            <!-- Search -->
                            <div class="flex-1 min-w-[200px]">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search invoices, customers, amounts..." 
                                       class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Status -->
                            <div class="min-w-[140px]">
                                <select name="status" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">All Statuses</option>
                                    @foreach(\App\Enums\InvoiceStatus::cases() as $status)
                                        <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>{{ $status->label() }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Customer -->
                            <div class="min-w-[160px]">
                                <select name="client_id" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">All Customers</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>{{ $client->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Date Preset -->
                            <div class="min-w-[140px]">
                                <select name="date_preset" id="date-preset-select" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Date Range</option>
                                    <option value="today" {{ request('date_preset') === 'today' ? 'selected' : '' }}>Today</option>
                                    <option value="this_week" {{ request('date_preset') === 'this_week' ? 'selected' : '' }}>This Week</option>
                                    <option value="this_month" {{ request('date_preset') === 'this_month' ? 'selected' : '' }}>This Month</option>
                                    <option value="last_month" {{ request('date_preset') === 'last_month' ? 'selected' : '' }}>Last Month</option>
                                    <option value="this_quarter" {{ request('date_preset') === 'this_quarter' ? 'selected' : '' }}>This Quarter</option>
                                    <option value="this_year" {{ request('date_preset') === 'this_year' ? 'selected' : '' }}>This Year</option>
                                    <option value="custom" {{ request('date_preset') === 'custom' || (request()->has('from_date') && !request()->has('date_preset')) ? 'selected' : '' }}>Custom Range</option>
                                </select>
                            </div>

                            <!-- Custom Date Range (shown when custom selected) -->
                            <div class="flex gap-2" id="custom-date-range" style="display: {{ request('date_preset') === 'custom' || (request()->has('from_date') && !request()->has('date_preset')) ? 'flex' : 'none' }};">
                                <input type="date" name="from_date" value="{{ request('from_date') }}" placeholder="From Date" 
                                       class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <input type="date" name="to_date" value="{{ request('to_date') }}" placeholder="To Date" 
                                       class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <!-- Filter Button -->
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition-colors flex items-center gap-2 whitespace-nowrap">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                                </svg>
                                Filter
                            </button>

                            <!-- Clear Filters Button -->
                            @if(request()->hasAny(['status', 'client_id', 'from_date', 'to_date', 'search', 'filter', 'date_preset']))
                                <a href="{{ route('invoices.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition-colors flex items-center gap-2 whitespace-nowrap">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Clear
                                </a>
                            @endif

                            <!-- Items Per Page -->
                            <div class="flex items-center gap-2">
                                <label class="text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">Items per page:</label>
                                <select name="per_page" onchange="this.form.submit()" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="10" {{ request('per_page', 15) == 10 ? 'selected' : '' }}>10</option>
                                    <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15</option>
                                    <option value="25" {{ request('per_page', 15) == 25 ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ request('per_page', 15) == 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ request('per_page', 15) == 100 ? 'selected' : '' }}>100</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Alerts -->
            @if(session('success'))
                <x-alert type="success" dismissible>{{ session('success') }}</x-alert>
            @endif
            @if(session('error'))
                <x-alert type="error" dismissible>{{ session('error') }}</x-alert>
            @endif

            <!-- Invoices Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <!-- New Invoice Button -->
                    <div class="mb-4 flex justify-end">
                        @can('create', App\Models\Invoice::class)
                            <a href="{{ route('invoices.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm touch-manipulation transition-all hover:shadow-lg flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                {{ __('New Invoice') }}
                            </a>
                        @endcan
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        <a href="{{ route('invoices.index', array_merge(request()->query(), ['sort_by' => 'invoice_number', 'sort_dir' => $sortBy === 'invoice_number' && $sortDir === 'asc' ? 'desc' : 'asc'])) }}" class="flex items-center gap-1 hover:text-gray-700 dark:hover:text-gray-200">
                                            Invoice #
                                            @if($sortBy === 'invoice_number')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDir === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                                </svg>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        <a href="{{ route('invoices.index', array_merge(request()->query(), ['sort_by' => 'client', 'sort_dir' => $sortBy === 'client' && $sortDir === 'asc' ? 'desc' : 'asc'])) }}" class="flex items-center gap-1 hover:text-gray-700 dark:hover:text-gray-200">
                                            Customer
                                            @if($sortBy === 'client')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDir === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                                </svg>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        <a href="{{ route('invoices.index', array_merge(request()->query(), ['sort_by' => 'issue_date', 'sort_dir' => $sortBy === 'issue_date' && $sortDir === 'asc' ? 'desc' : 'asc'])) }}" class="flex items-center gap-1 hover:text-gray-700 dark:hover:text-gray-200">
                                            Issue Date
                                            @if($sortBy === 'issue_date')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDir === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                                </svg>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        <a href="{{ route('invoices.index', array_merge(request()->query(), ['sort_by' => 'due_date', 'sort_dir' => $sortBy === 'due_date' && $sortDir === 'asc' ? 'desc' : 'asc'])) }}" class="flex items-center gap-1 hover:text-gray-700 dark:hover:text-gray-200">
                                            Due Date
                                            @if($sortBy === 'due_date')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDir === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                                </svg>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        <a href="{{ route('invoices.index', array_merge(request()->query(), ['sort_by' => 'total', 'sort_dir' => $sortBy === 'total' && $sortDir === 'asc' ? 'desc' : 'asc'])) }}" class="flex items-center gap-1 hover:text-gray-700 dark:hover:text-gray-200">
                                            Total
                                            @if($sortBy === 'total')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDir === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                                </svg>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        <a href="{{ route('invoices.index', array_merge(request()->query(), ['sort_by' => 'balance_due', 'sort_dir' => $sortBy === 'balance_due' && $sortDir === 'asc' ? 'desc' : 'asc'])) }}" class="flex items-center gap-1 hover:text-gray-700 dark:hover:text-gray-200">
                                            Balance Due
                                            @if($sortBy === 'balance_due')
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sortDir === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                                </svg>
                                            @endif
                                        </a>
                                    </th>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($invoices as $invoice)
                                    @php
                                        $isOverdue = $invoice->due_date < now() && in_array($invoice->status->value, ['issued', 'partially_paid']);
                                        $daysOverdue = $isOverdue ? now()->diffInDays($invoice->due_date) : null;
                                    @endphp
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors {{ $isOverdue ? 'bg-red-50 dark:bg-red-900/20' : '' }}">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="font-medium text-gray-900 dark:text-gray-100">{{ $invoice->invoice_number }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $invoice->items->count() }} items</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center gap-2">
                                                <x-client-avatar :name="$invoice->client->name" size="sm" />
                                                <span class="text-gray-900 dark:text-gray-100">{{ $invoice->client->name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-600 dark:text-gray-400">
                                            {{ $invoice->issue_date?->format('Y-m-d') ?? 'Draft' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-gray-900 dark:text-gray-100 font-medium">{{ $invoice->due_date->format('Y-m-d') }}</div>
                                            @if($isOverdue)
                                                <div class="text-xs text-red-600 dark:text-red-400 font-semibold">{{ $daysOverdue }} day{{ $daysOverdue != 1 ? 's' : '' }} overdue</div>
                                            @elseif($invoice->due_date <= now()->addDays(7) && in_array($invoice->status->value, ['issued', 'partially_paid']))
                                                <div class="text-xs text-yellow-600 dark:text-yellow-400">Due in {{ now()->diffInDays($invoice->due_date) }} day{{ now()->diffInDays($invoice->due_date) != 1 ? 's' : '' }}</div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap font-semibold text-gray-900 dark:text-gray-100">
                                            {{ format_currency($invoice->total) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($invoice->balance_due > 0)
                                                <div class="text-gray-900 dark:text-gray-100 font-medium">{{ format_currency($invoice->balance_due) }}</div>
                                                @if($invoice->status->value === 'partially_paid')
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                                        {{ number_format(($invoice->amount_paid / $invoice->total) * 100, 1) }}% paid
                                                    </div>
                                                @endif
                                            @else
                                                <span class="text-gray-400 dark:text-gray-500">—</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $statusColor = match($invoice->status->value) {
                                                    'draft' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
                                                    'issued' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                                    'partially_paid' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                                    'paid' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                                    'void' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                                    default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
                                                };
                                            @endphp
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $statusColor }}">
                                                {{ $invoice->status->label() }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div x-data="{ open: false }" class="relative inline-block text-left">
                                                <button @click.stop="open = !open" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100 focus:outline-none">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                                    </svg>
                                                </button>
                                                <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg z-10 border border-gray-200 dark:border-gray-700">
                                                    <div class="py-1">
                                                        <a href="{{ route('invoices.show', $invoice) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                            </svg>
                                                            View Details
                                                        </a>
                                                        @can('update', $invoice)
                                                            @if($invoice->canBeEdited())
                                                                <a href="{{ route('invoices.edit', $invoice) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                                    </svg>
                                                                    Edit Invoice
                                                                </a>
                                                            @endif
                                                        @endcan
                                                        <a href="{{ route('invoices.download.pdf', $invoice) }}" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center gap-2">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                            </svg>
                                                            Download PDF
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center justify-center">
                                                <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                <p class="text-gray-500 dark:text-gray-400 text-lg font-medium mb-2">No invoices found</p>
                                                <p class="text-gray-400 dark:text-gray-500 text-sm mb-4">Try adjusting your filters or create a new invoice</p>
                                                @can('create', App\Models\Invoice::class)
                                                    <a href="{{ route('invoices.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                                        Create Your First Invoice
                                                    </a>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if($invoices->count() > 0)
                            <tfoot class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-gray-100 text-right">
                                        Summary ({{ $summaryTotals['count'] }} invoices):
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-gray-100">
                                        {{ format_currency($summaryTotals['total_amount']) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-gray-100">
                                        {{ format_currency($summaryTotals['total_outstanding']) }}
                                    </td>
                                    <td colspan="2" class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                        Avg: {{ format_currency($summaryTotals['avg_value']) }}
                                    </td>
                                </tr>
                            </tfoot>
                            @endif
                        </table>
                    </div>

                    <div class="mt-4">{{ $invoices->links() }}</div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Show/hide custom date range on page load
        document.addEventListener('DOMContentLoaded', function() {
            const datePresetSelect = document.getElementById('date-preset-select');
            const customRange = document.getElementById('custom-date-range');
            
            if (datePresetSelect && customRange) {
                // Set initial state
                if (datePresetSelect.value === 'custom' || customRange.style.display === 'flex') {
                    customRange.style.display = 'flex';
                } else {
                    customRange.style.display = 'none';
                }
                
                // Handle changes
                datePresetSelect.addEventListener('change', function() {
                    if (this.value === 'custom') {
                        customRange.style.display = 'flex';
                    } else {
                        customRange.style.display = 'none';
                    }
                });
            }
        });
    </script>
    @endpush
</x-app-layout>
