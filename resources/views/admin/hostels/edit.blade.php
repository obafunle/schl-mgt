<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Hostel') }} - {{ $hostel->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.hostels.update', $hostel) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                                <input type="text" name="name" value="{{ old('name', $hostel->name) }}" required
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Code <span class="text-red-500">*</span></label>
                                <input type="text" name="code" value="{{ old('code', $hostel->code) }}" required
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Type <span class="text-red-500">*</span></label>
                                <select name="type" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                    <option value="boys" {{ old('type', $hostel->type) == 'boys' ? 'selected' : '' }}>Boys' Hostel</option>
                                    <option value="girls" {{ old('type', $hostel->type) == 'girls' ? 'selected' : '' }}>Girls' Hostel</option>
                                    <option value="mixed" {{ old('type', $hostel->type) == 'mixed' ? 'selected' : '' }}>Mixed</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Gender <span class="text-red-500">*</span></label>
                                <select name="gender" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                    <option value="male" {{ old('gender', $hostel->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender', $hostel->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="mixed" {{ old('gender', $hostel->gender) == 'mixed' ? 'selected' : '' }}>Mixed</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                <input type="text" name="phone" value="{{ old('phone', $hostel->phone) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input type="email" name="email" value="{{ old('email', $hostel->email) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea name="description" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">{{ old('description', $hostel->description) }}</textarea>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Facilities (comma separated)</label>
                                <input type="text" name="facilities" value="{{ old('facilities', implode(', ', $hostel->facilities ?? [])) }}" placeholder="Kitchen, Laundry, Common Room" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">House Master</label>
                                <input type="text" name="house_master" value="{{ old('house_master', $hostel->house_master) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Assistant House Master</label>
                                <input type="text" name="assistant_house_master" value="{{ old('assistant_house_master', $hostel->assistant_house_master) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select name="is_active" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                    <option value="1" {{ old('is_active', $hostel->is_active) ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('is_active', $hostel->is_active) ? '' : 'selected' }}>Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <a href="{{ route('admin.hostels.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Cancel</a>
                            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Update Hostel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
