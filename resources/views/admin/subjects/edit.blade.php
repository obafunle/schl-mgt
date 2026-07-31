<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __('Edit Subject') }}: {{ $subject->name }}
            </h2>
            <span class="text-sm text-gray-500">Code: {{ $subject->code }}</span>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.subjects.update', $subject) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            {{-- Name --}}
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">
                                    Subject Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="name" name="name" value="{{ old('name', $subject->name) }}" required
                                       placeholder="e.g., Mathematics"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Code --}}
                            <div>
                                <label for="code" class="block text-sm font-medium text-gray-700">
                                    Subject Code <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="code" name="code" value="{{ old('code', $subject->code) }}" required
                                       placeholder="e.g., MAT"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                @error('code')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Short Name --}}
                            <div>
                                <label for="short_name" class="block text-sm font-medium text-gray-700">
                                    Short Name
                                </label>
                                <input type="text" id="short_name" name="short_name" value="{{ old('short_name', $subject->short_name) }}"
                                       placeholder="e.g., Maths"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                @error('short_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Category --}}
                            <div>
                                <label for="category" class="block text-sm font-medium text-gray-700">
                                    Category <span class="text-red-500">*</span>
                                </label>
                                <select id="category" name="category" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat }}" {{ old('category', $subject->category) == $cat ? 'selected' : '' }}>
                                            {{ ucfirst($cat) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Level --}}
                            <div>
                                <label for="level" class="block text-sm font-medium text-gray-700">
                                    Level <span class="text-red-500">*</span>
                                </label>
                                <select id="level" name="level" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    <option value="">Select Level</option>
                                    @foreach($levels as $level)
                                        <option value="{{ $level }}" {{ old('level', $subject->level) == $level ? 'selected' : '' }}>
                                            {{ ucfirst($level) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('level')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Weekly Hours --}}
                            <div>
                                <label for="weekly_hours" class="block text-sm font-medium text-gray-700">
                                    Weekly Hours
                                </label>
                                <input type="number" id="weekly_hours" name="weekly_hours" value="{{ old('weekly_hours', $subject->weekly_hours) }}"
                                       min="1" max="40"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                @error('weekly_hours')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Exam Weight --}}
                            <div>
                                <label for="exam_weight" class="block text-sm font-medium text-gray-700">
                                    Exam Weight (%) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" id="exam_weight" name="exam_weight" value="{{ old('exam_weight', $subject->exam_weight) }}"
                                       min="0" max="100" required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                @error('exam_weight')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- CA Weight --}}
                            <div>
                                <label for="ca_weight" class="block text-sm font-medium text-gray-700">
                                    CA Weight (%) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" id="ca_weight" name="ca_weight" value="{{ old('ca_weight', $subject->ca_weight) }}"
                                       min="0" max="100" required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                @error('ca_weight')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Description --}}
                            <div class="md:col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700">
                                    Description
                                </label>
                                <textarea id="description" name="description" rows="3"
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('description', $subject->description) }}</textarea>
                                @error('description')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Status --}}
                            <div class="md:col-span-2">
                                <div class="flex items-center">
                                    <input type="checkbox" id="is_active" name="is_active" value="1"
                                           {{ old('is_active', $subject->is_active) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300">
                                    <label for="is_active" class="ml-2 text-sm text-gray-700">
                                        Active
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- Form Actions --}}
                        <div class="flex items-center justify-end pt-4 mt-6 space-x-3 border-t border-gray-200">
                            <a href="{{ route('admin.subjects.index') }}"
                               class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50">
                                Cancel
                            </a>
                            <button type="submit"
                                    style="display: inline-flex; align-items: center; padding: 8px 24px; font-size: 14px; font-weight: 600; color: #ffffff; background-color: #4f46e5; border: none; border-radius: 6px; cursor: pointer;">
                                Update Subject
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
