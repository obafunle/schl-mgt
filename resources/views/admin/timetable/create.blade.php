<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Timetable Entry') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.timetable.store') }}" method="POST">
                        @csrf

                        <!-- Class and Arm -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div>
                                <label for="class_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    Class <span class="text-red-500">*</span>
                                </label>
                                <select id="class_id" name="class_id" required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                    <option value="">Select Class</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                            {{ $class->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="class_arm_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    Class Arm
                                </label>
                                <select id="class_arm_id" name="class_arm_id"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                    <option value="">Select Arm</option>
                                </select>
                            </div>
                        </div>

                        <!-- Subject, Teacher, Room -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <div>
                                <label for="subject_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    Subject <span class="text-red-500">*</span>
                                </label>
                                <select id="subject_id" name="subject_id" required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                    <option value="">Select Subject</option>
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="teacher_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    Teacher <span class="text-red-500">*</span>
                                </label>
                                <select id="teacher_id" name="teacher_id" required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                    <option value="">Select Teacher</option>
                                    @foreach($teachers as $teacher)
                                        <option value="{{ $teacher->id }}">{{ $teacher->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="room_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    Room
                                </label>
                                <select id="room_id" name="room_id"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                    <option value="">Select Room</option>
                                    @foreach($rooms as $room)
                                        <option value="{{ $room->id }}">{{ $room->name }} ({{ $room->code }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Day, Period, Term, Academic Year -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                            <div>
                                <label for="day_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    Day <span class="text-red-500">*</span>
                                </label>
                                <select id="day_id" name="day_id" required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                    <option value="">Select Day</option>
                                    @foreach($days as $day)
                                        <option value="{{ $day->id }}">{{ $day->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="period_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    Period <span class="text-red-500">*</span>
                                </label>
                                <select id="period_id" name="period_id" required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                    <option value="">Select Period</option>
                                    @foreach($periods as $period)
                                        <option value="{{ $period->id }}">{{ $period->name }} ({{ $period->getTimeRange() }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="term_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    Term <span class="text-red-500">*</span>
                                </label>
                                <select id="term_id" name="term_id" required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                    <option value="">Select Term</option>
                                    @foreach($terms as $term)
                                        <option value="{{ $term->id }}">{{ $term->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="academic_year_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    Academic Year <span class="text-red-500">*</span>
                                </label>
                                <select id="academic_year_id" name="academic_year_id" required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                    <option value="">Select Year</option>
                                    @foreach($academicYears as $year)
                                        <option value="{{ $year->id }}">{{ $year->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Notes and Recurring -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                                    Notes
                                </label>
                                <textarea id="notes" name="notes" rows="2" 
                                          class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300"
                                          placeholder="Optional notes..."></textarea>
                            </div>
                            <div class="flex items-end">
                                <div class="flex items-center">
                                    <input type="checkbox" id="is_recurring" name="is_recurring" value="1" checked
                                           class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300">
                                    <label for="is_recurring" class="ml-2 text-sm text-gray-700">
                                        Recurring (weekly)
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Submit -->
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('admin.timetable.index') }}" 
                               class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                Create Entry
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Dynamic class arms based on class selection
        document.getElementById('class_id').addEventListener('change', function() {
            const classId = this.value;
            const armSelect = document.getElementById('class_arm_id');
            
            if (classId) {
                fetch(`/admin/get-class-arms?class_id=${classId}`)
                    .then(response => response.json())
                    .then(data => {
                        armSelect.innerHTML = '<option value="">Select Arm</option>';
                        data.forEach(arm => {
                            armSelect.innerHTML += `<option value="${arm.id}">${arm.name}</option>`;
                        });
                    });
            } else {
                armSelect.innerHTML = '<option value="">Select Arm</option>';
            }
        });
    </script>
    @endpush
</x-app-layout>