<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Quote') }} #{{ $quote->quote_number }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if (session('error'))
                        <x-alert type="error" dismissible>{{ session('error') }}</x-alert>
                    @endif
                    
                    @if ($errors->any())
                        <x-alert type="error" dismissible>
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </x-alert>
                    @endif
                    
                    <form method="POST" action="{{ route('quotes.update', $quote) }}" id="quote-form">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div>
                                <label class="block text-sm font-medium mb-2">Customer *</label>
                                <select name="client_id" id="client_id" required
                                        class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md">
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}" {{ old('client_id', $quote->client_id) == $client->id ? 'selected' : '' }}>
                                            {{ $client->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('client_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-2">Quote Date *</label>
                                <input type="date" name="quote_date" id="quote_date" value="{{ old('quote_date', $quote->quote_date->format('Y-m-d')) }}" required
                                       class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md">
                                @error('quote_date')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-2">Expiry Date</label>
                                <input type="date" name="expiry_date" id="expiry_date" value="{{ old('expiry_date', $quote->expiry_date?->format('Y-m-d')) }}"
                                       class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md">
                                @error('expiry_date')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-6">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-2 gap-2">
                                <label class="block text-sm font-medium">Quote Items *</label>
                                <button type="button" onclick="addItem()" class="bg-brand-gold hover:bg-brand-gold-light text-brand-black text-white text-sm font-bold py-2 px-4 rounded">
                                    + Add Item
                                </button>
                            </div>
                            <div class="overflow-x-auto">
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
                                    @foreach(old('items', $quote->items) as $index => $item)
                                        <tr class="item-row" data-index="{{ $index }}">
                                            <td class="px-4 py-2">
                                                <input type="text" name="items[{{ $index }}][description]" value="{{ old("items.$index.description", $item->description) }}" required
                                                       class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md item-description"
                                                       onchange="calculateRow({{ $index }})">
                                            </td>
                                            <td class="px-4 py-2">
                                                <input type="number" name="items[{{ $index }}][quantity]" value="{{ old("items.$index.quantity", $item->quantity) }}" step="0.01" min="0.01" required
                                                       class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md item-quantity"
                                                       onchange="calculateRow({{ $index }})">
                                            </td>
                                            <td class="px-4 py-2">
                                                <input type="number" name="items[{{ $index }}][unit_price]" value="{{ old("items.$index.unit_price", $item->unit_price) }}" step="0.01" min="0" required
                                                       class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md item-price"
                                                       onchange="calculateRow({{ $index }})">
                                            </td>
                                            <td class="px-4 py-2">
                                                <input type="number" name="items[{{ $index }}][discount]" value="{{ old("items.$index.discount", $item->discount ?? 0) }}" step="0.01" min="0"
                                                       class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md item-discount"
                                                       onchange="calculateRow({{ $index }})">
                                            </td>
                                            <td class="px-4 py-2">
                                                <input type="hidden" name="items[{{ $index }}][vat_applicable]" value="0">
                                                <input type="checkbox" name="items[{{ $index }}][vat_applicable]" value="1" {{ old("items.$index.vat_applicable", $item->vat_applicable) ? 'checked' : '' }}
                                                       class="item-vat h-5 w-5" onchange="calculateRow({{ $index }})">
                                            </td>
                                            <td class="px-4 py-2">
                                                <span class="item-total font-semibold">{{ format_currency($item->line_total) }}</span>
                                            </td>
                                            <td class="px-4 py-2">
                                                <button type="button" onclick="removeItem({{ $index }})" class="text-red-600 hover:text-red-800">Remove</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="border-t-2 border-gray-300 dark:border-gray-600">
                                        <td colspan="5" class="px-4 py-2 text-right font-semibold">Subtotal:</td>
                                        <td colspan="2" class="px-4 py-2 font-semibold" id="subtotal">{{ format_currency($quote->subtotal) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="px-4 py-2 text-right font-semibold">VAT (15%):</td>
                                        <td colspan="2" class="px-4 py-2 font-semibold" id="vat-total">{{ format_currency($quote->vat_total) }}</td>
                                    </tr>
                                    <tr class="border-t-2 border-gray-300 dark:border-gray-600">
                                        <td colspan="5" class="px-4 py-2 text-right font-semibold text-lg">Total:</td>
                                        <td colspan="2" class="px-4 py-2 font-semibold text-lg" id="total">{{ format_currency($quote->total) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                            </div>
                            @error('items')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div>
                                <label class="block text-sm font-medium mb-2">Notes</label>
                                <textarea name="notes" id="notes" rows="3"
                                          class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md">{{ old('notes', $quote->notes) }}</textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">Terms</label>
                                <textarea name="terms" id="terms" rows="3"
                                          class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md">{{ old('terms', $quote->terms) }}</textarea>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3">
                            <a href="{{ route('quotes.show', $quote) }}" class="text-center text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 py-2 px-4 rounded">Cancel</a>
                            <button type="submit" class="bg-brand-gold hover:bg-brand-gold-light text-brand-black text-white font-bold py-2 px-4 rounded">
                                Update Quote
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let itemIndex = {{ $quote->items->count() }};
        const VAT_RATE = 0.15;

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
                    <input type="text" name="items[${itemIndex}][description]" value="${description.replace(/"/g, '&quot;')}" required
                           class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md item-description"
                           onchange="calculateRow(${itemIndex})">
                </td>
                <td class="px-4 py-2">
                    <input type="number" name="items[${itemIndex}][quantity]" value="${quantity}" step="0.01" min="0.01" required
                           class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md item-quantity"
                           onchange="calculateRow(${itemIndex})">
                </td>
                <td class="px-4 py-2">
                    <input type="number" name="items[${itemIndex}][unit_price]" value="${unitPrice}" step="0.01" min="0" required
                           class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md item-price"
                           onchange="calculateRow(${itemIndex})">
                </td>
                <td class="px-4 py-2">
                    <input type="number" name="items[${itemIndex}][discount]" value="${discount}" step="0.01" min="0"
                           class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md item-discount"
                           onchange="calculateRow(${itemIndex})">
                </td>
                <td class="px-4 py-2">
                    <input type="hidden" name="items[${itemIndex}][vat_applicable]" value="0">
                    <input type="checkbox" name="items[${itemIndex}][vat_applicable]" value="1" ${vatApplicable ? 'checked' : ''}
                           class="item-vat h-5 w-5" onchange="calculateRow(${itemIndex})">
                </td>
                <td class="px-4 py-2">
                    <span class="item-total font-semibold">0.00</span>
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

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.item-row').forEach(row => {
                const index = parseInt(row.dataset.index);
                calculateRow(index);
            });
        });
    </script>
    @endpush
</x-app-layout>
