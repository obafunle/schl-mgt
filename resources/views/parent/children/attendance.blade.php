@extends('layouts.parent')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Breadcrumb -->
        <div class="mb-4 text-sm text-gray-500">
            <a href="{{ route('parent.children') }}" class="hover:text-blue-600">👨‍👧‍👦 My Children</a>
            <span class="mx-2">›</span>
            <span>{{ $child->full_name }} - Attendance</span>
        </div>

        <!-- Header -->
        <div class="mb-6 flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">📋 Attendance Record</h1>
                <p class="text-gray-600 text-sm">{{ $child->full_name }} ({{ $child->admission_number }})</p>
            </div>
            <a href="{{ route('parent.child.profile', $child->id) }}" 
               class="px-4 py-2 bg-gray-500 text-white text-sm rounded-lg hover:bg-gray-600 transition">
                ← Back
            </a>
        </div>

        <!-- Month Selector -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-4">
                <form method="GET" class="flex flex-wrap gap-4 items-end">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Month</label>
                        <select name="month" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                    {{ DateTime::createFromFormat('!m', $m)->format('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Year</label>
                        <select name="year" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                            @for($y = now()->year; $y >= now()->year - 5; $y--)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            🔍 Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                <span class="text-sm text-gray-500">Present</span>
                <p class="text-2xl font-bold text-green-600">{{ $summary['present'] }}</p>
            </div>
            <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                <span class="text-sm text-gray-500">Absent</span>
                <p class="text-2xl font-bold text-red-600">{{ $summary['absent'] }}</p>
            </div>
            <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                <span class="text-sm text-gray-500">Late</span>
                <p class="text-2xl font-bold text-yellow-600">{{ $summary['late'] }}</p>
            </div>
            <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                <span class="text-sm text-gray-500">Attendance Rate</span>
                <p class="text-2xl font-bold text-purple-600">{{ $summary['percentage'] }}%</p>
            </div>
        </div>

        <!-- Attendance Calendar -->
        @if($attendance->count() > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Day</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Clock In</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Clock Out</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Notes</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($attendance as $record)
                                    <tr>
                                        <td class="px-4 py-3 text-sm text-gray-500">{{ $record->date->format('M d, Y') }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-500">{{ $record->date->format('l') }}</td>
                                        <td class="px-4 py-3">
                                            <span class="px-3 py-1 text-xs rounded-full 
                                                {{ $record->status == 'present' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $record->status == 'absent' ? 'bg-red-100 text-red-800' : '' }}
                                                {{ $record->status == 'late' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $record->status == 'excused' ? 'bg-blue-100 text-blue-800' : '' }}
                                                {{ $record->status == 'holiday' ? 'bg-purple-100 text-purple-800' : '' }}">
                                                {{ ucfirst($record->status) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-500">{{ $record->clock_in ?? '-' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-500">{{ $record->clock_out ?? '-' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-500">{{ $record->notes ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 text-sm text-gray-500">
                        Showing {{ $attendance->count() }} records for {{ DateTime::createFromFormat('!m', $month)->format('F') }} {{ $year }}
                    </div>
                </div>
            </div>
        @else
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-12 text-center">
                    <div class="text-6xl mb-4">📋</div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">No Attendance Records</h3>
                    <p class="text-gray-500">No attendance records found for this period.</p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection