<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __('Class Management') }}
            </h2>
            @can('create_classes')
                <a href="{{ route('admin.classes.create') }}"
                   style="display: inline-flex; align-items: center; padding: 8px 20px; font-size: 14px; font-weight: 600; color: #ffffff; background-color: #059669; border: none; border-radius: 8px; box-shadow: 0 2px 4px rgba(5, 150, 105, 0.3); text-decoration: none; transition: all 0.25s ease; cursor: pointer;"
                   onmouseover="this.style.backgroundColor='#047857'; this.style.boxShadow='0 4px 12px rgba(5, 150, 105, 0.4)'; this.style.transform='scale(1.02)'"
                   onmouseout="this.style.backgroundColor='#059669'; this.style.boxShadow='0 2px 4px rgba(5, 150, 105, 0.3)'; this.style.transform='scale(1)'">
                    <span style="display: inline-block; vertical-align: middle; margin-right: 6px;">➕</span>
                    Add Class
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="p-4 mb-6 text-green-700 bg-green-100 border-l-4 border-green-500 rounded-r">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 mb-6 text-red-700 bg-red-100 border-l-4 border-red-500 rounded-r">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    {{-- Stats Cards --}}
                    <div class="grid grid-cols-2 gap-4 mb-6 md:grid-cols-4">
                        <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                            <span class="text-sm text-gray-500">Total Classes</span>
                            <p class="text-2xl font-bold text-blue-600">{{ $stats['total'] }}</p>
                        </div>
                        <div class="p-4 bg-green-50 rounded-lg border border-green-200">
                            <span class="text-sm text-gray-500">Active</span>
                            <p class="text-2xl font-bold text-green-600">{{ $stats['active'] }}</p>
                        </div>
                        <div class="p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                            <span class="text-sm text-gray-500">Total Arms</span>
                            <p class="text-2xl font-bold text-yellow-600">{{ $stats['total_arms'] }}</p>
                        </div>
                        <div class="p-4 bg-purple-50 rounded-lg border border-purple-200">
                            <span class="text-sm text-gray-500">Total Students</span>
                            <p class="text-2xl font-bold text-purple-600">{{ $stats['total_students'] }}</p>
                        </div>
                    </div>

                    {{-- Filters --}}
                    <form method="GET" class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-3">
                        <div>
                            <input type="text" name="search" placeholder="Search classes..."
                                   value="{{ request('search') }}"
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        </div>
                        <div>
                            <select name="category" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">All Categories</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>
                                        {{ ucfirst($cat) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <button type="submit" class="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-white bg-gray-800 border border-transparent rounded-md shadow-sm hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                                🔍 Filter
                            </button>
                        </div>
                    </form>

                    {{-- Classes Grid --}}
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                        @forelse($classes as $class)
                            <div class="border rounded-lg p-4 hover:shadow-md transition bg-white">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-800">{{ $class->name }}</h3>
                                        <p class="text-sm text-gray-500">{{ $class->code }}</p>
                                        <span class="px-2 py-1 text-xs rounded-full
                                            {{ $class->category == 'primary' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $class->category == 'junior' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $class->category == 'senior' ? 'bg-purple-100 text-purple-800' : '' }}">
                                            {{ $class->category_label }}
                                        </span>
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded-full
                                        {{ $class->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $class->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>

                                <div class="grid grid-cols-3 gap-2 mt-4 text-center">
                                    <div class="p-2 bg-gray-50 rounded">
                                        <div class="text-sm font-bold text-blue-600">{{ $class->arms->count() }}</div>
                                        <div class="text-xs text-gray-500">Arms</div>
                                    </div>
                                    <div class="p-2 bg-gray-50 rounded">
                                        <div class="text-sm font-bold text-green-600">{{ $class->students->count() }}</div>
                                        <div class="text-xs text-gray-500">Students</div>
                                    </div>
                                    <div class="p-2 bg-gray-50 rounded">
                                        <div class="text-sm font-bold text-purple-600">{{ $class->classSubjects->count() }}</div>
                                        <div class="text-xs text-gray-500">Subjects</div>
                                    </div>
                                </div>

                                <div class="flex flex-wrap gap-2 mt-4">
                                    <a href="{{ route('admin.classes.show', $class) }}" class="text-sm text-blue-600 hover:text-blue-800">View</a>
                                    <a href="{{ route('admin.classes.edit', $class) }}" class="text-sm text-green-600 hover:text-green-800">Edit</a>
                                    @if($class->students->count() == 0 && $class->arms->count() == 0)
                                        <form action="{{ route('admin.classes.destroy', $class) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Delete this class?')" class="text-sm text-red-600 hover:text-red-800">Delete</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full text-center py-12 text-gray-500">
                                <span class="text-4xl block mb-2">🏫</span>
                                No classes found.
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-4">
                        {{ $classes->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
