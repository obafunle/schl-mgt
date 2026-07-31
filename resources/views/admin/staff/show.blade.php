<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                {{-- ==========================================
                CLICKABLE PHOTO WITH MODAL & CLICKABLE TEXT
                ========================================== --}}
                <div class="text-center">
                    {{-- Clickable Photo --}}
                    <div class="relative cursor-pointer group" onclick="openPhotoModal('{{ $staff->photo_url }}', '{{ $staff->full_name }}')">
                        <img src="{{ $staff->photo_url }}"
                             alt="{{ $staff->full_name }}"
                             class="object-cover w-16 h-16 rounded-full border-2 border-gray-200 group-hover:border-indigo-500 transition duration-200">
                        {{-- Magnifying glass overlay --}}
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 rounded-full transition duration-200 flex items-center justify-center">
                            <svg class="w-8 h-8 text-white opacity-0 group-hover:opacity-100 transition duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                    {{-- "Click to enlarge" text - ALSO CLICKABLE --}}
                    <p class="mt-1 text-xs text-center text-blue-600 cursor-pointer hover:underline"
                       onclick="openPhotoModal('{{ $staff->photo_url }}', '{{ $staff->full_name }}')">
                       Click to enlarge
                    </p>
                </div>

                <div>
                    {{-- Full name includes middle name via getFullNameAttribute() --}}
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        {{ $staff->full_name }}
                    </h2>
                    <p class="text-sm text-gray-500">{{ $staff->staff_id }} • {{ $staff->getStaffTypeLabel() }}</p>
                </div>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('admin.staff.edit', $staff) }}"
                   class="px-4 py-2 text-sm font-bold text-white bg-green-500 rounded-lg hover:bg-green-700 transition">
                   ✏️ Edit Staff
                </a>
                <a href="{{ route('admin.staff.index') }}"
                   class="px-4 py-2 text-sm font-bold text-white bg-gray-500 rounded-lg hover:bg-gray-700 transition">
                   ← Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="p-4 mb-4 text-green-700 bg-green-100 border-l-4 border-green-500 rounded-r">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 mb-4 text-red-700 bg-red-100 border-l-4 border-red-500 rounded-r">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Status Banner -->
            <div class="p-4 mb-6 rounded-lg border
                {{ $staff->status == 'active' ? 'bg-green-50 border-green-400' : '' }}
                {{ $staff->status == 'inactive' ? 'bg-gray-50 border-gray-400' : '' }}
                {{ $staff->status == 'suspended' ? 'bg-yellow-50 border-yellow-400' : '' }}
                {{ $staff->status == 'terminated' ? 'bg-red-50 border-red-400' : '' }}">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="font-semibold">Status:</span>
                        <span class="px-2 py-1 text-sm font-medium rounded-full
                            {{ $staff->status == 'active' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $staff->status == 'inactive' ? 'bg-gray-100 text-gray-800' : '' }}
                            {{ $staff->status == 'suspended' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $staff->status == 'terminated' ? 'bg-red-100 text-red-800' : '' }}">
                            {{ ucfirst($staff->status) }}
                        </span>
                        @if($staff->termination_date)
                            <span class="ml-2 text-sm text-red-600">Terminated: {{ $staff->termination_date->format('M d, Y') }}</span>
                        @endif
                    </div>
                    <form action="{{ route('admin.staff.toggle-status', $staff) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                                class="px-3 py-1 text-sm rounded {{ $staff->status == 'active' ? 'bg-yellow-500 hover:bg-yellow-600 text-white' : 'bg-green-500 hover:bg-green-600 text-white' }}">
                            {{ $staff->status == 'active' ? '⏸ Deactivate' : '▶ Activate' }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-2 gap-4 mb-6 md:grid-cols-6">
                <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <span class="text-sm text-gray-500">Subjects</span>
                    <p class="text-2xl font-bold text-blue-600">{{ $stats['total_subjects'] }}</p>
                </div>
                <div class="p-4 bg-green-50 rounded-lg border border-green-200">
                    <span class="text-sm text-gray-500">Present</span>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['total_days_present'] }}</p>
                </div>
                <div class="p-4 bg-red-50 rounded-lg border border-red-200">
                    <span class="text-sm text-gray-500">Absent</span>
                    <p class="text-2xl font-bold text-red-600">{{ $stats['total_days_absent'] }}</p>
                </div>
                <div class="p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                    <span class="text-sm text-gray-500">Leave Days</span>
                    <p class="text-2xl font-bold text-yellow-600">{{ $stats['total_leave_days'] }}</p>
                </div>
                <div class="p-4 bg-purple-50 rounded-lg border border-purple-200">
                    <span class="text-sm text-gray-500">Avg Rating</span>
                    <p class="text-2xl font-bold text-purple-600">{{ number_format($stats['average_rating'] ?? 0, 1) }}</p>
                </div>
                <div class="p-4 bg-indigo-50 rounded-lg border border-indigo-200">
                    <span class="text-sm text-gray-500">Payrolls</span>
                    <p class="text-2xl font-bold text-indigo-600">{{ $stats['total_payrolls'] }}</p>
                </div>
            </div>

            <!-- Tabs -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg" x-data="{ tab: 'profile' }">
                <div class="overflow-x-auto border-b border-gray-200">
                    <nav class="flex -mb-px min-w-max">
                        <button @click="tab = 'profile'"
                                :class="{'border-indigo-500 text-indigo-600': tab === 'profile', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'profile'}"
                                class="px-6 py-4 text-sm font-medium transition border-b-2 whitespace-nowrap">
                            👤 Profile
                        </button>
                        <button @click="tab = 'subjects'"
                                :class="{'border-indigo-500 text-indigo-600': tab === 'subjects', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'subjects'}"
                                class="px-6 py-4 text-sm font-medium transition border-b-2 whitespace-nowrap">
                            📚 Subjects ({{ $staff->subjects->count() }})
                        </button>
                        <button @click="tab = 'leave'"
                                :class="{'border-indigo-500 text-indigo-600': tab === 'leave', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'leave'}"
                                class="px-6 py-4 text-sm font-medium transition border-b-2 whitespace-nowrap">
                            🏖 Leave ({{ $staff->leaveRequests->where('status', 'pending')->count() }})
                        </button>
                        <button @click="tab = 'attendance'"
                                :class="{'border-indigo-500 text-indigo-600': tab === 'attendance', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'attendance'}"
                                class="px-6 py-4 text-sm font-medium transition border-b-2 whitespace-nowrap">
                            📋 Attendance
                        </button>
                        <button @click="tab = 'payroll'"
                                :class="{'border-indigo-500 text-indigo-600': tab === 'payroll', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'payroll'}"
                                class="px-6 py-4 text-sm font-medium transition border-b-2 whitespace-nowrap">
                            💰 Payroll
                        </button>
                        <button @click="tab = 'performance'"
                                :class="{'border-indigo-500 text-indigo-600': tab === 'performance', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'performance'}"
                                class="px-6 py-4 text-sm font-medium transition border-b-2 whitespace-nowrap">
                            ⭐ Performance
                        </button>
                    </nav>
                </div>

                <div class="p-6">
                    <!-- ============================================ -->
                    <!-- PROFILE TAB -->
                    <!-- ============================================ -->
                    <div x-show="tab === 'profile'">
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div>
                                <h4 class="mb-3 text-lg font-semibold text-gray-700 border-b pb-2">👤 Personal Information</h4>
                                <dl class="space-y-2 text-sm">
                                    <div class="flex justify-between py-2 border-b">
                                        <dt class="text-gray-500">Full Name</dt>
                                        <dd class="font-medium">{{ $staff->full_name }}</dd>
                                    </div>
                                    <div class="flex justify-between py-2 border-b">
                                        <dt class="text-gray-500">Date of Birth</dt>
                                        <dd>{{ $staff->date_of_birth->format('M d, Y') }}</dd>
                                    </div>
                                    <div class="flex justify-between py-2 border-b">
                                        <dt class="text-gray-500">Gender</dt>
                                        <dd>{{ ucfirst($staff->gender) }}</dd>
                                    </div>
                                    <div class="flex justify-between py-2 border-b">
                                        <dt class="text-gray-500">Email</dt>
                                        <dd>{{ $staff->email }}</dd>
                                    </div>
                                    <div class="flex justify-between py-2 border-b">
                                        <dt class="text-gray-500">Phone</dt>
                                        <dd>{{ $staff->phone ?? 'N/A' }}</dd>
                                    </div>
                                    <div class="flex justify-between py-2 border-b">
                                        <dt class="text-gray-500">Address</dt>
                                        <dd>{{ $staff->address ?? 'N/A' }}</dd>
                                    </div>
                                    @if($staff->user)
                                        <div class="flex justify-between py-2 border-b">
                                            <dt class="text-gray-500">User Account</dt>
                                            <dd><span class="text-green-600">✅ Active</span></dd>
                                        </div>
                                    @endif
                                </dl>
                            </div>
                            <div>
                                <h4 class="mb-3 text-lg font-semibold text-gray-700 border-b pb-2">💼 Employment Details</h4>
                                <dl class="space-y-2 text-sm">
                                    <div class="flex justify-between py-2 border-b">
                                        <dt class="text-gray-500">Staff ID</dt>
                                        <dd class="font-mono">{{ $staff->staff_id }}</dd>
                                    </div>
                                    <div class="flex justify-between py-2 border-b">
                                        <dt class="text-gray-500">Type</dt>
                                        <dd>{{ $staff->getStaffTypeLabel() }}</dd>
                                    </div>
                                    <div class="flex justify-between py-2 border-b">
                                        <dt class="text-gray-500">Employment</dt>
                                        <dd>{{ $staff->getEmploymentTypeLabel() }}</dd>
                                    </div>
                                    <div class="flex justify-between py-2 border-b">
                                        <dt class="text-gray-500">Hire Date</dt>
                                        <dd>{{ $staff->hire_date->format('M d, Y') }}</dd>
                                    </div>
                                    <div class="flex justify-between py-2 border-b">
                                        <dt class="text-gray-500">Basic Salary</dt>
                                        <dd>₦{{ number_format($staff->basic_salary, 2) }}</dd>
                                    </div>
                                    <div class="flex justify-between py-2 border-b">
                                        <dt class="text-gray-500">Total Experience</dt>
                                        <dd>{{ $staff->total_experience }} years</dd>
                                    </div>
                                    @if($staff->classAssigned)
                                        <div class="flex justify-between py-2 border-b">
                                            <dt class="text-gray-500">Class Teacher</dt>
                                            <dd>{{ $staff->classAssigned->full_name }}</dd>
                                        </div>
                                    @endif
                                </dl>

                                @if($staff->qualifications)
                                    <h4 class="mt-4 mb-2 font-semibold text-gray-700">🎓 Qualifications</h4>
                                    <ul class="space-y-1 text-sm">
                                        @foreach($staff->qualification_list as $qual)
                                            <li class="text-gray-600">• {{ $qual }}</li>
                                        @endforeach
                                    </ul>
                                @endif

                                @if($staff->experience)
                                    <h4 class="mt-4 mb-2 font-semibold text-gray-700">💼 Experience</h4>
                                    <ul class="space-y-1 text-sm">
                                        @foreach($staff->experience_summary as $exp)
                                            <li class="text-gray-600">• {{ $exp }}</li>
                                        @endforeach
                                    </ul>
                                @endif

                                @if($staff->bank_name || $staff->bank_account_number)
                                    <h4 class="mt-4 mb-2 font-semibold text-gray-700">🏦 Bank Details</h4>
                                    <dl class="space-y-1 text-sm">
                                        <div class="flex justify-between">
                                            <dt class="text-gray-500">Bank</dt>
                                            <dd>{{ $staff->bank_name ?? 'N/A' }}</dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="text-gray-500">Account</dt>
                                            <dd>{{ $staff->bank_account_number ?? 'N/A' }}</dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="text-gray-500">Account Name</dt>
                                            <dd>{{ $staff->bank_account_name ?? 'N/A' }}</dd>
                                        </div>
                                    </dl>
                                @endif

                                @if($staff->next_of_kin_name)
                                    <h4 class="mt-4 mb-2 font-semibold text-gray-700">👨‍👩‍👦 Next of Kin</h4>
                                    <dl class="space-y-1 text-sm">
                                        <div class="flex justify-between">
                                            <dt class="text-gray-500">Name</dt>
                                            <dd>{{ $staff->next_of_kin_name }}</dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="text-gray-500">Phone</dt>
                                            <dd>{{ $staff->next_of_kin_phone ?? 'N/A' }}</dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="text-gray-500">Relationship</dt>
                                            <dd>{{ $staff->next_of_kin_relationship ?? 'N/A' }}</dd>
                                        </div>
                                    </dl>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- ============================================ -->
                    <!-- SUBJECTS TAB -->
                    <!-- ============================================ -->
                    <div x-show="tab === 'subjects'">
                        @if($staff->classSubjects->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Class</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hours/Week</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($staff->classSubjects as $index => $assignment)
                                            <tr>
                                                <td class="px-4 py-3 text-sm text-gray-500">{{ $index + 1 }}</td>
                                                <td class="px-4 py-3 text-sm font-medium">{{ $assignment->subject->name }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-500">{{ $assignment->subject->code }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-500">{{ $assignment->class->name ?? 'N/A' }}</td>
                                                <td class="px-4 py-3">
                                                    <span class="px-2 py-1 text-xs rounded-full
                                                        {{ $assignment->role == 'primary' ? 'bg-blue-100 text-blue-800' : '' }}
                                                        {{ $assignment->role == 'secondary' ? 'bg-green-100 text-green-800' : '' }}
                                                        {{ $assignment->role == 'assistant' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                                        {{ ucfirst($assignment->role) }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-500">{{ $assignment->weekly_hours }}</td>
                                                <td class="px-4 py-3">
                                                    <span class="px-2 py-1 text-xs rounded-full
                                                        {{ $assignment->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                        {{ $assignment->is_active ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4 text-sm text-gray-500">
                                Total: {{ $staff->classSubjects->count() }} subject assignments
                            </div>
                        @else
                            <div class="py-8 text-center text-gray-500">
                                <p class="mb-2 text-4xl">📚</p>
                                <p>No subjects assigned to this staff member.</p>
                            </div>
                        @endif
                    </div>

                    <!-- ============================================ -->
                    <!-- LEAVE TAB -->
                    <!-- ============================================ -->
                    <div x-show="tab === 'leave'">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h4 class="font-semibold text-gray-700">Leave Requests</h4>
                                <p class="text-sm text-gray-500">
                                    {{ $staff->leaveRequests->where('status', 'pending')->count() }} pending •
                                    {{ $staff->leaveRequests->where('status', 'approved')->count() }} approved •
                                    {{ $staff->leaveRequests->where('status', 'rejected')->count() }} rejected
                                </p>
                            </div>
                            <button onclick="document.getElementById('leave-form').style.display = 'block'"
                                    class="px-4 py-2 text-sm font-bold text-white bg-blue-500 rounded-lg hover:bg-blue-700 transition">
                                + Request Leave
                            </button>
                        </div>

                        <!-- Leave Request Form -->
                        <div id="leave-form" style="display: none;" class="p-4 mb-6 bg-gray-50 border border-gray-200 rounded-lg">
                            <form action="{{ route('admin.staff.store-leave-request', $staff) }}" method="POST">
                                @csrf
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Leave Type</label>
                                        <select name="leave_type" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                            <option value="annual">Annual Leave</option>
                                            <option value="sick">Sick Leave</option>
                                            <option value="maternity">Maternity Leave</option>
                                            <option value="paternity">Paternity Leave</option>
                                            <option value="study">Study Leave</option>
                                            <option value="compassionate">Compassionate Leave</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Start Date</label>
                                        <input type="date" name="start_date" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">End Date</label>
                                        <input type="date" name="end_date" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <label class="block text-sm font-medium text-gray-700">Reason</label>
                                    <textarea name="reason" rows="2" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300"></textarea>
                                </div>
                                <div class="flex justify-end mt-3 space-x-2">
                                    <button type="button" onclick="document.getElementById('leave-form').style.display = 'none'"
                                            class="px-4 py-2 text-gray-700 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
                                    <button type="submit" class="px-4 py-2 text-white bg-blue-600 rounded hover:bg-blue-700">Submit Request</button>
                                </div>
                            </form>
                        </div>

                        <!-- Leave Requests List -->
                        @if($staff->leaveRequests->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dates</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Days</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reason</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($staff->leaveRequests as $leave)
                                            <tr>
                                                <td class="px-4 py-3 text-sm">
                                                    <span class="font-medium">{{ $leave->getLeaveTypeLabel() }}</span>
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-500">
                                                    {{ $leave->start_date->format('M d') }} - {{ $leave->end_date->format('M d, Y') }}
                                                </td>
                                                <td class="px-4 py-3 text-sm text-center">{{ $leave->total_days }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-500 max-w-xs truncate">
                                                    {{ Str::limit($leave->reason, 50) }}
                                                </td>
                                                <td class="px-4 py-3">
                                                    <span class="px-2 py-1 text-xs rounded-full
                                                        {{ $leave->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                        {{ $leave->status == 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                                        {{ $leave->status == 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                                                        {{ $leave->status == 'cancelled' ? 'bg-gray-100 text-gray-800' : '' }}">
                                                        {{ ucfirst($leave->status) }}
                                                    </span>
                                                    @if($leave->status == 'rejected' && $leave->rejection_reason)
                                                        <div class="mt-1 text-xs text-red-500">{{ $leave->rejection_reason }}</div>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3">
                                                    @if($leave->status == 'pending')
                                                        <form action="{{ route('admin.staff.approve-leave-request', [$staff, $leave]) }}" method="POST" class="inline">
                                                            @csrf
                                                            <button type="submit" class="mr-2 text-sm text-green-600 hover:text-green-800">✅ Approve</button>
                                                        </form>
                                                        <button onclick="showRejectForm({{ $leave->id }})" class="text-sm text-red-600 hover:text-red-800">❌ Reject</button>
                                                        <div id="reject-form-{{ $leave->id }}" style="display: none;" class="mt-2">
                                                            <form action="{{ route('admin.staff.reject-leave-request', [$staff, $leave]) }}" method="POST">
                                                                @csrf
                                                                <input type="text" name="rejection_reason" placeholder="Reason for rejection" required
                                                                       class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                                                <div class="mt-2">
                                                                    <button type="submit" class="px-2 py-1 text-xs font-bold text-white bg-red-500 rounded hover:bg-red-700">Confirm Reject</button>
                                                                    <button type="button" onclick="document.getElementById('reject-form-{{ $leave->id }}').style.display = 'none'"
                                                                            class="ml-2 text-xs text-gray-500">Cancel</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="py-8 text-center text-gray-500">
                                <p class="mb-2 text-4xl">🏖</p>
                                <p>No leave requests found.</p>
                            </div>
                        @endif
                    </div>

                    <!-- ============================================ -->
                    <!-- ATTENDANCE TAB -->
                    <!-- ============================================ -->
                    <div x-show="tab === 'attendance'">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h4 class="font-semibold text-gray-700">Attendance Records</h4>
                                <p class="text-sm text-gray-500">
                                    Present: <span class="text-green-600">{{ $attendanceStats['present'] ?? 0 }}</span> •
                                    Absent: <span class="text-red-600">{{ $attendanceStats['absent'] ?? 0 }}</span> •
                                    Late: <span class="text-yellow-600">{{ $attendanceStats['late'] ?? 0 }}</span>
                                </p>
                            </div>
                            <button onclick="document.getElementById('attendance-form').style.display = 'block'"
                                    class="px-4 py-2 text-sm font-bold text-white bg-blue-500 rounded-lg hover:bg-blue-700 transition">
                                + Mark Attendance
                            </button>
                        </div>

                        <!-- Attendance Form -->
                        <div id="attendance-form" style="display: none;" class="p-4 mb-6 bg-gray-50 border border-gray-200 rounded-lg">
                            <form action="{{ route('admin.staff.store-attendance', $staff) }}" method="POST">
                                @csrf
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Date</label>
                                        <input type="date" name="date" value="{{ date('Y-m-d') }}" required
                                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Status</label>
                                        <select name="status" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                            <option value="present">Present</option>
                                            <option value="absent">Absent</option>
                                            <option value="late">Late</option>
                                            <option value="half-day">Half Day</option>
                                            <option value="leave">Leave</option>
                                            <option value="holiday">Holiday</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Clock In</label>
                                        <input type="time" name="clock_in" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Clock Out</label>
                                        <input type="time" name="clock_out" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <label class="block text-sm font-medium text-gray-700">Notes</label>
                                    <input type="text" name="notes" placeholder="Optional notes..."
                                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                </div>
                                <div class="flex justify-end mt-3 space-x-2">
                                    <button type="button" onclick="document.getElementById('attendance-form').style.display = 'none'"
                                            class="px-4 py-2 text-gray-700 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
                                    <button type="submit" class="px-4 py-2 text-white bg-green-600 rounded hover:bg-green-700">Save Attendance</button>
                                </div>
                            </form>
                        </div>

                        <!-- Attendance Records -->
                        @php
                            $attendanceRecords = $staff->attendance()->orderBy('date', 'desc')->limit(30)->get();
                        @endphp

                        @if($attendanceRecords->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Day</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Clock In</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Clock Out</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hours</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($attendanceRecords as $attendance)
                                            <tr>
                                                <td class="px-4 py-3 text-sm text-gray-500">{{ $attendance->date->format('M d, Y') }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-500">{{ $attendance->date->format('D') }}</td>
                                                <td class="px-4 py-3">
                                                    <span class="px-2 py-1 text-xs rounded-full
                                                        {{ $attendance->status == 'present' ? 'bg-green-100 text-green-800' : '' }}
                                                        {{ $attendance->status == 'absent' ? 'bg-red-100 text-red-800' : '' }}
                                                        {{ $attendance->status == 'late' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                        {{ $attendance->status == 'half-day' ? 'bg-orange-100 text-orange-800' : '' }}
                                                        {{ $attendance->status == 'leave' ? 'bg-purple-100 text-purple-800' : '' }}
                                                        {{ $attendance->status == 'holiday' ? 'bg-blue-100 text-blue-800' : '' }}">
                                                        {{ ucfirst(str_replace('-', ' ', $attendance->status)) }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-500">{{ $attendance->clock_in ?? '-' }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-500">{{ $attendance->clock_out ?? '-' }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-500">{{ $attendance->hours_worked ?? '-' }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-500">{{ $attendance->notes ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4 text-sm text-gray-500">Showing last 30 days</div>
                        @else
                            <div class="py-8 text-center text-gray-500">
                                <p class="mb-2 text-4xl">📋</p>
                                <p>No attendance records found.</p>
                            </div>
                        @endif
                    </div>

                    <!-- ============================================ -->
                    <!-- PAYROLL TAB -->
                    <!-- ============================================ -->
                    <div x-show="tab === 'payroll'">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h4 class="font-semibold text-gray-700">Payroll Records</h4>
                                <p class="text-sm text-gray-500">
                                    Total Payrolls: {{ $staff->payrolls->count() }} •
                                    Net Pay: ₦{{ number_format($staff->payrolls->sum('net_pay'), 2) }}
                                </p>
                            </div>
                            <button onclick="document.getElementById('payroll-form').style.display = 'block'"
                                    class="px-4 py-2 text-sm font-bold text-white bg-blue-500 rounded-lg hover:bg-blue-700 transition">
                                + Generate Payroll
                            </button>
                        </div>

                        <!-- Payroll Form -->
                        <div id="payroll-form" style="display: none;" class="p-4 mb-6 bg-gray-50 border border-gray-200 rounded-lg">
                            <form action="{{ route('admin.staff.generate-payroll', $staff) }}" method="POST">
                                @csrf
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Month</label>
                                        <select name="month" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                            @foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $month)
                                                <option value="{{ $month }}" {{ date('F') == $month ? 'selected' : '' }}>{{ $month }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Year</label>
                                        <select name="year" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                            @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                                <option value="{{ $y }}" {{ date('Y') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Basic Salary</label>
                                        <input type="number" name="basic_salary" value="{{ $staff->basic_salary }}" step="0.01"
                                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Allowances</label>
                                        <input type="number" name="allowances" value="0" step="0.01"
                                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Overtime Pay</label>
                                        <input type="number" name="overtime_pay" value="0" step="0.01"
                                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Bonus</label>
                                        <input type="number" name="bonus" value="0" step="0.01"
                                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Tax</label>
                                        <input type="number" name="tax" value="0" step="0.01"
                                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Pension</label>
                                        <input type="number" name="pension" value="0" step="0.01"
                                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                    </div>
                                </div>
                                <div class="flex justify-end mt-3 space-x-2">
                                    <button type="button" onclick="document.getElementById('payroll-form').style.display = 'none'"
                                            class="px-4 py-2 text-gray-700 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
                                    <button type="submit" class="px-4 py-2 text-white bg-green-600 rounded hover:bg-green-700">Generate Payroll</button>
                                </div>
                            </form>
                        </div>

                        <!-- Payroll Records -->
                        @if($staff->payrolls->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Period</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Basic</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Gross Pay</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deductions</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Net Pay</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($staff->payrolls as $payroll)
                                            <tr>
                                                <td class="px-4 py-3 text-sm text-gray-500">{{ $payroll->month }} {{ $payroll->year }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-500">₦{{ number_format($payroll->basic_salary, 2) }}</td>
                                                <td class="px-4 py-3 text-sm font-medium text-gray-900">₦{{ number_format($payroll->gross_pay, 2) }}</td>
                                                <td class="px-4 py-3 text-sm text-red-600">₦{{ number_format($payroll->total_deductions, 2) }}</td>
                                                <td class="px-4 py-3 text-sm font-bold text-green-600">₦{{ number_format($payroll->net_pay, 2) }}</td>
                                                <td class="px-4 py-3">
                                                    <span class="px-2 py-1 text-xs rounded-full
                                                        {{ $payroll->payment_status == 'paid' ? 'bg-green-100 text-green-800' : '' }}
                                                        {{ $payroll->payment_status == 'processed' ? 'bg-blue-100 text-blue-800' : '' }}
                                                        {{ $payroll->payment_status == 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                                        {{ ucfirst($payroll->payment_status) }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-sm">
                                                    @if($payroll->payment_status == 'pending')
                                                        <form action="{{ route('admin.staff.process-payroll', [$staff, $payroll]) }}" method="POST" class="inline">
                                                            @csrf
                                                            <button type="submit" class="mr-2 text-blue-600 hover:text-blue-800">Process</button>
                                                        </form>
                                                    @endif
                                                    @if($payroll->payment_status == 'processed')
                                                        <button onclick="showPayPaymentForm({{ $payroll->id }})" class="text-green-600 hover:text-green-800">Mark Paid</button>
                                                        <div id="pay-payment-form-{{ $payroll->id }}" style="display: none;" class="mt-2">
                                                            <form action="{{ route('admin.staff.mark-payroll-paid', [$staff, $payroll]) }}" method="POST">
                                                                @csrf
                                                                <div class="flex space-x-2">
                                                                    <input type="date" name="payment_date" required
                                                                           class="text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                                                    <input type="text" name="transaction_reference" placeholder="Reference"
                                                                           class="text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                                                    <button type="submit"
                                                                            class="px-2 py-1 text-xs font-bold text-white bg-green-500 rounded hover:bg-green-700">Confirm</button>
                                                                    <button type="button" onclick="document.getElementById('pay-payment-form-{{ $payroll->id }}').style.display = 'none'"
                                                                            class="text-xs text-gray-500">Cancel</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="py-8 text-center text-gray-500">
                                <p class="mb-2 text-4xl">💰</p>
                                <p>No payroll records found.</p>
                            </div>
                        @endif
                    </div>

                    <!-- ============================================ -->
                    <!-- PERFORMANCE TAB -->
                    <!-- ============================================ -->
                    <div x-show="tab === 'performance'">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h4 class="font-semibold text-gray-700">Performance Reviews</h4>
                                <p class="text-sm text-gray-500">
                                    {{ $staff->performanceReviews->count() }} reviews •
                                    Average Rating:
                                    @php
                                        $avgRating = $staff->performanceReviews()->approved()->avg('overall_rating');
                                    @endphp
                                    @if($avgRating)
                                        <span class="font-bold text-purple-600">{{ number_format($avgRating, 1) }} ⭐</span>
                                    @else
                                        <span class="text-gray-400">No ratings yet</span>
                                    @endif
                                </p>
                            </div>
                            <button onclick="document.getElementById('performance-form').style.display = 'block'"
                                    class="px-4 py-2 text-sm font-bold text-white bg-blue-500 rounded-lg hover:bg-blue-700 transition">
                                + Add Review
                            </button>
                        </div>

                        <!-- Performance Review Form -->
                        <div id="performance-form" style="display: none;" class="p-4 mb-6 bg-gray-50 border border-gray-200 rounded-lg">
                            <form action="{{ route('admin.staff.store-performance-review', $staff) }}" method="POST">
                                @csrf
                                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Review Date</label>
                                        <input type="date" name="review_date" value="{{ date('Y-m-d') }}" required
                                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Review Period</label>
                                        <input type="text" name="review_period" placeholder="e.g., Q1 2024" required
                                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4 mt-3 md:grid-cols-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Punctuality</label>
                                        <select name="punctuality" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                            <option value="">Select</option>
                                            @for($i = 1; $i <= 5; $i++)
                                                <option value="{{ $i }}">{{ $i }} - {{ ['Poor','Fair','Good','Very Good','Excellent'][$i-1] }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Productivity</label>
                                        <select name="productivity" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                            <option value="">Select</option>
                                            @for($i = 1; $i <= 5; $i++)
                                                <option value="{{ $i }}">{{ $i }} - {{ ['Poor','Fair','Good','Very Good','Excellent'][$i-1] }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Teamwork</label>
                                        <select name="teamwork" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                            <option value="">Select</option>
                                            @for($i = 1; $i <= 5; $i++)
                                                <option value="{{ $i }}">{{ $i }} - {{ ['Poor','Fair','Good','Very Good','Excellent'][$i-1] }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Communication</label>
                                        <select name="communication" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                            <option value="">Select</option>
                                            @for($i = 1; $i <= 5; $i++)
                                                <option value="{{ $i }}">{{ $i }} - {{ ['Poor','Fair','Good','Very Good','Excellent'][$i-1] }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Technical Skills</label>
                                        <select name="technical_skills" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                            <option value="">Select</option>
                                            @for($i = 1; $i <= 5; $i++)
                                                <option value="{{ $i }}">{{ $i }} - {{ ['Poor','Fair','Good','Very Good','Excellent'][$i-1] }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Leadership</label>
                                        <select name="leadership" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                            <option value="">Select</option>
                                            @for($i = 1; $i <= 5; $i++)
                                                <option value="{{ $i }}">{{ $i }} - {{ ['Poor','Fair','Good','Very Good','Excellent'][$i-1] }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Problem Solving</label>
                                        <select name="problem_solving" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300">
                                            <option value="">Select</option>
                                            @for($i = 1; $i <= 5; $i++)
                                                <option value="{{ $i }}">{{ $i }} - {{ ['Poor','Fair','Good','Very Good','Excellent'][$i-1] }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <label class="block text-sm font-medium text-gray-700">Strengths</label>
                                    <textarea name="strengths" rows="2" placeholder="Key strengths observed..."
                                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300"></textarea>
                                </div>
                                <div class="mt-2">
                                    <label class="block text-sm font-medium text-gray-700">Areas for Improvement</label>
                                    <textarea name="areas_for_improvement" rows="2" placeholder="Areas that need development..."
                                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300"></textarea>
                                </div>
                                <div class="mt-2">
                                    <label class="block text-sm font-medium text-gray-700">Goals for Next Period</label>
                                    <textarea name="goals" rows="2" placeholder="Goals and objectives for next review period..."
                                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300"></textarea>
                                </div>
                                <div class="mt-2">
                                    <label class="block text-sm font-medium text-gray-700">Reviewer Comments</label>
                                    <textarea name="reviewer_comments" rows="2" placeholder="Additional comments from reviewer..."
                                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300"></textarea>
                                </div>
                                <div class="flex justify-end mt-3 space-x-2">
                                    <button type="button" onclick="document.getElementById('performance-form').style.display = 'none'"
                                            class="px-4 py-2 text-gray-700 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>
                                    <button type="submit" class="px-4 py-2 text-white bg-purple-600 rounded hover:bg-purple-700">Submit Review</button>
                                </div>
                            </form>
                        </div>

                        <!-- Performance Reviews List -->
                        @if($staff->performanceReviews->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Period</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rating</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($staff->performanceReviews as $review)
                                            <tr>
                                                <td class="px-4 py-3 text-sm text-gray-500">{{ $review->review_period }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-500">{{ $review->review_date->format('M d, Y') }}</td>
                                                <td class="px-4 py-3">
                                                    @if($review->overall_rating)
                                                        <span class="text-sm font-bold text-purple-600">{{ number_format($review->overall_rating, 1) }} ⭐</span>
                                                        <div class="text-xs text-gray-500">{{ $review->getRatingLabel() }}</div>
                                                    @else
                                                        <span class="text-sm text-gray-400">Not rated</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3">
                                                    <span class="px-2 py-1 text-xs rounded-full
                                                        {{ $review->status == 'approved' ? 'bg-green-100 text-green-800' : '' }}
                                                        {{ $review->status == 'submitted' ? 'bg-blue-100 text-blue-800' : '' }}
                                                        {{ $review->status == 'reviewed' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                        {{ $review->status == 'draft' ? 'bg-gray-100 text-gray-800' : '' }}">
                                                        {{ ucfirst($review->status) }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-sm">
                                                    <button onclick="showReviewDetails({{ $review->id }})" class="text-blue-600 hover:text-blue-800">View</button>
                                                    @if($review->status != 'approved')
                                                        <form action="{{ route('admin.staff.approve-performance-review', [$staff, $review]) }}" method="POST" class="inline">
                                                            @csrf
                                                            <button type="submit" class="ml-2 text-green-600 hover:text-green-800">Approve</button>
                                                        </form>
                                                    @endif
                                                </td>
                                            </tr>
                                            <!-- Review Details Row -->
                                            <tr id="review-details-{{ $review->id }}" style="display: none;">
                                                <td colspan="5" class="px-4 py-3 bg-gray-50">
                                                    <div class="grid grid-cols-2 gap-4 text-sm md:grid-cols-4">
                                                        <div><span class="text-gray-500">Punctuality:</span> <span class="font-medium">{{ $review->punctuality ?? 'N/A' }}/5</span></div>
                                                        <div><span class="text-gray-500">Productivity:</span> <span class="font-medium">{{ $review->productivity ?? 'N/A' }}/5</span></div>
                                                        <div><span class="text-gray-500">Teamwork:</span> <span class="font-medium">{{ $review->teamwork ?? 'N/A' }}/5</span></div>
                                                        <div><span class="text-gray-500">Communication:</span> <span class="font-medium">{{ $review->communication ?? 'N/A' }}/5</span></div>
                                                        <div><span class="text-gray-500">Technical Skills:</span> <span class="font-medium">{{ $review->technical_skills ?? 'N/A' }}/5</span></div>
                                                        <div><span class="text-gray-500">Leadership:</span> <span class="font-medium">{{ $review->leadership ?? 'N/A' }}/5</span></div>
                                                        <div><span class="text-gray-500">Problem Solving:</span> <span class="font-medium">{{ $review->problem_solving ?? 'N/A' }}/5</span></div>
                                                        <div><span class="text-gray-500">Overall:</span> <span class="font-bold text-purple-600">{{ number_format($review->overall_rating, 1) ?? 'N/A' }}/5</span></div>
                                                    </div>
                                                    @if($review->strengths)
                                                        <div class="mt-2"><span class="text-gray-500">Strengths:</span> <span class="text-sm">{{ $review->strengths }}</span></div>
                                                    @endif
                                                    @if($review->areas_for_improvement)
                                                        <div class="mt-1"><span class="text-gray-500">Areas for Improvement:</span> <span class="text-sm">{{ $review->areas_for_improvement }}</span></div>
                                                    @endif
                                                    @if($review->reviewer_comments)
                                                        <div class="mt-1"><span class="text-gray-500">Reviewer Comments:</span> <span class="text-sm">{{ $review->reviewer_comments }}</span></div>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="py-8 text-center text-gray-500">
                                <p class="mb-2 text-4xl">⭐</p>
                                <p>No performance reviews found.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================
    PHOTO MODAL - Full Size Image Viewer
    ============================================================ --}}
    <div id="photoModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 hidden" onclick="closePhotoModal(event)">
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-2xl w-full mx-4 p-4 transform scale-95 transition-transform duration-300" onclick="event.stopPropagation()">
            {{-- Close Button --}}
            <button onclick="closePhotoModal()" class="absolute -top-3 -right-3 bg-red-500 hover:bg-red-600 text-white rounded-full w-10 h-10 flex items-center justify-center shadow-lg transition duration-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            <div class="text-center">
                <img id="modalImage" src="" alt="Staff Photo" class="max-h-[70vh] w-auto mx-auto rounded-lg shadow-md">
                <div id="modalCaption" class="mt-4 text-lg font-semibold text-gray-800"></div>
                <p class="text-sm text-gray-500 mt-1">Click anywhere outside to close</p>
            </div>
        </div>
    </div>

    <script>
        /**
         * Open the photo modal with the selected image
         */
        function openPhotoModal(imageUrl, staffName) {
            const modal = document.getElementById('photoModal');
            const image = document.getElementById('modalImage');
            const caption = document.getElementById('modalCaption');

            if (!modal || !image || !caption) {
                console.error('Modal elements not found!');
                return;
            }

            image.src = imageUrl;
            caption.textContent = staffName;

            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');

            // Smooth animation
            setTimeout(function() {
                const content = modal.querySelector('.relative');
                if (content) {
                    content.classList.remove('scale-95');
                    content.classList.add('scale-100');
                }
            }, 10);
        }

        /**
         * Close the photo modal
         */
        function closePhotoModal(event) {
            const modal = document.getElementById('photoModal');
            if (!modal) return;

            const content = modal.querySelector('.relative');

            // Only close if clicking the backdrop or the close button
            if (event && event.target !== event.currentTarget) {
                return;
            }

            if (content) {
                content.classList.remove('scale-100');
                content.classList.add('scale-95');
            }

            setTimeout(function() {
                modal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }, 300);
        }

        // Close modal with ESC key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const modal = document.getElementById('photoModal');
                if (modal && !modal.classList.contains('hidden')) {
                    closePhotoModal(event);
                }
            }
        });

        // Additional tab functions
        function showRejectForm(id) {
            document.getElementById('reject-form-' + id).style.display = 'block';
        }

        function showPayPaymentForm(id) {
            document.getElementById('pay-payment-form-' + id).style.display = 'block';
        }

        function showReviewDetails(id) {
            const details = document.getElementById('review-details-' + id);
            details.style.display = details.style.display === 'none' ? 'table-row' : 'none';
        }

        console.log('✅ Staff photo modal loaded successfully!');
    </script>

    <style>
        #photoModal {
            transition: opacity 0.3s ease;
        }
        #photoModal .relative {
            transition: transform 0.3s ease;
        }
        #photoModal img {
            max-height: 70vh;
            object-fit: contain;
        }
        .group:hover .group-hover\:bg-opacity-30 {
            --tw-bg-opacity: 0.3;
        }
    </style>
</x-app-layout>
