{{-- ============================================================
    CREATE STUDENT VIEW
    This page allows administrators to register new students
    ============================================================ --}}

<x-app-layout>
    {{-- Page Header --}}
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __('Add New Student') }}
            </h2>
            <span class="text-sm text-gray-500">
                {{ now()->format('l, F j, Y') }}
            </span>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">

                    {{-- ========================================
                    STUDENT REGISTRATION FORM
                    ======================================== --}}
                    <form action="{{ route('admin.students.store') }}"
                          method="POST"
                          enctype="multipart/form-data"
                          class="space-y-6"
                          id="studentForm">

                        @csrf

                        {{-- ====================================
                        PERSONAL INFORMATION SECTION
                        ==================================== --}}
                        <div>
                            <h3 class="pb-2 mb-4 text-lg font-semibold text-gray-700 border-b border-gray-200">
                                👤 Personal Information
                            </h3>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

                                <div>
                                    <label for="first_name" class="block text-sm font-medium text-gray-700">
                                        First Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text"
                                           id="first_name"
                                           name="first_name"
                                           value="{{ old('first_name') }}"
                                           required
                                           placeholder="e.g., John"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm transition duration-150 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    @error('first_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="last_name" class="block text-sm font-medium text-gray-700">
                                        Last Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text"
                                           id="last_name"
                                           name="last_name"
                                           value="{{ old('last_name') }}"
                                           required
                                           placeholder="e.g., Doe"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm transition duration-150 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    @error('last_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="middle_name" class="block text-sm font-medium text-gray-700">
                                        Middle Name
                                    </label>
                                    <input type="text"
                                           id="middle_name"
                                           name="middle_name"
                                           value="{{ old('middle_name') }}"
                                           placeholder="e.g., Michael"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm transition duration-150 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    @error('middle_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="date_of_birth" class="block text-sm font-medium text-gray-700">
                                        Date of Birth <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date"
                                           id="date_of_birth"
                                           name="date_of_birth"
                                           value="{{ old('date_of_birth') }}"
                                           required
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm transition duration-150 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    @error('date_of_birth')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="gender" class="block text-sm font-medium text-gray-700">
                                        Gender <span class="text-red-500">*</span>
                                    </label>
                                    <select id="gender"
                                            name="gender"
                                            required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm transition duration-150 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <option value="">Select Gender</option>
                                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                    </select>
                                    @error('gender')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700">
                                        Phone Number
                                    </label>
                                    <input type="tel"
                                           id="phone"
                                           name="phone"
                                           value="{{ old('phone') }}"
                                           placeholder="e.g., 08012345678"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm transition duration-150 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    @error('phone')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label for="email" class="block text-sm font-medium text-gray-700">
                                        Email Address <span class="text-xs text-gray-500">(Optional)</span>
                                    </label>
                                    <input type="email"
                                           id="email"
                                           name="email"
                                           value="{{ old('email') }}"
                                           placeholder="student@example.com"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm transition duration-150 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <p class="mt-1 text-xs text-gray-500">If provided, must be a valid email address.</p>
                                    @error('email')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label for="address" class="block text-sm font-medium text-gray-700">
                                        Home Address
                                    </label>
                                    <textarea id="address"
                                              name="address"
                                              rows="2"
                                              placeholder="Enter student's home address..."
                                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm transition duration-150 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('address') }}</textarea>
                                    @error('address')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- ====================================
                        ACADEMIC INFORMATION SECTION
                        ==================================== --}}
                        <div>
                            <h3 class="pb-2 mb-4 text-lg font-semibold text-gray-700 border-b border-gray-200">
                                📚 Academic Information
                            </h3>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">

                                <div>
                                    <label for="class_id" class="block text-sm font-medium text-gray-700">
                                        Class
                                    </label>
                                    <select id="class_id"
                                            name="class_id"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm transition duration-150 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <option value="">Select Class</option>
                                        @foreach($classes as $class)
                                            <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                                {{ $class->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('class_id')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="class_arm" class="block text-sm font-medium text-gray-700">
                                        Class Arm
                                    </label>
                                    <select id="class_arm"
                                            name="class_arm"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm transition duration-150 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <option value="">Select Arm</option>
                                    </select>
                                    @error('class_arm')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="academic_year_id" class="block text-sm font-medium text-gray-700">
                                        Academic Year <span class="text-red-500">*</span>
                                    </label>
                                    <select id="academic_year_id"
                                            name="academic_year_id"
                                            required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm transition duration-150 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <option value="">Select Year</option>
                                        @foreach($academicYears as $year)
                                            <option value="{{ $year->id }}" {{ old('academic_year_id') == $year->id ? 'selected' : '' }}>
                                                {{ $year->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('academic_year_id')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- ====================================
                        PARENT / GUARDIAN INFORMATION
                        ==================================== --}}
                        <div>
                            <h3 class="pb-2 mb-4 text-lg font-semibold text-gray-700 border-b border-gray-200">
                                👨‍👩‍👦 Parent / Guardian Information
                            </h3>

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">

                                <div>
                                    <label for="parent_name" class="block text-sm font-medium text-gray-700">
                                        Parent Name <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text"
                                           id="parent_name"
                                           name="parent_name"
                                           value="{{ old('parent_name') }}"
                                           required
                                           placeholder="e.g., Mr. John Doe"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm transition duration-150 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    @error('parent_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="parent_phone" class="block text-sm font-medium text-gray-700">
                                        Parent Phone <span class="text-red-500">*</span>
                                    </label>
                                    <input type="tel"
                                           id="parent_phone"
                                           name="parent_phone"
                                           value="{{ old('parent_phone') }}"
                                           required
                                           placeholder="e.g., 08012345678"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm transition duration-150 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    @error('parent_phone')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="parent_email" class="block text-sm font-medium text-gray-700">
                                        Parent Email <span class="text-xs text-gray-500">(Optional)</span>
                                    </label>
                                    <input type="email"
                                           id="parent_email"
                                           name="parent_email"
                                           value="{{ old('parent_email') }}"
                                           placeholder="parent@example.com"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm transition duration-150 focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <p class="mt-1 text-xs text-gray-500">If provided, must be a valid email address.</p>
                                    @error('parent_email')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- ====================================
                        PHOTO UPLOAD
                        ==================================== --}}
                        <div>
                            <h3 class="pb-2 mb-4 text-lg font-semibold text-gray-700 border-b border-gray-200">
                                📸 Profile Passport
                            </h3>

                            <div>
                                <label for="photo" class="block text-sm font-medium text-gray-700">
                                    Upload passport <span class="text-xs text-gray-500">(Optional)</span>
                                </label>
                                <input type="file"
                                       id="photo"
                                       name="photo"
                                       accept="image/*"
                                       class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:rounded-md file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100">
                                <p class="mt-1 text-xs text-gray-500">Max 2MB. Recommended: 300x300px</p>
                                @error('photo')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- ============================================================
                        FORM ACTIONS - WITH FORCED BACKGROUND COLOR
                        ============================================================ --}}
                        <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                            {{-- Left side: Required fields note --}}
                            <div class="text-sm text-gray-500">
                                <span class="text-red-500">*</span> Required fields
                            </div>

                            {{-- Right side: Both buttons --}}
                            <div class="flex items-center space-x-3">

                                {{-- 1. CANCEL BUTTON --}}
                                <a href="{{ route('admin.students.index') }}"
                                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Cancel
                                </a>

                                {{-- 2. CREATE STUDENT BUTTON - WITH FORCED STYLES --}}
                                <button type="submit"
                                        style="display: inline-flex; align-items: center; padding: 8px 16px; font-size: 14px; font-weight: 500; color: #ffffff; background-color: #4f46e5; border: 1px solid #4f46e5; border-radius: 6px; cursor: pointer; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                                    <svg style="width: 16px; height: 16px; margin-right: 8px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Create Student
                                </button>

                            </div>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================
    JAVASCRIPT: Dynamic Class Arm Loader
    ============================================================ --}}
    <script>
        (function() {
            'use strict';

            const classSelect = document.getElementById('class_id');
            const armSelect = document.getElementById('class_arm');

            if (!classSelect || !armSelect) {
                console.warn('⚠️ Class or Arm select element not found.');
                return;
            }

            function populateArms(arms) {
                armSelect.innerHTML = '<option value="">Select Arm</option>';
                if (!arms || arms.length === 0) {
                    armSelect.innerHTML = '<option value="">No arms available</option>';
                    return;
                }
                arms.forEach(function(arm) {
                    const option = document.createElement('option');
                    option.value = arm.name;
                    option.textContent = arm.name;
                    armSelect.appendChild(option);
                });
            }

            function showLoading() {
                armSelect.innerHTML = '<option value="">Loading...</option>';
            }

            function handleClassChange() {
                const classId = classSelect.value;
                if (!classId) {
                    populateArms([]);
                    return;
                }
                showLoading();
                const url = '/admin/get-class-arms?class_id=' + encodeURIComponent(classId);
                fetch(url)
                    .then(function(response) {
                        if (!response.ok) throw new Error('HTTP Error ' + response.status);
                        return response.json();
                    })
                    .then(function(data) {
                        populateArms(data);
                    })
                    .catch(function(error) {
                        console.error('❌ Failed to load class arms:', error);
                        armSelect.innerHTML = '<option value="">Error loading arms</option>';
                    });
            }

            classSelect.addEventListener('change', handleClassChange);
            if (classSelect.value) {
                handleClassChange();
            }

            console.log('✅ Dynamic Class Arm Loader initialized.');
        })();
    </script>

</x-app-layout>
