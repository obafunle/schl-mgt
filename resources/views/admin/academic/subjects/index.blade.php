<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Subject Management') }}
            </h2>
            @can('manage_subjects')
                <a href="{{ route('admin.subjects.create') }}" 
                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                    + Add New Subject
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
                    
                    <!-- Search & Filter -->
                    <div class="mb-4">
                        <form method="GET" class="flex flex-wrap gap-4">
                            <input type="text" name="search" placeholder="Search subjects..." 
                                   value="{{ request('search') }}"
                                   class="rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                            <select name="level" class="rounded-md border-gray-300 shadow-sm">
                                <option value="">All Levels</option>
                                <option value="primary" {{ request('level') == 'primary' ? 'selected' : '' }}>Primary</option>
                                <option value="junior" {{ request('level') == 'junior' ? 'selected' : '' }}>Junior</option>
                                <option value="senior" {{ request('level') == 'senior' ? 'selected' : '' }}>Senior</option>
                            </select>
                            <select name="category" class="rounded-md border-gray-300 shadow-sm">
                                <option value="">All Categories</option>
                                <option value="core" {{ request('category') == 'core' ? 'selected' : '' }}>Core</option>
                                <option value="science" {{ request('category') == 'science' ? 'selected' : '' }}>Science</option>
                                <option value="arts" {{ request('category') == 'arts' ? 'selected' : '' }}>Arts</option>
                                <option value="vocational" {{ request('category') == 'vocational' ? 'selected' : '' }}>Vocational</option>
                            </select>
                            <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-md">Filter</button>
                        </form>
                    </div>

                    <!-- Subjects Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Level</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Classes</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($subjects as $subject)
                                    <tr>
                                        <td class="px-6 py-4">
                                            <div>
                                                <div class="font-medium text-gray-900">{{ $subject->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $subject->short_name }}</div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">{{ $subject->code }}</td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-1 text-xs rounded-full 
                                                {{ $subject->level == 'primary' ? 'bg-blue-100 text-blue-800' : '' }}
                                                {{ $subject->level == 'junior' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $subject->level == 'senior' ? 'bg-purple-100 text-purple-800' : '' }}">
                                                {{ ucfirst($subject->level) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">{{ ucfirst($subject->category) }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ $subject->classSubjects->count() }} classes
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-1 text-xs rounded-full 
                                                {{ $subject->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $subject->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm font-medium">
                                            <a href="{{ route('admin.subjects.show', $subject) }}" 
                                               class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                            <a href="{{ route('admin.subjects.edit', $subject) }}" 
                                               class="text-green-600 hover:text-green-900 mr-3">Edit</a>
                                            @if($subject->classSubjects->count() === 0)
                                                <form action="{{ route('admin.subjects.destroy', $subject) }}" method="POST" class="inline">
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
                                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                            No subjects found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="mt-4">
                        {{ $subjects->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>