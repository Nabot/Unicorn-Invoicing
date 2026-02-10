<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Quotations') }}
                </h2>
            </div>
            <div>
                @can('create', App\Models\Quote::class)
                    <a href="{{ route('quotes.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition-all hover:shadow-lg">
                        + New Quote
                    </a>
                @endcan
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

            <!-- Filters -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-4">
                <div class="p-4">
                    <form method="GET" action="{{ route('quotes.index') }}" class="flex flex-wrap gap-4 items-end">
                        <div class="flex-1 min-w-[200px]">
                            <label class="block text-sm font-medium mb-1">Status</label>
                            <select name="status" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-md">
                                <option value="">All Statuses</option>
                                @foreach(\App\Enums\QuoteStatus::cases() as $status)
                                    <option value="{{ $status->value }}" {{ request('status') == $status->value ? 'selected' : '' }}>
                                        {{ $status->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex-1 min-w-[200px]">
                            <label class="block text-sm font-medium mb-1">Client</label>
                            <select name="client_id" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-md">
                                <option value="">All Clients</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                        {{ $client->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex-1 min-w-[200px]">
                            <label class="block text-sm font-medium mb-1">Search</label>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Quote number, client..." class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 rounded-md">
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Filter
                            </button>
                            @if(request()->anyFilled(['status', 'client_id', 'search']))
                                <a href="{{ route('quotes.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                    Clear
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <!-- New Quote Button Above Table -->
            @can('create', App\Models\Quote::class)
                <div class="mb-4 flex justify-end">
                    <a href="{{ route('quotes.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition-all hover:shadow-lg">
                        + New Quote
                    </a>
                </div>
            @endcan

            <!-- Quotes Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    <a href="{{ route('quotes.index', array_merge(request()->all(), ['sort_by' => 'quote_number', 'sort_dir' => $sortBy == 'quote_number' && $sortDir == 'asc' ? 'desc' : 'asc'])) }}" class="hover:text-gray-700 dark:hover:text-gray-200">
                                        Quote #
                                    </a>
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    <a href="{{ route('quotes.index', array_merge(request()->all(), ['sort_by' => 'client', 'sort_dir' => $sortBy == 'client' && $sortDir == 'asc' ? 'desc' : 'asc'])) }}" class="hover:text-gray-700 dark:hover:text-gray-200">
                                        Client
                                    </a>
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    <a href="{{ route('quotes.index', array_merge(request()->all(), ['sort_by' => 'quote_date', 'sort_dir' => $sortBy == 'quote_date' && $sortDir == 'asc' ? 'desc' : 'asc'])) }}" class="hover:text-gray-700 dark:hover:text-gray-200">
                                        Quote Date
                                    </a>
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Expiry Date
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    <a href="{{ route('quotes.index', array_merge(request()->all(), ['sort_by' => 'total', 'sort_dir' => $sortBy == 'total' && $sortDir == 'asc' ? 'desc' : 'asc'])) }}" class="hover:text-gray-700 dark:hover:text-gray-200">
                                        Total
                                    </a>
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($quotes as $quote)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <a href="{{ route('quotes.show', $quote) }}" class="text-blue-600 dark:text-blue-400 hover:underline font-medium">
                                            {{ $quote->quote_number }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        {{ $quote->client->name }}
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        {{ $quote->quote_date->format('M d, Y') }}
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        @if($quote->expiry_date)
                                            <span class="{{ $quote->isExpired() ? 'text-red-600 dark:text-red-400' : '' }}">
                                                {{ $quote->expiry_date->format('M d, Y') }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap font-semibold">
                                        {{ format_currency($quote->total) }}
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-{{ $quote->status->color() }}-100 text-{{ $quote->status->color() }}-800 dark:bg-{{ $quote->status->color() }}-900 dark:text-{{ $quote->status->color() }}-200">
                                            {{ $quote->status->label() }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('quotes.show', $quote) }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 mr-3">View</a>
                                        @can('update', $quote)
                                            @if($quote->canBeEdited())
                                                <a href="{{ route('quotes.edit', $quote) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">Edit</a>
                                            @endif
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                        No quotes found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                    {{ $quotes->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
