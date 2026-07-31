<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExeatRequest;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExeatController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->middleware('permission:manage_exeats');
        $this->notificationService = $notificationService;
    }

    public function index(Request $request)
    {
        $query = ExeatRequest::with(['student', 'parent', 'term', 'academicYear']);

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('student_id') && $request->student_id) {
            $query->where('student_id', $request->student_id);
        }

        $exeats = $query->latest()->paginate(20);
        $statuses = ['pending', 'approved', 'rejected', 'cancelled', 'completed'];

        return view('admin.exeats.index', compact('exeats', 'statuses'));
    }

    public function show(ExeatRequest $exeat)
    {
        $exeat->load(['student', 'parent', 'term', 'academicYear', 'approvedBy']);
        return view('admin.exeats.show', compact('exeat'));
    }

    public function approve(Request $request, ExeatRequest $exeat)
    {
        if ($exeat->status !== 'pending') {
            return back()->with('error', 'This request has already been processed.');
        }

        DB::transaction(function () use ($exeat) {
            $exeat->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            // Send notification to parent
            $this->notificationService->sendExeatApproved($exeat);
        });

        return redirect()->route('admin.exeats.index')
            ->with('success', 'Exeat request approved and parent notified.');
    }

    public function reject(Request $request, ExeatRequest $exeat)
    {
        if ($exeat->status !== 'pending') {
            return back()->with('error', 'This request has already been processed.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        DB::transaction(function () use ($exeat, $validated) {
            $exeat->update([
                'status' => 'rejected',
                'rejection_reason' => $validated['rejection_reason'],
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            // Send notification to parent
            $this->notificationService->sendExeatRejected($exeat);
        });

        return redirect()->route('admin.exeats.index')
            ->with('success', 'Exeat request rejected and parent notified.');
    }

    public function markCompleted(ExeatRequest $exeat)
    {
        if ($exeat->status !== 'approved') {
            return back()->with('error', 'Only approved exeats can be marked as completed.');
        }

        $exeat->update(['status' => 'completed']);

        return back()->with('success', 'Exeat marked as completed.');
    }

    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'exeat_ids' => 'required|array',
            'exeat_ids.*' => 'exists:exeat_requests,id',
            'action' => 'required|in:approve,reject,delete',
            'rejection_reason' => 'required_if:action,reject|string|max:500',
        ]);

        $count = 0;
        foreach ($validated['exeat_ids'] as $id) {
            $exeat = ExeatRequest::find($id);
            if (!$exeat) continue;

            if ($validated['action'] === 'approve' && $exeat->status === 'pending') {
                $exeat->update([
                    'status' => 'approved',
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                ]);
                $this->notificationService->sendExeatApproved($exeat);
                $count++;
            } elseif ($validated['action'] === 'reject' && $exeat->status === 'pending') {
                $exeat->update([
                    'status' => 'rejected',
                    'rejection_reason' => $validated['rejection_reason'],
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                ]);
                $this->notificationService->sendExeatRejected($exeat);
                $count++;
            } elseif ($validated['action'] === 'delete') {
                $exeat->delete();
                $count++;
            }
        }

        return back()->with('success', "Action performed on {$count} exeat requests.");
    }

    public function sendReminder(Request $request, ExeatRequest $exeat)
    {
        $this->notificationService->sendExeatSubmitted($exeat);

        return back()->with('success', 'Reminder notification sent to parent.');
    }
}