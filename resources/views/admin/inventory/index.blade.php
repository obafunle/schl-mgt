<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Inventory Management') }}
            </h2>
            @can('manage_inventory')
                <a href="{{ route('admin.inventory.create') }}"
                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-md hover:shadow-lg transition duration-200">
                    + Add Item
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
                        <div class="bg-indigo-50 p-4 rounded-lg"><span class="text-sm text-gray-500">Total Items</span><p class="text-2xl font-bold text-indigo-600">{{ $items->count() }}</p></div>
                        <div class="bg-emerald-50 p-4 rounded-lg"><span class="text-sm text-gray-500">In Stock</span><p class="text-2xl font-bold text-emerald-600">{{ $items->where('quantity', '>', 0)->count() }}</p></div>
                        <div class="bg-amber-50 p-4 rounded-lg"><span class="text-sm text-gray-500">Low Stock</span><p class="text-2xl font-bold text-amber-600">{{ $items->filter(function($i) { return $i->quantity <= $i->minimum_stock && $i->quantity > 0; })->count() }}</p></div>
                        <div class="bg-rose-50 p-4 rounded-lg"><span class="text-sm text-gray-500">Out of Stock</span><p class="text-2xl font-bold text-rose-600">{{ $items->where('quantity', 0)->count() }}</p></div>
                    </div>

                    <!-- Items Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Qty</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Min Stock</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($items as $item)
                                    <tr>
                                        <td class="px-4 py-3">
                                            <div class="font-medium">{{ $item->name }}</div>
                                            <div class="text-xs text-gray-500">₦{{ number_format($item->unit_price, 2) }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $item->code }}</td>
                                        <td class="px-4 py-3 text-sm text-center font-bold">{{ $item->quantity }}</td>
                                        <td class="px-4 py-3 text-sm text-center">{{ $item->minimum_stock }}</td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="px-2 py-1 text-xs rounded-full
                                                {{ $item->getStockStatus() == 'In Stock' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                                {{ $item->getStockStatus() == 'Low Stock' ? 'bg-amber-100 text-amber-800' : '' }}
                                                {{ $item->getStockStatus() == 'Out of Stock' ? 'bg-rose-100 text-rose-800' : '' }}">
                                                {{ $item->getStockStatus() }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center text-sm">
                                            <a href="{{ route('admin.inventory.show', $item) }}" class="text-blue-600 hover:text-blue-900 mr-2">View</a>
                                            <a href="{{ route('admin.inventory.edit', $item) }}" class="text-emerald-600 hover:text-emerald-900 mr-2">Edit</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">📦 No inventory items found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $items->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
