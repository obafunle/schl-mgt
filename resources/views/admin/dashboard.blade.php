<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto">

            <!-- Welcome Message -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold">Welcome back, {{ Auth::user()->name }}!</h3>
                    <p class="text-gray-600">Here's an overview of your school management system.</p>
                </div>
            </div>

            <!-- Stats Cards Row 1 -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-sm text-gray-500">Students</span>
                            <p class="text-2xl font-bold text-blue-600">{{ $totalStudents }}</p>
                            <span class="text-xs text-gray-400">Active: {{ $activeStudents }}</span>
                        </div>
                        <div class="text-3xl">👨‍🎓</div>
                    </div>
                </div>
                <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-sm text-gray-500">Staff</span>
                            <p class="text-2xl font-bold text-green-600">{{ $totalStaff }}</p>
                            <span class="text-xs text-gray-400">Teachers: {{ $teachers }}</span>
                        </div>
                        <div class="text-3xl">👨‍🏫</div>
                    </div>
                </div>
                <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-sm text-gray-500">Revenue</span>
                            <p class="text-2xl font-bold text-yellow-600">₦{{ number_format($totalPaid, 0) }}</p>
                            <span class="text-xs text-gray-400">Invoiced: ₦{{ number_format($totalInvoiced, 0) }}</span>
                        </div>
                        <div class="text-3xl">💰</div>
                    </div>
                </div>
                <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-sm text-gray-500">Exams</span>
                            <p class="text-2xl font-bold text-purple-600">{{ $totalExams }}</p>
                            <span class="text-xs text-gray-400">Completed: {{ $completedExams }}</span>
                        </div>
                        <div class="text-3xl">📝</div>
                    </div>
                </div>
            </div>

            <!-- Stats Cards Row 2 -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-sm text-gray-500">Hostel</span>
                            <p class="text-2xl font-bold text-indigo-600">{{ $occupiedBeds }}</p>
                            <span class="text-xs text-gray-400">Occupancy: {{ $occupancyRate }}%</span>
                        </div>
                        <div class="text-3xl">🏠</div>
                    </div>
                </div>
                <div class="bg-pink-50 p-4 rounded-lg border border-pink-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-sm text-gray-500">Library</span>
                            <p class="text-2xl font-bold text-pink-600">{{ $availableBooks }}</p>
                            <span class="text-xs text-gray-400">Borrowed: {{ $borrowedBooks }}</span>
                        </div>
                        <div class="text-3xl">📚</div>
                    </div>
                </div>
                <div class="bg-orange-50 p-4 rounded-lg border border-orange-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-sm text-gray-500">Transport</span>
                            <p class="text-2xl font-bold text-orange-600">{{ $assignedStudents }}</p>
                            <span class="text-xs text-gray-400">Vehicles: {{ $activeVehicles }}</span>
                        </div>
                        <div class="text-3xl">🚌</div>
                    </div>
                </div>
                <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-sm text-gray-500">Inventory</span>
                            <p class="text-2xl font-bold text-red-600">{{ $totalInventoryItems }}</p>
                            <span class="text-xs text-gray-400">Low Stock: {{ $lowStockItems }}</span>
                        </div>
                        <div class="text-3xl">📦</div>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="font-semibold text-gray-700 mb-3">📋 Recent Students</h3>
                        @if($recentStudents->count() > 0)
                            <ul class="divide-y divide-gray-200">
                                @foreach($recentStudents as $student)
                                    <li class="py-2 text-sm flex justify-between">
                                        <span>{{ $student->full_name }}</span>
                                        <span class="text-gray-500">{{ $student->created_at->diffForHumans() }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-gray-500 text-sm">No students registered yet.</p>
                        @endif
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="font-semibold text-gray-700 mb-3">💳 Recent Payments</h3>
                        @if($recentPayments->count() > 0)
                            <ul class="divide-y divide-gray-200">
                                @foreach($recentPayments as $payment)
                                    <li class="py-2 text-sm flex justify-between">
                                        <span>{{ $payment->student->full_name ?? 'N/A' }}</span>
                                        <span class="text-green-600 font-semibold">₦{{ number_format($payment->amount, 2) }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-gray-500 text-sm">No payments recorded yet.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('admin.students.create') }}"
                   class="bg-blue-500 hover:bg-blue-600 text-white p-4 rounded-lg text-center transition">
                    <div class="text-2xl mb-1">➕</div>
                    <div class="text-sm font-semibold">Add Student</div>
                </a>
                <a href="{{ route('admin.staff.create') }}"
                   class="bg-green-500 hover:bg-green-600 text-white p-4 rounded-lg text-center transition">
                    <div class="text-2xl mb-1">👨‍🏫</div>
                    <div class="text-sm font-semibold">Add Staff</div>
                </a>
                <a href="{{ route('admin.invoices.create') }}"
                   class="bg-yellow-500 hover:bg-yellow-600 text-white p-4 rounded-lg text-center transition">
                    <div class="text-2xl mb-1">📄</div>
                    <div class="text-sm font-semibold">Create Invoice</div>
                </a>
                <a href="{{ route('admin.examinations.create') }}"
                   class="bg-purple-500 hover:bg-purple-600 text-white p-4 rounded-lg text-center transition">
                    <div class="text-2xl mb-1">📝</div>
                    <div class="text-sm font-semibold">Create Exam</div>
                </a>
            </div>

        </div>
    </div>
</x-app-layout>
