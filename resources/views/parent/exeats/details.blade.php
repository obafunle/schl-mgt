@extends('layouts.parent')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Breadcrumb -->
        <div class="mb-4 text-sm text-gray-500">
            <a href="{{ route('parent.exeats') }}" class="hover:text-blue-600">📋 Exeat Requests</a>
            <span class="mx-2">›</span>
            <span>Exeat #{{ $exeat->exeat_number }}</span>
        </div>

        <!-- Header -->
        <div class="mb-6 flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">📋 Exeat Request Details</h1>
                <p class="text-gray-600 text-sm">#{{ $exeat->exeat_number }}</p>
            </div>
            <div class="flex space-x-2">
                @if($exeat->status === 'approved')
                    <a href="{{ route('parent.exeats.download', $exeat) }}" 
                       class="px-4 py-2 bg-green-500 text-white text-sm rounded-lg hover:bg-green-600 transition">
                        📄 Download PDF
                    </a>
                @endif
                @if($exeat->status === 'pending')
                    <form action="{{ route('parent.exeats.cancel', $exeat) }}" method="POST">
                        @csrf
                        <button type="submit" onclick="return confirm('Are you sure you want to cancel this request?')" 
                                class="px-4 py-2 bg-red-500 text-white text-sm rounded-lg hover:bg-red-600 transition">
                            ❌ Cancel Request
                        </button>
                    </form>
                @endif
                <a href="{{ route('parent.exeats') }}" 
                   class="px-4 py-2 bg-gray-500 text-white text-sm rounded-lg hover:bg-gray-600 transition">
                    ← Back
                </a>
            </div>
        </div>

        <!-- Status Banner -->
        <div class="mb-6 p-4 rounded-lg border 
            {{ $exeat->status == 'approved' ? 'bg-green-50 border-green-400' : '' }}
            {{ $exeat->status == 'pending' ? 'bg-yellow-50 border-yellow-400' : '' }}
            {{ $exeat->status == 'rejected' ? 'bg-red-50 border-red-400' : '' }}
            {{ $exeat->status == 'cancelled' ? 'bg-gray-50 border-gray-400' : '' }}
            {{ $exeat->status == 'completed' ? 'bg-blue-50 border-blue-400' : '' }}">
            <div class="flex items-center justify-between">
                <div>
                    <span class="font-semibold">Status:</span>
                    <span class="px-3 py-1 rounded-full text-sm font-medium
                        {{ $exeat->status == 'approved' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $exeat->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $exeat->status == 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                        {{ $exeat->status == 'cancelled' ? 'bg-gray-100 text-gray-800' : '' }}
                        {{ $exeat->status == 'completed' ? 'bg-blue-100 text-blue-800' : '' }}">
                        {{ $exeat->status_label }}
                    </span>
                    @if($exeat->status === 'approved' && $exeat->approved_at)
                        <span class="ml-2 text-sm text-gray-500">
                            Approved on: {{ $exeat->approved_at->format('M d, Y h:i A') }}
                            by {{ $exeat->approvedBy->name ?? 'Admin' }}
                        </span>
                    @endif
                    @if($exeat->status === 'rejected' && $exeat->rejection_reason)
                        <div class="mt-2 text-sm text-red-600">
                            <span class="font-semibold">Rejection Reason:</span>
                            {{ $exeat->rejection_reason }}
                        </div>
                    @endif
                </div>
                @if($exeat->status === 'approved')
                    <div class="text-center">
                        <div class="text-3xl mb-1">✅</div>
                        <div class="text-xs text-green-600 font-semibold">Approved</div>
                    </div>
                @endif
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <!-- Student Information -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-700 border-b pb-2 mb-4">👤 Student Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <span class="text-sm text-gray-500">Student Name</span>
                            <p class="font-semibold">{{ $exeat->student->full_name }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">Admission Number</span>
                            <p class="font-semibold">{{ $exeat->student->admission_number }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">Class</span>
                            <p class="font-semibold">{{ $exeat->student->class->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">Term / Academic Year</span>
                            <p class="font-semibold">{{ $exeat->term->name ?? 'N/A' }} / {{ $exeat->academicYear->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Exeat Details -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-700 border-b pb-2 mb-4">📋 Exeat Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <span class="text-sm text-gray-500">Departure Date</span>
                            <p class="font-semibold">{{ $exeat->departure_date->format('l, M d, Y') }}</p>
                            @if($exeat->departure_time)
                                <p class="text-sm text-gray-500">Time: {{ $exeat->departure_time->format('h:i A') }}</p>
                            @endif
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">Return Date</span>
                            <p class="font-semibold">{{ $exeat->return_date->format('l, M d, Y') }}</p>
                            @if($exeat->return_time)
                                <p class="text-sm text-gray-500">Time: {{ $exeat->return_time->format('h:i A') }}</p>
                            @endif
                        </div>
                        <div class="md:col-span-2">
                            <span class="text-sm text-gray-500">Duration</span>
                            <p class="font-semibold">{{ $exeat->getDaysDifference() }} day(s)</p>
                        </div>
                        <div class="md:col-span-2">
                            <span class="text-sm text-gray-500">Reason</span>
                            <p class="font-semibold">{{ $exeat->reason }}</p>
                        </div>
                        @if($exeat->destination)
                            <div>
                                <span class="text-sm text-gray-500">Destination</span>
                                <p class="font-semibold">{{ $exeat->destination }}</p>
                            </div>
                        @endif
                        @if($exeat->accompanied_by)
                            <div>
                                <span class="text-sm text-gray-500">Accompanied By</span>
                                <p class="font-semibold">{{ $exeat->accompanied_by }}</p>
                            </div>
                        @endif
                        @if($exeat->contact_during_absence)
                            <div class="md:col-span-2">
                                <span class="text-sm text-gray-500">Contact During Absence</span>
                                <p class="font-semibold">{{ $exeat->contact_during_absence }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Timeline -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-700 border-b pb-2 mb-4">⏰ Timeline</h3>
                    <div class="space-y-3">
                        <div class="flex items-start">
                            <div class="flex flex-col items-center mr-4">
                                <div class="w-3 h-3 rounded-full bg-blue-500"></div>
                                <div class="w-0.5 h-full bg-gray-300 flex-1"></div>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Request Submitted</p>
                                <p class="text-xs text-gray-500">{{ $exeat->created_at->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>

                        @if($exeat->status === 'approved' || $exeat->status === 'rejected')
                            <div class="flex items-start">
                                <div class="flex flex-col items-center mr-4">
                                    <div class="w-3 h-3 rounded-full {{ $exeat->status === 'approved' ? 'bg-green-500' : 'bg-red-500' }}"></div>
                                    <div class="w-0.5 h-full bg-gray-300 flex-1"></div>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $exeat->status === 'approved' ? '✅ Approved' : '❌ Rejected' }}</p>
                                    <p class="text-xs text-gray-500">{{ $exeat->approved_at ? $exeat->approved_at->format('M d, Y h:i A') : '' }}</p>
                                    @if($exeat->approvedBy)
                                        <p class="text-xs text-gray-500">By: {{ $exeat->approvedBy->name }}</p>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if($exeat->status === 'completed')
                            <div class="flex items-start">
                                <div class="flex flex-col items-center mr-4">
                                    <div class="w-3 h-3 rounded-full bg-purple-500"></div>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">🔄 Completed</p>
                                    <p class="text-xs text-gray-500">Return confirmed</p>
                                </div>
                            </div>
                        @endif

                        @if($exeat->status === 'cancelled')
                            <div class="flex items-start">
                                <div class="flex flex-col items-center mr-4">
                                    <div class="w-3 h-3 rounded-full bg-gray-500"></div>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">❌ Cancelled</p>
                                    <p class="text-xs text-gray-500">Cancelled by parent</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- QR Code (if approved) -->
                @if($exeat->status === 'approved')
                    <div class="text-center p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-700 mb-2">🔲 QR Code</h4>
                        <div class="inline-block p-4 bg-white rounded-lg">
                            <!-- You can generate QR code here using a package like simplesoftwareio/simple-qrcode -->
                            <div class="w-32 h-32 bg-gray-200 flex items-center justify-center text-gray-400 text-sm">
                                QR Code
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Scan at school gate for verification</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection