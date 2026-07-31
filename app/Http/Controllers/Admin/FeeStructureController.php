<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeeStructure;
use App\Models\ClassModel;
use App\Models\ClassArm;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FeeStructureController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_fees');
        $this->middleware('permission:create_fees')->only(['create', 'store']);
        $this->middleware('permission:edit_fees')->only(['edit', 'update']);
        $this->middleware('permission:delete_fees')->only(['destroy']);
    }

    /**
     * Display a listing of fee structures
     */
    public function index(Request $request)
    {
        $query = FeeStructure::with(['class', 'classArm', 'createdBy']);

        // Filters
        if ($request->has('class_id') && $request->class_id) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->has('is_active') && $request->is_active !== '') {
            $query->where('is_active', $request->is_active === 'true');
        }

        if ($request->has('frequency') && $request->frequency) {
            $query->where('frequency', $request->frequency);
        }

        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('code', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('description', 'LIKE', '%' . $request->search . '%');
            });
        }

        $feeStructures = $query->latest()->paginate(20);
        $classes = ClassModel::where('is_active', true)->get();

        return view('admin.fees.index', compact('feeStructures', 'classes'));
    }

    /**
     * Show the form for creating a new fee structure
     */
    public function create()
    {
        $classes = ClassModel::where('is_active', true)->get();
        return view('admin.fees.create', compact('classes'));
    }

    /**
     * Store a newly created fee structure
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:fee_structures,code',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0|max:99999999.99',
            'frequency' => 'required|in:one-time,termly,yearly,monthly',
            'class_id' => 'nullable|exists:classes,id',
            'class_arm_id' => 'nullable|exists:class_arms,id',
            'is_mandatory' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'meta' => 'nullable|array',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['is_mandatory'] = $request->has('is_mandatory');
        $validated['is_active'] = $request->has('is_active');

        // If class_arm_id is provided but class_id is not, we need class_id from the arm
        if ($validated['class_arm_id'] && !$validated['class_id']) {
            $arm = ClassArm::find($validated['class_arm_id']);
            $validated['class_id'] = $arm->class_id ?? null;
        }

        $feeStructure = FeeStructure::create($validated);

        activity()
            ->performedOn($feeStructure)
            ->causedBy(auth()->user())
            ->log('Created fee structure: ' . $feeStructure->name);

        return redirect()->route('admin.fees.index')
            ->with('success', 'Fee structure "' . $feeStructure->name . '" created successfully!');
    }

    /**
     * Display the specified fee structure
     */
    public function show(FeeStructure $fee)
    {
        $fee->load(['class', 'classArm', 'createdBy']);
        return view('admin.fees.show', compact('fee'));
    }

    /**
     * Show the form for editing the specified fee structure
     */
    public function edit(FeeStructure $fee)
    {
        $classes = ClassModel::where('is_active', true)->get();
        $arms = $fee->class_id ? ClassArm::where('class_id', $fee->class_id)->get() : collect();
        
        return view('admin.fees.edit', compact('fee', 'classes', 'arms'));
    }

    /**
     * Update the specified fee structure
     */
    public function update(Request $request, FeeStructure $fee)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:fee_structures,code,' . $fee->id,
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0|max:99999999.99',
            'frequency' => 'required|in:one-time,termly,yearly,monthly',
            'class_id' => 'nullable|exists:classes,id',
            'class_arm_id' => 'nullable|exists:class_arms,id',
            'is_mandatory' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'meta' => 'nullable|array',
        ]);

        $validated['is_mandatory'] = $request->has('is_mandatory');
        $validated['is_active'] = $request->has('is_active');

        // If class_arm_id is provided but class_id is not, we need class_id from the arm
        if ($validated['class_arm_id'] && !$validated['class_id']) {
            $arm = ClassArm::find($validated['class_arm_id']);
            $validated['class_id'] = $arm->class_id ?? null;
        }

        $fee->update($validated);

        activity()
            ->performedOn($fee)
            ->causedBy(auth()->user())
            ->log('Updated fee structure: ' . $fee->name);

        return redirect()->route('admin.fees.index')
            ->with('success', 'Fee structure "' . $fee->name . '" updated successfully!');
    }

    /**
     * Remove the specified fee structure
     */
    public function destroy(FeeStructure $fee)
    {
        // Check if this fee is being used in any invoices
        $usageCount = \App\Models\Invoice::whereJsonContains('items', ['code' => $fee->code])->count();
        
        if ($usageCount > 0) {
            return back()->with('error', 'Cannot delete this fee structure as it is being used in ' . $usageCount . ' invoice(s).');
        }

        $feeName = $fee->name;
        $fee->delete();

        activity()
            ->causedBy(auth()->user())
            ->log('Deleted fee structure: ' . $feeName);

        return redirect()->route('admin.fees.index')
            ->with('success', 'Fee structure "' . $feeName . '" deleted successfully!');
    }

    /**
     * Toggle active status of fee structure
     */
    public function toggleActive(FeeStructure $fee)
    {
        $fee->is_active = !$fee->is_active;
        $fee->save();

        $status = $fee->is_active ? 'activated' : 'deactivated';

        activity()
            ->performedOn($fee)
            ->causedBy(auth()->user())
            ->log($status . ' fee structure: ' . $fee->name);

        return back()->with('success', 'Fee structure "' . $fee->name . '" ' . $status . ' successfully!');
    }

    /**
     * Get class arms for a class (AJAX)
     */
    public function getArms(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
        ]);

        $arms = ClassArm::where('class_id', $request->class_id)
            ->where('is_active', true)
            ->get(['id', 'name', 'code']);

        return response()->json([
            'success' => true,
            'data' => $arms,
        ]);
    }

    /**
     * Clone an existing fee structure
     */
    public function clone(Request $request, FeeStructure $fee)
    {
        $newFee = $fee->replicate();
        $newFee->name = $fee->name . ' (Copy)';
        $newFee->code = $fee->code . '-' . Str::random(4);
        $newFee->created_by = auth()->id();
        $newFee->is_active = false;
        $newFee->save();

        activity()
            ->performedOn($newFee)
            ->causedBy(auth()->user())
            ->log('Cloned fee structure from: ' . $fee->name);

        return redirect()->route('admin.fees.edit', $newFee)
            ->with('success', 'Fee structure cloned successfully! Please review and update the details.');
    }

    /**
     * Bulk delete fee structures
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:fee_structures,id',
        ]);

        $count = FeeStructure::whereIn('id', $request->ids)->count();
        
        // Check if any are being used
        $usedFees = FeeStructure::whereIn('id', $request->ids)
            ->whereHas('invoices')
            ->get();

        if ($usedFees->count() > 0) {
            return back()->with('error', 'Cannot delete ' . $usedFees->count() . ' fee structure(s) as they are being used in invoices.');
        }

        FeeStructure::whereIn('id', $request->ids)->delete();

        activity()
            ->causedBy(auth()->user())
            ->log('Bulk deleted ' . $count . ' fee structures');

        return redirect()->route('admin.fees.index')
            ->with('success', $count . ' fee structure(s) deleted successfully!');
    }
}