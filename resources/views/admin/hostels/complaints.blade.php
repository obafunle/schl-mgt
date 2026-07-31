<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $hostel->name }} - Complaints
            </h2>
            <a href="{{ route('admin.hostels.show', $hostel) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg text-sm">← Back</a>
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
                        <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                            <span class="text-sm text-gray-500">Total Complaints</span>
                            <p class="text-2xl font-bold text-yellow-600">{{ $complaints->total() }}</p>
                        </div>
                        <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                            <span class="text-sm text-gray-500">Pending</span>
                            <p class="text-2xl font-bold text-red-600">{{ $complaints->where('status', 'pending')->count() }}</p>
                        </div>
                        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                            <span class="text-sm text-gray-500">In Progress</span>
                            <p class="text-2xl font-bold text-blue-600">{{ $complaints->where('status', 'in_progress')->count() }}</p>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                            <span class="text-sm text-gray-500">Resolved</span>
                            <p class="text-2xl font-bold text-green-600">{{ $complaints->where('status', 'resolved')->count() }}</p>
                        </div>
                    </div>

                    <!-- Complaints List -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Priority</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($complaints as $complaint)
                                    <tr>
                                        <td class="px-4 py-3">
                                            <div class="font-medium text-gray-900">{{ $complaint->title }}</div>
                                            <div class="text-xs text-gray-500">{{ $complaint->created_at->diffForHumans() }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $complaint->student->full_name }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $complaint->getCategoryLabel() }}</td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="px-2 py-1 text-xs rounded-full
                                                {{ $complaint->priority == 'critical' ? 'bg-red-100 text-red-800' : '' }}
                                                {{ $complaint->priority == 'high' ? 'bg-orange-100 text-orange-800' : '' }}
                                                {{ $complaint->priority == 'medium' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $complaint->priority == 'low' ? 'bg-gray-100 text-gray-800' : '' }}">
                                                {{ ucfirst($complaint->priority) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="px-2 py-1 text-xs rounded-full
                                                {{ $complaint->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $complaint->status == 'in_progress' ? 'bg-blue-100 text-blue-800' : '' }}
                                                {{ $complaint->status == 'resolved' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $complaint->status == 'closed' ? 'bg-gray-100 text-gray-800' : '' }}">
                                                {{ ucfirst(str_replace('_', ' ', $complaint->status)) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center text-sm">
                                            <a href="#" class="text-blue-600 hover:text-blue-900 mr-2">View</a>
                                            @if($complaint->status == 'pending')
                                                <form action="#" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-emerald-600 hover:text-emerald-900 mr-2">Resolve</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                            <span class="text-4xl block mb-2">📋</span>
                                            No complaints found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $complaints->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
