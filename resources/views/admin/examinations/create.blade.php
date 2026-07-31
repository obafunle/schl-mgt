<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create New Examination') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('admin.examinations.store') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Class -->
                            <div>
                                <label for="class_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    Class <span class="text-red-500">*</span>
                                </label>
                                <select id="class_id" name="class_id" required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                    <option value="">Select Class</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                            {{ $class->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Class Arm -->
                            <div>
                                <label for="class_arm_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    Class Arm <span class="text-red-500">*</span>
                                </label>
                                <select id="class_arm_id" name="class_arm_id" required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                    <option value="">Select Arm</option>
                                </select>
                            </div>

                            <!-- Subject -->
                            <div>
                                <label for="subject_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    Subject <span class="text-red-500">*</span>
                                </label>
                                <select id="subject_id" name="subject_id" required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                    <option value="">Select Subject</option>
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                            {{ $subject->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Exam Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                    Exam Name
                                </label>
                                <input type="text" id="name" name="name" value="{{ old('name') }}"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300"
                                       placeholder="e.g., First Term Examination">
                            </div>

                            <!-- Term -->
                            <div>
                                <label for="term_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    Term <span class="text-red-500">*</span>
                                </label>
                                <select id="term_id" name="term_id" required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                    <option value="">Select Term</option>
                                    @foreach($terms as $term)
                                        <option value="{{ $term->id }}" {{ old('term_id') == $term->id ? 'selected' : '' }}>
                                            {{ $term->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Academic Year -->
                            <div>
                                <label for="academic_year_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    Academic Year <span class="text-red-500">*</span>
                                </label>
                                <select id="academic_year_id" name="academic_year_id" required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                    <option value="">Select Academic Year</option>
                                    @foreach($academicYears as $year)
                                        <option value="{{ $year->id }}" {{ old('academic_year_id') == $year->id ? 'selected' : '' }}>
                                            {{ $year->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Exam Date -->
                            <div>
                                <label for="exam_date" class="block text-sm font-medium text-gray-700 mb-1">
                                    Exam Date <span class="text-red-500">*</span>
                                </label>
                                <input type="date" id="exam_date" name="exam_date" value="{{ old('exam_date') }}" required
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                            </div>

                            <!-- CA Deadline -->
                            <div>
                                <label for="ca_deadline" class="block text-sm font-medium text-gray-700 mb-1">
                                    CA Deadline
                                </label>
                                <input type="date" id="ca_deadline" name="ca_deadline" value="{{ old('ca_deadline') }}"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                            </div>

                            <!-- CA Weight -->
                            <div>
                                <label for="ca_weight" class="block text-sm font-medium text-gray-700 mb-1">
                                    CA Weight (%) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" id="ca_weight" name="ca_weight" value="{{ old('ca_weight', 40) }}" required
                                       min="0" max="100"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                            </div>

                            <!-- Exam Weight -->
                            <div>
                                <label for="exam_weight" class="block text-sm font-medium text-gray-700 mb-1">
                                    Exam Weight (%) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" id="exam_weight" name="exam_weight" value="{{ old('exam_weight', 60) }}" required
                                       min="0" max="100"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                            </div>

                            <!-- Total Marks -->
                            <div>
                                <label for="total_marks" class="block text-sm font-medium text-gray-700 mb-1">
                                    Total Marks
                                </label>
                                <input type="number" id="total_marks" name="total_marks" value="{{ old('total_marks', 100) }}"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                            </div>
                        </div>

                        <!-- Submit -->
                        <div class="mt-6 flex justify-end space-x-3">
                            <a href="{{ route('admin.examinations.index') }}" 
                               class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                Create Examination
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