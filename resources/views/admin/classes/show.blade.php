<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $class->name }} - Class Details
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.classes.edit', $class) }}" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg text-sm">✏️ Edit</a>
                <a href="{{ route('admin.classes.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg text-sm">← Back</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="bg-blue-50 p-4 rounded-lg"><span class="text-sm text-gray-500">Code</span><p class="font-bold">{{ $class->code }}</p></div>
                        <div class="bg-green-50 p-4 rounded-lg"><span class="text-sm text-gray-500">Level</span><p class="font-bold">{{ $class->level }}</p></div>
                        <div class="bg-purple-50 p-4 rounded-lg"><span class="text-sm text-gray-500">Category</span><p class="font-bold">{{ ucfirst($class->category) }}</p></div>
                    </div>

                    <div class="mt-6">
                        <h3 class="font-semibold text-gray-700 mb-3">Class Arms</h3>
                        @if($class->arms->count() > 0)
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                @foreach($class->arms as $arm)
                                    <div class="border rounded-lg p-3 text-center">
                                        <div class="font-bold">{{ $arm->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $arm->code }}</div>
                                        <div class="text-xs text-gray-400">Capacity: {{ $arm->capacity }}</div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-sm">No arms added yet.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
