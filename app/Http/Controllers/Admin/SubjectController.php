<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\ClassModel;
use App\Models\ClassSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubjectController extends Controller
{
    /**
     * Constructor with permission middleware.
     */
    public function __construct()
    {
        $this->middleware('permission:view_subjects')->only(['index', 'show']);
        $this->middleware('permission:create_subjects')->only(['create', 'store']);
        $this->middleware('permission:edit_subjects')->only(['edit', 'update']);
        $this->middleware('permission:delete_subjects')->only(['destroy']);
        $this->middleware('permission:manage_subjects')->only(['toggleStatus', 'bulkAction']);
    }

    /**
     * Display a listing of subjects with filters.
     */
    public function index(Request $request)
    {
        $query = Subject::with(['classSubjects', 'staffSubjects', 'createdBy']);

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%")
                  ->orWhere('short_name', 'LIKE', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        // Filter by level
        if ($request->has('level') && $request->level) {
            $query->where('level', $request->level);
        }

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->status == '1');
        }

        $subjects = $query->orderBy('name')->paginate(20);

        // Get stats
        $stats = [
            'total' => Subject::count(),
            'active' => Subject::where('is_active', true)->count(),
            'core' => Subject::where('category', 'core')->count(),
            'science' => Subject::where('category', 'science')->count(),
            'arts' => Subject::where('category', 'arts')->count(),
            'vocational' => Subject::where('category', 'vocational')->count(),
        ];

        $categories = ['core', 'science', 'arts', 'vocational', 'other'];
        $levels = ['primary', 'junior', 'senior'];

        return view('admin.subjects.index', compact('subjects', 'stats', 'categories', 'levels'));
    }

    /**
     * Show the form for creating a new subject.
     */
    public function create()
    {
        $categories = ['core', 'science', 'arts', 'vocational', 'other'];
        $levels = ['primary', 'junior', 'senior'];

        return view('admin.subjects.create', compact('categories', 'levels'));
    }

    /**
     * Store a newly created subject.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:subjects,name',
            'code' => 'required|string|max:50|unique:subjects,code',
            'short_name' => 'nullable|string|max:50',
            'category' => 'required|in:core,science,arts,vocational,other',
            'level' => 'required|in:primary,junior,senior',
            'weekly_hours' => 'nullable|integer|min:1|max:40',
            'exam_weight' => 'nullable|integer|min:0|max:100',
            'ca_weight' => 'nullable|integer|min:0|max:100',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        // Auto-calculate weights if not set
        if (!isset($validated['exam_weight']) && !isset($validated['ca_weight'])) {
            $validated['exam_weight'] = 60;
            $validated['ca_weight'] = 40;
        }

        // Ensure weights add up to 100
        if ($validated['exam_weight'] + $validated['ca_weight'] !== 100) {
            return back()->with('error', 'Exam weight and CA weight must add up to 100%.')
                ->withInput();
        }

        $validated['created_by'] = auth()->id();
        $validated['is_active'] = $request->has('is_active');

        DB::transaction(function () use ($validated) {
            $subject = Subject::create($validated);

            activity()
                ->performedOn($subject)
                ->causedBy(auth()->user())
                ->log('Created subject: ' . $subject->name . ' (' . $subject->code . ')');
        });

        return redirect()->route('admin.subjects.index')
            ->with('success', 'Subject "' . $validated['name'] . '" created successfully!');
    }

    /**
     * Display the specified subject with its class assignments.
     */
public function show(Subject $subject)
{
    // Eager load relationships
    $subject->load(['classSubjects.class', 'classSubjects.classArm', 'classSubjects.teacher', 'createdBy']);

    // Get all active classes (if needed by the view)
    $classes = ClassModel::where('is_active', true)->get();

    // Get all teachers assigned to this subject (distinct)
    $teachers = $subject->classSubjects
        ->map(fn($assignment) => $assignment->teacher)
        ->filter()
        ->unique('id')
        ->values();

    // Statistics
    $stats = [
        'total_classes' => $subject->classSubjects()->count(),
        'total_teachers' => $subject->classSubjects()->whereNotNull('teacher_id')->count(),
        'is_active' => $subject->is_active,
        'weekly_hours' => $subject->weekly_hours,
    ];

    return view('admin.subjects.show', compact('subject', 'stats', 'classes', 'teachers'));
}

    /**
     * Show the form for editing the specified subject.
     */
    public function edit(Subject $subject)
    {
        $categories = ['core', 'science', 'arts', 'vocational', 'other'];
        $levels = ['primary', 'junior', 'senior'];

        return view('admin.subjects.edit', compact('subject', 'categories', 'levels'));
    }

    /**
     * Update the specified subject.
     */
    public function update(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:subjects,name,' . $subject->id,
            'code' => 'required|string|max:50|unique:subjects,code,' . $subject->id,
            'short_name' => 'nullable|string|max:50',
            'category' => 'required|in:core,science,arts,vocational,other',
            'level' => 'required|in:primary,junior,senior',
            'weekly_hours' => 'nullable|integer|min:1|max:40',
            'exam_weight' => 'nullable|integer|min:0|max:100',
            'ca_weight' => 'nullable|integer|min:0|max:100',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        // Ensure weights add up to 100
        if ($validated['exam_weight'] + $validated['ca_weight'] !== 100) {
            return back()->with('error', 'Exam weight and CA weight must add up to 100%.')
                ->withInput();
        }

        $validated['is_active'] = $request->has('is_active');

        DB::transaction(function () use ($subject, $validated) {
            $subject->update($validated);

            activity()
                ->performedOn($subject)
                ->causedBy(auth()->user())
                ->log('Updated subject: ' . $subject->name . ' (' . $subject->code . ')');
        });

        return redirect()->route('admin.subjects.index')
            ->with('success', 'Subject "' . $subject->name . '" updated successfully!');
    }

    /**
     * Remove the specified subject.
     */
    public function destroy(Subject $subject)
    {
        // Check if subject is assigned to any class
        if ($subject->classSubjects()->count() > 0) {
            return back()->with('error', 'Cannot delete subject that is assigned to one or more classes.');
        }

        $subjectName = $subject->name;

        DB::transaction(function () use ($subject) {
            activity()
                ->performedOn($subject)
                ->causedBy(auth()->user())
                ->log('Deleted subject: ' . $subject->name);

            $subject->delete();
        });

        return redirect()->route('admin.subjects.index')
            ->with('success', 'Subject "' . $subjectName . '" deleted successfully!');
    }

    /**
     * Toggle active status of a subject.
     */
    public function toggleStatus(Subject $subject)
    {
        $subject->is_active = !$subject->is_active;
        $subject->save();

        $status = $subject->is_active ? 'activated' : 'deactivated';

        activity()
            ->performedOn($subject)
            ->causedBy(auth()->user())
            ->log($status . ' subject: ' . $subject->name);

        return back()->with('success', 'Subject "' . $subject->name . '" ' . $status . ' successfully.');
    }

    /**
     * Bulk action on subjects (activate, deactivate, delete).
     */
    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'subject_ids' => 'required|array',
            'subject_ids.*' => 'exists:subjects,id',
            'action' => 'required|in:activate,deactivate,delete',
        ]);

        $count = 0;
        $failed = 0;

        foreach ($validated['subject_ids'] as $id) {
            $subject = Subject::find($id);
            if (!$subject) continue;

            try {
                if ($validated['action'] === 'delete') {
                    // Check if assigned to classes
                    if ($subject->classSubjects()->count() > 0) {
                        $failed++;
                        continue;
                    }
                    $subject->delete();
                } else {
                    $subject->is_active = ($validated['action'] === 'activate');
                    $subject->save();
                }
                $count++;
            } catch (\Exception $e) {
                $failed++;
                Log::error('Bulk action failed for subject ' . $id . ': ' . $e->getMessage());
            }
        }

        $message = "Action performed on {$count} subject(s).";
        if ($failed > 0) {
            $message .= " {$failed} failed.";
        }

        return back()->with('success', $message);
    }

    /**
     * Get subjects for AJAX dropdown (e.g., in Staff/Class forms).
     */
    public function getSubjects(Request $request)
    {
        $query = Subject::where('is_active', true);

        if ($request->has('level') && $request->level) {
            $query->where('level', $request->level);
        }

        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%")
                  ->orWhere('short_name', 'LIKE', "%{$search}%");
            });
        }

        $subjects = $query->orderBy('name')->limit(50)->get(['id', 'name', 'code', 'short_name']);

        return response()->json($subjects);
    }

    /**
     * Display subjects by category.
     */
    public function byCategory($category)
    {
        $subjects = Subject::where('category', $category)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return response()->json($subjects);
    }

    /**
     * Display subjects by level.
     */
    public function byLevel($level)
    {
        $subjects = Subject::where('level', $level)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return response()->json($subjects);
    }
}
