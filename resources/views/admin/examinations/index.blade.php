<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Examination Management') }}
            </h2>
            @can('create_exams')
                <a href="{{ route('admin.examinations.create') }}"
                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                    + Create New Exam
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <!-- Filters -->
                    <div class="mb-6">
                        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <select name="class_id" class="w-full rounded-md border-gray-300 shadow-sm">
                                    <option value="">All Classes</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                            {{ $class->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <select name="term_id" class="w-full rounded-md border-gray-300 shadow-sm">
                                    <option value="">All Terms</option>
                                    @foreach($terms as $term)
                                        <option value="{{ $term->id }}" {{ request('term_id') == $term->id ? 'selected' : '' }}>
                                            {{ $term->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <select name="status" class="w-full rounded-md border-gray-300 shadow-sm">
                                    <option value="">All Status</option>
                                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                                </select>
                            </div>
                            <div>
                                <button type="submit" class="w-full bg-gray-800 text-white py-2 px-4 rounded-md hover:bg-gray-700">
                                    🔍 Filter
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Examinations Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Exam Details</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Class</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Term</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($examinations as $exam)
                                    <tr>
                                        <td class="px-6 py-4">
                                            <div>
                                                <div class="font-medium text-gray-900">
                                                    {{ $exam->name ?? $exam->subject->name . ' Exam' }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $exam->exam_date->format('M d, Y') }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900">
                                                {{ $exam->class->name }}
                                                @if($exam->classArm)
                                                    ({{ $exam->classArm->name }})
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ $exam->subject->name }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ $exam->term->name }}
                                            <br>
                                            <span class="text-xs">{{ $exam->academicYear->name }}</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-1 text-xs rounded-full
                                                {{ $exam->status == 'draft' ? 'bg-gray-100 text-gray-800' : '' }}
                                                {{ $exam->status == 'published' ? 'bg-blue-100 text-blue-800' : '' }}
                                                {{ $exam->status == 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $exam->status == 'archived' ? 'bg-red-100 text-red-800' : '' }}">
                                                {{ ucfirst($exam->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm font-medium">
                                            <a href="{{ route('admin.examinations.show', $exam) }}"
                                               class="text-blue-600 hover:text-blue-900 mr-3">View</a>

                                            @if($exam->status !== 'published' && $exam->status !== 'completed')
                                                <a href="{{ route('admin.examinations.edit', $exam) }}"
                                                   class="text-green-600 hover:text-green-900 mr-3">Edit</a>
                                            @endif

                                            @if($exam->status === 'draft')
                                                <form action="{{ route('admin.examinations.destroy', $exam) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" onclick="return confirm('Are you sure?')"
                                                            class="text-red-600 hover:text-red-900">Delete</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                            No examinations found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $examinations->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
