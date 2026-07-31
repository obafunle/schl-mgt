<?php

namespace App\Services;

use App\Models\TimetableEntry;
use App\Models\TimetableConflict;
use App\Models\ClassModel;
use App\Models\ClassArm;
use App\Models\Staff;
use App\Models\Room;
use App\Models\Subject;
use App\Models\Term;
use App\Models\AcademicYear;
use App\Models\TimetableDay;
use App\Models\TimetablePeriod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TimetableService
{
    /**
     * Detect conflicts for a timetable entry
     */
    public function detectConflicts(TimetableEntry $entry)
    {
        $conflicts = collect();

        // Check teacher conflict
        $teacherConflicts = TimetableEntry::where('teacher_id', $entry->teacher_id)
            ->where('day_id', $entry->day_id)
            ->where('period_id', $entry->period_id)
            ->where('id', '!=', $entry->id)
            ->where('status', 'scheduled')
            ->get();

        foreach ($teacherConflicts as $conflict) {
            $conflicts->push([
                'conflicting_entry_id' => $conflict->id,
                'conflict_type' => 'teacher',
                'description' => "Teacher {$entry->teacher->full_name} is already scheduled for {$conflict->subject->name} with {$conflict->class->name} at this time.",
            ]);
        }

        // Check room conflict
        if ($entry->room_id) {
            $roomConflicts = TimetableEntry::where('room_id', $entry->room_id)
                ->where('day_id', $entry->day_id)
                ->where('period_id', $entry->period_id)
                ->where('id', '!=', $entry->id)
                ->where('status', 'scheduled')
                ->get();

            foreach ($roomConflicts as $conflict) {
                $conflicts->push([
                    'conflicting_entry_id' => $conflict->id,
                    'conflict_type' => 'room',
                    'description' => "Room {$entry->room->name} is already occupied for {$conflict->subject->name} with {$conflict->class->name} at this time.",
                ]);
            }
        }

        // Check class conflict
        $classConflicts = TimetableEntry::where('class_id', $entry->class_id)
            ->where('day_id', $entry->day_id)
            ->where('period_id', $entry->period_id)
            ->where('id', '!=', $entry->id)
            ->where('status', 'scheduled')
            ->when($entry->class_arm_id, function ($q) use ($entry) {
                return $q->where('class_arm_id', $entry->class_arm_id);
            })
            ->get();

        foreach ($classConflicts as $conflict) {
            $conflicts->push([
                'conflicting_entry_id' => $conflict->id,
                'conflict_type' => 'class',
                'description' => "Class {$entry->class->name} already has {$conflict->subject->name} scheduled at this time.",
            ]);
        }

        return $conflicts;
    }

    /**
     * Save conflicts to database
     */
    public function saveConflicts(TimetableEntry $entry, $conflicts)
    {
        // Delete existing unresolved conflicts
        $entry->conflicts()->where('is_resolved', false)->delete();

        foreach ($conflicts as $conflict) {
            TimetableConflict::create([
                'entry_id' => $entry->id,
                'conflicting_entry_id' => $conflict['conflicting_entry_id'],
                'conflict_type' => $conflict['conflict_type'],
                'description' => $conflict['description'],
                'is_resolved' => false,
            ]);
        }
    }

    /**
     * Generate timetable for a class
     */
    public function generateTimetable($classId, $classArmId = null, $termId, $academicYearId)
    {
        $class = ClassModel::findOrFail($classId);
        $term = Term::findOrFail($termId);
        $academicYear = AcademicYear::findOrFail($academicYearId);

        // Get all subjects for this class
        $subjects = Subject::whereHas('classSubjects', function ($q) use ($classId, $classArmId) {
            $q->where('class_id', $classId);
            if ($classArmId) {
                $q->where('class_arm_id', $classArmId);
            }
        })->get();

        // Get teachers for each subject
        $teacherAssignments = \App\Models\StaffSubject::where('class_id', $classId)
            ->when($classArmId, function ($q) use ($classArmId) {
                return $q->where('class_arm_id', $classArmId);
            })
            ->get();

        // Get school days and periods
        $days = TimetableDay::where('is_school_day', true)->get();
        $periods = TimetablePeriod::where('type', 'academic')->get();
        $rooms = Room::where('is_active', true)->get();

        $entries = [];
        $conflicts = [];

        // Simple rotation algorithm - assign subjects to available slots
        foreach ($days as $dayIndex => $day) {
            foreach ($periods as $periodIndex => $period) {
                // Calculate which subject to assign based on day and period
                $subjectIndex = ($dayIndex * count($periods) + $periodIndex) % $subjects->count();
                $subject = $subjects->get($subjectIndex);

                if (!$subject) continue;

                // Find teacher for this subject
                $teacherAssignment = $teacherAssignments->where('subject_id', $subject->id)->first();
                if (!$teacherAssignment) continue;

                $teacher = Staff::find($teacherAssignment->staff_id);
                if (!$teacher) continue;

                // Find available room
                $room = $rooms->where('is_active', true)->first();
                if (!$room) continue;

                // Create entry
                $entry = TimetableEntry::create([
                    'class_id' => $classId,
                    'class_arm_id' => $classArmId,
                    'subject_id' => $subject->id,
                    'teacher_id' => $teacher->id,
                    'room_id' => $room->id,
                    'day_id' => $day->id,
                    'period_id' => $period->id,
                    'term_id' => $termId,
                    'academic_year_id' => $academicYearId,
                    'status' => 'scheduled',
                    'is_recurring' => true,
                    'start_date' => $term->start_date,
                    'end_date' => $term->end_date,
                    'created_by' => auth()->id(),
                ]);

                // Detect conflicts
                $entryConflicts = $this->detectConflicts($entry);
                if ($entryConflicts->count() > 0) {
                    $this->saveConflicts($entry, $entryConflicts);
                    $conflicts[] = [
                        'entry' => $entry,
                        'conflicts' => $entryConflicts,
                    ];
                }

                $entries[] = $entry;
            }
        }

        return [
            'entries' => $entries,
            'conflicts' => $conflicts,
        ];
    }

    /**
     * Get class timetable as grid
     */
    public function getTimetableGrid($classId, $classArmId = null, $termId, $academicYearId)
    {
        $entries = TimetableEntry::where('class_id', $classId)
            ->when($classArmId, function ($q) use ($classArmId) {
                return $q->where('class_arm_id', $classArmId);
            })
            ->where('term_id', $termId)
            ->where('academic_year_id', $academicYearId)
            ->where('status', 'scheduled')
            ->with(['subject', 'teacher', 'room', 'day', 'period'])
            ->get();

        $days = TimetableDay::where('is_school_day', true)->get();
        $periods = TimetablePeriod::where('type', 'academic')->get();

        // Build grid
        $grid = [];
        foreach ($periods as $period) {
            $row = [
                'period' => $period,
                'time' => $period->getTimeRange(),
                'entries' => [],
            ];

            foreach ($days as $day) {
                $entry = $entries->first(function ($e) use ($day, $period) {
                    return $e->day_id === $day->id && $e->period_id === $period->id;
                });

                $row['entries'][$day->id] = $entry ? [
                    'subject' => $entry->subject->name,
                    'teacher' => $entry->teacher->full_name,
                    'room' => $entry->room->name ?? 'N/A',
                    'subject_code' => $entry->subject->code,
                    'has_conflict' => $entry->hasConflict(),
                    'entry_id' => $entry->id,
                ] : null;
            }

            $grid[] = $row;
        }

        return [
            'grid' => $grid,
            'days' => $days,
            'periods' => $periods,
            'entries' => $entries,
        ];
    }

    /**
     * Get teacher timetable
     */
    public function getTeacherTimetable($teacherId, $termId, $academicYearId)
    {
        $entries = TimetableEntry::where('teacher_id', $teacherId)
            ->where('term_id', $termId)
            ->where('academic_year_id', $academicYearId)
            ->where('status', 'scheduled')
            ->with(['subject', 'class', 'classArm', 'room', 'day', 'period'])
            ->get();

        $days = TimetableDay::where('is_school_day', true)->get();
        $periods = TimetablePeriod::where('type', 'academic')->get();

        $grid = [];
        foreach ($periods as $period) {
            $row = [
                'period' => $period,
                'time' => $period->getTimeRange(),
                'entries' => [],
            ];

            foreach ($days as $day) {
                $entry = $entries->first(function ($e) use ($day, $period) {
                    return $e->day_id === $day->id && $e->period_id === $period->id;
                });

                $row['entries'][$day->id] = $entry ? [
                    'class' => $entry->class->name . ($entry->classArm ? ' ' . $entry->classArm->name : ''),
                    'subject' => $entry->subject->name,
                    'room' => $entry->room->name ?? 'N/A',
                    'entry_id' => $entry->id,
                ] : null;
            }

            $grid[] = $row;
        }

        return [
            'grid' => $grid,
            'days' => $days,
            'periods' => $periods,
            'entries' => $entries,
        ];
    }

    /**
     * Get room timetable
     */
    public function getRoomTimetable($roomId, $termId, $academicYearId)
    {
        $entries = TimetableEntry::where('room_id', $roomId)
            ->where('term_id', $termId)
            ->where('academic_year_id', $academicYearId)
            ->where('status', 'scheduled')
            ->with(['subject', 'class', 'classArm', 'teacher', 'day', 'period'])
            ->get();

        $days = TimetableDay::where('is_school_day', true)->get();
        $periods = TimetablePeriod::where('type', 'academic')->get();

        $grid = [];
        foreach ($periods as $period) {
            $row = [
                'period' => $period,
                'time' => $period->getTimeRange(),
                'entries' => [],
            ];

            foreach ($days as $day) {
                $entry = $entries->first(function ($e) use ($day, $period) {
                    return $e->day_id === $day->id && $e->period_id === $period->id;
                });

                $row['entries'][$day->id] = $entry ? [
                    'class' => $entry->class->name . ($entry->classArm ? ' ' . $entry->classArm->name : ''),
                    'subject' => $entry->subject->name,
                    'teacher' => $entry->teacher->full_name,
                    'entry_id' => $entry->id,
                ] : null;
            }

            $grid[] = $row;
        }

        return [
            'grid' => $grid,
            'days' => $days,
            'periods' => $periods,
            'entries' => $entries,
        ];
    }

    /**
     * Resolve conflicts for an entry
     */
    public function resolveConflicts(TimetableEntry $entry, $resolutionNotes = null)
    {
        $entry->conflicts()->update([
            'is_resolved' => true,
            'resolution_notes' => $resolutionNotes,
        ]);
    }

    /**
     * Swap two timetable entries
     */
    public function swapEntries($entryId1, $entryId2)
    {
        $entry1 = TimetableEntry::findOrFail($entryId1);
        $entry2 = TimetableEntry::findOrFail($entryId2);

        DB::transaction(function () use ($entry1, $entry2) {
            // Swap days and periods
            $tempDay = $entry1->day_id;
            $tempPeriod = $entry1->period_id;

            $entry1->day_id = $entry2->day_id;
            $entry1->period_id = $entry2->period_id;
            $entry1->save();

            $entry2->day_id = $tempDay;
            $entry2->period_id = $tempPeriod;
            $entry2->save();

            // Re-detect conflicts
            $this->saveConflicts($entry1, $this->detectConflicts($entry1));
            $this->saveConflicts($entry2, $this->detectConflicts($entry2));
        });

        return true;
    }

    /**
     * Move timetable entry to a new slot
     */
    public function moveEntry($entryId, $newDayId, $newPeriodId)
    {
        $entry = TimetableEntry::findOrFail($entryId);

        DB::transaction(function () use ($entry, $newDayId, $newPeriodId) {
            $entry->day_id = $newDayId;
            $entry->period_id = $newPeriodId;
            $entry->save();

            // Re-detect conflicts
            $this->saveConflicts($entry, $this->detectConflicts($entry));
        });

        return $entry;
    }

    /**
     * Clone timetable from one term to another
     */
    public function cloneTimetable($fromTermId, $toTermId, $academicYearId, $classId = null)
    {
        $fromTerm = Term::findOrFail($fromTermId);
        $toTerm = Term::findOrFail($toTermId);

        $query = TimetableEntry::where('term_id', $fromTermId)
            ->where('academic_year_id', $academicYearId)
            ->where('status', 'scheduled');

        if ($classId) {
            $query->where('class_id', $classId);
        }

        $entries = $query->get();

        DB::transaction(function () use ($entries, $toTerm) {
            foreach ($entries as $entry) {
                $newEntry = $entry->replicate();
                $newEntry->term_id = $toTerm->id;
                $newEntry->start_date = $toTerm->start_date;
                $newEntry->end_date = $toTerm->end_date;
                $newEntry->created_by = auth()->id();
                $newEntry->updated_by = null;
                $newEntry->created_at = now();
                $newEntry->updated_at = now();
                $newEntry->save();

                // Re-detect conflicts
                $this->saveConflicts($newEntry, $this->detectConflicts($newEntry));
            }
        });

        return $entries->count();
    }
}