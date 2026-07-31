<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __('Subject Management') }}
            </h2>
            @can('create_subjects')
                <a href="{{ route('admin.subjects.create') }}"
                    class="inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    + Add Subject
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            {{-- Flash Messages --}}
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
            @if(session('info'))
                <div class="p-4 mb-6 text-blue-700 bg-blue-100 border-l-4 border-blue-500 rounded-r">
                    {{ session('info') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    {{-- Stats Cards --}}
                    <div class="grid grid-cols-2 gap-3 mb-6 md:grid-cols-3 lg:grid-cols-6">
                        <div class="p-3 bg-blue-50 rounded-lg border border-blue-200">
                            <span class="text-xs text-gray-500">Total</span>
                            <p class="text-xl font-bold text-blue-600">{{ $stats['total'] ?? 0 }}</p>
                        </div>
                        <div class="p-3 bg-green-50 rounded-lg border border-green-200">
                            <span class="text-xs text-gray-500">Active</span>
                            <p class="text-xl font-bold text-green-600">{{ $stats['active'] ?? 0 }}</p>
                        </div>
                        <div class="p-3 bg-indigo-50 rounded-lg border border-indigo-200">
                            <span class="text-xs text-gray-500">Core</span>
                            <p class="text-xl font-bold text-indigo-600">{{ $stats['core'] ?? 0 }}</p>
                        </div>
                        <div class="p-3 bg-emerald-50 rounded-lg border border-emerald-200">
                            <span class="text-xs text-gray-500">Science</span>
                            <p class="text-xl font-bold text-emerald-600">{{ $stats['science'] ?? 0 }}</p>
                        </div>
                        <div class="p-3 bg-amber-50 rounded-lg border border-amber-200">
                            <span class="text-xs text-gray-500">Arts</span>
                            <p class="text-xl font-bold text-amber-600">{{ $stats['arts'] ?? 0 }}</p>
                        </div>
                        <div class="p-3 bg-orange-50 rounded-lg border border-orange-200">
                            <span class="text-xs text-gray-500">Vocational</span>
                            <p class="text-xl font-bold text-orange-600">{{ $stats['vocational'] ?? 0 }}</p>
                        </div>
                    </div>

                    {{-- Filters --}}
                    <form method="GET" class="grid grid-cols-1 gap-3 mb-6 md:grid-cols-2 lg:grid-cols-4">
                        <div>
                            <input type="text" name="search" placeholder="Search subjects..."
                                value="{{ request('search') }}"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        </div>
                        <div>
                            <select name="category" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                        {{ ucfirst($category) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <select name="level" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">All Levels</option>
                                @foreach($levels as $level)
                                    <option value="{{ $level }}" {{ request('level') == $level ? 'selected' : '' }}>
                                        {{ ucfirst($level) }}
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

                    {{-- Bulk Actions --}}
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-2">
                            <input type="checkbox" id="select-all" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300">
                            <span class="text-sm text-gray-500">Select All</span>
                        </div>
                        <div class="flex space-x-2" id="bulk-actions" style="display: none;">
                            <button onclick="bulkAction('activate')" class="px-3 py-1 text-sm text-white bg-green-600 rounded hover:bg-green-700">Activate</button>
                            <button onclick="bulkAction('deactivate')" class="px-3 py-1 text-sm text-white bg-yellow-600 rounded hover:bg-yellow-700">Deactivate</button>
                            <button onclick="bulkAction('delete')" class="px-3 py-1 text-sm text-white bg-red-600 rounded hover:bg-red-700">Delete</button>
                        </div>
                    </div>

                    {{-- Subjects Table --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <input type="checkbox" id="select-all-table" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300">
                                    </th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Level</th>
                                    <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Hours</th>
                                    <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($subjects as $subject)
                                    <tr class="hover:bg-gray-50 transition" id="subject-row-{{ $subject->id }}">
                                        <td class="px-3 py-3">
                                            <input type="checkbox" name="subject_ids[]" value="{{ $subject->id }}" class="subject-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300">
                                        </td>
                                        <td class="px-3 py-3">
                                            <div class="font-medium text-gray-900">{{ $subject->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $subject->short_name ?? '' }}</div>
                                        </td>
                                        <td class="px-3 py-3 text-sm text-gray-600">{{ $subject->code }}</td>

                                        {{-- ✅ CATEGORY BADGE with strtolower() fix --}}
                                        <td class="px-3 py-3">
                                            <span class="px-2 py-1 text-xs rounded-full
                                                @if(strtolower($subject->category) == 'core') bg-indigo-100 text-indigo-800
                                                @elseif(strtolower($subject->category) == 'science') bg-emerald-100 text-emerald-800
                                                @elseif(strtolower($subject->category) == 'arts') bg-amber-100 text-amber-800
                                                @elseif(strtolower($subject->category) == 'vocational') bg-orange-100 text-orange-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ ucfirst($subject->category ?? 'N/A') }}
                                            </span>
                                        </td>

                                        {{-- ✅ LEVEL BADGE with strtolower() fix --}}
                                        <td class="px-3 py-3">
                                            <span class="px-2 py-1 text-xs rounded-full
                                                @if(strtolower($subject->level) == 'primary') bg-blue-100 text-blue-800
                                                @elseif(strtolower($subject->level) == 'junior') bg-purple-100 text-purple-800
                                                @elseif(strtolower($subject->level) == 'senior') bg-pink-100 text-pink-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ ucfirst($subject->level ?? 'N/A') }}
                                            </span>
                                        </td>

                                        <td class="px-3 py-3 text-sm text-center text-gray-600">{{ $subject->weekly_hours }}</td>

                                        <td class="px-3 py-3 text-center">
                                            <span class="px-2 py-1 text-xs rounded-full {{ $subject->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ $subject->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>

                                        <td class="px-3 py-3 text-center text-sm whitespace-nowrap">
                                            <a href="{{ route('admin.subjects.show', $subject) }}" class="text-blue-600 hover:text-blue-900 mr-1">View</a>
                                            <a href="{{ route('admin.subjects.edit', $subject) }}" class="text-emerald-600 hover:text-emerald-900 mr-1">Edit</a>
                                            <button onclick="toggleStatus({{ $subject->id }})"
                                                class="text-{{ $subject->is_active ? 'yellow' : 'green' }}-600 hover:text-{{ $subject->is_active ? 'yellow' : 'green' }}-800 mr-1">
                                                {{ $subject->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                            <form action="{{ route('admin.subjects.destroy', $subject) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('Delete this subject?')"
                                                    class="text-red-600 hover:text-red-900">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                            <span class="text-4xl block mb-2">📚</span>
                                            No subjects found.
                                            <br>
                                            <a href="{{ route('admin.subjects.create') }}" class="text-blue-600 hover:underline">Create your first subject</a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-4">
                        {{ $subjects->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- JavaScript --}}
    <script>
        // ==========================================
        // SELECT ALL CHECKBOX
        // ==========================================
        document.addEventListener('DOMContentLoaded', function() {
            const selectAll = document.getElementById('select-all');
            const selectAllTable = document.getElementById('select-all-table');
            const checkboxes = document.querySelectorAll('.subject-checkbox');
            const bulkActions = document.getElementById('bulk-actions');

            function updateBulkActions() {
                const checked = document.querySelectorAll('.subject-checkbox:checked').length;
                bulkActions.style.display = checked > 0 ? 'flex' : 'none';
            }

            function toggleAll(source) {
                checkboxes.forEach(cb => {
                    cb.checked = source.checked;
                });
                updateBulkActions();
            }

            if (selectAll) {
                selectAll.addEventListener('change', function() {
                    toggleAll(this);
                    if (selectAllTable) selectAllTable.checked = this.checked;
                });
            }

            if (selectAllTable) {
                selectAllTable.addEventListener('change', function() {
                    toggleAll(this);
                    if (selectAll) selectAll.checked = this.checked;
                });
            }

            checkboxes.forEach(cb => {
                cb.addEventListener('change', updateBulkActions);
            });

            updateBulkActions();
        });

        // ==========================================
        // TOGGLE STATUS
        // ==========================================
        function toggleStatus(id) {
            if (confirm('Toggle subject status?')) {
                fetch(`/admin/subjects/${id}/toggle-status`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }).then(response => {
                    if (response.ok) {
                        window.location.reload();
                    } else {
                        alert('Failed to toggle status. Please try again.');
                    }
                }).catch(() => {
                    alert('Network error. Please try again.');
                });
            }
        }

        // ==========================================
        // BULK ACTIONS
        // ==========================================
        function bulkAction(action) {
            const ids = [];
            document.querySelectorAll('.subject-checkbox:checked').forEach(cb => {
                ids.push(cb.value);
            });

            if (ids.length === 0) {
                alert('Please select at least one subject.');
                return;
            }

            const messages = {
                activate: 'Activate selected subjects?',
                deactivate: 'Deactivate selected subjects?',
                delete: 'Delete selected subjects? This cannot be undone.'
            };

            if (!confirm(messages[action])) return;

            fetch(`/admin/subjects/bulk-action`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    subject_ids: ids,
                    action: action
                })
            }).then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message || 'Action failed. Please try again.');
                }
            }).catch(() => {
                alert('Network error. Please try again.');
            });
        }
    </script>

    <style>
        .subject-checkbox:checked {
            background-color: #4f46e5;
            border-color: #4f46e5;
        }
    </style>
</x-app-layout>
