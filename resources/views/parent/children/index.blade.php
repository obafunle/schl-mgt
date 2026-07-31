@extends('layouts.parent')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <div class="mb-6 flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">👨‍👧‍👦 My Children</h1>
                <p class="text-gray-600 text-sm">View and manage all your children's educational information</p>
            </div>
            <span class="px-4 py-2 bg-blue-100 text-blue-800 rounded-lg text-sm font-semibold">
                {{ $children->count() }} Children
            </span>
        </div>

        @if($children->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($children as $child)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition">
                        <div class="p-6">
                            <!-- Child Avatar & Info -->
                            <div class="flex items-center mb-4">
                                <div class="h-16 w-16 rounded-full bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center text-white text-2xl font-bold">
                                    {{ substr($child->first_name, 0, 1) }}{{ substr($child->last_name, 0, 1) }}
                                </div>
                                <div class="ml-4">
                                    <h3 class="font-bold text-lg text-gray-800">{{ $child->full_name }}</h3>
                                    <p class="text-sm text-gray-500">{{ $child->admission_number }}</p>
                                    <span class="text-xs px-2 py-1 rounded-full {{ $child->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($child->status) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Class Information -->
                            <div class="grid grid-cols-2 gap-2 mb-4 text-sm">
                                <div class="bg-gray-50 p-2 rounded">
                                    <span class="text-gray-500">Class</span>
                                    <p class="font-semibold">{{ $child->class->name ?? 'Not Assigned' }}</p>
                                </div>
                                <div class="bg-gray-50 p-2 rounded">
                                    <span class="text-gray-500">Arm</span>
                                    <p class="font-semibold">{{ $child->classArm->name ?? 'N/A' }}</p>
                                </div>
                            </div>

                            <!-- Quick Stats -->
                            <div class="grid grid-cols-3 gap-2 mb-4">
                                <div class="text-center p-2 bg-blue-50 rounded">
                                    <div class="font-bold text-blue-600">
                                        {{ $child->grades->where('term_id', $currentTerm?->id)->count() }}
                                    </div>
                                    <div class="text-xs text-gray-500">Subjects</div>
                                </div>
                                <div class="text-center p-2 bg-green-50 rounded">
                                    <div class="font-bold text-green-600">
                                        {{ $child->grades->where('term_id', $currentTerm?->id)->whereIn('grade', ['A','B','C','D','E'])->count() }}
                                    </div>
                                    <div class="text-xs text-gray-500">Passed</div>
                                </div>
                                <div class="text-center p-2 bg-red-50 rounded">
                                    <div class="font-bold text-red-600">
                                        {{ $child->grades->where('term_id', $currentTerm?->id)->where('grade', 'F')->count() }}
                                    </div>
                                    <div class="text-xs text-gray-500">Failed</div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('parent.child.profile', $child->id) }}" 
                                   class="flex-1 text-center px-3 py-2 bg-blue-500 text-white text-sm rounded hover:bg-blue-600 transition">
                                    👤 Profile
                                </a>
                                <a href="{{ route('parent.child.grades', $child->id) }}" 
                                   class="flex-1 text-center px-3 py-2 bg-green-500 text-white text-sm rounded hover:bg-green-600 transition">
                                    📊 Grades
                                </a>
                                <a href="{{ route('parent.fees', $child->id) }}" 
                                   class="flex-1 text-center px-3 py-2 bg-yellow-500 text-white text-sm rounded hover:bg-yellow-600 transition">
                                    💰 Fees
                                </a>
                                <a href="{{ route('parent.child.attendance', $child->id) }}" 
                                   class="flex-1 text-center px-3 py-2 bg-purple-500 text-white text-sm rounded hover:bg-purple-600 transition">
                                    📋 Attendance
                                </a>
                            </div>

                            <!-- Relationship Info -->
                            <div class="mt-3 pt-3 border-t border-gray-200 flex items-center justify-between text-xs text-gray-500">
                                <span>Relationship: <span class="font-medium">{{ ucfirst($child->pivot->relationship) }}</span></span>
                                @if($child->pivot->is_primary_contact)
                                    <span class="text-green-600 font-medium">⭐ Primary Contact</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-12 text-center">
                    <div class="text-6xl mb-4">👶</div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">No Children Registered</h3>
                    <p class="text-gray-500">You haven't registered any children yet. Please contact the school administration.</p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection