<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Add New Staff') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.staff.store') }}" method="POST" enctype="multipart/form-data" id="staffForm">
                        @csrf

                        {{-- Personal Information --}}
                        <div class="mb-6">
                            <h3 class="pb-2 mb-4 text-lg font-semibold text-gray-700 border-b border-gray-200">
                                👤 Personal Information
                            </h3>
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                                <div>
                                    <label for="first_name" class="block text-sm font-medium text-gray-700">
                                        First Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" required
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    @error('first_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="middle_name" class="block text-sm font-medium text-gray-700">
                                        Middle Name
                                    </label>
                                    <input type="text" id="middle_name" name="middle_name" value="{{ old('middle_name') }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                </div>
                                <div>
                                    <label for="last_name" class="block text-sm font-medium text-gray-700">
                                        Last Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" required
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    @error('last_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="date_of_birth" class="block text-sm font-medium text-gray-700">
                                        Date of Birth <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}" required
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    @error('date_of_birth')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="gender" class="block text-sm font-medium text-gray-700">
                                        Gender <span class="text-red-500">*</span>
                                    </label>
                                    <select id="gender" name="gender" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <option value="">Select Gender</option>
                                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                    </select>
                                    @error('gender')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700">
                                        Email <span class="text-red-500">*</span>
                                    </label>
                                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    @error('email')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700">
                                        Phone
                                    </label>
                                    <input type="tel" id="phone" name="phone" value="{{ old('phone') }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    @error('phone')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="md:col-span-2">
                                    <label for="address" class="block text-sm font-medium text-gray-700">
                                        Address
                                    </label>
                                    <textarea id="address" name="address" rows="2"
                                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('address') }}</textarea>
                                    @error('address')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="photo" class="block text-sm font-medium text-gray-700">
                                        Profile Photo
                                    </label>
                                    <input type="file" id="photo" name="photo" accept="image/*"
                                           class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:rounded-md file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100">
                                    <p class="mt-1 text-xs text-gray-500">Max 2MB. Recommended: 300x300px</p>
                                    @error('photo')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Employment Details --}}
                        <div class="mb-6">
                            <h3 class="pb-2 mb-4 text-lg font-semibold text-gray-700 border-b border-gray-200">
                                💼 Employment Details
                            </h3>
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                                <div>
                                    <label for="staff_type" class="block text-sm font-medium text-gray-700">
                                        Staff Type <span class="text-red-500">*</span>
                                    </label>
                                    <select id="staff_type" name="staff_type" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <option value="">Select Type</option>
                                        <option value="teacher" {{ old('staff_type') == 'teacher' ? 'selected' : '' }}>Teacher</option>
                                        <option value="admin" {{ old('staff_type') == 'admin' ? 'selected' : '' }}>Administrator</option>
                                        <option value="accountant" {{ old('staff_type') == 'accountant' ? 'selected' : '' }}>Accountant</option>
                                        <option value="librarian" {{ old('staff_type') == 'librarian' ? 'selected' : '' }}>Librarian</option>
                                        <option value="support" {{ old('staff_type') == 'support' ? 'selected' : '' }}>Support</option>
                                        <option value="other" {{ old('staff_type') == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('staff_type')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="employment_type" class="block text-sm font-medium text-gray-700">
                                        Employment Type <span class="text-red-500">*</span>
                                    </label>
                                    <select id="employment_type" name="employment_type" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <option value="">Select Type</option>
                                        <option value="permanent" {{ old('employment_type') == 'permanent' ? 'selected' : '' }}>Permanent</option>
                                        <option value="contract" {{ old('employment_type') == 'contract' ? 'selected' : '' }}>Contract</option>
                                        <option value="part-time" {{ old('employment_type') == 'part-time' ? 'selected' : '' }}>Part-Time</option>
                                        <option value="intern" {{ old('employment_type') == 'intern' ? 'selected' : '' }}>Intern</option>
                                    </select>
                                    @error('employment_type')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="hire_date" class="block text-sm font-medium text-gray-700">
                                        Hire Date <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" id="hire_date" name="hire_date" value="{{ old('hire_date') }}" required
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    @error('hire_date')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="basic_salary" class="block text-sm font-medium text-gray-700">
                                        Basic Salary (₦)
                                    </label>
                                    <input type="number" id="basic_salary" name="basic_salary" value="{{ old('basic_salary', 0) }}" step="0.01"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    @error('basic_salary')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="bank_name" class="block text-sm font-medium text-gray-700">
                                        Bank Name
                                    </label>
                                    <input type="text" id="bank_name" name="bank_name" value="{{ old('bank_name') }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    @error('bank_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="bank_account_number" class="block text-sm font-medium text-gray-700">
                                        Account Number
                                    </label>
                                    <input type="text" id="bank_account_number" name="bank_account_number" value="{{ old('bank_account_number') }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    @error('bank_account_number')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="bank_account_name" class="block text-sm font-medium text-gray-700">
                                        Account Name
                                    </label>
                                    <input type="text" id="bank_account_name" name="bank_account_name" value="{{ old('bank_account_name') }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    @error('bank_account_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Qualifications --}}
                        <div class="mb-6">
                            <h3 class="pb-2 mb-4 text-lg font-semibold text-gray-700 border-b border-gray-200">
                                🎓 Qualifications
                            </h3>
                            <div id="qualifications-container" class="space-y-3">
                                <div class="text-sm text-gray-500">Add qualifications (e.g., B.Sc, M.Sc, etc.)</div>
                            </div>
                            <button type="button" onclick="addQualification()"
                                    class="inline-flex items-center px-3 py-1 mt-2 text-sm text-white bg-green-600 rounded-md hover:bg-green-700">
                                + Add Qualification
                            </button>
                        </div>

                        {{-- Work Experience --}}
                        <div class="mb-6">
                            <h3 class="pb-2 mb-4 text-lg font-semibold text-gray-700 border-b border-gray-200">
                                💼 Work Experience
                            </h3>
                            <div id="experience-container" class="space-y-3">
                                <div class="text-sm text-gray-500">Add work experience</div>
                            </div>
                            <button type="button" onclick="addExperience()"
                                    class="inline-flex items-center px-3 py-1 mt-2 text-sm text-white bg-green-600 rounded-md hover:bg-green-700">
                                + Add Experience
                            </button>
                        </div>

                        {{-- Next of Kin --}}
                        <div class="mb-6">
                            <h3 class="pb-2 mb-4 text-lg font-semibold text-gray-700 border-b border-gray-200">
                                👨‍👩‍👦 Next of Kin
                            </h3>
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                                <div>
                                    <label for="next_of_kin_name" class="block text-sm font-medium text-gray-700">
                                        Name
                                    </label>
                                    <input type="text" id="next_of_kin_name" name="next_of_kin_name" value="{{ old('next_of_kin_name') }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    @error('next_of_kin_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="next_of_kin_phone" class="block text-sm font-medium text-gray-700">
                                        Phone Number
                                    </label>
                                    <input type="tel" id="next_of_kin_phone" name="next_of_kin_phone" value="{{ old('next_of_kin_phone') }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    @error('next_of_kin_phone')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="next_of_kin_relationship" class="block text-sm font-medium text-gray-700">
                                        Relationship
                                    </label>
                                    <input type="text" id="next_of_kin_relationship" name="next_of_kin_relationship" value="{{ old('next_of_kin_relationship') }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    @error('next_of_kin_relationship')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Subject Assignment (Only for Teachers) --}}
                        <div class="mb-6" id="teacher-fields" style="{{ old('staff_type') == 'teacher' ? 'display:block' : 'display:none' }}">
                            <h3 class="pb-2 mb-4 text-lg font-semibold text-gray-700 border-b border-gray-200">
                                📚 Subject Assignments
                            </h3>
                            <div id="subjects-container" class="space-y-3">
                                <div class="text-sm text-gray-500">Add subjects this teacher will teach</div>
                            </div>
                            <button type="button" onclick="addSubject()"
                                    class="inline-flex items-center px-3 py-1 text-sm text-white bg-green-600 rounded-md hover:bg-green-700">
                                + Add Subject
                            </button>
                        </div>

                        {{-- User Account --}}
                        <div class="mb-6">
                            <h3 class="pb-2 mb-4 text-lg font-semibold text-gray-700 border-b border-gray-200">
                                🔐 User Account
                            </h3>
                            <div class="space-y-4">
                                <div class="flex items-center">
                                    <input type="checkbox" id="create_user_account" name="create_user_account" value="1"
                                        {{ old('create_user_account') ? 'checked' : '' }}
                                        onchange="toggleUserAccount()"
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300">
                                    <label for="create_user_account" class="ml-2 text-sm text-gray-700">
                                        Create user account for this staff
                                    </label>
                                </div>

                                <div id="user-account-fields" style="display: {{ old('create_user_account') ? 'block' : 'none' }}">
                                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                        <div>
                                            <x-password-input
                                                name="password"
                                                label="Password"
                                                :required="old('create_user_account') ? true : false"
                                                placeholder="Enter password"
                                                autocomplete="new-password"
                                            />
                                            @error('password')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <x-password-input
                                                name="password_confirmation"
                                                label="Confirm Password"
                                                :required="old('create_user_account') ? true : false"
                                                placeholder="Re-enter password"
                                                autocomplete="new-password"
                                            />
                                        </div>
                                    </div>
                                    <p class="mt-2 text-xs text-gray-500">Password must be at least 8 characters.</p>
                                </div>
                            </div>
                        </div>

                        {{-- Form Actions --}}
                        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                            <div class="text-sm text-gray-500">
                                <span class="text-red-500">*</span> Required fields
                            </div>
                            <div class="flex items-center space-x-3">
                                <a href="{{ route('admin.staff.index') }}"
                                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50">
                                    Cancel
                                </a>
                                <button type="submit"
                                        style="display: inline-flex; align-items: center; padding: 8px 24px; font-size: 14px; font-weight: 600; color: #ffffff; background-color: #4f46e5; border: 1px solid transparent; border-radius: 6px; cursor: pointer;">
                                    Create Staff
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let subjectCount = 0;
        let qualificationCount = 0;
        let experienceCount = 0;

        function toggleTeacherFields() {
            const staffType = document.getElementById('staff_type').value;
            const teacherFields = document.getElementById('teacher-fields');
            teacherFields.style.display = staffType === 'teacher' ? 'block' : 'none';
        }

        function toggleUserAccount() {
            const checked = document.getElementById('create_user_account').checked;
            document.getElementById('user-account-fields').style.display = checked ? 'block' : 'none';
        }

        function addQualification() {
            qualificationCount++;
            const container = document.getElementById('qualifications-container');

            const div = document.createElement('div');
            div.className = 'qualification-row flex items-center space-x-3 p-3 bg-gray-50 rounded-lg border border-gray-200';
            div.id = 'qualification-' + qualificationCount;

            div.innerHTML = `
                <div class="flex-1">
                    <input type="text" name="qualifications[${qualificationCount}][degree]"
                           placeholder="Degree (e.g., B.Sc, M.Sc)"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500">
                </div>
                <div class="flex-1">
                    <input type="text" name="qualifications[${qualificationCount}][institution]"
                           placeholder="Institution"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500">
                </div>
                <div class="w-32">
                    <input type="text" name="qualifications[${qualificationCount}][year]"
                           placeholder="Year"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500">
                </div>
                <button type="button" onclick="removeQualification(${qualificationCount})"
                        class="text-red-500 hover:text-red-700">
                    ✕
                </button>
            `;

            container.appendChild(div);
        }

        function removeQualification(id) {
            const item = document.getElementById('qualification-' + id);
            if (item) item.remove();
        }

        function addExperience() {
            experienceCount++;
            const container = document.getElementById('experience-container');

            const div = document.createElement('div');
            div.className = 'experience-row flex items-center space-x-3 p-3 bg-gray-50 rounded-lg border border-gray-200';
            div.id = 'experience-' + experienceCount;

            div.innerHTML = `
                <div class="flex-1">
                    <input type="text" name="experience[${experienceCount}][position]"
                           placeholder="Position"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500">
                </div>
                <div class="flex-1">
                    <input type="text" name="experience[${experienceCount}][school]"
                           placeholder="School/Organization"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500">
                </div>
                <div class="w-32">
                    <input type="number" name="experience[${experienceCount}][years]"
                           placeholder="Years" step="0.5"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500">
                </div>
                <button type="button" onclick="removeExperience(${experienceCount})"
                        class="text-red-500 hover:text-red-700">
                    ✕
                </button>
            `;

            container.appendChild(div);
        }

        function removeExperience(id) {
            const item = document.getElementById('experience-' + id);
            if (item) item.remove();
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
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500">
                        <option value="">Select Subject</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }} ({{ $subject->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-40">
                    <select name="subjects[${subjectCount}][role]"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500">
                        <option value="primary">Primary</option>
                        <option value="secondary">Secondary</option>
                        <option value="assistant">Assistant</option>
                    </select>
                </div>
                <div class="w-32">
                    <input type="number" name="subjects[${subjectCount}][weekly_hours]"
                           placeholder="Hours" value="4" min="1" max="40"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500">
                </div>
                <button type="button" onclick="removeSubject(${subjectCount})"
                        class="text-red-500 hover:text-red-700">
                    ✕
                </button>
            `;

            container.appendChild(div);
        }

        function removeSubject(id) {
            const item = document.getElementById('subject-' + id);
            if (item) item.remove();
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('staff_type').addEventListener('change', toggleTeacherFields);
            toggleTeacherFields();
            toggleUserAccount();
        });
    </script>
</x-app-layout>
