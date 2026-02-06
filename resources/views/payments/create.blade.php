<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Record Payment') }} - Invoice #{{ $invoice->invoice_number }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-4">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">Invoice Details</h3>
                    <dl class="grid grid-cols-2 gap-4">
                        <dt class="font-medium">Invoice Number:</dt>
                        <dd>{{ $invoice->invoice_number }}</dd>
                        <dt class="font-medium">Client:</dt>
                        <dd>{{ $invoice->client->name }}</dd>
                        <dt class="font-medium">Total Amount:</dt>
                        <dd>{{ number_format($invoice->total, 2) }}</dd>
                        <dt class="font-medium">Amount Paid:</dt>
                        <dd>{{ number_format($invoice->amount_paid, 2) }}</dd>
                        <dt class="font-medium">Balance Due:</dt>
                        <dd class="font-semibold text-lg">{{ number_format($invoice->balance_due, 2) }}</dd>
                    </dl>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('payments.store', $invoice) }}">
                        @csrf

                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-2">Amount *</label>
                            <input type="number" name="amount" value="{{ old('amount') }}" step="0.01" min="0.01" max="{{ $invoice->balance_due }}" required
                                   class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md"
                                   placeholder="Maximum: {{ number_format($invoice->balance_due, 2) }}">
                            @error('amount')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-2">Payment Date *</label>
                            <input type="date" name="payment_date" value="{{ old('payment_date', now()->format('Y-m-d')) }}" required
                                   class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md">
                            @error('payment_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-2">Payment Method *</label>
                            <select name="method" required
                                    class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md">
                                @foreach(\App\Enums\PaymentMethod::cases() as $method)
                                    <option value="{{ $method->value }}" {{ old('method') === $method->value ? 'selected' : '' }}>
                                        {{ $method->label() }}
                                    </option>
                                @endforeach
                            </select>
                            @error('method')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium mb-2">Reference</label>
                            <input type="text" name="reference" value="{{ old('reference') }}"
                                   class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md"
                                   placeholder="Payment reference number">
                            @error('reference')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="flex items-center justify-end">
                            <a href="{{ route('invoices.show', $invoice) }}" class="mr-4 text-gray-600 hover:text-gray-800">Cancel</a>
                            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                Record Payment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
