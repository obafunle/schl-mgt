<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Library Management') }}
            </h2>
            @can('manage_library')
                <div class="flex space-x-2">
                    <a href="{{ route('admin.library.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">+ Add Book</a>
                    <a href="{{ route('admin.library.borrow') }}" class="bg-emerald-500 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded-lg">📖 Borrow Book</a>
                </div>
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
                        <div class="bg-indigo-50 p-4 rounded-lg"><span class="text-sm text-gray-500">Total Books</span><p class="text-2xl font-bold text-indigo-600">{{ $books->count() }}</p></div>
                        <div class="bg-emerald-50 p-4 rounded-lg"><span class="text-sm text-gray-500">Available</span><p class="text-2xl font-bold text-emerald-600">{{ $books->sum('available') }}</p></div>
                        <div class="bg-amber-50 p-4 rounded-lg"><span class="text-sm text-gray-500">Borrowed</span><p class="text-2xl font-bold text-amber-600">{{ $books->sum('borrowed') }}</p></div>
                        <div class="bg-rose-50 p-4 rounded-lg"><span class="text-sm text-gray-500">Categories</span><p class="text-2xl font-bold text-rose-600">{{ $books->pluck('category')->unique()->count() }}</p></div>
                    </div>

                    <!-- Books Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Book</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Author</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Qty</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Available</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Borrowed</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($books as $book)
                                    <tr>
                                        <td class="px-4 py-3"><div class="font-medium">{{ $book->title }}</div><div class="text-xs text-gray-500">{{ $book->category }}</div></td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $book->author }}</td>
                                        <td class="px-4 py-3 text-sm text-center">{{ $book->quantity }}</td>
                                        <td class="px-4 py-3 text-sm text-center text-emerald-600 font-bold">{{ $book->available }}</td>
                                        <td class="px-4 py-3 text-sm text-center text-amber-600">{{ $book->borrowed }}</td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="px-2 py-1 text-xs rounded-full {{ $book->available > 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                                {{ $book->available > 0 ? 'Available' : 'Out of Stock' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center text-sm">
                                            <a href="{{ route('admin.library.show', $book) }}" class="text-blue-600 hover:text-blue-900 mr-2">View</a>
                                            <a href="{{ route('admin.library.edit', $book) }}" class="text-emerald-600 hover:text-emerald-900 mr-2">Edit</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="px-4 py-8 text-center text-gray-500">📚 No books found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">{{ $books->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
