<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Fee Structures') }}
            </h2>
            @can('create_fees')
                <a href="{{ route('admin.fees.create') }}"
                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                    + Add New Fee
                </a>
            @endcan
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <!-- Filters -->
                    <div class="mb-6">
                        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                            <div>
                                <input type="text" name="search" placeholder="Search fees..."
                                       value="{{ request('search') }}"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                            </div>
                            <div>
                                <select name="class_id" class="w-full rounded-md border-gray-300 shadow-sm">
                                    <option value="">All Classes</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                            {{ $class->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <select name="frequency" class="w-full rounded-md border-gray-300 shadow-sm">
                                    <option value="">All Frequencies</option>
                                    <option value="one-time" {{ request('frequency') == 'one-time' ? 'selected' : '' }}>One Time</option>
                                    <option value="termly" {{ request('frequency') == 'termly' ? 'selected' : '' }}>Termly</option>
                                    <option value="yearly" {{ request('frequency') == 'yearly' ? 'selected' : '' }}>Yearly</option>
                                    <option value="monthly" {{ request('frequency') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                </select>
                            </div>
                            <div>
                                <select name="is_active" class="w-full rounded-md border-gray-300 shadow-sm">
                                    <option value="">All Status</option>
                                    <option value="true" {{ request('is_active') === 'true' ? 'selected' : '' }}>Active</option>
                                    <option value="false" {{ request('is_active') === 'false' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <div>
                                <button type="submit" class="w-full bg-gray-800 text-white py-2 px-4 rounded-md hover:bg-gray-700">
                                    🔍 Filter
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Summary Stats -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <span class="text-sm text-gray-500">Total Fees</span>
                            <p class="text-2xl font-bold text-blue-600">{{ $feeStructures->total() }}</p>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <span class="text-sm text-gray-500">Active Fees</span>
                            <p class="text-2xl font-bold text-green-600">{{ $feeStructures->where('is_active', true)->count() }}</p>
                        </div>
                        <div class="bg-yellow-50 p-4 rounded-lg">
                            <span class="text-sm text-gray-500">Mandatory Fees</span>
                            <p class="text-2xl font-bold text-yellow-600">{{ $feeStructures->where('is_mandatory', true)->count() }}</p>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg">
                            <span class="text-sm text-gray-500">Total Value</span>
                            <p class="text-2xl font-bold text-purple-600">
                                ₦{{ number_format($feeStructures->sum('amount'), 0) }}
                            </p>
                        </div>
                    </div>

                    <!-- Fees Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        <input type="checkbox" id="select-all" class="rounded border-gray-300">
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fee Details</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Frequency</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Class</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($feeStructures as $fee)
                                    <tr>
                                        <td class="px-6 py-4">
                                            <input type="checkbox" name="ids[]" value="{{ $fee->id }}"
                                                   class="fee-checkbox rounded border-gray-300">
                                        </td>
                                        <td class="px-6 py-4">
                                            <div>
                                                <div class="font-medium text-gray-900">{{ $fee->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $fee->code }}</div>
                                                @if($fee->description)
                                                    <div class="text-xs text-gray-400 mt-1">{{ Str::limit($fee->description, 50) }}</div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="font-semibold text-gray-900">
                                                ₦{{ number_format($fee->amount, 2) }}
                                            </span>
                                            @if($fee->is_mandatory)
                                                <span class="ml-2 text-xs bg-red-100 text-red-800 px-2 py-0.5 rounded">Required</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-1 text-xs rounded-full
                                                {{ $fee->frequency == 'one-time' ? 'bg-gray-100 text-gray-800' : '' }}
                                                {{ $fee->frequency == 'termly' ? 'bg-blue-100 text-blue-800' : '' }}
                                                {{ $fee->frequency == 'yearly' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $fee->frequency == 'monthly' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                                {{ ucfirst(str_replace('-', ' ', $fee->frequency)) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            @if($fee->class)
                                                {{ $fee->class->name }}
                                                @if($fee->classArm)
                                                    ({{ $fee->classArm->name }})
                                                @endif
                                            @else
                                                <span class="text-gray-400">All Classes</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-1 text-xs rounded-full
                                                {{ $fee->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $fee->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm font-medium">
                                            <a href="{{ route('admin.fees.show', $fee) }}"
                                               class="text-blue-600 hover:text-blue-900 mr-2">View</a>
                                            <a href="{{ route('admin.fees.edit', $fee) }}"
                                               class="text-green-600 hover:text-green-900 mr-2">Edit</a>
                                            <button onclick="toggleFee('{{ $fee->id }}')"
                                                    class="text-{{ $fee->is_active ? 'yellow' : 'green' }}-600 hover:text-{{ $fee->is_active ? 'yellow' : 'green' }}-900 mr-2">
                                                {{ $fee->is_active ? 'Deactivate' : 'Activate' }}
                                            </button>
                                            <button onclick="cloneFee('{{ $fee->id }}')"
                                                    class="text-purple-600 hover:text-purple-900 mr-2">Clone</button>
                                            @if($fee->invoices_count === 0)
                                                <form action="{{ route('admin.fees.destroy', $fee) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" onclick="return confirm('Are you sure?')"
                                                            class="text-red-600 hover:text-red-900">Delete</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                            No fee structures found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Bulk Actions -->
                    @if($feeStructures->count() > 0)
                        <div class="mt-4 flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <span class="text-sm text-gray-500">Selected: <span id="selected-count">0</span></span>
                                <button onclick="bulkDelete()"
                                        class="text-sm text-red-600 hover:text-red-800 disabled:opacity-50 disabled:cursor-not-allowed"
                                        id="bulk-delete-btn" disabled>
                                    Delete Selected
                                </button>
                            </div>
                            <div>
                                {{ $feeStructures->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Select all checkbox
        document.getElementById('select-all').addEventListener('change', function() {
            document.querySelectorAll('.fee-checkbox').forEach(cb => {
                cb.checked = this.checked;
            });
            updateSelectedCount();
        });

        // Individual checkboxes
        document.querySelectorAll('.fee-checkbox').forEach(cb => {
            cb.addEventListener('change', updateSelectedCount);
        });

        function updateSelectedCount() {
            const selected = document.querySelectorAll('.fee-checkbox:checked').length;
            document.getElementById('selected-count').textContent = selected;
            document.getElementById('bulk-delete-btn').disabled = selected === 0;
        }

        function toggleFee(id) {
            fetch(`{{ url('admin/fees') }}/${id}/toggle-active`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(() => window.location.reload());
        }

        function cloneFee(id) {
            fetch(`{{ url('admin/fees') }}/${id}/clone`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(response => {
                if (response.ok) {
                    window.location.reload();
                }
            });
        }

        function bulkDelete() {
            const ids = [];
            document.querySelectorAll('.fee-checkbox:checked').forEach(cb => {
                ids.push(cb.value);
            });

            if (ids.length === 0) return;

            if (confirm(`Are you sure you want to delete ${ids.length} fee structure(s)?`)) {
                fetch('{{ route("admin.fees.bulk-delete") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ ids: ids })
                }).then(response => response.json())
                  .then(data => {
                      if (data.success) {
                          window.location.reload();
                      } else {
                          alert(data.message || 'Error deleting fees');
                      }
                  });
            }
        }
    </script>
    @endpush
</x-app-layout>
