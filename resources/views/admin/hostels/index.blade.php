@extends('layouts.app')
<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Hostel Management') }}
            </h2>
            @can('manage_hostel')
                <a href="{{ route('admin.hostels.create') }}"
                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-md hover:shadow-lg transition duration-200">
                    + Add Hostel
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
                        <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-200">
                            <span class="text-sm text-gray-500">Total Hostels</span>
                            <p class="text-2xl font-bold text-indigo-600">{{ $hostels->total() }}</p>
                        </div>
                        <div class="bg-emerald-50 p-4 rounded-lg border border-emerald-200">
                            <span class="text-sm text-gray-500">Active</span>
                            <p class="text-2xl font-bold text-emerald-600">{{ $hostels->where('is_active', true)->count() }}</p>
                        </div>
                        <div class="bg-amber-50 p-4 rounded-lg border border-amber-200">
                            <span class="text-sm text-gray-500">Total Beds</span>
                            <p class="text-2xl font-bold text-amber-600">{{ $hostels->sum('total_beds') }}</p>
                        </div>
                        <div class="bg-rose-50 p-4 rounded-lg border border-rose-200">
                            <span class="text-sm text-gray-500">Occupancy Rate</span>
                            <p class="text-2xl font-bold text-rose-600">
                                @php
                                    $totalBeds = $hostels->sum('total_beds');
                                    $occupiedBeds = $hostels->sum('occupied_beds');
                                    $rate = $totalBeds > 0 ? round(($occupiedBeds / $totalBeds) * 100) : 0;
                                @endphp
                                {{ $rate }}%
                            </p>
                        </div>
                    </div>

                    <!-- Filters -->
                    <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div>
                            <input type="text" name="search" placeholder="Search hostels..."
                                   value="{{ request('search') }}"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                        </div>
                        <div>
                            <select name="type" class="w-full rounded-md border-gray-300 shadow-sm">
                                <option value="">All Types</option>
                                <option value="boys" {{ request('type') == 'boys' ? 'selected' : '' }}>Boys' Hostel</option>
                                <option value="girls" {{ request('type') == 'girls' ? 'selected' : '' }}>Girls' Hostel</option>
                                <option value="mixed" {{ request('type') == 'mixed' ? 'selected' : '' }}>Mixed</option>
                            </select>
                        </div>
                        <div>
                            <button type="submit" class="w-full bg-gray-800 text-white py-2 px-4 rounded-md hover:bg-gray-700 transition">
                                🔍 Filter
                            </button>
                        </div>
                    </form>

                    <!-- Hostels Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse($hostels as $hostel)
                            <div class="border rounded-lg p-5 hover:shadow-lg transition bg-white">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-800">{{ $hostel->name }}</h3>
                                        <p class="text-sm text-gray-500">{{ $hostel->code }}</p>
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded-full
                                        {{ $hostel->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $hostel->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                                <div class="mt-3 grid grid-cols-3 gap-2 text-center">
                                    <div class="bg-gray-50 p-2 rounded">
                                        <div class="text-sm font-bold text-blue-600">{{ $hostel->total_rooms }}</div>
                                        <div class="text-xs text-gray-500">Rooms</div>
                                    </div>
                                    <div class="bg-gray-50 p-2 rounded">
                                        <div class="text-sm font-bold text-emerald-600">{{ $hostel->available_beds }}</div>
                                        <div class="text-xs text-gray-500">Available</div>
                                    </div>
                                    <div class="bg-gray-50 p-2 rounded">
                                        <div class="text-sm font-bold text-amber-600">{{ $hostel->occupied_beds }}</div>
                                        <div class="text-xs text-gray-500">Occupied</div>
                                    </div>
                                </div>
                                <div class="mt-4 flex flex-wrap gap-2">
                                    <a href="{{ route('admin.hostels.show', $hostel) }}"
                                       class="text-sm bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded">View</a>
                                    <a href="{{ route('admin.hostels.edit', $hostel) }}"
                                       class="text-sm bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-1 rounded">Edit</a>
                                    <a href="{{ route('admin.hostels.rooms', $hostel) }}"
                                       class="text-sm bg-amber-500 hover:bg-amber-600 text-white px-3 py-1 rounded">Rooms</a>
                                    <a href="{{ route('admin.hostels.attendance', $hostel) }}"
                                       class="text-sm bg-purple-500 hover:bg-purple-600 text-white px-3 py-1 rounded">Attendance</a>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full text-center py-12">
                                <span class="text-4xl block mb-2">🏠</span>
                                <p class="text-gray-500">No hostels found. Create your first hostel.</p>
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-4">
                        {{ $hostels->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
