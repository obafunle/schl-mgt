<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __('Staff Management') }}
            </h2>
            @can('create_staff')
                <a href="{{ route('admin.staff.create') }}"
                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-md hover:shadow-lg transition duration-200">
                    + Add Staff
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    {{-- Stats --}}
                    <div class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-4">
                        <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                            <span class="text-sm text-gray-500">Total Staff</span>
                            <p class="text-2xl font-bold text-blue-600">{{ $staff->total() }}</p>
                        </div>
                        <div class="p-4 bg-green-50 rounded-lg border border-green-200">
                            <span class="text-sm text-gray-500">Active</span>
                            <p class="text-2xl font-bold text-green-600">{{ $staff->where('status', 'active')->count() }}</p>
                        </div>
                        <div class="p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                            <span class="text-sm text-gray-500">Teachers</span>
                            <p class="text-2xl font-bold text-yellow-600">{{ $staff->where('staff_type', 'teacher')->count() }}</p>
                        </div>
                        <div class="p-4 bg-purple-50 rounded-lg border border-purple-200">
                            <span class="text-sm text-gray-500">Admin</span>
                            <p class="text-2xl font-bold text-purple-600">{{ $staff->where('staff_type', 'admin')->count() }}</p>
                        </div>
                    </div>

                    {{-- Filters --}}
                    <form method="GET" class="grid grid-cols-1 gap-4 mb-6 md:grid-cols-4">
                        <div>
                            <input type="text"
                                   name="search"
                                   placeholder="Search staff..."
                                   value="{{ request('search') }}"
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        </div>
                        <div>
                            <select name="staff_type" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">All Types</option>
                                @foreach($staffTypes as $type)
                                    <option value="{{ $type }}" {{ request('staff_type') == $type ? 'selected' : '' }}>
                                        {{ ucfirst($type) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <select name="status" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">All Status</option>
                                @foreach($statuses as $status)
                                    <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                        {{ ucfirst($status) }}
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

                    {{-- Staff Table --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Staff</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($staff as $member)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-4 py-3">
                                            <div class="flex items-center">
                                                {{-- Photo with click to open modal --}}
                                                <div class="cursor-pointer group relative" onclick="openPhotoModal('{{ $member->photo_url }}', '{{ $member->full_name }}')">
                                                    <img src="{{ $member->photo_url }}"
                                                         alt="{{ $member->full_name }}"
                                                         class="object-cover w-10 h-10 rounded-full border-2 border-gray-200 group-hover:border-indigo-500 transition">
                                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 rounded-full transition duration-200 flex items-center justify-center">
                                                        <svg class="w-5 h-5 text-white opacity-0 group-hover:opacity-100 transition duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                                        </svg>
                                                    </div>
                                                </div>
                                                <div class="ml-3">
                                                    <div class="font-medium text-gray-900">{{ $member->full_name }}</div>
                                                    <div class="text-sm text-gray-500">{{ $member->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-500">{{ $member->staff_id }}</td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 text-xs rounded-full
                                                {{ $member->staff_type == 'teacher' ? 'bg-blue-100 text-blue-800' : '' }}
                                                {{ $member->staff_type == 'admin' ? 'bg-purple-100 text-purple-800' : '' }}
                                                {{ $member->staff_type == 'accountant' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $member->staff_type == 'support' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $member->staff_type == 'librarian' ? 'bg-pink-100 text-pink-800' : '' }}">
                                                {{ $member->getStaffTypeLabel() }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-500">
                                            <div>{{ $member->phone ?? 'N/A' }}</div>
                                            <div class="text-xs text-gray-400">{{ $member->subjects->count() }} subjects</div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="px-2 py-1 text-xs rounded-full
                                                {{ $member->status == 'active' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $member->status == 'inactive' ? 'bg-gray-100 text-gray-800' : '' }}
                                                {{ $member->status == 'suspended' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $member->status == 'terminated' ? 'bg-red-100 text-red-800' : '' }}">
                                                {{ ucfirst($member->status) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center text-sm">
                                            <a href="{{ route('admin.staff.show', $member) }}" class="text-blue-600 hover:text-blue-900 mr-2">View</a>
                                            <a href="{{ route('admin.staff.edit', $member) }}" class="text-green-600 hover:text-green-900 mr-2">Edit</a>
                                            <form action="{{ route('admin.staff.destroy', $member) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('Delete this staff?')" class="text-red-600 hover:text-red-900">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">No staff found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $staff->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Photo Modal --}}
    <div id="photoModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 hidden" onclick="closePhotoModal(event)">
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-2xl w-full mx-4 p-4 transform scale-95 transition-transform duration-300" onclick="event.stopPropagation()">
            <button onclick="closePhotoModal()" class="absolute -top-3 -right-3 bg-red-500 hover:bg-red-600 text-white rounded-full w-10 h-10 flex items-center justify-center shadow-lg transition duration-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            <div class="text-center">
                <img id="modalImage" src="" alt="Staff Photo" class="max-h-[70vh] w-auto mx-auto rounded-lg shadow-md">
                <div id="modalCaption" class="mt-4 text-lg font-semibold text-gray-800"></div>
                <p class="text-sm text-gray-500 mt-1">Click anywhere outside to close</p>
            </div>
        </div>
    </div>

    <script>
        function openPhotoModal(imageUrl, staffName) {
            document.getElementById('modalImage').src = imageUrl;
            document.getElementById('modalCaption').textContent = staffName;
            document.getElementById('photoModal').classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
            setTimeout(function() {
                var content = document.querySelector('#photoModal .relative');
                if (content) {
                    content.classList.remove('scale-95');
                    content.classList.add('scale-100');
                }
            }, 10);
        }

        function closePhotoModal(event) {
            var modal = document.getElementById('photoModal');
            if (event && event.target !== event.currentTarget) return;
            var content = modal.querySelector('.relative');
            if (content) {
                content.classList.remove('scale-100');
                content.classList.add('scale-95');
            }
            setTimeout(function() {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }, 300);
        }

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                var modal = document.getElementById('photoModal');
                if (modal && !modal.classList.contains('hidden')) {
                    closePhotoModal(event);
                }
            }
        });
    </script>
</x-app-layout>
