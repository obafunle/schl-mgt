<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Timetable') }} - {{ $class->name }} {{ $classArm ? $classArm->name : '' }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.timetable.index') }}" 
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg text-sm">
                    ← Back
                </a>
                <a href="{{ route('admin.timetable.create') }}?class_id={{ $class->id }}&term_id={{ $term->id }}" 
                   class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg text-sm">
                    + Add Entry
                </a>
                <a href="{{ route('admin.timetable.export', ['class_id' => $class->id, 'class_arm_id' => $classArm?->id, 'term_id' => $term->id, 'academic_year_id' => $term->academic_year_id, 'format' => 'pdf']) }}" 
                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg text-sm">
                    📄 Export PDF
                </a>
                <button onclick="window.print()" 
                        class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded-lg text-sm">
                    🖨️ Print
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Timetable Info -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                    <div>
                        <span class="font-semibold">Class:</span> {{ $class->name }} 
                        @if($classArm) <span class="font-semibold">Arm:</span> {{ $classArm->name }} @endif
                        <span class="ml-4 font-semibold">Term:</span> {{ $term->name }}
                        <span class="ml-4 font-semibold">Year:</span> {{ $term->academicYear->name ?? 'N/A' }}
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Total Entries: {{ $timetable['entries']->count() }}</span>
                        @php
                            $conflictCount = $timetable['entries']->filter(function($e) { return $e->hasConflict(); })->count();
                        @endphp
                        @if($conflictCount > 0)
                            <span class="ml-4 text-sm text-red-500">⚠️ {{ $conflictCount }} Conflict(s)</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Timetable Grid -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 border border-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase border border-gray-200" style="min-width: 100px;">
                                    Period / Time
                                </th>
                                @foreach($timetable['days'] as $day)
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase border border-gray-200" style="min-width: 120px;">
                                        {{ $day->short_name }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($timetable['grid'] as $row)
                                <tr>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-700 border border-gray-200 text-center">
                                        {{ $row['period']->name }}
                                        <br>
                                        <span class="text-xs text-gray-500">{{ $row['time'] }}</span>
                                    </td>
                                    @foreach($timetable['days'] as $day)
                                        <td class="px-2 py-2 text-center border border-gray-200 align-top" style="min-height: 80px;">
                                            @php
                                                $entry = $row['entries'][$day->id] ?? null;
                                            @endphp
                                            @if($entry)
                                                <div class="p-2 rounded {{ $entry['has_conflict'] ? 'bg-red-50 border border-red-300' : 'bg-blue-50' }}">
                                                    <div class="font-semibold text-sm text-gray-800">{{ $entry['subject'] }}</div>
                                                    <div class="text-xs text-gray-600">{{ $entry['teacher'] }}</div>
                                                    <div class="text-xs text-gray-500">Room: {{ $entry['room'] }}</div>
                                                    @if($entry['has_conflict'])
                                                        <div class="mt-1 text-xs text-red-500 font-bold">⚠️ Conflict</div>
                                                    @endif
                                                    <div class="mt-1 flex space-x-1 justify-center">
                                                        <a href="{{ route('admin.timetable.edit', $entry['entry_id']) }}" 
                                                           class="text-blue-600 hover:text-blue-800 text-xs">✏️</a>
                                                        <form action="{{ route('admin.timetable.destroy', $entry['entry_id']) }}" 
                                                              method="POST" class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" onclick="return confirm('Delete this entry?')" 
                                                                    class="text-red-600 hover:text-red-800 text-xs">🗑️</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="text-xs text-gray-400">—</div>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Conflicts Section -->
            @if($timetable['entries']->filter(function($e) { return $e->hasConflict(); })->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-red-600 mb-4">⚠️ Timetable Conflicts</h3>
                        <div class="space-y-3">
                            @foreach($timetable['entries'] as $entry)
                                @if($entry->hasConflict())
                                    <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <p class="font-semibold text-sm">
                                                    {{ $entry->subject->name }} - {{ $entry->class->name }}
                                                    @if($entry->classArm)
                                                        ({{ $entry->classArm->name }})
                                                    @endif
                                                </p>
                                                <p class="text-sm text-gray-600">
                                                    {{ $entry->day->name }} - {{ $entry->period->name }}
                                                    ({{ $entry->period->getTimeRange() }})
                                                </p>
                                                <p class="text-sm text-gray-600">Teacher: {{ $entry->teacher->full_name }}</p>
                                            </div>
                                            <div class="flex space-x-2">
                                                <a href="{{ route('admin.timetable.edit', $entry->id) }}" 
                                                   class="px-3 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600">
                                                    Resolve
                                                </a>
                                                <form action="{{ route('admin.timetable.resolve-conflicts', $entry->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="px-3 py-1 bg-green-500 text-white text-xs rounded hover:bg-green-600">
                                                        Mark Resolved
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                        @foreach($entry->getConflicts() as $conflict)
                                            <div class="mt-2 ml-4 text-sm text-red-600">
                                                • {{ $conflict->description }}
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>