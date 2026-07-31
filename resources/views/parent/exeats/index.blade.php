@extends('layouts.parent')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-6 flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">📋 Exeat Requests</h1>
                <p class="text-gray-600 text-sm">Request permission for your child to leave school</p>
            </div>
            <button onclick="document.getElementById('exeat-form').style.display = 'block'"
                    class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition">
                + New Exeat Request
            </button>
        </div>

        <!-- Exeat Request Form -->
        <div id="exeat-form" style="display: none;" class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">✏️ New Exeat Request</h3>
                <form action="{{ route('parent.exeats.store') }}" method="POST">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Student -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Student <span class="text-red-500">*</span></label>
                            <select name="student_id" required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                <option value="">Select Student</option>
                                @foreach($children as $child)
                                    <option value="{{ $child->id }}">{{ $child->full_name }} ({{ $child->admission_number }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Term -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Term</label>
                            <select name="term_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                @foreach($terms as $term)
                                    <option value="{{ $term->id }}">{{ $term->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Departure Date -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Departure Date <span class="text-red-500">*</span></label>
                            <input type="date" name="departure_date" required
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                        </div>

                        <!-- Return Date -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Return Date <span class="text-red-500">*</span></label>
                            <input type="date" name="return_date" required
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                        </div>

                        <!-- Departure Time -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Departure Time</label>
                            <input type="time" name="departure_time"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                        </div>

                        <!-- Return Time -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Return Time</label>
                            <input type="time" name="return_time"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                        </div>

                        <!-- Destination -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Destination</label>
                            <input type="text" name="destination" placeholder="Where is the student going?"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                        </div>

                        <!-- Accompanied By -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Accompanied By</label>
                            <input type="text" name="accompanied_by" placeholder="Who is accompanying the student?"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                        </div>

                        <!-- Contact During Absence -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Contact During Absence</label>
                            <input type="text" name="contact_during_absence" placeholder="Phone number or contact person"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                        </div>

                        <!-- Reason -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Reason <span class="text-red-500">*</span></label>
                            <textarea name="reason" rows="3" required placeholder="Explain why the student needs to leave..."
                                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300"></textarea>
                        </div>
                    </div>

                    <div class="mt-4 flex justify-end space-x-2">
                        <button type="button" onclick="document.getElementById('exeat-form').style.display = 'none'"
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Cancel</button>
                        <button type="submit" 
                                class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Submit Request</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Exeat Requests List -->
        @if($exeats->count() > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Exeat #</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Departure</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Return</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Days</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($exeats as $exeat)
                                    <tr>
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $exeat->exeat_number }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $exeat->student->full_name }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-500">{{ $exeat->departure_date->format('M d, Y') }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-500">{{ $exeat->return_date->format('M d, Y') }}</td>
                                        <td class="px-4 py-3 text-sm text-center text-gray-500">{{ $exeat->getDaysDifference() }}</td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="px-2 py-1 text-xs rounded-full 
                                                {{ $exeat->status == 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $exeat->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $exeat->status == 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                                                {{ $exeat->status == 'cancelled' ? 'bg-gray-100 text-gray-800' : '' }}
                                                {{ $exeat->status == 'completed' ? 'bg-blue-100 text-blue-800' : '' }}">
                                                {{ $exeat->status_label }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <a href="{{ route('parent.exeats.details', $exeat) }}" 
                                               class="text-blue-600 hover:text-blue-800 text-sm">View</a>
                                            @if($exeat->status === 'approved')
                                                <a href="{{ route('parent.exeats.download', $exeat) }}" 
                                                   class="text-green-600 hover:text-green-800 text-sm ml-2">Download</a>
                                            @endif
                                            @if($exeat->status === 'pending')
                                                <form action="{{ route('parent.exeats.cancel', $exeat) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" onclick="return confirm('Cancel this request?')" 
                                                            class="text-red-600 hover:text-red-800 text-sm ml-2">Cancel</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $exeats->links() }}
                    </div>
                </div>
            </div>
        @else
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-12 text-center">
                    <div class="text-6xl mb-4">📋</div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">No Exeat Requests</h3>
                    <p class="text-gray-500">You haven't made any exeat requests yet.</p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection