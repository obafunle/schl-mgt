<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TimetableEntry;
use App\Models\ClassModel;
use App\Models\ClassArm;
use App\Models\Subject;
use App\Models\Staff;
use App\Models\Room;
use App\Models\Term;
use App\Models\AcademicYear;
use App\Models\TimetableDay;
use App\Models\TimetablePeriod;
use App\Models\TimetableTemplate;
use App\Services\TimetableService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TimetableController extends Controller
{
    protected $timetableService;

    public function __construct(TimetableService $timetableService)
    {
        $this->middleware('permission:manage_timetable');
        $this->timetableService = $timetableService;
    }

    /**
     * Display timetable management index
     */
    public function index()
    {
        $classes = ClassModel::where('is_active', true)->get();
        $terms = Term::where('is_active', true)->get();
        $academicYears = AcademicYear::where('is_active', true)->get();

        return view('admin.timetable.index', compact('classes', 'terms', 'academicYears'));
    }

    /**
     * Show timetable for a class
     */
    public function show(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'term_id' => 'required|exists:terms,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'class_arm_id' => 'nullable|exists:class_arms,id',
        ]);

        $timetable = $this->timetableService->getTimetableGrid(
            $request->class_id,
            $request->class_arm_id,
            $request->term_id,
            $request->academic_year_id
        );

        $class = ClassModel::find($request->class_id);
        $classArm = $request->class_arm_id ? ClassArm::find($request->class_arm_id) : null;
        $term = Term::find($request->term_id);

        return view('admin.timetable.show', compact('timetable', 'class', 'classArm', 'term'));
    }

    /**
     * Show timetable creation form
     */
    public function create()
    {
        $classes = ClassModel::where('is_active', true)->get();
        $classArms = ClassArm::where('is_active', true)->get();
        $subjects = Subject::where('is_active', true)->get();
        $teachers = Staff::where('status', 'active')->where('staff_type', 'teacher')->get();
        $rooms = Room::where('is_active', true)->get();
        $days = TimetableDay::where('is_school_day', true)->get();
        $periods = TimetablePeriod::where('type', 'academic')->get();
        $terms = Term::where('is_active', true)->get();
        $academicYears = AcademicYear::where('is_active', true)->get();
        $templates = TimetableTemplate::where('is_active', true)->get();

        return view('admin.timetable.create', compact(
            'classes', 'classArms', 'subjects', 'teachers', 'rooms',
            'days', 'periods', 'terms', 'academicYears', 'templates'
        ));
    }

    /**
     * Store a new timetable entry
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'class_arm_id' => 'nullable|exists:class_arms,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:staff,id',
            'room_id' => 'nullable|exists:rooms,id',
            'day_id' => 'required|exists:timetable_days,id',
            'period_id' => 'required|exists:timetable_periods,id',
            'term_id' => 'required|exists:terms,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'notes' => 'nullable|string|max:500',
            'is_recurring' => 'boolean',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['status'] = 'scheduled';
        $validated['start_date'] = now();
        $validated['is_recurring'] = $request->has('is_recurring');

        $entry = TimetableEntry::create($validated);

        // Detect conflicts
        $conflicts = $this->timetableService->detectConflicts($entry);
        if ($conflicts->count() > 0) {
            $this->timetableService->saveConflicts($entry, $conflicts);
            $conflictMessage = 'Warning: ' . $conflicts->count() . ' conflict(s) detected.';
        } else {
            $conflictMessage = 'No conflicts detected.';
        }

        return redirect()->route('admin.timetable.show', [
            'class_id' => $validated['class_id'],
            'class_arm_id' => $validated['class_arm_id'],
            'term_id' => $validated['term_id'],
            'academic_year_id' => $validated['academic_year_id'],
        ])->with('success', 'Timetable entry created successfully! ' . $conflictMessage);
    }

    /**
     * Show the form for editing a timetable entry
     */
    public function edit(TimetableEntry $timetableEntry)
    {
        $classes = ClassModel::where('is_active', true)->get();
        $classArms = ClassArm::where('is_active', true)->get();
        $subjects = Subject::where('is_active', true)->get();
        $teachers = Staff::where('status', 'active')->where('staff_type', 'teacher')->get();
        $rooms = Room::where('is_active', true)->get();
        $days = TimetableDay::where('is_school_day', true)->get();
        $periods = TimetablePeriod::where('type', 'academic')->get();

        $entry = $timetableEntry->load(['class', 'classArm', 'subject', 'teacher', 'room', 'day', 'period']);

        return view('admin.timetable.edit', compact(
            'entry', 'classes', 'classArms', 'subjects',
            'teachers', 'rooms', 'days', 'periods'
        ));
    }

    /**
     * Update timetable entry
     */
    public function update(Request $request, TimetableEntry $timetableEntry)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'class_arm_id' => 'nullable|exists:class_arms,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:staff,id',
            'room_id' => 'nullable|exists:rooms,id',
            'day_id' => 'required|exists:timetable_days,id',
            'period_id' => 'required|exists:timetable_periods,id',
            'notes' => 'nullable|string|max:500',
        ]);

        $validated['updated_by'] = auth()->id();

        $timetableEntry->update($validated);

        // Re-detect conflicts
        $conflicts = $this->timetableService->detectConflicts($timetableEntry);
        if ($conflicts->count() > 0) {
            $this->timetableService->saveConflicts($timetableEntry, $conflicts);
            $conflictMessage = 'Warning: ' . $conflicts->count() . ' conflict(s) detected.';
        } else {
            $conflictMessage = 'No conflicts detected.';
        }

        return redirect()->route('admin.timetable.show', [
            'class_id' => $timetableEntry->class_id,
            'class_arm_id' => $timetableEntry->class_arm_id,
            'term_id' => $timetableEntry->term_id,
            'academic_year_id' => $timetableEntry->academic_year_id,
        ])->with('success', 'Timetable entry updated successfully! ' . $conflictMessage);
    }

    /**
     * Delete timetable entry
     */
    public function destroy(TimetableEntry $timetableEntry)
    {
        $classId = $timetableEntry->class_id;
        $classArmId = $timetableEntry->class_arm_id;
        $termId = $timetableEntry->term_id;
        $academicYearId = $timetableEntry->academic_year_id;

        $timetableEntry->delete();

        return redirect()->route('admin.timetable.show', [
            'class_id' => $classId,
            'class_arm_id' => $classArmId,
            'term_id' => $termId,
            'academic_year_id' => $academicYearId,
        ])->with('success', 'Timetable entry deleted successfully.');
    }

    /**
     * Generate auto timetable
     */
    public function generate(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'term_id' => 'required|exists:terms,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'class_arm_id' => 'nullable|exists:class_arms,id',
            'template_id' => 'nullable|exists:timetable_templates,id',
        ]);

        $result = $this->timetableService->generateTimetable(
            $request->class_id,
            $request->class_arm_id,
            $request->term_id,
            $request->academic_year_id
        );

        $conflictCount = count($result['conflicts']);

        return redirect()->route('admin.timetable.show', [
            'class_id' => $request->class_id,
            'class_arm_id' => $request->class_arm_id,
            'term_id' => $request->term_id,
            'academic_year_id' => $request->academic_year_id,
        ])->with('success', "Timetable generated successfully with {$result['entries']->count()} entries. {$conflictCount} conflict(s) detected.");
    }

    /**
     * Show teacher timetable
     */
    public function teacherTimetable(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:staff,id',
            'term_id' => 'required|exists:terms,id',
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);

        $timetable = $this->timetableService->getTeacherTimetable(
            $request->teacher_id,
            $request->term_id,
            $request->academic_year_id
        );

        $teacher = Staff::find($request->teacher_id);
        $term = Term::find($request->term_id);

        return view('admin.timetable.teacher', compact('timetable', 'teacher', 'term'));
    }

    /**
     * Show room timetable
     */
    public function roomTimetable(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'term_id' => 'required|exists:terms,id',
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);

        $timetable = $this->timetableService->getRoomTimetable(
            $request->room_id,
            $request->term_id,
            $request->academic_year_id
        );

        $room = Room::find($request->room_id);
        $term = Term::find($request->term_id);

        return view('admin.timetable.room', compact('timetable', 'room', 'term'));
    }

    /**
     * Resolve conflicts
     */
    public function resolveConflicts(Request $request, TimetableEntry $timetableEntry)
    {
        $request->validate([
            'resolution_notes' => 'nullable|string|max:500',
        ]);

        $this->timetableService->resolveConflicts($timetableEntry, $request->resolution_notes);

        return back()->with('success', 'Conflicts resolved successfully.');
    }

    /**
     * Swap timetable entries
     */
    public function swap(Request $request)
    {
        $request->validate([
            'entry_id_1' => 'required|exists:timetable_entries,id',
            'entry_id_2' => 'required|exists:timetable_entries,id',
        ]);

        $this->timetableService->swapEntries(
            $request->entry_id_1,
            $request->entry_id_2
        );

        return back()->with('success', 'Timetable entries swapped successfully.');
    }

    /**
     * Move timetable entry
     */
    public function move(Request $request)
    {
        $request->validate([
            'entry_id' => 'required|exists:timetable_entries,id',
            'day_id' => 'required|exists:timetable_days,id',
            'period_id' => 'required|exists:timetable_periods,id',
        ]);

        $entry = $this->timetableService->moveEntry(
            $request->entry_id,
            $request->day_id,
            $request->period_id
        );

        return redirect()->route('admin.timetable.show', [
            'class_id' => $entry->class_id,
            'class_arm_id' => $entry->class_arm_id,
            'term_id' => $entry->term_id,
            'academic_year_id' => $entry->academic_year_id,
        ])->with('success', 'Timetable entry moved successfully.');
    }

    /**
     * Clone timetable from previous term
     */
    public function clone(Request $request)
    {
        $request->validate([
            'from_term_id' => 'required|exists:terms,id',
            'to_term_id' => 'required|exists:terms,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'class_id' => 'nullable|exists:classes,id',
        ]);

        $count = $this->timetableService->cloneTimetable(
            $request->from_term_id,
            $request->to_term_id,
            $request->academic_year_id,
            $request->class_id
        );

        return back()->with('success', "Successfully cloned {$count} timetable entries.");
    }

    /**
     * Export timetable to PDF
     */
    public function export(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'term_id' => 'required|exists:terms,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'class_arm_id' => 'nullable|exists:class_arms,id',
            'format' => 'required|in:pdf,print',
        ]);

        $timetable = $this->timetableService->getTimetableGrid(
            $request->class_id,
            $request->class_arm_id,
            $request->term_id,
            $request->academic_year_id
        );

        $class = ClassModel::find($request->class_id);
        $classArm = $request->class_arm_id ? ClassArm::find($request->class_arm_id) : null;
        $term = Term::find($request->term_id);
        $academicYear = AcademicYear::find($request->academic_year_id);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.timetable.export', compact(
            'timetable', 'class', 'classArm', 'term', 'academicYear'
        ));

        $filename = 'timetable-' . $class->code . ($classArm ? '-' . $classArm->code : '') . '-' . $term->name . '.pdf';

        return $pdf->download($filename);
    }
}