<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $hostel->name }} - Rooms
            </h2>
            <div class="flex space-x-2">
                <button onclick="document.getElementById('room-form').style.display = 'block'"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg text-sm">+ Add Room</button>
                <a href="{{ route('admin.hostels.show', $hostel) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg text-sm">← Back</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div id="room-form" style="display: none;" class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form action="{{ route('admin.hostels.rooms.store', $hostel) }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Room Number *</label>
                                <input type="text" name="room_number" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                                <select name="room_type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                    <option value="dormitory">Dormitory</option>
                                    <option value="shared">Shared</option>
                                    <option value="single">Single</option>
                                    <option value="suite">Suite</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Capacity *</label>
                                <input type="number" name="capacity" value="4" min="1" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Floor</label>
                                <input type="text" name="floor" placeholder="Ground, 1st" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                            </div>
                        </div>
                        <div class="mt-3 flex justify-end space-x-2">
                            <button type="button" onclick="document.getElementById('room-form').style.display = 'none'"
                                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Add Room</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @forelse($rooms as $room)
                            <div class="border rounded-lg p-4 hover:shadow-md transition">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="font-bold text-gray-800">Room {{ $room->room_number }}</h4>
                                        <p class="text-sm text-gray-500">{{ $room->getRoomTypeLabel() }}</p>
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded-full {{ $room->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $room->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                                <div class="mt-3 grid grid-cols-3 gap-2 text-center">
                                    <div><span class="text-sm font-bold">{{ $room->capacity }}</span><div class="text-xs text-gray-500">Capacity</div></div>
                                    <div><span class="text-sm font-bold text-blue-600">{{ $room->occupied }}</span><div class="text-xs text-gray-500">Occupied</div></div>
                                    <div><span class="text-sm font-bold text-green-600">{{ $room->available }}</span><div class="text-xs text-gray-500">Available</div></div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full text-center py-8 text-gray-500">No rooms added yet.</div>
                        @endforelse
                    </div>
                    <div class="mt-4">{{ $rooms->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
