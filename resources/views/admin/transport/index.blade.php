<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Transport Management') }}
            </h2>
            @can('manage_transport')
                <a href="{{ route('admin.transport.create') }}"
                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-md hover:shadow-lg transition duration-200">
                    + Add Vehicle
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Stats -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <div class="bg-indigo-50 p-4 rounded-lg"><span class="text-sm text-gray-500">Total Vehicles</span><p class="text-2xl font-bold text-indigo-600">{{ $transport->count() }}</p></div>
                        <div class="bg-emerald-50 p-4 rounded-lg"><span class="text-sm text-gray-500">Active</span><p class="text-2xl font-bold text-emerald-600">{{ $transport->where('is_active', true)->count() }}</p></div>
                        <div class="bg-amber-50 p-4 rounded-lg"><span class="text-sm text-gray-500">Capacity</span><p class="text-2xl font-bold text-amber-600">{{ $transport->sum('capacity') }}</p></div>
                        <div class="bg-rose-50 p-4 rounded-lg"><span class="text-sm text-gray-500">Assigned Students</span><p class="text-2xl font-bold text-rose-600">{{ $transport->sum(function($v) { return $v->getAssignedStudentsCount(); }) }}</p></div>
                    </div>

                    <!-- Transport Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse($transport as $vehicle)
                            <div class="border rounded-lg p-5 hover:shadow-lg transition">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-800">{{ $vehicle->name }}</h3>
                                        <p class="text-sm text-gray-500">{{ $vehicle->code }}</p>
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded-full {{ $vehicle->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $vehicle->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                                <div class="mt-3 grid grid-cols-3 gap-2 text-center">
                                    <div class="bg-gray-50 p-2 rounded"><div class="text-sm font-bold text-blue-600">{{ $vehicle->capacity }}</div><div class="text-xs text-gray-500">Capacity</div></div>
                                    <div class="bg-gray-50 p-2 rounded"><div class="text-sm font-bold text-emerald-600">{{ $vehicle->getAssignedStudentsCount() }}</div><div class="text-xs text-gray-500">Assigned</div></div>
                                    <div class="bg-gray-50 p-2 rounded"><div class="text-sm font-bold text-amber-600">{{ $vehicle->getAvailableSeats() }}</div><div class="text-xs text-gray-500">Available</div></div>
                                </div>
                                <div class="mt-3 text-sm"><span class="text-gray-500">Driver:</span> {{ $vehicle->driver_name ?? 'Not Assigned' }}</div>
                                <div class="mt-4 flex flex-wrap gap-2">
                                    <a href="{{ route('admin.transport.show', $vehicle) }}" class="text-sm bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded">View</a>
                                    <a href="{{ route('admin.transport.edit', $vehicle) }}" class="text-sm bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-1 rounded">Edit</a>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full text-center py-12"><span class="text-4xl block mb-2">🚌</span><p class="text-gray-500">No vehicles found.</p></div>
                        @endforelse
                    </div>
                    <div class="mt-4">{{ $transport->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
