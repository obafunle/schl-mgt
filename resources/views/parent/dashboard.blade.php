@extends('layouts.parent')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Welcome Section -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-800">
                👋 Welcome back, {{ Auth::user()->parent->first_name }}!
            </h1>
            <p class="text-gray-600">Here's what's happening with your children's education.</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-sm text-gray-500">Children</span>
                        <p class="text-2xl font-bold text-blue-600">{{ $stats['total_children'] }}</p>
                    </div>
                    <div class="text-3xl">👨‍👧‍👦</div>
                </div>
            </div>
            <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-sm text-gray-500">Pending Exeats</span>
                        <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending_exeats'] }}</p>
                    </div>
                    <div class="text-3xl">📋</div>
                </div>
            </div>
            <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-sm text-gray-500">Unpaid Fees</span>
                        <p class="text-2xl font-bold text-red-600">{{ $stats['unpaid_invoices'] }}</p>
                    </div>
                    <div class="text-3xl">💰</div>
                </div>
            </div>
            <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-sm text-gray-500">Upcoming Exams</span>
                        <p class="text-2xl font-bold text-purple-600">{{ $stats['upcoming_exams'] }}</p>
                    </div>
                    <div class="text-3xl">📝</div>
                </div>
            </div>
        </div>

        <!-- Notifications -->
        @if($notifications->count() > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="font-semibold text-gray-700 mb-3">🔔 Notifications</h3>
                    <div class="space-y-2">
                        @foreach($notifications as $notification)
                            <a href="{{ $notification['link'] }}" class="block p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                <div class="flex items-center">
                                    <span class="text-2xl mr-3">{{ $notification['icon'] }}</span>
                                    <span class="text-sm text-gray-700">{{ $notification['message'] }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Children Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($children as $child)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xl">
                                    {{ substr($child->first_name, 0, 1) }}{{ substr($child->last_name, 0, 1) }}
                                </div>
                                <div class="ml-3">
                                    <h4 class="font-semibold text-gray-800">{{ $child->full_name }}</h4>
                                    <p class="text-sm text-gray-500">
                                        {{ $child->class->name ?? 'No Class' }} 
                                        @if($child->classArm)
                                            ({{ $child->classArm->name }})
                                        @endif
                                    </p>
                                    <p class="text-xs text-gray-400">Adm: {{ $child->admission_number }}</p>
                                </div>
                            </div>
                            <span class="px-2 py-1 text-xs rounded-full {{ $child->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($child->status) }}
                            </span>
                        </div>

                        <div class="grid grid-cols-3 gap-2 text-center text-sm">
                            <div class="p-2 bg-gray-50 rounded">
                                <div class="font-bold text-blue-600">
                                    {{ $child->grades->where('term_id', $currentTerm?->id)->count() }}
                                </div>
                                <div class="text-gray-500 text-xs">Subjects</div>
                            </div>
                            <div class="p-2 bg-gray-50 rounded">
                                <div class="font-bold text-green-600">
                                    {{ $child->grades->where('term_id', $currentTerm?->id)->whereIn('grade', ['A','B','C','D','E'])->count() }}
                                </div>
                                <div class="text-gray-500 text-xs">Passed</div>
                            </div>
                            <div class="p-2 bg-gray-50 rounded">
                                <div class="font-bold text-red-600">
                                    {{ $child->grades->where('term_id', $currentTerm?->id)->where('grade', 'F')->count() }}
                                </div>
                                <div class="text-gray-500 text-xs">Failed</div>
                            </div>
                        </div>

                        <div class="mt-4 flex flex-wrap gap-2">
                            <a href="{{ route('parent.child.profile', $child->id) }}" 
                               class="px-3 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600">
                                👤 Profile
                            </a>
                            <a href="{{ route('parent.child.grades', $child->id) }}" 
                               class="px-3 py-1 bg-green-500 text-white text-xs rounded hover:bg-green-600">
                                📊 Grades
                            </a>
                            <a href="{{ route('parent.fees', $child->id) }}" 
                               class="px-3 py-1 bg-yellow-500 text-white text-xs rounded hover:bg-yellow-600">
                                💰 Fees
                            </a>
                            <a href="{{ route('parent.child.attendance', $child->id) }}" 
                               class="px-3 py-1 bg-purple-500 text-white text-xs rounded hover:bg-purple-600">
                                📋 Attendance
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Recent Activities -->
        @if($recentActivities->count() > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6">
                    <h3 class="font-semibold text-gray-700 mb-3">📋 Recent Activities</h3>
                    <div class="space-y-3">
                        @foreach($recentActivities as $activity)
                            <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                <span class="text-2xl mr-3">{{ $activity['icon'] }}</span>
                                <div>
                                    <p class="text-sm text-gray-700">{{ $activity['activity'] }}</p>
                                    <div class="flex items-center text-xs text-gray-500">
                                        <span>{{ $activity['child'] }}</span>
                                        <span class="mx-2">•</span>
                                        <span>{{ $activity['date']->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection