<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Invoice') }} #{{ $invoice->invoice_number }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('admin.invoices.update', $invoice) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Student & Basic Info -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <!-- Student -->
                            <div>
                                <label for="student_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    Student <span class="text-red-500">*</span>
                                </label>
                                <select id="student_id" name="student_id" required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                    @foreach($students as $student)
                                        <option value="{{ $student->id }}" 
                                            {{ $invoice->student_id == $student->id ? 'selected' : '' }}>
                                            {{ $student->full_name }} ({{ $student->admission_number }})
                                            @if($student->class)
                                                - {{ $student->class->name }}
                                                @if($student->classArm)
                                                    {{ $student->classArm->name }}
                                                @endif
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Term -->
                            <div>
                                <label for="term_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    Term <span class="text-red-500">*</span>
                                </label>
                                <select id="term_id" name="term_id" required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                    @foreach($terms as $term)
                                        <option value="{{ $term->id }}" 
                                            {{ $invoice->term_id == $term->id ? 'selected' : '' }}>
                                            {{ $term->name }} - {{ $term->academicYear->name ?? 'N/A' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Academic Year -->
                            <div>
                                <label for="academic_year_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    Academic Year <span class="text-red-500">*</span>
                                </label>
                                <select id="academic_year_id" name="academic_year_id" required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                    @foreach($academicYears as $year)
                                        <option value="{{ $year->id }}" 
                                            {{ $invoice->academic_year_id == $year->id ? 'selected' : '' }}>
                                            {{ $year->name }}
                                            @if($year->is_current)
                                                (Current)
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Due Date -->
                            <div>
                                <label for="due_date" class="block text-sm font-medium text-gray-700 mb-1">
                                    Due Date <span class="text-red-500">*</span>
                                </label>
                                <input type="date" id="due_date" name="due_date" 
                                       value="{{ $invoice->due_date->format('Y-m-d') }}" required
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                            </div>
                        </div>

                        <!-- Invoice Items -->
                        <div class="mb-6">
                            <div class="flex justify-between items-center mb-3">
                                <h3 class="text-lg font-semibold text-gray-700">Invoice Items</h3>
                                <button type="button" onclick="addItem()" 
                                        class="bg-green-500 hover:bg-green-700 text-white text-sm font-bold py-1 px-3 rounded">
                                    + Add Item
                                </button>
                            </div>
                            
                            <div id="items-container" class="space-y-3">
                                @foreach($invoice->items as $index => $item)
                                    <div class="item-row flex items-center space-x-3 p-3 bg-gray-50 rounded-lg border border-gray-200" id="item-{{ $index }}">
                                        <div class="flex-1">
                                            <input type="text" name="items[{{ $index }}][name]" 
                                                   value="{{ $item['name'] }}"
                                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300"
                                                   required>
                                        </div>
                                        <div class="w-40">
                                            <input type="number" name="items[{{ $index }}][amount]" 
                                                   value="{{ $item['amount'] }}"
                                                   step="0.01" min="0"
                                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 item-amount"
                                                   required
                                                   onchange="calculateTotals()"
                                                   oninput="calculateTotals()">
                                        </div>
                                        <button type="button" onclick="removeItem({{ $index }})" 
                                                class="text-red-500 hover:text-red-700">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Discount -->
                            <div class="mt-4 flex items-center justify-end space-x-4">
                                <div class="flex items-center space-x-2">
                                    <label for="discount" class="text-sm font-medium text-gray-700">Discount:</label>
                                    <input type="number" id="discount" name="discount" 
                                           value="{{ $invoice->discount }}" step="0.01" min="0"
                                           class="w-32 rounded-md border-gray-300 shadow-sm focus:border-indigo-300"
                                           onchange="calculateTotals()">
                                </div>
                            </div>

                            <!-- Totals -->
                            <div class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Subtotal:</span>
                                    <span id="subtotal-display" class="font-semibold">₦{{ number_format($invoice->subtotal, 2) }}</span>
                                </div>
                                <div class="flex justify-between text-sm mt-1">
                                    <span class="text-gray-600">Discount:</span>
                                    <span id="discount-display" class="font-semibold text-red-600">₦{{ number_format($invoice->discount, 2) }}</span>
                                </div>
                                <div class="flex justify-between text-lg font-bold mt-2 pt-2 border-t border-gray-200">
                                    <span>Total:</span>
                                    <span id="total-display" class="text-blue-600">₦{{ number_format($invoice->total, 2) }}</span>
                                </div>
                                <div class="flex justify-between text-sm mt-2 pt-2 border-t border-gray-200">
                                    <span class="text-gray-600">Amount Paid:</span>
                                    <span class="font-semibold text-green-600">₦{{ number_format($invoice->amount_paid, 2) }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Balance:</span>
                                    <span class="font-semibold {{ $invoice->balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                                        ₦{{ number_format($invoice->balance, 2) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Submit -->
                        <div class="flex justify-end space-x-3 mt-6">
                            <a href="{{ route('admin.invoices.index') }}" 
                               class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
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
        let itemCount = {{ count($invoice->items) }};

        function addItem() {
            itemCount++;
            const container = document.getElementById('items-container');
            
            const div = document.createElement('div');
            div.className = 'item-row flex items-center space-x-3 p-3 bg-gray-50 rounded-lg border border-gray-200';
            div.id = 'item-' + itemCount;
            
            div.innerHTML = `
                <div class="flex-1">
                    <input type="text" name="items[${itemCount}][name]" 
                           placeholder="Item name (e.g., Tuition Fee)" 
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300"
                           required>
                </div>
                <div class="w-40">
                    <input type="number" name="items[${itemCount}][amount]" 
                           placeholder="Amount" 
                           step="0.01" min="0"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 item-amount"
                           required
                           onchange="calculateTotals()"
                           oninput="calculateTotals()">
                </div>
                <button type="button" onclick="removeItem(${itemCount})" 
                        class="text-red-500 hover:text-red-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            `;
            
            container.appendChild(div);
            calculateTotals();
        }

        function removeItem(id) {
            const item = document.getElementById('item-' + id);
            if (item) {
                item.remove();
                calculateTotals();
            }
        }

        function calculateTotals() {
            let subtotal = 0;
            const amounts = document.querySelectorAll('.item-amount');
            amounts.forEach(input => {
                const val = parseFloat(input.value) || 0;
                subtotal += val;
            });

            const discount = parseFloat(document.getElementById('discount').value) || 0;
            const total = subtotal - discount;

            document.getElementById('subtotal-display').textContent = '₦' + subtotal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            document.getElementById('discount-display').textContent = '₦' + discount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            document.getElementById('total-display').textContent = '₦' + total.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        }
    </script>
    @endpush
</x-app-layout>