<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Timetable Entry') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.timetable.update', $entry) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Class and Arm -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div>
                                <label for="class_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    Class <span class="text-red-500">*</span>
                                </label>
                                <select id="class_id" name="class_id" required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" 
                                            {{ $entry->class_id == $class->id ? 'selected' : '' }}>
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
                                    @foreach($classArms as $arm)
                                        <option value="{{ $arm->id }}" 
                                            {{ $entry->class_arm_id == $arm->id ? 'selected' : '' }}>
                                            {{ $arm->name }}
                                        </option>
                                    @endforeach
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
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->id }}" 
                                            {{ $entry->subject_id == $subject->id ? 'selected' : '' }}>
                                            {{ $subject->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="teacher_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    Teacher <span class="text-red-500">*</span>
                                </label>
                                <select id="teacher_id" name="teacher_id" required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                    @foreach($teachers as $teacher)
                                        <option value="{{ $teacher->id }}" 
                                            {{ $entry->teacher_id == $teacher->id ? 'selected' : '' }}>
                                            {{ $teacher->full_name }}
                                        </option>
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
                                        <option value="{{ $room->id }}" 
                                            {{ $entry->room_id == $room->id ? 'selected' : '' }}>
                                            {{ $room->name }} ({{ $room->code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Day and Period -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div>
                                <label for="day_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    Day <span class="text-red-500">*</span>
                                </label>
                                <select id="day_id" name="day_id" required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                    @foreach($days as $day)
                                        <option value="{{ $day->id }}" 
                                            {{ $entry->day_id == $day->id ? 'selected' : '' }}>
                                            {{ $day->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="period_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    Period <span class="text-red-500">*</span>
                                </label>
                                <select id="period_id" name="period_id" required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                    @foreach($periods as $period)
                                        <option value="{{ $period->id }}" 
                                            {{ $entry->period_id == $period->id ? 'selected' : '' }}>
                                            {{ $period->name }} ({{ $period->getTimeRange() }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="mb-6">
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                                Notes
                            </label>
                            <textarea id="notes" name="notes" rows="2" 
                                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">{{ $entry->notes }}</textarea>
                        </div>

                        <!-- Current Status -->
                        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                            <p class="text-sm text-gray-600">
                                <span class="font-semibold">Status:</span>
                                <span class="px-2 py-1 text-xs rounded-full 
                                    {{ $entry->status == 'scheduled' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $entry->status == 'rescheduled' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $entry->status == 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ ucfirst($entry->status) }}
                                </span>
                                @if($entry->hasConflict())
                                    <span class="ml-4 text-red-500 font-semibold">⚠️ Has Conflicts</span>
                                @endif
                            </p>
                        </div>

                        <!-- Submit -->
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('admin.timetable.show', ['class_id' => $entry->class_id, 'class_arm_id' => $entry->class_arm_id, 'term_id' => $entry->term_id, 'academic_year_id' => $entry->academic_year_id]) }}" 
                               class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                Update Entry
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>