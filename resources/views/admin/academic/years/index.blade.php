<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Academic Years & Terms') }}
            </h2>
            @can('manage_academic')
                <a href="{{ route('admin.academic-years.create') }}"
                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow-md hover:shadow-lg transition duration-200">
                    + Add Academic Year
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

                    <!-- Academic Years Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse($years as $year)
                            <div class="border rounded-lg p-5 hover:shadow-lg transition {{ $year->is_current ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200' }}">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-800">{{ $year->name }}</h3>
                                        <p class="text-sm text-gray-500">{{ $year->start_date->format('M d, Y') }} - {{ $year->end_date->format('M d, Y') }}</p>
                                    </div>
                                    @if($year->is_current)
                                        <span class="px-2 py-1 text-xs bg-indigo-100 text-indigo-800 rounded-full">Current</span>
                                    @endif
                                </div>

                                <div class="mt-3">
                                    <span class="text-sm text-gray-500">Terms:</span>
                                    <div class="flex gap-2 mt-1 flex-wrap">
                                        @foreach($year->terms as $term)
                                            <span class="px-2 py-1 text-xs rounded-full
                                                {{ $term->is_current ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600' }}">
                                                {{ $term->name }}
                                                @if($term->is_current) ⭐ @endif
                                            </span>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="mt-4 flex flex-wrap gap-2">
                                    @if(!$year->is_current)
                                        <form action="{{ route('admin.academic-years.set-current', $year) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-sm bg-indigo-500 hover:bg-indigo-600 text-white px-3 py-1 rounded">
                                                Set Current
                                            </button>
                                        </form>
                                    @endif
                                    <a href="{{ route('admin.academic-years.edit', $year) }}"
                                       class="text-sm bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-1 rounded">Edit</a>
                                    <form action="{{ route('admin.academic-years.destroy', $year) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Delete this academic year?')"
                                                class="text-sm bg-rose-500 hover:bg-rose-600 text-white px-3 py-1 rounded">Delete</button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full text-center py-12">
                                <span class="text-4xl block mb-2">📅</span>
                                <p class="text-gray-500">No academic years found. Create your first academic year.</p>
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-6">
                        {{ $years->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
