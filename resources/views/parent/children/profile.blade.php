<x-parent-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $child->full_name }} - Profile
            </h2>
            <a href="{{ route('parent.children') }}" class="text-sm text-blue-600 hover:text-blue-800">← Back to Children</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p><strong>Full Name:</strong> {{ $child->full_name }}</p>
                            <p><strong>Admission Number:</strong> {{ $child->admission_number }}</p>
                            <p><strong>Date of Birth:</strong> {{ $child->date_of_birth->format('M d, Y') }}</p>
                            <p><strong>Gender:</strong> {{ ucfirst($child->gender) }}</p>
                        </div>
                        <div>
                            <p><strong>Class:</strong> {{ $child->class->name ?? 'Not Assigned' }}</p>
                            <p><strong>Class Arm:</strong> {{ $child->class_arm ?? 'N/A' }}</p>
                            <p><strong>Academic Year:</strong> {{ $child->academicYear->name ?? 'N/A' }}</p>
                            <p><strong>Status:</strong> <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">{{ ucfirst($child->status) }}</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-parent-layout>
