<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $hostel->name }} - Hostel Fees
            </h2>
            <div class="flex space-x-2">
                <button onclick="document.getElementById('fee-form').style.display = 'block'"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg text-sm">+ Add Fee</button>
                <a href="{{ route('admin.hostels.show', $hostel) }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg text-sm">← Back</a>
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

            <!-- Add Fee Form -->
            <div id="fee-form" style="display: none;" class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Add Hostel Fee</h3>
                    <form action="#" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Fee Name <span class="text-red-500">*</span></label>
                                <input type="text" name="name" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Amount (₦) <span class="text-red-500">*</span></label>
                                <input type="number" name="amount" step="0.01" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Frequency</label>
                                <select name="frequency" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                    <option value="termly">Termly</option>
                                    <option value="yearly">Yearly</option>
                                    <option value="monthly">Monthly</option>
                                    <option value="one-time">One Time</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-3 flex justify-end space-x-2">
                            <button type="button" onclick="document.getElementById('fee-form').style.display = 'none'"
                                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Add Fee</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Fees List -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fee Name</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Frequency</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($fees as $fee)
                                    <tr>
                                        <td class="px-4 py-3">
                                            <div class="font-medium text-gray-900">{{ $fee->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $fee->description ?? '' }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-sm font-semibold">₦{{ number_format($fee->amount, 2) }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ ucfirst($fee->frequency) }}</td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="px-2 py-1 text-xs rounded-full
                                                {{ $fee->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ $fee->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center text-sm">
                                            <a href="#" class="text-emerald-600 hover:text-emerald-900 mr-2">Edit</a>
                                            <form action="#" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('Delete this fee?')"
                                                        class="text-rose-600 hover:text-rose-900">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                            <span class="text-4xl block mb-2">💰</span>
                                            No fees configured for this hostel.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $fees->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
