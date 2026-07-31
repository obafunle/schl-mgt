<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Invoice') }} #{{ $invoice->invoice_number }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.invoices.index') }}" 
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg text-sm">
                    ← Back
                </a>
                @if($invoice->status === 'draft')
                    <a href="{{ route('admin.invoices.edit', $invoice) }}" 
                       class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg text-sm">
                        Edit Invoice
                    </a>
                @endif
                @if($invoice->status !== 'paid' && $invoice->status !== 'cancelled')
                    <form action="{{ route('admin.invoices.send', $invoice) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg text-sm">
                            Send Invoice
                        </button>
                    </form>
                @endif
                @if($invoice->status !== 'paid' && $invoice->balance > 0)
                    <a href="{{ route('admin.invoices.pay', $invoice) }}" 
                       class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg text-sm">
                        💳 Pay Online
                    </a>
                @endif
                @if($invoice->status === 'draft')
                    <form action="{{ route('admin.invoices.destroy', $invoice) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Are you sure?')" 
                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg text-sm">
                            Delete
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('info'))
                <div class="mb-4 p-4 bg-blue-100 border-l-4 border-blue-500 text-blue-700">
                    {{ session('info') }}
                </div>
            @endif

            <!-- Invoice Status Banner -->
            <div class="mb-6 p-4 rounded-lg border 
                {{ $invoice->status === 'paid' ? 'bg-green-50 border-green-400' : '' }}
                {{ $invoice->status === 'partial' ? 'bg-yellow-50 border-yellow-400' : '' }}
                {{ $invoice->status === 'overdue' ? 'bg-red-50 border-red-400' : '' }}
                {{ $invoice->status === 'sent' ? 'bg-blue-50 border-blue-400' : '' }}
                {{ $invoice->status === 'draft' ? 'bg-gray-50 border-gray-400' : '' }}">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="font-semibold">Status:</span>
                        <span class="px-2 py-1 rounded-full text-sm font-medium
                            {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $invoice->status === 'partial' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $invoice->status === 'overdue' ? 'bg-red-100 text-red-800' : '' }}
                            {{ $invoice->status === 'sent' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $invoice->status === 'draft' ? 'bg-gray-100 text-gray-800' : '' }}
                            {{ $invoice->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                            {{ ucfirst($invoice->status) }}
                        </span>
                        @if($invoice->status === 'partial')
                            <span class="ml-2 text-sm text-yellow-700">Balance: ₦{{ number_format($invoice->balance, 2) }}</span>
                        @endif
                        @if($invoice->status === 'overdue')
                            <span class="ml-2 text-sm text-red-700">Overdue by {{ now()->diffInDays($invoice->due_date) }} days</span>
                        @endif
                    </div>
                    <div class="text-sm text-gray-500">
                        @if($invoice->paid_at)
                            Paid on: {{ $invoice->paid_at->format('M d, Y h:i A') }}
                        @endif
                    </div>
                </div>
            </div>

            <!-- Invoice Details -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    
                    <!-- Header -->
                    <div class="flex justify-between items-start mb-6 pb-6 border-b border-gray-200">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">INVOICE</h3>
                            <p class="text-sm text-gray-500">#{{ $invoice->invoice_number }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold">{{ config('app.name') }}</p>
                            <p class="text-sm text-gray-500">{{ config('app.school_address', '') }}</p>
                            <p class="text-sm text-gray-500">{{ config('app.school_phone', '') }}</p>
                            <p class="text-sm text-gray-500">{{ config('app.school_email', '') }}</p>
                        </div>
                    </div>

                    <!-- Student Info -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <h4 class="text-sm font-semibold text-gray-500 uppercase mb-2">Bill To:</h4>
                            <p class="font-semibold">{{ $invoice->student->full_name }}</p>
                            <p class="text-sm text-gray-600">Admission No: {{ $invoice->student->admission_number }}</p>
                            <p class="text-sm text-gray-600">Class: {{ $invoice->class->name }} @if($invoice->classArm)({{ $invoice->classArm->name }})@endif</p>
                            <p class="text-sm text-gray-600">Parent: {{ $invoice->student->parent_name }}</p>
                            <p class="text-sm text-gray-600">Phone: {{ $invoice->student->parent_phone }}</p>
                            @if($invoice->student->parent_email)
                                <p class="text-sm text-gray-600">Email: {{ $invoice->student->parent_email }}</p>
                            @endif
                        </div>
                        <div>
                            <h4 class="text-sm font-semibold text-gray-500 uppercase mb-2">Invoice Details:</h4>
                            <table class="w-full text-sm">
                                <tr>
                                    <td class="py-1 text-gray-500">Invoice Date:</td>
                                    <td class="py-1 font-medium">{{ $invoice->issue_date->format('M d, Y') }}</td>
                                </tr>
                                <tr>
                                    <td class="py-1 text-gray-500">Due Date:</td>
                                    <td class="py-1 font-medium">{{ $invoice->due_date->format('M d, Y') }}</td>
                                </tr>
                                <tr>
                                    <td class="py-1 text-gray-500">Term:</td>
                                    <td class="py-1 font-medium">{{ $invoice->term->name }}</td>
                                </tr>
                                <tr>
                                    <td class="py-1 text-gray-500">Academic Year:</td>
                                    <td class="py-1 font-medium">{{ $invoice->academicYear->name }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <div class="overflow-x-auto mb-6">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($invoice->items as $index => $item)
                                    <tr>
                                        <td class="px-4 py-3 text-sm text-gray-500">{{ $index + 1 }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ $item['name'] }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900 text-right">₦{{ number_format($item['amount'], 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="2" class="px-4 py-3 text-sm font-semibold text-right">Subtotal:</td>
                                    <td class="px-4 py-3 text-sm font-semibold text-right">₦{{ number_format($invoice->subtotal, 2) }}</td>
                                </tr>
                                @if($invoice->discount > 0)
                                    <tr>
                                        <td colspan="2" class="px-4 py-3 text-sm text-right text-red-600">Discount:</td>
                                        <td class="px-4 py-3 text-sm text-right text-red-600">-₦{{ number_format($invoice->discount, 2) }}</td>
                                    </tr>
                                @endif
                                <tr class="border-t-2 border-gray-300">
                                    <td colspan="2" class="px-4 py-3 text-lg font-bold text-right">Total:</td>
                                    <td class="px-4 py-3 text-lg font-bold text-right text-blue-600">₦{{ number_format($invoice->total, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="px-4 py-3 text-sm text-right text-green-600">Amount Paid:</td>
                                    <td class="px-4 py-3 text-sm text-right text-green-600">₦{{ number_format($invoice->amount_paid, 2) }}</td>
                                </tr>
                                <tr class="border-t border-gray-200">
                                    <td colspan="2" class="px-4 py-3 text-base font-bold text-right">Balance:</td>
                                    <td class="px-4 py-3 text-base font-bold text-right {{ $invoice->balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                                        ₦{{ number_format($invoice->balance, 2) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Payment History -->
                    @if($invoice->payments->count() > 0)
                        <div class="mt-6">
                            <h4 class="text-sm font-semibold text-gray-500 uppercase mb-3">Payment History</h4>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Method</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($invoice->payments as $payment)
                                            <tr>
                                                <td class="px-4 py-2 text-sm text-gray-500">{{ $payment->payment_date->format('M d, Y h:i A') }}</td>
                                                <td class="px-4 py-2 text-sm font-semibold text-gray-900">₦{{ number_format($payment->amount, 2) }}</td>
                                                <td class="px-4 py-2 text-sm text-gray-500">{{ ucfirst($payment->payment_method) }}</td>
                                                <td class="px-4 py-2 text-sm text-gray-500">{{ $payment->reference }}</td>
                                                <td class="px-4 py-2">
                                                    <span class="px-2 py-1 text-xs rounded-full 
                                                        {{ $payment->status === 'success' ? 'bg-green-100 text-green-800' : '' }}
                                                        {{ $payment->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                        {{ $payment->status === 'failed' ? 'bg-red-100 text-red-800' : '' }}">
                                                        {{ ucfirst($payment->status) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <!-- Manual Payment Form -->
                    @if($invoice->status !== 'paid' && $invoice->balance > 0)
                        <div class="mt-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <h4 class="text-sm font-semibold text-gray-700 mb-3">Record Manual Payment</h4>
                            <form action="{{ route('admin.invoices.process-payment', $invoice) }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                @csrf
                                <div>
                                    <label for="amount" class="block text-xs text-gray-600 mb-1">Amount (Max: ₦{{ number_format($invoice->balance, 2) }})</label>
                                    <input type="number" name="amount" id="amount" 
                                           step="0.01" min="1" max="{{ $invoice->balance }}"
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 text-sm"
                                           placeholder="Enter amount" required>
                                </div>
                                <div>
                                    <label for="payment_method" class="block text-xs text-gray-600 mb-1">Payment Method</label>
                                    <select name="payment_method" id="payment_method" 
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 text-sm" required>
                                        <option value="">Select</option>
                                        <option value="cash">Cash</option>
                                        <option value="bank_transfer">Bank Transfer</option>
                                        <option value="cheque">Cheque</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="reference" class="block text-xs text-gray-600 mb-1">Reference (Optional)</label>
                                    <input type="text" name="reference" id="reference" 
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 text-sm"
                                           placeholder="e.g., TRANS-123">
                                </div>
                                <div class="flex items-end">
                                    <button type="submit" 
                                            class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg text-sm">
                                        Record Payment
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif

                    <!-- Notes -->
                    <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <p class="text-sm text-blue-700">
                            <strong>💡 Note:</strong> 
                            @if($invoice->status === 'draft')
                                This invoice is still in draft mode. Send it to the parent to make it active.
                            @elseif($invoice->status === 'sent')
                                This invoice has been sent to the parent and is awaiting payment.
                            @elseif($invoice->status === 'partial')
                                This invoice has been partially paid. The balance is ₦{{ number_format($invoice->balance, 2) }}.
                            @elseif($invoice->status === 'paid')
                                This invoice has been fully paid. Thank you!
                            @elseif($invoice->status === 'overdue')
                                This invoice is overdue. Please follow up with the parent.
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>