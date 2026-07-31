<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create New Class') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.classes.store') }}" method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                                <input type="text" name="name" value="{{ old('name') }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Code <span class="text-red-500">*</span></label>
                                <input type="text" name="code" value="{{ old('code') }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Level <span class="text-red-500">*</span></label>
                                <input type="number" name="level" value="{{ old('level', 1) }}" min="1" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Category <span class="text-red-500">*</span></label>
                                <select name="category" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                    <option value="primary" {{ old('category') == 'primary' ? 'selected' : '' }}>Primary</option>
                                    <option value="junior" {{ old('category') == 'junior' ? 'selected' : '' }}>Junior Secondary</option>
                                    <option value="senior" {{ old('category') == 'senior' ? 'selected' : '' }}>Senior Secondary</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea name="description" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">{{ old('description') }}</textarea>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <a href="{{ route('admin.classes.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Cancel</a>
                            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Create Class</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
