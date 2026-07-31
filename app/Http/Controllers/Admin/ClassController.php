<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\ClassArm;
use App\Models\Subject;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClassController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_classes')->only(['index', 'show']);
        $this->middleware('permission:create_classes')->only(['create', 'store']);
        $this->middleware('permission:edit_classes')->only(['edit', 'update']);
        $this->middleware('permission:delete_classes')->only(['destroy']);
        $this->middleware('permission:manage_classes')->only(['addArm', 'assignSubject', 'removeSubject']);
    }

    /**
     * Display a listing of classes
     */
    public function index(Request $request)
    {
        $query = ClassModel::with(['arms', 'createdBy']);

        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->status);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        $classes = $query->orderBy('level')->orderBy('name')->paginate(20);
        $categories = ['primary', 'junior', 'senior'];
        $teachers = User::role('teacher')->get();

        // Statistics
        $stats = [
            'total' => ClassModel::count(),
            'active' => ClassModel::where('is_active', true)->count(),
            'primary' => ClassModel::where('category', 'primary')->count(),
            'junior' => ClassModel::where('category', 'junior')->count(),
            'senior' => ClassModel::where('category', 'senior')->count(),
            'total_arms' => ClassArm::count(),
            'total_students' => \App\Models\Student::count(),
        ];

        return view('admin.classes.index', compact('classes', 'categories', 'teachers', 'stats'));
    }

    /**
     * Show the form for creating a new class
     */
    public function create()
    {
        $categories = ['primary', 'junior', 'senior'];
        $teachers = User::role('teacher')->get();
        return view('admin.classes.create', compact('categories', 'teachers'));
    }

    /**
     * Store a newly created class
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:classes',
            'code' => 'required|string|max:50|unique:classes',
            'level' => 'required|integer|min:1',
            'category' => 'required|in:primary,junior,senior',
            'description' => 'nullable|string|max:500',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['is_active'] = $request->has('is_active');

        $class = ClassModel::create($validated);

        try {
            activity()
                ->performedOn($class)
                ->causedBy(auth()->user())
                ->log('Created class: ' . $class->name);
        } catch (\Exception $e) {
            // Activity log not set up
        }

        return redirect()->route('admin.classes.index')
            ->with('success', 'Class "' . $class->name . '" created successfully!');
    }

    /**
     * Display the specified class
     */
    public function show(ClassModel $class)
    {
        $class->load(['arms', 'classSubjects.subject', 'students']);
        return view('admin.classes.show', compact('class'));
    }

    /**
     * Show the form for editing the specified class
     */
    public function edit(ClassModel $class)
    {
        $categories = ['primary', 'junior', 'senior'];
        $teachers = User::role('teacher')->get();
        return view('admin.classes.edit', compact('class', 'categories', 'teachers'));
    }

    /**
     * Update the specified class
     */
    public function update(Request $request, ClassModel $class)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:classes,name,' . $class->id,
            'code' => 'required|string|max:50|unique:classes,code,' . $class->id,
            'level' => 'required|integer|min:1',
            'category' => 'required|in:primary,junior,senior',
            'description' => 'nullable|string|max:500',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $class->update($validated);

        try {
            activity()
                ->performedOn($class)
                ->causedBy(auth()->user())
                ->log('Updated class: ' . $class->name);
        } catch (\Exception $e) {
            // Activity log not set up
        }

        return redirect()->route('admin.classes.index')
            ->with('success', 'Class "' . $class->name . '" updated successfully!');
    }

    /**
     * Remove the specified class
     */
    public function destroy(ClassModel $class)
    {
        // Check if class has students
        if ($class->students()->count() > 0) {
            return back()->with('error', 'Cannot delete class with enrolled students.');
        }

        if ($class->arms()->count() > 0) {
            return back()->with('error', 'Cannot delete class with existing arms.');
        }

        $name = $class->name;
        $class->delete();

        try {
            activity()
                ->causedBy(auth()->user())
                ->log('Deleted class: ' . $name);
        } catch (\Exception $e) {
            // Activity log not set up
        }

        return redirect()->route('admin.classes.index')
            ->with('success', 'Class "' . $name . '" deleted successfully!');
    }

    /**
     * Toggle class active status
     */
    public function toggleStatus(ClassModel $class)
    {
        $class->is_active = !$class->is_active;
        $class->save();

        $status = $class->is_active ? 'activated' : 'deactivated';

        try {
            activity()
                ->performedOn($class)
                ->causedBy(auth()->user())
                ->log($status . ' class: ' . $class->name);
        } catch (\Exception $e) {
            // Activity log not set up
        }

        return back()->with('success', 'Class "' . $class->name . '" ' . $status . ' successfully!');
    }

    // ============================================================
    // CLASS ARM MANAGEMENT
    // ============================================================

    /**
     * Add an arm to a class
     */
    public function addArm(Request $request, ClassModel $class)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'code' => 'required|string|max:50|unique:class_arms',
            'capacity' => 'nullable|integer|min:1',
            'class_teacher_id' => 'nullable|exists:users,id',
            'is_active' => 'nullable|boolean',
        ]);

        // Check if arm already exists
        if ($class->hasArm($validated['name'])) {
            return back()->with('error', 'Arm "' . $validated['name'] . '" already exists for this class.');
        }

        $validated['class_id'] = $class->id;
        $validated['is_active'] = $request->has('is_active');

        $arm = ClassArm::create($validated);

        try {
            activity()
                ->performedOn($arm)
                ->causedBy(auth()->user())
                ->log('Added arm to class: ' . $class->name . ' - ' . $arm->name);
        } catch (\Exception $e) {
            // Activity log not set up
        }

        return back()->with('success', 'Arm "' . $arm->name . '" added successfully!');
    }

    /**
     * Remove an arm from a class
     */
    public function removeArm(ClassArm $arm)
    {
        if ($arm->students()->count() > 0) {
            return back()->with('error', 'Cannot delete arm with enrolled students.');
        }

        $armName = $arm->name;
        $className = $arm->class->name;

        $arm->delete();

        try {
            activity()
                ->causedBy(auth()->user())
                ->log('Removed arm from class: ' . $className . ' - ' . $armName);
        } catch (\Exception $e) {
            // Activity log not set up
        }

        return back()->with('success', 'Arm "' . $armName . '" removed successfully!');
    }

    /**
     * Update class arm
     */
    public function updateArm(Request $request, ClassArm $arm)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'capacity' => 'nullable|integer|min:1',
            'class_teacher_id' => 'nullable|exists:users,id',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $arm->update($validated);

        try {
            activity()
                ->performedOn($arm)
                ->causedBy(auth()->user())
                ->log('Updated arm: ' . $arm->name);
        } catch (\Exception $e) {
            // Activity log not set up
        }

        return back()->with('success', 'Arm updated successfully!');
    }

    // ============================================================
    // SUBJECT ASSIGNMENT
    // ============================================================

    /**
     * Assign a subject to a class
     */
    public function assignSubject(Request $request, ClassModel $class)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'class_arm_id' => 'nullable|exists:class_arms,id',
            'teacher_id' => 'nullable|exists:users,id',
            'weekly_hours' => 'nullable|integer|min:1|max:40',
            'is_core' => 'nullable|boolean',
            'is_compulsory' => 'nullable|boolean',
        ]);

        $validated['class_id'] = $class->id;
        $validated['is_core'] = $request->has('is_core');
        $validated['is_compulsory'] = $request->has('is_compulsory');

        // Check if subject already assigned
        $exists = \App\Models\ClassSubject::where('class_id', $class->id)
            ->where('subject_id', $validated['subject_id'])
            ->when($validated['class_arm_id'], function ($q) use ($validated) {
                return $q->where('class_arm_id', $validated['class_arm_id']);
            })
            ->exists();

        if ($exists) {
            return back()->with('error', 'This subject is already assigned to this class.');
        }

        $assignment = \App\Models\ClassSubject::create($validated);

        try {
            activity()
                ->performedOn($assignment)
                ->causedBy(auth()->user())
                ->log('Assigned subject to class: ' . $class->name);
        } catch (\Exception $e) {
            // Activity log not set up
        }

        return back()->with('success', 'Subject assigned successfully!');
    }

    /**
     * Remove a subject from a class
     */
    public function removeSubject($classSubjectId)
    {
        $assignment = \App\Models\ClassSubject::findOrFail($classSubjectId);

        $subjectName = $assignment->subject->name;
        $className = $assignment->class->name;

        $assignment->delete();

        try {
            activity()
                ->causedBy(auth()->user())
                ->log('Removed subject from class: ' . $className . ' - ' . $subjectName);
        } catch (\Exception $e) {
            // Activity log not set up
        }

        return back()->with('success', 'Subject removed successfully!');
    }

    // ============================================================
    // AJAX ENDPOINTS
    // ============================================================

    /**
     * Get class arms for a class (AJAX)
     */
    public function getArms(Request $request)
    {
        $classId = $request->get('class_id');

        if (!$classId) {
            return response()->json([]);
        }

        $arms = ClassArm::where('class_id', $classId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'code']);

        return response()->json($arms);
    }

    /**
     * Get classes by category (AJAX)
     */
    public function getByCategory(Request $request)
    {
        $category = $request->get('category');

        if (!$category) {
            return response()->json([]);
        }

        $classes = ClassModel::where('category', $category)
            ->where('is_active', true)
            ->orderBy('level')
            ->get(['id', 'name', 'code']);

        return response()->json($classes);
    }

    /**
     * Get all active classes (AJAX)
     */
    public function getActiveClasses(Request $request)
    {
        $classes = ClassModel::where('is_active', true)
            ->orderBy('level')
            ->get(['id', 'name', 'code', 'category']);

        return response()->json($classes);
    }
}
