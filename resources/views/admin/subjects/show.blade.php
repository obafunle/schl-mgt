<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ $subject->name }} - Subject Details
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.subjects.edit', $subject) }}"
                    class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-green-600 border border-transparent rounded-md shadow-sm hover:bg-green-700">
                    Edit Subject
                </a>
                <a href="{{ route('admin.subjects.index') }}"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50">
                    ← Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        {{-- LEFT COLUMN --}}
                        <div>
                            <h3 class="text-lg font-semibold text-gray-700 border-b pb-2">Subject Information</h3>
                            <dl class="mt-4 space-y-3 text-sm">
                                <div class="flex justify-between border-b py-2">
                                    <dt class="text-gray-500">Name</dt>
                                    <dd class="font-semibold">{{ $subject->name }}</dd>
                                </div>
                                <div class="flex justify-between border-b py-2">
                                    <dt class="text-gray-500">Code</dt>
                                    <dd class="font-mono">{{ $subject->code }}</dd>
                                </div>
                                <div class="flex justify-between border-b py-2">
                                    <dt class="text-gray-500">Short Name</dt>
                                    <dd>{{ $subject->short_name ?? 'N/A' }}</dd>
                                </div>

                                {{-- ✅ CATEGORY with TEXT --}}
                                <div class="flex justify-between border-b py-2">
                                    <dt class="text-gray-500">Category</dt>
                                    <dd>
                                        <span class="px-2 py-1 text-xs rounded-full
                                            @if($subject->category == 'core') bg-indigo-100 text-indigo-800
                                            @elseif($subject->category == 'science') bg-green-100 text-green-800
                                            @elseif($subject->category == 'arts') bg-yellow-100 text-yellow-800
                                            @elseif($subject->category == 'vocational') bg-orange-100 text-orange-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst($subject->category ?? 'N/A') }}
                                        </span>
                                    </dd>
                                </div>

                                {{-- ✅ LEVEL with TEXT --}}
                                <div class="flex justify-between border-b py-2">
                                    <dt class="text-gray-500">Level</dt>
                                    <dd>
                                        <span class="px-2 py-1 text-xs rounded-full
                                            @if($subject->level == 'primary') bg-blue-100 text-blue-800
                                            @elseif($subject->level == 'junior') bg-purple-100 text-purple-800
                                            @elseif($subject->level == 'senior') bg-pink-100 text-pink-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst($subject->level ?? 'N/A') }}
                                        </span>
                                    </dd>
                                </div>

                                <div class="flex justify-between border-b py-2">
                                    <dt class="text-gray-500">Weekly Hours</dt>
                                    <dd>{{ $subject->weekly_hours }}</dd>
                                </div>
                                <div class="flex justify-between border-b py-2">
                                    <dt class="text-gray-500">Exam Weight</dt>
                                    <dd>{{ $subject->exam_weight }}%</dd>
                                </div>
                                <div class="flex justify-between border-b py-2">
                                    <dt class="text-gray-500">CA Weight</dt>
                                    <dd>{{ $subject->ca_weight }}%</dd>
                                </div>
                                <div class="flex justify-between border-b py-2">
                                    <dt class="text-gray-500">Status</dt>
                                    <dd>
                                        <span class="px-2 py-1 text-xs rounded-full {{ $subject->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $subject->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        {{-- RIGHT COLUMN --}}
                        <div>
                            <h3 class="text-lg font-semibold text-gray-700 border-b pb-2">Statistics</h3>
                            <div class="mt-4 grid grid-cols-2 gap-4">
                                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                                    <span class="text-sm text-gray-500">Assigned to Classes</span>
                                    <p class="text-2xl font-bold text-blue-600">{{ $stats['total_classes'] }}</p>
                                </div>
                                <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                                    <span class="text-sm text-gray-500">Teachers Assigned</span>
                                    <p class="text-2xl font-bold text-green-600">{{ $stats['total_teachers'] }}</p>
                                </div>
                                <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                                    <span class="text-sm text-gray-500">Weekly Hours</span>
                                    <p class="text-2xl font-bold text-yellow-600">{{ $subject->weekly_hours }}</p>
                                </div>
                                <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                                    <span class="text-sm text-gray-500">Status</span>
                                    <p class="text-2xl font-bold text-purple-600">{{ $subject->is_active ? 'Active' : 'Inactive' }}</p>
                                </div>
                            </div>

                            @if($subject->description)
                                <div class="mt-6">
                                    <h3 class="text-sm font-semibold text-gray-700">Description</h3>
                                    <p class="mt-1 text-sm text-gray-600">{{ $subject->description }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- CLASS ASSIGNMENTS --}}
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-700 mb-4">📚 Assigned to Classes</h3>
                        @if($subject->classSubjects->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Class</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Arm</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Teacher</th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Weekly Hours</th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Core</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($subject->classSubjects as $assignment)
                                            <tr>
                                                <td class="px-4 py-3 text-sm">{{ $assignment->class->name ?? 'N/A' }}</td>
                                                <td class="px-4 py-3 text-sm">{{ $assignment->classArm->name ?? 'N/A' }}</td>
                                                <td class="px-4 py-3 text-sm">{{ $assignment->teacher->full_name ?? 'Not Assigned' }}</td>
                                                <td class="px-4 py-3 text-sm text-center">{{ $assignment->weekly_hours }}</td>
                                                <td class="px-4 py-3 text-center">
                                                    <span class="px-2 py-1 text-xs rounded-full {{ $assignment->is_core ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 text-gray-600' }}">
                                                        {{ $assignment->is_core ? 'Yes' : 'No' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-sm text-gray-500">This subject is not assigned to any class yet.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
