<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Invoice') }} #{{ $invoice->invoice_number }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form method="POST" action="{{ route('invoices.update', $invoice) }}" id="invoice-form">
                        @csrf
                        @method('PATCH')

                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div>
                                <label class="block text-sm font-medium mb-2">Customer *</label>
                                <select name="client_id" required
                                        class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md">
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}" {{ ($invoice->client_id == $client->id || old('client_id') == $client->id) ? 'selected' : '' }}>
                                            {{ $client->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('client_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-2">Due Date *</label>
                                <input type="date" name="due_date" value="{{ old('due_date', $invoice->due_date->format('Y-m-d')) }}" required
                                       class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md">
                                @error('due_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="mb-6">
                            <div class="flex justify-between items-center mb-2">
                                <label class="block text-sm font-medium">Invoice Items *</label>
                                <button type="button" onclick="addItem()" class="bg-brand-gold hover:bg-brand-gold-light text-brand-black text-white text-sm font-bold py-1 px-3 rounded">
                                    + Add Item
                                </button>
                            </div>
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700" id="items-table">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-2 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Description</th>
                                        <th class="px-4 py-2 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Quantity</th>
                                        <th class="px-4 py-2 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Unit Price</th>
                                        <th class="px-4 py-2 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Discount</th>
                                        <th class="px-4 py-2 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">VAT</th>
                                        <th class="px-4 py-2 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Total</th>
                                        <th class="px-4 py-2 bg-gray-50 dark:bg-gray-700 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="items-tbody">
                                    <!-- Items will be populated from existing invoice -->
                                </tbody>
                                <tfoot>
                                    <tr class="border-t-2 border-gray-300 dark:border-gray-600">
                                        <td colspan="4" class="px-4 py-2 text-right font-semibold">Subtotal:</td>
                                        <td colspan="2" class="px-4 py-2 font-semibold" id="subtotal">0.00</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="px-4 py-2 text-right font-semibold">VAT (15%):</td>
                                        <td colspan="2" class="px-4 py-2 font-semibold" id="vat-total">0.00</td>
                                    </tr>
                                    <tr class="border-t-2 border-gray-300 dark:border-gray-600">
                                        <td colspan="4" class="px-4 py-2 text-right font-semibold text-lg">Total:</td>
                                        <td colspan="2" class="px-4 py-2 font-semibold text-lg" id="total">0.00</td>
                                    </tr>
                                </tfoot>
                            </table>
                            @error('items')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div>
                                <label class="block text-sm font-medium mb-2">Notes</label>
                                <textarea name="notes" rows="3"
                                          class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md">{{ old('notes', $invoice->notes) }}</textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">Terms</label>
                                <textarea name="terms" rows="3"
                                          class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md">{{ old('terms', $invoice->terms) }}</textarea>
                            </div>
                        </div>

                        <div class="flex items-center justify-end">
                            <a href="{{ route('invoices.show', $invoice) }}" class="mr-4 text-gray-600 hover:text-gray-800">Cancel</a>
                            <button type="submit" class="bg-brand-gold hover:bg-brand-gold-light text-brand-black text-white font-bold py-2 px-4 rounded">
                                Update Invoice
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let itemIndex = 0;
        const VAT_RATE = 0.15;
        const existingItems = @json($invoice->items);

        function addItem(item = null) {
            const tbody = document.getElementById('items-tbody');
            const row = document.createElement('tr');
            row.className = 'item-row';
            row.dataset.index = itemIndex;
            
            const description = item?.description || '';
            const quantity = item?.quantity || 1;
            const unitPrice = item?.unit_price || 0;
            const discount = item?.discount || 0;
            const vatApplicable = item?.vat_applicable ?? true;
            
            row.innerHTML = `
                <td class="px-4 py-2">
                    <input type="text" name="items[${itemIndex}][description]" value="${description}" required
                           class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md item-description">
                </td>
                <td class="px-4 py-2">
                    <input type="number" name="items[${itemIndex}][quantity]" value="${quantity}" step="0.01" min="0.01" required
                           class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md item-quantity" onchange="calculateRow(${itemIndex})">
                </td>
                <td class="px-4 py-2">
                    <input type="number" name="items[${itemIndex}][unit_price]" value="${unitPrice}" step="0.01" min="0" required
                           class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md item-price" onchange="calculateRow(${itemIndex})">
                </td>
                <td class="px-4 py-2">
                    <input type="number" name="items[${itemIndex}][discount]" value="${discount}" step="0.01" min="0"
                           class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md item-discount" onchange="calculateRow(${itemIndex})">
                </td>
                <td class="px-4 py-2">
                    <input type="hidden" name="items[${itemIndex}][vat_applicable]" value="0">
                    <input type="checkbox" name="items[${itemIndex}][vat_applicable]" value="1" ${vatApplicable ? 'checked' : ''}
                           class="item-vat" onchange="calculateRow(${itemIndex})">
                </td>
                <td class="px-4 py-2">
                    <span class="item-total">0.00</span>
                </td>
                <td class="px-4 py-2">
                    <button type="button" onclick="removeItem(${itemIndex})" class="text-red-600 hover:text-red-800">Remove</button>
                </td>
            `;
            
            tbody.appendChild(row);
            calculateRow(itemIndex);
            itemIndex++;
            calculateTotals();
        }

        function removeItem(index) {
            const row = document.querySelector(`tr[data-index="${index}"]`);
            if (row) {
                row.remove();
                calculateTotals();
            }
        }

        function calculateRow(index) {
            const row = document.querySelector(`tr[data-index="${index}"]`);
            if (!row) return;

            const quantity = parseFloat(row.querySelector('.item-quantity').value) || 0;
            const unitPrice = parseFloat(row.querySelector('.item-price').value) || 0;
            const discount = parseFloat(row.querySelector('.item-discount').value) || 0;
            const vatApplicable = row.querySelector('.item-vat').checked;
            
            const lineSubtotal = Math.round(quantity * unitPrice * 100) / 100;
            const discountAmount = Math.round(discount * 100) / 100;
            const lineSubtotalAfterDiscount = Math.max(0, Math.round((lineSubtotal - discountAmount) * 100) / 100);
            const lineVat = vatApplicable ? Math.round(lineSubtotalAfterDiscount * VAT_RATE * 100) / 100 : 0;
            const lineTotal = Math.round((lineSubtotalAfterDiscount + lineVat) * 100) / 100;
            
            row.querySelector('.item-total').textContent = lineTotal.toFixed(2);
            calculateTotals();
        }

        function calculateTotals() {
            const rows = document.querySelectorAll('.item-row');
            let subtotal = 0;
            let vatTotal = 0;
            let total = 0;

            rows.forEach(row => {
                const quantity = parseFloat(row.querySelector('.item-quantity').value) || 0;
                const unitPrice = parseFloat(row.querySelector('.item-price').value) || 0;
                const discount = parseFloat(row.querySelector('.item-discount').value) || 0;
                const vatApplicable = row.querySelector('.item-vat').checked;
                
                const lineSubtotal = Math.round(quantity * unitPrice * 100) / 100;
                const discountAmount = Math.round(discount * 100) / 100;
                const lineSubtotalAfterDiscount = Math.max(0, Math.round((lineSubtotal - discountAmount) * 100) / 100);
                const lineVat = vatApplicable ? Math.round(lineSubtotalAfterDiscount * VAT_RATE * 100) / 100 : 0;
                const lineTotal = Math.round((lineSubtotalAfterDiscount + lineVat) * 100) / 100;
                
                subtotal += lineSubtotalAfterDiscount;
                vatTotal += lineVat;
                total += lineTotal;
            });

            document.getElementById('subtotal').textContent = subtotal.toFixed(2);
            document.getElementById('vat-total').textContent = vatTotal.toFixed(2);
            document.getElementById('total').textContent = total.toFixed(2);
        }

        // Load existing items on page load
        document.addEventListener('DOMContentLoaded', function() {
            if (existingItems.length > 0) {
                existingItems.forEach(item => {
                    addItem(item);
                });
            } else {
                addItem();
            }
        });

        // Validate form before submit
        document.getElementById('invoice-form').addEventListener('submit', function(e) {
            const rows = document.querySelectorAll('.item-row');
            if (rows.length === 0) {
                e.preventDefault();
                alert('Please add at least one invoice item.');
                return false;
            }
        });
    </script>
    @endpush
</x-app-layout>
