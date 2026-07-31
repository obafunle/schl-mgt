<?php

namespace Tests\Feature\Modules;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\Invoice;
use App\Models\FeeStructure;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FeeManagementTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    public function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->create();
        $this->admin->assignRole('super-admin');
    }

    /** @test */
    public function admin_can_create_fee_structure()
    {
        $feeData = [
            'name' => 'Tuition Fee',
            'code' => 'TUIT',
            'amount' => 50000,
            'frequency' => 'termly',
            'is_mandatory' => true
        ];

        $response = $this->actingAs($this->admin)
            ->post('/admin/fees', $feeData);

        $response->assertRedirect('/admin/fees');
        $this->assertDatabaseHas('fee_structures', [
            'name' => 'Tuition Fee',
            'amount' => 50000
        ]);
    }

    /** @test */
    public function admin_can_generate_invoice()
    {
        $student = Student::factory()->create();
        $feeStructure = FeeStructure::factory()->create();

        $invoiceData = [
            'student_id' => $student->id,
            'term_id' => 1,
            'academic_year_id' => 1,
            'items' => [
                ['name' => 'Tuition', 'amount' => 50000],
                ['name' => 'PTA Levy', 'amount' => 5000]
            ],
            'due_date' => now()->addDays(30)->format('Y-m-d')
        ];

        $response = $this->actingAs($this->admin)
            ->post('/admin/invoices', $invoiceData);

        $response->assertRedirect('/admin/invoices');
        $this->assertDatabaseHas('invoices', [
            'student_id' => $student->id,
            'total' => 55000
        ]);
    }

    /** @test */
    public function invoice_calculates_balance_correctly()
    {
        $invoice = Invoice::factory()->create([
            'total' => 100000,
            'amount_paid' => 40000
        ]);

        $this->assertEquals(60000, $invoice->balance);
    }

    /** @test */
    public function invoice_status_updates_after_payment()
    {
        $invoice = Invoice::factory()->create([
            'total' => 100000,
            'amount_paid' => 0,
            'status' => 'sent'
        ]);

        $invoice->updatePayment(100000);
        
        $this->assertEquals('paid', $invoice->fresh()->status);
    }
}