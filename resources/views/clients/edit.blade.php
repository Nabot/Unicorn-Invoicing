<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Customer') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('clients.update', $client) }}">
                        @csrf
                        @method('PATCH')

                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-2">Name *</label>
                            <input type="text" name="name" value="{{ old('name', $client->name) }}" required
                                   class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md">
                            @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-2">Email</label>
                            <input type="email" name="email" value="{{ old('email', $client->email) }}"
                                   class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md">
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-2">Phone</label>
                            <input type="text" name="phone" value="{{ old('phone', $client->phone) }}"
                                   class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md">
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-2">Address</label>
                            <textarea name="address" rows="3"
                                      class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md">{{ old('address', $client->address) }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-2">VAT Number</label>
                            <input type="text" name="vat_number" value="{{ old('vat_number', $client->vat_number) }}"
                                   class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md">
                        </div>

                        <div class="flex items-center justify-end">
                            <a href="{{ route('clients.show', $client) }}" class="mr-4 text-gray-600 hover:text-gray-800">Cancel</a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Update Customer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
