<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <!-- Clickable Photo -->
                <div class="cursor-pointer group" onclick="openPhotoModal('{{ $student->photo_url }}', '{{ $student->full_name }}')">
                    <img src="{{ $student->photo_url }}"
                         alt="{{ $student->full_name }}"
                         class="h-16 w-16 rounded-full object-cover border-2 border-gray-200 group-hover:border-indigo-500 transition duration-200">
                    <p class="text-xs text-center text-blue-600 mt-1 hover:underline">Click to enlarge</p>
                </div>
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ $student->full_name }}
                    </h2>
                    <p class="text-sm text-gray-500">{{ $student->admission_number }}</p>
                </div>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('admin.students.edit', $student) }}"
                   class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg text-sm">✏️ Edit</a>
                <a href="{{ route('admin.students.index') }}"
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg text-sm">← Back</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="font-semibold text-gray-700 mb-3">Personal Information</h3>
                            <dl class="space-y-2 text-sm">
                                <div class="flex justify-between border-b py-2">
                                    <dt class="text-gray-500">Full Name</dt>
                                    <dd class="font-medium">{{ $student->full_name }}</dd>
                                </div>
                                <div class="flex justify-between border-b py-2">
                                    <dt class="text-gray-500">Date of Birth</dt>
                                    <dd>{{ $student->date_of_birth->format('M d, Y') }}</dd>
                                </div>
                                <div class="flex justify-between border-b py-2">
                                    <dt class="text-gray-500">Gender</dt>
                                    <dd>{{ ucfirst($student->gender) }}</dd>
                                </div>
                                <div class="flex justify-between border-b py-2">
                                    <dt class="text-gray-500">Email</dt>
                                    <dd>{{ $student->email ?? 'N/A' }}</dd>
                                </div>
                                <div class="flex justify-between border-b py-2">
                                    <dt class="text-gray-500">Phone</dt>
                                    <dd>{{ $student->phone ?? 'N/A' }}</dd>
                                </div>
                                <div class="flex justify-between border-b py-2">
                                    <dt class="text-gray-500">Address</dt>
                                    <dd>{{ $student->address ?? 'N/A' }}</dd>
                                </div>
                            </dl>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-700 mb-3">Academic Information</h3>
                            <dl class="space-y-2 text-sm">
                                <div class="flex justify-between border-b py-2">
                                    <dt class="text-gray-500">Admission Number</dt>
                                    <dd class="font-mono">{{ $student->admission_number }}</dd>
                                </div>
                                <div class="flex justify-between border-b py-2">
                                    <dt class="text-gray-500">Class</dt>
                                    <dd>{{ $student->class->name ?? 'Not Assigned' }}</dd>
                                </div>
                                <div class="flex justify-between border-b py-2">
                                    <dt class="text-gray-500">Class Arm</dt>
                                    <dd>{{ $student->class_arm ?? 'N/A' }}</dd>
                                </div>
                                <div class="flex justify-between border-b py-2">
                                    <dt class="text-gray-500">Academic Year</dt>
                                    <dd>{{ $student->academicYear->name ?? 'N/A' }}</dd>
                                </div>
                                <div class="flex justify-between border-b py-2">
                                    <dt class="text-gray-500">Status</dt>
                                    <dd>
                                        <span class="px-2 py-1 text-xs rounded-full
                                            {{ $student->status == 'active' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                            {{ $student->status == 'graduated' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $student->status == 'transferred' ? 'bg-amber-100 text-amber-800' : '' }}
                                            {{ $student->status == 'suspended' ? 'bg-rose-100 text-rose-800' : '' }}">
                                            {{ ucfirst($student->status) }}
                                        </span>
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h3 class="font-semibold text-gray-700 mb-3">Parent/Guardian Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div><span class="text-gray-500">Name:</span> {{ $student->parent_name }}</div>
                            <div><span class="text-gray-500">Phone:</span> {{ $student->parent_phone }}</div>
                            <div><span class="text-gray-500">Email:</span> {{ $student->parent_email ?? 'N/A' }}</div>
                        </div>
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
    SCRIPTS - Placed at the bottom, after the modal
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

        console.log('Photo modal ready!');
    </script>
</x-app-layout>
