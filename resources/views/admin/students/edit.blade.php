<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Student') }} - {{ $student->full_name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.students.update', $student) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- First Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">First Name <span class="text-red-500">*</span></label>
                                <input type="text" name="first_name" value="{{ old('first_name', $student->first_name) }}" required
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                            </div>

                            <!-- Last Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Last Name <span class="text-red-500">*</span></label>
                                <input type="text" name="last_name" value="{{ old('last_name', $student->last_name) }}" required
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                            </div>

                            <!-- Middle Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Middle Name</label>
                                <input type="text" name="middle_name" value="{{ old('middle_name', $student->middle_name) }}"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                            </div>

                            <!-- Date of Birth -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth <span class="text-red-500">*</span></label>
                                <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $student->date_of_birth->format('Y-m-d')) }}" required
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                            </div>

                            <!-- Gender -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Gender <span class="text-red-500">*</span></label>
                                <select name="gender" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                    <option value="male" {{ old('gender', $student->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender', $student->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input type="email" name="email" value="{{ old('email', $student->email) }}"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                            </div>

                            <!-- Phone -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                <input type="text" name="phone" value="{{ old('phone', $student->phone) }}"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                            </div>

                            <!-- Class -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Class</label>
                                <select name="class_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                    <option value="">Select Class</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" {{ old('class_id', $student->class_id) == $class->id ? 'selected' : '' }}>
                                            {{ $class->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Academic Year -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Academic Year</label>
                                <select name="academic_year_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                    <option value="">Select Year</option>
                                    @foreach($academicYears as $year)
                                        <option value="{{ $year->id }}" {{ old('academic_year_id', $student->academic_year_id) == $year->id ? 'selected' : '' }}>
                                            {{ $year->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Parent Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Parent/Guardian Name <span class="text-red-500">*</span></label>
                                <input type="text" name="parent_name" value="{{ old('parent_name', $student->parent_name) }}" required
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                            </div>

                            <!-- Parent Phone -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Parent Phone <span class="text-red-500">*</span></label>
                                <input type="text" name="parent_phone" value="{{ old('parent_phone', $student->parent_phone) }}" required
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                            </div>

                            <!-- Parent Email -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Parent Email</label>
                                <input type="email" name="parent_email" value="{{ old('parent_email', $student->parent_email) }}"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                            </div>

                            <!-- Photo -->
                            <div class="md:col-span-2">
                                @if($student->photo)
                                    <div class="mb-2">
                                        <img src="{{ $student->photo_url }}" alt="{{ $student->full_name }}" class="h-16 w-16 rounded-full object-cover">
                                    </div>
                                @endif
                                <label class="block text-sm font-medium text-gray-700 mb-1">Profile Photo</label>
                                <input type="file" name="photo" accept="image/*"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                <p class="text-xs text-gray-500 mt-1">Leave blank to keep current photo. Max 2MB.</p>
                            </div>

                            <!-- Status -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                    <option value="active" {{ old('status', $student->status) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="graduated" {{ old('status', $student->status) == 'graduated' ? 'selected' : '' }}>Graduated</option>
                                    <option value="transferred" {{ old('status', $student->status) == 'transferred' ? 'selected' : '' }}>Transferred</option>
                                    <option value="suspended" {{ old('status', $student->status) == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <a href="{{ route('admin.students.index') }}"
                               class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Cancel</a>
                            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Update Student</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
