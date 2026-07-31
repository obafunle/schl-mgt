<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Staff') }}: {{ $staff->full_name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('admin.staff.update', $staff) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Personal Information -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-700 border-b pb-2 mb-4">👤 Personal Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">
                                        First Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="first_name" name="first_name" value="{{ old('first_name', $staff->first_name) }}"
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300" required>
                                </div>
                                <div>
                                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">
                                        Last Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="last_name" name="last_name" value="{{ old('last_name', $staff->last_name) }}"
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300" required>
                                </div>
                                <div>
                                    <label for="middle_name" class="block text-sm font-medium text-gray-700 mb-1">
                                        Middle Name
                                    </label>
                                    <input type="text" id="middle_name" name="middle_name" value="{{ old('middle_name', $staff->middle_name) }}"
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                </div>
                                <div>
                                    <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-1">
                                        Date of Birth <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" id="date_of_birth" name="date_of_birth"
                                           value="{{ old('date_of_birth', $staff->date_of_birth->format('Y-m-d')) }}" required
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                </div>
                                <div>
                                    <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">
                                        Gender <span class="text-red-500">*</span>
                                    </label>
                                    <select id="gender" name="gender" required
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                        <option value="male" {{ old('gender', $staff->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender', $staff->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="photo" class="block text-sm font-medium text-gray-700 mb-1">
                                        Passport Photo
                                    </label>
                                    @if($staff->photo)
                                        <div class="mb-2">
                                            <img src="{{ $staff->photo_url }}" alt="{{ $staff->full_name }}"
                                                 class="h-16 w-16 rounded-full object-cover">
                                        </div>
                                    @endif
                                    <input type="file" id="photo" name="photo" accept="image/*"
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                    <p class="text-xs text-gray-500 mt-1">Leave blank to keep current photo. Max 2MB.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-700 border-b pb-2 mb-4">📞 Contact Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                        Email Address <span class="text-red-500">*</span>
                                    </label>
                                    <input type="email" id="email" name="email" value="{{ old('email', $staff->email) }}"
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300" required>
                                </div>
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                                        Phone Number
                                    </label>
                                    <input type="tel" id="phone" name="phone" value="{{ old('phone', $staff->phone) }}"
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                </div>
                                <div class="md:col-span-2">
                                    <label for="address" class="block text-sm font-medium text-gray-700 mb-1">
                                        Address
                                    </label>
                                    <textarea id="address" name="address" rows="2"
                                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">{{ old('address', $staff->address) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Employment Details -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-700 border-b pb-2 mb-4">💼 Employment Details</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="staff_type" class="block text-sm font-medium text-gray-700 mb-1">
                                        Staff Type <span class="text-red-500">*</span>
                                    </label>
                                    <select id="staff_type" name="staff_type" required
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300"
                                            onchange="toggleTeacherFields()">
                                        <option value="teacher" {{ old('staff_type', $staff->staff_type) == 'teacher' ? 'selected' : '' }}>Teacher</option>
                                        <option value="admin" {{ old('staff_type', $staff->staff_type) == 'admin' ? 'selected' : '' }}>Administrator</option>
                                        <option value="accountant" {{ old('staff_type', $staff->staff_type) == 'accountant' ? 'selected' : '' }}>Accountant</option>
                                        <option value="librarian" {{ old('staff_type', $staff->staff_type) == 'librarian' ? 'selected' : '' }}>Librarian</option>
                                        <option value="support" {{ old('staff_type', $staff->staff_type) == 'support' ? 'selected' : '' }}>Support Staff</option>
                                        <option value="other" {{ old('staff_type', $staff->staff_type) == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="employment_type" class="block text-sm font-medium text-gray-700 mb-1">
                                        Employment Type <span class="text-red-500">*</span>
                                    </label>
                                    <select id="employment_type" name="employment_type" required
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                        <option value="permanent" {{ old('employment_type', $staff->employment_type) == 'permanent' ? 'selected' : '' }}>Permanent</option>
                                        <option value="contract" {{ old('employment_type', $staff->employment_type) == 'contract' ? 'selected' : '' }}>Contract</option>
                                        <option value="part-time" {{ old('employment_type', $staff->employment_type) == 'part-time' ? 'selected' : '' }}>Part-Time</option>
                                        <option value="intern" {{ old('employment_type', $staff->employment_type) == 'intern' ? 'selected' : '' }}>Intern</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="hire_date" class="block text-sm font-medium text-gray-700 mb-1">
                                        Hire Date <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" id="hire_date" name="hire_date"
                                           value="{{ old('hire_date', $staff->hire_date->format('Y-m-d')) }}" required
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                </div>
                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                                        Status <span class="text-red-500">*</span>
                                    </label>
                                    <select id="status" name="status" required
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                        <option value="active" {{ old('status', $staff->status) == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status', $staff->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        <option value="suspended" {{ old('status', $staff->status) == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                        <option value="terminated" {{ old('status', $staff->status) == 'terminated' ? 'selected' : '' }}>Terminated</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="termination_date" class="block text-sm font-medium text-gray-700 mb-1">
                                        Termination Date
                                    </label>
                                    <input type="date" id="termination_date" name="termination_date"
                                           value="{{ old('termination_date', $staff->termination_date?->format('Y-m-d')) }}"
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                </div>
                                <div>
                                    <label for="basic_salary" class="block text-sm font-medium text-gray-700 mb-1">
                                        Basic Salary (₦)
                                    </label>
                                    <input type="number" id="basic_salary" name="basic_salary"
                                           value="{{ old('basic_salary', $staff->basic_salary) }}" step="0.01" min="0"
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                </div>
                            </div>
                        </div>

                        <!-- Teacher Specific - Subjects -->
                        <div class="mb-8" id="teacher-fields" style="{{ $staff->staff_type === 'teacher' ? 'display:block' : 'display:none' }}">
                            <h3 class="text-lg font-semibold text-gray-700 border-b pb-2 mb-4">📚 Subject Assignments</h3>
                            <div id="subjects-container" class="space-y-3">
                                @foreach($assignedSubjects as $index => $assigned)
                                    <div class="subject-row flex items-center space-x-3 p-3 bg-gray-50 rounded-lg border border-gray-200" id="subject-{{ $index + 1000 }}">
                                        <div class="flex-1">
                                            <select name="subjects[{{ $index + 1000 }}][subject_id]" required
                                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                                <option value="">Select Subject</option>
                                                @foreach($subjects as $subject)
                                                    <option value="{{ $subject->id }}"
                                                        {{ $assigned->subject_id == $subject->id ? 'selected' : '' }}>
                                                        {{ $subject->name }} ({{ $subject->code }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="w-40">
                                            <select name="subjects[{{ $index + 1000 }}][role]"
                                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                                <option value="primary" {{ $assigned->role == 'primary' ? 'selected' : '' }}>Primary</option>
                                                <option value="secondary" {{ $assigned->role == 'secondary' ? 'selected' : '' }}>Secondary</option>
                                                <option value="assistant" {{ $assigned->role == 'assistant' ? 'selected' : '' }}>Assistant</option>
                                            </select>
                                        </div>
                                        <div class="w-32">
                                            <input type="number" name="subjects[{{ $index + 1000 }}][weekly_hours]"
                                                   value="{{ $assigned->weekly_hours }}" min="1" max="40"
                                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                        </div>
                                        <button type="button" onclick="removeSubject({{ $index + 1000 }})"
                                                class="text-red-500 hover:text-red-700">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" onclick="addSubject()"
                                    class="mt-2 bg-green-500 hover:bg-green-700 text-white text-sm font-bold py-1 px-3 rounded">
                                + Add Subject
                            </button>
                        </div>

                        <!-- Bank Details -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-700 border-b pb-2 mb-4">🏦 Bank Details</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="bank_name" class="block text-sm font-medium text-gray-700 mb-1">
                                        Bank Name
                                    </label>
                                    <input type="text" id="bank_name" name="bank_name"
                                           value="{{ old('bank_name', $staff->bank_name) }}"
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                </div>
                                <div>
                                    <label for="bank_account_number" class="block text-sm font-medium text-gray-700 mb-1">
                                        Account Number
                                    </label>
                                    <input type="text" id="bank_account_number" name="bank_account_number"
                                           value="{{ old('bank_account_number', $staff->bank_account_number) }}"
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                </div>
                                <div class="md:col-span-2">
                                    <label for="bank_account_name" class="block text-sm font-medium text-gray-700 mb-1">
                                        Account Name
                                    </label>
                                    <input type="text" id="bank_account_name" name="bank_account_name"
                                           value="{{ old('bank_account_name', $staff->bank_account_name) }}"
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                </div>
                            </div>
                        </div>

                        <!-- Next of Kin (Fixed: uses correct field names) -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-700 border-b pb-2 mb-4">👨‍👩‍👦 Next of Kin</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="next_of_kin_name" class="block text-sm font-medium text-gray-700 mb-1">
                                        Name
                                    </label>
                                    <input type="text" id="next_of_kin_name" name="next_of_kin_name"
                                        value="{{ old('next_of_kin_name', $staff->next_of_kin_name ?? '') }}"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                </div>
                                <div>
                                    <label for="next_of_kin_phone" class="block text-sm font-medium text-gray-700 mb-1">
                                        Phone Number
                                    </label>
                                    <input type="text" id="next_of_kin_phone" name="next_of_kin_phone"
                                        value="{{ old('next_of_kin_phone', $staff->next_of_kin_phone ?? '') }}"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                </div>
                                <div>
                                    <label for="next_of_kin_relationship" class="block text-sm font-medium text-gray-700 mb-1">
                                        Relationship
                                    </label>
                                    <input type="text" id="next_of_kin_relationship" name="next_of_kin_relationship"
                                        value="{{ old('next_of_kin_relationship', $staff->next_of_kin_relationship ?? '') }}"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                </div>
                            </div>
                        </div>

                        <!-- Password Section (NEW - with Eye Icon) -->
{{-- PasswordSection-Onlyshowifstaffhasauseraccount --}}
    @if($staff->user)
        <div class="mb-8">
            <h3 class="text-lg font-semibold text-gray-700 border-b pb-2 mb-4">🔐 Password</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-password-input
                        name="password"
                        label="New Password"
                        placeholder="Leave blank to keep current password"
                        autocomplete="new-password"
                    />
                    <p class="mt-1 text-xs text-gray-500">Leave blank to keep current password. Min 8 characters.</p>
                </div>
                <div>
                    <x-password-input
                        name="password_confirmation"
                        label="Confirm New Password"
                        placeholder="Re-enter new password"
                        autocomplete="new-password"
                    />
                </div>
            </div>
        </div>
    @else
    {{-- Show a message that this staff doesn't have a user account --}}
    <div class="mb-8">
        <h3 class="text-lg font-semibold text-gray-700 border-b pb-2 mb-4">🔐 User Account</h3>
        <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
            <p class="text-sm text-yellow-700">
                ⚠️ This staff member does not have a user account.
                <a href="{{ route('admin.staff.edit', $staff) }}#create-account" class="text-blue-600 hover:underline">Create account?</a>
            </p>
        </div>
    </div>
@endif

                        <!-- Submit -->
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('admin.staff.index') }}"
                               class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                Cancel
                            </a>
                            <button type="submit"
                                    class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                Update Staff
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let subjectCount = {{ $assignedSubjects->count() + 1000 }};

        function toggleTeacherFields() {
            const staffType = document.getElementById('staff_type').value;
            const teacherFields = document.getElementById('teacher-fields');
            if (staffType === 'teacher') {
                teacherFields.style.display = 'block';
            } else {
                teacherFields.style.display = 'none';
            }
        }

        function addSubject() {
            subjectCount++;
            const container = document.getElementById('subjects-container');

            const div = document.createElement('div');
            div.className = 'subject-row flex items-center space-x-3 p-3 bg-gray-50 rounded-lg border border-gray-200';
            div.id = 'subject-' + subjectCount;

            div.innerHTML = `
                <div class="flex-1">
                    <select name="subjects[${subjectCount}][subject_id]" required
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                        <option value="">Select Subject</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }} ({{ $subject->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-40">
                    <select name="subjects[${subjectCount}][role]"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                        <option value="primary">Primary</option>
                        <option value="secondary">Secondary</option>
                        <option value="assistant">Assistant</option>
                    </select>
                </div>
                <div class="w-32">
                    <input type="number" name="subjects[${subjectCount}][weekly_hours]"
                           placeholder="Hours" value="4" min="1" max="40"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                </div>
                <button type="button" onclick="removeSubject(${subjectCount})"
                        class="text-red-500 hover:text-red-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            `;

            container.appendChild(div);
        }

        function removeSubject(id) {
            const item = document.getElementById('subject-' + id);
            if (item) {
                item.remove();
            }
        }
    </script>
    @endpush
</x-app-layout>
