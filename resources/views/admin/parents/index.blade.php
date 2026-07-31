<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Parents Management') }}
            </h2>
            @can('manage_parents')
                <a href="{{ route('admin.parents.create') }}"
                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-md hover:shadow-lg transition duration-200">
                    + Add Parent
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <!-- Stats -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-200">
                            <span class="text-sm text-gray-500">Total Parents</span>
                            <p class="text-2xl font-bold text-indigo-600">{{ $parents->count() }}</p>
                        </div>
                        <div class="bg-emerald-50 p-4 rounded-lg border border-emerald-200">
                            <span class="text-sm text-gray-500">Active</span>
                            <p class="text-2xl font-bold text-emerald-600">{{ $parents->where('status', 'active')->count() }}</p>
                        </div>
                        <div class="bg-amber-50 p-4 rounded-lg border border-amber-200">
                            <span class="text-sm text-gray-500">Children Registered</span>
                            <p class="text-2xl font-bold text-amber-600">{{ $parents->sum(function($p) { return $p->children->count(); }) }}</p>
                        </div>
                    </div>

                    <!-- Search -->
                    <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div>
                            <input type="text" name="search" placeholder="Search by name or email..."
                                   value="{{ request('search') }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        </div>
                        <div>
                            <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                <option value="">All Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                            </select>
                        </div>
                        <div>
                            <button type="submit" class="w-full bg-gray-800 text-white py-2 px-4 rounded-md hover:bg-gray-700 transition">
                                🔍 Filter
                            </button>
                        </div>
                    </form>

                    <!-- Parents Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Parent</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Children</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($parents as $parent)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-4 py-3">
                                            <div>
                                                <div class="font-medium text-gray-900">{{ $parent->full_name }}</div>
                                                <div class="text-sm text-gray-500">{{ $parent->email }}</div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="text-sm text-gray-600">{{ $parent->phone ?? 'N/A' }}</div>
                                            <div class="text-sm text-gray-500">{{ $parent->occupation ?? 'N/A' }}</div>
                                        </td>
                                        <td class="px-4 py-3">
                                            @foreach($parent->children as $child)
                                                <span class="inline-block px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded mr-1 mb-1">
                                                    {{ $child->full_name }}
                                                </span>
                                            @endforeach
                                            @if($parent->children->count() == 0)
                                                <span class="text-sm text-gray-400">No children</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="px-2 py-1 text-xs rounded-full
                                                {{ $parent->status == 'active' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                                {{ $parent->status == 'inactive' ? 'bg-gray-100 text-gray-800' : '' }}
                                                {{ $parent->status == 'suspended' ? 'bg-rose-100 text-rose-800' : '' }}">
                                                {{ ucfirst($parent->status) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center text-sm">
                                            <a href="{{ route('admin.parents.show', $parent) }}"
                                               class="text-blue-600 hover:text-blue-900 mr-2">View</a>
                                            <a href="{{ route('admin.parents.edit', $parent) }}"
                                               class="text-emerald-600 hover:text-emerald-900 mr-2">Edit</a>
                                            <form action="{{ route('admin.parents.destroy', $parent) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('Delete this parent?')"
                                                        class="text-rose-600 hover:text-rose-900">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                            <span class="text-4xl block mb-2">👨‍👩‍👧</span>
                                            No parents found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $parents->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
