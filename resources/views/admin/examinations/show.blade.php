<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Examination Details') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.examinations.enter-grades', $examination) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg text-sm">📝 Enter Grades</a>
                <a href="{{ route('admin.examinations.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg text-sm">← Back</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                        <div class="bg-blue-50 p-4 rounded-lg"><span class="text-sm text-gray-500">Class</span><p class="font-bold">{{ $examination->class->name }}</p></div>
                        <div class="bg-green-50 p-4 rounded-lg"><span class="text-sm text-gray-500">Subject</span><p class="font-bold">{{ $examination->subject->name }}</p></div>
                        <div class="bg-purple-50 p-4 rounded-lg"><span class="text-sm text-gray-500">Date</span><p class="font-bold">{{ $examination->exam_date->format('M d, Y') }}</p></div>
                        <div class="bg-amber-50 p-4 rounded-lg"><span class="text-sm text-gray-500">Status</span><p class="font-bold">{{ ucfirst($examination->status) }}</p></div>
                    </div>

                    <div class="mt-6">
                        <h3 class="font-semibold text-gray-700 mb-3">Grades Summary</h3>
                        @if($examination->grades->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                                            <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">CA</th>
                                            <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Exam</th>
                                            <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Total</th>
                                            <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Grade</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($examination->grades as $grade)
                                            <tr>
                                                <td class="px-4 py-2 text-sm">{{ $grade->student->full_name }}</td>
                                                <td class="px-4 py-2 text-center text-sm">{{ $grade->ca_score ?? '-' }}</td>
                                                <td class="px-4 py-2 text-center text-sm">{{ $grade->exam_score ?? '-' }}</td>
                                                <td class="px-4 py-2 text-center text-sm font-bold">{{ $grade->total_score ?? '-' }}</td>
                                                <td class="px-4 py-2 text-center text-sm font-bold">{{ $grade->grade ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-gray-500 text-sm">No grades entered yet.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
