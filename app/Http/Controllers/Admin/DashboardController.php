<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Staff;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Examination;
use App\Models\Grade;
use App\Models\Hostel;
use App\Models\HostelBedAssignment;
use App\Models\LibraryBook;
use App\Models\LibraryBorrowing;
use App\Models\Transport;
use App\Models\TransportAssignment;
use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        // ==========================================
        // STUDENT STATISTICS
        // ==========================================
        $totalStudents = Student::count();
        $activeStudents = Student::where('status', 'active')->count();
        $graduatedStudents = Student::where('status', 'graduated')->count();

        // ==========================================
        // STAFF STATISTICS
        // ==========================================
        $totalStaff = Staff::count();
        $activeStaff = Staff::where('status', 'active')->count();
        $teachers = Staff::where('staff_type', 'teacher')->where('status', 'active')->count();

        // ==========================================
        // FINANCIAL STATISTICS
        // ==========================================
        $totalInvoiced = Invoice::sum('total');
        $totalPaid = Invoice::sum('amount_paid');
        $totalBalance = Invoice::sum('balance');
        $paidInvoices = Invoice::where('status', 'paid')->count();
        $overdueInvoices = Invoice::where('status', 'overdue')->count();

        // ==========================================
        // EXAMINATION STATISTICS
        // ==========================================
        $totalExams = Examination::count();
        $completedExams = Examination::where('status', 'completed')->count();
        $publishedExams = Examination::where('status', 'published')->count();
        $totalGrades = Grade::count();
        $averageScore = Grade::avg('total_score');

        // ==========================================
        // HOSTEL STATISTICS
        // ==========================================
        $totalHostels = Hostel::count();
        $totalBeds = Hostel::sum('total_beds');
        $occupiedBeds = HostelBedAssignment::where('status', 'active')->count();
        $availableBeds = $totalBeds - $occupiedBeds;
        $occupancyRate = $totalBeds > 0 ? round(($occupiedBeds / $totalBeds) * 100, 2) : 0;

        // ==========================================
        // LIBRARY STATISTICS
        // ==========================================
        $totalBooks = LibraryBook::sum('quantity');
        $availableBooks = LibraryBook::sum('available');
        $borrowedBooks = LibraryBorrowing::where('status', 'borrowed')->count();
        $overdueBooks = LibraryBorrowing::where('status', 'overdue')->count();

        // ==========================================
        // TRANSPORT STATISTICS
        // ==========================================
        $totalVehicles = Transport::count();
        $activeVehicles = Transport::where('is_active', true)->count();
        $assignedStudents = TransportAssignment::where('status', 'active')->count();
        $totalCapacity = Transport::sum('capacity');

        // ==========================================
        // INVENTORY STATISTICS
        // ==========================================
        $totalInventoryItems = InventoryItem::count();
        $lowStockItems = InventoryItem::whereRaw('quantity <= minimum_stock')->count();
        $outOfStockItems = InventoryItem::where('quantity', 0)->count();

        // ==========================================
        // RECENT ACTIVITIES (Last 5)
        // ==========================================
        $recentStudents = Student::latest()->limit(5)->get();
        $recentPayments = Payment::with(['student'])->latest()->limit(5)->get();
        $recentInvoices = Invoice::with(['student'])->latest()->limit(5)->get();

        // ==========================================
        // CHART DATA (Monthly trends)
        // ==========================================
        $monthlyStudents = Student::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('YEAR(created_at) as year'),
            DB::raw('COUNT(*) as count')
        )
        ->groupBy('year', 'month')
        ->orderBy('year', 'asc')
        ->orderBy('month', 'asc')
        ->limit(12)
        ->get();

        $monthlyRevenue = Payment::select(
            DB::raw('MONTH(payment_date) as month'),
            DB::raw('YEAR(payment_date) as year'),
            DB::raw('SUM(amount) as total')
        )
        ->where('status', 'success')
        ->groupBy('year', 'month')
        ->orderBy('year', 'asc')
        ->orderBy('month', 'asc')
        ->limit(12)
        ->get();

        // ==========================================
        // PASS DATA TO VIEW
        // ==========================================
        return view('admin.dashboard', compact(
            'totalStudents',
            'activeStudents',
            'graduatedStudents',
            'totalStaff',
            'activeStaff',
            'teachers',
            'totalInvoiced',
            'totalPaid',
            'totalBalance',
            'paidInvoices',
            'overdueInvoices',
            'totalExams',
            'completedExams',
            'publishedExams',
            'totalGrades',
            'averageScore',
            'totalHostels',
            'totalBeds',
            'occupiedBeds',
            'availableBeds',
            'occupancyRate',
            'totalBooks',
            'availableBooks',
            'borrowedBooks',
            'overdueBooks',
            'totalVehicles',
            'activeVehicles',
            'assignedStudents',
            'totalCapacity',
            'totalInventoryItems',
            'lowStockItems',
            'outOfStockItems',
            'recentStudents',
            'recentPayments',
            'recentInvoices',
            'monthlyStudents',
            'monthlyRevenue'
        ));
    }
}
