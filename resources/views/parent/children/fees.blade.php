@extends('layouts.parent')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Breadcrumb -->
        <div class="mb-4 text-sm text-gray-500">
            <a href="{{ route('parent.children') }}" class="hover:text-blue-600">👨‍👧‍👦 My Children</a>
            <span class="mx-2">›</span>
            <span>{{ $child->full_name }} - Fees</span>
        </div>

        <!-- Header -->
        <div class="mb-6 flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">💰 Fee Management</h1>
                <p class="text-gray-600 text-sm">{{ $child->full_name }} ({{ $child->admission_number }})</p>
            </div>
            <a href="{{ route('parent.child.profile', $child->id) }}" 
               class="px-4 py-2 bg-gray-500 text-white text-sm rounded-lg hover:bg-gray-600 transition">
                ← Back
            </a>
        </div>

        <!-- Fee Summary -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                <span class="text-sm text-gray-500">Total Invoiced</span>
                <p class="text-2xl font-bold text-blue-600">₦{{ number_format($totalInvoiced, 2) }}</p>
            </div>
            <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                <span class="text-sm text-gray-500">Total Paid</span>
                <p class="text-2xl font-bold text-green-600">₦{{ number_format($totalPaid, 2) }}</p>
            </div>
            <div class="bg-{{ $balance > 0 ? 'red' : 'green' }}-50 p-4 rounded-lg border border-{{ $balance > 0 ? 'red' : 'green' }}-200">
                <span class="text-sm text-gray-500">Balance</span>
                <p class="text-2xl font-bold text-{{ $balance > 0 ? 'red' : 'green' }}-600">₦{{ number_format($balance, 2) }}</p>
            </div>
        </div>

        <!-- Invoices List -->
        @if($invoices->count() > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Invoice #</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Due Date</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Paid</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Balance</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($invoices as $invoice)
                                    <tr>
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $invoice->invoice_number }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-500">{{ $invoice->issue_date->format('M d, Y') }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-500">
                                            {{ $invoice->due_date->format('M d, Y') }}
                                            @if($invoice->due_date < now() && $invoice->status != 'paid')
                                                <span class="ml-2 text-red-500 text-xs font-bold">OVERDUE</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm text-right font-semibold">₦{{ number_format($invoice->total, 2) }}</td>
                                        <td class="px-4 py-3 text-sm text-right text-green-600">₦{{ number_format($invoice->amount_paid, 2) }}</td>
                                        <td class="px-4 py-3 text-sm text-right font-bold {{ $invoice->balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                                            ₦{{ number_format($invoice->balance, 2) }}
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="px-2 py-1 text-xs rounded-full 
                                                {{ $invoice->status == 'paid' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $invoice->status == 'sent' ? 'bg-blue-100 text-blue-800' : '' }}
                                                {{ $invoice->status == 'partial' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $invoice->status == 'overdue' ? 'bg-red-100 text-red-800' : '' }}
                                                {{ $invoice->status == 'draft' ? 'bg-gray-100 text-gray-800' : '' }}">
                                                {{ ucfirst($invoice->status) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            @if($invoice->balance > 0)
                                                <a href="{{ route('parent.pay.invoice', $invoice->id) }}" 
                                                   class="px-3 py-1 bg-green-500 text-white text-xs rounded hover:bg-green-600 transition">
                                                    💳 Pay Now
                                                </a>
                                            @else
                                                <span class="text-green-600 text-xs font-semibold">✅ Paid</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $invoices->links() }}
                    </div>
                </div>
            </div>
        @else
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-12 text-center">
                    <div class="text-6xl mb-4">💰</div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">No Invoices Found</h3>
                    <p class="text-gray-500">No invoices have been generated for this student yet.</p>
                </div>
            </div>
        @endif

        <!-- Payment History -->
        @php
            $payments = \App\Models\Payment::where('student_id', $child->id)
                ->where('status', 'success')
                ->latest()
                ->limit(10)
                ->get();
        @endphp

        @if($payments->count() > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6">
                    <h3 class="font-semibold text-gray-700 mb-3">📋 Recent Payments</h3>
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
                                @foreach($payments as $payment)
                                    <tr>
                                        <td class="px-4 py-2 text-sm text-gray-500">{{ $payment->payment_date->format('M d, Y h:i A') }}</td>
                                        <td class="px-4 py-2 text-sm font-semibold text-gray-900">₦{{ number_format($payment->amount, 2) }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-500">{{ ucfirst($payment->payment_method) }}</td>
                                        <td class="px-4 py-2 text-sm text-gray-500">{{ $payment->reference }}</td>
                                        <td class="px-4 py-2">
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Success</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection