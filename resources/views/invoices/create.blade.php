<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Create Invoice') }}
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
                    
                    <form method="POST" action="{{ route('invoices.store') }}" id="invoice-form">
                        @csrf

                        <div id="auto-save-indicator" class="mb-4 hidden">
                            <x-alert type="info" dismissible>
                                <span id="auto-save-message">Draft saved automatically</span>
                            </x-alert>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div>
                                <label class="block text-sm font-medium mb-2">Customer *</label>
                                <select name="client_id" id="client_id" required
                                        class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        data-validate="required">
                                    <option value="">Select a customer</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                            {{ $client->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('client_id')
                                    <p class="text-red-500 text-xs mt-1 error-message">{{ $message }}</p>
                                @enderror
                                <p class="text-red-500 text-xs mt-1 error-message hidden" id="client_id_error"></p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-2">Due Date *</label>
                                <input type="date" name="due_date" id="due_date" value="{{ old('due_date', now()->addDays(30)->format('Y-m-d')) }}" required
                                       class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       data-validate="required|date|after_or_equal:today">
                                @error('due_date')
                                    <p class="text-red-500 text-xs mt-1 error-message">{{ $message }}</p>
                                @enderror
                                <p class="text-red-500 text-xs mt-1 error-message hidden" id="due_date_error"></p>
                            </div>
                        </div>

                        <div class="mb-6">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-2 gap-2">
                                <label class="block text-sm font-medium">Invoice Items *</label>
                                <button type="button" onclick="addItem()" class="bg-brand-gold hover:bg-brand-gold-light text-brand-black text-white text-sm font-bold py-2 px-4 rounded touch-manipulation">
                                    + Add Item
                                </button>
                            </div>
                            <div class="overflow-x-auto -mx-6 sm:mx-0">
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
                                    <!-- Items will be added here dynamically -->
                                </tbody>
                                <tfoot>
                                    <tr class="border-t-2 border-gray-300 dark:border-gray-600">
                                        <td colspan="5" class="px-4 py-2 text-right font-semibold">Subtotal:</td>
                                        <td colspan="2" class="px-4 py-2 font-semibold" id="subtotal">0.00</td>
                                    </tr>
                                    <tr>
                                        <td colspan="5" class="px-4 py-2 text-right font-semibold">VAT (15%):</td>
                                        <td colspan="2" class="px-4 py-2 font-semibold" id="vat-total">0.00</td>
                                    </tr>
                                    <tr class="border-t-2 border-gray-300 dark:border-gray-600">
                                        <td colspan="5" class="px-4 py-2 text-right font-semibold text-lg">Total:</td>
                                        <td colspan="2" class="px-4 py-2 font-semibold text-lg" id="total">0.00</td>
                                    </tr>
                                </tfoot>
                            </table>
                            </div>
                            @error('items')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            <p class="text-red-500 text-xs mt-1 error-message hidden" id="items_error"></p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div>
                                <label class="block text-sm font-medium mb-2">Notes</label>
                                <textarea name="notes" id="notes" rows="3"
                                          class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md">{{ old('notes') }}</textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-2">Terms</label>
                                <textarea name="terms" id="terms" rows="3"
                                          class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md">{{ old('terms') }}</textarea>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3">
                            <a href="{{ route('invoices.index') }}" class="text-center text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 py-2 px-4 rounded">Cancel</a>
                            <button type="submit" id="submit-btn" class="bg-brand-gold hover:bg-brand-gold-light text-brand-black text-white font-bold py-2 px-4 rounded touch-manipulation disabled:opacity-50 disabled:cursor-not-allowed">
                                <span id="submit-text">Create Invoice</span>
                                <span id="submit-loading" class="hidden">Creating...</span>
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
        const oldItems = @json(old('items', []));
        const STORAGE_KEY = 'invoice_draft_' + Date.now();
        let autoSaveTimer = null;
        let formChanged = false;

        // Form persistence - Auto-save to localStorage
        function saveFormDraft() {
            if (!formChanged) return;
            
            const formData = {
                client_id: document.getElementById('client_id')?.value || '',
                due_date: document.getElementById('due_date')?.value || '',
                notes: document.getElementById('notes')?.value || '',
                terms: document.getElementById('terms')?.value || '',
                items: getFormItems()
            };
            
            try {
                localStorage.setItem(STORAGE_KEY, JSON.stringify(formData));
                showAutoSaveIndicator('Draft saved automatically');
                formChanged = false;
            } catch (e) {
                console.error('Failed to save draft:', e);
            }
        }

        function getFormItems() {
            const items = [];
            document.querySelectorAll('.item-row').forEach(row => {
                const description = row.querySelector('.item-description')?.value || '';
                const quantity = row.querySelector('.item-quantity')?.value || '';
                const unitPrice = row.querySelector('.item-price')?.value || '';
                const discount = row.querySelector('.item-discount')?.value || 0;
                const vatApplicable = row.querySelector('.item-vat')?.checked || false;
                
                if (description || quantity || unitPrice) {
                    items.push({
                        description: description,
                        quantity: quantity,
                        unit_price: unitPrice,
                        discount: discount,
                        vat_applicable: vatApplicable
                    });
                }
            });
            return items;
        }

        function restoreFormDraft() {
            try {
                const saved = localStorage.getItem(STORAGE_KEY);
                if (!saved) return false;
                
                const formData = JSON.parse(saved);
                
                if (formData.client_id) {
                    document.getElementById('client_id').value = formData.client_id;
                }
                if (formData.due_date) {
                    document.getElementById('due_date').value = formData.due_date;
                }
                if (formData.notes) {
                    document.getElementById('notes').value = formData.notes;
                }
                if (formData.terms) {
                    document.getElementById('terms').value = formData.terms;
                }
                
                if (formData.items && formData.items.length > 0) {
                    // Clear existing items
                    document.getElementById('items-tbody').innerHTML = '';
                    itemIndex = 0;
                    
                    formData.items.forEach(item => {
                        addItem(item);
                    });
                    
                    showAutoSaveIndicator('Draft restored from previous session', 'info');
                    return true;
                }
            } catch (e) {
                console.error('Failed to restore draft:', e);
            }
            return false;
        }

        function clearFormDraft() {
            try {
                localStorage.removeItem(STORAGE_KEY);
            } catch (e) {
                console.error('Failed to clear draft:', e);
            }
        }

        function showAutoSaveIndicator(message, type = 'success') {
            const indicator = document.getElementById('auto-save-indicator');
            const messageEl = document.getElementById('auto-save-message');
            if (indicator && messageEl) {
                messageEl.textContent = message;
                indicator.classList.remove('hidden');
                setTimeout(() => {
                    indicator.classList.add('hidden');
                }, 3000);
            }
        }

        // Auto-save on form changes
        function setupAutoSave() {
            const form = document.getElementById('invoice-form');
            const inputs = form.querySelectorAll('input, select, textarea');
            
            inputs.forEach(input => {
                input.addEventListener('input', () => {
                    formChanged = true;
                    clearTimeout(autoSaveTimer);
                    autoSaveTimer = setTimeout(saveFormDraft, 2000); // Save after 2 seconds of inactivity
                });
                
                input.addEventListener('change', () => {
                    formChanged = true;
                    clearTimeout(autoSaveTimer);
                    autoSaveTimer = setTimeout(saveFormDraft, 1000);
                });
            });
        }

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
                <td class="px-2 sm:px-4 py-2">
                    <input type="text" name="items[${itemIndex}][description]" value="${description.replace(/"/g, '&quot;')}" required
                           class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md item-description focus:ring-2 focus:ring-blue-500"
                           data-validate="required" oninput="validateItem(${itemIndex}); formChanged = true; clearTimeout(autoSaveTimer); autoSaveTimer = setTimeout(saveFormDraft, 2000);">
                    <p class="text-red-500 text-xs mt-1 error-message hidden" id="item_${itemIndex}_description_error"></p>
                </td>
                <td class="px-2 sm:px-4 py-2">
                    <input type="number" name="items[${itemIndex}][quantity]" value="${quantity}" step="0.01" min="0.01" max="999999.99" required
                           class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md item-quantity focus:ring-2 focus:ring-blue-500"
                           data-validate="required|numeric|min:0.01" onchange="calculateRow(${itemIndex}); validateItem(${itemIndex}); formChanged = true; clearTimeout(autoSaveTimer); autoSaveTimer = setTimeout(saveFormDraft, 2000);">
                    <p class="text-red-500 text-xs mt-1 error-message hidden" id="item_${itemIndex}_quantity_error"></p>
                </td>
                <td class="px-2 sm:px-4 py-2">
                    <input type="number" name="items[${itemIndex}][unit_price]" value="${unitPrice}" step="0.01" min="0" max="999999.99" required
                           class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md item-price focus:ring-2 focus:ring-blue-500"
                           data-validate="required|numeric|min:0" onchange="calculateRow(${itemIndex}); validateItem(${itemIndex}); formChanged = true; clearTimeout(autoSaveTimer); autoSaveTimer = setTimeout(saveFormDraft, 2000);">
                    <p class="text-red-500 text-xs mt-1 error-message hidden" id="item_${itemIndex}_unit_price_error"></p>
                </td>
                <td class="px-2 sm:px-4 py-2">
                    <input type="number" name="items[${itemIndex}][discount]" value="${discount}" step="0.01" min="0" max="999999.99"
                           class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md item-discount focus:ring-2 focus:ring-blue-500"
                           data-validate="numeric|min:0" onchange="calculateRow(${itemIndex}); formChanged = true; clearTimeout(autoSaveTimer); autoSaveTimer = setTimeout(saveFormDraft, 2000);">
                    <p class="text-red-500 text-xs mt-1 error-message hidden" id="item_${itemIndex}_discount_error"></p>
                </td>
                <td class="px-2 sm:px-4 py-2">
                    <input type="hidden" name="items[${itemIndex}][vat_applicable]" value="0">
                    <input type="checkbox" name="items[${itemIndex}][vat_applicable]" value="1" ${vatApplicable ? 'checked' : ''}
                           class="item-vat h-5 w-5 touch-manipulation" onchange="calculateRow(${itemIndex}); formChanged = true; clearTimeout(autoSaveTimer); autoSaveTimer = setTimeout(saveFormDraft, 2000);">
                </td>
                <td class="px-2 sm:px-4 py-2">
                    <span class="item-total font-semibold">0.00</span>
                </td>
                <td class="px-2 sm:px-4 py-2">
                    <button type="button" onclick="removeItem(${itemIndex})" class="text-red-600 hover:text-red-800 touch-manipulation text-sm sm:text-base">Remove</button>
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
                formChanged = true;
                clearTimeout(autoSaveTimer);
                autoSaveTimer = setTimeout(saveFormDraft, 1000);
            }
        }

        // Real-time validation
        function validateItem(index) {
            const row = document.querySelector(`tr[data-index="${index}"]`);
            if (!row) return true;
            
            let isValid = true;
            const description = row.querySelector('.item-description')?.value.trim() || '';
            const quantity = parseFloat(row.querySelector('.item-quantity')?.value) || 0;
            const unitPrice = parseFloat(row.querySelector('.item-price')?.value) || 0;
            
            // Validate description
            const descError = document.getElementById(`item_${index}_description_error`);
            if (!description) {
                if (descError) {
                    descError.textContent = 'Description is required';
                    descError.classList.remove('hidden');
                }
                isValid = false;
            } else {
                if (descError) descError.classList.add('hidden');
            }
            
            // Validate quantity
            const qtyError = document.getElementById(`item_${index}_quantity_error`);
            if (!quantity || quantity < 0.01) {
                if (qtyError) {
                    qtyError.textContent = 'Quantity must be at least 0.01';
                    qtyError.classList.remove('hidden');
                }
                isValid = false;
            } else if (quantity > 999999.99) {
                if (qtyError) {
                    qtyError.textContent = 'Quantity cannot exceed 999,999.99';
                    qtyError.classList.remove('hidden');
                }
                isValid = false;
            } else {
                if (qtyError) qtyError.classList.add('hidden');
            }
            
            // Validate unit price
            const priceError = document.getElementById(`item_${index}_unit_price_error`);
            if (unitPrice < 0) {
                if (priceError) {
                    priceError.textContent = 'Unit price cannot be negative';
                    priceError.classList.remove('hidden');
                }
                isValid = false;
            } else if (unitPrice > 999999.99) {
                if (priceError) {
                    priceError.textContent = 'Unit price cannot exceed 999,999.99';
                    priceError.classList.remove('hidden');
                }
                isValid = false;
            } else {
                if (priceError) priceError.classList.add('hidden');
            }
            
            return isValid;
        }

        function validateForm() {
            let isValid = true;
            
            // Validate client
            const clientId = document.getElementById('client_id')?.value || '';
            const clientError = document.getElementById('client_id_error');
            if (!clientId) {
                if (clientError) {
                    clientError.textContent = 'Please select a customer';
                    clientError.classList.remove('hidden');
                }
                isValid = false;
            } else {
                if (clientError) clientError.classList.add('hidden');
            }
            
            // Validate due date
            const dueDate = document.getElementById('due_date')?.value || '';
            const dateError = document.getElementById('due_date_error');
            if (!dueDate) {
                if (dateError) {
                    dateError.textContent = 'Please select a due date';
                    dateError.classList.remove('hidden');
                }
                isValid = false;
            } else {
                const selectedDate = new Date(dueDate);
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                if (selectedDate < today) {
                    if (dateError) {
                        dateError.textContent = 'Due date must be today or a future date';
                        dateError.classList.remove('hidden');
                    }
                    isValid = false;
                } else {
                    if (dateError) dateError.classList.add('hidden');
                }
            }
            
            // Validate items
            const rows = document.querySelectorAll('.item-row');
            const itemsError = document.getElementById('items_error');
            if (rows.length === 0) {
                if (itemsError) {
                    itemsError.textContent = 'Please add at least one invoice item';
                    itemsError.classList.remove('hidden');
                }
                isValid = false;
            } else {
                let hasValidItem = false;
                rows.forEach(row => {
                    const index = row.dataset.index;
                    if (validateItem(index)) {
                        hasValidItem = true;
                    }
                });
                
                if (!hasValidItem) {
                    if (itemsError) {
                        itemsError.textContent = 'Please add at least one valid invoice item';
                        itemsError.classList.remove('hidden');
                    }
                    isValid = false;
                } else {
                    if (itemsError) itemsError.classList.add('hidden');
                }
            }
            
            return isValid;
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

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Try to restore draft first
            const draftRestored = restoreFormDraft();
            
            if (!draftRestored) {
                // Restore old items from server validation or add first item
                if (oldItems && oldItems.length > 0) {
                    oldItems.forEach(item => {
                        addItem(item);
                    });
                } else {
                    addItem();
                }
            }
            
            // Setup auto-save
            setupAutoSave();
            
            // Save draft before page unload
            window.addEventListener('beforeunload', function(e) {
                if (formChanged) {
                    saveFormDraft();
                }
            });
        });

        // Validate form before submit
        document.getElementById('invoice-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!validateForm()) {
                // Scroll to first error
                const firstError = document.querySelector('.error-message:not(.hidden)');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return false;
            }
            
            // Disable submit button and show loading
            const submitBtn = document.getElementById('submit-btn');
            const submitText = document.getElementById('submit-text');
            const submitLoading = document.getElementById('submit-loading');
            
            if (submitBtn) {
                submitBtn.disabled = true;
                if (submitText) submitText.classList.add('hidden');
                if (submitLoading) submitLoading.classList.remove('hidden');
            }
            
            // Clear draft on successful submit
            clearFormDraft();
            
            // Submit form
            this.submit();
        });
    </script>
    @endpush
</x-app-layout>
