<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\ClassModel;
use App\Models\ClassArm;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_students')->only(['index', 'show']);
        $this->middleware('permission:create_students')->only(['create', 'store']);
        $this->middleware('permission:edit_students')->only(['edit', 'update']);
        $this->middleware('permission:delete_students')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = Student::with(['class', 'academicYear']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%")
                  ->orWhere('middle_name', 'LIKE', "%{$search}%")
                  ->orWhere('admission_number', 'LIKE', "%{$search}%");
            });
        }

        if ($request->has('class_id') && $request->class_id) {
            $query->where('class_id', $request->class_id);
        }

        $students = $query->latest()->paginate(20);
        $classes = ClassModel::where('is_active', true)->get();

        return view('admin.students.index', compact('students', 'classes'));
    }

    public function create()
    {
        $classes = ClassModel::where('is_active', true)->get();
        $academicYears = AcademicYear::where('is_active', true)->get();
        $classArms = ClassArm::where('is_active', true)->get();

        return view('admin.students.create', compact('classes', 'academicYears', 'classArms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'email' => 'nullable|email|unique:students,email',
            'class_id' => 'nullable|exists:classes,id',
            'class_arm' => 'nullable|string|max:10',
            'academic_year_id' => 'required|exists:academic_years,id',
            'parent_name' => 'required|string|max:255',
            'parent_phone' => 'required|string|max:20',
            'parent_email' => 'nullable|email',
            'photo' => 'nullable|image|max:2048'
        ]);

        $validated['admission_number'] = $this->generateAdmissionNumber();
        $validated['created_by'] = auth()->id();

        if ($request->hasFile('photo')) {
            $validated['photo'] = $this->uploadPhoto($request->file('photo'));
        }

        $student = Student::create($validated);

        try {
            activity()
                ->performedOn($student)
                ->causedBy(auth()->user())
                ->log('Created student: ' . $student->full_name);
        } catch (\Exception $e) {
            // Activity log might not be set up, ignore
        }

        return redirect()->route('admin.students.index')
            ->with('success', 'Student created successfully!');
    }

    public function show(Student $student)
    {
        $student->load(['class', 'academicYear', 'grades', 'payments', 'attendances']);
        return view('admin.students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        $classes = ClassModel::where('is_active', true)->get();
        $academicYears = AcademicYear::where('is_active', true)->get();
        $classArms = ClassArm::where('is_active', true)->get();

        return view('admin.students.edit', compact('student', 'classes', 'academicYears', 'classArms'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'email' => 'nullable|email|unique:students,email,' . $student->id,
            'class_id' => 'nullable|exists:classes,id',
            'class_arm' => 'nullable|string|max:10',
            'academic_year_id' => 'required|exists:academic_years,id',
            'parent_name' => 'required|string|max:255',
            'parent_phone' => 'required|string|max:20',
            'parent_email' => 'nullable|email',
            'photo' => 'nullable|image|max:2048',
            'status' => 'nullable|in:active,graduated,transferred,suspended'
        ]);

        if ($request->hasFile('photo')) {
            if ($student->photo) {
                Storage::disk('public')->delete($student->photo);
            }
            $validated['photo'] = $this->uploadPhoto($request->file('photo'));
        }

        $student->update($validated);

        try {
            activity()
                ->performedOn($student)
                ->causedBy(auth()->user())
                ->log('Updated student: ' . $student->full_name);
        } catch (\Exception $e) {
            // Ignore
        }

        return redirect()->route('admin.students.index')
            ->with('success', 'Student updated successfully!');
    }

    public function destroy(Student $student)
    {
        if ($student->photo) {
            Storage::disk('public')->delete($student->photo);
        }

        $student->delete();

        try {
            activity()
                ->causedBy(auth()->user())
                ->log('Deleted student: ' . $student->full_name);
        } catch (\Exception $e) {
            // Ignore
        }

        return redirect()->route('admin.students.index')
            ->with('success', 'Student deleted successfully!');
    }

    /**
     * Generate admission number
     */
    private function generateAdmissionNumber()
    {
        $year = date('Y');
        $lastStudent = Student::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        if ($lastStudent) {
            $lastNumber = (int)substr($lastStudent->admission_number, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $year . '-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Upload photo with resize (like Staff)
     */
    private function uploadPhoto($photo)
    {
        $filename = time() . '-' . Str::random(10) . '.' . $photo->getClientOriginalExtension();
        $path = $photo->storeAs('students/photos', $filename, 'public');

        try {
            $image = Image::make(Storage::disk('public')->path($path));
            $image->resize(300, 300, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            $image->save();
        } catch (\Exception $e) {
            Log::warning('Could not resize student image: ' . $e->getMessage());
        }

        return $path;
    }
}
