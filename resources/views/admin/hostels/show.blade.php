<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $hostel->name }} - Hostel Details
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.hostels.edit', $hostel) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg text-sm">✏️ Edit</a>
                <a href="{{ route('admin.hostels.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg text-sm">← Back</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Stats Cards -->
            <div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-6">
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <span class="text-sm text-gray-500">Total Rooms</span>
                    <p class="text-2xl font-bold text-blue-600">{{ $stats['total_rooms'] }}</p>
                </div>
                <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                    <span class="text-sm text-gray-500">Total Beds</span>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['total_beds'] }}</p>
                </div>
                <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                    <span class="text-sm text-gray-500">Occupied</span>
                    <p class="text-2xl font-bold text-yellow-600">{{ $stats['occupied_beds'] }}</p>
                </div>
                <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                    <span class="text-sm text-gray-500">Available</span>
                    <p class="text-2xl font-bold text-purple-600">{{ $stats['available_beds'] }}</p>
                </div>
                <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                    <span class="text-sm text-gray-500">Occupancy Rate</span>
                    <p class="text-2xl font-bold text-red-600">{{ $stats['occupancy_rate'] }}%</p>
                </div>
                <div class="bg-orange-50 p-4 rounded-lg border border-orange-200">
                    <span class="text-sm text-gray-500">Pending Complaints</span>
                    <p class="text-2xl font-bold text-orange-600">{{ $stats['pending_complaints'] }}</p>
                </div>
            </div>

            <!-- Hostel Info -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p><strong>Code:</strong> {{ $hostel->code }}</p>
                            <p><strong>Type:</strong> {{ $hostel->getTypeLabel() }}</p>
                            <p><strong>Gender:</strong> {{ $hostel->getGenderLabel() }}</p>
                            <p><strong>House Master:</strong> {{ $hostel->house_master ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p><strong>Phone:</strong> {{ $hostel->phone ?? 'N/A' }}</p>
                            <p><strong>Email:</strong> {{ $hostel->email ?? 'N/A' }}</p>
                            <p><strong>Address:</strong> {{ $hostel->address ?? 'N/A' }}</p>
                            <p><strong>Status:</strong>
                                <span class="px-2 py-1 text-xs rounded-full {{ $hostel->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $hostel->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </p>
                        </div>
                    </div>
                    @if($hostel->facilities)
                        <div class="mt-4">
                            <strong>Facilities:</strong>
                            <div class="flex flex-wrap gap-2 mt-1">
                                @foreach($hostel->facilities as $facility)
                                    <span class="px-2 py-1 text-xs bg-gray-100 rounded">{{ $facility }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Tabs -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg" x-data="{ tab: 'rooms' }">
                <div class="border-b border-gray-200 overflow-x-auto">
                    <nav class="flex -mb-px min-w-max">
                        <button @click="tab = 'rooms'"
                                :class="{'border-indigo-500 text-indigo-600': tab === 'rooms', 'border-transparent text-gray-500 hover:text-gray-700': tab !== 'rooms'}"
                                class="py-4 px-6 border-b-2 font-medium text-sm transition whitespace-nowrap">
                            🛏️ Rooms ({{ $hostel->rooms->count() }})
                        </button>
                        <button @click="tab = 'assignments'"
                                :class="{'border-indigo-500 text-indigo-600': tab === 'assignments', 'border-transparent text-gray-500 hover:text-gray-700': tab !== 'assignments'}"
                                class="py-4 px-6 border-b-2 font-medium text-sm transition whitespace-nowrap">
                            👨‍👦 Assignments
                        </button>
                        <button @click="tab = 'complaints'"
                                :class="{'border-indigo-500 text-indigo-600': tab === 'complaints', 'border-transparent text-gray-500 hover:text-gray-700': tab !== 'complaints'}"
                                class="py-4 px-6 border-b-2 font-medium text-sm transition whitespace-nowrap">
                            📋 Complaints ({{ $hostel->complaints->count() }})
                        </button>
                    </nav>
                </div>

                <div class="p-6">
                    <!-- Rooms Tab -->
                    <div x-show="tab === 'rooms'">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="font-semibold text-gray-700">Rooms</h3>
                            <a href="{{ route('admin.hostels.rooms', $hostel) }}" class="bg-blue-500 hover:bg-blue-700 text-white text-sm font-bold py-2 px-4 rounded-lg">
                                + Manage Rooms
                            </a>
                        </div>
                        @if($hostel->rooms->count() > 0)
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                @foreach($hostel->rooms as $room)
                                    <div class="border rounded-lg p-3 text-center">
                                        <div class="font-bold">{{ $room->room_number }}</div>
                                        <div class="text-sm text-gray-500">{{ $room->getRoomTypeLabel() }}</div>
                                        <div class="text-xs text-gray-400">Capacity: {{ $room->capacity }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-sm">No rooms added yet.</p>
                        @endif
                    </div>

                    <!-- Assignments Tab -->
                    <div x-show="tab === 'assignments'">
                        <h3 class="font-semibold text-gray-700 mb-4">Student Assignments</h3>
                        @php
                            $assignments = $hostel->assignments()->where('status', 'active')->with(['student', 'room'])->get();
                        @endphp
                        @if($assignments->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Room</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Bed</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($assignments as $assignment)
                                            <tr>
                                                <td class="px-4 py-2 text-sm">{{ $assignment->student->full_name }}</td>
                                                <td class="px-4 py-2 text-sm">{{ $assignment->room->room_number }}</td>
                                                <td class="px-4 py-2 text-sm">{{ $assignment->bed_number ?? 'N/A' }}</td>
                                                <td class="px-4 py-2 text-sm">
                                                    <form action="{{ route('admin.hostels.release', $assignment) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" onclick="return confirm('Release this student?')"
                                                                class="text-red-600 hover:text-red-800">Release</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-gray-500 text-sm">No students assigned.</p>
                        @endif
                    </div>

                    <!-- Complaints Tab -->
                    <div x-show="tab === 'complaints'">
                        <h3 class="font-semibold text-gray-700 mb-4">Complaints</h3>
                        @if($hostel->complaints->count() > 0)
                            <div class="space-y-3">
                                @foreach($hostel->complaints as $complaint)
                                    <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <h4 class="font-semibold text-sm">{{ $complaint->title }}</h4>
                                                <p class="text-sm text-gray-600">{{ $complaint->description }}</p>
                                                <p class="text-xs text-gray-500 mt-1">
                                                    By: {{ $complaint->student->full_name }} |
                                                    {{ $complaint->created_at->diffForHumans() }}
                                                </p>
                                            </div>
                                            <div class="text-right">
                                                <span class="px-2 py-1 text-xs rounded-full
                                                    {{ $complaint->priority == 'critical' ? 'bg-red-100 text-red-800' : '' }}
                                                    {{ $complaint->priority == 'high' ? 'bg-orange-100 text-orange-800' : '' }}
                                                    {{ $complaint->priority == 'medium' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                    {{ $complaint->priority == 'low' ? 'bg-gray-100 text-gray-800' : '' }}">
                                                    {{ ucfirst($complaint->priority) }}
                                                </span>
                                                <span class="ml-2 px-2 py-1 text-xs rounded-full
                                                    {{ $complaint->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                    {{ $complaint->status == 'in_progress' ? 'bg-blue-100 text-blue-800' : '' }}
                                                    {{ $complaint->status == 'resolved' ? 'bg-green-100 text-green-800' : '' }}">
                                                    {{ ucfirst(str_replace('_', ' ', $complaint->status)) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-sm">No complaints reported.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
