<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AcademicYearController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage_academic');
    }

    public function index()
    {
        $years = AcademicYear::with('terms', 'createdBy')
            ->orderBy('start_date', 'desc')
            ->paginate(10);
        return view('admin.academic.years.index', compact('years'));
    }

    public function create()
    {
        return view('admin.academic.years.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:academic_years',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'description' => 'nullable|string'
        ]);

        $validated['created_by'] = auth()->id();
        
        DB::transaction(function () use ($validated) {
            $year = AcademicYear::create($validated);
            
            // Create default terms
            $terms = [
                ['name' => 'First Term', 'term_number' => 1],
                ['name' => 'Second Term', 'term_number' => 2],
                ['name' => 'Third Term', 'term_number' => 3]
            ];
            
            foreach ($terms as $termData) {
                Term::create([
                    'academic_year_id' => $year->id,
                    'name' => $termData['name'],
                    'term_number' => $termData['term_number'],
                    'start_date' => $validated['start_date'],
                    'end_date' => $validated['end_date']
                ]);
            }
        });

        return redirect()->route('admin.academic.years.index')
            ->with('success', 'Academic year created successfully!');
    }

    public function edit(AcademicYear $academicYear)
    {
        return view('admin.academic.years.edit', compact('academicYear'));
    }

    public function update(Request $request, AcademicYear $academicYear)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:academic_years,name,' . $academicYear->id,
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
            'is_current' => 'boolean',
            'description' => 'nullable|string'
        ]);

        // If setting as current, unset others
        if ($request->has('is_current') && $request->is_current) {
            AcademicYear::where('is_current', true)->update(['is_current' => false]);
        }

        $academicYear->update($validated);

        return redirect()->route('admin.academic.years.index')
            ->with('success', 'Academic year updated successfully!');
    }

    public function destroy(AcademicYear $academicYear)
    {
        if ($academicYear->is_current) {
            return back()->with('error', 'Cannot delete the current academic year.');
        }
        
        $academicYear->delete();
        return redirect()->route('admin.academic.years.index')
            ->with('success', 'Academic year deleted successfully!');
    }

    public function setCurrent(AcademicYear $academicYear)
    {
        DB::transaction(function () use ($academicYear) {
            AcademicYear::where('is_current', true)->update(['is_current' => false]);
            $academicYear->update(['is_current' => true]);
        });

        return back()->with('success', 'Current academic year set successfully!');
    }
}