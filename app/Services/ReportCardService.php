<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Term;
use App\Models\AcademicYear;
use App\Models\Grade;
use App\Models\ReportCard;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportCardService
{
    /**
     * Generate report card for a student
     */
    public function generateReportCard($studentId, $termId, $academicYearId)
    {
        $student = Student::with(['class', 'classArm'])->findOrFail($studentId);
        $term = Term::findOrFail($termId);
        $academicYear = AcademicYear::findOrFail($academicYearId);

        // Get all grades for this student
        $grades = Grade::where('student_id', $studentId)
            ->where('term_id', $termId)
            ->where('academic_year_id', $academicYearId)
            ->with(['subject', 'examination'])
            ->get();

        if ($grades->isEmpty()) {
            return null;
        }

        // Calculate summary
        $totalScore = $grades->sum('total_score');
        $average = $totalScore / $grades->count();
        $passed = $grades->whereIn('grade', ['A', 'B', 'C', 'D', 'E'])->count();
        $failed = $grades->where('grade', 'F')->count();
        $gpa = $grades->avg(function ($grade) {
            return $grade->getGradePoint();
        });

        // Determine promotion status (simplified)
        $promotionStatus = 'pending';
        if ($average >= 50 && $failed === 0) {
            $promotionStatus = 'promoted';
        } elseif ($average < 40 || $failed > 3) {
            $promotionStatus = 'repeated';
        } elseif ($average >= 40 && $average < 50) {
            $promotionStatus = 'demoted';
        }

        // Create or update report card
        $reportCard = ReportCard::updateOrCreate(
            [
                'student_id' => $studentId,
                'term_id' => $termId,
                'academic_year_id' => $academicYearId,
            ],
            [
                'class_id' => $student->class_id,
                'class_arm_id' => $student->class_arm_id,
                'total_score' => $totalScore,
                'average_score' => round($average, 2),
                'grade_point_average' => round($gpa, 2),
                'total_subjects' => $grades->count(),
                'subjects_passed' => $passed,
                'subjects_failed' => $failed,
                'promotion_status' => $promotionStatus,
                'status' => 'draft',
                'generated_by' => auth()->id(),
            ]
        );

        return $reportCard;
    }

    /**
     * Generate PDF report card
     */
    public function generatePDF($reportCardId)
    {
        $reportCard = ReportCard::with([
            'student',
            'class',
            'classArm',
            'term',
            'academicYear'
        ])->findOrFail($reportCardId);

        // Get all grades for this report card
        $grades = Grade::where('student_id', $reportCard->student_id)
            ->where('term_id', $reportCard->term_id)
            ->where('academic_year_id', $reportCard->academic_year_id)
            ->with(['subject'])
            ->orderBy('subject_id')
            ->get();

        $data = [
            'reportCard' => $reportCard,
            'grades' => $grades,
            'school' => [
                'name' => config('app.name'),
                'address' => config('app.school_address', ''),
                'phone' => config('app.school_phone', ''),
                'email' => config('app.school_email', ''),
                'logo' => config('app.school_logo', ''),
            ]
        ];

        $pdf = Pdf::loadView('admin.reports.report-card', $data);
        $pdf->setPaper('a4', 'portrait');

        return $pdf;
    }

    /**
     * Batch generate report cards for a class
     */
    public function batchGenerateReportCards($classId, $classArmId, $termId, $academicYearId)
    {
        $students = Student::where('class_id', $classId)
            ->where('class_arm_id', $classArmId)
            ->where('status', 'active')
            ->get();

        $results = [];
        foreach ($students as $student) {
            $reportCard = $this->generateReportCard(
                $student->id,
                $termId,
                $academicYearId
            );
            $results[] = $reportCard;
        }

        return $results;
    }
}