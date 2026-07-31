<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Room Timetable') }} - {{ $room->name }}
            </h2>
            <a href="{{ route('admin.timetable.index') }}" 
               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg text-sm">
                ← Back
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                    <div>
                        <span class="font-semibold">Room:</span> {{ $room->name }} ({{ $room->code }})
                        <span class="ml-4 font-semibold">Type:</span> {{ $room->getTypeLabel() }}
                        <span class="ml-4 font-semibold">Capacity:</span> {{ $room->capacity }}
                        <span class="ml-4 font-semibold">Term:</span> {{ $term->name }}
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Total Bookings: {{ $timetable['entries']->count() }}</span>
                    </div>
                </div>

                <div class="p-6 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 border border-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase border border-gray-200">
                                    Period / Time
                                </th>
                                @foreach($timetable['days'] as $day)
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase border border-gray-200">
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
                                        <td class="px-2 py-2 text-center border border-gray-200 align-top">
                                            @php
                                                $entry = $row['entries'][$day->id] ?? null;
                                            @endphp
                                            @if($entry)
                                                <div class="p-2 rounded bg-blue-50">
                                                    <div class="font-semibold text-sm text-gray-800">{{ $entry['class'] }}</div>
                                                    <div class="text-xs text-gray-600">{{ $entry['subject'] }}</div>
                                                    <div class="text-xs text-gray-500">Teacher: {{ $entry['teacher'] }}</div>
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
        </div>
    </div>
</x-app-layout>