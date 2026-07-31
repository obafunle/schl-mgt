<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Invoices') }}
            </h2>
            @can('create_fees')
                <div class="flex space-x-2">
                    <a href="{{ route('admin.invoices.create') }}"
                       class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-md hover:shadow-lg transition duration-200">
                        + New Invoice
                    </a>
                    <a href="{{ route('admin.invoices.bulk-generate') }}"
                       class="bg-emerald-500 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded-lg shadow-md hover:shadow-lg transition duration-200">
                        📋 Bulk Generate
                    </a>
                </div>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <!-- Stats -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-200">
                            <span class="text-sm text-gray-500">Total Invoices</span>
                            <p class="text-2xl font-bold text-indigo-600">{{ $invoices->count() }}</p>
                        </div>
                        <div class="bg-emerald-50 p-4 rounded-lg border border-emerald-200">
                            <span class="text-sm text-gray-500">Paid</span>
                            <p class="text-2xl font-bold text-emerald-600">{{ $invoices->where('status', 'paid')->count() }}</p>
                        </div>
                        <div class="bg-amber-50 p-4 rounded-lg border border-amber-200">
                            <span class="text-sm text-gray-500">Partial</span>
                            <p class="text-2xl font-bold text-amber-600">{{ $invoices->where('status', 'partial')->count() }}</p>
                        </div>
                        <div class="bg-rose-50 p-4 rounded-lg border border-rose-200">
                            <span class="text-sm text-gray-500">Overdue</span>
                            <p class="text-2xl font-bold text-rose-600">{{ $invoices->where('status', 'overdue')->count() }}</p>
                        </div>
                    </div>

                    <!-- Filters -->
                    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <div>
                            <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                <option value="">All Status</option>
                                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partial</option>
                                <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                            </select>
                        </div>
                        <div>
                            <select name="class_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                <option value="">All Classes</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <select name="term_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                <option value="">All Terms</option>
                                @foreach($terms as $term)
                                    <option value="{{ $term->id }}" {{ request('term_id') == $term->id ? 'selected' : '' }}>
                                        {{ $term->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <button type="submit" class="w-full bg-gray-800 text-white py-2 px-4 rounded-md hover:bg-gray-700 transition">
                                🔍 Filter
                            </button>
                        </div>
                    </form>

                    <!-- Invoices Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice #</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($invoices as $invoice)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $invoice->invoice_number }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $invoice->student->full_name }}</td>
                                        <td class="px-4 py-3 text-sm font-semibold">₦{{ number_format($invoice->total, 2) }}</td>
                                        <td class="px-4 py-3 text-sm font-semibold {{ $invoice->balance > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                            ₦{{ number_format($invoice->balance, 2) }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $invoice->due_date->format('M d, Y') }}</td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="px-2 py-1 text-xs rounded-full
                                                {{ $invoice->status == 'paid' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                                {{ $invoice->status == 'partial' ? 'bg-amber-100 text-amber-800' : '' }}
                                                {{ $invoice->status == 'overdue' ? 'bg-rose-100 text-rose-800' : '' }}
                                                {{ $invoice->status == 'sent' ? 'bg-blue-100 text-blue-800' : '' }}
                                                {{ $invoice->status == 'draft' ? 'bg-gray-100 text-gray-800' : '' }}">
                                                {{ ucfirst($invoice->status) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center text-sm">
                                            <a href="{{ route('admin.invoices.show', $invoice) }}"
                                               class="text-blue-600 hover:text-blue-900 mr-2">View</a>
                                            @if($invoice->status == 'draft')
                                                <a href="{{ route('admin.invoices.edit', $invoice) }}"
                                                   class="text-emerald-600 hover:text-emerald-900 mr-2">Edit</a>
                                            @endif
                                            @if($invoice->balance > 0)
                                                <a href="{{ route('admin.invoices.pay', $invoice) }}"
                                                   class="text-amber-600 hover:text-amber-900">Pay</a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                            <span class="text-4xl block mb-2">📄</span>
                                            No invoices found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $invoices->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
