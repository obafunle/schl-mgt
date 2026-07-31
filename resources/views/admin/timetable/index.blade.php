<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Timetable Management') }}
            </h2>
            @can('manage_timetable')
                <a href="{{ route('admin.timetable.create') }}"
                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg">
                    + Create Timetable Entry
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

            @if(session('warning'))
                <div class="mb-4 p-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700">
                    {{ session('warning') }}
                </div>
            @endif

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 text-center">
                        <a href="#" onclick="document.getElementById('view-timetable-form').submit();"
                           class="text-blue-600 hover:text-blue-800">
                            <div class="text-3xl mb-2">📅</div>
                            <div class="font-semibold">View Timetable</div>
                            <div class="text-sm text-gray-500">Class timetable</div>
                        </a>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 text-center">
                        <a href="{{ route('admin.timetable.create') }}"
                           class="text-green-600 hover:text-green-800">
                            <div class="text-3xl mb-2">➕</div>
                            <div class="font-semibold">Add Entry</div>
                            <div class="text-sm text-gray-500">Single entry</div>
                        </a>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 text-center">
                        <a href="#" onclick="document.getElementById('generate-timetable-form').submit();"
                           class="text-purple-600 hover:text-purple-800">
                            <div class="text-3xl mb-2">⚡</div>
                            <div class="font-semibold">Auto Generate</div>
                            <div class="text-sm text-gray-500">Generate timetable</div>
                        </a>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 text-center">
                        <a href="#" onclick="document.getElementById('clone-timetable-form').submit();"
                           class="text-yellow-600 hover:text-yellow-800">
                            <div class="text-3xl mb-2">📋</div>
                            <div class="font-semibold">Clone Timetable</div>
                            <div class="text-sm text-gray-500">From previous term</div>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Hidden Forms -->
            <form id="view-timetable-form" action="{{ route('admin.timetable.show') }}" method="GET" style="display:none;">
                @csrf
                <input type="hidden" name="class_id" value="{{ $classes->first()->id ?? '' }}">
                <input type="hidden" name="term_id" value="{{ $terms->first()->id ?? '' }}">
                <input type="hidden" name="academic_year_id" value="{{ $academicYears->first()->id ?? '' }}">
            </form>

            <form id="generate-timetable-form" action="{{ route('admin.timetable.generate') }}" method="POST" style="display:none;">
                @csrf
                <input type="hidden" name="class_id" value="{{ $classes->first()->id ?? '' }}">
                <input type="hidden" name="term_id" value="{{ $terms->first()->id ?? '' }}">
                <input type="hidden" name="academic_year_id" value="{{ $academicYears->first()->id ?? '' }}">
            </form>

            <form id="clone-timetable-form" action="{{ route('admin.timetable.clone') }}" method="POST" style="display:none;">
                @csrf
                <input type="hidden" name="from_term_id" value="{{ $terms->first()->id ?? '' }}">
                <input type="hidden" name="to_term_id" value="{{ $terms->last()->id ?? '' }}">
                <input type="hidden" name="academic_year_id" value="{{ $academicYears->first()->id ?? '' }}">
            </form>

            <!-- View Timetable Form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">📅 View Class Timetable</h3>
                    <form action="{{ route('admin.timetable.show') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Class</label>
                            <select name="class_id" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                <option value="">Select Class</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Class Arm</label>
                            <select name="class_arm_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                <option value="">All Arms</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Term</label>
                            <select name="term_id" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                @foreach($terms as $term)
                                    <option value="{{ $term->id }}">{{ $term->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Academic Year</label>
                            <select name="academic_year_id" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}">{{ $year->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-4">
                            <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700">
                                🔍 View Timetable
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <span class="text-sm text-gray-500">Total Classes</span>
                    <p class="text-2xl font-bold text-blue-600">{{ $classes->count() }}</p>
                </div>
                <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                    <span class="text-sm text-gray-500">Active Terms</span>
                    <p class="text-2xl font-bold text-green-600">{{ $terms->where('is_active', true)->count() }}</p>
                </div>
                <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                    <span class="text-sm text-gray-500">School Days</span>
                    <p class="text-2xl font-bold text-yellow-600">{{ \App\Models\TimetableDay::where('is_school_day', true)->count() }}</p>
                </div>
                <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                    <span class="text-sm text-gray-500">Periods Per Day</span>
                    <p class="text-2xl font-bold text-purple-600">{{ \App\Models\TimetablePeriod::where('type', 'academic')->count() }}</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
