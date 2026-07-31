@extends('layouts.parent')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Breadcrumb -->
        <div class="mb-4 text-sm text-gray-500">
            <a href="{{ route('parent.children') }}" class="hover:text-blue-600">👨‍👧‍👦 My Children</a>
            <span class="mx-2">›</span>
            <span>{{ $child->full_name }} - Grades</span>
        </div>

        <!-- Header -->
        <div class="mb-6 flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">📊 Academic Performance</h1>
                <p class="text-gray-600 text-sm">{{ $child->full_name }} ({{ $child->admission_number }})</p>
                <p class="text-sm text-gray-500">{{ $child->class->name ?? 'No Class' }} @if($child->classArm)({{ $child->classArm->name }})@endif</p>
            </div>
            <div class="flex items-center space-x-2">
                @if($reportCard)
                    <a href="#" class="px-4 py-2 bg-green-500 text-white text-sm rounded-lg hover:bg-green-600 transition">
                        📄 Download Report Card
                    </a>
                @endif
                <a href="{{ route('parent.child.profile', $child->id) }}" 
                   class="px-4 py-2 bg-gray-500 text-white text-sm rounded-lg hover:bg-gray-600 transition">
                    ← Back
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-4">
                <form method="GET" class="flex flex-wrap gap-4 items-end">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Term</label>
                        <select name="term_id" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                            <option value="">All Terms</option>
                            @foreach($terms as $term)
                                <option value="{{ $term->id }}" {{ $selectedTerm == $term->id ? 'selected' : '' }}>
                                    {{ $term->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Academic Year</label>
                        <select name="academic_year_id" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                            <option value="">All Years</option>
                            @foreach($academicYears ?? [] as $year)
                                <option value="{{ $year->id }}" {{ $selectedYear == $year->id ? 'selected' : '' }}>
                                    {{ $year->name }}
                                </option>
                            @endforeach
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
        @if($grades->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <span class="text-sm text-gray-500">Total Score</span>
                    <p class="text-2xl font-bold text-blue-600">{{ number_format($summary['total_score'], 2) }}</p>
                </div>
                <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                    <span class="text-sm text-gray-500">Average</span>
                    <p class="text-2xl font-bold text-green-600">{{ number_format($summary['average'], 2) }}%</p>
                </div>
                <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                    <span class="text-sm text-gray-500">Subjects</span>
                    <p class="text-2xl font-bold text-yellow-600">{{ $summary['subjects'] }}</p>
                </div>
                <div class="bg-{{ $summary['passed'] >= $summary['subjects'] / 2 ? 'green' : 'red' }}-50 p-4 rounded-lg border border-{{ $summary['passed'] >= $summary['subjects'] / 2 ? 'green' : 'red' }}-200">
                    <span class="text-sm text-gray-500">Passed / Failed</span>
                    <p class="text-2xl font-bold text-{{ $summary['passed'] >= $summary['subjects'] / 2 ? 'green' : 'red' }}-600">
                        {{ $summary['passed'] }} / {{ $summary['failed'] }}
                    </p>
                </div>
            </div>

            <!-- Grades Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">CA (40%)</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Exam (60%)</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Total</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Grade</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Remark</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Position</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($grades as $index => $grade)
                                    <tr>
                                        <td class="px-4 py-3 text-sm text-gray-500">{{ $index + 1 }}</td>
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $grade->subject->name }}</td>
                                        <td class="px-4 py-3 text-sm text-center text-gray-600">{{ $grade->ca_score ?? '-' }}</td>
                                        <td class="px-4 py-3 text-sm text-center text-gray-600">{{ $grade->exam_score ?? '-' }}</td>
                                        <td class="px-4 py-3 text-sm text-center font-semibold">{{ number_format($grade->total_score, 2) }}</td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="px-3 py-1 text-sm font-bold rounded-full 
                                                {{ $grade->grade == 'A' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $grade->grade == 'B' ? 'bg-blue-100 text-blue-800' : '' }}
                                                {{ $grade->grade == 'C' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $grade->grade == 'D' ? 'bg-orange-100 text-orange-800' : '' }}
                                                {{ $grade->grade == 'E' ? 'bg-red-100 text-red-800' : '' }}
                                                {{ $grade->grade == 'F' ? 'bg-gray-100 text-gray-800' : '' }}">
                                                {{ $grade->grade ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-center text-gray-600">{{ $grade->remark ?? '-' }}</td>
                                        <td class="px-4 py-3 text-sm text-center text-gray-600">{{ $grade->position ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="4" class="px-4 py-3 text-sm font-semibold text-right">Summary</td>
                                    <td class="px-4 py-3 text-sm font-bold text-center text-blue-600">
                                        {{ number_format($summary['average'], 2) }}%
                                    </td>
                                    <td colspan="3" class="px-4 py-3 text-sm text-gray-500">
                                        {{ $summary['passed'] }} Passed, {{ $summary['failed'] }} Failed
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Report Card Section -->
            @if($reportCard)
                <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-semibold text-gray-700">📄 Term Report Card</h3>
                                <p class="text-sm text-gray-500">Overall performance summary for this term</p>
                            </div>
                            <a href="#" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                Download PDF
                            </a>
                        </div>
                        <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                            <div class="p-3 bg-gray-50 rounded">
                                <div class="text-sm text-gray-500">Total Score</div>
                                <div class="font-bold text-lg">{{ number_format($reportCard->total_score, 2) }}</div>
                            </div>
                            <div class="p-3 bg-gray-50 rounded">
                                <div class="text-sm text-gray-500">Average</div>
                                <div class="font-bold text-lg text-blue-600">{{ number_format($reportCard->average_score, 2) }}%</div>
                            </div>
                            <div class="p-3 bg-gray-50 rounded">
                                <div class="text-sm text-gray-500">GPA</div>
                                <div class="font-bold text-lg text-purple-600">{{ number_format($reportCard->grade_point_average, 2) }}</div>
                            </div>
                            <div class="p-3 bg-gray-50 rounded">
                                <div class="text-sm text-gray-500">Promotion</div>
                                <div class="font-bold text-lg {{ $reportCard->promotion_status === 'promoted' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ ucfirst($reportCard->promotion_status) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @else
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-12 text-center">
                    <div class="text-6xl mb-4">📊</div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">No Grades Available</h3>
                    <p class="text-gray-500">No grades have been recorded for this student yet.</p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection