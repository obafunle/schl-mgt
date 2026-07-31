<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Examination;
use App\Models\ClassModel;
use App\Models\ClassArm;
use App\Models\Subject;
use App\Models\Term;
use App\Models\AcademicYear;
use App\Models\Grade;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ExaminationController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_exams')->only(['index', 'show']);
        $this->middleware('permission:create_exams')->only(['create', 'store']);
        $this->middleware('permission:edit_exams')->only(['edit', 'update']);
        $this->middleware('permission:delete_exams')->only(['destroy']);
        $this->middleware('permission:enter_grades')->only(['enterGrades', 'storeGrades']);
        $this->middleware('permission:approve_grades')->only(['approveGrades', 'publishResults']);
    }

    /**
     * Display a listing of examinations
     */
    public function index(Request $request)
    {
        $query = Examination::with(['class', 'subject', 'term', 'academicYear', 'createdBy']);

        // Filters
        if ($request->has('class_id') && $request->class_id) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->has('term_id') && $request->term_id) {
            $query->where('term_id', $request->term_id);
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $examinations = $query->latest()->paginate(20);
        $classes = ClassModel::where('is_active', true)->get();
        $terms = Term::where('is_active', true)->get();

        return view('admin.examinations.index', compact('examinations', 'classes', 'terms'));
    }

    /**
     * Show the form for creating a new examination
     */
    public function create()
    {
        $classes = ClassModel::where('is_active', true)->get();
        $subjects = Subject::where('is_active', true)->get();
        $academicYears = AcademicYear::where('is_active', true)->get();
        $terms = Term::where('is_active', true)->get();

        return view('admin.examinations.create', compact('classes', 'subjects', 'academicYears', 'terms'));
    }

    /**
     * Store a newly created examination
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'class_arm_id' => 'required|exists:class_arms,id',
            'subject_id' => 'required|exists:subjects,id',
            'term_id' => 'required|exists:terms,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'name' => 'nullable|string|max:255',
            'exam_date' => 'required|date',
            'ca_deadline' => 'nullable|date',
            'total_marks' => 'nullable|integer|min:1',
            'ca_weight' => 'nullable|integer|min:0|max:100',
            'exam_weight' => 'nullable|integer|min:0|max:100',
        ]);

        // Auto-calculate weights if not set
        if (!isset($validated['ca_weight']) || !isset($validated['exam_weight'])) {
            $validated['ca_weight'] = 40;
            $validated['exam_weight'] = 60;
        }

        // Ensure weights add up to 100
        if ($validated['ca_weight'] + $validated['exam_weight'] !== 100) {
            return back()->with('error', 'CA and Exam weights must add up to 100%');
        }

        $validated['created_by'] = auth()->id();
        $validated['status'] = 'draft';

        $examination = Examination::create($validated);

        // Optional: Create grade entries for all students in this class
        $this->createGradeEntries($examination);

        return redirect()->route('admin.examinations.index')
            ->with('success', 'Examination created successfully!');
    }

    /**
     * Display the specified examination
     */
    public function show(Examination $examination)
    {
        $examination->load(['class', 'classArm', 'subject', 'term', 'academicYear', 'grades.student']);
        
        // Get students for this exam
        $students = $examination->getStudents();
        
        // Get grade statistics
        $statistics = $this->getExamStatistics($examination);

        return view('admin.examinations.show', compact('examination', 'students', 'statistics'));
    }

    /**
     * Show the form for editing the specified examination
     */
    public function edit(Examination $examination)
    {
        $classes = ClassModel::where('is_active', true)->get();
        $arms = ClassArm::where('class_id', $examination->class_id)->get();
        $subjects = Subject::where('is_active', true)->get();
        $academicYears = AcademicYear::where('is_active', true)->get();
        $terms = Term::where('is_active', true)->get();

        return view('admin.examinations.edit', compact(
            'examination', 'classes', 'arms', 'subjects', 'academicYears', 'terms'
        ));
    }

    /**
     * Update the specified examination
     */
    public function update(Request $request, Examination $examination)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'class_arm_id' => 'required|exists:class_arms,id',
            'subject_id' => 'required|exists:subjects,id',
            'term_id' => 'required|exists:terms,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'name' => 'nullable|string|max:255',
            'exam_date' => 'required|date',
            'ca_deadline' => 'nullable|date',
            'total_marks' => 'nullable|integer|min:1',
            'ca_weight' => 'nullable|integer|min:0|max:100',
            'exam_weight' => 'nullable|integer|min:0|max:100',
        ]);

        if ($validated['ca_weight'] + $validated['exam_weight'] !== 100) {
            return back()->with('error', 'CA and Exam weights must add up to 100%');
        }

        $examination->update($validated);

        return redirect()->route('admin.examinations.index')
            ->with('success', 'Examination updated successfully!');
    }

    /**
     * Remove the specified examination
     */
    public function destroy(Examination $examination)
    {
        if ($examination->status === 'published' || $examination->status === 'completed') {
            return back()->with('error', 'Cannot delete published or completed examination.');
        }

        // Delete associated grades first
        $examination->grades()->delete();
        $examination->delete();

        return redirect()->route('admin.examinations.index')
            ->with('success', 'Examination deleted successfully!');
    }

    /**
     * Show grade entry form
     */
    public function enterGrades(Examination $examination)
    {
        $examination->load(['class', 'classArm', 'subject']);
        $students = $examination->getStudents();
        
        // Get existing grades
        $grades = [];
        foreach ($students as $student) {
            $grade = $examination->grades()->where('student_id', $student->id)->first();
            if ($grade) {
                $grades[$student->id] = $grade;
            }
        }

        return view('admin.examinations.enter-grades', compact('examination', 'students', 'grades'));
    }

    /**
     * Store grades
     */
    public function storeGrades(Request $request, Examination $examination)
    {
        $validated = $request->validate([
            'grades' => 'required|array',
            'grades.*.student_id' => 'required|exists:students,id',
            'grades.*.ca_score' => 'nullable|numeric|min:0|max:100',
            'grades.*.exam_score' => 'nullable|numeric|min:0|max:100',
        ]);

        DB::transaction(function () use ($examination, $validated) {
            foreach ($validated['grades'] as $gradeData) {
                $grade = Grade::updateOrCreate(
                    [
                        'student_id' => $gradeData['student_id'],
                        'examination_id' => $examination->id,
                    ],
                    [
                        'subject_id' => $examination->subject_id,
                        'class_id' => $examination->class_id,
                        'class_arm_id' => $examination->class_arm_id,
                        'term_id' => $examination->term_id,
                        'academic_year_id' => $examination->academic_year_id,
                        'ca_score' => $gradeData['ca_score'] ?? null,
                        'exam_score' => $gradeData['exam_score'] ?? null,
                        'entered_by' => auth()->id(),
                        'entered_at' => now(),
                    ]
                );

                // Calculate total, grade, and remark
                $grade->calculateTotal();
                $grade->save();
            }
        });

        // Update examination status
        if ($examination->isComplete()) {
            $examination->update(['status' => 'completed']);
        }

        return redirect()->route('admin.examinations.show', $examination)
            ->with('success', 'Grades saved successfully!');
    }

    /**
     * Calculate positions for an examination
     */
    public function calculatePositions(Examination $examination)
    {
        DB::transaction(function () use ($examination) {
            $grades = $examination->grades()
                ->whereNotNull('total_score')
                ->orderBy('total_score', 'desc')
                ->get();

            $position = 0;
            $prevScore = null;
            $totalStudents = $grades->count();

            foreach ($grades as $index => $grade) {
                if ($grade->total_score !== $prevScore) {
                    $position = $index + 1;
                }
                
                $grade->position = $position;
                $grade->total_students = $totalStudents;
                $grade->save();
                
                $prevScore = $grade->total_score;
            }
        });

        return back()->with('success', 'Positions calculated successfully!');
    }

    /**
     * Approve grades for an examination
     */
    public function approveGrades(Examination $examination)
    {
        $examination->grades()->update([
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        $examination->update(['status' => 'published']);

        // Generate report cards
        $this->generateReportCards($examination);

        return back()->with('success', 'Grades approved and published successfully!');
    }

    /**
     * Generate report cards for all students in an examination
     */
    protected function generateReportCards(Examination $examination)
    {
        $students = $examination->getStudents();
        
        foreach ($students as $student) {
            // Get all grades for this student in this term
            $grades = Grade::where('student_id', $student->id)
                ->where('term_id', $examination->term_id)
                ->where('academic_year_id', $examination->academic_year_id)
                ->get();

            if ($grades->count() > 0) {
                $totalScore = $grades->sum('total_score');
                $average = $totalScore / $grades->count();
                $passed = $grades->whereIn('grade', ['A', 'B', 'C', 'D', 'E'])->count();
                $failed = $grades->where('grade', 'F')->count();
                $gpa = $grades->avg('getGradePoint');

                ReportCard::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'term_id' => $examination->term_id,
                        'academic_year_id' => $examination->academic_year_id,
                    ],
                    [
                        'class_id' => $examination->class_id,
                        'class_arm_id' => $examination->class_arm_id,
                        'total_score' => $totalScore,
                        'average_score' => $average,
                        'grade_point_average' => $gpa,
                        'total_subjects' => $grades->count(),
                        'subjects_passed' => $passed,
                        'subjects_failed' => $failed,
                        'status' => 'published',
                        'generated_by' => auth()->id(),
                        'published_at' => now(),
                    ]
                );
            }
        }
    }

    /**
     * Create grade entries for all students in a class
     */
    protected function createGradeEntries(Examination $examination)
    {
        $students = $examination->getStudents();
        
        foreach ($students as $student) {
            Grade::firstOrCreate(
                [
                    'student_id' => $student->id,
                    'examination_id' => $examination->id,
                ],
                [
                    'subject_id' => $examination->subject_id,
                    'class_id' => $examination->class_id,
                    'class_arm_id' => $examination->class_arm_id,
                    'term_id' => $examination->term_id,
                    'academic_year_id' => $examination->academic_year_id,
                    'entered_by' => auth()->id(),
                ]
            );
        }
    }

    /**
     * Get examination statistics
     */
    protected function getExamStatistics(Examination $examination)
    {
        $grades = $examination->grades()->whereNotNull('total_score')->get();
        
        if ($grades->count() === 0) {
            return [
                'total_students' => 0,
                'average_score' => 0,
                'highest_score' => 0,
                'lowest_score' => 0,
                'pass_rate' => 0,
                'grade_distribution' => ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'E' => 0, 'F' => 0]
            ];
        }

        $total = $grades->count();
        $avg = $grades->avg('total_score');
        $highest = $grades->max('total_score');
        $lowest = $grades->min('total_score');
        $passed = $grades->whereIn('grade', ['A', 'B', 'C', 'D', 'E'])->count();

        $distribution = ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'E' => 0, 'F' => 0];
        foreach ($grades as $grade) {
            if ($grade->grade) {
                $distribution[$grade->grade]++;
            }
        }

        return [
            'total_students' => $total,
            'average_score' => round($avg, 2),
            'highest_score' => round($highest, 2),
            'lowest_score' => round($lowest, 2),
            'pass_rate' => round(($passed / $total) * 100, 2),
            'grade_distribution' => $distribution
        ];
    }

    /**
     * Get class arms for a class (AJAX)
     */
    public function getClassArms(Request $request)
    {
        $arms = ClassArm::where('class_id', $request->class_id)
            ->where('is_active', true)
            ->get();

        return response()->json($arms);
    }

    /**
     * Publish results to parent portal
     */
    public function publishResults(Examination $examination)
    {
        // This would trigger notifications to parents
        // We'll implement this later with the parent portal
        
        return back()->with('success', 'Results published to parent portal successfully!');
    }
}