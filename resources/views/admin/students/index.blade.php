<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Students') }}
            </h2>
            @can('create_students')
                <a href="{{ route('admin.students.create') }}"
                   style="display: inline-flex; align-items: center; padding: 8px 20px; font-size: 14px; font-weight: 600; color: #ffffff; background-color: #4f46e5; border: none; border-radius: 8px; box-shadow: 0 2px 4px rgba(79, 70, 229, 0.3); text-decoration: none; transition: all 0.25s ease; transform: scale(1); cursor: pointer;"
                   onmouseover="this.style.backgroundColor='#4338ca'; this.style.boxShadow='0 4px 12px rgba(79, 70, 229, 0.4)'; this.style.transform='scale(1.02)'"
                   onmouseout="this.style.backgroundColor='#4f46e5'; this.style.boxShadow='0 2px 4px rgba(79, 70, 229, 0.3)'; this.style.transform='scale(1)'"
                   onmousedown="this.style.transform='scale(0.97)'"
                   onmouseup="this.style.transform='scale(1.02)'">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: inline-block; vertical-align: middle;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add Student
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

                    <!-- Filters -->
                    <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div>
                            <input type="text" name="search" placeholder="Search students..."
                                   value="{{ request('search') }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        </div>
                        <div>
                            <select name="class_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                <option value="">All Classes</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <button type="submit" class="w-full bg-gray-800 text-white py-2 px-4 rounded-md hover:bg-gray-700 transition">
                                🔍 Filter
                            </button>
                        </div>
                    </form>

                    <!-- Students Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Admission #</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Class</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Parent</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($students as $student)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-4 py-3">
                                            <div class="flex items-center">
                                                <!-- Clickable Photo with Modal -->
                                                <div class="cursor-pointer group relative"
                                                     onclick="openPhotoModal('{{ $student->photo_url }}', '{{ $student->full_name }}')">
                                                    <img src="{{ $student->photo_url }}"
                                                         alt="{{ $student->full_name }}"
                                                         class="h-12 w-12 rounded-full object-cover border-2 border-gray-200 group-hover:border-indigo-500 transition duration-200">
                                                    <!-- Magnifying glass overlay on hover -->
                                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 rounded-full transition duration-200 flex items-center justify-center">
                                                        <svg class="w-6 h-6 text-white opacity-0 group-hover:opacity-100 transition duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                                        </svg>
                                                    </div>
                                                    <p class="text-xs text-center text-blue-600 mt-0.5 opacity-0 group-hover:opacity-100 transition duration-200">Click to enlarge</p>
                                                </div>

                                                <div class="ml-3">
                                                    <div class="font-medium text-gray-900">{{ $student->full_name }}</div>
                                                    <div class="text-sm text-gray-500">{{ $student->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $student->admission_number }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $student->class->name ?? 'N/A' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $student->parent_name }}</td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="px-2 py-1 text-xs rounded-full
                                                {{ $student->status == 'active' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                                {{ $student->status == 'graduated' ? 'bg-blue-100 text-blue-800' : '' }}
                                                {{ $student->status == 'transferred' ? 'bg-amber-100 text-amber-800' : '' }}
                                                {{ $student->status == 'suspended' ? 'bg-rose-100 text-rose-800' : '' }}">
                                                {{ ucfirst($student->status) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center text-sm">
                                            <a href="{{ route('admin.students.show', $student) }}"
                                               class="text-blue-600 hover:text-blue-900 mr-2">View</a>
                                            <a href="{{ route('admin.students.edit', $student) }}"
                                               class="text-emerald-600 hover:text-emerald-900 mr-2">Edit</a>
                                            <form action="{{ route('admin.students.destroy', $student) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('Are you sure?')"
                                                        class="text-rose-600 hover:text-rose-900">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                            <span class="text-4xl block mb-2">👨‍🎓</span>
                                            No students found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $students->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ==========================================================
    PHOTO MODAL
    ========================================================== -->
    <div id="photoModal"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 hidden"
         onclick="closePhotoModal(event)">
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-2xl w-full mx-4 p-4 transform scale-95 transition-transform duration-300"
             onclick="event.stopPropagation()">
            <button onclick="closePhotoModal()"
                    class="absolute -top-3 -right-3 bg-red-500 hover:bg-red-600 text-white rounded-full w-10 h-10 flex items-center justify-center shadow-lg transition duration-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            <div class="text-center">
                <img id="modalImage"
                     src=""
                     alt="Student Photo"
                     class="max-h-[70vh] w-auto mx-auto rounded-lg shadow-md">
                <div id="modalCaption" class="mt-4 text-lg font-semibold text-gray-800"></div>
                <p class="text-sm text-gray-500 mt-1">Click anywhere outside to close</p>
            </div>
        </div>
    </div>

    <!-- ==========================================================
    SCRIPTS
    ========================================================== -->
    <script>
        function openPhotoModal(imageUrl, studentName) {
            console.log('Opening modal for:', studentName);
            var modal = document.getElementById('photoModal');
            var image = document.getElementById('modalImage');
            var caption = document.getElementById('modalCaption');

            if (!modal || !image || !caption) {
                console.error('Modal elements not found!');
                return;
            }

            image.src = imageUrl;
            caption.textContent = studentName;

            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');

            setTimeout(function() {
                var content = modal.querySelector('.relative');
                if (content) {
                    content.classList.remove('scale-95');
                    content.classList.add('scale-100');
                }
            }, 10);
        }

        function closePhotoModal(event) {
            console.log('Closing modal');
            var modal = document.getElementById('photoModal');
            if (!modal) return;

            var content = modal.querySelector('.relative');

            if (event && event.target !== event.currentTarget) {
                return;
            }

            if (content) {
                content.classList.remove('scale-100');
                content.classList.add('scale-95');
            }

            setTimeout(function() {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }, 300);
        }

        // Close modal with ESC key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                var modal = document.getElementById('photoModal');
                if (modal && !modal.classList.contains('hidden')) {
                    closePhotoModal(event);
                }
            }
        });

        console.log('Photo modal ready on index page!');
    </script>

    <style>
        #photoModal {
            transition: opacity 0.3s ease;
        }
        #photoModal .relative {
            transition: transform 0.3s ease;
        }
        #photoModal img {
            max-height: 70vh;
            object-fit: contain;
        }
        .group:hover .group-hover\:border-indigo-500 {
            border-color: #6366f1;
        }
    </style>
</x-app-layout>
