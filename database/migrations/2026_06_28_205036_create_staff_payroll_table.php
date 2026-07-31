<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('staff_payroll', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained()->onDelete('cascade');
            $table->string('payroll_period'); // March 2024
            $table->string('month');
            $table->year('year');
            
            // Earnings
            $table->decimal('basic_salary', 15, 2);
            $table->decimal('allowances', 15, 2)->default(0);
            $table->decimal('overtime_pay', 15, 2)->default(0);
            $table->decimal('bonus', 15, 2)->default(0);
            $table->json('other_earnings')->nullable(); // [{name: "Transport", amount: 5000}]
            
            // Deductions
            $table->decimal('tax', 15, 2)->default(0);
            $table->decimal('pension', 15, 2)->default(0);
            $table->decimal('loan_deduction', 15, 2)->default(0);
            $table->decimal('other_deductions', 15, 2)->default(0);
            $table->json('deductions_details')->nullable(); // [{name: "Health Insurance", amount: 2000}]
            
            // Totals
            $table->decimal('gross_pay', 15, 2);
            $table->decimal('total_deductions', 15, 2);
            $table->decimal('net_pay', 15, 2);
            
            // Payment
            $table->enum('payment_status', ['pending', 'processed', 'paid'])->default('pending');
            $table->date('payment_date')->nullable();
            $table->string('transaction_reference')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            
            $table->unique(['staff_id', 'month', 'year']);
            $table->index(['month', 'year']);
            $table->index('payment_status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('staff_payroll');
    }
};