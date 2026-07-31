<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Enter Grades') }}
            </h2>
            <div class="text-sm text-gray-500">
                {{ $examination->subject->name }} - {{ $examination->class->name }}
                @if($examination->classArm)
                    ({{ $examination->classArm->name }})
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <div class="mb-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p><strong>CA Weight:</strong> {{ $examination->ca_weight }}%</p>
                                <p><strong>Exam Weight:</strong> {{ $examination->exam_weight }}%</p>
                                <p><strong>Total Marks:</strong> {{ $examination->total_marks }}</p>
                            </div>
                            <div>
                                <span class="px-3 py-1 bg-{{ $examination->getStatusColor() }}-100 text-{{ $examination->getStatusColor() }}-800 rounded-full text-sm">
                                    {{ $examination->getStatusLabel() }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('admin.examinations.store-grades', $examination) }}" method="POST">
                        @csrf
                        
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Student</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Admission No</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">CA Score ({{ $examination->ca_weight }}%)</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Exam Score ({{ $examination->exam_weight }}%)</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Grade</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Remark</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($students as $index => $student)
                                        @php
                                            $grade = $grades[$student->id] ?? null;
                                            $caScore = $grade ? $grade->ca_score : null;
                                            $examScore = $grade ? $grade->exam_score : null;
                                        @endphp
                                        <tr>
                                            <td class="px-4 py-2 text-sm text-gray-500">{{ $index + 1 }}</td>
                                            <td class="px-4 py-2">
                                                <div class="flex items-center">
                                                    <img src="{{ $student->photo_url }}" alt="" class="h-8 w-8 rounded-full object-cover mr-2">
                                                    <span class="text-sm font-medium">{{ $student->full_name }}</span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-2 text-sm text-gray-500">{{ $student->admission_number }}</td>
                                            <td class="px-4 py-2">
                                                <input type="number" 
                                                       name="grades[{{ $student->id }}][student_id]" 
                                                       value="{{ $student->id }}" hidden>
                                                <input type="number" 
                                                       name="grades[{{ $student->id }}][ca_score]" 
                                                       value="{{ $caScore }}"
                                                       step="0.01" min="0" max="100"
                                                       class="w-20 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 grade-input"
                                                       data-student="{{ $student->id }}">
                                            </td>
                                            <td class="px-4 py-2">
                                                <input type="number" 
                                                       name="grades[{{ $student->id }}][exam_score]" 
                                                       value="{{ $examScore }}"
                                                       step="0.01" min="0" max="100"
                                                       class="w-20 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 grade-input"
                                                       data-student="{{ $student->id }}">
                                            </td>
                                            <td class="px-4 py-2 text-sm font-medium total-score" id="total-{{ $student->id }}">
                                                {{ $grade ? $grade->total_score : '-' }}
                                            </td>
                                            <td class="px-4 py-2 text-sm font-bold grade-display" id="grade-{{ $student->id }}">
                                                {{ $grade ? $grade->grade : '-' }}
                                            </td>
                                            <td class="px-4 py-2 text-sm remark-display" id="remark-{{ $student->id }}">
                                                {{ $grade ? $grade->remark : '-' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <a href="{{ route('admin.examinations.show', $examination) }}" 
                               class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                                💾 Save All Grades
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('.grade-input');
            
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    const studentId = this.dataset.student;
                    const caInput = document.querySelector(`input[name="grades[${studentId}][ca_score]"]`);
                    const examInput = document.querySelector(`input[name="grades[${studentId}][exam_score]"]`);
                    
                    const ca = parseFloat(caInput?.value) || 0;
                    const exam = parseFloat(examInput?.value) || 0;
                    const total = ca + exam;
                    
                    // Update total
                    document.getElementById(`total-${studentId}`).textContent = total.toFixed(2);
                    
                    // Calculate grade
                    let grade = 'F';
                    let remark = 'Fail';
                    if (total >= 70) { grade = 'A'; remark = 'Excellent'; }
                    else if (total >= 60) { grade = 'B'; remark = 'Good'; }
                    else if (total >= 50) { grade = 'C'; remark = 'Fair'; }
                    else if (total >= 40) { grade = 'D'; remark = 'Pass'; }
                    else if (total >= 30) { grade = 'E'; remark = 'Poor'; }
                    
                    document.getElementById(`grade-${studentId}`).textContent = grade;
                    document.getElementById(`remark-${studentId}`).textContent = remark;
                });
            });
        });
    </script>
    @endpush
</x-app-layout>