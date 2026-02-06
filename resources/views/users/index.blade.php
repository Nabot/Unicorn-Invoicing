<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Users') }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <!-- Filters -->
                    <div class="mb-4 space-y-4">
                        <form method="GET" action="{{ route('users.index') }}" class="flex flex-wrap gap-2">
                            <select name="sort_by" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="name_asc" {{ request('sort_by', 'name_asc') === 'name_asc' ? 'selected' : '' }}>Name (A-Z)</option>
                                <option value="name_desc" {{ request('sort_by') === 'name_desc' ? 'selected' : '' }}>Name (Z-A)</option>
                                <option value="email_asc" {{ request('sort_by') === 'email_asc' ? 'selected' : '' }}>Email (A-Z)</option>
                                <option value="email_desc" {{ request('sort_by') === 'email_desc' ? 'selected' : '' }}>Email (Z-A)</option>
                                <option value="recent" {{ request('sort_by') === 'recent' ? 'selected' : '' }}>Recently Added</option>
                            </select>

                            <select name="per_page" onchange="this.form.submit()" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="10" {{ request('per_page', 15) == 10 ? 'selected' : '' }}>10 per page</option>
                                <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15 per page</option>
                                <option value="25" {{ request('per_page', 15) == 25 ? 'selected' : '' }}>25 per page</option>
                                <option value="50" {{ request('per_page', 15) == 50 ? 'selected' : '' }}>50 per page</option>
                                <option value="100" {{ request('per_page', 15) == 100 ? 'selected' : '' }}>100 per page</option>
                            </select>
                            
                            @if(request()->has('sort_by') && request('sort_by') !== 'name_asc')
                                <a href="{{ route('users.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded transition-all hover:shadow-lg flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Clear Filters
                                </a>
                            @endif
                        </form>
                    </div>

                    @if(session('success'))
                        <x-alert type="success" dismissible>{{ session('success') }}</x-alert>
                    @endif
                    @if(session('error'))
                        <x-alert type="error" dismissible>{{ session('error') }}</x-alert>
                    @endif

                    <!-- User Limit Info and New User Button -->
                    <div class="mb-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            <span class="font-medium">Users:</span> 
                            <span class="{{ $currentUserCount >= $maxUsers ? 'text-red-600 dark:text-red-400 font-bold' : '' }}">
                                {{ $currentUserCount }} / {{ $maxUsers }}
                            </span>
                            @if($currentUserCount >= $maxUsers)
                                <span class="ml-2 text-red-600 dark:text-red-400">(Limit reached)</span>
                            @endif
                        </div>
                        @if($canAddMore)
                            <a href="{{ route('users.create') }}" 
                               class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-sm touch-manipulation transition-all hover:shadow-lg flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                {{ __('New User') }}
                            </a>
                        @else
                            <button disabled 
                                    class="bg-gray-400 text-white font-bold py-2 px-4 rounded text-sm cursor-not-allowed flex items-center gap-2 opacity-50">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                {{ __('New User') }}
                            </button>
                        @endif
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-800" onclick="window.location.href='{{ route('users.index', array_merge(request()->query(), ['sort_by' => request('sort_by') === 'name_asc' ? 'name_desc' : 'name_asc'])) }}'">
                                        Name
                                        @if(request('sort_by') === 'name_asc' || (!request()->has('sort_by')))
                                            <svg class="inline-block w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                            </svg>
                                        @elseif(request('sort_by') === 'name_desc')
                                            <svg class="inline-block w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        @endif
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Email
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Role
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Invoices Created
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Total Revenue
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Created
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($users as $user)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-semibold">
                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                        {{ $user->name }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-gray-100">{{ $user->email }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($user->roles->count() > 0)
                                                @foreach($user->roles as $role)
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                        {{ $role->name }}
                                                    </span>
                                                @endforeach
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                                    No Role
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ number_format($user->total_invoices ?? 0) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ format_currency($user->total_revenue ?? 0) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $user->created_at->format('M j, Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end gap-2">
                                                <a href="#" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">View</a>
                                                <a href="#" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">Edit</a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center justify-center">
                                                <svg class="w-16 h-16 text-gray-400 dark:text-gray-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                </svg>
                                                <p class="text-gray-500 dark:text-gray-400 text-lg font-medium mb-2">No users found</p>
                                                <p class="text-gray-400 dark:text-gray-500 text-sm">There are no users in your company yet.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($users->hasPages())
                        <div class="mt-4">
                            {{ $users->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
